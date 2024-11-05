<?php
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../../includes/common.php');
$user_id=$_SESSION['logic_erp']['user_id'];
if( $_SESSION['logic_erp']['user_id'] == "" )
{
	header("location:login.php");
	die;
}

$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
extract($_REQUEST);
$storeNameArr=return_library_array( "SELECT id,store_name from lib_store_location ", "id", "store_name" );
//manual precision settings here
ini_set('precision',8);
/*
|--------------------------------------------------------------------------
| load_drop_down_buyer
|--------------------------------------------------------------------------
|
*/
if($action=="load_drop_down_buyer")
{
	if($type==1)
		$party="1,3,21,90";
	else
		$party="80";
	echo create_drop_down( "cbo_buyer_id", 140, "SELECT buy.id, buy.buyer_name FROM lib_buyer buy, lib_buyer_tag_company b WHERE buy.status_active =1 AND buy.is_deleted=0 AND b.buyer_id=buy.id AND b.tag_company IN (".$choosenCompany.") ".$buyer_cond." AND buy.id IN (SELECT buyer_id FROM lib_buyer_party_type WHERE party_type IN (".$party.")) GROUP BY buy.id, buy.buyer_name ORDER BY buy.buyer_name","id,buyer_name", 1, "--Select Buyer--", $selected, "","" )."**";
	exit();
}

/*
|--------------------------------------------------------------------------
| job_no_popup
|--------------------------------------------------------------------------
|
*/
if($action=="job_no_popup")
{
	echo load_html_head_contents("Job Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>

	<script>
		var selected_id = new Array();
		var selected_name = new Array();
		var selected_attach_id = new Array();
		function toggle( x, origColor )
		{
			var newColor = 'yellow';
			if ( x.style )
			{
				x.style.backgroundColor = ( newColor == x.style.backgroundColor ) ? origColor : newColor;
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
                                    <?php
                                    $type = 1;
                                    if($type == 1)
                                        $party="1,3,21,90";
                                    else
                                        $party="80";
										
									//is_disabled
									$is_disabled = ($buyer_name != 0 ? '1' : '0');

                                    echo create_drop_down( "cbo_buyer_name", 140, "SELECT buy.id, buy.buyer_name FROM lib_buyer buy, lib_buyer_tag_company b WHERE buy.status_active =1 AND buy.is_deleted=0 AND b.buyer_id=buy.id AND b.tag_company IN (".$companyID.") AND buy.id IN (SELECT buyer_id FROM lib_buyer_party_type WHERE party_type IN (".$party.")) GROUP BY buy.id, buy.buyer_name ORDER BY buy.buyer_name","id,buyer_name", "1", "-- All Buyer--",$buyer_name, "", $is_disabled);
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
                                    <input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<?php echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_year_id; ?>'+'**'+'<? echo $cbo_month_id; ?>'+'**'+'<? echo $party; ?>', 'create_job_no_search_list_view', 'search_div', 'rack_wise_grey_fabrics_stock_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
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

/*
|--------------------------------------------------------------------------
| create_job_no_search_list_view
|--------------------------------------------------------------------------
|
*/
if($action=="create_job_no_search_list_view")
{
	$data = explode('**',$data);
	$company_id = $data[0];
	$year_id = $data[4];
	$month_id = $data[5];
	$party = $data[6];
	//echo $month_id;

	/*
	|--------------------------------------------------------------------------
	| buyer checking
	|--------------------------------------------------------------------------
	|
	*/
	if($data[1]==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="")
				$buyer_id_cond=" AND buyer_name IN (".$_SESSION['logic_erp']["buyer_id"].")";
			else
				$buyer_id_cond="";
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		$buyer_id_cond=" AND buyer_name = ".$data[1]."";
	}

	$search_by = $data[2];
	$search_string = "%".trim($data[3])."%";

	if($search_by == 2)
		$search_field = "style_ref_no";
	else
		$search_field = "job_no";

	if($db_type == 0)
	{
		if($year_id != 0)
			$year_search_cond = " AND year(insert_date) = ".$year_id."";
		else
			$year_search_cond = "";
		$year_cond = "year(insert_date) AS year";
	}
	else if($db_type==2)
	{
		if($year_id != 0)
			$year_search_cond = " AND TO_CHAR(insert_date,'YYYY') = ".$year_id."";
		else
			$year_search_cond="";
		$year_cond = "TO_CHAR(insert_date,'YYYY') AS year";
	}
	
	$company_arr=return_library_array( "SELECT id, company_name FROM lib_company WHERE id IN(".$company_id.")", "id", "company_name" );
	$buyer_arr=return_library_array( "SELECT buy.id, buy.buyer_name FROM lib_buyer buy, lib_buyer_tag_company b WHERE buy.status_active =1 AND buy.is_deleted=0 AND b.buyer_id=buy.id AND b.tag_company IN (".$company_id.") AND buy.id IN (SELECT buyer_id FROM lib_buyer_party_type WHERE party_type IN (".$party.")) GROUP BY buy.id, buy.buyer_name ORDER BY buy.buyer_name","id", "buyer_name");
	$arr=array (0=>$company_arr,1=>$buyer_arr);
	$sql= "SELECT id, job_no, job_no_prefix_num, company_name, buyer_name, style_ref_no, ".$year_cond." FROM wo_po_details_master WHERE status_active=1 AND is_deleted=0 AND company_name IN(".$company_id.") AND ".$search_field." LIKE '".$search_string."' ".$buyer_id_cond." ".$year_search_cond." ".$month_cond." ORDER BY job_no DESC";
	echo create_list_view("tbl_list_search", "Company,Buyer Name,Job No,Year,Style Ref. No", "120,80,80,60","620","270",0, $sql , "js_set_value", "id,job_no_prefix_num", "", 1, "company_name,buyer_name,0,0,0", $arr , "company_name,buyer_name,job_no_prefix_num,year,style_ref_no", "",'','0,0,0,0,0','',1) ;
	exit();
}

/*
|--------------------------------------------------------------------------
| booking_no_popup
|--------------------------------------------------------------------------
|
*/
if($action=="booking_no_popup")
{
	echo load_html_head_contents("Booking Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>

	<script>
		var selected_id = new Array();
		var selected_name = new Array;

		function check_all_datas()
		{
			var tbl_row_count = document.getElementById( 'tbl_list_div' ).rows.length;
			tbl_row_count = tbl_row_count - 1;

			for( var i = 1; i <= tbl_row_count; i++ )
			{
				$('#tr_'+i).trigger('click');
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

		function js_set_value2( str )
		{
			if (str!="") str=str.split("_");
			toggle( document.getElementById( 'tr_' + str[0] ), '#FFFFCC' );
			if( jQuery.inArray( str[1], selected_id ) == -1 )
			{
				selected_id.push( str[1] );
				selected_name.push( str[2] );
			}
			else
			{
				for( var i = 0; i < selected_id.length; i++ )
				{
					if( selected_id[i] == str[1] ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );

			}
			
			var id = ''; 
			var name = '';
			for( var i = 0; i < selected_id.length; i++ )
			{
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
		
		function func_onchange_booking_search_by(data)
		{
			//alert('su..re');
			//1 = booking no
			//2 = job no
			//3 = Style Ref.
			var jobNo = '<?php echo $txt_job_no; ?>';
			if(data == 2 && jobNo != '')
			{
				$('#txt_search_common').val('<? echo $txt_job_no; ?>').attr('disabled', 'disabled');
			}
			else
			{
				$('#txt_search_common').removeAttr('disabled');
			}
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
						<th>
                            <input type="reset" name="button" class="formbutton" value="Reset"  style="width:100px;">
                            <input type="hidden" name="hide_job_no" id="hide_job_no" value="" />
                            <input type="hidden" name="hide_job_id" id="hide_job_id" value="" />
						</th>
						<!--<input type="hidden" name="hide_recv_id" id="hide_recv_id" value="" />-->
					</thead>
					<tbody>
						<tr>
							<td align="center">
								<?
								//is_disabled
								$is_disabled = ($buyer_name != 0 ? '1' : '0');

								echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer_name,"",$is_disabled );
								?>
							</td>

							<td align="center">
								<?
								$search_by_arr=array(1=>"Booking No",2=>"Job No",3=>"Style Ref");
								$dd="change_search_event(this.value, '0*0*0', '0*0*0', '../../');func_onchange_booking_search_by(this.value) ";
								echo create_drop_down( "cbo_search_by", 130, $search_by_arr,"",0, "--Select--", "1",$dd,0 );
								?>
							</td>
							<td align="center" id="search_by_td">
								<input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />
							</td>
							<td align="center">
								<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_year_id; ?>'+'**'+'<? echo $cbo_month_id; ?>'+'**'+'<? echo $txt_job_no; ?>', 'create_booking_no_search_list_view', 'search_div', 'rack_wise_grey_fabrics_stock_report_controller', 'setFilterGrid(\'tbl_list_div\',-1)');" style="width:100px;" />
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

/*
|--------------------------------------------------------------------------
| create_booking_no_search_list_view
|--------------------------------------------------------------------------
|
*/
if($action=="create_booking_no_search_list_view")
{
	$data=explode('**',$data);
	$company_id=$data[0];
	$year_id=$data[4];
	$month_id=$data[5];

	if($data[1]==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="")
			{
				$buyer_id_cond = " AND a.buyer_name IN (".$_SESSION['logic_erp']["buyer_id"].")";
				$buyer_id_cond2 = " AND a.buyer_id IN (".$_SESSION['logic_erp']["buyer_id"].")";
			}
			else
			{
				$buyer_id_cond = "";
				$buyer_id_cond2="";
			}
		}
		else
		{
			$buyer_id_cond = "";
			$buyer_id_cond2="";
		}
	}
	else
	{
		$buyer_id_cond = " AND a.buyer_name=$data[1]";
		$buyer_id_cond2 = " AND a.buyer_id=$data[1]";
	}

	$search_by = $data[2];
	$search_string = "%".trim($data[3])."%";

	if($search_by == 3)
	{
		$search_field = "a.style_ref_no";
	}
	else if($search_by == 2)
	{
		$search_field = "a.job_no_prefix_num";
	}
	else
		$search_field = "b.booking_no";

	if($db_type == 0)
		$year_field_by = " AND YEAR(a.insert_date)";
	else if($db_type == 2)
		$year_field_by = " AND TO_CHAR(a.insert_date,'YYYY')";
	else
		$year_field_by = "";
	
	
	if($db_type == 0)
		$month_field_by = " AND month(a.insert_date)";
	else if($db_type == 2)
		$month_field_by = " AND to_char(a.insert_date,'MM')";
	else
		$month_field_by = "";
	
	if($db_type == 0)
		$year_field = " YEAR(a.insert_date) AS year";
	else if($db_type == 2)
		$year_field = " TO_CHAR(a.insert_date,'YYYY') AS year";
	else
		$year_field = "";

	if($year_id != 0)
		$year_cond = " ".$year_field_by." = ".$year_id."";
	else
		$year_cond = "";
	
	if($month_id != 0)
		$month_cond = " ".$month_field_by." = ".$month_id."";
	else
		$month_cond = "";

	$sql= "
		SELECT a.job_no,a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no,b.booking_no,c.booking_no_prefix_num,c.id as booking_id 
		FROM wo_po_details_master a
		INNER JOIN wo_booking_dtls b ON a.job_no = b.job_no
		INNER JOIN wo_booking_mst c ON b.booking_no = c.booking_no
		WHERE a.status_active = 1 AND a.is_deleted = 0 AND b.booking_type IN(1,4) AND a.company_name IN(".$company_id.") AND ".$search_field." LIKE '".$search_string."' ".$buyer_id_cond." ".$year_cond." ".$month_cond."
		GROUP BY a.job_no, b.booking_no, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no, c.id, c.booking_no_prefix_num
		ORDER BY a.job_no DESC
	";
	//echo $sql;
	$sqlResult=sql_select($sql);
	if(empty($sqlResult))
	{
		echo get_empty_data_msg();
		die;
	}
	
	$buyerIdArr = array();
	foreach($sqlResult as $row)
	{
		$buyerIdArr[$row[csf('buyer_name')]] = $row[csf('buyer_name')];
	}
	
	$buyer_arr=return_library_array( "SELECT id, buyer_name FROM lib_buyer WHERE id IN (".implode(',', $buyerIdArr).")",'id','buyer_name');
	$company_arr=return_library_array( "SELECT id, company_name FROM lib_company WHERE id IN (".$company_id.")",'id','company_name');
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
							if ($i % 2 == 0)
								$bgcolor = "#E9F3FF";
							else
								$bgcolor = "#FFFFFF";

							$data = $i.'_'.$row[csf('booking_id')].'_'.$row[csf('booking_no_prefix_num')];
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
	<?php
	exit();
}

/*
|--------------------------------------------------------------------------
| item_description_search
|--------------------------------------------------------------------------
|
*/
if($action=="item_description_search")
{
	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
	<script>
		var selected_id = new Array();
		var selected_name = new Array();
		var selected_no = new Array();

		function check_all_data()
		{
			var tbl_row_count = document.getElementById( 'list_view' ).rows.length;
			tbl_row_count = tbl_row_count - 1;
			for( var i = 1; i <= tbl_row_count; i++ )
			{
				var onclickString = $('#tr_' + i).attr('onclick');
				var paramArr = onclickString.split("'");
				var functionParam = paramArr[1];
				js_set_value( functionParam );

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

		function js_set_value( strCon ) 
		{
			var splitSTR = strCon.split("_");
			var str = splitSTR[0];
			var selectID = splitSTR[1];
			var selectDESC = splitSTR[2];
			
			toggle( document.getElementById( 'tr_' + str ), '#FFFFCC' );
			
			if( jQuery.inArray( selectID, selected_id ) == -1 )
			{
				selected_id.push( selectID );
				selected_name.push( selectDESC );
				selected_no.push(str);					
			}
			else
			{
				for( var i = 0; i < selected_id.length; i++ )
				{
					if( selected_id[i] == selectID )
						break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 ); 
				selected_no.splice( i, 1 );
			}
			
			var id = '';
			var name = '';
			var job = '';
			var num='';
			for( var i = 0; i < selected_id.length; i++ )
			{
				id += selected_id[i] + ',';
				name += selected_name[i] + ',';
				num += selected_no[i] + ','; 
			}
			id 		= id.substr( 0, id.length - 1 );
			name 	= name.substr( 0, name.length - 1 );
			num 	= num.substr( 0, num.length - 1 ); 
			
			$('#txt_selected_id').val( id );
			$('#txt_selected').val( name ); 
			$('#txt_selected_no').val( num );
		}

		function fn_check_lot()
		{ 
			show_list_view ( document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+'<? echo $company; ?>'+'_'+document.getElementById('txt_prod_id').value, 'create_lot_search_list_view', 'search_div', 'rack_wise_grey_fabrics_stock_report_controller', 'setFilterGrid("list_view",-1)');
		}
	</script>
	<body>
		<div align="center" style="width:100%;" >
			<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
				<table width="600" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
					<thead>
						<tr>                	 
							<th>Search By</th>
							<th align="center" width="180" id="search_by_td_up">Enter Item Description</th>
							<th align="center" width="120">Product Id</th>
							<th width="120">
								<input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px" class="formbutton"  />
								<input type='hidden' id='txt_selected_id' />
								<input type='hidden' id='txt_selected' />
								<input type='hidden' id='txt_selected_no' />
							</th>
						</tr>
					</thead>
					<tbody>
						<tr align="center">
							<td align="center">
								<?php 
								$search_by = array(1=>'Item Description');
								$dd="";
								echo create_drop_down( "cbo_search_by", 150, $search_by, "", 0, "--Select--", "", "", 0);
								?>
							</td>
							<td  align="center" id="search_by_td">				
								<input type="text" style="width:130px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />
							</td>
							<td align="center">				
								<input type="text" style="width:90px" class="text_boxes"  name="txt_prod_id" id="txt_prod_id" />
							</td> 
							<td align="center">
								<input type="button" name="btn_show" class="formbutton" value="Show" onClick="fn_check_lot()" style="width:100px;" />				
							</td>
						</tr>
					</tbody>
					</tr>         
				</table>    
				<div align="center" valign="top" style="margin-top:5px" id="search_div"> </div> 
			</form>
		</div>
	</body>           
	<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script> 
	</html>
	<?
	exit();
}

/*
|--------------------------------------------------------------------------
| create_lot_search_list_view
|--------------------------------------------------------------------------
|
*/
if($action=="create_lot_search_list_view")
{
	$ex_data = explode("_",$data);
	$txt_search_by = $ex_data[0];
	$txt_search_common = trim($ex_data[1]);
	$company = $ex_data[2];
	$prod_id = $ex_data[3];
	
	$sql_cond = "";
	if(trim($txt_search_common) != "")
	{
		if(trim($txt_search_by) == 1) // for LOT NO
		{
			//$sql_cond = " AND product_name_details LIKE '%$txt_search_common%'";	 
			$sql_cond = " AND item_description LIKE '%$txt_search_common%'";	 
		}
		else if(trim($txt_search_by) == 2) // for Yarn Count
		{
			if($txt_search_common == 0)
			{
				$sql_cond = " ";	 	
			}
			else
			{
				$sql_cond = " AND item_group_id LIKE '%$txt_search_common%'";	 	
			}
		} 
	} 
	
	if($prod_id != "")
		$sql_cond .= " AND id = ".$prod_id."";
	
	$sql = "SELECT id, product_name_details, gsm, dia_width FROM product_details_master WHERE company_id IN(".$company.") AND item_category_id = 13 ".$sql_cond.""; 
	$arr=array();
	echo create_list_view("list_view", "Product Id, Item Description, GSM, Dia","70,230,100","550","260",0, $sql, "js_set_value", "id,product_name_details", "", 1, "0,0,0,0", $arr, "id,product_name_details,gsm,dia_width", "","","0","",1);
	exit();
}

/*
|--------------------------------------------------------------------------
| store_popup
|--------------------------------------------------------------------------
|
*/
if ($action=="store_popup")
{
	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$data=explode('_',$data);
	?>	
    <script>
		var selected_id = new Array();
		var selected_name = new Array();
		var selected_attach_id = new Array();

		function check_all_data() 
		{
			var tbl_row_count = document.getElementById( 'list_view' ).rows.length;
			tbl_row_count = tbl_row_count - 0;
			for( var i = 1; i <= tbl_row_count; i++ ) 
			{
				var onclickString = $('#tr_' + i).attr('onclick');
				var paramArr = onclickString.split("'");
				var functionParam = paramArr[1];
				js_set_value( functionParam );
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
			
		function js_set_value(id)
		{
			var str=id.split("_");
			toggle( document.getElementById( 'tr_' + str[0] ), '#FFFFFF' );
			var strdt=str[2];
			str=str[1];
		
			if( jQuery.inArray(  str , selected_id ) == -1 )
			{
				selected_id.push( str );
				selected_name.push( strdt );
			}
			else 
			{
				for( var i = 0; i < selected_id.length; i++ )
				{
					if( selected_id[i] == str  ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i,1 );
			}
			var id = '';
			var ddd='';
			for( var i = 0; i < selected_id.length; i++ )
			{
				id += selected_id[i] + ',';
				ddd += selected_name[i] + ',';
			}
			id = id.substr( 0, id.length - 1 );
			ddd = ddd.substr( 0, ddd.length - 1 );
			$('#store_id').val( id );
			$('#store_name').val( ddd );
		}
	</script>
    <input type="hidden" id="store_id" />
    <input type="hidden" id="store_name" />
 	<?		
	$store_sql = "SELECT a.id, a.store_name, a.company_id, a.store_location from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id and b.category_type=13 and a.company_id IN($data[0]) $store_location_credential_cond and a.status_active=1 and a.is_deleted=0 group by a.id, a.store_name, a.company_id, a.store_location order by a.store_name";
	// echo $store_sql; 
	$companyArr = return_library_array( "SELECT id, company_name FROM lib_company WHERE id IN (".$data[0].")", "id", "company_name" );
	$arr=array(0=>$companyArr,1=>$locationArr,2=>$storeArr);
	echo  create_list_view("list_view", "Company,Location,Store", "70,100,150","420","360",0, $store_sql, "js_set_value", "id,store_name", "", 1, "company_id", $arr , "company_id,store_location,store_name", "rack_wise_grey_fabrics_stock_report_controller",'setFilterGrid("list_view",-1);','0,0,0','',1) ;
	disconnect($con);
	exit();
}

/*
|--------------------------------------------------------------------------
| floor_popup
|--------------------------------------------------------------------------
|
*/
if ($action=="floor_popup")
{
	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$data=explode('_',$data);
	?>	
    <script>
		var selected_id = new Array();
		var selected_name = new Array();
		var selected_attach_id = new Array();

		function check_all_data() 
		{
			var tbl_row_count = document.getElementById( 'list_view' ).rows.length;
			tbl_row_count = tbl_row_count - 0;
			for( var i = 1; i <= tbl_row_count; i++ ) 
			{
				var onclickString = $('#tr_' + i).attr('onclick');
				var paramArr = onclickString.split("'");
				var functionParam = paramArr[1];
				js_set_value( functionParam );
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
			
		function js_set_value(id)
		{
			var str=id.split("_");
			toggle( document.getElementById( 'tr_' + str[0] ), '#FFFFFF' );
			var strdt=str[2];
			str=str[1];
		
			if( jQuery.inArray(  str , selected_id ) == -1 )
			{
				selected_id.push( str );
				selected_name.push( strdt );
			}
			else 
			{
				for( var i = 0; i < selected_id.length; i++ )
				{
					if( selected_id[i] == str  ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i,1 );
			}
			var id = '';
			var ddd='';
			for( var i = 0; i < selected_id.length; i++ )
			{
				id += selected_id[i] + ',';
				ddd += selected_name[i] + ',';
			}
			id = id.substr( 0, id.length - 1 );
			ddd = ddd.substr( 0, ddd.length - 1 );
			$('#floor_id').val( id );
			$('#floor_name').val( ddd );
		}
	</script>
    <input type="hidden" id="floor_id" />
    <input type="hidden" id="floor_name" />
 	<?

 	if ($data[1]=="")
		$store_cond = "";
	else
		$store_cond = " AND b.store_id IN(".$data[1].")";

	$floor_sql = "
		SELECT a.floor_room_rack_id, a.floor_room_rack_name, a.company_id, b.location_id, b.store_id
    	FROM lib_floor_room_rack_mst a
		INNER JOIN lib_floor_room_rack_dtls b ON a.floor_room_rack_id = b.floor_id
    	WHERE a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 AND a.company_id IN(".$data[0].") $store_cond
    	GROUP BY a.floor_room_rack_id, a.floor_room_rack_name, a.company_id, b.location_id, b.store_id
    	ORDER BY a.floor_room_rack_name";
    // echo $floor_sql;	
	
	$companyArr = return_library_array( "SELECT id, company_name FROM lib_company WHERE id IN (".$data[0].")", "id", "company_name" );
	$locationArr = return_library_array( "SELECT id, location_name FROM lib_location WHERE status_active = 1 AND is_deleted = 0 AND company_id IN (SELECT id FROM lib_company WHERE id IN (".$data[0].")) ORDER BY location_name","id","location_name" );
	$storeArr = return_library_array("SELECT id, store_name FROM lib_store_location WHERE status_active = 1 AND is_deleted=0 ORDER BY store_name","id","store_name");
	$arr=array(0=>$companyArr,1=>$locationArr,2=>$storeArr);
	echo  create_list_view("list_view", "Company,Location,Store,Floor", "70,100,150,150","520","360",0, $floor_sql, "js_set_value", "floor_room_rack_id,floor_room_rack_name", "", 1, "company_id,location_id,store_id,0", $arr , "company_id,location_id,store_id,floor_room_rack_name", "rack_wise_grey_fabrics_stock_report_controller",'setFilterGrid("list_view",-1);','0,0,0,0','',1) ;
	disconnect($con);
	exit();
}

/*
|--------------------------------------------------------------------------
| room_popup
|--------------------------------------------------------------------------
|
*/
if ($action=="room_popup")
{
	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$data=explode('_',$data);
	?>	
    <script>
		var selected_id = new Array();
		var selected_name = new Array();
		var selected_attach_id = new Array();
		function check_all_data() 
		{
			var tbl_row_count = document.getElementById( 'list_view' ).rows.length;
			tbl_row_count = tbl_row_count - 0;
			for( var i = 1; i <= tbl_row_count; i++ ) 
			{
				var onclickString = $('#tr_' + i).attr('onclick');
				var paramArr = onclickString.split("'");
				var functionParam = paramArr[1];
				js_set_value( functionParam );
				
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
			
		function js_set_value(id)
		{
			var str=id.split("_");
			toggle( document.getElementById( 'tr_' + str[0] ), '#FFFFFF' );
			var strdt=str[2];
			str=str[1];
		
			if( jQuery.inArray(  str , selected_id ) == -1 )
			{
				selected_id.push( str );
				selected_name.push( strdt );
			}
			else 
			{
				for( var i = 0; i < selected_id.length; i++ )
				{
					if( selected_id[i] == str  ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i,1 );
			}
			var id = '';
			var ddd='';
			for( var i = 0; i < selected_id.length; i++ )
			{
				id += selected_id[i] + ',';
				ddd += selected_name[i] + ',';
			}
			id = id.substr( 0, id.length - 1 );
			ddd = ddd.substr( 0, ddd.length - 1 );
			$('#room_id').val( id );
			$('#room_name').val( ddd );
		}
	</script>

    <input type="hidden" id="room_id" />
    <input type="hidden" id="room_name" />
 	<?
	if ($data[1]=="")
		$floor_cond = "";
	else
		$floor_cond = " AND b.floor_id IN(".$data[1].")";
	
	$room_sql = "
		SELECT a.floor_room_rack_id, a.floor_room_rack_name, a.company_id, b.location_id, b.store_id, b.floor_id
    	FROM lib_floor_room_rack_mst a
		INNER JOIN lib_floor_room_rack_dtls b ON a.floor_room_rack_id = b.room_id 
    	WHERE a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 AND a.company_id IN(".$data[0].") ".$floor_cond."
    	GROUP BY a.floor_room_rack_id, a.floor_room_rack_name, a.company_id, b.location_id, b.store_id, b.floor_id
    	ORDER BY a.floor_room_rack_name
	";
    //echo $room_sql;die;

	$companyArr = return_library_array( "SELECT id, company_name FROM lib_company WHERE id IN(".$data[0].")", "id", "company_name" );
	$locationArr = return_library_array( "SELECT id, location_name FROM lib_location WHERE status_active = 1 AND is_deleted = 0 AND company_id IN (SELECT id FROM lib_company WHERE id IN (".$data[0].")) ORDER BY location_name","id","location_name" );
	$storeArr = return_library_array("SELECT id, store_name FROM lib_store_location WHERE status_active = 1 AND is_deleted = 0 ORDER BY store_name","id","store_name");
	$floorArr = return_library_array("SELECT a.floor_room_rack_name, a.floor_room_rack_id FROM lib_floor_room_rack_mst a INNER JOIN lib_floor_room_rack_dtls b ON a.floor_room_rack_id = b.floor_id WHERE a.company_id in(".$data[0].") ".$floor_cond." AND a.is_deleted = 0 AND a.status_active = 1 GROUP BY a.floor_room_rack_name, a.floor_room_rack_id","floor_room_rack_id","floor_room_rack_name");
	$arr=array(0=>$companyArr,1=>$locationArr,2=>$storeArr,3=>$floorArr);
	echo  create_list_view("list_view", "Company,Location,Store,Floor,Room", "70,100,150,80,80","520","360",0, $room_sql, "js_set_value", "floor_room_rack_id,floor_room_rack_name", "", 1, "company_id,location_id,store_id,floor_id,0", $arr , "company_id,location_id,store_id,floor_id,floor_room_rack_name", "rack_wise_grey_fabrics_stock_report_controller",'setFilterGrid("list_view",-1);','0,0,0,0,0','',1) ;
	disconnect($con);
	exit();
}

/*
|--------------------------------------------------------------------------
| rack_popup
|--------------------------------------------------------------------------
|
*/
if ($action=="rack_popup")
{
	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$data=explode('_',$data);
	?>	
    <script>
		var selected_id = new Array();
		var selected_name = new Array();
		var selected_attach_id = new Array();

		function check_all_data() 
		{
			var tbl_row_count = document.getElementById( 'list_view' ).rows.length;
			tbl_row_count = tbl_row_count - 0;
			for( var i = 1; i <= tbl_row_count; i++ ) 
			{
				var onclickString = $('#tr_' + i).attr('onclick');
				var paramArr = onclickString.split("'");
				var functionParam = paramArr[1];
				js_set_value( functionParam );
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
			
		function js_set_value(id)
		{
			var str=id.split("_");
			toggle( document.getElementById( 'tr_' + str[0] ), '#FFFFFF' );
			var strdt=str[2];
			str=str[1];
		
			if( jQuery.inArray(  str , selected_id ) == -1 )
			{
				selected_id.push( str );
				selected_name.push( strdt );
			}
			else 
			{
				for( var i = 0; i < selected_id.length; i++ )
				{
					if( selected_id[i] == str  ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i,1 );
			}
			var id = '';
			var ddd='';
			for( var i = 0; i < selected_id.length; i++ )
			{
				id += selected_id[i] + ',';
				ddd += selected_name[i] + ',';
			}
			id = id.substr( 0, id.length - 1 );
			ddd = ddd.substr( 0, ddd.length - 1 );
			$('#rack_id').val( id );
			$('#rack_name').val( ddd );
		}
	</script>
    <input type="hidden" id="rack_id" />
    <input type="hidden" id="rack_name" />
 	<?
	if ($data[1]=="")
		$floor_cond="";
	else
		$floor_cond=" AND b.floor_id IN(".$data[1].")";
		
	if ($data[2]=="")
		$room_cond="";
	else
		$room_cond=" AND b.room_id IN(".$data[2].")";
	
	$rack_sql = "SELECT a.floor_room_rack_id, a.floor_room_rack_name, a.company_id, b.location_id, b.store_id, b.floor_id, b.room_id
    FROM lib_floor_room_rack_mst a INNER JOIN lib_floor_room_rack_dtls b ON a.floor_room_rack_id = b.rack_id 
    WHERE a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 AND a.company_id IN(".$data[0].") ".$floor_cond." ".$room_cond."
    GROUP BY a.floor_room_rack_id, a.floor_room_rack_name, a.company_id, b.location_id, b.store_id, b.floor_id, b.room_id
    ORDER BY a.floor_room_rack_name";

	$companyArr = return_library_array( "SELECT id, company_name FROM lib_company WHERE id IN(".$data[0].")", "id", "company_name" );
	$locationArr = return_library_array( "SELECT id, location_name FROM lib_location WHERE status_active = 1 AND is_deleted = 0 AND company_id IN (SELECT id FROM lib_company WHERE id IN (".$data[0].")) ORDER BY location_name","id","location_name" );
	$storeArr = return_library_array("SELECT id, store_name FROM lib_store_location WHERE status_active = 1 AND is_deleted = 0 ORDER BY store_name","id","store_name");
	$floorArr = return_library_array("SELECT a.floor_room_rack_name, a.floor_room_rack_id FROM lib_floor_room_rack_mst a INNER JOIN lib_floor_room_rack_dtls b ON a.floor_room_rack_id = b.floor_id WHERE a.company_id in(".$data[0].") ".$floor_cond." AND a.is_deleted = 0 AND a.status_active = 1 GROUP BY a.floor_room_rack_name, a.floor_room_rack_id","floor_room_rack_id","floor_room_rack_name");
	$roomArr = return_library_array("SELECT a.floor_room_rack_name, a.floor_room_rack_id FROM lib_floor_room_rack_mst a INNER JOIN lib_floor_room_rack_dtls b ON a.floor_room_rack_id = b.room_id WHERE  a.company_id IN(".$data[0].") ".$floor_cond." ".$room_cond." AND a.is_deleted = 0 AND a.status_active = 1 GROUP BY a.floor_room_rack_name, a.floor_room_rack_id","floor_room_rack_id","floor_room_rack_name");
	$arr=array(0=>$companyArr,1=>$locationArr,2=>$storeArr,3=>$floorArr,4=>$roomArr);
	echo  create_list_view("list_view", "Company,Location,Store,Floor,Room,Rack", "70,100,150,80,80,100","590","360",0, $rack_sql, "js_set_value", "floor_room_rack_id,floor_room_rack_name", "", 1, "company_id,location_id,store_id,floor_id,room_id,0", $arr , "company_id,location_id,store_id,floor_id,room_id,floor_room_rack_name", "rack_wise_grey_fabrics_stock_report_controller",'setFilterGrid("list_view",-1);','0,0,0,0,0,0','',1) ;
	disconnect($con);
	exit();
}

/*
|--------------------------------------------------------------------------
| shelf_popup
|--------------------------------------------------------------------------
|
*/
if ($action=="shelf_popup")
{
	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$data=explode('_',$data);
	?>	
    <script>
		var selected_id = new Array();
		var selected_name = new Array();
		var selected_attach_id = new Array();

		function check_all_data() 
		{
			var tbl_row_count = document.getElementById( 'list_view' ).rows.length;
			tbl_row_count = tbl_row_count - 0;
			for( var i = 1; i <= tbl_row_count; i++ ) 
			{
				var onclickString = $('#tr_' + i).attr('onclick');
				var paramArr = onclickString.split("'");
				var functionParam = paramArr[1];
				js_set_value( functionParam );
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
			
		function js_set_value(id)
		{
			var str=id.split("_");
			toggle( document.getElementById( 'tr_' + str[0] ), '#FFFFFF' );
			var strdt=str[2];
			str=str[1];
		
			if( jQuery.inArray(  str , selected_id ) == -1 )
			{
				selected_id.push( str );
				selected_name.push( strdt );
			}
			else 
			{
				for( var i = 0; i < selected_id.length; i++ )
				{
					if( selected_id[i] == str  ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i,1 );
			}
			var id = '';
			var ddd='';
			for( var i = 0; i < selected_id.length; i++ )
			{
				id += selected_id[i] + ',';
				ddd += selected_name[i] + ',';
			}
			id = id.substr( 0, id.length - 1 );
			ddd = ddd.substr( 0, ddd.length - 1 );
			$('#shelf_id').val( id );
			$('#shelf_name').val( ddd );
		}
	</script>
    <input type="hidden" id="shelf_id" />
    <input type="hidden" id="shelf_name" />
 	<?
	if ($data[1]=="")
		$floor_cond="";
	else
		$floor_cond=" AND b.floor_id IN(".$data[1].")";
		
	if ($data[2]=="")
		$room_cond="";
	else
		$room_cond=" AND b.room_id IN(".$data[2].")";

	if ($data[3]=="")
		$rack_cond="";
	else
		$rack_cond=" AND b.rack_id IN(".$data[3].")";
	
	$shelf_sql = "SELECT a.floor_room_rack_id, a.floor_room_rack_name, a.company_id, b.location_id, b.store_id, b.floor_id, b.room_id, b.rack_id
    FROM lib_floor_room_rack_mst a INNER JOIN lib_floor_room_rack_dtls b ON a.floor_room_rack_id = b.shelf_id 
    WHERE a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 AND a.company_id IN(".$data[0].") ".$floor_cond." ".$room_cond." ".$rack_cond."
    GROUP BY a.floor_room_rack_id, a.floor_room_rack_name, a.company_id, b.location_id, b.store_id, b.floor_id, b.room_id, b.rack_id
    ORDER BY a.floor_room_rack_name";
    // echo $shelf_sql;
	$companyArr = return_library_array( "SELECT id, company_name FROM lib_company WHERE id IN(".$data[0].")", "id", "company_name" );
	$locationArr = return_library_array( "SELECT id, location_name FROM lib_location WHERE status_active = 1 AND is_deleted = 0 AND company_id IN (SELECT id FROM lib_company WHERE id IN (".$data[0].")) ORDER BY location_name","id","location_name" );
	$storeArr = return_library_array("SELECT id, store_name FROM lib_store_location WHERE status_active = 1 AND is_deleted = 0 ORDER BY store_name","id","store_name");
	$floorArr = return_library_array("SELECT a.floor_room_rack_name, a.floor_room_rack_id FROM lib_floor_room_rack_mst a INNER JOIN lib_floor_room_rack_dtls b ON a.floor_room_rack_id = b.floor_id WHERE a.company_id in(".$data[0].") ".$floor_cond." AND a.is_deleted = 0 AND a.status_active = 1 GROUP BY a.floor_room_rack_name, a.floor_room_rack_id","floor_room_rack_id","floor_room_rack_name");
	$roomArr = return_library_array("SELECT a.floor_room_rack_name, a.floor_room_rack_id FROM lib_floor_room_rack_mst a INNER JOIN lib_floor_room_rack_dtls b ON a.floor_room_rack_id = b.room_id WHERE  a.company_id IN(".$data[0].") ".$floor_cond." ".$room_cond." AND a.is_deleted = 0 AND a.status_active = 1 GROUP BY a.floor_room_rack_name, a.floor_room_rack_id","floor_room_rack_id","floor_room_rack_name");
	$rackArr = return_library_array("SELECT a.floor_room_rack_name, a.floor_room_rack_id FROM lib_floor_room_rack_mst a INNER JOIN lib_floor_room_rack_dtls b ON a.floor_room_rack_id = b.rack_id WHERE  a.company_id IN(".$data[0].") ".$floor_cond." ".$room_cond." ".$rack_cond." AND a.is_deleted = 0 AND a.status_active = 1 GROUP BY a.floor_room_rack_name, a.floor_room_rack_id","floor_room_rack_id","floor_room_rack_name");
	$arr=array(0=>$companyArr,1=>$locationArr,2=>$storeArr,3=>$floorArr,4=>$roomArr,5=>$rackArr);
	
	echo  create_list_view("list_view", "Company,Location,Store,Floor,Room,Rack,Shelf", "70,100,150,80,80,80,100","690","360",0, $shelf_sql, "js_set_value", "floor_room_rack_id,floor_room_rack_name", "", 1, "company_id,location_id,store_id,floor_id,room_id,rack_id,0", $arr, "company_id,location_id,store_id,floor_id,room_id,rack_id,floor_room_rack_name", "rack_wise_grey_fabrics_stock_report_controller",'setFilterGrid("list_view",-1);','0,0,0,0,0,0,0','',1) ;

	//echo  create_list_view("list_view", "Company,Location,Store,Floor,Room,Rack,Shelf", "70,100,150,80,80,80,100","690","360",0, $shelf_sql, "js_set_value", "floor_room_rack_id,floor_room_rack_name", "", 1, "company_id,location_id,store_id,floor_id,room_id,0", $arr , "company_id,location_id,store_id,floor_id,room_id,room_id,rack_id,floor_room_rack_name", "rack_wise_grey_fabrics_stock_report_controller",'setFilterGrid("list_view",-1);','0,0,0,0,0,0','',1) ;

	disconnect($con);
	exit();
}

/*
|--------------------------------------------------------------------------
| report_generate
|--------------------------------------------------------------------------
|
*/
if($action=="report_generate")
{
	$started = microtime(true);
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	ob_start();
	$companyId = str_replace("'", "", $cbo_company_id);
	$buyerId = str_replace("'", "", $cbo_buyer_id);
	$year = str_replace("'", "", $cbo_year);
	$jobNo = str_replace("'", "", $txt_job_no);
	$jobId = str_replace("'", "", $txt_job_id);
	$bookingNo = str_replace("'", "", $txt_booking_no);
	$bookingId = str_replace("'", "", $txt_booking_id);
	$searchBy = str_replace("'", "", $cbo_search_by);
	$searchCommon = str_replace("'", "", $txt_search_comm);
	$product = str_replace("'", "", $txt_product);
	$productId = str_replace("'", "", $txt_product_id);
	$productNo = str_replace("'", "", $txt_product_no);
	$storeId = str_replace("'", "", $txt_store_id);
	$floorId = str_replace("'", "", $txt_floor_id);
	$roomId = str_replace("'", "", $txt_room_id);
	$rackId = str_replace("'", "", $txt_rack_id);
	$shelfId = str_replace("'", "", $txt_shelf_id);
	$valueType = str_replace("'", "", $cbo_value_type);
	$fromDate = str_replace("'", "", $txt_date_from);
	$toDate = str_replace("'", "", $txt_date_to);
	$reportType = str_replace("'", "", $rpt_type);
	//$_SESSION["date_from"] = date('Y-m-d', strtotime($date_from));
	//$_SESSION["date_to"] = date('Y-m-d', strtotime($date_to));
	
	//buyerIdCondition
	$buyerIdCondition = '';
	if($buyerId != 0)
	{
		// $buyerIdCondition = " AND a.buyer_id = ".$buyerId."";
		$buyerIdCondition = " AND d.buyer_name = ".$buyerId."";
	}
	
	//yearCondition;
	$yearCondition = '';
	if($db_type==0)
	{
		$yearCondition = " AND YEAR(d.insert_date) = ".$year."";
	}
	else if($db_type==2)
	{
		$yearCondition = " AND TO_CHAR(d.insert_date,'YYYY') = ".$year."";
	}
	
	//jobNoCondition
	$jobNoCondition = '';
	if($jobNo != '')
	{
		$jobNoCondition = " AND d.job_no_prefix_num IN(".$jobNo.")";
	}
	
	//bookingNoCondition
	$bookingNoCondition = '';
	$bookingPoArr = array();
	if($bookingNo != '')
	{
		$sqlBooking = "
			SELECT
				a.company_id, a.buyer_id,
				b.job_no, b.booking_no, b.booking_type, b.is_short, b.po_break_down_id
			FROM
				wo_booking_mst a
				INNER JOIN wo_booking_dtls b ON a.booking_no = b.booking_no
			WHERE
				a.status_active = 1
				AND a.is_deleted = 0
				AND a.company_id IN(".$companyId.")
				AND b.status_active = 1
				AND b.is_deleted = 0
				AND b.booking_type IN(1,4)
				AND a.booking_no_prefix_num IN(".$bookingNo.")
			GROUP BY
				a.company_id, a.buyer_id, 
				b.job_no, b.booking_no, b.booking_type, b.is_short, b.po_break_down_id
		";
		$sqlBookingRslt = sql_select($sqlBooking);
		foreach($sqlBookingRslt as $row)
		{
			$bookingPoArr[$row[csf('po_break_down_id')]] = $row[csf('po_break_down_id')];
		}
		
		//have to work
		if(!empty($bookingPoArr))
		{
			//$bookingNoCondition = " AND e.po_breakdown_id IN(".implode(',', $bookingPoArr).")";
			$bookingNoCondition = where_con_using_array($bookingPoArr, '0', 'e.po_breakdown_id');
		}
		//echo "<pre>";
		//print_r($bookingPoArr); die;
	}
	
	//searchByCondition
	$searchByCondition = '';
	if($searchCommon != '')
	{
		$expSearchCommon = explode(',', $searchCommon);
		$dataSearchCommon = '';
		foreach($expSearchCommon as $val)
		{
			if($dataSearchCommon != '')
			{
				$dataSearchCommon .= ',';
			}
			$dataSearchCommon .= "'".$val."'";
		}
		
		//style no
		if($searchBy == 1)
		{
			$searchByCondition = " AND d.style_ref_no IN(".$dataSearchCommon.")";
		}
		//order no
		else if($searchBy == 2)
		{
			$searchByCondition = " AND c.po_number IN(".$dataSearchCommon.")";
		}
	}
	
	//productIdCondition
	$productIdCondition = '';
	if($productId != '')
	{
		$expProductId = explode(",",$productId);
		if($db_type==2 && count($expProductId) >1000)
		{
			$productIdCondition = " AND (";
			$prodIdsArr=array_chunk($expProductId,999);
			foreach($prodIdsArr as $pids)
			{
				$pids=implode(",",$pids);
				$productIdCondition .= " e.prod_id IN(".$pids.") OR ";
			}
			$productIdCondition = chop($productIdCondition,'OR ');
			$productIdCondition .= ")";
		}
		else
		{
			$productIdCondition = " AND e.prod_id IN(".$productId.")";
		}
	}

	//storeCondition
	$storeCondition = '';
	if($storeId != '')
	{
		$storeCondition = " AND f.store_id IN(".$storeId.")";
	}

	//floorCondition
	$floorCondition = '';
	if($floorId != '')
	{
		$floorCondition = " AND f.floor_id IN(".$floorId.")";
	}

	//roomCondition
	$roomCondition = '';
	if($roomId != '')
	{
		$roomCondition = " AND f.room IN(".$roomId.")";
	}

	//rackCondition
	$rackCondition = '';
	if($rackId != '')
	{
		$rackCondition = " AND f.rack IN(".$rackId.")";
	}

	//rackCondition
	$shelfCondition = '';
	if($shelfId != '')
	{
		$shelfCondition = " AND f.self IN(".$shelfId.")";
	}
	
	//dateCondition
	$dateCondition = '';
	$dateCondition2 = '';
	if($fromDate != '' && $toDate != '')
	{
		if($db_type == 0)
		{
			$startDate = change_date_format($fromDate,"yyyy-mm-dd","");
			$endDate = change_date_format($toDate,"yyyy-mm-dd","");
		}
		else if($db_type==2)
		{
			$startDate = change_date_format($fromDate,"","",1);
			$endDate = change_date_format($toDate,"","",1);
		}
		
		if ($reportType == 1)
		{
			$dateCondition = " AND f.transaction_date <= '".$startDate."'";
			$dateCondition2 = " AND a.transfer_date <= '".$startDate."'";
		}
		else
		{
			$dateCondition = " AND f.transaction_date <= '".$endDate."'";
			$dateCondition2 = " AND a.transfer_date <= '".$endDate."'";
		}
	}

	/*
	|--------------------------------------------------------------------------
	|
	| transfer out qty
	|--------------------------------
	| entry_form IN(13,81,82,83)
	| trans_type = 6
	|
	| issue return qty
	|--------------------------------
	| entry_form IN(51,84)
	| trans_type = 4
	|
	| transfer in qty
	|--------------------------------
	| entry_form IN(13)
	| trans_type = 5
	|
	| receive qty
	|--------------------------------
	| entry_form IN(2,22,58)
	| trans_type = 1
	|
	|--------------------------------------------------------------------------
	|
	*/
	/*
	$sqlRcvRollQty = "SELECT d.company_name, e.prod_id, e.po_breakdown_id, e.entry_form, SUM(g.quantity) AS rcv_qty, f.store_id, f.floor_id, f.room, f.rack, f.self, f.bin_box, CASE WHEN e.entry_form IN (58,84) THEN COUNT(g.id) WHEN e.entry_form IN (2,22) THEN SUM(g.no_of_roll) ELSE 0 END AS no_of_roll_rcv FROM wo_po_break_down c INNER JOIN wo_po_details_master d ON c.job_no_mst = d.job_no INNER JOIN order_wise_pro_details e ON c.id = e.po_breakdown_id INNER JOIN inv_transaction f ON e.trans_id = f.id INNER JOIN pro_grey_prod_entry_dtls g ON f.id = g.trans_id WHERE c.status_active = 1 AND c.is_deleted = 0 AND d.status_active = 1 AND d.is_deleted = 0 AND e.status_active = 1 AND e.is_deleted = 0 AND e.entry_form IN(2,22,58,84) AND e.trans_type IN(1,4) AND f.status_active = 1 AND f.is_deleted = 0 AND d.company_name IN(".$companyId.") ".$buyerIdCondition." ".$yearCondition." ".$jobNoCondition." ".$bookingNoCondition." ".$searchByCondition." ".$productIdCondition." ".$floorCondition." ".$roomCondition." ".$rackCondition." ".$dateCondition." GROUP BY  d.company_name, e.prod_id, e.po_breakdown_id, e.entry_form, f.store_id, f.floor_id, f.room, f.rack, f.self, f.bin_box";	
	*/
	//only for roll basis 
	$sqlRcvRollQty = "
		SELECT
			d.company_name,
			e.prod_id, e.po_breakdown_id, e.entry_form, SUM(h.qnty) AS rcv_qty,
			f.store_id, f.floor_id, f.room, f.rack, f.self, f.bin_box,
			COUNT(h.id) AS no_of_roll_rcv
        FROM
			wo_po_break_down c
			INNER JOIN wo_po_details_master d ON c.job_no_mst = d.job_no
			INNER JOIN order_wise_pro_details e ON c.id = e.po_breakdown_id
            INNER JOIN inv_transaction f ON e.trans_id = f.id
			INNER JOIN pro_grey_prod_entry_dtls g ON f.id = g.trans_id
			LEFT JOIN pro_roll_details h ON g.id = h.dtls_id
        WHERE
			c.status_active = 1
			AND c.is_deleted = 0
			AND d.status_active = 1
			AND d.is_deleted = 0
			AND e.status_active = 1
			AND e.is_deleted = 0
			AND e.entry_form IN(2,22,58,84)
			AND e.trans_type IN(1,4)
			AND e.trans_id > 0
			AND f.status_active = 1
			AND f.is_deleted = 0
			AND d.company_name IN(".$companyId.")
			".$buyerIdCondition."
			".$yearCondition."
			".$jobNoCondition."
			".$bookingNoCondition."
			".$searchByCondition."
			".$productIdCondition."
			".$storeCondition."
			".$floorCondition."
			".$roomCondition."
			".$rackCondition."
			".$shelfCondition."
			".$dateCondition."
			AND h.entry_form IN(2,22,58,84)
        GROUP BY 
			d.company_name,
			e.prod_id, e.po_breakdown_id, e.entry_form,
			f.store_id, f.floor_id, f.room, f.rack, f.self, f.bin_box
	";	
	//echo $sqlRcvRollQty; die;
	$sqlRcvRollRslt = sql_select($sqlRcvRollQty);
	$dataArr = array();
	$poArr = array();
	foreach($sqlRcvRollRslt as $row)
	{
		$compId = $row[csf('company_name')];
		$productId = $row[csf('prod_id')];
		$orderId = $row[csf('po_breakdown_id')];
		$storeId = $row[csf('store_id')]*1;
		$floorId = $row[csf('floor_id')]*1;
		$roomId = $row[csf('room')]*1;
		$rackId = $row[csf('rack')]*1;
		$selfId = $row[csf('self')]*1;
		$binId = $row[csf('bin_box')]*1;
		$poArr[$orderId] = $orderId;
		
		if ($reportType == 1 || $reportType == 3)
		{
			//$rollRcvQty += $row[csf('no_of_roll_rcv')];
			$dataArr[$compId][$productId][$orderId][$storeId][$floorId][$roomId][$rackId][$selfId][$binId]['rollRcvQty'] += $row[csf('no_of_roll_rcv')];
			if($row[csf('entry_form')]  == 84)
			{
				//$issueReturnQty += $row[csf('rcv_qty')];
				$dataArr[$compId][$productId][$orderId][$storeId][$floorId][$roomId][$rackId][$selfId][$binId]['issueReturnQty'] += $row[csf('rcv_qty')];
			}
			else
			{
				//$rcvQty += $row[csf('rcv_qty')];
				$dataArr[$compId][$productId][$orderId][$storeId][$floorId][$roomId][$rackId][$selfId][$binId]['rcvQty'] += $row[csf('rcv_qty')];
			}
			
		}
		elseif ($reportType == 5) // Shelf wise
		{
			$dataArr[$compId][$productId][$orderId][$storeId][$floorId][$roomId][$rackId][$selfId]['rollRcvQty'] += $row[csf('no_of_roll_rcv')];
			if($row[csf('entry_form')]  == 84)
			{
				//$issueReturnQty += $row[csf('rcv_qty')];
				$dataArr[$compId][$productId][$orderId][$storeId][$floorId][$roomId][$rackId][$selfId]['issueReturnQty'] += $row[csf('rcv_qty')];
			}
			else
			{
				//$rcvQty += $row[csf('rcv_qty')];
				$dataArr[$compId][$productId][$orderId][$storeId][$floorId][$roomId][$rackId][$selfId]['rcvQty'] += $row[csf('rcv_qty')];
			}
			
		}
		elseif ($reportType == 2)
		{
			//$rollRcvQty += $row[csf('no_of_roll_rcv')];
			//$rcvQty += $row[csf('rcv_qty')];
			
			$dataArr[$compId][$productId][$orderId][$storeId][$floorId][$roomId][$rackId][$selfId][$binId]['rollRcvQty'] += $row[csf('no_of_roll_rcv')];
			$dataArr[$compId][$productId][$orderId][$storeId][$floorId][$roomId][$rackId][$selfId][$binId]['rcvQty'] += $row[csf('rcv_qty')];
			
		}
		elseif ($reportType == 4)
		{
		}
	}
	unset($sqlRcvRollRslt);
	//echo "<pre>";
	//print_r($dataArr); die;

	/*
	|--------------------------------------------------------------------------
	| for issue qty and roll
	| order to order transfer
	|--------------------------------------------------------------------------
	|
	*/
	$sqlNoOfRoll="
		SELECT
			d.company_name,
			e.prod_id, e.po_breakdown_id, e.trans_type, SUM(e.quantity) AS rcv_qty,
			f.store_id, f.floor_id, f.room, f.rack, f.self, f.bin_box, COUNT(g.id) AS issue_roll
		FROM 
			wo_po_break_down c
			INNER JOIN wo_po_details_master d ON c.job_no_mst = d.job_no
			INNER JOIN order_wise_pro_details e ON c.id = e.po_breakdown_id
			INNER JOIN inv_transaction f ON e.trans_id = f.id 
			INNER JOIN inv_item_transfer_dtls g ON e.dtls_id = g.id 
		WHERE
			c.status_active = 1
			AND c.is_deleted = 0
			AND d.status_active = 1
			AND d.is_deleted = 0
			AND e.status_active = 1 
			AND e.is_deleted = 0 
			AND e.entry_form IN(82,83,110,183) 
			AND e.trans_type IN(5,6) 
			AND f.status_active = 1 
			AND f.is_deleted = 0 
			AND g.status_active = 1 
			AND g.is_deleted = 0	
			".$buyerIdCondition."
			".$yearCondition."
			".$jobNoCondition."
			".$bookingNoCondition."
			".$searchByCondition."
			".$productIdCondition."
			".$storeCondition."
			".$floorCondition."
			".$roomCondition."
			".$rackCondition."
			".$shelfCondition."
			".$dateCondition."
        GROUP BY 
			d.company_name,
			e.prod_id, e.po_breakdown_id, e.trans_type,
			f.store_id, f.floor_id, f.room, f.rack, f.self, f.bin_box
		UNION ALL
		SELECT
			d.company_name,
			e.prod_id, e.po_breakdown_id, e.trans_type, SUM(e.quantity) AS rcv_qty, 
			f.store_id, f.floor_id, f.room, f.rack, f.self, f.bin_box, SUM(g.roll) AS issue_roll
		FROM
			wo_po_break_down c
			INNER JOIN wo_po_details_master d ON c.job_no_mst = d.job_no
			INNER JOIN order_wise_pro_details e ON c.id = e.po_breakdown_id
			INNER JOIN inv_transaction f ON e.trans_id = f.id 
			INNER JOIN inv_item_transfer_dtls g ON e.dtls_id = g.id 
		WHERE
			c.status_active = 1
			AND c.is_deleted = 0
			AND d.status_active = 1
			AND d.is_deleted = 0
			AND e.status_active = 1 
			AND e.is_deleted = 0 
			AND e.entry_form IN(13) 
			AND e.trans_type IN(5,6) 
			AND f.status_active = 1 
			AND f.is_deleted = 0 
			AND g.status_active = 1 
			AND g.is_deleted = 0
			".$buyerIdCondition."
			".$yearCondition."
			".$jobNoCondition."
			".$bookingNoCondition."
			".$searchByCondition."
			".$productIdCondition."
			".$storeCondition."
			".$floorCondition."
			".$roomCondition."
			".$rackCondition."
			".$shelfCondition."
			".$dateCondition."
        GROUP BY 
			d.company_name,
			e.prod_id, e.po_breakdown_id, e.trans_type,
			f.store_id, f.floor_id, f.room, f.rack, f.self, f.bin_box
	";
	//echo $sqlNoOfRoll; die;
	$sqlNoOfRollResult = sql_select($sqlNoOfRoll);
	foreach($sqlNoOfRollResult as $row)
	{
		$compId = $row[csf('company_name')];
		$productId = $row[csf('prod_id')];
		$orderId = $row[csf('po_breakdown_id')];
		$storeId = $row[csf('store_id')]*1;
		$floorId = $row[csf('floor_id')]*1;
		$roomId = $row[csf('room')]*1;
		$rackId = $row[csf('rack')]*1;
		$selfId = $row[csf('self')]*1;
		$binId = $row[csf('bin_box')]*1;

		if ($reportType == 1 || $reportType == 3)
		{
			if($row[csf('trans_type')] == 5)
			{
				//$rollRcvQty += $row[csf('issue_roll')];
				//$transferInQty += $row[csf('rcv_qty')];
				$poArr[$orderId] = $orderId;
				$dataArr[$compId][$productId][$orderId][$storeId][$floorId][$roomId][$rackId][$selfId][$binId]['rollRcvQty'] += $row[csf('issue_roll')];
				$dataArr[$compId][$productId][$orderId][$storeId][$floorId][$roomId][$rackId][$selfId][$binId]['transferInQty'] += $row[csf('rcv_qty')];
			}
			if($row[csf('trans_type')] == 6)
			{
				//$rollIssueQty += $row[csf('issue_roll')];
				//$transferOutQty += $row[csf('rcv_qty')];
				
				$transOutArr[$compId][$productId][$orderId][$storeId][$floorId][$roomId][$rackId][$selfId][$binId]['rollIssueQty'] += $row[csf('issue_roll')];
				$transOutArr[$compId][$productId][$orderId][$storeId][$floorId][$roomId][$rackId][$selfId][$binId]['transferOutQty'] += $row[csf('rcv_qty')];
			}
		}
		elseif ($reportType == 5) // shelf wise
		{
			if($row[csf('trans_type')] == 5)
			{
				//$rollRcvQty += $row[csf('issue_roll')];
				//$transferInQty += $row[csf('rcv_qty')];
				$poArr[$orderId] = $orderId;
				$dataArr[$compId][$productId][$orderId][$storeId][$floorId][$roomId][$rackId][$selfId]['rollRcvQty'] += $row[csf('issue_roll')];
				$dataArr[$compId][$productId][$orderId][$storeId][$floorId][$roomId][$rackId][$selfId]['transferInQty'] += $row[csf('rcv_qty')];
			}
			if($row[csf('trans_type')] == 6)
			{
				//$rollIssueQty += $row[csf('issue_roll')];
				//$transferOutQty += $row[csf('rcv_qty')];
				
				$transOutArr[$compId][$productId][$orderId][$storeId][$floorId][$roomId][$rackId][$selfId]['rollIssueQty'] += $row[csf('issue_roll')];
				$transOutArr[$compId][$productId][$orderId][$storeId][$floorId][$roomId][$rackId][$selfId]['transferOutQty'] += $row[csf('rcv_qty')];
			}
		}
		elseif ($reportType == 2)
		{
			if($row[csf('trans_type')] == 5)
			{
				//$rollRcvQty += $row[csf('issue_roll')];
				//$transferInQty += $row[csf('rcv_qty')];
				
				$poArr[$orderId] = $orderId;
				$dataArr[$compId][$productId][$orderId][$storeId][$floorId][$roomId][$rackId][$selfId][$binId]['rollRcvQty'] += $row[csf('issue_roll')];
				$dataArr[$compId][$productId][$orderId][$storeId][$floorId][$roomId][$rackId][$selfId][$binId]['transferInQty'] += $row[csf('rcv_qty')];
			}
			if($row[csf('trans_type')] == 6)
			{
				//$rollIssueQty += $row[csf('issue_roll')];
				//$transferOutQty += $row[csf('rcv_qty')];
				
				$transOutArr[$compId][$productId][$orderId][$storeId][$floorId][$roomId][$rackId][$selfId][$binId]['rollIssueQty'] += $row[csf('issue_roll')];
				$transOutArr[$compId][$productId][$orderId][$storeId][$floorId][$roomId][$rackId][$selfId][$binId]['transferOutQty'] += $row[csf('rcv_qty')];
			}
		}
		elseif ($reportType == 4)
		{
		}
	}
	unset($sqlNoOfRollResult);
	//echo $rollRcvQty."=".$transferInQty."=".$rollIssueQty."=".$transferOutQty;
	//echo "<pre>";
	//print_r($noOfRollIssueArr);
	
	/*
	|--------------------------------------------------------------------------
	| for issue qty and roll
	|--------------------------------------------------------------------------
	|
	*/
	if(!empty($poArr))
	{
		$con = connect();
		execute_query("DELETE FROM TMP_PO_ID WHERE USER_ID = ".$user_id."");
		oci_commit($con);
		
		$con = connect();
		foreach($poArr as $poId)
		{
			execute_query("INSERT INTO TMP_PO_ID(PO_ID,USER_ID) VALUES(".$poId.", ".$user_id.")");
		}
		oci_commit($con);
		//disconnect($con);
		
		$sqlNoOfRollIssue="
			SELECT
				d.company_name,
				e.prod_id, e.po_breakdown_id, SUM(e.quantity) AS issue_qty,
				f.store_id, f.floor_id, f.room, f.rack, f.self, f.bin_box,
				SUM(g.no_of_roll) AS issue_roll
			FROM
				wo_po_break_down c
				INNER JOIN wo_po_details_master d ON c.job_no_mst = d.job_no
				INNER JOIN order_wise_pro_details e ON c.id = e.po_breakdown_id
				INNER JOIN inv_transaction f ON e.trans_id = f.id
				INNER JOIN inv_grey_fabric_issue_dtls g ON e.dtls_id = g.id
			WHERE
				e.status_active = 1
				AND e.is_deleted = 0
				AND e.entry_form IN(16)
				AND e.trans_type = 2
				AND f.status_active = 1
				AND f.is_deleted = 0
				AND g.status_active = 1
				AND g.is_deleted = 0
				".$dateCondition."
				AND e.po_breakdown_id in(SELECT PO_ID FROM TMP_PO_ID WHERE USER_ID = ".$user_id.")
			GROUP BY 
				d.company_name,
				e.prod_id, e.po_breakdown_id,
				f.store_id, f.floor_id, f.room, f.rack, f.self, f.bin_box
			UNION ALL
			SELECT
				d.company_name,
				e.prod_id, e.po_breakdown_id, SUM(e.quantity) AS issue_qty,
				f.store_id, f.floor_id, f.room, f.rack, f.self, f.bin_box,
				COUNT(g.id) AS issue_roll
			FROM
				wo_po_break_down c
				INNER JOIN wo_po_details_master d ON c.job_no_mst = d.job_no
				INNER JOIN order_wise_pro_details e ON c.id = e.po_breakdown_id
				INNER JOIN inv_transaction f ON e.trans_id = f.id
				INNER JOIN pro_roll_details g ON e.dtls_id = g.dtls_id
			WHERE
				e.status_active = 1
				AND e.is_deleted = 0
				AND e.entry_form IN(61)
				AND e.trans_type = 2
				AND f.status_active = 1
				AND f.is_deleted = 0
				AND g.status_active = 1
				AND g.is_deleted = 0
				AND g.entry_form IN(61) 
				".$dateCondition."
				AND e.po_breakdown_id in(SELECT PO_ID FROM TMP_PO_ID WHERE USER_ID = ".$user_id.")
			GROUP BY 
				d.company_name,
				e.prod_id, e.po_breakdown_id,
				f.store_id, f.floor_id, f.room, f.rack, f.self, f.bin_box
		";
		//disconnect($con); die;
		//echo $sqlNoOfRollIssue; die;
		$sqlNoOfRollIssueResult = sql_select($sqlNoOfRollIssue);
		$noOfRollIssueArr = array();
		foreach($sqlNoOfRollIssueResult as $row)
		{
			$compId = $row[csf('company_name')];
			$productId = $row[csf('prod_id')];
			$orderId = $row[csf('po_breakdown_id')];
			$storeId = $row[csf('store_id')]*1;
			$floorId = $row[csf('floor_id')]*1;
			$roomId = $row[csf('room')]*1;
			$rackId = $row[csf('rack')]*1;
			$selfId = $row[csf('self')]*1;
			$binId = $row[csf('bin_box')]*1;
			
			if ($reportType == 1 || $reportType == 3)
			{
				//$issueQty += $row[csf('issue_qty')]*1;
				//$rollIssueQty += $row[csf('issue_roll')];
				
				$issueQtyArr[$compId][$productId][$orderId][$storeId][$floorId][$roomId][$rackId][$selfId][$binId]['issueQty'] += $row[csf('issue_qty')];
				$noOfRollIssueArr[$compId][$productId][$orderId][$storeId][$floorId][$roomId][$rackId][$selfId][$binId]['rollIssueQty'] += $row[csf('issue_roll')];
			}
			elseif ($reportType == 5) // shelf wise
			{
				$issueQtyArr[$compId][$productId][$orderId][$storeId][$floorId][$roomId][$rackId][$selfId]['issueQty'] += $row[csf('issue_qty')];
				$noOfRollIssueArr[$compId][$productId][$orderId][$storeId][$floorId][$roomId][$rackId][$selfId]['rollIssueQty'] += $row[csf('issue_roll')];
			}
			elseif ($reportType == 2)
			{
				//$issueQty += $row[csf('issue_qty')]*1;
				//$rollIssueQty += $row[csf('issue_roll')];
				
				$issueQtyArr[$compId][$productId][$orderId][$storeId][$floorId][$roomId][$rackId][$selfId][$binId]['issueQty'] += $row[csf('issue_qty')];
				$noOfRollIssueArr[$compId][$productId][$orderId][$storeId][$floorId][$roomId][$rackId][$selfId][$binId]['rollIssueQty'] += $row[csf('issue_roll')];
			}
			elseif ($reportType == 4)
			{
			}
		}
		unset($sqlNoOfRollIssueResult);
	}
	//echo $issueQty."=".$rollIssueQty;
	//echo "<pre>";
	//print_r($issueQtyArr);
	
	if(empty($dataArr))
	{
		echo get_empty_data_msg();
		die;
	}
	
	$company_arr = return_library_array( "select id, company_short_name from lib_company", "id", "company_short_name"  );
	$buyer_array = return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$count_arr = return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');
	$brand_arr = return_library_array( "select id, brand_name from lib_brand",'id','brand_name');
	$color_arr = return_library_array( 'SELECT id, color_name FROM lib_color', 'id', 'color_name');
	
	//floorSql
	$floorSql = "
		SELECT a.floor_room_rack_id, a.floor_room_rack_name, a.company_id, b.location_id, b.store_id
    	FROM lib_floor_room_rack_mst a
		INNER JOIN lib_floor_room_rack_dtls b ON a.floor_room_rack_id = b.floor_id
    	WHERE a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 AND a.company_id IN(".$companyId.")
	";
	$floorDetails = return_library_array( $floorSql, 'floor_room_rack_id', 'floor_room_rack_name');
	
	//roomSql
	$roomSql = "
		SELECT a.floor_room_rack_id, a.floor_room_rack_name, a.company_id, b.location_id, b.store_id
    	FROM lib_floor_room_rack_mst a
		INNER JOIN lib_floor_room_rack_dtls b ON a.floor_room_rack_id = b.room_id
    	WHERE a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 AND a.company_id IN(".$companyId.")
	";
	$roomDetails = return_library_array( $roomSql, 'floor_room_rack_id', 'floor_room_rack_name');
	
	//rackSql
	$rackSql = "
		SELECT a.floor_room_rack_id, a.floor_room_rack_name, a.company_id, b.location_id, b.store_id, b.serial_no
    	FROM lib_floor_room_rack_mst a
		INNER JOIN lib_floor_room_rack_dtls b ON a.floor_room_rack_id = b.rack_id
    	WHERE a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 AND a.company_id IN(".$companyId.")
	";
	$rackDetails = return_library_array( $rackSql, 'floor_room_rack_id', 'floor_room_rack_name');
	
	$rackSerialNoSql = "
		SELECT b.floor_room_rack_dtls_id, b.serial_no
    	FROM lib_floor_room_rack_mst a
		INNER JOIN lib_floor_room_rack_dtls b ON a.floor_room_rack_id = b.rack_id
    	WHERE a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 AND a.company_id IN(".$companyId.")
		GROUP BY b.floor_room_rack_dtls_id, b.serial_no
		ORDER BY b.serial_no ASC
	";
	$rackSerialNoResult = sql_select($rackSerialNoSql);
	foreach($rackSerialNoResult as $row)
	{
		$rackSerialNoArr[$row[csf('floor_room_rack_dtls_id')]] = $row[csf('serial_no')];
	}
	unset($rackSerialNoResult);

	//selfSql
	$selfSql = "
		SELECT a.floor_room_rack_id, a.floor_room_rack_name, a.company_id, b.location_id, b.store_id
    	FROM lib_floor_room_rack_mst a
		INNER JOIN lib_floor_room_rack_dtls b ON a.floor_room_rack_id = b.shelf_id
    	WHERE a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 AND a.company_id IN(".$companyId.")
	";
	$selfDetails = return_library_array( $selfSql, 'floor_room_rack_id', 'floor_room_rack_name');
	
	//binSql
	$binSql = "
		SELECT a.floor_room_rack_id, a.floor_room_rack_name, a.company_id, b.location_id, b.store_id
    	FROM lib_floor_room_rack_mst a
		INNER JOIN lib_floor_room_rack_dtls b ON a.floor_room_rack_id = b.bin_id
    	WHERE a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 AND a.company_id IN(".$companyId.")
	";
	$binDetails = return_library_array( $binSql, 'floor_room_rack_id', 'floor_room_rack_name');
		
	//composition and constructtion
	$composition_arr=array();
	$constructtion_arr=array();
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$deter_array=sql_select($sql_deter);
	foreach( $deter_array as $row) 
	{
		$constructtion_arr[$row[csf('id')]]=$row[csf('construction')];
		$composition_arr[$row[csf('id')]].=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ";
	}
	unset($deter_array);

	//for order wise and rack wise button
	if ($reportType == 2 || $reportType == 3)
	{
		$prodArray = array();
		$poArray = array();
		foreach($dataArr as $compId=>$compArr)
		{
			foreach($compArr as $productId=>$productArr)
			{
				foreach($productArr as $orderId=>$orderArr)
				{
					foreach($orderArr as $storeId=>$storeArr)
					{
						foreach($storeArr as $floorId=>$floorArr)
						{
							foreach($floorArr as $roomId=>$roomArr)
							{
								foreach($roomArr as $rackId=>$rackArr)
								{
									foreach($rackArr as $selfId=>$selfArr)
									{
										foreach($selfArr as $binId=>$row)
										{
											$prodArray[$productId] = $productId;
											$poArray[$orderId] = $orderId;
										}
									}
								}
							}
						}
					}
				}
			}
		}

		$sqlYarn = "SELECT e.prod_id, e.entry_form, g.febric_description_id, g.gsm, g.width, g.machine_dia, g.stitch_length, g.color_id, g.color_range_id, g.yarn_count, g.brand_id, g.yarn_lot, d.booking_id from order_wise_pro_details e inner join pro_grey_prod_entry_dtls g on e.dtls_id = g.id   inner join inv_receive_master d on d.id = g.mst_id
		where e.entry_form in(2,22,58,84) ".where_con_using_array($prodArray, '0', 'e.prod_id')."";
		// echo $sqlYarn;
		$sqlYarnRslt = sql_select($sqlYarn);
		$yarnInfoArr = array();
		foreach($sqlYarnRslt as $row)
		{
			$prodId = $row[csf('prod_id')];
			// echo $prodId.'===<br>';
			$yarnInfoArr[$prodId]['construction'] = $constructtion_arr[$row[csf('febric_description_id')]];
			$yarnInfoArr[$prodId]['composition'] = $composition_arr[$row[csf('febric_description_id')]];
			$yarnInfoArr[$prodId]['gsm'] = $row[csf('gsm')];
			$yarnInfoArr[$prodId]['width'] = $row[csf('width')];
			$yarnInfoArr[$prodId]['machine_dia'] = $row[csf('machine_dia')];
			$yarnInfoArr[$prodId]['stitch_length'] = $row[csf('stitch_length')];
			if ($row[csf('entry_form')]==2) 
			{
				$yarnInfoArr[$prodId]['program_no'] = $row[csf('booking_id')];
			}
			
			$expColor = explode(',', $row[csf('color_id')]);
			$clrArr = array();
			foreach($expColor as $clr)
			{
				$clrArr[$clr] = $color_arr[$clr];
			}
			
			$yarnInfoArr[$prodId]['color_id'] = implode(',', $clrArr);
			$yarnInfoArr[$prodId]['color_range_id'] = $color_range[$row[csf('color_range_id')]];
			$yarnInfoArr[$prodId]['yarn_count'] = $row[csf('yarn_count')];
			$yarnInfoArr[$prodId]['brand_id'] = $row[csf('brand_id')];
			$yarnInfoArr[$prodId]['yarn_lot'] = $row[csf('yarn_lot')];
		}
		unset($sqlYarnRslt);
		//echo "<pre>";
		//print_r($infoArr);
	}

	//for shelf wise button
	if ($reportType == 5)
	{
		$prodArray = array();
		$poArray = array();
		foreach($dataArr as $compId=>$compArr)
		{
			foreach($compArr as $productId=>$productArr)
			{
				foreach($productArr as $orderId=>$orderArr)
				{
					foreach($orderArr as $storeId=>$storeArr)
					{
						foreach($storeArr as $floorId=>$floorArr)
						{
							foreach($floorArr as $roomId=>$roomArr)
							{
								foreach($roomArr as $rackId=>$rackArr)
								{
									foreach($rackArr as $selfId=>$row)
									{
										$prodArray[$productId] = $productId;
										$poArray[$orderId] = $orderId;
									}
								}
							}
						}
					}
				}
			}
		}
		
		$sqlYarn = "SELECT e.prod_id, e.entry_form, g.febric_description_id, g.gsm, g.width, g.machine_dia, g.stitch_length, g.color_id, g.color_range_id, g.yarn_count, g.brand_id, g.yarn_lot, d.booking_id from order_wise_pro_details e inner join pro_grey_prod_entry_dtls g on e.dtls_id = g.id   inner join inv_receive_master d on d.id = g.mst_id
		where e.entry_form in(2,22,58,84) ".where_con_using_array($prodArray, '0', 'e.prod_id')."";
		// echo $sqlYarn;
		$sqlYarnRslt = sql_select($sqlYarn);
		$yarnInfoArr = array();
		foreach($sqlYarnRslt as $row)
		{
			$prodId = $row[csf('prod_id')];
			// echo $prodId.'===<br>';
			$yarnInfoArr[$prodId]['construction'] = $constructtion_arr[$row[csf('febric_description_id')]];
			$yarnInfoArr[$prodId]['composition'] = $composition_arr[$row[csf('febric_description_id')]];
			$yarnInfoArr[$prodId]['gsm'] = $row[csf('gsm')];
			$yarnInfoArr[$prodId]['width'] = $row[csf('width')];
			$yarnInfoArr[$prodId]['machine_dia'] = $row[csf('machine_dia')];
			$yarnInfoArr[$prodId]['stitch_length'] = $row[csf('stitch_length')];
			if ($row[csf('entry_form')]==2) 
			{
				$yarnInfoArr[$prodId]['program_no'] = $row[csf('booking_id')];
			}
			
			$expColor = explode(',', $row[csf('color_id')]);
			$clrArr = array();
			foreach($expColor as $clr)
			{
				$clrArr[$clr] = $color_arr[$clr];
			}
			
			$yarnInfoArr[$prodId]['color_id'] = implode(',', $clrArr);
			$yarnInfoArr[$prodId]['color_range_id'] = $color_range[$row[csf('color_range_id')]];
			$yarnInfoArr[$prodId]['yarn_count'] = $row[csf('yarn_count')];
			$yarnInfoArr[$prodId]['brand_id'] = $row[csf('brand_id')];
			$yarnInfoArr[$prodId]['yarn_lot'] = $row[csf('yarn_lot')];
		}
		unset($sqlYarnRslt);
		//echo "<pre>";
		//print_r($infoArr);
	}
	
	/*
	|--------------------------------------------------------------------------
	| Summary
	|--------------------------------------------------------------------------
	|
	*/
	if ($reportType == 1)
	{
		$newDataArr = array();
		foreach($dataArr as $compId=>$compArr)
		{
			foreach($compArr as $productId=>$productArr)
			{
				foreach($productArr as $orderId=>$orderArr)
				{
					foreach($orderArr as $storeId=>$storeArr)
					{
						foreach($storeArr as $floorId=>$floorArr)
						{
							foreach($floorArr as $roomId=>$roomArr)
							{
								foreach($roomArr as $rackId=>$rackArr)
								{
									foreach($rackArr as $selfId=>$selfArr)
									{
										foreach($selfArr as $binId=>$row)
										{
											if($floorId && $roomId && $rackId)
											{
												//total receive calculation
												$totalRcvQty = number_format($row['rcvQty'],2,'.','')+number_format($row['issueReturnQty'],2,'.','')+number_format($row['transferInQty'],2,'.','');
												//total issue calculation
												$issueQty = $issueQtyArr[$compId][$productId][$orderId][$storeId][$floorId][$roomId][$rackId][$selfId][$binId]['issueQty'];
												$rcvReturnQty = 0;
												$transferOutQty = $transOutArr[$compId][$productId][$orderId][$storeId][$floorId][$roomId][$rackId][$selfId][$binId]['transferOutQty'];
												$totalIssueQty = number_format($issueQty,2,'.','')+number_format($rcvReturnQty,2,'.','')+number_format($transferOutQty,2,'.','');
												//$isqty+=$issueQty;
												//$tQty+=$transferOutQty;
												
												//stock calculation
												$stockQty = $totalRcvQty - $totalIssueQty;
												if($stockQty > 0)
												{
													//$rcv +=$totalRcvQty;
													//$issue +=$totalIssueQty;
													$newDataArr[$floorDetails[$floorId]][$roomDetails[$roomId]][$rackSerialNoArr[$rackId]][$rackDetails[$rackId]][$compId]['stockQty'] += $stockQty;
													//$newDataArr[$floorId][$roomId][$rackId][$compId]['stockQty'] += $stockQty;
													//$tot_com[$compId]=$compId;
												}
											}
										}
									}
								}
							}
						}
					}
				}
			}
		}
		//echo $rcv.'='.$issue.'='.$isqty.'='.$tQty;
		//echo "<pre>";
		//print_r($newDataArr); die;

		$expcompany = explode(',', $companyId);
		$onOfCompany = count($expcompany);
		$comWidth = (120*$onOfCompany);
		$colSpan = (6+$onOfCompany);
		$width = 480+$comWidth;
		?>
		<fieldset style="width:<? echo $width+20; ?>px;margin:5px auto;">
			<table width="<? echo $width; ?>" cellpadding="1" cellspacing="0" border="1" rules="all" class="rpt_table" align="left">
				<thead>
                    <tr class="form_caption" style="border:none;">
                    	<th align="center" colspan="<?php echo $colSpan; ?>" style="font-size:16px"><strong>Rack Wise Grey Fabrics Stock Report<br />This report is generated based on date : <?php echo date('d-m-Y', strtotime($fromDate)); ?></strong></th>
                    </tr>
                    <tr>
						<th width="30" rowspan="2">SL</th>
						<th width="60" rowspan="2">Floor</th>
						<th width="60" rowspan="2">Room</th>
						<th width="60" rowspan="2">Rack</th>
                        <th width="<?php echo $comWidth; ?>" colspan="<?php echo $onOfCompany; ?>">Stock Qty As On <?php echo date('d-m-Y', strtotime($fromDate)); ?></th>
                        <th width="120" rowspan="2">Rack Total</th>
                        <th rowspan="2">Remarks</th>
					</tr>
                    <tr>
                    <?php
					foreach($expcompany as $com)
					{
						?>
                        <th width="120"><?php echo $company_arr[$com];?></th>
                        <?php
					}
					?>
                    </tr>
				</thead>
			</table>
			<div style="width:<? echo $width+20; ?>px; overflow-y: scroll; max-height:380px;" id="scroll_body">
				<table width="<? echo $width; ?>" cellpadding="1" cellspacing="0" border="1" rules="all" class="rpt_table" align="left" id="table_body">
					<tbody>
					<?php
					$sl = 0;
					$grandTotal = array();
					$rackIdArrChart = array();
					$rackQtyArrChart = array();
					foreach($newDataArr as $floorId=>$floorArr)
					{
						foreach($floorArr as $roomId=>$roomArr)
						{
							ksort($roomArr);
							foreach($roomArr as $seq=>$seqArr)
							{
								foreach($seqArr as $rackId=>$row)
								{
									if($valueType != 1 && $row['stockQty'] == 0)
									{
										continue;
									}
									
									$sl++;
									?>
									<tr>
										<td width="30" align="center"><?php echo $sl; ?></td>
                                        <td style="word-break:break-all;" width="60"><?php echo $floorId; ?></td>
										<td style="word-break:break-all;" width="60"><?php echo $roomId; ?></td>
										<td style="word-break:break-all;" width="60"><?php echo $rackId; ?></td>
										<?
										$rac_tot=0;
										foreach($expcompany as $com)
										{
											?>
											<td width="120" title="<?php //echo ?>" align="right"><?php echo number_format($row[$com]['stockQty'],2); ?></td>
											<?php
											$rac_tot += $row[$com]['stockQty'];
											$rac_grand_tot += $row[$com]['stockQty'];
											$com_grand_tot[$com] += $row[$com]['stockQty'];
											
											if($rackId !='')
											{
												$rackIdArrChart[$rackId] = $rackId;
												$rackQtyArrChart[$com][$rackId] += number_format($row[$com]['stockQty'], 2, ".", "");
											}											
										}
										?>
										<td width="120" align="right"><?php echo number_format($rac_tot,2); ?></td>
										<td align="right"></td>
									</tr>
									<?php
								}
							}
						}
					}
                    ?>
                    </tbody>
				</table>
            </div>
            <table width="<? echo $width; ?>" cellpadding="1" cellspacing="0" border="1" rules="all" class="rpt_table" align="left" id="table_foot">
                <tfoot>
                    <tr>
                        <th width="30">&nbsp;</th>
                        <th width="60">&nbsp;</th>
                        <th width="60">&nbsp;</th>
                        <th width="60">Total</th>
                        <?
						$rac_tot=0;
						foreach($expcompany as $com)
						{
							?>
							<th width="120" align="right"><?php echo number_format($com_grand_tot[$com],2); ?></th>
							<?php
						}
						?>
                        <th width="120" align="right"><?php echo number_format($rac_grand_tot,2); ?></th>
                        <th align="right"></th>
                    </tr>
                </tfoot>
            </table>
        </fieldset>
        <?
    }
	
	/*
	|--------------------------------------------------------------------------
	| Order Wise
	|--------------------------------------------------------------------------
	|
	*/
	else if ($reportType == 2)
	{
		$con = connect();
		execute_query("DELETE FROM TMP_PO_ID WHERE USER_ID = ".$user_id."");
		oci_commit($con);
		
		$con = connect();
		foreach($poArray as $poId)
		{
			$dataPoArr[]= "(".$poId.",".$user_id.")";
		}
		$con = connect();
		$rID = sql_insert_zs("TMP_PO_ID", 'PO_ID,USER_ID', $dataPoArr, 1, 0);
		oci_commit($con);
		//disconnect($con);

		//for booking information
		$sqlBooking = "
			SELECT
				b.job_no, b.booking_no, b.booking_type, b.is_short, b.po_break_down_id,
				c.po_number,
				d.style_ref_no, d.buyer_name
			FROM
				wo_booking_dtls b
				INNER JOIN wo_po_break_down c ON b.po_break_down_id = c.id
				INNER JOIN wo_po_details_master d ON c.job_no_mst = d.job_no
			WHERE
				b.status_active = 1
				AND b.is_deleted = 0
				AND b.booking_type IN(1,4)
				AND c.status_active = 1
				AND c.is_deleted = 0
				AND d.status_active = 1
				AND d.is_deleted = 0
				AND c.id IN(SELECT PO_ID FROM TMP_PO_ID WHERE USER_ID = ".$user_id.")
			GROUP BY
				b.job_no, b.booking_no, b.booking_type, b.is_short, b.po_break_down_id,
				c.po_number,
				d.style_ref_no, d.buyer_name
		";
		//echo $sqlMain; die;
		$sqlBookingRslt = sql_select($sqlBooking);
		$bookingInfoArr = array();
		foreach($sqlBookingRslt as $row)
		{
			$orderId = $row[csf('po_break_down_id')];
			$bookingInfoArr[$orderId]['job_no'] = $row[csf('job_no')];
			$bookingInfoArr[$orderId]['buyer_id'] = $row[csf('buyer_name')];
			$bookingInfoArr[$orderId]['po_number'] = $row[csf('po_number')];
			$bookingInfoArr[$orderId]['style_ref_no'] = $row[csf('style_ref_no')];
			
			$bookingInfoArr[$orderId]['booking_type'][] = $row[csf('booking_type')];
			$bookingInfoArr[$orderId]['is_short'][] = $row[csf('is_short')];
			$bookingInfoArr[$orderId]['booking_no'][] = $row[csf('booking_no')];
		}
		unset($sqlBookingRslt);
		//echo "<pre>";
		//print_r($bookingInfoArr);
				
		$width = 2860;
		?>
		<fieldset style="width:<?php echo $width+20; ?>px;margin:5px auto;">
			<table width="<?php echo $width; ?>" cellpadding="1" cellspacing="0" border="1" rules="all" class="rpt_table" align="left">
				<thead>
                    <tr class="form_caption" style="border:none;">
                    	<th align="center" colspan="37" style="font-size:16px"><strong>Rack Wise Grey Fabrics Stock Report</strong></th>
                    </tr>
                    <tr>
						<th width="30">SL</th>
						<th width="60">Company</th>
						<th width="60">Floor</th>
						<th width="60">Room</th>
						<th width="60">Rack</th>
						<th width="60">Shelf</th>
						<th width="60">Bin</th>
						<th width="70">Job No.</th>
						<th width="100">Buyer</th>
						<th width="70">Order No.</th>
						<th width="100">Style Ref</th>
						<th width="70">Booking Type</th>
						<th width="100">Booking No.</th>
						<th width="110">Constraction</th>
						<th width="120">Composition</th>
						<th width="50">GSM</th>
						<th width="50">F/Dia</th>
						<th width="50">M/Dia</th>
						<th width="60">Stich Length</th>
						<th width="100">Dyeing Color</th>
						<th width="100">Color Range</th>
						<th width="60">Y. Count</th>
						<th width="60">Y. Brand</th>
						<th width="100">Y. Lot</th>
						<th width="100">Program No</th>
						<th width="80">Recv. Qty.</th>
						<th width="80">Issue Ret. Qty.</th>
						<th width="80">Transf. In Qty.</th>
						<th width="80">Total Recv.</th>
						<th width="80">No of Roll Recv.</th>
						<th width="80">Issue Qty.</th>
						<th width="80">Recv. Ret. Qty.</th>
						<th width="80">Transf. Out Qty.</th>
						<th width="80">Total Issue</th>
						<th width="80">No of Roll Issued</th>
						<th width="100">Stock Qty.</th>
						<th>No Of Roll Bal.</th>
					</tr>
				</thead>
			</table>
			<div style="width:<?php echo $width+20; ?>px; overflow-y: scroll; max-height:380px;" id="scroll_body">
				<table width="<?php echo $width; ?>" cellpadding="1" cellspacing="0" border="1" rules="all" class="rpt_table" align="left" id="table_body">
					<tbody>
					<?php
					$sl = 0;
					$grandTotal = array();
					foreach($dataArr as $compId=>$compArr)
					{
						foreach($compArr as $productId=>$productArr)
						{
							foreach($productArr as $orderId=>$orderArr)
							{
								foreach($orderArr as $storeId=>$storeArr)
								{
									foreach($storeArr as $floorId=>$floorArr)
									{
										foreach($floorArr as $roomId=>$roomArr)
										{
											foreach($roomArr as $rackId=>$rackArr)
											{
												foreach($rackArr as $selfId=>$selfArr)
												{
													foreach($selfArr as $binId=>$row)
													{
														//total receive calculation
														$row['totalRcvQty'] = number_format($row['rcvQty'],2,'.','')+number_format($row['issueReturnQty'],2,'.','')+number_format($row['transferInQty'],2,'.','');
														
														//total issue calculation
														$row['issueQty'] = $issueQtyArr[$compId][$productId][$orderId][$storeId][$floorId][$roomId][$rackId][$selfId][$binId]['issueQty'];
														$row['rcvReturnQty'] = 0;
														$row['transferOutQty'] = $transOutArr[$compId][$productId][$orderId][$storeId][$floorId][$roomId][$rackId][$selfId][$binId]['transferOutQty'];
														$row['totalIssueQty'] = number_format($row['issueQty'],2,'.','')+number_format($row['rcvReturnQty'],2,'.','')+number_format($row['transferOutQty'],2,'.','');
														
														//stock qty calculation
														$row['stockQty'] = number_format($row['totalRcvQty'],2,'.','') - number_format($row['totalIssueQty'],2,'.','');

														//roll balance calculation
														$row['rollIssueQty'] = $transOutArr[$compId][$productId][$orderId][$storeId][$floorId][$roomId][$rackId][$selfId][$binId]['rollIssueQty']+$noOfRollIssueArr[$compId][$productId][$orderId][$storeId][$floorId][$roomId][$rackId][$selfId][$binId]['rollIssueQty'];
														$row['rollBalanceQty'] = $row['rollRcvQty'] - $row['rollIssueQty'];

														if($valueType != 1 && $row['stockQty'] == 0)
														{
															continue;
														}
														
														if($row['stockQty'] >= 0)
														{
															$sl++;
															/*if($sl == 10000)
															{
																break;
															}*/
															//echo $yarnCount;
															//print_r($yarnCountArr);
															$row['job_no'] = $bookingInfoArr[$orderId]['job_no'];
															$row['buyer_id'] = $bookingInfoArr[$orderId]['buyer_id'];
															$row['po_number'] = $bookingInfoArr[$orderId]['po_number'];
															$row['style_ref_no'] = $bookingInfoArr[$orderId]['style_ref_no'];
															$row['booking_type'] = $bookingInfoArr[$orderId]['booking_type'];
															$row['is_short'] = $bookingInfoArr[$orderId]['is_short'];
															$row['booking_no'] = $bookingInfoArr[$orderId]['booking_no'];
															
															$row['construction'] = $yarnInfoArr[$productId]['construction'];
															$row['composition'] = $yarnInfoArr[$productId]['composition'];
															$row['gsm'] = $yarnInfoArr[$productId]['gsm'];
															$row['width'] = $yarnInfoArr[$productId]['width'];
															$row['machine_dia'] = $yarnInfoArr[$productId]['machine_dia'];
															$row['stitch_length'] = $yarnInfoArr[$productId]['stitch_length'];
															$row['program_no'] = $yarnInfoArr[$productId]['program_no'];
															// echo $row['program_no'].'<br>';
															$row['color_id'] = $yarnInfoArr[$productId]['color_id'];
															$row['color_range_id'] = $yarnInfoArr[$productId]['color_range_id'];
															$row['yarn_count'] = $yarnInfoArr[$productId]['yarn_count'];
															
															$yarnCountArr=explode(',', $row['yarn_count']);
															$yarnCount="";
															foreach ($yarnCountArr as $key => $yCount) 
															{
																if ($yarnCount=="") 
																{
																	$yarnCount.=$count_arr[$yCount];
																}
																else
																{
																	$yarnCount.=', '.$count_arr[$yCount];
																}
															}
															$row['brand_id'] = $yarnInfoArr[$productId]['brand_id'];
															$row['yarn_lot'] = $yarnInfoArr[$productId]['yarn_lot'];
															?>
                                                            <tr valign="middle">
                                                                <td width="30" align="center"><?php echo $sl; ?></td>
                                                                <td width="60"><div style="word-break:break-all"><?php echo $company_arr[$compId]; ?></div></td>
                                                                <td width="60"><div style="word-break:break-all"><?php echo $floorDetails[$floorId]; ?></div></td>
                                                                <td width="60"><div style="word-break:break-all"><?php echo $roomDetails[$roomId]; ?></div></td>
                                                                <td width="60"><div style="word-break:break-all"><?php echo $rackDetails[$rackId]; ?></div></td>
                                                                <td width="60"><div style="word-break:break-all"><?php echo $selfDetails[$selfId]; ?></div></td>
                                                                <td width="60"><div style="word-break:break-all"><?php echo $binDetails[$binId]; ?></div></td>
                                                                <td width="70"><div style="word-break:break-all"><?php echo $row['job_no']; ?></div></td>
                                                                <td width="100"><div style="word-break:break-all"><?php echo $buyer_array[$row['buyer_id']]; ?></div></td>
                                                                <td width="70"><div style="word-break:break-all"><?php echo $row['po_number']; ?></div></td>
                                                                <td width="100"><div style="word-break:break-all"><?php echo $row['style_ref_no']; ?></div></td>
                                                                <td width="70"><div style="word-break:break-all">
                                                                <?php 
                                                                $bData = array();
                                                                $bData['booking_type'] = $row['booking_type'];
                                                                $bData['is_short'] = $row['is_short'];
                                                                echo implode(', ', getBookingType($bData));
                                                                ?>
                                                                </div></td>
                                                                <td width="100"><div style="word-break:break-all"><?php echo implode(', ', $row['booking_no']); ?></div></td>
                                                                <td width="110"><div style="word-break:break-all"><?php echo $row['construction']; ?></div></td>
                                                                <td width="120"><div style="word-break:break-all"><?php echo $row['composition']; ?></div></td>
                                                                <td width="50" title="prodId: <? echo $prodId.'='.$productId; ?>"><?php echo $row['gsm']; ?></td>
                                                                <td width="50"><?php echo $row['width']; ?></td>
                                                                <td width="50"><?php echo $row['machine_dia']; ?></td>
                                                                <td width="60"><?php echo $row['stitch_length']; ?></td>
                                                                <td width="100"><div style="word-break:break-all"><?php echo $row['color_id']; ?></div></td>
                                                                <td width="100"><div style="word-break:break-all"><?php echo $row['color_range_id']; ?></div></td>
                                                                <td width="60"><div style="word-break:break-all"><?php echo $yarnCount;//$count_arr[$row['yarn_count']]; ?></div></td>
                                                                <td width="60"><div style="word-break:break-all"><?php echo $brand_arr[$row['brand_id']]; ?></div></td>
                                                                <td width="100"><div style="word-break:break-all"><?php echo $row['yarn_lot']; ?></div></td>
                                                                <td width="100"><div style="word-break:break-all"><?php echo $row['program_no']; ?></div></td>
                                                                <td width="80" align="right"><?php echo number_format($row['rcvQty'],2); ?></td>
                                                                <td width="80" align="right"><?php echo number_format($row['issueReturnQty'],2); ?></td>
                                                                <td width="80" align="right"><?php echo number_format($row['transferInQty'],2); ?></td>
                                                                <td width="80" align="right"><?php echo number_format($row['totalRcvQty'],2); ?></td>
                                                                <td width="80" align="right"><?php echo number_format($row['rollRcvQty'],2); ?></td>
                                                                <td width="80" align="right"><?php echo number_format($row['issueQty'],2); ?></td>
                                                                <td width="80" align="right"><?php echo number_format($row['rcvReturnQty'],2); ?></td>
                                                                <td width="80" align="right"><?php echo number_format($row['transferOutQty'],2); ?></td>
                                                                <td width="80" align="right"><?php echo number_format($row['totalIssueQty'],2); ?></td>
                                                                <td width="80" align="right"><?php echo number_format($row['rollIssueQty'],2); ?></td>
                                                                <td width="100" align="right"><?php echo number_format($row['stockQty'],2); ?></td>
                                                                <td align="right"><?php echo number_format($row['rollBalanceQty'],2); ?></td>
                                                            </tr>
															<?php
															//$grandTotal
															$grandTotal['rcvQty'] += number_format($row['rcvQty'],2,'.','');
															$grandTotal['issueReturnQty'] += number_format($row['issueReturnQty'],2,'.','');
															$grandTotal['transferInQty'] += number_format($row['transferInQty'],2,'.','');
															$grandTotal['totalRcvQty'] += number_format($row['totalRcvQty'],2,'.','');
															$grandTotal['totalRollRcvQty'] += number_format($row['rollRcvQty'],2,'.','');
													
															$grandTotal['issueQty'] += number_format($row['issueQty'],2,'.','');
															$grandTotal['rcvReturnQty'] += number_format($row['rcvReturnQty'],2,'.','');
															$grandTotal['transferOutQty'] += number_format($row['transferOutQty'],2,'.','');
															$grandTotal['totalIssueQty'] += number_format($row['totalIssueQty'],2,'.','');
															$grandTotal['totalRollIssueQty'] += number_format($row['rollIssueQty'],2,'.','');
															$grandTotal['totalStockQty'] += number_format($row['stockQty'],2,'.','');
															$grandTotal['totalRollBalanceQty'] += number_format($row['rollBalanceQty'],2,'.','');
														}
													}
												}
											}
										}
									}
								}
							}
						}
					}
                    ?>
                    </tbody>
                </table>
            </div>
            <table width="<?php echo $width; ?>" cellpadding="1" cellspacing="0" border="1" rules="all" class="rpt_table" align="left" id="table_foot">
                <tfoot>
                    <tr>
                        <th width="30">&nbsp;</th>
                        <th width="60">&nbsp;</th>
                        <th width="60">&nbsp;</th>
                        <th width="60">&nbsp;</th>
                        <th width="60">&nbsp;</th>
                        <th width="60">&nbsp;</th>
                        <th width="60">&nbsp;</th>
                        <th width="70">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="70">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="70">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="110">&nbsp;</th>
                        <th width="120">&nbsp;</th>
                        <th width="50">&nbsp;</th>
                        <th width="50">&nbsp;</th>
                        <th width="50">&nbsp;</th>
                        <th width="60">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="60">&nbsp;</th>
                        <th width="60">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="100" align="right">Total</th>
                        <th width="80" align="right" id="value_rcvQty"><?php echo number_format($grandTotal['rcvQty'],2); ?></th>
                        <th width="80" align="right" id="value_issueReturnQty"><?php echo number_format($grandTotal['issueReturnQty'],2); ?></th>
                        <th width="80" align="right" id="value_transferInQty"><?php echo number_format($grandTotal['transferInQty'],2); ?></th>
                        <th width="80" align="right" id="value_totalRcvQty"><?php echo number_format($grandTotal['totalRcvQty'],2); ?></th>
                        <th width="80" align="right" id="value_totalRollRcvQty"><?php echo number_format($grandTotal['totalRollRcvQty'],2); ?></th>
                        <th width="80" align="right" id="value_issueQty"><?php echo number_format($grandTotal['issueQty'],2); ?></th>
                        <th width="80" align="right" id="value_rcvReturnQty"><?php echo number_format($grandTotal['rcvReturnQty'],2); ?></th>
                        <th width="80" align="right" id="value_transferOutQty"><?php echo number_format($grandTotal['transferOutQty'],2); ?></th>
                        <th width="80" align="right" id="value_totalIssueQty"><?php echo number_format($grandTotal['totalIssueQty'],2); ?></th>
                        <th width="80" align="right" id="value_totalRollIssueQty"><?php echo number_format($grandTotal['totalRollIssueQty'],2); ?></th>
                        <th width="100" align="right" id="value_totalStockQty"><?php echo number_format($grandTotal['totalStockQty'],2); ?></th>
                        <th align="right" id="value_totalRollBalanceQty"><?php echo number_format($grandTotal['totalRollBalanceQty'],2); ?></th>
                    </tr>
                </tfoot>
            </table>
        </fieldset>
        <?php
    }
	
	/*
	|--------------------------------------------------------------------------
	| Rack Wise
	|--------------------------------------------------------------------------
	|
	*/
	else if ($reportType == 3)
	{
		$width = 1500;
		?>
		<fieldset style="width:<? echo $width+20; ?>px;margin:5px auto;">
			<table width="<? echo $width; ?>" cellpadding="1" cellspacing="0" border="1" rules="all" class="rpt_table" align="left">
				<thead>
                    <tr class="form_caption" style="border:none;">
                    	<th align="center" colspan="21" style="font-size:16px"><strong>Rack Wise Grey Fabrics Stock Report</strong></th>
                    </tr>
                    <tr>
						<th width="30">SL</th>
						<th width="60">Company</th>
						<th width="60">Floor</th>
						<th width="60">Room</th>
						<th width="60">Rack</th>
						<th width="60">Shelf</th>
						<th width="60">Bin</th>
						<th width="50">Product ID</th>
						<th width="110">Constraction</th>
						<th width="120">Composition</th>
						<th width="50">GSM</th>
						<th width="50">F/Dia</th>
						<th width="50">M/Dia</th>
						<th width="60">Stich Length</th>
						<th width="100">Dyeing Color</th>
						<th width="100">Color Range</th>
						<th width="60">Y. Count</th>
						<th width="60">Y. Brand</th>
						<th width="100">Y. Lot</th>
						<th width="100">Stock Qty.</th>
						<th>No Of Roll Bal.</th>
					</tr>
				</thead>
			</table>
			<div style="width:<? echo $width+20; ?>px; overflow-y: scroll; max-height:380px;" id="scroll_body">
				<table width="<? echo $width; ?>" cellpadding="1" cellspacing="0" border="1" rules="all" class="rpt_table" align="left" id="table_body">
					<tbody>
					<?php
					$sl = 0;
					$grandTotal = array();
					foreach($dataArr as $compId=>$compArr)
					{
						foreach($compArr as $productId=>$productArr)
						{
							foreach($productArr as $orderId=>$orderArr)
							{
								foreach($orderArr as $storeId=>$storeArr)
								{
									foreach($storeArr as $floorId=>$floorArr)
									{
										foreach($floorArr as $roomId=>$roomArr)
										{
											foreach($roomArr as $rackId=>$rackArr)
											{
												$stockQty = 0;
												$rollBalanceQty = 0;
												foreach($rackArr as $selfId=>$selfArr)
												{
													foreach($selfArr as $binId=>$row)
													{
														//roll balance calculation
														$rollIssueQty = $transOutArr[$compId][$productId][$orderId][$storeId][$floorId][$roomId][$rackId][$selfId][$binId]['rollIssueQty']+$noOfRollIssueArr[$compId][$productId][$orderId][$storeId][$floorId][$roomId][$rackId][$selfId][$binId]['rollIssueQty'];
														$rollBlncQty = $row['rollRcvQty'] - $rollIssueQty;
														
														
														//total receive calculation
														$totalRcvQty = number_format($row['rcvQty'],2,'.','')+number_format($row['issueReturnQty'],2,'.','')+number_format($row['transferInQty'],2,'.','');
														//total issue calculation
														$issueQty = $issueQtyArr[$compId][$productId][$orderId][$storeId][$floorId][$roomId][$rackId][$selfId][$binId]['issueQty'];
														$rcvReturnQty = 0;
														$transferOutQty = $transOutArr[$compId][$productId][$orderId][$storeId][$floorId][$roomId][$rackId][$selfId][$binId]['transferOutQty'];
														$totalIssueQty = number_format($issueQty,2,'.','')+number_format($rcvReturnQty,2,'.','')+number_format($transferOutQty,2,'.','');
														//$isqty+=$issueQty;
														//$tQty+=$transferOutQty;
														
														//stock calculation
														$stkQty = $totalRcvQty - $totalIssueQty;
														if($stkQty > 0)
														{
															$stockQty += $stkQty;
															$rollBalanceQty += $rollBlncQty;
														}

													}
												}
												
												if($valueType != 1 && $stockQty == 0)
												{
													continue;
												}
												if($stockQty >= 0)
												{
													$sl++;
													
													$row['construction'] = $yarnInfoArr[$productId]['construction'];
													$row['composition'] = $yarnInfoArr[$productId]['composition'];
													$row['gsm'] = $yarnInfoArr[$productId]['gsm'];
													$row['width'] = $yarnInfoArr[$productId]['width'];
													$row['machine_dia'] = $yarnInfoArr[$productId]['machine_dia'];
													$row['stitch_length'] = $yarnInfoArr[$productId]['stitch_length'];
													$row['color_id'] = $yarnInfoArr[$productId]['color_id'];
													$row['color_range_id'] = $yarnInfoArr[$productId]['color_range_id'];
													$row['yarn_count'] = $yarnInfoArr[$productId]['yarn_count'];
													
													$yarnCountArr=explode(',', $row['yarn_count']);
													$yarnCount="";
													foreach ($yarnCountArr as $key => $yCount) 
													{
														if ($yarnCount=="") 
														{
															$yarnCount.=$count_arr[$yCount];
														}
														else
														{
															$yarnCount.=', '.$count_arr[$yCount];
														}
													}
													$row['brand_id'] = $yarnInfoArr[$productId]['brand_id'];
													$row['yarn_lot'] = $yarnInfoArr[$productId]['yarn_lot'];
													
													?>
													<tr>
														<td width="30" align="center"><?php echo $sl; ?></td>
														<td width="60"><div style="word-break:break-all"><?php echo $company_arr[$compId]; ?></div></td>
														<td width="60"><div style="word-break:break-all"><?php echo $floorDetails[$floorId]; ?></div></td>
														<td width="60"><div style="word-break:break-all"><?php echo $roomDetails[$roomId]; ?></div></td>
														<td width="60"><div style="word-break:break-all"><?php echo $rackDetails[$rackId]; ?></div></td>
														<td width="60"><div style="word-break:break-all"><?php echo $selfDetails[$selfId]; ?></div></td>
														<td width="60"><div style="word-break:break-all"><?php echo $binDetails[$binId]; ?></div></td>
														<td width="50"><div style="word-break:break-all"><?php echo $productId; ?></div></td>
														<td width="110"><div style="word-break:break-all"><?php echo $row['construction']; ?></div></td>
														<td width="120"><div style="word-break:break-all"><?php echo $row['composition']; ?></div></td>
														<td width="50" title="prodId: <? echo $prodId.'='.$productId; ?>"><?php echo $row['gsm']; ?></td>
														<td width="50"><?php echo $row['width']; ?></td>
														<td width="50"><?php echo $row['machine_dia']; ?></td>
														<td width="60"><?php echo $row['stitch_length']; ?></td>
														<td width="100"><div style="word-break:break-all"><?php echo $row['color_id']; ?></div></td>
														<td width="100"><div style="word-break:break-all"><?php echo $row['color_range_id']; ?></div></td>
														<td width="60"><div style="word-break:break-all"><?php echo $yarnCount;//$count_arr[$row['yarn_count']]; ?></div></td>
														<td width="60"><div style="word-break:break-all"><?php echo $brand_arr[$row['brand_id']]; ?></div></td>
														<td width="100"><div style="word-break:break-all"><?php echo $row['yarn_lot']; ?></div></td>
														

														<td width="100" title="<?php echo $productId."=".$orderId."=".$rackId; ?>" class="word_break" align="right"><a href="##" onclick="openmypage_stock('<? echo $compId;?>','<? echo $orderId;?>','<? echo $productId;?>','<? echo $storeId;?>','<? echo $floorId;?>','<? echo $roomId;?>','<? echo $rackId;?>');"><?php echo number_format($stockQty,2); ?></a></td>

														<td align="right"><?php echo number_format($rollBalanceQty,2); ?></td>
													</tr>
													<?php
													//$grandTotal
													$grandTotal['totalStockQty'] += $stockQty;
													$grandTotal['totalRollBalanceQty'] += $rollBalanceQty;
												}
											}
										}
									}
								}
							}
						}
                    }
                    ?>
                    </tbody>
				</table> 
            </div>
            <table width="<? echo $width; ?>" cellpadding="1" cellspacing="0" border="1" rules="all" class="rpt_table" align="left" id="table_body">
                <tfoot>
                    <tr>
                        <th width="30">&nbsp;</th>
                        <th width="60">&nbsp;</th>
                        <th width="60">&nbsp;</th>
                        <th width="60">&nbsp;</th>
                        <th width="60">&nbsp;</th>
                        <th width="60">&nbsp;</th>
                        <th width="60">&nbsp;</th>
                        <th width="50">&nbsp;</th>
                        <th width="110">&nbsp;</th>
                        <th width="120">&nbsp;</th>
                        <th width="50">&nbsp;</th>
                        <th width="50">&nbsp;</th>
                        <th width="50">&nbsp;</th>
                        <th width="60">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="60">&nbsp;</th>
                        <th width="60">&nbsp;</th>
                        <th width="100">Total</th>
                        <th width="100" align="right" id="value_totalStockQty"><?php echo number_format($grandTotal['totalStockQty'],2); ?></th>
                        <th align="right" id="value_totalRollBalanceQty"><?php echo number_format($grandTotal['totalRollBalanceQty'],2); ?></th>
                    </tr>
                </tfoot>
            </table>
        </fieldset>
        <?
    }

    /*
	|--------------------------------------------------------------------------
	| Shelf Wise
	|--------------------------------------------------------------------------
	|
	*/
	else if ($reportType == 5)
	{
		$width = 1500;
		?>
		<fieldset style="width:<? echo $width+20; ?>px;margin:5px auto;">
			<table width="<? echo $width; ?>" cellpadding="1" cellspacing="0" border="1" rules="all" class="rpt_table" align="left">
				<thead>
                    <tr class="form_caption" style="border:none;">
                    	<th align="center" colspan="20" style="font-size:16px"><strong>Rack Wise Grey Fabrics Stock Report</strong></th>
                    </tr>
                    <tr>
						<th width="30">SL</th>
						<th width="60">Company</th>
						<th width="60">Floor</th>
						<th width="60">Room</th>
						<th width="60">Rack</th>
						<th width="60">Shelf</th>
						<th width="50">Product ID</th>
						<th width="110">Constraction</th>
						<th width="120">Composition</th>
						<th width="50">GSM</th>
						<th width="50">F/Dia</th>
						<th width="50">M/Dia</th>
						<th width="60">Stich Length</th>
						<th width="100">Dyeing Color</th>
						<th width="100">Color Range</th>
						<th width="60">Y. Count</th>
						<th width="60">Y. Brand</th>
						<th width="100">Y. Lot</th>
						<th width="100">Stock Qty.</th>
						<th>No Of Roll Bal.</th>
					</tr>
				</thead>
			</table>
			<div style="width:<? echo $width+20; ?>px; overflow-y: scroll; max-height:380px;" id="scroll_body">
				<table width="<? echo $width; ?>" cellpadding="1" cellspacing="0" border="1" rules="all" class="rpt_table" align="left" id="table_body">
					<tbody>
					<?php
					$sl = 0;
					$grandTotal = array();
					foreach($dataArr as $compId=>$compArr)
					{
						foreach($compArr as $productId=>$productArr)
						{
							foreach($productArr as $orderId=>$orderArr)
							{
								foreach($orderArr as $storeId=>$storeArr)
								{
									foreach($storeArr as $floorId=>$floorArr)
									{
										foreach($floorArr as $roomId=>$roomArr)
										{
											foreach($roomArr as $rackId=>$rackArr)
											{
												foreach($rackArr as $selfId=>$row)
												{
													//roll balance calculation
													$rollIssueQty = $transOutArr[$compId][$productId][$orderId][$storeId][$floorId][$roomId][$rackId][$selfId]['rollIssueQty']+$noOfRollIssueArr[$compId][$productId][$orderId][$storeId][$floorId][$roomId][$rackId][$selfId]['rollIssueQty'];
													$rollBalanceQty = $row['rollRcvQty'] - $rollIssueQty;
													
													
													//total receive calculation
													$totalRcvQty = number_format($row['rcvQty'],2,'.','')+number_format($row['issueReturnQty'],2,'.','')+number_format($row['transferInQty'],2,'.','');
													//total issue calculation
													$issueQty = $issueQtyArr[$compId][$productId][$orderId][$storeId][$floorId][$roomId][$rackId][$selfId]['issueQty'];
													$rcvReturnQty = 0;
													$transferOutQty = $transOutArr[$compId][$productId][$orderId][$storeId][$floorId][$roomId][$rackId][$selfId]['transferOutQty'];
													$totalIssueQty = number_format($issueQty,2,'.','')+number_format($rcvReturnQty,2,'.','')+number_format($transferOutQty,2,'.','');
													//$isqty+=$issueQty;
													//$tQty+=$transferOutQty;
													
													//stock calculation
													$stockQty = $totalRcvQty - $totalIssueQty;
													$stockQty=number_format($stockQty,2,'.','');
												
													if($valueType != 1 && $stockQty == 0)
													{
														continue;
													}
													if($stockQty >= 0)
													{
														$sl++;
														
														$row['construction'] = $yarnInfoArr[$productId]['construction'];
														$row['composition'] = $yarnInfoArr[$productId]['composition'];
														$row['gsm'] = $yarnInfoArr[$productId]['gsm'];
														$row['width'] = $yarnInfoArr[$productId]['width'];
														$row['machine_dia'] = $yarnInfoArr[$productId]['machine_dia'];
														$row['stitch_length'] = $yarnInfoArr[$productId]['stitch_length'];
														$row['color_id'] = $yarnInfoArr[$productId]['color_id'];
														$row['color_range_id'] = $yarnInfoArr[$productId]['color_range_id'];
														$row['yarn_count'] = $yarnInfoArr[$productId]['yarn_count'];
														
														$yarnCountArr=explode(',', $row['yarn_count']);
														$yarnCount="";
														foreach ($yarnCountArr as $key => $yCount) 
														{
															if ($yarnCount=="") 
															{
																$yarnCount.=$count_arr[$yCount];
															}
															else
															{
																$yarnCount.=', '.$count_arr[$yCount];
															}
														}
														$row['brand_id'] = $yarnInfoArr[$productId]['brand_id'];
														$row['yarn_lot'] = $yarnInfoArr[$productId]['yarn_lot'];
														
														?>
														<tr>
															<td width="30" align="center"><?php echo $sl; ?></td>
															<td width="60"><p><div style="word-break:break-all"><?php echo $company_arr[$compId]; ?></div></p></td>
															<td width="60"><div style="word-break:break-all"><?php echo $floorDetails[$floorId]; ?></div></td>
															<td width="60"><div style="word-break:break-all"><?php echo $roomDetails[$roomId]; ?></div></td>
															<td width="60"><div style="word-break:break-all"><?php echo $rackDetails[$rackId]; ?></div></td>
															<td width="60"><div style="word-break:break-all"><?php echo $selfDetails[$selfId]; ?></div></td>
															
															<td width="50"><div style="word-break:break-all"><?php echo $productId; ?></div></td>
															<td width="110"><div style="word-break:break-all"><?php echo $row['construction']; ?></div></td>
															<td width="120"><div style="word-break:break-all"><?php echo $row['composition']; ?></div></td>
															<td width="50" title="prodId: <? echo $prodId.'='.$productId; ?>"><?php echo $row['gsm']; ?></td>
															<td width="50"><?php echo $row['width']; ?></td>
															<td width="50"><?php echo $row['machine_dia']; ?></td>
															<td width="60"><?php echo $row['stitch_length']; ?></td>
															<td width="100"><div style="word-break:break-all"><?php echo $row['color_id']; ?></div></td>
															<td width="100"><div style="word-break:break-all"><?php echo $row['color_range_id']; ?></div></td>
															<td width="60"><div style="word-break:break-all"><?php echo $yarnCount;//$count_arr[$row['yarn_count']]; ?></div></td>
															<td width="60"><div style="word-break:break-all"><?php echo $brand_arr[$row['brand_id']]; ?></div></td>
															<td width="100"><div style="word-break:break-all"><?php echo $row['yarn_lot']; ?></div></td>
															

															<td width="100" title="<?php echo $productId."=".$orderId."=".$rackId; ?>" class="word_break" align="right"><a href="##" onclick="openmypage_stock('<? echo $compId;?>','<? echo $orderId;?>','<? echo $productId;?>','<? echo $storeId;?>','<? echo $floorId;?>','<? echo $roomId;?>','<? echo $rackId;?>','<? echo $selfId;?>');"><?php echo number_format($stockQty,2); ?></a></td>

															<td align="right"><?php echo number_format($rollBalanceQty,2); ?></td>
														</tr>
														<?php
														//$grandTotal
														$grandTotal['totalStockQty'] += $stockQty;
														$grandTotal['totalRollBalanceQty'] += $rollBalanceQty;
													}
												}
											}
										}
									}
								}
							}
						}
                    }
                    ?>
                    </tbody>
				</table> 
            </div>
            <table width="<? echo $width; ?>" cellpadding="1" cellspacing="0" border="1" rules="all" class="rpt_table" align="left" id="table_body">
                <tfoot>
                    <tr>
                        <th width="30">&nbsp;</th>
                        <th width="60">&nbsp;</th>
                        <th width="60">&nbsp;</th>
                        <th width="60">&nbsp;</th>
                        <th width="60">&nbsp;</th>
                        <th width="60">&nbsp;</th>
                        <th width="50">&nbsp;</th>
                        <th width="110">&nbsp;</th>
                        <th width="120">&nbsp;</th>
                        <th width="50">&nbsp;</th>
                        <th width="50">&nbsp;</th>
                        <th width="50">&nbsp;</th>
                        <th width="60">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="60">&nbsp;</th>
                        <th width="60">&nbsp;</th>
                        <th width="100">Total</th>
                        <th width="100" align="right" id="value_totalStockQty"><?php echo number_format($grandTotal['totalStockQty'],2); ?></th>
                        <th align="right" id="value_totalRollBalanceQty"><?php echo number_format($grandTotal['totalRollBalanceQty'],2); ?></th>
                    </tr>
                </tfoot>
            </table>
        </fieldset>
        <?
    }
	
	/*
	|--------------------------------------------------------------------------
	| Date Wise
	|--------------------------------------------------------------------------
	|
	*/
	else if ($reportType == 4)
	{
		$width = 700;
		?>
		<fieldset style="width:<? echo $width+20; ?>px;margin:5px auto;">
			<table width="<? echo $width; ?>" cellpadding="1" cellspacing="0" border="1" rules="all" class="rpt_table" align="left">
				<thead>
					<tr>
						<th width="30">SL</th>
						<th width="110">Company</th>
						<th width="110">Floor</th>
						<th width="100">Room</th>
						<th width="120">Rack</th>
						<th width="150">Shelf</th>
						<th width="80">Bin</th>
                        <!--
						<th width="110">Job No.</th>
						<th width="100">Buyer</th>
						<th width="110">Order No.</th>
						<th width="140">Style Ref</th>
						<th width="100">Booking Type</th>
						<th width="110">Booking No.</th>
						<th width="110">Constraction</th>
						<th width="120">Composition</th>
						<th width="80">GSM</th>
						<th width="80">F/Dia</th>
						<th width="80">M/Dia</th>
						<th width="100">Stich Length</th>
						<th width="100">Dyeing Color</th>
						<th width="100">Color Range</th>
						<th width="100">Y. Count</th>
						<th width="100">Y. Brand</th>
						<th width="100">Y. Lot</th>
						<th width="80">Recv. Qty.</th>
						<th width="80">Issue Ret. Qty.</th>
						<th width="80">Transf. In Qty.</th>
						<th width="80">Total Recv.</th>
						<th width="80">No of Roll Recv.</th>
						<th width="80">Issue Qty.</th>
						<th width="80">Recv. Ret. Qty.</th>
						<th width="80">Transf. Out Qty.</th>
						<th width="80">Total Issue</th>
						<th width="80">No of Roll Issued</th>
						<th width="100">Stock Qty.</th>
						<th>No Of Roll Bal.</th>
                        -->
					</tr>
				</thead>
			</table>
			<div style="width:<? echo $width+20; ?>px; overflow-y: scroll; max-height:380px;">
				<table width="<? echo $width; ?>" cellpadding="1" cellspacing="0" border="1" rules="all" class="rpt_table" align="left" id="table_body">
					<h1 style="color:red;">under construction</h1>
                </table>
            </div>
        </fieldset>
        <?
    }

	execute_query("DELETE FROM TMP_PO_ID WHERE USER_ID = ".$user_id."");
	oci_commit($con);
	
	$html = ob_get_contents();
	ob_clean();
	foreach (glob("*.xls") as $filename)
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
			@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc, $html);
	
	if($reportType != 1)
	{
		echo $html."####".$filename."####".$reportType;
	}
	else
	{
		/*
		ksort($rackIdArrChart);
		ksort($rackQtyArrChart);
		$rackIdArrChart = implode("','",$rackIdArrChart);
		$rackQtyArrChart = implode(',',$rackQtyArrChart);
		$html.='<br><div id="container" style="width:'.$width.'px;border:1px solid #CCC;"></div>';
		echo $html."####".$filename."####".$reportType."####".$rackQtyArrChart."####'".$rackIdArrChart."'";
		*/
		
		foreach($rackQtyArrChart as $company_id=>$valArr)
		{
			$companyTextArr[$rack]=$rack;
			$dataARr[$company_id]="{ name: '".$company_arr[$company_id]."', data:[".implode(',',$valArr)."], stack: 'none'}";
		}
		
		$rackIdArrChart = "'".implode("','",$rackIdArrChart)."'";
		$rackQtyArrChart= implode(',',$dataARr);
		$html.='<br><div id="container" style="width:'.($width+500).'px;border:1px solid #CCC;"></div>';
		echo $html."####".$filename."####".$reportType."####".$rackQtyArrChart."####".$rackIdArrChart."";
	}
	disconnect($con);
	die;
}

if($action=="report_generate_04022021")
{
	$started = microtime(true);
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	ob_start();
	$companyId = str_replace("'", "", $cbo_company_id);
	$buyerId = str_replace("'", "", $cbo_buyer_id);
	$year = str_replace("'", "", $cbo_year);
	$jobNo = str_replace("'", "", $txt_job_no);
	$jobId = str_replace("'", "", $txt_job_id);
	$bookingNo = str_replace("'", "", $txt_booking_no);
	$bookingId = str_replace("'", "", $txt_booking_id);
	$searchBy = str_replace("'", "", $cbo_search_by);
	$searchCommon = str_replace("'", "", $txt_search_comm);
	$product = str_replace("'", "", $txt_product);
	$productId = str_replace("'", "", $txt_product_id);
	$productNo = str_replace("'", "", $txt_product_no);
	$floorId = str_replace("'", "", $txt_floor_id);
	$roomId = str_replace("'", "", $txt_room_id);
	$rackId = str_replace("'", "", $txt_rack_id);
	$valueType = str_replace("'", "", $cbo_value_type);
	$fromDate = str_replace("'", "", $txt_date_from);
	$toDate = str_replace("'", "", $txt_date_to);
	$reportType = str_replace("'", "", $rpt_type);
	//$_SESSION["date_from"] = date('Y-m-d', strtotime($date_from));
	//$_SESSION["date_to"] = date('Y-m-d', strtotime($date_to));
	
	//buyerIdCondition
	$buyerIdCondition = '';
	if($buyerId != 0)
	{
		$buyerIdCondition = " AND a.buyer_id = ".$buyerId."";
	}
	
	//yearCondition;
	$yearCondition = '';
	if($db_type==0)
	{
		$yearCondition = " AND YEAR(d.insert_date) = ".$year."";
	}
	else if($db_type==2)
	{
		$yearCondition = " AND TO_CHAR(d.insert_date,'YYYY') = ".$year."";
	}
	
	//jobNoCondition
	$jobNoCondition = '';
	if($jobNo != '')
	{
		$jobNoCondition = " AND d.job_no_prefix_num IN(".$jobNo.")";
	}
	
	//bookingNoCondition
	$bookingNoCondition = '';
	$bookingPoArr = array();
	if($bookingNo != '')
	{
		$sqlBooking = "
			SELECT
				a.company_id, a.buyer_id,
				b.job_no, b.booking_no, b.booking_type, b.is_short, b.po_break_down_id
			FROM
				wo_booking_mst a
				INNER JOIN wo_booking_dtls b ON a.booking_no = b.booking_no
			WHERE
				a.status_active = 1
				AND a.is_deleted = 0
				AND a.company_id IN(".$companyId.")
				AND b.status_active = 1
				AND b.is_deleted = 0
				AND b.booking_type IN(1,4)
				AND a.booking_no_prefix_num IN(".$bookingNo.")
			GROUP BY
				a.company_id, a.buyer_id, 
				b.job_no, b.booking_no, b.booking_type, b.is_short, b.po_break_down_id
		";
		$sqlBookingRslt = sql_select($sqlBooking);
		foreach($sqlBookingRslt as $row)
		{
			$bookingPoArr[$row[csf('po_break_down_id')]] = $row[csf('po_break_down_id')];
		}
		
		//have to work
		if(!empty($bookingPoArr))
		{
			//$bookingNoCondition = " AND e.po_breakdown_id IN(".implode(',', $bookingPoArr).")";
			$bookingNoCondition = where_con_using_array($bookingPoArr, '0', 'e.po_breakdown_id');
		}
		//echo "<pre>";
		//print_r($bookingPoArr); die;
	}
	
	//searchByCondition
	$searchByCondition = '';
	if($searchCommon != '')
	{
		$expSearchCommon = explode(',', $searchCommon);
		$dataSearchCommon = '';
		foreach($expSearchCommon as $val)
		{
			if($dataSearchCommon != '')
			{
				$dataSearchCommon .= ',';
			}
			$dataSearchCommon .= "'".$val."'";
		}
		
		//style no
		if($searchBy == 1)
		{
			$searchByCondition = " AND d.style_ref_no IN(".$dataSearchCommon.")";
		}
		//order no
		else if($searchBy == 2)
		{
			$searchByCondition = " AND c.po_number IN(".$dataSearchCommon.")";
		}
	}
	
	//productIdCondition
	$productIdCondition = '';
	if($productId != '')
	{
		$expProductId = explode(",",$productId);
		if($db_type==2 && count($expProductId) >1000)
		{
			$productIdCondition = " AND (";
			$prodIdsArr=array_chunk($expProductId,999);
			foreach($prodIdsArr as $pids)
			{
				$pids=implode(",",$pids);
				$productIdCondition .= " e.prod_id IN(".$pids.") OR ";
			}
			$productIdCondition = chop($productIdCondition,'OR ');
			$productIdCondition .= ")";
		}
		else
		{
			$productIdCondition = " AND e.prod_id IN(".$productId.")";
		}
	}

	//floorCondition
	$floorCondition = '';
	if($floorId != '')
	{
		$floorCondition = " AND f.floor_id IN(".$floorId.")";
	}

	//roomCondition
	$roomCondition = '';
	if($roomId != '')
	{
		$roomCondition = " AND f.room IN(".$roomId.")";
	}

	//rackCondition
	$rackCondition = '';
	if($rackId != '')
	{
		$rackCondition = " AND f.rack IN(".$rackId.")";
	}
	
	//dateCondition
	$dateCondition = '';
	$dateCondition2 = '';
	if($fromDate != '' && $toDate != '')
	{
		if($db_type == 0)
		{
			$startDate = change_date_format($fromDate,"yyyy-mm-dd","");
			$endDate = change_date_format($toDate,"yyyy-mm-dd","");
		}
		else if($db_type==2)
		{
			$startDate = change_date_format($fromDate,"","",1);
			$endDate = change_date_format($toDate,"","",1);
		}
		
		if ($reportType == 1)
		{
			$dateCondition = " AND f.transaction_date <= '".$startDate."'";
			$dateCondition2 = " AND a.transfer_date <= '".$startDate."'";
		}
		else
		{
			$dateCondition = " AND f.transaction_date <= '".$endDate."'";
			$dateCondition2 = " AND a.transfer_date <= '".$endDate."'";
		}
	}

	/*
	|--------------------------------------------------------------------------
	|
	| transfer out qty
	|--------------------------------
	| entry_form IN(13,81,82,83)
	| trans_type = 6
	|
	| issue return qty
	|--------------------------------
	| entry_form IN(51,84)
	| trans_type = 4
	|
	| transfer in qty
	|--------------------------------
	| entry_form IN(13)
	| trans_type = 5
	|
	| receive qty
	|--------------------------------
	| entry_form IN(2,22,58)
	| trans_type = 1
	|
	|--------------------------------------------------------------------------
	|
	*/
	/*
	$sqlRcvRollQty = "SELECT d.company_name, e.prod_id, e.po_breakdown_id, e.entry_form, SUM(g.quantity) AS rcv_qty, f.store_id, f.floor_id, f.room, f.rack, f.self, f.bin_box, CASE WHEN e.entry_form IN (58,84) THEN COUNT(g.id) WHEN e.entry_form IN (2,22) THEN SUM(g.no_of_roll) ELSE 0 END AS no_of_roll_rcv FROM wo_po_break_down c INNER JOIN wo_po_details_master d ON c.job_no_mst = d.job_no INNER JOIN order_wise_pro_details e ON c.id = e.po_breakdown_id INNER JOIN inv_transaction f ON e.trans_id = f.id INNER JOIN pro_grey_prod_entry_dtls g ON f.id = g.trans_id WHERE c.status_active = 1 AND c.is_deleted = 0 AND d.status_active = 1 AND d.is_deleted = 0 AND e.status_active = 1 AND e.is_deleted = 0 AND e.entry_form IN(2,22,58,84) AND e.trans_type IN(1,4) AND f.status_active = 1 AND f.is_deleted = 0 AND d.company_name IN(".$companyId.") ".$buyerIdCondition." ".$yearCondition." ".$jobNoCondition." ".$bookingNoCondition." ".$searchByCondition." ".$productIdCondition." ".$floorCondition." ".$roomCondition." ".$rackCondition." ".$dateCondition." GROUP BY  d.company_name, e.prod_id, e.po_breakdown_id, e.entry_form, f.store_id, f.floor_id, f.room, f.rack, f.self, f.bin_box";	
	*/
	//only for roll basis 
	$sqlRcvRollQty = "
		SELECT
			d.company_name,
			e.prod_id, e.po_breakdown_id, e.entry_form, SUM(h.qnty) AS rcv_qty,
			f.store_id, f.floor_id, f.room, f.rack, f.self, f.bin_box,
			--CASE WHEN e.entry_form IN (58,84) THEN COUNT(g.id) WHEN e.entry_form IN (2,22) THEN SUM(g.no_of_roll) ELSE 0 END AS no_of_roll_rcv,
			COUNT(h.id) AS no_of_roll_rcv
        FROM
			wo_po_break_down c
			INNER JOIN wo_po_details_master d ON c.job_no_mst = d.job_no
			INNER JOIN order_wise_pro_details e ON c.id = e.po_breakdown_id
            INNER JOIN inv_transaction f ON e.trans_id = f.id
			INNER JOIN pro_grey_prod_entry_dtls g ON f.id = g.trans_id
			LEFT JOIN pro_roll_details h ON g.id = h.dtls_id
        WHERE
			c.status_active = 1
			AND c.is_deleted = 0
			AND d.status_active = 1
			AND d.is_deleted = 0
			AND e.status_active = 1
			AND e.is_deleted = 0
			AND e.entry_form IN(2,22,58,84)
			AND e.trans_type IN(1,4)
			AND e.trans_id > 0
			AND f.status_active = 1
			AND f.is_deleted = 0
			AND d.company_name IN(".$companyId.")
			".$buyerIdCondition."
			".$yearCondition."
			".$jobNoCondition."
			".$bookingNoCondition."
			".$searchByCondition."
			".$productIdCondition."
			".$floorCondition."
			".$roomCondition."
			".$rackCondition."
			".$dateCondition."
			AND h.entry_form IN(2,22,58,84)
        GROUP BY 
			d.company_name,
			e.prod_id, e.po_breakdown_id, e.entry_form,
			f.store_id, f.floor_id, f.room, f.rack, f.self, f.bin_box
	";	
	//echo $sqlRcvRollQty; die;
	$sqlRcvRollRslt = sql_select($sqlRcvRollQty);
	$dataArr = array();
	$poArr = array();
	foreach($sqlRcvRollRslt as $row)
	{
		$compId = $row[csf('company_name')];
		$productId = $row[csf('prod_id')];
		$orderId = $row[csf('po_breakdown_id')];
		$storeId = $row[csf('store_id')]*1;
		$floorId = $row[csf('floor_id')]*1;
		$roomId = $row[csf('room')]*1;
		$rackId = $row[csf('rack')]*1;
		$selfId = $row[csf('self')]*1;
		$binId = $row[csf('bin_box')]*1;
		$poArr[$orderId] = $orderId;
		
		if ($reportType == 1 || $reportType == 3)
		{
			//$rollRcvQty += $row[csf('no_of_roll_rcv')];
			$dataArr[$compId][$productId][$orderId][$storeId][$floorId][$roomId][$rackId][$selfId][$binId]['rollRcvQty'] += $row[csf('no_of_roll_rcv')];
			if($row[csf('entry_form')]  == 84)
			{
				//$issueReturnQty += $row[csf('rcv_qty')];
				$dataArr[$compId][$productId][$orderId][$storeId][$floorId][$roomId][$rackId][$selfId][$binId]['issueReturnQty'] += $row[csf('rcv_qty')];
			}
			else
			{
				//$rcvQty += $row[csf('rcv_qty')];
				$dataArr[$compId][$productId][$orderId][$storeId][$floorId][$roomId][$rackId][$selfId][$binId]['rcvQty'] += $row[csf('rcv_qty')];
			}
			
		}
		elseif ($reportType == 2)
		{
			//$rollRcvQty += $row[csf('no_of_roll_rcv')];
			//$rcvQty += $row[csf('rcv_qty')];
			
			$dataArr[$compId][$productId][$orderId][$storeId][$floorId][$roomId][$rackId][$selfId][$binId]['rollRcvQty'] += $row[csf('no_of_roll_rcv')];
			$dataArr[$compId][$productId][$orderId][$storeId][$floorId][$roomId][$rackId][$selfId][$binId]['rcvQty'] += $row[csf('rcv_qty')];
			
		}
		elseif ($reportType == 4)
		{
		}
	}
	//echo $rollRcvQty."=".$rcvQty;
	//echo "<pre>";
	//print_r($dataArr); die;

	/*
	|--------------------------------------------------------------------------
	| for issue qty and roll
	| order to order transfer
	|--------------------------------------------------------------------------
	|
	*/
	$sqlNoOfRoll="
		SELECT
			d.company_name,
			e.prod_id, e.po_breakdown_id, e.trans_type, SUM(e.quantity) AS rcv_qty,
			f.store_id, f.floor_id, f.room, f.rack, f.self, f.bin_box, COUNT(g.id) AS issue_roll
		FROM 
			wo_po_break_down c
			INNER JOIN wo_po_details_master d ON c.job_no_mst = d.job_no
			INNER JOIN order_wise_pro_details e ON c.id = e.po_breakdown_id
			INNER JOIN inv_transaction f ON e.trans_id = f.id 
			INNER JOIN inv_item_transfer_dtls g ON e.dtls_id = g.id 
		WHERE
			c.status_active = 1
			AND c.is_deleted = 0
			AND d.status_active = 1
			AND d.is_deleted = 0
			AND e.status_active = 1 
			AND e.is_deleted = 0 
			AND e.entry_form IN(82,83,110,183) 
			AND e.trans_type IN(5,6) 
			AND f.status_active = 1 
			AND f.is_deleted = 0 
			AND g.status_active = 1 
			AND g.is_deleted = 0	
			".$buyerIdCondition."
			".$yearCondition."
			".$jobNoCondition."
			".$bookingNoCondition."
			".$searchByCondition."
			".$productIdCondition."
			".$floorCondition."
			".$roomCondition."
			".$rackCondition."
			".$dateCondition."
        GROUP BY 
			d.company_name,
			e.prod_id, e.po_breakdown_id, e.trans_type,
			f.store_id, f.floor_id, f.room, f.rack, f.self, f.bin_box
		UNION ALL
		SELECT
			d.company_name,
			e.prod_id, e.po_breakdown_id, e.trans_type, SUM(e.quantity) AS rcv_qty, 
			f.store_id, f.floor_id, f.room, f.rack, f.self, f.bin_box, SUM(g.roll) AS issue_roll
		FROM
			wo_po_break_down c
			INNER JOIN wo_po_details_master d ON c.job_no_mst = d.job_no
			INNER JOIN order_wise_pro_details e ON c.id = e.po_breakdown_id
			INNER JOIN inv_transaction f ON e.trans_id = f.id 
			INNER JOIN inv_item_transfer_dtls g ON e.dtls_id = g.id 
		WHERE
			c.status_active = 1
			AND c.is_deleted = 0
			AND d.status_active = 1
			AND d.is_deleted = 0
			AND e.status_active = 1 
			AND e.is_deleted = 0 
			AND e.entry_form IN(13) 
			AND e.trans_type IN(5,6) 
			AND f.status_active = 1 
			AND f.is_deleted = 0 
			AND g.status_active = 1 
			AND g.is_deleted = 0
			".$buyerIdCondition."
			".$yearCondition."
			".$jobNoCondition."
			".$bookingNoCondition."
			".$searchByCondition."
			".$productIdCondition."
			".$floorCondition."
			".$roomCondition."
			".$rackCondition."
			".$dateCondition."
        GROUP BY 
			d.company_name,
			e.prod_id, e.po_breakdown_id, e.trans_type,
			f.store_id, f.floor_id, f.room, f.rack, f.self, f.bin_box
	";
	//echo $sqlNoOfRoll; die;
	$sqlNoOfRollResult = sql_select($sqlNoOfRoll);
	foreach($sqlNoOfRollResult as $row)
	{
		$compId = $row[csf('company_name')];
		$productId = $row[csf('prod_id')];
		$orderId = $row[csf('po_breakdown_id')];
		$storeId = $row[csf('store_id')]*1;
		$floorId = $row[csf('floor_id')]*1;
		$roomId = $row[csf('room')]*1;
		$rackId = $row[csf('rack')]*1;
		$selfId = $row[csf('self')]*1;
		$binId = $row[csf('bin_box')]*1;

		if ($reportType == 1 || $reportType == 3)
		{
			if($row[csf('trans_type')] == 5)
			{
				//$rollRcvQty += $row[csf('issue_roll')];
				//$transferInQty += $row[csf('rcv_qty')];
				$poArr[$orderId] = $orderId;
				$dataArr[$compId][$productId][$orderId][$storeId][$floorId][$roomId][$rackId][$selfId][$binId]['rollRcvQty'] += $row[csf('issue_roll')];
				$dataArr[$compId][$productId][$orderId][$storeId][$floorId][$roomId][$rackId][$selfId][$binId]['transferInQty'] += $row[csf('rcv_qty')];
			}
			if($row[csf('trans_type')] == 6)
			{
				//$rollIssueQty += $row[csf('issue_roll')];
				//$transferOutQty += $row[csf('rcv_qty')];
				
				$transOutArr[$compId][$productId][$orderId][$storeId][$floorId][$roomId][$rackId][$selfId][$binId]['rollIssueQty'] += $row[csf('issue_roll')];
				$transOutArr[$compId][$productId][$orderId][$storeId][$floorId][$roomId][$rackId][$selfId][$binId]['transferOutQty'] += $row[csf('rcv_qty')];
			}
			
		}
		elseif ($reportType == 2)
		{
			if($row[csf('trans_type')] == 5)
			{
				//$rollRcvQty += $row[csf('issue_roll')];
				//$transferInQty += $row[csf('rcv_qty')];
				
				$poArr[$orderId] = $orderId;
				$dataArr[$compId][$productId][$orderId][$storeId][$floorId][$roomId][$rackId][$selfId][$binId]['rollRcvQty'] += $row[csf('issue_roll')];
				$dataArr[$compId][$productId][$orderId][$storeId][$floorId][$roomId][$rackId][$selfId][$binId]['transferInQty'] += $row[csf('rcv_qty')];
			}
			if($row[csf('trans_type')] == 6)
			{
				//$rollIssueQty += $row[csf('issue_roll')];
				//$transferOutQty += $row[csf('rcv_qty')];
				
				$transOutArr[$compId][$productId][$orderId][$storeId][$floorId][$roomId][$rackId][$selfId][$binId]['rollIssueQty'] += $row[csf('issue_roll')];
				$transOutArr[$compId][$productId][$orderId][$storeId][$floorId][$roomId][$rackId][$selfId][$binId]['transferOutQty'] += $row[csf('rcv_qty')];
			}
		}
		elseif ($reportType == 4)
		{
		}
	}
	//echo $rollRcvQty."=".$transferInQty."=".$rollIssueQty."=".$transferOutQty;
	//echo "<pre>";
	//print_r($noOfRollIssueArr);
	
	/*
	|--------------------------------------------------------------------------
	| for issue qty and roll
	|--------------------------------------------------------------------------
	|
	*/
	/*
	//dataType=0 for int 1 for string; 
	function where_con_using_array($arrayData,$dataType=0,$table_coloum){
	".where_con_using_array($poArr, '0', 'e.po_breakdown_id')."
	*/
	
	if(!empty($poArr))
	{
		$sqlNoOfRollIssue="
			SELECT
				d.company_name,
				e.prod_id, e.po_breakdown_id, SUM(e.quantity) AS issue_qty,
				f.store_id, f.floor_id, f.room, f.rack, f.self, f.bin_box,
				SUM(g.no_of_roll) AS issue_roll
			FROM
				wo_po_break_down c
				INNER JOIN wo_po_details_master d ON c.job_no_mst = d.job_no
				INNER JOIN order_wise_pro_details e ON c.id = e.po_breakdown_id
				INNER JOIN inv_transaction f ON e.trans_id = f.id
				INNER JOIN inv_grey_fabric_issue_dtls g ON e.dtls_id = g.id
			WHERE
				e.status_active = 1
				AND e.is_deleted = 0
				AND e.entry_form IN(16)
				AND e.trans_type = 2
				AND f.status_active = 1
				AND f.is_deleted = 0
				AND g.status_active = 1
				AND g.is_deleted = 0
				".$dateCondition."
				".where_con_using_array($poArr, '0', 'e.po_breakdown_id')."
			GROUP BY 
				d.company_name,
				e.prod_id, e.po_breakdown_id,
				f.store_id, f.floor_id, f.room, f.rack, f.self, f.bin_box
			UNION ALL
			SELECT
				d.company_name,
				e.prod_id, e.po_breakdown_id, SUM(e.quantity) AS issue_qty,
				f.store_id, f.floor_id, f.room, f.rack, f.self, f.bin_box,
				COUNT(g.id) AS issue_roll
			FROM
				wo_po_break_down c
				INNER JOIN wo_po_details_master d ON c.job_no_mst = d.job_no
				INNER JOIN order_wise_pro_details e ON c.id = e.po_breakdown_id
				INNER JOIN inv_transaction f ON e.trans_id = f.id
				INNER JOIN pro_roll_details g ON e.dtls_id = g.dtls_id
			WHERE
				e.status_active = 1
				AND e.is_deleted = 0
				AND e.entry_form IN(61)
				AND e.trans_type = 2
				AND f.status_active = 1
				AND f.is_deleted = 0
				AND g.status_active = 1
				AND g.is_deleted = 0
				AND g.entry_form IN(61) 
				".$dateCondition."
				".where_con_using_array($poArr, '0', 'e.po_breakdown_id')."
			GROUP BY 
				d.company_name,
				e.prod_id, e.po_breakdown_id,
				f.store_id, f.floor_id, f.room, f.rack, f.self, f.bin_box
		";
		//echo $sqlNoOfRollIssue; die;
		$sqlNoOfRollIssueResult = sql_select($sqlNoOfRollIssue);
		$noOfRollIssueArr = array();
		foreach($sqlNoOfRollIssueResult as $row)
		{
			$compId = $row[csf('company_name')];
			$productId = $row[csf('prod_id')];
			$orderId = $row[csf('po_breakdown_id')];
			$storeId = $row[csf('store_id')]*1;
			$floorId = $row[csf('floor_id')]*1;
			$roomId = $row[csf('room')]*1;
			$rackId = $row[csf('rack')]*1;
			$selfId = $row[csf('self')]*1;
			$binId = $row[csf('bin_box')]*1;
			
			if ($reportType == 1 || $reportType == 3)
			{
				//$issueQty += $row[csf('issue_qty')]*1;
				//$rollIssueQty += $row[csf('issue_roll')];
				
				$issueQtyArr[$compId][$productId][$orderId][$storeId][$floorId][$roomId][$rackId][$selfId][$binId]['issueQty'] += $row[csf('issue_qty')];
				$noOfRollIssueArr[$compId][$productId][$orderId][$storeId][$floorId][$roomId][$rackId][$selfId][$binId]['rollIssueQty'] += $row[csf('issue_roll')];
			}
			elseif ($reportType == 2)
			{
				//$issueQty += $row[csf('issue_qty')]*1;
				//$rollIssueQty += $row[csf('issue_roll')];
				
				$issueQtyArr[$compId][$productId][$orderId][$storeId][$floorId][$roomId][$rackId][$selfId][$binId]['issueQty'] += $row[csf('issue_qty')];
				$noOfRollIssueArr[$compId][$productId][$orderId][$storeId][$floorId][$roomId][$rackId][$selfId][$binId]['rollIssueQty'] += $row[csf('issue_roll')];
			}
			elseif ($reportType == 4)
			{
			}
		}
	}
	//echo $issueQty."=".$rollIssueQty;
	//echo "<pre>";
	//print_r($issueQtyArr);
	
	if(empty($dataArr))
	{
		echo get_empty_data_msg();
		die;
	}
	
	$company_arr = return_library_array( "select id, company_short_name from lib_company", "id", "company_short_name"  );
	$buyer_array = return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$count_arr = return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');
	$brand_arr = return_library_array( "select id, brand_name from lib_brand",'id','brand_name');
	$color_arr = return_library_array( 'SELECT id, color_name FROM lib_color', 'id', 'color_name');
	
	//floorSql
	$floorSql = "
		SELECT a.floor_room_rack_id, a.floor_room_rack_name, a.company_id, b.location_id, b.store_id
    	FROM lib_floor_room_rack_mst a
		INNER JOIN lib_floor_room_rack_dtls b ON a.floor_room_rack_id = b.floor_id
    	WHERE a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 AND a.company_id IN(".$companyId.")
	";
	$floorDetails = return_library_array( $floorSql, 'floor_room_rack_id', 'floor_room_rack_name');
	
	//roomSql
	$roomSql = "
		SELECT a.floor_room_rack_id, a.floor_room_rack_name, a.company_id, b.location_id, b.store_id
    	FROM lib_floor_room_rack_mst a
		INNER JOIN lib_floor_room_rack_dtls b ON a.floor_room_rack_id = b.room_id
    	WHERE a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 AND a.company_id IN(".$companyId.")
	";
	$roomDetails = return_library_array( $roomSql, 'floor_room_rack_id', 'floor_room_rack_name');
	
	//rackSql
	$rackSql = "
		SELECT a.floor_room_rack_id, a.floor_room_rack_name, a.company_id, b.location_id, b.store_id, b.serial_no
    	FROM lib_floor_room_rack_mst a
		INNER JOIN lib_floor_room_rack_dtls b ON a.floor_room_rack_id = b.rack_id
    	WHERE a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 AND a.company_id IN(".$companyId.")
	";
	$rackDetails = return_library_array( $rackSql, 'floor_room_rack_id', 'floor_room_rack_name');
	
	$rackSerialNoSql = "
		SELECT b.floor_room_rack_dtls_id, b.serial_no
    	FROM lib_floor_room_rack_mst a
		INNER JOIN lib_floor_room_rack_dtls b ON a.floor_room_rack_id = b.rack_id
    	WHERE a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 AND a.company_id IN(".$companyId.")
		GROUP BY b.floor_room_rack_dtls_id, b.serial_no
		ORDER BY b.serial_no ASC
	";
	$rackSerialNoResult = sql_select($rackSerialNoSql);
	foreach($rackSerialNoResult as $row)
	{
		$rackSerialNoArr[$row[csf('floor_room_rack_dtls_id')]] = $row[csf('serial_no')];
	}

	//selfSql
	$selfSql = "
		SELECT a.floor_room_rack_id, a.floor_room_rack_name, a.company_id, b.location_id, b.store_id
    	FROM lib_floor_room_rack_mst a
		INNER JOIN lib_floor_room_rack_dtls b ON a.floor_room_rack_id = b.shelf_id
    	WHERE a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 AND a.company_id IN(".$companyId.")
	";
	$selfDetails = return_library_array( $selfSql, 'floor_room_rack_id', 'floor_room_rack_name');
	
	//binSql
	$binSql = "
		SELECT a.floor_room_rack_id, a.floor_room_rack_name, a.company_id, b.location_id, b.store_id
    	FROM lib_floor_room_rack_mst a
		INNER JOIN lib_floor_room_rack_dtls b ON a.floor_room_rack_id = b.bin_id
    	WHERE a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 AND a.company_id IN(".$companyId.")
	";
	$binDetails = return_library_array( $binSql, 'floor_room_rack_id', 'floor_room_rack_name');
		
	//composition and constructtion
	$composition_arr=array();
	$constructtion_arr=array();
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$deter_array=sql_select($sql_deter);
	foreach( $deter_array as $row) 
	{
		$constructtion_arr[$row[csf('id')]]=$row[csf('construction')];
		$composition_arr[$row[csf('id')]].=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ";
	}
	
	$invTransactionIdArr = array();
	$orderIdArr = array();
	foreach($sqlMainResult as $row)
	{
		$invTransactionIdArr[$row[csf('id')]] = $row[csf('id')];
		$orderIdArr[$row[csf('po_break_down_id')]] = $row[csf('po_break_down_id')];
	}	

	//for order wise and rack wise button
	if ($reportType == 2 || $reportType == 3)
	{
		$prodArray = array();
		$poArray = array();
		foreach($dataArr as $compId=>$compArr)
		{
			foreach($compArr as $productId=>$productArr)
			{
				foreach($productArr as $orderId=>$orderArr)
				{
					foreach($orderArr as $storeId=>$storeArr)
					{
						foreach($storeArr as $floorId=>$floorArr)
						{
							foreach($floorArr as $roomId=>$roomArr)
							{
								foreach($roomArr as $rackId=>$rackArr)
								{
									foreach($rackArr as $selfId=>$selfArr)
									{
										foreach($selfArr as $binId=>$row)
										{
											$prodArray[$productId] = $productId;
											$poArray[$orderId] = $orderId;
										}
									}
								}
							}
						}
					}
				}
			}
		}

		//PRODUCT_DETAILS_MASTER
		/*
		$sqlProduct="SELECT id, FROM product_details_master WHERE id IN(".implode(',', $prodArray).")";
		//echo $sqlProduct; die;
		$sqlProductRslt = sql_select($sqlProduct);
		$ProductInfoArr = array();
		foreach($sqlProductRslt as $row)
		{
			$orderId = $row[csf('po_break_down_id')];
			$ProductInfoArr[$orderId]['job_no'] = $row[csf('job_no')];
			$ProductInfoArr[$orderId]['buyer_id'] = $row[csf('buyer_name')];
			$ProductInfoArr[$orderId]['po_number'] = $row[csf('po_number')];
			$ProductInfoArr[$orderId]['style_ref_no'] = $row[csf('style_ref_no')];
			
			$ProductInfoArr[$orderId]['booking_type'][] = $row[csf('booking_type')];
			$ProductInfoArr[$orderId]['is_short'][] = $row[csf('is_short')];
			$ProductInfoArr[$orderId]['booking_no'][] = $row[csf('booking_no')];
	
		}
		//echo "<pre>";
		//print_r($infoArr);
		*/	
	
		//for yarn information
		$sqlYarn = "SELECT e.prod_id, g.febric_description_id, g.gsm, g.width, g.machine_dia, g.stitch_length, g.color_id, g.color_range_id, g.yarn_count, g.brand_id, g.yarn_lot FROM order_wise_pro_details e INNER JOIN pro_grey_prod_entry_dtls g ON e.dtls_id = g.id WHERE e.entry_form in(2,22,58,84) ".where_con_using_array($prodArray, '0', 'e.prod_id')."";
		$sqlYarnRslt = sql_select($sqlYarn);
		$yarnInfoArr = array();
		foreach($sqlYarnRslt as $row)
		{
			$prodId = $row[csf('prod_id')];
			$yarnInfoArr[$prodId]['construction'] = $constructtion_arr[$row[csf('febric_description_id')]];
			$yarnInfoArr[$prodId]['composition'] = $composition_arr[$row[csf('febric_description_id')]];
			$yarnInfoArr[$prodId]['gsm'] = $row[csf('gsm')];
			$yarnInfoArr[$prodId]['width'] = $row[csf('width')];
			$yarnInfoArr[$prodId]['machine_dia'] = $row[csf('machine_dia')];
			$yarnInfoArr[$prodId]['stitch_length'] = $row[csf('stitch_length')];
			
			$expColor = explode(',', $row[csf('color_id')]);
			$clrArr = array();
			foreach($expColor as $clr)
			{
				$clrArr[$clr] = $color_arr[$clr];
			}
			
			$yarnInfoArr[$prodId]['color_id'] = implode(',', $clrArr);
			$yarnInfoArr[$prodId]['color_range_id'] = $color_range[$row[csf('color_range_id')]];
			$yarnInfoArr[$prodId]['yarn_count'] = $row[csf('yarn_count')];
			$yarnInfoArr[$prodId]['brand_id'] = $row[csf('brand_id')];
			$yarnInfoArr[$prodId]['yarn_lot'] = $row[csf('yarn_lot')];
		}
		//echo "<pre>";
		//print_r($infoArr);
	}
	
	/*
	|--------------------------------------------------------------------------
	| Summary
	|--------------------------------------------------------------------------
	|
	*/
	if ($reportType == 1)
	{
		$newDataArr = array();
		foreach($dataArr as $compId=>$compArr)
		{
			foreach($compArr as $productId=>$productArr)
			{
				foreach($productArr as $orderId=>$orderArr)
				{
					foreach($orderArr as $storeId=>$storeArr)
					{
						foreach($storeArr as $floorId=>$floorArr)
						{
							foreach($floorArr as $roomId=>$roomArr)
							{
								foreach($roomArr as $rackId=>$rackArr)
								{
									foreach($rackArr as $selfId=>$selfArr)
									{
										foreach($selfArr as $binId=>$row)
										{
											if($floorId && $roomId && $rackId)
											{
												//total receive calculation
												$totalRcvQty = number_format($row['rcvQty'],2,'.','')+number_format($row['issueReturnQty'],2,'.','')+number_format($row['transferInQty'],2,'.','');
												//total issue calculation
												$issueQty = $issueQtyArr[$compId][$productId][$orderId][$storeId][$floorId][$roomId][$rackId][$selfId][$binId]['issueQty'];
												$rcvReturnQty = 0;
												$transferOutQty = $transOutArr[$compId][$productId][$orderId][$storeId][$floorId][$roomId][$rackId][$selfId][$binId]['transferOutQty'];
												$totalIssueQty = number_format($issueQty,2,'.','')+number_format($rcvReturnQty,2,'.','')+number_format($transferOutQty,2,'.','');
												//$isqty+=$issueQty;
												//$tQty+=$transferOutQty;
												
												//stock calculation
												$stockQty = $totalRcvQty - $totalIssueQty;
												if($stockQty > 0)
												{
													//$rcv +=$totalRcvQty;
													//$issue +=$totalIssueQty;
													$newDataArr[$floorDetails[$floorId]][$roomDetails[$roomId]][$rackSerialNoArr[$rackId]][$rackDetails[$rackId]][$compId]['stockQty'] += $stockQty;
													//$newDataArr[$floorId][$roomId][$rackId][$compId]['stockQty'] += $stockQty;
													//$tot_com[$compId]=$compId;
												}
											}
										}
									}
								}
							}
						}
					}
				}
			}
		}
		//echo $rcv.'='.$issue.'='.$isqty.'='.$tQty;
		//echo "<pre>";
		//print_r($newDataArr); die;

		$expcompany = explode(',', $companyId);
		$onOfCompany = count($expcompany);
		$comWidth = (120*$onOfCompany);
		$colSpan = (6+$onOfCompany);
		$width = 480+$comWidth;
		?>
		<fieldset style="width:<? echo $width+20; ?>px;margin:5px auto;">
			<table width="<? echo $width; ?>" cellpadding="1" cellspacing="0" border="1" rules="all" class="rpt_table" align="left">
				<thead>
                    <tr class="form_caption" style="border:none;">
                    	<th align="center" colspan="<?php echo $colSpan; ?>" style="font-size:16px"><strong>Rack Wise Grey Fabrics Stock Report<br />This report is generated based on date : <?php echo date('d-m-Y', strtotime($fromDate)); ?></strong></th>
                    </tr>
                    <tr>
						<th width="30" rowspan="2">SL</th>
						<th width="60" rowspan="2">Floor</th>
						<th width="60" rowspan="2">Room</th>
						<th width="60" rowspan="2">Rack</th>
                        <th width="<?php echo $comWidth; ?>" colspan="<?php echo $onOfCompany; ?>">Stock Qty As On <?php echo date('d-m-Y', strtotime($fromDate)); ?></th>
                        <th width="120" rowspan="2">Rack Total</th>
                        <th rowspan="2">Remarks</th>
					</tr>
                    <tr>
                    <?php
					foreach($expcompany as $com)
					{
						?>
                        <th width="120"><?php echo $company_arr[$com];?></th>
                        <?php
					}
					?>
                    </tr>
				</thead>
			</table>
			<div style="width:<? echo $width+20; ?>px; overflow-y: scroll; max-height:380px;" id="scroll_body">
				<table width="<? echo $width; ?>" cellpadding="1" cellspacing="0" border="1" rules="all" class="rpt_table" align="left" id="table_body">
					<tbody>
					<?php
					$sl = 0;
					$grandTotal = array();
					$rackIdArrChart = array();
					$rackQtyArrChart = array();
					foreach($newDataArr as $floorId=>$floorArr)
					{
						foreach($floorArr as $roomId=>$roomArr)
						{
							ksort($roomArr);
							foreach($roomArr as $seq=>$seqArr)
							{
								foreach($seqArr as $rackId=>$row)
								{
									if($valueType != 1 && $row['stockQty'] == 0)
									{
										continue;
									}
									
									$sl++;
									?>
									<tr>
										<td width="30" align="center"><?php echo $sl; ?></td>
                                        <td style="word-break:break-all;" width="60"><?php echo $floorId; ?></td>
										<td style="word-break:break-all;" width="60"><?php echo $roomId; ?></td>
										<td style="word-break:break-all;" width="60"><?php echo $rackId; ?></td>
										<?
										$rac_tot=0;
										foreach($expcompany as $com)
										{
											?>
											<td width="120" title="<?php //echo ?>" align="right"><?php echo number_format($row[$com]['stockQty'],2); ?></td>
											<?php
											$rac_tot += $row[$com]['stockQty'];
											$rac_grand_tot += $row[$com]['stockQty'];
											$com_grand_tot[$com] += $row[$com]['stockQty'];
											
											if($rackId !='')
											{
												$rackIdArrChart[$rackId] = $rackId;
												$rackQtyArrChart[$com][$rackId] += number_format($row[$com]['stockQty'], 2, ".", "");
											}											
										}
										?>
										<td width="120" align="right"><?php echo number_format($rac_tot,2); ?></td>
										<td align="right"></td>
									</tr>
									<?php
								}
							}
						}
					}
                    ?>
                    </tbody>
				</table>
            </div>
            <table width="<? echo $width; ?>" cellpadding="1" cellspacing="0" border="1" rules="all" class="rpt_table" align="left" id="table_foot">
                <tfoot>
                    <tr>
                        <th width="30">&nbsp;</th>
                        <th width="60">&nbsp;</th>
                        <th width="60">&nbsp;</th>
                        <th width="60">Total</th>
                        <?
						$rac_tot=0;
						foreach($expcompany as $com)
						{
							?>
							<th width="120" align="right"><?php echo number_format($com_grand_tot[$com],2); ?></th>
							<?php
						}
						?>
                        <th width="120" align="right"><?php echo number_format($rac_grand_tot,2); ?></th>
                        <th align="right"></th>
                    </tr>
                </tfoot>
            </table>
        </fieldset>
        <?
    }
	
	/*
	|--------------------------------------------------------------------------
	| Order Wise
	|--------------------------------------------------------------------------
	|
	*/
	else if ($reportType == 2)
	{
		//for booking information
		$sqlBooking = "
			SELECT
				b.job_no, b.booking_no, b.booking_type, b.is_short, b.po_break_down_id,
				c.po_number,
				d.style_ref_no, d.buyer_name
			FROM
				wo_booking_dtls b
				INNER JOIN wo_po_break_down c ON b.po_break_down_id = c.id
				INNER JOIN wo_po_details_master d ON c.job_no_mst = d.job_no
			WHERE
				b.status_active = 1
				AND b.is_deleted = 0
				AND b.booking_type IN(1,4)
				AND c.status_active = 1
				AND c.is_deleted = 0
				AND d.status_active = 1
				AND d.is_deleted = 0
				".where_con_using_array($poArray, '0', 'c.id')."
			GROUP BY
				b.job_no, b.booking_no, b.booking_type, b.is_short, b.po_break_down_id,
				c.po_number,
				d.style_ref_no, d.buyer_name
		";
		//echo $sqlMain; die;
		$sqlBookingRslt = sql_select($sqlBooking);
		$bookingInfoArr = array();
		foreach($sqlBookingRslt as $row)
		{
			$orderId = $row[csf('po_break_down_id')];
			$bookingInfoArr[$orderId]['job_no'] = $row[csf('job_no')];
			$bookingInfoArr[$orderId]['buyer_id'] = $row[csf('buyer_name')];
			$bookingInfoArr[$orderId]['po_number'] = $row[csf('po_number')];
			$bookingInfoArr[$orderId]['style_ref_no'] = $row[csf('style_ref_no')];
			
			$bookingInfoArr[$orderId]['booking_type'][] = $row[csf('booking_type')];
			$bookingInfoArr[$orderId]['is_short'][] = $row[csf('is_short')];
			$bookingInfoArr[$orderId]['booking_no'][] = $row[csf('booking_no')];
		}
		//echo "<pre>";
		//print_r($infoArr);
				
		$width = 2760;
		?>
		<fieldset style="width:<?php echo $width+20; ?>px;margin:5px auto;">
			<table width="<?php echo $width; ?>" cellpadding="1" cellspacing="0" border="1" rules="all" class="rpt_table" align="left">
				<thead>
                    <tr class="form_caption" style="border:none;">
                    	<th align="center" colspan="36" style="font-size:16px"><strong>Rack Wise Grey Fabrics Stock Report</strong></th>
                    </tr>
                    <tr>
						<th width="30">SL</th>
						<th width="60">Company</th>
						<th width="60">Floor</th>
						<th width="60">Room</th>
						<th width="60">Rack</th>
						<th width="60">Shelf</th>
						<th width="60">Bin</th>
						<th width="70">Job No.</th>
						<th width="100">Buyer</th>
						<th width="70">Order No.</th>
						<th width="100">Style Ref</th>
						<th width="70">Booking Type</th>
						<th width="100">Booking No.</th>
						<th width="110">Constraction</th>
						<th width="120">Composition</th>
						<th width="50">GSM</th>
						<th width="50">F/Dia</th>
						<th width="50">M/Dia</th>
						<th width="60">Stich Length</th>
						<th width="100">Dyeing Color</th>
						<th width="100">Color Range</th>
						<th width="60">Y. Count</th>
						<th width="60">Y. Brand</th>
						<th width="100">Y. Lot</th>
						<th width="80">Recv. Qty.</th>
						<th width="80">Issue Ret. Qty.</th>
						<th width="80">Transf. In Qty.</th>
						<th width="80">Total Recv.</th>
						<th width="80">No of Roll Recv.</th>
						<th width="80">Issue Qty.</th>
						<th width="80">Recv. Ret. Qty.</th>
						<th width="80">Transf. Out Qty.</th>
						<th width="80">Total Issue</th>
						<th width="80">No of Roll Issued</th>
						<th width="100">Stock Qty.</th>
						<th>No Of Roll Bal.</th>
					</tr>
				</thead>
			</table>
			<div style="width:<?php echo $width+20; ?>px; overflow-y: scroll; max-height:380px;" id="scroll_body">
				<table width="<?php echo $width; ?>" cellpadding="1" cellspacing="0" border="1" rules="all" class="rpt_table" align="left" id="table_body">
					<tbody>
					<?php

					$sl = 0;
					$grandTotal = array();
					foreach($dataArr as $compId=>$compArr)
					{
						foreach($compArr as $productId=>$productArr)
						{
							foreach($productArr as $orderId=>$orderArr)
							{
								foreach($orderArr as $storeId=>$storeArr)
								{
									foreach($storeArr as $floorId=>$floorArr)
									{
										foreach($floorArr as $roomId=>$roomArr)
										{
											foreach($roomArr as $rackId=>$rackArr)
											{
												foreach($rackArr as $selfId=>$selfArr)
												{
													foreach($selfArr as $binId=>$row)
													{
														//total receive calculation
														$row['totalRcvQty'] = number_format($row['rcvQty'],2,'.','')+number_format($row['issueReturnQty'],2,'.','')+number_format($row['transferInQty'],2,'.','');
														
														//total issue calculation
														$row['issueQty'] = $issueQtyArr[$compId][$productId][$orderId][$storeId][$floorId][$roomId][$rackId][$selfId][$binId]['issueQty'];
														$row['rcvReturnQty'] = 0;
														$row['transferOutQty'] = $transOutArr[$compId][$productId][$orderId][$storeId][$floorId][$roomId][$rackId][$selfId][$binId]['transferOutQty'];
														$row['totalIssueQty'] = number_format($row['issueQty'],2,'.','')+number_format($row['rcvReturnQty'],2,'.','')+number_format($row['transferOutQty'],2,'.','');
														
														//stock qty calculation
														$row['stockQty'] = $row['totalRcvQty'] - $row['totalIssueQty'];

														//roll balance calculation
														$row['rollIssueQty'] = $transOutArr[$compId][$productId][$orderId][$storeId][$floorId][$roomId][$rackId][$selfId][$binId]['rollIssueQty']+$noOfRollIssueArr[$compId][$productId][$orderId][$storeId][$floorId][$roomId][$rackId][$selfId][$binId]['rollIssueQty'];
														$row['rollBalanceQty'] = $row['rollRcvQty'] - $row['rollIssueQty'];

														if($valueType != 1 && $row['stockQty'] == 0)
														{
															continue;
														}
														
														if($row['stockQty'] >= 0)
														{
															$sl++;
															//echo $yarnCount;
															//print_r($yarnCountArr);
															$row['job_no'] = $bookingInfoArr[$orderId]['job_no'];
															$row['buyer_id'] = $bookingInfoArr[$orderId]['buyer_id'];
															$row['po_number'] = $bookingInfoArr[$orderId]['po_number'];
															$row['style_ref_no'] = $bookingInfoArr[$orderId]['style_ref_no'];
															$row['booking_type'] = $bookingInfoArr[$orderId]['booking_type'];
															$row['is_short'] = $bookingInfoArr[$orderId]['is_short'];
															$row['booking_no'] = $bookingInfoArr[$orderId]['booking_no'];
															
															$row['construction'] = $yarnInfoArr[$prodId]['construction'];
															$row['composition'] = $yarnInfoArr[$prodId]['composition'];
															$row['gsm'] = $yarnInfoArr[$prodId]['gsm'];
															$row['width'] = $yarnInfoArr[$prodId]['width'];
															$row['machine_dia'] = $yarnInfoArr[$prodId]['machine_dia'];
															$row['stitch_length'] = $yarnInfoArr[$prodId]['stitch_length'];
															$row['color_id'] = $yarnInfoArr[$prodId]['color_id'];
															$row['color_range_id'] = $yarnInfoArr[$prodId]['color_range_id'];
															$row['yarn_count'] = $yarnInfoArr[$prodId]['yarn_count'];
															
															$yarnCountArr=explode(',', $row['yarn_count']);
															$yarnCount="";
															foreach ($yarnCountArr as $key => $yCount) 
															{
																if ($yarnCount=="") 
																{
																	$yarnCount.=$count_arr[$yCount];
																}
																else
																{
																	$yarnCount.=', '.$count_arr[$yCount];
																}
															}
															$row['brand_id'] = $yarnInfoArr[$prodId]['brand_id'];
															$row['yarn_lot'] = $yarnInfoArr[$prodId]['yarn_lot'];
															?>
                                                            <tr valign="middle">
                                                                <td width="30" align="center"><?php echo $sl; ?></td>
                                                                <td width="60"><div style="word-break:break-all"><?php echo $company_arr[$compId]; ?></div></td>
                                                                <td width="60"><div style="word-break:break-all"><?php echo $floorDetails[$floorId]; ?></div></td>
                                                                <td width="60"><div style="word-break:break-all"><?php echo $roomDetails[$roomId]; ?></div></td>
                                                                <td width="60"><div style="word-break:break-all"><?php echo $rackDetails[$rackId]; ?></div></td>
                                                                <td width="60"><div style="word-break:break-all"><?php echo $selfDetails[$selfId]; ?></div></td>
                                                                <td width="60"><div style="word-break:break-all"><?php echo $binDetails[$binId]; ?></div></td>
                                                                <td width="70"><div style="word-break:break-all"><?php echo $row['job_no']; ?></div></td>
                                                                <td width="100"><div style="word-break:break-all"><?php echo $buyer_array[$row['buyer_id']]; ?></div></td>
                                                                <td width="70"><div style="word-break:break-all"><?php echo $row['po_number']; ?></div></td>
                                                                <td width="100"><div style="word-break:break-all"><?php echo $row['style_ref_no']; ?></div></td>
                                                                <td width="70"><div style="word-break:break-all">
                                                                <?php 
                                                                $bData = array();
                                                                $bData['booking_type'] = $row['booking_type'];
                                                                $bData['is_short'] = $row['is_short'];
                                                                echo implode(', ', getBookingType($bData));
                                                                ?>
                                                                </div></td>
                                                                <td width="100"><div style="word-break:break-all"><?php echo implode(', ', $row['booking_no']); ?></div></td>
                                                                <td width="110"><div style="word-break:break-all"><?php echo $row['construction']; ?></div></td>
                                                                <td width="120"><div style="word-break:break-all"><?php echo $row['composition']; ?></div></td>
                                                                <td width="50"><?php echo $row['gsm']; ?></td>
                                                                <td width="50"><?php echo $row['width']; ?></td>
                                                                <td width="50"><?php echo $row['machine_dia']; ?></td>
                                                                <td width="60"><?php echo $row['stitch_length']; ?></td>
                                                                <td width="100"><div style="word-break:break-all"><?php echo $row['color_id']; ?></div></td>
                                                                <td width="100"><div style="word-break:break-all"><?php echo $row['color_range_id']; ?></div></td>
                                                                <td width="60"><div style="word-break:break-all"><?php echo $yarnCount;//$count_arr[$row['yarn_count']]; ?></div></td>
                                                                <td width="60"><div style="word-break:break-all"><?php echo $brand_arr[$row['brand_id']]; ?></div></td>
                                                                <td width="100"><div style="word-break:break-all"><?php echo $row['yarn_lot']; ?></div></td>
                                                                <td width="80" align="right"><?php echo number_format($row['rcvQty'],2); ?></td>
                                                                <td width="80" align="right"><?php echo number_format($row['issueReturnQty'],2); ?></td>
                                                                <td width="80" align="right"><?php echo number_format($row['transferInQty'],2); ?></td>
                                                                <td width="80" align="right"><?php echo number_format($row['totalRcvQty'],2); ?></td>
                                                                <td width="80" align="right"><?php echo number_format($row['rollRcvQty'],2); ?></td>
                                                                <td width="80" align="right"><?php echo number_format($row['issueQty'],2); ?></td>
                                                                <td width="80" align="right"><?php echo number_format($row['rcvReturnQty'],2); ?></td>
                                                                <td width="80" align="right"><?php echo number_format($row['transferOutQty'],2); ?></td>
                                                                <td width="80" align="right"><?php echo number_format($row['totalIssueQty'],2); ?></td>
                                                                <td width="80" align="right"><?php echo number_format($row['rollIssueQty'],2); ?></td>
                                                                <td width="100" align="right"><?php echo number_format($row['stockQty'],2); ?></td>
                                                                <td align="right"><?php echo number_format($row['rollBalanceQty'],2); ?></td>
                                                            </tr>
															<?php
															//$grandTotal
															$grandTotal['rcvQty'] += number_format($row['rcvQty'],2,'.','');
															$grandTotal['issueReturnQty'] += number_format($row['issueReturnQty'],2,'.','');
															$grandTotal['transferInQty'] += number_format($row['transferInQty'],2,'.','');
															$grandTotal['totalRcvQty'] += number_format($row['totalRcvQty'],2,'.','');
															$grandTotal['totalRollRcvQty'] += number_format($row['rollRcvQty'],2,'.','');
													
															$grandTotal['issueQty'] += number_format($row['issueQty'],2,'.','');
															$grandTotal['rcvReturnQty'] += number_format($row['rcvReturnQty'],2,'.','');
															$grandTotal['transferOutQty'] += number_format($row['transferOutQty'],2,'.','');
															$grandTotal['totalIssueQty'] += number_format($row['totalIssueQty'],2,'.','');
															$grandTotal['totalRollIssueQty'] += number_format($row['rollIssueQty'],2,'.','');
															$grandTotal['totalStockQty'] += number_format($row['stockQty'],2,'.','');
															$grandTotal['totalRollBalanceQty'] += number_format($row['rollBalanceQty'],2,'.','');
														}
													}
												}
											}
										}
									}
								}
							}
						}
					}
                    ?>
                    </tbody>
                </table>
            </div>
            <table width="<?php echo $width; ?>" cellpadding="1" cellspacing="0" border="1" rules="all" class="rpt_table" align="left" id="table_foot">
                <tfoot>
                    <tr>
                        <th width="30">&nbsp;</th>
                        <th width="60">&nbsp;</th>
                        <th width="60">&nbsp;</th>
                        <th width="60">&nbsp;</th>
                        <th width="60">&nbsp;</th>
                        <th width="60">&nbsp;</th>
                        <th width="60">&nbsp;</th>
                        <th width="70">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="70">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="70">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="110">&nbsp;</th>
                        <th width="120">&nbsp;</th>
                        <th width="50">&nbsp;</th>
                        <th width="50">&nbsp;</th>
                        <th width="50">&nbsp;</th>
                        <th width="60">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="60">&nbsp;</th>
                        <th width="60">&nbsp;</th>
                        <th width="100" align="right">Total</th>
                        <th width="80" align="right" id="value_rcvQty"><?php echo number_format($grandTotal['rcvQty'],2); ?></th>
                        <th width="80" align="right" id="value_issueReturnQty"><?php echo number_format($grandTotal['issueReturnQty'],2); ?></th>
                        <th width="80" align="right" id="value_transferInQty"><?php echo number_format($grandTotal['transferInQty'],2); ?></th>
                        <th width="80" align="right" id="value_totalRcvQty"><?php echo number_format($grandTotal['totalRcvQty'],2); ?></th>
                        <th width="80" align="right" id="value_totalRollRcvQty"><?php echo number_format($grandTotal['totalRollRcvQty'],2); ?></th>
                        <th width="80" align="right" id="value_issueQty"><?php echo number_format($grandTotal['issueQty'],2); ?></th>
                        <th width="80" align="right" id="value_rcvReturnQty"><?php echo number_format($grandTotal['rcvReturnQty'],2); ?></th>
                        <th width="80" align="right" id="value_transferOutQty"><?php echo number_format($grandTotal['transferOutQty'],2); ?></th>
                        <th width="80" align="right" id="value_totalIssueQty"><?php echo number_format($grandTotal['totalIssueQty'],2); ?></th>
                        <th width="80" align="right" id="value_totalRollIssueQty"><?php echo number_format($grandTotal['totalRollIssueQty'],2); ?></th>
                        <th width="100" align="right" id="value_totalStockQty"><?php echo number_format($grandTotal['totalStockQty'],2); ?></th>
                        <th align="right" id="value_totalRollBalanceQty"><?php echo number_format($grandTotal['totalRollBalanceQty'],2); ?></th>
                    </tr>
                </tfoot>
            </table>
        </fieldset>
        <?php
    }
	
	/*
	|--------------------------------------------------------------------------
	| Rack Wise
	|--------------------------------------------------------------------------
	|
	*/
	else if ($reportType == 3)
	{
		$width = 1450;
		?>
		<fieldset style="width:<? echo $width+20; ?>px;margin:5px auto;">
			<table width="<? echo $width; ?>" cellpadding="1" cellspacing="0" border="1" rules="all" class="rpt_table" align="left">
				<thead>
                    <tr class="form_caption" style="border:none;">
                    	<th align="center" colspan="20" style="font-size:16px"><strong>Rack Wise Grey Fabrics Stock Report</strong></th>
                    </tr>
                    <tr>
						<th width="30">SL</th>
						<th width="60">Company</th>
						<th width="60">Floor</th>
						<th width="60">Room</th>
						<th width="60">Rack</th>
						<th width="60">Shelf</th>
						<th width="60">Bin</th>
						<th width="110">Constraction</th>
						<th width="120">Composition</th>
						<th width="50">GSM</th>
						<th width="50">F/Dia</th>
						<th width="50">M/Dia</th>
						<th width="60">Stich Length</th>
						<th width="100">Dyeing Color</th>
						<th width="100">Color Range</th>
						<th width="60">Y. Count</th>
						<th width="60">Y. Brand</th>
						<th width="100">Y. Lot</th>
						<th width="100">Stock Qty.</th>
						<th>No Of Roll Bal.</th>
					</tr>
				</thead>
			</table>
			<div style="width:<? echo $width+20; ?>px; overflow-y: scroll; max-height:380px;" id="scroll_body">
				<table width="<? echo $width; ?>" cellpadding="1" cellspacing="0" border="1" rules="all" class="rpt_table" align="left" id="table_body">
					<tbody>
					<?php
					$sl = 0;
					$grandTotal = array();
					foreach($dataArr as $compId=>$compArr)
					{
						foreach($compArr as $productId=>$productArr)
						{
							foreach($productArr as $orderId=>$orderArr)
							{
								foreach($orderArr as $storeId=>$storeArr)
								{
									foreach($storeArr as $floorId=>$floorArr)
									{
										foreach($floorArr as $roomId=>$roomArr)
										{
											foreach($roomArr as $rackId=>$rackArr)
											{
												$stockQty = 0;
												$rollBalanceQty = 0;
												foreach($rackArr as $selfId=>$selfArr)
												{
													foreach($selfArr as $binId=>$row)
													{
														//roll balance calculation
														$rollIssueQty = $transOutArr[$compId][$productId][$orderId][$storeId][$floorId][$roomId][$rackId][$selfId][$binId]['rollIssueQty']+$noOfRollIssueArr[$compId][$productId][$orderId][$storeId][$floorId][$roomId][$rackId][$selfId][$binId]['rollIssueQty'];
														$rollBlncQty = $row['rollRcvQty'] - $rollIssueQty;
														
														
														//total receive calculation
														$totalRcvQty = number_format($row['rcvQty'],2,'.','')+number_format($row['issueReturnQty'],2,'.','')+number_format($row['transferInQty'],2,'.','');
														//total issue calculation
														$issueQty = $issueQtyArr[$compId][$productId][$orderId][$storeId][$floorId][$roomId][$rackId][$selfId][$binId]['issueQty'];
														$rcvReturnQty = 0;
														$transferOutQty = $transOutArr[$compId][$productId][$orderId][$storeId][$floorId][$roomId][$rackId][$selfId][$binId]['transferOutQty'];
														$totalIssueQty = number_format($issueQty,2,'.','')+number_format($rcvReturnQty,2,'.','')+number_format($transferOutQty,2,'.','');
														//$isqty+=$issueQty;
														//$tQty+=$transferOutQty;
														
														//stock calculation
														$stkQty = $totalRcvQty - $totalIssueQty;
														if($stkQty > 0)
														{
															$stockQty += $stkQty;
															$rollBalanceQty += $rollBlncQty;
														}

													}
												}
												
												if($valueType != 1 && $stockQty == 0)
												{
													continue;
												}
												if($stockQty >= 0)
												{
													$sl++;
													
													$row['construction'] = $yarnInfoArr[$prodId]['construction'];
													$row['composition'] = $yarnInfoArr[$prodId]['composition'];
													$row['gsm'] = $yarnInfoArr[$prodId]['gsm'];
													$row['width'] = $yarnInfoArr[$prodId]['width'];
													$row['machine_dia'] = $yarnInfoArr[$prodId]['machine_dia'];
													$row['stitch_length'] = $yarnInfoArr[$prodId]['stitch_length'];
													$row['color_id'] = $yarnInfoArr[$prodId]['color_id'];
													$row['color_range_id'] = $yarnInfoArr[$prodId]['color_range_id'];
													$row['yarn_count'] = $yarnInfoArr[$prodId]['yarn_count'];
													
													$yarnCountArr=explode(',', $row['yarn_count']);
													$yarnCount="";
													foreach ($yarnCountArr as $key => $yCount) 
													{
														if ($yarnCount=="") 
														{
															$yarnCount.=$count_arr[$yCount];
														}
														else
														{
															$yarnCount.=', '.$count_arr[$yCount];
														}
													}
													$row['brand_id'] = $yarnInfoArr[$prodId]['brand_id'];
													$row['yarn_lot'] = $yarnInfoArr[$prodId]['yarn_lot'];
													
													?>
													<tr>
														<td width="30" align="center"><?php echo $sl; ?></td>
														<td width="60"><div style="word-break:break-all"><?php echo $company_arr[$compId]; ?></div></td>
														<td width="60"><div style="word-break:break-all"><?php echo $floorDetails[$floorId]; ?></div></td>
														<td width="60"><div style="word-break:break-all"><?php echo $roomDetails[$roomId]; ?></div></td>
														<td width="60"><div style="word-break:break-all"><?php echo $rackDetails[$rackId]; ?></div></td>
														<td width="60"><div style="word-break:break-all"><?php echo $selfDetails[$selfId]; ?></div></td>
														<td width="60"><div style="word-break:break-all"><?php echo $binDetails[$binId]; ?></div></td>
														<td width="110"><div style="word-break:break-all"><?php echo $row['construction']; ?></div></td>
														<td width="120"><div style="word-break:break-all"><?php echo $row['composition']; ?></div></td>
														<td width="50"><?php echo $row['gsm']; ?></td>
														<td width="50"><?php echo $row['width']; ?></td>
														<td width="50"><?php echo $row['machine_dia']; ?></td>
														<td width="60"><?php echo $row['stitch_length']; ?></td>
														<td width="100"><div style="word-break:break-all"><?php echo $row['color_id']; ?></div></td>
														<td width="100"><div style="word-break:break-all"><?php echo $row['color_range_id']; ?></div></td>
														<td width="60"><div style="word-break:break-all"><?php echo $yarnCount;//$count_arr[$row['yarn_count']]; ?></div></td>
														<td width="60"><div style="word-break:break-all"><?php echo $brand_arr[$row['brand_id']]; ?></div></td>
														<td width="100"><div style="word-break:break-all"><?php echo $row['yarn_lot']; ?></div></td>
														<td width="100" align="right" title="<?php echo $productId."=".$orderId."=".$rackId; ?>"><?php echo number_format($stockQty,2); ?></td>
														<td align="right"><?php echo number_format($rollBalanceQty,2); ?></td>
													</tr>
													<?php
													//$grandTotal
													$grandTotal['totalStockQty'] += $stockQty;
													$grandTotal['totalRollBalanceQty'] += $rollBalanceQty;
												}
											}
										}
									}
								}
							}
						}
                    }
                    ?>
                    </tbody>
				</table> 
            </div>
            <table width="<? echo $width; ?>" cellpadding="1" cellspacing="0" border="1" rules="all" class="rpt_table" align="left" id="table_body">
                <tfoot>
                    <tr>
                        <th width="30">&nbsp;</th>
                        <th width="60">&nbsp;</th>
                        <th width="60">&nbsp;</th>
                        <th width="60">&nbsp;</th>
                        <th width="60">&nbsp;</th>
                        <th width="60">&nbsp;</th>
                        <th width="60">&nbsp;</th>
                        <th width="110">&nbsp;</th>
                        <th width="120">&nbsp;</th>
                        <th width="50">&nbsp;</th>
                        <th width="50">&nbsp;</th>
                        <th width="50">&nbsp;</th>
                        <th width="60">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="60">&nbsp;</th>
                        <th width="60">&nbsp;</th>
                        <th width="100">Total</th>
                        <th width="100" align="right" id="value_totalStockQty"><?php echo number_format($grandTotal['totalStockQty'],2); ?></th>
                        <th align="right" id="value_totalRollBalanceQty"><?php echo number_format($grandTotal['totalRollBalanceQty'],2); ?></th>
                    </tr>
                </tfoot>
            </table>
        </fieldset>
        <?
    }
	
	/*
	|--------------------------------------------------------------------------
	| Date Wise
	|--------------------------------------------------------------------------
	|
	*/
	else if ($reportType == 4)
	{
		$width = 700;
		?>

		<fieldset style="width:<? echo $width+20; ?>px;margin:5px auto;">
			<table width="<? echo $width; ?>" cellpadding="1" cellspacing="0" border="1" rules="all" class="rpt_table" align="left">
				<thead>
					<tr>
						<th width="30">SL</th>
						<th width="110">Company</th>
						<th width="110">Floor</th>
						<th width="100">Room</th>
						<th width="120">Rack</th>
						<th width="150">Shelf</th>
						<th width="80">Bin</th>
                        <!--
						<th width="110">Job No.</th>
						<th width="100">Buyer</th>
						<th width="110">Order No.</th>
						<th width="140">Style Ref</th>
						<th width="100">Booking Type</th>
						<th width="110">Booking No.</th>
						<th width="110">Constraction</th>
						<th width="120">Composition</th>
						<th width="80">GSM</th>
						<th width="80">F/Dia</th>
						<th width="80">M/Dia</th>
						<th width="100">Stich Length</th>
						<th width="100">Dyeing Color</th>
						<th width="100">Color Range</th>
						<th width="100">Y. Count</th>
						<th width="100">Y. Brand</th>
						<th width="100">Y. Lot</th>
						<th width="80">Recv. Qty.</th>
						<th width="80">Issue Ret. Qty.</th>
						<th width="80">Transf. In Qty.</th>
						<th width="80">Total Recv.</th>
						<th width="80">No of Roll Recv.</th>
						<th width="80">Issue Qty.</th>
						<th width="80">Recv. Ret. Qty.</th>
						<th width="80">Transf. Out Qty.</th>
						<th width="80">Total Issue</th>
						<th width="80">No of Roll Issued</th>
						<th width="100">Stock Qty.</th>
						<th>No Of Roll Bal.</th>
                        -->
					</tr>
				</thead>
			</table>
			<div style="width:<? echo $width+20; ?>px; overflow-y: scroll; max-height:380px;">
				<table width="<? echo $width; ?>" cellpadding="1" cellspacing="0" border="1" rules="all" class="rpt_table" align="left" id="table_body">
					<h1 style="color:red;">under construction</h1>
                </table>
            </div>
        </fieldset>
        <?
    }
	
	$html = ob_get_contents();
	ob_clean();
	foreach (glob("*.xls") as $filename)
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
			@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc, $html);
	
	if($reportType != 1)
	{
		echo $html."####".$filename."####".$reportType;
	}
	else
	{
		/*
		ksort($rackIdArrChart);
		ksort($rackQtyArrChart);
		$rackIdArrChart = implode("','",$rackIdArrChart);
		$rackQtyArrChart = implode(',',$rackQtyArrChart);
		$html.='<br><div id="container" style="width:'.$width.'px;border:1px solid #CCC;"></div>';
		echo $html."####".$filename."####".$reportType."####".$rackQtyArrChart."####'".$rackIdArrChart."'";
		*/
		
		foreach($rackQtyArrChart as $company_id=>$valArr)
		{
			$companyTextArr[$rack]=$rack;
			$dataARr[$company_id]="{ name: '".$company_arr[$company_id]."', data:[".implode(',',$valArr)."], stack: 'none'}";
		}
		
		$rackIdArrChart = "'".implode("','",$rackIdArrChart)."'";
		$rackQtyArrChart= implode(',',$dataARr);
		$html.='<br><div id="container" style="width:'.($width+500).'px;border:1px solid #CCC;"></div>';
		echo $html."####".$filename."####".$reportType."####".$rackQtyArrChart."####".$rackIdArrChart."";
	}
	die;
}

function getBookingType($data)
{
	$rtnData = array();
	foreach($data['booking_type'] as $val)
	{
		if($val == 1)
		{
			foreach($data['is_short'] as $shortVal)
			{
				if($shortVal == 1)
				{
					$rtnData['Short'] = 'Short';
				}
				elseif($shortVal == 2)
				{
					$rtnData['Main'] = 'Main';
				}
			}
		}
		elseif($val == 4)
		{
			$rtnData['Sample'] = 'Sample';
		}
	}
	return $rtnData;
}

function sql_insert_zs( $strTable, $arrNames, $arrValues, $commit, $contain_lob )
{
	//global $con ;
	$con = connect();
	if($contain_lob=="") $contain_lob=0;
	if( $contain_lob==0)
	{
		$count=count($arrValues);
		//for multi row
		if( $count >1 )
		{
			$k=1;	
			foreach( $arrValues as $rows)
			{
				if($k==1)
				{
					$strQuery= "INSERT ALL \n";
				}
				$strQuery .=" INTO ".$strTable." (".$arrNames.") values ".$rows." \n";
				if( $count==$k )
				{
					$count=$count-$k;
					$strQuery .= "SELECT * FROM dual";
					$stid =  oci_parse($con, $strQuery);
					$exestd=oci_execute($stid, OCI_NO_AUTO_COMMIT);
					if(!$exestd)
						return 0;
						
					$strQuery="";
					$k=0;
				}
				else if ( $k==50 )
				{
					$count=$count-$k;
					$strQuery .= "SELECT * FROM dual";
					//return $strQuery;
					$stid =  oci_parse($con, $strQuery);
					$exestd=oci_execute($stid,OCI_NO_AUTO_COMMIT);
					if(!$exestd)
						return 0;
						
					$strQuery="";
					$k=0;
				}
				$k++;
			}
			return 1;
			//return $strQuery; 
		}
		//for single row
		else
		{
			$strQuery= "INSERT  \n";
			foreach( $arrValues as $rows)
			{
				$strQuery .=" INTO ".$strTable." (".$arrNames.") values ".$rows." \n";
			}
			//return $strQuery;
			$stid =  oci_parse($con, $strQuery);
			$exestd=oci_execute($stid,OCI_NO_AUTO_COMMIT);
			if (!$exestd) return 0; 
			else return 1;
		}
	}

	/*$stid =  oci_parse($con, $strQuery);
	$exestd=oci_execute($stid,OCI_NO_AUTO_COMMIT);
	if ($exestd) 
		return "1";
	else 
		return "0";
	die;*/
}

if ($action=="stock_popup")
{
	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$data=explode('_',$data);

	$companyId=$data[0];
	$po_id=$data[1];
	$product_ids=$data[2];
	$storeId=$data[3];
	$floorId=$data[4];
	$roomId=$data[5];
	$rackId=$data[6];
	$selfId=$data[7];

	//storeCondition
	$storeCondition = '';
	if($storeId != '')
	{
		$storeCondition = " AND f.store_id IN(".$storeId.")";
	}

	//floorCondition
	$floorCondition = '';
	if($floorId != '')
	{
		$floorCondition = " AND f.floor_id IN(".$floorId.")";
	}

	//roomCondition
	$roomCondition = '';
	if($roomId != '')
	{
		$roomCondition = " AND f.room IN(".$roomId.")";
	}

	//rackCondition
	$rackCondition = '';
	if($rackId != '')
	{
		$rackCondition = " AND f.rack IN(".$rackId.")";
	}

	//shelfCondition
	$shelfCondition = '';
	if($selfId != '')
	{
		$shelfCondition = " AND f.self IN(".$selfId.")";
	}

	/*
	|--------------------------------------------------------------------------
	|
	| for receive qty
	|--------------------------------------------------------------------------
	|
	*/
	$sqlRcvRollQty = "
		SELECT c.po_number, f.company_id, e.prod_id, e.po_breakdown_id, e.entry_form, h.qnty AS rcv_qty, f.store_id, f.floor_id, f.room, f.rack, f.self, f.bin_box, h.barcode_no
		FROM wo_po_break_down c
			INNER JOIN order_wise_pro_details e ON c.id = e.po_breakdown_id
			INNER JOIN inv_transaction f ON e.trans_id = f.id 
			INNER JOIN pro_grey_prod_entry_dtls g ON f.id = g.trans_id 
			INNER JOIN pro_roll_details h ON g.id = h.dtls_id 
        WHERE
			e.status_active = 1
			AND e.is_deleted = 0
			AND e.entry_form IN(2,22,58,84)
			AND e.trans_type IN(1,4)
			AND e.trans_id > 0
			AND f.status_active = 1
			AND f.is_deleted = 0
			AND f.company_id IN(".$companyId.")
			and h.po_breakdown_id=$po_id
			and f.prod_id=$product_ids
			".$storeCondition."
			".$floorCondition."
			".$roomCondition."
			".$rackCondition."
			".$shelfCondition."
			AND h.entry_form IN(2,22,58,84)
	";	
	// echo $sqlRcvRollQty; die;
	$sqlRcvRollRslt = sql_select($sqlRcvRollQty);
	$dataArr = array();
	$poArr = array();
	$barcodeArr = array();
	foreach($sqlRcvRollRslt as $row)
	{
		$compId = $row[csf('company_id')];
		$productId = $row[csf('prod_id')];
		$orderId = $row[csf('po_breakdown_id')];
		$storeId = $row[csf('store_id')]*1;
		$floorId = $row[csf('floor_id')]*1;
		$roomId = $row[csf('room')]*1;
		$rackId = $row[csf('rack')]*1;
		$selfId = $row[csf('self')]*1;
		$binId = $row[csf('bin_box')]*1;
		$poArr[$orderId] = $orderId;

		//$dataArr[$compId][$productId][$orderId][$storeId][$floorId][$roomId][$rackId][$selfId][$binId]['rcvQty'] += $row[csf('rcv_qty')];
		
		$dataArr[$row[csf('barcode_no')]]['rcvQty'] += $row[csf('rcv_qty')];
		$dataArr[$row[csf('barcode_no')]]['po_number'] = $row[csf('po_number')];

		$barcodeArr[$row[csf("barcode_no")]]        = $row[csf("barcode_no")];
	}
	unset($sqlRcvRollRslt);
	// echo "<pre>"; print_r($dataArr); die;

	/*
	|--------------------------------------------------------------------------
	| for issue qty and roll
	| order to order transfer
	|--------------------------------------------------------------------------
	|
	*/
	$sqlNoOfRoll="
		SELECT c.po_number, d.company_name, e.prod_id, e.po_breakdown_id, e.trans_type, h.qnty AS rcv_qty, f.store_id, f.floor_id, f.room, f.rack, f.self, f.bin_box, h.barcode_no
		FROM 
			wo_po_break_down c
			INNER JOIN wo_po_details_master d ON c.job_no_mst = d.job_no
			INNER JOIN order_wise_pro_details e ON c.id = e.po_breakdown_id
			INNER JOIN inv_transaction f ON e.trans_id = f.id 
			INNER JOIN inv_item_transfer_dtls g ON e.dtls_id = g.id 
			INNER JOIN pro_roll_details h ON e.dtls_id = h.dtls_id and g.id=h.dtls_id
		WHERE
			c.status_active = 1
			AND c.is_deleted = 0
			AND d.status_active = 1
			AND d.is_deleted = 0
			AND e.status_active = 1 
			AND e.is_deleted = 0 
			AND e.entry_form IN(82,83,110,183) 
			AND h.entry_form IN(82,83,110,183)
			AND e.trans_type IN(5,6) 
			AND f.status_active = 1 
			AND f.is_deleted = 0 
			AND g.status_active = 1 
			AND g.is_deleted = 0
			AND f.company_id IN($companyId)
			and e.po_breakdown_id=$po_id
			and f.prod_id=$product_ids
			".$storeCondition."
			".$floorCondition."
			".$roomCondition."
			".$rackCondition."
			".$shelfCondition."
	";
	// echo $sqlNoOfRoll; die;
	$sqlNoOfRollResult = sql_select($sqlNoOfRoll);
	foreach($sqlNoOfRollResult as $row)
	{
		$compId = $row[csf('company_name')];
		$productId = $row[csf('prod_id')];
		$orderId = $row[csf('po_breakdown_id')];
		$storeId = $row[csf('store_id')]*1;
		$floorId = $row[csf('floor_id')]*1;
		$roomId = $row[csf('room')]*1;
		$rackId = $row[csf('rack')]*1;
		$selfId = $row[csf('self')]*1;
		$binId = $row[csf('bin_box')]*1;

		if($row[csf('trans_type')] == 5)
		{
			$poArr[$orderId] = $orderId;
			//$dataArr[$compId][$productId][$orderId][$storeId][$floorId][$roomId][$rackId][$selfId][$binId]['transferInQty'] += $row[csf('rcv_qty')];
			$dataArr[$row[csf('barcode_no')]]['rcvQty'] += $row[csf('rcv_qty')];
			$dataArr[$row[csf('barcode_no')]]['po_number'] = $row[csf('po_number')];
		}
		if($row[csf('trans_type')] == 6)
		{
			//$transOutArr[$compId][$productId][$orderId][$storeId][$floorId][$roomId][$rackId][$selfId][$binId]['transferOutQty'] += $row[csf('rcv_qty')];
			$transOutArr[$row[csf('barcode_no')]]['transferOutQty'] += $row[csf('rcv_qty')];
		}
		$barcodeArr[$row[csf("barcode_no")]]        = $row[csf("barcode_no")];
	}
	// echo "<pre>";print_r($transOutArr);die;
	unset($sqlNoOfRollResult);

	$con = connect();
	execute_query("DELETE FROM TMP_BARCODE_NO WHERE USERID = ".$user_id."");
	oci_commit($con);
	
	$barcodeArr = array_filter($barcodeArr);
	foreach($barcodeArr as $barcode)
	{
		execute_query("INSERT INTO TMP_BARCODE_NO(BARCODE_NO,USERID) VALUES(".$barcode.", ".$user_id.")");
	}
	oci_commit($con);

	/*$barcodeArr = array_filter($barcodeArr);
    if(!empty($barcodeArr))
    {
        $all_barcode_ids = implode(",", $barcodeArr);
        $barcodeCond = $all_barcode_cond = "";
        if($db_type==2 && count($barcodeArr)>999)
        {
            $all_barcode_chunk=array_chunk($barcodeArr,999) ;
            foreach($all_barcode_chunk as $chunk_arr)
            {
                $barcodeCond.=" b.barcode_no in(".implode(",",$chunk_arr).") or ";
            }
            $all_barcode_cond.=" and (".chop($barcodeCond,'or ').")";
        }
        else
        {
            $all_barcode_cond=" and b.barcode_no in($all_barcode_ids)";
        }
    }*/

    if(count($barcodeArr ) >0 ) // production
    {
        $production_sql = sql_select("SELECT b.barcode_no,a.color_range_id,a.yarn_lot, a.yarn_count,b.po_breakdown_id, a.prod_id, b.booking_no,c.booking_id, b.receive_basis, a.color_id, a.febric_description_id, a.gsm, a.width, a.stitch_length, a.machine_dia, a.machine_gg,a.machine_no_id, c.knitting_source, c.challan_no as production_challan, c.knitting_company, a.yarn_prod_id, a.body_part_id, b.coller_cuff_size, c.entry_form, c.recv_number
        from TMP_BARCODE_NO d, pro_roll_details b, pro_grey_prod_entry_dtls a, inv_receive_master c
        where d.barcode_no=b.barcode_no and d.userid=$user_id and b.dtls_id=a.id and a.mst_id = c.id and c.entry_form in(2,58) and b.entry_form in(2,58)  and a.status_active=1 and b.status_active=1 and b.receive_basis=2 order by c.entry_form desc");
        $yarn_prod_id_check=array();$prog_no_check=array();
        foreach ($production_sql as $row)
        {            
            $prodBarcodeData[$row[csf('barcode_no')]]["booking_id"]=$row[csf('booking_id')];
            $prodBarcodeData[$row[csf("barcode_no")]]["color_id"] =$row[csf("color_id")];

            if ($row[csf("entry_form")]==2) 
            {
            	$prodBarcodeData[$row[csf("barcode_no")]]["recv_number"] =$row[csf("recv_number")];
            }
   		}
   	}
   	execute_query("DELETE FROM TMP_BARCODE_NO WHERE USERID = ".$user_id."");
	oci_commit($con);

	/*
	|--------------------------------------------------------------------------
	| for issue qty and roll
	|--------------------------------------------------------------------------
	|
	*/
	$sqlNoOfRollIssue="		
		SELECT d.company_name, e.prod_id, e.po_breakdown_id, g.qnty AS issue_qty, f.store_id, f.floor_id, f.room, f.rack, f.self, f.bin_box, g.barcode_no
		FROM
			wo_po_break_down c
			INNER JOIN wo_po_details_master d ON c.job_no_mst = d.job_no
			INNER JOIN order_wise_pro_details e ON c.id = e.po_breakdown_id
			INNER JOIN inv_transaction f ON e.trans_id = f.id
			INNER JOIN pro_roll_details g ON e.dtls_id = g.dtls_id
		WHERE
			e.status_active = 1
			AND e.is_deleted = 0
			AND e.entry_form IN(61)
			AND e.trans_type = 2
			AND f.status_active = 1
			AND f.is_deleted = 0
			AND g.status_active = 1
			AND g.is_deleted = 0
			AND g.entry_form IN(61) 
			AND f.company_id IN(".$companyId.")
			and e.po_breakdown_id=$po_id
			and f.prod_id=$product_ids
			".$storeCondition."
			".$floorCondition."
			".$roomCondition."
			".$rackCondition."
			".$shelfCondition."
	";
	// echo $sqlNoOfRollIssue; die;
	$sqlNoOfRollIssueResult = sql_select($sqlNoOfRollIssue);
	$noOfRollIssueArr = array();
	foreach($sqlNoOfRollIssueResult as $row)
	{
		$compId = $row[csf('company_name')];
		$productId = $row[csf('prod_id')];
		$orderId = $row[csf('po_breakdown_id')];
		$storeId = $row[csf('store_id')]*1;
		$floorId = $row[csf('floor_id')]*1;
		$roomId = $row[csf('room')]*1;
		$rackId = $row[csf('rack')]*1;
		$selfId = $row[csf('self')]*1;
		$binId = $row[csf('bin_box')]*1;
		
		//$issueQtyArr[$compId][$productId][$orderId][$storeId][$floorId][$roomId][$rackId][$selfId][$binId]['issueQty'] += $row[csf('issue_qty')];

		$issueQtyArr[$row[csf('barcode_no')]]['issueQty'] += $row[csf('issue_qty')];
	}
	unset($sqlNoOfRollIssueResult);

	$colorArr=return_library_array( "select id, color_name from lib_color where status_active=1", "id", "color_name" );

	?>
	<fieldset style="width:<? echo $popup_width;?>; margin-left:3px">
		<div id="report_container">
			<!-- ====================Start========================= -->
			<table border="1" class="rpt_table" rules="all" width="650" cellpadding="0" cellspacing="0">
                <thead>
                    <tr>
                        <th width="30">SL</th>
                        <th width="120">Bacode No</th>
                        <th width="80">Program</th>
                        <th width="100">System Id</th>
                        <th width="100">Order</th>
                        <th width="100">Fabric Color</th>
                        <th width="">Roll Weight</th>
                    </tr>
				</thead>
            </table>
            <div style="width:670px; max-height:410px; overflow-y:scroll" id="scroll_body">
                <table border="1" class="rpt_table" rules="all" width="650" cellpadding="0" cellspacing="0" id="table_body">
                    <?		
                    // echo "<pre>";print_r($dataArr);
					$sl=0;
					foreach($dataArr as $barcode=>$row)
					{
						$totalRcvQty = number_format($row['rcvQty'],2,'.','')+number_format($row['transferInQty'],2,'.','');
						$rcvReturnQty = 0;
						$transferOutQty = $transOutArr[$barcode]['transferOutQty'];
						$issueQty=$issueQtyArr[$barcode]['issueQty'];

						$totalIssueQty = number_format($issueQty,2,'.','')+number_format($rcvReturnQty,2,'.','')+number_format($transferOutQty,2,'.','');

						//stock calculation
						$stkQty = $totalRcvQty - $totalIssueQty;
						// echo $totalRcvQty.' - '.$totalIssueQty.'<br>';
						// echo $stkQty.'<br>';
						if($stkQty > 0)
						{
							$sl++;

							$program_no=$prodBarcodeData[$barcode]["booking_id"];
							$production_number=$prodBarcodeData[$barcode]["recv_number"];
							$color_id=$prodBarcodeData[$barcode]["color_id"];
							?>
							<tr>
								<td width="30" align="center"><?php echo $sl; ?></td>
								<td width="120"><div style="word-break:break-all"><?php echo $barcode; ?></div></td>
								<td width="80"><div style="word-break:break-all"><?php echo $program_no; ?></div></td>
								<td width="100"><div style="word-break:break-all"><?php echo $production_number; ?></div></td>
								<td width="100"><div style="word-break:break-all"><?php echo $row['po_number']; ?></div></td>
								<td width="100"><div style="word-break:break-all"><?php echo $colorArr[$color_id]; ?></div></td>
								<td align="right"><?php echo number_format($stkQty,2); ?></td>
							</tr>
							<?php
							//$grandTotal
							$total_recv_qty += $stkQty;
						}
                    }
                    ?>
                    <tfoot>
                    	<tr>
                            <th colspan="6" align="right">Sub Total</th>
                            <th align="right"><? echo number_format($total_recv_qty,2); ?></th>
                        </tr>
                    </tfoot>
                </table>
			</div>
			<!-- =====================End========================== -->	
		</div>
	</fieldset>	
	<?	
    
	exit();
}
?>