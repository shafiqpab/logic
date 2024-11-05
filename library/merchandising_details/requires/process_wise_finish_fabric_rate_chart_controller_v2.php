<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

$permission=$_SESSION['page_permission'];

if($action=="show_details_part")
{
	$data = explode("_", $data);
	$company_id = $data[0];
	$cbo_main_process = $data[1];
	if($cbo_main_process==1)
	{
		?>
		<legend>Finish Fabric Rate Chart Details Part</legend>
		<table width="100%" border="0" cellpadding="0" cellspacing="2" align="left" class="rtp_table">
			<thead>
				<tr align="left">
					<th>Fabric type</th>
					<th>Count Range</th>
					<th>Rate Popup</th>
					<th>Status</th>
				</tr>
			</thead>
			<tbody>
				<tr align="left">
					<td>
						<? echo create_drop_down( "cbo_fabric_type", 150, "select id, fabric_construction_name from lib_fabric_construction where status_active =1 and is_deleted=0  order by fabric_construction_name","id,fabric_construction_name", 1, "-- Select --", $selected, "" ); ?>
					</td>
					<td>
						<!-- <input type="text" id="txt_count_range_from"  name="txt_count_range_from" class="text_boxes" style="width:52px" value="" placeholder="From"/>&nbsp;<input type="text" id="txt_count_range_to"  name="txt_count_range_to" class="text_boxes" style="width:52px" value=""  placeholder="To"/> -->
						<?
							echo create_drop_down( "cbo_count_range_from", 100, "select id, yarn_count from lib_yarn_count where status_active =1 and is_deleted=0  order by yarn_count","id,yarn_count", 1, "-- Select --", $selected, "" );
							echo create_drop_down( "cbo_count_range_to", 100, "select id, yarn_count from lib_yarn_count where status_active =1 and is_deleted=0  order by yarn_count","id,yarn_count", 1, "-- Select --", $selected, "" );
						?>
					</td>
					<td>
						<input type="text" id="txt_rate_popup"  name="txt_rate_popup"  class="text_boxes" style="width:120px" value="" readonly placeholder="Browse" onDblClick="openmypage_rate(document.getElementById('cbo_main_process').value);" />
					</td>
					<td>
						<? echo create_drop_down( "cbo_status", 130, $row_status, "", "", "-- Select --", "1", "","","" );?>
					</td>
				</tr>
			</tbody>
		</table>
		<?
	}
	else if ($cbo_main_process==30)
	{
		?>
		<legend>Finish Fabric Rate Chart Details Part</legend>
		<table width="100%" border="0" cellpadding="0" cellspacing="2" align="left" class="rtp_table">
			<thead>
				<tr align="left">
					<th>Yarn Dyeing Part</th>
					<th>Color Range</th>
					<th>Rate</th>
					<th>Status</th>
				</tr>
			</thead>
			<tbody>
				<tr align="left">
					<td>
						<?
							echo create_drop_down("cbo_yarn_dyeing_part", 130, $yarn_dyeing_part_arr, "", 1, "-- Select --", 0, "");
						?>
					</td>
					<td>
						<?
						echo create_drop_down("cbo_yarn_color_range", 100, $color_range, "", 1, "-- Select --", 0, "", "0", "", "", "", "", "", "", "");
						?>
					</td>
					<td>
						<input type="text" id="txt_rate_popup"  name="txt_rate_popup"  class="text_boxes" style="width:120px" value="" readonly placeholder="Browse" onDblClick="openmypage_rate(document.getElementById('cbo_main_process').value);" />
					</td>
					<td>
						<? echo create_drop_down( "cbo_status", 130, $row_status, "", "", "-- Select --", "1", "","","" );?>
					</td>
				</tr>
			</tbody>
		</table>
		<?
	}
	else if($cbo_main_process==31)
	{
		?>
		<legend>Finish Fabric Rate Chart Details Part</legend>
		<table width="100%" border="0" cellpadding="0" cellspacing="2" align="left" class="rtp_table">
		<thead>
			<tr align="left">
				<th>Dyeing Part</th>
				<th>Width/Dia type</th>
				<th>Color Range</th>
				<th>Dyeing Upto</th>
				<th>Rate</th>
				<th>Status</th>
			</tr>
		</thead>
		<tbody>
			<tr align="left">
				<td>
					<? 
					$dyeing_part_arr = array();
					//$dyeing_part_arr = array(1=>'Cotton Part Dyeing',2=>'Double Part Dyeing',3=>'Polyester Part Dyeing',4=>'Viscose Dyeing');
					//echo create_drop_down( "cbo_dyeing_part", 130,  $blank_array,"", "", "-- Select --", '', "",$disabled,"" ); 
					echo create_drop_down("cbo_dyeing_part", 130, $fabric_dyeing_part_arr, "", 1, "-- Select --", 0, "");
					?>
				</td>
				<td>
					<?
					echo create_drop_down("cbo_diawidthtype", 100, $fabric_typee, "", 1, "-- Select --", 0, "", "", "", "", "", "", "", "", "");
					?>
				</td>
				<td>
					<?
						echo create_drop_down("cbo_color_range", 100, $color_range, "", 1, "-- Select --", 0, "", "0", "", "", "", "", "", "", "");
					?>
				</td>
				<td>
					<?
						echo create_drop_down("cbo_dyeing_upto", 100, $dyeing_sub_process, "", 1, "-- Select --", 0, "", "0", "", "", "", "", "", "", "");
					?>
				</td>
				<td>
					<input type="text" id="txt_rate_popup"  name="txt_rate_popup"  class="text_boxes" style="width:120px" value="" readonly placeholder="Browse" onDblClick="openmypage_rate(document.getElementById('cbo_main_process').value);" />
				</td>
				<td>
					<? echo create_drop_down( "cbo_status", 130, $row_status, "", "", "-- Select --", "1", "","","" );?>
				</td>
			</tr>
		</tbody>
		</table>
		<?

	}
	else if ($cbo_main_process==35)
	{
		?>
		<legend>Finish Fabric Rate Chart Details Part</legend>
		<table width="100%" border="0" cellpadding="0" cellspacing="2" align="left" class="rtp_table">
			<thead>
				<tr align="left">
					<th>AOP Type</th>
					<th>No. Of Color</th>
					<th>Coverage %</th>
					<th>AOP Upto</th>
					<th>Rate</th>
					<th>Status</th>
				</tr>
			</thead>
			<tbody>
				<tr align="left">
					<td>
						<?
						echo create_drop_down( "cbo_aop_type", 130,  $print_type,"", "", "-- Select --", '', "","","" ); 
						?>
					</td>
					<td>
						<? 
						$no_color_arr=array(1=>"1 - 3",2=>"1 - 5",3=>"4 - 6",4=>"7 - 12",5=>"7 - 9",6=>"10 - 12",7=>"70% - 100% Light Color",8=>"50% - 69% Light Color",9=>"25%-49% Light Color",9=>"0.1% - 24% Light Color",10=>"0.1% - 24% Deep Color",11=>"25% - 49% Deep Color",12=>"50% - 69% Deep Color",13=>"70% - 100% Deep Color",14=>"25%-49% light Color ");
						echo create_drop_down( "cbo_no_color", 130, $no_color_arr,"", 1, "-- Select --", '', "","","" );
						?>
					</td>
					<td>
						<input type="text" id="txt_coverage_from"  name="txt_coverage_from" class="text_boxes" style="width:52px" value="" placeholder="From"/>&nbsp;
						<input type="text" id="txt_coverage_to"  name="txt_coverage_to" class="text_boxes" style="width:52px" value=""  placeholder="To"/>
					</td>
					<td>
						<?
							echo create_drop_down("cbo_aop_upto", 100, $dyeing_sub_process, "", 1, "-- Select --", 0, "", "0", "", "", "", "", "", "", "");
						?>
					</td>
					<td>
						<input type="text" id="txt_rate_popup"  name="txt_rate_popup"  class="text_boxes" style="width:120px" value="" readonly placeholder="Browse" onDblClick="openmypage_rate(document.getElementById('cbo_main_process').value);" />
					</td>
					<td>
						<? echo create_drop_down( "cbo_status", 130, $row_status, "", "", "-- Select --", "1", "","","" );?>
					</td>
				</tr>
			</tbody>
		</table>
		<?
	}
	else if ($cbo_main_process ==1000)
	{
		?>
		<legend>Finish Fabric Rate Chart Details Part</legend>
		<table width="100%" border="0" cellpadding="0" cellspacing="2" align="left" class="rtp_table">
			<thead>
				<tr align="left">
					<th>Additional Process</th>
					<th>Rate</th>
					<th>Status</th>
				</tr>
			</thead>
			<tbody>
				<tr align="left">
					<td>
						<?
							echo create_drop_down("cbo_additional_process", 130, $additional_part_arr, "", 1, "-- Select --", 0, "");
						?>
					</td>
					
					<td>
						<input type="text" id="txt_rate_popup"  name="txt_rate_popup"  class="text_boxes" style="width:120px" value="" readonly placeholder="Browse" onDblClick="openmypage_rate(document.getElementById('cbo_main_process').value);" />
					</td>
					<td>
						<? echo create_drop_down( "cbo_status", 130, $row_status, "", "", "-- Select --", "1", "","","" );?>
					</td>
				</tr>
			</tbody>
		</table>
		<?
	}
}

if($action=="search_list_view")
{
	/* $product_arr = return_library_array("select id, product_name_details from product_details_master where item_category_id=2","id","product_name_details");
	$store_arr=return_library_array( "select id, store_name from lib_store_location",'id','store_name');
	
	$batch_arr = return_library_array("select id, batch_no from pro_batch_create_mst","id","batch_no"); */

	
	$data = explode("_",$data);
	$company_id = $data[0];
	$cbo_main_process = $data[1];

	$sql="SELECT a.company_id, a.main_process, b.id as dtls_id, b.* from lib_prcs_finfab_rt_chrt_mst a, lib_prcs_finfab_rt_chrt_dtls b where a.id = b.mst_id and b.status_active=1 and b.is_deleted=0 and a.company_id=$company_id and a.main_process=$cbo_main_process";
    //echo $sql ;
	?>
	<div>
		<fieldset>
			<legend>Fabric Details List</legend>

			<?
			if($cbo_main_process ==1)
			{
				$contruction_arr=return_library_array( "select id, fabric_construction_name from lib_fabric_construction",'id','fabric_construction_name');
				$y_count_arr=return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');
				?>
				<table cellspacing="0" cellpadding="0" border="1" rules="all" width="500" class="rpt_table" align="center">
					<thead>
						<th width="50">SL</th>
						<th width="200">Construction</th>
						<th width="150">Count Range</th>
						<th width="100">Status</th>
					</thead>
				</table>
				<div style="width:520px; overflow-y:scroll; max-height:210px;" id="buyer_list_view" align="center">
					<table cellspacing="0" cellpadding="0" border="1" rules="all" width="500" class="rpt_table" id="list_view" align="center" >
						<?
						$i=1;
						$nameArray=sql_select( $sql );
						foreach ($nameArray as $row)
						{
							if ($i%2==0)
								$bgcolor="#E9F3FF";
							else
								$bgcolor="#FFFFFF";

							?>
							<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="set_form_data('<? echo $row[csf('dtls_id')]."**". $cbo_main_process; ?>')">
								<td width="50" align="center"><? echo $i; ?></td>
								<td width="200" align="center"><p><? echo $contruction_arr[$row[csf('knit_fabric_type')]]; ?></p></td>
								<td width="150" align="center"><p><? echo $y_count_arr[$row[csf('knit_from_count')]]." - ".$y_count_arr[$row[csf('knit_to_count')]]; ?></p></td>
								<td width="100"><p><? echo $row_status[$row[csf('status')]]; ?>&nbsp;</p></td>
							</tr>
							<?
							$i++;
						}
						?>
					</table>
				</div>
				<?
			}
			if($cbo_main_process ==30)
			{
				?>
				<table cellspacing="0" cellpadding="0" border="1" rules="all" width="500" class="rpt_table" align="center">
					<thead>
						<th width="50">SL</th>
						<th width="200">Yarn Dyeing Part</th>
						<th width="150">Color Range</th>
						<th width="100">Status</th>
					</thead>
				</table>
				<div style="width:520px; overflow-y:scroll; max-height:210px;" id="buyer_list_view" align="center">
					<table cellspacing="0" cellpadding="0" border="1" rules="all" width="500" class="rpt_table" id="list_view" align="center" >
						<?
						$i=1;
						$nameArray=sql_select( $sql );
						foreach ($nameArray as $row)
						{
							if ($i%2==0)
								$bgcolor="#E9F3FF";
							else
								$bgcolor="#FFFFFF";

							?>
							<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="set_form_data('<? echo $row[csf('dtls_id')]."**". $cbo_main_process; ?>')">
								<td width="50" align="center"><? echo $i; ?></td>
								<td width="200" align="center"><p><? echo $yarn_dyeing_part_arr[$row[csf('yarn_dyeing_part')]]; ?></p></td>
								<td width="150" align="center"><p><? echo $color_range[$row[csf('yarn_color_range')]]; ?></p></td>
								<td width="100"><p><? echo $row_status[$row[csf('status')]]; ?>&nbsp;</p></td>
							</tr>
							<?
							$i++;
						}
						?>
					</table>
				</div>
				<?
			}
			if($cbo_main_process ==31)
			{
				?>
				<table cellspacing="0" cellpadding="0" border="1" rules="all" width="650" class="rpt_table" align="center">
					<thead>
						<th width="50">SL</th>
						<th width="100">Dyeing Part</th>
						<th width="100">Width/Dia type</th>
						<th width="150">Color Range</th>
						<th width="150">Dyeing Upto</th>
						<th width="100">Status</th>
					</thead>
				</table>
				<div style="width:670px; overflow-y:scroll; max-height:210px;" id="buyer_list_view" align="center">
					<table cellspacing="0" cellpadding="0" border="1" rules="all" width="650" class="rpt_table" id="list_view" align="center" >
						<?
						$i=1;
						$nameArray=sql_select( $sql );
						foreach ($nameArray as $row)
						{
							if ($i%2==0)
								$bgcolor="#E9F3FF";
							else
								$bgcolor="#FFFFFF";

							?>
							<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="set_form_data('<? echo $row[csf('dtls_id')]."**". $cbo_main_process; ?>')">
								<td width="50" align="center"><? echo $i; ?></td>
								<td width="100" align="center"><p><? echo $fabric_dyeing_part_arr[$row[csf('dyeing_part')]]; ?></p></td>
								<td width="100" align="center"><p><? echo $fabric_typee[$row[csf('dia_width_type')]]; ?></p></td>
								<td width="150" align="center"><p><? echo $color_range[$row[csf('dyeing_color_range')]]; ?></p></td>
								<td width="150" align="center"><p><? echo $dyeing_sub_process[$row[csf('DYEING_UPTO')]]; ?></p></td>
								<td width="100"><p><? echo $row_status[$row[csf('status')]]; ?>&nbsp;</p></td>
							</tr>
							<?
							$i++;
						}
						?>
					</table>
				</div>
				<?
			}
			if($cbo_main_process ==35)
			{
				?>
				<table cellspacing="0" cellpadding="0" border="1" rules="all" width="650" class="rpt_table" align="center">
					<thead>
						<th width="50">SL</th>
						<th width="100">AOP Type</th>
						<th width="100">No. Of Color</th>
						<th width="150">Coverage %</th>
						<th width="150">AOP Upto</th>
						<th width="100">Status</th>
					</thead>
				</table>
				<div style="width:670px; overflow-y:scroll; max-height:210px;" id="buyer_list_view" align="center">
					<table cellspacing="0" cellpadding="0" border="1" rules="all" width="650" class="rpt_table" id="list_view" align="center" >
						<?
						$i=1;
						$nameArray=sql_select( $sql );
						foreach ($nameArray as $row)
						{
							if ($i%2==0)
								$bgcolor="#E9F3FF";
							else
								$bgcolor="#FFFFFF";

								$no_color_arr=array(1=>"1 - 3",2=>"1 - 5",3=>"4 - 6",4=>"7 - 12",5=>"7 - 9",6=>"10 - 12",7=>"70% - 100% Light Color",8=>"50% - 69% Light Color",9=>"25%-49% Light Color",9=>"0.1% - 24% Light Color",10=>"0.1% - 24% Deep Color",11=>"25% - 49% Deep Color",12=>"50% - 69% Deep Color",13=>"70% - 100% Deep Color",14=>"25%-49% light Color ");
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="set_form_data('<? echo $row[csf('dtls_id')]."**". $cbo_main_process; ?>')">
								<td width="50" align="center"><? echo $i; ?></td>
								<td width="100" align="center"><p><? echo $print_type[$row[csf('aop_type')]]; ?></p></td>
								<td width="100" align="center"><p><? echo $no_color_arr[$row[csf('aop_no_of_color')]]; ?></p></td>
								<td width="150" align="center"><p><? echo "(".$row[csf('aop_coverage_from')]."-".$row[csf('aop_coverage_to')].")"; ?></p></td>
								<td width="150" align="center"><p><? echo $dyeing_sub_process[$row[csf('AOP_UPTO')]]; ?></p></td>
								<td width="100"><p><? echo $row_status[$row[csf('status')]]; ?>&nbsp;</p></td>
							</tr>
							<?
							$i++;
						}
						?>
					</table>
				</div>
				<?
			}
			if($cbo_main_process ==1000)
			{
				?>
				<table cellspacing="0" cellpadding="0" border="1" rules="all" width="350" class="rpt_table" align="center">
					<thead>
						<th width="50">SL</th>
						<th width="200">Additional Process</th>
						<th width="100">Status</th>
					</thead>
				</table>
				<div style="width:370px; overflow-y:scroll; max-height:210px;" id="buyer_list_view" align="center">
					<table cellspacing="0" cellpadding="0" border="1" rules="all" width="350" class="rpt_table" id="list_view" align="center" >
						<?
						$i=1;
						$nameArray=sql_select( $sql );
						foreach ($nameArray as $row)
						{
							if ($i%2==0)
								$bgcolor="#E9F3FF";
							else
								$bgcolor="#FFFFFF";

							?>
							<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="set_form_data('<? echo $row[csf('dtls_id')]."**". $cbo_main_process; ?>')">
								<td width="50" align="center"><? echo $i; ?></td>
								<td width="200" align="center"><p><? echo $additional_part_arr[$row[csf('additional_process')]]; ?></p></td>
								<td width="100"><p><? echo $row_status[$row[csf('status')]]; ?>&nbsp;</p></td>
							</tr>
							<?
							$i++;
						}
						?>
					</table>
				</div>
				<?
			}
			?>
		</fieldset>
	</div>
	<?
	exit();
}

if ($action=="load_php_data_to_form")
{
	$data = explode("_",$data);
	$dtls_id = $data[0];
	$main_process_id = $data[1];

	$nameArray=sql_select("SELECT a.company_id, a.main_process,b.id as dtls_id, b.* from lib_prcs_finfab_rt_chrt_mst a, lib_prcs_finfab_rt_chrt_dtls b where a.id = b.mst_id and b.status_active=1 and b.is_deleted=0 and b.id='$dtls_id'");


	foreach ($nameArray as $inf)
	{
		$main_process=$inf[csf("main_process")];

		if($main_process ==1)
		{
			echo "document.getElementById('cbo_fabric_type').value  	= '".($inf[csf("knit_fabric_type")])."';\n";
			echo "document.getElementById('cbo_count_range_from').value  		= '".($inf[csf("knit_from_count")])."';\n";
			echo "document.getElementById('cbo_count_range_to').value  		= '".($inf[csf("knit_to_count")])."';\n";
			echo "document.getElementById('cbo_status').value = '".($inf[csf("status")])."';\n";
			echo "document.getElementById('update_id').value = '".($inf[csf("dtls_id")])."';\n";
		}
		else if($main_process ==30)
		{
			echo "document.getElementById('cbo_yarn_dyeing_part').value  	= '".($inf[csf("yarn_dyeing_part")])."';\n";
			echo "document.getElementById('cbo_yarn_color_range').value  		= '".($inf[csf("yarn_color_range")])."';\n";
			echo "document.getElementById('cbo_status').value = '".($inf[csf("status")])."';\n";
			echo "document.getElementById('update_id').value = '".($inf[csf("dtls_id")])."';\n";
		}
		else if($main_process ==31)
		{
			echo "document.getElementById('cbo_dyeing_part').value  	= '".($inf[csf("dyeing_part")])."';\n";
			echo "document.getElementById('cbo_diawidthtype').value  		= '".($inf[csf("dia_width_type")])."';\n";
			echo "document.getElementById('cbo_color_range').value  		= '".($inf[csf("dyeing_color_range")])."';\n";
			echo "document.getElementById('cbo_dyeing_upto').value  		= '".($inf[csf("dyeing_upto")])."';\n";
			echo "document.getElementById('cbo_status').value = '".($inf[csf("status")])."';\n";
			echo "document.getElementById('update_id').value = '".($inf[csf("dtls_id")])."';\n";
		}
		else if($main_process ==35)
		{
			echo "document.getElementById('cbo_aop_type').value  	= '".($inf[csf("aop_type")])."';\n";
			echo "document.getElementById('cbo_no_color').value  		= '".($inf[csf("aop_no_of_color")])."';\n";
			echo "document.getElementById('txt_coverage_from').value  		= '".($inf[csf("aop_coverage_from")])."';\n";
			echo "document.getElementById('txt_coverage_to').value  		= '".($inf[csf("aop_coverage_to")])."';\n";
			echo "document.getElementById('cbo_aop_upto').value  		= '".($inf[csf("aop_upto")])."';\n";
			echo "document.getElementById('cbo_status').value = '".($inf[csf("status")])."';\n";
			echo "document.getElementById('update_id').value = '".($inf[csf("dtls_id")])."';\n";
		}
		else if($main_process ==1000)
		{
			echo "document.getElementById('cbo_additional_process').value  		= '".($inf[csf("additional_process")])."';\n";
			echo "document.getElementById('cbo_status').value = '".($inf[csf("status")])."';\n";
			echo "document.getElementById('update_id').value = '".($inf[csf("dtls_id")])."';\n";
		}
	    echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_fabric_count_determination',1);\n";  

	}
	exit();
}

if ($action=="search_list_view_bk")
{
	$composition_arr=array();
	$lib_yarn_count=return_library_array( "select yarn_count,id from lib_yarn_count", "id", "yarn_count");
	$user_arr=return_library_array( "select user_full_name,id from user_passwd", "id", "user_full_name");
	$lib_group_short=return_library_array( "select id,group_short_name from lib_group where id=1 and status_active=1", "id", "group_short_name");

	$body_type_arr=return_library_array( "select id,body_part_full_name from lib_body_part where  status_active=1", "id", "body_part_full_name");
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$group_short_name=$lib_group_short[1];
	$part_type_arr=array(1=>"Within Group",2=>"In-Bound");
	/*$sql_q="select mst_id, copmposition_id, percent, count_id, type_id from lib_yarn_count_determina_dtls where is_deleted=0  order by id";
	
	
	$data_array=sql_select($sql_q);
	if (count($data_array)>0)
	{
		foreach( $data_array as $row )
		{
			$compo_per="";
			if(($row[csf('percent')]*1)>0) $compo_per=$row[csf('percent')]."% "; else $compo_per="";
			if(array_key_exists($row[csf('mst_id')],$composition_arr))
			{
				$composition_arr[$row[csf('mst_id')]]=$row[csf('mst_id')].','.$composition_arr[$row[csf('mst_id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$compo_per.$lib_yarn_count[$row[csf('count_id')]]." ".$yarn_type[$row[csf('type_id')]].",";
			}
			else
			{
				$composition_arr[$row[csf('mst_id')]]=$composition[$row[csf('copmposition_id')]]." ".$compo_per.$lib_yarn_count[$row[csf('count_id')]]." ".$yarn_type[$row[csf('type_id')]];
			}
			$sys_code=$group_short_name.'-'.$row[csf('mst_id')];
			$sysCodeArr[$row[csf('mst_id')]]=$sys_code;
		}
	}
	unset($data_array);*/
	
	$sql="select a.fab_nature_id,a.construction,a.gsm_weight,a.color_range_id,a.stich_length,a.process_loss,b.copmposition_id,b.percent,b.count_id,b.type_id,a.id,b.id as bid from  lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by a.id,b.id";
	$data_array=sql_select($sql);
	$sysCodeArr=array();
	if (count($data_array)>0)
	{
		foreach( $data_array as $row )
		{
			if(array_key_exists($row[csf('id')],$composition_arr))
			{
				$composition_arr[$row[csf('id')]]=$row[csf('construction')].','.$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ".$lib_yarn_count[$row[csf('count_id')]]." ".$yarn_type[$row[csf('type_id')]].",";
			}
			else
			{
				$composition_arr[$row[csf('id')]]=$row[csf('construction')].','.$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ".$lib_yarn_count[$row[csf('count_id')]]." ".$yarn_type[$row[csf('type_id')]].",";
			}
			$sys_code=$group_short_name.'-'.$row[csf('id')];
			$sysCodeArr[$row[csf('id')]]=$sys_code;
		}
	}
	
	//print_r($sysCodeArr);				
	$sql="select id, company_id, fabric_description, fabric_source, body_part_id, body_part_type, color_type, party_type, party_name, uom,rate_bdt, rate_usd, aop_type, aop_process_upto, no_of_color, effective_date, coverage_range_from, coverage_range_to, count_range_from, count_range_to, color_range from  process_finish_fabric_rate_chat where is_deleted=0 order by id DESC";				
	$arr=array (0=>$company_arr,1=>$composition_arr,2=>$fabric_source,3=>$body_type_arr,4=>$body_part_type,5=>$color_type,9=>$no_color_arr,12=>$conversion_cost_head_array,13=>$aop_process_arr,14=>$part_type_arr,15=>$company_arr,18=>$unit_of_measurement);
	
	echo  create_list_view ( "list_view", "Company Name,Fabric Name,Fabric Source,Body part,Body Part Type,Color Type,Count Range From,Count Range To,Color Range,No. Of Color,Coverage % from,Coverage % to,AOP Type,AOP Process Upto,Party Type,Party Name,Rate BDT,Rate USD,UOM,Effect Date", "100,200,70,80,70,80,50,50,50,50,50,50,50,50,60,60,60,80,60,70","1470","350",0, $sql, "get_php_form_data", "id", "'load_php_data_to_form'",1, "company_id,fabric_description,fabric_source,body_part_id,body_part_type,color_type,0,0,0,no_of_color,0,0,aop_type,aop_process_upto,party_type,0,0,0,uom", $arr , "company_id,fabric_description,fabric_source,body_part_id,body_part_type,color_type,count_range_from,count_range_to,color_range,no_of_color,coverage_range_from,coverage_range_to,aop_type,aop_process_upto,party_type,party_name,rate_bdt,rate_usd,uom,effective_date", "requires/process_wise_finish_fabric_rate_chart_controller_v2",'setFilterGrid("list_view",-1);','0,0,0,0,0,0,2,2,0,2,2,2,0,0,0,0,2,2,0,3') ;

	exit();
}

   
if ($action == "load_drop_down_body_type") {

	$nameArray = sql_select("select id, body_part_full_name, body_part_short_name,entry_page_id, body_part_type,status,is_emplishment from lib_body_part where is_deleted=0 and id='$data'");
	foreach ($nameArray as $inf) 
	{
		echo "document.getElementById('cbo_body_part_type_id').value  = '" . $inf[csf("body_part_type")] . "';\n";
	}
}

if ($action == "load_drop_down_party")
{
	$data = explode("_", $data);
	$company_id = $data[1];

	if ($data[0] == 1) {
		//echo "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 and comp.id=$company_id order by comp.company_name";die;
		echo create_drop_down("cbo_party_name", 130, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0  order by comp.company_name", "id,company_name", 1, "-- Select Buyer --", 0, "", 0);
	} else if ($data[0] == 2) {

	$buyer_data=sql_select("select id, buyer_name,party_type from lib_buyer where status_active=1  order by id");
	foreach($buyer_data as $val){
		$party_id=explode(",",$val[csf("party_type")]);
		foreach($party_id as $row){
			if($row==2 || $row==3){
				$buyer_arr[$val[csf("id")]]=$val[csf("id")];
			}
		}
	}
	// echo count($buyer_arr);

	echo create_drop_down("cbo_party_name", 130, "select buy.id, buy.buyer_name from lib_buyer buy where status_active=1  ".where_con_using_array($buyer_arr,1,'buy.id')." order by buy.buyer_name", "id,buyer_name", 1, "-- Select Buyer --", $selected, "", 0);
	}
	else
	{
		echo create_drop_down( "cbo_party_name", 130, $blank_arr,"", 1, "-- Select --", '', "",$disabled,"" );
	}
	
	exit();
}


if($action=="fabric_description_popup")
{
	echo load_html_head_contents("Fabric Description Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>
		function js_set_value(data)
		{
			var data=data.split('_');
			var fabric_yarn_description=return_global_ajax_value(data[0], 'fabric_yarn_description', '', 'pre_cost_entry_controller_v2');
			var fabric_yarn_description_arr=fabric_yarn_description.split("**");
			var fabric_description=trim(data[2])+' '+trim(fabric_yarn_description_arr[0]);
			document.getElementById('fab_des_id').value=data[0];
			document.getElementById('fab_nature_id').value=data[1];
			document.getElementById('construction').value=trim(data[2]);
			document.getElementById('fab_gsm').value=trim(data[3]);
			document.getElementById('process_loss').value=trim(data[4]);
			document.getElementById('fab_desctiption').value=trim(fabric_description);
			document.getElementById('composition').value=trim(fabric_yarn_description_arr[0]);
			var yarn =fabric_yarn_description_arr[1].split("_");
			if(yarn[1]*1==0 || yarn[1]==""){
				alert("Composition not set in yarn count determination");
				return;
			}
			document.getElementById('yarn_desctiption').value=trim(fabric_yarn_description_arr[1]);
			parent.emailwindow.hide();
		}
		function toggle( x, origColor )
		{
			var newColor = 'yellow';
			document.getElementById(x).style.backgroundColor = ( newColor == document.getElementById(x).style.backgroundColor )? origColor : newColor;
		}
			</script>
    </head>
    <body>
    <div align="center">
        <form name="styleRef_form" id="styleRef_form">
		<fieldset>
            <table cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
                <thead>
                    <tr>
                    	<th colspan="3" align="center"><? echo create_drop_down( "cbo_string_search_type", 100, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
                    </tr>
                    <tr>
                        <th>Construction</th>
                        <th>GSM/Weight</th>
                        <th><input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;" onClick="reset_form('styleRef_form','search_div','','','','');"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td align="center"><input type="text" style="width:130px" class="text_boxes" name="txt_construction" id="txt_construction" /></td>
                        <td align="center">	<input type="text" style="width:130px" class="text_boxes" name="txt_gsm_weight" id="txt_gsm_weight" /></td>
                        <td align="center">
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $fabric_nature; ?>'+'**'+'<? echo $libyarncountdeterminationid; ?>'+'**'+document.getElementById('txt_construction').value+'**'+document.getElementById('txt_gsm_weight').value+'**'+document.getElementById('cbo_string_search_type').value, 'fabric_description_popup_search_list_view', 'search_div', 'pre_cost_entry_controller_v2', 'setFilterGrid(\'list_view\',-1)'); toggle( 'tr_'+'<? echo $libyarncountdeterminationid; ?>', '#FFFFCC');" style="width:100px;" />
                        </td>
                    </tr>
            	</tbody>
           	</table>
            <div style="margin-top:10px" id="search_div"></div>
		</fieldset>
	</form>
    </div>
    </body>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit();
}

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	
	if ($operation==0)  // Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		$cbo_main_process=str_replace("'","",$cbo_main_process);
		
		$id = return_next_id_by_sequence("PRCS_WISE_FIN_RATE_MST_SEQ", "lib_prcs_finfab_rt_chrt_mst", $con);
		
		$field_array= "id, company_id, main_process, inserted_by, insert_date, status_active, is_deleted";
		$data_array="(".$id.",".$cbo_company_name.",".$cbo_main_process.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,'0')";
		//echo "10**INSERT INTO process_finish_fabric_rate_chat(".$field_array.") VALUES ".$data_array;die;

		$dtls_id = return_next_id_by_sequence("PRCS_WISE_FIN_RATE_DTLS_SEQ", "lib_prcs_finfab_rt_chrt_dtls", $con);
		if($cbo_main_process==1)
		{
			$field_array_dtls= "id, mst_id, knit_fabric_type, knit_from_count, knit_to_count, status, inserted_by, insert_date, status_active, is_deleted";
			$data_array_dtls="(".$dtls_id.",".$id.",".$cbo_fabric_type.",".$cbo_count_range_from.",".$cbo_count_range_to.",".$cbo_status.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,'0')";

			//cbo_company_name*cbo_main_process*cbo_fabric_type*cbo_count_range_from*cbo_count_range_to*cbo_status*update_id
		}
		else if($cbo_main_process==30)
		{
			$field_array_dtls= "id, mst_id, yarn_dyeing_part, yarn_color_range, status, inserted_by, insert_date, status_active, is_deleted";
			$data_array_dtls="(".$dtls_id.",".$id.",".$cbo_yarn_dyeing_part.",".$cbo_yarn_color_range.",".$cbo_status.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,'0')";


			//cbo_yarn_dyeing_part*cbo_yarn_color_range
		}
		else if($cbo_main_process==31)
		{
			$field_array_dtls= "id, mst_id, dyeing_part, dia_width_type, dyeing_color_range, dyeing_upto, status, inserted_by, insert_date, status_active, is_deleted";
			$data_array_dtls="(".$dtls_id.",".$id.",".$cbo_dyeing_part.",".$cbo_diawidthtype.",".$cbo_color_range.",".$cbo_dyeing_upto.",".$cbo_status.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,'0')";
			//cbo_dyeing_part*cbo_diawidthtype*cbo_color_range*cbo_dyeing_upto*cbo_status
		}
		else if($cbo_main_process==35)
		{
			$field_array_dtls= "id, mst_id, aop_type, aop_no_of_color, aop_coverage_from, aop_coverage_to, aop_upto, status, inserted_by, insert_date, status_active, is_deleted";
			$data_array_dtls="(".$dtls_id.",".$id.",".$cbo_aop_type.",".$cbo_no_color.",".$txt_coverage_from.",".$txt_coverage_to.",".$cbo_aop_upto.",".$cbo_status.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,'0')";
			//cbo_aop_type*cbo_no_color*txt_coverage_from*txt_coverage_to*cbo_aop_upto
		}
		else if ($cbo_main_process==1000)
		{
			$field_array_dtls= "id, mst_id, additional_process, status, inserted_by, insert_date, status_active, is_deleted";
			$data_array_dtls="(".$dtls_id.",".$id.",".$cbo_additional_process.",".$cbo_status.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,'0')";
			//cbo_additional_process
		}

		
		$rID=sql_insert("lib_prcs_finfab_rt_chrt_mst",$field_array,$data_array,0);
		$rID2=sql_insert("lib_prcs_finfab_rt_chrt_dtls",$field_array_dtls,$data_array_dtls,0);
		
		//echo "10**".$rID.'=='.$rID_1;die;
		//check_table_status( $_SESSION['menu_id'],0);
		if($db_type==0)
		{
			if($rID && $rID2){
				mysql_query("COMMIT");  
				echo "0**".$rID."**".$rID2;
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".$rID ."**". $rID2;
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID2)
			{
				oci_commit($con);  
				echo "0**".$rID."**".$rID2;;
			}
		else{
				oci_rollback($con); 
				echo "10**".$rID ."**". $rID2;
			}
		}
		disconnect($con);
		die;
		
	}
	else if ($operation==1)   // Update Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		$update_id=str_replace("'","",$update_id);
		
		
		//$field_array= "id, company_id, main_process, inserted_by, insert_date, status_active, is_deleted";
		//$data_array="(".$id.",".$cbo_company_name.",".$cbo_main_process.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,'0')";
		//echo "10**INSERT INTO process_finish_fabric_rate_chat(".$field_array.") VALUES ".$data_array;die;

		$cbo_main_process = str_replace("'","",$cbo_main_process);

		//$dtls_id = return_next_id_by_sequence("PRCS_WISE_FIN_RATE_DTLS_SEQ", "lib_prcs_finfab_rt_chrt_dtls", $con);
		if($cbo_main_process==1)
		{
			$field_array_dtls= "knit_fabric_type*knit_from_count*knit_to_count*status*updated_by*update_date";
			$data_array_dtls="".$cbo_fabric_type."*".$cbo_count_range_from."*".$cbo_count_range_to."*".$cbo_status."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			//cbo_company_name*cbo_main_process*cbo_fabric_type*cbo_count_range_from*cbo_count_range_to*cbo_status*update_id
		}
		else if($cbo_main_process==30)
		{
			$field_array_dtls= "yarn_dyeing_part*yarn_color_range*status*updated_by*update_date";
			$data_array_dtls="".$cbo_yarn_dyeing_part."*".$cbo_yarn_color_range."*".$cbo_status."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			//cbo_yarn_dyeing_part*cbo_yarn_color_range
		}
		else if($cbo_main_process==31)
		{
			$field_array_dtls= "dyeing_part*dia_width_type*dyeing_color_range*dyeing_upto*status*updated_by*update_date";
			$data_array_dtls="".$cbo_dyeing_part."*".$cbo_diawidthtype."*".$cbo_color_range."*".$cbo_dyeing_upto."*".$cbo_status."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			//cbo_dyeing_part*cbo_diawidthtype*cbo_color_range*cbo_dyeing_upto*cbo_status
		}
		else if($cbo_main_process==35)
		{
			$field_array_dtls= "aop_type*aop_no_of_color*aop_coverage_from*aop_coverage_to*aop_upto*status*updated_by*update_date";
			$data_array_dtls="".$cbo_aop_type."*".$cbo_no_color."*".$txt_coverage_from."*".$txt_coverage_to."*".$cbo_aop_upto."*".$cbo_status."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			//cbo_aop_type*cbo_no_color*txt_coverage_from*txt_coverage_to*cbo_aop_upto
		}
		else if ($cbo_main_process==1000)
		{
			$field_array_dtls= "additional_process*status*updated_by*update_date";
			$data_array_dtls="".$cbo_additional_process."*".$cbo_status."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			//cbo_additional_process
		}

		$rID=sql_update("lib_prcs_finfab_rt_chrt_dtls",$field_array_dtls,$data_array_dtls,"id","".$update_id."",1);
		
		//echo "10**".$field_array_dtls."<br>".$data_array_dtls;die;

		if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");  
				echo "1**".$rID;
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".$rID;
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID)
			{
				oci_commit($con);  
				echo "1**".$rID;
			}
			else{
				oci_rollback($con); 
				echo "10**".$rID."**".sql_update("lib_prcs_finfab_rt_chrt_dtls",$field_array_dtls,$data_array_dtls,"id","".$update_id."",1,1);
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==2) // Delete Here
	{
		echo "10**";die;
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		$field_array1="updated_by*update_date*status_active*is_deleted";
		$data_array1="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'0'*'1'";
		$rID=sql_delete("process_finish_fabric_rate_chat",$field_array1,$data_array1,"id","".$update_id."",1);
	
		
		if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");  
				echo "2**".$rID;
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".$rID;
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
		 if($rID)
			{
				oci_commit($con);   
				echo "2**".$rID;
			}
			else{
				oci_rollback($con);
				echo "10**".$rID;
			}
		}
		disconnect($con);
		die;
	}
}

/* if($action=="composition_popup")
{
	echo load_html_head_contents("Order Info","../../../", 1, 1, '','1','');
	extract($_REQUEST);	
	
	?>
	<script>
		function js_set_value(id,name)
		{
			document.getElementById('hidcompid').value=id;
			document.getElementById('hidcompname').value=name;
			parent.emailwindow.hide();
		}
    </script>
	</head>
	<body>
		<?

		$composition_arr=array();
		$lib_yarn_count=return_library_array( "select yarn_count,id from lib_yarn_count", "id", "yarn_count");
		$lib_group_short=return_library_array( "select id,group_short_name from lib_group where id=1 and status_active=1", "id", "group_short_name");
		$group_short_name=$lib_group_short[1];
		$arr=array (0=>$item_category, 3=>$color_range,6=>$composition,8=>$lib_yarn_count,9=>$yarn_type);


		$sql="select a.fab_nature_id,a.construction,a.gsm_weight,a.color_range_id,a.stich_length,a.process_loss,b.copmposition_id,b.percent,b.count_id,b.type_id,a.id,b.id as bid from  lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by a.id,b.id";
		$data_array=sql_select($sql);
		$sysCodeArr=array();
		if (count($data_array)>0)
		{
			foreach( $data_array as $row )
			{
				if(array_key_exists($row[csf('id')],$composition_arr))
				{
					$composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ".$lib_yarn_count[$row[csf('count_id')]]." ".$yarn_type[$row[csf('type_id')]].",";
				}
				else
				{
					$composition_arr[$row[csf('id')]]=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ".$lib_yarn_count[$row[csf('count_id')]]." ".$yarn_type[$row[csf('type_id')]].",";
				}
				$sys_code=$group_short_name.'-'.$row[csf('id')];
				$sysCodeArr[$row[csf('id')]]=$sys_code;
			}
		}
		?>
		<fieldset style="width:600px;margin-left:10px">
	        <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
	            <table cellpadding="0" cellspacing="0" border="1" rules="all" width="600" class="rpt_table">
	                <thead>
					<th width="50">SL No</th>
					<th width="100">Fab Nature</th>
					<th width="100">Construction</th>
					<th width="170">Composition</th>
					<th width="80">Fabric Composition</th>
					<th width="100">GSM/Weight</th>
					
					<input type="hidden" name="hidcompid" id="hidcompid" value="" style="width:50px">
					<input type="hidden" name="hidcompname" id="hidcompname" value="" style="width:50px">
                    </th>
	                </thead>
                </table>
                <table cellpadding="0" cellspacing="0" border="1" rules="all" width="600" class="rpt_table" id="comp_tbl">
                    <tbody>
                    <? 
					$sql_data=sql_select("select a.fab_nature_id, a.construction, a.gsm_weight, a.color_range_id, a.id, a.fabric_composition_id from lib_yarn_count_determina_mst a where a.status_active=1 and a.is_deleted=0 
					 group by a.id, a.fab_nature_id, a.construction, a.gsm_weight, a.color_range_id, a.fabric_composition_id 
					 order by a.id");

                    $i=1; 
					foreach($sql_data as $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						?>
						<tr id="tr_<? echo $row[csf('id')]; ?>" bgcolor="<? echo $bgcolor; ?>" height="20" style="cursor:pointer; word-break:break-all;" onClick="js_set_value('<? echo $row[csf('id')]; ?>','<? echo $row[csf('construction')].",".$composition_arr[$row[csf('id')]]; ?>')">
							<td width="50"><? echo $i; ?></td>
							<td width="100" align="left"><? echo $item_category[$row[csf('fab_nature_id')]]; ?></td>
							<td width="100" align="left"><? echo $row[csf('construction')]; ?></td>
							<td width="170"><? echo $composition_arr[$row[csf('id')]]; ?></td>
							<td width="80" align="right"><? echo $fabric_composition[$row[csf('fabric_composition_id')]]; ?></td>
							<td width="100" align="right"><? echo $row[csf('gsm_weight')]; ?></td>
						</tr>
						<?
						$i++;
					}?>
                    </tbody>
	            </table>
	            <div id="search_div" style="margin-top:5px"></div>   
	        </form>
	    </fieldset>
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    <script>setFilterGrid('comp_tbl',-1);</script>
	</html>
	<?
	exit();
} */

function exchange_rate_to_dollar($date,$company_id=0)
{
	$data=explode("**",$data);
	if($db_type==0)
	{
		$conversion_date=change_date_format($date, "Y-m-d", "-",1);
	}
	else
	{
		$conversion_date=change_date_format($date, "d-M-y", "-",1);
	}
	$exchange_rate=set_conversion_rate( 2, $conversion_date, $company_id );
	echo $exchange_rate;
	exit();
}

if ($action=="process_wise_rate_popup")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
    <script>
	var permission='<? echo $permission; ?>';
	var cbo_company_name='<? echo $cbo_company_name; ?>';
	function fnc_process_rate_entry(operation)
	{
		//var tot_row=$('#tbl_process_rate_details tr').length-2;
		var dtls_id=document.getElementById('dtls_id').value;
		var tot_rate=document.getElementById('tot_rate_bdt').value;

		var tot_row =$("#tot_row").val()*1;
		
		var data_all=''; var z=1;
		for(i=1; i<=tot_row; i++)
		{
			if( ($('#txtrateBdt_'+i).val()*1)>0)
			{
				data_all+="&hdnServiceCompanyId_" + z + "='" + $('#hdnServiceCompanyId_'+i).val()+"'"+"&hdnServiceCompanyType_" + z + "='" + $('#hdnServiceCompanyType_'+i).val()+"'"+"&txtrateBdt_" + z + "='" + $('#txtrateBdt_'+i).val()+"'"+"&txtIssueDate_" + z + "='" + $('#txtIssueDate_'+i).val()+"'"+"&txtrateUsd_" + z + "='" + $('#txtrateUsd_'+i).val()+"'";

				z++;
			}
		}
		if(data_all=='')
		{
			alert("No Data Select");	
			return;
		}
		//alert(data_all);
		var data="action=save_update_delete_process_rate&operation="+operation+'&total_row='+z+get_submitted_data_string('dtls_id*tot_rate_bdt',"../../../")+data_all;
		
		//alert(data);
		freeze_window(operation);
		http.open("POST","process_wise_finish_fabric_rate_chart_controller_v2.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange=fnc_process_rate_entry_response;
	}
		
	function fnc_process_rate_entry_response()
	{
		if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split('**');	
			show_msg2(reponse[0]);
			$("#mst_id").val(reponse[1]);
			
			release_freezing();	
			if(reponse[0]==0 || reponse[0] ==1){
				set_button_status(1, permission, 'fnc_process_rate_entry',1);
			}
			//fn_close();
		}
	}
	function show_msg2( msg )
	{
		$('#messagebox_2', window.document).fadeTo(100,1,function() //start fading the messagebox
		{
			$('#messagebox_2', window.document).html( operation_success_msg[trim(msg)] ).removeClass('messagebox').addClass('messagebox_error').fadeOut(5500);
		});

	}
		
	function fn_sum()
	{
		/* var totBdtRate = 0;var totUsdRate = 0;
        $("#table_1").find('tbody tr ').each(function(){
			totBdtRate+=$(this).find('input[name="txtrateBdt[]"]').val()*1;
			totUsdRate+=$(this).find('input[name="txtrateUsd[]"]').val()*1;
        }); 
		$("#table_2").find('tbody tr ').each(function(){
			totBdtRate+=$(this).find('input[name="txtrateBdt[]"]').val()*1;
			totUsdRate+=$(this).find('input[name="txtrateUsd[]"]').val()*1;
        }); 

        $("#tot_rate_bdt").val(totBdtRate);
        $("#tot_rate_usd").val(totUsdRate); */

		var tot_row =$("#tot_row").val()*1;
		var totBdtRate = 0;var totUsdRate = 0;
		for(var i=1; i<=tot_row; i++)
		{
			totBdtRate+=$("#txtrateBdt_"+i).val()*1;
			totUsdRate+=$("#txtrateUsd_"+i).val()*1;
		}
		$("#tot_rate_bdt").val(totBdtRate);
        $("#tot_rate_usd").val(totUsdRate);

	}

	function fn_close(str)
	{
		parent.emailwindow.hide(); 
	}

	function exchange_rate(sl)
	{
		var recv_date = $('#txtIssueDate_'+sl).val();

		if(recv_date =="")
		{
			$('#txtrateBdt_'+sl).val("");
			return;
		}

		var response=return_global_ajax_value( 2+"**"+recv_date+"**"+cbo_company_name, 'check_conversion_rate', '', 'process_wise_finish_fabric_rate_chart_controller_v2');
		var dollar = $('#txtrateBdt_'+sl).val()/response;
		$('#txtrateUsd_'+sl).val(number_format(dollar,4,'.' , ""));
	}
		
	</script>
    </head>
    <body>
    
	<?
	$sql_up_data=sql_select("SELECT id, service_company, company_type, effective_date, bdt, usd from lib_prcs_finfab_rt_chrt_rate where dtls_id=$dtls_id and status_active=1 and is_deleted=0");

	$processRateArr=array();
	foreach($sql_up_data as $row)
	{
		$processRateArr[$row[csf("company_type")]][$row[csf("service_company")]]['effective_date']=$row[csf("effective_date")];
		$processRateArr[$row[csf("company_type")]][$row[csf("service_company")]]['bdt']=$row[csf("bdt")];
		$processRateArr[$row[csf("company_type")]][$row[csf("service_company")]]['usd']=$row[csf("usd")];
	}

	$sql_company=sql_select("SELECT id, company_name from lib_company where status_active=1 and is_deleted=0");
	$sql_supplier=sql_select("SELECT a.id, a.buyer_name FROM lib_buyer a, lib_buyer_tag_company b, lib_buyer_party_type c where a.id=b.buyer_id and a.id=c.buyer_id and c.party_type=2 and a.status_active=1 and a.is_deleted=0 and b.tag_company= $cbo_company_name group by a.id, a.buyer_name order by a.buyer_name");

	$tot_row = count($sql_company) + count($sql_supplier);
    ?>
    <form name="rate_1" id="rate_1">
		<div>
			<?=load_freeze_divs("../../../",$permission); ?>
			<div id="messagebox_2" style="max-height:50px; overflow-y:scroll;"></div>
			<div style="width:100%; max-height:245px; overflow-y:scroll;" id="list_container" align="left">
				<center>
					<table width="550" cellspacing="0" class="rpt_table" border="0" rules="all" id="table_1">
						<thead>
							<tr>
								<th width="50">SL</th>
								<th width="200">Within Group Company</th>
								<th width="100">Effective Date</th>
								<th width="100">BDT &#2547;</th>
								<th width="100">USD &#36;</th>
							</tr>
						</thead>
						<tbody id="tbl_company">
						<?
						$i=1;
						foreach($sql_company as $row)
						{
							$effective_date = $processRateArr[1][$row[csf("id")]]['effective_date'];
							$bdt 			= $processRateArr[1][$row[csf("id")]]['bdt'];
							$usd 			= $processRateArr[1][$row[csf("id")]]['usd'];
							?>
							<tr>
								<td width="50" align="center"><?=$i;?></td>
								<td width="200" style="word-break:break-all;width:200px;">
									<?= $row[csf("company_name")]; ?>
									<input type="hidden" id="hdnServiceCompanyId_<?=$i; ?>" name="hdnServiceCompanyId[]; ?>" value="<?= $row[csf("id")]; ?>"/>
									<input type="hidden" id="hdnServiceCompanyType_<?=$i; ?>" name="hdnServiceCompanyType[]; ?>" value="<?= 1; ?>"/>
								</td>
								<td>
									<input type="text" id="txtIssueDate_<?=$i; ?>" name="txtIssueDate[]" class="datepicker" style="width:100px;" value="<? echo ($effective_date !="") ? $effective_date : date("d-m-Y"); ?>" placeholder="Select Date" onblur="exchange_rate(<? echo $i;?>);"/>
								</td>
								<td align="center">
									<input type="text" id="txtrateBdt_<?=$i; ?>" style="width:100px" name="txtrateBdt[]" class="text_boxes_numeric" value="<?= ($bdt !="") ? $bdt : ""; ?>" onBlur="exchange_rate(<? echo $i;?>);fn_sum();" />
								</td>
								<td align="center">
									<input type="text" id="txtrateUsd_<?=$i; ?>" style="width:100px" name="txtrateUsd[]" class="text_boxes_numeric" value="<?= ($usd !="") ? $usd : ""; ?>" disabled />
								</td>
							</tr>
							<?
							$i++;
						}
						?>
						</tbody>
					</table>
				</center>
				<center>
					<table width="550" cellspacing="0" class="rpt_table" border="0" rules="all" id="table_2">
						<thead>
							<tr>
								<th width="50">SL</th>
								<th width="200">Customer List</th>
								<th width="100">Effective Date</th>
								<th width="100">BDT &#2547;</th>
								<th width="100">USD &#36;</th>
							</tr>
						</thead>
						<tbody id="tbl_supplier">
						<?
						
						$j=1;
						foreach($sql_supplier as $row)
						{
							$effective_date = $processRateArr[2][$row[csf("id")]]['effective_date'];
							$bdt 			= $processRateArr[2][$row[csf("id")]]['bdt'];
							$usd 			= $processRateArr[2][$row[csf("id")]]['usd'];
							?>
							<tr>
								<td width="50" align="center"><?=$j;?></td>
								<td width="200" style="word-break:break-all;width:200px;">
									<?= $row[csf("buyer_name")]; ?>
									<input type="hidden" id="hdnServiceCompanyId_<?=$i; ?>" name="hdnServiceCompanyId[]; ?>" value="<?= $row[csf("id")]; ?>"/>
									<input type="hidden" id="hdnServiceCompanyType_<?=$i; ?>" name="hdnServiceCompanyType[]; ?>" value="<?= 2; ?>"/>
								</td>
								<td>
									<input type="text" id="txtIssueDate_<?=$i; ?>" name="txtIssueDate[]" class="datepicker" style="width:100px;" value="<? echo ($effective_date !="") ? $effective_date : date("d-m-Y"); ?>" placeholder="Select Date" onblur="exchange_rate(<? echo $i;?>);"/>
								</td>
								<td align="center">
									<input type="text" id="txtrateBdt_<?=$i; ?>" style="width:100px" name="txtrateBdt[]" class="text_boxes_numeric" value="<?= ($bdt !="") ? $bdt : ""; ?>" onBlur="exchange_rate(<? echo $i;?>);fn_sum();" />
								</td>
								<td align="center">
									<input type="text" id="txtrateUsd_<?=$i; ?>" style="width:100px" name="txtrateUsd[]" class="text_boxes_numeric" value="<?= ($usd !="") ? $usd : ""; ?>" disabled />
								</td>
							</tr>
							<?
							$i++;$j++;
						}
						?>
						</tbody>
					</table>
				</center>

			</div>
			<div style="width:100%;max-height:80px; overflow-y:scroll;" id="" align="left">
				<center>
					<table width="550" cellspacing="0" class="rpt_table" border="0"  rules="all" >
						<tfoot>
							<tr style="background:#CCC">
								<td align="right" colspan="3" style="width:350px"><b>Total:</b></td>
								<td align="center"><input type="text" style="width:100px" class="text_boxes"  id="tot_rate_bdt" name="tot_rate_bdt" disabled /></td>
								<td align="center">
									<input type="text" style="width:100px" class="text_boxes"  id="tot_rate_usd" name="tot_rate_usd" disabled />
									
								</td>
							</tr>
						</tfoot>
				
					</table>
				</center>
				<center>
					<table width="550" cellspacing="0" class="rpt_table" border="0"rules="all">
						<tr>
							<td colspan="3" align="center">
							<input type="hidden" id="dtls_id" name="dtls_id" value="<?=$dtls_id; ?>" />
							<input type="hidden"  id="tot_row" name="tot_row" value="<? echo $tot_row ;?>"/>
							<?
							if(count($sql_up_data)==0)
							{
								echo load_submit_buttons($permission, "fnc_process_rate_entry", 0,0,"reset_form('rate_1','','','','','');",1);
							}
							else
							{
								echo load_submit_buttons($permission, "fnc_process_rate_entry", 1,0,"reset_form('rate_1','','','','','');",1);
							}
							?>
							</td>
						</tr>
					</table>
				</center>
			</div>
		</div>
    </form>
    </body>  
	<script>
		var tableFilters = 	{	};	
		setFilterGrid("tbl_company",tableFilters,-1)
		setFilterGrid("tbl_supplier",tableFilters,-1)
		fn_sum();
    </script>         
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit();
}

if ($action == "check_conversion_rate") {
	$data = explode("**", $data);
	if ($db_type == 0) {
		$conversion_date = change_date_format($data[1], "Y-m-d", "-", 1);
	} else {
		$conversion_date = change_date_format($data[1], "d-M-y", "-", 1);
	}
	$exchange_rate = set_conversion_rate($data[0], $conversion_date, $data[2]);
	echo $exchange_rate;
	exit();
}

if ($action=="save_update_delete_process_rate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$sqlDate=sql_select("select mst_id from lib_prcs_finfab_rt_chrt_dtls where id=$dtls_id");
	$mst_id=$sqlDate[0][csf("mst_id")];
	
	if ($operation==0)  // Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		$id=return_next_id( "id", "lib_prcs_finfab_rt_chrt_rate", 1 ) ;
		$field_array= "id,mst_id, dtls_id,service_company,company_type,effective_date,bdt,usd,inserted_by,insert_date,status_active,is_deleted";
		$k=1;
		for ($i=1;$i<=$total_row;$i++)
		{
			$hdnServiceCompanyId="hdnServiceCompanyId_".$i;
			$hdnServiceCompanyType="hdnServiceCompanyType_".$i;
			$txtIssueDate="txtIssueDate_".$i;
			$txtrateBdt="txtrateBdt_".$i;
			$txtrateUsd="txtrateUsd_".$i;
			
			if( (str_replace("'","",$$txtrateBdt)*1)>0)
			{
				if($db_type==0)
				{
					$effDate=change_date_format(str_replace("'","",$$txtIssueDate), "Y-m-d", "-",1);
				}
				else
				{
					$effDate=change_date_format(str_replace("'","",$$txtIssueDate), "d-M-y", "-",1);
				}

				if ($k!=1) $data_array .=",";
				$data_array .="(".$id.",".$mst_id.",".$dtls_id.",".$$hdnServiceCompanyId.",".$$hdnServiceCompanyType.",'".$effDate."',".$$txtrateBdt.",".$$txtrateUsd.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1',0)";
				$id++; $k++;
			}
		}

		$rID=sql_insert("lib_prcs_finfab_rt_chrt_rate",$field_array,$data_array,1);
		//echo "10**INSERT INTO lib_prcs_finfab_rt_chrt_rate (".$field_array.") values ".$data_array ;die;
		//echo "10**".$rID;die;
		if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");  
				echo "0**".$rID;
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".$rID;
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID )
			{
				oci_commit($con);  
				echo "0**".$rID;
			}
		else{
				oci_rollback($con); 
				echo "10**".$rID;
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==1)   // Update Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		$rID_del=execute_query("DELETE  from lib_prcs_finfab_rt_chrt_rate WHERE dtls_id=".$dtls_id."");

		$id=return_next_id( "id", "lib_prcs_finfab_rt_chrt_rate", 1 ) ;
		$field_array= "id,mst_id, dtls_id,service_company,company_type,effective_date,bdt,usd,inserted_by,insert_date,status_active,is_deleted";
		$k=1;
		for ($i=1;$i<=$total_row;$i++)
		{
			$hdnServiceCompanyId="hdnServiceCompanyId_".$i;
			$hdnServiceCompanyType="hdnServiceCompanyType_".$i;
			$txtIssueDate="txtIssueDate_".$i;
			$txtrateBdt="txtrateBdt_".$i;
			$txtrateUsd="txtrateUsd_".$i;
			
			if( (str_replace("'","",$$txtrateBdt)*1)>0)
			{
				//echo "10**".$$txtIssueDate;die;
				if($db_type==0)
				{
					$effDate=change_date_format(str_replace("'","",$$txtIssueDate), "Y-m-d", "-",1);
				}
				else
				{
					$effDate=change_date_format(str_replace("'","",$$txtIssueDate), "d-M-y", "-",1);
				}

				if ($k!=1) $data_array .=",";
				$data_array .="(".$id.",".$mst_id.",".$dtls_id.",".$$hdnServiceCompanyId.",".$$hdnServiceCompanyType.",'".$effDate."',".$$txtrateBdt.",".$$txtrateUsd.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1',0)";
				$id++; $k++;
			}
		}

		$rID=sql_insert("lib_prcs_finfab_rt_chrt_rate",$field_array,$data_array,1);
		
		if($db_type==0)
		{
			if($rID && $rID_del){
				mysql_query("COMMIT");  
				echo "0**".$mst_id;
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".$mst_id;
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID_del)
			{
				oci_commit($con);  
				echo "0**".$mst_id;
			}
		else{
				oci_rollback($con); 
				echo "10**".$mst_id;
			}
		}
		disconnect($con);
		die;
	}
}
?>