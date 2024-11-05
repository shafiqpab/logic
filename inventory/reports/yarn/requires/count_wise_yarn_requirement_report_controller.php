<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../../includes/common.php');
require_once('../../../../includes/class3/class.conditions.php');
require_once('../../../../includes/class3/class.reports.php');
require_once('../../../../includes/class3/class.yarns.php');
require_once('../../../../includes/class3/class.fabrics.php');

$user_id=$_SESSION['logic_erp']['user_id'];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
/*$color_array=return_library_array( "select id, color_name from lib_color", "id", "color_name");
$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name");
$supplier_details=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name");
$buyer_short_name_library=return_library_array( "select id, short_name from lib_buyer", "id", "short_name");
$yarn_count_details=return_library_array( "select id, yarn_count from lib_yarn_count", "id", "yarn_count");*/


if($action=="load_drop_down_buyer")
{
	$data=explode("_",$data);
	if($data[1]==1) $party="1,3,21,90"; else $party="80";
	echo create_drop_down( "cbo_buyer_id", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data[0]' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in ($party)) order by buy.buyer_name","id,buyer_name", 1, "--Select Buyer--", $selected, "","" );
	exit();
}

//$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
//$buyer_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );
//$buyer_array=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
if($action=="job_no_popup")
{
	echo load_html_head_contents("Job Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>
		
		var selected_id = new Array, selected_name = new Array(); selected_attach_id = new Array();
		
		function toggle( x, origColor ) 
		{
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		
		function check_all_data()
		{
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;
			tbl_row_count = tbl_row_count - 1;

			for( var i = 1; i <= tbl_row_count; i++ )
			{
				$('#tr_'+i).trigger('click'); 
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
                                <td align="center">	
                                    <?
                                    $search_by_arr=array(1=>"Job No",2=>"Style Ref");
                                    $dd="change_search_event(this.value, '0*0', '0*0', '../../') ";							
                                    echo create_drop_down( "cbo_search_by", 130, $search_by_arr,"",0, "--Select--", "",$dd,0 );
                                    ?>
                                </td>     
                                <td align="center" id="search_by_td">				
                                    <input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />	
                                </td> 	
                                <td align="center">
                                    <input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_year_id; ?>'+'**'+'<? echo $cbo_month_id; ?>', 'create_job_no_search_list_view', 'search_div', 'count_wise_yarn_requirement_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
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
	$month_id=$data[5];
	//echo $month_id;

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

	if($search_by==2) $search_field="style_ref_no"; else $search_field="job_no";
	
	$arr=array (0=>$company_arr,1=>$buyer_arr);
	
	if($db_type==0)
	{
		if($year_id!=0) $year_search_cond=" and year(insert_date)=$year_id"; else $year_search_cond="";
		$year_cond= "year(insert_date)as year";
	}
	else if($db_type==2)
	{
		if($year_id!=0) $year_search_cond=" and TO_CHAR(insert_date,'YYYY')=$year_id"; else $year_search_cond="";
		$year_cond= "TO_CHAR(insert_date,'YYYY') as year";
	}
	
	$sql= "select id, job_no, job_no_prefix_num, company_name, buyer_name, style_ref_no, $year_cond from wo_po_details_master where status_active=1 and is_deleted=0 and company_name=$company_id and $search_field like '$search_string' $buyer_id_cond $year_search_cond $month_cond order by job_no DESC";
	
	echo create_list_view("tbl_list_search", "Company,Buyer Name,Job No,Year,Style Ref. No", "120,80,80,60","620","270",0, $sql , "js_set_value", "id,job_no", "", 1, "company_name,buyer_name,0,0,0", $arr , "company_name,buyer_name,job_no_prefix_num,year,style_ref_no", "",'','0,0,0,0,0','',1) ;
	exit(); 
}

if($action=="booking_no_popup")
{
	echo load_html_head_contents("Booking Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	
	<script>
	
		var selected_id = new Array; var selected_name = new Array;
		function check_all_datas()
		{
			var tbl_row_count = document.getElementById( 'tbl_list_div' ).rows.length;
			tbl_row_count = tbl_row_count - 1;

			for( var i = 1; i <= tbl_row_count; i++ )
			{
				$('#tr_'+i).trigger('click'); 
			}
		}
		
		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		
		function js_set_value2( str ) {
			
			if (str!="") str=str.split("_");
			
			toggle( document.getElementById( 'tr_' + str[0] ), '#FFFFCC' );
			 
			if( jQuery.inArray( str[1], selected_id ) == -1 ) {
				selected_id.push( str[1] );
				selected_name.push( str[2] );
			
				
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == str[1] ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
				
			}
			var id = ''; var name = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + ',';
				
			}
			
			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );
			
			
			$('#hide_job_id').val( id );
			$('#hide_job_no').val( name );
			//$('#hide_recv_id').val( rec_id );
			//$("#hide_booing_type").val(str[3]);
		}
	</script>
    </head>
    <body>
        <div align="center">
            <form name="styleRef_form" id="styleRef_form">
                <fieldset style="width:680px;">
                    <table width="670" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
                        <thead>
                            <th>Buyer</th>
                            <th>Search By</th>
                            <th id="search_by_td_up" width="170">Please Enter Booking No</th>
                            <th><input type="reset" name="button" class="formbutton" value="Reset"  style="width:100px;"></th> 
                            <input type="hidden" name="hide_job_no" id="hide_job_no" value="" />
                            <input type="hidden" name="hide_job_id" id="hide_job_id" value="" />
                        </thead>
                        <tbody>
                            <tr>
                                <td align="center">
                                     <? 
                                        echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer_name,"",0 );
                                    ?>
                                </td>   
                                              
                                <td align="center">	
                                <?
                                    $search_by_arr=array(1=>"Booking No",2=>"Job No",3=>"Style Ref");
                                    $dd="change_search_event(this.value, '0*0*0', '0*0*0', '../../') ";							
                                    echo create_drop_down( "cbo_search_by", 130, $search_by_arr,"",0, "--Select--", "1",$dd,0 );
                                ?>
                                </td>     
                                <td align="center" id="search_by_td">				
                                    <input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />	
                                </td> 	
                                <td align="center">
                                    <input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_year_id; ?>', 'create_booking_no_search_list_view', 'search_div', 'count_wise_yarn_requirement_report_controller', 'setFilterGrid(\'tbl_list_div\',-1)');" style="width:100px;" />
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
	
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	
	if($data[1]==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="")
			{ 
				$buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")";
				$buyer_id_cond2=" and a.buyer_id in (".$_SESSION['logic_erp']["buyer_id"].")";
			}
			else 
			{ 
				$buyer_id_cond="";$buyer_id_cond2="";
			}
		}
		else
		{
			$buyer_id_cond="";$buyer_id_cond2="";
		}
	}
	else
	{
		$buyer_id_cond=" and a.buyer_name=$data[1]";
		$buyer_id_cond2=" and a.buyer_id=$data[1]";
	}
	
	$search_by=$data[2];
	$search_string="%".trim($data[3])."%";

	if($search_by==3) 
	{
		 $search_field="a.style_ref_no";
	}
	else if($search_by==2)
	{
		 $search_field="a.job_no_prefix_num";
	}
	else $search_field="b.booking_no";
	//$year="year(insert_date)";
	if($db_type==0) $year_field_by="and YEAR(a.insert_date)"; 
	else if($db_type==2) $year_field_by=" and to_char(a.insert_date,'YYYY')";
	else $year_field_by="";
	if($db_type==0) $month_field_by="and month(a.insert_date)"; 
	else if($db_type==2) $month_field_by=" and to_char(a.insert_date,'MM')";
	else $month_field_by="";
	if($db_type==0) $year_field=" YEAR(a.insert_date) as year"; 
	else if($db_type==2) $year_field="  to_char(a.insert_date,'YYYY') as year";
	else $year_field="";

	if($year_id!=0) $year_cond=" $year_field_by=$year_id"; else $year_cond="";
	
	$sql= "select a.job_no,a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no,b.booking_no,c.booking_no_prefix_num,c.id as booking_id  
	from wo_po_details_master a,wo_booking_dtls b ,wo_booking_mst c 
	where a.job_no=b.job_no and b.booking_no=c.booking_no and a.status_active=1 and a.is_deleted=0 and b.booking_type in(1,4) and a.company_name=$company_id and $search_field like '$search_string' $buyer_id_cond $year_cond 
	group by a.job_no,b.booking_no,a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no,c.id,c.booking_no_prefix_num  order by a.job_no desc";
	
	//echo $sql;//die;	
	$sqlResult=sql_select($sql);
	?>
    <div align="center">
    <fieldset style="width:650px;margin-left:10px">
        <form name="searchprocessfrm_1" id="searchprocessfrm_1" autocomplete="off">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="670" class="rpt_table">
                <thead>
                    <th width="30">SL</th>
                    <th width="130">Company</th> 
                    <th width="110">Buyer</th>
                    <th width="110">Job No</th>
                    <th width="120">Style Ref.</th>
                    <th width="">Booking No</th>
                </thead>
            </table>
            <div style="width:670px; overflow-y:scroll; max-height:280px;" id="buyer_list_view" align="center">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="650" class="rpt_table" id="tbl_list_div">
            <?
            $i=1;
            foreach($sqlResult as $row )
            {
				if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
				
				$data=$i.'_'.$row[csf('booking_id')].'_'.$row[csf('booking_no')];
				//echo $data;
				?>
				<tr id="tr_<?php echo $i; ?>" bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"  onClick="js_set_value2('<? echo $data;?>')">
				<td width="30" align="center"><?php echo $i; ?>
				<td width="130"><p><? echo $company_arr[$row[csf('company_name')]]; ?></p></td>
				<td width="110"><p><? echo $buyer_arr[$row[csf('buyer_name')]]; ?></p></td>
				<td width="110"><p><? echo $row[csf('job_no')]; ?></p></td>
				<td width="120"><p><? echo $row[csf('style_ref_no')]; ?></p></td>
				<td width=""><p><? echo $row[csf('booking_no')]; ?></p></td>
				</tr>
				<?
				$i++;
            }
            ?>
            </table>
            </div>
            <table width="650" cellspacing="0" cellpadding="0" style="border:none" align="center">
                <tr>
                    <td align="center" height="30" valign="bottom">
                    <div style="width:100%">
                        <div style="width:50%; float:left" align="left">
                        <input type="checkbox" name="check_all" id="check_all" onClick="check_all_datas()"/>
                        Check / Uncheck All
                        </div>
                        <div style="width:50%; float:left" align="left">
                        <input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px"/>
                        </div>
                    </div>
                    </td>
                </tr>
            </table>
        </form>
    </fieldset>
    </div>
					
	<?
   exit(); 
}  

if($action=="report_generate2")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name");
	$count_arr=return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');
	$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name");
	
	$print_report_format=0;
	$print_report_format=return_field_value("format_id","lib_report_template","template_name =".$cbo_company_id." and module_id=7 and report_id=47 and is_deleted=0 and status_active=1");
	$imge_arr=return_library_array( "select master_tble_id, image_location from  common_photo_library where file_type=1",'master_tble_id','image_location');

	$company_name= str_replace("'","",$cbo_company_id);
	$bookingNo = str_replace("'", "", $txt_booking_no);
    $bookingId = str_replace("'", "", $txt_booking_id);

  

    if($bookingNo !="")
    {
    	if($bookingId!="") $bookingIdCond = " and a.id in($bookingId)"; else $bookingIdCond = "and a.booking_no_prefix_num in($bookingNo)";
    	$sql_booking = "SELECT b.po_break_down_id from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.item_category in(2,3,13) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $bookingIdCond";
    	$sql_res = sql_select($sql_booking);
    	$poIdArray = array();
    	foreach ($sql_res as $val) 
    	{
    		$poIdArray[$val[csf('po_break_down_id')]] = $val[csf('po_break_down_id')];
    	}
    	$bookingPoIds = implode(",", $poIdArray);
    }

    if($bookingPoIds !="")	// check booking po
	{
		$po_style_cond=" and b.id in($bookingPoIds)";
	}
	if(str_replace("'","",$cbo_buyer_id)==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else $buyer_id_cond="";
	}
	else $buyer_id_cond=" and a.buyer_name=$cbo_buyer_id";
	
	$booking_print_arr=array();
	$booking_print_sql=sql_select("select report_id, format_id from lib_report_template where template_name='$company_name' and module_id=2 and report_id in (1,2,3) and is_deleted=0 and status_active=1");
	foreach($booking_print_sql as $print_id)
	{
		$booking_print_arr[$print_id[csf('report_id')]]=(int) $print_id[csf('format_id')];
	}
	unset($booking_print_sql);
	//print_r($booking_print_arr); die;

	$print_report_format_ids2 = return_field_value("format_id","lib_report_template","template_name=".$company_name." and module_id=2 and report_id=1 and is_deleted=0 and status_active=1");
	$format_ids=explode(",",$print_report_format_ids2);

	$date_type = str_replace("'","",$cbo_search_by);
	$start_date=str_replace("'","",trim($txt_date_from));
	$end_date=str_replace("'","",trim($txt_date_to));
	if($date_type==1)
	{
		if ($start_date=="" && $end_date=="")
			$date_search_cond="";
		else
			$date_search_cond="and b.po_received_date between '$start_date' and '$end_date'";	
	}
	else
	{
		if ($start_date=="" && $end_date=="")
			$date_search_cond="";
		else
			$date_search_cond="and b.pub_shipment_date between '$start_date' and '$end_date'";
	}
	
	$cbo_year=str_replace("'","",$cbo_year);
	if(trim($cbo_year)!=0) 
	{
		if($db_type==0) $year_cond=" and YEAR(a.insert_date)=$cbo_year";
		else if($db_type==2) $year_cond=" and to_char(a.insert_date,'YYYY')=$cbo_year";
		else $year_cond="";
	}
	else $year_cond="";
	
	$txt_job_no=str_replace("'","",$txt_job_no);
	$job_no_cond="";
	if(trim($txt_job_no)!="")
	{
		$job_no=trim($txt_job_no); 
		$job_no_cond=" and a.job_no_prefix_num=$job_no";
	}
	
	$cbo_type=str_replace("'","",$cbo_type);
	$txt_search_string=str_replace("'","",$txt_search_string);

	$sql="SELECT a.company_name, a.buyer_name, a.job_no_prefix_num, a.job_no, a.style_ref_no, a.gmts_item_id, a.order_uom, a.total_set_qnty as ratio, b.grouping, b.file_no, b.id as po_id, b.po_number, b.po_quantity as po_qnty, b.pub_shipment_date, b.shiping_status, b.insert_date, b.po_received_date, b.plan_cut, b.is_confirmed from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name='$company_name'  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $buyer_id_cond $date_search_cond $year_cond $job_no_cond $po_style_cond group by a.company_name, a.buyer_name, a.job_no_prefix_num, a.job_no, a.style_ref_no, a.gmts_item_id, a.order_uom, a.total_set_qnty, b.grouping, b.file_no, b.id, b.po_number, b.po_quantity, b.pub_shipment_date, b.shiping_status, b.insert_date, b.po_received_date, b.plan_cut, b.is_confirmed order by b.pub_shipment_date, b.id";	
	//echo $sql; die;
	$nameArray=sql_select($sql);
	$po_data_arr=array();
	$job_data_arr=array();
	$job_allData_arr=array();
	$tot_rows=0;
	$poIds='';
	if(count($nameArray)>0)
	{
		foreach($nameArray as $row)
		{
			$tot_rows++;
			$poIds.=$row[csf("po_id")].",";
			
			$po_data_arr[$row[csf("po_id")]]=$row[csf("company_name")]."##".$row[csf("buyer_name")]."##".$row[csf("job_no_prefix_num")]."##".$row[csf("job_no")]."##".$row[csf("style_ref_no")]."##".$row[csf("gmts_item_id")]."##".$row[csf("order_uom")]."##".$row[csf("ratio")]."##".$row[csf("grouping")]."##".$row[csf("file_no")]."##".$row[csf("po_number")]."##".$row[csf("po_qnty")]."##".$row[csf("pub_shipment_date")]."##".$row[csf("shiping_status")]."##".$row[csf("insert_date")]."##".$row[csf("po_received_date")]."##".$row[csf("plan_cut")]."##".$row[csf("is_confirmed")];
		}
	}
	else
	{
		echo get_empty_data_msg();
		die;
	}
	unset($nameArray);

	$print_report_format2=return_field_value("format_id","lib_report_template","template_name =".$cbo_company_id." and module_id=5 and report_id=69 and is_deleted=0 and status_active=1");
	$printButton=explode(',',$print_report_format2);
	$print_type='';
	foreach($printButton as $id)
	{
		if($id==134)$print_type=4;
		else if($id==135)$print_type=6;
		else if($id==136)$print_type=7;
		else if($id==137)$print_type=8;
		else if($id==64)$print_type=9;
		break;
	}
	//echo $print_type;die;
	
	//print_r($job_allData_arr);
	//die;
	$poIds=chop($poIds,',');
	$poIds_country_cond="";
	$yarn_iss_po_cond="";
	$yarn_allo_po_cond="";
	$wo_po_cond="";
	$cons_po_cond="";
	if($db_type==2 && $tot_rows>1000)
	{
		$poIds_country_cond=" and (";
		$yarn_iss_po_cond=" and (";
		$yarn_allo_po_cond=" and (";
		$wo_po_cond=" and (";
			
		$cons_po_cond=" and (";
		
		$poIdsArr=array_chunk(explode(",",$poIds),999);
		foreach($poIdsArr as $ids)
		{
			$ids=implode(",",$ids);
			$poIds_country_cond.=" po_break_down_id in($ids) or ";
			$yarn_iss_po_cond.=" a.po_breakdown_id in($ids) or ";
			$yarn_allo_po_cond.=" a.po_break_down_id in($ids) or ";
			
			$wo_po_cond.=" b.po_break_down_id in($ids) or ";
			$cons_po_cond.=" b.po_break_down_id in($ids) or ";
		}
		$poIds_country_cond=chop($poIds_country_cond,'or ');
		$poIds_country_cond.=")";
		
		$yarn_iss_po_cond=chop($yarn_iss_po_cond,'or ');
		$yarn_iss_po_cond.=")";
		
		$yarn_allo_po_cond=chop($yarn_allo_po_cond,'or ');
		$yarn_allo_po_cond.=")";
		
		$wo_po_cond=chop($wo_po_cond,'or ');
		$wo_po_cond.=")";
		
		$cons_po_cond=chop($cons_po_cond,'or ');
		$cons_po_cond.=")";
	}
	else
	{
		$poIds_country_cond=" and po_break_down_id in ($poIds)";
		$yarn_iss_po_cond=" and a.po_breakdown_id in ($poIds)";
		$yarn_allo_po_cond=" and a.po_break_down_id in ($poIds)";
		$wo_po_cond=" and b.po_break_down_id in ($poIds)";
		$cons_po_cond=" and b.po_break_down_id in ($poIds)";
	}
	
	$dataArrayYarn=array(); 
	$sql_yarn_iss="select a.po_breakdown_id, b.yarn_count_id, b.yarn_comp_type1st, b.yarn_comp_percent1st, b.yarn_comp_type2nd, b.yarn_comp_percent2nd, b.yarn_type,a.trans_id, 
			sum(CASE WHEN a.entry_form ='3' THEN a.quantity ELSE 0 END) AS issue_qnty,
			sum(CASE WHEN a.entry_form ='9' THEN a.quantity ELSE 0 END) AS return_qnty
			from order_wise_pro_details a, product_details_master b where a.prod_id=b.id and b.item_category_id=1 and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1 and a.issue_purpose in (1,4) $yarn_iss_po_cond group by a.po_breakdown_id, b.yarn_count_id, b.yarn_comp_type1st, b.yarn_comp_percent1st, b.yarn_comp_type2nd, b.yarn_comp_percent2nd, b.yarn_type,a.trans_id";
	$dataArrayIssue=sql_select($sql_yarn_iss);
	//echo $sql_yarn_iss;
	foreach($dataArrayIssue as $row_yarn_iss)
	{
		$ystr=$row_yarn_iss[csf('yarn_count_id')].'--'.$row_yarn_iss[csf('yarn_comp_type1st')].'--'.$row_yarn_iss[csf('yarn_comp_percent1st')].'--'.$row_yarn_iss[csf('yarn_type')];
		$dataArrayYarn[$row_yarn_iss[csf('po_breakdown_id')]][$ystr]['issQty']+=$row_yarn_iss[csf('issue_qnty')];
		$dataArrayYarn[$row_yarn_iss[csf('po_breakdown_id')]][$ystr]['retQty']+=$row_yarn_iss[csf('return_qnty')];
		if($row_yarn_iss[csf('return_qnty')]>0)
		{

			$dataArrayYarn[$row_yarn_iss[csf('po_breakdown_id')]][$ystr]['trans_id']=$row_yarn_iss[csf('trans_id')];
		}
	}
	unset($dataArrayIssue);
	//print_r($dataArrayYarnIssue);die;
	$yarnAllocationArr=array(); //$yarnAllocationJobArr=array();
	$sql_yarn_allocation="select a.po_break_down_id, b.yarn_count_id, b.yarn_comp_type1st, b.yarn_comp_percent1st, b.yarn_comp_type2nd, b.yarn_comp_percent2nd, b.yarn_type, 
			sum(a.qnty) AS allocation_qty
			from inv_material_allocation_dtls a, product_details_master b where a.item_id=b.id and b.item_category_id=1 and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1 $yarn_allo_po_cond group by a.po_break_down_id, b.yarn_count_id, b.yarn_comp_type1st, b.yarn_comp_percent1st, b.yarn_comp_type2nd, b.yarn_comp_percent2nd, b.yarn_type";
	$dataArrayAllocation=sql_select($sql_yarn_allocation);
	//echo $sql_yarn_allocation;die;
	foreach($dataArrayAllocation as $allocationRow)
	{
		$yastr=$allocationRow[csf('yarn_count_id')].'--'.$allocationRow[csf('yarn_comp_type1st')].'--'.$allocationRow[csf('yarn_comp_percent1st')].'--'.$allocationRow[csf('yarn_type')];
		$dataArrayYarn[$allocationRow[csf('po_break_down_id')]][$yastr]['allQty']+=$allocationRow[csf('allocation_qty')];
	}
	unset($dataArrayAllocation);
	//print_r($yarnAllocationArr);die;
	
	$dataArrayWo=array(); $fab_source_arr=array();
	$sql_wo="select b.po_break_down_id, a.id, a.booking_no, a.insert_date, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.booking_no_prefix_num, a.job_no, a.is_short, a.is_approved, a.po_break_down_id as wo_po_id, b.fabric_color_id, sum(b.fin_fab_qnty) as req_qnty, sum(b.grey_fab_qnty) as grey_req_qnty from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.item_category in(2,3,13) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $wo_po_cond group by b.po_break_down_id, a.id, a.booking_no, a.insert_date, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.booking_no_prefix_num, a.job_no, a.is_short, a.is_approved, a.po_break_down_id, b.fabric_color_id";
	$resultWo=sql_select($sql_wo);
	foreach($resultWo as $woRow)
	{
		$dataArrayWo[$woRow[csf('po_break_down_id')]].=$woRow[csf('id')]."**".$woRow[csf('booking_no')]."**".$woRow[csf('insert_date')]."**".$woRow[csf('item_category')]."**".$woRow[csf('fabric_source')]."**".$woRow[csf('company_id')]."**".$woRow[csf('booking_type')]."**".$woRow[csf('booking_no_prefix_num')]."**".$woRow[csf('job_no')]."**".$woRow[csf('is_short')]."**".$woRow[csf('is_approved')]."**".$woRow[csf('fabric_color_id')]."**".$woRow[csf('req_qnty')]."**".$woRow[csf('grey_req_qnty')]."**".$woRow[csf('wo_po_id')]."__";
	}
	unset($resultWo);
	
	$table_width="1575"; $colspan="17";
	ob_start();
	?>
    <fieldset style="width:100%">	
        <table cellpadding="0" cellspacing="0" width="<?=$table_width; ?>" border="0" rules="all">
            <tr class="form_caption">
               <td align="center" width="100%" colspan="<?=$colspan; ?>" style="font-size:16px"><strong><?=$company_arr[$company_name]; ?></strong></td>
            </tr>
            <tr class="form_caption">
               <td align="center" width="100%" colspan="<?=$colspan; ?>" style="font-size:16px"><strong><?=$report_title; ?></strong></td>
            </tr>
        </table>
        <table class="rpt_table" border="1" rules="all" width="<?=$table_width; ?>" cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                	<th width="40">SL</th>
                    <th width="125">Fabric Booking No</th>
                    <th width="125">Yarn Purchase<br>Requisition</th>
                    <th width="100">Job Number</th>
                    <th width="120">Order Number</th>
                    <th width="80">Buyer Name</th>
                    <th width="130">Style Ref.</th>
                    <th width="80">Pub. Ship. Date</th>
                    <th width="80" title="Total Grey Req. Qty/ Plancut Qty. (Pcs.)">Avg Grey Cons./Pcs</th>
                    <th width="70">Count</th>
                    <th width="110">Composition</th>
                    <th width="80">Type</th>
                    <th width="80">Required<br/><font style="font-size:9px; font-weight:80">(As Per Pre-Cost)</font></th>
                    <th width="80">Allocated</th>
                    <th width="80">Issued</th>
                    <th width="80">Issue Return</th>
                    <th>Balance<br/><font style="font-size:8px; font-weight:80">{(Required As Per Pre-Cost - Issued) + Issue Return}</font></th>
                </tr>

            </thead>
        </table>
        <div style="width:<?=$table_width; ?>px; overflow-y:scroll; max-height:400px;" id="scroll_body">
            <table width="<?=$table_width-18; ?>" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_body" >
            <? 
			$condition= new condition();
			$condition->company_name("=$company_name");
			if(str_replace("'","",$cbo_buyer_name)>0){
				$condition->buyer_name("=$cbo_buyer_id");
			}
			if(str_replace("'","",$txt_job_no) !=''){
				$condition->job_no_prefix_num("=$txt_job_no");
			}
			else if($date_type==1)
			{
				if ($start_date=="" && $end_date=="") $condition->po_received_date(" between '$start_date' and '$end_date'");
			}
			else 
			{
				if ($start_date=="" && $end_date=="") $condition->pub_shipment_date(" between '$start_date' and '$end_date'");
			}
			
			$condition->init();
			$yarn= new yarn($condition);
			$yarn_des_data=$yarn->getOrderCountCompositionPercentAndTypeWiseYarnQtyArray();
			//print_r($yarn_des_data);die;
			
			$fabric= new fabric($condition);
			//echo $fabric->getQuery(); die;
			$fabric_costing_arr=$fabric->getQtyArray_by_order_knitAndwoven_greyAndfinish();
			
			//die;
			$k=1; $i=1; $buyerWiseQtyArr=array();
			foreach($po_data_arr as $po_id=>$other_data)
			{
				$ex_data=explode('##',$other_data);
				$company_id=''; $buyer_name='';  $job_no_prefix_num=''; $job_no=''; $style_ref_no=''; $gmts_item_id=''; $order_uom=''; $ratio=''; $grouping=''; $file_no=''; $po_number=''; $po_qnty=''; $pub_shipment_date=''; $shiping_status=''; $insert_date=''; $po_received_date=''; $plan_cut=''; $is_confirmed='';// $po_id
				$company_id=$ex_data[0];
				$buyer_name=$ex_data[1];
				$job_no_prefix_num=$ex_data[2];
				$job_no=$ex_data[3];
				$job_id=$ex_data[3];
				$style_ref_no=$ex_data[4];
				$gmts_item_id=$ex_data[5];
				$order_uom=$ex_data[6];
				$ratio=$ex_data[7];
				$grouping=$ex_data[8];
				$file_no=$ex_data[9];
				$po_number=$ex_data[10];
				$po_qnty=$ex_data[11];
				$pub_shipment_date=$ex_data[12];
				$shiping_status=$ex_data[13];
				$insert_date=$ex_data[14];
				$po_received_date=$ex_data[15];
				$plan_cut=$ex_data[16];
				$is_confirmed=$ex_data[17];
				
				$template_id=$template_id_arr[$po_id];
				
				$order_qnty_in_pcs=$po_qnty*$ratio;
				$plan_cut_qnty=$plan_cut*$ratio;
				$order_qty_array[$buyer_name]+=$order_qnty_in_pcs;
				$gmts_item='';
				$gmts_item_id=explode(",",$gmts_item_id);
				foreach($gmts_item_id as $item_id)
				{
					if($gmts_item=="") $gmts_item=$garments_item[$item_id]; else $gmts_item.=",".$garments_item[$item_id];
				}
				
				$dzn_qnty=0; $balance=0; $job_mkt_required=0; $yarn_issued=0;
				if($costing_per_id_library[$job_no]==1) $dzn_qnty=12;
				else if($costing_per_id_library[$job_no]==3) $dzn_qnty=12*2;
				else if($costing_per_id_library[$job_no]==4) $dzn_qnty=12*3;
				else if($costing_per_id_library[$job_no]==5) $dzn_qnty=12*4;
				else $dzn_qnty=1;
				
				$dzn_qnty=$dzn_qnty*$ratio;
				
				$buyer_name_array[$buyer_name]=$buyer_arr[$buyer_name];
				$grey_cons=$fabric_costing_arr['knit']['grey'][$po_id]/$plan_cut_qnty;
				
				$booking_array=array(); $color_data_array=array(); $grey_req_color_arr=array(); $fabric_source_arr=array();
				$required_qnty=0; $main_booking=''; $sample_booking=''; 
				$dataArray=array_filter(explode("__",$dataArrayWo[$po_id]));
				if(count($dataArray)>0)
				{
					foreach($dataArray as $woRow)
					{
						$woRow=explode("**",$woRow);
						$id=$woRow[0];
						$booking_no=$woRow[1];
						$insert_date=$woRow[2];
						$item_category=$woRow[3];
						$fabric_source=$woRow[4];
						$company_id=$woRow[5];
						$booking_type=$woRow[6];
						$booking_no_prefix_num=$woRow[7];
						$job_no=$woRow[8];
						$is_short=$woRow[9];
						$is_approved=$woRow[10];
						$fabric_color_id=$woRow[11];
						$req_qnty=$woRow[12];
						$grey_req_qnty=$woRow[13];
						$wo_po_id=$woRow[14];
						$book_prefix_no = $woRow[7];

						$row_id=$format_ids[0];
						
						//if($row[csf('booking_type')]==1 && $row[csf('entry_form')]==118) $row_id=$format_ids[0];


						if($fabric_source==1) 
						{
							$grey_req_qnty=$grey_req_qnty;  $req_qnty=$req_qnty;
						}
						else 
						{
							//$grey_req_qnty=$req_qnty=0;
						}
						$required_qnty+=$grey_req_qnty;

						if(!in_array($id,$booking_array))
						{
							$system_date=date('d-M-Y', strtotime($insert_date));
							$wo_color = "";
							if ($fabric_source == 2) $wo_color = "color:#000"; else $wo_color = "";
							
							if($booking_type==4)
							{
								$action_name='show_fabric_booking_report'; 
								$sample_booking.="<a href='##' style='color:#000; text-decoration: none;' onclick=\"generate_worder_report('3','".$booking_no."','".$company_id."','".$wo_po_id."','".$item_category."','".$fabric_source."','".$job_no."','".$is_approved."','".$action_name."','".$print_report_format."')\"><font style='font-weight:bold' color='$wo_color;'>".$booking_no."(".$system_date.")"."</font></a>";
							}
							else
							{
								$all_book_prefix_no .= $book_prefix_no . ",";
								if($is_short==1) 
								{
									$pre="S,";
									$action_name=$report_format_arr[$booking_print_arr[2]];
								}
								else 
								{
									$pre="M,"; 
									$action_name=$report_format_arr[$booking_print_arr[1]];
								}
								if($action_name=='') $action_name='show_fabric_booking_report';
								//if($is_short==1) $pre="S"; else $pre="M"; 
								
								//$main_booking.="<a href='##' style='color:#000; text-decoration: none;' onclick=\"generate_worder_report('".$is_short."','".$booking_no."','".$company_id."','".$wo_po_id."','".$item_category."','".$fabric_source."','".$job_no."','".$is_approved."','".$action_name."','".$print_report_format."')\"><font style='font-weight:bold' color='$wo_color;'>".$booking_no."(".$system_date.")".$pre."</font></a>";

								if($format_ids[0]==719)
								{
									
									
									$main_booking.="<a href='##' style='color:#000; text-decoration: none;' onclick=\"generate_worder_report('".$is_short."','".$booking_no."','".$company_id."','".$po_id."','".$item_category."','".$fabric_source."','".$job_id."','".$is_approved."','show_fabric_booking_report16','".$print_report_format."')\"><font style='font-weight:bold' color='$wo_color;'>".$booking_no."(".$system_date.")".$pre."</font></a>";
								}
								else if($format_ids[0]==1)
								{
									
									$main_booking.="<a href='##' style='color:#000; text-decoration: none;' onclick=\"generate_worder_report('".$is_short."','".$booking_no."','".$company_id."','".$po_id."','".$item_category."','".$fabric_source."','".$job_id."','".$is_approved."','show_fabric_booking_report_gr','".$print_report_format."')\"><font style='font-weight:bold' color='$wo_color;'>".$booking_no."(".$system_date.")".$pre."</font></a>";
								}
								else if($format_ids[0]==2)
								{
									

									$main_booking.="<a href='##' style='color:#000; text-decoration: none;' onclick=\"generate_worder_report('".$is_short."','".$booking_no."','".$company_id."','".$po_id."','".$item_category."','".$fabric_source."','".$job_id."','".$is_approved."','show_fabric_booking_report','".$print_report_format."')\"><font style='font-weight:bold' color='$wo_color;'>".$booking_no."(".$system_date.")".$pre."</font></a>";
								}
								else if($format_ids[0]==3)
								{
									

									$main_booking.="<a href='##' style='color:#000; text-decoration: none;' onclick=\"generate_worder_report('".$is_short."','".$booking_no."','".$company_id."','".$po_id."','".$item_category."','".$fabric_source."','".$job_id."','".$is_approved."','show_fabric_booking_report3','".$print_report_format."')\"><font style='font-weight:bold' color='$wo_color;'>".$booking_no."(".$system_date.")".$pre."</font></a>";
								}
								else if($format_ids[0]==4)
								{
									
									$main_booking.="<a href='##' style='color:#000; text-decoration: none;' onclick=\"generate_worder_report('".$is_short."','".$booking_no."','".$company_id."','".$po_id."','".$item_category."','".$fabric_source."','".$job_id."','".$is_approved."','show_fabric_booking_report1','".$print_report_format."')\"><font style='font-weight:bold' color='$wo_color;'>".$booking_no."(".$system_date.")".$pre."</font></a>";
								}
								else if($format_ids[0]==5)
								{
									
									$main_booking.="<a href='##' style='color:#000; text-decoration: none;' onclick=\"generate_worder_report('".$is_short."','".$booking_no."','".$company_id."','".$po_id."','".$item_category."','".$fabric_source."','".$job_id."','".$is_approved."','show_fabric_booking_report2','".$print_report_format."')\"><font style='font-weight:bold' color='$wo_color;'>".$booking_no."(".$system_date.")".$pre."</font></a>";
								}
								else if($format_ids[0]==6)
								{
									
									$main_booking.="<a href='##' style='color:#000; text-decoration: none;' onclick=\"generate_worder_report('".$is_short."','".$booking_no."','".$company_id."','".$po_id."','".$item_category."','".$fabric_source."','".$job_id."','".$is_approved."','show_fabric_booking_report4','".$print_report_format."')\"><font style='font-weight:bold' color='$wo_color;'>".$booking_no."(".$system_date.")".$pre."</font></a>";
								}
								else if($format_ids[0]==7)
								{
									
									$main_booking.="<a href='##' style='color:#000; text-decoration: none;' onclick=\"generate_worder_report('".$is_short."','".$booking_no."','".$company_id."','".$po_id."','".$item_category."','".$fabric_source."','".$job_id."','".$is_approved."','show_fabric_booking_report5','".$print_report_format."')\"><font style='font-weight:bold' color='$wo_color;'>".$booking_no."(".$system_date.")".$pre."</font></a>";
								}
								else if($format_ids[0]==28)
								{
								
									$main_booking.="<a href='##' style='color:#000; text-decoration: none;' onclick=\"generate_worder_report('".$is_short."','".$booking_no."','".$company_id."','".$po_id."','".$item_category."','".$fabric_source."','".$job_id."','".$is_approved."','show_fabric_booking_report_akh','".$print_report_format."')\"><font style='font-weight:bold' color='$wo_color;'>".$booking_no."(".$system_date.")".$pre."</font></a>";
								}
								else if($format_ids[0]==45)
								{
								
									$main_booking.="<a href='##' style='color:#000; text-decoration: none;' onclick=\"generate_worder_report('".$is_short."','".$booking_no."','".$company_id."','".$po_id."','".$item_category."','".$fabric_source."','".$job_id."','".$is_approved."','show_fabric_booking_report_urmi','".$print_report_format."')\"><font style='font-weight:bold' color='$wo_color;'>".$booking_no."(".$system_date.")".$pre."</font></a>";
								}
								else if($format_ids[0]==53)
								{
								
									$main_booking.="<a href='##' style='color:#000; text-decoration: none;' onclick=\"generate_worder_report('".$is_short."','".$booking_no."','".$company_id."','".$po_id."','".$item_category."','".$fabric_source."','".$job_id."','".$is_approved."','show_fabric_booking_report_jk','".$print_report_format."')\"><font style='font-weight:bold' color='$wo_color;'>".$booking_no."(".$system_date.")".$pre."</font></a>";
								}
								else if($format_ids[0]==73)
								{
								
									$main_booking.="<a href='##' style='color:#000; text-decoration: none;' onclick=\"generate_worder_report('".$is_short."','".$booking_no."','".$company_id."','".$po_id."','".$item_category."','".$fabric_source."','".$job_id."','".$is_approved."','show_fabric_booking_report_mf','".$print_report_format."')\"><font style='font-weight:bold' color='$wo_color;'>".$booking_no."(".$system_date.")".$pre."</font></a>";
								}
								else if($format_ids[0]==84)
								{
								
									$main_booking.="<a href='##' style='color:#000; text-decoration: none;' onclick=\"generate_worder_report('".$is_short."','".$booking_no."','".$company_id."','".$po_id."','".$item_category."','".$fabric_source."','".$job_id."','".$is_approved."','show_fabric_booking_report_islam','".$print_report_format."')\"><font style='font-weight:bold' color='$wo_color;'>".$booking_no."(".$system_date.")".$pre."</font></a>";
								}
								else if($format_ids[0]==93)
								{
								
									$main_booking.="<a href='##' style='color:#000; text-decoration: none;' onclick=\"generate_worder_report('".$is_short."','".$booking_no."','".$company_id."','".$po_id."','".$item_category."','".$fabric_source."','".$job_id."','".$is_approved."','show_fabric_booking_report_libas','".$print_report_format."')\"><font style='font-weight:bold' color='$wo_color;'>".$booking_no."(".$system_date.")".$pre."</font></a>";
								}
								else if($format_ids[0]==129)
								{
								
									$main_booking.="<a href='##' style='color:#000; text-decoration: none;' onclick=\"generate_worder_report('".$is_short."','".$booking_no."','".$company_id."','".$po_id."','".$item_category."','".$fabric_source."','".$job_id."','".$is_approved."','show_fabric_booking_report_print5','".$print_report_format."')\"><font style='font-weight:bold' color='$wo_color;'>".$booking_no."(".$system_date.")".$pre."</font></a>";
								}
								else if($format_ids[0]==193)
								{
								
									$main_booking.="<a href='##' style='color:#000; text-decoration: none;' onclick=\"generate_worder_report('".$is_short."','".$booking_no."','".$company_id."','".$po_id."','".$item_category."','".$fabric_source."','".$job_id."','".$is_approved."','show_fabric_booking_report_print4','".$print_report_format."')\"><font style='font-weight:bold' color='$wo_color;'>".$booking_no."(".$system_date.")".$pre."</font></a>";
								}
								else if($format_ids[0]==269)
								{
								
									$main_booking.="<a href='##' style='color:#000; text-decoration: none;' onclick=\"generate_worder_report('".$is_short."','".$booking_no."','".$company_id."','".$po_id."','".$item_category."','".$fabric_source."','".$job_id."','".$is_approved."','show_fabric_booking_report_knit','".$print_report_format."')\"><font style='font-weight:bold' color='$wo_color;'>".$booking_no."(".$system_date.")".$pre."</font></a>";
								}
								else if($format_ids[0]==280)
								{
								
									$main_booking.="<a href='##' style='color:#000; text-decoration: none;' onclick=\"generate_worder_report('".$is_short."','".$booking_no."','".$company_id."','".$po_id."','".$item_category."','".$fabric_source."','".$job_id."','".$is_approved."','show_fabric_booking_report_print14','".$print_report_format."')\"><font style='font-weight:bold' color='$wo_color;'>".$booking_no."(".$system_date.")".$pre."</font></a>";
								}
								else if($format_ids[0]==39)
								{
								
									$main_booking.="<a href='##' style='color:#000; text-decoration: none;' onclick=\"generate_worder_report('".$is_short."','".$booking_no."','".$company_id."','".$po_id."','".$item_category."','".$fabric_source."','".$job_id."','".$is_approved."','show_fabric_booking_report_print39','".$print_report_format."')\"><font style='font-weight:bold' color='$wo_color;'>".$booking_no."(".$system_date.")".$pre."</font></a>";
								}

								else if($format_ids[0]==304)
								{
								
									$main_booking.="<a href='##' style='color:#000; text-decoration: none;' onclick=\"generate_worder_report('".$is_short."','".$booking_no."','".$company_id."','".$po_id."','".$item_category."','".$fabric_source."','".$job_id."','".$is_approved."','show_fabric_booking_report10','".$print_report_format."')\"><font style='font-weight:bold' color='$wo_color;'>".$booking_no."(".$system_date.")".$pre."</font></a>";
								}
								else if($format_ids[0]==723)
								{
								
									$main_booking.="<a href='##' style='color:#000; text-decoration: none;' onclick=\"generate_worder_report('".$is_short."','".$booking_no."','".$company_id."','".$po_id."','".$item_category."','".$fabric_source."','".$job_id."','".$is_approved."','show_fabric_booking_report17','".$print_report_format."')\"><font style='font-weight:bold' color='$wo_color;'>".$booking_no."(".$system_date.")".$pre."</font></a>";
								}

							}
							$booking_array[]=$id;
						}
					}
				}
				else
				{
					$main_booking.="No Booking";
					$main_booking_excel.="No Booking";
					$sample_booking.="No Booking";
					$sample_booking_excel.="No Booking";
					$all_book_prefix_no = "&nbsp;";
				}
				
				if($main_booking=="")
				{
					$main_booking.="No Booking";
					$main_booking_excel.="No Booking";
				}
				
				if($sample_booking=="") 
				{
					$sample_booking.="No Booking";
					$sample_booking_excel.="No Booking";
				}
				
				$yarn_descrip_data = array();
				$yarn_descrip_data = $yarn_des_data[$po_id];
				//BOM DATA
				/*echo "<pre>";
				print_r($yarn_descrip_data);
				echo "</pre>";
				die;*/
				$qnty=0; $bomReqYarn_arr=array();
				foreach($yarn_descrip_data as $count=>$count_value)
				{
					foreach($count_value as $Composition=>$composition_value)
					{
						foreach($composition_value as $percent=>$percent_value)
						{	
							foreach($percent_value as $type_ref=>$bomReq)
							{
								$count_id=$count;//$yarnRow[0];
								$copm_one_id=$Composition;//$yarnRow[1];
								$percent_one=$percent;//$yarnRow[2];
								$type_id=$type_ref;//$yarnRow[5];
								$yarnstr=$count_id.'--'.$copm_one_id.'--'.$percent_one.'--'.$type_id;
								$bomReqYarn_arr[]=$yarnstr;
								if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

								
								$allocation_qty=$dataArrayYarn[$po_id][$yarnstr]['allQty'];
								$issQty=$dataArrayYarn[$po_id][$yarnstr]['issQty'];
								$retQty=$dataArrayYarn[$po_id][$yarnstr]['retQty'];


								
								
								$balance=($bomReq-$issQty)+$retQty;
								?>
                                <tr bgcolor="<?=$bgcolor; ?>" onClick="change_color('tr_<?=$k; ?>','<?=$bgcolor; ?>')" id="tr_<?=$k; ?>">
                                    <td width="40"><?=$i; ?></td>
                                    <td width="125">
                                    	<? 


                                    	//echo $variable;
                                    		if($main_booking!="No Booking"){
												echo $main_booking; 
												if($sample_booking!="No Booking"){
													echo "<br/>";
													echo $sample_booking;
												}
											}
											else if($sample_booking!="No Booking"){
												echo $sample_booking;
											}else{
												echo $main_booking; 
											}
 									?></td>
                                    <td width="125">
                                    	<?php 
                                    		$sql_req="select a.id,a.is_approved,a.requ_no,b.job_id,b.job_no,b.color_id,b.count_id,b.composition_id,b.com_percent,b.yarn_type_id,b.booking_no,b.quantity from inv_purchase_requisition_mst a,inv_purchase_requisition_dtls b where a.id=b.mst_id and b.status_active=1 and b.is_deleted=0 and b.job_no='".$job_no."'" ;
                                    		$res_req=sql_select($sql_req);
                                    		if(count($res_req)){
                                    			
                                    			$req_print="<a href='##' style='color:#000; text-decoration: none;' onclick=\"fnc_yarn_req_entry('".$print_type."','".$company_id."','".$res_req[0][csf('id')]."','".$row[0][csf('is_approved')]."')\"><font style='font-weight:bold' color='$wo_color;'>".$res_req[0][csf('requ_no')]."</font></a>";
                                    			echo $req_print;
                                    			
                                    		}
                                    		unset($sql_req);
                                    		unset($res_req);
                                    	?>
                                    </td>
                                    <td width="100" align="center"><?=$job_no; ?></td>
                                    <td width="120"><?=$po_number; ?></td>
                                    <td width="80"><p><?=$buyer_arr[$buyer_name]; ?></p></td>
                                    <td width="130"><p><?=$style_ref_no; ?></p></td>
                                    <td width="80" align="center">&nbsp;<?=change_date_format($pub_shipment_date); ?></td>
                                    <td width="80" align="right" title="<?=$fabric_costing_arr['knit']['grey'][$po_id]."/".$plan_cut_qnty; ?>"><?=number_format($grey_cons,5,'.',''); ?></td>                                  
                                    <td width="70" style="mso-number-format:'\@';"><?=$count_arr[$count_id]; ?>&nbsp;</td>
                                    <td width="110"><?=$compos=$composition[$copm_one_id]." ".$percent_one." %"." ".$composition[$copm_two_id]; ?></td>
                                    <td width="80"><?=$yarn_type[$type_id]; ?></td>
                                    <td width="80" align="right"><?=number_format($bomReq,2,'.',''); ?></td>
                                    <td width="80" align="right"><?=number_format($allocation_qty,2,'.',''); ?></td>
                                    <td width="80" align="right">
                                    	<a href="##" onClick="openmypage('<? echo $po_id; ?>','yarn_issue','<? echo $count_id; ?>','<? echo $copm_one_id; ?>','<? echo $percent_one; ?>','','','<? echo $type_id; ?>','')"><? echo number_format($issQty,2,'.','');?>
                                    		
                                    	</a>
                                    	
                                    		
                                    </td>
                                    <td width="80" align="right">
                                    	<?php 
                                    		$trans_id='';
                                    		if($retQty>0)
											{

												$trans_id=$dataArrayYarn[$po_id][$yarnstr]['trans_id'];
											}
										?>
                                    	<a href="##" onClick="openmypage('<? echo $po_id; ?>','yarn_return','<? echo $count_id; ?>','<? echo $copm_one_id; ?>','<? echo $percent_one; ?>','','','<? echo $type_id; ?>','<? echo $trans_id;?>')"><? echo number_format($retQty,2,'.','');?>
                                    		
                                    	</a>
                                    	
                                    		
                                    	</td>
                                    <td align="right" title="Grey Req.-(Yarn Issue+Net Transfer)"><?=number_format($balance,2,'.',''); ?></td>
                                </tr>
                            	<?
								
								$buyerWiseQtyArr[$buyer_name]['req']+=$bomReq;
								$buyerWiseQtyArr[$buyer_name]['allo']+=$allocation_qty;
								$buyerWiseQtyArr[$buyer_name]['issue']+=$issQty;
								$buyerWiseQtyArr[$buyer_name]['return']+=$retQty;
								$buyerWiseQtyArr[$buyer_name]['balance']+=$balance;
								
								$grandReq+=$bomReq;
								$grandAllocation+=$allocation_qty;
								$grandIssue+=$issQty;
								$grandReturn+=$retQty;
								$grandBal+=$balance;
								$i++; $k++;
								
							}
						}
					}
				}
				//Issue Qty and Allocation Qty
				foreach($dataArrayYarn[$po_id] as $ystrdata=>$isQty)
				{
					if(!in_array($ystrdata,$bomReqYarn_arr))
					{
						// echo "<pre>";
						// echo $isQty['retQty'];
						// echo "</pre>";
						$exydata=explode('--',$ystrdata);
						$count_id=$exydata[0];
						$copm_one_id=$exydata[1];
						$percent_one=$exydata[2];
						$type_id=$exydata[3];
						$yarnstr=$ystrdata;//$count_id.'--'.$copm_one_id.'--'.$percent_one.'--'.$type_id;
						$bomReqYarn_arr[]=$ystrdata;
						$bomReq=0;
						$allocation_qty=$isQty['allQty'];
						$retQty=$isQty['retQty'];
						
						$balance=($bomReq-$isQty['issQty'])+$retQty;
						
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						?>
						<tr bgcolor="<?=$bgcolor; ?>" onClick="change_color('tr_<?=$k; ?>','<?=$bgcolor; ?>')" id="tr_<?=$k; ?>">
							<td width="40"><?=$i; ?></td>
							<td width="125"><?
							if($main_booking!="No Booking"){
								echo $main_booking; 
								if($sample_booking!="No Booking"){
									echo "<br/>";
									echo $sample_booking;
								}
							}
							else if($sample_booking!="No Booking"){
								echo $sample_booking;
							}else{
								echo $main_booking; 
							}

							?></td>
							<td width="125">
								<?php 
                            		$sql_req="select a.id,a.is_approved,a.requ_no,b.job_id,b.job_no,b.color_id,b.count_id,b.composition_id,b.com_percent,b.yarn_type_id,b.booking_no,b.quantity from inv_purchase_requisition_mst a,inv_purchase_requisition_dtls b where a.id=b.mst_id and b.status_active=1 and b.is_deleted=0 and b.job_no='".$job_no."'" ;
                            		$res_req=sql_select($sql_req);
                            		if(count($res_req)){
                            			
                            			$req_print="<a href='##' style='color:#000; text-decoration: none;' onclick=\"fnc_yarn_req_entry('".$print_type."','".$company_id."','".$res_req[0][csf('id')]."','".$row[0][csf('is_approved')]."')\"><font style='font-weight:bold' color='$wo_color;'>".$res_req[0][csf('requ_no')]."</font></a>";
                            			echo $req_print;
                            			
                            		}
                            		unset($sql_req);
                            		unset($res_req);
                            	?>
							</td>
							<td width="100" align="center"><?=$job_no; ?></td>
							<td width="120"><?=$po_number; ?></td>
							<td width="80"><p><?=$buyer_arr[$buyer_name]; ?></p></td>
							<td width="130"><p><?=$style_ref_no; ?></p></td>
							<td width="80" align="center">&nbsp;<?=change_date_format($pub_shipment_date); ?></td>
							<td width="80" align="right" title="<?=$fabric_costing_arr['knit']['grey'][$po_id]."/".$plan_cut_qnty; ?>"><?=number_format($grey_cons,5,'.',''); ?></td>                                  
							<td width="70" style="mso-number-format:'\@';"><?=$count_arr[$count_id]; ?>&nbsp;</td>
							<td width="110"><?=$compos=$composition[$copm_one_id]." ".$percent_one." %"." ".$composition[$copm_two_id]; ?></td>
							<td width="80"><?=$yarn_type[$type_id]; ?></td>
							<td width="80" align="right"><?=number_format($bomReq,2,'.',''); ?></td>
							<td width="80" align="right"><?=number_format($allocation_qty,2,'.',''); ?></td>
							<td width="80" align="right">
								<a href="##" onClick="openmypage('<? echo $po_id; ?>','yarn_issue','<? echo $count_id; ?>','<? echo $copm_one_id; ?>','<? echo $percent_one; ?>','','','<? echo $type_id; ?>','')"><? echo number_format($isQty['issQty'],2,'.','');?>
									
								</a>
							</td>
							<td width="80" align="right">
								<?php 
									$trans_id='';
									if($isQty['retQty']>0)
									{
										$trans_id=$isQty['trans_id'];

									} 
								?>
								<a href="##" onClick="openmypage('<? echo $po_id; ?>','yarn_return','<? echo $count_id; ?>','<? echo $copm_one_id; ?>','<? echo $percent_one; ?>','','','<? echo $type_id; ?>','<? echo $trans_id;?>')"><? echo number_format($isQty['retQty'],2,'.','');?>
									
								</a>
								
									
								</td>
							<td align="right" title="Grey Req.-(Yarn Issue+Net Transfer)"><?=number_format($balance,2,'.',''); ?></td>
						</tr>
						<?
						$buyerWiseQtyArr[$buyer_name]['req']+=$bomReq;
						$buyerWiseQtyArr[$buyer_name]['allo']+=$allocation_qty;
						$buyerWiseQtyArr[$buyer_name]['issue']+=$isQty['issQty'];
						$buyerWiseQtyArr[$buyer_name]['return']+=$retQty;
						$buyerWiseQtyArr[$buyer_name]['balance']+=$balance;
						
						$grandReq+=$bomReq;
						$grandAllocation+=$allocation_qty;
						$grandIssue+=$isQty['issQty'];
						$grandReturn+=$retQty;
						$grandBal+=$balance;
						$i++; $k++;
						
					}
				}
			}
			?>
            </table>
        </div>
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<?=$table_width; ?>" class="tbl_bottom">
            <tr style="font-size:13px">
                <td width="40">&nbsp;</td>
                <td width="125">&nbsp;</td>
                <td width="125">&nbsp;</td>
                <td width="100">&nbsp;</td>
                <td width="120">&nbsp;</td>
                <td width="80">&nbsp;</td>
                <td width="130">&nbsp;</td>
                <td width="80">&nbsp;</td>
                <td width="80">&nbsp;</td>
                <td width="70">&nbsp;</td>
                <td width="110">Total:</td>
                <td width="80">&nbsp;</td>
                <td width="80" id="value_td_req" align="right"><?=number_format($grandReq,2,'.',''); ?></td>
                <td width="80" id="value_td_all" align="right"><?=number_format($grandAllocation,2,'.',''); ?></td>
                <td width="80" id="value_td_iss" align="right"><?=number_format($grandIssue,2,'.',''); ?></td>
                <td width="80" id="value_td_ret" align="right"><?=number_format($grandReturn,2,'.',''); ?></td>
                <td id="value_td_bal" align="right"><?=number_format($grandBal,2,'.',''); ?></td>
            </tr>
        </table>
        <br>
        <div align="left">
            <table class="rpt_table" border="1" rules="all" width="540" cellpadding="0" cellspacing="0">
                <thead>
                    <tr>
                        <th colspan="5">Buyer Wise Summery</th>
                    </tr>
                    <tr>
                        <th width="120">Buyer</th>
                        <th width="100">Required</th>
                        <th width="100">Issue</th>
                        <th width="100">Return</th>
                        <th>Balance</th>
                    </tr>
                </thead>
            </table>
            <div style="width:540px; overflow-y:scroll; max-height:200px;" id="scroll_body1">
                <table width="520" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_body1" >
                    <? foreach($buyerWiseQtyArr as $buyerId=>$buyerQty)
                    {
                        ?>
                        <tr>
                            <td width="120"><?=$buyer_arr[$buyerId]; ?></td>
                            <td width="100" align="right"><?=number_format($buyerQty['req'],2,'.',''); ?></td>
                            <td width="100" align="right"><?=number_format($buyerQty['issue'],2,'.',''); ?></td>
                            <td width="100" align="right"><?=number_format($buyerQty['return'],2,'.',''); ?></td>
                            <td align="right"><?=number_format($buyerQty['balance'],2,'.',''); ?></td>
                        </tr>
                        <?
                        $bsummReq+=$buyerQty['req'];
                        $bsummIssue+=$buyerQty['issue'];
                        $bsummRet+=$buyerQty['return'];
                        $bsummBalance+=$buyerQty['balance'];
                    }
                    ?>
                </table>
            </div>
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="540" class="tbl_bottom">
                <tr style="font-size:13px">
                    <td width="120">Total:</td>
                    <td width="100" align="right"><?=number_format($bsummReq,2,'.',''); ?></td>
                    <td width="100" align="right"><?=number_format($bsummIssue,2,'.',''); ?></td>
                    <td width="100" align="right"><?=number_format($bsummRet,2,'.',''); ?></td>
                    <td align="right"><?=number_format($bsummBalance,2,'.',''); ?></td>
                </tr>
            </table>
        </div>
    </fieldset>
	<?
	foreach (glob("$user_id*.xls") as $filename) 
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	$filename=$user_id."_".$name.".xls";
	echo "$total_data####$filename";
	exit();
}

if($action=="yarn_requisition_print")
{
  $data=explode('*',$data);
  // print_r($data);die;
  if($data[4]==2){
      echo load_html_head_contents($data[2],"../../../", 1, 1, $unicode,'','');
  }else{
      echo load_html_head_contents($data[2],"../../", 1, 1, $unicode,'','');
  }


  //echo "jahid";die;

  $com_sql=sql_select("select a.id, a.company_name, a.city from lib_company a where a.id=$data[0]");

  $company_name=$com_sql[0][csf("company_name")];
  $location_name=$com_sql[0][csf("city")];
  //$address=$com_sql[0][csf("address")];
  $count_arr=return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');

  /*$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
  $location=return_field_value("location_name","lib_location","id=$data[0]" );
  $address=return_field_value("address","lib_location","id=$data[0]");
  $lib_country_arr=return_library_array( "select id,country_name from lib_country","id", "country_name"  );
  $item_name_arr=return_library_array("select id,item_name from lib_item_group", "id","item_name");
  $supplier_name_library = return_library_array('SELECT id,supplier_name FROM lib_supplier','id','supplier_name');
  $lib_terms_condition=return_library_array( "select id, terms from lib_terms_condition",'id','terms');
  $color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');

  $buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');*/


  if($db_type==0)
  {
    $sql_data = sql_select("SELECT a.id, a.requ_prefix_num, a.requ_no, a.item_category_id, a.supplier_id, a.delivery_date, a.pay_mode, a.requisition_date, a.cbo_currency, a.source, a.attention, a.do_no, a.remarks, group_concat(b.job_id) as job_id FROM  inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b WHERE a.id=b.mst_id and a.id = $data[1] group by a.id, a.requ_prefix_num, a.requ_no, a.item_category_id, a.supplier_id, a.delivery_date, a.pay_mode, a.requisition_date, a.cbo_currency, a.source, a.attention, a.do_no, a.remarks");
  }
  else if($db_type==2)
  {
    $sql_data = sql_select("SELECT a.id, a.requ_prefix_num, a.requ_no, a.item_category_id, a.supplier_id, a.delivery_date, a.pay_mode, a.requisition_date, a.cbo_currency, a.source, a.attention, a.do_no, a.remarks, LISTAGG(CAST(b.job_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.job_id) as job_id FROM  inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b WHERE a.id=b.mst_id and a.id = $data[1] group by a.id, a.requ_prefix_num, a.requ_no, a.item_category_id, a.supplier_id, a.delivery_date, a.pay_mode, a.requisition_date, a.cbo_currency, a.source, a.attention, a.do_no, a.remarks");
  }



  $job_all_id="";
  foreach($sql_data as $row)
  {
    $requ_prefix_num=$row[csf("requ_prefix_num")];
    $requ_no=$row[csf("requ_no")];
    $item_category_id=$row[csf("item_category_id")];
    $supplier_id=$row[csf("supplier_id")];
    $delivery_date=$row[csf("delivery_date")];
    $requisition_date=$row[csf("requisition_date")];
    $cbo_currency=$row[csf("cbo_currency")];
    $pay_mode_id=$row[csf("pay_mode")];
    $source_id=$row[csf("source")];
    $attention=$row[csf("attention")];
    $do_no=$row[csf("do_no")];
    $remarks=$row[csf("remarks")];
    $job_id_all=array_unique(explode(",",$row[csf("job_id")]));
    foreach($job_id_all as $job_id)
    {
      if($job_all_id=="") $job_all_id=$job_id; else $job_all_id.=",".$job_id;
    }

  }


  if($job_all_id!="")
  {
    $sql_job=sql_select("select a.id, min(b.po_received_date) as po_received_date, min(b.pub_shipment_date) as pub_shipment_date from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id in($job_all_id) group by a.id");
    foreach($sql_job as $row)
    {
      $buyer_job_arr[$row[csf("id")]]["po_received_date"]=$row[csf("po_received_date")];
      $buyer_job_arr[$row[csf("id")]]["pub_shipment_date"]=$row[csf("pub_shipment_date")];
    }
  }




  $sql_supplier = sql_select("SELECT id,supplier_name,contact_no,country_id,web_site,email,address_1,address_2,address_3,address_4 FROM  lib_supplier WHERE id = $supplier_id");

   foreach($sql_supplier as $supplier_data)
  {//contact_no
    $row_mst[csf('supplier_id')];

    if($supplier_data[csf('address_1')]!='')$address_1 = $supplier_data[csf('address_1')].','.' ';else $address_1='';
    if($supplier_data[csf('address_2')]!='')$address_2 = $supplier_data[csf('address_2')].','.' ';else $address_2='';
    if($supplier_data[csf('address_3')]!='')$address_3 = $supplier_data[csf('address_3')].','.' ';else $address_3='';
    if($supplier_data[csf('address_4')]!='')$address_4 = $supplier_data[csf('address_4')].','.' ';else $address_4='';
    if($supplier_data[csf('contact_no')]!='')$contact_no = $supplier_data[csf('contact_no')].','.' ';else $contact_no='';
    if($supplier_data[csf('web_site')]!='')$web_site = $supplier_data[csf('web_site')].','.' ';else $web_site='';
    if($supplier_data[csf('email')]!='')$email = $supplier_data[csf('email')].','.' ';else $email='';
    //if($supplier_data[csf('country_id')]!=0)$country = $supplier_data[csf('country_id')].','.' ';else $country='';
    $country = $supplier_data['country_id'];

    $supplier_address = $address_1;
    $supplier_country =$country;
    $supplier_phone =$contact_no;
    $supplier_email = $email;
  }
  $image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$data[0]'","image_location");
  $varcode_booking_no=$requ_no;
  ?>
  <div style="width:1030px;">
    <table width="1000" cellspacing="0" align="center">
        <tr>
          <td rowspan="3" width="70">
              <? if($data[4] == 2){ ?>
              <img src="../../../<? echo $image_location; ?>" height="70" width="200"></td>
          <? }else{  ?>
              <img src="../../<? echo $image_location; ?>" height="70" width="200"></td>
          <? }?>
            <td colspan="2" style="font-size:xx-large;" align="center"><strong><? echo $company_name; ?></strong></td>
            <td rowspan="3" colspan="2" width="250" id="barcode_img_id"> </td>
        </tr>
        <tr class="form_caption">
          <td colspan="2" align="center" style="font-size:14px"><? echo $location_name; //.",".$address; ?></td>
        </tr>
        <tr>
            <td colspan="2" align="center" style="font-size:x-large"><strong><? echo $data[2]; ?></strong></td>
        </tr>
        <tr>
            <td colspan="2" align="center" style="font-size:x-large"><strong>&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font style="color:#F00"><? if($data[3]==1){ echo "(Approved)";}else{echo "";}; ?> </font></strong></td>
        </tr>
    </table>
    <table width="1000" cellspacing="0" align="center">
         <tr>
            <td width="300" ><strong>To</strong>,&nbsp;<? echo $attention; ?></td>
            <td width="175"><strong>Req. No:</strong> &nbsp;<? echo $requ_no; ?></td>
            <td width="175" ><strong>Req. Date:</strong>&nbsp;<? if($delivery_date!="" && $delivery_date!="0000-00-00") echo change_date_format($requisition_date); ?></td>
            <td width="175"><strong>Delivery. Date:</strong>&nbsp;<? if($delivery_date!="" && $delivery_date!="0000-00-00") echo change_date_format($delivery_date); ?></td>
            <td align="left"><strong>Pay Mode :</strong>&nbsp;<? echo $pay_mode[$pay_mode_id]; ?></td>
        </tr>
        <tr>
            <td ><? echo $supplier_arr[$supplier_id];  echo $supplier_address;  echo  $lib_country_arr[$country];  echo "<br> Cell :".$supplier_phone;  echo "Email :".$supplier_email; ?></td>
            <td ><strong>Currency :</strong>&nbsp;<? echo $currency[$cbo_currency]; ?></td>
            <td ><strong>Source :</strong>&nbsp;<? echo $source[$source_id]; ?></td>
            <td ><strong>D/O No.:</strong>&nbsp;<? echo $do_no; ?></td>
            <td ><strong>Remarks:</strong>&nbsp;<? echo $remarks; ?></td>
        </tr>

        <tr>
            <td align="right" colspan="5" >&nbsp;</td>
        </tr>
    </table>
    <br>
    <table align="center" cellspacing="0" width="1000"  border="1" rules="all" class="rpt_table" >
        <thead bgcolor="#dddddd" align="center">
            <th width="30">SL</th>
            <th width="110">Yarn Color</th>
            <th width="50">Count</th>
            <th width="140">Composition</th>
            <th width="30">%</th>
            <th width="70">Yarn Type</th>
            <th width="40" >UOM</th>
            <th width="70">Req Qty. </th>
            <th width="50">Rate</th>
            <th width="80">Amount</th>
            <th width="70">OPD</th>
            <th width="70">TOD</th>
            <th width="40">Lead Time (Days)</th>
            <th width="70">Yarn Inhouse Date</th>
            <th >Remarks</th>
        </thead>
        <tbody>
    <?


    $i=1; $buy_job_sty_val="";
    $mst_id=$dataArray[0][csf('id')];

    $sql_dtls="Select a.id, a.mst_id, a.job_id, a.job_no, a.buyer_id, a.style_ref_no, a.color_id, a.count_id, a.composition_id, a.com_percent, a.yarn_type_id, a.yarn_inhouse_date, a.cons_uom, a.quantity, a.rate, a.amount, a.remarks from  inv_purchase_requisition_dtls a where a.mst_id='$data[1]' and a.status_active=1 and a.is_deleted=0 order by a.id";
    //echo $sql_dtls;
    $sql_result = sql_select($sql_dtls);

    foreach($sql_result as $row)
    {
       $job_numbers .= "'".$row[csf("job_no")]."',";
       $booking_numbers .= "'".$row[csf("booking_no")]."',";
    }

    $job_numbers = chop( $job_numbers,"," );
    $booking_numbers = chop( $booking_numbers, "," );

    $sales_sql_result = sql_select("SELECT sales_booking_no,job_no,within_group,buyer_id,po_buyer FROM fabric_sales_order_mst WHERE status_active=1 and is_deleted=0 and job_no in($job_numbers) and sales_booking_no in($booking_numbers)");

    $salesData = array();
    foreach($sales_sql_result as $row)
    {
        if($row[csf("within_group")]==1)
        {
            $salesData[$row[csf("sales_booking_no")]][$row[csf("job_no")]]['buyer_id'] = $row[csf("po_buyer")];
        }else {
            $salesData[$row[csf("sales_booking_no")]][$row[csf("job_no")]]['buyer_id'] = $row[csf("buyer_id")];
        }
    }

    $job_independ_check=$sql_result[0][csf("job_id")];
    $job_id_ref=array();
    $i=1;$k=1;
    foreach($sql_result as $row)
    {
      if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

      if($salesData[$row[csf("booking_no")]][$row[csf("job_no")]]['buyer_id']!="")
      {
            $buyerId = $salesData[$row[csf("booking_no")]][$row[csf("job_no")]]['buyer_id'];
      }else {
            $buyerId = $row[csf("buyer_id")];
      }

      if($job_independ_check>0)
      {
        if(!in_array($row[csf("job_id")],$job_id_ref))
        {
          $job_id_ref[]=$row[csf("job_id")];

          if($k!=1)
          {
            ?>
                        <tr bgcolor="#CCCCCC">
                            <td >&nbsp;</td>
                            <td >&nbsp;</td>
                            <td >&nbsp;</td>
                            <td >&nbsp;</td>
                            <td >&nbsp;</td>
                      <td align="right" colspan="2">Job Total:</td>
                            <td align="right"><? echo number_format($job_wise_qnty,2); ?></td>
                            <td >&nbsp;</td>
                            <td align="right"><? echo number_format($job_wise_amount,4); ?></td>
                            <td >&nbsp;</td>
                            <td >&nbsp;</td>
                            <td >&nbsp;</td>
                            <td >&nbsp;</td>
                            <td >&nbsp;</td>
                        </tr>
                        <?
            $job_wise_qnty=$job_wise_amount=0;
          }
          ?>
          <tr bgcolor="#FFFFCC">
            <td colspan="15">Job No: <? echo $row[csf("job_no")];?> &nbsp;&nbsp;Buyer Name : <? echo $buyer_arr[$buyerId];?> &nbsp;&nbsp; Style : <? echo $row[csf("style_ref_no")];?></td>
          </tr>
          <?
          $k++;
        }
        ?>
                <tr bgcolor="<? echo $bgcolor; ?>">
                    <td align="center"><? echo $i; ?></td>
                    <td align="center"><p><? echo $color_arr[$row[csf("color_id")]]; ?>&nbsp;</p></td>
                    <td align="center"><p><? echo $count_arr[$row[csf("count_id")]]; ?>&nbsp;</p></td>
                    <td ><p><? echo $composition[$row[csf("composition_id")]]; ?></p></td>
                    <td align="center"><p><? echo $row[csf("com_percent")]; ?></p></td>
                    <td align="center"><p><? echo $yarn_type[$row[csf("yarn_type_id")]]; ?></p></td>
                    <td align="center"><p><? echo $unit_of_measurement[$row[csf("cons_uom")]]; ?></p></td>
                    <td align="right"><p><? echo number_format($row[csf("quantity")],2); $grand_tot_qnty+=$row[csf("quantity")]; ?></p></td>
                    <td align="right"><p><? echo number_format($row[csf("rate")],4,".",""); ?></p></td>
                    <td align="right"><p><? echo number_format($row[csf("amount")],4,".",""); $grand_total_val+=$row[csf("amount")];  ?></p></td>
                    <td align="center"><p><? if($buyer_job_arr[$row[csf("job_id")]]["po_received_date"]!="" && $buyer_job_arr[$row[csf("job_id")]]["po_received_date"]!="0000-00-00") echo change_date_format($buyer_job_arr[$row[csf("job_id")]]["po_received_date"]); else echo "&nbsp;"; ?></p></td>
                    <td align="center"><p><? if($buyer_job_arr[$row[csf("job_id")]]["pub_shipment_date"]!="" && $buyer_job_arr[$row[csf("job_id")]]["pub_shipment_date"]!="0000-00-00") echo change_date_format($buyer_job_arr[$row[csf("job_id")]]["pub_shipment_date"]); else echo "&nbsp;"; ?></p></td>
                    <td align="center"><p><?  $days_remian=datediff("d",$buyer_job_arr[$row[csf("job_id")]]["po_received_date"],$buyer_job_arr[$row[csf("job_id")]]["pub_shipment_date"]); if($days_remian!="")  echo $days_remian; ?></p></td>
                    <td align="center"><p><? if($row[csf("yarn_inhouse_date")]!="" && $row[csf("yarn_inhouse_date")]!="0000-00-00") echo change_date_format($row[csf("yarn_inhouse_date")]); ?></p></td>
                    <td ><p><? echo $row[csf("remarks")]; ?></p></td>
                </tr>
                <?
        $job_wise_qnty +=$row[csf("quantity")];
        $job_wise_amount +=$row[csf("amount")];
      }
      else
      {
        ?>
                <tr bgcolor="<? echo $bgcolor; ?>">
                    <td align="center"><? echo $i; ?></td>
                    <td align="center"><p><? echo $color_arr[$row[csf("color_id")]]; ?>&nbsp;</p></td>
                    <td align="center"><p><? echo $count_arr[$row[csf("count_id")]]; ?>&nbsp;</p></td>
                    <td ><p><? echo $composition[$row[csf("composition_id")]]; ?></p></td>
                    <td align="center"><p><? echo $row[csf("com_percent")]; ?></p></td>
                    <td align="center"><p><? echo $yarn_type[$row[csf("yarn_type_id")]]; ?></p></td>
                    <td align="center"><p><? echo $unit_of_measurement[$row[csf("cons_uom")]]; ?></p></td>
                    <td align="right"><p><? echo number_format($row[csf("quantity")],2); $grand_tot_qnty+=$row[csf("quantity")]; ?></p></td>
                    <td align="right"><p><? echo number_format($row[csf("rate")],4,".",""); ?></p></td>
                    <td align="right"><p><? echo number_format($row[csf("amount")],4,".",""); $grand_total_val+=$row[csf("amount")]; ?></p></td>
                    <td align="center"><p><? //echo $row[csf("com_percent")]; ?></p></td>
                    <td align="center"><p><? //echo $row[csf("com_percent")]; ?></p></td>
                    <td align="center"><p><? //echo $row[csf("com_percent")]; ?></p></td>
                    <td align="center"><p><? if($row[csf("yarn_inhouse_date")]!="" && $row[csf("yarn_inhouse_date")]!="0000-00-00") echo change_date_format($row[csf("yarn_inhouse_date")]); ?></p></td>
                    <td ><p><? echo $row[csf("remarks")]; ?></p></td>
                </tr>
                <?
      }
      //if
                        $i++;
    }
    if($job_independ_check>0)
    {
      ?>
          <tr bgcolor="#CCCCCC">
                <td >&nbsp;</td>
                <td >&nbsp;</td>
                <td >&nbsp;</td>
                <td >&nbsp;</td>
                <td >&nbsp;</td>
                <td align="right" colspan="2">Job Total:</td>
                <td align="right"><? echo number_format($job_wise_qnty,2); ?></td>
                <td >&nbsp;</td>
                <td align="right"><? echo number_format($job_wise_amount,4); ?></td>
                <td >&nbsp;</td>
                <td >&nbsp;</td>
                <td >&nbsp;</td>
                <td >&nbsp;</td>
                <td >&nbsp;</td>
            </tr>
            <?
    }
    ?>
    </tbody>
        <tfoot>
          <tr>
              <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th colspan="2">Grand Total</th>
                <th><? echo number_format($grand_tot_qnty,2); ?></th>
                <th>&nbsp;</th>
                <th><? echo number_format($grand_total_val,4); ?></th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>

            </tr>
        </tfoot>
  </table>
    <br>

     <?
        echo get_spacial_instruction($requ_no,$width="1000px");

        $approved_sql=sql_select("SELECT  mst_id, approved_by ,min(approved_date) as approved_date  from approval_history where entry_form=20 AND  mst_id ='$data[1]'  group by mst_id, approved_by order by  approved_by");
        $approved_his_sql=sql_select("SELECT  mst_id, approved_by ,approved_date,un_approved_reason,un_approved_date,approved_no  from approval_history where entry_form=20 AND  mst_id ='$data[1]'  order by  approved_no,approved_date");
        $user_lib_name=return_library_array("SELECT id,user_full_name from user_passwd", "id", "user_full_name");
        $user_lib_desg=return_library_array("SELECT id,designation from user_passwd", "id", "designation");
        $designation_lib=return_library_array("SELECT id,custom_designation from lib_designation", "id", "custom_designation");
      ?>
     <? if(count($approved_sql)>0)
      {
          $sl=1;
          ?>
          <div style="margin-top:15px">
              <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
                  <label><b>Others Purchase Order Approval Status </b></label>
                  <thead>
                    <tr style="font-weight:bold">
                        <th width="20">SL</th>
                        <th width="250">Name</th>
                        <th width="200">Designation</th>
                        <th width="100">Approval Date</th>
                    </tr>
                </thead>
                  <? foreach ($approved_sql as $key => $value)
                  {
                      ?>
                      <tr>
                          <td width="20"><? echo $sl; ?></td>
                          <td width="250"><? echo $user_lib_name[$value[csf("approved_by")]]; ?></td>
                          <td width="200"><? echo $designation_lib[$user_lib_desg[$value[csf("approved_by")]]]; ?></td>
                          <td width="100"><? $approved_date=explode(" ",$value[csf("approved_date")]);

              echo change_date_format($approved_date[0])." ".$approved_date[1];  ?></td>
                      </tr>
                      <?
                      $sl++;
                  }
                  ?>
              </table>
          </div>
          <?
      }
      ?>
      <? if(count($approved_his_sql) > 0)
      {
          $sl=1;
          ?>
          <div style="margin-top:15px">
              <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
                  <label><b>Others Purchase Order Approval / Un-Approval History </b></label>
                  <thead>
                    <tr style="font-weight:bold">
                        <th width="20">SL</th>
                        <th width="150">Approved / Un-Approved</th>
                        <th width="150">Designation</th>
                        <th width="50">Approval Status</th>
                        <th width="150">Reason for Un-Approval</th>
                        <th width="150">Date</th>
                    </tr>
                </thead>
                  <? foreach ($approved_his_sql as $key => $value)
                  {
                    if($sl%2==0)$bgcolor="#E9F3FF";  else $bgcolor="#FFFFFF";
                      ?>
                      <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $sl; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $sl; ?>">
                          <td width="20"><? echo $sl; ?></td>
                          <td width="150"><? echo $user_lib_name[$value[csf("approved_by")]]; ?></td>
                          <td width="150"><? echo $designation_lib[$user_lib_desg[$value[csf("approved_by")]]]; ?></td>
                          <td width="50">Yes</td>
                          <td width="150"><? echo $value[csf("un_approved_reason")] ?></td>
                          <td width="150"><? $approved_date=explode(" ",$value[csf("approved_date")]);

              echo change_date_format($approved_date[0])." ".$approved_date[1]; ?></td>
                      </tr>
                          <?
                          $sl++;
                          $un_approved_date= explode(" ",$value[csf('un_approved_date')]);
                          $un_approved_date=$un_approved_date[0];
                          if($db_type==0) //Mysql
                          {
                              if($un_approved_date=="" || $un_approved_date=="0000-00-00") $un_approved_date="";else $un_approved_date=$un_approved_date;
                          }
                          else
                          {
                              if($un_approved_date=="") $un_approved_date="";else $un_approved_date=$un_approved_date;
                          }

                          if($un_approved_date!="")
                          {
                          ?>
                          <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $sl; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $sl; ?>">
                          <td width="20"><? echo $sl; ?></td>
                          <td width="150"><? echo $user_lib_name[$value[csf("approved_by")]]; ?></td>
                          <td width="150"><? echo $designation_lib[$user_lib_desg[$value[csf("approved_by")]]]; ?></td>
                          <td width="50">No</td>
                          <td width="150"><? echo $value[csf("un_approved_reason")] ?></td>
                          <td width="150"><? $approved_date=explode(" ",$value[csf("un_approved_date")]);

              echo change_date_format($approved_date[0])." ".$approved_date[1]; ?></td>
                      </tr>

              <?
              $sl++;

            }

                  }
                  ?>
              </table>
          </div>
          <?
      }
      ?>
       <br/>
      <?
        echo signature_table(102, $data[0], "900px");
      ?>
  </div>
  <? if($data[4] == 2){ ?>
      <script type="text/javascript" src="../../../js/jquery.js"></script>
     <script type="text/javascript" src="../../../js/jquerybarcode.js"></script>
  <? }else{ ?>
     <script type="text/javascript" src="../../js/jquery.js"></script>
    <script type="text/javascript" src="../../js/jquerybarcode.js"></script>
    <? }?>
    <script>
    fnc_generate_Barcode('<? echo $varcode_booking_no; ?>','barcode_img_id');
    </script>
  <?
    exit();

}

if($action=="yarn_requisition_print_2")
{
	$data=explode('*',$data);
	echo load_html_head_contents($data[2],"../../", 1, 1, $unicode,'','');
	$com_sql=sql_select("select a.id, a.company_name, a.city from lib_company a where a.id=$data[0]");

	$company_name=$com_sql[0][csf("company_name")];
	$location_name=$com_sql[0][csf("city")];
	$count_arr=return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');
	if($db_type==0)
	{
		$sql_data = sql_select("SELECT a.id, a.requ_prefix_num, a.requ_no, a.item_category_id, a.supplier_id, a.delivery_date, a.pay_mode, a.requisition_date, a.cbo_currency, a.source, a.attention, a.do_no, a.remarks, group_concat(b.job_id) as job_id FROM  inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b WHERE a.id=b.mst_id and a.id = $data[1] group by a.id, a.requ_prefix_num, a.requ_no, a.item_category_id, a.supplier_id, a.delivery_date, a.pay_mode, a.requisition_date, a.cbo_currency, a.source, a.attention, a.do_no, a.remarks");
	}
	else if($db_type==2)
	{
		$sql_data = sql_select("SELECT a.id, a.requ_prefix_num, a.requ_no, a.item_category_id, a.supplier_id, a.delivery_date, a.pay_mode, a.requisition_date, a.cbo_currency, a.source, a.attention, a.do_no, a.remarks,a.is_approved, LISTAGG(CAST(b.job_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.job_id) as job_id FROM  inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b WHERE a.id=b.mst_id and a.id = $data[1] group by a.id, a.requ_prefix_num, a.requ_no, a.item_category_id, a.supplier_id, a.delivery_date, a.pay_mode, a.requisition_date, a.cbo_currency, a.source, a.attention, a.do_no, a.remarks, a.is_approved");
	}



	$job_all_id="";
	foreach($sql_data as $row)
	{
		$requ_prefix_num=$row[csf("requ_prefix_num")];
		$requ_no=$row[csf("requ_no")];
		$item_category_id=$row[csf("item_category_id")];
		$supplier_id=$row[csf("supplier_id")];
		$delivery_date=$row[csf("delivery_date")];
		$requisition_date=$row[csf("requisition_date")];
		$cbo_currency=$row[csf("cbo_currency")];
		$pay_mode_id=$row[csf("pay_mode")];
		$source_id=$row[csf("source")];
		$attention=$row[csf("attention")];
		$do_no=$row[csf("do_no")];
    $remarks=$row[csf("remarks")];
		$is_approved=$row[csf("is_approved")];
		$job_id_all=array_unique(explode(",",$row[csf("job_id")]));
		foreach($job_id_all as $job_id)
		{
		  if($job_all_id=="") $job_all_id=$job_id; else $job_all_id.=",".$job_id;
		}
	}


	$sql_supplier = sql_select("SELECT id,supplier_name,contact_no,country_id,web_site,email,address_1,address_2,address_3,address_4 FROM  lib_supplier WHERE id = $supplier_id");
	foreach($sql_supplier as $supplier_data)
	{
		//contact_no
		$row_mst[csf('supplier_id')];

		if($supplier_data[csf('address_1')]!='')$address_1 = $supplier_data[csf('address_1')].','.' ';else $address_1='';
		if($supplier_data[csf('address_2')]!='')$address_2 = $supplier_data[csf('address_2')].','.' ';else $address_2='';
		if($supplier_data[csf('address_3')]!='')$address_3 = $supplier_data[csf('address_3')].','.' ';else $address_3='';
		if($supplier_data[csf('address_4')]!='')$address_4 = $supplier_data[csf('address_4')].','.' ';else $address_4='';
		if($supplier_data[csf('contact_no')]!='')$contact_no = $supplier_data[csf('contact_no')].','.' ';else $contact_no='';
		if($supplier_data[csf('web_site')]!='')$web_site = $supplier_data[csf('web_site')].','.' ';else $web_site='';
		if($supplier_data[csf('email')]!='')$email = $supplier_data[csf('email')].','.' ';else $email='';
		//if($supplier_data[csf('country_id')]!=0)$country = $supplier_data[csf('country_id')].','.' ';else $country='';
		$country = $supplier_data['country_id'];

		$supplier_address = $address_1;
		$supplier_country =$country;
		$supplier_phone =$contact_no;
		$supplier_email = $email;
	}
	$image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$data[0]'","image_location");
	$varcode_booking_no=$requ_no;
	?>
	<div style="width:1070px;">
    <table width="1050" cellspacing="0" align="center">
        <tr>
        	<td rowspan="3" width="70"><img src="../../<? echo $image_location; ?>" height="70" width="200"></td>
            <td colspan="2" style="font-size:xx-large;" align="center"><strong><? echo $company_name; ?></strong></td>
            <td rowspan="3" colspan="2" width="250" id="barcode_img_id"></td>
        </tr>
        <tr class="form_caption">
        	<td colspan="2" align="center" style="font-size:14px"><? echo $location_name; //.",".$address; ?></td>
        </tr>
        <tr>
            <td colspan="2" align="center" style="font-size:x-large"><strong><? echo $data[2]; ?></strong></td>
        </tr>
    </table>
    <table width="1070" cellspacing="0" align="center">
    	<tr>
            <td width="300" ><strong>To</strong>,&nbsp;<? echo $attention; ?></td>
            <td width="200"><strong>Req. No:</strong> &nbsp;<? echo $requ_no; ?></td>
            <td width="200" ><strong>Req. Date:</strong>&nbsp;<? if($delivery_date!="" && $delivery_date!="0000-00-00") echo change_date_format($requisition_date); ?></td>
            <td width="200"><strong>Delivery. Date:</strong>&nbsp;<? if($delivery_date!="" && $delivery_date!="0000-00-00") echo change_date_format($delivery_date); ?></td>
            <td align="left"><strong>Pay Mode :</strong>&nbsp;<? echo $pay_mode[$pay_mode_id]; ?></td>
        </tr>
        <tr>
            <td ><? echo $supplier_arr[$supplier_id];  echo $supplier_address;  echo  $lib_country_arr[$country];  echo "<br> Cell :".$supplier_phone;  echo "Email :".$supplier_email; ?></td>
            <td ><strong>Currency :</strong>&nbsp;<? echo $currency[$cbo_currency]; ?></td>
            <td ><strong>Source :</strong>&nbsp;<? echo $source[$source_id]; ?></td>
            <td ><strong>D/O No.:</strong>&nbsp;<? echo $do_no; ?></td>
            <td ><strong>Remarks:</strong>&nbsp;<? echo $remarks; ?></td>
        </tr>

        <tr>
            <th align="center" colspan="5" style="color: red; font-weight: bold; font-size: 30px ">
              <? if($is_approved !=0 && $is_approved ==1) { echo 'Approved' ;}else if($is_approved ==3){ echo 'Partial Approved' ;} ?>
            </th>
        </tr>
    </table>
    <br>
    <table align="center" cellspacing="0" width="1250"  border="1" rules="all" class="rpt_table" >
        <thead bgcolor="#dddddd" align="center">
            <th width="20">SL</th>
            <th width="80">Job No</th>
 			      <th width="100">Internal Ref.</th>
 			      <th width="80">Fab Booking</th>
            <th width="90">Buyer Name</th>
            <th width="50">Style Reff.</th>
            <th width="70">Yarn Color</th>
            <th width="40">Count</th>
            <th width="140">Composition</th>
            <th width="30">%</th>
            <th width="70">Yarn Type</th>
            <th width="40" >UOM</th>
            <th width="70">Req Qty. </th>
            <th width="50">Rate</th>
            <th width="80">Amount</th>
            <th width="65">Yarn Inhouse Date</th>
            <th width="65">Remarks</th>
            <th>LC/SC</th>
        </thead>
        <tbody>
		<?
        $i=1; $buy_job_sty_val="";
        $mst_id=$dataArray[0][csf('id')];

        $sql_dtls="Select a.id, a.mst_id, a.job_id, a.job_no, a.booking_no, a.buyer_id, a.style_ref_no, a.color_id, a.count_id, a.composition_id, a.com_percent, a.yarn_type_id, a.yarn_inhouse_date, a.cons_uom, a.quantity, a.rate, a.amount, a.remarks
        from inv_purchase_requisition_dtls a where a.mst_id='$data[1]' and a.status_active=1 and a.is_deleted=0 order by a.id";
        //echo $sql_dtls;
        $sql_result = sql_select($sql_dtls);

        foreach($sql_result as $row)
        {
          if($array_check[$row[csf("job_no")]]!=$row[csf("job_no")]){
            $array_check[$row[csf("job_no")]] =$row[csf("job_no")];
            $job_numbers .= "'".$row[csf("job_no")]."',";
          }

           $booking_numbers .= "'".$row[csf("booking_no")]."',";
        }

        $job_numbers = chop( $job_numbers,"," );
        $booking_numbers = chop( $booking_numbers, "," );
		$sql_job=sql_select("select a.job_no, b.sc_lc, b.grouping
		from wo_po_details_master a, wo_po_break_down b
		where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.job_no in($job_numbers)
		group by a.job_no, b.sc_lc, b.grouping");
    
		foreach($sql_job as $row)
		{
      $buyer_job_arr[$row[csf("job_no")]]["sc_lc"].=$row[csf("sc_lc")].",";
			$internal_ref_arr[$row[csf("job_no")]]["internal_ref"].=$row[csf("grouping")].",";
		}

        $sales_sql_result = sql_select("SELECT sales_booking_no,job_no,within_group,buyer_id,po_buyer FROM fabric_sales_order_mst WHERE status_active=1 and is_deleted=0 and job_no in($job_numbers) and sales_booking_no in($booking_numbers)");

        $salesData = array();
        foreach($sales_sql_result as $row)
        {
            if($row[csf("within_group")]==1)
            {
                $salesData[$row[csf("sales_booking_no")]][$row[csf("job_no")]]['buyer_id'] = $row[csf("po_buyer")];
            }else {
                $salesData[$row[csf("sales_booking_no")]][$row[csf("job_no")]]['buyer_id'] = $row[csf("buyer_id")];
            }
        }


        $job_independ_check=$sql_result[0][csf("job_id")];
        $job_id_ref=array();
        $i=1;$k=1;
        foreach($sql_result as $row)
        {

            if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
            if($salesData[$row[csf("booking_no")]][$row[csf("job_no")]]['buyer_id']!="")
            {
                $buyerId = $salesData[$row[csf("booking_no")]][$row[csf("job_no")]]['buyer_id'];
            }else {
                $buyerId = $row[csf("buyer_id")];
            }

            if($job_independ_check>0)
            {
                if(!in_array($row[csf("job_no")],$job_id_ref))
                {
                    $job_id_ref[]=$row[csf("job_no")];
                    if($k!=1)
                    {
                        ?>
                        <tr bgcolor="#CCCCCC">
                            <td >&nbsp;</td>
                            <td >&nbsp;</td>
                            <td >&nbsp;</td>
                            <td >&nbsp;</td>
                            <td >&nbsp;</td>
                            <td >&nbsp;</td>
                            <td >&nbsp;</td>
                            <td >&nbsp;</td>
                            <td >&nbsp;</td>
                            <td >&nbsp;</td>
                            <td align="right" colspan="2">Style Total:</td>
                            <td align="right"><? echo number_format($job_wise_qnty,2); ?></td>
                            <td >&nbsp;</td>
                            <td align="right"><? echo number_format($job_wise_amount,4); ?></td>
                            <td >&nbsp;</td>
                            <td >&nbsp;</td>
                            <td >&nbsp;</td>
                        </tr>
                        <?
                        $job_wise_qnty=$job_wise_amount=0;
                    }
                    $k++;
                }
                ?>
                <tr bgcolor="<? echo $bgcolor; ?>">
                    <td align="center"><? echo $i++; ?></td>
                    <td align="center"><? echo $row[csf("job_no")]; ?></td>
                    <td align="center"><p><? echo chop($internal_ref_arr[$row[csf("job_no")]]["internal_ref"],","); ?></td>
                    <td align="center"><? echo $row[csf("booking_no")]; ?></td>
                    <td align="center"><? echo $buyer_arr[$buyerId]; ?></td>
                    <td align="center"><? echo $row[csf("style_ref_no")]; ?></td>
                    <td align="center"><p><? echo $color_arr[$row[csf("color_id")]]; ?>&nbsp;</p></td>
                    <td align="center"><p><? echo $count_arr[$row[csf("count_id")]]; ?>&nbsp;</p></td>
                    <td align="center"><p><? echo $composition[$row[csf("composition_id")]]; ?></p></td>
                    <td align="center"><p><? echo $row[csf("com_percent")]; ?></p></td>
                    <td align="center"><p><? echo $yarn_type[$row[csf("yarn_type_id")]]; ?></p></td>
                    <td align="center"><p><? echo $unit_of_measurement[$row[csf("cons_uom")]]; ?></p></td>
                    <td align="right"><p><? echo number_format($row[csf("quantity")],2); $grand_tot_qnty+=$row[csf("quantity")]; ?></p></td>
                    <td align="right"><p><? echo number_format($row[csf("rate")],4,".",""); ?></p></td>
                    <td align="right"><p><? echo number_format($row[csf("amount")],4,".",""); $grand_total_val+=$row[csf("amount")];  ?></p></td>
                    <td align="center"><p><? if($row[csf("yarn_inhouse_date")]!="" && $row[csf("yarn_inhouse_date")]!="0000-00-00") echo change_date_format($row[csf("yarn_inhouse_date")]); ?></p></td>
                    <td><p><? echo $row[csf("remarks")]; ?></p></td>
                    <td><p><? echo chop($buyer_job_arr[$row[csf("job_no")]]["sc_lc"],","); ?></p></td>
                </tr>
                <?
                $job_wise_qnty +=$row[csf("quantity")];
                $job_wise_amount +=$row[csf("amount")];
            }
            else
            {
                ?>
                <tr bgcolor="<? echo $bgcolor; ?>">
                    <td align="center"><? echo $i++; ?></td>
                    <td align="center"><? echo $row[csf("job_no")]; ?></td>
                    <td align="center"><p><? echo chop($internal_ref_arr[$row[csf("job_no")]]["internal_ref"],","); ?></td>
                    <td align="center"><? echo $row[csf("booking_no")]; ?></td>
                    <td align="center"><? echo $buyer_arr[$buyerId]; ?></td>
                    <td align="center"><? echo $row[csf("style_ref_no")]; ?></td>
                    <td align="center"><p><? echo $color_arr[$row[csf("color_id")]]; ?>&nbsp;</p></td>
                    <td align="center"><p><? echo $count_arr[$row[csf("count_id")]]; ?>&nbsp;</p></td>
                    <td align="center"><p><? echo $composition[$row[csf("composition_id")]]; ?></p></td>
                    <td align="center"><p><? echo $row[csf("com_percent")]; ?></p></td>
                    <td align="center"><p><? echo $yarn_type[$row[csf("yarn_type_id")]]; ?></p></td>
                    <td align="center"><p><? echo $unit_of_measurement[$row[csf("cons_uom")]]; ?></p></td>
                    <td align="right"><p><? echo number_format($row[csf("quantity")],2); $grand_tot_qnty+=$row[csf("quantity")]; ?></p></td>
                    <td align="right"><p><? echo number_format($row[csf("rate")],4,".",""); ?></p></td>
                    <td align="right"><p><? echo number_format($row[csf("amount")],4,".",""); $grand_total_val+=$row[csf("amount")]; ?></p></td>
                    <td align="center"><p><? if($row[csf("yarn_inhouse_date")]!="" && $row[csf("yarn_inhouse_date")]!="0000-00-00") echo change_date_format($row[csf("yarn_inhouse_date")]); ?></p></td>
                    <td><p><? echo $row[csf("remarks")]; ?></p></td>
                    <td><p><? echo chop($buyer_job_arr[$row[csf("job_no")]]["sc_lc"],","); ?></p></td>
                </tr>
                <?
            }
        }
        if($job_independ_check>0)
        {
            ?>
            <tr bgcolor="#CCCCCC">
                <td >&nbsp;</td>
                <td >&nbsp;</td>
                <td >&nbsp;</td>
                <td >&nbsp;</td>
                <td >&nbsp;</td>
                <td >&nbsp;</td>
                <td >&nbsp;</td>
                <td >&nbsp;</td>
                <td >&nbsp;</td>
                <td >&nbsp;</td>
                <td align="right" colspan="2">Style Total:</td>
                <td align="right"><? echo number_format($job_wise_qnty,2); ?></td>
                <td >&nbsp;</td>
                <td align="right"><? echo number_format($job_wise_amount,4); ?></td>
                <td >&nbsp;</td>
                <td >&nbsp;</td>
                <td >&nbsp;</td>
            </tr>
            <?
        }
        ?>
        </tbody>
        <tfoot>
          <tr>
                <th colspan="12">Grand Total</th>
                <th><? echo number_format($grand_tot_qnty,2); ?></th>
                <th>&nbsp;</th>
                <th><? //echo number_format($grand_total_val,4); ?></th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
            </tr>
        </tfoot>
	</table>
    <br>
    <h2>Summery</h2>
    <br>
    <table  width="700" class="rpt_table" border="1" cellpadding="0" cellspacing="0" align="center" rules="all">
        <thead>
            <tr>
                <th width="3%">Sl</th>
                <th width="25%">Composition</th>
                <th width="27%">Color</th>
                <th width="15%">Yarn type</th>
                <th width="15%">Count</th>
                <th>Yarn Qty</th>
            </tr>
        </thead>
        <tbody>
        <?php
    $i = 1;
    $buy_job_sty_val = "";
    $mst_id = $dataArray[0][csf('id')];

    $sql_dtls = "Select a.count_id, a.composition_id, a.color_id,a.yarn_type_id, sum(a.quantity) as yarn_group_total from  inv_purchase_requisition_dtls a where a.mst_id='$data[1]' and a.status_active=1 and a.is_deleted=0 group by a.count_id, a.composition_id,a.color_id,a.yarn_type_id";
    //echo $sql_dtls;//die;
    $sql_result = sql_select($sql_dtls);
    $total = 0;
    foreach ($sql_result as $row) {
    	?>
    			<tr>
            <td align="center"><? echo $i++; ?></td>
            <td align="center" ><p><? echo $composition[$row[csf("composition_id")]]; ?></p></td>
            <td align="center" ><p><? echo $color_arr[$row[csf("color_id")]]; ?></p></td>
            <td align="center" ><? echo $yarn_type[$row[csf("yarn_type_id")]]; ?><p><? //echo $composition[$row[csf("composition_id")]]; ?></p></td>
            <td align="center"><p><? echo $count_arr[$row[csf("count_id")]]; ?>&nbsp;</p></td>
            <td align="center"><p><? echo number_format($row[csf("yarn_group_total")],2); $tot_qnty+=$row[csf("yarn_group_total")]; ?>&nbsp;</p></td>
    			</tr>
    			<?php
    }
    ?>
        </tbody>
        <tfoot>
            <tr>
                <th colspan="5" align="center">Total</th>
                <th align="center"><? echo number_format($tot_qnty,2); ?></th>
            </tr>
        </tfoot>
    </table>
    <br>
      <?

       $lib_designation=return_library_array(" select id,custom_designation from lib_designation","id","custom_designation");

     $data_array=sql_select("select b.approved_by,b.approved_no, b.approved_date, b.un_approved_reason, c.user_full_name,c.designation  from  inv_purchase_requisition_mst a , approval_history b, user_passwd c where a.id=b.mst_id and b.approved_by=c.id and a.id=$data[1] and b.entry_form=20 order by b.id asc");

    ?>
       <table  width="100%" class="rpt_table"   border="1" cellpadding="0" cellspacing="0" rules="all">
            <thead>
            <tr style="border:1px solid black;">
              <th colspan="5" style="border:1px solid black;">Approval Status</th>
            </tr>
            <tr style="border:1px solid black;">
              <th width="3%" style="border:1px solid black;">Sl</th>
              <th width="20%" style="border:1px solid black;">Name/Designation</th>
              <th width="27%" style="border:1px solid black;">Approval Date</th>
              <th width="20%" style="border:1px solid black;">Approval No</th>
              <th width="30%" style="border:1px solid black;">Un Approval Cause</th>
            </tr>
            </thead>
            <tbody>
            <?
      $i=1;
      foreach($data_array as $row){
      ?>
        <tr style="border:1px solid black;">
          <td width="3%" style="border:1px solid black;"><? echo $i;?></td>
          <td width="20%" style="border:1px solid black;"><? echo $row[csf('user_full_name')]." / ". $lib_designation[$row[csf('designation')]];?></td>
          <td width="27%" style="border:1px solid black;"><? echo date ("d-m-Y h:i:s",strtotime($row[csf('approved_date')])); ?></td>
          <td width="20%" style="border:1px solid black;"><? echo $row[csf('approved_no')];?></td>
          <td width="30%" style="border:1px solid black;"><? echo $row[csf('un_approved_reason')];?></td>
        </tr>
      <?
        $i++;
      }
        ?>
            </tbody>
        </table>
        <br>
    <?
    //echo $job_numbers;
	   //------------------------------ Query for TNA start-----------------------------------
        $job_no_all=explode(",","",$job_numbers);
        $sql_tna_task = "select id,po_number_id,
        (case when task_number=45 then task_start_date else null end) as yarn_requisition_start_date,
        (case when task_number=45 then task_finish_date else null end) as yarn_requisition_end_date,
        (case when task_number=47 then task_start_date else null end) as yarn_rec_start_date,
        (case when task_number=47 then task_finish_date else null end) as yarn_rec_end_date
        from tna_process_mst
        where status_active=1 and job_no in($job_numbers)";

        $tna_start_sql=sql_select( $sql_tna_task);
				$tna_date_task_arr=array();
				foreach($tna_start_sql as $row)
				{
					if($row[csf("yarn_requisition_start_date")]!="" && $row[csf("yarn_requisition_start_date")]!="0000-00-00"){
						$tna_date_task_arr[$row[csf("po_number_id")]]['yarn_requisition_start_date']=$row[csf("yarn_requisition_start_date")];
						$tna_date_task_arr[$row[csf("po_number_id")]]['yarn_requisition_end_date']=$row[csf("yarn_requisition_end_date")];
          }
          if($row[csf("yarn_rec_start_date")]!="" && $row[csf("yarn_rec_start_date")]!="0000-00-00"){
						$tna_date_task_arr[$row[csf("po_number_id")]]['yarn_rec_start_date']=$row[csf("yarn_rec_start_date")];
						$tna_date_task_arr[$row[csf("po_number_id")]]['yarn_rec_end_date']=$row[csf("yarn_rec_end_date")];
          }

          if($po_number_ids[$row[csf("po_number_id")]] != $row[csf("po_number_id")]){
            $all_po_number_ids[$row[csf("po_number_id")]] = $row[csf("po_number_id")];
          }
        }

        $po_sql ="SELECT a.style_ref_no,a.job_no, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id in(".implode(",", $all_po_number_ids).")  and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0";
        $po_sql_res=sql_select($po_sql);
        foreach ($po_sql_res as $row)
        {
          //$po_num_arr[$row[csf("id")]]['style']=$row[csf("style_ref_no")];
          $po_num_arr[$row[csf("id")]]['po']=$row[csf("po_number")];
          $po_num_arr[$row[csf("id")]]['job']=$row[csf("job_no")];
        }
        unset($po_sql_res);
        //$po_num_arr=return_library_array("select id,po_number from wo_po_break_down where id in(".implode(",", $all_po_number_ids).")",'id','po_number');
        //$po_num_arr=return_library_array("select id,job_no from wo_po_break_down where id in(".implode(",", $all_po_number_ids).")",'id','po_number');

        //print_r($tna_date_task_arr);//die;

	  //------------------------------ Query for TNA end-----------------------------------

    $task_short_name_arr=return_library_array( "select task_name,task_short_name from lib_tna_task where is_deleted=0 and status_active=1 and task_name in(45,47) order by task_name",'task_name','task_short_name');

    if(count($task_short_name_arr)>0)
    {
	   ?>
      <fieldset id="div_size_color_matrix" style="max-width:1200;">
        <legend>TNA Information</legend>
        <!--<span style="font-size:180; font-weight:bold;"></span>-->
        <table width="100%" class="rpt_table"  style="border:1px solid black;font-size:12px" border="1" cellpadding="2" cellspacing="0" rules="all">
          <thead>
            <tr>
            	<th width="50"  rowspan="2" align="center" valign="middle">SL</th>
              <th width="100" rowspan="2"  align="center" valign="middle"><b>Job No</b></th>
            	<th width="100" rowspan="2"  align="center" valign="middle"><b>Order No</b></th>
              <?
                $i=0;
                foreach ($task_short_name_arr as $key => $value) {
                  ?>
                    <th colspan="2" align="center" valign="middle"><b><? echo $task_short_name_arr[$key];?></b></th>
                  <?
                  $i++;
                }
              ?>
              <!-- <th colspan="2" align="center" valign="top"><b><? //echo $task_short_name_arr[252];?></b></th>
              <th colspan="2" align="center" valign="top"><b><? //echo $task_short_name_arr[47];?></b></th> -->
            </tr>
            <tr>
            	<th width="85" align="center" valign="middle"><b>Start Date</b></th>
              <th width="85" align="center" valign="middle"><b>End Date</b></th>

              <th width="85" align="center" valign="middle"><b>Start Date</b></th>
              <th width="85" align="center" valign="middle"><b>End Date</b></th>
            </tr>
          </thead>
          <tbody>
            <?
              $i=1;
              foreach($tna_date_task_arr as $order_id=>$row)
              {
                ?>
                <tr>
                	<td align="center"><? echo $i; ?></td>
                    <td align="center"><? echo $po_num_arr[$order_id]['job']; ?></td>
                    <td align="center"><? echo $po_num_arr[$order_id]['po']; ?></td>
                    <td align="center"><? echo change_date_format($row['yarn_requisition_start_date']); ?></td>
                    <td  align="center"><? echo change_date_format($row['yarn_requisition_end_date']); ?></td>

                    <td align="center"><? echo change_date_format($row['yarn_rec_start_date']); ?></td>
                    <td  align="center"><? echo change_date_format($row['yarn_rec_end_date']); ?></td>
                </tr>
                <?
                $i++;
              }
            ?>
          </tbody>
        </table>
      </fieldset>
      <br>
      <?
    }
    echo get_spacial_instruction($requ_no,$width="1070px");
    echo signature_table(102, $data[0], "1070px");
    ?>
    </div>
    <script type="text/javascript" src="../../js/jquery.js"></script>
    <script type="text/javascript" src="../../js/jquerybarcode.js"></script>
    <script>
    	fnc_generate_Barcode('<? echo $varcode_booking_no; ?>','barcode_img_id');
    </script>
    <?
    exit();
}

if($action=="yarn_requisition_print_5")
{
	$data=explode('*',$data);
	echo load_html_head_contents($data[2],"../../", 1, 1, $unicode,'','');
	$com_sql=sql_select("select a.id, a.company_name, a.city from lib_company a where a.id=$data[0]");

	$company_name=$com_sql[0][csf("company_name")];
	$location_name=$com_sql[0][csf("city")];
	$count_arr=return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');
	if($db_type==0)
	{
		$sql_data = sql_select("SELECT a.id, a.requ_prefix_num, a.requ_no, a.item_category_id, a.supplier_id, a.delivery_date, a.pay_mode, a.requisition_date, a.cbo_currency, a.source, a.attention, a.do_no, a.remarks, group_concat(b.job_id) as job_id FROM  inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b WHERE a.id=b.mst_id and a.id = $data[1] group by a.id, a.requ_prefix_num, a.requ_no, a.item_category_id, a.supplier_id, a.delivery_date, a.pay_mode, a.requisition_date, a.cbo_currency, a.source, a.attention, a.do_no, a.remarks");
	}
	else if($db_type==2)
	{
		$sql_data = sql_select("SELECT a.id, a.requ_prefix_num, a.requ_no, a.item_category_id, a.supplier_id, a.delivery_date, a.pay_mode, a.requisition_date, a.cbo_currency, a.source, a.attention, a.do_no, a.remarks, LISTAGG(CAST(b.job_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.job_id) as job_id FROM  inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b WHERE a.id=b.mst_id and a.id = $data[1] group by a.id, a.requ_prefix_num, a.requ_no, a.item_category_id, a.supplier_id, a.delivery_date, a.pay_mode, a.requisition_date, a.cbo_currency, a.source, a.attention, a.do_no, a.remarks");
	}



	$job_all_id="";
	foreach($sql_data as $row)
	{
		$requ_prefix_num=$row[csf("requ_prefix_num")];
		$requ_no=$row[csf("requ_no")];
		$item_category_id=$row[csf("item_category_id")];
		$supplier_id=$row[csf("supplier_id")];
		$delivery_date=$row[csf("delivery_date")];
		$requisition_date=$row[csf("requisition_date")];
		$cbo_currency=$row[csf("cbo_currency")];
		$pay_mode_id=$row[csf("pay_mode")];
		$source_id=$row[csf("source")];
		$attention=$row[csf("attention")];
		$do_no=$row[csf("do_no")];
		$remarks=$row[csf("remarks")];
		$job_id_all=array_unique(explode(",",$row[csf("job_id")]));
		foreach($job_id_all as $job_id)
		{
		  if($job_all_id=="") $job_all_id=$job_id; else $job_all_id.=",".$job_id;
		}
	}


	$sql_supplier = sql_select("SELECT id,supplier_name,contact_no,country_id,web_site,email,address_1,address_2,address_3,address_4 FROM  lib_supplier WHERE id = $supplier_id");
	foreach($sql_supplier as $supplier_data)
	{
		//contact_no
		$row_mst[csf('supplier_id')];

		if($supplier_data[csf('address_1')]!='')$address_1 = $supplier_data[csf('address_1')].','.' ';else $address_1='';
		if($supplier_data[csf('address_2')]!='')$address_2 = $supplier_data[csf('address_2')].','.' ';else $address_2='';
		if($supplier_data[csf('address_3')]!='')$address_3 = $supplier_data[csf('address_3')].','.' ';else $address_3='';
		if($supplier_data[csf('address_4')]!='')$address_4 = $supplier_data[csf('address_4')].','.' ';else $address_4='';
		if($supplier_data[csf('contact_no')]!='')$contact_no = $supplier_data[csf('contact_no')].','.' ';else $contact_no='';
		if($supplier_data[csf('web_site')]!='')$web_site = $supplier_data[csf('web_site')].','.' ';else $web_site='';
		if($supplier_data[csf('email')]!='')$email = $supplier_data[csf('email')].','.' ';else $email='';
		//if($supplier_data[csf('country_id')]!=0)$country = $supplier_data[csf('country_id')].','.' ';else $country='';
		$country = $supplier_data['country_id'];

		$supplier_address = $address_1;
		$supplier_country =$country;
		$supplier_phone =$contact_no;
		$supplier_email = $email;
	}
	$image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$data[0]'","image_location");
	$varcode_booking_no=$requ_no;
	?>
	<div style="width:1070px;">
    <table width="1050" cellspacing="0" align="center">
        <tr>
        	<td rowspan="3" width="70"><img src="../../<? echo $image_location; ?>" height="70" width="200"></td>
            <td colspan="2" style="font-size:xx-large;" align="center"><strong><? echo $company_name; ?></strong></td>
            <td rowspan="3" colspan="2" width="250" id="barcode_img_id"></td>
        </tr>
        <tr class="form_caption">
        	<td colspan="2" align="center" style="font-size:14px"><? echo $location_name; //.",".$address; ?></td>
        </tr>
        <tr>
            <td colspan="2" align="center" style="font-size:x-large"><strong><? echo $data[2]; ?></strong></td>
        </tr>
    </table>
    <table width="1070" cellspacing="0" align="center">
    	<tr>
            <td width="300" ><strong>To</strong>,&nbsp;<? echo $attention; ?></td>
            <td width="200"><strong>Req. No:</strong> &nbsp;<? echo $requ_no; ?></td>
            <td width="200" ><strong>Req. Date:</strong>&nbsp;<? if($delivery_date!="" && $delivery_date!="0000-00-00") echo change_date_format($requisition_date); ?></td>
            <td width="200"><strong>Delivery. Date:</strong>&nbsp;<? if($delivery_date!="" && $delivery_date!="0000-00-00") echo change_date_format($delivery_date); ?></td>
            <td align="left"><strong>Pay Mode :</strong>&nbsp;<? echo $pay_mode[$pay_mode_id]; ?></td>
        </tr>
        <tr>
            <td ><? echo $supplier_arr[$supplier_id];  echo $supplier_address;  echo  $lib_country_arr[$country];  echo "<br> Cell :".$supplier_phone;  echo "Email :".$supplier_email; ?></td>
            <td ><strong>Currency :</strong>&nbsp;<? echo $currency[$cbo_currency]; ?></td>
            <td ><strong>Source :</strong>&nbsp;<? echo $source[$source_id]; ?></td>
            <td ><strong>D/O No.:</strong>&nbsp;<? echo $do_no; ?></td>
            <td ><strong>Remarks:</strong>&nbsp;<? echo $remarks; ?></td>
        </tr>
        <tr>
            <td align="right" colspan="5" >&nbsp;</td>
        </tr>
    </table>
    <br>
    <?
      $i=1; $buy_job_sty_val="";
      $mst_id=$dataArray[0][csf('id')];

      $sql_dtls="Select a.id, a.mst_id, a.job_id, a.job_no, a.booking_no, a.buyer_id, a.style_ref_no, a.color_id, a.count_id, a.composition_id, a.com_percent, a.yarn_type_id, a.yarn_inhouse_date, a.cons_uom, a.quantity, a.rate, a.amount, a.remarks
      from inv_purchase_requisition_dtls a where a.mst_id='$data[1]' and a.status_active=1 and a.is_deleted=0 order by a.id";
     //echo $sql_dtls;
      $sql_result = sql_select($sql_dtls);
      $job_independ_check=$sql_result[0][csf("job_id")];
    ?>
    <table align="center" cellspacing="0" width="1230"  border="1" rules="all" class="rpt_table" >
        <thead bgcolor="#dddddd" align="center">
            <th width="20">SL</th>
            <th width="80">Job No</th>
            <th width="80">Fab Booking</th>
            <th width="90">Buyer Name</th>
            <th width="50">Style Reff.</th>
            <th width="70">Yarn Color</th>
            <th width="40">Count</th>
            <th width="140">Composition</th>
            <th width="30">%</th>
            <th width="70">Yarn Type</th>
            <th width="40" >UOM</th>
            <th width="70">Req Qty. </th>
            <th width="50">Rate</th>
            <th width="80">Amount</th>
            <? if($job_independ_check>0){ ?>
            <th width="80">Job Qnty(Pcs)</th>
            <th width="80">FOB</th>
            <th width="70">Shipment date</th>
              <? }?>
            <th width="65">Yarn Inhouse Date</th>
            <th width="65">Remarks</th>
            <th>LC/SC</th>
        </thead>
        <tbody>
		<?

        foreach($sql_result as $row)
        {
          if($array_check[$row[csf("job_no")]]!=$row[csf("job_no")]){
            $array_check[$row[csf("job_no")]] =$row[csf("job_no")];
            $job_numbers .= "'".$row[csf("job_no")]."',";
          }

           $booking_numbers .= "'".$row[csf("booking_no")]."',";
        }

        $job_numbers = chop( $job_numbers,"," );
        $booking_numbers = chop( $booking_numbers, "," );
        $job_sql_query = "select a.job_no, b.sc_lc, b.po_total_price,b.po_quantity,max(b.pub_shipment_date) as pub_shipment_date
        from wo_po_details_master a, wo_po_break_down b
        where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.job_no in($job_numbers)
        group by a.job_no, b.sc_lc,b.po_total_price,b.po_quantity";
        //echo $job_sql_query;//die;
        $sql_job=sql_select($job_sql_query);
        foreach($sql_job as $row)
        {
          $buyer_job_arr[$row[csf("job_no")]]["sc_lc"].=$row[csf("sc_lc")].",";
          //if($FOB_amount_array[$row[csf("job_no")]]==$FOB_amount_array[$row[csf("job_no")]])
          //{
            //$FOB_amount_array[$row[csf("job_no")]]=$row[csf("job_no")];
            $FOB_amount_array[$row[csf("job_no")]]+=$row[csf("po_total_price")];
            $job_qntity_array[$row[csf("job_no")]]+=$row[csf("po_quantity")];
			      $job_ship_date_array[$row[csf("job_no")]]=$row[csf("pub_shipment_date")];
          //}
        }

        $sales_sql_result = sql_select("SELECT sales_booking_no,job_no,within_group,buyer_id,po_buyer FROM fabric_sales_order_mst WHERE status_active=1 and is_deleted=0 and job_no in($job_numbers) and sales_booking_no in($booking_numbers)");

        $salesData = array();
        foreach($sales_sql_result as $row)
        {
            if($row[csf("within_group")]==1)
            {
                $salesData[$row[csf("sales_booking_no")]][$row[csf("job_no")]]['buyer_id'] = $row[csf("po_buyer")];
            }else {
                $salesData[$row[csf("sales_booking_no")]][$row[csf("job_no")]]['buyer_id'] = $row[csf("buyer_id")];
            }
        }

        foreach ($sql_result as $row) {
          $FOB_array[$row[csf("job_no")]]+=1;

        }
        //print_r($FOB_array);//die;
        $job_id_ref=array();
        $i=1;$k=1;
        foreach($sql_result as $row)
        {

            if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
            if($salesData[$row[csf("booking_no")]][$row[csf("job_no")]]['buyer_id']!="")
            {
                $buyerId = $salesData[$row[csf("booking_no")]][$row[csf("job_no")]]['buyer_id'];
            }else {
                $buyerId = $row[csf("buyer_id")];
            }


            if($check_data[$row[csf("job_no")]]!=$row[csf("job_no")])
            {
              $check_data[$row[csf("job_no")]]=$row[csf("job_no")];
              //print_r( $check_data);die;
              $rowspan=1;
            }else{
              $rowspan++;
            }
            if($job_independ_check>0)
            {
                if(!in_array($row[csf("job_no")],$job_id_ref))
                {
                    $job_id_ref[]=$row[csf("job_no")];
                    if($k!=1)
                    {
                        ?>
                        <tr bgcolor="#CCCCCC">
                            <td >&nbsp;</td>
                            <td >&nbsp;</td>
                            <td >&nbsp;</td>
                            <td >&nbsp;</td>
                            <td >&nbsp;</td>
                            <td >&nbsp;</td>
                            <td >&nbsp;</td>
                            <td >&nbsp;</td>
                            <td >&nbsp;</td>
                            <td align="right" colspan="2">Style Total:</td>
                            <td align="right"><? echo number_format($job_wise_qnty,2); ?></td>
                            <td >&nbsp;</td>
                            <td align="right"><? echo number_format($job_wise_amount,4); ?></td>
                            <td >&nbsp;</td>
                            <td >&nbsp;</td>
                            <td >&nbsp;</td>
                            <td >&nbsp;</td>
                            <td >&nbsp;</td>
                           	<td >&nbsp;</td>
                        </tr>
                        <?
                        $job_wise_qnty=$job_wise_amount=0;
                    }
                    $k++;
                }
                ?>
                <tr bgcolor="<? echo $bgcolor; ?>">
                    <td align="center"><? echo $i++; ?></td>
                    <td align="center"><? echo $row[csf("job_no")]; ?></td>
                    <td align="center"><? echo $row[csf("booking_no")]; ?></td>
                    <td align="center"><? echo $buyer_arr[$buyerId]; ?></td>
                    <td align="center"><? echo $row[csf("style_ref_no")]; ?></td>
                    <td align="center"><p><? echo $color_arr[$row[csf("color_id")]]; ?>&nbsp;</p></td>
                    <td align="center"><p><? echo $count_arr[$row[csf("count_id")]]; ?>&nbsp;</p></td>
                    <td align="center"><p><? echo $composition[$row[csf("composition_id")]]; ?></p></td>
                    <td align="center"><p><? echo $row[csf("com_percent")]; ?></p></td>
                    <td align="center"><p><? echo $yarn_type[$row[csf("yarn_type_id")]]; ?></p></td>
                    <td align="center"><p><? echo $unit_of_measurement[$row[csf("cons_uom")]]; ?></p></td>
                    <td align="right"><p><? echo number_format($row[csf("quantity")],2); $grand_tot_qnty+=$row[csf("quantity")]; ?></p></td>
                    <td align="right"><p><? echo number_format($row[csf("rate")],4,".",""); ?></p></td>
                    <td align="right"><p><? echo number_format($row[csf("amount")],4,".",""); $grand_total_val+=$row[csf("amount")];  ?></p></td>
                    <? if($rowspan==1){ ?>
                    <td align="right" rowspan="<? echo $FOB_array[$row[csf("job_no")]];?>" style="vertical-align:middle;"><p>
                      <?
                          echo $job_qntity_array[$row[csf("job_no")]];
                          $grand_total_job_qnty+=$job_qntity_array[$row[csf("job_no")]];
                      ?>
                      </p>
                    </td>
                    <td align="right" rowspan="<? echo $FOB_array[$row[csf("job_no")]];?>" style="vertical-align:middle;"><p>
                      <?
                          echo number_format($FOB_amount_array[$row[csf("job_no")]],2,".","");
                          $grand_total_fob+=$FOB_amount_array[$row[csf("job_no")]];
                      ?>
                      </p>
                    </td>
                    <td rowspan="<? echo $FOB_array[$row[csf("job_no")]];?>"><? echo change_date_format($job_ship_date_array[$row[csf("job_no")]]);?></td>
                    <? }?>
                    <td align="center"><p><? if($row[csf("yarn_inhouse_date")]!="" && $row[csf("yarn_inhouse_date")]!="0000-00-00") echo change_date_format($row[csf("yarn_inhouse_date")]); ?></p></td>
                    <td><p><? echo $row[csf("remarks")]; ?></p></td>
                    <td><p><? echo chop($buyer_job_arr[$row[csf("job_no")]]["sc_lc"],","); ?></p></td>
                </tr>
                <?
                $job_wise_qnty +=$row[csf("quantity")];
                $job_wise_amount +=$row[csf("amount")];
            }
            else
            {
                ?>
                <tr bgcolor="<? echo $bgcolor; ?>">
                    <td align="center"><? echo $i++; ?></td>
                    <td align="center"><? echo $row[csf("job_no")]; ?></td>
                    <td align="center"><? echo $row[csf("booking_no")]; ?></td>
                    <td align="center"><? echo $buyer_arr[$buyerId]; ?></td>
                    <td align="center"><? echo $row[csf("style_ref_no")]; ?></td>
                    <td align="center"><p><? echo $color_arr[$row[csf("color_id")]]; ?>&nbsp;</p></td>
                    <td align="center"><p><? echo $count_arr[$row[csf("count_id")]]; ?>&nbsp;</p></td>
                    <td align="center"><p><? echo $composition[$row[csf("composition_id")]]; ?></p></td>
                    <td align="center"><p><? echo $row[csf("com_percent")]; ?></p></td>
                    <td align="center"><p><? echo $yarn_type[$row[csf("yarn_type_id")]]; ?></p></td>
                    <td align="center"><p><? echo $unit_of_measurement[$row[csf("cons_uom")]]; ?></p></td>
                    <td align="right"><p><? echo number_format($row[csf("quantity")],2); $grand_tot_qnty+=$row[csf("quantity")]; ?></p></td>
                    <td align="right"><p><? echo number_format($row[csf("rate")],4,".",""); ?></p></td>
                    <td align="right"><p><? echo number_format($row[csf("amount")],4,".",""); $grand_total_val+=$row[csf("amount")]; ?></p></td>
                    <td align="center"><p><? if($row[csf("yarn_inhouse_date")]!="" && $row[csf("yarn_inhouse_date")]!="0000-00-00") echo change_date_format($row[csf("yarn_inhouse_date")]); ?></p></td>
                    <td><p><? echo $row[csf("remarks")]; ?></p></td>
                    <td><p><? echo chop($buyer_job_arr[$row[csf("job_no")]]["sc_lc"],","); ?></p></td>
                </tr>
                <?
            }

        }
        if($job_independ_check>0)
        {
            ?>
            <tr bgcolor="#CCCCCC">
                <td >&nbsp;</td>
                <td >&nbsp;</td>
                <td >&nbsp;</td>
                <td >&nbsp;</td>
                <td >&nbsp;</td>
                <td >&nbsp;</td>
                <td >&nbsp;</td>
                <td >&nbsp;</td>
                <td >&nbsp;</td>
                <td align="right" colspan="2">Style Total:</td>
                <td align="right"><? echo number_format($job_wise_qnty,2); ?></td>
                <td >&nbsp;</td>
                <td align="right"><? echo number_format($job_wise_amount,4); ?></td>
                <td >&nbsp;</td>
                <td >&nbsp;</td>
                <td >&nbsp;</td>
                <td >&nbsp;</td>
                <td >&nbsp;</td>
                <td >&nbsp;</td>
            </tr>
            <?
        }
        ?>
        </tbody>
        <tfoot>
          <tr>
                <th colspan="11">Grand Total</th>
                <th><? echo number_format($grand_tot_qnty,2); ?></th>
                <th>&nbsp;</th>
                <th><? echo number_format($grand_total_val,2); ?></th>
                <? if($job_independ_check>0){ ?>
                <th><? echo $grand_total_job_qnty; ?></th>
                <th><? echo number_format($grand_total_fob,2); ?></th>
                <th>&nbsp;</th>
                <? } ?>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>

            </tr>
        </tfoot>
	</table>
    <br>
    <h2>Summery</h2>
    <br>
    <table  width="700" class="rpt_table" border="1" cellpadding="0" cellspacing="0" align="center" rules="all">
        <thead>
            <tr>
                <th width="3%">Sl</th>
                <th width="25%">Composition</th>
                <th width="27%">Color</th>
                <th width="15%">Yarn type</th>
                <th width="15%">Count</th>
                <th>Yarn Qty</th>
            </tr>
        </thead>
        <tbody>
        <?php
  $i = 1;
  $buy_job_sty_val = "";
  $mst_id = $dataArray[0][csf('id')];

  $sql_dtls = "Select a.count_id, a.composition_id, a.color_id,a.yarn_type_id, sum(a.quantity) as yarn_group_total from  inv_purchase_requisition_dtls a where a.mst_id='$data[1]' and a.status_active=1 and a.is_deleted=0 group by a.count_id, a.composition_id,a.color_id,a.yarn_type_id";
  //echo $sql_dtls;//die;
  $sql_result = sql_select($sql_dtls);
  $total = 0;
  foreach ($sql_result as $row) {
  	?>
			<tr>
                <td align="center"><? echo $i++; ?></td>
                <td align="center" ><p><? echo $composition[$row[csf("composition_id")]]; ?></p></td>
                <td align="center" ><p><? echo $color_arr[$row[csf("color_id")]]; ?></p></td>
                <td align="center" ><? echo $yarn_type[$row[csf("yarn_type_id")]]; ?><p><? //echo $composition[$row[csf("composition_id")]]; ?></p></td>
                <td align="center"><p><? echo $count_arr[$row[csf("count_id")]]; ?>&nbsp;</p></td>
                <td align="center"><p><? echo number_format($row[csf("yarn_group_total")],2); $tot_qnty+=$row[csf("yarn_group_total")]; ?>&nbsp;</p></td>
			</tr>
			<?php
  }
  ?>
        </tbody>
        <tfoot>
            <tr>
                <th colspan="5" align="center">Total</th>
                <th align="center"><? echo number_format($tot_qnty,2); ?></th>
            </tr>
        </tfoot>
    </table>
    <br>
    <?
    //echo $job_numbers;
	   //------------------------------ Query for TNA start-----------------------------------
        $job_no_all=explode(",","",$job_numbers);



        $sql_tna_task = "select id,po_number_id,
        (case when task_number=45 then task_start_date else null end) as yarn_requisition_start_date,
        (case when task_number=45 then task_finish_date else null end) as yarn_requisition_end_date,
        (case when task_number=47 then task_start_date else null end) as yarn_rec_start_date,
        (case when task_number=47 then task_finish_date else null end) as yarn_rec_end_date
        from tna_process_mst
        where status_active=1 and job_no in($job_numbers)";

        $tna_start_sql=sql_select( $sql_tna_task);

				$tna_date_task_arr=array();
				foreach($tna_start_sql as $row)
				{

					if($row[csf("yarn_requisition_start_date")]!="" && $row[csf("yarn_requisition_start_date")]!="0000-00-00")
					{
						$tna_date_task_arr[$row[csf("po_number_id")]]['yarn_requisition_start_date']=$row[csf("yarn_requisition_start_date")];
						$tna_date_task_arr[$row[csf("po_number_id")]]['yarn_requisition_end_date']=$row[csf("yarn_requisition_end_date")];
          }
          if($row[csf("yarn_rec_start_date")]!="" && $row[csf("yarn_rec_start_date")]!="0000-00-00")
					{
						$tna_date_task_arr[$row[csf("po_number_id")]]['yarn_rec_start_date']=$row[csf("yarn_rec_start_date")];
						$tna_date_task_arr[$row[csf("po_number_id")]]['yarn_rec_end_date']=$row[csf("yarn_rec_end_date")];
          }

          if($po_number_ids[$row[csf("po_number_id")]] != $row[csf("po_number_id")])
          {
            $all_po_number_ids[$row[csf("po_number_id")]] = $row[csf("po_number_id")];
          }
        }

        $po_num_arr=return_library_array("select id,po_number from wo_po_break_down where id in(".implode(",", $all_po_number_ids).")",'id','po_number');

        //print_r($tna_date_task_arr);//die;

	  //------------------------------ Query for TNA end-----------------------------------

    $task_short_name_arr=return_library_array( "select task_name,task_short_name from lib_tna_task where is_deleted=0 and status_active=1 and task_name in(45,47)",'task_name','task_short_name');

    if(count($task_short_name_arr)>0)
    {
	   ?>

      <fieldset id="div_size_color_matrix" style="max-width:1200;">
        <legend>TNA Information</legend>
        <!--<span style="font-size:180; font-weight:bold;"></span>-->
        <table width="100%" class="rpt_table"  style="border:1px solid black;font-size:12px" border="1" cellpadding="2" cellspacing="0" rules="all">
          <thead>
            <tr>
            	<th width="50"  rowspan="2" align="center" valign="middle">SL</th>
            	<th width="100" rowspan="2"  align="center" valign="middle"><b>Order No</b></th>
              <?
                $i=0;
                foreach ($task_short_name_arr as $key => $value) {
                  ?>
                    <th colspan="2" align="center" valign="middle"><b><? echo $task_short_name_arr[$key];?></b></th>
                  <?
                  $i++;
                }
              ?>
              <!-- <th colspan="2" align="center" valign="top"><b><? //echo $task_short_name_arr[252];?></b></th>
              <th colspan="2" align="center" valign="top"><b><? //echo $task_short_name_arr[47];?></b></th> -->
            </tr>
            <tr>
            	<th width="85" align="center" valign="middle"><b>Start Date</b></th>
              <th width="85" align="center" valign="middle"><b>End Date</b></th>

              <th width="85" align="center" valign="middle"><b>Start Date</b></th>
              <th width="85" align="center" valign="middle"><b>End Date</b></th>
            </tr>
          </thead>
          <tbody>
            <?
              $i=1;
              foreach($tna_date_task_arr as $order_id=>$row)
              {
                ?>
                <tr>
                	<td align="center"><? echo $i; ?></td>
                    <td align="center"><? echo $po_num_arr[$order_id]; ?></td>
                    <td align="center"><? echo change_date_format($row['yarn_requisition_start_date']); ?></td>
                    <td  align="center"><? echo change_date_format($row['yarn_requisition_end_date']); ?></td>

                    <td align="center"><? echo change_date_format($row['yarn_rec_start_date']); ?></td>
                    <td  align="center"><? echo change_date_format($row['yarn_rec_end_date']); ?></td>
                </tr>
                <?
                $i++;
              }
            ?>
          </tbody>
        </table>
      </fieldset>
      <br>
      <?
    }
    echo get_spacial_instruction($requ_no,$width="1070px");
    echo signature_table(102, $data[0], "1070px");
    ?>
    </div>
    <script type="text/javascript" src="../../js/jquery.js"></script>
    <script type="text/javascript" src="../../js/jquerybarcode.js"></script>
    <script>
    	fnc_generate_Barcode('<? echo $varcode_booking_no; ?>','barcode_img_id');
    </script>
    <?
    exit();
}

if($action=="yarn_requisition_print_3")
{
  $data=explode('*',$data);
  echo load_html_head_contents($data[2],"../../", 1, 1, $unicode,'','');

  //echo "jahid";die;

  $com_sql=sql_select("select a.id, a.company_name, a.city from lib_company a where a.id=$data[0]");

  $company_name=$com_sql[0][csf("company_name")];
  $location_name=$com_sql[0][csf("city")];
  //$address=$com_sql[0][csf("address")];
  $count_arr=return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');
  $buyer_arr=return_library_array( "select id, buyer_name from lib_buyer ",'id','buyer_name');


  if($db_type==0)
  {
    $sql_data = sql_select("SELECT a.id, a.requ_prefix_num, a.requ_no, a.item_category_id, a.supplier_id, a.delivery_date, a.pay_mode, a.requisition_date, a.cbo_currency, a.source, a.attention, a.do_no, a.dealing_marchant, a.remarks, b.buyer_id, group_concat(b.job_id) as job_id,a.basis FROM  inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b WHERE a.id=b.mst_id and a.id = $data[1] group by a.id, a.requ_prefix_num, a.requ_no, a.item_category_id, a.supplier_id, a.delivery_date, a.pay_mode, a.requisition_date, a.cbo_currency, a.source, a.attention, a.do_no, a.dealing_marchant, a.remarks, b.buyer_id,a.basis");
  }
  else if($db_type==2)
  {
    $sql_data = sql_select("SELECT a.id, a.requ_prefix_num, a.requ_no, a.item_category_id, a.supplier_id, a.delivery_date, a.pay_mode, a.requisition_date, a.cbo_currency, a.source, a.attention, a.do_no, a.dealing_marchant, a.remarks, b.buyer_id, LISTAGG(CAST(b.job_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.job_id) as job_id,a.basis FROM  inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b WHERE a.id=b.mst_id and a.id = $data[1] group by a.id, a.requ_prefix_num, a.requ_no, a.item_category_id, a.supplier_id, a.delivery_date, a.pay_mode, a.requisition_date, a.cbo_currency, a.source, a.attention, a.do_no, a.dealing_marchant, a.remarks, b.buyer_id,a.basis");
  }
  $job_all_id="";$buyer_name='';
  foreach($sql_data as $row)
  {
    if($buyer_name!='') $buyer_name.=",";
    $buyer_name.=$buyer_arr[$row[csf("buyer_id")]];
    $requ_prefix_num=$row[csf("requ_prefix_num")];
    $requ_no=$row[csf("requ_no")];
    $item_category_id=$row[csf("item_category_id")];
    $supplier_id=$row[csf("supplier_id")];
    $delivery_date=$row[csf("delivery_date")];
    $requisition_date=$row[csf("requisition_date")];
    $cbo_currency=$row[csf("cbo_currency")];
    $pay_mode_id=$row[csf("pay_mode")];
    $source_id=$row[csf("source")];
    $attention=$row[csf("attention")];
    $do_no=$row[csf("do_no")];
    $dealing_marchant=$row[csf("dealing_marchant")];
    $remarks=$row[csf("remarks")];
    $job_id_all=array_unique(explode(",",$row[csf("job_id")]));
    foreach($job_id_all as $job_id)
    {
      if($job_all_id=="") $job_all_id=$job_id; else $job_all_id.=",".$job_id;
    }

  }


  if($job_all_id!="")
  {
    $sql_job=sql_select("select a.id, min(b.po_received_date) as po_received_date, min(b.pub_shipment_date) as pub_shipment_date from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id in($job_all_id) group by a.id");
    foreach($sql_job as $row)
    {
      $buyer_job_arr[$row[csf("id")]]["po_received_date"]=$row[csf("po_received_date")];
      $buyer_job_arr[$row[csf("id")]]["pub_shipment_date"]=$row[csf("pub_shipment_date")];
    }
  }




  $sql_supplier = sql_select("SELECT id,supplier_name,contact_no,country_id,web_site,email,address_1,address_2,address_3,address_4 FROM  lib_supplier WHERE id = $supplier_id");

   foreach($sql_supplier as $supplier_data)
  {//contact_no
    $row_mst[csf('supplier_id')];

    if($supplier_data[csf('address_1')]!='')$address_1 = $supplier_data[csf('address_1')].','.' ';else $address_1='';
    if($supplier_data[csf('address_2')]!='')$address_2 = $supplier_data[csf('address_2')].','.' ';else $address_2='';
    if($supplier_data[csf('address_3')]!='')$address_3 = $supplier_data[csf('address_3')].','.' ';else $address_3='';
    if($supplier_data[csf('address_4')]!='')$address_4 = $supplier_data[csf('address_4')].','.' ';else $address_4='';
    if($supplier_data[csf('contact_no')]!='')$contact_no = $supplier_data[csf('contact_no')].','.' ';else $contact_no='';
    if($supplier_data[csf('web_site')]!='')$web_site = $supplier_data[csf('web_site')].','.' ';else $web_site='';
    if($supplier_data[csf('email')]!='')$email = $supplier_data[csf('email')].','.' ';else $email='';
    //if($supplier_data[csf('country_id')]!=0)$country = $supplier_data[csf('country_id')].','.' ';else $country='';
    $country = $supplier_data['country_id'];

    $supplier_address = $address_1;
    $supplier_country =$country;
    $supplier_phone =$contact_no;
    $supplier_email = $email;
  }
  $image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$data[0]'","image_location");
  $varcode_booking_no=$requ_no;
  ?>
  <div style="width:1030px;">
    <table width="1000" cellspacing="0" align="center">
        <tr>
          <td rowspan="3" width="70"><img src="../../<? echo $image_location; ?>" height="70" width="200"></td>
            <td colspan="2" style="font-size:xx-large;" align="center"><strong><? echo $company_name; ?></strong></td>
            <td rowspan="3" colspan="2" width="250" id="barcode_img_id"> </td>
        </tr>
        <tr class="form_caption">
          <td colspan="2" align="center" style="font-size:14px"><? echo $location_name; //.",".$address; ?></td>
        </tr>
        <tr>
            <td colspan="2" align="center" style="font-size:x-large"><strong><? echo $data[2]; ?></strong></td>
        </tr>
    </table>
    <table width="1000" cellspacing="0" align="center">
         <tr>
            <td width="300" ><strong>To</strong>,&nbsp;<? echo $attention; ?></td>
          </tr>
          <tr>
            <td width="175"><strong>Dealing Merchant:</strong>&nbsp;<? echo $dealing_marchant; ?></td>
            <td width="175"><strong>Req. No:</strong> &nbsp;<? echo $requ_no; ?></td>
            <td width="175" ><strong>Req. Date:</strong>&nbsp;<? if($delivery_date!="" && $delivery_date!="0000-00-00") echo change_date_format($requisition_date); ?></td>
            <td width="175"><strong>Delivery. Date:</strong>&nbsp;<? if($delivery_date!="" && $delivery_date!="0000-00-00") echo change_date_format($delivery_date); ?></td>
            <td align="left"><strong>Pay Mode :</strong>&nbsp;<? echo $pay_mode[$pay_mode_id]; ?></td>
        </tr>
        <tr>
            <td ><? echo $supplier_arr[$supplier_id];  echo $supplier_address;  echo  $lib_country_arr[$country];  echo "<br> <b>Cell</b> :".$supplier_phone;  echo "<b>Email</b> :".$supplier_email; ?></td>
            <td ><strong>Currency :</strong>&nbsp;<? echo $currency[$cbo_currency]; ?></td>
            <td ><strong>Source :</strong>&nbsp;<? echo $source[$source_id]; ?></td>
            <td ><strong>D/O No.:</strong>&nbsp;<? echo $do_no; ?></td>
            <td ><strong>Remarks:</strong>&nbsp;<? echo $remarks; ?></td>
        </tr>
        <tr>
            <td ><strong>Buyer:</strong>&nbsp;<? echo $buyer_name; ?></td>
        </tr>

        <tr>
            <td align="right" colspan="5" >&nbsp;</td>
        </tr>
    </table>
    <br>
    <table align="center" cellspacing="0" width="1000"  border="1" rules="all" class="rpt_table" >
      <thead bgcolor="#dddddd" align="center">
          <th width="30">SL</th>
          <th width="110">Yarn Color</th>
          <th width="50">Count</th>
          <th width="140">Composition</th>
          <th width="30">%</th>
          <th width="70">Yarn Type</th>
          <th width="40" >UOM</th>
          <th width="70">Req Qty. </th>
          <th width="50">Rate</th>
          <th width="80">Amount</th>
          <th width="70">OPD</th>
          <th width="70">TOD</th>
          <th width="40">Lead Time (Days)</th>
          <th width="70">Yarn Inhouse Date</th>
          <th >Remarks</th>
      </thead>
      <tbody>
        <?
          $i=1; $buy_job_sty_val="";
          $mst_id=$dataArray[0][csf('id')];

          if($data[4] == 4)  //sales order
          {
            $fso_or_job_text = "FSO No";
          }else{
            $fso_or_job_text = "Job No";
          }
          //$sql_dtls="Select a.id, a.mst_id, a.job_id, a.job_no, a.buyer_id, a.style_ref_no, a.color_id, a.count_id, a.composition_id, a.com_percent, a.yarn_type_id, a.yarn_inhouse_date, a.cons_uom, a.quantity, a.rate, a.amount, a.remarks from  inv_purchase_requisition_dtls a where a.mst_id='$data[1]' and a.status_active=1 and a.is_deleted=0 order by a.id";

          $sql_dtls = " select a.id, a.mst_id, a.job_id , a.job_no ,a.booking_no, a.buyer_id, a.style_ref_no, a.color_id, a.count_id, a.composition_id,
           a.com_percent, a.yarn_type_id, a.yarn_inhouse_date, a.cons_uom, a.quantity, a.rate, a.amount, a.remarks ,c.within_group,d.job_no as po_job
           from inv_purchase_requisition_mst b,inv_purchase_requisition_dtls a
           left join fabric_sales_order_mst c on a.job_no = c.job_no
           left join wo_booking_dtls d on c.sales_booking_no = d.booking_no
           where a.mst_id = b.id and a.mst_id='$data[1]' and a.status_active=1 and a.is_deleted=0
            group by a.id, a.mst_id, a.job_id , a.job_no ,a.booking_no, a.buyer_id, a.style_ref_no, a.color_id, a.count_id, a.composition_id,
          a.com_percent, a.yarn_type_id, a.yarn_inhouse_date, a.cons_uom, a.quantity, a.rate, a.amount, a.remarks ,c.within_group,d.job_no
           order by a.id";
          //echo $sql_dtls;
          $sql_result = sql_select($sql_dtls);

          foreach($sql_result as $row)
          {
             $job_numbers .= "'".$row[csf("job_no")]."',";
             $booking_numbers .= "'".$row[csf("booking_no")]."',";
          }

          $job_numbers = chop( $job_numbers,"," );
          $booking_numbers = chop( $booking_numbers, "," );

          $sales_sql_result = sql_select("SELECT sales_booking_no,job_no,within_group,buyer_id,po_buyer FROM fabric_sales_order_mst WHERE status_active=1 and is_deleted=0 and job_no in($job_numbers) and sales_booking_no in($booking_numbers)");

          $salesData = array();
          foreach($sales_sql_result as $row)
          {
              if($row[csf("within_group")]==1)
              {
                  $salesData[$row[csf("sales_booking_no")]][$row[csf("job_no")]]['buyer_id'] = $row[csf("po_buyer")];
              }else {
                  $salesData[$row[csf("sales_booking_no")]][$row[csf("job_no")]]['buyer_id'] = $row[csf("buyer_id")];
              }
          }


          $job_independ_check=$sql_result[0][csf("job_id")];
          $job_id_ref=array();
          $i=1;$k=1;
          foreach($sql_result as $row)
          {
            if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

            if($salesData[$row[csf("booking_no")]][$row[csf("job_no")]]['buyer_id']!="")
            {
                 $buyerId = $salesData[$row[csf("booking_no")]][$row[csf("job_no")]]['buyer_id'];
            }else {
                 $buyerId = $row[csf("buyer_id")];
            }

            if($job_independ_check>0)
            {
                if(!in_array($row[csf("job_id")],$job_id_ref))
                {
                  $job_id_ref[]=$row[csf("job_id")];

                  if($k!=1)
                  {
                    ?>
                      <tr bgcolor="#CCCCCC">
                          <td >&nbsp;</td>
                          <td >&nbsp;</td>
                          <td >&nbsp;</td>
                          <td >&nbsp;</td>
                          <td >&nbsp;</td>
                          <td align="right" colspan="2"><? echo $fso_or_job_text;?> Total:</td>
                          <td align="right"><? echo number_format($job_wise_qnty,2); ?></td>
                          <td >&nbsp;</td>
                          <td align="right"><? echo number_format($job_wise_amount,4); ?></td>
                          <td >&nbsp;</td>
                          <td >&nbsp;</td>
                          <td >&nbsp;</td>
                          <td >&nbsp;</td>
                          <td >&nbsp;</td>
                      </tr>
                      <?
                    $job_wise_qnty=$job_wise_amount=0;
                  }
                  ?>
                  <tr bgcolor="#FFFFCC">
                    <td colspan="15"><? echo $fso_or_job_text;?> : <? echo $row[csf("job_no")];?> &nbsp;&nbsp;Buyer Name : <? echo $buyer_arr[$buyerId];?> &nbsp;&nbsp; Style : <? echo $row[csf("style_ref_no")];

                    if($row[csf("within_group")] == 1) {
                      echo "&nbsp;&nbsp; Job No: ".$row[csf("po_job")] ;
                    } echo " &nbsp;&nbsp; Booking No: ".$row[csf("booking_no")];
                    ?>
                    </td>
                  </tr>
                  <?
                  $k++;
                }
                ?>
                  <tr bgcolor="<? echo $bgcolor; ?>">
                      <td align="center"><? echo $i; ?></td>
                      <td align="center"><p><? echo $color_arr[$row[csf("color_id")]]; ?>&nbsp;</p></td>
                      <td align="center"><p><? echo $count_arr[$row[csf("count_id")]]; ?>&nbsp;</p></td>
                      <td ><p><? echo $composition[$row[csf("composition_id")]]; ?></p></td>
                      <td align="center"><p><? echo $row[csf("com_percent")]; ?></p></td>
                      <td align="center"><p><? echo $yarn_type[$row[csf("yarn_type_id")]]; ?></p></td>
                      <td align="center"><p><? echo $unit_of_measurement[$row[csf("cons_uom")]]; ?></p></td>
                      <td align="right"><p><? echo number_format($row[csf("quantity")],2); $grand_tot_qnty+=$row[csf("quantity")]; ?></p></td>
                      <td align="right"><p><? echo number_format($row[csf("rate")],4,".",""); ?></p></td>
                      <td align="right"><p><? echo number_format($row[csf("amount")],4,".",""); $grand_total_val+=$row[csf("amount")];  ?></p></td>
                      <td align="center"><p><? if($buyer_job_arr[$row[csf("job_id")]]["po_received_date"]!="" && $buyer_job_arr[$row[csf("job_id")]]["po_received_date"]!="0000-00-00") echo change_date_format($buyer_job_arr[$row[csf("job_id")]]["po_received_date"]); else echo "&nbsp;"; ?></p></td>
                      <td align="center"><p><? if($buyer_job_arr[$row[csf("job_id")]]["pub_shipment_date"]!="" && $buyer_job_arr[$row[csf("job_id")]]["pub_shipment_date"]!="0000-00-00") echo change_date_format($buyer_job_arr[$row[csf("job_id")]]["pub_shipment_date"]); else echo "&nbsp;"; ?></p></td>
                      <td align="center"><p><?  $days_remian=datediff("d",$buyer_job_arr[$row[csf("job_id")]]["po_received_date"],$buyer_job_arr[$row[csf("job_id")]]["pub_shipment_date"]); if($days_remian!="")  echo $days_remian; ?></p></td>
                      <td align="center"><p><? if($row[csf("yarn_inhouse_date")]!="" && $row[csf("yarn_inhouse_date")]!="0000-00-00") echo change_date_format($row[csf("yarn_inhouse_date")]); ?></p></td>
                      <td ><p><? echo $row[csf("remarks")]; ?></p></td>
                  </tr>
                <?
                $job_wise_qnty +=$row[csf("quantity")];
                $job_wise_amount +=$row[csf("amount")];

            }
            else
            {
              ?>
                      <tr bgcolor="<? echo $bgcolor; ?>">
                          <td align="center"><? echo $i; ?></td>
                          <td align="center"><p><? echo $color_arr[$row[csf("color_id")]]; ?>&nbsp;</p></td>
                          <td align="center"><p><? echo $count_arr[$row[csf("count_id")]]; ?>&nbsp;</p></td>
                          <td ><p><? echo $composition[$row[csf("composition_id")]]; ?></p></td>
                          <td align="center"><p><? echo $row[csf("com_percent")]; ?></p></td>
                          <td align="center"><p><? echo $yarn_type[$row[csf("yarn_type_id")]]; ?></p></td>
                          <td align="center"><p><? echo $unit_of_measurement[$row[csf("cons_uom")]]; ?></p></td>
                          <td align="right"><p><? echo number_format($row[csf("quantity")],2); $grand_tot_qnty+=$row[csf("quantity")]; ?></p></td>
                          <td align="right"><p><? echo number_format($row[csf("rate")],4,".",""); ?></p></td>
                          <td align="right"><p><? echo number_format($row[csf("amount")],4,".",""); $grand_total_val+=$row[csf("amount")]; ?></p></td>
                          <td align="center"><p><? //echo $row[csf("com_percent")]; ?></p></td>
                          <td align="center"><p><? //echo $row[csf("com_percent")]; ?></p></td>
                          <td align="center"><p><? //echo $row[csf("com_percent")]; ?></p></td>
                          <td align="center"><p><? if($row[csf("yarn_inhouse_date")]!="" && $row[csf("yarn_inhouse_date")]!="0000-00-00") echo change_date_format($row[csf("yarn_inhouse_date")]); ?></p></td>
                          <td ><p><? echo $row[csf("remarks")]; ?></p></td>
                      </tr>
                      <?
            }
            //if
            $i++;
          }
          if($job_independ_check>0)
          {
            ?>
                <tr bgcolor="#CCCCCC">
                      <td >&nbsp;</td>
                      <td >&nbsp;</td>
                      <td >&nbsp;</td>
                      <td >&nbsp;</td>
                      <td >&nbsp;</td>
                      <td align="right" colspan="2"><? echo $fso_or_job_text; ?> Total:</td>
                      <td align="right"><? echo number_format($job_wise_qnty,2); ?></td>
                      <td >&nbsp;</td>
                      <td align="right"><? echo number_format($job_wise_amount,4); ?></td>
                      <td >&nbsp;</td>
                      <td >&nbsp;</td>
                      <td >&nbsp;</td>
                      <td >&nbsp;</td>
                      <td >&nbsp;</td>
                  </tr>
                  <?
          }
        ?>
    </tbody>
    <tfoot>
      <tr>
          <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th colspan="2">Grand Total</th>
            <th><? echo number_format($grand_tot_qnty,2); ?></th>
            <th>&nbsp;</th>
            <th><? echo number_format($grand_total_val,4); ?></th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>

        </tr>
    </tfoot>
  </table>
    <br>

     <?
        echo get_spacial_instruction($requ_no,$width="1000px");
	    echo signature_table(102, $data[0], "900px");
     ?>
  </div>
     <script type="text/javascript" src="../../js/jquery.js"></script>
    <script type="text/javascript" src="../../js/jquerybarcode.js"></script>
    <script>
    fnc_generate_Barcode('<? echo $varcode_booking_no; ?>','barcode_img_id');
    </script>
  <?
    exit();
}

if($action=="yarn_requisition_print_4")
{
  $data=explode('*',$data);
  echo load_html_head_contents($data[2],"../../", 1, 1, $unicode,'','');

  $com_sql=sql_select("select a.id, a.company_name, a.city from lib_company a where a.id=$data[0]");

  $company_name=$com_sql[0][csf("company_name")];
  $location_name=$com_sql[0][csf("city")];
  $count_arr=return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');
  if($db_type==0)
  {
    $sql_data = sql_select("SELECT a.id, a.requ_prefix_num, a.requ_no, a.item_category_id, a.supplier_id, a.delivery_date, a.pay_mode, a.requisition_date, a.cbo_currency, a.source, a.attention, a.do_no, a.remarks, group_concat(b.job_id) as job_id FROM  inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b WHERE a.id=b.mst_id and a.id = $data[1] group by a.id, a.requ_prefix_num, a.requ_no, a.item_category_id, a.supplier_id, a.delivery_date, a.pay_mode, a.requisition_date, a.cbo_currency, a.source, a.attention, a.do_no, a.remarks");
  }
  else if($db_type==2)
  {
    $sql_data = sql_select("SELECT a.id, a.requ_prefix_num, a.requ_no, a.item_category_id, a.supplier_id, a.delivery_date, a.pay_mode, a.requisition_date, a.cbo_currency, a.source, a.attention, a.do_no, a.remarks, LISTAGG(CAST(b.job_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.job_id) as job_id FROM  inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b WHERE a.id=b.mst_id and a.id = $data[1] group by a.id, a.requ_prefix_num, a.requ_no, a.item_category_id, a.supplier_id, a.delivery_date, a.pay_mode, a.requisition_date, a.cbo_currency, a.source, a.attention, a.do_no, a.remarks");
  }

  foreach($sql_data as $row)
  {
    $requ_prefix_num=$row[csf("requ_prefix_num")];
    $requ_no=$row[csf("requ_no")];
    $item_category_id=$row[csf("item_category_id")];
    $supplier_id=$row[csf("supplier_id")];
    $delivery_date=$row[csf("delivery_date")];
    $requisition_date=$row[csf("requisition_date")];
    $cbo_currency=$row[csf("cbo_currency")];
    $pay_mode_id=$row[csf("pay_mode")];
    $source_id=$row[csf("source")];
    $attention=$row[csf("attention")];
    $do_no=$row[csf("do_no")];
    $remarks=$row[csf("remarks")];
  }

  $varcode_booking_no=$requ_no;
  ?>
  <div style="width:690px;">
    <table width="650" cellspacing="0" align="center">
        <tr>
            <td colspan="2" style="font-size:xx-large;" align="center"><strong><? echo $company_name; ?></strong></td>
        </tr>
        <tr>
            <td colspan="2" align="center" style="font-size:x-large"><strong><? echo $data[2]; ?></strong></td>
        </tr>
    </table>
    <br>
    <table width="650" cellspacing="0" align="center">
        <tr>
            <td width="300"><strong>Req. No:</strong> &nbsp;<? echo $requ_no; ?></td>
            <td width="175" ><strong>Req. Date:</strong>&nbsp;<? if($delivery_date!="" && $delivery_date!="0000-00-00") echo change_date_format($requisition_date); ?></td>
            <td><strong>Delivery. Date:</strong>&nbsp;<? if($delivery_date!="" && $delivery_date!="0000-00-00") echo change_date_format($delivery_date); ?></td>
        </tr>

        <tr>
            <td align="right" colspan="3" >&nbsp;</td>
        </tr>
    </table>
    <br>
    <table  width="650"  cellpadding="0" cellspacing="0" align="center" rules="all">
    <tr><td>Summery</td></tr>
    </table>
    <br>
      <table  width="650" class="rpt_table" border="1" cellpadding="0" cellspacing="0" align="center" rules="all">
          <thead>
              <tr>
                  <th width="3%">Sl</th>
                  <th width="20%">Count</th>
                  <th width="20%">Yarn type</th>
                  <th width="37%">Composition</th>
                  <th>Yarn Qty</th>
              </tr>
          </thead>
          <tbody>
             <!-- write the code -->
             <?php
              $i = 1;
              $buy_job_sty_val = "";
              $mst_id = $dataArray[0][csf('id')];

              $sql_dtls = "Select a.count_id, a.composition_id,a.yarn_type_id, sum(a.quantity) as yarn_group_total from  inv_purchase_requisition_dtls a where a.mst_id='$data[1]' and a.status_active=1 and a.is_deleted=0 group by a.count_id, a.composition_id,a.yarn_type_id";
              //echo $sql_dtls;//die;
              $sql_result = sql_select($sql_dtls);
              $total = 0;
              foreach ($sql_result as $row) {
              	?>
                  <tr>
                    <td align="center"><? echo $i++; ?></td>
                     <td align="center"><p><? echo $count_arr[$row[csf("count_id")]]; ?>&nbsp;</p></td>
                    <!-- <td align="center"><p><? echo $row[csf("yarn_group_total")]; ?>&nbsp;</p></td> -->
                     <td align="center" ><? echo $yarn_type[$row[csf("yarn_type_id")]]; ?><p><? //echo $composition[$row[csf("composition_id")]]; ?></p></td>
                     <td align="center" ><p><? echo $composition[$row[csf("composition_id")]]; ?></p></td>
                    <td align="center"><p><? echo number_format($row[csf("yarn_group_total")],2); $tot_qnty+=$row[csf("yarn_group_total")]; ?>&nbsp;</p></td>

                  </tr>

                  <?php
                }

                ?>
          </tbody>
          <tfoot>
            <tr>
                <th colspan="4" align="center">Total</th>
                <th align="center"><? echo number_format($tot_qnty,2); ?></th>
            </tr>
          </tfoot>
      </table>
      <br>
      <table  width="650"  cellpadding="0" cellspacing="0" align="center" rules="all">
      <tr><td>
       <?
          echo get_spacial_instruction($requ_no,$width="650px");
          echo signature_table(102, $data[0], "650px");
       ?>
      </td></tr>
    </table>
    </div>
       <script type="text/javascript" src="../../js/jquery.js"></script>
    <?
      exit();
}

if($action=="yarn_allocation_pop")
{
	echo load_html_head_contents("Report Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	
	$sqlWO="select a.fabric_source,b.po_break_down_id,a.booking_no from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.item_category in(2,3,13) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.po_break_down_id in ($order_id)";
	 $resultWo=sql_select($sqlWO);
	 $fab_source_ids="";
		foreach($resultWo as $woRow)
		{
			 $fab_source_ids.=$woRow[csf('fabric_source')].',';
		}
		$fab_source=rtrim($fab_source_ids,',');
		$fab_source_id=array_unique(explode(",",$fab_source));
?>
<script>

	function print_window()
	{
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
	
		d.close();
	}	
	
</script>	

	<div style="width:675px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
	<fieldset style="width:670px; margin-left:7px">
		<div id="report_container">
			<table border="1" class="rpt_table" rules="all" width="650" cellpadding="0" cellspacing="0">
                <thead>
                    <tr>
                    	<th width="40">SL</th>
                        <th width="100">Lot</th>
                        <th width="70">Count</th>
                        <th width="200">Composition</th>
                        <th width="130">Supplier</th>
                        <th>Allocated Qty</th>
                    </tr>
				</thead>
                <?
				if($yarn_comp_type2nd!='') $yarn_comp_type2nd_cond="and c.yarn_comp_type2nd='$yarn_comp_type2nd'"; else $yarn_comp_type2nd_cond="";
				if($yarn_comp_percent2nd!='') $yarn_comp_percent2nd_cond="and c.yarn_comp_percent2nd='$yarn_comp_percent2nd'"; else $yarn_comp_percent2nd_cond="";
				$sql="select a.po_break_down_id, sum(a.qnty) as allocation_qty, c.lot, c.yarn_type, c.yarn_count_id, c.product_name_details, c.supplier_id from inv_material_allocation_dtls a, product_details_master c where a.item_id=c.id and a.po_break_down_id in ($order_id) and c.yarn_count_id='$yarn_count' and c.yarn_comp_type1st='$yarn_comp_type1st' and c.yarn_comp_percent1st='$yarn_comp_percent1st' $yarn_comp_type2nd_cond $yarn_comp_percent2nd_cond and c.yarn_type='$yarn_type_id' and a.status_active=1 and a.is_deleted=0 and a.item_category=1 group by a.po_break_down_id, c.lot, c.yarn_type, c.yarn_count_id, c.product_name_details, c.supplier_id";
				
                $total_allocation_qty=0; $i=1;
                $result=sql_select($sql);
				foreach($result as $row)
				{
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
					
					foreach($fab_source_id as $fsid)
					{
						if($fsid==1)
						{
							$allocation_qty=$row[csf('allocation_qty')];
						}
						
					}
                    ?>
                    <tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                    	<td width="40"><? echo $i; ?></td>
                    	<td width="100"><p><? echo $row[csf('lot')]; ?></p></td>
                        <td width="70"><? echo $yarn_count_details[$row[csf('yarn_count_id')]]; ?></td>
                        <td width="200"><p><? echo $row[csf('product_name_details')]; ?></p></td>
                        <td width="130"><p><? echo $supplier_details[$row[csf('supplier_id')]]; ?></p></td>
                        <td align="right"><? echo number_format($allocation_qty,2); ?> </td>
                    </tr>
                <?
					$total_allocation_qty+=$allocation_qty;
					$i++;
                }
				unset($result);
                ?>
                <tfoot>
                	<th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th>Total Allocation</th>
                    <th><? echo number_format($total_allocation_qty,2);?></th>
                </tfoot>
            </table>	
		</div>
	</fieldset>  
<?
exit();
}

if ($action=="yarn_issue_not") 
{
    echo load_html_head_contents("Report Info", "../../../../", 1, 1, '', '', '');
    extract($_REQUEST);
    $brand_array = return_library_array("select id, brand_name from lib_brand", "id", "brand_name");
    $yarn_desc_array = explode(",", $yarn_count);
    //print_r($yarn_desc_array);
    ?>
    <script>

        function print_window()
        {

            var w = window.open("Surprise", "#");
            var d = w.document.open();
            d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' +
                    '<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>' + document.getElementById('report_container').innerHTML + '</body</html>');

            d.close();
        }

    </script>	
    <div style="width:970px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
    <fieldset style="width:970px; margin-left:3px">
        <div id="report_container">
        <table border="1" class="rpt_table" rules="all" width="300" cellpadding="0" cellspacing="0" align="center">
            	<thead>
                	<tr>
						<th colspan="3"><b>TNA Details</b></th>
                    </tr>
                    <tr>
                    	<th>Date Type</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                    </tr>
				</thead>
                <tbody>
                <? $yarn_tna="select task_start_date, task_finish_date, actual_start_date, actual_finish_date from tna_process_mst where task_number=50 and po_number_id in ($order_id) and is_deleted=0 and status_active=1"; 
				
				$tna_sql=sql_select($yarn_tna);
				?>
                    <tr bgcolor="#E9F3FF">
                    	<td>Plan</td>
                        <td><? if($tna_sql[0][csf('task_start_date')]=="" || $tna_sql[0][csf('task_start_date')]=='0000-00-00') echo ""; else echo change_date_format($tna_sql[0][csf('task_start_date')]); ?></td>
                        <td><? if($tna_sql[0][csf('task_finish_date')]=="" || $tna_sql[0][csf('task_finish_date')]=='0000-00-00') echo ""; else echo change_date_format($tna_sql[0][csf('task_finish_date')]); ?></td>
					</tr>
                	<tr bgcolor="#FFFFFF">
                    	<td>Actual</td>
                        <td><? if($tna_sql[0][csf('actual_start_date')]=="" || $tna_sql[0][csf('actual_start_date')]=='0000-00-00') echo ""; else echo change_date_format($tna_sql[0][csf('actual_start_date')]); ?></td>
                        <td><? if($tna_sql[0][csf('actual_finish_date')]=="" || $tna_sql[0][csf('actual_finish_date')]=='0000-00-00') echo ""; else echo change_date_format($tna_sql[0][csf('actual_finish_date')]); ?></td>
					</tr>
                </tbody>
            </table>
            
            <table border="1" class="rpt_table" rules="all" width="960" cellpadding="0" cellspacing="0">
                <thead>
                <th colspan="10"><b>Yarn Issue</b></th>
                </thead>
                <thead>
                <th width="105">Issue Id</th>
                <th width="90">Issue To</th>
                <th width="105">Booking No</th>
                <th width="70">Challan No</th>
                <th width="75">Issue Date</th>
                <th width="70">Brand</th>
                <th width="60">Lot No</th>
                <th width="180">Yarn Description</th>
                <th width="90">Issue Qnty (In)</th>
                <th>Issue Qnty (Out)</th>
                </thead>
                <?
                $i = 1;
                $total_yarn_issue_qnty = 0;
                $total_yarn_issue_qnty_out = 0;
                $yarn_desc_array_for_return = array();
                $sql_yarn_iss = "select b.yarn_count_id, b.yarn_comp_type1st, b.yarn_comp_percent1st, b.yarn_comp_type2nd, b.yarn_comp_percent2nd, b.yarn_type from order_wise_pro_details a, product_details_master b where a.prod_id=b.id and a.po_breakdown_id in ($order_id) and a.entry_form in(3,9) and b.item_category_id=1 and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1 and a.issue_purpose in (1,4) group by b.yarn_count_id, b.yarn_comp_type1st, b.yarn_comp_percent1st, b.yarn_comp_type2nd, b.yarn_comp_percent2nd, b.yarn_type";
                $dataArrayIssue = sql_select($sql_yarn_iss);
                foreach ($dataArrayIssue as $row_yarn_iss) {
                    if ($row_yarn_iss[csf('yarn_comp_percent2nd')] != 0) {
                        $compostion_not_req = $composition[$row_yarn_iss[csf('yarn_comp_type1st')]] . " " . $row_yarn_iss[csf('yarn_comp_percent1st')] . " %" . " " . $composition[$row_yarn_iss[csf('yarn_comp_type2nd')]] . " " . $row_yarn_iss[csf('yarn_comp_percent2nd')] . " %";
                    } else {
                        $compostion_not_req = $composition[$row_yarn_iss[csf('yarn_comp_type1st')]] . " " . $row_yarn_iss[csf('yarn_comp_percent1st')] . " %" . " " . $composition[$row_yarn_iss[csf('yarn_comp_type2nd')]];
                    }

                    $desc = $yarn_count_details[$row_yarn_iss[csf('yarn_count_id')]] . " " . $compostion_not_req . " " . $yarn_type[$row_yarn_iss[csf('yarn_type')]];

                    $yarn_desc_for_return = $row_yarn_iss[csf('yarn_count_id')] . "__" . $row_yarn_iss[csf('yarn_comp_type1st')] . "__" . $row_yarn_iss[csf('yarn_comp_percent1st')] . "__" . $row_yarn_iss[csf('yarn_comp_type2nd')] . "__" . $row_yarn_iss[csf('yarn_comp_percent2nd')] . "__" . $row_yarn_iss[csf('yarn_type')];

                    $yarn_desc_array_for_return[$desc] = $yarn_desc_for_return;

                    if (!in_array($desc, $yarn_desc_array)) {
                        $sql = "select a.issue_number, a.issue_date, a.challan_no, a.knit_dye_source, a.knit_dye_company, a.booking_no, sum(b.quantity) as issue_qnty, c.lot, c.yarn_type, c.id as prod_id, c.product_name_details, c.brand as brand_id, d.requisition_no from inv_issue_master a, order_wise_pro_details b, product_details_master c, inv_transaction d where a.id=d.mst_id and d.transaction_type=2 and d.item_category=1 and d.id=b.trans_id and b.trans_type=2 and b.entry_form=3 and b.po_breakdown_id in ($order_id) and b.prod_id=c.id and c.yarn_count_id='" . $row_yarn_iss[csf('yarn_count_id')] . "' and c.yarn_comp_type1st='" . $row_yarn_iss[csf('yarn_comp_type1st')] . "' and c.yarn_comp_percent1st='" . $row_yarn_iss[csf('yarn_comp_percent1st')] . "' and c.yarn_comp_type2nd='" . $row_yarn_iss[csf('yarn_comp_type2nd')] . "' and c.yarn_comp_percent2nd='" . $row_yarn_iss[csf('yarn_comp_percent2nd')] . "' and c.yarn_type='" . $row_yarn_iss[csf('yarn_type')] . "' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.issue_purpose in (1,4) group by a.id, c.id, a.issue_number, a.issue_date, a.challan_no, a.knit_dye_source, a.knit_dye_company, a.booking_no, c.lot, c.yarn_type, c.product_name_details, c.brand, d.requisition_no";
                        $result = sql_select($sql);
                        foreach ($result as $row) 
						{
                            if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";

                            if ($row[csf('knit_dye_source')] == 1) $issue_to = $company_library[$row[csf('knit_dye_company')]]; else if ($row[csf('knit_dye_source')] == 3)  $issue_to = $supplier_details[$row[csf('knit_dye_company')]]; else  $issue_to = "&nbsp;";
							
							if($row[csf('booking_no')]=="") $row[csf('booking_no')]=$row[csf('requisition_no')];

                            $yarn_issued = $row[csf('issue_qnty')];
                            ?>
                            <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                                <td width="105"><p><? echo $row[csf('issue_number')]; ?></p></td>
                                <td width="90"><p><? echo $issue_to; ?></p></td>
                                <td width="105"><p><? echo $row[csf('booking_no')]; ?>&nbsp;</p></td>
                                <td width="70"><p><? echo $row[csf('challan_no')]; ?>&nbsp;</p></td>
                                <td width="75" align="center"><? echo change_date_format($row[csf('issue_date')]); ?></td>
                                <td width="70"><p><? echo $brand_array[$row[csf('brand_id')]]; ?>&nbsp;</p></td>
                                <td width="60"><p><? echo $row[csf('lot')]; ?></p></td>
                                <td width="180"><p><? echo $row[csf('product_name_details')]; ?></p></td>
                                <td align="right" width="90">
                                    <?
                                    if ($row[csf('knit_dye_source')] != 3) {
                                        echo number_format($yarn_issued, 2, '.', '');
                                        $total_yarn_issue_qnty += $yarn_issued;
                                    } else
                                        echo "&nbsp;";
                                    ?>
                                </td>
                                <td align="right">
                                    <?
                                    if ($row[csf('knit_dye_source')] == 3) {
                                        echo number_format($yarn_issued, 2, '.', '');
                                        $total_yarn_issue_qnty_out += $yarn_issued;
                                    } else
                                        echo "&nbsp;";
                                    ?>
                                </td>
                            </tr>
                            <?
                            $i++;
                        }
                    }
                }
                ?>
                <tr style="font-weight:bold">
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td align="right">Total</td>
                    <td align="right"><? echo number_format($total_yarn_issue_qnty, 2, '.', ''); ?></td>
                    <td align="right"><? echo number_format($total_yarn_issue_qnty_out, 2, '.', ''); ?></td>
                </tr>
                <tr style="font-weight:bold">
                    <td align="right" colspan="9">Issue Total</td>
                    <td align="right"><? echo number_format($total_yarn_issue_qnty + $total_yarn_issue_qnty_out, 2, '.', ''); ?></td>
                </tr>
                <thead>
                <th colspan="10"><b>Yarn Return</b></th>
                </thead>
                <thead>
                <th width="105">Return Id</th>
                <th width="90">Return From</th>
                <th width="105">Booking No</th>
                <th width="70">Challan No</th>
                <th width="75">Return Date</th>
                <th width="70">Brand</th>
                <th width="60">Lot No</th>
                <th width="180">Yarn Description</th>
                <th width="90">Return Qnty (In)</th>
                <th>Return Qnty (Out)</th>
                </thead>
                <?
                $total_yarn_return_qnty = 0;
                $total_yarn_return_qnty_out = 0;
                foreach ($yarn_desc_array_for_return as $key => $value) {
                    if (!in_array($key, $yarn_desc_array)) {
                        $desc = explode("__", $value);
                        $yarn_count = $desc[0];
                        $yarn_comp_type1st = $desc[1];
                        $yarn_comp_percent1st = $desc[2];
                        $yarn_comp_type2nd = $desc[3];
                        $yarn_comp_percent2nd = $desc[4];
                        $yarn_type_id = $desc[5];

                        $sql = "select a.recv_number, a.receive_date, a.challan_no, a.knitting_source, a.knitting_company, a.booking_no, sum(b.quantity) as returned_qnty, c.lot, c.yarn_type, c.id as prod_id, c.product_name_details, c.brand as brand_id from inv_receive_master a, order_wise_pro_details b, product_details_master c, inv_transaction d where a.id=d.mst_id and d.transaction_type=4 and d.item_category=1 and d.id=b.trans_id and b.trans_type=4 and b.entry_form=9 and b.po_breakdown_id in ($order_id) and b.prod_id=c.id and c.yarn_count_id='$yarn_count' and c.yarn_comp_type1st='$yarn_comp_type1st' and c.yarn_comp_percent1st='$yarn_comp_percent1st' and c.yarn_comp_type2nd='$yarn_comp_type2nd' and c.yarn_comp_percent2nd='$yarn_comp_percent2nd' and c.yarn_type='$yarn_type_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.issue_purpose in (1,4) group by a.id, c.id, a.recv_number, a.receive_date, a.challan_no, a.knitting_source, a.knitting_company, a.booking_no, c.lot, c.yarn_type, c.product_name_details, c.brand";
                        $result = sql_select($sql);
                        foreach ($result as $row)
						{
                            if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";

                            if ($row[csf('knitting_source')] == 1) $return_from = $company_library[$row[csf('knitting_company')]]; else if ($row[csf('knitting_source')] == 3) $return_from = $supplier_details[$row[csf('knitting_company')]]; else $return_from = "&nbsp;";

                            $yarn_returned = $row[csf('returned_qnty')];
                            ?>
                            <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                                <td width="105"><p><? echo $row[csf('recv_number')]; ?></p></td>
                                <td width="90"><p><? echo $return_from; ?></p></td>
                                <td width="105"><p><? echo $row[csf('booking_no')]; ?>&nbsp;</p></td>
                                <td width="70"><p><? echo $row[csf('challan_no')]; ?>&nbsp;</p></td>
                                <td width="75" align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
                                <td width="70"><p><? echo $brand_array[$row[csf('brand_id')]]; ?>&nbsp;</p></td>
                                <td width="60"><p><? echo $row[csf('lot')]; ?></p></td>
                                <td width="180"><p><? echo $row[csf('product_name_details')]; ?></p></td>
                                <td align="right" width="90">
                                    <?
                                    if ($row[csf('knitting_source')] != 3) {
                                        echo number_format($yarn_returned, 2, '.', '');
                                        $total_yarn_return_qnty += $yarn_returned;
                                    } else
                                        echo "&nbsp;";
                                    ?>
                                </td>
                                <td align="right">
                                    <?
                                    if ($row[csf('knitting_source')] == 3) {
                                        echo number_format($yarn_returned, 2, '.', '');
                                        $total_yarn_return_qnty_out += $yarn_returned;
                                    } else
                                        echo "&nbsp;";
                                    ?>
                                </td>
                            </tr>
                            <?
                            $i++;
                        }
                    }
                }
                ?>
                <tr style="font-weight:bold">
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td align="right">Balance</td>
                    <td align="right"><? echo number_format($total_yarn_issue_qnty - $total_yarn_return_qnty, 2, '.', ''); ?></td>
                    <td align="right"><? echo number_format($total_yarn_issue_qnty_out - $total_yarn_return_qnty_out, 2, '.', ''); ?></td>
                </tr>
                <tfoot>    
                    <tr>
                        <th align="right" colspan="9">Total Balance</th>
                        <th align="right"><? echo number_format(($total_yarn_issue_qnty + $total_yarn_issue_qnty_out) - ($total_yarn_return_qnty + $total_yarn_return_qnty_out), 2); ?></th>
                    </tr>
                </tfoot>
            </table>	
        </div>
    </fieldset>  
    <?
    exit();
}

if($action=="yarn_issue")
{
	echo load_html_head_contents("Report Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	$brand_array=return_library_array( "select id, brand_name from lib_brand", "id", "brand_name"  );
	$yarn_count_details=return_library_array( "select id, yarn_count from lib_yarn_count", "id", "yarn_count");
	
	$sqlWO="select a.fabric_source,b.po_break_down_id,a.booking_no from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.item_category in(2,3,13) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.po_break_down_id in ($order_id)";
	 $resultWo=sql_select($sqlWO);
	 $fab_source_ids="";
		foreach($resultWo as $woRow)
		{
			 $fab_source_ids.=$woRow[csf('fabric_source')].',';
		}
		$fab_source=rtrim($fab_source_ids,',');
		$fab_source_id=array_unique(explode(",",$fab_source));
	?>
	<script>

		function print_window()
		{
			//document.getElementById('scroll_body').style.overflow="auto";
			//document.getElementById('scroll_body').style.maxHeight="none";
			
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
		
			d.close();
			//document.getElementById('scroll_body').style.overflowY="scroll";
			//document.getElementById('scroll_body').style.maxHeight="230px";
		}	
		
	</script>	
	<div style="width:870px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
	<fieldset style="width:865px; margin-left:3px">
		<div id="report_container">
        	
			<table border="1" class="rpt_table" rules="all" width="860" cellpadding="0" cellspacing="0">
            	<thead>
					<th colspan="10"><b>Yarn Issue</b></th>
				</thead>
				<thead>
                    <th width="105">Issue Id</th>
                    <th width="90">Issue To</th>
                    <th width="105">Booking No</th>
                    <th width="80">Challan No</th>
                    <th width="75">Issue Date</th>
                    <th width="70">Brand</th>
                    <th width="60">Lot No</th>
                    <th width="80">Description</th>
                    <th width="90">Issue Qnty (In)</th>
                    <th>Issue Qnty (Out)</th>
				</thead>
                <?
                $i=1; $total_yarn_issue_qnty=0; $total_yarn_issue_qnty_out=0;
				if($yarn_comp_type2nd!="") $yarn_comp_type2nd_cond="and c.yarn_comp_type2nd='$yarn_comp_type2nd'"; else $yarn_comp_type2nd_cond="";
				if($yarn_comp_percent2nd!="") $yarn_comp_percent2nd_cond="and c.yarn_comp_percent2nd='$yarn_comp_percent2nd'"; else $yarn_comp_percent2nd_cond="";
				$sql="select a.id as issue_id, a.issue_number, a.issue_date, a.challan_no, a.knit_dye_source, a.knit_dye_company, a.booking_no, sum(b.quantity) as issue_qnty, c.lot, c.yarn_type, c.id as prod_id, c.product_name_details, d.brand_id, a.issue_basis, d.requisition_no,c.yarn_comp_type1st,c.yarn_count_id,c.yarn_comp_percent1st from inv_issue_master a, order_wise_pro_details b, product_details_master c, inv_transaction d where a.id=d.mst_id and d.transaction_type=2 and d.item_category=1 and c.item_category_id=1 and d.id=b.trans_id and b.trans_type=2 and b.entry_form=3 and b.po_breakdown_id in ($order_id) and b.prod_id=c.id and c.yarn_count_id='$yarn_count' and c.yarn_comp_type1st='$yarn_comp_type1st' and c.yarn_comp_percent1st='$yarn_comp_percent1st' $yarn_comp_type2nd_cond $yarn_comp_percent2nd_cond and c.yarn_type='$yarn_type_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.issue_purpose  in (1,4) group by a.id, c.id, a.issue_number, a.issue_date, a.challan_no, a.knit_dye_source, a.knit_dye_company, a.booking_no, c.lot, c.yarn_type, c.product_name_details, d.brand_id, a.issue_basis, d.requisition_no,c.yarn_comp_type1st,c.yarn_count_id,c.yarn_comp_percent1st order by a.issue_date DESC";
                $result=sql_select($sql);
                foreach($result as $row)
				{
					if($row[csf('issue_basis')] == 3){
						$requisition_no_arr[$row[csf("requisition_no")]] = $row[csf("requisition_no")];
					}
					$issue_id_arr[$row[csf("issue_id")]] = $row[csf("issue_id")];
				}
				$requisition_no_arr = array_filter($requisition_no_arr);

				if(!empty($requisition_no_arr))
				{
					$requ_booking_no_arr = return_library_array("select a.requisition_no, c.booking_no from ppl_yarn_requisition_entry a, ppl_planning_info_entry_dtls b, ppl_planning_info_entry_mst c where a.knit_id = b.id and b.mst_id = c.id and a.status_active=1 and a.requisition_no in (".implode(",", $requisition_no_arr).") group by a.requisition_no, c.booking_no","requisition_no","booking_no");					
				}
                

				foreach($result as $row)
				{
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
						
					$issue_to="";
					if($row[csf('knit_dye_source')]==1) $issue_to=$company_library[$row[csf('knit_dye_company')]]; 
					else $issue_to=$supplier_details[$row[csf('knit_dye_company')]];
						
                   foreach($fab_source_id as $fsid)
					{
						if($fsid==1)
						{
						 	$yarn_issued=$row[csf('issue_qnty')];	
						}
						
					}
                    ?>
                    <tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                    	<td width="105"><p><? echo $row[csf('issue_number')]; ?></p></td>
                        <td width="90"><p><? echo $issue_to; ?></p></td>
                        <td width="105">
                        	<p>
                        	<? 
                        		if($row[csf('issue_basis')] == 3){
									echo $requ_booking_no_arr[$row[csf("requisition_no")]];
								}
								else if($row[csf('issue_basis')] == 1)
								{
									echo $row[csf('booking_no')];
								}
                        		
                        	?>
                        	&nbsp;
                        	</p>
                        </td>
                        <td width="80"><p><? echo $row[csf('challan_no')]; ?>&nbsp;</p></td>
                        <td width="75" align="center"><? echo change_date_format($row[csf('issue_date')]); ?></td>
                        <td width="70"><p><? echo $brand_array[$row[csf('brand_id')]]; ?>&nbsp;</p></td>
                        <td width="60"><p><? echo $row[csf('lot')]; ?></p></td>
                        <td width="80"><p><? echo $yarn_count_details[$row[csf('yarn_count_id')]].' '.$composition[$row[csf('yarn_comp_type1st')]].' '.$row[csf('yarn_comp_percent1st')].'% '.$yarn_type[$row[csf('yarn_type')]]; ?></p></td>
                        <td align="right" width="90">
							<? 
								if($row[csf('knit_dye_source')]!=3)
								{
									echo number_format($yarn_issued,2);
									$total_yarn_issue_qnty+=$yarn_issued;
								}
								else echo "&nbsp;";
                            ?>
                        </td>
                        <td align="right">
							<? 
								if($row[csf('knit_dye_source')]==3)
								{ 
									echo number_format($yarn_issued,2); 
									$total_yarn_issue_qnty_out+=$yarn_issued;
								}
								else echo "&nbsp;";
                            ?>
                        </td>
                    </tr>
                <?
                $total_issue = $total_yarn_issue_qnty+$total_yarn_issue_qnty_out;
                $i++;
                }
				unset($result);
                ?>
                <tr style="font-weight:bold">
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td align="right">Total</td>
                    <td align="right"><? echo number_format($total_yarn_issue_qnty,2);?></td>
                    <td align="right"><? echo number_format($total_yarn_issue_qnty_out,2);?></td>
                </tr>
                <tr style="font-weight:bold">
                    <td align="right" colspan="9">Issue Total</td>
                    <td align="right"><? echo number_format($total_issue,2);?></td>
                </tr>
                
               

            </table>	
		</div>
	</fieldset>  
	<?
	exit();
}

if($action=="yarn_return")
{
	echo load_html_head_contents("Report Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	$brand_array=return_library_array( "select id, brand_name from lib_brand", "id", "brand_name"  );
	$yarn_count_details=return_library_array( "select id, yarn_count from lib_yarn_count", "id", "yarn_count");
	$sqlWO="select a.fabric_source,b.po_break_down_id,a.booking_no from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.item_category in(2,3,13) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.po_break_down_id in ($order_id)";
	 $resultWo=sql_select($sqlWO);
	 $fab_source_ids="";
	 $booking_array=array();
		foreach($resultWo as $woRow)
		{
			 $fab_source_ids.=$woRow[csf('fabric_source')].',';
			 $booking_array[$woRow[csf('po_break_down_id')]]['booking_no']=$woRow[csf('booking_no')];
		}
		$fab_source=rtrim($fab_source_ids,',');
		$fab_source_id=array_unique(explode(",",$fab_source));
	?>
	<script>

		function print_window()
		{
			//document.getElementById('scroll_body').style.overflow="auto";
			//document.getElementById('scroll_body').style.maxHeight="none";
			
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
		
			d.close();
			//document.getElementById('scroll_body').style.overflowY="scroll";
			//document.getElementById('scroll_body').style.maxHeight="230px";
		}	
		
	</script>	
	<div style="width:970px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
	<fieldset style="width:865px; margin-left:3px">
		<div id="report_container">
        	
			<table border="1" class="rpt_table" rules="all" width="960" cellpadding="0" cellspacing="0">
            	
				
               
                <thead>
                    <tr><th colspan="14"><b>Yarn Return</b></th></tr>
                </thead>
                <thead>
                	
	                </tr>
	                	<th rowspan="2" width="105">Return Id</th>
	                    <th rowspan="2" width="90">Return From</th>
	                    <th rowspan="2" width="105">Booking No</th>
	                    <th rowspan="2" width="80">Challan No</th>
	                    <th rowspan="2" width="70">Brand</th>
	                    <th rowspan="2" width="60">Lot No</th>
	                    <th rowspan="2" width="75">Return Date</th>
	                    <th rowspan="2" width="80">Yarn Type</th>
	                    <th colspan="3" width="180">Return Qnty (In)</th>
	                    <th colspan="3">Return Qnty (Out)</th>
	                </tr>
	                <tr>
	                	<th width="50">Usable</th>
	                	<th width="50">Reject</th>
	                	<th>Total</th>
	                	<th width="50">Usable</th>
	                	<th width="50">Reject</th>
	                	<th>Total</th>
	                	
	                </tr>
               </thead>
               <tbody>
                <?
                $total_yarn_return_qnty=0; $total_yarn_return_qnty_out=0;
				if($yarn_comp_type2nd!="") $yarn_comp_type2nd_cond="and c.yarn_comp_type2nd='$yarn_comp_type2nd'"; else $yarn_comp_type2nd_cond="";
				if($yarn_comp_percent2nd!="") $yarn_comp_percent2nd_cond="and c.yarn_comp_percent2nd='$yarn_comp_percent2nd'"; else $yarn_comp_percent2nd_cond="";

				if(!empty($issue_id_arr))
				{
					$issue_id_cond = " and a.issue_id in (".implode(',', $issue_id_arr).") ";
				}
                //$sql="select a.recv_number, a.receive_date, a.challan_no, a.knitting_source, a.knitting_company, a.booking_no, sum(b.quantity) as returned_qnty, c.lot, c.yarn_type, c.id as prod_id, c.product_name_details, d.brand_id, a.receive_basis,c.yarn_count_id,c.yarn_comp_type1st,c.yarn_comp_percent1st,d.cons_reject_qnty,d.cons_quantity from inv_receive_master a, order_wise_pro_details b, product_details_master c, inv_transaction d where a.id=d.mst_id and d.transaction_type=4 and c.item_category_id=1 and d.item_category=1 and d.id=b.trans_id and b.trans_type=4 and b.entry_form=9 and b.po_breakdown_id in ($order_id) and b.prod_id=c.id and c.yarn_count_id='$yarn_count' and c.yarn_comp_type1st='$yarn_comp_type1st' and c.yarn_comp_percent1st='$yarn_comp_percent1st' $yarn_comp_type2nd_cond $yarn_comp_percent2nd_cond and c.yarn_type='$yarn_type_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.issue_purpose  in (1,4) and b.trans_id=$trans_id $issue_id_cond group by a.id, c.id, a.recv_number, a.receive_date, a.challan_no, a.knitting_source, a.knitting_company, a.booking_no, c.lot, c.yarn_type, c.product_name_details, d.brand_id, a.receive_basis,c.yarn_count_id,c.yarn_comp_type1st,c.yarn_comp_percent1st,d.cons_reject_qnty,d.cons_quantity order by a.receive_date DESC";

                $sql="select trans_id, trans_type,entry_form, po_breakdown_id,prod_id,sum(quantity) as quantity ,sum(reject_qty) as reject_qty  from order_wise_pro_details where status_active=1 and entry_form=9 and po_breakdown_id=$order_id and trans_id=$trans_id  group by trans_id, trans_type,entry_form, po_breakdown_id,prod_id";
                //echo $sql;
                $result=sql_select($sql);
                $prod_id='';
                $trans_id='';
                foreach ($result as $row) {
                	$prod_id=$row[csf('prod_id')];
                	$trans_id=$row[csf('trans_id')];
                }

                $sql_tran="select a.recv_number, a.receive_date, a.challan_no, a.knitting_source, a.knitting_company, a.booking_no,d.brand_id, a.receive_basis,d.id as trans_id from inv_receive_master a ,inv_transaction d where a.id=d.mst_id and d.transaction_type=4 and a.status_active=1 and d.status_active=1 and d.id=$trans_id";
                //echo "<pre>".$sql_tran."</pre>";
                $result_tran=sql_select( $sql_tran);
                $trans_data=array();
                foreach ($result_tran as $row) {
                	$trans_data[$row[csf('trans_id')]]['recv_number']=$row[csf('recv_number')];
                	$trans_data[$row[csf('trans_id')]]['receive_date']=$row[csf('receive_date')];
                	$trans_data[$row[csf('trans_id')]]['challan_no']=$row[csf('challan_no')];
                	$trans_data[$row[csf('trans_id')]]['knitting_source']=$row[csf('knitting_source')];
                	$trans_data[$row[csf('trans_id')]]['knitting_company']=$row[csf('knitting_company')];
                	$trans_data[$row[csf('trans_id')]]['booking_no']=$row[csf('booking_no')];
                	$trans_data[$row[csf('trans_id')]]['brand_id']=$row[csf('brand_id')];
                	$trans_data[$row[csf('trans_id')]]['receive_basis']=$row[csf('receive_basis')];
                	$trans_data[$row[csf('trans_id')]]['trans_id']=$row[csf('trans_id')];
                }

                $sql_prod="select c.lot, c.yarn_type, c.id as prod_id, c.product_name_details,c.yarn_count_id,c.yarn_comp_type1st,c.yarn_comp_percent1st from product_details_master c where c.status_active=1 and c.is_deleted=0 and c.id=$prod_id";
                //echo $sql_prod;
                //echo "<pre>".$sql_prod."</pre>";
                $result_prod=sql_select($sql_prod);

                $prod_data=array();
                foreach ($result_prod as $row) {
                	$prod_data[$row[csf('prod_id')]]['prod_id']=$row[csf('prod_id')];
                	$prod_data[$row[csf('prod_id')]]['lot']=$row[csf('lot')];
                	$prod_data[$row[csf('prod_id')]]['yarn_type']=$row[csf('yarn_type')];
                	$prod_data[$row[csf('prod_id')]]['product_name_details']=$row[csf('product_name_details')];
                	$prod_data[$row[csf('prod_id')]]['yarn_count_id']=$row[csf('yarn_count_id')];
                	$prod_data[$row[csf('prod_id')]]['yarn_comp_type1st']=$row[csf('yarn_comp_type1st')];
                	$prod_data[$row[csf('prod_id')]]['yarn_comp_percent1st']=$row[csf('yarn_comp_percent1st')];
                }

                
				foreach($result as $row)
				{
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	

					$prod_id=$row[csf('prod_id')];
					$trans_id=$row[csf('trans_id')];
					$recv_number=$trans_data[$trans_id]['recv_number'];
					$receive_date=$trans_data[$trans_id]['receive_date'];
					$challan_no=$trans_data[$trans_id]['challan_no'];
					$knitting_source=$trans_data[$trans_id]['knitting_source'];
					$knitting_company=$trans_data[$trans_id]['knitting_company'];
					$booking_no=$trans_data[$trans_id]['booking_no'];
					$brand_id=$trans_data[$trans_id]['brand_id'];
					$receive_basis=$trans_data[$trans_id]['receive_basis'];

					$prod_id=$prod_data[$row[csf('prod_id')]]['prod_id'];
					$lot=$prod_data[$row[csf('prod_id')]]['lot'];
					$yarn_type=$prod_data[$row[csf('prod_id')]]['yarn_type'];
					$product_name_details=$prod_data[$row[csf('prod_id')]]['product_name_details'];
					$yarn_count_id=$prod_data[$row[csf('prod_id')]]['yarn_count_id'];
					$yarn_comp_type1st=$prod_data[$row[csf('prod_id')]]['yarn_comp_type1st'];
					$yarn_comp_percent1st=$prod_data[$row[csf('prod_id')]]['yarn_comp_percent1st'];

					
					$return_from="";
					if($knitting_source==1) $return_from=$company_library[$knitting_company]; 
					else $return_from=$supplier_details[$knitting_company];
						
                    $yarn_returned=$row[csf('returned_qnty')];
                    $reject_qty=$row[csf('reject_qty')];
                    $quantity=$row[csf('quantity')];
                    ?>
                    <tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                    	<td ><p><? echo $recv_number; ?></p></td>
                        <td ><p><? echo $return_from; ?></p></td>
                        <td >
                        	<p>
                        	<? 
                        		if($receive_basis == 3)
                        		{
									echo $booking_array[$order_id]['booking_no'];
								}
								else if($receive_basis == 1)
								{
									echo $booking_no;
								}
                        	?>
                        	&nbsp;
                        	</p>
                        </td>
                        <td ><p><? echo $challan_no; ?>&nbsp;</p></td>
                        <td ><p><? echo $brand_array[$brand_id]; ?>&nbsp;</p></td>
                        <td ><p><? echo $lot; ?></p></td>
                        <td  align="center"><? echo change_date_format($receive_date); ?></td>
                        <td ><p><? echo $yarn_count_details[$yarn_count_id].' '.$composition[$yarn_comp_type1st].' '.$yarn_comp_percent1st.'% '.$yarn_type[$yarn_type]; ?></p></td>
                        <td align="right" >
							<? 
								if($knitting_source!=3)
								{
									echo number_format($quantity,2);
									
								}
								else echo "&nbsp;";
                            ?>

                        </td>
                        <td align="right" >
                        	<? 
								if($knitting_source!=3)
								{
									echo number_format($reject_qty,2);
									$total_yarn_return_qnty+=$quantity+$reject_qty;
								}
								else echo "&nbsp;";
                            ?>
                        </td>
                        <td align="right" >
                        	<? 
								if($knitting_source!=3)
								{
									echo number_format($quantity+$reject_qty,2);
									
								}
								else echo "&nbsp;";
                            ?>
                        </td>

                            <td align="right" >
							<? 
								if($knitting_source==3)
								{
									echo number_format($quantity,2);
									
								}
								else echo "&nbsp;";
                            ?>

                        </td>
                        <td align="right" >
                        	<? 
								if($knitting_source==3)
								{
									echo number_format($reject_qty,2);
									
								}
								else echo "&nbsp;";
                            ?>
                        </td>
                        
                        <td align="right">
							<? 
								if($knitting_source==3)
								{ 
									echo number_format($quantity+$reject_qty,2); 
									$total_yarn_return_qnty_out+=$quantity+$reject_qty;
								}
								else echo "&nbsp;";
                            ?>
                        </td>
                    </tr>
                <?
                $return_qnty = $total_yarn_return_qnty+$total_yarn_return_qnty_out;
                $i++;
                }
				unset($result);
               
                ?>
                <tr style="font-weight:bold">
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td align="right">Total</td>
                    <td align="right" colspan="3"><? echo number_format($total_yarn_return_qnty,2);?></td>
                    <td align="right" colspan="3"><? echo number_format($total_yarn_return_qnty_out,2);?></td>
                </tr>
            </tbody>
            <tfoot>
                <tr style="font-weight:bold">
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td align="right" colspan="3">Return Total</td>
                    <td align="right" colspan="3"><? number_format($return_qnty,2);?></td>
                </tr>
            </tfoot>
               
            </table>	
		</div>
	</fieldset>  
	<?
	exit();
}