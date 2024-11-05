<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');
$user_id = $_SESSION['logic_erp']["user_id"];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

//--------------------------------------------------------------------------------------------------------------------

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location", 90, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' order by location_name","id,location_name", 1, "-- Select --", $selected, "load_drop_down( 'requires/line_allocation_report_controller', $data+'_'+this.value, 'load_drop_down_floor', 'floor_td' ); load_drop_down( 'requires/line_allocation_report_controller', $data+'_'+this.value, 'load_drop_down_sewing_group', 'sgroup_td' );",0 );
	exit();     	
}

if ($action=="load_drop_down_floor")
{
	$data=explode("_",$data);
	echo create_drop_down( "cbo_floor", 90, "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and company_id=$data[0] and location_id='$data[1]' order by floor_name","id,floor_name", 1, "-- Select --", $selected, "",0 ); 
	exit();    	 
}

if ($action=="load_drop_down_sewing_group")
{
	$data=explode("_",$data);
	if(!$data[1]) $location_cond=''; else $location_cond=" and location_name='$data[1]'";
	echo create_drop_down( "cbo_sew_group", 80, "select sewing_group, sewing_group from lib_sewing_line where status_active =1 and is_deleted=0 and company_name='$data[0]' and sewing_group!=' ' $location_cond group by sewing_group","sewing_group,sewing_group", 1, "-- Select --", $selected, "",0 );
	exit();     	
}

if($action=="line_no_popup")
{
	echo load_html_head_contents("Line Info","../../../", 1, 1, $unicode,1,'');
	$ex_data=explode('_',$data);
	$company_id=$ex_data[0];
	$location_id=$ex_data[1];
	$floor_id=$ex_data[2];
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
			
			$('#hid_line_id').val( id );
			$('#hid_line_name').val( name );
		}
		
    </script>
    <input type="hidden" name="hid_line_id" id="hid_line_id" />
    <input type="hidden" name="hid_line_name" id="hid_line_name" />
    <fieldset style="width:450px; margin-left:5px">
        <table border="1" class="rpt_table" rules="all" width="450" cellpadding="0" cellspacing="0">
            <thead>
                <th width="50">SL</th>
                <th>Sewing Line</th>
            </thead>
        </table>
        <div style="width:450px; max-height:280px; overflow-y:auto;">
        <table border="1" class="rpt_table" rules="all" width="430" cellpadding="0" cellspacing="0" id="tbl_list_search">
            <tbody>
                <?
				$line_arr=return_library_array( "select id, line_name from lib_sewing_line",'id','line_name');
                $i=1; 
				if($location_id==0) $location_cond=""; else $location_cond="and location_id=$location_id";
				if($floor_id==0) $floor_cond=""; else $floor_cond="and floor_id=$floor_id";
				$sql_line="select id, line_number from  prod_resource_mst where company_id=$company_id and is_deleted=0 $location_cond $floor_cond";
                $sql_result=sql_select($sql_line);
                foreach($sql_result as $row)
                {
                    if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
					$ex_line=explode(',',$row[csf('line_number')]);
					$line_name="";
					foreach($ex_line as $line_id)
					{
						if($line_name=="") $line_name=$line_arr[$line_id]; else $line_name.=', '.$line_arr[$line_id];
					}
                ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i;?>" onClick="js_set_value('<? echo $i.'_'.$row[csf('id')].'_'.$line_name; ?>');" style="cursor:pointer;">
                        <td width="50"><? echo $i; ?></td>
                        <td><p><? echo $line_name; ?>&nbsp;</p></td>
                    </tr>
                <?
                $i++;
                }
                ?>
                </tbody>
            </table>
        </div>
         <table width="450" cellspacing="0" cellpadding="0" style="border:none" align="center">
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
	</fieldset>  
    <?
	exit();
}

if($action=="report_generate")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	
	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name");
	$buyerArr = return_library_array("select id,short_name from lib_buyer where status_active=1 and is_deleted=0","id","short_name"); 
	$lineArr = return_library_array("select id,line_name from lib_sewing_line order by id","id","line_name"); 
	$color_arr=return_library_array( "select id,color_name from lib_color", "id", "color_name");
	$fromDate = change_date_format( str_replace("'","",trim($txt_date_from)) );
	$toDate = change_date_format( str_replace("'","",trim($txt_date_to)) );

	$cbo_sew_group = str_replace("'","",trim($cbo_sew_group));
	$txt_line_id = str_replace("'","",trim($txt_line_id));
	$txt_job_no = str_replace("'","",trim($txt_job_no));
	$txt_po_id = str_replace("'","",trim($txt_po_id));
	$year=str_replace("'","",trim($cbo_year));
	
	if (str_replace("'","",$cbo_location)==0) $location_cond=""; else $location_cond=" and a.location_id=$cbo_location"; 
	if(str_replace("'","",$cbo_floor)==0) $floor_cond="";else $floor_cond=" and a.floor_id=$cbo_floor";
	if($cbo_sew_group!=0) $group_cond=" and g.sewing_group='$cbo_sew_group'"; else $group_cond="";
	if($txt_line_id=='') $line_cond="";else $line_cond=" and a.id in ($txt_line_id)";
	if($txt_job_no=="") $job_cond=""; else $job_cond=" and c.job_no_prefix_num in ($txt_job_no)";
	if($txt_po_id=="") $po_cond=""; else $po_cond=" and d.id in ($txt_po_id)";
	
	if(str_replace("'","",trim($txt_date_from))=="" || str_replace("'","",trim($txt_date_to))=="") $allocation_start_date_cond="";
	else $allocation_start_date_cond=" and b.from_date between $txt_date_from and $txt_date_to";
	//echo $cbo_sew_group.'=='.$group_cond;
	if($db_type==0) 
	{
		$year_id_cond="YEAR(c.insert_date)";
		$line_lib_cond="and SUBSTRING_INDEX( a.line_number, ' , ', 1)=g.id";
		if($year!=0) $year_cond=" and year(c.insert_date)=$year"; else $year_cond="";
	}
	else if($db_type==2) 
	{
		$year_id_cond="to_char(c.insert_date,'YYYY')";
		$line_lib_cond="and REGEXP_SUBSTR( a.line_number, '[^,]+', 1)=g.id";
		if($year!=0) $year_cond="and to_char(c.insert_date,'YYYY')=$year"; else $year_cond="";
	}

	$variable_settings=return_field_value("production_entry","variable_settings_production","company_name =$cbo_company_name and variable_list=60 and is_deleted=0 and status_active=1");
	if($variable_settings ==0) $variable_settings=1;
	if($variable_settings==1)// order level
	{
		$color_cond = "";
		$item_cond = "";
	}
	else // color and size level
	{
		$color_cond = " and e.color_number_id=f.color_id ";
		$item_cond = " and e.item_number_id=f.gmts_item_id ";
	}
	
	$sql="SELECT a.line_number, b.target_per_hour, b.helper, b.working_hour, b.operator, b.from_date, b.to_date, b.active_machine, c.job_no_prefix_num, $year_id_cond as year, c.buyer_name, c.style_ref_no, c.set_break_down, d.id, d.po_number, d.pub_shipment_date, e.item_number_id, e.color_number_id, sum(e.order_quantity) as po_qty, g.sewing_group,g.sewing_line_serial 
	from prod_resource_mst a, prod_resource_dtls_mast b,  wo_po_details_master c, wo_po_break_down d, wo_po_color_size_breakdown e, prod_resource_color_size f, lib_sewing_line g
	where a.id=b.mst_id and b.id=f.dtls_id and c.id=d.job_id and d.id=e.po_break_down_id and d.id=f.po_id  
	and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1 
	and a.company_id=$cbo_company_name $color_cond $item_cond
	$location_cond $floor_cond $group_cond $line_cond $job_cond $po_cond $year_cond $line_lib_cond $allocation_start_date_cond
	group by a.line_number, b.target_per_hour, b.helper, b.working_hour, b.operator, b.from_date, b.to_date, b.active_machine, c.job_no_prefix_num, c.insert_date, c.buyer_name, c.style_ref_no, c.set_break_down, d.id, d.po_number, d.pub_shipment_date, e.item_number_id, e.color_number_id, g.sewing_line_serial,g.sewing_group order by g.sewing_line_serial,g.sewing_group ASC";
	//echo $sql;
	$sql_result = sql_select($sql);
	ob_start();
	?>
	<table width="1338" cellspacing="0">
		<tr class="form_caption" style="border:none;">
			<td colspan="19" align="center" style="border:none;font-size:14px; font-weight:bold" ><? echo $report_title; ?></td>
		</tr>
		<tr style="border:none;">
			<td colspan="19" align="center" style="border:none; font-size:16px; font-weight:bold">
			Company Name:<? echo $company_library[str_replace("'","",$cbo_company_name)]; ?>                                
			</td>
		</tr>
		<tr style="border:none;">
			<td colspan="19" align="center" style="border:none;font-size:12px; font-weight:bold">
			<? echo "From $fromDate To $toDate" ;?>
			</td>
		</tr>
	</table>
    <br /> 
    <table width="1370" cellspacing="0" border="1" class="rpt_table" rules="all">
        <thead>
            <tr style="font-size:13px">
                <th width="30">SL.</th>    
                <th width="100">Line Name</th>
                <th width="100">Buyer</th>
                <th width="60">Job Year</th>
                <th width="60">Job No</th>
                <th width="100">Style</th>
                <th width="100">Order No</th>
                <th width="70">Ship Date</th>
                <th width="100">Gmts Item</th>
                <th width="90">Color</th>
                <th width="60">PO Qty</th>
                <th width="40">Tgt Per Hr</th>
                <th width="70">Sew. Start Date</th>
                <th width="70">Sew. End Date</th>
                <th width="50">Helper</th>
                <th width="50">Work Hour</th>
                <th width="50">Operator</th>
                <th width="50">No of M/C</th>
                <th width="50">Day Target</th>
                <th>SMV</th>
                
             </tr>
        </thead>
    </table>
    <div style="width:1390px; max-height:350px; overflow-y:scroll" id="scroll_body" > 
    <table width="1370" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
		<?		 
		$i=1; 
		$sewing_group_array=array();  
		$k=1;
		foreach($sql_result as $row)
		{
			if($i%2==0)$bgcolor="#E9F3FF";  else $bgcolor="#FFFFFF";
			
			$line_name="";
			$ex_line=explode(',',$row[csf('line_number')]);
			foreach($ex_line as $line_id)
			{
				if($line_name=='') $line_name=$lineArr[$line_id]; else $line_name.=', '.$lineArr[$line_id];
			}
			if (!in_array($row[csf("sewing_group")],$sewing_group_array) )
			{
				?>
					<tr bgcolor="#dddddd">
						<td colspan="20" align="left"><b>Sewing Group: <? echo $row[csf("sewing_group")]; ?></b></td>
					</tr>
				<?
				$sewing_group_array[]=$row[csf("sewing_group")];            
				$k++;
			}
			$day_target= $row[csf('working_hour')]*$row[csf('target_per_hour')];
			$ex_set_break_data='';
			$ex_set_break_data=explode('__',$row[csf("set_break_down")]);
			foreach($ex_set_break_data as $smv_data)
			{
				$smv=0; $ex_smv='';
				$ex_smv=explode('_',$smv_data);
				$smv=$ex_smv[2];
			}
			
			?>
			<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>" style="font-size:13px">
				<td width="30"><? echo $i; ?></td>
				<td width="100" bgcolor="#99CCCC"><div style="word-wrap:break-word; width:98px"><? echo $line_name; ?></div></td>
				<td width="100"><div style="word-wrap:break-word; width:98px">&nbsp;<? echo $buyerArr[$row[csf('buyer_name')]]; ?></div></td>
				<td width="60" align="center"><? echo $row[csf('year')]; ?></td>
				<td width="60" align="center" bgcolor="#CCFFFF"><? echo $row[csf('job_no_prefix_num')]; ?></td>
				<td width="100"><? echo $row[csf('style_ref_no')]; ?></td>
				<td width="100" bgcolor="#FFCCFF"><? echo $row[csf('po_number')]; ?></td>
				<td width="70"><? echo change_date_format($row[csf('pub_shipment_date')]); ?>
				<td width="100" bgcolor="#FFFFCC"><div style="word-wrap:break-word; width:100px"><? echo $garments_item[$row[csf('item_number_id')]]; ?></div></td>
				<td width="90" bgcolor="#CCCCCC"><div style="word-wrap:break-word; width:90px"><? echo $color_arr[$row[csf('color_number_id')]]; ?></div></td>
				<td width="60" align="right">&nbsp;<? echo number_format($row[csf('po_qty')],2); ?></td>
				<td width="40" align="center" bgcolor="#FFCCFF"><? echo $row[csf('target_per_hour')]; ?></td>
				<td width="70"><? echo change_date_format($row[csf('from_date')]); ?></td>
				<td width="70"><? echo change_date_format($row[csf('to_date')]); ?></td>
				<td width="50" align="center">&nbsp;<? echo $row[csf('helper')]; ?></td>
				<td width="50" align="center">&nbsp;<? echo $row[csf('working_hour')]; ?></td>
				<td width="50" align="center">&nbsp;<? echo $row[csf('operator')]; ?></td>
				<td width="50" align="right">&nbsp;<? echo $row[csf('active_machine')]; ?></td>
				<td width="50" align="right">&nbsp;<? echo $day_target; ?></td>
				<td align="right"><? echo $smv; ?>&nbsp;</td>
				
			</tr>
			<?
			$i++;
		}
		?>
		</table>
    </div>
	<?
	$html = ob_get_contents();
    ob_clean();
    //$new_link=create_delete_report_file( $html, 2, $delete, "../../../" );
    foreach (glob("$user_id*.xls") as $filename) {
    //if( @filemtime($filename) < (time()-$seconds_old) )
    @unlink($filename);
    }
    //---------end------------//
    $name=time();
    $filename=$user_id."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');	
    $is_created = fwrite($create_new_doc, $html);
    echo "$html**$filename**".date('d-m-Y');
    exit();
}