<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');

$user_id=$_SESSION['logic_erp']['user_id'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 130, "select a.id,a.buyer_name from lib_buyer a, lib_buyer_tag_company b where a.id=b.buyer_id and b.tag_company=$data and a.status_active=1 and a.is_deleted=0 order by a.buyer_name","id,buyer_name", 1, "-- Select buyer --", 0, "","" );//load_drop_down( 'requires/daily_knitting_production_report_controller',document.getElementById('cbo_company_id').value+'_'+this.value, 'load_drop_machine', 'machine_td' );$location_cond
  exit();
}
if($db_type==0) $insert_year="SUBSTRING_INDEX(a.insert_date, '-', 1)";
if($db_type==2) $insert_year="extract( year from b.insert_date)";
//item style------------------------------//

if($action=="job_popup")
{
	echo load_html_head_contents("Search Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>

	<script>

		var selected_id = new Array; var selected_name = new Array;var selected_style = new Array;var selected_id_arr = new Array;

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

			toggle( document.getElementById( 'tr_' + str[0] ), '#FFFFCC' );

			if( jQuery.inArray( str[0], selected_id_arr ) == -1 ) {
				selected_id_arr.push( str[0] );
				selected_id.push( str[1] );
				selected_name.push( str[2] );
				selected_style.push( str[3] );
				selected_internal.push( str[4] );


			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == str[1] ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
				selected_style.splice( i, 1 );
				selected_internal.splice( i, 1 );
			}
			var id = ''; var name = '';var style = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + '*';
				style += selected_style[i] + '*';
				internal += selected_style[i] + '*';

			}

			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );
			style = style.substr( 0, style.length - 1 );
			internal = style.substr( 0, style.length - 1 );


			$('#hide_job_id').val( id );
			$('#hide_job_no').val( name );
			$('#hide_style_no').val( style );
			$('#hide_internale_ref').val(internal);



		}

    </script>

	</head>

	<body>
	<div align="center">
		<form name="styleRef_form" id="styleRef_form">
			<fieldset style="width:710px;">
	            <table width="700" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
	            	<thead>
	                    <th class="must_entry_caption">Company Name</th>
	                    <th>Buyer</th>
	                    <th>Year</th>
	                    <th>Search By</th>
	                    <th id="search_by_td_up" width="100">Job No</th>
	                    <th>
                            <input type="reset" name="button" class="formbutton" value="Reset"  style="width:80px;">
                            <input type="hidden" name="hide_job_no" id="hide_job_no" value="" />
                            <input type="hidden" name="hide_job_id" id="hide_job_id" value="" />
                            <input type="hidden" name="hide_style_no" id="hide_style_no" value="" />
							<input type="hidden" name="hide_internal_ref" id="hide_internal_ref" value="" />
                        </th>
	                </thead>
	                <tbody>
	                	<tr>
	                        <td align="center">
	                        	 <?
									echo create_drop_down( "cbo_company_name", 130, "SELECT id,company_name from lib_company comp where status_active =1 and is_deleted=0 order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'hourly_cutting_production_monitoring_report_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );" );
								?>
	                        </td>
	                        <td align="center" id="buyer_td">
	                        	 <?
									echo create_drop_down( "cbo_buyer_name", 130, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$company_name $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",0,"",0 );
								?>
	                        </td>
	                        <td align="center">
	                    	<?
								echo create_drop_down( "cbo_year", 110, $year,"",1, "--Select--", "",'',0 );
							?>
	                        </td>
	                        <td align="center">
	                    	<?
	                       		$search_by_arr=array(1=>"Job No",2=>"Style Ref",3=>"IR/IB");
								$dd="change_search_event(this.value, '0*0*0', '0*0*0', '../../') ";
								echo create_drop_down( "cbo_search_by", 100, $search_by_arr,"",0, "--Select--", "",$dd,0 );
							?>
	                        </td>
	                        <td align="center" id="search_by_td">
	                            <input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />
	                        </td>
	                        <td align="center">
	                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view (document.getElementById('cbo_company_name').value+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+document.getElementById('cbo_year').value, 'search_list_view', 'search_div', 'hourly_cutting_production_monitoring_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:80px;" />
	                    	</td>
	                    </tr>
	            	</tbody>
	           	</table>
	            <div style="margin-top:15px" id="search_div"></div>
			</fieldset>
		</form>
	</div>
	</body>
	<script type="text/javascript">
		$("#cbo_year").val('<?=$cbo_year;?>');
	</script>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if($action=="search_list_view")
{
	$data=explode('**',$data);
	$company_id=$data[0];

	if($data[1]==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		$buyer_id_cond=" and a.buyer_name=$data[1]";
	}

	$search_by=$data[2];
	if(str_replace("'", "", $data[3])!="")
	{
		$search_string="".trim($data[3])."";
	}

	if($search_by==1)
		$search_field="a.job_no_prefix_num";
	else if($search_by==2)
		$search_field="a.style_ref_no";
	else
	    $search_field="b.grouping";
	$search_cond="";
	if($search_string!="")	{$search_cond=" and $search_field='$search_string'";}
	$job_year =$data[4];

	if($job_year!=0)
	{
		if($db_type==0)
		{
			$job_year_cond=" and year(a.insert_date)='$job_year'";
		}
		else
		{
			$job_year_cond=" and to_char(a.insert_date,'YYYY')='$job_year'";
		}
	}
	else
	{
		$job_year_cond="";
	}
	$company_library=return_library_array( "SELECT id, company_name from lib_company", "id", "company_name"  );
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$arr=array (0=>$company_library,1=>$buyer_arr);

	if($db_type==0) $year_field="YEAR(a.insert_date) as year";
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
	else $year_field="";


	$sql= "SELECT a.id, a.job_no, $year_field, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no,b.grouping from wo_po_details_master a,wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_name=$company_id $search_cond $buyer_id_cond $job_no_cond $job_year_cond group by a.id,
         a.job_no, a.insert_date, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no,b.grouping order by a.id desc";
    // echo $sql;

	echo create_list_view("tbl_list_search", "Company,Buyer Name,Year,Job No,Style Ref. No", "100,100,50,100","550","220",0, $sql , "js_set_value", "id,job_no,style_ref_no","",1,"company_name,buyer_name,0,0,0,0",$arr,"company_name,buyer_name,year,job_no,style_ref_no","",'','0,0,0,0,0','',1) ;
   exit();
}


if($action=="generate_report")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$company_name 		= str_replace("'","",$cbo_company_name);
	$wo_company_name 	= str_replace("'","",$cbo_wo_company_name);
	$buyer_name 		= str_replace("'","",$cbo_buyer_name);
	$hidden_job_id 		= str_replace("'","",$hidden_job_id);
	$cutting_date 		= str_replace("'","",$txt_date);

	// ========================= lay cond ========================
	$sql_lay_cond .= ($company_name != 0) 		? " and a.company_name=$company_name" : "";
	$sql_lay_cond .= ($buyer_name != 0) 		? " and a.buyer_name=$buyer_name" : "";
	$sql_lay_cond .= ($hidden_job_id != "") 	? " and a.id in($hidden_job_id)" : "";
	$sql_lay_cond .= ($wo_company_name != "") 	? " and c.working_company_id in($wo_company_name)" : "";

	if($cutting_date !="")
    {
        if($db_type==0)
        {
            $start_date=change_date_format($cutting_date,"yyyy-mm-dd","");
        }
        else
        {
            $start_date=date("j-M-Y",strtotime($cutting_date));
        }
        $sql_lay_cond.= " and c.entry_date='$start_date'";
    }

	$company_lib=return_library_array( "SELECT id, company_name from lib_company", "id", "company_name"  );
	$location_lib=return_library_array( "SELECT id, location_name from lib_location", "id", "location_name"  );
	$floor_lib=return_library_array( "select id,floor_name from lib_prod_floor", "id", "floor_name"  );
    $season_lib = return_library_array("select id, season_name from lib_buyer_season where status_active =1 and is_deleted=0", "id", "season_name");
    $buyer_lib  = return_library_array("select id, buyer_name from  lib_buyer", "id", "buyer_name");
	$color_lib=return_library_array( "select id,color_name from lib_color", "id", "color_name" );
	$size_lib=return_library_array( "select id,size_name from lib_size", "id", "size_name" );
	$lineArr = return_library_array("select a.id,a.line_name from lib_sewing_line a","id","line_name");
	$prod_reso_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');

	// echo $date = date('d-m-Y H:i a',strtotime($start_date.' 11:20'));
	// echo date('H A',strtotime($date));

	/*==========================================================================================/
	/										getting lay  data 									/
	/==========================================================================================*/

	if($type==1){
	$sql=" SELECT a.id as job_id,a.job_no,a.buyer_name,a.style_ref_no as style,a.client_id,d.gmt_item_id as item_id,c.start_time,
		e.size_qty as total_lay,e.id as bndle_id,c.table_no
		from wo_po_details_master a,wo_po_break_down b,ppl_cut_lay_mst c,ppl_cut_lay_dtls d,ppl_cut_lay_bundle e where a.job_no=c.job_no and a.id=b.job_id and c.id=d.mst_id and b.id=e.order_id and c.id=e.mst_id and d.id=e.dtls_id and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and e.size_qty>0 $sql_lay_cond";
	// echo $sql;die;
	$sql_res = sql_select($sql);
    if(count($sql_res)==0)
    {
        ?>
        <center>
            <div style="width: 80%;" class="alert alert-danger">Data not found.Please try again.</div>
        </center>
        <?
        die();
    }

	foreach ($sql_res as $val)
	{
		$table_id_array[$val['TABLE_NO']] = $val['TABLE_NO'];
	}
	$table_id = implode(",", $table_id_array);
	$table_no_arr=return_library_array( "select id, table_no from lib_cutting_table where id in($table_id)",'id','table_no');

    $hour = array();
	$data_array = array();
	$table_id_array = array();
	foreach ($sql_res as $val)
	{
		$prod_hour = date('H:00 A',strtotime($start_date.' '.$val['START_TIME']));
    	$hour[$prod_hour] = $prod_hour;

		$data_array[$table_no_arr[$val['TABLE_NO']]]['style'] .= $val['STYLE']."**";
		$data_array[$table_no_arr[$val['TABLE_NO']]]['buyer_name'] .= $val['BUYER_NAME']."**";
		$data_array[$table_no_arr[$val['TABLE_NO']]]['client_id'] .= $val['CLIENT_ID']."**";
		$data_array[$table_no_arr[$val['TABLE_NO']]]['job_no'] .= $val['JOB_NO']."**";
		$data_array[$table_no_arr[$val['TABLE_NO']]]['item_id'] .= $val['ITEM_ID']."**";

		$data_array[$table_no_arr[$val['TABLE_NO']]][$prod_hour] += $val['TOTAL_LAY'];
		$data_array[$table_no_arr[$val['TABLE_NO']]]['total_prod'] += $val['TOTAL_LAY'];
		$data_array[$table_no_arr[$val['TABLE_NO']]]['table_no'] .= $val['TABLE_NO']."**";
	}

	ksort($hour);
	// echo "<pre>";print_r($data_array);echo "</pre>";
	$tbl_width = 550+count($hour)*50;
	$col_span = 7+count($hour);
	ob_start();
	?>
	<fieldset style="width:<?=$tbl_width+20;?>px;">

		<div style="width:<?=$tbl_width+20;?>px;">
			<table width="<?=$tbl_width;?>"  cellspacing="0">
				<tr class="form_caption" style="border:none;">
					<td colspan="<?=$col_span;?>" align="center" style="border:none;font-size:18px; font-weight:bold" >Hourly Cutting Production Monitoring Report</td>
				</tr>
				<tr class="form_caption" style="border:none;">
					<td colspan="<?=$col_span;?>" align="center" style="border:none;font-size:16px; font-weight:bold" ><?=$company_lib[$wo_company_name];?></td>
				</tr>
				<tr class="form_caption" style="border:none;">
					<td colspan="<?=$col_span;?>" align="center" style="border:none;font-size:15px; font-weight:bold" >Date: <?=$start_date;?></td>
				</tr>
			</table>

			<div style="width:<?=$tbl_width+20;?>px; float:left;">
					<table cellspacing="0" cellpadding="0"  border="1" style="border-collapse: collapse;" rules="all"  width="<?=$tbl_width;?>" class="rpt_table" align="left">
						<thead>
							<tr>
								<th width="20">Sl</th>
								<th width="50">Table</th>
								<th width="100">Buyer</th>
								<th width="100">Client</th>
								<th width="100">Style</th>
								<th width="100">Garment Item</th>
								<th width="80">Total Prod.</th>
								<?
								foreach ($hour as $key => $val)
								{
									?>
									<th width="50"><?=$key;?></th>
									<?
								}
								?>
							</tr>
						</thead>
					</table>

					<div style="max-height:200px; overflow-y:scroll; width:<?=$tbl_width+20;?>px;" id="scroll_body">
						<table  border="1" style="border-collapse: collapse;" class="rpt_table"  width="<?=$tbl_width;?>" rules="all" id="table_body"  align="left">
							<tbody>
								<?
								$i=1;
								ksort($data_array);
								$total_qty_arr = array();
								$total_cut = 0;
								foreach ($data_array as $table_no => $row)
								{
									$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
									$style = implode(", ",array_unique(array_filter(explode('**', $row['style']))));
									$job_no = implode("*",array_unique(array_filter(explode('**', $row['job_no']))));
									$table_ids = implode(",",array_unique(array_filter(explode('**', $row['table_no']))));
									$buyer_id_arr = array_unique(array_filter(explode('**', $row['buyer_name'])));
									$buyer_name = "";
									$buyer_id = "";
									foreach ($buyer_id_arr as $val)
									{
										$buyer_name .= ($buyer_name=="") ? $buyer_lib[$val] : ", ".$buyer_lib[$val];
										$buyer_id .= ($buyer_id=="") ? $val : ",".$val;
									}

									$buyer_client_id_arr = array_unique(array_filter(explode('**', $row['client_id'])));
									$client_name = "";
									$client_id = "";
									foreach ($buyer_client_id_arr as $val)
									{
										$client_name .= ($client_name=="") ? $buyer_lib[$val] : ", ".$buyer_lib[$val];
										$client_id .= ($client_id=="") ? $val : ",".$val;
									}

									$item_id_arr = array_unique(array_filter(explode('**', $row['item_id'])));
									$item_name = "";
									$item_id = "";
									foreach ($item_id_arr as $val)
									{
										$item_name .= ($item_name=="") ? $garments_item[$val] : ", ".$garments_item[$val];
										$item_id .= ($item_id=="") ? $val : ",".$val;
									}

									$search_string = $table_ids."**".$buyer_id."**".$client_id."**".$job_no."**".$item_id."**".$company_name."**".$wo_company_name."**".$cutting_date;

									?>
									<tr bgcolor="<? echo $bgcolor;?>" id="tr_<?= $i;?>" onClick="change_color('tr_<?= $i; ?>','<?= $bgcolor; ?>')">
										<td width="20"><p><?=$i;?></p></td>
										<td width="50"><p><?=$table_no;?></p></td>
										<td width="100"><p><?=$buyer_name;?></p></td>
										<td width="100"><p><?=$client_name;?></p></td>
										<td width="100"><p><?=$style;?></p></td>
										<td width="100"><p><?=$item_name;?></p></td>
										<td width="80" align="right">
											<a href="javascript:void(0)" onclick="openmypage_cutting_popup('<?=$search_string;?>')">
												<?=number_format($row['total_prod'],0);?>
											</a>
										</td>
										<?
										foreach ($hour as $h_key => $h_value)
										{
											?>
											<td align="right" width="50"><?=$row[$h_key];?></td>
											<?
											$total_qty_arr[$h_key] += $row[$h_key];
										}
										?>
									</tr>
									<?
									$i++;
									$total_cut += $row['total_prod'];

								}
								?>
							</tbody>
						</table>
					</div>
					<table style="border-collapse: collapse;"  border="1" class="rpt_table"  width="<?=$tbl_width;?>" rules="all" align="left" >
						<tfoot>
							<tr>
								<th width="20"></th>
								<th width="50"></th>
								<th width="100"></th>
								<th width="100"></th>
								<th width="100"></th>
								<th width="100">Grand Total</th>
								<th width="80"><?=number_format($total_cut,0);?></th>
								<?
								foreach ($hour as $h_key => $h_value)
								{
									?>
									<th align="right" width="50"><?=$total_qty_arr[$h_key];?></th>
									<?
								}
								?>
							</tr>
						</tfoot>
					</table>
			</div>
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
	//$filename=$user_id."_".$name.".xls";

	echo "$total_data####$filename";
	exit();
}

	if($type==2){


	// echo $date = date('d-m-Y H:i a',strtotime($start_date.' 11:20'));
	// echo date('H A',strtotime($date));

	/*==========================================================================================/
	/										getting lay  data 									/
	/==========================================================================================*/

	$sql=" SELECT a.id as job_id,a.job_no,a.buyer_name,a.style_ref_no as style,a.client_id,d.gmt_item_id as item_id,c.start_time,
		e.size_qty as total_lay,e.id as bndle_id,c.table_no,b.grouping
		from wo_po_details_master a,wo_po_break_down b,ppl_cut_lay_mst c,ppl_cut_lay_dtls d,ppl_cut_lay_bundle e where a.job_no=c.job_no and a.id=b.job_id and c.id=d.mst_id and b.id=e.order_id and c.id=e.mst_id and d.id=e.dtls_id and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and e.size_qty>0 $sql_lay_cond";
	// echo $sql;die;
	$sql_res = sql_select($sql);
    if(count($sql_res)==0)
    {
        ?>
        <center>
            <div style="width: 80%;" class="alert alert-danger">Data not found.Please try again.</div>
        </center>
        <?
        die();
    }

	foreach ($sql_res as $val)
	{
		$table_id_array[$val['TABLE_NO']] = $val['TABLE_NO'];
	}
	$table_id = implode(",", $table_id_array);
	$table_no_arr=return_library_array( "select id, table_no from lib_cutting_table where id in($table_id)",'id','table_no');

    $hour = array();
	$data_array = array();
	$table_id_array = array();
	foreach ($sql_res as $val)
	{
		$prod_hour = date('H:00 A',strtotime($start_date.' '.$val['START_TIME']));
    	$hour[$prod_hour] = $prod_hour;

		$data_array[$table_no_arr[$val['TABLE_NO']]]['style'] .= $val['STYLE']."**";
		$data_array[$table_no_arr[$val['TABLE_NO']]]['buyer_name'] .= $val['BUYER_NAME']."**";
		$data_array[$table_no_arr[$val['TABLE_NO']]]['grouping'] .= $val['GROUPING']."**";
		$data_array[$table_no_arr[$val['TABLE_NO']]]['client_id'] .= $val['CLIENT_ID']."**";
		$data_array[$table_no_arr[$val['TABLE_NO']]]['job_no'] .= $val['JOB_NO']."**";
		$data_array[$table_no_arr[$val['TABLE_NO']]]['item_id'] .= $val['ITEM_ID']."**";

		$data_array[$table_no_arr[$val['TABLE_NO']]][$prod_hour] += $val['TOTAL_LAY'];
		$data_array[$table_no_arr[$val['TABLE_NO']]]['total_prod'] += $val['TOTAL_LAY'];
		$data_array[$table_no_arr[$val['TABLE_NO']]]['table_no'] .= $val['TABLE_NO']."**";
	}

	ksort($hour);
	//  echo "<pre>";print_r($data_array);echo "</pre>";
	$tbl_width = 650+count($hour)*50;
	$col_span = 7+count($hour);
	ob_start();
	?>
	<fieldset style="width:<?=$tbl_width+20;?>px;">

		<div style="width:<?=$tbl_width+20;?>px;">
			<table width="<?=$tbl_width;?>"  cellspacing="0">
				<tr class="form_caption" style="border:none;">
					<td colspan="<?=$col_span;?>" align="center" style="border:none;font-size:18px; font-weight:bold" >Hourly Cutting Production Monitoring Report</td>
				</tr>
				<tr class="form_caption" style="border:none;">
					<td colspan="<?=$col_span;?>" align="center" style="border:none;font-size:16px; font-weight:bold" ><?=$company_lib[$wo_company_name];?></td>
				</tr>
				<tr class="form_caption" style="border:none;">
					<td colspan="<?=$col_span;?>" align="center" style="border:none;font-size:15px; font-weight:bold" >Date: <?=$start_date;?></td>
				</tr>
			</table>

			<div style="width:<?=$tbl_width+20;?>px; float:left;">
					<table cellspacing="0" cellpadding="0"  border="1" style="border-collapse: collapse;" rules="all"  width="<?=$tbl_width;?>" class="rpt_table" align="left">
						<thead>
							<tr>
								<th width="20">Sl</th>
								<th width="50">Table</th>
								<th width="100">Buyer</th>
								<th width="100">IR/IB</th>
								<th width="100">Client</th>
								<th width="100">Style</th>
								<th width="100">Garment Item</th>
								<th width="80">Total Prod.</th>
								<?
								foreach ($hour as $key => $val)
								{
									?>
									<th width="50"><?=$key;?></th>
									<?
								}
								?>
							</tr>
						</thead>
					</table>

					<div style="max-height:200px; overflow-y:scroll; width:<?=$tbl_width+20;?>px;" id="scroll_body">
						<table  border="1" style="border-collapse: collapse;" class="rpt_table"  width="<?=$tbl_width;?>" rules="all" id="table_body"  align="left">
							<tbody>
								<?
								$i=1;
								ksort($data_array);
								$total_qty_arr = array();
								$total_cut = 0;
								foreach ($data_array as $table_no => $row)
								{
									$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
									$style = implode(", ",array_unique(array_filter(explode('**', $row['style']))));
									$grouping = implode(", ",array_unique(array_filter(explode('**', $row['grouping']))));
									$job_no = implode("*",array_unique(array_filter(explode('**', $row['job_no']))));
									$table_ids = implode(",",array_unique(array_filter(explode('**', $row['table_no']))));


									$buyer_id_arr = array_unique(array_filter(explode('**', $row['buyer_name'])));
									$buyer_name = "";
									$buyer_id = "";
									foreach ($buyer_id_arr as $val)
									{
										$buyer_name .= ($buyer_name=="") ? $buyer_lib[$val] : ", ".$buyer_lib[$val];
										$buyer_id .= ($buyer_id=="") ? $val : ",".$val;
									}

									$buyer_client_id_arr = array_unique(array_filter(explode('**', $row['client_id'])));

									$client_name = "";
									$client_id = "";
									foreach ($buyer_client_id_arr as $val)
									{
										$client_name .= ($client_name=="") ? $buyer_lib[$val] : ", ".$buyer_lib[$val];
										$client_id .= ($client_id=="") ? $val : ",".$val;
									}

									$item_id_arr = array_unique(array_filter(explode('**', $row['item_id'])));
									$item_name = "";
									$item_id = "";
									foreach ($item_id_arr as $val)
									{
										$item_name .= ($item_name=="") ? $garments_item[$val] : ", ".$garments_item[$val];
										$item_id .= ($item_id=="") ? $val : ",".$val;
									}

									$search_string = $table_ids."**".$buyer_id."**".$client_id."**".$job_no."**".$item_id."**".$company_name."**".$wo_company_name."**".$cutting_date;

									?>
									<tr bgcolor="<? echo $bgcolor;?>" id="tr_<?= $i;?>" onClick="change_color('tr_<?= $i; ?>','<?= $bgcolor; ?>')">
										<td width="20"><p><?=$i;?></p></td>
										<td width="50"><p><?=$table_no;?></p></td>
										<td width="100"><p><?=$buyer_name;?></p></td>
										<td width="100"><p><?=$grouping;?></p></td>
										<td width="100"><p><?=$client_name;?></p></td>
										<td width="100"><p><?=$style;?></p></td>
										<td width="100"><p><?=$item_name;?></p></td>
										<td width="80" align="right">
											<a href="javascript:void(0)" onclick="openmypage_cutting_popup_one('<?=$search_string;?>')">
												<?=number_format($row['total_prod'],0);?>
											</a>
										</td>
										<?
										foreach ($hour as $h_key => $h_value)
										{
											?>
											<td align="right" width="50"><?=$row[$h_key];?></td>
											<?
											$total_qty_arr[$h_key] += $row[$h_key];
										}
										?>
									</tr>
									<?
									$i++;
									$total_cut += $row['total_prod'];

								}
								?>
							</tbody>
						</table>
					</div>
					<table style="border-collapse: collapse;"  border="1" class="rpt_table"  width="<?=$tbl_width;?>" rules="all" align="left" >
						<tfoot>
							<tr>
								<th width="20"></th>
								<th width="50"></th>
								<th width="100"></th>
								<th width="100"></th>
								<th width="100"></th>
								<th width="100"></th>
								<th width="100">Grand Total</th>
								<th width="80"><?=number_format($total_cut,0);?></th>
								<?
								foreach ($hour as $h_key => $h_value)
								{
									?>
									<th align="right" width="50"><?=$total_qty_arr[$h_key];?></th>
									<?
								}
								?>
							</tr>
						</tfoot>
					</table>
			</div>
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
	//$filename=$user_id."_".$name.".xls";

	echo "$total_data####$filename";
	exit();
	}
}

if($action=="cutting_popup")
{
	extract($_REQUEST);
 	echo load_html_head_contents("Remarks", "../../../", 1, 1,$unicode,'','');
 	list($table_no,$buyer_id,$client_id,$job_no,$item_id,$company_id,$working_company_id,$entry_date) = explode("**", $search_string);

	$buyer_library=return_library_array( "select id,buyer_name from  lib_buyer", "id", "buyer_name"  );
	$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
	$actual_resource_library=return_library_array( "select id,line_number from prod_resource_mst", "id", "line_number"  );

	$all_jobs = "'".implode("','", explode("*", $job_no))."'";
	$job_sql = sql_select("SELECT a.company_name,a.style_ref_no,a.buyer_name,b.po_number from wo_po_details_master a,wo_po_break_down b where a.job_no=b.job_no_mst and b.job_no_mst in($all_jobs)");
	?>
    <div id="data_panel" style="width:100%;text-align: center;padding: 5px;">
		<script>
            function new_window()
            {
                var w = window.open("Surprise", "#");
                var d = w.document.open();
                d.write(document.getElementById('details_reports').innerHTML);
                d.close();
            }
        </script>
    	<input type="button" value="Print" id="print" class="formbutton" style="width:100px;" onclick="new_window()" />
    	<span id="popup_report_container" align="center" style="width: 120px;"> </span>
    </div>
    <?
     ob_start();
	?>
    <div align="center" id="details_reports">
    <fieldset style="width:770px">
            <table cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all" width="770">
                <thead>
                    <tr>
                        <th width="30">SL</th>
                        <th width="60">Cut No</th>
                        <th width="60">Order Cut No</th>
                        <th width="80">Job No</th>
                        <th width="100">GMTS Item</th>
                        <th width="100">Style Ref.</th>
						<th width="100">PO</th>
                        <th width="100">Buyer</th>
                        <th width="100">Color</th>
                        <th width="60">Marker Qty</th>
                        <th width="60">Marker Length</th>
                        <th width="60">Marker Width</th>
                        <th width="60">Entry Date</th>
                    </tr>
                </thead>
                <tbody>
				<?
				$sql_cond = ""; $po_sql_cond = "";
				$sql_cond .= ($table_no!=0) ? " and a.table_no in($table_no)" : "";
				$sql_cond .= ($buyer_id!="") ? " and c.buyer_name in($buyer_id)" : "";
				$po_sql_cond .= ($buyer_id!="") ? " and a.buyer_name in($buyer_id)" : "";
				// $sql_cond .= ($client_id!=0) ? " and c.client_id in($client_id)" : "";
				$sql_cond .= ($job_no!="") ? " and c.job_no in($all_jobs)" : "";
				$po_sql_cond .= ($job_no!="") ? " and a.job_no in($all_jobs)" : "";
				$sql_cond .= ($item_id!="") ? " and b.gmt_item_id in($item_id)" : "";
				$sql_cond .= ($company_id!=0) ? " and c.company_name=$company_id" : "";
				$po_sql_cond .= ($company_id!=0) ? " and a.company_name=$company_id" : "";
				$sql_cond .= ($working_company_id!="") ? " and a.working_company_id in($working_company_id)" : "";
				$sql_cond .= ($entry_date!="") ? " and a.entry_date='$entry_date'" : "";

                $sql = "SELECT c.id,c.job_no,c.buyer_name, a.cut_num_prefix_no as cut_no,a.table_no,a.marker_length,a.marker_width, a.entry_date as production_date,b.color_id,b.order_cut_no,c.style_ref_no, e.grouping,sum(d.size_qty) as marker_qty, b.order_ids, b.gmt_item_id as item_id from  ppl_cut_lay_mst a,  ppl_cut_lay_dtls b, wo_po_details_master c,ppl_cut_lay_bundle d,wo_po_break_down e where a.id=b.mst_id and a.job_no=c.job_no and a.id=d.mst_id and b.id=d.dtls_id and c.id=e.job_id $sql_cond group by c.id,c.job_no,c.buyer_name, a.cut_num_prefix_no,a.table_no,a.marker_length,a.marker_width, a.entry_date,b.color_id,b.order_cut_no,c.style_ref_no, b.order_ids, b.gmt_item_id,e.grouping order by a.cut_num_prefix_no";
			    // echo $sql;

			 	$sql1 = "SELECT b.id,b.po_number  from wo_po_details_master a,wo_po_break_down b where a.id=b.job_id $po_sql_cond and b.status_active=1 and b.is_deleted=0 group by b.id,b.po_number ";

                // echo $sql;
                $result = sql_select($sql);
				$result1 = sql_select($sql1);
                $i=1;
                $tot_lay_qty = 0;
				$po_no_arr = array();
				foreach($result1 as $row){

					$po_no_arr[$row[csf("id")]] = $row[csf("po_number")] ;
				}


				$po_id_arr =array();
                foreach($result as $row)
                {
                    if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

					$po_id_arr  = array_unique(explode(",",$row[csf("order_ids")]));

                    $po_no = '';
                    foreach($po_id_arr as $po_id)
                    {
                    	if($po_no!='')
						{
							$po_no .=", ";
						}

						$po_no .= $po_no_arr[$po_id] ;
                    }

                    $item_id_arr = array_unique(array_filter(explode('**', $row[csf('item_id')])));
					$item_name = "";
					foreach ($item_id_arr as $val)
					{
						 //echo $val;
						$item_name .= ($item_name=="") ? $garments_item[$val] : ", ".$garments_item[$val];
					}

                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>">
                        <td align="center"><? echo $i; ?></td>
                        <td align="center"><?=$row[csf("cut_no")];?></td>
                        <td align="center"><?=$row[csf("order_cut_no")];?></td>
                        <td align="left"><?=$row['JOB_NO'];?></td>
                        <td align="left"><?=$item_name;?></td>
                        <td align="left"><?=$row[csf("style_ref_no")];?></td>
						<td align="left"><?= $po_no;?></td>
                        <td align="left"><?=$buyer_library[$row['BUYER_NAME']];?></td>
                        <td align="left"><?=$color_library[$row[csf("color_id")]];?></td>
                        <td align="right"><?=number_format($row[csf("marker_qty")],0);?></td>
                        <td align="center"><?=$row[csf("marker_length")];?></td>
                        <td align="center"><?=$row[csf("marker_width")];?></td>
                        <td align="center"><?=change_date_format($row[csf("production_date")]);?></td>
                    </tr>
                    <?
                    $i++;
                    $tot_lay_qty += $row[csf("marker_qty")];
                }
                ?>
                </tbody>
                <tfoot>
                	<th colspan="9">Total</th>
                	<th><? echo number_format($tot_lay_qty,0); ?></th>
                	<th></th>
                	<th></th>
                	<th></th>
                </tfoot>
            </table>
        </fieldset>
	<?
	$html=ob_get_contents();
	ob_flush();

	foreach (glob("$user_id*.xls") as $filename)
	{
	   @unlink($filename);
	}

	//html to xls convert
	$name=time();
	$name=$user_id."_".$name.".xls";
	$create_new_excel = fopen(''.$name, 'w');
	$is_created = fwrite($create_new_excel,$html);
	?>
    <input type="hidden" id="txt_excl_link" value="<? echo 'requires/'.$name; ?>" />
    <script>
		$(document).ready(function(e)
		{
			document.getElementById('popup_report_container').innerHTML='<a href="<? echo $name?>" style="text-decoration:none"><input type="button" value="Convert to Excel" name="excel" id="excel" style="padding:0 2px;" class="formbutton"/></a>&nbsp;&nbsp;';
		});
	</script>
	</div>
	<?
	exit();
}

if($action=="cutting_popup_one")
{
	extract($_REQUEST);
 	echo load_html_head_contents("Remarks", "../../../", 1, 1,$unicode,'','');
 	list($table_no,$buyer_id,$client_id,$job_no,$item_id,$company_id,$working_company_id,$entry_date) = explode("**", $search_string);

	$buyer_library=return_library_array( "select id,buyer_name from  lib_buyer", "id", "buyer_name"  );
	$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
	$actual_resource_library=return_library_array( "select id,line_number from prod_resource_mst", "id", "line_number"  );

	$all_jobs = "'".implode("','", explode("*", $job_no))."'";
	$job_sql = sql_select("SELECT a.company_name,a.style_ref_no,a.buyer_name,b.po_number from wo_po_details_master a,wo_po_break_down b where a.job_no=b.job_no_mst and b.job_no_mst in($all_jobs)");
	?>
    <div id="data_panel" style="width:100%;text-align: center;padding: 5px;">
		<script>
            function new_window()
            {
                var w = window.open("Surprise", "#");
                var d = w.document.open();
                d.write(document.getElementById('details_reports').innerHTML);
                d.close();
            }
        </script>
    	<input type="button" value="Print" id="print" class="formbutton" style="width:100px;" onclick="new_window()" />
    	<span id="popup_report_container" align="center" style="width: 120px;"> </span>
    </div>
    <?
     ob_start();
	?>
    <div align="center" id="details_reports">
    <fieldset style="width:870px">
            <table cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all" width="870">
                <thead>
                    <tr>
                        <th width="30">SL</th>
                        <th width="60">Cut No</th>
                        <th width="60">Order Cut No</th>
                        <th width="80">Job No</th>
                        <th width="100">IR/IB</th>
                        <th width="100">GMTS Item</th>
                        <th width="100">Style Ref.</th>
						<th width="100">PO</th>
                        <th width="100">Buyer</th>
                        <th width="100">Color</th>
                        <th width="60">Marker Qty</th>
                        <th width="60">Marker Length</th>
                        <th width="60">Marker Width</th>
                        <th width="60">Entry Date</th>
                    </tr>
                </thead>
                <tbody>
				<?
				$sql_cond = ""; $po_sql_cond = "";
				$sql_cond .= ($table_no!=0) ? " and a.table_no in($table_no)" : "";
				$sql_cond .= ($buyer_id!="") ? " and c.buyer_name in($buyer_id)" : "";
				$po_sql_cond .= ($buyer_id!="") ? " and a.buyer_name in($buyer_id)" : "";
				// $sql_cond .= ($client_id!=0) ? " and c.client_id in($client_id)" : "";
				$sql_cond .= ($job_no!="") ? " and c.job_no in($all_jobs)" : "";
				$po_sql_cond .= ($job_no!="") ? " and a.job_no in($all_jobs)" : "";
				$sql_cond .= ($item_id!="") ? " and b.gmt_item_id in($item_id)" : "";
				$sql_cond .= ($company_id!=0) ? " and c.company_name=$company_id" : "";
				$po_sql_cond .= ($company_id!=0) ? " and a.company_name=$company_id" : "";
				$sql_cond .= ($working_company_id!="") ? " and a.working_company_id in($working_company_id)" : "";
				$sql_cond .= ($entry_date!="") ? " and a.entry_date='$entry_date'" : "";

                $sql = "SELECT c.id,c.job_no,c.buyer_name, a.cut_num_prefix_no as cut_no,a.table_no,a.marker_length,a.marker_width, a.entry_date as production_date,b.color_id,b.order_cut_no,c.style_ref_no, e.grouping,b.marker_qty, b.order_ids, b.gmt_item_id as item_id from  ppl_cut_lay_mst a,  ppl_cut_lay_dtls b, wo_po_details_master c,ppl_cut_lay_bundle d,wo_po_break_down e where a.id=b.mst_id and a.job_no=c.job_no and a.id=d.mst_id and b.id=d.dtls_id and c.id=e.job_id and e.id=d.order_id $sql_cond group by c.id,c.job_no,c.buyer_name, a.cut_num_prefix_no,a.table_no,a.marker_length,a.marker_width, a.entry_date,b.color_id,b.order_cut_no,c.style_ref_no, b.marker_qty, b.order_ids, b.gmt_item_id,e.grouping order by a.cut_num_prefix_no";
			    //  echo $sql;

			 	$sql1 = "SELECT b.id,b.po_number  from wo_po_details_master a,wo_po_break_down b where a.id=b.job_id $po_sql_cond and b.status_active=1 and b.is_deleted=0 group by b.id,b.po_number ";

                // echo $sql;
                $result = sql_select($sql);
				$result1 = sql_select($sql1);
                $i=1;
                $tot_lay_qty = 0;
				$po_no_arr = array();
				foreach($result1 as $row){

					$po_no_arr[$row[csf("id")]] = $row[csf("po_number")] ;
				}


				$po_id_arr =array();
                foreach($result as $row)
                {
                    if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

					$po_id_arr  = array_unique(explode(",",$row[csf("order_ids")]));

                    $po_no = '';
                    foreach($po_id_arr as $po_id)
                    {
                    	if($po_no!='')
						{
							$po_no .=", ";
						}

						$po_no .= $po_no_arr[$po_id] ;
                    }

                    $item_id_arr = array_unique(array_filter(explode('**', $row[csf('item_id')])));
					$item_name = "";
					foreach ($item_id_arr as $val)
					{
						 //echo $val;
						$item_name .= ($item_name=="") ? $garments_item[$val] : ", ".$garments_item[$val];
					}

                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>">
                        <td align="center"><? echo $i; ?></td>
                        <td align="center"><?=$row[csf("cut_no")];?></td>
                        <td align="center"><?=$row[csf("order_cut_no")];?></td>
                        <td align="left"><?=$row['JOB_NO'];?></td>
                        <td align="left"><?=$row['GROUPING'];?></td>
                        <td align="left"><?=$item_name;?></td>
                        <td align="left"><?=$row[csf("style_ref_no")];?></td>
						<td align="left"><?= $po_no;?></td>
                        <td align="left"><?=$buyer_library[$row['BUYER_NAME']];?></td>
                        <td align="left"><?=$color_library[$row[csf("color_id")]];?></td>
                        <td align="right"><?=number_format($row[csf("marker_qty")],0);?></td>
                        <td align="center"><?=$row[csf("marker_length")];?></td>
                        <td align="center"><?=$row[csf("marker_width")];?></td>
                        <td align="center"><?=change_date_format($row[csf("production_date")]);?></td>
                    </tr>
                    <?
                    $i++;
                    $tot_lay_qty += $row[csf("marker_qty")];
                }
                ?>
                </tbody>
                <tfoot>
                	<th colspan="10">Total</th>
                	<th><? echo number_format($tot_lay_qty,0); ?></th>
                	<th></th>
                	<th></th>
                	<th></th>
                </tfoot>
            </table>
        </fieldset>
	<?
	$html=ob_get_contents();
	ob_flush();

	foreach (glob("$user_id*.xls") as $filename)
	{
	   @unlink($filename);
	}

	//html to xls convert
	$name=time();
	$name=$user_id."_".$name.".xls";
	$create_new_excel = fopen(''.$name, 'w');
	$is_created = fwrite($create_new_excel,$html);
	?>
    <input type="hidden" id="txt_excl_link" value="<? echo 'requires/'.$name; ?>" />
    <script>
		$(document).ready(function(e)
		{
			document.getElementById('popup_report_container').innerHTML='<a href="<? echo $name?>" style="text-decoration:none"><input type="button" value="Convert to Excel" name="excel" id="excel" style="padding:0 2px;" class="formbutton"/></a>&nbsp;&nbsp;';
		});
	</script>
	</div>
	<?
	exit();
}
?>