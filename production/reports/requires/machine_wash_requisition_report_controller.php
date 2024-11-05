<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id = $_SESSION['logic_erp']["user_id"];

//--------------------------------------------------------------------------------------------------------------------

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_id", 130, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' order by location_name","id,location_name", 1, "--Select Location--", $selected, "load_drop_down( 'requires/machine_wash_requisition_report_controller', document.getElementById('cbo_company_id').value+'_'+this.value, 'load_drop_down_floor', 'floor_td' );",0 );
	exit();
}

if ($action=="load_drop_down_floor")
{
	$ex_data=explode('_',$data);
	if($ex_data[1]!=0) $location_cond=" and b.location_id='$ex_data[1]'"; else $location_cond="";
	
	echo create_drop_down( "cbo_floor_id", 120, "select a.id, a.floor_name from lib_prod_floor a, lib_machine_name b where a.id=b.floor_id and b.category_id=2 and b.company_id='$ex_data[0]' and b.status_active=1 and b.is_deleted=0  $location_cond group by a.id, a.floor_name order by a.floor_name","id,floor_name", 1, "-- Select Floor --", 0, "","" );
    exit();	 
}

if($action=="machine_no_popup")
{
	echo load_html_head_contents("PO Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$im_data=explode('_',$data);
	// print_r ($im_data);

	$floor_cond='';
	if ($im_data[2]==0) {
		$floor_cond = "";
	}
	else{
		$floor_cond = "and a.floor_id=$im_data[2]";
	}
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
		
		function toggle( x, origColor ) 
		{
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		
		function js_set_value( str ) 
		{
			
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
				name += selected_name[i] + '*';
			}
			
			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );
			
			$('#hid_machine_id').val( id );
			$('#hid_machine_name').val( name );
		}
		
		function hidden_field_reset()
		{
			$('#hid_machine_id').val('');
			$('#hid_machine_name').val( '' );
			selected_id = new Array();
			selected_name = new Array();
		}
    </script>
	</head>
	<input type="hidden" name="hid_machine_id" id="hid_machine_id" />
	<input type="hidden" name="hid_machine_name" id="hid_machine_name" />
	<?	
		$sql = "select a.id, a.machine_no, a.brand, a.origin, a.machine_group, b.floor_name from lib_machine_name a, lib_prod_floor b where a.floor_id=b.id and a.category_id=2 and a.company_id=$im_data[0] $floor_cond and a.status_active=1 and a.is_deleted=0 and a.is_locked=0 order by a.machine_no, b.floor_name ";
		// echo  $sql;
		
		echo create_list_view("tbl_list_search", "Machine Name,Machine Group,Floor Name", "200,110,110","450","350",0, $sql , "js_set_value", "id,machine_no", "", 1, "0,0,0", $arr , "machine_no,machine_group,floor_name", "",'setFilterGrid(\'tbl_list_search\',-1);','0,0,0','',1) ;
	
   exit(); 
}

if($action=="report_generate") 
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 

	$cbo_company  = str_replace("'","",$cbo_company_id);	
	$cbo_location = str_replace("'","",$cbo_location_id);
	$floor_name   = str_replace("'","",$cbo_floor_id);
	$cbo_type   = str_replace("'","",$cbo_type);
	$machine_name = str_replace("'","",$txt_machine_id);
	$date_from    = str_replace("'","",$txt_date_from);
	$date_to      = str_replace("'","",$txt_date_to);

	if ($cbo_location == 0 || $cbo_location == '') $location_cond = ""; else $location_cond = " and a.location_id = $cbo_location ";
	if ($floor_name == 0 || $floor_name == '') $floor_cond = ""; else $floor_cond = " and c.floor_id = $cbo_floor_id";
	if ($req_no == '') $req_cond = ""; else $req_cond = " and a.requ_prefix_num = $req_no";
	if ($machine_name == "") $machine_cond = ""; else $machine_cond = " and a.machine_id in ($machine_name) ";

	if ($cbo_type == 1)
	{
		if($db_type==0)
		{
			if( $date_from!="" && $date_to!="" )
			{
				$date_cond .= " and a.requisition_date between '".change_date_format($date_from,'yyyy-mm-dd')."' and '".change_date_format($date_to,'yyyy-mm-dd')."'";
			}
		}
		else if($db_type==2)
		{
			if($date_from!="" && $date_to!="") 
			{
				$date_cond .= " and a.requisition_date between '".change_date_format($date_from,'','',1)."' and '".change_date_format($date_to,'','',1)."'";
			}
		}

		$company_arr  = return_library_array("select id,company_name from lib_company", "id", "company_name");
		$machine_arr  = return_library_array("select id, machine_no from lib_machine_name",'id','machine_no');
		$floor_arr    = return_library_array("select id,floor_name from lib_prod_floor", "id", "floor_name");
		$location_arr = return_library_array("select id,location_name from lib_location", "id", "location_name" );
		
		$issue_sql = "SELECT b.req_no, SUM (c.req_qny_edit*a.avg_rate_per_unit) AS chem_cost, SUM (c.req_qny_edit) as total_issue_qty
		FROM product_details_master a, inv_issue_master b, dyes_chem_issue_dtls c
		WHERE b.id=c.mst_id and a.id=c.product_id and b.status_active=1 and b.is_deleted=0 and a.item_category_id in (5,7,6) and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 GROUP BY b.req_no";
		//echo $iss_sql;

		$issue_result = sql_select($issue_sql);
		$total_issue_qty_arr = array();
		$chem_cost_arr = array();	
		foreach($issue_result as $row)
		{
			$total_issue_qty_arr[$row[csf('req_no')]]['total_issue_qty'] = $row[csf('total_issue_qty')];
			$chem_cost_arr[$row[csf('req_no')]]['chem_cost'] = $row[csf('chem_cost')];
		}

	  	// Main query	
		$data_sql="SELECT a.id, a.company_id, a.location_id, a.requisition_date, a.requ_no, a.requ_prefix_num, a.requisition_basis, a.machine_id, sum(b.req_qny_edit) as req_qny_edit, c.floor_id, c.brand
		from dyes_chem_issue_requ_mst a, dyes_chem_issue_requ_dtls b, lib_machine_name c
		where a.id=b.mst_id and a.machine_id=c.id and a.company_id=$cbo_company $location_cond $floor_cond $machine_cond $req_cond $date_cond and a.entry_form=259 and a.requisition_basis=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0	
		group by a.id, a.company_id, a.location_id, a.requisition_date, a.requ_prefix_num, a.requisition_basis, a.machine_id, a.requ_no, c.floor_id, c.brand";
		//echo $data_sql;
		$sql_result = sql_select($data_sql);
		
		$data_arr=array();
		$summary_chem_cost_arr = array();
		foreach ($sql_result as $row)
		{
			$data_arr[$row[csf('company_id')]][$row[csf('location_id')]][$row[csf('floor_id')]][$row[csf('machine_id')]][$row[csf('requ_prefix_num')]]['id'] = $row[csf('id')];
			$data_arr[$row[csf('company_id')]][$row[csf('location_id')]][$row[csf('floor_id')]][$row[csf('machine_id')]][$row[csf('requ_prefix_num')]]['company_id'] = $row[csf('company_id')];
			$data_arr[$row[csf('company_id')]][$row[csf('location_id')]][$row[csf('floor_id')]][$row[csf('machine_id')]][$row[csf('requ_prefix_num')]]['location_id'] = $row[csf('location_id')];
			$data_arr[$row[csf('company_id')]][$row[csf('location_id')]][$row[csf('floor_id')]][$row[csf('machine_id')]][$row[csf('requ_prefix_num')]]['requisition_date'] = $row[csf('requisition_date')];
			$data_arr[$row[csf('company_id')]][$row[csf('location_id')]][$row[csf('floor_id')]][$row[csf('machine_id')]][$row[csf('requ_prefix_num')]]['requ_no']=$row[csf('requ_no')];
			$data_arr[$row[csf('company_id')]][$row[csf('location_id')]][$row[csf('floor_id')]][$row[csf('machine_id')]][$row[csf('requ_prefix_num')]]['requ_prefix_num'] = $row[csf('requ_prefix_num')];
			$data_arr[$row[csf('company_id')]][$row[csf('location_id')]][$row[csf('floor_id')]][$row[csf('machine_id')]][$row[csf('requ_prefix_num')]]['requisition_basis'] = $row[csf('requisition_basis')];
			$data_arr[$row[csf('company_id')]][$row[csf('location_id')]][$row[csf('floor_id')]][$row[csf('machine_id')]][$row[csf('requ_prefix_num')]]['machine_id'] = $row[csf('machine_id')];
			$data_arr[$row[csf('company_id')]][$row[csf('location_id')]][$row[csf('floor_id')]][$row[csf('machine_id')]][$row[csf('requ_prefix_num')]]['req_qny_edit'] = $row[csf('req_qny_edit')];
			$data_arr[$row[csf('company_id')]][$row[csf('location_id')]][$row[csf('floor_id')]][$row[csf('machine_id')]][$row[csf('requ_prefix_num')]]['floor_id'] = $row[csf('floor_id')];
			$data_arr[$row[csf('company_id')]][$row[csf('location_id')]][$row[csf('floor_id')]][$row[csf('machine_id')]][$row[csf('requ_prefix_num')]]['brand'] = $row[csf('brand')];

			$summary_arr[$row[csf('floor_id')]][$row[csf('machine_id')]]['floor_id'] = $row[csf('floor_id')];
			$summary_arr[$row[csf('floor_id')]][$row[csf('machine_id')]]['machine_id'] = $row[csf('machine_id')];
			$summary_arr[$row[csf('floor_id')]][$row[csf('machine_id')]]['id'] .= $row[csf('id')].',';
			$summary_chem_cost_arr[$row[csf('floor_id')]][$row[csf('machine_id')]]['sum'] += $chem_cost_arr[$row[csf('id')]]['chem_cost'];
		}  
		//echo '<pre>';print_r($summary_arr);
		ob_start();
		?>
		<style>
			.wrd_brk{word-break: break-all;}
			.left{text-align: left;}
			.center{text-align: center;}
			.right{text-align: right;}
		</style>
		<div style="width: 1190px;">
	        <table width="1030" cellpadding="0" cellspacing="0" id="caption" align="center">
	            <tr>
	               <td align="center" width="100%" colspan="6" class="form_caption" ><strong style="font-size:16px"><? echo $company_arr[$cbo_company]; ?></strong></td>
	            </tr> 
	            <tr>  
	               <td align="center" width="100%" colspan="6" class="form_caption" ><strong style="font-size:13px"><? echo $report_title;  if (str_replace("'","",$txt_date_from) != "" && str_replace("'","",$txt_date_to) != ""){echo ";<br> From : ".change_date_format(str_replace("'","",$txt_date_from))." To : ".change_date_format(str_replace("'","",$txt_date_to))."" ;}?></strong></td>
	            </tr>
	        </table>
	 
			<div>
				<h3>Summary</h3>
				<table width="300" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
					<thead>
						<tr>
							<th width="100" class="wrd_brk">Floor Name</th>
			                <th width="100" class="wrd_brk">M/C Name</th>
			                <th width="100" class="wrd_brk">Total Chem/Dyes Cost(TK)</th>
			            </tr>
					</thead>
				</table>
			    <div style="width:320px; overflow-y:auto; max-height:300px;" id="scroll_body">
					<table width="300" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body2" align="left">
						<tbody>
							<?
							$j=1;
							foreach ($summary_arr as $floor_id => $floor)
							{
								foreach ($floor as $machine_id => $row)
								{
									if ($j%2==0) $bgcolors="#EFEFEF"; else $bgcolors="#FFFFFF";
									?>
									<tr bgcolor="<? echo $bgcolors; ?>">
					                    <td width="100" class="wrd_brk"><? echo $floor_arr[$floor_id]; ?></th>
					                    <td width="100" class="wrd_brk"><? echo $machine_arr[$machine_id]; ?></th>
					                    <td width="100" class="wrd_brk right" title="<? echo trim($row['id'],','); ?>"><? echo number_format($summary_chem_cost_arr[$floor_id][$machine_id]['sum'],2); ?></th>
									</tr>
									<?
									$j++;
								}
							}
							?>
						</tbody>
				    </table>
			    </div>
			</div>
			<br>

			<table width="1190" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="scroll_body" align="left">
				<thead>
					<tr>
		                <th width="150" class="wrd_brk">Company Name</th>
						<th width="120" class="wrd_brk">Location</th>
						<th width="120" class="wrd_brk">Floor Name</th>
		                <th width="100" class="wrd_brk">M/C Name</th>
		                <th width="100" class="wrd_brk">M/C Brand</th>
		                <th width="100" class="wrd_brk">Req. Date</th>
		                <th width="100" class="wrd_brk">Req. No</th>
		                <th width="100" class="wrd_brk">Req. Basis</th>
		                <th width="100" class="wrd_brk">Req. Qty</th>
		                <th width="100" class="wrd_brk">Issue. Qty</th>
		                <th width="100" class="wrd_brk">Total Chem/Dyes Cost(TK)</th>
		            </tr>
				</thead>
			</table>
		    <div style="width:1210px; overflow-y:auto; max-height:290px;font-size:12px; overflow-x:hidden;" id="scroll_body_short" align="left">
				<table width="1190" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body" align="left">
					<tbody>
						<?
						$i=1;
						foreach ($data_arr as $company_id => $company_val)
						{
							foreach ($company_val as $location_id => $location_val)
							{
								foreach ($location_val as $floor_id => $floor_val)
								{
									foreach ($floor_val as $machine_id => $machin_val)
									{
										foreach ($machin_val as $reqs_no => $row)
										{
											if ($i%2==0) $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
											$issue_qty=$total_issue_qty_arr[$row['id']]['total_issue_qty'];
											$chem_cost=$chem_cost_arr[$row['id']]['chem_cost'];
											if($chem_cost=='') { $chem_cost=0; }
											if($issue_qty=='') { $issue_qty=0; }
											?>
											<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
							                    <td width="150" class="wrd_brk"><? echo $company_arr[$company_id]; ?></td>
							                    <td width="120" class="wrd_brk"><? echo $location_arr[$location_id]; ?></td>
							                    <td width="120" class="wrd_brk"><? echo $floor_arr[$floor_id]; ?></td>
							                    <td width="100" class="wrd_brk"><? echo $machine_arr[$machine_id]; ?></td>
							                    <td width="100" class="wrd_brk"><? echo $row['brand']; ?></td>
							                    <td width="100" class="wrd_brk"><? echo change_date_format($row['requisition_date']); ?></td>
												<td width="100" class="wrd_brk center" title="<? echo $row['id']; ?>"><p><a href="#report_details" onClick="requ_popup('<? echo $row['company_id'];?>','<? echo $row['id'];?>','requ_details','Requisition Details');"><? echo $row['requ_prefix_num']; ?></a></p></td>
							                    <td width="100" class="wrd_brk"><? echo $receive_basis_arr[$row['requisition_basis']]; ?></td>
							                    <td width="100" class="wrd_brk right"><? echo $row['req_qny_edit']; ?></td>
							                    <td width="100" class="wrd_brk right"><? echo $issue_qty; ?></td>
							                    <td width="100" class="wrd_brk right"><? echo number_format($chem_cost,2); ?></td>
											</tr>
											<?
											$i++;
											$total_req_qty += $row['req_qny_edit'];
											$total_issue_qty += $issue_qty;
											$total_chem_cost += $chem_cost;
										}
									}
								}
							}
						}
						?>
					</tbody>
	        	</table>
	        	<table width="1190" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="report_table_footer" align="left">
				    <tfoot>
				        <tr class="tbl_bottom">
    			            <td width="890" class="wrd_brk right" colspan="8"><strong>Total:</strong></td>
				            <td width="100" class="wrd_brk right"><strong><? echo number_format($total_req_qty,2); ?></strong></td>
				            <td width="100" class="wrd_brk right"><strong><? echo number_format($total_issue_qty,2); ?></strong></td>
				            <td width="100" class="wrd_brk right"><strong><? echo number_format($total_chem_cost,2); ?></strong></td>
				        </tr>
				    </tfoot>
			    </table>
		    </div>
		</div>
	    <?
	}
	else
	{
		if($db_type==0)
		{
			if( $date_from!="" && $date_to!="" )
			{
				$date_cond .= " and a.issue_date between '".change_date_format($date_from,'yyyy-mm-dd')."' and '".change_date_format($date_to,'yyyy-mm-dd')."'";
			}
		}
		else if($db_type==2)
		{
			if($date_from!="" && $date_to!="")
			{
				$date_cond .= " and a.issue_date between '".change_date_format($date_from,'','',1)."' and '".change_date_format($date_to,'','',1)."'";
			}
		}

		$company_arr  = return_library_array("select id, company_name from lib_company", "id", "company_name");
		$machine_arr  = return_library_array("select id, machine_no from lib_machine_name",'id','machine_no');
		$floor_arr    = return_library_array("select id, floor_name from lib_prod_floor", "id", "floor_name");
		$location_arr = return_library_array("select id, location_name from lib_location", "id", "location_name" );
		
		$data_sql = "SELECT a.id, a.company_id, a.location_id, a.issue_date, a.issue_number, a.issue_number_prefix_num, a.issue_basis, a.machine_id, sum(b.required_qnty) as req_qty, sum(b.req_qny_edit) as req_qny_edit, sum(b.req_qny_edit*d.avg_rate_per_unit) as chem_cost, c.floor_id, c.brand
		from inv_issue_master a, dyes_chem_issue_dtls b, lib_machine_name c, product_details_master d
		where a.id=b.mst_id and a.machine_id=c.id and b.product_id=d.id and d.item_category_id in(5,6,7) and a.company_id=$cbo_company $location_cond $floor_cond $machine_cond $date_cond and a.entry_form=5 and a.issue_basis=7 and a.issue_purpose=13 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0	
		group by a.id, a.company_id, a.location_id, a.issue_date, a.issue_number, a.issue_number_prefix_num, a.issue_basis, a.machine_id, c.floor_id, c.brand";
		//echo $data_sql;
		$sql_result=sql_select($data_sql);
		
		$data_arr = array();
		$summary_arr = array();
		$summary_chem_cost_arr = array();
		foreach ($sql_result as $row)
		{
			$data_arr[$row[csf('company_id')]][$row[csf('location_id')]][$row[csf('floor_id')]][$row[csf('machine_id')]][$row[csf('issue_number_prefix_num')]]['id'] = $row[csf('id')];
			$data_arr[$row[csf('company_id')]][$row[csf('location_id')]][$row[csf('floor_id')]][$row[csf('machine_id')]][$row[csf('issue_number_prefix_num')]]['company_id'] = $row[csf('company_id')];
			$data_arr[$row[csf('company_id')]][$row[csf('location_id')]][$row[csf('floor_id')]][$row[csf('machine_id')]][$row[csf('issue_number_prefix_num')]]['location_id'] = $row[csf('location_id')];
			$data_arr[$row[csf('company_id')]][$row[csf('location_id')]][$row[csf('floor_id')]][$row[csf('machine_id')]][$row[csf('issue_number_prefix_num')]]['issue_date'] = $row[csf('issue_date')];
			$data_arr[$row[csf('company_id')]][$row[csf('location_id')]][$row[csf('floor_id')]][$row[csf('machine_id')]][$row[csf('issue_number_prefix_num')]]['issue_number']=$row[csf('issue_number')];
			$data_arr[$row[csf('company_id')]][$row[csf('location_id')]][$row[csf('floor_id')]][$row[csf('machine_id')]][$row[csf('issue_number_prefix_num')]]['issue_number_prefix_num'] = $row[csf('issue_number_prefix_num')];
			$data_arr[$row[csf('company_id')]][$row[csf('location_id')]][$row[csf('floor_id')]][$row[csf('machine_id')]][$row[csf('issue_number_prefix_num')]]['issue_basis'] = $row[csf('issue_basis')];
			$data_arr[$row[csf('company_id')]][$row[csf('location_id')]][$row[csf('floor_id')]][$row[csf('machine_id')]][$row[csf('issue_number_prefix_num')]]['machine_id'] = $row[csf('machine_id')];
			$data_arr[$row[csf('company_id')]][$row[csf('location_id')]][$row[csf('floor_id')]][$row[csf('machine_id')]][$row[csf('issue_number_prefix_num')]]['req_qty'] = $row[csf('req_qty')];
			$data_arr[$row[csf('company_id')]][$row[csf('location_id')]][$row[csf('floor_id')]][$row[csf('machine_id')]][$row[csf('issue_number_prefix_num')]]['req_qny_edit'] = $row[csf('req_qny_edit')];
			$data_arr[$row[csf('company_id')]][$row[csf('location_id')]][$row[csf('floor_id')]][$row[csf('machine_id')]][$row[csf('issue_number_prefix_num')]]['floor_id'] = $row[csf('floor_id')];
			$data_arr[$row[csf('company_id')]][$row[csf('location_id')]][$row[csf('floor_id')]][$row[csf('machine_id')]][$row[csf('issue_number_prefix_num')]]['brand'] = $row[csf('brand')];
			$data_arr[$row[csf('company_id')]][$row[csf('location_id')]][$row[csf('floor_id')]][$row[csf('machine_id')]][$row[csf('issue_number_prefix_num')]]['chem_cost'] = $row[csf('chem_cost')];

			$summary_arr[$row[csf('floor_id')]][$row[csf('machine_id')]]['floor_id'] = $row[csf('floor_id')];
			$summary_arr[$row[csf('floor_id')]][$row[csf('machine_id')]]['machine_id'] = $row[csf('machine_id')];
			$summary_arr[$row[csf('floor_id')]][$row[csf('machine_id')]]['id'] .= $row[csf('id')].',';
			$summary_chem_cost_arr[$row[csf('floor_id')]][$row[csf('machine_id')]]['sum'] += $row[csf('chem_cost')];
		}  
		//echo '<pre>';print_r($data_arr);
		ob_start();
		?>
		<style>
			.wrd_brk{word-break: break-all;}
			.left{text-align: left;}
			.center{text-align: center;}
			.right{text-align: right;}
		</style>
		<div style="width: 1190px;">
	        <table width="1030" cellpadding="0" cellspacing="0" id="caption" align="center">
	            <tr>
	               <td align="center" width="100%" colspan="6" class="form_caption" ><strong style="font-size:16px"><? echo $company_arr[$cbo_company]; ?></strong></td>
	            </tr>
	            <tr>
	               <td align="center" width="100%" colspan="6" class="form_caption" ><strong style="font-size:13px"><? echo $report_title;  echo ";<br> From : ".change_date_format(str_replace("'","",$txt_date_from))." To : ".change_date_format(str_replace("'","",$txt_date_to))."" ;?></strong></td>
	            </tr>
	        </table>
	 
			<div>
				<h3>Summary</h3>
				<table width="300" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
					<thead>
						<tr>
							<th width="100" class="wrd_brk">Floor Name</th>
			                <th width="100" class="wrd_brk">M/C Name</th>
			                <th width="100" class="wrd_brk">Total Chem/Dyes Cost(TK)</th>
			            </tr>
					</thead>
				</table>
			    <div style="width:320px; overflow-y:auto; max-height:300px;" id="scroll_body">
					<table width="300" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body2" align="left">
						<tbody>
							<?
							$j=1;
							foreach ($summary_arr as $floor_id => $floor)
							{
								foreach ($floor as $machine_id => $row)
								{
									if ($j%2==0) $bgcolors="#EFEFEF"; else $bgcolors="#FFFFFF";
									?>
									<tr bgcolor="<? echo $bgcolors; ?>">
					                    <td width="100" class="wrd_brk"><? echo $floor_arr[$floor_id]; ?></th>
					                    <td width="100" class="wrd_brk"><? echo $machine_arr[$machine_id]; ?></th>
					                    <td width="100" class="wrd_brk right" title="<? echo trim($row['id'],','); ?>"><? echo number_format($summary_chem_cost_arr[$floor_id][$machine_id]['sum'],2); ?></th>
									</tr>
									<?
									$j++;
								}
							}
							?>
						</tbody>
				    </table>
			    </div>
			</div>
			<br>

			<table width="1190" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="scroll_body" align="left">
				<thead>
					<tr>
		                <th width="150" class="wrd_brk">Company Name</th>
						<th width="120" class="wrd_brk">Location</th>
						<th width="120" class="wrd_brk">Floor Name</th>
		                <th width="100" class="wrd_brk">M/C Name</th>
		                <th width="100" class="wrd_brk">M/C Brand</th>
		                <th width="100" class="wrd_brk">Issue Date</th>
		                <th width="100" class="wrd_brk">Issue No</th>
		                <th width="100" class="wrd_brk">Issue Basis</th>
		                <th width="100" class="wrd_brk">Req. Qty</th>
		                <th width="100" class="wrd_brk">Issue. Qty</th>
		                <th width="100" class="wrd_brk">Total Chem/Dyes Cost(TK)</th>
		            </tr>
				</thead>
			</table>
		    <div style="width:1210px; overflow-y:auto; max-height:290px;font-size:12px; overflow-x:hidden;" id="scroll_body_short" align="left">
				<table width="1190" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body" align="left">
					<tbody>
						<?
						$i=1; 
						$total_issue_qty = 0; $total_req_qty = 0; $total_chem_cost = 0;
						foreach ($data_arr as $company_id => $company_val)
						{
							foreach ($company_val as $location_id => $location_val)
							{
								foreach ($location_val as $floor_id => $floor_val)
								{
									foreach ($floor_val as $machine_id => $machin_val)
									{
										foreach ($machin_val as $issue_no => $row)
										{
											if ($i%2==0) $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
											?>
											<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
							                    <td width="150" class="wrd_brk"><? echo $company_arr[$company_id]; ?></td>
							                    <td width="120" class="wrd_brk"><? echo $location_arr[$location_id]; ?></td>
							                    <td width="120" class="wrd_brk"><? echo $floor_arr[$floor_id]; ?></td>
							                    <td width="100" class="wrd_brk"><? echo $machine_arr[$machine_id]; ?></td>
							                    <td width="100" class="wrd_brk"><? echo $row['brand']; ?></td>
							                    <td width="100" class="wrd_brk"><? echo change_date_format($row['issue_date']); ?></td>
												<td width="100" class="wrd_brk center" title="<? echo $row['id']; ?>"><p><a href="#report_details" onClick="issue_popup('<? echo $row['company_id'];?>','<? echo $row['id'];?>','issue_details','Issue Details');"><? echo $row['issue_number_prefix_num']; ?></a></p></td>
							                    <td width="100" class="wrd_brk"><? echo $receive_basis_arr[$row['issue_basis']]; ?></td>
							                    <td width="100" class="wrd_brk right"><? echo $row['req_qty']; ?></td>
							                    <td width="100" class="wrd_brk right"><? echo $row['req_qny_edit']; ?></td>
							                    <td width="100" class="wrd_brk right"><? echo $row['chem_cost']; ?></td>
											</tr>
											<?
											$i++;
											$total_req_qty += $row['req_qty'];
											$total_issue_qty += $row['req_qny_edit'];
											$total_chem_cost += $row['chem_cost'];
										}
									}
								}
							}
						}
						?>
					</tbody>
	        	</table>
	        	<table width="1190" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="report_table_footer" align="left">
				    <tfoot>
				        <tr class="tbl_bottom">
				            <td width="890" class="wrd_brk right" colspan="8"><strong>Total:</strong></td>
				            <td width="100" class="wrd_brk right"><strong><? echo number_format($total_req_qty,2); ?></strong></td>
				            <td width="100" class="wrd_brk right"><strong><? echo number_format($total_issue_qty,2); ?></strong></td>
				            <td width="100" class="wrd_brk right"><strong><? echo number_format($total_chem_cost,2); ?></strong></td>
				        </tr>
				    </tfoot>
			    </table>
		    </div>
		</div>
		<?
	}
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
	echo "$total_data****$filename";
	exit();
}

if($action=="issue_details")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);

	$supplierArr = return_library_array("select id, supplier_name from lib_supplier where status_active=1 and is_deleted=0", "id", "supplier_name");
	$companyArr = return_library_array("select id, company_name from lib_company where status_active=1 and is_deleted=0", "id", "company_name");
	$itemgroupArr = return_library_array("select id, item_name from  lib_item_group where status_active=1 and is_deleted=0", "id", "item_name");
	?>
	<script>
		function window_close()
		{
			parent.emailwindow.hide();
		}
	</script>
	<style>
		.wrd_brk{word-break: break-all;}
		.left{text-align: left;}
		.center{text-align: center;}
		.right{text-align: right;}
	</style>
	<div style="width:880px" align="center" id="scroll_body">
	<fieldset style="width:100%; margin-left:10px">
	    <input type="button" value="Print" onClick="print_report('<? echo $company_id.'*'.$mst_id;?>','chemical_dyes_issue_print','../../../inventory/chemical_dyes/requires/chemical_dyes_issue_controller')" style="width:100px" class="formbutton"/>
	    <input type="button" value="Close" onClick="window_close()" style="width:100px" class="formbutton"/>
	    <div id="report_container" align="center" style="width:100%">
	    <table class="rpt_table" border="1" rules="all" width="870" cellpadding="0" cellspacing="0">
	        <thead>
	            <tr>
	                <th width="30" class="wrd_brk">SL</th>
	                <th width="120" class="wrd_brk">Item Category</th>
	                <th width="120" class="wrd_brk">Group</th>
	                <th width="120" class="wrd_brk">Item Description</th>
	                <th width="80" class="wrd_brk">UOM</th>
	                <th width="80" class="wrd_brk">Dose Base</th>
	                <th width="80" class="wrd_brk">Req. Qty.</th>
	                <th width="80" class="wrd_brk">Issue Qty.</th>
	                <th width="80" class="wrd_brk">Avg. Rate</th>
	                <th width="80" class="wrd_brk">Issue Vaule</th>
	            </tr>
	        </thead>
	    	<tbody>
				<?
		        if ($mst_id==0) $mst_id_cond =""; else $mst_id_cond =" and c.mst_id in ($mst_id)";
		        $group_arr = return_library_array( "select id, item_name from lib_item_group where item_category in (5,6,7) and status_active=1 and is_deleted=0",'id','item_name');

				$issue_dtls_sql = "SELECT c.id as dtls_id, a.item_category_id, a.item_group_id, a.sub_group_name, a.item_description, a.unit_of_measure, a.avg_rate_per_unit, c.recipe_qnty, c.req_qny_edit as issue_qty, c.required_qnty as req_qty, c.dose_base
				FROM product_details_master a, inv_issue_master b, dyes_chem_issue_dtls c
				WHERE b.id=c.mst_id AND a.id=c.product_id $mst_id_cond AND a.item_category_id IN (5,7,6) AND a.status_active=1 AND a.is_deleted =0 AND b.status_active=1 AND b.is_deleted=0 AND c.status_active=1 AND c.is_deleted=0 
				ORDER BY c.id";
		        //echo $issue_dtls_sql;
		        $result = sql_select($issue_dtls_sql);
		        $i = 1;
		        $total_req_qty = 0; $total_issue_qty = 0; $total_avg_rate = 0; $total_issue_val = 0;
		        foreach($result as $row)
		        {
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					$issue_val = $row[csf('issue_qty')]*$row[csf("avg_rate_per_unit")];
					?>
					<tr bgcolor="<? echo $bgcolor; ?>">
						<td width="30" class="wrd_brk"><? echo $i; ?></td>
						<td width="120" class="wrd_brk"><? echo $item_category[$row[csf("item_category_id")]]; ?></td>
						<td width="120" class="wrd_brk"><? echo $group_arr[$row[csf("item_group_id")]]; ?></td>
		                <td width="120" class="wrd_brk center"><? echo $row[csf("item_description")]; ?></td>
						<td width="80" class="wrd_brk right"><? echo $unit_of_measurement[$row[csf("unit_of_measure")]]; ?></td>
						<td width="80" class="wrd_brk right"><? echo $dose_base[$row[csf("dose_base")]]; ?></td>
						<td width="80" class="wrd_brk right"><? echo $row[csf('req_qty')]; ?></td>
						<td width="80" class="wrd_brk right"><? echo $row[csf('issue_qty')]; ?></td>
						<td width="80" class="wrd_brk right"><? echo number_format($row[csf("avg_rate_per_unit")],2,'.',''); ?></td>
						<td width="80" class="wrd_brk right"><? echo number_format($issue_val,2,'.',''); ?></td>
					</tr>
					<?
					$i++;
					$total_req_qty += $row[csf("req_qty")];
					$total_issue_qty += $row[csf("issue_qty")];
					$total_avg_rate += $row[csf("avg_rate_per_unit")];
					$total_issue_val += $issue_val;
		        }
		        ?>
	    	</tbody>
		    <tfoot>
		        <th width="550" class="wrd_brk right" colspan="6">Total : </th>
		        <th width="80" class="wrd_brk right"><? echo number_format($total_req_qty,2); ?></th>
		        <th width="80" class="wrd_brk right"><? echo number_format($total_issue_qty,2); ?></th>
		        <th width="80" class="wrd_brk right"><? echo number_format($total_avg_rate,2); ?></th>
		        <th width="80" class="wrd_brk right"><? echo number_format($total_issue_val,2); ?></th>
		    </tfoot>
	    </table>
	    </div>
		</fieldset>
	</div>
	<?
	exit();
}

if($action=="requ_details")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);

	$supplierArr = return_library_array("select id, supplier_name from lib_supplier where status_active=1 and is_deleted=0", "id", "supplier_name");
	$companyArr = return_library_array("select id, company_name from lib_company where status_active=1 and is_deleted=0", "id", "company_name");
	$itemgroupArr = return_library_array("select id, item_name from  lib_item_group where status_active=1 and is_deleted=0", "id", "item_name");
	?>
	<script>
		function window_close()
		{
			parent.emailwindow.hide();
		}
	</script>
	<style>
		.wrd_brk{word-break: break-all;}
		.left{text-align: left;}
		.center{text-align: center;}
		.right{text-align: right;}
	</style>
	<div style="width:880px" align="center" id="scroll_body">
	<fieldset style="width:100%; margin-left:10px">
	    <input type="button" value="Print" onClick="print_report('<? echo $company_id.'*'.$mst_id;?>','chemical_dyes_issue_requisition_print','../../../inventory/chemical_dyes/requires/machine_wash_issue_requisition_controller')" style="width:100px" class="formbutton"/>
	    <input type="button" value="Close" onClick="window_close()" style="width:100px" class="formbutton"/>
	    <div id="report_container" align="center" style="width:100%">
	    <table class="rpt_table" border="1" rules="all" width="870" cellpadding="0" cellspacing="0">
	        <thead>
	            <tr>
	                <th width="30" class="wrd_brk">SL</th>
	                <th width="120" class="wrd_brk">Item Category</th>
	                <th width="120" class="wrd_brk">Group</th>
	                <th width="120" class="wrd_brk">Item Description</th>
	                <th width="80" class="wrd_brk">UOM</th>
	                <th width="80" class="wrd_brk">Dose Base</th>
	                <th width="80" class="wrd_brk">Req. Qty.</th>
	                <th width="80" class="wrd_brk">Issue Qty.</th>
	                <th width="80" class="wrd_brk">Avg. Rate</th>
	                <th width="80" class="wrd_brk">Issue Vaule</th>
	            </tr>
	        </thead>
	    	<tbody>
				<?
		        $i = 1;
		        if ($mst_id==0) $mst_id_cond =""; else $mst_id_cond =" and b.mst_id in ($mst_id)";
		        $group_arr=return_library_array( "select id,item_name from lib_item_group where item_category in (5,6,7) and status_active=1 and is_deleted=0",'id','item_name');
		       	$sql = "SELECT a.id, a.item_category_id, a.item_group_id, a.sub_group_name, a.item_description, a.unit_of_measure, a.avg_rate_per_unit, b.dose_base, b.ratio, b.recipe_qnty, b.req_qny_edit,b.remarks from product_details_master a, dyes_chem_issue_requ_dtls b where a.id=b.product_id $mst_id_cond and b.status_active=1 and b.is_deleted=0 and a.item_category_id in (5,7,6) and a.status_active=1 and a.is_deleted=0 order by b.seq_no";
		        //echo $sql;

				$iss_sql = "SELECT a.id, a.avg_rate_per_unit, c.recipe_qnty, c.req_qny_edit as issue_qty
				FROM product_details_master a, inv_issue_master b, dyes_chem_issue_dtls c
				WHERE b.id=c.mst_id and a.id=c.product_id AND b.status_active=1 AND b.is_deleted=0 AND a.item_category_id IN (5,7,6) AND a.status_active=1 AND a.is_deleted = 0
				ORDER BY b.id";
		        //echo $iss_sql;
		        $iss_result = sql_select($iss_sql);
		        $iss_arr = array();
		        foreach($iss_result as $row)
		        {
		        	$iss_arr[$row[csf('id')]]['issue_qty'] = $row[csf('issue_qty')];
		        }

		        $result = sql_select($sql);
		        foreach( $result as $row)
		        {
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					$iss_qty = $iss_arr[$row[csf('id')]]['issue_qty'];
					?>
					<tr bgcolor="<? echo $bgcolor; ?>">
						<td width="30" class="wrd_brk"><? echo $i; ?></td>
						<td width="120" class="wrd_brk"><? echo $item_category[$row[csf("item_category_id")]]; ?></td>
						<td width="120" class="wrd_brk"><? echo $group_arr[$row[csf("item_group_id")]]; ?></td>
		                <td width="120" class="wrd_brk center"><? echo $row[csf("item_description")]; ?></td>
						<td width="80" class="wrd_brk right"><? echo $unit_of_measurement[$row[csf("unit_of_measure")]]; ?></td>
						<td width="80" class="wrd_brk right"><? echo $dose_base[$row[csf("dose_base")]]; ?></td>
						<td width="80" class="wrd_brk right"><? echo $row[csf('req_qny_edit')]; ?></td>
						<td width="80" class="wrd_brk right"><? echo $iss_qty; ?></td>
						<td width="80" class="wrd_brk right"><? echo  number_format($row[csf("avg_rate_per_unit")],6,'.',''); ?></td>
						<td width="80" class="wrd_brk right"><? $req_value=$iss_qty*$row[csf("avg_rate_per_unit")]; echo number_format($req_value,6,'.',''); ?></td>
					</tr>
					<?
					$i++;
					$total_req_qty += $row[csf("req_qny_edit")];
					$total_iss_qty += $iss_qty;
					$total_avg_rate += $row[csf("avg_rate_per_unit")];
					$total_issue_vaule += $req_value;
		        }
		        ?>
	    	</tbody>
		    <tfoot>
		        <th width="550" class="wrd_brk right" colspan="6">Total : </th>
		        <th width="80" class="wrd_brk right"><? echo number_format($total_req_qty,2); ?></th>
		        <th width="80" class="wrd_brk right"><? echo number_format($total_iss_qty,2); ?></th>
		        <th width="80" class="wrd_brk right"><? echo number_format($total_avg_rate,2); ?></th>
		        <th width="80" class="wrd_brk right"><? echo number_format($total_issue_vaule,2); ?></th>
		    </tfoot>
	    </table>
	    </div>
		</fieldset>
	</div>
	<?
	exit();
}
?>
