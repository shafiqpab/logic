<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');
$user_id = $_SESSION['logic_erp']["user_id"];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

//--------------------------------------------------------------------------------------------------------------------


if($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 120, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "","");
	exit();
}


if($action=="job_popup")
{
	echo load_html_head_contents("Search Info", "../../../", 1, 1,'','','');
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
			
			$('#hide_job_id').val( id );
			$('#hide_job_no').val( name );
		}
	
    </script>

	</head>

	<body>
	<div align="center">
		<form name="styleRef_form" id="styleRef_form">
			<fieldset style="width:710px;">
	            <table width="700" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
	            	<thead>
	                    <th>Buyer</th>
	                    <th>Year</th>
	                    <th>Search By</th>
	                    <th id="search_by_td_up" width="100">Job No</th>
	                    <th>
                            <input type="reset" name="button" class="formbutton" value="Reset"  style="width:80px;"> 
                            <input type="hidden" name="hide_job_no" id="hide_job_no" value="" />
                            <input type="hidden" name="hide_job_id" id="hide_job_id" value="" />
                        </th>
	                </thead>
	                <tbody>
	                	<tr>
	                        <td align="center">
	                        	 <? 
									echo create_drop_down( "cbo_buyer_name", 120, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$lc_company $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",0,"",0 );
								?>
	                        </td>                  
	                        <td align="center">	
	                    	<?						
								echo create_drop_down( "cbo_year", 110, $year,"",1, "--Select--", "",'',0 );
							?>
	                        </td>                 
	                        <td align="center">	
	                    	<?
	                       		$search_by_arr=array(1=>"Job No",2=>"Style Ref");
								$dd="change_search_event(this.value, '0*0*0', '0*0*0', '../../') ";							
								echo create_drop_down( "cbo_search_by", 100, $search_by_arr,"",0, "--Select--", "",$dd,0 );
							?>
	                        </td>     
	                        <td align="center" id="search_by_td">				
	                            <input type="text" style="width:100px" class="text_boxes" name="txt_search_common" id="txt_search_common" />	
	                        </td> 
	                        <td align="center">
	                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $company; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+document.getElementById('cbo_year').value, 'search_list_view', 'search_div', 'color_and_size_wise_rmg_production_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:80px;" />
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
if($action=="cutting_popup")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode,1,1);
	extract($_REQUEST);
	$data=explode('*',$data);
	// print_r($data);
	?>
    <?
    // $prod_con .= ($job_id=="") ? "" : " and a.job_no_prefix_num=".$job_id;
		$floorArr = return_library_array("select id,floor_name from lib_prod_floor","id","floor_name");
		$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name" );
		$size_library=return_library_array( "select id,size_name from lib_size", "id", "size_name" );
		$job_cond_id = "and b.job_no_mst='" . str_replace("'", "", $job_id) . "'";
		$prod_con .= ($size_number_id=="") ? "" : " and j.size_id=".$size_number_id;
		$prod_con .= ($color_number_id=="") ? "" : " and c.color_id=".$color_number_id;
		$prod_con .= ($item_number_id=="") ? "" : " and c.gmt_item_id=".$item_number_id;

		$cut_lay_popupsql="SELECT b.job_no_mst as job_id,c.color_id as color_number_id,j.size_id as size_number_id,j.size_qty as plan_cutting_total,i.entry_date,i.floor_id,a.style_ref_no,b.po_number from wo_po_details_master a,wo_po_break_down b, ppl_cut_lay_dtls c,ppl_cut_lay_mst i,ppl_cut_lay_bundle j WHERE a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and c.is_deleted=0 and i.status_active=1 and i.is_deleted=0 and a.id=b.job_id and b.job_no_mst=i.job_no and i.id=j.mst_id and i.id=c.mst_id and b.id=j.order_id and c.id=j.dtls_id  $job_cond_id $prod_con";

		$tot_cut_lay_sql=sql_select($cut_lay_popupsql);
		$cut_popup_arr=array();
		$size_number_arr=array();
		$sub_total_arr=array();
		$style_ref_arr=array();
		$po_number_array=array();
		$color_number_arr=array();


		foreach($tot_cut_lay_sql as $row)
		{
			$size_number_arr[$row[csf('size_number_id')]]=[$row[csf('size_number_id')]];
			$color=$color_library[$row[csf('color_number_id')]];
			$cut_popup_arr[$row[csf('floor_id')]][$row[csf('entry_date')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['qty']+=$row[csf('plan_cutting_total')];
			$sub_total_arr[$row[csf('size_number_id')]]+=$row[csf('plan_cutting_total')];
			$style=$row[csf('style_ref_no')];
			$po_number_array[$row[csf('po_number')]]=$row[csf('po_number')];

		}
		//  echo '<pre>';
		//  print_r($color_number_arr);
		//  echo '<pre>';
		//  print_r($sub_total_arr);
		// echo $color;
		$po_array=implode(",",$po_number_array);


		$rowspan_arr=array();

		foreach($cut_popup_arr as $floor_id=>$floor_val)
		{
			foreach($floor_val as $entry_date=>$entry_val)
			{
				foreach($entry_val as $color_number_id=>$color_val)
				{
					foreach($color_val as $size_number_id=>$row)
					{
						$rowspan_arr[$floor_id]++;
					}
				}
			}
		}
	      
		$tbl_width = 420+(count($size_number_arr)*60);

?>
            <h1>Cut and Lay Details</h1>
            <div style="text-align:left;">
				<strong>Style: <? echo $style; ?>,</strong>
                <strong>Po Number: <?  echo $po_array;?></strong>
				<strong>Color: <? echo $color;  ?></strong>

			</div>
	       <div style="width:<?=$tbl_width+20;?>px;" align="center">
	       <table width="<?=$tbl_width;?>" cellspacing="0" border="1" class="rpt_table" rules="all" >
			<thead>
				<tr>
					<th width="20px">SL</th>
					<th width="100px">Floor</th>
					<th width="100px">Date</th>
					<th width="100px">Color</th>
					<?
					foreach($size_number_arr as $size_id=>$size_data)
					{
						?>
						<th width="60"><?
						echo $size_library[$size_id];
						?></th>
						<?
					}
					?>
					<th>Prod Qty</th>
				</tr>
			</thead>
			<tbody>
				<?
				$i=1;
				foreach($cut_popup_arr as $floor_id=>$floor_val)
				{
					$l=0;
					foreach($floor_val as $entry_date=>$entry_val)
					{
						foreach($entry_val as $color_number_id=>$color_val)
						{
							foreach($color_val as $size_number_id=>$row)
							{

								if ($i%2==0)
								$bgcolor="#E9F3FF";
								else
								$bgcolor="#FFFFFF";


							?>
								<tr bgcolor="<? echo $bgcolor; ?>">
								<td><? echo $i; ?></td>
								<?
								if($l==0)
								{
								?>
									<td align="center" valign="middle" rowspan="<? echo $rowspan_arr[$floor_id];?>"><? echo $floorArr[$floor_id];?></td>
								<?
								}
								?>
								<td ><? echo $entry_date;?></td>
								<td><? echo $color_library[$color_number_id];?></td>
								<?
								$prod_qty=0;
								foreach($size_number_arr as $size_number_id=>$size_data)
								{
									?>
									<td align="right"><? echo $row['qty']; $prod_qty+=$row['qty'];?></td>
									<?
								}
								?>
								<td align="right"><? echo $prod_qty; $gr_prod_qty+=$prod_qty;?></td>
								</tr>

								<?
								$i++;
								$l++;
							}
						}
					}
				}
			?>
			</tbody>
			<tfoot>
				<tr>
					<th width="20"></th>
					<th width="100"></th>
					<th width="100"></th>
					<th width="100">Total</th>
					<?
						foreach($size_number_arr as $size_id=>$val)
						{
							if($val !="")
							{
								?>
								<th width="60" align="right"><? echo $sub_total_arr[$size_id];?></th>
								<?
							}
						}
					?>
					<th align="100"><? echo $gr_prod_qty; ?></th>
				</tr>
			</tfoot>
		</table>
		</div>


<?
}
if($action=="sewing_popup")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode,1,1);
	extract($_REQUEST);
	$data=explode('*',$data);
	// print_r($data);
	?>
	<div id="data_panel" align="center" style="width:100%">
	<script>
	   function new_window()
	   {
		   document.getElementById('scroll_body').style.overflow="auto";
		   document.getElementById('scroll_body').style.maxHeight="none";
		   $('#table_body tr:first').hide();
		   var w = window.open("Surprise", "#");
		   var d = w.document.open();
		   d.write(document.getElementById('details_reports').innerHTML);
		   d.close();
		   $('#table_body tr:first').show();
		   document.getElementById('scroll_body').style.overflowY="scroll";
		   document.getElementById('scroll_body').style.maxHeight="none";
	   }
	</script>
<!-- <input type="button" value="Print" id="print" class="formbutton" style="width:100px;" onclick="new_window()" /> -->
</div>
<br>
<?
		$sewing_library=return_library_array( "select id,line_name from  lib_sewing_line", "id", "line_name");
		$prod_reso_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');
		$floorArr = return_library_array("select id,floor_name from lib_prod_floor","id","floor_name");
		$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name" );
		$size_library=return_library_array( "select id,size_name from lib_size", "id", "size_name" );
		$prod_con .= ($size_number_id=="") ? "" : " and c.size_number_id=".$size_number_id;
		$prod_con .= ($color_number_id=="") ? "" : " and c.color_number_id=".$color_number_id;
		$prod_con .= ($item_number_id=="") ? "" : " and c.item_number_id=".$item_number_id;
		$job_cond_id = "and b.job_no_mst='" . str_replace("'", "", $job_id) . "'";

		$sewinginput_sql="SELECT b.job_no_mst as job_id,c.color_number_id,c.size_number_id,c.item_number_id,e.production_qnty as sewin_total,d.floor_id as sewing_floor,d.sewing_line,d.production_date,a.style_ref_no,b.po_number
		from wo_po_details_master a,wo_po_break_down b,wo_po_color_size_breakdown c,pro_garments_production_mst d,pro_garments_production_dtls e WHERE a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and a.id=b.job_id and a.id=c.job_id and b.id=c.po_break_down_id  and d.id=e.mst_id and c.id=e.color_size_break_down_id and d.production_type=4 and e.production_type=4 $prod_con $job_cond_id ";

		$tot_sewing_input_sql=sql_select($sewinginput_sql);
		$sewinput_popup_arr=array();
		$size_number_arr=array();
		$sub_total_arr=array();
		$style_ref_arr=array();
		$po_number_array=array();
		$color_number_arr=array();

		foreach($tot_sewing_input_sql as $row)
		{
			$size_number_arr[$row[csf('size_number_id')]]=[$row[csf('size_number_id')]];
			$color=$color_library[$row[csf('color_number_id')]];
			$sewinput_popup_arr[$row[csf('sewing_floor')]][$row[csf('sewing_line')]][$row[csf('production_date')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['qty']+=$row[csf('sewin_total')];
			$sub_total_arr[$row[csf('sewing_floor')]][$row[csf('size_number_id')]]+=$row[csf('sewin_total')];
			$style=$row[csf('style_ref_no')];
			$po_number_array[$row[csf('po_number')]]=$row[csf('po_number')];

		}
			// echo '<pre>';
			// print_r($sewinput_popup_arr);
			// echo '<pre>';
			// print_r($sub_total_arr);
		   // echo $color;
			$po_array=implode(",",$po_number_array);
			$tbl_width = 520+(count($size_number_arr)*60);

?>
           <h1>SewingInput Details</h1>
           <div style="text-align:left;">
				<strong>Style: <? echo $style; ?>,</strong>
                <strong>Po Number: <?  echo $po_array;?></strong>
				<strong>Color: <? echo $color;  ?></strong>
			</div>
			<div style="width:<?=$tbl_width+20;?>px;" align="center">
	        <table width="<?=$tbl_width;?>" cellspacing="0" border="1" class="rpt_table" rules="all">
			<thead>
				<tr>
					<th width="20">SL</th>
					<th width="100">Floor</th>
					<th width="100">Line</th>
					<th width="100">Date</th>
					<th width="100">Color</th>
					<?
					foreach($size_number_arr as $size_id=>$size_data)
					{
						?>
						<th width="60"><?
						echo $size_library[$size_id];
						?></th>
						<?
					}
					?>
					<th width="100">Prod Qty.</th>
				</tr>
			</thead>
			<tbody>
				<?

				$i=1;
				 foreach($sewinput_popup_arr as $sewing_floor=>$sewing_val)
				 {
					foreach($sewing_val as $sewing_line=>$line_val)
					{
						foreach($line_val as $prod_date=>$prod_val)
						{
							foreach($prod_val as $color_number_id=>$color_number_val)
							{
								foreach($color_number_val as $size_number_id=>$row)
								{
									if ($i%2==0)
									$bgcolor="#E9F3FF";
									else
									$bgcolor="#FFFFFF";
								  ?>
								  <tr bgcolor="<? echo $bgcolor; ?>">
								  <td><? echo $i; ?></td>
								  <td><? echo $floorArr[$sewing_floor];?></td>
								  <td><? echo $sewing_library[$prod_reso_arr[$sewing_line]];?></td>
								  <td><? echo $prod_date;?></td>
								  <td><? echo $color_library[$color_number_id];?></td>
								  <?
									$prod_qty=0;
									foreach($size_number_arr as $size_number_id=>$size_data)
									{
										?>
										<td align="right"><? echo $row['qty']; $prod_qty+=$row['qty'];?></td>
										<?
									}
								   ?>

								   <td align="right"><? echo $prod_qty; $gr_prod_qty+=$prod_qty;  ?></td>


								  </tr>
								  <?
								  $i++;
								}
							}
						}
					}
					?>
					 <tr style="text-align: right;font-weight:bold;background:#cddcdc">
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th>Sub Total:</th>
						<?
						    $sub_prod_qty=0;
							foreach($size_number_arr as $size_id=>$val)
							{
								if($val !="")
								{
									?>
									<th width="60" align="right"><? echo $sub_total_arr[$sewing_floor][$size_id]; $sub_prod_qty+=$sub_total_arr[$sewing_floor][$size_id];  ?></th>
									<?
								}
							}
						?>
						<th align="right"><? echo $sub_prod_qty; $gr_sub_prod_qty+=$sub_prod_qty;?></th>
					 </tr>
					<?
				 }
				?>
			</tbody>
			<tfoot>
				<tr>
				<th width="20"></th>
				<th width="100"></th>
				<th width="100"></th>
				<th width="100"></th>
				<th width="100">Total</th>
				<?
					foreach($size_number_arr as $size_id=>$val)
					{
						if($val !="")
						{
							?>
							<th width="60" align="right"><? echo $gr_sub_prod_qty; ?></th>
							<?
						}
					}
				?>
				<th align="100"><? echo $gr_prod_qty; ?></th>
				</tr>
		   </tfoot>
			</table>
			</div>
<?
}
if($action=="sewingout_popup")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode,1,1);
	extract($_REQUEST);
	$data=explode('*',$data);
	// print_r($data);
	?>
	<div id="data_panel" align="center" style="width:100%">
	<script>
	   function new_window()
	   {
		   document.getElementById('scroll_body').style.overflow="auto";
		   document.getElementById('scroll_body').style.maxHeight="none";
		   $('#table_body tr:first').hide();
		   var w = window.open("Surprise", "#");
		   var d = w.document.open();
		   d.write(document.getElementById('details_reports').innerHTML);
		   d.close();
		   $('#table_body tr:first').show();
		   document.getElementById('scroll_body').style.overflowY="scroll";
		   document.getElementById('scroll_body').style.maxHeight="none";
	   }
	</script>
<!-- <input type="button" value="Print" id="print" class="formbutton" style="width:100px;" onclick="new_window()" /> -->
</div>
<br>
<?
    $sewing_library=return_library_array( "select id,line_name from  lib_sewing_line", "id", "line_name");
	$prod_reso_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');
	$floorArr = return_library_array("select id,floor_name from lib_prod_floor","id","floor_name");
	$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name" );
	$size_library=return_library_array( "select id,size_name from lib_size", "id", "size_name" );
	$prod_con .= ($size_number_id=="") ? "" : " and c.size_number_id=".$size_number_id;
	$prod_con .= ($color_number_id=="") ? "" : " and c.color_number_id=".$color_number_id;
	$prod_con .= ($item_number_id=="") ? "" : " and c.item_number_id=".$item_number_id;
    $job_cond_id = "and b.job_no_mst='" . str_replace("'", "", $job_id) . "'";

    $sewingoutput_sql="SELECT b.job_no_mst as job_id,c.color_number_id,c.size_number_id,c.item_number_id,e.production_qnty as sewin_total,d.floor_id as sewing_floor,d.sewing_line,d.production_date,a.style_ref_no,b.po_number
	from wo_po_details_master a,wo_po_break_down b,wo_po_color_size_breakdown c,pro_garments_production_mst d,pro_garments_production_dtls e WHERE a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and a.id=b.job_id and a.id=c.job_id and b.id=c.po_break_down_id  and d.id=e.mst_id and c.id=e.color_size_break_down_id and d.production_type=5 and e.production_type=5 $prod_con $job_cond_id ";

	$tot_sewing_output_sql=sql_select($sewingoutput_sql);
	$sewoutput_popup_arr=array();
	$size_number_arr=array();
	$sub_total_arr=array();
	$style_ref_arr=array();
	$po_number_array=array();
	$color_number_arr=array();

	foreach($tot_sewing_output_sql as $row)
	{
		$size_number_arr[$row[csf('size_number_id')]]=[$row[csf('size_number_id')]];
		$color=$color_library[$row[csf('color_number_id')]];
		$sewoutput_popup_arr[$row[csf('sewing_floor')]][$row[csf('sewing_line')]][$row[csf('production_date')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['qty']+=$row[csf('sewin_total')];
		$sub_total_arr[$row[csf('sewing_floor')]][$row[csf('size_number_id')]]+=$row[csf('sewin_total')];
		$style=$row[csf('style_ref_no')];
		$po_number_array[$row[csf('po_number')]]=$row[csf('po_number')];

	}
    // echo '<pre>';
    // print_r($sewinput_popup_arr);
    // echo '<pre>';
    // print_r($sub_total_arr);
   // echo $color;
   $po_array=implode(",",$po_number_array);
   $tbl_width = 520+(count($size_number_arr)*60);

?>
           <h1>Sewingout Details</h1>
           <div style="text-align:left;">
				<strong>Style: <? echo $style; ?>,</strong>
                <strong>Po Number: <?  echo $po_array;?></strong>
				<strong>Color: <? echo $color;  ?></strong>
			</div>
			<div style="width:<?=$tbl_width+20;?>px;" align="center">
	        <table width="<?=$tbl_width;?>" cellspacing="0" border="1" class="rpt_table" rules="all">
			<thead>
				<tr>
					<th width="20">SL</th>
					<th width="100">Floor</th>
					<th width="100">Line</th>
					<th width="100">Date</th>
					<th width="100">Color</th>
					<?
					foreach($size_number_arr as $size_id=>$size_data)
					{
						?>
						<th width="60"><?
						echo $size_library[$size_id];
						?></th>
						<?
					}
					?>
					<th width="100">Prod Qty.</th>
				</tr>
			</thead>
			<tbody>
				<?

				$i=1;
				 foreach($sewoutput_popup_arr as $sewing_floor=>$sewing_val)
				 {
					foreach($sewing_val as $sewing_line=>$line_val)
					{
						foreach($line_val as $prod_date=>$prod_val)
						{
							foreach($prod_val as $color_number_id=>$color_number_val)
							{
								foreach($color_number_val as $size_number_id=>$row)
								{
									if ($i%2==0)
									$bgcolor="#E9F3FF";
									else
									$bgcolor="#FFFFFF";
								  ?>
								  <tr bgcolor="<? echo $bgcolor; ?>">
								  <td><? echo $i; ?></td>
								  <td><? echo $floorArr[$sewing_floor];?></td>
								  <td><? echo $sewing_library[$prod_reso_arr[$sewing_line]];?></td>
								  <td><? echo $prod_date;?></td>
								  <td><? echo $color_library[$color_number_id];?></td>
								  <?
									$prod_qty=0;
									foreach($size_number_arr as $size_number_id=>$size_data)
									{
										?>
										<td align="right"><? echo $row['qty']; $prod_qty+=$row['qty'];?></td>
										<?
									}
								   ?>

								   <td align="right"><? echo $prod_qty; $gr_prod_qty+=$prod_qty;  ?></td>


								  </tr>
								  <?
								  $i++;
								}
							}
						}
					}
					?>
					 <tr style="text-align: right;font-weight:bold;background:#cddcdc">
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th>Sub Total:</th>
						<?
						    $sub_prod_qty=0;
							foreach($size_number_arr as $size_id=>$val)
							{
								if($val !="")
								{
									?>
									<th width="60" align="right"><? echo $sub_total_arr[$sewing_floor][$size_id]; $sub_prod_qty+=$sub_total_arr[$sewing_floor][$size_id];  ?></th>
									<?
								}
							}
						?>
						<th align="right"><? echo $sub_prod_qty; $gr_sub_prod_qty+=$sub_prod_qty;?></th>
					 </tr>
					<?
				 }
				?>
			</tbody>
			<tfoot>
				<tr>
				<th width="20"></th>
				<th width="100"></th>
				<th width="100"></th>
				<th width="100"></th>
				<th width="100">Total</th>
				<?
					foreach($size_number_arr as $size_id=>$val)
					{
						if($val !="")
						{
							?>
							<th width="60" align="right"><? echo $gr_sub_prod_qty; ?></th>
							<?
						}
					}
				?>
				<th align="100"><? echo $gr_prod_qty; ?></th>
				</tr>
		   </tfoot>
			</table>
			</div>
<?
}
if($action=="iron_popup")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode,1,1);
	extract($_REQUEST);
	$data=explode('*',$data);
	// print_r($data);
	?>
	<div id="data_panel" align="center" style="width:100%">
	<script>
	   function new_window()
	   {
		   document.getElementById('scroll_body').style.overflow="auto";
		   document.getElementById('scroll_body').style.maxHeight="none";
		   $('#table_body tr:first').hide();
		   var w = window.open("Surprise", "#");
		   var d = w.document.open();
		   d.write(document.getElementById('details_reports').innerHTML);
		   d.close();
		   $('#table_body tr:first').show();
		   document.getElementById('scroll_body').style.overflowY="scroll";
		   document.getElementById('scroll_body').style.maxHeight="none";
	   }
	</script>
<!-- <input type="button" value="Print" id="print" class="formbutton" style="width:100px;" onclick="new_window()" /> -->
</div>
<br>
<?
    $sewing_library=return_library_array( "select id,line_name from  lib_sewing_line", "id", "line_name");
	$prod_reso_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');
	$floorArr = return_library_array("select id,floor_name from lib_prod_floor","id","floor_name");
	$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name" );
	$tableNameArr = return_library_array("select id, table_name from lib_table_entry where table_type=2  and is_deleted=0 and status_active=1","id","table_name");
	$size_library=return_library_array( "select id,size_name from lib_size", "id", "size_name" );
	$prod_con .= ($size_number_id=="") ? "" : " and c.size_number_id=".$size_number_id;
	$prod_con .= ($color_number_id=="") ? "" : " and c.color_number_id=".$color_number_id;
	$prod_con .= ($item_number_id=="") ? "" : " and c.item_number_id=".$item_number_id;
    $job_cond_id = "and b.job_no_mst='" . str_replace("'", "", $job_id) . "'";

    $iron_sql="SELECT b.job_no_mst as job_id,c.color_number_id,c.size_number_id,c.item_number_id,e.production_qnty as iron_total,d.floor_id as iron_floor,d.table_no,d.production_date,a.style_ref_no,b.po_number
	from wo_po_details_master a,wo_po_break_down b,wo_po_color_size_breakdown c,pro_garments_production_mst d,pro_garments_production_dtls e WHERE a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and a.id=b.job_id and a.id=c.job_id and b.id=c.po_break_down_id  and d.id=e.mst_id and c.id=e.color_size_break_down_id and d.production_type=7 and e.production_type=7 $prod_con $job_cond_id ";

	$tot_iron_sql=sql_select($iron_sql);
	$iron_popup_arr=array();
	$size_number_arr=array();
	$sub_total_arr=array();
	$style_ref_arr=array();
	$po_number_array=array();
	$color_number_arr=array();

	foreach($tot_iron_sql as $row)
	{
		$size_number_arr[$row[csf('size_number_id')]]=[$row[csf('size_number_id')]];
		$color=$color_library[$row[csf('color_number_id')]];
		$iron_popup_arr[$row[csf('iron_floor')]][$row[csf('table_no')]][$row[csf('production_date')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['qty']+=$row[csf('iron_total')];
		$sub_total_arr[$row[csf('iron_floor')]][$row[csf('size_number_id')]]+=$row[csf('iron_total')];
		$style=$row[csf('style_ref_no')];
		$po_number_array[$row[csf('po_number')]]=$row[csf('po_number')];

	}
    // echo '<pre>';
    // print_r($iron_popup_arr);
    // echo '<pre>';
    // print_r($sub_total_arr);
   // echo $color;
   $po_array=implode(",",$po_number_array);
   $tbl_width = 520+(count($size_number_arr)*60);

?>
           <h1>Iron Details</h1>
           <div style="text-align:left;">
				<strong>Style: <? echo $style; ?>,</strong>
                <strong>Po Number: <?  echo $po_array;?></strong>
				<strong>Color: <? echo $color;  ?></strong>
			</div>
			<div style="width:<?=$tbl_width+20;?>px;" align="center">
	        <table width="<?=$tbl_width;?>" cellspacing="0" border="1" class="rpt_table" rules="all">
			<thead>
				<tr>
					<th width="20">SL</th>
					<th width="100">Floor</th>
					<th width="100">Table</th>
					<th width="100">Date</th>
					<th width="100">Color</th>
					<?
					foreach($size_number_arr as $size_id=>$size_data)
					{
						?>
						<th width="60"><?
						echo $size_library[$size_id];
						?></th>
						<?
					}
					?>
					<th width="100">Prod Qty.</th>
				</tr>
			</thead>
			<tbody>
				<?

				$i=1;
				 foreach($iron_popup_arr as $iron_floor=>$floor_val)
				 {
					foreach($floor_val as $table_no=>$table_val)
					{
						foreach($table_val as $prod_date=>$prod_val)
						{
							foreach($prod_val as $color_number_id=>$color_number_val)
							{
								foreach($color_number_val as $size_number_id=>$row)
								{
									if ($i%2==0)
									$bgcolor="#E9F3FF";
									else
									$bgcolor="#FFFFFF";
								  ?>
								  <tr bgcolor="<? echo $bgcolor; ?>">
								  <td><? echo $i; ?></td>
								  <td><? echo $floorArr[$iron_floor];?></td>
								  <td><? echo $tableNameArr[$table_no];?></td>
								  <td><? echo $prod_date;?></td>
								  <td><? echo $color_library[$color_number_id];?></td>
								  <?
									$prod_qty=0;
									foreach($size_number_arr as $size_number_id=>$size_data)
									{
										?>
										<td align="right"><? echo $row['qty']; $prod_qty+=$row['qty'];?></td>
										<?
									}
								   ?>

								   <td align="right"><? echo $prod_qty; $gr_prod_qty+=$prod_qty;  ?></td>


								  </tr>
								  <?
								  $i++;
								}
							}
						}
					}
					?>
					 <tr style="text-align: right;font-weight:bold;background:#cddcdc">
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th>Sub Total:</th>
						<?
						    $sub_prod_qty=0;
							foreach($size_number_arr as $size_id=>$val)
							{
								if($val !="")
								{
									?>
									<th width="60" align="right"><? echo $sub_total_arr[$iron_floor][$size_id]; $sub_prod_qty+=$sub_total_arr[$iron_floor][$size_id];  ?></th>
									<?
								}
							}
						?>
						<th align="right"><? echo $sub_prod_qty; $gr_sub_prod_qty+=$sub_prod_qty;?></th>
					 </tr>
					<?
				 }
				?>
			</tbody>
			<tfoot>
				<tr>
				<th width="20"></th>
				<th width="100"></th>
				<th width="100"></th>
				<th width="100"></th>
				<th width="100">Total</th>
				<?
					foreach($size_number_arr as $size_id=>$val)
					{
						if($val !="")
						{
							?>
							<th width="60" align="right"><? echo $gr_sub_prod_qty; ?></th>
							<?
						}
					}
				?>
				<th align="100"><? echo $gr_prod_qty; ?></th>
				</tr>
		   </tfoot>
			</table>
			</div>
<?
}
if($action=="poly_popup")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode,1,1);
	extract($_REQUEST);
	$data=explode('*',$data);
	// print_r($data);
	?>
	<div id="data_panel" align="center" style="width:100%">
	<script>
	   function new_window()
	   {
		   document.getElementById('scroll_body').style.overflow="auto";
		   document.getElementById('scroll_body').style.maxHeight="none";
		   $('#table_body tr:first').hide();
		   var w = window.open("Surprise", "#");
		   var d = w.document.open();
		   d.write(document.getElementById('details_reports').innerHTML);
		   d.close();
		   $('#table_body tr:first').show();
		   document.getElementById('scroll_body').style.overflowY="scroll";
		   document.getElementById('scroll_body').style.maxHeight="none";
	   }
	</script>
<!-- <input type="button" value="Print" id="print" class="formbutton" style="width:100px;" onclick="new_window()" /> -->
</div>
<br>
<?
    $line_library=return_library_array( "select id,line_name from lib_sewing_line", "id", "line_name"  );
	$prod_reso_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');
	$floorArr = return_library_array("select id,floor_name from lib_prod_floor","id","floor_name");
	$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name" );
	$size_library=return_library_array( "select id,size_name from lib_size", "id", "size_name" );
	$prod_con .= ($size_number_id=="") ? "" : " and c.size_number_id=".$size_number_id;
	$prod_con .= ($color_number_id=="") ? "" : " and c.color_number_id=".$color_number_id;
	$prod_con .= ($item_number_id=="") ? "" : " and c.item_number_id=".$item_number_id;
    $job_cond_id = "and b.job_no_mst='" . str_replace("'", "", $job_id) . "'";

    $polypopup_sql="SELECT b.job_no_mst as job_id,c.color_number_id,c.size_number_id,c.item_number_id,e.production_qnty as poly_total,d.floor_id as poly_floor,d.sewing_line,d.production_date,a.style_ref_no,b.po_number
	from wo_po_details_master a,wo_po_break_down b,wo_po_color_size_breakdown c,pro_garments_production_mst d,pro_garments_production_dtls e WHERE a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and a.id=b.job_id and a.id=c.job_id and b.id=c.po_break_down_id  and d.id=e.mst_id and c.id=e.color_size_break_down_id and d.production_type=11 and e.production_type=11 $prod_con $job_cond_id ";

	$tot_polypopup_sql=sql_select($polypopup_sql);
	$poly_popup_arr=array();
	$size_number_arr=array();
	$sub_total_arr=array();
	$style_ref_arr=array();
	$po_number_array=array();
	$color_number_arr=array();

	foreach($tot_polypopup_sql as $row)
	{
		$size_number_arr[$row[csf('size_number_id')]]=[$row[csf('size_number_id')]];
		$color=$color_library[$row[csf('color_number_id')]];
		$poly_popup_arr[$row[csf('poly_floor')]][$row[csf('sewing_line')]][$row[csf('production_date')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['qty']+=$row[csf('poly_total')];
		$sub_total_arr[$row[csf('poly_floor')]][$row[csf('size_number_id')]]+=$row[csf('poly_total')];
		$style=$row[csf('style_ref_no')];
		$po_number_array[$row[csf('po_number')]]=$row[csf('po_number')];

	}
    // echo '<pre>';
    // print_r($poly_popup_arr);
    // echo '<pre>';
    // print_r($sub_total_arr);
   // echo $color;
   $po_array=implode(",",$po_number_array);
   $tbl_width = 520+(count($size_number_arr)*60);

?>
           <h1>Poly Details</h1>
           <div style="text-align:left;">
				<strong>Style: <? echo $style; ?>,</strong>
                <strong>Po Number: <?  echo $po_array;?></strong>
				<strong>Color: <? echo $color;  ?></strong>
			</div>
			<div style="width:<?=$tbl_width+20;?>px;" align="center">
	        <table width="<?=$tbl_width;?>" cellspacing="0" border="1" class="rpt_table" rules="all">
			<thead>
				<tr>
					<th width="20">SL</th>
					<th width="100">Floor</th>
					<th width="100">Line</th>
					<th width="100">Date</th>
					<th width="100">Color</th>
					<?
					foreach($size_number_arr as $size_id=>$size_data)
					{
						?>
						<th width="60"><?
						echo $size_library[$size_id];
						?></th>
						<?
					}
					?>
					<th width="100">Prod Qty.</th>
				</tr>
			</thead>
			<tbody>
				<?

				$i=1;
				 foreach($poly_popup_arr as $poly_floor=>$poly_val)
				 {
					foreach($poly_val as $sewing_line=>$line_val)
					{
						foreach($line_val as $prod_date=>$prod_val)
						{
							foreach($prod_val as $color_number_id=>$color_number_val)
							{
								foreach($color_number_val as $size_number_id=>$row)
								{
									if ($i%2==0)
									$bgcolor="#E9F3FF";
									else
									$bgcolor="#FFFFFF";
								  ?>
								  <tr bgcolor="<? echo $bgcolor; ?>">
								  <td><? echo $i; ?></td>
								  <td><? echo $floorArr[$poly_floor];?></td>
								  <td><? echo $sewing_library[$prod_reso_arr[$sewing_line]];?></td>
								  <td><? echo $prod_date;?></td>
								  <td><? echo $color_library[$color_number_id];?></td>
								  <?
									$prod_qty=0;
									foreach($size_number_arr as $size_number_id=>$size_data)
									{
										?>
										<td align="right"><? echo $row['qty']; $prod_qty+=$row['qty'];?></td>
										<?
									}
								   ?>

								   <td align="right"><? echo $prod_qty; $gr_prod_qty+=$prod_qty;  ?></td>


								  </tr>
								  <?
								  $i++;
								}
							}
						}
					}
					?>
					 <tr style="text-align: right;font-weight:bold;background:#cddcdc">
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th>Sub Total:</th>
						<?
						    $sub_prod_qty=0;
							foreach($size_number_arr as $size_id=>$val)
							{
								if($val !="")
								{
									?>
									<th width="60" align="right"><? echo $sub_total_arr[$poly_floor][$size_id]; $sub_prod_qty+=$sub_total_arr[$poly_floor][$size_id];  ?></th>
									<?
								}
							}
						?>
						<th align="right"><? echo $sub_prod_qty; $gr_sub_prod_qty+=$sub_prod_qty;?></th>
					 </tr>
					<?
				 }
				?>
			</tbody>
			<tfoot>
				<tr>
				<th width="20"></th>
				<th width="100"></th>
				<th width="100"></th>
				<th width="100"></th>
				<th width="100">Total</th>
				<?
					foreach($size_number_arr as $size_id=>$val)
					{
						if($val !="")
						{
							?>
							<th width="60" align="right"><? echo $gr_sub_prod_qty; ?></th>
							<?
						}
					}
				?>
				<th align="100"><? echo $gr_prod_qty; ?></th>
				</tr>
		   </tfoot>
			</table>
			</div>
<?
}
if($action=="finish_popup")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode,1,1);
	extract($_REQUEST);
	$data=explode('*',$data);
	// print_r($data);
	?>
	<div id="data_panel" align="center" style="width:100%">
	<script>
	   function new_window()
	   {
		   document.getElementById('scroll_body').style.overflow="auto";
		   document.getElementById('scroll_body').style.maxHeight="none";
		   $('#table_body tr:first').hide();
		   var w = window.open("Surprise", "#");
		   var d = w.document.open();
		   d.write(document.getElementById('details_reports').innerHTML);
		   d.close();
		   $('#table_body tr:first').show();
		   document.getElementById('scroll_body').style.overflowY="scroll";
		   document.getElementById('scroll_body').style.maxHeight="none";
	   }
	</script>
<!-- <input type="button" value="Print" id="print" class="formbutton" style="width:100px;" onclick="new_window()" /> -->
</div>
<br>
<?
    $sewing_library=return_library_array( "select id,line_name from  lib_sewing_line", "id", "line_name");
	$prod_reso_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');
	$floorArr = return_library_array("select id,floor_name from lib_prod_floor","id","floor_name");
	$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name" );
	$tableNameArr = return_library_array("select id, table_name from lib_table_entry where table_type=2  and is_deleted=0 and status_active=1","id","table_name");
	$size_library=return_library_array( "select id,size_name from lib_size", "id", "size_name" );
	$prod_con .= ($size_number_id=="") ? "" : " and c.size_number_id=".$size_number_id;
	$prod_con .= ($color_number_id=="") ? "" : " and c.color_number_id=".$color_number_id;
	$prod_con .= ($item_number_id=="") ? "" : " and c.item_number_id=".$item_number_id;
    $job_cond_id = "and b.job_no_mst='" . str_replace("'", "", $job_id) . "'";

    $finish_sql="SELECT b.job_no_mst as job_id,c.color_number_id,c.size_number_id,c.item_number_id,e.production_qnty as finish_total,d.floor_id as finish_floor,d.production_date,a.style_ref_no,b.po_number
	from wo_po_details_master a,wo_po_break_down b,wo_po_color_size_breakdown c,pro_garments_production_mst d,pro_garments_production_dtls e WHERE a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and a.id=b.job_id and a.id=c.job_id and b.id=c.po_break_down_id  and d.id=e.mst_id and c.id=e.color_size_break_down_id and d.production_type=8 and e.production_type=8 $prod_con $job_cond_id ";

	$tot_finish_sql=sql_select($finish_sql);
	$finish_popup_arr=array();
	$size_number_arr=array();
	$sub_total_arr=array();
	$style_ref_arr=array();
	$po_number_array=array();
	$color_number_arr=array();

	foreach($tot_finish_sql as $row)
	{
		$size_number_arr[$row[csf('size_number_id')]]=[$row[csf('size_number_id')]];
		$color=$color_library[$row[csf('color_number_id')]];
		$finish_popup_arr[$row[csf('finish_floor')]][$row[csf('production_date')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['qty']+=$row[csf('finish_total')];
		$sub_total_arr[$row[csf('finish_floor')]][$row[csf('size_number_id')]]+=$row[csf('finish_total')];
		$style=$row[csf('style_ref_no')];
		$po_number_array[$row[csf('po_number')]]=$row[csf('po_number')];

	}
    // echo '<pre>';
    // print_r($iron_popup_arr);
    // echo '<pre>';
    // print_r($sub_total_arr);
   // echo $color;
   $po_array=implode(",",$po_number_array);
   $tbl_width = 420+(count($size_number_arr)*60);

?>
           <h1>Packing & Finishing Details</h1>
           <div style="text-align:left;">
				<strong>Style: <? echo $style; ?>,</strong>
                <strong>Po Number: <?  echo $po_array;?></strong>
				<strong>Color: <? echo $color;  ?></strong>
			</div>
			<div style="width:<?=$tbl_width+20;?>px;" align="center">
	        <table width="<?=$tbl_width;?>" cellspacing="0" border="1" class="rpt_table" rules="all">
			<thead>
				<tr>
					<th width="20">SL</th>
					<th width="100">Floor</th>
					<th width="100">Date</th>
					<th width="100">Color</th>
					<?
					foreach($size_number_arr as $size_id=>$size_data)
					{
						?>
						<th width="60"><?
						echo $size_library[$size_id];
						?></th>
						<?
					}
					?>
					<th width="100">Prod Qty.</th>
				</tr>
			</thead>
			<tbody>
				<?

				$i=1;
				 foreach($finish_popup_arr as $finish_floor=>$floor_val)
				 {
						foreach($floor_val as $prod_date=>$prod_val)
						{
							foreach($prod_val as $color_number_id=>$color_number_val)
							{
								foreach($color_number_val as $size_number_id=>$row)
								{
									if ($i%2==0)
									$bgcolor="#E9F3FF";
									else
									$bgcolor="#FFFFFF";
								  ?>
								  <tr bgcolor="<? echo $bgcolor; ?>">
								  <td><? echo $i; ?></td>
								  <td><? echo $floorArr[$finish_floor];?></td>
								  <td><? echo $prod_date;?></td>
								  <td><? echo $color_library[$color_number_id];?></td>
								  <?
									$prod_qty=0;
									foreach($size_number_arr as $size_number_id=>$size_data)
									{
										?>
										<td align="right"><? echo $row['qty']; $prod_qty+=$row['qty'];?></td>
										<?
									}
								   ?>

								   <td align="right"><? echo $prod_qty; $gr_prod_qty+=$prod_qty;  ?></td>


								  </tr>
								  <?
								  $i++;
								}
							}
						}

					?>
					 <tr style="text-align: right;font-weight:bold;background:#cddcdc">
						<th></th>
						<th></th>
						<th></th>
						<th>Sub Total:</th>
						<?
						    $sub_prod_qty=0;
							foreach($size_number_arr as $size_id=>$val)
							{
								if($val !="")
								{
									?>
									<th width="60" align="right"><? echo $sub_total_arr[$finish_floor][$size_id]; $sub_prod_qty+=$sub_total_arr[$finish_floor][$size_id];  ?></th>
									<?
								}
							}
						?>
						<th align="right"><? echo $sub_prod_qty; $gr_sub_prod_qty+=$sub_prod_qty;?></th>
					 </tr>
					<?
				 }
				?>
			</tbody>
			<tfoot>
				<tr>
				<th width="20"></th>
				<th width="100"></th>
				<th width="100"></th>
				<th width="100">Total</th>
				<?
					foreach($size_number_arr as $size_id=>$val)
					{
						if($val !="")
						{
							?>
							<th width="60" align="right"><? echo $gr_sub_prod_qty; ?></th>
							<?
						}
					}
				?>
				<th align="100"><? echo $gr_prod_qty; ?></th>
				</tr>
		   </tfoot>
			</table>
			</div>
<?
}
if($action=="hold_popup")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode,1,1);
	extract($_REQUEST);
	$data=explode('*',$data);
	// print_r($data);
	?>
	<div id="data_panel" align="center" style="width:100%">
	<script>
	   function new_window()
	   {
		   document.getElementById('scroll_body').style.overflow="auto";
		   document.getElementById('scroll_body').style.maxHeight="none";
		   $('#table_body tr:first').hide();
		   var w = window.open("Surprise", "#");
		   var d = w.document.open();
		   d.write(document.getElementById('details_reports').innerHTML);
		   d.close();
		   $('#table_body tr:first').show();
		   document.getElementById('scroll_body').style.overflowY="scroll";
		   document.getElementById('scroll_body').style.maxHeight="none";
	   }
	</script>
<!-- <input type="button" value="Print" id="print" class="formbutton" style="width:100px;" onclick="new_window()" /> -->
</div>
<br>
<?
		$prod_con .= ($size_number_id=="") ? "" : " and c.size_number_id=".$size_number_id;
		$prod_con .= ($color_number_id=="") ? "" : " and c.color_number_id=".$color_number_id;
		$prod_con .= ($item_number_id=="") ? "" : " and c.item_number_id=".$item_number_id;
		$job_cond_id = "and b.job_no_mst='" . str_replace("'", "", $job_id) . "'";
		$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
		$floorArr = return_library_array("select id,floor_name from lib_prod_floor","id","floor_name");
		$sewing_library=return_library_array( "select id,line_name from  lib_sewing_line", "id", "line_name");
		$prod_reso_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');

		$holdpopup_sql="SELECT b.job_no_mst as job_id,c.color_number_id,c.size_number_id,c.item_number_id,e.  	production_qnty as hold_qnty,e.bndl_hold_reason,d.production_date,a.buyer_name,d.sewing_line,a.style_ref_no,
		d.floor_id
		from wo_po_details_master a,wo_po_break_down b,wo_po_color_size_breakdown c,pro_garments_production_mst d,pro_garments_production_dtls e WHERE a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and a.id=b.job_id and a.id=c.job_id and b.id=c.po_break_down_id  and d.id=e.mst_id and c.id=e.color_size_break_down_id and d.production_type=5 and e.production_type=5 and e.bndl_hold_reason !=0 $prod_con  $job_cond_id";

		$tot_hold_qty=sql_select($holdpopup_sql);
        $hold_arr=array();
		foreach($tot_hold_qty as $row)
		{
			$hold_arr[$row[csf('floor_id')]][$row[csf('production_date')]][$row[csf('buyer_name')]][$row[csf('sewing_line')]][$row[csf('style_ref_no')]][$row[csf('item_number_id')]]['qty']+=$row[csf('hold_qnty')];
			$hold_arr[$row[csf('floor_id')]][$row[csf('production_date')]][$row[csf('buyer_name')]][$row[csf('sewing_line')]][$row[csf('style_ref_no')]][$row[csf('item_number_id')]]['bndl_hold_reason']=$row[csf('bndl_hold_reason')];

		}
		// echo '<pre>';
		// print_r($hold_arr);
		// echo '</pre>';
?>
         <h1>Hold Quantity Details</h1>
          <div id="details_reports" align="center" style="width:100%;" >
			<table width="810" class="rpt_table" rules="all" border="1">
				<thead>
					<tr>
						<th width="100">Floor</th>
						<th width="100">Date</th>
						<th width="100">Line</th>
						<th width="100">Buyer</th>
						<th width="100">Style</th>
						<th width="100">Item</th>
						<th width="60">Hold Qty</th>
						<th width="150">Cause</th>
					</tr>
				</thead>
				<tbody>
					<?
						$i=1;
					  foreach($hold_arr as $floor_id=>$floor_val)
					  {
						  foreach($floor_val as $prod_date=>$prod_val)
						  {
							 foreach($prod_val as $buyer_name=>$buyer_val)
							 {
								foreach($buyer_val as $sewing_line=>$sewing_val)
								{
									foreach($sewing_val as $style_ref=>$style_val)
									{
										foreach($style_val as $item_number=>$row)
										{
											if ($i%2==0)
											$bgcolor="#E9F3FF";
											else
											$bgcolor="#FFFFFF";

                                         ?>
										   <tr bgcolor="<? echo $bgcolor; ?>">
								            <td><? echo $floorArr[$floor_id]; ?></td>
								            <td><? echo $prod_date; ?></td>
								            <td><? echo $sewing_library[$prod_reso_arr[$sewing_line]]; ?></td>
								            <td><? echo $buyer_arr[$buyer_name]; ?></td>
								            <td><? echo $style_ref; ?></td>
								            <td><? echo $garments_item[$item_number]; ?></td>
								            <td align="right"><? echo $row['qty']; $tot_qty+=$row['qty']; ?></td>
								            <td><? echo $bundle_hold_reason_array[$row['bndl_hold_reason']]; ?></td>
										   </tr>

										<?
										 $i++;
										}
									}
								}
							 }
						  }
					  }

					?>
				</tbody>
				<tfoot>
					<tr>
						<th colspan="6">Total</th>
						<th align="right"><? echo number_format($tot_qty,2);?></th>
						<th align="right"><??></th>
					</tr>
				</tfoot>

<?
}

if($action=="grand_hold_popup")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode,1,1);
	extract($_REQUEST);
	$data=explode('*',$data);
	// print_r($data);
	?>
	<div id="data_panel" align="center" style="width:100%">
	<script>
	   function new_window()
	   {
		   document.getElementById('scroll_body').style.overflow="auto";
		   document.getElementById('scroll_body').style.maxHeight="none";
		   $('#table_body tr:first').hide();
		   var w = window.open("Surprise", "#");
		   var d = w.document.open();
		   d.write(document.getElementById('details_reports').innerHTML);
		   d.close();
		   $('#table_body tr:first').show();
		   document.getElementById('scroll_body').style.overflowY="scroll";
		   document.getElementById('scroll_body').style.maxHeight="none";
	   }
	</script>
<!-- <input type="button" value="Print" id="print" class="formbutton" style="width:100px;" onclick="new_window()" /> -->
</div>
<br>

<?

		$job_cond_id = "and b.job_no_mst='" . str_replace("'", "", $job_id) . "'";
		$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
		$floorArr = return_library_array("select id,floor_name from lib_prod_floor","id","floor_name");

	    $grand_holdpopup_sql="SELECT e.production_qnty as hold_qnty,e.bndl_hold_reason,a.style_ref_no,
		d.floor_id
		from wo_po_details_master a,wo_po_break_down b,wo_po_color_size_breakdown c,pro_garments_production_mst d,pro_garments_production_dtls e WHERE a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and a.id=b.job_id and a.id=c.job_id and b.id=c.po_break_down_id  and d.id=e.mst_id and c.id=e.color_size_break_down_id and d.production_type=5 and e.production_type=5 and e.bndl_hold_reason !=0  $job_cond_id";

		$grand_hold_qty=sql_select($grand_holdpopup_sql);
		$grand_hold_arr=array();
		$bundle_hold_reason_arr=array();
		$floor_total_arr=array();
		foreach($grand_hold_qty as $row)
		{
			$grand_hold_qnty_arr[$row[csf('floor_id')]][$row[csf('bndl_hold_reason')]]['qty']+=$row[csf('hold_qnty')];
			$bundle_hold_reason_arr[$row[csf('bndl_hold_reason')]]=$row[csf('bndl_hold_reason')];
			$grand_hold_arr[$row[csf('floor_id')]]['style_ref_no']=$row[csf('style_ref_no')];
            $floor_total_arr[$row[csf('bndl_hold_reason')]]+=$row[csf('hold_qnty')];
		}
		// echo '<pre>';
		
		// print_r($grand_hold_arr);

		$tbl_width=300+(count($bundle_hold_reason_arr)*100);

?>
		<h1>Grand Hold Details</h1>
        <div style="width:<?=$tbl_width+20;?>px;" align="center">
		<table width="<?=$tbl_width;?>" class="rpt_table" rules="all" border="1">
		<thead>
			<th width="100">Floor</th>
			<th width="100">Style</th>
			<?
			foreach($bundle_hold_reason_arr as $bndl_hold_reason=>$bundle_data)
			{
				?>
				<th width="100"><?
				echo $bundle_hold_reason_array[$bundle_data];
				?></th>
			<?
			}
			?>
			<th width="100">Total</th>
		</thead>
		<tbody>
			<?
			  $i=1;
			  foreach($grand_hold_arr as $floor_id=>$floor_val)
			  {

					if ($i%2==0)
					$bgcolor="#E9F3FF";
					else
					$bgcolor="#FFFFFF";
			  ?>
               	<tr bgcolor="<? echo $bgcolor; ?>">
				<td><? echo $floorArr[$floor_id];?></td>
				<td><? echo $floor_val['style_ref_no'];?></td>
				<?
					foreach($bundle_hold_reason_arr as $bndl_hold_reason=>$bundle_data)
					{
						?>
						<td width="100" align="right"><?
						echo $grand_hold_qnty_arr[$floor_id][$bndl_hold_reason]['qty']; $gr_total+=$grand_hold_qnty_arr[$floor_id][$bndl_hold_reason]['qty'];
						?></td>
					<?
					}
			    ?>
               <td align="right"><? echo number_format($gr_total,2);?></td>
				</tr>
			  <?

			  }

			?>
		</tbody>
		<tfoot>
			<th colspan="2">Total</th>
			<?
			foreach($bundle_hold_reason_arr as $bndl_hold_reason=>$bundle_data)
			{
				?>
				<th width="100" align="right"><?
                 echo $floor_total_arr[$bundle_data]; $gr_floor_total+=$floor_total_arr[$bundle_data];
				?></th>
			<?
			}
		    ?>
			<th><? echo number_format($gr_floor_total,2);?></th>
		</tfoot>
<?
}

if($action=="reject_popup")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode,1,1);
	extract($_REQUEST);
	$data=explode('*',$data);
	// print_r($data);
	?>
	<div id="data_panel" align="center" style="width:100%">
	<script>
	   function new_window()
	   {
		   document.getElementById('scroll_body').style.overflow="auto";
		   document.getElementById('scroll_body').style.maxHeight="none";
		   $('#table_body tr:first').hide();
		   var w = window.open("Surprise", "#");
		   var d = w.document.open();
		   d.write(document.getElementById('details_reports').innerHTML);
		   d.close();
		   $('#table_body tr:first').show();
		   document.getElementById('scroll_body').style.overflowY="scroll";
		   document.getElementById('scroll_body').style.maxHeight="none";
	   }
	</script>
<!-- <input type="button" value="Print" id="print" class="formbutton" style="width:100px;" onclick="new_window()" /> -->
</div>
<br>
<?
    $size_library=return_library_array( "select id,size_name from lib_size", "id", "size_name" );
	$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name" );
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
    $prod_con .= ($size_number_id=="") ? "" : " and c.size_number_id=".$size_number_id;
	$prod_con .= ($color_number_id=="") ? "" : " and c.color_number_id=".$color_number_id;
	$job_cond_id = "and b.job_no_mst='" . str_replace("'", "", $job_id) . "'";

    $sql="SELECT b.job_no_mst  as job_id,c.color_number_id,c.size_number_id,c.item_number_id,a.style_ref_no,a.buyer_name,
	(CASE WHEN e.production_type=1 and d.production_type=1 THEN e.reject_qty ELSE 0 END) as cutting_reject,
	(CASE WHEN e.production_type=3 and d.production_type=3 and d.embel_name=2 THEN e.reject_qty ELSE 0 END) as em_reject,
	(CASE WHEN e.production_type=5 and d.production_type=5 THEN e.reject_qty ELSE 0 END) as sewing_reject,
	(CASE WHEN e.production_type=7 and d.production_type=7 THEN e.reject_qty ELSE 0 END) as iron_reject,
	(CASE WHEN e.production_type=3 and d.production_type=3 and d.embel_name=1 THEN e.reject_qty ELSE 0 END) as print_reject
	from wo_po_details_master a,wo_po_break_down b,wo_po_color_size_breakdown c,pro_garments_production_mst d,pro_garments_production_dtls e WHERE a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and a.id=b.job_id and a.id=c.job_id and b.id=c.po_break_down_id  and d.id=e.mst_id and c.id=e.color_size_break_down_id $prod_con  $job_cond_id";

	$total_reject_qty=sql_select($sql);
    $total_reject_arr=array();
	$size_number_arr=array();
	$size_total_arr=array();

	foreach($total_reject_qty as $row)
	{
		$size_number_arr[$row[csf('size_number_id')]]=[$row[csf('size_number_id')]];
		
		$total_reject_arr[$row[csf('buyer_name')]][$row[csf('style_ref_no')]][$row[csf('job_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['em_qty']+=$row[csf('em_reject')];
		$total_reject_arr[$row[csf('buyer_name')]][$row[csf('style_ref_no')]][$row[csf('job_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['sewing_qty']+=$row[csf('sewing_reject')];
		$total_reject_arr[$row[csf('buyer_name')]][$row[csf('style_ref_no')]][$row[csf('job_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['iron_qty']+=$row[csf('iron_reject')];
		$total_reject_arr[$row[csf('buyer_name')]][$row[csf('style_ref_no')]][$row[csf('job_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['print_qty']+=$row[csf('print_reject')];

	}
    // echo '<pre>';
	// print_r($total_reject_arr);
	// echo '</pre>';
	$cutting_reject_sql="SELECT a.buyer_name,a.style_ref_no,b.id as po_id,b.job_no_mst as job_id,c.color_number_id,c.size_number_id,c.item_number_id,l.qc_pass_qty as cutting_total,l.bundle_qty as bundle_total
	from wo_po_details_master a,wo_po_break_down b,wo_po_color_size_breakdown c,pro_gmts_cutting_qc_mst k,pro_gmts_cutting_qc_dtls l
	WHERE a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and k.status_active=1 and k.is_deleted=0 and l.status_active=1 and l.is_deleted=0 and a.id=b.job_id and a.id=c.job_id and b.id=l.order_id  and k.id=l.mst_id and c.id=l.color_size_id and b.id=c.po_break_down_id  $prod_con  $job_cond_id";

	$cutting_reject=sql_select($cutting_reject_sql);
	$cutting_reject_arr=array();

	foreach($cutting_reject as $row)
	{
		$cutting_reject_arr[$row[csf('buyer_name')]][$row[csf('style_ref_no')]][$row[csf('job_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['cut_qty']+=$row[csf('bundle_total')]-$row[csf('cutting_total')];
	}
	// echo '<pre>';
	// print_r($cutting_reject_arr);
	// echo '</pre>';



      $tbl_width = 720+(count($size_number_arr)*60);

	       


?>
     <h1>Rejection Details</h1>
     <div style="width:<?=$tbl_width+20;?>px;" align="center">
		<table width="<?=$tbl_width;?>" class="rpt_table" rules="all" border="1">
		    <thead>
				<tr>
					<th width="20" rowspan="2">SL</th>
					<th width="100" rowspan="2">Buyer</th>
					<th width="100" rowspan="2">Style Ref</th>
					<th width="100" rowspan="2">Job No</th>
					<th width="100" rowspan="2">Item</th>
					<th width="100" rowspan="2">Color</th>
					<th width="100" rowspan="2">Department</th>
					<?
					foreach($size_number_arr as $size_id=>$size_data)
					{
						?>
						<th width="60"><?
						echo $size_library[$size_id];
						?></th>
					<?
					}
                    ?>
					<th width="100" rowspan="2">Total</th>
				</tr>
			</thead>
			<tbody>
				<?

				  $i=1;
				  foreach($total_reject_arr as $buyer_name=>$buyer_id)
				  {
					 foreach($buyer_id as $style_ref_no=>$style_id)
					 {
						foreach($style_id as $job_id=>$job_val)
						{
							foreach($job_val as $item_id=> $item_val)
							{
								foreach($item_val as $color_id=>$color_val)
								{
									foreach($color_val as $size_id=>$row)
									{
										    if ($i%2==0)
											$bgcolor="#E9F3FF";
											else
											$bgcolor="#FFFFFF";
											$gr_cut_qty=0;
											$gr_sew_qty=0;
											$gr_print_qty=0;
											$gr_em_qty=0;
											$gr_iron_qty=0;

									  ?>
									   <tr bgcolor="<? echo $bgcolor; ?>">
                                       <td align="right"><? echo $i; ?></td>
                                       <td style="word-break:break-all;"><? echo $buyer_arr[$buyer_name]; ?></td>
                                       <td style="word-break:break-all;"><? echo $style_ref_no; ?></td>
                                       <td style="word-break:break-all;"><? echo $job_id; ?></td>
                                       <td style="word-break:break-all;"><? echo $garments_item[$item_id]; ?></td>
                                       <td style="word-break:break-all;"><? echo $color_library[$color_id]; ?></td>
                                       <td style="word-break:break-all;">
									   <div><?="Cutting";?></div>
									   <hr class="style-one">
									   <div><?="Sewing";?></div>
									   <hr class="style-one">
									   <div><?="Printing";?></div>
									   <hr class="style-one">
									   <div><?="Embrodiery";?></div>
									   <hr class="style-one">
									   <div><?="Iron";?></div>
									   <hr class="style-one">
									   </td>
									   <td>
									   <?
										foreach($size_number_arr as $size_id=>$size_data)
										{
											
										?>
										  	<div align="right"><?=$cutting_reject_arr[$buyer_name][$style_ref_no][$job_id][$item_id][$color_id][$size_id]['cut_qty']; $gr_cut_qty+=$cutting_reject_arr[$buyer_name][$style_ref_no][$job_id][$item_id][$color_id][$size_id]['cut_qty'];?></div>
								            <hr class="style-one">

										<?
										}
										?>
										<?
										foreach($size_number_arr as $size_id=>$size_data)
										{
											
										?>
										  	<div align="right"><?=$row['sewing_qty'];$gr_sew_qty+=$row['sewing_qty'];?></div>
								            <hr class="style-one">

										<?
										}
										?>
										<?
										foreach($size_number_arr as $size_id=>$size_data)
										{
											
										?>
										  	<div align="right"><?=$row['print_qty'];$gr_print_qty+=$row['print_qty'];?></div>
								            <hr class="style-one">

										<?
										}
										?>
										<?
										foreach($size_number_arr as $size_id=>$size_data)
										{
											
										?>
										  	<div align="right"><?=$row['em_qty'];$gr_em_qty+=$row['em_qty'];?></div>
								            <hr class="style-one">

										<?
										}
										?>
										<?
										foreach($size_number_arr as $size_id=>$size_data)
										{
											
										?>
										  	<div align="right"><?=$row['iron_qty']; $gr_iron_qty+=$row['iron_qty']; ?></div>
								            <hr class="style-one">

										<?
										}
										?>
									   </td>
									   <td>
									   <?
										foreach($size_number_arr as $size_id=>$size_data)
										{

										?>
										  	<div align="right"><?=$gr_cut_qty; $tot_gr_cut_qty+=$gr_cut_qty;?></div>
								            <hr class="style-one">

										<?
										}
										?>
									    <?
										foreach($size_number_arr as $size_id=>$size_data)
										{
										?>
										  	<div align="right"><?=$gr_sew_qty; $tot_gr_sew_qty+=$gr_sew_qty;?></div>
								            <hr class="style-one">

										<?
										}
										?>
										 <?
										foreach($size_number_arr as $size_id=>$size_data)
										{
										?>
										  	<div align="right"><?=$gr_print_qty;$tot_gr_print_qty+=$gr_print_qty;?></div>
								            <hr class="style-one">

										<?
										}
										?>
										 <?
										foreach($size_number_arr as $size_id=>$size_data)
										{
										?>
										  	<div align="right"><?=$gr_em_qty;$tot_gr_em_qty+=$gr_em_qty;?></div>
								            <hr class="style-one">

										<?
										}
										?>
									    <?
										foreach($size_number_arr as $size_id=>$size_data)
										{
										?>
										  	<div align="right"><?=$gr_iron_qty; $tot_gr_iron_qty+=$gr_iron_qty;?></div>
								            <hr class="style-one">

										<?
										}
										?>
									   </td>
									   </tr>

									  <?
                                       $i++;
									  

									}
								}
							}
						}
					 }
				  }

               ?>
			</tbody>
			<tfoot>
				<tr>
					<th colspan="7">Total</th>
					<?
					foreach($size_number_arr as $size_id=>$size_data)
					{
						?>
						<th width="60"><??></th>
					<?
					}
                    ?>
					<th><?  $grand_total=$tot_gr_cut_qty+$tot_gr_sew_qty+$tot_gr_print_qty+$tot_gr_em_qty+$tot_gr_iron_qty;
                            echo number_format($grand_total,0);?></th>
				</tr>
			</tfoot>
		</table>
	 </div>
<?
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
	$search_cond="";
	if($search_string!="")	{$search_cond=" and $search_field like '%$search_string%'";}
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


	$sql= "SELECT a.id, a.job_no, $year_field, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no from wo_po_details_master a where a.status_active=1 and a.is_deleted=0 and a.company_name=$company_id $search_cond $buyer_id_cond $job_no_cond $job_year_cond group by a.id,
         a.job_no, a.insert_date, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no order by a.id desc";
    // echo $sql;

	echo create_list_view("tbl_list_search", "Company,Buyer Name,Year,Job No,Style Ref. No", "100,100,50,100","550","220",0, $sql , "js_set_value", "id,job_no,style_ref_no","",1,"company_name,buyer_name,0,0,0,0",$arr,"company_name,buyer_name,year,job_no,style_ref_no","",'','0,0,0,0,0','',1) ;
   exit();
}

if($action=="report_generate")
{
    $process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name");
	$buyerArr = return_library_array("select id,short_name from lib_buyer where status_active=1 and is_deleted=0","id","short_name");
	$color_library=return_library_array( "select id,color_name from lib_color ", "id", "color_name");
	$size_library=return_library_array( "select id,size_name from lib_size", "id", "size_name" );

	$job_cond_id = "";
	$company_id = str_replace("'","",$cbo_company_name);
	$from_date = str_replace( "'", "", $txt_date_from );
	$to_date  = str_replace( "'", "", $txt_date_to );
	$sql_cond="";
	$com_cond1="";
	$com_cond2="";
	$ex_fact_date="";
	$sql_cond .= ($company_id!=0) ? " and a.company_name in($company_id)" : "";
	if (str_replace("'", "", $cbo_buyer_name) == 0)  $buyer_name = "";
	else $buyer_name = "and a.buyer_name=" . str_replace("'", "", $cbo_buyer_name) . "";
	$sql_cond .= ($from_date!="") ? " and d.production_date between '$from_date' and '$to_date'" : "";
	if (str_replace("'", "", $txt_job_no) == "") $job_cond_id = "";
	else $job_cond_id = "and a.job_no='" . str_replace("'", "", $txt_job_no) . "'";
	$com_cond1 .= ($company_id!=0) ? " and a.company_name in($company_id)" : "";
	$ex_fact_date .= ($from_date!="") ? " and f.ex_factory_date between '$from_date' and '$to_date'" : "";
	$cut_date .= ($from_date!="") ? " and i.entry_date between '$from_date' and '$to_date'" : "";
	$qc_date.= ($from_date!="") ? " and k.entry_date between '$from_date' and '$to_date'" : "";
	$date_cond2=($from_date!="") ? " and b.pr_date between '$from_date' and '$to_date'" : "";
	$com_cond2 .= ($company_id!=0) ? " and a.company_id in($company_id)" : "";
	$com_cond3 .= ($company_id!=0) ? " and k.company_id in($company_id)" : "";

    if($type==1)
    {
          

		/*===================================================================================== /
		/									chk	shift time 										/
		/===================================================================================== */
		// if($db_type==0)
		// {
		// 	$min_shif_start=return_field_value("min(TIME_FORMAT(d.prod_start_time, '%H:%i' ))  as line_start_time","prod_resource_mst a,prod_resource_dtls  b,prod_resource_dtls_time d ","a.id=d.mst_id and a.id=b.mst_id and  a.company_id in($company_id) and shift_id=1 and pr_date between $txt_date_from and $txt_date_to and a.is_deleted=0 and b.is_deleted=0 and d.is_deleted=0","line_start_time");
		// }
		// else
		// {
		// 	$min_shif_start=return_field_value("min(TO_CHAR(d.prod_start_time,'HH24:MI')) as line_start_time","prod_resource_mst a,prod_resource_dtls b,prod_resource_dtls_time d ","a.id=d.mst_id and a.id=b.mst_id and  a.company_id in($company_id) and shift_id=1 and pr_date between $txt_date_from and $txt_date_to and a.is_deleted=0 and b.is_deleted=0 and d.is_deleted=0","line_start_time");
			
		// }

		// if($min_shif_start=="")
		// {
		// 	echo "<p style='font-size:20px', align='center'>No Line Engage for the selected Date.Please Check Actual Production Resource Entry.<p/>";
		// 	disconnect($con);
		// 	die;
		// }


		// /*===================================================================================== /
		// 	/									get	shift time 										/
		// /===================================================================================== */

		// 	$start_time_arr=array();
		// 	if($db_type==0)
		// 	{
		// 		$start_time_data_arr=sql_select("select company_name, shift_id, TIME_FORMAT( prod_start_time, '%H:%i' ) as prod_start_time,TIME_FORMAT( lunch_start_time, '%H:%i' ) as lunch_start_time from variable_settings_production where company_name in($company_id) and shift_id=1  and variable_list=26 and status_active=1 and is_deleted=0");
		// 	}
		// 	else
		// 	{
		// 		$start_time_data_arr=sql_select("select company_name, shift_id, TO_CHAR(prod_start_time,'HH24:MI') as prod_start_time,TO_CHAR(lunch_start_time,'HH24:MI') as lunch_start_time from variable_settings_production where  company_name in($company_id) and  shift_id=1 and variable_list=26 and status_active=1 and is_deleted=0");

		// 	}
		// 	$lunch_start_time_arr = array();
		// 	foreach($start_time_data_arr as $row)
		// 	{
		// 		$start_time_arr[$row[csf('shift_id')]]['pst']=$row[csf('prod_start_time')];
		// 		$start_time_arr[$row[csf('shift_id')]]['lst']=$row[csf('lunch_start_time')];
		// 		$exp = explode(":",$row[csf('lunch_start_time')]);
		// 		$lunch_start_time_arr[$row[csf('company_name')]] = $exp[0]*1;
		// 	}
		// 	$prod_start_hour=$start_time_arr[1]['pst'];
		// 	$global_start_lanch=$start_time_arr[1]['lst'];
		// 	if($prod_start_hour=="") $prod_start_hour="08:00";
		// 	$start_time=explode(":",$prod_start_hour);
		// 	$hour=$start_time[0]*1;
		// 	$minutes=$start_time[1];
		// 	$last_hour=23;
		// 	$lineWiseProd_arr=array();
		// 	$prod_arr=array();
		// 	$start_hour_arr=array();
		// 	$start_hour=$prod_start_hour;
		// 	$start_hour_arr[$hour]=$start_hour;
		// 	for($j=$hour;$j<$last_hour;$j++)
		// 	{
		// 		$start_hour=add_time($start_hour,60);
		// 		$start_hour_arr[$j+1]=substr($start_hour,0,5);
		// 	}
		// 	//echo $pc_date_time;die;
		// 	$start_hour_arr[$j+1]='23:59';
		// 	if($prod_start_hour>$min_shif_start)  $prod_start_hour=$min_shif_start;
		// 	$actual_date=date("Y-m-d");
		// 	$actual_production_date=date("Y-m-d",strtotime(str_replace("'","",$txt_date_from)));
		// 	$actual_time=substr(date("Y-m-d H:i:s",strtotime($pc_date_time)),11,2);
		// 	$acturl_hour_minute=date("H:i",strtotime($pc_date_time));
		// 	$generated_hourarr=array();
		// 	$first_hour_time=explode(":",$min_shif_start);
		// 	$hour_line=$first_hour_time[0]*1;
		// 	$minutes_one=$start_time[1];
		// 	$line_start_hour_arr[$hour_line]=$min_shif_start;

		// 	for($l=$hour_line;$l<$last_hour;$l++)
		// 	{
		// 		$min_shif_start=add_time($min_shif_start,60);
		// 		$line_start_hour_arr[$l+1]=substr($min_shif_start,0,5);
		// 	}

		// 	$line_start_hour_arr[$j+1]='23:59';
			// print_r($start_hour_arr);die;





          $main_sql="SELECT a.buyer_name,a.style_ref_no,b.id as po_id,b.job_no_mst as job_id,c.color_number_id,c.item_number_id,c.size_number_id,d.sewing_line,d.production_date,(CASE WHEN d.production_type ='5' THEN e.production_qnty END) AS sewout_quantity,d.po_break_down_id,d.company_id, TO_CHAR(d.production_hour,'HH24') as hour,d.production_date

		   from wo_po_details_master a,wo_po_break_down b,wo_po_color_size_breakdown c,pro_garments_production_mst d,pro_garments_production_dtls e WHERE a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and a.id=b.job_id and a.id=c.job_id and b.id=c.po_break_down_id and b.id=d.po_break_down_id  and d.id=e.mst_id and c.id=e.color_size_break_down_id   $sql_cond $buyer_name $job_cond_id ";

		  $m_sql=sql_select($main_sql);
          $main_arr=array();
		  $line_wise_production_arr=array();
		  $lc_com_array = array();
		  $poIdArr=array();
		  $all_style_arr=array();
		  $style_wise_po_arr=array();
		  $job_id_arr=array();
		  $style_wise_total_hour=array();


		  foreach($m_sql as $row)
		  {
			$main_arr[$row[csf('buyer_name')]][$row[csf('job_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['style']=$row[csf('style_ref_no')];
			$job_id=$row[csf('job_id')];
	

			$line_wise_production_arr[$row[csf('job_id')]][$row[csf('po_id')]][$row[csf('item_number_id')]][$row[csf('production_date')]][$row[csf('sewing_line')]]['qty']+=$row[csf('sewout_quantity')];
			$lc_com_array[$row[csf('company_id')]] = $row[csf('company_id')];
		    $poIdArr[$row[csf('po_break_down_id')]] = $row[csf('po_break_down_id')];
			$all_style_arr[$row[csf('style_ref_no')]] = $row[csf('style_ref_no')];
			$style_wise_po_arr[$row[csf('style_ref_no')]][$row[csf('po_break_down_id')]] = $row[csf('po_break_down_id')];

			$style_wise_total_hour[$row[csf('style_ref_no')]][$row[csf('sewing_line')]][$row[csf('production_date')]][$row[csf('hour')]]=[$row[csf('hour')]];

		  }

		//   echo '<pre>';
		//   print_r($line_wise_production_arr);
		//   echo '</pre>';
		//   echo '<pre>';
		//   print_r($job_id);
		//   echo '</pre>';
		// echo $job_id;
		$prod_reso_allo=return_field_value("auto_update","variable_settings_production","company_name=$company_id and variable_list=23 and is_deleted=0 and status_active=1");

		if($prod_reso_allo==1)
		{
			$prod_resource_sql="SELECT a.id as line_id,b.pr_date, b.man_power, b.working_hour from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id $com_cond2 $date_cond2";

			$total_prod_resource_sql=sql_select($prod_resource_sql);
			$prod_resource_array=array();

			foreach($total_prod_resource_sql as $row)
			{
				$prod_resource_array[$row[csf('pr_date')]][$row[csf('line_id')]]['man_power']=$row[csf('man_power')];
				$prod_resource_array[$row[csf('pr_date')]][$row[csf('line_id')]]['working_hour']=$row[csf('working_hour')];
			}
			//   echo '<pre>';
			//   print_r($prod_resource_array);
			//   echo '</pre>';
		}

		// echo $sewout_prod_qty;

		/*===================================================================================== /
		/										smv sorce 										/
		/===================================================================================== */
		$lc_com_ids = implode(",",$lc_com_array);
		$poIds_cond = where_con_using_array($poIdArr,0,"b.id");
		$smv_source=return_field_value("smv_source","variable_settings_production","company_name in ($lc_com_ids) and variable_list=25 and   status_active=1 and is_deleted=0");
		// echo $smv_source;


		if($smv_source=="") $smv_source=0; else $smv_source=$smv_source;
		if($smv_source==3) // from gsd enrty
		{
			$style_cond = where_con_using_array($all_style_arr,1,"a.style_ref");
			$sql_item="SELECT a.id,a.APPLICABLE_PERIOD,a.BULLETIN_TYPE,A.TOTAL_SMV,A.STYLE_REF,a.GMTS_ITEM_ID  from PPL_GSD_ENTRY_MST a where a.APPLICABLE_PERIOD <= $txt_date and A.IS_DELETED=0 and A.STATUS_ACTIVE=1 $style_cond  group by a.id,a.APPLICABLE_PERIOD,a.BULLETIN_TYPE,A.TOTAL_SMV,A.STYLE_REF,a.GMTS_ITEM_ID ORDER BY a.GMTS_ITEM_ID ,a.APPLICABLE_PERIOD  DESC , a.id DESC";//and a.APPROVED=1
			$gsdSqlResult=sql_select($sql_item);
			// echo $sql_item;die;

			foreach($gsdSqlResult as $rows)
			{
				foreach($style_wise_po_arr[$rows['STYLE_REF']] as $po_id)
				{
					if($item_smv_array[$po_id][$rows['GMTS_ITEM_ID']]=='')
					{
						$item_smv_array[$po_id][$rows['GMTS_ITEM_ID']]=$rows['TOTAL_SMV'];
					}
				}
			}
		}
		else
		{
			$sql_item="SELECT b.id, a.set_break_down, c.gmts_item_id, c.set_item_ratio, c.smv_pcs, c.smv_pcs_precost,c.smv_set from wo_po_details_master a,wo_po_break_down b, wo_po_details_mas_set_details c where b.job_id=a.id and b.job_id=c.job_id and a.company_name in($lc_com_ids) $poIds_cond and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1";
			// echo $sql_item;
			$resultItem=sql_select($sql_item);

			foreach($resultItem as $itemData)
			{
				if($smv_source==1)
				{
					$item_smv_array[$itemData[csf('id')]][$itemData[csf('gmts_item_id')]]=$itemData[csf('smv_pcs')];
				}
				if($smv_source==2)
				{
					$item_smv_array[$itemData[csf('id')]][$itemData[csf('gmts_item_id')]]=$itemData[csf('smv_pcs_precost')];
				}
			}
		}
		// echo "<pre>";print_r($item_smv_array);echo "</pre>";
		$produce_minute_arr=array();
		$prod_effiency_hour_arr=array();
		$sewing_effiency_arr=array();
		foreach($line_wise_production_arr as $job_id=>$job_val)
		{
			foreach($job_val as $po_id=>$po_val)
			{
				foreach($po_val as $item_id=>$item_val)
				{
					foreach($item_val as $prod_date=>$prod_val)
					{
						foreach($prod_val as $sew_line=>$row)
						{
							$sewout_prod_qty+=$row['qty'];
							$produce_minute_arr[$job_id]+=$row['qty']*$item_smv_array[$po_id][$item_id];
							$prod_effiency_hour_arr[$job_id]+=$prod_resource_array[$prod_date][$sew_line]['man_power']*($prod_resource_array[$prod_date][$sew_line]['working_hour']*60);

							$sewing_effiency_arr[$job_id]=(($produce_minute_arr[$job_id]/$prod_effiency_hour_arr[$job_id]))*100;


						}
					}
				}
			}
		}
			// echo "<pre>";print_r($produce_minute_arr[$job_id]);echo "</pre>";

	    // $jobid_cond = where_con_using_array($job_id_arr,0,"b.job_id");
         $order_sql="SELECT c.job_no_mst as job_id,c.color_number_id,c.item_number_id,c.size_number_id,c.order_quantity from  wo_po_color_size_breakdown c WHERE c.status_active=1 and c.is_deleted=0 and c.job_no_mst='$job_id'";

		 $main_order_sql=sql_select($order_sql);
         $order_arr=array();

		 foreach($main_order_sql as $row)
		 {
			$order_arr[$row[csf('job_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['order_quantity']+=$row[csf('order_quantity')];
		 }
		 
	    //  echo "<pre>";print_r($order_arr);echo "</pre>";

		$cm_sql="SELECT e.cm_cost,e.job_no as job_id from wo_pre_cost_dtls e WHERE e.status_active=1 and e.is_deleted=0 and e.job_no='$job_id'";

		$total_cm_sql=sql_select($cm_sql);
		$cm_arr=array();

		foreach($total_cm_sql as $row)
		{
		   $cm_arr[$row[csf('job_id')]]['cm_cost']=$row[csf('cm_cost')];
		}






        // $cm_sql="SELECT a.buyer_name,a.style_ref_no,b.id as po_id,b.job_no_mst as job_id,c.color_number_id,c.item_number_id,c.size_number_id,c.order_quantity,e.cm_cost from wo_po_details_master a,wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_dtls e WHERE a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and a.id=b.job_id and a.id=c.job_id and b.id=c.po_break_down_id and a.id=e.job_id and b.job_id=e.job_id $sql_cond $buyer_name $job_cond_id ";

		// $c_sql=sql_select($cm_sql);
		// $c_arr=array();

		// foreach($c_sql as $row)
		// {
		// 	$main_arr[$row[csf('buyer_name')]][$row[csf('job_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['order_quantity']+=$row[csf('order_quantity')];

		// 	$c_arr[$row[csf('buyer_name')]][$row[csf('job_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['cm_cost']=$row[csf('cm_cost')];

		// }
		//   echo '<pre>';
		//   print_r($c_arr);
		//   echo '</pre>';

		if($from_date !="")
		{
		  	$cutting_sql="SELECT b.job_no_mst as job_id,c.color_number_id,c.size_number_id,c.item_number_id,
			(CASE WHEN k.entry_date>=$txt_date_from AND k.entry_date<=$txt_date_to THEN l.qc_pass_qty ELSE 0 END) as cutting_total,
			(CASE WHEN   k.entry_date=$txt_date_from AND k.entry_date=$txt_date_to THEN l.qc_pass_qty ELSE 0 END) as cutting_today,
			(CASE WHEN  k.entry_date=$txt_date_from AND k.entry_date=$txt_date_to THEN l.bundle_qty ELSE 0 END) as bundle_total
		   
			from wo_po_details_master a,wo_po_break_down b,wo_po_color_size_breakdown c,pro_gmts_cutting_qc_mst k,pro_gmts_cutting_qc_dtls l
			WHERE a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and k.status_active=1 and k.is_deleted=0 and l.status_active=1 and l.is_deleted=0 and a.id=b.job_id and a.id=c.job_id and b.id=l.order_id  and k.id=l.mst_id and c.id=l.color_size_id and b.id=c.po_break_down_id  $com_cond3 $buyer_name $job_cond_id $qc_date";
		}
		else{

		 	$cutting_sql="SELECT a.buyer_name,b.id as po_id,b.job_no_mst as job_id,c.color_number_id,c.size_number_id,c.item_number_id,l.qc_pass_qty as cutting_total,l.bundle_qty as bundle_total

            from wo_po_details_master a,wo_po_break_down b,wo_po_color_size_breakdown c,pro_gmts_cutting_qc_mst k,pro_gmts_cutting_qc_dtls l
			WHERE a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and k.status_active=1 and k.is_deleted=0 and l.status_active=1 and l.is_deleted=0 and a.id=b.job_id and a.id=c.job_id and b.id=l.order_id  and k.id=l.mst_id and c.id=l.color_size_id and b.id=c.po_break_down_id  $com_cond3 $buyer_name $job_cond_id $qc_date";


		}

			$cutting_details=sql_select($cutting_sql);
			$cutting_qc_arr=array();

			foreach($cutting_details as $row)
			{
                $cutting_qc_arr[$row[csf('job_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['cutting_today']+=$row[csf('cutting_today')];

				$cutting_qc_arr[$row[csf('job_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['cutting_total']+=$row[csf('cutting_total')];
				$cutting_qc_arr[$row[csf('job_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['bundle_total']+=$row[csf('bundle_total')];
			}
			// echo '<pre>';
			// print_r($cutting_qc_arr);

			if($from_date !="")
		    {
			   	$cut_lay_sql="SELECT b.job_no_mst as job_id,c.color_id as color_number_id,j.size_id as    size_number_id,c.gmt_item_id as item_number_id,
				(CASE WHEN i.entry_date>=$txt_date_from AND i.entry_date<=$txt_date_to THEN j.size_qty ELSE 0 END) as plan_cutting_total,
				(CASE WHEN   i.entry_date=$txt_date_from AND i.entry_date=$txt_date_to THEN j.size_qty ELSE 0 END) as plan_cutting_today
				from wo_po_details_master a,wo_po_break_down b, ppl_cut_lay_dtls c,ppl_cut_lay_mst i,ppl_cut_lay_bundle j WHERE a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and i.status_active=1 and i.is_deleted=0 and i.status_active=1 and i.is_deleted=0 and a.id=b.job_id   and b.job_no_mst=i.job_no and i.id=j.mst_id and i.id=c.mst_id and b.id=j.order_id and c.id=j.dtls_id $com_cond1 $buyer_name $job_cond_id $cut_date";
		    }
			else
			{
			  	$cut_lay_sql="SELECT b.job_no_mst as job_id,c.color_id as color_number_id,j.size_id as    size_number_id,j.size_qty as plan_cutting_total,c.gmt_item_id as item_number_id	from wo_po_details_master a,wo_po_break_down b, ppl_cut_lay_dtls c,ppl_cut_lay_mst i,ppl_cut_lay_bundle j WHERE a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and c.is_deleted=0 and i.status_active=1 and i.is_deleted=0 and a.id=b.job_id   and b.job_no_mst=i.job_no and i.id=j.mst_id and i.id=c.mst_id and b.id=j.order_id and c.id=j.dtls_id $com_cond1 $buyer_name $job_cond_id $cut_date";
			}


			$plan_cut=sql_select($cut_lay_sql);
			$plan_cut_arr=array();

			foreach($plan_cut as $row)
			{
				$plan_cut_arr[$row[csf('job_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['plan_cutting_today']+=$row[csf('plan_cutting_today')];
				$plan_cut_arr[$row[csf('job_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['plan_cutting_total']+=$row[csf('plan_cutting_total')];
			}
			// echo '<pre>';
			// print_r($plan_cut_arr);
			if($from_date !="")
			{
			    $sendem_sql="SELECT b.job_no_mst as job_id,c.color_number_id,c.size_number_id,c.item_number_id,
				(CASE WHEN e.production_type=2 and d.embel_name=2 and d.production_date>=$txt_date_from AND d.production_date<=$txt_date_to THEN e.production_qnty ELSE 0 END) as sendem_total,
				(CASE WHEN e.production_type=2 and d.embel_name=2 and d.production_date=$txt_date_from AND d.production_date=$txt_date_to THEN e.production_qnty ELSE 0 END) as sendem_today,
				(CASE WHEN e.production_type=2 and d.production_date>=$txt_date_from AND d.production_date<=$txt_date_to THEN e.reject_qty ELSE 0 END) as reject_total
				from wo_po_details_master a,wo_po_break_down b,wo_po_color_size_breakdown c,pro_garments_production_mst d,pro_garments_production_dtls e WHERE a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and a.id=b.job_id and a.id=c.job_id and b.id=c.po_break_down_id  and d.id=e.mst_id and c.id=e.color_size_break_down_id and d.production_type=2 and e.production_type=2 and d.embel_name=2 $sql_cond $buyer_name $job_cond_id";
			}
			else
			{
				$sendem_sql="SELECT b.job_no_mst as job_id,c.color_number_id,c.size_number_id,c.item_number_id,e.production_qnty as sendem_total,e.reject_qty as reject_total
				from wo_po_details_master a,wo_po_break_down b,wo_po_color_size_breakdown c,pro_garments_production_mst d,pro_garments_production_dtls e WHERE a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and a.id=b.job_id and a.id=c.job_id and b.id=c.po_break_down_id  and d.id=e.mst_id and c.id=e.color_size_break_down_id and d.production_type=2 and e.production_type=2 and d.embel_name=2 $sql_cond $buyer_name $job_cond_id";
			}


			 $sendem_details=sql_select($sendem_sql);
			 $sendem_arr=array();

			 foreach($sendem_details as $row)
			 {
				 $sendem_arr[$row[csf('job_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['sendem_today']+=$row[csf('sendem_today')];
				 $sendem_arr[$row[csf('job_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['sendem_total']+=$row[csf('sendem_total')];
				 $sendem_arr[$row[csf('job_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['reject_total']+=$row[csf('reject_total')];
			 }
			//  echo '<pre>';
			//  print_r($sendem_arr);

			if($from_date !="")
			{
			    $sendrcv_sql="SELECT b.job_no_mst as job_id,c.color_number_id,c.size_number_id,c.item_number_id,
				(CASE WHEN e.production_type=3 and d.embel_name=2 and d.production_date>=$txt_date_from AND d.production_date<=$txt_date_to THEN e.production_qnty ELSE 0 END) as sendrcv_total,
				(CASE WHEN e.production_type=3 and d.embel_name=2 and d.production_date=$txt_date_from AND d.production_date=$txt_date_to THEN e.production_qnty ELSE 0 END) as sendrcv_today,
				(CASE WHEN e.production_type=3 and d.production_date>=$txt_date_from AND d.production_date<=$txt_date_to THEN e.reject_qty ELSE 0 END) as reject_total
				from wo_po_details_master a,wo_po_break_down b,wo_po_color_size_breakdown c,pro_garments_production_mst d,pro_garments_production_dtls e WHERE a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and a.id=b.job_id and a.id=c.job_id and b.id=c.po_break_down_id  and d.id=e.mst_id and c.id=e.color_size_break_down_id and d.production_type=3 and e.production_type=3 and d.embel_name=2 $sql_cond $buyer_name $job_cond_id";
			}
			else
			{
				$sendrcv_sql="SELECT b.job_no_mst as job_id,c.color_number_id,c.size_number_id,c.item_number_id,e.production_qnty as sendrcv_total,e.reject_qty as reject_total
				from wo_po_details_master a,wo_po_break_down b,wo_po_color_size_breakdown c,pro_garments_production_mst d,pro_garments_production_dtls e WHERE a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and a.id=b.job_id and a.id=c.job_id and b.id=c.po_break_down_id  and d.id=e.mst_id and c.id=e.color_size_break_down_id and d.production_type=3 and e.production_type=3 and d.embel_name=2 $sql_cond $buyer_name $job_cond_id";
			}


			 $sendrcv_details=sql_select($sendrcv_sql);
			 $sendrcv_arr=array();

			 foreach($sendrcv_details as $row)
			 {
				 $sendrcv_arr[$row[csf('job_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['sendrcv_today']+=$row[csf('sendrcv_today')];
				 $sendrcv_arr[$row[csf('job_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['sendrcv_total']+=$row[csf('sendrcv_total')];
				 $sendrcv_arr[$row[csf('job_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['reject_total']+=$row[csf('reject_total')];
			 }
			//  echo '<pre>';
			//  print_r($sendrcv_arr);

			if($from_date !="")
			{
			    $sendprint_sql="SELECT b.job_no_mst as job_id,c.color_number_id,c.size_number_id,c.item_number_id,
				(CASE WHEN e.production_type=2 and d.embel_name=1 and d.production_date>=$txt_date_from AND d.production_date<=$txt_date_to THEN e.production_qnty ELSE 0 END) as sendprint_total,
				(CASE WHEN e.production_type=2 and d.embel_name=1 and d.production_date=$txt_date_from AND d.production_date=$txt_date_to THEN e.production_qnty ELSE 0 END) as sendprint_today,
				(CASE WHEN e.production_type=2 and d.production_date>=$txt_date_from AND d.production_date<=$txt_date_to THEN e.reject_qty ELSE 0 END) as reject_total
				from wo_po_details_master a,wo_po_break_down b,wo_po_color_size_breakdown c,pro_garments_production_mst d,pro_garments_production_dtls e WHERE a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and a.id=b.job_id and a.id=c.job_id and b.id=c.po_break_down_id  and d.id=e.mst_id and c.id=e.color_size_break_down_id and d.production_type=2 and e.production_type=2 and d.embel_name=1 $sql_cond $buyer_name $job_cond_id";
			}
			else
			{
				$sendprint_sql="SELECT b.job_no_mst as job_id,c.color_number_id,c.size_number_id,c.item_number_id,e.production_qnty as sendprint_total,e.reject_qty as reject_total
				from wo_po_details_master a,wo_po_break_down b,wo_po_color_size_breakdown c,pro_garments_production_mst d,pro_garments_production_dtls e WHERE a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and a.id=b.job_id and a.id=c.job_id and b.id=c.po_break_down_id  and d.id=e.mst_id and c.id=e.color_size_break_down_id and d.production_type=2 and e.production_type=2 and d.embel_name=1 $sql_cond $buyer_name $job_cond_id";
			}


			 $sendprint_details=sql_select($sendprint_sql);
			 $sendprint_arr=array();

			 foreach($sendprint_details as $row)
			 {
				 $sendprint_arr[$row[csf('job_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['sendprint_today']+=$row[csf('sendprint_today')];
				 $sendprint_arr[$row[csf('job_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['sendprint_total']+=$row[csf('sendprint_total')];
				 $sendprint_arr[$row[csf('job_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['reject_total']+=$row[csf('reject_total')];
			 }
			//  echo '<pre>';
			//  print_r($sendprint_arr);

			if($from_date !="")
			{
			    $rprint_sql="SELECT b.job_no_mst as job_id,c.color_number_id,c.size_number_id,c.item_number_id,
				(CASE WHEN e.production_type=3 and d.embel_name=1 and d.production_date>=$txt_date_from AND d.production_date<=$txt_date_to THEN e.production_qnty ELSE 0 END) as printrcv_total,
				(CASE WHEN e.production_type=3 and d.embel_name=1 and d.production_date=$txt_date_from AND d.production_date=$txt_date_to THEN e.production_qnty ELSE 0 END) as printrcv_today,
				(CASE WHEN e.production_type=3 and d.production_date>=$txt_date_from AND d.production_date<=$txt_date_to THEN e.reject_qty ELSE 0 END) as reject_total
				from wo_po_details_master a,wo_po_break_down b,wo_po_color_size_breakdown c,pro_garments_production_mst d,pro_garments_production_dtls e WHERE a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and a.id=b.job_id and a.id=c.job_id and b.id=c.po_break_down_id  and d.id=e.mst_id and c.id=e.color_size_break_down_id and d.production_type=3 and e.production_type=3 and d.embel_name=1 $sql_cond $buyer_name $job_cond_id";
			}
			else
			{
				$rprint_sql="SELECT b.job_no_mst as job_id,c.color_number_id,c.size_number_id,c.item_number_id,e.production_qnty as printrcv_total,e.reject_qty as reject_total
				from wo_po_details_master a,wo_po_break_down b,wo_po_color_size_breakdown c,pro_garments_production_mst d,pro_garments_production_dtls e WHERE a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and a.id=b.job_id and a.id=c.job_id and b.id=c.po_break_down_id  and d.id=e.mst_id and c.id=e.color_size_break_down_id and d.production_type=3 and e.production_type=3 and d.embel_name=1 $sql_cond $buyer_name $job_cond_id";
			}


			 $rprint_details=sql_select($rprint_sql);
			 $rprint_arr=array();

			 foreach($rprint_details as $row)
			 {
				 $rprint_arr[$row[csf('job_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['printrcv_today']+=$row[csf('printrcv_today')];
				 $rprint_arr[$row[csf('job_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['printrcv_total']+=$row[csf('printrcv_total')];
				 $rprint_arr[$row[csf('job_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['reject_total']+=$row[csf('reject_total')];
			 }
			//  echo '<pre>';
			//  print_r($sendrcv_arr);

			if($from_date !="")
			{
			     $sewinginput_sql="SELECT b.job_no_mst as job_id,c.color_number_id,c.size_number_id, c.item_number_id,
				(CASE WHEN e.production_type=4 and d.production_date>=$txt_date_from AND d.production_date<=$txt_date_to THEN e.production_qnty ELSE 0 END) as sewin_total,
				(CASE WHEN e.production_type=4  and d.production_date=$txt_date_from AND d.production_date=$txt_date_to THEN e.production_qnty ELSE 0 END) as sewin_today
				from wo_po_details_master a,wo_po_break_down b,wo_po_color_size_breakdown c,pro_garments_production_mst d,pro_garments_production_dtls e WHERE a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and a.id=b.job_id and a.id=c.job_id and b.id=c.po_break_down_id  and d.id=e.mst_id and c.id=e.color_size_break_down_id and d.production_type=4 and e.production_type=4  $sql_cond $buyer_name $job_cond_id";
			}
			else
			{
			   	$sewinginput_sql="SELECT b.job_no_mst as job_id,c.color_number_id,c.size_number_id,c.item_number_id,e.production_qnty as sewin_total
				from wo_po_details_master a,wo_po_break_down b,wo_po_color_size_breakdown c,pro_garments_production_mst d,pro_garments_production_dtls e WHERE a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and a.id=b.job_id and a.id=c.job_id and b.id=c.po_break_down_id  and d.id=e.mst_id and c.id=e.color_size_break_down_id and d.production_type=4 and e.production_type=4  $sql_cond $buyer_name $job_cond_id";
			}


			 $sewinput_details=sql_select($sewinginput_sql);
			 $sewinput_arr=array();

			 foreach($sewinput_details as $row)
			 {
				 $sewinput_arr[$row[csf('job_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['sewin_today']+=$row[csf('sewin_today')];
				 $sewinput_arr[$row[csf('job_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['sewin_total']+=$row[csf('sewin_total')];
			 }
			//  echo '<pre>';
			//  print_r($sewinput_arr);
			if($from_date !="")
			{
			    $sewingoutput_sql="SELECT b.job_no_mst as job_id,c.color_number_id,c.size_number_id, 
				c.item_number_id,
				(CASE WHEN e.production_type=5 and d.production_date>=$txt_date_from AND d.production_date<=$txt_date_to THEN e.production_qnty ELSE 0 END) as sewout_total,
				(CASE WHEN e.production_type=5  and d.production_date=$txt_date_from AND d.production_date=$txt_date_to THEN e.production_qnty ELSE 0 END) as sewout_today,
				(CASE WHEN e.production_type=5 and d.production_date>=$txt_date_from AND d.production_date<=$txt_date_to THEN e.reject_qty ELSE 0 END) as reject_total
				from wo_po_details_master a,wo_po_break_down b,wo_po_color_size_breakdown c,pro_garments_production_mst d,pro_garments_production_dtls e WHERE a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and a.id=b.job_id and a.id=c.job_id and b.id=c.po_break_down_id  and d.id=e.mst_id and c.id=e.color_size_break_down_id and d.production_type=5 and e.production_type=5  $sql_cond $buyer_name $job_cond_id";
			}
			else
			{
			   	$sewingoutput_sql="SELECT b.job_no_mst as job_id,c.color_number_id,c.size_number_id,c.item_number_id,e.production_qnty as sewout_total,e.reject_qty as reject_total
				from wo_po_details_master a,wo_po_break_down b,wo_po_color_size_breakdown c,pro_garments_production_mst d,pro_garments_production_dtls e WHERE a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and a.id=b.job_id and a.id=c.job_id and b.id=c.po_break_down_id  and d.id=e.mst_id and c.id=e.color_size_break_down_id and d.production_type=5 and e.production_type=5  $sql_cond $buyer_name $job_cond_id";
			}


			 $sewoutput_details=sql_select($sewingoutput_sql);
			 $sewoutput_arr=array();

			 foreach($sewoutput_details as $row)
			 {
				 $sewoutput_arr[$row[csf('job_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['sewout_today']+=$row[csf('sewout_today')];
				 $sewoutput_arr[$row[csf('job_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['sewout_total']+=$row[csf('sewout_total')];
				 $sewoutput_arr[$row[csf('job_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['reject_total']+=$row[csf('reject_total')];
			 }
			//  echo '<pre>';
			//  print_r($sewoutput_arr);

			$hold_sql="SELECT b.job_no_mst as job_id,c.color_number_id,c.size_number_id,c.item_number_id,e.  	production_qnty as hold_qnty,e.bndl_hold_reason
			from wo_po_details_master a,wo_po_break_down b,wo_po_color_size_breakdown c,pro_garments_production_mst d,pro_garments_production_dtls e WHERE a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and a.id=b.job_id and a.id=c.job_id and b.id=c.po_break_down_id  and d.id=e.mst_id and c.id=e.color_size_break_down_id and d.production_type=5 and e.production_type=5 and e.bndl_hold_reason !=0  $sql_cond $buyer_name $job_cond_id";

			$total_hold_sql=sql_select($hold_sql);
            $total_hold_arr=array();

			foreach($total_hold_sql as $row)
			{
				$total_hold_arr[$row[csf('job_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['hold_qnty']+=$row[csf('hold_qnty')];
			}
			//  echo '<pre>';
			//  print_r($total_hold_arr);

			if($from_date !="")
			{
			    $iron_sql="SELECT b.job_no_mst as job_id,c.color_number_id,c.size_number_id,c.item_number_id,
				(CASE WHEN e.production_type=7 and d.production_date>=$txt_date_from AND d.production_date<=$txt_date_to THEN e.production_qnty ELSE 0 END) as iron_total,
				(CASE WHEN e.production_type=7  and d.production_date=$txt_date_from AND d.production_date=$txt_date_to THEN e.production_qnty ELSE 0 END) as iron_today,
				(CASE WHEN e.production_type=7 and d.production_date>=$txt_date_from AND d.production_date<=$txt_date_to THEN e.reject_qty ELSE 0 END) as reject_total
				from wo_po_details_master a,wo_po_break_down b,wo_po_color_size_breakdown c,pro_garments_production_mst d,pro_garments_production_dtls e WHERE a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and a.id=b.job_id and a.id=c.job_id and b.id=c.po_break_down_id  and d.id=e.mst_id and c.id=e.color_size_break_down_id and d.production_type=7 and e.production_type=7  $sql_cond $buyer_name $job_cond_id";
			}
			else
			{
			   	$iron_sql="SELECT b.job_no_mst as job_id,c.color_number_id,c.size_number_id,c.item_number_id,e.production_qnty as iron_total,e.reject_qty as reject_total
				from wo_po_details_master a,wo_po_break_down b,wo_po_color_size_breakdown c,pro_garments_production_mst d,pro_garments_production_dtls e WHERE a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and a.id=b.job_id and a.id=c.job_id and b.id=c.po_break_down_id  and d.id=e.mst_id and c.id=e.color_size_break_down_id and d.production_type=7 and e.production_type=7  $sql_cond $buyer_name $job_cond_id";
			}

			 $iron_details=sql_select($iron_sql);
			 $iron_arr=array();

			 foreach($iron_details as $row)
			 {
				 $iron_arr[$row[csf('job_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['iron_today']+=$row[csf('iron_today')];
				 $iron_arr[$row[csf('job_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['iron_total']+=$row[csf('iron_total')];
				 $iron_arr[$row[csf('job_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['reject_total']+=$row[csf('reject_total')];
			 }

			 if($from_date !="")
			{
			    $reiron_sql="SELECT b.job_no_mst as job_id,c.color_number_id,c.size_number_id,c.item_number_id,
				(CASE WHEN e.production_type=7 and d.production_date>=$txt_date_from AND d.production_date<=$txt_date_to THEN e.re_production_qty ELSE 0 END) as reiron_total,
				(CASE WHEN e.production_type=7  and d.production_date=$txt_date_from AND d.production_date=$txt_date_to THEN e.re_production_qty ELSE 0 END) as reiron_today
				from wo_po_details_master a,wo_po_break_down b,wo_po_color_size_breakdown c,pro_garments_production_mst d,pro_garments_production_dtls e WHERE a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and a.id=b.job_id and a.id=c.job_id and b.id=c.po_break_down_id  and d.id=e.mst_id and c.id=e.color_size_break_down_id and d.production_type=7 and e.production_type=7  $sql_cond $buyer_name $job_cond_id";
			}
			else
			{
			   	$reiron_sql="SELECT b.job_no_mst as job_id,c.color_number_id,c.size_number_id,c.item_number_id,e.re_production_qty  as reiron_total
				from wo_po_details_master a,wo_po_break_down b,wo_po_color_size_breakdown c,pro_garments_production_mst d,pro_garments_production_dtls e WHERE a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and a.id=b.job_id and a.id=c.job_id and b.id=c.po_break_down_id  and d.id=e.mst_id and c.id=e.color_size_break_down_id and d.production_type=7 and e.production_type=7  $sql_cond $buyer_name $job_cond_id";
			}

			$reiron_details=sql_select($reiron_sql);
			$reiron_arr=array();

			foreach($reiron_details as $row)
			{
				$reiron_arr[$row[csf('job_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['reiron_today']+=$row[csf('reiron_today')];
				$reiron_arr[$row[csf('job_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['reiron_total']+=$row[csf('reiron_total')];
			}

			if($from_date !="")
			{

				$poly_sql="SELECT b.job_no_mst as job_id,c.color_number_id,c.size_number_id,c.item_number_id,
				(CASE WHEN e.production_type=11 and d.production_date>=$txt_date_from AND d.production_date<=$txt_date_to THEN e.production_qnty ELSE 0 END) as poly_total,
				(CASE WHEN e.production_type=11  and d.production_date=$txt_date_from AND d.production_date=$txt_date_to THEN e.production_qnty ELSE 0 END) as poly_today,
				(CASE WHEN e.production_type=11 and d.production_date>=$txt_date_from AND d.production_date<=$txt_date_to THEN e.reject_qty ELSE 0 END) as reject_total
				from wo_po_details_master a,wo_po_break_down b,wo_po_color_size_breakdown c,pro_garments_production_mst d,pro_garments_production_dtls e WHERE a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and a.id=b.job_id and a.id=c.job_id and b.id=c.po_break_down_id  and d.id=e.mst_id and c.id=e.color_size_break_down_id and d.production_type=11 and e.production_type=11  $sql_cond $buyer_name $job_cond_id";
			}
			else
			{
			   	$poly_sql="SELECT b.job_no_mst as job_id,c.color_number_id,c.size_number_id,c.item_number_id,e.production_qnty as poly_total,e.reject_qty as reject_total
				from wo_po_details_master a,wo_po_break_down b,wo_po_color_size_breakdown c,pro_garments_production_mst d,pro_garments_production_dtls e WHERE a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and a.id=b.job_id and a.id=c.job_id and b.id=c.po_break_down_id  and d.id=e.mst_id and c.id=e.color_size_break_down_id and d.production_type=11 and e.production_type=11  $sql_cond $buyer_name $job_cond_id";
			}

			$poly_details=sql_select($poly_sql);
			$poly_arr=array();

			foreach($poly_details as $row)
			{
				$poly_arr[$row[csf('job_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['poly_today']+=$row[csf('poly_today')];
				$poly_arr[$row[csf('job_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['poly_total']+=$row[csf('poly_total')];
				$poly_arr[$row[csf('job_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['reject_total']+=$row[csf('reject_total')];
			}

			//  echo '<pre>';
			//  print_r($poly_arr);
			if($from_date !="")
			{

			   	$finish_sql="SELECT b.job_no_mst as job_id,c.color_number_id,c.size_number_id,c.item_number_id,
				(CASE WHEN e.production_type=8 and d.production_date>=$txt_date_from AND d.production_date<=$txt_date_to THEN e.production_qnty ELSE 0 END) as finish_total,
				(CASE WHEN e.production_type=8  and d.production_date=$txt_date_from AND d.production_date=$txt_date_to THEN e.production_qnty ELSE 0 END) as finish_today
				from wo_po_details_master a,wo_po_break_down b,wo_po_color_size_breakdown c,pro_garments_production_mst d,pro_garments_production_dtls e WHERE a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and a.id=b.job_id and a.id=c.job_id and b.id=c.po_break_down_id  and d.id=e.mst_id and c.id=e.color_size_break_down_id and d.production_type=8 and e.production_type=8  $sql_cond $buyer_name $job_cond_id";
			}
			else
			{
			   	$finish_sql="SELECT b.job_no_mst as job_id,c.color_number_id,c.size_number_id,c.item_number_id,e.production_qnty as finish_total
				from wo_po_details_master a,wo_po_break_down b,wo_po_color_size_breakdown c,pro_garments_production_mst d,pro_garments_production_dtls e WHERE a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and a.id=b.job_id and a.id=c.job_id and b.id=c.po_break_down_id  and d.id=e.mst_id and c.id=e.color_size_break_down_id and d.production_type=8 and e.production_type=8  $sql_cond $buyer_name $job_cond_id";
			}


			$finish_details=sql_select($finish_sql);
			$finish_arr=array();

			foreach($finish_details as $row)
			{
				$finish_arr[$row[csf('job_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['finish_today']+=$row[csf('finish_today')];
				$finish_arr[$row[csf('job_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['finish_total']+=$row[csf('finish_total')];
			}

			//  echo '<pre>';
			//  print_r($finish_arr);
			if($from_date !="")
			{

			   	$ex_factory_sql="SELECT b.job_no_mst as job_id,c.color_number_id,c.size_number_id,c.item_number_id,
				(CASE WHEN  f.ex_factory_date>=$txt_date_from AND f.ex_factory_date<=$txt_date_to THEN g.production_qnty ELSE 0 END) as ex_factory_total,
				(CASE WHEN   f.ex_factory_date=$txt_date_from AND f.ex_factory_date=$txt_date_to THEN g.production_qnty ELSE 0 END) as ex_factory_today
				from wo_po_details_master a,wo_po_break_down b,wo_po_color_size_breakdown c,pro_ex_factory_mst f,pro_ex_factory_dtls g WHERE a.status_active=1 and a.is_deleted=0 and b.status_active=1  and b.is_deleted=0 and c.status_active=1  and c.is_deleted=0  and f.status_active=1 and f.is_deleted=0 and g.status_active=1 and g.is_deleted=0 and a.id=b.job_id and b.id=f.po_break_down_id and f.id=g.mst_id and c.id=g.color_size_break_down_id and f.entry_form !=85  $com_cond1 $buyer_name $job_cond_id $ex_fact_date";
			}
			else
			{
			   	$ex_factory_sql="SELECT b.job_no_mst as job_id,c.color_number_id,c.size_number_id,c.item_number_id, g.production_qnty as ex_factory_total
				from wo_po_details_master a,wo_po_break_down b,wo_po_color_size_breakdown c,pro_ex_factory_mst f,pro_ex_factory_dtls g WHERE a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1  and c.is_deleted=0 and f.status_active=1 and f.is_deleted=0 and g.status_active=1 and g.is_deleted=0  and a.id=b.job_id and  b.id=f.po_break_down_id  and f.id=g.mst_id and c.id=g.color_size_break_down_id and f.entry_form !=85    $com_cond1 $buyer_name $job_cond_id $ex_fact_date";
			}

			$ex_factory_details=sql_select($ex_factory_sql);
			$ex_factory_arr=array();

			foreach($ex_factory_details as $row)
			{
				$ex_factory_arr[$row[csf('job_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['ex_factory_total']+=$row[csf('ex_factory_total')];
				$ex_factory_arr[$row[csf('job_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['ex_factory_today']+=$row[csf('ex_factory_today')];
			}
			//  echo '<pre>';
			//  print_r($ex_factory_arr);










		$rowspan_arr=array();

		foreach($main_arr as $buyer_name=>$buyer_val)
		{  
			foreach($buyer_val as $job_id=>$job_val)
			{
				foreach($job_val as $item_number_id=>$item_val)
				{
					foreach($item_val as $color_number_id=>$color_val)
					{
						foreach($color_val as $size_number_id=>$row)
						{
							$rowspan_arr[$buyer_name][$job_id][$item_number_id][$color_number_id]++;

						}
					}
				}
			}
		   
		}




		?>


       <br>
	   <fieldset width="100%">
		<div style="width:5970px">
		 <table class="rpt_table" width="5950px" cellpadding="0" cellspacing="0" border="1" rules="all">
			<thead>
				<tr>
					<th width="50px" rowspan="2">SI</th>
					<th width="100px" rowspan="2">Buyer</th>
					<th width="100px" rowspan="2">Style</th>
					<th width="100px" rowspan="2">Job No</th>
					<th width="100px" rowspan="2">Garment Item</th>
					<th width="100px" rowspan="2">Color</th>
					<th width="100px" rowspan="2">Size</th>
					<th width="100px" rowspan="2">Order Qty(Pcs)</th>
					<th width="100px" rowspan="2">CM</th>
					<th width="400px" colspan="4">Cutting Qty</th>
					<th width="300px" colspan="3">Cutting QC Qty</th>
					<th width="300px" colspan="3">Send to Print</th>
					<th width="400px" colspan="4">Rcv From Print</th>
					<th width="300px" colspan="3">Send To Emb</th>
					<th width="400px" colspan="4">Rcv From Emv</th>
					<th width="300px" colspan="3">Sewing Input</th>
					<th width="400px" colspan="4">Sewing Output</th>
					<th width="100px" rowspan="2">Sewing Efficiency%</th>
					<th width="100px" rowspan="2">Hold Qty</th>
					<th width="400px" colspan="4">Iron Entry</th>
					<th width="200px" colspan="2">Re Iron</th>
					<th width="300px" colspan="3">Poly Entry</th>
					<th width="200px" colspan="2">Re Poly</th>
					<th width="100px" rowspan="2">Finishing Efficiency</th>
					<th width="300px" colspan="3">Packing & Finishing</th>
					<th width="200px" colspan="2">Re-packing/Re-check</th>
					<th width="300px" colspan="3">Ex-Factory</th>
					<th width="100px" rowspan="2">Ex-Factory Status%</th>


				</tr>
				<tr>
					<th width="100">Today</th>
					<th width="100">Total</th>
					<th width="100">WIP</th>
					<th width="100">Cutting %</th>
					<th width="100">Today</th>
					<th width="100">Total</th>
					<th width="100">Total Rej</th>
					<th width="100">Today</th>
					<th width="100">Total</th>
					<th width="100">WIP</th>
					<th width="100">Today</th>
					<th width="100">Total</th>
					<th width="100">Total rej</th>
					<th width="100">WIP</th>
					<th width="100">Today</th>
					<th width="100">Total</th>
					<th width="100">WIP</th>
					<th width="100">Today</th>
					<th width="100">Total</th>
					<th width="100">Total rej</th>
					<th width="100">WIP</th>
					<th width="100">Today</th>
					<th width="100">Total</th>
					<th width="100">WIP</th>
					<th width="100">Today</th>
					<th width="100">Total</th>
					<th width="100">Total rej</th>
					<th width="100">WIP</th>
					<th width="100">Today</th>
					<th width="100">Total</th>
					<th width="100">Total rej</th>
					<th width="100">WIP</th>
					<th width="100">Today</th>
					<th width="100">Total</th>
					<th width="100">Today</th>
					<th width="100">Total</th>
					<th width="100">Wip</th>
					<th width="100">Today</th>
					<th width="100">Total</th>
					<th width="100">Today</th>
					<th width="100">Total</th>
					<th width="100">Wip</th>
					<th width="100">Today</th>
					<th width="100">Total</th>
					<th width="100">Today</th>
					<th width="100">Total</th>
					<th width="100">Wip</th>



				</tr>
			</thead>
			<tbody id="table_body_id">
		  		<?
				  $i=1;
				  foreach($main_arr as $buyer_name=>$buyer_val)
				  {
						$buyer_wise_order_qty=0;
						$buyer_wise_plan_cut_today=0;
						$buyer_wise_plan_cut_total=0;
						$buyer_wise_cutting_qc_today=0;
						$buyer_wise_cutting_qc_total=0;
						$buyer_wise_cutting_qc_reject=0;
						$buyer_wise_send_print_today=0;
						$buyer_wise_send_print_total=0;
						$buyer_wise_rcv_print_today=0;
						$buyer_wise_rcv_print_total=0;
						$buyer_wise_rcv_print_reject=0;
						$buyer_wise_send_em_today=0;
						$buyer_wise_send_em_total=0;
						$buyer_wise_rcv_em_today=0;
						$buyer_wise_rcv_em_total=0;
						$buyer_wise_rcv_em_reject=0;
						$buyer_wise_sewing_input_today=0;
						$buyer_wise_sewing_input_total=0;
						$buyer_wise_sewing_output_today=0;
						$buyer_wise_sewing_output_total=0;
						$buyer_wise_sewing_output_reject=0;
						$buyer_wise_hold=0;
						$buyer_wise_iron_today=0;
						$buyer_wise_iron_total=0;
						$buyer_wise_iron_reject=0;
						$buyer_wise_reiron_today=0;
						$buyer_wise_reiron_total=0;
						$buyer_wise_poly_entry_today=0;
						$buyer_wise_poly_entry_total=0;
						$buyer_wise_finish_today=0;
						$buyer_wise_finish_total=0;
						$buyer_wise_ex_factory_today=0;
						$buyer_wise_ex_factory_total=0;

						foreach($buyer_val as $job_id=>$job_val)
						{
							
							$job_wise_order_qty=0;
							$job_wise_plan_cut_today=0;
							$job_wise_plan_cut_total=0;
							$job_wise_cutting_qc_today=0;
							$job_wise_cutting_qc_total=0;
							$job_wise_cutting_qc_reject=0;
							$job_wise_send_print_today=0;
							$job_wise_send_print_total=0;
							$job_wise_rcv_print_today=0;
							$job_wise_rcv_print_total=0;
							$job_wise_rcv_print_reject=0;
							$job_wise_send_em_today=0;
							$job_wise_send_em_total=0;
							$job_wise_rcv_em_today=0;
							$job_wise_rcv_em_total=0;
							$job_wise_rcv_em_reject=0;
							$job_wise_sewing_input_today=0;
							$job_wise_sewing_input_total=0;
							$job_wise_sewing_output_today=0;
							$job_wise_sewing_output_total=0;
							$job_wise_sewing_output_reject=0;
							$job_wise_hold=0;
							$job_wise_iron_today=0;
							$job_wise_iron_total=0;
							$job_wise_iron_reject=0;
							$job_wise_reiron_today=0;
							$job_wise_reiron_total=0;
							$job_wise_poly_entry_today=0;
							$job_wise_poly_entry_total=0;
							$job_wise_finish_today=0;
							$job_wise_finish_total=0;
							$job_wise_ex_factory_today=0;
							$job_wise_ex_factory_total=0;
							foreach($job_val as $item_number_id=>$item_val)
							{
								
								foreach($item_val as $color_number_id=>$color_val)
								{
									$l=0;
									$color_wise_order_qty=0;
									$color_wise_plan_cut_today=0;
									$color_wise_plan_cut_total=0;
									$color_wise_cutting_qc_today=0;
									$color_wise_cutting_qc_total=0;
									$color_wise_cutting_qc_reject=0;
									$color_wise_send_print_today=0;
									$color_wise_send_print_total=0;
									$color_wise_rcv_print_today=0;
									$color_wise_rcv_print_total=0;
									$color_wise_rcv_print_reject=0;
									$color_wise_send_em_today=0;
									$color_wise_send_em_total=0;
									$color_wise_rcv_em_today=0;
									$color_wise_rcv_em_total=0;
									$color_wise_rcv_em_reject=0;
									$color_wise_sewing_input_today=0;
									$color_wise_sewing_input_total=0;
									$color_wise_sewing_output_today=0;
									$color_wise_sewing_output_total=0;
									$color_wise_sewing_output_reject=0;
									$color_wise_hold=0;
									$color_wise_iron_today=0;
									$color_wise_iron_total=0;
									$color_wise_iron_reject=0;
									$color_wise_reiron_today=0;
									$color_wise_reiron_total=0;
									$color_wise_poly_entry_today=0;
									$color_wise_poly_entry_total=0;
									$color_wise_finish_today=0;
									$color_wise_finish_total=0;
									$color_wise_ex_factory_today=0;
									$color_wise_ex_factory_total=0;

									foreach($color_val as $size_number_id=>$row)
									{
										if ($i%2==0)
										$bgcolor="#E9F3FF";
										else
										$bgcolor="#FFFFFF";

									  ?>
                                        <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
											<td><? echo $i;?></td>
											<td><p><? echo $buyerArr[$buyer_name]; ?></p></td>
											<td><p><? echo $row['style'];?></p></td>
											<td><p><? echo $job_id;?></td>
											<td><p><? echo  $garments_item[$item_number_id];?></td>
											<td><p><? echo $color_library[$color_number_id];?></p></td>
											<td><p><? echo $size_library[$size_number_id];?></p></td>
											<td align="right"><? echo $order_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['order_quantity'];?></td>
											<?
											if($l==0)
											{
											?>
											<td align="center" valign="middle" rowspan="<? echo $rowspan_arr[$buyer_name][$job_id][$item_number_id][$color_number_id];?>"><? echo $cm_arr[$job_id]['cm_cost'];?></td>
											<?
											}
											?>
											<td align="right"><? echo $plan_cut_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['plan_cutting_today'];?></td>
											<td align="right"><a href="##" onclick="openmypage_cutting('<? echo  $job_id; ?>','<? echo $item_number_id; ?>','<? echo $color_number_id; ?>',<? echo $size_number_id; ?>, 'cutting_popup',850,350)"> <? echo $plan_cut_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['plan_cutting_total'];?></a></td>
											<td align="right"><? $plan_wip=$plan_cut_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['plan_cutting_total']-$order_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['order_quantity']; echo fn_number_format($plan_wip,2); ?></td>
											<td align="right"><? $plan_per=($plan_cut_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['plan_cutting_total']/$order_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['order_quantity'])*100; echo fn_number_format($plan_per,2); ?></td>
											<td align="right"><? echo $cutting_qc_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['cutting_today'];?></td>
											<td align="right"><? echo $cutting_qc_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['cutting_total']; ?></td>
											<td align="right"><a href="##" onclick="openmypage_reject('<? echo  $job_id; ?>','<? echo $color_number_id; ?>',<? echo $size_number_id; ?>, 'reject_popup',850,350)"><? $cutting_reject= $cutting_qc_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['bundle_total']-$cutting_qc_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['cutting_total']; echo fn_number_format($cutting_reject,2);?></a></td>
											<td align="right"><? echo $sendprint_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['sendprint_today'];?></td>
											<td align="right"><? echo $sendprint_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['sendprint_total'];?></td>
											<td align="right"><? $sendprint_wip=$sendprint_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['sendprint_total']-$cutting_qc_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['cutting_total'];echo fn_number_format($sendprint_wip,2); ?></td>

											<td align="right"><? echo $rprint_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['printrcv_today'];?></td>

											<td align="right"><? echo $rprint_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['printrcv_total'];?></td>
											<td align="right"><a href="##" onclick="openmypage_reject('<? echo  $job_id; ?>','<? echo $color_number_id; ?>',<? echo $size_number_id; ?>, 'reject_popup',850,350)"><? echo $rprint_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['reject_total'];?></a></td>
											<td align="right"><? $print_wip=$rprint_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['printrcv_total']-$sendprint_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['sendprint_total']; echo fn_number_format($print_wip,2);?></td>
											<td align="right"><? echo $sendem_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['sendem_today'];?></td>
											<td align="right"><? echo $sendem_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['sendem_total'];?></td>
											<td align="right"><?  $sendem_wip=$sendem_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['sendem_total']-$cutting_qc_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['cutting_total']; echo fn_number_format($sendem_wip);?></td>
											<td align="right"><? echo $sendrcv_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['sendrcv_today'];?></td>
											<td align="right"><? echo $sendrcv_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['sendrcv_total'];?></td>
											<td align="right"><a href="##" onclick="openmypage_reject('<? echo  $job_id; ?>','<? echo $color_number_id; ?>',<? echo $size_number_id; ?>, 'reject_popup',850,350)"><? echo $sendrcv_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['reject_total'];?></a></td>
											<td align="right"><? $sendrcv_wip=$sendrcv_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['sendrcv_total']-$sendem_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['sendem_total']; echo fn_number_format($sendrcv_wip,2);?></td>

											<td align="right"><? echo $sewinput_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['sewin_today'];?></td>
											<td align="right"><a href="##" onclick="openmypage_sewing('<? echo  $job_id; ?>','<? echo $item_number_id; ?>','<? echo $color_number_id; ?>',<? echo $size_number_id; ?>, 'sewing_popup',850,350)"> <? echo $sewinput_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['sewin_total'];?></a></td>
											<td align="right"><? $sewin_wip=$sewinput_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['sewin_total']-$cutting_qc_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['cutting_total']; echo fn_number_format($sewin_wip);?></td>
											<td align="right"><? echo $sewoutput_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['sewout_today'];?></td>
											<td align="right"><a href="##" onclick="openmypage_sewingout('<? echo  $job_id; ?>','<? echo $item_number_id; ?>','<? echo $color_number_id; ?>',<? echo $size_number_id; ?>, 'sewingout_popup',850,350)"><? echo $sewoutput_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['sewout_total'];?></a></td>
											<td align="right"><a href="##" onclick="openmypage_reject('<? echo  $job_id; ?>','<? echo $color_number_id; ?>',<? echo $size_number_id; ?>, 'reject_popup',850,350)"><? echo $sewoutput_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['reject_total'];?></a></td>
											<td align="right"><? $sew_wip=$sewoutput_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['sewout_total']-$sewinput_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['sewin_total']; echo fn_number_format($sew_wip,2);  ?></td>
											<?
											if($l==0)
											{
											?>
											<td align="center" valign="middle" rowspan="<? echo $rowspan_arr[$buyer_name][$job_id][$item_number_id][$color_number_id];?>"><? echo fn_number_format($sewing_effiency_arr[$job_id],2);
											?></td>
											<?
											}
											?>
											<td align="right"><a href="##" onclick="openmypage_hold('<? echo  $job_id; ?>','<? echo $item_number_id; ?>','<? echo $color_number_id; ?>',<? echo $size_number_id; ?>, 'hold_popup',850,350)"><? echo $total_hold_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['hold_qnty'];?></a></td>
											<td align="right"><? echo $iron_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['iron_today'];?></td>
											<td align="right"><a href="##" onclick="openmypage_iron('<? echo  $job_id; ?>','<? echo $item_number_id; ?>','<? echo $color_number_id; ?>',<? echo $size_number_id; ?>, 'iron_popup',850,350)"><? echo $iron_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['iron_total'];?></a></td>
											<td align="right"><a href="##" onclick="openmypage_reject('<? echo  $job_id; ?>','<? echo $color_number_id; ?>',<? echo $size_number_id; ?>, 'reject_popup',850,350)"><? echo $iron_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['reject_total']; ?></a></td>
											<td align="right"><? $iron_wip=$iron_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['iron_total']-$sewoutput_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['sewout_total']; echo fn_number_format($iron_wip); ?></td>
											<td align="right"><? echo $reiron_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['reiron_today'];?></td>
											<td align="right"><? echo $reiron_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['reiron_total'];?></td>
											<td align="right"><?echo $poly_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['poly_today'];?></td>
											<td align="right"><a href="##" onclick="openmypage_poly('<? echo  $job_id; ?>','<? echo $item_number_id; ?>','<? echo $color_number_id; ?>',<? echo $size_number_id; ?>, 'poly_popup',850,350)"><? echo $poly_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['poly_total'];?></a></td>
											<td align="right"><? $poly_wip=$poly_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['poly_total']-$iron_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['iron_total']; echo fn_number_format($poly_wip,2); ?></td>
											<td align="right"><??></td>
											<td align="right"><??></td>
											<?
											if($l==0)
											{
											?>
											<td align="center" valign="middle" rowspan="<? echo $rowspan_arr[$buyer_name][$job_id][$item_number_id][$color_number_id];?>"><? ?></td>
											<?
											}
											?>
											<td align="right"><? echo $finish_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['finish_today'];?></td>
											<td align="right"><a href="##" onclick="openmypage_finish('<? echo  $job_id; ?>','<? echo $item_number_id; ?>','<? echo $color_number_id; ?>',<? echo $size_number_id; ?>, 'finish_popup',850,350)"><? echo $finish_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['finish_total'];?></a></td>
											<td align="right"><? $finish_wip=$finish_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['finish_total']-$poly_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['poly_total']; echo fn_number_format($finish_wip,2); ?></td>
											<td></td>
											<td></td>
											<td align="right"><? echo $ex_factory_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['ex_factory_today'];?></td>
											<td align="right"><? echo $ex_factory_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['ex_factory_total'];?></td>
											<td align="right"><? $ex_fac_wip=$ex_factory_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['ex_factory_total']-$order_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['order_quantity']; echo fn_number_format($ex_fac_wip,2); ?></td>
											<td align="right"><? $ex_fac_per=($ex_factory_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['ex_factory_total']/$order_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['order_quantity'])*100; echo fn_number_format($ex_fac_per,2); ?></td>

										</tr>
                                      <?
									  $i++;
									  $l++;
									  $job_wise_order_qty+=$order_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['order_quantity'];
									  $job_wise_plan_cut_today+=$plan_cut_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['plan_cutting_today'];
							          $job_wise_plan_cut_total+=$plan_cut_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['plan_cutting_total'];
									  $job_wise_cutting_qc_today+=$cutting_qc_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['cutting_today'];
									  $job_wise_cutting_qc_total+=$cutting_qc_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['cutting_total'];
									  $job_wise_cutting_qc_reject+=$cutting_reject;
									  $job_wise_send_print_today+=$sendprint_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['sendprint_today'];
									  $job_wise_send_print_total+=$sendprint_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['sendprint_total'];
									  $job_wise_rcv_print_today+=$rprint_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['printrcv_today'];
									  $job_wise_rcv_print_total+=$rprint_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['printrcv_total'];
									  $job_wise_rcv_print_reject+=$rprint_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['reject_total'];
									  $job_wise_send_em_today+= $sendem_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['sendem_today'];
									  $job_wise_send_em_total+= $sendem_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['sendem_total'];
									  $job_wise_rcv_em_today+= $sendrcv_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['sendrcv_today'];
									  $job_wise_rcv_em_total+=$sendrcv_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['sendrcv_total'];
									  $job_wise_rcv_em_reject+=$sendrcv_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['reject_total'];
								      $job_wise_sewing_input_today+=$sewinput_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['sewin_today'];
									  $job_wise_sewing_input_total+=$sewinput_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['sewin_total'];
									  $job_wise_sewing_output_today+=$sewoutput_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['sewout_today'];
									  $job_wise_sewing_output_total+=$sewoutput_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['sewout_total'];
									  $job_wise_sewing_output_reject+=$sewoutput_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['reject_total'];
									  $job_wise_hold+=$total_hold_arr[$job_id][$color_number_id][$item_number_id][$size_number_id]['hold_qnty'];
									  $job_wise_iron_today+=$iron_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['iron_today'];
									  $job_wise_iron_total+=$iron_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['iron_total'];
									  $job_wise_iron_reject+=$iron_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['reject_total'];
									  $job_wise_reiron_today+=$reiron_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['reiron_today'];
									  $job_wise_reiron_total+=$reiron_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['reiron_total'];
									  $job_wise_poly_entry_today+=$poly_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['poly_today'];
									  $job_wise_poly_entry_total+=$poly_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['poly_total'];
									  $job_wise_finish_today+=$finish_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['finish_today'];
									  $job_wise_finish_total+=$finish_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['finish_total'];
									  $job_wise_ex_factory_today+=$ex_factory_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['ex_factory_today'];
									  $job_wise_ex_factory_total+=$ex_factory_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['ex_factory_total'];

									//=====================Color Sub Total=======================//
										$color_wise_order_qty+=$order_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['order_quantity'];
										$color_wise_plan_cut_today+=$plan_cut_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['plan_cutting_today'];
										$color_wise_plan_cut_total+=$plan_cut_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['plan_cutting_total'];
										$color_wise_cutting_qc_today+=$cutting_qc_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['cutting_today'];
										$color_wise_cutting_qc_total+=$cutting_qc_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['cutting_total'];
										$color_wise_cutting_qc_reject+=$cutting_reject;
										$color_wise_send_print_today+=$sendprint_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['sendprint_today'];

										$color_wise_send_print_total+=$sendprint_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['sendprint_total'];

										$color_wise_rcv_print_today+=$rprint_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['printrcv_today'];
										$color_wise_rcv_print_total+=$rprint_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['printrcv_total'];
										$color_wise_rcv_print_reject+=$rprint_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['reject_total'];
										$color_wise_send_em_today+= $sendem_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['sendem_today'];

										$color_wise_send_em_total+= $sendem_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['sendem_total'];

										$color_wise_rcv_em_today+= $sendrcv_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['sendrcv_today'];

										$color_wise_rcv_em_total+=$sendrcv_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['sendrcv_total'];
										$color_wise_rcv_em_reject+=$sendrcv_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['reject_total'];
										$color_wise_sewing_input_today+=$sewinput_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['sewin_today'];
										$color_wise_sewing_input_total+=$sewinput_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['sewin_total'];

										$color_wise_sewing_output_today+=$sewoutput_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['sewout_today'];

										$color_wise_sewing_output_total+=$sewoutput_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['sewout_total'];

										$color_wise_sewing_output_reject+=$sewoutput_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['reject_total'];

										$color_wise_hold+=$total_hold_arr[$job_id][$color_number_id][$item_number_id][$size_number_id]['hold_qnty'];

										$color_wise_iron_today+=$iron_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['iron_today'];

										$color_wise_iron_total+=$iron_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['iron_total'];

										$color_wise_iron_reject+=$iron_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['reject_total'];
										$color_wise_reiron_today+=$reiron_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['reiron_today'];
										$color_wise_reiron_total+=$reiron_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['reiron_total'];

										$color_wise_poly_entry_today+=$poly_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['poly_today'];

										$color_wise_poly_entry_total+=$poly_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['poly_total'];
										$color_wise_finish_today+=$finish_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['finish_today'];
										$color_wise_finish_total+=$finish_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['finish_total'];

										$color_wise_ex_factory_today+=$ex_factory_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['ex_factory_today'];

										$color_wise_ex_factory_total+=$ex_factory_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['ex_factory_total'];

									 //==================Buyer Sub Total=======================//
									  $buyer_wise_order_qty+=$order_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['order_quantity'];
									  $buyer_wise_plan_cut_today+=$plan_cut_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['plan_cutting_today'];
							          $buyer_wise_plan_cut_total+=$plan_cut_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['plan_cutting_total'];
									  $buyer_wise_cutting_qc_today+=$cutting_qc_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['cutting_today'];
									  $buyer_wise_cutting_qc_total+=$cutting_qc_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['cutting_total'];
									  $buyer_wise_cutting_qc_reject+=$cutting_reject;
									  $buyer_wise_send_print_today+=$sendprint_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['sendprint_today'];
									  $buyer_wise_send_print_total+=$sendprint_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['sendprint_total'];
									  $buyer_wise_rcv_print_today+=$rprint_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['printrcv_today'];
									  $buyer_wise_rcv_print_total+=$rprint_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['printrcv_total'];
									  $buyer_wise_rcv_print_reject+=$rprint_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['reject_total'];
									  $buyer_wise_send_em_today+= $sendem_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['sendem_today'];
									  $buyer_wise_send_em_total+= $sendem_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['sendem_total'];
									  $buyer_wise_rcv_em_today+= $sendrcv_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['sendrcv_today'];
									  $buyer_wise_rcv_em_total+=$sendrcv_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['sendrcv_total'];
									  $buyer_wise_rcv_em_reject+=$sendrcv_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['reject_total'];
								      $buyer_wise_sewing_input_today+=$sewinput_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['sewin_today'];
									  $buyer_wise_sewing_input_total+=$sewinput_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['sewin_total'];
									  $buyer_wise_sewing_output_today+=$sewoutput_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['sewout_today'];
									  $buyer_wise_sewing_output_total+=$sewoutput_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['sewout_total'];
									  $buyer_wise_sewing_output_reject+=$sewoutput_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['reject_total'];
									  $buyer_wise_hold+=$total_hold_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['hold_qnty'];
									  $buyer_wise_iron_today+=$iron_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['iron_today'];
									  $buyer_wise_iron_total+=$iron_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['iron_total'];
									  $buyer_wise_iron_reject+=$iron_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['reject_total'];
									  $buyer_wise_reiron_today+=$reiron_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['reiron_today'];
									  $buyer_wise_reiron_total+=$reiron_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['reiron_total'];
									  $buyer_wise_poly_entry_today+=$poly_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['poly_today'];
									  $buyer_wise_poly_entry_total+=$poly_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['poly_total'];
									  $buyer_wise_finish_today+=$finish_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['finish_today'];
									  $buyer_wise_finish_total+=$finish_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['finish_total'];
									  $buyer_wise_ex_factory_today+=$ex_factory_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['ex_factory_today'];
									  $buyer_wise_ex_factory_total+=$ex_factory_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['ex_factory_total'];

									//   =======================Grand Total=========================//
									  $gr_wise_order_qty+=$order_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['order_quantity'];
									  $gr_wise_plan_cut_today+=$plan_cut_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['plan_cutting_today'];
							          $gr_wise_plan_cut_total+=$plan_cut_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['plan_cutting_total'];
									  $gr_wise_cutting_qc_today+=$cutting_qc_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['cutting_today'];
									  $gr_wise_cutting_qc_total+=$cutting_qc_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['cutting_total'];
									  $gr_wise_cutting_qc_reject+=$cutting_reject;
									  $gr_wise_send_print_today+=$sendprint_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['sendprint_today'];
									  $gr_wise_send_print_total+=$sendprint_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['sendprint_total'];
									  $gr_wise_rcv_print_today+=$rprint_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['printrcv_today'];
									  $gr_wise_rcv_print_total+=$rprint_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['printrcv_total'];
									  $gr_wise_rcv_print_reject+=$rprint_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['reject_total'];
									  $gr_wise_send_em_today+= $sendem_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['sendem_today'];
									  $gr_wise_send_em_total+= $sendem_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['sendem_total'];
									  $gr_wise_rcv_em_today+= $sendrcv_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['sendrcv_today'];
									  $gr_wise_rcv_em_total+=$sendrcv_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['sendrcv_total'];
									  $gr_wise_rcv_em_reject+=$sendrcv_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['reject_total'];
								      $gr_wise_sewing_input_today+=$sewinput_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['sewin_today'];
									  $gr_wise_sewing_input_total+=$sewinput_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['sewin_total'];
									  $gr_wise_sewing_output_today+=$sewoutput_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['sewout_today'];
									  $gr_wise_sewing_output_total+=$sewoutput_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['sewout_total'];
									  $gr_wise_sewing_output_reject+=$sewoutput_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['reject_total'];
									  $gr_wise_hold+=$total_hold_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['hold_qnty'];
									  $gr_wise_iron_today+=$iron_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['iron_today'];
									  $gr_wise_iron_total+=$iron_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['iron_total'];
									  $gr_wise_iron_reject+=$iron_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['reject_total'];
									  $gr_wise_reiron_today+=$reiron_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['reiron_today'];
									  $gr_wise_reiron_total+=$reiron_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['reiron_total'];
									  $gr_wise_poly_entry_today+=$poly_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['poly_today'];
									  $gr_wise_poly_entry_total+=$poly_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['poly_total'];
									  $gr_wise_finish_today+=$finish_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['finish_today'];
									  $gr_wise_finish_total+=$finish_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['finish_total'];
									  $gr_wise_ex_factory_today+=$ex_factory_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['ex_factory_today'];
									  $gr_wise_ex_factory_total+=$ex_factory_arr[$job_id][$item_number_id][$color_number_id][$size_number_id]['ex_factory_total'];

									}
									?>
								   <tr style="text-align: right;font-weight:bold;background:#cddcdc">
									<td></td>
									<td></td>
									<td></td>
									<td></td>
									<td></td>
									<td></td>
									<td> Color Total:</td>
									<td align="right"><? echo number_format($color_wise_order_qty,2);?></td>
									<td ></td>
									<td align="right"><? echo number_format($color_wise_plan_cut_today,2);?></td>
									<td align="right"><? echo number_format($color_wise_plan_cut_total,2);?></td>
									<td></td>
									<td></td>
									<td align="right"><? echo number_format($color_wise_cutting_qc_today,2);?></td>
									<td align="right"><? echo number_format($color_wise_cutting_qc_total,2);?></td>
									<td align="right"><? echo number_format($color_wise_cutting_qc_reject,2)?></td>
									<td align="right"><? echo number_format($color_wise_send_print_today,2);?></td>
									<td align="right"><? echo number_format($color_wise_send_print_total,2);?></td>
									<td></td>
									<td align="right"><? echo number_format($color_wise_rcv_print_today,2); ?></td>
									<td align="right"><? echo number_format($color_wise_rcv_print_total,2); ?></td>
									<td align="right"><? echo number_format($color_wise_rcv_print_reject,2);?></td>
									<td align="right"></td>
									<td align="right"><? echo number_format($color_wise_send_em_today,2); ?></td>
									<td align="right"><? echo number_format($color_wise_send_em_total,2);?></td>
									<td></td>
									<td align="right"><? echo number_format($color_wise_rcv_em_today,2)?></td>
									<td align="right"><?  echo number_format($color_wise_rcv_em_total,2)?></td>
									<td align="right"><? echo number_format($color_wise_rcv_em_reject,2)?></td>
									<td></td>
									<td align="right"><? echo number_format($color_wise_sewing_input_today,2);?></td>
									<td align="right"><? echo number_format($color_wise_sewing_input_total,2);?></td>
									<td></td>
									<td align="right"><? echo number_format($color_wise_sewing_output_today,2);?></td>
									<td align="right"><? echo number_format($color_wise_sewing_output_total,2);?></td>
									<td align="right"><? echo number_format($color_wise_sewing_output_reject,2);?></td>
									<td></td>
									<td align="right"></td>
									<td align="right"><? echo number_format($color_wise_hold,2);?></td>
									<td align="right"><? echo number_format($color_wise_iron_today,2);?></td>
									<td align="right"><? echo number_format($color_wise_iron_total,2);?></td>
									<td align="right"><? echo number_format($color_wise_iron_reject,2);?></td>
									<td></td>
									<td align="right"><? echo number_format($color_wise_reiron_today,2);?></td>
									<td align="right"><? echo number_format($color_wise_reiron_total,2);?></td>
									<td align="right"><? echo number_format($color_wise_poly_entry_today,2);?></td>
									<td align="right"><? echo number_format($color_wise_poly_entry_total,2);?></td>
									<td></td>
									<td></td>
									<td></td>
									<td></td>
									<td align="right"><? echo number_format($color_wise_finish_today,2);?></td>
									<td align="right"><? echo number_format($color_wise_finish_total,2);?></td>
									<td></td>
									<td></td>
									<td></td>
									<td align="right"><? echo number_format($color_wise_ex_factory_today,2);?></td>
									<td align="right"><? echo number_format($color_wise_ex_factory_total,2);?></td>
									<td></td>
									<td></td>
									<td></td>
									<td></td>
									<td></td>
							       </tr>

                                <?
								}
							}
							?>
							<tr style="text-align: right;font-weight:bold;background:#cddcdc">
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td> Job Total:</td>
								<td align="right"><? echo number_format($job_wise_order_qty,2);?></td>
								<td ></td>
								<td align="right"><? echo number_format($job_wise_plan_cut_today,2);?></td>
								<td align="right"><? echo number_format($job_wise_plan_cut_total,2);?></td>
								<td></td>
								<td></td>
								<td align="right"><? echo number_format($job_wise_cutting_qc_today,2);?></td>
								<td align="right"><? echo number_format($job_wise_cutting_qc_total,2);?></td>
								<td align="right"><? echo number_format($job_wise_cutting_qc_reject,2)?></td>
								<td align="right"><? echo number_format($job_wise_send_print_today,2);?></td>
								<td align="right"><? echo number_format($job_wise_send_print_total,2);?></td>
								<td></td>
								<td align="right"><? echo number_format($job_wise_rcv_print_today,2); ?></td>
								<td align="right"><? echo number_format($job_wise_rcv_print_total,2); ?></td>
								<td align="right"><? echo number_format($job_wise_rcv_print_reject,2);?></td>
								<td align="right"></td>
								<td align="right"><? echo number_format($job_wise_send_em_today,2); ?></td>
								<td align="right"><? echo number_format($job_wise_send_em_total,2);?></td>
								<td></td>
								<td align="right"><? echo number_format($job_wise_rcv_em_today,2)?></td>
								<td align="right"><?  echo number_format($job_wise_rcv_em_total,2)?></td>
								<td align="right"><? echo number_format($job_wise_rcv_em_reject,2)?></td>
								<td></td>
								<td align="right"><? echo number_format($job_wise_sewing_input_today,2);?></td>
								<td align="right"><? echo number_format($job_wise_sewing_input_total,2);?></td>
								<td></td>
								<td align="right"><? echo number_format($job_wise_sewing_output_today,2);?></td>
								<td align="right"><? echo number_format($job_wise_sewing_output_total,2);?></td>
								<td align="right"><? echo number_format($job_wise_sewing_output_reject,2);?></td>
								<td></td>
								<td align="right"></td>
								<td align="right"><? echo number_format($job_wise_hold,2);?></td>
								<td align="right"><? echo number_format($job_wise_iron_today,2);?></td>
								<td align="right"><? echo number_format($job_wise_iron_total,2);?></td>
								<td align="right"><? echo number_format($job_wise_iron_reject,2);?></td>
								<td></td>
								<td align="right"><? echo number_format($job_wise_reiron_today,2);?></td>
								<td align="right"><? echo number_format($job_wise_reiron_total,2);?></td>
								<td align="right"><? echo number_format($job_wise_poly_entry_today,2);?></td>
								<td align="right"><? echo number_format($job_wise_poly_entry_total,2);?></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td align="right"><? echo number_format($job_wise_finish_today,2);?></td>
								<td align="right"><? echo number_format($job_wise_finish_total,2);?></td>
								<td></td>
								<td></td>
								<td></td>
								<td align="right"><? echo number_format($job_wise_ex_factory_today,2);?></td>
								<td align="right"><? echo number_format($job_wise_ex_factory_total,2);?></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>

							</tr>
                         <?
						}
					 
					 ?>
					          <tr style="text-align: right;font-weight:bold;background:#dccddc;">
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td> Buyer Total:</td>
								<td align="right"><? echo number_format($buyer_wise_order_qty,2);?></td>
								<td ></td>
								<td align="right"><? echo number_format($buyer_wise_plan_cut_today,2);?></td>
								<td align="right"><? echo number_format($buyer_wise_plan_cut_total,2);?></td>
								<td></td>
								<td></td>
								<td align="right"><? echo number_format($buyer_wise_cutting_qc_today,2);?></td>
								<td align="right"><? echo number_format($buyer_wise_cutting_qc_total,2);?></td>
								<td align="right"><? echo number_format($buyer_wise_cutting_qc_reject,2)?></td>
								<td align="right"><? echo number_format($buyer_wise_send_print_today,2);?></td>
								<td align="right"><? echo number_format($buyer_wise_send_print_total,2);?></td>
								<td></td>
								<td align="right"><? echo number_format($buyer_wise_rcv_print_today,2); ?></td>
								<td align="right"><? echo number_format($buyer_wise_rcv_print_total,2); ?></td>
								<td align="right"><? echo number_format($buyer_wise_rcv_print_reject,2);?></td>
								<td align="right"></td>
								<td align="right"><? echo number_format($buyer_wise_send_em_today,2); ?></td>
								<td align="right"><? echo number_format($buyer_wise_send_em_total,2);?></td>
								<td></td>
								<td align="right"><? echo number_format($buyer_wise_rcv_em_today,2)?></td>
								<td align="right"><?  echo number_format($buyer_wise_rcv_em_total,2)?></td>
								<td align="right"><? echo number_format($buyer_wise_rcv_em_reject,2)?></td>
								<td></td>
								<td align="right"><? echo number_format($buyer_wise_sewing_input_today,2);?></td>
								<td align="right"><? echo number_format($buyer_wise_sewing_input_total,2);?></td>
								<td></td>
								<td align="right"><? echo number_format($buyer_wise_sewing_output_today,2);?></td>
								<td align="right"><? echo number_format($buyer_wise_sewing_output_total,2);?></td>
								<td align="right"><? echo number_format($buyer_wise_sewing_output_reject,2);?></td>
								<td></td>
								<td align="right"></td>
								<td align="right"><? echo number_format($buyer_wise_hold,2);?></td>
								<td align="right"><? echo number_format($buyer_wise_iron_today,2);?></td>
								<td align="right"><? echo number_format($buyer_wise_iron_total,2);?></td>
								<td align="right"><? echo number_format($buyer_wise_iron_reject,2);?></td>
								<td></td>
								<td align="right"><? echo number_format($buyer_wise_reiron_today,2);?></td>
								<td align="right"><? echo number_format($buyer_wise_reiron_total,2);?></td>
								<td align="right"><? echo number_format($buyer_wise_poly_entry_today,2);?></td>
								<td align="right"><? echo number_format($buyer_wise_poly_entry_total,2);?></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td align="right"><? echo number_format($buyer_wise_finish_today,2);?></td>
								<td align="right"><? echo number_format($buyer_wise_finish_total,2);?></td>
								<td></td>
								<td></td>
								<td></td>
								<td align="right"><? echo number_format($buyer_wise_ex_factory_today,2);?></td>
								<td align="right"><? echo number_format($buyer_wise_ex_factory_total,2);?></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>

							</tr>
					<?
				  }
				?>
			</tbody>
			<tfoot>
				<tr>
					<th width="50"></th>
					<th width="100"></th>
					<th width="100"></th>
					<th width="100"></th>
					<th width="100"></th>
					<th width="100"></th>
					<th width="100">Grand Total:</th>
					<th width="100"><? echo number_format($gr_wise_order_qty,2);?></th>
					<th width="100"></th>
					<th width="100"><? echo number_format($gr_wise_plan_cut_today,2);?></th>
					<th width="100"><? echo number_format($gr_wise_plan_cut_total,2);?></th>
					<th width="100"></th>
					<th width="100"></th>
					<th width="100"><? echo number_format($gr_wise_cutting_qc_today,2);?></th>
					<th width="100"><? echo number_format($gr_wise_cutting_qc_total,2);?></th>
					<th width="100"><? echo number_format($gr_wise_cutting_qc_reject,2);?></th>
					<th width="100"><? echo number_format($gr_wise_send_print_today,2);?></th>
					<th width="100"><? echo number_format($gr_wise_send_print_total,2);?></th>
					<th width="100"></th>
					<th width="100"><? echo number_format($gr_wise_rcv_print_today,2);?></th>
					<th width="100"><? echo number_format($gr_wise_rcv_print_total,2);?></th>
					<th width="100"><? echo number_format($gr_wise_rcv_print_reject,2);?></th>
					<th width="100"></th>
					<th width="100"><? echo number_format($gr_wise_send_em_today,2);?></th>
					<th width="100"><? echo number_format($gr_wise_send_em_total,2);?></th>
					<th width="100"></th>
					<th width="100"><? echo number_format($gr_wise_rcv_em_today,2);?></th>
					<th width="100"><? echo number_format($gr_wise_rcv_em_total,2);?></th>
					<th width="100"><? echo number_format($gr_wise_rcv_em_reject,2);?></th>
					<th width="100"></th>
					<th width="100"><? echo number_format($gr_wise_sewing_input_today,2);?></th>
					<th width="100"><? echo number_format($gr_wise_sewing_input_total,2);?></th>
					<th width="100"></th>
					<th width="100"><? echo number_format($gr_wise_sewing_output_today,2);?></th>
					<th width="100"><? echo number_format($gr_wise_sewing_output_total,2);?></th>
					<th width="100"><? echo number_format($gr_wise_sewing_output_reject,2);?></th>
					<th width="100"></th>
					<th width="100"></th>
					<th width="100"><a href="##" onclick="openmypage_holdqty('<? echo  $job_id; ?>','grand_hold_popup',850,350)"><? echo number_format($gr_wise_hold,2);?></a></th>
					<th width="100"><? echo number_format($gr_wise_iron_today,2);?></th>
					<th width="100"><? echo number_format($gr_wise_iron_total,2);?></th>
					<th width="100"><? echo number_format($gr_wise_iron_reject,2);?></th>
					<th width="100"></th>
					<th width="100"><? echo number_format($gr_wise_reiron_today,2);?></th>
					<th width="100"><? echo number_format($gr_wise_reiron_total,2);?></th>
					<th width="100"><? echo number_format($gr_wise_poly_entry_today,2);?></th>
					<th width="100"><? echo number_format($gr_wise_poly_entry_total,2);?></th>
					<th width="100"></th>
					<th width="100"></th>
					<th width="100"></th>
					<th width="100"></th>
					<th width="100"><? echo number_format($gr_wise_finish_today,2);?></th>
					<th width="100"><? echo number_format($gr_wise_finish_total,2);?></th>
					<th width="100"></th>
					<th width="100"></th>
					<th width="100"></th>
					<th width="100"><? echo number_format($gr_wise_ex_factory_today,2);?></th>
					<th width="100"><? echo number_format($gr_wise_ex_factory_total,2);?></th>
					<th width="100"></th>
					<th width="100"></th>

				</tr>
			</tfoot>
		 </table>

		</div>

	   </fieldset>









     <?
    }

}

?>