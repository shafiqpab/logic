<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');
$user_name=$_SESSION['logic_erp']['user_id'];
$user_id = $_SESSION['logic_erp']["user_id"];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

function get_users_buyer()
{
	$byr_str = '';
	if ($_SESSION['logic_erp']['data_level_secured'] == 1)
	{
		if ($_SESSION['logic_erp']['buyer_id'] != '')
		{
			$byr_str = $_SESSION['logic_erp']['buyer_id'];
		}
	}
	return $byr_str;
}

if ($action=="load_drop_down_buyer")
{
	extract($_REQUEST);
	$choosenCompany = $choosenCompany;
	// echo create_drop_down( "cbo_buyer_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company in ($choosenCompany) $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,80,90)) $buyer_cond group by buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "- All Buyer -", $selected, "" );
	echo create_drop_down( "cbo_buyer_name", 120, "select distinct buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company in($choosenCompany) $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,80,90))  order by buyer_name","id,buyer_name", 1, "-- Select buyer --", 0, "","" );
	exit();
}

if($action=="job_no_popup")
{
	echo load_html_head_contents("Job Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);

	$is_byr_fld_dissable = 0;
	if($buyer_name != 0)
	{
		$is_byr_fld_dissable = 1;
	}
	?>
	<script>
		function js_set_value(str)
		{
			var splitData = str.split("_");
			//alert (splitData[1]);
			$("#hide_job_id").val(splitData[0]);
			$("#hide_job_no").val(splitData[1]);
			parent.emailwindow.hide();
		}
    </script>
    </head>
    <body>
    <div align="center">
        <form name="styleRef_form" id="styleRef_form">
		<fieldset style="width:580px;">
            <table width="570" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
            	<thead>
                    <th>Cust. Buyer</th>
                    <th>Search By</th>
                    <th id="search_by_td_up" width="170">Please Enter Job No</th>
                    <th><input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;" onClick="reset_form('styleRef_form','search_div','','','','');"></th><input type="hidden" name="hide_job_id" id="hide_job_id" value="" />
                    <input type="hidden" name="hide_job_no" id="hide_job_no" value="" />
                </thead>
                <tbody>
                	<tr>
                        <td align="center">
                        	 <?
								echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,80,90)) group by buy.id,buy.buyer_name order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer_name,'',$is_byr_fld_dissable);
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
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_year_id; ?>', 'create_job_no_search_list_view', 'search_div', 'yarn_allocation_history_report_sales_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
                    </td>
                    </tr>
            	</tbody>
           	</table>
            <div style="margin-top:15px" id="search_div"></div>
		</fieldset>
	</form>
    </div>
    </body>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit();
}

if($action=="style_no_popup")
{
	echo load_html_head_contents("Style Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>
		var selected_id = new Array; var selected_name = new Array;

		function check_all_data()
		{
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;
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

		function js_set_value( str ) {

			if (str!="") str=str.split("_");
			// alert(str[2]);
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
							<th id="search_by_td_up" width="170">Please Enter Style Ref</th>
							<th><input type="reset" name="button" class="formbutton" value="Reset"  style="width:100px;"></th>
							<input type="hidden" name="hide_job_no" id="hide_job_no" value="" />
							<input type="hidden" name="hide_job_id" id="hide_job_id" value="" />
						</thead>
						<tbody>
							<tr>
								<td align="center">
									<?
									echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company in ($companyID) $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer_name,"",0 );
									?>
								</td>
								<td align="center">
									<?
									$search_by_arr=array(2=>"Style Ref",1=>"Job No");
									$dd="change_search_event(this.value, '0*0', '0*0', '../../') ";
									echo create_drop_down( "cbo_search_by", 130, $search_by_arr,"",0, "--Select--", "",$dd,0, );
									?>
								</td>
								<td align="center" id="search_by_td">
									<input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />
								</td>
								<td align="center">
									<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_year; ?>'+'**'+'<? echo $cbo_month_id; ?>', 'create_style_no_search_list_view', 'search_div', 'yarn_allocation_history_report_sales_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
								</td>
							</tr>
						</tbody>
					</table>
					<div style="margin-top:15px" id="search_div"></div>
				</fieldset>
			</form>
		</div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if($action=="create_style_no_search_list_view")
{
	$data=explode('**',$data);
	$company_id=$data[0];
	$year_id=$data[4];
	//echo $year_id;die;
	$month_id=$data[5];


	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');

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

	if($search_by==2) $search_field="style_ref_no"; else $search_field="job_no_prefix_num";
	//$year="year(insert_date)";
	if($db_type==0) $year_field_by=" and YEAR(insert_date)";
	else if($db_type==2) $year_field_by=" and to_char(insert_date,'YYYY')";
	else $year_field_by="";
	if($db_type==0) $month_field_by="and month(insert_date)";
	else if($db_type==2) $month_field_by=" and to_char(insert_date,'MM')";
	else $month_field_by="";
	if($db_type==0) $year_field=" YEAR(insert_date) as year";
	else if($db_type==2) $year_field="  to_char(insert_date,'YYYY') as year";
	else $year_field="";

	if($year_id!=0) $year_cond=" $year_field_by=$year_id"; else $year_cond="";
	if($month_id!=0) $month_cond=" $month_field_by=$month_id"; else $month_cond="";


	$arr=array (0=>$company_arr,1=>$buyer_arr);

	$sql= "select id, job_no, job_no_prefix_num, company_name, buyer_name, style_ref_no, $year_field  from wo_po_details_master where status_active=1 and is_deleted=0 and company_name in ($company_id) and $search_field like '$search_string' $buyer_id_cond $year_cond $month_cond order by job_no";
	//echo $sql;die;

	echo create_list_view("tbl_list_search", "Company,Buyer Name,Job No,Year,Style Ref. No", "120,130,80,60","600","240",0, $sql , "js_set_value", "id,style_ref_no", "", 1, "company_name,buyer_name,0,0,0", $arr , "company_name,buyer_name,job_no_prefix_num,year,style_ref_no", "",'','0,0,0,0,0','',1) ;

	exit();
}

if ($action == "create_job_no_search_list_view")
{
	$data=explode('**',$data);
	$company_id=$data[0];
	$year_id=$data[4];

	//for company
	$company_cond = " and company_id in (".$company_id.")";

	$buyer_id_cond = '';
	if($data[1]==0)
	{
		$byr_id = get_users_buyer();
		if($byr_id != '')
		{
			$buyer_id_cond = " and customer_buyer in (".$byr_id.")";
		}
	}
	else
	{
		$buyer_id_cond=" and customer_buyer = ".$data[1];
	}

	$search_by=$data[2];
	$search_string="%".trim($data[3])."%";
	if($search_by==2)
		$search_field="style_ref_no";
	else
		$search_field="job_no";

	$year_field="to_char(insert_date,'YYYY') as year";
	if($year_id!=0)
		$year_cond=" and to_char(insert_date,'YYYY') = ".$year_id;
	else
		$year_cond="";

	//main query
	$sql = "select id, $year_field, job_no_prefix_num, job_no, company_id, within_group, sales_order_type, sales_booking_no, booking_date, buyer_id, customer_buyer, style_ref_no from fabric_sales_order_mst where entry_form=472 and status_active=1 and is_deleted=0 and ".$search_field." like '".$search_string."'".$company_cond.$buyer_id_cond.$year_cond." order by id desc";
	echo $sql; die;
	$result = sql_select($sql);
	if(empty($result))
	{
		echo "<div style='width:610px; text-align:center'>".get_empty_data_msg()."</div>";
		die;
	}

	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="560" class="rpt_table">
		<thead>
			<th width="40">SL</th>
			<th width="120">Company</th>
			<th width="120">Cust. Buyer</th>
			<th width="100">FSO No</th>
			<th width="60">Year</th>
			<th>Style Ref. No</th>
		</thead>
	</table>
	<div style="width:560px; max-height:260px; overflow-y:scroll" id="list_container_batch" align="left">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="540" class="rpt_table"
		id="tbl_list_search">
		<?
		$i = 1;
		foreach ($result as $row)
		{
			if ($i % 2 == 0)
				$bgcolor = "#E9F3FF";
			else
				$bgcolor = "#FFFFFF";

			if ($row[csf('within_group')] == 1)
			{
				//$buyer = $company_arr[$row[csf('buyer_id')]];
				$customer_buyer = $company_arr[$row[csf('customer_buyer')]];
			}
			else
			{
				//$buyer = $buyer_arr[$row[csf('buyer_id')]];
				$customer_buyer = $buyer_arr[$row[csf('customer_buyer')]];
			}

			$data = $row[csf('id')]."_".$row[csf('job_no_prefix_num')];
			?>
			<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $data; ?>');">
				<td width="40"><? echo $i; ?></td>
				<td width="120" align="center"><p><? echo $company_arr[$row[csf('company_id')]]; ?></p></td>
				<td width="120"><p><? echo $customer_buyer; ?>&nbsp;</p></td>
				<td width="100" align="center"><? echo $row[csf('job_no')]; ?></td>
				<td width="60"><p><? echo $row[csf('year')]; ?>&nbsp;</p></td>
				<td><p>&nbsp;<? echo $row[csf('style_ref_no')]; ?></p></td>
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

if ($action=="booking_no_popup")
{
	echo load_html_head_contents("Order Info","../../../", 1, 1, '','1','');
	extract($_REQUEST);
	$ex_data=explode('_',$data);
	$company_id=$ex_data[0];
	$buyer_id=$ex_data[1];
?>
	<script>
		function js_set_value(wo_id,wo_no)
		{
			document.getElementById('txt_wo_no').value=wo_no;
			document.getElementById('txt_wo_id').value=wo_id;
			parent.emailwindow.hide();
		}
    </script>
</head>
<body>
	<fieldset style="width:600px;margin-left:10px">
        <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
            <table cellpadding="0" cellspacing="0" border="1" rules="all" width="600" class="rpt_table" align="center">
                <thead>
                    <th>Buyer</th>
                    <th>Please Enter Booking No</th>
                    <th>
                        <input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
                        <input type="hidden" name="txt_wo_no" id="txt_wo_no" value="" style="width:50px">
                        <input type="hidden" name="txt_wo_id" id="txt_wo_id" value="" style="width:50px">
                    </th>
                </thead>
                <tr class="general">
                    <td align="center">
                        <?
							echo create_drop_down( "cbo_buyer_id", 130, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$company_id' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id,buy.buyer_name order by buy.buyer_name","id,buyer_name", 1, "--Select Buyer--", $buyer_id, "" );
                        ?>
                    </td>
                    <td align="center">
                        <input type="text" style="width:150px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />
                    </td>
                    <td align="center">
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_buyer_id').value+'_'+<? echo $company_id; ?>+'_'+document.getElementById('txt_search_common').value, 'create_wo_search_list_view', 'search_div', 'yarn_allocation_history_report_sales_controller', 'setFilterGrid(\'list_view\',-1);')" style="width:100px;" />
                    </td>
                </tr>
            </table>
            <div id="search_div" style="margin-top:10px"></div>
        </form>
    </fieldset>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}

if ($action=="create_wo_search_list_view")
{
	$data=explode('_',$data);
	//echo $data;
	if ($data[0]==0) $buyer_id=""; else $buyer_id=" and buyer_id=$data[0]";
	if ($data[1]==0) $company_id=""; else $company_id=" and company_id=$data[1]";
	if ($data[2]==0) $search_wo=""; else $search_wo=" and booking_no_prefix_num=$data[2]";

	/*if($db_type==0)
	{
		if ($data[3]==0) $year_id_cond=""; else $year_id_cond=" and YEAR(insert_date)=$data[3]";
	}
	elseif($db_type==2)
	{
		if ($data[3]==0) $year_id_cond=""; else $year_id_cond=" and TO_CHAR(insert_date,'YYYY')=$data[3]";
	}

	if ($data[4]==0) $category_id_cond=""; else $category_id_cond=" and item_category=$data[4]";
	if ($data[5]==1 || $data[5]==2)  $wo_type_cond=" and booking_type in (1,2) and is_short='$data[5]'"; else $wo_type_cond="";
	if ($data[5]==3) $wo_type_cond_sam="  and booking_type=4"; else $wo_type_cond_sam="";
	*/

	if($db_type==0)
	{
		$year=" YEAR(insert_date) as year";
	}
	elseif($db_type==2)
	{
		$year=" TO_CHAR(insert_date,'YYYY') as year";
	}
	$buyerArr = return_library_array("select id,buyer_name from lib_buyer ","id","buyer_name");
	/*if($data[5]==0)
	{*/
		$sql= "select id, booking_no, $year, booking_no_prefix_num, booking_date, buyer_id, booking_type, is_short
		from wo_booking_mst
		where status_active=1 and is_deleted=0 $company_id $buyer_id $year_id_cond $search_wo and booking_type=1 and is_short=2
		order by id Desc";

		/*echo $sql= "select id, booking_no, $year, booking_no_prefix_num, booking_date, buyer_id, booking_type, is_short, 0 as type from wo_booking_mst where status_active=1 and is_deleted=0 $company_id $buyer_id $year_id_cond
		union all
		SELECT id, booking_no, $year, booking_no_prefix_num, booking_date, buyer_id, booking_type, is_short, 1 as type from wo_non_ord_samp_booking_mst where status_active=1 and is_deleted=0 $company_id $buyer_id $year_id_cond ";*/
		//$search_wo $category_id_cond $wo_type_cond $wo_type_cond_sam  $search_wo $category_id_cond

	/*}
	else if ($data[5]==1 || $data[5]==2 || $data[5]==3)
	{
		$sql= "select id, booking_no, $year, booking_no_prefix_num, booking_date, buyer_id, booking_type, is_short, 0 as type from wo_booking_mst where status_active=1 and is_deleted=0 $company_id $buyer_id $year_id_cond $search_wo $category_id_cond $wo_type_cond $wo_type_cond_sam";
	}
	else
	{
		$sql= "SELECT id, booking_no, $year, booking_no_prefix_num, booking_date, buyer_id, booking_type, is_short, 1 as type from wo_non_ord_samp_booking_mst where status_active=1 and is_deleted=0 $company_id $buyer_id $year_id_cond $search_wo $category_id_cond";
	}*/
	//echo $sql;

?>
    <div>
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="600" class="rpt_table" >
            <thead>
                <th width="30">SL</th>
                <th width="80">WO No </th>
                <th width="80">Year</th>
                <th width="130">WO Type</th>
                <th width="150">Buyer</th>
                <th width="100">WO Date</th>
            </thead>
        </table>
        <div style="width:618px; overflow-y:scroll; max-height:300px;" id="" align="center">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="600" class="rpt_table" id="list_view" >
            <?
				$i=1;
				$nameArray=sql_select( $sql );
				foreach ($nameArray as $selectResult)
				{
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

					if ($selectResult[csf("type")]==0)
					{
						if ($selectResult[csf("booking_type")]==1 || $selectResult[csf("booking_type")]==2)
						{
							if ($selectResult[csf("is_short")]==1)
							{
								$wo_type="Short";
							}
							else
							{
								$wo_type="Main";
							}
						}
						elseif($selectResult[csf("booking_type")]==4)
						{
							$wo_type="Sample With Order";
						}
					}
					else
					{
						$wo_type="Sample Non Order";
					}
				?>
                    <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $selectResult[csf('id')]; ?>,'<? echo $selectResult[csf('booking_no')]; ?>')">
                        <td width="30" align="center"><? echo $i; ?></td>
                        <td width="80" align="center"><p><? echo $selectResult[csf('booking_no_prefix_num')]; ?></p></td>
                        <td width="80" align="center"><? echo $selectResult[csf('year')]; ?></td>
                        <td width="130"><p><? echo $wo_type; ?></p></td>
                        <td width="150"><p><? echo $buyerArr[$selectResult[csf('buyer_id')]]; ?></p></td>
                        <td  width="100" align="center"><? echo change_date_format($selectResult[csf('booking_date')]); ?></td>
                    </tr>
                <?
                	$i++;
				}
			?>
            </table>
        </div>
	</div>
	<?
	exit();
}

if($action=="report_generate_pre")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$company_id=str_replace("'","",$cbo_company_id);
	$cbo_buyer_name=str_replace("'","",$cbo_buyer_name);
	$date_from=str_replace("'","",$txt_date_from);
	$date_to=str_replace("'","",$txt_date_to);
	$job_no=str_replace("'","",$txt_job_no);
	$order_no=str_replace("'","",$txt_order_no);
	$cbo_job_year_id=str_replace("'","",$cbo_job_year_id);
	$txt_booking_no=str_replace("'","",trim($txt_booking_no));
	$txt_int_ref=str_replace("'","",trim($txt_int_ref));
	$txt_style_no=str_replace("'","",trim($txt_style_no));
	$txt_style_id=str_replace("'","",trim($txt_style_id));


	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name");
	$supplier=return_library_array( "select id, short_name from lib_supplier",'id','short_name');
	$yarn_count=return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');
	$color_arr=return_library_array( "select id,color_name from lib_color","id","color_name");
    $user_name = return_library_array("select id, user_name from user_passwd", 'id', 'user_name');
    $buyer_arr = return_library_array("select id, buyer_name from lib_buyer", 'id', 'buyer_name');
    $brand_arr = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');
	$comments_acceptance_arr = array(1=>'Acceptable',2=>'Special',3=>'Consideration',4=>'Not Acceptable');
	//--------------------------------------------------------------------------------------------------------------------

	if(str_replace("'","",$cbo_buyer_name)==0)
	{
		$byr_id = get_users_buyer();
		if($byr_id != '')
		{
			$buyer_id_cond = " AND B.CUSTOMER_BUYER IN (".$byr_id.")";
		}
	}
	else
	{
		$buyer_id_cond=" AND B.CUSTOMER_BUYER = ".$cbo_buyer_name;
	}

	if ($job_no=="")
		$job_no_cond="";
	else
		$job_no_cond=" AND B.JOB_NO_PREFIX_NUM IN ('".$job_no."')";

	if($order_no=="")
	{
		$po_cond="";
	}
	else
	{
		if(str_replace("'","",$hide_order_id)!="")
		{
			$po_id=str_replace("'","",$hide_order_id);
			$po_cond="and b.id in(".$po_id.")";
		}
		else
		{
			$po_number=trim($order_no)."%";
			$po_cond="and b.po_number like '$po_number'";
		}
	}

	$start_date_db=change_date_format(str_replace("'","",$txt_date_from),"yyyy-mm-dd","");
	$start_date=change_date_format(str_replace("'","",$txt_date_from),"","",1);
	$end_date=change_date_format(str_replace("'","",$txt_date_to),"","",1);
	if($start_date=="" && $end_date=="")
	{
		$date_cond="";
	}
	else
	{
		$date_cond=" AND A.ALLOCATION_DATE BETWEEN '".$start_date."' and '".$end_date."'";
	}

	if($db_type==0) $lead_day="DATEDIFF(b.pub_shipment_date,b.po_received_date) as  date_diff";
	else if($db_type==2) $lead_day="(b.pub_shipment_date-b.po_received_date) as  date_diff";

	$prod_data=sql_select( "select id, product_name_details, supplier_id, lot, yarn_count_id, yarn_type, yarn_comp_type1st,	yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, color, brand from product_details_master where item_category_id=1");
	$prod_data_arr=array();
	foreach($prod_data as $row)
	{
		$compos="";
		if($row[csf('yarn_comp_percent2nd')]!=0)
		{
			$compos=$composition[$row[csf('yarn_comp_type1st')]]." ".$row[csf('yarn_comp_percent1st')]." %"." ".$composition[$row[csf('yarn_comp_type2nd')]]." ".$row[csf('yarn_comp_percent2nd')]." %";
		}
		else
		{
			$compos=$composition[$row[csf('yarn_comp_type1st')]]." ".$row[csf('yarn_comp_percent1st')]." %"." ".$composition[$row[csf('yarn_comp_type2nd')]];
		}

		$prod_data_arr[$row[csf('id')]]['prod_details']=$compos;
		$prod_data_arr[$row[csf('id')]]['supp']=$row[csf('supplier_id')];
		$prod_data_arr[$row[csf('id')]]['lot']=$row[csf('lot')];
		$prod_data_arr[$row[csf('id')]]['yarn_count_id']=$row[csf('yarn_count_id')];
		$prod_data_arr[$row[csf('id')]]['yarn_type']=$row[csf('yarn_type')];
		$prod_data_arr[$row[csf('id')]]['color']=$row[csf('color')];
		$prod_data_arr[$row[csf('id')]]['brand']=$row[csf('brand')];

	}

	if($db_type==0) $year_field="YEAR(b.insert_date)";
	else if($db_type==2) $year_field="to_char(b.insert_date,'YYYY')";
	if($cbo_job_year_id) $year_field_cond = " and $year_field = '$cbo_job_year_id'";

	if ($txt_booking_no=="") $booking_cond="";
	else $booking_cond=" and a.booking_no like '%$txt_booking_no%' ";

	if($txt_int_ref !="")
	{
		$int_ref_cond=" and GROUPING like '%$txt_int_ref%' ";
		$sql_grouping = "SELECT JOB_NO_MST as JOB_NO, GROUPING as INT_REF FROM WO_PO_BREAK_DOWN WHERE STATUS_ACTIVE=1 AND IS_DELETED=0 ".$int_ref_cond."  group by JOB_NO_MST,GROUPING";
		//echo $sql_grouping;
		$sql_grouping_result=sql_select($sql_grouping);
		$jo_no_chk = array();
		$job_no_arr = array();
		foreach ($sql_grouping_result as $row)
		{
			if($jo_no_chk[$row['JOB_NO']] == "")
			{
				$jo_no_chk[$row['JOB_NO']] = $row['JOB_NO'];
				array_push($job_no_arr,$row['JOB_NO']);
			}
		}

		if(!empty($job_no_arr))
		{
			$po_job_no_cond = "".where_con_using_array($job_no_arr,1,'B.PO_JOB_NO')."";
		}
	}

	if($txt_style_no !="")
	{
		$style = explode(",", $txt_style_no);
		$style_cond = "and (B.STYLE_REF_NO like '%".$style[0]."%'";
		if(count($style)>1)
		{
			for($i=1; $i<count($style); $i++)
			{
				$style_cond .= "or B.STYLE_REF_NO like '%".$style[$i]."%'";
			}
		}
		$style_cond .= ")";
		// echo "<pre>";
		// print_r($style); die;
	}

	$sql = "SELECT A.ID, A.MST_ID, A.JOB_NO, TO_CHAR(A.INSERT_DATE,'YYYY') AS YEAR, A.BOOKING_NO, A.ITEM_CATEGORY, A.ITEM_ID, SUM(A.QNTY) AS QNTY, A.STATUS_ACTIVE, A.INSERT_DATE, A.INSERTED_BY, A.REMARKS, B.COMPANY_ID, B.CUSTOMER_BUYER,B.STYLE_REF_NO,B.PO_JOB_NO, B.BOOKING_ID FROM INV_MAT_ALLOCATION_MST_LOG A, FABRIC_SALES_ORDER_MST B WHERE A.JOB_NO = B.JOB_NO AND A.BOOKING_NO = B.SALES_BOOKING_NO AND B.COMPANY_ID in (".$company_id.")".$buyer_id_cond.$job_no_cond.$booking_cond.$date_cond.$po_job_no_cond.$year_field_cond.$style_cond." GROUP BY A.ID, A.MST_ID, A.JOB_NO, A.BOOKING_NO, A.ITEM_CATEGORY, A.ITEM_ID, A.STATUS_ACTIVE, A.INSERT_DATE, A.INSERTED_BY, A.REMARKS, B.COMPANY_ID, B.CUSTOMER_BUYER, B.STYLE_REF_NO, B.PO_JOB_NO, B.BOOKING_ID ORDER BY A.JOB_NO,A.ITEM_ID ASC";

	// echo $sql; die;

	$data_result=sql_select($sql);
	$prod_arr = array();

	$booking_id_arr = array();
	foreach ($data_result as $key => $value)
	{
		$item_id_row_marge[$value['JOB_NO']][$value['ITEM_ID']]++;
		$prod_arr[$value['ITEM_ID']] = $value['ITEM_ID'];
		$booking_id_arr[$value['BOOKING_ID']] = $value['BOOKING_ID'];
	}

	$con = connect();
	execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from in (1,2) and ENTRY_FORM = 146");
	oci_commit($con);
	disconnect($con);


	$prod_arr = array_filter($prod_arr);
	if(!empty($prod_arr))
	{
		fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 146, 1,$prod_arr, $empty_arr); //recv id
		//die;
		$sql_yrn_test = "SELECT a.id, c.comments_knit_acceptance, b.yarn_quality_coments, b.lot_number from product_details_master a, inv_yarn_test_mst b, inv_yarn_test_comments c, gbl_temp_engine d where a.id = b.prod_id and b.id = c.mst_table_id and a.company_id = b.company_id and a.company_id in (".$company_id.") and a.item_category_id = 1 and a.status_active = 1 and a.is_deleted = 0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  and a.id=d.ref_val and d.user_id=$user_id and d.entry_form=146 and d.ref_from=1 ";
		//echo $sql_yrn_test;die;
		$sql_yrn_test_rslt = sql_select($sql_yrn_test);
		$yrn_test_data = array();
		foreach($sql_yrn_test_rslt as $row)
		{
			$yrn_test_data[$row[csf('id')]]['comments_knit_acceptance'] = $row[csf('comments_knit_acceptance')];
			$yrn_test_data[$row[csf('id')]]['yarn_quality_coments'] = $row[csf('yarn_quality_coments')];
			$yrn_test_data[$row[csf('id')]]['lot_number'] = $row[csf('lot_number')];
		}
	}

	$booking_id_arr = array_filter($booking_id_arr);
	if(!empty($booking_id_arr))
	{
		fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 146, 2,$booking_id_arr, $empty_arr); //recv id
		//die;
		$sql_grouping = "SELECT C.JOB_NO_MST as JOB_NO, C.GROUPING as INT_REF 
		FROM GBL_TEMP_ENGINE D, WO_PO_BREAK_DOWN C, WO_BOOKING_DTLS B,WO_BOOKING_MST A 
		WHERE D.USER_ID=$user_id AND D.ENTRY_FORM=146 AND D.REF_FROM=2 and A.ID=D.REF_VAL AND C.ID=B.PO_BREAK_DOWN_ID and B.BOOKING_MST_ID=A.ID AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0";
		$sql_grouping_result=sql_select($sql_grouping);
		$int_ref_info_arr = array();
		foreach($sql_grouping_result as $row)
		{
			$int_ref_info_arr[$row['JOB_NO']]['INT_REF'] = $row['INT_REF'];
		}
	}


	$con = connect();
	execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from in (1,2) and ENTRY_FORM=146");
	oci_commit($con);
	disconnect($con);

	ob_start();
	$tot_row = 19;
	?>
	<div align="center">
	    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="2132" class="rpt_table" align="left" id="table_header">
	        <thead>

	        	<tr class="form_caption" style="border:none;">
	        	    <td align="center" style="border:none; font-size:18px;" colspan="<? echo $tot_row; ?>">
	        	        <? echo $company_library[$company_id]; ?>
	        	    </td>
	        	</tr>
	        	<tr class="form_caption" style="border:none;">
	        	    <td align="center" style="border:none;font-size:16px; font-weight:bold"  colspan="<? echo $tot_row; ?>"> <? echo $report_title ;?></td>
	        	</tr>
	        	<tr class="form_caption" style="border:none;">
	        	    <td align="center" style="border:none;font-size:12px; font-weight:bold"  colspan="<? echo $tot_row; ?>"> <? if($start_date!="" && $end_date!="") echo "From ".change_date_format($start_date)." To ".change_date_format($end_date);?></td>
	        	</tr>
	        	<tr>
		            <th width="30"><p>SL</p></th>
		            <th width="100"><p>Buyer</p></th>
					<th width="100"><p>Style Ref.</p></th>
		            <th width="120"><p>Sales Order No</p></th>
		            <th width="120"><p>Internal Ref</p></th>
		            <th width="60"><p>Year</p></th>
		            <th width="120"><p>Sales/Booking No</p></th>
		            <th width="100"><p>Product ID</p></th>
		            <th width="100" style="word-wrap: break-word;">Yarn Quality Comments</th>
		            <th width="100"><p>Count</p></th>
		            <th width="130"><p>Composition</p></th>
		            <th width="100"><p>Yarn Type</p></th>
		            <th width="100"><p>Color</p></th>
		            <th width="70"><p>Lot</p></th>
		            <th width="70"><p>Brand</p></th>
		            <th width="110"><p>Supplier</p></th>
		            <th width="100">Quantity (kg)</th>
		            <th width="150"><p>Allocation Date & Time</p></th>
		            <th width="100"><p>User ID</p></th>
		            <th width="100"><p>Status</p></th>
		            <th width=""><p>Remarks</p></th>
	        	</tr>
	        </thead>
	    </table>
	    <div style="width:2150; max-height:320px; overflow-y:scroll;float: left;"  id="scroll_body" align="left">
		    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="2132" class="rpt_table" id="tbl_list_search" align="left">
				<?
		        $i=1;
			
		        foreach( $data_result as $row)
		        {
					//if($i%2==0) $bgcolor="#E9F3FF";
					//else $bgcolor="#FFFFFF";

					$bgcolor="#FFFFFF";
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
						<?
						if($item_id_marge_check[$row['JOB_NO']][$row['ITEM_ID']]=="")
						{
							$item_id_marge_check[$row['JOB_NO']][$row['ITEM_ID']]=$row['ITEM_ID'];
							$yarn_test = $comments_acceptance_arr[$yrn_test_data[$row['ITEM_ID']]['comments_knit_acceptance']];

							$rspn = $item_id_row_marge[$row['JOB_NO']][$row['ITEM_ID']];
							?>
			                <td rowspan="<? echo $rspn;?>" width="30" style="word-wrap: break-word;word-break: break-all;" valign="middle" align="center"><? echo $i; ?></td>
			                <td rowspan="<? echo $rspn;?>" width="100" style="word-wrap: break-word;word-break: break-all;" valign="middle" align="center"><? echo $buyer_arr[$row['CUSTOMER_BUYER']]; ?>&nbsp;</td>
							<td rowspan="<? echo $rspn;?>" width="100" style="word-wrap: break-word;word-break: break-all;" valign="middle" align="center"><? echo $row['STYLE_REF_NO']; ?></td>
			                <td rowspan="<? echo $rspn;?>" width="120" style="word-wrap: break-word;word-break: break-all;" valign="middle" align="center"><? echo $row['JOB_NO']; ?></td>
			                <td rowspan="<? echo $rspn;?>" width="120" style="word-wrap: break-word;word-break: break-all;" valign="middle" align="center"><? echo $int_ref_info_arr[$row['PO_JOB_NO']]['INT_REF'];; ?></td>
			                <td rowspan="<? echo $rspn;?>" width="60" style="word-wrap: break-word;word-break: break-all;" valign="middle" align="center"><? echo $row['YEAR']; ?></td>
			                <td rowspan="<? echo $rspn;?>" width="120" style="word-wrap: break-word;word-break: break-all;" valign="middle" align="center"><? echo $row['BOOKING_NO']; ?></td>
		                	<td rowspan="<? echo $rspn;?>" width="100" style="word-wrap: break-word;word-break: break-all;" valign="middle" align="center"><? echo $row['ITEM_ID']; ?></td>
                            <?
                            if($yrn_test_data[$row['ITEM_ID']]['yarn_quality_coments'] != '')
							{
								?>
								<td rowspan="<? echo $rspn;?>" width="100" style="word-wrap: break-word;word-break: break-all;" valign="middle" align="center" onClick="show_test_report(event);"><a href='##'><? echo $yarn_test; ?></a><input type="hidden" value='<? echo $company_id.'*'.$row['ITEM_ID']?>'></td>
								<?
							}
                            else if($yrn_test_data[$row['ITEM_ID']]['lot_number'] != '')
							{
								?>
								<td rowspan="<? echo $rspn;?>" width="100" style="word-wrap: break-word;word-break: break-all;" valign="middle" align="center" onClick="show_test_report2(event);"><p><a href='##'><? echo $yarn_test; ?></a></p><input type="hidden" value='<? echo $company_id.'*'.$row['ITEM_ID']?>'></td>
								<?
							}
							else
							{
								?>
                                <td rowspan="<? echo $rspn;?>" width="100" style="word-wrap: break-word;word-break: break-all;" valign="middle" align="center"></td>
                                <?
							}
			                $i++;
						}

						$date = date_create($row['INSERT_DATE']);
						$date_time = date_format($date,"d-m-Y h:i:s a");
		                ?>
		                <td width="100" style="word-wrap: break-word;word-break: break-all;" valign="middle" align="center"><? echo $yarn_count[$prod_data_arr[$row['ITEM_ID']]['yarn_count_id']]; ?></td>
		                <td width="130" style="word-wrap: break-word;word-break: break-all;" valign="middle" align="center"><? echo $prod_data_arr[$row['ITEM_ID']]['prod_details']; ?></td>
		                <td width="100" style="word-wrap: break-word;word-break: break-all;" valign="middle" align="center"><? echo $yarn_type[$prod_data_arr[$row['ITEM_ID']]['yarn_type']]; ?></td>
		                <td width="100" style="word-wrap: break-word;word-break: break-all;" valign="middle" align="center"><? echo $color_arr[$prod_data_arr[$row['ITEM_ID']]['color']]; ?></td>
		                <td width="70" style="word-wrap: break-word;word-break: break-all;" valign="middle" align="center"><? echo $prod_data_arr[$row['ITEM_ID']]['lot']; ?></td>
						<td width="70" style="word-wrap: break-word;word-break: break-all;" valign="middle" align="center"><? echo $brand_arr[$prod_data_arr[$row['ITEM_ID']]['brand']]; ?></td>
		                <td width="110" style="word-wrap: break-word;word-break: break-all;" valign="middle" align="center"><? echo $supplier[$prod_data_arr[$row['ITEM_ID']]['supp']]; ?></td>
		                <td width="100" valign="middle" align="right">
		                	<?
		                	echo $row['QNTY'];
		                
		                	?>
		                </td>
		                <td width="150" style="word-wrap: break-word;word-break: break-all;" valign="middle" align="center"><? echo $date_time; ?>&nbsp;</td>
		                <td width="100" style="word-wrap: break-word;word-break: break-all;" valign="middle" align="center"><? echo $user_name[$row[csf('inserted_by')]]; ?>&nbsp;</td>
		                <td width="100" style="word-wrap: break-word;word-break: break-all;" valign="middle" align="center">
		                	<?
		                		$status = $row['STATUS_ACTIVE'];
		                		echo ($status == 1) ? "Active": "Delete";
		                	?>
		                </td>
		                <td width="" style="word-wrap: break-word;word-break: break-all;" valign="middle" align="center"><? echo $row['REMARKS']; ?>&nbsp;</td>
					</tr>
					<?
						$total_allocation_qty += $row['QNTY'];
		        }
		        ?>
		    </table>
	    </div>
		<table width="2132" border="1" cellpadding="2" cellspacing="0" class="rpt_table" style="font:'Arial Narrow'; font-size:14px" rules="all" id="">
            <tfoot>
                <tr valign="middle">
					<th width="30">&nbsp;</th>
		            <th width="100">&nbsp;</th>
					<th width="100">&nbsp;</th>
		            <th width="120">&nbsp;</th>
		            <th width="120">&nbsp;</th>
		            <th width="60">&nbsp;</th>
		            <th width="120">&nbsp;</th>
		            <th width="100">&nbsp;</th>
		            <th width="100">&nbsp;</th>
		            <th width="100">&nbsp;</th>
		            <th width="130">&nbsp;</th>
		            <th width="100">&nbsp;</th>
		            <th width="100">&nbsp;</th>
		            <th width="70">&nbsp;</th>
		            <th width="70">&nbsp;</th>
		            <th width="110">Total : </th>
		            <th width="100" id="total_allocation_qnty"><? echo number_format($total_allocation_qty,2,".",""); ?>&nbsp;</th>
		            <th width="150">&nbsp;</th>
		            <th width="100">&nbsp;</th>
		            <th width="100">&nbsp;</th>
		            <th width="">&nbsp;</th>
                </tr>
            </tfoot>
        </table>
	</div>
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
	$filename="".$user_id."_".$name.".xls";
	echo "$total_data####$filename";
	exit();
}

if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$company_id=str_replace("'","",$cbo_company_id);
	$cbo_buyer_name=str_replace("'","",$cbo_buyer_name);
	$date_from=str_replace("'","",$txt_date_from);
	$date_to=str_replace("'","",$txt_date_to);
	$job_no=str_replace("'","",$txt_job_no);
	$order_no=str_replace("'","",$txt_order_no);
	$cbo_job_year_id=str_replace("'","",$cbo_job_year_id);
	$txt_booking_no=str_replace("'","",trim($txt_booking_no));
	$txt_int_ref=str_replace("'","",trim($txt_int_ref));
	$txt_style_no=str_replace("'","",trim($txt_style_no));
	$txt_style_id=str_replace("'","",trim($txt_style_id));


	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name");
	$supplier=return_library_array( "select id, short_name from lib_supplier",'id','short_name');
	$yarn_count=return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');
	$color_arr=return_library_array( "select id,color_name from lib_color","id","color_name");
    $user_name = return_library_array("select id, user_name from user_passwd", 'id', 'user_name');
    $buyer_arr = return_library_array("select id, buyer_name from lib_buyer", 'id', 'buyer_name');
    $brand_arr = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');
	$comments_acceptance_arr = array(1=>'Acceptable',2=>'Special',3=>'Consideration',4=>'Not Acceptable');
	//--------------------------------------------------------------------------------------------------------------------

	if(str_replace("'","",$cbo_buyer_name)==0)
	{
		$byr_id = get_users_buyer();
		if($byr_id != '')
		{
			$buyer_id_cond = " AND B.CUSTOMER_BUYER IN (".$byr_id.")";
		}
	}
	else
	{
		$buyer_id_cond=" AND B.CUSTOMER_BUYER = ".$cbo_buyer_name;
	}

	if ($job_no=="")
		$job_no_cond="";
	else
		$job_no_cond=" AND B.JOB_NO_PREFIX_NUM IN ('".$job_no."')";

	if($order_no=="")
	{
		$po_cond="";
	}
	else
	{
		if(str_replace("'","",$hide_order_id)!="")
		{
			$po_id=str_replace("'","",$hide_order_id);
			$po_cond="and b.id in(".$po_id.")";
		}
		else
		{
			$po_number=trim($order_no)."%";
			$po_cond="and b.po_number like '$po_number'";
		}
	}

	$start_date_db=change_date_format(str_replace("'","",$txt_date_from),"yyyy-mm-dd","");
	$start_date=change_date_format(str_replace("'","",$txt_date_from),"","",1);
	$end_date=change_date_format(str_replace("'","",$txt_date_to),"","",1);
	if($start_date=="" && $end_date=="")
	{
		$date_cond="";
	}
	else
	{
		$date_cond=" AND A.ALLOCATION_DATE BETWEEN '".$start_date."' and '".$end_date."'";
	}

	if($db_type==0) $lead_day="DATEDIFF(b.pub_shipment_date,b.po_received_date) as  date_diff";
	else if($db_type==2) $lead_day="(b.pub_shipment_date-b.po_received_date) as  date_diff";

	$prod_data=sql_select( "select id, product_name_details, supplier_id, lot, yarn_count_id, yarn_type, yarn_comp_type1st,	yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, color, brand from product_details_master where item_category_id=1");
	$prod_data_arr=array();
	foreach($prod_data as $row)
	{
		$compos="";
		if($row[csf('yarn_comp_percent2nd')]!=0)
		{
			$compos=$composition[$row[csf('yarn_comp_type1st')]]." ".$row[csf('yarn_comp_percent1st')]." %"." ".$composition[$row[csf('yarn_comp_type2nd')]]." ".$row[csf('yarn_comp_percent2nd')]." %";
		}
		else
		{
			$compos=$composition[$row[csf('yarn_comp_type1st')]]." ".$row[csf('yarn_comp_percent1st')]." %"." ".$composition[$row[csf('yarn_comp_type2nd')]];
		}

		$prod_data_arr[$row[csf('id')]]['prod_details']=$compos;
		$prod_data_arr[$row[csf('id')]]['supp']=$row[csf('supplier_id')];
		$prod_data_arr[$row[csf('id')]]['lot']=$row[csf('lot')];
		$prod_data_arr[$row[csf('id')]]['yarn_count_id']=$row[csf('yarn_count_id')];
		$prod_data_arr[$row[csf('id')]]['yarn_type']=$row[csf('yarn_type')];
		$prod_data_arr[$row[csf('id')]]['color']=$row[csf('color')];
		$prod_data_arr[$row[csf('id')]]['brand']=$row[csf('brand')];

	}

	if($db_type==0) $year_field="YEAR(b.insert_date)";
	else if($db_type==2) $year_field="to_char(b.insert_date,'YYYY')";
	if($cbo_job_year_id) $year_field_cond = " and $year_field = '$cbo_job_year_id'";

	if ($txt_booking_no=="") $booking_cond="";
	else $booking_cond=" and a.booking_no like '%$txt_booking_no%' ";

	if($txt_int_ref !="")
	{
		$int_ref_cond=" and GROUPING like '%$txt_int_ref%' ";
		$sql_grouping = "SELECT JOB_NO_MST as JOB_NO, GROUPING as INT_REF FROM WO_PO_BREAK_DOWN WHERE STATUS_ACTIVE=1 AND IS_DELETED=0 ".$int_ref_cond."  group by JOB_NO_MST,GROUPING";
		//echo $sql_grouping;
		$sql_grouping_result=sql_select($sql_grouping);
		$jo_no_chk = array();
		$job_no_arr = array();
		foreach ($sql_grouping_result as $row)
		{
			if($jo_no_chk[$row['JOB_NO']] == "")
			{
				$jo_no_chk[$row['JOB_NO']] = $row['JOB_NO'];
				array_push($job_no_arr,$row['JOB_NO']);
			}
		}

		if(!empty($job_no_arr))
		{
			$po_job_no_cond = "".where_con_using_array($job_no_arr,1,'B.PO_JOB_NO')."";
		}
	}

	if($txt_style_no !="")
	{
		$style = explode(",", $txt_style_no);
		$style_cond = "and (B.STYLE_REF_NO like '%".$style[0]."%'";
		if(count($style)>1)
		{
			for($i=1; $i<count($style); $i++)
			{
				$style_cond .= "or B.STYLE_REF_NO like '%".$style[$i]."%'";
			}
		}
		$style_cond .= ")";
		// echo "<pre>";
		// print_r($style); die;
	}

	$sql = "SELECT A.ID, A.MST_ID, A.JOB_NO, TO_CHAR(A.INSERT_DATE,'YYYY') AS YEAR, A.BOOKING_NO, A.ITEM_CATEGORY, A.ITEM_ID, SUM(A.QNTY) AS QNTY, A.STATUS_ACTIVE, A.INSERT_DATE, A.INSERTED_BY, c.REMARKS, B.COMPANY_ID, B.CUSTOMER_BUYER,B.STYLE_REF_NO,B.PO_JOB_NO, B.BOOKING_ID FROM INV_MAT_ALLOCATION_MST_LOG A left join INV_MATERIAL_ALLOCATION_MST c on a.job_no = c.job_no, FABRIC_SALES_ORDER_MST B WHERE A.JOB_NO = B.JOB_NO AND A.BOOKING_NO = B.SALES_BOOKING_NO AND B.COMPANY_ID in (".$company_id.")".$buyer_id_cond.$job_no_cond.$booking_cond.$date_cond.$po_job_no_cond.$year_field_cond.$style_cond." GROUP BY A.ID, A.MST_ID, A.JOB_NO, A.BOOKING_NO, A.ITEM_CATEGORY, A.ITEM_ID, A.STATUS_ACTIVE, A.INSERT_DATE, A.INSERTED_BY, c.REMARKS, B.COMPANY_ID, B.CUSTOMER_BUYER, B.STYLE_REF_NO, B.PO_JOB_NO, B.BOOKING_ID ORDER BY A.JOB_NO,A.ITEM_ID ASC";

	// echo $sql; die;

	$data_result=sql_select($sql);
	$prod_arr = array();

	$booking_id_arr = array();
	foreach ($data_result as $key => $value)
	{
		$item_id_row_marge[$value['JOB_NO']][$value['ITEM_ID']]++;
		$prod_arr[$value['ITEM_ID']] = $value['ITEM_ID'];
		$booking_id_arr[$value['BOOKING_ID']] = $value['BOOKING_ID'];
	}

	$con = connect();
	execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from in (1,2) and ENTRY_FORM = 146");
	oci_commit($con);
	disconnect($con);


	$prod_arr = array_filter($prod_arr);
	if(!empty($prod_arr))
	{
		fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 146, 1,$prod_arr, $empty_arr); //recv id
		//die;
		$sql_yrn_test = "SELECT a.id, c.comments_knit_acceptance, b.yarn_quality_coments, b.lot_number from product_details_master a, inv_yarn_test_mst b, inv_yarn_test_comments c, gbl_temp_engine d where a.id = b.prod_id and b.id = c.mst_table_id and a.company_id = b.company_id and a.company_id in (".$company_id.") and a.item_category_id = 1 and a.status_active = 1 and a.is_deleted = 0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  and a.id=d.ref_val and d.user_id=$user_id and d.entry_form=146 and d.ref_from=1 ";
		//echo $sql_yrn_test;die;
		$sql_yrn_test_rslt = sql_select($sql_yrn_test);
		$yrn_test_data = array();
		foreach($sql_yrn_test_rslt as $row)
		{
			$yrn_test_data[$row[csf('id')]]['comments_knit_acceptance'] = $row[csf('comments_knit_acceptance')];
			$yrn_test_data[$row[csf('id')]]['yarn_quality_coments'] = $row[csf('yarn_quality_coments')];
			$yrn_test_data[$row[csf('id')]]['lot_number'] = $row[csf('lot_number')];
		}
	}

	$booking_id_arr = array_filter($booking_id_arr);
	if(!empty($booking_id_arr))
	{
		fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 146, 2,$booking_id_arr, $empty_arr); //recv id
		//die;
		$sql_grouping = "SELECT C.JOB_NO_MST as JOB_NO, C.GROUPING as INT_REF FROM WO_BOOKING_MST A, WO_BOOKING_DTLS B, WO_PO_BREAK_DOWN C, GBL_TEMP_ENGINE D WHERE A.ID = B.BOOKING_MST_ID AND B.PO_BREAK_DOWN_ID=C.ID AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0 AND A.ID=D.REF_VAL AND D.USER_ID=$user_id AND D.ENTRY_FORM=146 AND D.REF_FROM=2 GROUP BY C.JOB_NO_MST, C.GROUPING";
		//echo $sql_grouping;die;
		$sql_grouping_result=sql_select($sql_grouping);
		$int_ref_info_arr = array();
		foreach($sql_grouping_result as $row)
		{
			$int_ref_info_arr[$row['JOB_NO']]['INT_REF'] = $row['INT_REF'];
		}
	}


	$con = connect();
	execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from in (1,2) and ENTRY_FORM=146");
	oci_commit($con);
	disconnect($con);

	ob_start();
	$tot_row = 19;
	?>
	<div align="center">
	    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="2132" class="rpt_table" align="left" id="table_header">
	        <thead>

	        	<tr class="form_caption" style="border:none;">
	        	    <td align="center" style="border:none; font-size:18px;" colspan="<? echo $tot_row; ?>">
	        	        <? echo $company_library[$company_id]; ?>
	        	    </td>
	        	</tr>
	        	<tr class="form_caption" style="border:none;">
	        	    <td align="center" style="border:none;font-size:16px; font-weight:bold"  colspan="<? echo $tot_row; ?>"> <? echo $report_title ;?></td>
	        	</tr>
	        	<tr class="form_caption" style="border:none;">
	        	    <td align="center" style="border:none;font-size:12px; font-weight:bold"  colspan="<? echo $tot_row; ?>"> <? if($start_date!="" && $end_date!="") echo "From ".change_date_format($start_date)." To ".change_date_format($end_date);?></td>
	        	</tr>
	        	<tr>
		            <th width="30"><p>SL</p></th>
		            <th width="100"><p>Buyer</p></th>
					<th width="100"><p>Style Ref.</p></th>
		            <th width="120"><p>Sales Order No</p></th>
		            <th width="120"><p>Internal Ref</p></th>
		            <th width="60"><p>Year</p></th>
		            <th width="120"><p>Sales/Booking No</p></th>
		            <th width="100"><p>Product ID</p></th>
		            <th width="100" style="word-wrap: break-word;">Yarn Quality Comments</th>
		            <th width="100"><p>Count</p></th>
		            <th width="130"><p>Composition</p></th>
		            <th width="100"><p>Yarn Type</p></th>
		            <th width="100"><p>Color</p></th>
		            <th width="70"><p>Lot</p></th>
		            <th width="70"><p>Brand</p></th>
		            <th width="110"><p>Supplier</p></th>
		            <th width="100">Quantity (kg)</th>
		            <th width="150"><p>Allocation Date & Time</p></th>
		            <th width="100"><p>User ID</p></th>
		            <th width="100"><p>Status</p></th>
		            <th width=""><p>Remarks</p></th>
	        	</tr>
	        </thead>
	    </table>
	    <div style="width:2150; max-height:320px; overflow-y:scroll;float: left;"  id="scroll_body" align="left">
		    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="2132" class="rpt_table" id="tbl_list_search" align="left">
				<?
		        $i=1;
			
		        foreach( $data_result as $row)
		        {
					//if($i%2==0) $bgcolor="#E9F3FF";
					//else $bgcolor="#FFFFFF";

					$bgcolor="#FFFFFF";
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
						<?$rspn=1;?>
						<td rowspan="<? echo $rspn;?>" width="30" style="word-wrap: break-word;word-break: break-all;" valign="middle" align="center"><? echo $i; ?></td>
						<td rowspan="<? echo $rspn;?>" width="100" style="word-wrap: break-word;word-break: break-all;" valign="middle" align="center"><? echo $buyer_arr[$row['CUSTOMER_BUYER']]; ?>&nbsp;</td>
						<td rowspan="<? echo $rspn;?>" width="100" style="word-wrap: break-word;word-break: break-all;" valign="middle" align="center"><? echo $row['STYLE_REF_NO']; ?></td>
						<td rowspan="<? echo $rspn;?>" width="120" style="word-wrap: break-word;word-break: break-all;" valign="middle" align="center"><? echo $row['JOB_NO']; ?></td>
						<td rowspan="<? echo $rspn;?>" width="120" style="word-wrap: break-word;word-break: break-all;" valign="middle" align="center"><? echo $int_ref_info_arr[$row['PO_JOB_NO']]['INT_REF'];; ?></td>
						<td rowspan="<? echo $rspn;?>" width="60" style="word-wrap: break-word;word-break: break-all;" valign="middle" align="center"><? echo $row['YEAR']; ?></td>
						<td rowspan="<? echo $rspn;?>" width="120" style="word-wrap: break-word;word-break: break-all;" valign="middle" align="center"><? echo $row['BOOKING_NO']; ?></td>
						<td rowspan="<? echo $rspn;?>" width="100" style="word-wrap: break-word;word-break: break-all;" valign="middle" align="center"><? echo $row['ITEM_ID']; ?></td>
						
						
						<?
						if($yrn_test_data[$row['ITEM_ID']]['yarn_quality_coments'] != '')
						{
							?>
							<td rowspan="<? echo $rspn;?>" width="100" style="word-wrap: break-word;word-break: break-all;" valign="middle" align="center" onClick="show_test_report(event);"><a href='##'><? echo $yarn_test; ?></a><input type="hidden" value='<? echo $company_id.'*'.$row['ITEM_ID']?>'></td>
							<?
						}
						else if($yrn_test_data[$row['ITEM_ID']]['lot_number'] != '')
						{
							?>
							<td rowspan="<? echo $rspn;?>" width="100" style="word-wrap: break-word;word-break: break-all;" valign="middle" align="center" onClick="show_test_report2(event);"><p><a href='##'><? echo $yarn_test; ?></a></p><input type="hidden" value='<? echo $company_id.'*'.$row['ITEM_ID']?>'></td>
							<?
						}
						else
						{
							?>
							<td rowspan="<? echo $rspn;?>" width="100" style="word-wrap: break-word;word-break: break-all;" valign="middle" align="center"></td>
							<?
						}
						$date = date_create($row['INSERT_DATE']);
						$date_time = date_format($date,"d-m-Y h:i:s a");
		                ?>
		                <td width="100" style="word-wrap: break-word;word-break: break-all;" valign="middle" align="center"><? echo $yarn_count[$prod_data_arr[$row['ITEM_ID']]['yarn_count_id']]; ?></td>
		                <td width="130" style="word-wrap: break-word;word-break: break-all;" valign="middle" align="center"><? echo $prod_data_arr[$row['ITEM_ID']]['prod_details']; ?></td>
		                <td width="100" style="word-wrap: break-word;word-break: break-all;" valign="middle" align="center"><? echo $yarn_type[$prod_data_arr[$row['ITEM_ID']]['yarn_type']]; ?></td>
		                <td width="100" style="word-wrap: break-word;word-break: break-all;" valign="middle" align="center"><? echo $color_arr[$prod_data_arr[$row['ITEM_ID']]['color']]; ?></td>
		                <td width="70" style="word-wrap: break-word;word-break: break-all;" valign="middle" align="center"><? echo $prod_data_arr[$row['ITEM_ID']]['lot']; ?></td>
						<td width="70" style="word-wrap: break-word;word-break: break-all;" valign="middle" align="center"><? echo $brand_arr[$prod_data_arr[$row['ITEM_ID']]['brand']]; ?></td>
		                <td width="110" style="word-wrap: break-word;word-break: break-all;" valign="middle" align="center"><? echo $supplier[$prod_data_arr[$row['ITEM_ID']]['supp']]; ?></td>
		                <td width="100" valign="middle" align="right">
		                	<?
		                	echo $row['QNTY'];
		                
		                	?>
		                </td>
		                <td width="150" style="word-wrap: break-word;word-break: break-all;" valign="middle" align="center"><? echo $date_time; ?>&nbsp;</td>
		                <td width="100" style="word-wrap: break-word;word-break: break-all;" valign="middle" align="center"><? echo $user_name[$row[csf('inserted_by')]]; ?>&nbsp;</td>
		                <td width="100" style="word-wrap: break-word;word-break: break-all;" valign="middle" align="center">
		                	<?
		                		$status = $row['STATUS_ACTIVE'];
		                		echo ($status == 1) ? "Active": "Delete";
		                	?>
		                </td>
		                <td width="" style="word-wrap: break-word;word-break: break-all;" valign="middle" align="center"><? echo $row['REMARKS']; ?>&nbsp;</td>
					</tr>
					<?
						$total_allocation_qty += $row['QNTY'];
						$i++;
		        }
		        ?>
		    </table>
	    </div>
		<table width="2132" border="1" cellpadding="2" cellspacing="0" class="rpt_table" style="font:'Arial Narrow'; font-size:14px" rules="all" id="">
            <tfoot>
                <tr valign="middle">
					<th width="30">&nbsp;</th>
		            <th width="100">&nbsp;</th>
					<th width="100">&nbsp;</th>
		            <th width="120">&nbsp;</th>
		            <th width="120">&nbsp;</th>
		            <th width="60">&nbsp;</th>
		            <th width="120">&nbsp;</th>
		            <th width="100">&nbsp;</th>
		            <th width="100">&nbsp;</th>
		            <th width="100">&nbsp;</th>
		            <th width="130">&nbsp;</th>
		            <th width="100">&nbsp;</th>
		            <th width="100">&nbsp;</th>
		            <th width="70">&nbsp;</th>
		            <th width="70">&nbsp;</th>
		            <th width="110">Total : </th>
		            <th width="100" id="value_total_allocation_qty"><? //echo number_format($total_allocation_qty,2,".",""); ?>&nbsp;</th>
		            <th width="150">&nbsp;</th>
		            <th width="100">&nbsp;</th>
		            <th width="100">&nbsp;</th>
		            <th width="">&nbsp;</th>
                </tr>
            </tfoot>
        </table>
	</div>
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
	$filename="".$user_id."_".$name.".xls";
	echo "$total_data####$filename";
	exit();
}

if($action=="report_generate_22022022")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$company_id=str_replace("'","",$cbo_company_id);
	$cbo_buyer_name=str_replace("'","",$cbo_buyer_name);
	$date_from=str_replace("'","",$txt_date_from);
	$date_to=str_replace("'","",$txt_date_to);
	$job_no=str_replace("'","",$txt_job_no);
	$order_no=str_replace("'","",$txt_order_no);
	$cbo_job_year_id=str_replace("'","",$cbo_job_year_id);
	$txt_booking_no=str_replace("'","",trim($txt_booking_no));

	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name");
	$supplier=return_library_array( "select id, short_name from lib_supplier",'id','short_name');
	$yarn_count=return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');
	$color_arr=return_library_array( "select id,color_name from lib_color","id","color_name");
    $user_name = return_library_array("select id, user_name from user_passwd", 'id', 'user_name');
    $buyer_arr = return_library_array("select id, buyer_name from lib_buyer", 'id', 'buyer_name');
	$comments_acceptance_arr = array(1=>'Acceptable',2=>'Special',3=>'Consideration',4=>'Not Acceptable');
	//--------------------------------------------------------------------------------------------------------------------

	if(str_replace("'","",$cbo_buyer_name)==0)
	{
		$byr_id = get_users_buyer();
		if($byr_id != '')
		{
			$buyer_id_cond = " AND B.CUSTOMER_BUYER IN (".$byr_id.")";
		}
	}
	else
	{
		$buyer_id_cond=" AND B.CUSTOMER_BUYER = ".$cbo_buyer_name;
	}

	if ($job_no=="")
		$job_no_cond="";
	else
		$job_no_cond=" AND B.JOB_NO_PREFIX_NUM IN ('".$job_no."')";

	if($order_no=="")
	{
		$po_cond="";
	}
	else
	{
		if(str_replace("'","",$hide_order_id)!="")
		{
			$po_id=str_replace("'","",$hide_order_id);
			$po_cond="and b.id in(".$po_id.")";
		}
		else
		{
			$po_number=trim($order_no)."%";
			$po_cond="and b.po_number like '$po_number'";
		}
	}

	$start_date_db=change_date_format(str_replace("'","",$txt_date_from),"yyyy-mm-dd","");
	$start_date=change_date_format(str_replace("'","",$txt_date_from),"","",1);
	$end_date=change_date_format(str_replace("'","",$txt_date_to),"","",1);
	if($start_date=="" && $end_date=="")
	{
		$date_cond="";
	}
	else
	{
		$date_cond=" AND A.ALLOCATION_DATE BETWEEN '".$start_date."' and '".$end_date."'";
	}

	if($db_type==0) $lead_day="DATEDIFF(b.pub_shipment_date,b.po_received_date) as  date_diff";
	else if($db_type==2) $lead_day="(b.pub_shipment_date-b.po_received_date) as  date_diff";

	$prod_data=sql_select( "select id, product_name_details, supplier_id, lot, yarn_count_id, yarn_type, yarn_comp_type1st,	yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, color from product_details_master where item_category_id=1");
	$prod_data_arr=array();
	foreach($prod_data as $row)
	{
		$compos="";
		if($row[csf('yarn_comp_percent2nd')]!=0)
		{
			$compos=$composition[$row[csf('yarn_comp_type1st')]]." ".$row[csf('yarn_comp_percent1st')]." %"." ".$composition[$row[csf('yarn_comp_type2nd')]]." ".$row[csf('yarn_comp_percent2nd')]." %";
		}
		else
		{
			$compos=$composition[$row[csf('yarn_comp_type1st')]]." ".$row[csf('yarn_comp_percent1st')]." %"." ".$composition[$row[csf('yarn_comp_type2nd')]];
		}

		$prod_data_arr[$row[csf('id')]]['prod_details']=$compos;
		$prod_data_arr[$row[csf('id')]]['supp']=$row[csf('supplier_id')];
		$prod_data_arr[$row[csf('id')]]['lot']=$row[csf('lot')];
		$prod_data_arr[$row[csf('id')]]['yarn_count_id']=$row[csf('yarn_count_id')];
		$prod_data_arr[$row[csf('id')]]['yarn_type']=$row[csf('yarn_type')];
		$prod_data_arr[$row[csf('id')]]['color']=$row[csf('color')];

	}
	if($db_type==0) $year_field="YEAR(c.insert_date)";
	else if($db_type==2) $year_field="to_char(c.insert_date,'YYYY')";
	if($cbo_job_year_id) $year_field_cond = " and $year_field = '$cbo_job_year_id'";

	if ($txt_booking_no=="") $booking_cond="";
	else $booking_cond=" and a.booking_no like '%$txt_booking_no%' ";

	$sql = "SELECT A.ID, A.MST_ID, A.JOB_NO, TO_CHAR(A.INSERT_DATE,'YYYY') AS YEAR, A.BOOKING_NO, A.ITEM_CATEGORY, A.ITEM_ID, SUM(A.QNTY) AS QNTY, A.STATUS_ACTIVE, A.INSERT_DATE, A.INSERTED_BY, A.REMARKS, B.COMPANY_ID, B.CUSTOMER_BUYER FROM INV_MAT_ALLOCATION_MST_LOG A, FABRIC_SALES_ORDER_MST B WHERE A.JOB_NO = B.JOB_NO AND A.BOOKING_NO = B.SALES_BOOKING_NO AND B.COMPANY_ID = ".$company_id.$buyer_id_cond.$job_no_cond.$booking_cond.$date_cond." GROUP BY A.ID, A.MST_ID, A.JOB_NO, A.BOOKING_NO, A.ITEM_CATEGORY, A.ITEM_ID, A.STATUS_ACTIVE, A.INSERT_DATE, A.INSERTED_BY, A.REMARKS, B.COMPANY_ID, B.CUSTOMER_BUYER ORDER BY A.ITEM_ID ASC";
	//echo $sql; die;
	$data_result=sql_select($sql);
	$prod_arr = array();
	foreach ($data_result as $key => $value)
	{
		$item_id_row_marge[$value['ITEM_ID']]++;
		$prod_arr[$value['ITEM_ID']] = $value['ITEM_ID'];
	}

	$con = connect();
	execute_query("DELETE FROM TMP_PROD_ID WHERE USERID = ".$user_id);
	oci_commit($con);

	//for product id
	$con = connect();
	foreach($prod_arr as $key=>$val)
	{
		execute_query("INSERT INTO TMP_PROD_ID(PROD_ID, USERID) VALUES('".$val."', '".$user_id."')");
	}
	oci_commit($con);

	$sql_yrn_test = "select a.id, c.comments_knit_acceptance, b.yarn_quality_coments, b.lot_number from product_details_master a, inv_yarn_test_mst b, inv_yarn_test_comments c, tmp_prod_id d where a.id = b.prod_id and a.company_id = b.company_id and b.id = c.mst_table_id and a.id = d.prod_id and b.prod_id = d.prod_id and a.company_id = ".$company_id." and a.item_category_id = 1 and a.status_active = 1 and a.is_deleted = 0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.userid = ".$user_id;
	//echo $sql_yrn_test;
	$sql_yrn_test_rslt = sql_select($sql_yrn_test);
	$yrn_test_data = array();
	foreach($sql_yrn_test_rslt as $row)
	{
		$yrn_test_data[$row[csf('id')]]['comments_knit_acceptance'] = $row[csf('comments_knit_acceptance')];
		$yrn_test_data[$row[csf('id')]]['yarn_quality_coments'] = $row[csf('yarn_quality_coments')];
		$yrn_test_data[$row[csf('id')]]['lot_number'] = $row[csf('lot_number')];
	}

	ob_start();
	$tot_row = 18;
	?>
	<div align="center">
	    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1842" class="rpt_table" align="left" id="table_header">
	        <thead>

	        	<tr class="form_caption" style="border:none;">
	        	    <td align="center" style="border:none; font-size:18px;" colspan="<? echo $tot_row; ?>">
	        	        <? echo $company_library[$company_id]; ?>
	        	    </td>
	        	</tr>
	        	<tr class="form_caption" style="border:none;">
	        	    <td align="center" style="border:none;font-size:16px; font-weight:bold"  colspan="<? echo $tot_row; ?>"> <? echo $report_title ;?></td>
	        	</tr>
	        	<tr class="form_caption" style="border:none;">
	        	    <td align="center" style="border:none;font-size:12px; font-weight:bold"  colspan="<? echo $tot_row; ?>"> <? if($start_date!="" && $end_date!="") echo "From ".change_date_format($start_date)." To ".change_date_format($end_date);?></td>
	        	</tr>
	        	<tr>
		            <th width="30"><p>SL</p></th>
		            <th width="100"><p>Buyer</p></th>
		            <th width="120"><p>Sales Order No</p></th>
		            <th width="60"><p>Year</p></th>
		            <th width="120"><p>Sales/Booking No</p></th>
		            <th width="100"><p>Product ID</p></th>
		            <th width="100" style="word-wrap: break-word;">Yarn Quality Comments</th>
		            <th width="100"><p>Count</p></th>
		            <th width="130"><p>Composition</p></th>
		            <th width="100"><p>Yarn Type</p></th>
		            <th width="100"><p>Color</p></th>
		            <th width="70"><p>Lot</p></th>
		            <th width="110"><p>Supplier</p></th>
		            <th width="100">Quantity (kg)</th>
		            <th width="150"><p>Allocation Date & Time</p></th>
		            <th width="100"><p>User ID</p></th>
		            <th width="100"><p>Status</p></th>
		            <th width=""><p>Remarks</p></th>
	        	</tr>
	        </thead>
	    </table>
	    <div style="width:1860px; max-height:320px; overflow-y:scroll;float: left;"  id="scroll_body" align="left">
		    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1842" class="rpt_table" id="tbl_list_search" align="left">
				<?
		        $i=1;
		        foreach( $data_result as $row)
		        {
					//if($i%2==0) $bgcolor="#E9F3FF";
					//else $bgcolor="#FFFFFF";

					$bgcolor="#FFFFFF";
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
						<?
						if($item_id_marge_check[$row['ITEM_ID']]=="")
						{
							$item_id_marge_check[$row['ITEM_ID']]=$row['ITEM_ID'];
							$yarn_test = $comments_acceptance_arr[$yrn_test_data[$row['ITEM_ID']]['comments_knit_acceptance']];
							?>
			                <td rowspan="<? echo $item_id_row_marge[$row['ITEM_ID']];?>" width="30" style="word-wrap: break-word;word-break: break-all;" valign="middle" align="center"><p><? echo $i; ?></p></td>
			                <td rowspan="<? echo $item_id_row_marge[$row['ITEM_ID']];?>" width="100" style="word-wrap: break-word;word-break: break-all;" valign="middle" align="center"><p><? echo $buyer_arr[$row['CUSTOMER_BUYER']]; ?>&nbsp;</p></td>
			                <td rowspan="<? echo $item_id_row_marge[$row['ITEM_ID']];?>" width="120" style="word-wrap: break-word;word-break: break-all;" valign="middle" align="center"><p><? echo $row['JOB_NO']; ?></p></td>
			                <td rowspan="<? echo $item_id_row_marge[$row['ITEM_ID']];?>" width="60" style="word-wrap: break-word;word-break: break-all;" valign="middle" align="center"><p><? echo $row['YEAR']; ?></p></td>
			                <td rowspan="<? echo $item_id_row_marge[$row['ITEM_ID']];?>" width="120" style="word-wrap: break-word;word-break: break-all;" valign="middle" align="center"><p><? echo $row['BOOKING_NO']; ?></p></td>
		                	<td rowspan="<? echo $item_id_row_marge[$row['ITEM_ID']];?>" width="100" style="word-wrap: break-word;word-break: break-all;" valign="middle" align="center"><p><? echo $row['ITEM_ID']; ?></p></td>
                            <?
                            if($yrn_test_data[$row['ITEM_ID']]['yarn_quality_coments'] != '')
							{
								?>
								<td rowspan="<? echo $item_id_row_marge[$row['ITEM_ID']];?>" width="100" style="word-wrap: break-word;word-break: break-all;" valign="middle" align="center" onClick="show_test_report(event);"><p><a href='##'><? echo $yarn_test; ?></a></p><input type="hidden" value='<? echo $company_id.'*'.$row['ITEM_ID']?>'></td>
								<?
							}
                            else if($yrn_test_data[$row['ITEM_ID']]['lot_number'] != '')
							{
								?>
								<td rowspan="<? echo $item_id_row_marge[$row['ITEM_ID']];?>" width="100" style="word-wrap: break-word;word-break: break-all;" valign="middle" align="center" onClick="show_test_report2(event);"><p><a href='##'><? echo $yarn_test; ?></a></p><input type="hidden" value='<? echo $company_id.'*'.$row['ITEM_ID']?>'></td>
								<?
							}
							else
							{
								?>
                                <td rowspan="<? echo $item_id_row_marge[$row['ITEM_ID']];?>" width="100" style="word-wrap: break-word;word-break: break-all;" valign="middle" align="center"></td>
                                <?
							}
			                $i++;
						}

						$date = date_create($row['INSERT_DATE']);
						$date_time = date_format($date,"d-m-Y h:i:s a");
		                ?>
		                <td width="100" style="word-wrap: break-word;word-break: break-all;" valign="middle" align="center"><p><? echo $yarn_count[$prod_data_arr[$row['ITEM_ID']]['yarn_count_id']]; ?></p></td>
		                <td width="130" style="word-wrap: break-word;word-break: break-all;" valign="middle" align="center"><p style="word-break: break-all;"><? echo $prod_data_arr[$row['ITEM_ID']]['prod_details']; ?></p></td>
		                <td width="100" style="word-wrap: break-word;word-break: break-all;" valign="middle" align="center"><p style="word-break: break-all;"><? echo $yarn_type[$prod_data_arr[$row['ITEM_ID']]['yarn_type']]; ?></p></td>
		                <td width="100" style="word-wrap: break-word;word-break: break-all;" valign="middle" align="center"><p style="word-break: break-all;"><? echo $color_arr[$prod_data_arr[$row['ITEM_ID']]['color']]; ?></p></td>
		                <td width="70" style="word-wrap: break-word;word-break: break-all;" valign="middle" align="center"><p><? echo $prod_data_arr[$row['ITEM_ID']]['lot']; ?></p></td>
		                <td width="110" style="word-wrap: break-word;word-break: break-all;" valign="middle" align="center"><p style="word-break: break-all;"><? echo $supplier[$prod_data_arr[$row['ITEM_ID']]['supp']]; ?></p></td>
		                <td width="100" valign="middle" align="right">
		                	<?
		                	echo decimal_format($row['QNTY'], '1', ',');
		                	$total_allocation_qty += decimal_format($row['QNTY'], '1', '');
		                	?>
		                </td>
		                <td width="150" style="word-wrap: break-word;word-break: break-all;" valign="middle" align="center"><p><? echo $date_time; ?>&nbsp;</p></td>
		                <td width="100" style="word-wrap: break-word;word-break: break-all;" valign="middle" align="center"><p><? echo $user_name[$row[csf('inserted_by')]]; ?>&nbsp;</p></td>
		                <td width="100" style="word-wrap: break-word;word-break: break-all;" valign="middle" align="center">
		                	<?
		                		$status = $row['STATUS_ACTIVE'];
		                		echo ($status == 1) ? "Active": "Delete";
		                	?>
		                <p>&nbsp;</p></td>
		                <td width="" style="word-wrap: break-word;word-break: break-all;" valign="middle" align="center"><p><? echo $row['REMARKS']; ?>&nbsp;</p></td>
					</tr>
					<?
		        }
		        ?>
		    </table>
	    </div>
	</div>
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
	$filename="".$user_name."_".$name.".xls";
	echo "$total_data####$filename";
	exit();
}

if($action=="report_generate_30122021")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$company_id=str_replace("'","",$cbo_company_id);
	$cbo_buyer_name=str_replace("'","",$cbo_buyer_name);
	$date_from=str_replace("'","",$txt_date_from);
	$date_to=str_replace("'","",$txt_date_to);
	$job_no=str_replace("'","",$txt_job_no);
	$order_no=str_replace("'","",$txt_order_no);
	$cbo_job_year_id=str_replace("'","",$cbo_job_year_id);
	$txt_booking_no=str_replace("'","",trim($txt_booking_no));

	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name");
	$supplier=return_library_array( "select id, short_name from lib_supplier",'id','short_name');
	$yarn_count=return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');
	$color_arr=return_library_array( "select id,color_name from lib_color","id","color_name");
    $user_name = return_library_array("select id, user_name from user_passwd", 'id', 'user_name');
    $buyer_arr = return_library_array("select id, buyer_name from lib_buyer", 'id', 'buyer_name');
	//--------------------------------------------------------------------------------------------------------------------

	if(str_replace("'","",$cbo_buyer_name)==0)
	{
		$byr_id = get_users_buyer();
		if($byr_id != '')
		{
			$buyer_id_cond = " AND B.CUSTOMER_BUYER IN (".$byr_id.")";
		}
	}
	else
	{
		$buyer_id_cond=" AND B.CUSTOMER_BUYER = ".$cbo_buyer_name;
	}

	if ($job_no=="")
		$job_no_cond="";
	else
		$job_no_cond=" AND B.JOB_NO_PREFIX_NUM IN ('".$job_no."')";

	if($order_no=="")
	{
		$po_cond="";
	}
	else
	{
		if(str_replace("'","",$hide_order_id)!="")
		{
			$po_id=str_replace("'","",$hide_order_id);
			$po_cond="and b.id in(".$po_id.")";
		}
		else
		{
			$po_number=trim($order_no)."%";
			$po_cond="and b.po_number like '$po_number'";
		}
	}

	$start_date_db=change_date_format(str_replace("'","",$txt_date_from),"yyyy-mm-dd","");
	$start_date=change_date_format(str_replace("'","",$txt_date_from),"","",1);
	$end_date=change_date_format(str_replace("'","",$txt_date_to),"","",1);
	if($start_date=="" && $end_date=="")
	{
		$date_cond="";
	}
	else
	{
		$date_cond=" AND A.ALLOCATION_DATE BETWEEN '".$start_date."' and '".$end_date."'";
	}

	if($db_type==0) $lead_day="DATEDIFF(b.pub_shipment_date,b.po_received_date) as  date_diff";
	else if($db_type==2) $lead_day="(b.pub_shipment_date-b.po_received_date) as  date_diff";

	$prod_data=sql_select( "select id, product_name_details, supplier_id, lot, yarn_count_id, yarn_type, yarn_comp_type1st,	yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, color from product_details_master where item_category_id=1");
	$prod_data_arr=array();
	foreach($prod_data as $row)
	{
		$compos="";
		if($row[csf('yarn_comp_percent2nd')]!=0)
		{
			$compos=$composition[$row[csf('yarn_comp_type1st')]]." ".$row[csf('yarn_comp_percent1st')]." %"." ".$composition[$row[csf('yarn_comp_type2nd')]]." ".$row[csf('yarn_comp_percent2nd')]." %";
		}
		else
		{
			$compos=$composition[$row[csf('yarn_comp_type1st')]]." ".$row[csf('yarn_comp_percent1st')]." %"." ".$composition[$row[csf('yarn_comp_type2nd')]];
		}

		$prod_data_arr[$row[csf('id')]]['prod_details']=$compos;
		$prod_data_arr[$row[csf('id')]]['supp']=$row[csf('supplier_id')];
		$prod_data_arr[$row[csf('id')]]['lot']=$row[csf('lot')];
		$prod_data_arr[$row[csf('id')]]['yarn_count_id']=$row[csf('yarn_count_id')];
		$prod_data_arr[$row[csf('id')]]['yarn_type']=$row[csf('yarn_type')];
		$prod_data_arr[$row[csf('id')]]['color']=$row[csf('color')];

	}
	if($db_type==0) $year_field="YEAR(c.insert_date)";
	else if($db_type==2) $year_field="to_char(c.insert_date,'YYYY')";
	if($cbo_job_year_id) $year_field_cond = " and $year_field = '$cbo_job_year_id'";

	if ($txt_booking_no=="") $booking_cond="";
	else $booking_cond=" and a.booking_no like '%$txt_booking_no%' ";

	$sql = "SELECT A.ID, A.MST_ID, A.JOB_NO, TO_CHAR(A.INSERT_DATE,'YYYY') AS YEAR, A.BOOKING_NO, A.ITEM_CATEGORY, A.ITEM_ID, SUM(A.QNTY) AS QNTY, A.STATUS_ACTIVE, A.INSERT_DATE, A.INSERTED_BY, A.REMARKS, B.COMPANY_ID, B.CUSTOMER_BUYER FROM INV_MAT_ALLOCATION_MST_LOG A, FABRIC_SALES_ORDER_MST B WHERE A.JOB_NO = B.JOB_NO AND A.BOOKING_NO = B.SALES_BOOKING_NO AND B.COMPANY_ID = ".$company_id.$buyer_id_cond.$job_no_cond.$booking_cond.$date_cond." GROUP BY A.ID, A.MST_ID, A.JOB_NO, A.BOOKING_NO, A.ITEM_CATEGORY, A.ITEM_ID, A.STATUS_ACTIVE, A.INSERT_DATE, A.INSERTED_BY, A.REMARKS, B.COMPANY_ID, B.CUSTOMER_BUYER ORDER BY A.ITEM_ID ASC";
	//echo $sql; die;
	$data_result=sql_select($sql);
	foreach ($data_result as $key => $value)
	{
		$item_id_row_marge[$value['ITEM_ID']]++;
	}

	ob_start();
	?>
	<div align="center">
	    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1722" class="rpt_table" align="left" id="table_header">
	        <thead>

	        	<tr class="form_caption" style="border:none;">
	        	    <td align="center" style="border:none; font-size:18px;" colspan="16">
	        	        <? echo $company_library[$company_id]; ?>
	        	    </td>
	        	</tr>
	        	<tr class="form_caption" style="border:none;">
	        	    <td align="center" style="border:none;font-size:16px; font-weight:bold"  colspan="16"> <? echo $report_title ;?></td>
	        	</tr>
	        	<tr class="form_caption" style="border:none;">
	        	    <td align="center" style="border:none;font-size:12px; font-weight:bold"  colspan="16"> <? if($start_date!="" && $end_date!="") echo "From ".change_date_format($start_date)." To ".change_date_format($end_date);?></td>
	        	</tr>
	        	<tr>
		            <th width="30"><p>SL</p></th>
		            <th width="100"><p>Buyer</p></th>
		            <th width="100"><p>Sales Order No</p></th>
		            <th width="60"><p>Year</p></th>
		            <th width="120"><p>Sales/Booking No</p></th>
		            <th width="100"><p>Product ID</p></th>
		            <th width="100"><p>Count</p></th>
		            <th width="130"><p>Composition</p></th>
		            <th width="100"><p>Yarn Type</p></th>
		            <th width="100"><p>Color</p></th>
		            <th width="70"><p>Lot</p></th>
		            <th width="110"><p>Supplier</p></th>
		            <th width="100">Quantity (kg)</th>
		            <th width="150"><p>Allocation Date & Time</p></th>
		            <th width="100"><p>User ID</p></th>
		            <th width="100"><p>Status</p></th>
		            <th width=""><p>Remarks</p></th>
	        	</tr>

	        </thead>
	    </table>
	    <div style="width:1740px; max-height:320px; overflow-y:scroll;float: left;"  id="scroll_body" align="left">
		    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1722" class="rpt_table" id="tbl_list_search" align="left">
				<?
		        $i=1;
		        foreach( $data_result as $row)
		        {
					//if($i%2==0) $bgcolor="#E9F3FF";
					//else $bgcolor="#FFFFFF";

					$bgcolor="#FFFFFF";
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
						<?
						if($item_id_marge_check[$row['ITEM_ID']]=="")
						{
							$item_id_marge_check[$row['ITEM_ID']]=$row['ITEM_ID'];

							?>
			                <td rowspan="<? echo $item_id_row_marge[$row['ITEM_ID']];?>" width="30" style="word-wrap: break-word;word-break: break-all;" valign="middle" align="center"><p><? echo $i; ?></p></td>
			                <td rowspan="<? echo $item_id_row_marge[$row['ITEM_ID']];?>" width="100" style="word-wrap: break-word;word-break: break-all;" valign="middle" align="center"><p><? echo $buyer_arr[$row['CUSTOMER_BUYER']]; ?>&nbsp;</p></td>
			                <td rowspan="<? echo $item_id_row_marge[$row['ITEM_ID']];?>" width="100" style="word-wrap: break-word;word-break: break-all;" valign="middle" align="center"><p><? echo $row['JOB_NO']; ?></p></td>
			                <td rowspan="<? echo $item_id_row_marge[$row['ITEM_ID']];?>" width="60" style="word-wrap: break-word;word-break: break-all;" valign="middle" align="center"><p><? echo $row['YEAR']; ?></p></td>
			                <td rowspan="<? echo $item_id_row_marge[$row['ITEM_ID']];?>" width="120" style="word-wrap: break-word;word-break: break-all;" valign="middle" align="center"><p><? echo $row['BOOKING_NO']; ?></p></td>
		                	<td rowspan="<? echo $item_id_row_marge[$row['ITEM_ID']];?>" width="100" style="word-wrap: break-word;word-break: break-all;" valign="middle" align="center"><p><? echo $row['ITEM_ID']; ?></p></td>

			                <?
			                $i++;
						}

						$date = date_create($row['INSERT_DATE']);
						$date_time = date_format($date,"d-m-Y h:i:s a");
		                ?>
		                <td width="100" style="word-wrap: break-word;word-break: break-all;" valign="middle" align="center"><p><? echo $yarn_count[$prod_data_arr[$row['ITEM_ID']]['yarn_count_id']]; ?></p></td>
		                <td width="130" style="word-wrap: break-word;word-break: break-all;" valign="middle" align="center"><p style="word-break: break-all;"><? echo $prod_data_arr[$row['ITEM_ID']]['prod_details']; ?></p></td>
		                <td width="100" style="word-wrap: break-word;word-break: break-all;" valign="middle" align="center"><p style="word-break: break-all;"><? echo $yarn_type[$prod_data_arr[$row['ITEM_ID']]['yarn_type']]; ?></p></td>
		                <td width="100" style="word-wrap: break-word;word-break: break-all;" valign="middle" align="center"><p style="word-break: break-all;"><? echo $color_arr[$prod_data_arr[$row['ITEM_ID']]['color']]; ?></p></td>
		                <td width="70" style="word-wrap: break-word;word-break: break-all;" valign="middle" align="center"><p><? echo $prod_data_arr[$row['ITEM_ID']]['lot']; ?></p></td>
		                <td width="110" style="word-wrap: break-word;word-break: break-all;" valign="middle" align="center"><p style="word-break: break-all;"><? echo $supplier[$prod_data_arr[$row['ITEM_ID']]['supp']]; ?></p></td>
		                <td width="100" valign="middle" align="right">
		                	<?
		                	echo decimal_format($row['QNTY'], '1', ',');
		                	$total_allocation_qty += decimal_format($row['QNTY'], '1', '');
		                	?>
		                </td>
		                <td width="150" style="word-wrap: break-word;word-break: break-all;" valign="middle" align="center"><p><? echo $date_time; ?>&nbsp;</p></td>
		                <td width="100" style="word-wrap: break-word;word-break: break-all;" valign="middle" align="center"><p><? echo $user_name[$row[csf('inserted_by')]]; ?>&nbsp;</p></td>
		                <td width="100" style="word-wrap: break-word;word-break: break-all;" valign="middle" align="center">
		                	<?
		                		$status = $row['STATUS_ACTIVE'];
		                		echo ($status == 1) ? "Active": "Delete";
		                	?>
		                <p>&nbsp;</p></td>
		                <td width="" style="word-wrap: break-word;word-break: break-all;" valign="middle" align="center"><p><? echo $row['REMARKS']; ?>&nbsp;</p></td>
					</tr>
					<?
		        }
		        ?>
		    </table>
	    </div>
	</div>
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
	$filename="".$user_name."_".$name.".xls";
	echo "$total_data####$filename";
	exit();
}
?>