<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');
$user_id=$_SESSION['logic_erp']['user_id'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action==="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 120, "select a.id, a.buyer_name from lib_buyer a, lib_buyer_tag_company b where a.id=b.buyer_id and b.tag_company=$data and a.status_active=1 and a.is_deleted=0 order by a.buyer_name","id,buyer_name", 1, "-- Select buyer --", 0, "","" );
    exit();
}

if ($action==="load_drop_down_location")
{
    extract($_REQUEST);
    $choosenCompany = $choosenCompany;
	echo create_drop_down( "cbo_location_name", 150, "SELECT id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id in($choosenCompany) group by id,location_name  order by location_name","id,location_name", 0, "-- Select location --", $selected, "" );
	exit();
}

if ($action==="load_drop_down_floor")
{
    extract($_REQUEST);
    $choosenLocation = $choosenLocation;
	echo create_drop_down( "cbo_floor_name", 150, "SELECT id,floor_name from lib_prod_floor where location_id in($choosenLocation) and status_active =1 and is_deleted=0 group by id,floor_name order by floor_name","id,floor_name", 0, "-- Select Floor --", $selected, "" );
	exit();
}

if($action==="job_popup")
{
	echo load_html_head_contents("Job Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>

    <script>

		var selected_id   = new Array;
		var selected_name = new Array;

		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}

		function js_set_value( str ) {
			//alert(str);return;
			if (str != '') str=str.split('_');

			toggle( document.getElementById( 'tr_' + str[0] ), '#FFFFCC' );

			if ( jQuery.inArray( str[1], selected_id ) == -1 ) {
				selected_id.push( str[1] );
				selected_name.push( str[2] );
			}
			else
			{
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == str[1] ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
			}
			var id = ''; var name = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + '*';
			}

			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );

			$('#hide_job_id').val( id );
			$('#hide_job_no').val( name );
		}

    </script>
    </head>
    <body>
    <div align="center" style="width:820px;">
        <form name="styleRef_form" id="styleRef_form">
		<fieldset style="width:800px;">
            <table width="800" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
            	<thead>
                    <th>Company</th>
                    <th>Buyer</th>
                    <th>Search By</th>
                    <th id="search_by_td_up" width="170">Please Enter Job No</th>
                    <th><input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;" onClick="reset_form('styleRef_form','search_div','','','','');"></th>
                    <input type="hidden" id="hide_job_id" name="hide_job_id" />
                    <input type="hidden" id="hide_job_no" name="hide_job_no" />
                </thead>
                <tbody>
                	<tr class="general">
                    	<td align="center">
							<?
                                echo create_drop_down( "cbo_company_name", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 order by company_name","id,company_name", 1, "-- Select Company --", $selected, "" );
                            ?>
                        </td>
                        <td align="center">
                        	 <?
								//echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$company_id $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer_name,"",0 );
								echo create_drop_down( "cbo_buyer_name", 140, "select a.id, a.buyer_name from lib_buyer a where a.status_active=1 and a.is_deleted=0 order by a.buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "",0,"" );
							?>
                        </td>
                        <td align="center">
	                    	<?
	                       		$search_by_arr=array(1=>"Job No",2=>"Style Ref");
								$dd="change_search_event(this.value, '0*0', '0*0', '../../') ";
								echo create_drop_down( "cbo_search_by", 130, $search_by_arr,"",0, "--Select--", "", $dd, 0 );
							?>
                        </td>
                        <td align="center" id="search_by_td">
                            <input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />
                        </td>
                        <td align="center">
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view (document.getElementById('cbo_company_name').value+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value, 'create_job_popup_search_list_view', 'search_div', 'ship_date_wise_production_analysis_report_controller', 'setFilterGrid(\'list_view\',-1)');" style="width:100px;" />
                    	</td>
                    </tr>
            	</tbody>
           	</table>
            <div style="margin-top:15px" id="search_div" align="left"></div>
		</fieldset>
	</form>
    </div>
    </body>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit();
}

if ($action==="create_job_popup_search_list_view")
{
	list($company_id, $buyer_id, $search_type, $search_value) = explode('**',$data);
	//echo $company_id.'rakib'.$buyer_id.'rakib'.$search_type.'rakib'.$search_value;
	if($company_id == 0)
	{
		echo "<strong style='color:red'>Please Select Company Name</strong>";
		die;
	}

	$company_arr = return_library_array( "select id, company_name from lib_company where status_active=1 and is_deleted=0", "id", "company_name" );
    $buyer_short_library = return_library_array( "select id,buyer_name from lib_buyer where status_active=1 and is_deleted=0", "id", "buyer_name" );

	if($search_type == 1 && $search_value != ''){
		$search_cond = " and a.job_no like('%$search_value')";
	}
	else if($search_type == 2 && $search_value != ''){
		$search_cond = " and a.style_ref_no like('%$search_value%')";
	}

	if($buyer_id == 0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_cond="";
		}
		else
		{
			$buyer_cond="";
		}
	}
	else
	{
		$buyer_cond=" and a.buyer_name=$buyer_id";
	}

	$sql_po = "SELECT a.ID, b.PO_NUMBER from wo_po_details_master a, wo_po_break_down b
	where a.job_no=b.job_no_mst and a.status_active in(1,2,3) and a.is_deleted=0 and b.status_active in(1,2,3) and b.is_deleted=0 and a.company_name=$company_id $buyer_cond $search_cond";
	$sql_po_res=sql_select($sql_po);
	$po_number_arr=array();
	foreach ($sql_po_res as $row) {
		$po_number_arr[$row['ID']] .= $row['PO_NUMBER'].',';
	}


	$sql= "SELECT a.ID, a.JOB_NO_PREFIX_NUM, a.JOB_NO, a.COMPANY_NAME, a.BUYER_NAME, a.STYLE_REF_NO
	from wo_po_details_master a, wo_po_break_down b
	where a.job_no=b.job_no_mst and a.status_active in(1,2,3) and a.is_deleted=0 and b.status_active in(1,2,3) and b.is_deleted=0 and a.company_name=$company_id $buyer_cond $search_cond
	group by a.ID, a.JOB_NO_PREFIX_NUM, a.JOB_NO, a.COMPANY_NAME, a.BUYER_NAME, a.STYLE_REF_NO
	order by a.id desc";
	//echo $sql;//die;
	$sql_res=sql_select($sql);

	?>
    <table width="800" border="1" rules="all" class="rpt_table">
        <thead>
            <tr>
                <th width="30">SL</th>
                <th width="150">Company</th>
                <th width="120">Buyer</th>
                <th width="120">Job no</th>
                <th width="120">Style</th>
                <th>Po number</th>
            </tr>
       </thead>
    </table>
    <div style="max-height:820px; overflow:auto;">
	    <table id="list_view" width="800" border="1" rules="all" class="rpt_table">
	     <?
	        $i=1;
	        foreach($sql_res as $row)
	        {
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$po_number = implode(',',array_unique(explode(',',$row['PO_NUMBER'])));
				?>
				<tr bgcolor="<?= $bgcolor;?>" id="tr_<?= $i; ?>" onClick="js_set_value('<?= $i; ?>'+'_'+'<?= $row['ID']; ?>'+'_'+'<?= $row['JOB_NO']; ?>')" style="text-decoration:none; cursor:pointer">
	                <td width="30" align="center"><?= $i; ?></td>
	                <td width="150"><p><?= $company_arr[$row['COMPANY_NAME']]; ?></p></td>
	                <td width="120"><p><?= $buyer_short_library[$row['BUYER_NAME']]; ?></p></td>
	                <td width="120"><p><?= $row['JOB_NO']; ?></p></td>
	                <td width="120"><p><?= $row['STYLE_REF_NO']; ?></p></td>
	                <td><p><?= rtrim($po_number_arr[$row['ID']],','); ?></p></td>
				</tr>
				<?
				$i++;
			}
			?>
	    </table>
    </div>
    <div style="width: 820px; text-align: center; padding-top: 5px;">
        <input type="submit" class="formbutton" id="close" style="width:80px" onClick="parent.emailwindow.hide();" value="Close"/>
    </div>
    <?
	exit();
}

if($action==="order_popup")
{
	echo load_html_head_contents("Order Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>

    <script>

		var selected_id   = new Array;
		var selected_name = new Array;

		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}

		function js_set_value( str ) {
			//alert(str);return;
			if (str != '') str=str.split('_');

			toggle( document.getElementById( 'tr_' + str[0] ), '#FFFFCC' );

			if ( jQuery.inArray( str[1], selected_id ) == -1 ) {
				selected_id.push( str[1] );
				selected_name.push( str[2] );
			}
			else
			{
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == str[1] ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
			}
			var id = ''; var name = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + '*';
			}

			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );

			$('#hide_order_id').val( id );
			$('#hide_order_no').val( name );
		}

    </script>
    </head>
    <body>
    <div align="center" style="width:820px;">
        <form name="styleRef_form" id="styleRef_form">
		<fieldset style="width:800px;">
            <table width="800" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
            	<thead>
                    <th>Company</th>
                    <th>Buyer</th>
                    <th>Search By</th>
                    <th id="search_by_td_up" width="170">Please Enter Order No</th>
                    <th><input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;" onClick="reset_form('styleRef_form','search_div','','','','');"></th>
                    <input type="hidden" id="hide_order_id" name="hide_order_id" />
                    <input type="hidden" id="hide_order_no" name="hide_order_no" />
                </thead>
                <tbody>
                	<tr class="general">
                    	<td align="center">
							<?
                                echo create_drop_down( "cbo_company_name", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 order by company_name","id,company_name", 1, "-- Select Company --", $selected, "" );
                            ?>
                        </td>
                        <td align="center">
                        	 <?
								//echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$company_id $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer_name,"",0 );
								echo create_drop_down( "cbo_buyer_name", 140, "select a.id, a.buyer_name from lib_buyer a where a.status_active=1 and a.is_deleted=0 order by a.buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "",0,"" );
							?>
                        </td>
                        <td align="center">
	                    	<?
	                       		$search_by_arr=array(1=>"Order No",2=>"Style Ref");
								$dd="change_search_event(this.value, '0*0', '0*0', '../../') ";
								echo create_drop_down( "cbo_search_by", 130, $search_by_arr,"",0, "--Select--", "", $dd, 0 );
							?>
                        </td>
                        <td align="center" id="search_by_td">
                            <input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />
                        </td>
                        <td align="center">
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view (document.getElementById('cbo_company_name').value+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value, 'create_order_popup_search_list_view', 'search_div', 'ship_date_wise_production_analysis_report_controller', 'setFilterGrid(\'list_view\',-1)');" style="width:100px;" />
                    	</td>
                    </tr>
            	</tbody>
           	</table>
            <div style="margin-top:15px" id="search_div" align="left"></div>
		</fieldset>
	</form>
    </div>
    </body>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit();
}

if ($action==="create_order_popup_search_list_view")
{
	list($company_id, $buyer_id, $search_type, $search_value) = explode('**',$data);
	//echo $company_id.'rakib'.$buyer_id.'rakib'.$search_type.'rakib'.$search_value;
	if($company_id == 0)
	{
		echo "<strong style='color:red'>Please Select Company Name</strong>";
		die;
	}

	$company_arr = return_library_array( "select id, company_name from lib_company where status_active=1 and is_deleted=0", "id", "company_name" );
    $buyer_short_library = return_library_array( "select id,buyer_name from lib_buyer where status_active=1 and is_deleted=0", "id", "buyer_name" );

    $search_cond='';
	if($search_type == 1 && $search_value != ''){
		$search_cond = " and b.po_number like('%$search_value')";
	}
	else if($search_type == 2 && $search_value != ''){
		$search_cond = " and a.style_ref_no like('%$search_value%')";
	}

	if($buyer_id == 0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_cond="";
		}
		else
		{
			$buyer_cond="";
		}
	}
	else
	{
		$buyer_cond=" and a.buyer_name=$buyer_id";
	}

	$sql= "SELECT a.JOB_NO_PREFIX_NUM, a.COMPANY_NAME, a.BUYER_NAME, a.STYLE_REF_NO, b.ID, b.PO_NUMBER
	from wo_po_details_master a, wo_po_break_down b
	where a.job_no=b.job_no_mst and a.status_active in(1,2,3) and a.is_deleted=0 and b.status_active in(1,2,3) and b.is_deleted=0 and a.company_name=$company_id $buyer_cond $search_cond
	group by a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no, b.id, b.po_number
	order by b.id desc";
	//echo $sql;die;
	$sql_res=sql_select($sql);
	?>
    <table width="800" border="1" rules="all" class="rpt_table">
        <thead>
            <tr>
                <th width="30">SL</th>
                <th width="150">Company</th>
                <th width="120">Buyer</th>
                <th width="120">Job no</th>
                <th width="120">Style</th>
                <th>PO number</th>
            </tr>
       </thead>
    </table>
    <div style="max-height:820px; overflow:auto;">
	    <table id="list_view" width="800" border="1" rules="all" class="rpt_table">
	     <?
	        $i=1;
	        foreach($sql_res as $row)
	        {
				if($i%2==0) $bgcolor="#E9F3FF";
				else $bgcolor="#FFFFFF";
				?>
				<tr bgcolor="<?= $bgcolor;?>" id="tr_<?= $i; ?>" onClick="js_set_value('<?= $i; ?>'+'_'+'<?= $row['ID']; ?>'+'_'+'<?= $row['PO_NUMBER']; ?>')" style="text-decoration:none; cursor:pointer">
	                <td width="30" align="center"><?= $i; ?></td>
	                <td width="150"><p><?= $company_arr[$row['COMPANY_NAME']]; ?></p></td>
	                <td width="120"><p><?= $buyer_short_library[$row['BUYER_NAME']]; ?></p></td>
	                <td width="120"><p><?= $row['JOB_NO_PREFIX_NUM']; ?></p></td>
	                <td width="120"><p><?= $row['STYLE_REF_NO']; ?></p></td>
	                <td><p><?= $row['PO_NUMBER']; ?></p></td>
				</tr>
				<?
				$i++;
			}
			?>
	    </table>
    </div>
    <div style="width: 820px; text-align: center; padding-top: 5px;">
        <input type="submit" class="formbutton" id="close" style="width:80px" onClick="parent.emailwindow.hide();" value="Close"/>
    </div>
    <?
	exit();
}

if($action==="generate_report")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$cbo_company_name      = str_replace("'","",$cbo_company_name);
	$cbo_work_company_name = str_replace("'","",$cbo_work_company_name);
	$cbo_floor_name        = str_replace("'","",$cbo_floor_name);
	$cbo_location_name     = str_replace("'","",$cbo_location_name);
	$cbo_buyer_name        = trim(str_replace("'","",$cbo_buyer_name));
	$txt_job_no            = trim(str_replace("'","",$txt_job_no));
	$hidden_job_id         = str_replace("'","",$hidden_job_id);
	$txt_order_no          = str_replace("'","",$txt_order_no);
	$hidden_order_id       = str_replace("'","",$hidden_order_id);
	$txt_date_from         = str_replace("'","",$txt_date_from);
	$txt_date_to           = str_replace("'","",$txt_date_to);

	//echo $cbo_work_company_name.'system';die;

	$company_arr=return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0", 'id','company_name');
	$company_short_arr=return_library_array("select id,company_short_name from lib_company where status_active=1 and is_deleted=0", 'id','company_short_name');
	$buyer_arr=return_library_array("select id,buyer_name from lib_buyer where status_active=1 and is_deleted=0",'id','buyer_name');
	$season_arr=return_library_array("select id,season_name from lib_buyer_season where status_active=1 and is_deleted=0",'id','season_name');
	$country_arr=return_library_array("select id,country_name from lib_country where status_active=1 and is_deleted=0", "id", "country_name");
	$color_arr=return_library_array("select id,color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");

	$job_no_cond=$order_no_cond='';
	if ($hidden_job_id != '') {
		$job_no_cond=" and e.id in($hidden_job_id)";
	} elseif ($hidden_order_id != '') {
		$order_no_cond=" and d.id in($hidden_order_id)";
	}


	$company_cond=$buyer_cond='';
	if ($cbo_company_name != 0)
	{
		$company_cond=" and e.company_name=$cbo_company_name";
		$company_cond2=" and a.company_id=$cbo_company_name";
	}

	if ($cbo_buyer_name != 0) $buyer_cond=" and e.buyer_name=$cbo_buyer_name";
	if ($cbo_work_company_name != '')
	{
		$work_company_cond=" and a.delivery_company_id in($cbo_work_company_name)";
		$work_company_cond2=" and a.serving_company in($cbo_work_company_name)";
		$work_company_cond3=" and a.working_company_id in($cbo_work_company_name)";
	}

	if ($cbo_location_name != '') $location_cond=" and a.delivery_location_id in($cbo_location_name)";
	if ($cbo_floor_name != '') $floor_cond=" and a.delivery_floor_id in($cbo_floor_name)";

	$date_cond = '';
	if ($db_type==0)
	{
		$txt_date_from = date("Y-m-d", strtotime(str_replace("'", "",  $txt_date_from)));
		$txt_date_to = date("Y-m-d", strtotime(str_replace("'", "",  $txt_date_to)));
		$date_cond = " and b.ex_factory_date between '".$txt_date_from."' and '".$txt_date_to."'";
	}
	else
	{
		$txt_date_from = date("d-M-Y", strtotime(str_replace("'", "",  $txt_date_from)));
		$txt_date_to = date("d-M-Y", strtotime(str_replace("'", "",  $txt_date_to)));
		$date_cond = " and b.ex_factory_date between '".$txt_date_from."' and '".$txt_date_to."'";
	}

	$group_concat="";
	if ($db_type=0)
	{
		$group_concat = "group_concat(b.challan_no) as CHALLAN_NO, ";
		$group_concat .= "group_concat(b.ex_factory_date) as EX_FACTORY_DATE";
	}
	else
	{
		$group_concat = "listagg(cast(b.challan_no as varchar2(4000)),',') within group (order by b.challan_no) as CHALLAN_NO, ";
		$group_concat .= "listagg(cast(b.ex_factory_date as varchar2(4000)),',') within group (order by b.ex_factory_date) as EX_FACTORY_DATE";
	}

	$sql_foc_claim_sea = "SELECT e.BUYER_NAME, e.CLIENT_ID as BUYER_CLIENT, $group_concat, c.PO_BREAK_DOWN_ID, c.ITEM_NUMBER_ID, c.COUNTRY_ID, c.COLOR_NUMBER_ID,
	sum(CASE WHEN b.ex_factory_date between '$txt_date_from' and '$txt_date_to' and b.entry_form!=85 and b.shiping_mode=2 and b.foc_or_claim=1 THEN k.production_qnty ELSE 0 END) as AIR_QTY_FOC,
	sum(CASE WHEN b.ex_factory_date between '$txt_date_from' and '$txt_date_to' and b.entry_form!=85 and b.shiping_mode=2 and b.foc_or_claim=2 THEN k.production_qnty ELSE 0 END) as AIR_QTY_CLAIM,
	sum(CASE WHEN b.ex_factory_date between '$txt_date_from' and '$txt_date_to' and b.entry_form!=85 and b.shiping_mode=1 THEN k.production_qnty ELSE 0 END) as SEA_QTY,
	sum(CASE WHEN b.ex_factory_date between '$txt_date_from' and '$txt_date_to' and b.entry_form!=85 and b.shiping_mode=3 THEN k.production_qnty ELSE 0 END) as ROAD_QTY,
	sum(CASE WHEN b.entry_form!=85 and b.shiping_mode=2 and b.foc_or_claim=1 THEN k.production_qnty ELSE 0 END) as TTL_AIR_QTY_FOC,
	sum(CASE WHEN b.entry_form!=85 and b.shiping_mode=2 and b.foc_or_claim=2 THEN k.production_qnty ELSE 0 END) as TTL_AIR_QTY_CLAIM,
	sum(CASE WHEN b.entry_form!=85 and b.shiping_mode=1 THEN k.production_qnty ELSE 0 END) as TTL_SEA_QTY

	from pro_ex_factory_delivery_mst a, pro_ex_factory_mst b, pro_ex_factory_dtls k, wo_po_color_size_breakdown c, wo_po_break_down d, wo_po_details_master e
	where a.id=b.delivery_mst_id and b.id=k.mst_id and k.color_size_break_down_id=c.id and c.po_break_down_id=d.id and e.id=d.job_id and e.id=c.job_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and k.status_active=1 and k.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active in(1,2,3) and d.is_deleted=0 $company_cond $work_company_cond $location_cond $floor_cond $buyer_cond $job_no_cond $order_no_cond $date_cond
	group by e.buyer_name,e.client_id,c.po_break_down_id, c.item_number_id, c.country_id, c.color_number_id";
	// echo $sql_foc_claim_sea;die();
	$sql_foc_claim_sea_res=sql_select($sql_foc_claim_sea);

	$buyer_summay_arr=array();
	$foc_claim_sea_qty=array();
	$challan_ex_date_arr=array();
	foreach ($sql_foc_claim_sea_res as $row)
	{
		$challan_ex_date_arr[$row['PO_BREAK_DOWN_ID']][$row['ITEM_NUMBER_ID']][$row['COUNTRY_ID']][$row['COLOR_NUMBER_ID']]['CHALLAN_NO'].=$row['CHALLAN_NO'];
		$challan_ex_date_arr[$row['PO_BREAK_DOWN_ID']][$row['ITEM_NUMBER_ID']][$row['COUNTRY_ID']][$row['COLOR_NUMBER_ID']]['EX_FACTORY_DATE'].=$row['EX_FACTORY_DATE'];

		$foc_claim_sea_qty[$row['PO_BREAK_DOWN_ID']][$row['ITEM_NUMBER_ID']][$row['COUNTRY_ID']][$row['COLOR_NUMBER_ID']]['AIR_QTY_FOC']+=$row['AIR_QTY_FOC'];
		$foc_claim_sea_qty[$row['PO_BREAK_DOWN_ID']][$row['ITEM_NUMBER_ID']][$row['COUNTRY_ID']][$row['COLOR_NUMBER_ID']]['AIR_QTY_CLAIM']+=$row['AIR_QTY_CLAIM'];
		$foc_claim_sea_qty[$row['PO_BREAK_DOWN_ID']][$row['ITEM_NUMBER_ID']][$row['COUNTRY_ID']][$row['COLOR_NUMBER_ID']]['SEA_QTY']+=$row['SEA_QTY'];

		$foc_claim_sea_qty[$row['PO_BREAK_DOWN_ID']][$row['ITEM_NUMBER_ID']][$row['COUNTRY_ID']][$row['COLOR_NUMBER_ID']]['TTL_AIR_QTY_FOC'] += $row['TTL_AIR_QTY_FOC'];
		$foc_claim_sea_qty[$row['PO_BREAK_DOWN_ID']][$row['ITEM_NUMBER_ID']][$row['COUNTRY_ID']][$row['COLOR_NUMBER_ID']]['TTL_AIR_QTY_CLAIM'] += $row['TTL_AIR_QTY_CLAIM'];
		$foc_claim_sea_qty[$row['PO_BREAK_DOWN_ID']][$row['ITEM_NUMBER_ID']][$row['COUNTRY_ID']][$row['COLOR_NUMBER_ID']]['TTL_SEA_QTY'] += $row['TTL_SEA_QTY'];
		$foc_claim_sea_qty[$row['PO_BREAK_DOWN_ID']][$row['ITEM_NUMBER_ID']][$row['COUNTRY_ID']][$row['COLOR_NUMBER_ID']]['ROAD_QTY'] += $row['ROAD_QTY'];


		$buyer_summay_arr[$row['BUYER_NAME']][$row['BUYER_CLIENT']]['BUYER_NAME']=$row['BUYER_NAME'];
		$buyer_summay_arr[$row['BUYER_NAME']][$row['BUYER_CLIENT']]['BUYER_CLIENT']=$row['BUYER_CLIENT'];
		//$buyer_summay_arr[$row['BUYER_NAME']]['ORDER_QTY']+=$row['ORDER_QTY'];
		$buyer_summay_arr[$row['BUYER_NAME']][$row['BUYER_CLIENT']]['AIR_QTY_FOC']+=$row['AIR_QTY_FOC'];
		$buyer_summay_arr[$row['BUYER_NAME']][$row['BUYER_CLIENT']]['AIR_QTY_CLAIM']+=$row['AIR_QTY_CLAIM'];
		$buyer_summay_arr[$row['BUYER_NAME']][$row['BUYER_CLIENT']]['SEA_QTY']+=$row['SEA_QTY'];
		$buyer_summay_arr[$row['BUYER_NAME']][$row['BUYER_CLIENT']]['ROAD_QTY']+=$row['ROAD_QTY'];
		$po_ids .= $row['PO_BREAK_DOWN_ID'].',';
	}


	/*$sql_summary = "SELECT d.id as PO_ID, e.BUYER_NAME, e.CLIENT_ID as BUYER_CLIENT,
	sum(CASE WHEN b.entry_form!=85 and b.shiping_mode=2 and b.foc_or_claim=1 THEN b.ex_factory_qnty ELSE 0 END) as AIR_QTY_FOC,
	sum(CASE WHEN b.entry_form!=85 and b.shiping_mode=2 and b.foc_or_claim=2 THEN b.ex_factory_qnty ELSE 0 END) as AIR_QTY_CLAIM,
	sum(CASE WHEN b.entry_form!=85 and b.shiping_mode=1 THEN b.ex_factory_qnty ELSE 0 END) as SEA_QTY
	from pro_ex_factory_delivery_mst a, pro_ex_factory_mst b, wo_po_break_down d, wo_po_details_master e
	where a.id=b.delivery_mst_id and b.po_break_down_id=d.id and d.job_no_mst=e.job_no $company_cond $work_company_cond $location_cond $floor_cond $buyer_cond $job_no_cond $order_no_cond $date_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.status_active in(1,2,3) and d.is_deleted=0 and e.status_active in(1,2,3) and e.is_deleted=0
	group by d.id, e.BUYER_NAME, e.CLIENT_ID
	order by e.buyer_name ASC";

	//echo $sql_summary;die;
	$sql_summary_res=sql_select($sql_summary);
	$tot_rows=0;
	$buyer_summay_arr=array();
	foreach ($sql_summary_res as $row)
	{
		$tot_rows++;
		$buyer_summay_arr[$row['BUYER_NAME']][$row['BUYER_CLIENT']]['BUYER_NAME']=$row['BUYER_NAME'];
		$buyer_summay_arr[$row['BUYER_NAME']][$row['BUYER_CLIENT']]['BUYER_CLIENT']=$row['BUYER_CLIENT'];
		//$buyer_summay_arr[$row['BUYER_NAME']]['ORDER_QTY']+=$row['ORDER_QTY'];
		$buyer_summay_arr[$row['BUYER_NAME']][$row['BUYER_CLIENT']]['AIR_QTY_FOC']+=$row['AIR_QTY_FOC'];
		$buyer_summay_arr[$row['BUYER_NAME']][$row['BUYER_CLIENT']]['AIR_QTY_CLAIM']+=$row['AIR_QTY_CLAIM'];
		$buyer_summay_arr[$row['BUYER_NAME']][$row['BUYER_CLIENT']]['SEA_QTY']+=$row['SEA_QTY'];
		$po_ids .= $row['PO_ID'].',';
	}*/
	//echo '<pre>';print_r($buyer_summay_arr);die;

	$po_ids = array_flip(array_flip(explode(',', rtrim($po_ids,','))));

	function where_con($arrayData,$dataType=0,$table_coloum){
		$chunk_list_arr=array_chunk($arrayData,999);
		$p=1;
		foreach($chunk_list_arr as $process_arr)
		{
			if($dataType==0){
				if($p==1){$sql .=" and (".$table_coloum." in(".implode(',',$process_arr).")"; }
				else {$sql .=" or ".$table_coloum." in(".implode(',',$process_arr).")";}
			}
			else{
				if($p==1){$sql .=" and (".$table_coloum." in('".implode("','",$process_arr)."')"; }
				else {$sql .=" or ".$table_coloum." in('".implode("','",$process_arr)."')";}
			}
			$p++;
		}

		$sql.=") ";
		return $sql;
	}

	$po_id_cond=where_con($po_ids,0,"d.id");

	$sql_cut_lay=" SELECT e.BUYER_NAME, e.CLIENT_ID as BUYER_CLIENT, c.ORDER_ID, b.GMT_ITEM_ID, c.COUNTRY_ID, b.COLOR_ID,
	sum(case when a.entry_date between '$txt_date_from' and '$txt_date_to' then c.size_qty else 0 end ) as CUTTING_QTY,
	sum(c.size_qty ) as TTL_CUTTING_QTY
	from ppl_cut_lay_mst a, ppl_cut_lay_dtls b, ppl_cut_lay_bundle c, wo_po_break_down d, wo_po_details_master e
	where a.id=b.mst_id and b.id=c.dtls_id and c.order_id=d.id and d.job_no_mst=e.job_no and a.status_active=1 and b.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active in(1,2,3) and d.is_deleted=0 and e.status_active in(1,2,3) and e.is_deleted=0 $company_cond $work_company_cond3 $po_id_cond
	group by e.buyer_name, e.CLIENT_ID, c.order_id, b.gmt_item_id, c.country_id, b.color_id";

	$sql_cut_lay_res=sql_select($sql_cut_lay);
	$production_summary_cutting_arr=array();
	$production_details_cutting_arr=array();
	foreach ($sql_cut_lay_res as $row)
	{
		$production_summary_cutting_arr[$row['BUYER_NAME']][$row['BUYER_CLIENT']]['CUTTING_QTY'] += $row['CUTTING_QTY'];
		$production_details_cutting_arr[$row['ORDER_ID']][$row['GMT_ITEM_ID']][$row['COUNTRY_ID']][$row['COLOR_ID']]['CUTTING_QTY'] += $row['CUTTING_QTY'];
		$production_details_cutting_arr[$row['ORDER_ID']][$row['GMT_ITEM_ID']][$row['COUNTRY_ID']][$row['COLOR_ID']]['TTL_CUTTING_QTY'] += $row['TTL_CUTTING_QTY'];
	}
	//echo '<pre>';print_r($production_summary_cutting_arr);die;

	$sql_production = "SELECT e.BUYER_NAME, e.CLIENT_ID as BUYER_CLIENT, c.PO_BREAK_DOWN_ID, c.ITEM_NUMBER_ID, c.COUNTRY_ID, c.COLOR_NUMBER_ID,
	sum(CASE WHEN a.production_date between '$txt_date_from' and '$txt_date_to' and a.production_type=4 and b.production_type=4 THEN b.production_qnty ELSE 0 END) AS INPUT_QTY,
	sum(CASE WHEN a.production_type=4 and b.production_type=4 THEN b.production_qnty ELSE 0 END) AS TTL_INPUT_QTY,
	sum(CASE WHEN a.production_date between '$txt_date_from' and '$txt_date_to' and a.production_type=5 and b.production_type=5 THEN b.reject_qty ELSE 0 END) AS REJECT_OUTPUT_QTY,
	sum(CASE WHEN a.production_date between '$txt_date_from' and '$txt_date_to' and a.production_type=11 and b.production_type=11 THEN b.production_qnty ELSE 0 END) AS POLY_QTY,
	sum(CASE WHEN a.production_type=11 and b.production_type=11 THEN b.production_qnty ELSE 0 END) AS TTL_POLY_QTY,
	sum(CASE WHEN a.production_date between '$txt_date_from' and '$txt_date_to' and a.production_type=11 and b.production_type=11 THEN b.reject_qty ELSE 0 END) AS REJECT_POLY_QTY,
	sum(CASE WHEN a.production_date between '$txt_date_from' and '$txt_date_to' and a.production_type=8 and b.production_type=8 THEN b.production_qnty ELSE 0 END) AS FINISHING_QTY,
	sum(CASE WHEN a.production_type=8 and b.production_type=8 THEN b.production_qnty ELSE 0 END) AS TTL_FINISHING_QTY,
	sum(CASE WHEN a.production_date between '$txt_date_from' and '$txt_date_to' and a.production_type=8 and b.production_type=8 THEN b.reject_qty ELSE 0 END) AS REJECT_FINISHING_QTY
	from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown c, wo_po_break_down d, wo_po_details_master e
	where a.id=b.mst_id and b.color_size_break_down_id=c.id and c.po_break_down_id=d.id and d.job_no_mst=e.job_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active in(1,2,3) and d.is_deleted=0 and e.status_active in(1,2,3) and e.is_deleted=0 $company_cond2 $work_company_cond2 $po_id_cond
	group by e.buyer_name, e.CLIENT_ID, c.po_break_down_id, c.item_number_id, c.country_id, c.color_number_id
	order by e.buyer_name ASC";
		//echo $sql_production;die;
	$sql_production_res=sql_select($sql_production);
	$production_summary_arr=array();
	$production_details_arr=array();
	foreach ($sql_production_res as$row)
	{
		$production_summary_arr[$row['BUYER_NAME']][$row['BUYER_CLIENT']]['INPUT_QTY'] += $row['INPUT_QTY'];
		$production_summary_arr[$row['BUYER_NAME']][$row['BUYER_CLIENT']]['REJECT_OUTPUT_QTY'] += $row['REJECT_OUTPUT_QTY'];
		$production_summary_arr[$row['BUYER_NAME']][$row['BUYER_CLIENT']]['POLY_QTY'] += $row['POLY_QTY'];
		$production_summary_arr[$row['BUYER_NAME']][$row['BUYER_CLIENT']]['REJECT_POLY_QTY'] += $row['REJECT_POLY_QTY'];
		$production_summary_arr[$row['BUYER_NAME']][$row['BUYER_CLIENT']]['FINISHING_QTY'] += $row['FINISHING_QTY'];
		$production_summary_arr[$row['BUYER_NAME']][$row['BUYER_CLIENT']]['REJECT_FINISHING_QTY'] += $row['REJECT_FINISHING_QTY'];

		$production_details_arr[$row['PO_BREAK_DOWN_ID']][$row['ITEM_NUMBER_ID']][$row['COUNTRY_ID']][$row['COLOR_NUMBER_ID']]['INPUT_QTY'] += $row['INPUT_QTY'];
		$production_details_arr[$row['PO_BREAK_DOWN_ID']][$row['ITEM_NUMBER_ID']][$row['COUNTRY_ID']][$row['COLOR_NUMBER_ID']]['TTL_INPUT_QTY'] += $row['TTL_INPUT_QTY'];
		$production_details_arr[$row['PO_BREAK_DOWN_ID']][$row['ITEM_NUMBER_ID']][$row['COUNTRY_ID']][$row['COLOR_NUMBER_ID']]['REJECT_OUTPUT_QTY'] += $row['REJECT_OUTPUT_QTY'];
		$production_details_arr[$row['PO_BREAK_DOWN_ID']][$row['ITEM_NUMBER_ID']][$row['COUNTRY_ID']][$row['COLOR_NUMBER_ID']]['POLY_QTY'] += $row['POLY_QTY'];
		$production_details_arr[$row['PO_BREAK_DOWN_ID']][$row['ITEM_NUMBER_ID']][$row['COUNTRY_ID']][$row['COLOR_NUMBER_ID']]['TTL_POLY_QTY'] += $row['TTL_POLY_QTY'];
		$production_details_arr[$row['PO_BREAK_DOWN_ID']][$row['ITEM_NUMBER_ID']][$row['COUNTRY_ID']][$row['COLOR_NUMBER_ID']]['REJECT_POLY_QTY'] += $row['REJECT_POLY_QTY'];
		$production_details_arr[$row['PO_BREAK_DOWN_ID']][$row['ITEM_NUMBER_ID']][$row['COUNTRY_ID']][$row['COLOR_NUMBER_ID']]['FINISHING_QTY'] += $row['FINISHING_QTY'];
		$production_details_arr[$row['PO_BREAK_DOWN_ID']][$row['ITEM_NUMBER_ID']][$row['COUNTRY_ID']][$row['COLOR_NUMBER_ID']]['TTL_FINISHING_QTY'] += $row['TTL_FINISHING_QTY'];
		$production_details_arr[$row['PO_BREAK_DOWN_ID']][$row['ITEM_NUMBER_ID']][$row['COUNTRY_ID']][$row['COLOR_NUMBER_ID']]['REJECT_FINISHING_QTY'] += $row['REJECT_FINISHING_QTY'];
	}
	//print_r($production_summary_arr);die;


	/*$sql_foc_claim_sea = "SELECT $group_concat, c.PO_BREAK_DOWN_ID, c.ITEM_NUMBER_ID, c.COUNTRY_ID, c.COLOR_NUMBER_ID,
	sum(CASE WHEN b.ex_factory_date between '$txt_date_from' and '$txt_date_to' and b.entry_form!=85 and b.shiping_mode=2 and b.foc_or_claim=1 THEN k.production_qnty ELSE 0 END) as AIR_QTY_FOC,
	sum(CASE WHEN b.ex_factory_date between '$txt_date_from' and '$txt_date_to' and b.entry_form!=85 and b.shiping_mode=2 and b.foc_or_claim=2 THEN k.production_qnty ELSE 0 END) as AIR_QTY_CLAIM,
	sum(CASE WHEN b.ex_factory_date between '$txt_date_from' and '$txt_date_to' and b.entry_form!=85 and b.shiping_mode=1 THEN k.production_qnty ELSE 0 END) as SEA_QTY,
	sum(CASE WHEN b.entry_form!=85 and b.shiping_mode=2 and b.foc_or_claim=1 THEN k.production_qnty ELSE 0 END) as TTL_AIR_QTY_FOC,
	sum(CASE WHEN b.entry_form!=85 and b.shiping_mode=2 and b.foc_or_claim=2 THEN k.production_qnty ELSE 0 END) as TTL_AIR_QTY_CLAIM,
	sum(CASE WHEN b.entry_form!=85 and b.shiping_mode=1 THEN k.production_qnty ELSE 0 END) as TTL_SEA_QTY
	from pro_ex_factory_delivery_mst a, pro_ex_factory_mst b, pro_ex_factory_dtls k, wo_po_color_size_breakdown c, wo_po_break_down d
	where a.id=b.delivery_mst_id and b.id=k.mst_id and k.color_size_break_down_id=c.id and c.po_break_down_id=d.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and k.status_active=1 and k.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active in(1,2,3) and d.is_deleted=0 $company_cond2 $work_company_cond $po_id_cond $date_cond
	group by c.po_break_down_id, c.item_number_id, c.country_id, c.color_number_id";
	// echo $sql_foc_claim_sea;die();
	$sql_foc_claim_sea_res=sql_select($sql_foc_claim_sea);
	$foc_claim_sea_qty=array();
	$challan_ex_date_arr=array();
	foreach ($sql_foc_claim_sea_res as $row)
	{
		$challan_ex_date_arr[$row['PO_BREAK_DOWN_ID']][$row['ITEM_NUMBER_ID']][$row['COUNTRY_ID']][$row['COLOR_NUMBER_ID']]['CHALLAN_NO'].=$row['CHALLAN_NO'];
		$challan_ex_date_arr[$row['PO_BREAK_DOWN_ID']][$row['ITEM_NUMBER_ID']][$row['COUNTRY_ID']][$row['COLOR_NUMBER_ID']]['EX_FACTORY_DATE'].=$row['EX_FACTORY_DATE'];

		$foc_claim_sea_qty[$row['PO_BREAK_DOWN_ID']][$row['ITEM_NUMBER_ID']][$row['COUNTRY_ID']][$row['COLOR_NUMBER_ID']]['AIR_QTY_FOC']+=$row['AIR_QTY_FOC'];
		$foc_claim_sea_qty[$row['PO_BREAK_DOWN_ID']][$row['ITEM_NUMBER_ID']][$row['COUNTRY_ID']][$row['COLOR_NUMBER_ID']]['AIR_QTY_CLAIM']+=$row['AIR_QTY_CLAIM'];
		$foc_claim_sea_qty[$row['PO_BREAK_DOWN_ID']][$row['ITEM_NUMBER_ID']][$row['COUNTRY_ID']][$row['COLOR_NUMBER_ID']]['SEA_QTY']+=$row['SEA_QTY'];

		$foc_claim_sea_qty[$row['PO_BREAK_DOWN_ID']][$row['ITEM_NUMBER_ID']][$row['COUNTRY_ID']][$row['COLOR_NUMBER_ID']]['TTL_AIR_QTY_FOC'] += $row['TTL_AIR_QTY_FOC'];
		$foc_claim_sea_qty[$row['PO_BREAK_DOWN_ID']][$row['ITEM_NUMBER_ID']][$row['COUNTRY_ID']][$row['COLOR_NUMBER_ID']]['TTL_AIR_QTY_CLAIM'] += $row['TTL_AIR_QTY_CLAIM'];
		$foc_claim_sea_qty[$row['PO_BREAK_DOWN_ID']][$row['ITEM_NUMBER_ID']][$row['COUNTRY_ID']][$row['COLOR_NUMBER_ID']]['TTL_SEA_QTY'] += $row['TTL_SEA_QTY'];
	}*/
	//echo '<pre>';print_r($challan_ex_date_arr);

	$sql_details="SELECT e.BUYER_NAME, e.JOB_NO, e.STYLE_REF_NO, e.CLIENT_ID as BUYER_CLIENT, e.JOB_NO_PREFIX_NUM, e.season_buyer_wise as SEASON, c.ID, d.PO_NUMBER, d.SHIPING_STATUS, c.PO_BREAK_DOWN_ID, c.ITEM_NUMBER_ID, c.COUNTRY_ID, c.COLOR_NUMBER_ID, c.ORDER_QUANTITY
	from wo_po_color_size_breakdown c, wo_po_break_down d, wo_po_details_master e
	where c.po_break_down_id=d.id and d.job_no_mst=e.job_no
	and c.status_active=1 and c.is_deleted=0 and d.status_active in(1,2,3) and d.is_deleted=0 and e.status_active in(1,2,3) and e.is_deleted=0 $po_id_cond
	group by e.buyer_name, e.job_no, e.style_ref_no, e.client_id, e.job_no_prefix_num, e.season_buyer_wise, c.id, d.po_number, d.shiping_status, c.po_break_down_id, c.item_number_id, c.country_id, c.color_number_id, c.order_quantity";
	//echo $sql_details;

	$sql_details_res=sql_select($sql_details);
	$order_color_data=array();
	$order_qty_summary_arr=array();
	foreach ($sql_details_res as $row)
	{
		$order_qty_summary_arr[$row['BUYER_NAME']][$row['BUYER_CLIENT']]['ORDER_QUANTITY'] += $row['ORDER_QUANTITY'];
		$order_color_data[$row['BUYER_NAME']][$row['JOB_NO']][$row['PO_BREAK_DOWN_ID']][$row['ITEM_NUMBER_ID']][$row['COUNTRY_ID']][$row['COLOR_NUMBER_ID']]['CHALLAN_NO']=$row['CHALLAN_NO'];
		$order_color_data[$row['BUYER_NAME']][$row['JOB_NO']][$row['PO_BREAK_DOWN_ID']][$row['ITEM_NUMBER_ID']][$row['COUNTRY_ID']][$row['COLOR_NUMBER_ID']]['EX_FACTORY_DATE']=$row['EX_FACTORY_DATE'];
		$order_color_data[$row['BUYER_NAME']][$row['JOB_NO']][$row['PO_BREAK_DOWN_ID']][$row['ITEM_NUMBER_ID']][$row['COUNTRY_ID']][$row['COLOR_NUMBER_ID']]['BUYER_NAME']=$row['BUYER_NAME'];
		$order_color_data[$row['BUYER_NAME']][$row['JOB_NO']][$row['PO_BREAK_DOWN_ID']][$row['ITEM_NUMBER_ID']][$row['COUNTRY_ID']][$row['COLOR_NUMBER_ID']]['JOB_NO']=$row['JOB_NO'];
		$order_color_data[$row['BUYER_NAME']][$row['JOB_NO']][$row['PO_BREAK_DOWN_ID']][$row['ITEM_NUMBER_ID']][$row['COUNTRY_ID']][$row['COLOR_NUMBER_ID']]['JOB_NO_PREFIX_NUM']=$row['JOB_NO_PREFIX_NUM'];
		$order_color_data[$row['BUYER_NAME']][$row['JOB_NO']][$row['PO_BREAK_DOWN_ID']][$row['ITEM_NUMBER_ID']][$row['COUNTRY_ID']][$row['COLOR_NUMBER_ID']]['STYLE_REF_NO']=$row['STYLE_REF_NO'];
		$order_color_data[$row['BUYER_NAME']][$row['JOB_NO']][$row['PO_BREAK_DOWN_ID']][$row['ITEM_NUMBER_ID']][$row['COUNTRY_ID']][$row['COLOR_NUMBER_ID']]['BUYER_CLIENT']=$row['BUYER_CLIENT'];
		$order_color_data[$row['BUYER_NAME']][$row['JOB_NO']][$row['PO_BREAK_DOWN_ID']][$row['ITEM_NUMBER_ID']][$row['COUNTRY_ID']][$row['COLOR_NUMBER_ID']]['SEASON']=$row['SEASON'];

		$order_color_data[$row['BUYER_NAME']][$row['JOB_NO']][$row['PO_BREAK_DOWN_ID']][$row['ITEM_NUMBER_ID']][$row['COUNTRY_ID']][$row['COLOR_NUMBER_ID']]['PO_NUMBER']=$row['PO_NUMBER'];
		$order_color_data[$row['BUYER_NAME']][$row['JOB_NO']][$row['PO_BREAK_DOWN_ID']][$row['ITEM_NUMBER_ID']][$row['COUNTRY_ID']][$row['COLOR_NUMBER_ID']]['SHIPING_STATUS']=$shipment_status[$row['SHIPING_STATUS']];
		$order_color_data[$row['BUYER_NAME']][$row['JOB_NO']][$row['PO_BREAK_DOWN_ID']][$row['ITEM_NUMBER_ID']][$row['COUNTRY_ID']][$row['COLOR_NUMBER_ID']]['ITEM_NUMBER_ID']=$row['ITEM_NUMBER_ID'];
		$order_color_data[$row['BUYER_NAME']][$row['JOB_NO']][$row['PO_BREAK_DOWN_ID']][$row['ITEM_NUMBER_ID']][$row['COUNTRY_ID']][$row['COLOR_NUMBER_ID']]['COUNTRY_ID']=$row['COUNTRY_ID'];
		$order_color_data[$row['BUYER_NAME']][$row['JOB_NO']][$row['PO_BREAK_DOWN_ID']][$row['ITEM_NUMBER_ID']][$row['COUNTRY_ID']][$row['COLOR_NUMBER_ID']]['COLOR_NUMBER_ID']=$row['COLOR_NUMBER_ID'];
		$order_color_data[$row['BUYER_NAME']][$row['JOB_NO']][$row['PO_BREAK_DOWN_ID']][$row['ITEM_NUMBER_ID']][$row['COUNTRY_ID']][$row['COLOR_NUMBER_ID']]['ORDER_QUANTITY']+=$row['ORDER_QUANTITY'];
	}
	//echo '<pre>';print_r($order_color_data);

    ob_start();
    $summary_table_width=1590;
    $details_table_width=2790;

	?>
	<div width="<?= $details_table_width; ?>">
		<table cellpadding="0" cellspacing="0" width="<?= $summary_table_width ?>">
			<tr>
			   <td align="center" width="100%"><strong style="font-size:16px">Buyer Wise Summary (<?
			   	if ($cbo_company_name != 0){
			   		$company = $company_arr[$cbo_company_name];
			   	} else {
			   		$cbo_work_company_name_arr=explode(",",$cbo_work_company_name);
			   		foreach ($cbo_work_company_name_arr as $working_comp_name) {
						$workingCompanyName .= $company_short_arr[$working_comp_name].', ';
					}
					$company = chop($workingCompanyName,', ');
			   	}
			   	echo $company;
			   	?>)</strong></td>
			</tr>
			<tr>
			   <td align="center" width="100%"><strong style="font-size:16px">Date:&nbsp;<?= change_date_format($txt_date_from); ?>&nbsp;To&nbsp;<?= change_date_format($txt_date_to); ?></strong></td>
			</tr>
		</table>

		<table width="<?= $summary_table_width; ?>" class="rpt_table" border="1" rules="all" cellpadding="0" cellspacing="0">
			<thead>
				<tr>
					<th width="50">SL</th>
					<th width="120">Buyer</th>
	                <th width="120">Buyer Client</th>
					<th width="100">Order Qty</th>
	                <th width="100">Cut & Lay Qty</th>
	                <th width="100">Input Qty</th>
	                <th width="100">Poly Qty</th>
	                <th width="100">Reject Qty</th>
	                <th width="100">Finishing Qty</th>
	                <th width="100">AIR Qty FOC</th>
	                <th width="100">AIR Qty Claim</th>
	                <th width="100">SEA Qty</th>
	                <th width="100">Road Qty</th>
	                <th width="100">Shipment Qty</th>
	                <th width="100">Excess Qty</th>
	                <th width="100">Short Qty</th>
				</tr>
			</thead>
		</table>
		<div style="width:<?= $summary_table_width+20; ?>"px; overflow-y:auto; max-height:300px" id="scroll_body_summary">
		    <table width="<?= $summary_table_width; ?>" class="rpt_table" border="1" rules="all" cellpadding="0" cellspacing="0" id="table_body_summary">
		        <tbody>
		        	<?
		        	$i=1;
		        	$tot_order_qty=$tot_cutting_qty=$tot_input_qty=$tot_poly_qty=$tot_reject_all=0;
		        	$tot_finishing_qty=$tot_air_qty_foc=$tot_air_qty_claim=$tot_sea_qty=$tot_shipment_qty=0;
		        	$tot_excess_qty=$tot_short_qty=0;
		        	foreach($buyer_summay_arr as $buyer_id=>$buyer_data)
					{
			        	foreach ($buyer_data as $buyer_client_id=>$value)
			        	{
			        		$order_qty = $order_qty_summary_arr[$buyer_id][$buyer_client_id]['ORDER_QUANTITY'];
			        		$cutting_qty = $production_summary_cutting_arr[$buyer_id][$buyer_client_id]['CUTTING_QTY'];
			        		$input_qty = $production_summary_arr[$buyer_id][$buyer_client_id]['INPUT_QTY'];
			        		$reject_output_qty = $production_summary_arr[$buyer_id][$buyer_client_id]['REJECT_OUTPUT_QTY'];
			        		$poly_qty = $production_summary_arr[$buyer_id][$buyer_client_id]['POLY_QTY'];
			        		$reject_poly_qty = $production_summary_arr[$buyer_id][$buyer_client_id]['REJECT_POLY_QTY'];
			        		$reject_finishing_qty = $production_summary_arr[$buyer_id][$buyer_client_id]['REJECT_FINISHING_QTY'];
			        		$finishing_qty = $production_summary_arr[$buyer_id][$buyer_client_id]['FINISHING_QTY'];
			        		$reject_all = $reject_output_qty+$reject_poly_qty+$reject_finishing_qty;
			        		$shipment_qty = $value['AIR_QTY_FOC'] +$value['AIR_QTY_CLAIM'] + $value['SEA_QTY']+$value['ROAD_QTY'];
			        		$excess_short_qty = 0;
			        		$excess_short_qty = $shipment_qty - $order_qty;
			        		if ($excess_short_qty > 0)
		        			{
		        				$excess_qty = $excess_short_qty;
		        				$short_qty = 0;
		        			}
			        		else if ($excess_short_qty < 0)
			        		{
			        			$short_qty = $excess_short_qty;
			        			$excess_qty = 0;
			        		}

			        		if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			        		?>
				        	<tr bgcolor="<?= $bgcolor;  ?>" onclick="change_color('trds_<?= $i; ?>','<?= $bgcolor; ?>')" id="trds_<?= $i; ?>">
				        		<td width="50" align="center"><?= $i; ?></td>
				        		<td width="120"><p><?= $buyer_arr[$value['BUYER_NAME']]; ?></p></td>
				        		<td width="120"><p><?= $buyer_arr[$value['BUYER_CLIENT']]; ?></p></td>
				        		<td width="100" align="right"><p><?= number_format($order_qty,0); ?></p></td>
				        		<td width="100" align="right"><p><?= number_format($cutting_qty,0); ?></p></td>
				        		<td width="100" align="right"><p><?= number_format($input_qty,0); ?></p></td>
				        		<td width="100" align="right"><p><?= number_format($poly_qty,0); ?></p></td>
				        		<td width="100" align="right"><p><?= number_format($reject_all,0); ?></p></td>
				        		<td width="100" align="right"><p><?= number_format($finishing_qty,0); ?></p></td>
				        		<td width="100" align="right"><p><?= number_format($value['AIR_QTY_FOC'],0); ?></p></td>
				        		<td width="100" align="right"><p><?= number_format($value['AIR_QTY_CLAIM'],0); ?></p></td>
				        		<td width="100" align="right"><p><?= number_format($value['SEA_QTY'],0); ?></p></td>
				        		<td width="100" align="right"><p><?= number_format($value['ROAD_QTY'],0); ?></p></td>
				        		<td width="100" align="right" title="AIR Qty FOC+AIR Qty Claim+SEA Qty + Road Qty"><p><?= number_format($shipment_qty,0); ?></p></td>
				        		<td width="100" align="right" title="Shipment Qty-Order Qty"><p><?= number_format($excess_qty,0); ?></p></td>
				        		<td width="100" align="right" title="Shipment Qty-Order Qty"><p><?= number_format($short_qty,0); ?></p></td>
				        	</tr>
				        	<?
				        	$i++;
				        	$tot_order_qty += $order_qty;
				        	$tot_cutting_qty += $cutting_qty;
				        	$tot_input_qty += $input_qty;
				        	$tot_poly_qty += $poly_qty;
				        	$tot_reject_all += $reject_all;
				        	$tot_finishing_qty += $finishing_qty;
				        	$tot_air_qty_foc += $value['AIR_QTY_FOC'];
				        	$tot_air_qty_claim += $value['AIR_QTY_CLAIM'];
				        	$tot_sea_qty += $value['SEA_QTY'];
				        	$tot_road_qty += $value['ROAD_QTY'];
				        	$tot_shipment_qty += $shipment_qty;
				        	$tot_excess_qty += $excess_qty;
				        	$tot_short_qty += $short_qty;
				        }
			        }
			        ?>
		        </tbody>
		    </table>
	    </div>
	    <table width="<?= $summary_table_width; ?>" class="rpt_table" border="1" rules="all" cellpadding="0" cellspacing="0">
			<tfoot>
				<tr>
					<th width="50"><p>&nbsp;</p></th>
					<th width="120"><p>&nbsp;</p></th>
	                <th width="120"><p><strong>Total&nbsp;</strong></p></th>
					<th width="100" align="right" id="tot_order_qty_id"><p><?= number_format($tot_order_qty,0); ?></p></th>
					<th width="100" align="right" id="tot_cutting_qty_id"><p><?= number_format($tot_cutting_qty,0); ?></p></th>
					<th width="100" align="right" id="tot_input_qty_id"><p><?= number_format($tot_input_qty,0); ?></p></th>
					<th width="100" align="right" id="tot_poly_qty_id"><p><?= number_format($tot_poly_qty,0); ?></p></th>
					<th width="100" align="right" id="tot_reject_qty_finishing_id"><p><?= number_format($tot_reject_all,0); ?></p></th>
					<th width="100" align="right" id="tot_finishing_qty_id"><p><?= number_format($tot_finishing_qty,0); ?></p></th>
					<th width="100" align="right" id="tot_air_qty_foc_id"><p><?= number_format($tot_air_qty_foc,0); ?></p></th>
					<th width="100" align="right" id="tot_air_qty_claim_id"><p><?= number_format($tot_air_qty_claim,0); ?></p></th>
					<th width="100" align="right" id="tot_sea_qty_id"><p><?= number_format($tot_sea_qty,0); ?></p></th>
					<th width="100" align="right" id="tot_road_qty_id"><p><?= number_format($tot_road_qty,0); ?></p></th>
					<th width="100" align="right" id="tot_shipment_qty_id"><p><?= number_format($tot_shipment_qty ,0); ?></p></th>
					<th width="100" align="right" id="tot_excess_qty_id"><p><?= number_format($tot_excess_qty,0); ?></p></th>
					<th width="100" align="right" id="tot_short_qty_id"><p><?= number_format($tot_short_qty,0); ?></p></th>
				</tr>
			</tfoot>
		</table>
		<br>
		<table cellpadding="0" cellspacing="0" width="<?= $details_table_width ?>">
			<tr>
			   <td align="center" width="100%"><strong style="font-size:16px">Details Report</strong></td>
			</tr>
		</table>
		<table width="<?= $details_table_width; ?>" class="rpt_table" border="1" rules="all" cellpadding="0" cellspacing="0">
			<thead>
				<tr>
					<th width="50">SL</th>
					<th width="150">Ex-Factory Date</th>
	                <th width="150">Challan No</th>
					<th width="100">Buyer</th>
	                <th width="100">Buyer Client</th>
	                <th width="100">Style No</th>
	                <th width="100">Job No</th>
	                <th width="100">Season</th>
	                <th width="100">PO No</th>
	                <th width="100">Country</th>
	                <th width="100">Garments.Item</th>
	                <th width="100">Color</th>
	                <th width="80">Order Qty</th>
	                <th width="80">Cut & Lay Qty</th>
	                <th width="80">TTL Cut Qty</th>
	                <th width="80">Input Qty</th>
	                <th width="80">TTL Input Qty</th>
	                <th width="80">Poly Qty</th>
	                <th width="80">TTL Poly Qty</th>
	                <th width="80">TTL Gmts Reject</th>
	                <th width="80">Finishing Qty</th>
	                <th width="80">TTL Finishing Qty</th>
	                <th width="80">AIR Qty FOC</th>
	                <th width="80">AIR Qty Claim</th>
	                <th width="80">SEA Qty</th>
					<th width="80">Road Qty</th>
	                <th width="80">Shipment Qty</th>
	                <th width="80">TTL Shipment Qty</th>
	                <th width="80">Cut To Ship Balance</th>
	                <th width="80">Cut To Ship %</th>
	                <th width="80">Short/Excess Ship Qty</th>
	                <th width="100">Ship Status</th>
				</tr>
			</thead>
			<tbody>
		        	<?
		        	$i=1;
		        	foreach($order_color_data as $buyer_id=>$buyer_data)
					{
						$buyer_tot_order_quantity=$buyer_tot_cutting_qty=$buyer_tot_ttl_cutting_qty=0;
						$buyer_tot_input_qty=$buyer_tot_ttl_input_qty=$buyer_tot_poly_qty=0;
						$buyer_tot_ttl_poly_qty=$buyer_tot_ttl_gmts_reject=$buyer_tot_finishing_qty=0;
						$buyer_tot_ttl_finishing_qty=$buyer_tot_air_qty_foc=$buyer_tot_air_qty_claim=0;
						$buyer_tot_sea_qty=$buyer_tot_shipment_qty=$buyer_tot_ttl_shipment_qty=0;
						$buyer_tot_excess_short_qty=0;
						foreach($buyer_data as $job_no=>$job_data)
						{
							$job_tot_order_quantity=$job_tot_cutting_qty=$job_tot_ttl_cutting_qty=0;
							$job_tot_input_qty=$job_tot_ttl_input_qty=$job_tot_poly_qty=0;
							$job_tot_ttl_poly_qty=$job_tot_ttl_gmts_reject=$job_tot_finishing_qty=0;
							$job_tot_ttl_finishing_qty=$job_tot_air_qty_foc=$job_tot_air_qty_claim=0;
							$job_tot_sea_qty=$job_tot_shipment_qty=$job_tot_ttl_shipment_qty=0;
							$job_tot_excess_short_qty=0;
							foreach($job_data as $order_id=>$order_data)
							{
								$po_tot_order_quantity=$po_tot_cutting_qty=$po_tot_ttl_cutting_qty=0;
								$po_tot_input_qty=$po_tot_ttl_input_qty=$po_tot_poly_qty=0;
								$po_tot_ttl_poly_qty=$po_tot_ttl_gmts_reject=$po_tot_finishing_qty=0;
								$po_tot_ttl_finishing_qty=$po_tot_air_qty_foc=$po_tot_air_qty_claim=0;
								$po_tot_sea_qty=$po_tot_shipment_qty=$po_tot_ttl_shipment_qty=0;
								$po_tot_excess_short_qty=0;
								foreach($order_data as $item_id=>$item_data)
								{
									foreach($item_data as $country_id=>$country_data)
									{
										foreach($country_data as $color_id=>$value)
										{
											$challan_no = implode(",", array_unique(explode(",", $challan_ex_date_arr[$order_id][$item_id][$country_id][$color_id]['CHALLAN_NO'])));
											$ex_factory_date = array_unique(explode(",", $challan_ex_date_arr[$order_id][$item_id][$country_id][$color_id]['EX_FACTORY_DATE']));
											$ex_factory_date_all='';
											foreach ($ex_factory_date as $ex_date) {
												$ex_factory_date_all .= change_date_format($ex_date).',';
											}
											$ex_factory_date_all = rtrim($ex_factory_date_all,',');


											$cutting_qty = $production_details_cutting_arr[$order_id][$item_id][$country_id][$color_id]['CUTTING_QTY'];
											$ttl_cutting_qty = $production_details_cutting_arr[$order_id][$item_id][$country_id][$color_id]['TTL_CUTTING_QTY'];
											$input_qty = $production_details_arr[$order_id][$item_id][$country_id][$color_id]['INPUT_QTY'];
											$ttl_input_qty = $production_details_arr[$order_id][$item_id][$country_id][$color_id]['TTL_INPUT_QTY'];
											$reject_output_qty = $production_details_arr[$order_id][$item_id][$country_id][$color_id]['REJECT_OUTPUT_QTY'];
											$poly_qty = $production_details_arr[$order_id][$item_id][$country_id][$color_id]['POLY_QTY'];
											$ttl_poly_qty = $production_details_arr[$order_id][$item_id][$country_id][$color_id]['TTL_POLY_QTY'];
											$reject_poly_qty = $production_details_arr[$order_id][$item_id][$country_id][$color_id]['REJECT_POLY_QTY'];
											$finishing_qty = $production_details_arr[$order_id][$item_id][$country_id][$color_id]['FINISHING_QTY'];
											$ttl_finishing_qty = $production_details_arr[$order_id][$item_id][$country_id][$color_id]['TTL_FINISHING_QTY'];
											$reject_finishing_qty = $production_details_arr[$order_id][$item_id][$country_id][$color_id]['REJECT_FINISHING_QTY'];
											$ttl_gmts_reject=$reject_output_qty+$reject_poly_qty+$reject_finishing_qty;
											$air_qty_foc = $foc_claim_sea_qty[$order_id][$item_id][$country_id][$color_id]['AIR_QTY_FOC'];
											$air_qty_claim = $foc_claim_sea_qty[$order_id][$item_id][$country_id][$color_id]['AIR_QTY_CLAIM'];
											$sea_qty = $foc_claim_sea_qty[$order_id][$item_id][$country_id][$color_id]['SEA_QTY'];
											$road_qty = $foc_claim_sea_qty[$order_id][$item_id][$country_id][$color_id]['ROAD_QTY'];
											$shipment_qty = $air_qty_foc+$air_qty_claim+$sea_qty+$road_qty;
											$ttl_air_qty_foc = $foc_claim_sea_qty[$order_id][$item_id][$country_id][$color_id]['TTL_AIR_QTY_FOC'];
											$ttl_air_qty_claim = $foc_claim_sea_qty[$order_id][$item_id][$country_id][$color_id]['TTL_AIR_QTY_CLAIM'];
											$ttl_sea_qty = $foc_claim_sea_qty[$order_id][$item_id][$country_id][$color_id]['TTL_SEA_QTY'];

											$ttl_shipment_qty =($ttl_air_qty_foc+$ttl_air_qty_claim+$ttl_sea_qty+$road_qty);
											// echo $ttl_shipment_qty."<br>";

											$cut_to_ship_balance=$ttl_shipment_qty-$ttl_cutting_qty;
											$cut_to_ship_percentage=$ttl_shipment_qty*100/$ttl_cutting_qty;

											$excess_short_qty = $ttl_shipment_qty - $value['ORDER_QUANTITY'];

											if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
		        							?>
								        	<tr bgcolor="<?= $bgcolor;  ?>" onclick="change_color('trdsw_<?= $i; ?>','<?= $bgcolor; ?>')" id="trdsw_<?= $i; ?>">
									        	<td width="50" align="center"><?= $i; ?></td>
									        	<td width="150"><p><?= $ex_factory_date_all; ?></p></td>
									        	<td width="150"><p><a href="##" onClick="openmypage_challan_popup('<?= $cbo_company_name; ?>','<?= $cbo_work_company_name; ?>','<?= $order_id; ?>','<?= $job_no; ?>','<?= $cbo_buyer_name; ?>','<?= $cbo_location_name; ?>','<?= $cbo_floor_name; ?>','<?= $txt_date_from; ?>','<?= $txt_date_to; ?>','challan_popup')"><?= $challan_no; ?></a></p></td>
									        	<td width="100"><p><?= $buyer_arr[$buyer_id]; ?></p></td>
									        	<td width="100"><p><?= $buyer_arr[$value['BUYER_CLIENT']]; ?></p></td>
									        	<td width="100"><p><?= $value['STYLE_REF_NO']; ?></p></td>
									        	<td width="100"><p><?= $value['JOB_NO']; ?></p></td>
									        	<td width="100"><p><?= $season_arr[$value['SEASON']]; ?></p></td>
									        	<td width="100"><p><?= $value['PO_NUMBER']; ?></p></td>
									        	<td width="100"><p><?= $country_arr[$country_id]; ?></p></td>
									        	<td width="100"><p><?= $garments_item[$item_id];; ?></p></td>
									        	<td width="100"><p><?= $color_arr[$color_id]; ?></p></td>
									        	<td width="80" align="right"><p><?= number_format($value['ORDER_QUANTITY'],0); ?></p></td>
									        	<td width="80" align="right"><p><?= number_format($cutting_qty,0); ?></p></td>
									        	<td width="80" align="right"><p><?= number_format($ttl_cutting_qty,0); ?></p></td>
									        	<td width="80" align="right"><p><?= number_format($input_qty,0); ?></p></td>
									        	<td width="80" align="right"><p><?= number_format($ttl_input_qty,0); ?></p></p></td>
									        	<td width="80" align="right"><p><?= number_format($poly_qty,0); ?></p></td>
									        	<td width="80" align="right"><p><?= number_format($ttl_poly_qty,0); ?></p></td>
									        	<td width="80" align="right" title="Rreject Output Qty+Reject Poly Qty+Reject Finishing Qty"><p><?= number_format($ttl_gmts_reject,0); ?></p></td>
									        	<td width="80" align="right"><p><?= number_format($finishing_qty,0); ?></p></td>
									        	<td width="80" align="right"><p><?= number_format($ttl_finishing_qty,0); ?></p></td>
									        	<td width="80" align="right"><p><?= number_format($air_qty_foc,0); ?></p></td>
									        	<td width="80" align="right"><p><?= number_format($air_qty_claim,0); ?></p></td>
									        	<td width="80" align="right"><p><?= number_format($sea_qty,0); ?></p></td>
												<td width="80" align="right"><p><?= number_format($road_qty,0); ?></p></td>
									        	<td width="80" align="right" title="AIR Qty FOC+AIR Qty Claim+SEA Qty+ROAD_QTY"><p><?= number_format($shipment_qty,0); ?></p></td>
									        	<td width="80" align="right" title="TTL AIR Qty FOC+TTL AIR Qty Claim+TTL SEA Qty"><p><?= number_format($ttl_shipment_qty,0); ?></p></td>
									        	<td width="80" align="right" title="TTL Shipment Qty-TTL Cutting Qty"><p><?= number_format($cut_to_ship_balance,0); ?></p></td>
									        	<td width="80" align="right" title="TTL Shipment Qty*100/TTL Cutting Qty"><p><?= number_format($cut_to_ship_percentage,0); ?></p></td>
									        	<td width="80" align="right" title="TTL Shipment Qty-Order Qty"><p><?= number_format($excess_short_qty,0); ?></p></td>
									        	<td width="100" align="right"><p><?= $value['SHIPING_STATUS']; ?></p></td>
									        </tr>
									        <?
									        $i++;
									        $po_tot_order_quantity+=$value['ORDER_QUANTITY'];
									        $po_tot_cutting_qty+=$cutting_qty;
									        $po_tot_ttl_cutting_qty+=$ttl_cutting_qty;
									        $po_tot_input_qty+=$input_qty;
									        $po_tot_ttl_input_qty+=$ttl_input_qty;
									        $po_tot_poly_qty+=$poly_qty;
									        $po_tot_ttl_poly_qty+=$ttl_poly_qty;
									        $po_tot_ttl_gmts_reject+=$ttl_gmts_reject;
									        $po_tot_finishing_qty+=$finishing_qty;
									        $po_tot_ttl_finishing_qty+=$ttl_finishing_qty;
									        $po_tot_air_qty_foc+=$air_qty_foc;
									        $po_tot_air_qty_claim+=$air_qty_claim;
									        $po_tot_sea_qty+=$sea_qty;
											$po_tot_road_qty+=$road_qty;
									        $po_tot_shipment_qty+=$shipment_qty;
									        $po_tot_ttl_shipment_qty+=$ttl_shipment_qty;
									        $po_tot_excess_short_qty+=$excess_short_qty;

									        $job_tot_order_quantity+=$value['ORDER_QUANTITY'];
									        $job_tot_cutting_qty+=$cutting_qty;
									        $job_tot_ttl_cutting_qty+=$ttl_cutting_qty;
									        $job_tot_input_qty+=$input_qty;
									        $job_tot_ttl_input_qty+=$ttl_input_qty;
									        $job_tot_poly_qty+=$poly_qty;
									        $job_tot_ttl_poly_qty+=$ttl_poly_qty;
									        $job_tot_ttl_gmts_reject+=$ttl_gmts_reject;
									        $job_tot_finishing_qty+=$finishing_qty;
									        $job_tot_ttl_finishing_qty+=$ttl_finishing_qty;
									        $job_tot_air_qty_foc+=$air_qty_foc;
									        $job_tot_air_qty_claim+=$air_qty_claim;
									        $job_tot_sea_qty+=$sea_qty;
											$job_tot_road_qty+=$road_qty;
									        $job_tot_shipment_qty+=$shipment_qty;
									        $job_tot_ttl_shipment_qty+=$ttl_shipment_qty;
									        $job_tot_excess_short_qty+=$excess_short_qty;

									        $buyer_tot_order_quantity+=$value['ORDER_QUANTITY'];
									        $buyer_tot_cutting_qty+=$cutting_qty;
									        $buyer_tot_ttl_cutting_qty+=$ttl_cutting_qty;
									        $buyer_tot_input_qty+=$input_qty;
									        $buyer_tot_ttl_input_qty+=$ttl_input_qty;
									        $buyer_tot_poly_qty+=$poly_qty;
									        $buyer_tot_ttl_poly_qty+=$ttl_poly_qty;
									        $buyer_tot_ttl_gmts_reject+=$ttl_gmts_reject;
									        $buyer_tot_finishing_qty+=$finishing_qty;
									        $buyer_tot_ttl_finishing_qty+=$ttl_finishing_qty;
									        $buyer_tot_air_qty_foc+=$air_qty_foc;
									        $buyer_tot_air_qty_claim+=$air_qty_claim;
									        $buyer_tot_sea_qty+=$sea_qty;
											$buyer_tot_road_qty+=$road_qty;
									        $buyer_tot_shipment_qty+=$shipment_qty;
									        $buyer_tot_ttl_shipment_qty+=$ttl_shipment_qty;
									        $buyer_tot_excess_short_qty+=$excess_short_qty;

									        $grand_tot_order_quantity+=$value['ORDER_QUANTITY'];
									        $grand_tot_cutting_qty+=$cutting_qty;
									        $grand_tot_ttl_cutting_qty+=$ttl_cutting_qty;
									        $grand_tot_input_qty+=$input_qty;
									        $grand_tot_ttl_input_qty+=$ttl_input_qty;
									        $grand_tot_poly_qty+=$poly_qty;
									        $grand_tot_ttl_poly_qty+=$ttl_poly_qty;
									        $grand_tot_ttl_gmts_reject+=$ttl_gmts_reject;
									        $grand_tot_finishing_qty+=$finishing_qty;
									        $grand_tot_ttl_finishing_qty+=$ttl_finishing_qty;
									        $grand_tot_air_qty_foc+=$air_qty_foc;
									        $grand_tot_air_qty_claim+=$air_qty_claim;
									        $grand_tot_sea_qty+=$sea_qty;
											$grand_tot_road_qty+=$road_qty;
									        $grand_tot_shipment_qty+=$shipment_qty;
									        $grand_tot_ttl_shipment_qty+=$ttl_shipment_qty;
									        $grand_tot_excess_short_qty+=$excess_short_qty;

									    }
									}
								}
								//echo $po_tot_ttl_cutting_qty.'**'.$po_tot_ttl_shipment_qty.'system';
								?>
								<tr bgcolor="#CCCCCC">
									<!-- <td width="50">&nbsp;</td>
									<td width="150">&nbsp;</td>
									<td width="150">&nbsp;</td>
									<td width="100">&nbsp;</td>
									<td width="100">&nbsp;</td>
									<td width="100">&nbsp;</td>
									<td width="100">&nbsp;</td>
									<td width="100">&nbsp;</td>
									<td width="100">&nbsp;</td>
									<td width="100">&nbsp;</td>
									<td width="100">&nbsp;</td> -->
						        	<td colspan="12" align="right" style="font-weight:bold;">PO Total:</td>
						        	<td width="80" align="right"><p><?= number_format($po_tot_order_quantity,0); ?></p></td>
						        	<td width="80" align="right"><p><?= number_format($po_tot_cutting_qty,0); ?></p></td>
						        	<td width="80" align="right"><p><?= number_format($po_tot_ttl_cutting_qty,0); ?></p></td>
						        	<td width="80" align="right"><p><?= number_format($po_tot_input_qty,0); ?></p></td>
						        	<td width="80" align="right"><p><?= number_format($po_tot_ttl_input_qty,0); ?></p></p></td>
						        	<td width="80" align="right"><p><?= number_format($po_tot_poly_qty,0); ?></p></td>
						        	<td width="80" align="right"><p><?= number_format($po_tot_ttl_poly_qty,0); ?></p></td>
						        	<td width="80" align="right"><p><?= number_format($po_tot_ttl_gmts_reject,0); ?></p></td>
						        	<td width="80" align="right"><p><?= number_format($po_tot_finishing_qty,0); ?></p></td>
						        	<td width="80" align="right"><p><?= number_format($po_tot_ttl_finishing_qty,0); ?></p></td>
						        	<td width="80" align="right"><p><?= number_format($po_tot_air_qty_foc,0); ?></p></td>
						        	<td width="80" align="right"><p><?= number_format($po_tot_air_qty_claim,0); ?></p></td>
						        	<td width="80" align="right"><p><?= number_format($po_tot_sea_qty,0); ?></p></td>
									<td width="80" align="right"><p><?= number_format($po_tot_road_qty,0); ?></p></td>
						        	<td width="80" align="right"><p><?= number_format($po_tot_shipment_qty,0); ?></p></td>
						        	<td width="80" align="right"><p><?= number_format($po_tot_ttl_shipment_qty,0); ?></p></td>
						        	<td width="80" align="right"><p><?= number_format($po_tot_ttl_shipment_qty-$po_tot_ttl_cutting_qty,0); ?></p></td>
						        	<td width="80" align="right"><p><?= number_format($po_tot_ttl_shipment_qty*100/$po_tot_ttl_cutting_qty,0); ?></p></td>
						        	<td width="80" align="right"><p><?= number_format($po_tot_excess_short_qty,0); ?></p></td>
						        	<td width="100" align="right"><p>&nbsp;</p></td>
						        </tr>
								<?
							}
							?>
							<tr bgcolor="#F4F3C4">
					        	<td align="right" colspan="12" style="font-weight:bold;">Job Total:</td>
					        	<td width="80" align="right"><p><?= number_format($job_tot_order_quantity,0); ?></p></td>
					        	<td width="80" align="right"><p><?= number_format($job_tot_cutting_qty,0); ?></p></td>
					        	<td width="80" align="right"><p><?= number_format($job_tot_ttl_cutting_qty,0); ?></p></td>
					        	<td width="80" align="right"><p><?= number_format($job_tot_input_qty,0); ?></p></td>
					        	<td width="80" align="right"><p><?= number_format($job_tot_ttl_input_qty,0); ?></p></p></td>
					        	<td width="80" align="right"><p><?= number_format($job_tot_poly_qty,0); ?></p></td>
					        	<td width="80" align="right"><p><?= number_format($job_tot_ttl_poly_qty,0); ?></p></td>
					        	<td width="80" align="right"><p><?= number_format($job_tot_ttl_gmts_reject,0); ?></p></td>
					        	<td width="80" align="right"><p><?= number_format($job_tot_finishing_qty,0); ?></p></td>
					        	<td width="80" align="right"><p><?= number_format($job_tot_ttl_finishing_qty,0); ?></p></td>
					        	<td width="80" align="right"><p><?= number_format($job_tot_air_qty_foc,0); ?></p></td>
					        	<td width="80" align="right"><p><?= number_format($job_tot_air_qty_claim,0); ?></p></td>
					        	<td width="80" align="right"><p><?= number_format($job_tot_sea_qty,0); ?></p></td>
								<td width="80" align="right"><p><?= number_format($job_tot_road_qty,0); ?></p></td>
					        	<td width="80" align="right"><p><?= number_format($job_tot_shipment_qty,0); ?></p></td>
					        	<td width="80" align="right"><p><?= number_format($job_tot_ttl_shipment_qty,0); ?></p></td>
					        	<td width="80" align="right"><p><?= number_format($job_tot_ttl_shipment_qty-$job_tot_ttl_cutting_qty,0); ?></p></td>
					        	<td width="80" align="right"><p><?= number_format($job_tot_ttl_shipment_qty*100/$job_tot_ttl_cutting_qty,0); ?></p></td>
					        	<td width="80" align="right"><p><?= number_format($job_tot_excess_short_qty,0); ?></p></td>
					        	<td width="100" align="right"><p>&nbsp;</p></td>
					        </tr>
							<?
						}
						?>
						<tr bgcolor="#CCCCCC">
				        	<td align="right" colspan="12" style="font-weight:bold;">Buyer Total:</td>
				        	<td width="80" align="right"><p><?= number_format($buyer_tot_order_quantity,0); ?></p></td>
				        	<td width="80" align="right"><p><?= number_format($buyer_tot_cutting_qty,0); ?></p></td>
				        	<td width="80" align="right"><p><?= number_format($buyer_tot_ttl_cutting_qty,0); ?></p></td>
				        	<td width="80" align="right"><p><?= number_format($buyer_tot_input_qty,0); ?></p></td>
				        	<td width="80" align="right"><p><?= number_format($buyer_tot_ttl_input_qty,0); ?></p></p></td>
				        	<td width="80" align="right"><p><?= number_format($buyer_tot_poly_qty,0); ?></p></td>
				        	<td width="80" align="right"><p><?= number_format($buyer_tot_ttl_poly_qty,0); ?></p></td>
				        	<td width="80" align="right"><p><?= number_format($buyer_tot_ttl_gmts_reject,0); ?></p></td>
				        	<td width="80" align="right"><p><?= number_format($buyer_tot_finishing_qty,0); ?></p></td>
				        	<td width="80" align="right"><p><?= number_format($buyer_tot_ttl_finishing_qty,0); ?></p></td>
				        	<td width="80" align="right"><p><?= number_format($buyer_tot_air_qty_foc,0); ?></p></td>
				        	<td width="80" align="right"><p><?= number_format($buyer_tot_air_qty_claim,0); ?></p></td>
				        	<td width="80" align="right"><p><?= number_format($buyer_tot_sea_qty,0); ?></p></td>
							<td width="80" align="right"><p><?= number_format($buyer_tot_road_qty,0); ?></p></td>
				        	<td width="80" align="right"><p><?= number_format($buyer_tot_shipment_qty,0); ?></p></td>
				        	<td width="80" align="right"><p><?= number_format($buyer_tot_ttl_shipment_qty,0); ?></p></td>
				        	<td width="80" align="right"><p><?= number_format($buyer_tot_ttl_shipment_qty-$buyer_tot_ttl_cutting_qty,0); ?></p></td>
				        	<td width="80" align="right"><p><?= number_format($buyer_tot_ttl_shipment_qty*100/$buyer_tot_ttl_cutting_qty,0); ?></p></td>
				        	<td width="80" align="right"><p><?= number_format($buyer_tot_excess_short_qty,0); ?></p></td>
				        	<td width="100" align="right"><p>&nbsp;</p></td>
					    </tr>
						<?
					}
					?>
				</tbody>
				<tfoot>
					<tr>
						<th width="50"><p>&nbsp;</p></th>
						<th width="150"><p>&nbsp;</p></th>
						<th width="150"><p>&nbsp;</p></th>
						<th width="100"><p>&nbsp;</p></th>
						<th width="100"><p>&nbsp;</p></th>
						<th width="100"><p>&nbsp;</p></th>
						<th width="100"><p>&nbsp;</p></th>
						<th width="100"><p>&nbsp;</p></th>
						<th width="100"><p>&nbsp;</p></th>
						<th width="100"><p>&nbsp;</p></th>
						<th width="100"><p>&nbsp;</p></th>
						<th width="100" align="right" style="font-weight:bold;"><p>Grand Total:</p></th>
						<th width="80" align="right"><p><?= number_format($grand_tot_order_quantity,0); ?></p></th>
						<th width="80" align="right"><p><?= number_format($grand_tot_cutting_qty,0); ?></p></th>
						<th width="80" align="right"><p><?= number_format($grand_tot_ttl_cutting_qty,0); ?></p></th>
						<th width="80" align="right"><p><?= number_format($grand_tot_input_qty,0); ?></p></th>
						<th width="80" align="right"><p><?= number_format($grand_tot_ttl_input_qty,0); ?></p></p></th>
						<th width="80" align="right"><p><?= number_format($grand_tot_poly_qty,0); ?></p></th>
						<th width="80" align="right"><p><?= number_format($grand_tot_ttl_poly_qty,0); ?></p></th>
						<th width="80" align="right"><p><?= number_format($grand_tot_ttl_gmts_reject,0); ?></p></th>
						<th width="80" align="right"><p><?= number_format($grand_tot_finishing_qty,0); ?></p></th>
						<th width="80" align="right"><p><?= number_format($grand_tot_ttl_finishing_qty,0); ?></p></th>
						<th width="80" align="right"><p><?= number_format($grand_tot_air_qty_foc,0); ?></p></th>
						<th width="80" align="right"><p><?= number_format($grand_tot_air_qty_claim,0); ?></p></th>
						<th width="80" align="right"><p><?= number_format($grand_tot_sea_qty,0); ?></p></th>
						<th width="80" align="right"><p><?= number_format($grand_tot_road_qty,0); ?></p></th>
						<th width="80" align="right"><p><?= number_format($grand_tot_shipment_qty,0); ?></p></th>
						<th width="80" align="right"><p><?= number_format($grand_tot_ttl_shipment_qty,0); ?></p></th>
						<th width="80" align="right"><p><?= number_format($grand_tot_ttl_shipment_qty-$grand_tot_ttl_cutting_qty,0); ?></p></th>
						<th width="80" align="right"><p><?= number_format($grand_tot_ttl_shipment_qty*100/$grand_tot_ttl_cutting_qty,0); ?></p></th>
						<th width="80" align="right"><p><?= number_format($grand_tot_excess_short_qty,0); ?></p></th>
						<th width="100" align="right"><p>&nbsp;</p></th>
					</tr>
		        </foot>
		</table>

	</div>
	<?
    foreach (glob("$user_id*.xls") as $filename)
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}

	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	echo "$total_data####$filename";
	exit();
}

if ($action==="challan_popup")
{
	echo load_html_head_contents("Challan Info","../../../", 1, 1, '','','');
	extract($_REQUEST);
	list($company_id, $work_comp_ids, $order_id, $job_no, $buyer_id, $location_ids, $floor_ids, $txt_date_from, $txt_date_to) = explode('**', $data);
	//echo $company_id;die;
	$company_arr=return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0", 'id','company_name');

	$company_cond=$work_comp_cond=$buyer_cond='';
	$job_no_cond=$location_cond=$floor_cond='';

	if ($company_id != 0) $company_cond=" and d.company_name=$company_id";
	if ($work_comp_ids != '') $work_comp_cond=" and a.delivery_company_id in($work_comp_ids)";
	if ($buyer_id != 0) $buyer_cond=" and d.buyer_name=$buyer_id";
	if ($job_no != '') $job_no_cond=" and d.job_no='".$job_no."'";
	if ($location_ids != '') $location_cond=" and a.delivery_location_id in($location_ids)";
	if ($floor_ids != '') $floor_cond=" and a.delivery_floor_id in($floor_ids)";

	$date_cond = '';
	if ($txt_date_from != '' && $txt_date_to != '') {
		$date_cond = " and b.ex_factory_date between '".$txt_date_from."' and '".$txt_date_to."'";
	}


	$sql_challan_dtls = "SELECT  a.sys_number as CHALLAN_NO, a.DELIVERY_COMPANY_ID, b.EX_FACTORY_DATE, b.EX_FACTORY_QNTY, b.SHIPING_MODE
	from pro_ex_factory_delivery_mst a, pro_ex_factory_mst b, wo_po_break_down c, wo_po_details_master d
	where a.id=b.delivery_mst_id and b.po_break_down_id=c.id and c.job_no_mst=d.job_no and c.id=$order_id $company_cond $work_comp_cond $location_cond $floor_cond $buyer_cond $job_no_cond $date_cond and b.entry_form!=85 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active in(1,2,3) and c.is_deleted=0 and c.is_deleted=0 and d.status_active in(1,2,3) and d.is_deleted=0
	order by b.ex_factory_date ASC";

	$sql_challan_dtls_res=sql_select($sql_challan_dtls);
	$table_width = 600;
	?>
	<div style="width:100%" id="report_container">
		<table width="<? echo $table_width; ?>" cellspacing="0" cellpadding="0">
			<thead>
				<tr>
					<td colspan="6" style="font-size:16px" width="100%" align="center"><strong>Challan Info</strong>
					</td>
				</tr>
			</thead>
		</table>
			<table border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>" cellpadding="0" cellspacing="0" id="table_header">
				<thead>
					<tr>
	                    <th width="50">SL</th>
	                    <th width="100">Date</th>
	                    <th width="120">Delivery Company</th>
	                    <th width="120">Challan No</th>
	                    <th width="100">Quantity</th>
	                    <th width="100">Ship Mode</th>
                    </tr>
				</thead>
            </table>
            <div style="width:<? echo $table_width+20; ?>px; overflow-y:scroll; max-height:250px;font-size:12px; overflow-x:hidden;" id="scroll_body">
				<table border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>" cellpadding="0" cellspacing="0" id="table_body">
	                <tbody>
	                <?
               		$i=1;
               		$total_quantity=0;
               		foreach ($sql_challan_dtls_res as $row)
               		{
	               		if($i%2==0) $bgcolor="#E9F3FF";
	               		else $bgcolor="#FFFFFF";
	               		?>
						<tr bgcolor="<?= $bgcolor; ?>" onClick="change_color('tr_<?= $i; ?>','<?= $bgcolor; ?>')" id="tr_<?= $i; ?>">
							<td width="50" align="center"><?= $i; ?></td>
							<td width="100"><p><?= change_date_format($row['EX_FACTORY_DATE']); ?></p></td>
                            <td width="120"><p><?= $company_arr[$row['DELIVERY_COMPANY_ID']]; ?></p></td>
                            <td width="120"><p><?= $row['CHALLAN_NO']; ?></p></td>
                            <td width="100" align="right"><p><?= number_format($row['EX_FACTORY_QNTY'],0); ?></p></td>
                            <td width="100" align="center"><p><?= $shipment_mode[$row['SHIPING_MODE']]; ?></p></td>
                        </tr>
	                    <?
	                    $i++;
	                    $total_quantity += $row['EX_FACTORY_QNTY'];
                    }
                    ?>
	                </tbody>
	            </table>

	            <table border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>" cellpadding="0" cellspacing="0" id="report_table_footer">
					<tfoot>
						<th width="50">&nbsp;</th>
	                    <th width="100">&nbsp;</th>
	                    <th width="120">&nbsp;</th>
	                    <th width="120">Total:</th>
	                    <th width="100"><?= number_format($total_quantity,0); ?></th>
	                    <th width="100">&nbsp;</th>
					</tfoot>
	            </table>
            </div>
        </div>
    <?
	exit();
}
