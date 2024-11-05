<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
function pre($array){
	echo "<pre>";
	print_r($array);
	echo "</pre>";
}
$floor_library=return_library_array( "select id,floor_name from lib_prod_floor", "id", "floor_name"  );
$line_library=return_library_array( "select id,line_name from lib_sewing_line ", "id", "line_name"  );
$lineArr = return_library_array("select id,line_name from lib_sewing_line order by id","id","line_name");
$prod_reso_line_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');

if($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "","");
	exit();
}

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_id", 140, "select id,location_name from lib_location where status_active=1 and is_deleted=0 and company_id='$data' order by location_name","id,location_name", 1, "-- Select Location --", $selected, "load_drop_down( 'requires/line_wise_productivity_analysis_report_controller', this.value, 'load_drop_down_floor', 'floor_td' );get_php_form_data( this.value, 'eval_multi_select', 'requires/line_wise_productivity_analysis_report_controller' );load_drop_down( 'requires/line_wise_productivity_analysis_report_controller',document.getElementById('cbo_floor_id').value+'_'+this.value+'_'+document.getElementById('cbo_company_id').value+'_'+document.getElementById('txt_date').value, 'load_drop_down_line', 'line_td' );",0 );
	exit();
}

if ($action=="load_drop_down_floor")
{
	echo create_drop_down( "cbo_floor_id", 130, "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and location_id='$data' order by floor_name","id,floor_name", 1, "-- Select Floor --", $selected, "load_drop_down( 'requires/line_wise_productivity_analysis_report_controller', this.value+'_'+document.getElementById('cbo_location_id').value+'_'+document.getElementById('cbo_company_id').value+'_'+document.getElementById('txt_date').value, 'load_drop_down_line', 'line_td' );",0 );
	exit();
}
if ($action == "eval_multi_select") {
    echo "set_multiselect('cbo_floor_id','0','0','','0');\n";
	echo 'setTimeout[($("#floor_td a").attr("onclick","disappear_list(cbo_floor_id,0);getLineId();") ,3000)];';
    exit();
}

if($action=="load_drop_down_line")
{
	extract($_REQUEST);
	$explode_data = explode("_",str_replace("'", "", $formData));
	$txt_sewing_date = $explode_data[3];
	$cond="";
	$prod_reso_allocation=return_field_value("auto_update","variable_settings_production","company_name=$explode_data[2] and variable_list=23 and is_deleted=0 and status_active=1");
	if($prod_reso_allocation==1)
	{
		$line_library=return_library_array( "select id,line_name from lib_sewing_line", "id", "line_name"  );
		$line_array=array();
		if($txt_sewing_date=="")
		{
			if( $explode_data[1] ) $cond.= " and location_id= $explode_data[1]";
			if( $explode_data[0] ) $cond.= " and floor_id in($explode_data[0])";

			$line_data=sql_select("select id, line_number from prod_resource_mst where is_deleted=0 $cond");
		}
		else
		{
			if( $explode_data[1]) $cond.= " and a.location_id= $explode_data[1]";
			if( $explode_data[0]) $cond.= " and a.floor_id in($explode_data[0])";

			if($db_type==0)
			{
				$line_data=sql_select("select a.id, a.line_number from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id and b.pr_date='".change_date_format($txt_sewing_date,'yyyy-mm-dd')."' and a.is_deleted=0 and b.is_deleted=0 $cond group by a.id  order by a.prod_resource_num");
			}
			else if($db_type==2 || $db_type==1)
			{
				$line_data=sql_select("select a.id, a.line_number,a.prod_resource_num from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id and b.pr_date='".date("j-M-Y",strtotime($txt_sewing_date))."' and a.is_deleted=0 and b.is_deleted=0 $cond group by a.id,a.prod_resource_num, a.line_number  order by a.prod_resource_num");
			}
		}
		$line_merge=9999;
		foreach($line_data as $row)
		{
			$line='';
			$line_number=explode(",",$row[csf('line_number')]);
			foreach($line_number as $val)
			{
				if(count($line_number)>1)
				{
					$line_merge++;
					$new_arr[$line_merge]=$row[csf('id')];
				}
				else
					$new_arr[$val]=$row[csf('id')];
				if($line=='') $line=$line_library[$val]; else $line.=",".$line_library[$val];
			}
			$line_array[$row[csf('id')]]=$line;
		}

		ksort($new_arr);
		foreach($new_arr as $key=>$v)
		{
			$line_array_new[$v]=$line_array[$v];
		}
		//echo create_drop_down( "cbo_sewing_line", 110,$line_array_new,"", 1, "--- Select ---", $selected, "",0,0 );
		echo create_drop_down( "cbo_line", 110,$line_array_new,"", 1, "--- Select ---", $selected, "",0,0 );
	}
	else
	{
		if( $explode_data[0]==0 && $explode_data[1]!=0 ) $cond = " and location_name= $explode_data[1]";
		if( $explode_data[0]!=0 ) $cond = " and floor_name in($explode_data[0])";

		echo create_drop_down( "cbo_line", 110, "select id,line_name from lib_sewing_line where  is_deleted=0 and status_active=1 and floor_name!=0 $cond order by line_name","id,line_name", 1, "--- Select ---", $selected, "",0,0 );
	}
	exit();
}
if ($action=="reject_qty")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$data=explode('*',$data);
	?>
	 	<?
           list($date,$line,$loc_id,$floor_id,$po_id,$item_id,$color_id) = explode('__',$search_string);

			$prod_con = "";
			$prod_con .= ($color_id=="") ? "" : " and d.color_number_id=".$color_id;
			$prod_con .= ($item_id=="") ? "" : " and a.item_number_id=".$item_id;
			$prod_con .= ($po_id=="") ? "" : " and a.po_break_down_id=".$po_id;
			$prod_con .= ($cbo_floor == "") ? "" : " and a.floor_id in($cbo_floor)";
			$prod_con .= ($loc_id==0) ? "" : " and a.location=".$loc_id;
			// $prod_con .= ($line_id=="") ? "" : " and a.sewing_line=".$line_id;
			$prod_con .= ($date=="") ? "" : " and a.production_date='$date'";


			$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name" );
			$size_library=return_library_array( "select id,size_name from lib_size", "id", "size_name" );

			$sql="SELECT a.location,a.production_date,b.reject_qty,a.floor_id,a.po_break_down_id as po_id,a.item_number_id as item_id, d.color_number_id, d.size_number_id,a.sewing_line,a.production_type
			from
				pro_garments_production_mst a,
				pro_garments_production_dtls b,
				wo_po_color_size_breakdown d
			where
					a.id=b.mst_id
				and   b.color_size_break_down_id=d.id
				and   a.status_active=1
				and   a.is_deleted=0
				and   b.status_active=1
				and   b.is_deleted=0
				and   d.status_active =1
				and   d.is_deleted=0 $prod_con
				";
			// echo $sql;

			$result=sql_select($sql);
			foreach($result as $row){
			$bundle_size_arr[$row[csf('size_number_id')]]=$row[csf('size_number_id')];
			$bundle_color_size_arr[$row[csf('color_number_id')]][$row[csf('size_number_id')]]['qty']+=$row[csf('reject_qty')];
			$total_reject+=$row[csf('reject_qty')];


			}
			$sql_defect="SELECT a.po_break_down_id,a.defect_type_id,a.defect_qty,a.defect_point_id from pro_gmts_prod_dft a where a.production_type=5 and a.defect_type_id in(1,2,3,4,5,6,7) and a.po_break_down_id in($po_id) and a.status_active=1 and a.is_deleted=0";
			// echo $sql_defect;
			$defect_result=sql_select($sql_defect);
			$defect_array=array();
			foreach($defect_result as $value)
			{
				$defect_array[$value[csf('defect_type_id')]][$value[csf('defect_point_id')]]+=$value[csf('defect_qty')];

			}
			// echo "<pre>";print_r($defect_array);
			$tbl_width = 400+(count($bundle_size_arr)*60);
    //    ?>
       <div style="width:<?=$tbl_width+20;?>px;" align="center">
       <table width="<?=$tbl_width;?>" cellspacing="0" border="1" class="rpt_table" rules="all" >
             <thead>
             	<tr>
                	<th colspan="<?=(5+count($bundle_size_arr));?>">Sewing Reject Quantity</th>
                </tr>
                <tr>
                    <th width="20">SL</th>
                    <th width="130">Color</th>
					<?
                    foreach($bundle_size_arr as $size_id=>$size_data)
					{
						?>
						<th width="60"><?
						echo $size_library[$size_id];
						?></th>
						<?
					}
					?>
					<th width="120">Defect Name</th>
					<th width="50">Defect Qty</th>
                    <th width="80">Total Reject Qty.</th>
                 </tr>
				 <tbody>

         		<?
		       	// echo "<br>";
		      	// print_r($bundle_color_size_arr);
		        $i=1;
			    foreach($bundle_color_size_arr as $color_id=>$color_data)
			    {
				    $rowspn=count($color_data['size']);
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					?>
					<tr bgcolor="<? echo $bgcolor; ?>">

						<td><? echo $i; ?></td>
						<td><? echo $color_library[$color_id]; ?></td>
						<?
                        foreach($bundle_size_arr as $size_id=>$size_data)
						{
					     	?>
						 	<td align="right"><? echo $color_data[$size_id]['qty'];?></td>
						 	<?
					   	}
					  	?>
						<td>
							<?
							foreach ($defect_array[3] as $key => $v)
							{
								?>
								<div><?=$sew_fin_reject_type_for_arr[$key];?></div>
								<hr class="style-one">
								<?
							}
							?>
							<?
							foreach ($defect_array[2] as $key => $v)
							{
								?>
								<div><?=$sew_fin_spot_defect_type[$key];?></div>
								<hr class="style-one">
								<?
							}
							?>
							<?
							foreach ($defect_array[1] as $key => $v)
							{
								?>
								<div><?=$sew_fin_alter_defect_type[$key];?></div>
								<hr class="style-one">
								<?
							}
							?>
							<?
							foreach ($defect_array[4] as $key => $v) //Front
							{
								?>
								<div><?=$sew_fin_woven_defect_array[$key];?></div>
								<hr class="style-one">
								<?
							}
							?>
							<?
							foreach ($defect_array[5] as $key => $v) //Back
							{
								?>
								<div><?=$sew_fin_woven_defect_array[$key];?></div>
								<hr class="style-one">
								<?
							}
							?>
							<?
							foreach ($defect_array[6] as $key => $v) //West
							{
								?>
								<div><?=$sew_fin_woven_defect_array[$key];?></div>
								<hr class="style-one">
								<?
							}
							?>
							<?
							foreach ($defect_array[7] as $key => $v) //Measure
							{
								?>
								<div><?=$sew_fin_measurment_check_array[$key];?></div>
								<hr class="style-one">
								<?
							}
							?>

						</td>
						<td>
							<?
							foreach ($defect_array[3] as $key => $v)
							{
								?>
								<div><?=$v;?></div>
								<hr class="style-one">
								<?
							}
							?>
							<?
							foreach ($defect_array[2] as $key => $v)
							{
								?>
								<div><?=$v;?></div>
								<hr class="style-one">
								<?
							}
							?>
							<?
							foreach ($defect_array[1] as $key => $v)
							{
								?>
								<div><?=$v;?></div>
								<hr class="style-one">
								<?
							}
							?>
							<?
							foreach ($defect_array[4] as $key => $v) //Front
							{
								?>
								<div><?=$v;?></div>
								<hr class="style-one">
								<?
							}
							?>
							<?
							foreach ($defect_array[5] as $key => $v) //Back
							{
								?>
								<div><?=$v;?></div>
								<hr class="style-one">
								<?
							}
							?>
							<?
							foreach ($defect_array[6] as $key => $v) //West
							{
								?>
								<div><?=$v;?></div>
								<hr class="style-one">
								<?
							}
							?>
							<?
							foreach ($defect_array[7] as $key => $v) //Measure
							{
								?>
								<div><?=$v;?></div>
								<hr class="style-one">
								<?
							}
							?>

						</td>
                        <td align="right"><?  echo number_format($total_reject,0); ?>&nbsp;</td>
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
if ($action=="sewin_popup_one")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$data=explode('*',$data);
	?>
	 	<?
          

			$prod_con = "";
			$prod_con .= ($company_name=="") ? "" : " and a.company_id=".$company_name;
			$prod_con .= ($color_id=="") ? "" : " and d.color_number_id=".$color_id;
			// $prod_con .= ($line_id=="") ? "" : " and a.sewing_line=".$line_id;
			$prod_con .= ($prod_date=="") ? "" : " and a.production_date='$prod_date'";


			$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name" );
			$size_library=return_library_array( "select id,size_name from lib_size", "id", "size_name" );

			$sql="SELECT a.production_date,b.production_qnty, d.color_number_id, d.size_number_id,a.production_type
			from
				pro_garments_production_mst a,
				pro_garments_production_dtls b,
				wo_po_color_size_breakdown d
			where
					a.id=b.mst_id
				and   b.color_size_break_down_id=d.id
				and   a.status_active=1
				and   a.is_deleted=0
				and   b.status_active=1
				and   b.is_deleted=0
				and   d.status_active =1
				and   d.is_deleted=0
				and   a.production_type=4	
				and   b.production_type=4
				 $prod_con
				";
			//   echo $sql;

			$result=sql_select($sql);
			foreach($result as $row){
			$bundle_size_arr[$row[csf('size_number_id')]]=$row[csf('size_number_id')];
			$bundle_color_size_arr[$row[csf('color_number_id')]][$row[csf('size_number_id')]]['qty']+=$row[csf('production_qnty')];

			}
			
			// echo "<pre>";print_r($defect_array);
			$tbl_width = 150+(count($bundle_size_arr)*60);
    //    ?>
       <div style="width:<?=$tbl_width+20;?>px;" align="center">
       <table width="<?=$tbl_width;?>" cellspacing="0" border="1" class="rpt_table" rules="all" >
             <thead>
             	<tr>
                	<th colspan="<?=(2+count($bundle_size_arr));?>">Sewing Input Today Quantity</th>
                </tr>
                <tr>
                    <th width="20">SL</th>
                    <th width="130">Color</th>
					<?
                    foreach($bundle_size_arr as $size_id=>$size_data)
					{
						?>
						<th width="60"><?
						echo $size_library[$size_id];
						?></th>
						<?
					}
					?>
                 </tr>
				 <tbody>

         		<?
		       	// echo "<br>";
		      	// print_r($bundle_color_size_arr);
		        $i=1;
			    foreach($bundle_color_size_arr as $color_id=>$color_data)
			    {
				    $rowspn=count($color_data['size']);
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					?>
					<tr bgcolor="<? echo $bgcolor; ?>">

						<td><? echo $i; ?></td>
						<td><? echo $color_library[$color_id]; ?></td>
						<?
                        foreach($bundle_size_arr as $size_id=>$size_data)
						{
					     	?>
						 	<td align="right"><? echo $color_data[$size_id]['qty'];?></td>
						 	<?
					   	}
					  	?>
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
if ($action=="sewin_popup_total")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$data=explode('*',$data);
	?>
	 	<?
          
		    $prod_date = $txt_date;
			$prod_con = "";
			$prod_con .= ($company_name=="") ? "" : " and a.company_id=".$company_name;
			$prod_con .= ($color_id=="") ? "" : " and d.color_number_id=".$color_id;
			// $prod_con .= ($line_id=="") ? "" : " and a.sewing_line=".$line_id;
			$prod_con .= ($txt_date=="") ? "" : " and a.production_date<='$txt_date'";
			$prod_con .= ($po_id=="") ? "" : " and a.po_break_down_id=".$po_id;
			


			$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name" );
			$size_library=return_library_array( "select id,size_name from lib_size", "id", "size_name" );

			$sql="SELECT a.production_date,b.production_qnty, d.color_number_id, d.size_number_id,a.production_type
			from
				pro_garments_production_mst a,
				pro_garments_production_dtls b,
				wo_po_color_size_breakdown d
			where
					a.id=b.mst_id
				and   b.color_size_break_down_id=d.id
				and   a.status_active=1
				and   a.is_deleted=0
				and   b.status_active=1
				and   b.is_deleted=0
				and   d.status_active =1
				and   d.is_deleted=0
				and   a.production_type=4	
				and   b.production_type=4
				 $prod_con
				";
			//  echo $sql;

			$result=sql_select($sql);
			foreach($result as $row){
			$bundle_size_arr[$row[csf('size_number_id')]]=$row[csf('size_number_id')];
			$bundle_color_size_arr[$row[csf('color_number_id')]][$row[csf('size_number_id')]]['qty']+=$row[csf('production_qnty')];

			}
			
			// echo "<pre>";print_r($defect_array);
			$tbl_width = 150+(count($bundle_size_arr)*60);
    //    ?>
       <div style="width:<?=$tbl_width+20;?>px;" align="center">
       <table width="<?=$tbl_width;?>" cellspacing="0" border="1" class="rpt_table" rules="all" >
             <thead>
             	<tr>
                	<th colspan="<?=(2+count($bundle_size_arr));?>">Sewing Input Today Quantity</th>
                </tr>
                <tr>
                    <th width="20">SL</th>
                    <th width="130">Color</th>
					<?
                    foreach($bundle_size_arr as $size_id=>$size_data)
					{
						?>
						<th width="60"><?
						echo $size_library[$size_id];
						?></th>
						<?
					}
					?>
                 </tr>
				 <tbody>

         		<?
		       	// echo "<br>";
		      	// print_r($bundle_color_size_arr);
		        $i=1;
			    foreach($bundle_color_size_arr as $color_id=>$color_data)
			    {
				    $rowspn=count($color_data['size']);
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					?>
					<tr bgcolor="<? echo $bgcolor; ?>">

						<td><? echo $i; ?></td>
						<td><? echo $color_library[$color_id]; ?></td>
						<?
                        foreach($bundle_size_arr as $size_id=>$size_data)
						{
					     	?>
						 	<td align="right"><? echo $color_data[$size_id]['qty'];?></td>
						 	<?
					   	}
					  	?>
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

if ($action=="sewout_popup_one")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$data=explode('*',$data);
	?>
	 	<?
          

			$prod_con = "";
			$prod_con .= ($company_name=="") ? "" : " and a.company_id=".$company_name;
			$prod_con .= ($color_id=="") ? "" : " and d.color_number_id=".$color_id;
			// $prod_con .= ($line_id=="") ? "" : " and a.sewing_line=".$line_id;
			$prod_con .= ($prod_date=="") ? "" : " and a.production_date='$prod_date'";


			$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name" );
			$size_library=return_library_array( "select id,size_name from lib_size", "id", "size_name" );

			$sql="SELECT a.production_date,b.production_qnty, d.color_number_id, d.size_number_id,a.production_type
			from
				pro_garments_production_mst a,
				pro_garments_production_dtls b,
				wo_po_color_size_breakdown d
			where
					a.id=b.mst_id
				and   b.color_size_break_down_id=d.id
				and   a.status_active=1
				and   a.is_deleted=0
				and   b.status_active=1
				and   b.is_deleted=0
				and   d.status_active =1
				and   d.is_deleted=0
				and   a.production_type=5	
				and   b.production_type=5
				 $prod_con
				";
			//   echo $sql;

			$result=sql_select($sql);
			foreach($result as $row){
			$bundle_size_arr[$row[csf('size_number_id')]]=$row[csf('size_number_id')];
			$bundle_color_size_arr[$row[csf('color_number_id')]][$row[csf('size_number_id')]]['qty']+=$row[csf('production_qnty')];

			}
			
			// echo "<pre>";print_r($defect_array);
			$tbl_width = 150+(count($bundle_size_arr)*60);
    //    ?>
       <div style="width:<?=$tbl_width+20;?>px;" align="center">
       <table width="<?=$tbl_width;?>" cellspacing="0" border="1" class="rpt_table" rules="all" >
             <thead>
             	<tr>
                	<th colspan="<?=(2+count($bundle_size_arr));?>">Sewing Input Today Quantity</th>
                </tr>
                <tr>
                    <th width="20">SL</th>
                    <th width="130">Color</th>
					<?
                    foreach($bundle_size_arr as $size_id=>$size_data)
					{
						?>
						<th width="60"><?
						echo $size_library[$size_id];
						?></th>
						<?
					}
					?>
                 </tr>
				 <tbody>

         		<?
		       	// echo "<br>";
		      	// print_r($bundle_color_size_arr);
		        $i=1;
			    foreach($bundle_color_size_arr as $color_id=>$color_data)
			    {
				    $rowspn=count($color_data['size']);
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					?>
					<tr bgcolor="<? echo $bgcolor; ?>">

						<td><? echo $i; ?></td>
						<td><? echo $color_library[$color_id]; ?></td>
						<?
                        foreach($bundle_size_arr as $size_id=>$size_data)
						{
					     	?>
						 	<td align="right"><? echo $color_data[$size_id]['qty'];?></td>
						 	<?
					   	}
					  	?>
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
if ($action=="sewout_popup_total")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$data=explode('*',$data);
	?>
	 	<?
          
		    $prod_date = $txt_date;
			$prod_con = "";
			$prod_con .= ($company_name=="") ? "" : " and a.company_id=".$company_name;
			$prod_con .= ($color_id=="") ? "" : " and d.color_number_id=".$color_id;
			// $prod_con .= ($line_id=="") ? "" : " and a.sewing_line=".$line_id;
			$prod_con .= ($txt_date=="") ? "" : " and a.production_date<='$txt_date'";
			$prod_con .= ($po_id=="") ? "" : " and a.po_break_down_id=".$po_id;
			


			$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name" );
			$size_library=return_library_array( "select id,size_name from lib_size", "id", "size_name" );

			$sql="SELECT a.production_date,b.production_qnty, d.color_number_id, d.size_number_id,a.production_type
			from
				pro_garments_production_mst a,
				pro_garments_production_dtls b,
				wo_po_color_size_breakdown d
			where
					a.id=b.mst_id
				and   b.color_size_break_down_id=d.id
				and   a.status_active=1
				and   a.is_deleted=0
				and   b.status_active=1
				and   b.is_deleted=0
				and   d.status_active =1
				and   d.is_deleted=0
				and   a.production_type=5
				and   b.production_type=5
				 $prod_con
				";
			//  echo $sql;

			$result=sql_select($sql);
			foreach($result as $row){
			$bundle_size_arr[$row[csf('size_number_id')]]=$row[csf('size_number_id')];
			$bundle_color_size_arr[$row[csf('color_number_id')]][$row[csf('size_number_id')]]['qty']+=$row[csf('production_qnty')];

			}
			
			// echo "<pre>";print_r($defect_array);
			$tbl_width = 150+(count($bundle_size_arr)*60);
    //    ?>
       <div style="width:<?=$tbl_width+20;?>px;" align="center">
       <table width="<?=$tbl_width;?>" cellspacing="0" border="1" class="rpt_table" rules="all" >
             <thead>
             	<tr>
                	<th colspan="<?=(2+count($bundle_size_arr));?>">Sewing Output Total Quantity</th>
                </tr>
                <tr>
                    <th width="20">SL</th>
                    <th width="130">Color</th>
					<?
                    foreach($bundle_size_arr as $size_id=>$size_data)
					{
						?>
						<th width="60"><?
						echo $size_library[$size_id];
						?></th>
						<?
					}
					?>
                 </tr>
				 <tbody>

         		<?
		       	// echo "<br>";
		      	// print_r($bundle_color_size_arr);
		        $i=1;
			    foreach($bundle_color_size_arr as $color_id=>$color_data)
			    {
				    $rowspn=count($color_data['size']);
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					?>
					<tr bgcolor="<? echo $bgcolor; ?>">

						<td><? echo $i; ?></td>
						<td><? echo $color_library[$color_id]; ?></td>
						<?
                        foreach($bundle_size_arr as $size_id=>$size_data)
						{
					     	?>
						 	<td align="right"><? echo $color_data[$size_id]['qty'];?></td>
						 	<?
					   	}
					  	?>
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

if ($action=="line_balance_popup")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$data=explode('*',$data);
	?>
	 	<?
          
		    $prod_date = $txt_date;
			$prod_con = "";
			$prod_con .= ($company_name=="") ? "" : " and a.company_id=".$company_name;
			$prod_con .= ($color_id=="") ? "" : " and d.color_number_id=".$color_id;
			// $prod_con .= ($line_id=="") ? "" : " and a.sewing_line=".$line_id;
			$prod_con .= ($txt_date=="") ? "" : " and a.production_date<='$txt_date'";
			$prod_con .= ($po_id=="") ? "" : " and a.po_break_down_id=".$po_id;
			


			$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name" );
			$size_library=return_library_array( "select id,size_name from lib_size", "id", "size_name" );

			$sql="SELECT a.production_date,b.production_qnty, d.color_number_id, d.size_number_id,a.production_type
			from
				pro_garments_production_mst a,
				pro_garments_production_dtls b,
				wo_po_color_size_breakdown d
			where
					a.id=b.mst_id
				and   b.color_size_break_down_id=d.id
				and   a.status_active=1
				and   a.is_deleted=0
				and   b.status_active=1
				and   b.is_deleted=0
				and   d.status_active =1
				and   d.is_deleted=0
				and   a.production_type=5
				and   b.production_type=5
				 $prod_con
				";
			//  echo $sql;

			$result=sql_select($sql);
			foreach($result as $row){
			$bundle_size_arr[$row[csf('size_number_id')]]=$row[csf('size_number_id')];
			$bundle_color_size_arr[$row[csf('color_number_id')]][$row[csf('size_number_id')]]['qty']+=$row[csf('production_qnty')];

			}
			
			$sewin_sql="SELECT a.production_date,b.production_qnty, d.color_number_id, d.size_number_id,a.production_type
			from
				pro_garments_production_mst a,
				pro_garments_production_dtls b,
				wo_po_color_size_breakdown d
			where
					a.id=b.mst_id
				and   b.color_size_break_down_id=d.id
				and   a.status_active=1
				and   a.is_deleted=0
				and   b.status_active=1
				and   b.is_deleted=0
				and   d.status_active =1
				and   d.is_deleted=0
				and   a.production_type=4
				and   b.production_type=4
				 $prod_con
				";
			//  echo $sewin_sql;

			$sew_result=sql_select($sewin_sql);
			foreach($sew_result as $row)
			{	
				$sewin_size_arr[$row[csf('color_number_id')]][$row[csf('size_number_id')]]['qty']+=$row[csf('production_qnty')];

			}


			$tbl_width = 150+(count($bundle_size_arr)*60);
    //    ?>
       <div style="width:<?=$tbl_width+20;?>px;" align="center">
       <table width="<?=$tbl_width;?>" cellspacing="0" border="1" class="rpt_table" rules="all" >
             <thead>
             	<tr>
                	<th colspan="<?=(2+count($bundle_size_arr));?>">Sewing Output Total Quantity</th>
                </tr>
                <tr>
                    <th width="20">SL</th>
                    <th width="130">Color</th>
					<?
                    foreach($bundle_size_arr as $size_id=>$size_data)
					{
						?>
						<th width="60"><?
						echo $size_library[$size_id];
						?></th>
						<?
					}
					?>
                 </tr>
				 <tbody>

         		<?
		       	// echo "<br>";
		      	// print_r($bundle_color_size_arr);
		        $i=1;
			    foreach($bundle_color_size_arr as $color_id=>$color_data)
			    {
				    $rowspn=count($color_data['size']);
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					?>
					<tr bgcolor="<? echo $bgcolor; ?>">

						<td><? echo $i; ?></td>
						<td><? echo $color_library[$color_id]; ?></td>
						<?
                        foreach($bundle_size_arr as $size_id=>$size_data)
						{
					     	?>
						 	<td align="right"><? $line_balance=$sewin_size_arr[$color_id][$size_id]['qty']-$color_data[$size_id]['qty']; echo $line_balance;?></td>
						 	<?
					   	}
					  	?>
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
if ($action=="po_balance_popup")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$data=explode('*',$data);
	?>
	 	<?
          
		    $prod_date = $txt_date;
			$prod_con = "";
			$prod_con .= ($company_name=="") ? "" : " and a.company_id=".$company_name;
			$prod_con .= ($color_id=="") ? "" : " and d.color_number_id=".$color_id;
			// $prod_con .= ($line_id=="") ? "" : " and a.sewing_line=".$line_id;
			$prod_con .= ($txt_date=="") ? "" : " and a.production_date<='$txt_date'";
			$prod_con .= ($po_id=="") ? "" : " and a.po_break_down_id=".$po_id;
			


			$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name" );
			$size_library=return_library_array( "select id,size_name from lib_size", "id", "size_name" );

			$sql="SELECT a.production_date, d.color_number_id, d.size_number_id,a.production_type,
			      (CASE WHEN a.production_type=5 and b.production_type=5 THEN b.production_qnty ELSE 0 END)
				  as sewing_output,d.order_quantity
			from
				pro_garments_production_mst a,
				pro_garments_production_dtls b,
				wo_po_color_size_breakdown d
			where
					a.id=b.mst_id
				and   b.color_size_break_down_id=d.id
				and   a.status_active=1
				and   a.is_deleted=0
				and   b.status_active=1
				and   b.is_deleted=0
				and   d.status_active =1
				and   d.is_deleted=0
				and   a.production_type IN (4,5)
				and   b.production_type IN (4,5)
				 $prod_con
				";
			//   echo $sql;

			$result=sql_select($sql);
			foreach($result as $row){
				$bundle_size_arr[$row[csf('size_number_id')]]=$row[csf('size_number_id')];
				$bundle_color_size_arr[$row[csf('color_number_id')]][$row[csf('size_number_id')]]['qty']+=$row[csf('sewing_output')];
				$bundle_color_size_arr[$row[csf('color_number_id')]][$row[csf('size_number_id')]]['order_qty']+=$row[csf('order_quantity')];

			}
			
			// echo "<pre>";print_r($defect_array);
			$tbl_width = 150+(count($bundle_size_arr)*60);
    //    ?>
       <div style="width:<?=$tbl_width+20;?>px;" align="center">
       <table width="<?=$tbl_width;?>" cellspacing="0" border="1" class="rpt_table" rules="all" >
             <thead>
             	<tr>
                	<th colspan="<?=(2+count($bundle_size_arr));?>">Sewing Output Total Quantity</th>
                </tr>
                <tr>
                    <th width="20">SL</th>
                    <th width="130">Color</th>
					<?
                    foreach($bundle_size_arr as $size_id=>$size_data)
					{
						?>
						<th width="60"><?
						echo $size_library[$size_id];
						?></th>
						<?
					}
					?>
                 </tr>
				 <tbody>

         		<?
		       	// echo "<br>";
		      	// print_r($bundle_color_size_arr);
		        $i=1;
			    foreach($bundle_color_size_arr as $color_id=>$color_data)
			    {
				    $rowspn=count($color_data['size']);
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					?>
					<tr bgcolor="<? echo $bgcolor; ?>">

						<td><? echo $i; ?></td>
						<td><? echo $color_library[$color_id]; ?></td>
						<?
                        foreach($bundle_size_arr as $size_id=>$size_data)
						{
					     	?>
						 	<td align="right"><? $po_balance=$color_data[$size_id]['order_qty']-$color_data[$size_id]['qty']; echo $po_balance;?></td>
						 	<?
					   	}
					  	?>
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





if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$companyArr = return_library_array("select id,company_name from lib_company","id","company_name");
	$buyerArr = return_library_array("select id,short_name from lib_buyer","id","short_name");
	$locationArr = return_library_array("select id,location_name from lib_location","id","location_name");
	$floorArr = return_library_array("select id,floor_name from lib_prod_floor","id","floor_name");
	$color_library=return_library_array( "select id,color_name from lib_color ", "id", "color_name"  );
	$line_library=return_library_array( "select id,line_name from lib_sewing_line ", "id", "line_name"  );
	$sewLineArr = return_library_array("select id,line_name from lib_sewing_line order by id","id","line_name");

	$lineArr=array(); $lineSerialArr=array(); $lastSlNo='';
	$lineDataArr = sql_select("select id, line_name, sewing_line_serial from lib_sewing_line where is_deleted=0 and status_active=1 order by sewing_line_serial");
	foreach($lineDataArr as $lRow)
	{
		$lineArr[$lRow[csf('id')]]=$lRow[csf('line_name')];
		$lineSerialArr[$lRow[csf('id')]]=$lRow[csf('sewing_line_serial')];
		$lastSlNo=$lRow[csf('sewing_line_serial')];
	}

	$type=str_replace("'","",$type);
	$comapny_id=str_replace("'","",$cbo_company_id);
	$wo_company_id=str_replace("'","",$cbo_wo_company_id);
	$cbo_floor_id=str_replace("'","",$cbo_floor_id);

	$prod_reso_allo=return_field_value("auto_update","variable_settings_production","company_name=$wo_company_id and variable_list=23 and is_deleted=0 and status_active=1");

	if(str_replace("'","",$cbo_buyer_name)==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="")
			{
				$buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")";
				$sub_buyer_id_cond=" and b.buyer_buyer in (".$_SESSION['logic_erp']["buyer_id"].")";
			}
			else
			{
				$buyer_id_cond="";
				$sub_buyer_id_cond="";
			}
		}
		else
		{
			$buyer_id_cond="";
			$sub_buyer_id_cond="";
		}
	}
	else
	{
		$buyer_id_cond=" and a.buyer_name=$cbo_buyer_name";
		$sub_buyer_id_cond=" and b.buyer_buyer=$cbo_buyer_name";
	}

	if($wo_company_id=="") $wo_company_cond=""; else $wo_company_cond=" and c.serving_company in($wo_company_id)";
	if($wo_company_id=="") $dtls_wo_company_cond=""; else $dtls_wo_company_cond=" and a.company_id in($wo_company_id)";

	if(empty($comapny_id)) $company_cond=""; else $company_cond=" and a.company_name = $comapny_id";
	if(empty($comapny_id)) $sub_company_cond=""; else $sub_company_cond=" and a.company_id = $comapny_id";
	if(empty($comapny_id)) $dtls_company_cond=""; else $dtls_company_cond=" and a.company_id=$comapny_id";

	if(str_replace("'","",$cbo_location_id)==0) $location="%%"; else $location=str_replace("'","",$cbo_location_id);

	if($cbo_floor_id=="") $floor=""; else $floor="and c.floor_id in($cbo_floor_id)";
	if($cbo_floor_id=="") $floor_con=""; else $floor_con="and  floor_id in(".$cbo_floor_id.")";
	if($cbo_floor_id=="") $floor_con3=""; else $floor_con3="  and a.floor_id  in(".$cbo_floor_id.")";

    if(str_replace("'","",$cbo_line)==0) $line="%%"; else $line=str_replace("'","",$cbo_line);

	if($type==1) // show btn start
	{
		$costing_per_arr = return_library_array("select job_no,costing_per from wo_pre_cost_mst","job_no","costing_per");
		$tot_cost_arr = return_library_array("select job_no, cm_for_sipment_sche from wo_pre_cost_dtls","job_no","cm_for_sipment_sche");
		$prod_reso_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');

		if($db_type==0)
		{
			$job_po_id_arr=return_library_array( "select job_no_mst, group_concat(id) as po_id from wo_po_break_down group by job_no_mst",'job_no_mst','po_id');
		}
		else
		{
			$job_po_id_arr=return_library_array("SELECT job_no_mst,LISTAGG(id,',') WITHIN GROUP (ORDER BY id) as po_id from wo_po_break_down group by job_no_mst",'job_no_mst','po_id');
		}

		$prod_resource_array=array();
		$dataArray=sql_select("SELECT a.id, a.location_id, a.floor_id, a.line_number, b.active_machine, b.pr_date, b.man_power, b.operator, b.helper, b.line_chief, b.smv_adjust, b.smv_adjust_type, b.target_per_hour, b.working_hour from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id $dtls_company_cond $dtls_wo_company_cond and b.pr_date=$txt_date and b.is_deleted=0");

		//$dataArray_for_subcon=sql_select("SELECT a.id, a.location_id, a.floor_id, a.line_number, b.active_machine, b.pr_date, b.man_power, b.operator, b.helper, b.smv_adjust, b.smv_adjust_type, b.line_chief, b.target_per_hour, b.working_hour, c.from_date, c.to_date, c.capacity as mc_capacity from prod_resource_mst a, prod_resource_dtls b, prod_resource_dtls_mast c where a.id=c.mst_id and a.id=b.mst_id and c.id=b.mast_dtl_id and a.company_id in($comapny_id) and pr_date=$txt_date");// and a.id=1 and c.from_date=$txt_date

		foreach($dataArray as $row)
		{
			$conv_pr_date=change_date_format($row[csf('pr_date')]);
			$prod_resource_array[$row[csf('id')]][$conv_pr_date]['man_power']=$row[csf('man_power')];
			$prod_resource_array[$row[csf('id')]][$conv_pr_date]['operator']=$row[csf('operator')];
			$prod_resource_array[$row[csf('id')]][$conv_pr_date]['helper']=$row[csf('helper')];
			$prod_resource_array[$row[csf('id')]][$conv_pr_date]['line_chief']=$row[csf('line_chief')];
			$prod_resource_array[$row[csf('id')]][$conv_pr_date]['active_machine']=$row[csf('active_machine')];
			$prod_resource_array[$row[csf('id')]][$conv_pr_date]['tpd']=$row[csf('target_per_hour')]*$row[csf('working_hour')];
			$prod_resource_array[$row[csf('id')]][$conv_pr_date]['tsmv']=$row[csf('man_power')]*$row[csf('working_hour')]*60;
			$prod_resource_array[$row[csf('id')]][$conv_pr_date]['smv_adjust']=$row[csf('smv_adjust')];
			$prod_resource_array[$row[csf('id')]][$conv_pr_date]['smv_adjust_type']=$row[csf('smv_adjust_type')];
		}
		$pr_date=change_date_format(str_replace("'","",$txt_date));

		if(str_replace("'","",$cbo_source)!=0){$source_con=" and c.production_source=$cbo_source";}
		else{$source_con="";}

	    /*===================================================================================== /
	    /								get inhouse production data					           /
	    /===================================================================================== */
		if($db_type==0)
		{
			$sql="SELECT a.job_no, a.company_name, a.buyer_name, a.style_ref_no, a.order_uom, a.gmts_item_id, a.set_break_down, a.total_set_qnty as ratio, group_concat(distinct(b.id)) as po_id, group_concat(concat_ws('**',b.id,b.po_number,b.po_quantity,b.unit_price)) as po_data, c.item_number_id, c.location, c.floor_id, c.prod_reso_allo, c.sewing_line, sum(c.production_quantity) as qnty from wo_po_details_master a, wo_po_break_down b, pro_garments_production_mst c where a.job_no=b.job_no_mst $company_cond and b.id=c.po_break_down_id $wo_company_cond and c.location like '$location'  and c.sewing_line like '$line' and production_date=$txt_date and production_type=5 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $floor $source_con $buyer_id_cond group by a.job_no, c.location, c.floor_id, c.prod_reso_allo, c.sewing_line order by c.location, c.floor_id";//, c.item_number_id
		}
		else
		{
			$sql="SELECT a.job_no, a.company_name, a.buyer_name, a.style_ref_no, b.grouping, a.order_uom, a.gmts_item_id, a.set_break_down, a.total_set_qnty as ratio, LISTAGG(b.id, ',') WITHIN GROUP (ORDER BY b.id) as po_id, LISTAGG(cast(b.id || '**' || b.po_number || '**' || b.po_quantity || '**' || b.unit_price as varchar2(4000)), ',') WITHIN GROUP (ORDER BY b.id) as po_data, c.item_number_id, c.location, c.floor_id, c.prod_reso_allo, c.sewing_line, sum(c.production_quantity) as qnty from wo_po_details_master a, wo_po_break_down b, pro_garments_production_mst c where a.job_no=b.job_no_mst $company_cond and b.id=c.po_break_down_id $wo_company_cond and c.location like '$location'  and c.sewing_line like '$line' and production_date=$txt_date and production_type=5 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $source_con $buyer_id_cond $floor group by a.job_no, a.company_name, a.buyer_name, a.style_ref_no, b.grouping, a.order_uom, a.gmts_item_id, a.set_break_down, a.total_set_qnty, c.location, c.floor_id, c.prod_reso_allo, c.sewing_line, c.item_number_id order by c.location, c.floor_id";//, c.sewing_line
		}
		// echo $sql;
		$line_data_array=array();
		$job_arr=array();
		$result = sql_select($sql);
		foreach($result as $row )
		{
			$sewing_line_id='';
			if($row[csf('prod_reso_allo')]==1)
			{
				$sewing_line_id=$prod_reso_arr[$row[csf('sewing_line')]];
			}
			else
			{
				$sewing_line_id=$row[csf('sewing_line')];
			}

			if($lineSerialArr[$sewing_line_id]=="")
			{
				$lastSlNo++;
				$slNo=$lastSlNo;
				$lineSerialArr[$sewing_line_id];
			}
			else $slNo=$lineSerialArr[$sewing_line_id];

			//echo $slNo."**".$sewing_line_id."<br>";
			$po_data=implode(",",array_unique(explode(",",$row[csf('po_data')])));
			//echo $po_data."<br>";
			$line_data_array[$row[csf('location')]][$row[csf('floor_id')]][$slNo].=$row[csf('job_no')]."##".$row[csf('company_name')]."##".$row[csf('buyer_name')]."##".$row[csf('style_ref_no')]."##".$row[csf('grouping')]."##".$row[csf('order_uom')]."##".$row[csf('gmts_item_id')]."##".$row[csf('set_break_down')]."##".$row[csf('ratio')]."##".$row[csf('po_id')]."##".$row[csf('item_number_id')]."##".$row[csf('prod_reso_allo')]."##".$row[csf('sewing_line')]."##".$row[csf('qnty')]."##".$po_data.":";
		}
        // echo "<pre>"; print_r($line_data_array); echo "</pre>";die();
		/*===================================================================================== /
	    /										subcoutact data									/
	    /===================================================================================== */
	    $sql_subcon="SELECT a.subcon_job as job_no, a.COMPANY_ID as company_name, b.buyer_buyer as buyer_name, b.buyer_style_ref as style_ref_no, 0 as grouping, b.order_uom,  0 as set_break_down, 1 as ratio, LISTAGG(b.id, ',') WITHIN GROUP (ORDER BY b.id) as po_id, LISTAGG(cast(b.id || '**' || b.order_no || '**' || b.order_quantity || '**' || b.unit_price as varchar2(4000)), ',') WITHIN GROUP (ORDER BY b.id) as po_data, b.gmts_item_id,c.gmts_item_id as item_number_id, c.location_id as location, c.floor_id, c.prod_reso_allo, c.line_id as sewing_line, sum(c.production_qnty) as qnty from subcon_ord_mst a, subcon_ord_dtls b, subcon_gmts_prod_dtls c where a.subcon_job=b.job_no_mst $sub_company_cond and b.id=c.order_id and c.location_id like '$location'  and c.line_id like '$line' and production_date=$txt_date and production_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  $sub_buyer_id_cond $floor GROUP BY a.subcon_job, a.COMPANY_ID, b.buyer_buyer, b.buyer_style_ref, b.order_uom, b.gmts_item_id,c.gmts_item_id, c.location_id, c.floor_id, c.prod_reso_allo, c.line_id";//, c.sewing_line
	    // echo $sql_subcon;die();
	    $subcon_result = sql_select($sql_subcon);
	    foreach($subcon_result as $row )
		{
			$sewing_line_id='';
			if($row[csf('prod_reso_allo')]==1)
			{
				$sewing_line_id=$prod_reso_arr[$row[csf('sewing_line')]];
			}
			else
			{
				$sewing_line_id=$row[csf('sewing_line')];
			}

			if($lineSerialArr[$sewing_line_id]=="")
			{
				$lastSlNo++;
				$slNo=$lastSlNo;
				$lineSerialArr[$sewing_line_id];
			}
			else $slNo=$lineSerialArr[$sewing_line_id];

			//echo $slNo."**".$sewing_line_id."<br>";
			$po_data=implode(",",array_unique(explode(",",$row[csf('po_data')])));
			//echo $po_data."<br>";
			$line_data_array[$row[csf('location')]][$row[csf('floor_id')]][$slNo].=$row[csf('job_no')]."##".$row[csf('company_name')]."##".$row[csf('buyer_name')]."##".$row[csf('style_ref_no')]."##".$row[csf('grouping')]."##".$row[csf('order_uom')]."##".$row[csf('gmts_item_id')]."##".$row[csf('set_break_down')]."##".$row[csf('ratio')]."##".$row[csf('po_id')]."##".$row[csf('item_number_id')]."##".$row[csf('prod_reso_allo')]."##".$row[csf('sewing_line')]."##".$row[csf('qnty')]."##".$po_data.":";
		}

		// echo "<pre>"; print_r($line_data_array); echo "</pre>";die();

		$i=1; $k=1; $html=''; $buyer_data_array=array();
		$total_order_qnty=0; $total_machine_qnty=0; $tot_worker=0; $tot_operator=0; $tot_helper=0; $tot_prev_input_qnty=0; $tot_prev_out_qnty=0;
		$tot_prev_wip=0; $tot_today_target=0; $tot_today_input_qnty=0; $tot_today_out_qnty=0; $tot_today_smv=0; $tot_item_smv=0; $tot_achv_smv=0;
		$tot_cm_value=0; $grand_tot_prod=0; $tot_wip=0; $grand_tot_smv_used=0; $grand_tot_achv_smv=0;

		foreach($line_data_array as $location=>$locData )
		{
			$location_order_qnty=0;
			$location_machine_qnty=0;
			$location_worker=0;
			$location_operator=0;
			$location_helper=0;
			$location_prev_input_qnty=0;
			$location_prev_out_qnty=0;
			$location_prev_wip=0;
			$location_today_target=0;
			$location_today_input_qnty=0;
			$location_today_out_qnty=0;
			$location_today_smv=0;
			$location_item_smv=0;
			$location_achv_smv=0;
			$location_cm_value=0;
			$location_tot_prod=0;
			$location_wip=0;
			$location_tot_smv_used=0;
			$location_tot_achv_smv=0;
			foreach($locData as $floor=>$floorData )
			{
				$html.='<tr bgcolor="#EFEFEF"><td colspan="36"><b>Floor name: '.$floorArr[$floor].'; Location name: '.$locationArr[$location].'</b></td></tr>';

				$floor_order_qnty=0;
				$floor_machine_qnty=0;
				$floor_worker=0;
				$floor_operator=0;
				$floor_helper=0;
				$floor_prev_input_qnty=0;
				$floor_prev_out_qnty=0;
				$floor_prev_wip=0;
				$floor_today_target=0;
				$floor_today_input_qnty=0;
				$floor_today_out_qnty=0;
				$floor_today_smv=0;
				$floor_item_smv=0;
				$floor_achv_smv=0;
				$floor_cm_value=0;
				$floor_tot_prod=0;
				$floor_wip=0;
				$floor_tot_smv_used=0;
				$floor_tot_achv_smv=0;

				ksort($floorData);
				foreach($floorData as $slNo=>$lineData )
				{
					$lineSlData=explode(":",chop($lineData,':'));
					foreach($lineSlData as $value )
					{
						$lineDataArr=explode("##",$value);
						$row[csf('job_no')]=$lineDataArr[0];
						$row[csf('company_name')]=$lineDataArr[1];
						$row[csf('buyer_name')]=$lineDataArr[2];
						$row[csf('style_ref_no')]=$lineDataArr[3];
						$row[csf('grouping')]=$lineDataArr[4];
						$row[csf('order_uom')]=$lineDataArr[5];
						$row[csf('gmts_item_id')]=$lineDataArr[6];
						$row[csf('set_break_down')]=$lineDataArr[7];
						$row[csf('ratio')]=$lineDataArr[8];
						$row[csf('po_id')]=$lineDataArr[9];
						$row[csf('item_number_id')]=$lineDataArr[10];
						$row[csf('prod_reso_allo')]=$lineDataArr[11];
						$row[csf('sewing_line')]=$lineDataArr[12];
						$row[csf('qnty')]=$lineDataArr[13];
						$row[csf('po_data')]=$lineDataArr[14];

						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

						$po_number=''; $unit_price=''; $po_quantity=0; $item_smv=0; $po_array=array();

						$exp_grmts_item = explode("__",$row[csf("set_break_down")]);
						foreach($exp_grmts_item as $value)
						{
							$grmts_item_qty = explode("_",$value);
							if($row[csf('item_number_id')]==$grmts_item_qty[0])
							{
								$set_qty=$grmts_item_qty[1];
								$item_smv=$grmts_item_qty[2];
								break;
							}
						}

						$po_data = explode(",",$row[csf("po_data")]);
						foreach($po_data as $val)
						{
							$po_val=explode("**",$val);
							$po_array[$po_val[0]]['no']=$po_val[1];
							$po_array[$po_val[0]]['qnty']=$po_val[2];
							$po_array[$po_val[0]]['unit_price']=$po_val[3];
						}

						$po_ids = array_unique(explode(",",$row[csf("po_id")]));
						foreach($po_ids as $id)
						{
							if($po_number=="") $po_number=$po_array[$id]['no']; else $po_number.=",".$po_array[$id]['no'];
							if($unit_price=="") $unit_price=$po_array[$id]['unit_price']; else $unit_price.=",".$po_array[$id]['unit_price'];
							$po_quantity+=$po_array[$id]['qnty']*$set_qty;
						}

						$job_po_id=$row[csf("po_id")];
						if($job_po_id!="") $job_po_id=$job_po_id;else $job_po_id=0;
						$mst_sql= "SELECT
										min(CASE WHEN production_type ='4' THEN production_date END) AS frstinput_date,
										sum(CASE WHEN production_type ='4' and production_date=$txt_date and po_break_down_id in($job_po_id) THEN production_quantity ELSE 0 END) AS today_input_qnty,
										sum(CASE WHEN production_type ='4' and production_date<$txt_date THEN production_quantity ELSE 0 END) AS prev_input_qnty,
										sum(CASE WHEN production_type ='5' and production_date<$txt_date THEN production_quantity ELSE 0 END) AS prev_out_qnty
									from
										pro_garments_production_mst
									where
										po_break_down_id in($job_po_id) and item_number_id='".$row[csf("item_number_id")]."' and location='".$location."' and prod_reso_allo='".$row[csf("prod_reso_allo")]."' and sewing_line='".$row[csf("sewing_line")]."' and is_deleted=0 and status_active=1 $floor_con
                                    UNION ALL

                                    SELECT
										min(CASE WHEN production_type ='7' THEN production_date END) AS frstinput_date,
										sum(CASE WHEN production_type ='7' and production_date=$txt_date and order_id in($job_po_id) THEN production_qnty ELSE 0 END) AS today_input_qnty,
										sum(CASE WHEN production_type ='7' and production_date<$txt_date THEN production_qnty ELSE 0 END) AS prev_input_qnty,
										sum(CASE WHEN production_type ='2' and production_date<$txt_date THEN production_qnty ELSE 0 END) AS prev_out_qnty
									from
										subcon_gmts_prod_dtls
									where
										order_id in($job_po_id) and gmts_item_id='".$row[csf("item_number_id")]."' and location_id='".$location."' and prod_reso_allo='".$row[csf("prod_reso_allo")]."' and line_id='".$row[csf("sewing_line")]."' and is_deleted=0 and status_active=1 $floor_con
										";
						// echo $mst_sql."<br>";
						$dataArray = sql_select($mst_sql);
						$fstinput_date=$dataArray[0][csf('frstinput_date')];
						$prev_input_qnty=$dataArray[0][csf('prev_input_qnty')];
						$prev_out_qnty=$dataArray[0][csf('prev_out_qnty')];
						$prev_wip=$prev_input_qnty-$prev_out_qnty;
						$today_input_qnty=$dataArray[0][csf('today_input_qnty')];

						$today_ach_perc=$row[csf('qnty')]/$prod_resource_array[$row[csf('sewing_line')]][$pr_date]['tpd']*100;

						$total_adjustment=0;
						$smv_adjustmet_type=$prod_resource_array[$row[csf('sewing_line')]][$pr_date]['smv_adjust_type'];
						if(str_replace("'","",$smv_adjustmet_type)==1) $total_adjustment=$prod_resource_array[$row[csf('sewing_line')]][$pr_date]['smv_adjust'];
						if(str_replace("'","",$smv_adjustmet_type)==2) $total_adjustment=($prod_resource_array[$row[csf('sewing_line')]][$pr_date]['smv_adjust'])*(-1);

						$today_smv=$prod_resource_array[$row[csf('sewing_line')]][$pr_date]['tsmv']+$total_adjustment;
						$achv_smv=$row[csf('qnty')]*$item_smv;
						//	$achv_smv=number_format($achv_smv,2);
						$today_aff_perc=$achv_smv/$today_smv*100;
						$total_prod=$row[csf('qnty')]+$prev_out_qnty;
						$wip=$prev_input_qnty+$today_input_qnty-$total_prod;

					   // $no_of_days=return_field_value("count(id)","lib_capacity_calc_dtls","date_calc between '$fstinput_date' and $txt_date and day_status=1");
						$dataLibArray=sql_select("select b.date_calc from lib_capacity_calc_mst a, lib_capacity_calc_dtls b where a.id=b.mst_id and a.comapny_id='$comapny_id' and b.date_calc between '$fstinput_date' and $txt_date and day_status=1");
						$no_of_days=0; $total_smv_used=0; $total_adjust=0;
						foreach($dataLibArray as $libRow)
						{
							$date_calc=change_date_format($libRow[csf('date_calc')]);
							$smv_adjustmet_type=$prod_resource_array[$row[csf('sewing_line')]][$date_calc]['smv_adjust_type'];
							if(str_replace("'","",$smv_adjustmet_type)==1) $total_adjust=$prod_resource_array[$row[csf('sewing_line')]][$date_calc]['smv_adjust'];
							else if(str_replace("'","",$smv_adjustmet_type)==2) $total_adjust=($prod_resource_array[$row[csf('sewing_line')]][$date_calc]['smv_adjust'])*(-1);
							else $total_adjust=0;
							$total_smv_used+=$prod_resource_array[$row[csf('sewing_line')]][$date_calc]['tsmv']+$total_adjust;
							$no_of_days++;
						}
						//$total_smv_used=$today_smv*$no_of_days;
						$avg_per_day=$total_prod/$no_of_days;
						$total_smv_achv=$item_smv*$total_prod;
						$avg_aff_perc=$total_smv_achv/$total_smv_used*100;

						$dzn_qnty=0; $cm_value=0;
						if($costing_per_arr[$row[csf('job_no')]]==1) $dzn_qnty=12;
						else if($costing_per_arr[$row[csf('job_no')]]==3) $dzn_qnty=12*2;
						else if($costing_per_arr[$row[csf('job_no')]]==4) $dzn_qnty=12*3;
						else if($costing_per_arr[$row[csf('job_no')]]==5) $dzn_qnty=12*4;
						else $dzn_qnty=1;

						$dzn_qnty=$dzn_qnty*$row[csf('ratio')];
						$cm_value=($tot_cost_arr[$row[csf('job_no')]]/$dzn_qnty)*$row[csf('qnty')];

						$actual_line_arr.=$row[csf('sewing_line')].",";
						$sewing_line='';
						if($row[csf('prod_reso_allo')]==1)
						{
							$line_number=explode(",",$prod_reso_arr[$row[csf('sewing_line')]]);
							foreach($line_number as $val)
							{
								if($sewing_line=='') $sewing_line=$lineArr[$val]; else $sewing_line.=",".$lineArr[$val];
							}
						}
						else $sewing_line=$lineArr[$row[csf('sewing_line')]];

						$po_number=implode(",",array_unique(explode(",",$po_number)));
						$unit_price=implode(",",array_unique(explode(",",$unit_price)));
						$html.="<tr bgcolor='$bgcolor' onclick=change_color('tr_$i','$bgcolor') id=tr_$i>";
						$html.='<td valign="middle" width="40" align="center">'.$i.'</td>
								<td valign="middle" width="50"><p>'.$sewing_line.'</p></td>
								<td valign="middle" width="70"><p>'.$buyerArr[$row[csf('buyer_name')]].'</p></td>
								<td valign="middle" width="110"><p>'.$po_number.'</p></td>
								<td valign="middle" width="110"><p>'.$row[csf('job_no')].'</p></td>
								<td valign="middle" width="110"><p>'.$row[csf('style_ref_no')].'</p></td>
								<td valign="middle" width="110"><p>'.$row[csf('grouping')].'</p></td>
								<td valign="middle" width="140"><p>'.$garments_item[$row[csf('item_number_id')]].'</p></td>
								<td valign="middle" width="75" align="right">'.$po_quantity.'</td>
								<td valign="middle" width="75" align="right">'.$unit_price.'</td>
								<td valign="middle" width="70" align="right">'.$prod_resource_array[$row[csf('sewing_line')]][$pr_date]['active_machine'].'</td>
								<td valign="middle" width="60" align="right">'.$prod_resource_array[$row[csf('sewing_line')]][$pr_date]['man_power'].'</td>
								<td valign="middle" width="60" align="right">'.$prod_resource_array[$row[csf('sewing_line')]][$pr_date]['operator'].'</td>
								<td valign="middle" width="60" align="right">'.$prod_resource_array[$row[csf('sewing_line')]][$pr_date]['helper'].'</td>
								<td valign="middle" width="120"><p>'.$prod_resource_array[$row[csf('sewing_line')]][$pr_date]['line_chief'].'</p></td>
								<td valign="middle" width="80" align="center">'.change_date_format($fstinput_date).'</td>
								<td valign="middle" width="75" align="right"><a href="##" onclick="openmypage('.$comapny_id.",'".$row[csf('po_id')]."',".$row[csf('item_number_id')].",".$row[csf('location')].",".$floor.",".$row[csf('sewing_line')].",".$txt_date.",'prev_qnty',4".",".$row[csf('prod_reso_allo')].')">'.$prev_input_qnty.'</a></td>
								<td valign="middle" width="75" align="right"><a href="##" onclick="openmypage('.$comapny_id.",'".$row[csf('po_id')]."',".$row[csf('item_number_id')].",".$row[csf('location')].",".$floor.",".$row[csf('sewing_line')].",".$txt_date.",'prev_qnty',5".",".$row[csf('prod_reso_allo')].')">'.$prev_out_qnty.'</a></td>
								<td valign="middle" width="75" align="right">'.$prev_wip.'</td>
								<td valign="middle" width="75" align="right">'.$prod_resource_array[$row[csf('sewing_line')]][$pr_date]['tpd'].'</td>
								<td valign="middle" width="75" align="right">'.$today_input_qnty.'</td>
								<td valign="middle" width="75" align="right"><a href="##" onclick="openmypage('.$comapny_id.",'".$row[csf('po_id')]."',".$row[csf('item_number_id')].",".$row[csf('location')].",".$floor.",".$row[csf('sewing_line')].",".$txt_date.",'today_prod',5".",".$row[csf('prod_reso_allo')].')">'.$row[csf('qnty')].'</a></td>
								<td valign="middle" width="75" align="right">'.number_format($today_ach_perc,2).'</td>
								<td valign="middle" width="75" align="right">'.$today_smv.'</td>
								<td valign="middle" width="70" align="right">'.$item_smv.'</td>
								<td valign="middle" width="100" align="right">'.number_format($achv_smv,2).'</td>
								<td valign="middle" width="75" align="right">'.number_format($today_aff_perc,2).'</td>
								<td valign="middle" width="100" align="right">'.number_format($tot_cost_arr[$row[csf('job_no')]]).'</td>
								<td valign="middle" width="110" align="right">'.number_format($cm_value,2,'.','').'</td>
								<td valign="middle" width="90" align="right"><a href="##" onclick="openmypage('.$comapny_id.",'".$job_po_id."',".$row[csf('item_number_id')].",".$location.",".$floor.",".$row[csf('sewing_line')].",".$txt_date.",'tot_prod',5".",".$row[csf('prod_reso_allo')].')">'.$total_prod.'</a></td>
								<td valign="middle" width="80" align="right">'.number_format($avg_per_day,2,'.','').'</td>
								<td valign="middle" width="80" align="right">'.$wip.'</td>
								<td valign="middle" width="100" align="right"><a href="##" onclick="openmypage('.$comapny_id.",'".$row[csf('po_id')]."',".$row[csf('item_number_id')].",".$location.",".$floor.",".$row[csf('sewing_line')].",".$txt_date.",'tot_smv_used',"."'".$fstinput_date."'".",".$row[csf('prod_reso_allo')].')">'.number_format($total_smv_used,2,'.','').'</a></td>
								<td valign="middle" width="100" align="right">'.number_format($total_smv_achv,2,'.','').'</td>
								<td valign="middle" align="right" width="100">'.number_format($avg_aff_perc,2,'.','').'</td>';

						 $total_po_id=explode(",",$row[csf("po_id")]);
						 $total_po_id=implode("*",$total_po_id);
						 $line_number_id=$row[csf('sewing_line')];

						 $html.='<td width="50"><input type="button"  value="View" class="formbutton" onclick="show_line_remarks('.$comapny_id.",'".$total_po_id."','".$floor."','".$line_number_id."','remarks_popup',".$txt_date.')"/></td>
							</tr>';

						$i++;

						$total_order_qnty+=$po_quantity;
						$total_machine_qnty+=$prod_resource_array[$row[csf('sewing_line')]][$pr_date]['active_machine'];
						$tot_worker+=$prod_resource_array[$row[csf('sewing_line')]][$pr_date]['man_power'];
						$tot_operator+=$prod_resource_array[$row[csf('sewing_line')]][$pr_date]['operator'];
						$tot_helper+=$prod_resource_array[$row[csf('sewing_line')]][$pr_date]['helper'];
						$tot_prev_input_qnty+=$prev_input_qnty;
						$tot_prev_out_qnty+=$prev_out_qnty;
						$tot_prev_wip+=$prev_wip;
						$tot_today_target+=$prod_resource_array[$row[csf('sewing_line')]][$pr_date]['tpd'];
						$tot_today_input_qnty+=$today_input_qnty;
						$tot_today_out_qnty+=$row[csf('qnty')];
						$tot_today_smv+=$today_smv;
						$tot_item_smv+=$item_smv;
						$tot_achv_smv+=$achv_smv;
						$tot_cm_value+=$cm_value;
						$grand_tot_prod+=$total_prod;
						$tot_wip+=$wip;
						$grand_tot_smv_used+=$total_smv_used;
						$grand_tot_achv_smv+=$total_smv_achv;

						$location_order_qnty+=$po_quantity;
						$location_machine_qnty+=$prod_resource_array[$row[csf('sewing_line')]][$pr_date]['active_machine'];
						$location_worker+=$prod_resource_array[$row[csf('sewing_line')]][$pr_date]['man_power'];
						$location_operator+=$prod_resource_array[$row[csf('sewing_line')]][$pr_date]['operator'];
						$location_helper+=$prod_resource_array[$row[csf('sewing_line')]][$pr_date]['helper'];
						$location_prev_input_qnty+=$prev_input_qnty;
						$location_prev_out_qnty+=$prev_out_qnty;
						$location_prev_wip+=$prev_wip;
						$location_today_target+=$prod_resource_array[$row[csf('sewing_line')]][$pr_date]['tpd'];
						$location_today_input_qnty+=$today_input_qnty;
						$location_today_out_qnty+=$row[csf('qnty')];
						$location_today_smv+=$today_smv;
						$location_item_smv+=$item_smv;
						$location_achv_smv+=$achv_smv;
						$location_cm_value+=$cm_value;
						$location_tot_prod+=$total_prod;
						$location_wip+=$wip;
						$location_tot_smv_used+=$total_smv_used;
						$location_tot_achv_smv+=$total_smv_achv;

						$floor_order_qnty+=$po_quantity;
						$floor_machine_qnty+=$prod_resource_array[$row[csf('sewing_line')]][$pr_date]['active_machine'];
						$floor_worker+=$prod_resource_array[$row[csf('sewing_line')]][$pr_date]['man_power'];
						$floor_operator+=$prod_resource_array[$row[csf('sewing_line')]][$pr_date]['operator'];
						$floor_helper+=$prod_resource_array[$row[csf('sewing_line')]][$pr_date]['helper'];
						$floor_prev_input_qnty+=$prev_input_qnty;
						$floor_prev_out_qnty+=$prev_out_qnty;
						$floor_prev_wip+=$prev_wip;
						$floor_today_target+=$prod_resource_array[$row[csf('sewing_line')]][$pr_date]['tpd'];
						$floor_today_input_qnty+=$today_input_qnty;
						$floor_today_out_qnty+=$row[csf('qnty')];
						$floor_today_smv+=$today_smv;
						$floor_item_smv+=$item_smv;
						$floor_achv_smv+=$achv_smv;
						$floor_cm_value+=$cm_value;
						$floor_tot_prod+=$total_prod;
						$floor_wip+=$wip;
						$floor_tot_smv_used+=$total_smv_used;
						$floor_tot_achv_smv+=$total_smv_achv;

						$buyer_data_array[$row[csf('buyer_name')]]['toin']+=$today_input_qnty;
						$buyer_data_array[$row[csf('buyer_name')]]['topd']+=$row[csf('qnty')];
						$buyer_data_array[$row[csf('buyer_name')]]['tosmv']+=$today_smv;
						$buyer_data_array[$row[csf('buyer_name')]]['achv_smv']+=$achv_smv;
						$buyer_data_array[$row[csf('buyer_name')]]['tpd']+=$prod_resource_array[$row[csf('sewing_line')]][$pr_date]['tpd'];
						$buyer_data_array[$row[csf('buyer_name')]]['man_power']+=$prod_resource_array[$row[csf('sewing_line')]][$pr_date]['man_power'];
						$buyer_data_array[$row[csf('buyer_name')]]['operator']+=$prod_resource_array[$row[csf('sewing_line')]][$pr_date]['operator'];
						$buyer_data_array[$row[csf('buyer_name')]]['helper']+=$prod_resource_array[$row[csf('sewing_line')]][$pr_date]['helper'];
						$buyer_data_array[$row[csf('buyer_name')]]['cm']+=$cm_value;

						if($duplicate_array[$row[csf('prod_reso_allo')]][$floor][$row[csf('sewing_line')]]=="")
						{
							$total_actual_machine_qnty+=$prod_resource_array[$row[csf('sewing_line')]][$pr_date]['active_machine'];
							$tot_actual_worker+=$prod_resource_array[$row[csf('sewing_line')]][$pr_date]['man_power'];
							$tot_actual_operator+=$prod_resource_array[$row[csf('sewing_line')]][$pr_date]['operator'];
							$tot_actual_helper+=$prod_resource_array[$row[csf('sewing_line')]][$pr_date]['helper'];
							$tot_actual_today_target+=$prod_resource_array[$row[csf('sewing_line')]][$pr_date]['tpd'];
							$tot_actual_today_smv+=$today_smv;
							$grand_tot_actual_smv_used+=$total_smv_used;

							$duplicate_array[$row[csf('prod_reso_allo')]][$floor][$row[csf('sewing_line')]]=$row[csf('sewing_line')];
						}
					}
				}

				$floor_today_ach_perc=($floor_today_out_qnty/$floor_today_target)*100;
				$floor_today_aff_perc=$floor_achv_smv/$floor_today_smv*100;
				$floor_avg_aff_perc=$floor_tot_achv_smv/$floor_tot_smv_used*100;

					$html.='<tr bgcolor="#CCCCCC">
							<td colspan="8" align="right" width="">Floor Total</td>
							<td align="right" width="75"></td>
							<td align="right" width="75"></td>
							<td align="right" width="70">'.$floor_machine_qnty.'</td>

							<td align="right" width="60">'.$floor_worker.'</td>
							<td align="right" width="60">'.$floor_operator.'</td>
							<td align="right" width="60">'.$floor_helper.'</td>

							<td align="right" width="120"></td>
							<td align="right" width="80"></td>

							<td align="right" width="75">'.$floor_prev_input_qnty.'</td>
							<td align="right" width="75">'.$floor_prev_out_qnty.'</td>
							<td align="right" width="75">'.$floor_prev_wip.'</td>
							<td align="right" width="75">'.$floor_today_target.'</td>
							<td align="right" width="75">'.$floor_today_input_qnty.'</td>
							<td align="right" width="75">'.$floor_today_out_qnty.'</td>
							<td align="right" width="75">'.number_format($floor_today_aff_perc,2,'.','').'</td>
							<td align="right" width="75">'.$floor_today_smv.'</td>

							<td align="right" width="70"></td>
							<td align="right" width="100">'.number_format($floor_achv_smv, 2).'</td>
							<td align="right" width="75">'.number_format($floor_today_aff_perc,2,'.','').'</td>

							<td align="right" width="100"></td>
							<td align="right" width="110">'.number_format($floor_cm_value,2,'.','').'</td>
							<td align="right" width="90">'.$floor_tot_prod.'</td>

							<td align="right" width="80"></td> 
							<td align="right" width="80">'.$floor_wip.'</td>

							<td align="right" width="100">'.number_format($floor_tot_smv_used,2,'.','').'</td>
							<td align="right" width="100">'.number_format($floor_tot_achv_smv,2,'.','').'</td>
							<td align="right" width="100">'.number_format($floor_avg_aff_perc,2,'.','').'</td>
							<td align="right" width="50"></td>
						</tr>'; 
			}//.$floor_item_smv.
			$location_today_ach_perc=0;
			$location_today_ach_perc=$location_today_out_qnty/$location_today_target*100;
			$location_today_aff_perc=$location_achv_smv/$location_today_smv*100;
			$location_avg_aff_perc=$location_tot_achv_smv/$location_tot_smv_used*100;
			

					$html.='<tr bgcolor="#E9F3FF">
						<td colspan="8" align="right" width="">Location Total</td>
						<td align="right" width="75"></td>
						<td align="right" width="75"></td>
						<td align="right" width="70">'.$location_machine_qnty.'</td>

						<td align="right" width="60">'.$location_worker.'</td>
						<td align="right" width="60">'.$location_operator.'</td>
						<td align="right" width="60">'.$location_helper.'</td>

						<td align="right" width="120"></td>
						<td align="right" width="80"></td>

						<td align="right" width="75">'.$location_prev_input_qnty.'</td>
						<td align="right" width="75">'.$location_prev_out_qnty.'</td>
						<td align="right" width="75">'.$location_prev_wip.'</td>
						<td align="right" width="75">'.$location_today_target.'</td>
						<td align="right" width="75">'.$location_today_input_qnty.'</td>
						<td align="right" width="75">'.$location_today_out_qnty.'</td>
						<td align="right" width="75">'.number_format($location_today_ach_perc,2,'.','').'</td>
						<td align="right" width="75">'.$location_today_smv.'</td>

						<td align="right" width="70"></td>
						<td align="right" width="100">'.number_format($location_achv_smv, 2).'</td>
						<td align="right" width="75">'.number_format($location_today_aff_perc,2,'.','').'</td>
						<td align="right" width="100"></td>

						<td align="right" width="110">'.number_format($location_cm_value,2,'.','').'</td>
						<td align="right" width="90">'.$location_tot_prod.'</td>
						<td align="right" width="80"></td> 
						<td align="right" width="80">'.$location_wip.'</td>

						<td align="right" width="100">'.number_format($location_tot_smv_used,2,'.','').'</td>
						<td align="right" width="100">'.number_format($location_tot_achv_smv,2,'.','').'</td>
						<td align="right" width="100">'.number_format($location_avg_aff_perc,2,'.','').'</td>
						<td align="right" width="50"></td>
					</tr>';
		}

		ob_start();
		?>
		<style type="text/css">
			/* .rpt_table th, .rpt_table td{
				padding-right: 2px;
				padding-left: 2px;
			} */
		</style>
		<fieldset style="width:2995px">
			<table width="2995" cellpadding="0" cellspacing="0">
				<tr class="form_caption">
					<td colspan="33" align="center"><strong><? echo $report_title; ?></strong></td>
				</tr>
				<tr class="form_caption">
					<td colspan="33" align="center"><strong><? echo $companyArr[$comapny_id]; ?></strong></td>
				</tr>
			</table>
			<table id="table_header_1" class="rpt_table" width="2995" cellpadding="0" cellspacing="0" border="1" rules="all">
				<thead>
					<th width="40">SL</th>
					<th width="50">Line No</th>
					<th width="70">Buyer</th>
					<th width="110">Order No</th>
					<th width="110">Job NO</th>
					<th width="110">Style Ref.</th>
					<th width="110">Internal Ref.</th>
					<th width="140">Garments Item</th>
					<th width="75">Order Qnty</th>
					<th width="75">Unit Price</th>
					<th width="70">Machine Qnty</th>
					<th width="60">Worker</th>
					<th width="60">Operator</th>
					<th width="60">Helper</th>
					<th width="120">Line Chief</th>
					<th width="80">1st Input Date</th>
					<th width="75">Prev. Input Qnty</th>
					<th width="75">Prev. Prod. Qnty</th>
					<th width="75">Prev. WIP</th>
					<th width="75">Today Target</th>
					<th width="75">Today Input</th>
					<th width="75">Today Prod.</th>
					<th width="75">Today Achv. %</th>
					<th width="75">Today SMV</th>
					<th width="70">Item SMV</th>
					<th width="100">Achieved SMV</th>
					<th width="75" title="(Today Prod/Today Target)*100">Today Eff. %</th>
					<th width="100">CM/DZN</th>
					<th width="110">Today CM Value</th>
					<th width="90">Total Prod.</th>
					<th width="80">Avg. Prod./Day</th>
					<th width="80">WIP</th>
					<th width="100">TTL SMV Used</th>
					<th width="100">TTL SMV Achv.</th>
					<th width="100">Avg. Eff. %</th>
					<th width="50">Remarks</th>
				</thead>
			</table>
			<div style="width: 2995px; max-height:400px; overflow-y:scroll" id="scroll_body">
				<table class="rpt_table" width="2995" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_bodys">
					<? echo $html; ?>
					<tfoot>
						<?
							$grand_today_ach_perc=$tot_today_out_qnty/$tot_actual_today_target*100;
							$grand_today_aff_perc=$tot_achv_smv/$tot_actual_today_smv*100;
							$grand_avg_aff_perc=$grand_tot_achv_smv/$grand_tot_actual_smv_used*100;
                        ?>
						<tr>
							<th colspan="8" align="right">Actual Total</th>
							<th align="right" width="75"></th>
							<th align="right" width="75"></th>
							<th align="right" width="70"><? echo $total_actual_machine_qnty; ?></th>

							<th align="right" width="60"><? echo $tot_actual_worker; ?></th>
							<th align="right" width="60"><? echo $tot_actual_operator; ?></th>
							<th align="right" width="60"><? echo $tot_actual_helper; ?></th>

							<th align="right" width="120"></th>
							<th align="right" width="80"></th>

							<th align="right" width="75"><? echo $tot_prev_input_qnty; ?></th>
							<th align="right" width="75"><? echo $tot_prev_out_qnty; ?></th>
							<th align="right" width="75"><? echo $tot_prev_wip; ?></th>
							<th align="right" width="75"><? echo $tot_actual_today_target; ?></th>
							<th align="right" width="75"><? echo $tot_today_input_qnty; ?></th>
							<th align="right" width="75"><? echo $tot_today_out_qnty; ?></th>
							<th align="right" width="75"><? echo number_format($grand_today_ach_perc,2,'.',''); ?></th>
							<th align="right" width="75"><? echo $tot_actual_today_smv; ?></th>

							<th align="right" width="70"></th>
							<th align="right" width="100"><? echo number_format($tot_achv_smv, 2); ?></th>
							<th align="right" width="75"><? echo number_format($grand_today_aff_perc,2,'.',''); ?></th>
							<th align="right" width="100"></th>

							<th align="right" width="110"><? echo number_format($tot_cm_value,2,'.',''); ?></th>
							<th align="right" width="90"><? echo $grand_tot_prod; ?></th>
							<th align="right" width="80"></th>
							<th align="right" width="80"><? echo $tot_wip; ?></th>

							<th align="right" width="100"><? echo number_format($grand_tot_actual_smv_used,2,'.',''); ?></th>
							<th align="right" width="100"><? echo number_format($grand_tot_achv_smv,2,'.',''); ?></th>
							<th align="right" width="100"><? echo number_format($grand_avg_aff_perc,2,'.',''); ?></th>
							<th align="right" width="50"></th>
						</tr>
					</tfoot>
				</table>
			</div>
		</fieldset>
        <br/>
         <fieldset style="width:950px">
			<label><b>No Production Line</b></label>
        	<table id="table_header_1" class="rpt_table" width="930" cellpadding="0" cellspacing="0" border="1" rules="all">
				<thead>
					<th width="40">SL</th>
					<th width="100">Line No</th>
					<th width="100">Floor</th>
					<th width="75">Man Power</th>
					<th width="75">Operator</th>
					<th width="75">Helper</th>
                    <th width="75">Working Hour</th>
					<th>Remarks</th>

				</thead>
			</table>
			<div style="width:950px; max-height:400px; overflow-y:scroll" id="scroll_body">
				<table class="rpt_table" width="930" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
                <?
					if($actual_line_arr!="")
					{
						$actual_line_arr=implode(",",array_unique(explode(",",chop($actual_line_arr,","))));
						$line_cond=" and a.id not in ($actual_line_arr)";
					}

					if($db_type==0) $remarks_cond="group_concat(d.remarks)";
					else if($db_type==2) $remarks_cond="LISTAGG(cast(d.remarks as varchar2(4000)), ',') WITHIN GROUP (ORDER BY d.remarks)";

			 		//$dataArray=sql_select("select a.id, a.location_id, a.floor_id, a.line_number, b.active_machine, b.man_power, b.operator, b.helper, b.line_chief, b.target_per_hour, b.working_hour, $remarks_cond as remarks from prod_resource_mst a, prod_resource_dtls b,prod_resource_dtls_time d where a.id=b.mst_id and b.mast_dtl_id=d.mast_dtl_id and a.company_id=$comapny_id and b.pr_date=".$txt_date." and d.shift_id=1 and a.is_deleted=0 and b.is_deleted=0 $line_cond group by a.id, a.location_id, a.floor_id, a.line_number, b.active_machine, b.man_power, b.operator, b.helper, b.line_chief, b.target_per_hour, b.working_hour");
					$dataArray=sql_select("select a.id, a.floor_id, a.line_number, b.man_power, b.operator, b.helper, b.working_hour, $remarks_cond as remarks from prod_resource_mst a, prod_resource_dtls b,prod_resource_dtls_time d where a.id=b.mst_id and b.mast_dtl_id=d.mast_dtl_id $dtls_company_cond $dtls_wo_company_cond and a.location_id like '$location' and b.pr_date=".$txt_date." and d.shift_id=1 and a.is_deleted=0 and b.is_deleted=0 $line_cond group by a.id, a.floor_id, a.line_number, b.man_power, b.operator, b.helper, b.working_hour");
					$l=1;
					foreach( $dataArray as $row )
					{
						if ($l%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$sewing_line='';
						$line_number=explode(",",$row[csf('line_number')]);
						foreach($line_number as $val)
						{
							if($sewing_line=='') $sewing_line=$lineArr[$val]; else $sewing_line.=",".$lineArr[$val];
						}
						$remarks=implode(",",array_unique(explode(",",$row[csf('remarks')])));
					?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tbltr_<? echo $l; ?>','<? echo $bgcolor; ?>')" id="tbltr_<? echo $l; ?>">
                        	<td width="40"><? echo $l; ?></td>
                            <td width="100"><p><? echo $sewing_line; ?>&nbsp;</p></td>
                            <td width="100" align="right"><p>&nbsp;<? echo $floorArr[$row[csf('floor_id')]]; ?></p></td>
                            <td width="75" align="right">&nbsp;<? echo $row[csf('man_power')]; ?></th>
                            <td width="75" align="right">&nbsp;<? echo $row[csf('operator')]; ?></th>
                            <td width="75" align="right">&nbsp;<? echo $row[csf('helper')]; ?></td>
                            <td width="75" align="right">&nbsp;<? echo $row[csf('working_hour')]; ?></td>
                            <td><? echo $remarks; ?>&nbsp;</td>
                        </tr>
                    <?
						$l++;
					}
				?>
				</table>
			</div>
		</fieldset>
		<?
	}
	else if($type==2) // show2 btn start
	{

		// var_dump($_REQUEST);
		$shift_arr = array(1 => "A", 2 => "B", 3 => "C");
		$company_name=str_replace("'","",$cbo_company_id);
		$cbo_location=str_replace("'","",$cbo_location_id);
		$cbo_floor=str_replace("'","",$cbo_floor_id);
		$cbo_line=str_replace("'","",$cbo_line);
		$buyer_name=str_replace("'","",$cbo_buyer_name);
		$source=str_replace("'","",$cbo_source);
		$shift_name=str_replace("'","",$cbo_shift_name);
		$prod_date = $txt_date;

		$prod_con = "";
		$prod_con .= ($company_name==0) ? "" : " and a.company_id=".$company_name;
		$prod_con .= ($buyer_name==0) ? "" : " and d.buyer_name=".$buyer_name;
		$prod_con .= ($cbo_location==0) ? "" : " and a.location=".$cbo_location;
		$prod_con .= ($cbo_floor == "") ? "" : " and a.floor_id in($cbo_floor)";
		$prod_con .= ($cbo_line==0) ? "" : " and a.sewing_line=".$cbo_line;
		$prod_con .= ($source==0) ? "" : " and a.production_source=".$source;
		$prod_con .= ($shift_name==0) ? "" : " and a.shift_name=".$shift_name;
		$prod_con .= ($source==0) ? "" : " and a.production_date=".$prod_date;

		$sql= "SELECT a.location,a.po_break_down_id,a.floor_id,a.prod_reso_allo,a.sewing_line,a.item_number_id,a.remarks, c.color_number_id,a.shift_name,
			d.style_ref_no,e.po_number,e.grouping,sum(c.order_quantity) as order_quantity
		FROM
		pro_garments_production_mst a,pro_garments_production_dtls b ,wo_po_color_size_breakdown c, wo_po_details_master d, wo_po_break_down e
		WHERE a.production_type in(4,5) and b.production_type in(4,5) and a.id=b.mst_id and b.color_size_break_down_id=c.id and a.po_break_down_id=c.po_break_down_id and
		a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $prod_con and d.job_no=e.job_no_mst AND e.id=a.po_break_down_id AND e.id=c.po_break_down_id and d.status_active=1 and d.is_deleted=0
		group by a.location,a.po_break_down_id,a.floor_id,a.prod_reso_allo,a.sewing_line,a.item_number_id,a.remarks, c.color_number_id,a.shift_name,
			d.style_ref_no,e.po_number,e.grouping order by a.floor_id,a.sewing_line";
		//  echo $sql;die();
		$result=sql_select($sql);
		$data_array = array();
		$po_id_arr = array();
		$color_id_arr = array();
		foreach ($result as $key => $val)
		{
			if($val[csf("prod_reso_allo")]==1)
            {
        		$line_resource_mst_arr=explode(",",$prod_reso_line_arr[$val[csf('sewing_line')]]);
				$line_name="";
				foreach($line_resource_mst_arr as $resource_id)
				{
					if($line_name == ""){$line_name=$sewLineArr[$resource_id];}
					else{$line_name=",".$sewLineArr[$resource_id];}
				}
				$data_array[$line_name][$val[csf('location')]][$val[csf('floor_id')]][$val[csf('po_break_down_id')]][$val[csf('item_number_id')]][$val[csf('color_number_id')]][$val[csf('shift_name')]]['po_no'] = $val[csf('po_number')];
				$data_array[$line_name][$val[csf('location')]][$val[csf('floor_id')]][$val[csf('po_break_down_id')]][$val[csf('item_number_id')]][$val[csf('color_number_id')]][$val[csf('shift_name')]]['grouping'] = $val[csf('grouping')];
				$data_array[$line_name][$val[csf('location')]][$val[csf('floor_id')]][$val[csf('po_break_down_id')]][$val[csf('item_number_id')]][$val[csf('color_number_id')]][$val[csf('shift_name')]]['style_ref_no'] = $val[csf('style_ref_no')];
				$data_array[$line_name][$val[csf('location')]][$val[csf('floor_id')]][$val[csf('po_break_down_id')]][$val[csf('item_number_id')]][$val[csf('color_number_id')]][$val[csf('shift_name')]]['item_number_id'] = $val[csf('item_number_id')];
				$data_array[$line_name][$val[csf('location')]][$val[csf('floor_id')]][$val[csf('po_break_down_id')]][$val[csf('item_number_id')]][$val[csf('color_number_id')]][$val[csf('shift_name')]]['floor_id'] = $val[csf('floor_id')];
				$data_array[$line_name][$val[csf('location')]][$val[csf('floor_id')]][$val[csf('po_break_down_id')]][$val[csf('item_number_id')]][$val[csf('color_number_id')]][$val[csf('shift_name')]]['remarks'] = $val[csf('remarks')];
				$data_array[$line_name][$val[csf('location')]][$val[csf('floor_id')]][$val[csf('po_break_down_id')]][$val[csf('item_number_id')]][$val[csf('color_number_id')]][$val[csf('shift_name')]]['prod_reso_allo'] = $val[csf('prod_reso_allo')];
				$data_array[$line_name][$val[csf('location')]][$val[csf('floor_id')]][$val[csf('po_break_down_id')]][$val[csf('item_number_id')]][$val[csf('color_number_id')]][$val[csf('shift_name')]]['order_quantity'] = $val[csf('order_quantity')];
			}
			else
			{
				$line_name=$sewLineArr[$val[csf('sewing_line')]];
				$data_array[$line_name][$val[csf('location')]][$val[csf('floor_id')]][$val[csf('po_break_down_id')]][$val[csf('item_number_id')]][$val[csf('color_number_id')]][$val[csf('shift_name')]]['po_no'] = $val[csf('po_number')];
				$data_array[$line_name][$val[csf('location')]][$val[csf('floor_id')]][$val[csf('po_break_down_id')]][$val[csf('item_number_id')]][$val[csf('color_number_id')]][$val[csf('shift_name')]]['grouping'] = $val[csf('grouping')];
				$data_array[$line_name][$val[csf('location')]][$val[csf('floor_id')]][$val[csf('po_break_down_id')]][$val[csf('item_number_id')]][$val[csf('color_number_id')]][$val[csf('shift_name')]]['style_ref_no'] = $val[csf('style_ref_no')];
				$data_array[$line_name][$val[csf('location')]][$val[csf('floor_id')]][$val[csf('po_break_down_id')]][$val[csf('item_number_id')]][$val[csf('color_number_id')]][$val[csf('shift_name')]]['item_number_id'] = $val[csf('item_number_id')];
				$data_array[$line_name][$val[csf('location')]][$val[csf('floor_id')]][$val[csf('po_break_down_id')]][$val[csf('item_number_id')]][$val[csf('color_number_id')]][$val[csf('shift_name')]]['floor_id'] = $val[csf('floor_id')];
				$data_array[$line_name][$val[csf('location')]][$val[csf('floor_id')]][$val[csf('po_break_down_id')]][$val[csf('item_number_id')]][$val[csf('color_number_id')]][$val[csf('shift_name')]]['remarks'] = $val[csf('remarks')];
				$data_array[$line_name][$val[csf('location')]][$val[csf('floor_id')]][$val[csf('po_break_down_id')]][$val[csf('item_number_id')]][$val[csf('color_number_id')]][$val[csf('shift_name')]]['prod_reso_allo'] = $val[csf('prod_reso_allo')];
				$data_array[$line_name][$val[csf('location')]][$val[csf('floor_id')]][$val[csf('po_break_down_id')]][$val[csf('item_number_id')]][$val[csf('color_number_id')]][$val[csf('shift_name')]]['order_quantity'] = $val[csf('order_quantity')];
			}
			$po_id_arr[$val[csf('po_break_down_id')]] = $val[csf('po_break_down_id')];
			$color_id_arr[$val[csf('color_number_id')]] = $val[csf('color_number_id')];
			
		}
		// echo "<pre>";
		// print_r($data_array);
		// echo "</pre>";
		$poId = implode(",", $po_id_arr);
		$colorId = implode(",", $color_id_arr);
		$sql_prod= "SELECT a.location, a.floor_id,a.sewing_line, a.prod_reso_allo,a.po_break_down_id, c.color_number_id,a.item_number_id, a.production_type,a.shift_name,
		sum(case when a.production_type=4 and a.production_date<=$prod_date then b.production_qnty else 0 end) as total_input,
		sum(case when a.production_type=4 and a.production_date=$prod_date then b.production_qnty else 0 end) as today_input,
		sum(case when a.production_type=5 and a.production_date<=$prod_date then b.production_qnty else 0 end) as total_output,
		sum(case when a.production_type=5 and a.production_date=$prod_date then b.production_qnty else 0 end) as today_output

		FROM pro_garments_production_mst a,pro_garments_production_dtls b, wo_po_color_size_breakdown c
		WHERE a.company_id=$company_name  and a.production_source = '1' and a.production_type in(4,5) and b.production_type in(4,5) and a.production_date<=$prod_date and c.id=b.color_size_break_down_id and a.id=b.mst_id and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.po_break_down_id in($poId) and a.po_break_down_id=c.po_break_down_id and c.status_active=1 and c.is_deleted=0 group by a.location, a.floor_id,a.sewing_line, a.prod_reso_allo,a.po_break_down_id, c.color_number_id,a.item_number_id, a.production_type,a.shift_name";
		//  echo $sql_prod;die();
		$sql_prod_res = sql_select($sql_prod);
		$prod_data = array();
		foreach ($sql_prod_res as $key => $val)
		{
			if($val[csf("prod_reso_allo")]==1)
            {
        		$line_resource_mst_arr=explode(",",$prod_reso_line_arr[$val[csf('sewing_line')]]);
				$line_name="";
				foreach($line_resource_mst_arr as $resource_id)
				{
					if($line_name == ""){$line_name=$sewLineArr[$resource_id];}
					else{$line_name=",".$sewLineArr[$resource_id];}
				}

				$prod_data[$line_name][$val[csf('location')]][$val[csf('floor_id')]][$val[csf('po_break_down_id')]][$val[csf('item_number_id')]][$val[csf('color_number_id')]][$val[csf('production_type')]][$val[csf('shift_name')]]['today_input'] += $val[csf('today_input')];
				$prod_data[$line_name][$val[csf('location')]][$val[csf('floor_id')]][$val[csf('po_break_down_id')]][$val[csf('item_number_id')]][$val[csf('color_number_id')]][$val[csf('production_type')]][$val[csf('shift_name')]]['total_input'] += $val[csf('total_input')];

				$prod_data[$line_name][$val[csf('location')]][$val[csf('floor_id')]][$val[csf('po_break_down_id')]][$val[csf('item_number_id')]][$val[csf('color_number_id')]][$val[csf('production_type')]][$val[csf('shift_name')]]['today_output'] += $val[csf('today_output')];
				$prod_data[$line_name][$val[csf('location')]][$val[csf('floor_id')]][$val[csf('po_break_down_id')]][$val[csf('item_number_id')]][$val[csf('color_number_id')]][$val[csf('production_type')]][$val[csf('shift_name')]]['total_output'] += $val[csf('total_output')];

			}
			else
			{
				$line_name=$sewLineArr[$val[csf('sewing_line')]];
				$prod_data[$line_name][$val[csf('location')]][$val[csf('floor_id')]][$val[csf('po_break_down_id')]][$val[csf('item_number_id')]][$val[csf('color_number_id')]][$val[csf('production_type')]][$val[csf('shift_name')]]['today_input'] += $val[csf('today_input')];
				$prod_data[$line_name][$val[csf('location')]][$val[csf('floor_id')]][$val[csf('po_break_down_id')]][$val[csf('item_number_id')]][$val[csf('color_number_id')]][$val[csf('production_type')]][$val[csf('shift_name')]]['total_input'] += $val[csf('total_input')];

				$prod_data[$line_name][$val[csf('location')]][$val[csf('floor_id')]][$val[csf('po_break_down_id')]][$val[csf('item_number_id')]][$val[csf('color_number_id')]][$val[csf('production_type')]][$val[csf('shift_name')]]['today_output'] += $val[csf('today_output')];
				$prod_data[$line_name][$val[csf('location')]][$val[csf('floor_id')]][$val[csf('po_break_down_id')]][$val[csf('item_number_id')]][$val[csf('color_number_id')]][$val[csf('production_type')]][$val[csf('shift_name')]]['total_output'] += $val[csf('total_output')];
			}
		}
		// echo "<pre>";
		// print_r($prod_data);
		// echo "</pre>";
		/*$sql_order= "SELECT c.po_break_down_id, c.color_number_id,c.item_number_id, c.order_quantity
		FROM wo_po_color_size_breakdown c
		WHERE  c.po_break_down_id in($poId) and c.status_active=1 and c.is_deleted=0";

		$sql_order_res = sql_select($sql_order);
		$order_qnty_data = [];
		foreach ($sql_order_res as $key => $val)
		{
			$order_qnty_data[$val[csf('po_break_down_id')]][$val[csf('item_number_id')]][$val[csf('color_number_id')]]['order_quantity'] += $val[csf('order_quantity')];
		}*/

		ob_start();
		?>
        <fieldset style="width:1540px; margin: 0 auto">
			<table width="1520" cellpadding="0" cellspacing="0" align="left">
				<tr class="form_caption">
					<td colspan="13" align="center"><strong><? echo $report_title; ?></strong></td>
				</tr>
				<tr class="form_caption">
					<td colspan="14" align="center"><strong><? echo $companyArr[$comapny_id]; ?></strong></td>
				</tr>
			</table>
        	<table id="table_header_1" class="rpt_table" width="1520" cellpadding="0" cellspacing="0" border="1" rules="all" align="left">
				<thead>
					<th style="word-wrap: break-word;word-break: break-all;" width="40">SL</th>
					<th style="word-wrap: break-word;word-break: break-all;" width="100">Interna.Ref. No</th>
					<th style="word-wrap: break-word;word-break: break-all;" width="100">Order No</th>
					<th style="word-wrap: break-word;word-break: break-all;" width="100">Style No</th>
					<th style="word-wrap: break-word;word-break: break-all;" width="130">Gmt. Item</th>
					<th style="word-wrap: break-word;word-break: break-all;" width="100">Gmt. Color</th>
					<th style="word-wrap: break-word;word-break: break-all;" width="80">Order Qty.</th>
					<th style="word-wrap: break-word;word-break: break-all;" width="100">Floor Name</th>
					<th style="word-wrap: break-word;word-break: break-all;" width="100">Line No</th>
					<th style="word-wrap: break-word;word-break: break-all;" width="100">Shift Name</th>
					<th style="word-wrap: break-word;word-break: break-all;" width="75">Today Input</th>
					<th style="word-wrap: break-word;word-break: break-all;" width="75">Total Input</th>
					<th style="word-wrap: break-word;word-break: break-all;" width="75">Today Output</th>
					<th style="word-wrap: break-word;word-break: break-all;" width="75">Total Output</th>
					<th style="word-wrap: break-word;word-break: break-all;" width="75">Line Balance</th>
					<th style="word-wrap: break-word;word-break: break-all;" width="75">PO Balance</th>
                  
					<th style="word-wrap: break-word;word-break: break-all;" width="120">Remarks</th>
				</thead>
			</table>
			<div style="width:1540px; max-height:400px; overflow-y:scroll" id="scroll_body">
				<table class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body" width="1520" align="left">
					<?
					$i=1;
					foreach ($data_array as $line_name => $line_data)
					{
						foreach ($line_data as $location_id => $location_data)
						{
							foreach ($location_data as $floor_id => $floor_data)
							{
								foreach ($floor_data as $po_id => $po_data)
								{
									foreach ($po_data as $item_id => $item_data)
									{
										foreach ($item_data as $color_id => $color_data)
										{

											foreach($color_data as $shift_id=>$row)
											{

												$today_in_qnty = $prod_data[$line_name][$location_id][$floor_id][$po_id][$item_id][$color_id][4][$shift_id]['today_input'];
												$total_in_qnty = $prod_data[$line_name][$location_id][$floor_id][$po_id][$item_id][$color_id][4][$shift_id]['total_input'];
												$today_out_qnty = $prod_data[$line_name][$location_id][$floor_id][$po_id][$item_id][$color_id][5][$shift_id]['today_output'];
												$total_out_qnty = $prod_data[$line_name][$location_id][$floor_id][$po_id][$item_id][$color_id][5][$shift_id]['total_output'];
												$po_balance=$row['order_quantity'] -$total_out_qnty;

												if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
												?>
												<tr title="<? echo "po_id=$po_id,Item_id=$item_id,location_id=$location_id,Floor_id=$floor_id,Line_id=$line_name,Color_id=$color_id" ?>" bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
													<td style="word-wrap: break-word;word-break: break-all;" width="40"><? echo $i;?></td>
													<td style="word-wrap: break-word;word-break: break-all;" width="100"><? echo $row['grouping'];?></td>
													<td style="word-wrap: break-word;word-break: break-all;" width="100"><? echo $row['po_no'];?></td>
													<td style="word-wrap: break-word;word-break: break-all;" width="100"><? echo $row['style_ref_no'];?></td>
													<td style="word-wrap: break-word;word-break: break-all;" width="130"><? echo $garments_item[$item_id];?></td>
													<td style="word-wrap: break-word;word-break: break-all;" width="100"><? echo $color_library[$color_id];?></td>
													<td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo $row['order_quantity'];?></td>
													<td style="word-wrap: break-word;word-break: break-all;" width="100"><? echo $floor_library[$floor_id];?></td>
													<td style="word-wrap: break-word;word-break: break-all;" width="100"><? echo $line_name;?></td>
													<td style="word-wrap: break-word;word-break: break-all;" width="100"><? echo $shift_arr[$shift_id];?></td>

													<td style="word-wrap: break-word;word-break: break-all;" width="75" align="right"><a href="##" onclick="openmypage_sew_today('<? echo  $company_name; ?>','<? echo  $color_id; ?>',<? echo $prod_date; ?>, 'sewin_popup_one',850,350)"><? echo $today_in_qnty;?></a></td>
													<td style="word-wrap: break-word;word-break: break-all;" width="75" align="right"><a href="##" onclick="openmypage_sew_total('<? echo  $company_name; ?>','<? echo  $color_id; ?>','<? echo  $po_id; ?>', <? echo $txt_date; ?>,'sewin_popup_total',850,350)"><? echo $total_in_qnty;?></a></td>

													<td style="word-wrap: break-word;word-break: break-all;" width="75" align="right"><a href="##" onclick="openmypage_sewout_today('<? echo  $company_name; ?>','<? echo  $color_id; ?>',<? echo $prod_date; ?>, 'sewout_popup_one',850,350)"><? echo $today_out_qnty;?></a></td>

													<td style="word-wrap: break-word;word-break: break-all;" width="75" align="right"><a href="##" onclick="openmypage_sewout_total('<? echo  $company_name; ?>','<? echo  $color_id; ?>','<? echo  $po_id; ?>', <? echo $txt_date; ?>,'sewout_popup_total',850,350)"><? echo $total_out_qnty;?></a></td>

													<td style="word-wrap: break-word;word-break: break-all;" width="75" align="right"><a href="##" onclick="openmypage_line_total('<? echo  $company_name; ?>','<? echo  $color_id; ?>','<? echo  $po_id; ?>', <? echo $txt_date; ?>,'line_balance_popup',850,350)"><? echo ($total_in_qnty - $total_out_qnty);?></a></td>

													<td style="word-wrap: break-word;word-break: break-all;" width="75" align="right"><a href="##" onclick="openmypage_po_total('<? echo  $company_name; ?>','<? echo  $color_id; ?>','<? echo  $po_id; ?>', <? echo $txt_date; ?>,'po_balance_popup',850,350)"><? echo $po_balance;?></td>

												

													<td style="word-wrap: break-word;word-break: break-all;" width="120"><? echo $row['remarks'];?></td>
												</tr>
												<?
												$i++;
												$today_input+=$today_in_qnty;
												$total_input+=$total_in_qnty;
												$today_out+=$today_out_qnty;
												$total_output+=$total_out_qnty;
												$total_po+=$po_balance;
												$total_line+=$total_in_qnty - $total_out_qnty;
											}	
						                }
						            }
				                }
				            }
				        }
				    }
	                ?>
				</table>
			</div>

			<table class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body" width="1520" align="left">
				<tfoot>
					<tr>
					 <th width="40"></th>
                     <th width="100"></th>
                     <th width="100"></th>
                     <th width="100"></th>
                     <th width="130"></th>
                     <th width="100"></th>
                     <th width="80"></th>
                     <th width="100"></th>
                     <th width="100"></th>
                     <th width="100">Total</th>
                     <th width="75"><? echo $today_input;?></th>
                     <th width="75"><? echo $total_input;?></th>
                     <th width="75"><? echo $today_out;?></th>
                     <th width="75"><? echo $total_output;?></th>
					 <th width="75"><? echo $total_line;?></th>
                     <th width="75"><? echo $total_po;?></th>
				
                     <th width="120"></th>
                   
				   </tr>
				</tfoot>




			</table>
			
		</fieldset>

    	<?
	}
	else if($type==3) // show3 btn start
	{
		if($cbo_floor_id=="") $floor_con=""; else $floor_con="and  a.floor_id in(".$cbo_floor_id.")";
		$prod_reso_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');
		$dataArray=sql_select("SELECT a.id as line_id,b.man_power, b.operator, b.helper,b.working_hour from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id and  a.location_id like '$location'  $dtls_company_cond $dtls_wo_company_cond and b.pr_date=$txt_date and b.is_deleted=0");
		// pre($dataArray);die;
		// echo "SELECT a.line_number,b.man_power, b.operator, b.helper,b.working_hour from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id and  a.location_id like '$location'  $dtls_company_cond $dtls_wo_company_cond and b.pr_date=$txt_date and b.is_deleted=0";die;
		$prod_line_arr = array();
		foreach($dataArray as $row)
		{
			$prod_line_arr [$row['LINE_ID']]=$row['LINE_ID'];
			$prod_resource_array[$row['LINE_ID']]['USE_TOTAL']=$row['MAN_POWER'];
			$prod_resource_array[$row['LINE_ID']]['USE_OP']=$row['OPERATOR'];
			$prod_resource_array[$row['LINE_ID']]['USE_HP']=$row['HELPER'];
			$prod_resource_array[$row['LINE_ID']]['WORKING_HOUR']=$row['WORKING_HOUR'];
		}
		$line_con = where_con_using_array($prod_line_arr,0,'c.sewing_line');
		// pre($prod_line_arr); die;
		$prod_sql = "SELECT a.id as job_id,a.buyer_name as buyer,a.style_ref_no as style,a.gmts_item_id as item,b.id as po_id, c.prod_reso_allo,c.sewing_line,c.company_id,c.production_quantity as prod_qty FROM wo_po_details_master a,wo_po_break_down b,wo_po_color_size_breakdown d ,pro_garments_production_mst c,pro_garments_production_dtls e WHERE a.id=b.job_id and b.id=c.po_break_down_id and b.id=d.po_break_down_id and c.id=e.mst_id and d.id=e.color_size_break_down_id $company_cond $wo_company_cond $line_con and c.production_date=$txt_date and c.production_type=5 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 $source_con $buyer_id_cond $floor ";
		// echo $prod_sql; die;
		$prod_sql_res = sql_select($prod_sql);
		$line_wise_data_arr = array();
		$line_name_arr  = array();
		$lc_com_array  = array();
		$all_style_arr  = array();
		$poIdArr  = array();
		$job_id_arr  = array();
		foreach ($prod_sql_res as  $v) {
			if($v['PROD_RESO_ALLO']==1)
			{
				$line_name = "";
				// echo $prod_reso_arr[$v['SEWING_LINE']]."<br>";
				$sewing_line_id_arr=explode(",",$prod_reso_arr[$v['SEWING_LINE']]);
				foreach ($sewing_line_id_arr as $r)
				{
					// echo $prod_reso_arr[$v['SEWING_LINE']]."<br>";
					$line_name .= ($line_name=="") ? $lineArr[$r] : ",". $lineArr[$r];
				}
				$sewing_line_id = $sewing_line_id_arr[0];
			}
			else
			{
				$sewing_line_id=$v['SEWING_LINE'];
				$line_name=$lineArr[$v['SEWING_LINE']];
			}

			if($lineSerialArr[$sewing_line_id]=="")
			{
				$lastSlNo++;
				$slNo=$lastSlNo;
				$lineSerialArr[$sewing_line_id]=$slNo;
			}
			else
			{
			 	$slNo=$lineSerialArr[$sewing_line_id];
			}
			$line_name_arr[$v['SEWING_LINE']] = $line_name;
			$lc_com_array[$v['COMPANY_ID']] = $v['COMPANY_ID'];
			$all_style_arr[$v['STYLE']] = $v['STYLE'];
			$poIdArr[$v['PO_ID']] = $v['PO_ID'];
			$job_id_arr[$v['JOB_ID']] = $v['JOB_ID'];
			$buyer_id_arr [$v['BUYER']] = $v['BUYER'];
			if ($style_wise_po_arr[$v['STYLE']] = '')
			{
				$style_wise_po_arr[$v['STYLE']] = $v['PO_ID'];
			}else{
				$style_wise_po_arr[$v['STYLE']] .= ','.$v['PO_ID'];
			}
			$line_wise_data_arr[$slNo][$v['SEWING_LINE']][$v['PO_ID']][$v['ITEM']]['LINE'] = $line_name;
			$line_wise_data_arr[$slNo][$v['SEWING_LINE']][$v['PO_ID']][$v['ITEM']]['BUYER'] = $v['BUYER'];
			$line_wise_data_arr[$slNo][$v['SEWING_LINE']][$v['PO_ID']][$v['ITEM']]['STYLE'] = $v['STYLE'];
			$line_wise_data_arr[$slNo][$v['SEWING_LINE']][$v['PO_ID']][$v['ITEM']]['PROD_QTY'] = $v['PROD_QTY'];
			$line_wise_data_arr[$slNo][$v['SEWING_LINE']][$v['PO_ID']][$v['ITEM']]['JOB_ID'] = $v['JOB_ID'];

		}
		// ============= PO ACTIVE TIME =============
		$line_con2 = where_con_using_array($prod_line_arr,0,'c.sewing_line');
		$poIds_cond2 = where_con_using_array($poIdArr,0,"b.id");

		$po_active_sql = "SELECT c.sewing_line,c.production_date,a.id as job_id,a.gmts_item_id as item,b.id as po_id FROM wo_po_details_master a,wo_po_break_down b,pro_garments_production_mst c WHERE a.id=b.job_id and b.id=c.po_break_down_id $company_cond $wo_company_cond $line_con2 and c.production_type=5 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  $source_con $buyer_id_cond $floor $poIds_cond2";
		// echo $po_active_sql;die;
		$po_active_res = sql_select($po_active_sql);
		// pre($po_active_res);die;
		foreach($po_active_res as $v)
		{
			$prod_dates=strtotime($v['PRODUCTION_DATE']);
			if($duplicate_date_arr[$v['SEWING_LINE']][$v['JOB_ID']][$prod_dates]=="")
			{
				$active_days_arr[$v['SEWING_LINE']][$v['JOB_ID']][$v['ITEM']]+=1;
				// $active_days_arr_powise[$v['PO_BREAK_DOWN_ID']][$v['ITEM_NUMBER_ID']]+=1;
				$duplicate_date_arr[$v['SEWING_LINE']][$v['JOB_ID']][$prod_dates]=$prod_dates;
			}
		}
		// pre($active_days_arr); die;
		$job_id_cond = where_con_using_array($job_id_arr,1,'job_id');
		// echo "SELECT job_no, sew_effi_percent from wo_pre_cost_mst where status_active=1 $job_id_cond";die;
		$effi_per_arr = return_library_array("SELECT job_id, sew_effi_percent from wo_pre_cost_mst where status_active=1 $job_id_cond","job_id","sew_effi_percent");
		// pre($effi_per_arr);die;

		/*===================================================================================== /
		/										smv sorce 										/
		/===================================================================================== */
		$lc_com_ids = implode(",",$lc_com_array);
		$poIds_cond = where_con_using_array($poIdArr,0,"b.id");
		$smv_source=return_field_value("smv_source","variable_settings_production","company_name in ($lc_com_ids) and variable_list=25 and   status_active=1 and is_deleted=0");
		// echo $smv_source;

		if($smv_source=="") $smv_source=1; else $smv_source=$smv_source;
		if($smv_source==3) // from gsd enrty
		{
			$style_cond = where_con_using_array($all_style_arr,1,"a.style_ref");
			$sql_item="SELECT a.id,a.APPLICABLE_PERIOD,a.BULLETIN_TYPE,A.TOTAL_SMV,A.STYLE_REF,a.GMTS_ITEM_ID  from PPL_GSD_ENTRY_MST a where a.APPLICABLE_PERIOD <= $txt_date and A.IS_DELETED=0 and A.STATUS_ACTIVE=1 and a.APPROVED=1 $style_cond group by a.id,a.APPLICABLE_PERIOD,a.BULLETIN_TYPE,A.TOTAL_SMV,A.STYLE_REF,a.GMTS_ITEM_ID ORDER BY a.GMTS_ITEM_ID ,a.APPLICABLE_PERIOD  DESC , a.id DESC";
			$gsdSqlResult=sql_select($sql_item);
			// echo $sql_item;die;
			$style_wise_unique_po_arr = array_unique(explode(',',$style_wise_po_arr[$rows['STYLE_REF']]));
			foreach($gsdSqlResult as $rows)
			{
				foreach( $style_wise_unique_po_arr as $po_id)
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
					$item_smv_array[$itemData['ID']][$itemData['GMTS_ITEM_ID']]=$itemData['SMV_PCS'];
				}
				if($smv_source==2)
				{
					$item_smv_array[$itemData['ID']][$itemData['GMTS_ITEM_ID']]=$itemData['SMV_PCS_PRECOST'];
				}
			}
		}
		?>
		 <fieldset style="width:1440px; margin: 0 auto">
			<table width="1420" cellpadding="0" cellspacing="0" align="left">
				<tr class="form_caption">
					<td colspan="13" align="center"><strong><? echo $report_title; ?></strong></td>
				</tr>
				<tr class="form_caption">
					<td colspan="13" align="center"><strong><? echo $companyArr[$comapny_id]; ?></strong></td>
				</tr>
			</table>
        	<table id="table_header_1" class="rpt_table" width="1420" cellpadding="0" cellspacing="0" border="1" rules="all" align="left">
				<thead>
					<th width="40"> Line No </th>
					<th width="100"> Buyer </th>
					<th width="100"> Style </th>
					<th width="130"> Item</th>
					<th width="100"> Use [Total]</th>
					<th width="80"> Use [OP] </th>
					<th width="80"> Use [HP] </th>
					<th width="80"> SMV </th>
					<th width="80"> Running Day </th>
					<th width="80"> W/H </th>
					<th width="80"> Forecast Efficiency % </th>
					<th width="80"> Forecast Pcs/Hour </th>
					<th width="80"> Forecast Pcs/Day </th>
					<th width="80"> Achieved Efficiency % </th>
					<th width="80">  AchievedPcs/Day  </th>
					<th width="80"> Achieved % </th>
					<th width="80"> VariancePcs/Day </th>
					<!-- <th width="80"> Remarks </th>  -->
					<th width="80"> Forecast Available Minute </th>
					<th width="80"> Forecast Produce Minute </th>
					<th width="80"> Achieved Produce Minute </th>
				</thead>
				<tbody>
					<?php
					foreach ($line_wise_data_arr as $slNo => $sl_array)
					{
						foreach ($sl_array as $sew_line => $sew_line_array)
						{
							foreach ($sew_line_array as $po_id => $po_arr)
							{
								foreach ($po_arr as $item => $v)
								{
									$use_total = $man_power =$prod_resource_array[$sew_line]['USE_TOTAL'];
									$use_op = $prod_resource_array[$sew_line]['USE_OP'];
									$use_hp = $prod_resource_array[$sew_line]['USE_HP'];
									$working_hour = $prod_resource_array[$sew_line]['WORKING_HOUR'];
									$smv_val = $item_smv_array[$po_id][$item];
									$smv = (is_nan($smv_val) || is_infinite($smv_val) || $smv_val=='') ? 0 : ($smv_val*1);
									$running_days = $active_days_arr[$sew_line][$v['JOB_ID']][$item];
									$forecast_effi = $effi_per_arr[$v['JOB_ID']];
									$forecast_pcs_hour = $smv ? ($man_power *( $forecast_effi / 100) * 60) / $smv : 0;
									$forecast_pcs_day = $forecast_pcs_hour * $working_hour;
									$achieved_pcs_day =  $v['PROD_QTY'];
									$achieved_efficiency =( $forecast_effi / $forecast_pcs_day * $achieved_pcs_day);
									$achieved = $forecast_pcs_day ? ($achieved_pcs_day / $forecast_pcs_day *100) : 0;
									$variance_pcs_day = $achieved_pcs_day - $forecast_pcs_day;
									$forecast_available_minute = $man_power * $working_hour * 60;
									$forecast_produce_minute = $smv * $forecast_pcs_day;
									$achieved_produce_minute = $smv * $achieved_pcs_day;
								?>
									<tr>
										<td width="40" title="<?= $sew_line; ?>"> <?= $v['LINE'];?> </td>
										<td width="100"> <?= $buyerArr[$v['BUYER']]; ?> </td>
										<td width="100"> <?= $v['STYLE']; ?> </td>
										<td width="130"> <?= $garments_item[$item]; ?> </td>
										<td width="100" align="right"> <?= $use_total; ?> </td>
										<td width="80" align="right"> <?= $use_op; ?> </td>
										<td width="80" align="right"> <?= $use_hp; ?> </td>
										<td width="80" align="right"> <?= $smv; ?> </td>
										<td width="80" align="right"> <?= $running_days ?> </td>
										<td width="80" align="right"> <?= $working_hour;?> </td>
										<td width="80" align="right" title="<?= $forecast_effi ?>"> <?= round($forecast_effi);?>% </td>
										<td width="80" align="right"> <?= number_format($forecast_pcs_hour,2);?> </td>
										<td width="80" align="right"> <?= number_format($forecast_pcs_day,2);?> </td>
										<td width="80" align="right" title="<?= $achieved_efficiency?>">  <?= round($achieved_efficiency) ?>% </td>
										<td width="80" align="right"> <?= number_format($achieved_pcs_day,2) ?> </td>
										<td width="80" align="right"  title="<?= $achieved ?>"> <?= round($achieved) ?> % </td>
										<td width="80" align="right"> <?= number_format($variance_pcs_day,2) ?> </td>
										<!-- <td width="80" align="right"> Remarks </td>  -->
										<td width="80" align="right"> <?= number_format($forecast_available_minute,2) ?> </td>
										<td width="80" align="right"> <?=number_format($forecast_produce_minute,2) ?> </td>
										<td width="80" align="right"> <?=number_format($achieved_produce_minute,2) ?></td>
									</tr>
								<?php
								}
							}
						}
					}
					?>
				</tbody>
			</table>
		</fieldset>
		<?php
	}
	else if($type==4)  //show4 btn start
	{
       	// var_dump($_REQUEST);
		$company_name=str_replace("'","",$cbo_company_id);
		$cbo_location=str_replace("'","",$cbo_location_id);
		$cbo_floor=str_replace("'","",$cbo_floor_id);
		$cbo_line=str_replace("'","",$cbo_line);
		$buyer_name=str_replace("'","",$cbo_buyer_name);
		$source=str_replace("'","",$cbo_source);
		$prod_date = $txt_date;

		$prod_con = "";
		$prod_con .= ($company_name==0) ? "" : " and a.company_id=".$company_name;
		$prod_con .= ($buyer_name==0) ? "" : " and d.buyer_name=".$buyer_name;
		$prod_con .= ($cbo_location==0) ? "" : " and a.location=".$cbo_location;
		$prod_con .= ($cbo_floor == "") ? "" : " and a.floor_id in($cbo_floor)";
		$prod_con .= ($cbo_line==0) ? "" : " and a.sewing_line=".$cbo_line;
		$prod_con .= ($source==0) ? "" : " and a.production_source=".$source;
		$prod_con .= ($source==0) ? "" : " and a.production_date=".$prod_date;

		$sql= "SELECT a.location,a.po_break_down_id,a.floor_id,a.prod_reso_allo,a.sewing_line,a.item_number_id,a.remarks, c.color_number_id,a.production_date,b.reject_qty,
			d.style_ref_no,e.po_number,e.grouping,sum(c.order_quantity) as order_quantity,max(a.production_date) as last_output,current_date as current_date
		FROM
		pro_garments_production_mst a,pro_garments_production_dtls b ,wo_po_color_size_breakdown c, wo_po_details_master d, wo_po_break_down e
		WHERE a.production_type in(4,5) and b.production_type in(4,5) and a.id=b.mst_id and b.color_size_break_down_id=c.id and a.po_break_down_id=c.po_break_down_id and
		a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $prod_con and d.job_no=e.job_no_mst AND e.id=a.po_break_down_id AND e.id=c.po_break_down_id and d.status_active=1 and d.is_deleted=0
		group by a.location,a.po_break_down_id,a.floor_id,a.prod_reso_allo,a.sewing_line,a.item_number_id,a.remarks,a.production_date,b.reject_qty, c.color_number_id,
			d.style_ref_no,e.po_number,e.grouping order by a.floor_id,a.sewing_line";
			//  echo $sql;
		$result=sql_select($sql);
		$data_array = array();
		$po_id_arr = array();
		$color_id_arr = array();
		foreach ($result as $key => $val)
		{
			if($val[csf("prod_reso_allo")]==1)
            {
        		$line_resource_mst_arr=explode(",",$prod_reso_line_arr[$val[csf('sewing_line')]]);
				$line_name="";
				foreach($line_resource_mst_arr as $resource_id)
				{
					if($line_name == ""){$line_name=$sewLineArr[$resource_id];}
					else{$line_name=",".$sewLineArr[$resource_id];}
				}
				$data_array[$line_name][$val[csf('location')]][$val[csf('floor_id')]][$val[csf('po_break_down_id')]][$val[csf('item_number_id')]][$val[csf('color_number_id')]]['po_no'] = $val[csf('po_number')];
				$data_array[$line_name][$val[csf('location')]][$val[csf('floor_id')]][$val[csf('po_break_down_id')]][$val[csf('item_number_id')]][$val[csf('color_number_id')]]['production_date'] = $val[csf('production_date')];
				$data_array[$line_name][$val[csf('location')]][$val[csf('floor_id')]][$val[csf('po_break_down_id')]][$val[csf('item_number_id')]][$val[csf('color_number_id')]]['grouping'] = $val[csf('grouping')];
				$data_array[$line_name][$val[csf('location')]][$val[csf('floor_id')]][$val[csf('po_break_down_id')]][$val[csf('item_number_id')]][$val[csf('color_number_id')]]['style_ref_no'] = $val[csf('style_ref_no')];
				$data_array[$line_name][$val[csf('location')]][$val[csf('floor_id')]][$val[csf('po_break_down_id')]][$val[csf('item_number_id')]][$val[csf('color_number_id')]]['item_number_id'] = $val[csf('item_number_id')];
				$data_array[$line_name][$val[csf('location')]][$val[csf('floor_id')]][$val[csf('po_break_down_id')]][$val[csf('item_number_id')]][$val[csf('color_number_id')]]['floor_id'] = $val[csf('floor_id')];
				$data_array[$line_name][$val[csf('location')]][$val[csf('floor_id')]][$val[csf('po_break_down_id')]][$val[csf('item_number_id')]][$val[csf('color_number_id')]]['remarks'] = $val[csf('remarks')];
				$data_array[$line_name][$val[csf('location')]][$val[csf('floor_id')]][$val[csf('po_break_down_id')]][$val[csf('item_number_id')]][$val[csf('color_number_id')]]['prod_reso_allo'] = $val[csf('prod_reso_allo')];
				$data_array[$line_name][$val[csf('location')]][$val[csf('floor_id')]][$val[csf('po_break_down_id')]][$val[csf('item_number_id')]][$val[csf('color_number_id')]]['order_quantity'] = $val[csf('order_quantity')];
				$data_array[$line_name][$val[csf('location')]][$val[csf('floor_id')]][$val[csf('po_break_down_id')]][$val[csf('item_number_id')]][$val[csf('color_number_id')]]['reject_qty'] += $val[csf('reject_qty')];
				$data_array[$line_name][$val[csf('location')]][$val[csf('floor_id')]][$val[csf('po_break_down_id')]][$val[csf('item_number_id')]][$val[csf('color_number_id')]]['last_output'] = $val[csf('last_output')];
				$data_array[$line_name][$val[csf('location')]][$val[csf('floor_id')]][$val[csf('po_break_down_id')]][$val[csf('item_number_id')]][$val[csf('color_number_id')]]['current_date'] = $val[csf('current_date')];
			}
			else
			{
				$line_name=$sewLineArr[$val[csf('sewing_line')]];
				$data_array[$line_name][$val[csf('location')]][$val[csf('floor_id')]][$val[csf('po_break_down_id')]][$val[csf('item_number_id')]][$val[csf('color_number_id')]]['po_no'] = $val[csf('po_number')];
				$data_array[$line_name][$val[csf('location')]][$val[csf('floor_id')]][$val[csf('po_break_down_id')]][$val[csf('item_number_id')]][$val[csf('color_number_id')]]['grouping'] = $val[csf('grouping')];
				$data_array[$line_name][$val[csf('location')]][$val[csf('floor_id')]][$val[csf('po_break_down_id')]][$val[csf('item_number_id')]][$val[csf('color_number_id')]]['style_ref_no'] = $val[csf('style_ref_no')];
				$data_array[$line_name][$val[csf('location')]][$val[csf('floor_id')]][$val[csf('po_break_down_id')]][$val[csf('item_number_id')]][$val[csf('color_number_id')]]['item_number_id'] = $val[csf('item_number_id')];
				$data_array[$line_name][$val[csf('location')]][$val[csf('floor_id')]][$val[csf('po_break_down_id')]][$val[csf('item_number_id')]][$val[csf('color_number_id')]]['floor_id'] = $val[csf('floor_id')];
				$data_array[$line_name][$val[csf('location')]][$val[csf('floor_id')]][$val[csf('po_break_down_id')]][$val[csf('item_number_id')]][$val[csf('color_number_id')]]['remarks'] = $val[csf('remarks')];
				$data_array[$line_name][$val[csf('location')]][$val[csf('floor_id')]][$val[csf('po_break_down_id')]][$val[csf('item_number_id')]][$val[csf('color_number_id')]]['prod_reso_allo'] = $val[csf('prod_reso_allo')];
				$data_array[$line_name][$val[csf('location')]][$val[csf('floor_id')]][$val[csf('po_break_down_id')]][$val[csf('item_number_id')]][$val[csf('color_number_id')]]['order_quantity'] = $val[csf('order_quantity')];
			}
			$po_id_arr[$val[csf('po_break_down_id')]] = $val[csf('po_break_down_id')];
			$color_id_arr[$val[csf('color_number_id')]] = $val[csf('color_number_id')];
		}
		// echo "<pre>";
		// print_r($data_array);
		// echo "</pre>";
		$poId = implode(",", $po_id_arr);
		$colorId = implode(",", $color_id_arr);
		$sql_prod= "SELECT a.location, a.floor_id,a.sewing_line, a.prod_reso_allo,a.po_break_down_id, c.color_number_id,a.item_number_id, a.production_type,
		sum(case when a.production_type=4 and a.production_date<=$prod_date then b.production_qnty else 0 end) as total_input,
		sum(case when a.production_type=4 and a.production_date=$prod_date then b.production_qnty else 0 end) as today_input,
		sum(case when a.production_type=5 and a.production_date<=$prod_date then b.production_qnty else 0 end) as total_output,
		sum(case when a.production_type=5 and a.production_date=$prod_date then b.production_qnty else 0 end) as today_output


		FROM pro_garments_production_mst a,pro_garments_production_dtls b, wo_po_color_size_breakdown c
		WHERE a.company_id=$company_name and a.production_source = '1' and a.production_type in(4,5) and b.production_type in(4,5) and a.production_date<=$prod_date and c.id=b.color_size_break_down_id and a.id=b.mst_id and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.po_break_down_id in($poId) and a.po_break_down_id=c.po_break_down_id and c.status_active=1 and c.is_deleted=0 group by a.location, a.floor_id,a.sewing_line, a.prod_reso_allo,a.po_break_down_id, c.color_number_id,a.item_number_id, a.production_type";
		// echo $sql_prod;die();
		$sql_prod_res = sql_select($sql_prod);
		$prod_data = array();
		foreach ($sql_prod_res as $key => $val)
		{
			if($val[csf("prod_reso_allo")]==1)
            {
        		$line_resource_mst_arr=explode(",",$prod_reso_line_arr[$val[csf('sewing_line')]]);
				$line_name="";
				foreach($line_resource_mst_arr as $resource_id)
				{
					if($line_name == ""){$line_name=$sewLineArr[$resource_id];}
					else{$line_name=",".$sewLineArr[$resource_id];}
				}

				$prod_data[$line_name][$val[csf('location')]][$val[csf('floor_id')]][$val[csf('po_break_down_id')]][$val[csf('item_number_id')]][$val[csf('color_number_id')]][$val[csf('production_type')]]['today_input'] += $val[csf('today_input')];
				$prod_data[$line_name][$val[csf('location')]][$val[csf('floor_id')]][$val[csf('po_break_down_id')]][$val[csf('item_number_id')]][$val[csf('color_number_id')]][$val[csf('production_type')]]['total_input'] += $val[csf('total_input')];

				$prod_data[$line_name][$val[csf('location')]][$val[csf('floor_id')]][$val[csf('po_break_down_id')]][$val[csf('item_number_id')]][$val[csf('color_number_id')]][$val[csf('production_type')]]['today_output'] += $val[csf('today_output')];
				$prod_data[$line_name][$val[csf('location')]][$val[csf('floor_id')]][$val[csf('po_break_down_id')]][$val[csf('item_number_id')]][$val[csf('color_number_id')]][$val[csf('production_type')]]['total_output'] += $val[csf('total_output')];

			}
			else
			{
				$line_name=$sewLineArr[$val[csf('sewing_line')]];
				$prod_data[$line_name][$val[csf('location')]][$val[csf('floor_id')]][$val[csf('po_break_down_id')]][$val[csf('item_number_id')]][$val[csf('color_number_id')]][$val[csf('production_type')]]['today_input'] += $val[csf('today_input')];
				$prod_data[$line_name][$val[csf('location')]][$val[csf('floor_id')]][$val[csf('po_break_down_id')]][$val[csf('item_number_id')]][$val[csf('color_number_id')]][$val[csf('production_type')]]['total_input'] += $val[csf('total_input')];

				$prod_data[$line_name][$val[csf('location')]][$val[csf('floor_id')]][$val[csf('po_break_down_id')]][$val[csf('item_number_id')]][$val[csf('color_number_id')]][$val[csf('production_type')]]['today_output'] += $val[csf('today_output')];
				$prod_data[$line_name][$val[csf('location')]][$val[csf('floor_id')]][$val[csf('po_break_down_id')]][$val[csf('item_number_id')]][$val[csf('color_number_id')]][$val[csf('production_type')]]['total_output'] += $val[csf('total_output')];
			}
		}
		// echo "<pre>";
		// print_r($prod_data);
		// echo "</pre>";
		/*$sql_order= "SELECT c.po_break_down_id, c.color_number_id,c.item_number_id, c.order_quantity
		FROM wo_po_color_size_breakdown c
		WHERE  c.po_break_down_id in($poId) and c.status_active=1 and c.is_deleted=0";

		$sql_order_res = sql_select($sql_order);
		$order_qnty_data = [];
		foreach ($sql_order_res as $key => $val)
		{
			$order_qnty_data[$val[csf('po_break_down_id')]][$val[csf('item_number_id')]][$val[csf('color_number_id')]]['order_quantity'] += $val[csf('order_quantity')];
		}*/

		ob_start();
		?>
        <fieldset style="width:1860px; margin: 0 auto">
			<table width="1840" cellpadding="0" cellspacing="0" align="left">
				<tr class="form_caption">
					<td colspan="13" align="center"><strong><? echo $report_title; ?></strong></td>
				</tr>
				<tr class="form_caption">
					<td colspan="13" align="center"><strong><? echo $companyArr[$comapny_id]; ?></strong></td>
				</tr>
			</table>
        	<table id="table_header_1" class="rpt_table" width="1840" cellpadding="0" cellspacing="0" border="1" rules="all" align="left">
				<thead>
					<th style="word-wrap: break-word;word-break: break-all;" width="40">SL</th>
					<th style="word-wrap: break-word;word-break: break-all;" width="100">Date</th>
					<th style="word-wrap: break-word;word-break: break-all;" width="100">Interna.Ref. No</th>
					<th style="word-wrap: break-word;word-break: break-all;" width="100">Order No</th>
					<th style="word-wrap: break-word;word-break: break-all;" width="100">Style No</th>
					<th style="word-wrap: break-word;word-break: break-all;" width="130">Garments Item</th>
					<th style="word-wrap: break-word;word-break: break-all;" width="100">Garments Color</th>
					<th style="word-wrap: break-word;word-break: break-all;" width="80">Order Quantity</th>
					<th style="word-wrap: break-word;word-break: break-all;" width="100">Floor Name</th>
					<th style="word-wrap: break-word;word-break: break-all;" width="100">Line No</th>
					<th style="word-wrap: break-word;word-break: break-all;" width="75">Today Input</th>
					<th style="word-wrap: break-word;word-break: break-all;" width="75">Total Input</th>
					<th style="word-wrap: break-word;word-break: break-all;" width="75">Today Output</th>
					<th style="word-wrap: break-word;word-break: break-all;" width="75">Total Output</th>
					<th style="word-wrap: break-word;word-break: break-all;" width="75">Total Prod</th>
                    <th style="word-wrap: break-word;word-break: break-all;" width="75">Line Balance</th>
                    <th style="word-wrap: break-word;word-break: break-all;" width="75">Rejection</th>
                    <th style="word-wrap: break-word;word-break: break-all;" width="75">Last Output <br>date<br></th>
                    <th style="word-wrap: break-word;word-break: break-all;" width="75">Today Date</th>
                    <th style="word-wrap: break-word;word-break: break-all;" width="75">Idol Date</th>
					<th style="word-wrap: break-word;word-break: break-all;" width="120">Floor Comments</th>
				</thead>
			</table>
			<div style="width:1860px; max-height:400px; overflow-y:scroll" id="scroll_body">
				<table class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body" width="1840" align="left">
					<?
					$i=1;
					foreach ($data_array as $line_name => $line_data)
					{
						foreach ($line_data as $location_id => $location_data)
						{
							foreach ($location_data as $floor_id => $floor_data)
							{
								foreach ($floor_data as $po_id => $po_data)
								{
									foreach ($po_data as $item_id => $item_data)
									{
										foreach ($item_data as $color_id => $row)
										{
											$search_string = $row['production_date']."__".$line_name."__".$location_id."__".$floor_id."__".$po_id."__".$item_id."__".$color_id;
											$today_in_qnty = $prod_data[$line_name][$location_id][$floor_id][$po_id][$item_id][$color_id][4]['today_input'];
											$total_in_qnty = $prod_data[$line_name][$location_id][$floor_id][$po_id][$item_id][$color_id][4]['total_input'];
											$today_out_qnty = $prod_data[$line_name][$location_id][$floor_id][$po_id][$item_id][$color_id][5]['today_output'];
											$total_out_qnty = $prod_data[$line_name][$location_id][$floor_id][$po_id][$item_id][$color_id][5]['total_output'];

											if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
											?>
						                	<tr title="<? echo "po_id=$po_id,Item_id=$item_id,location_id=$location_id,Floor_id=$floor_id,Line_id=$line_name,Color_id=$color_id" ?>" bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
						                		<td style="word-wrap: break-word;word-break: break-all;" width="40"><? echo $i;?></td>
												<td style="word-wrap: break-word;word-break: break-all;" width="100"><? echo $row['production_date'];?></td>
						                		<td style="word-wrap: break-word;word-break: break-all;" width="100"><? echo $row['grouping'];?></td>
						                		<td style="word-wrap: break-word;word-break: break-all;" width="100"><? echo $row['po_no'];?></td>
						                		<td style="word-wrap: break-word;word-break: break-all;" width="100"><? echo $row['style_ref_no'];?></td>
						                		<td style="word-wrap: break-word;word-break: break-all;" width="130"><? echo $garments_item[$item_id];?></td>
						                		<td style="word-wrap: break-word;word-break: break-all;" width="100"><? echo $color_library[$color_id];?></td>
						                		<td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo $row['order_quantity'];?></td>
						                		<td style="word-wrap: break-word;word-break: break-all;" width="100"><? echo $floor_library[$floor_id];?></td>
						                		<td style="word-wrap: break-word;word-break: break-all;" width="100"><? echo $line_name;?></td>
												<td style="word-wrap: break-word;word-break: break-all;" width="75" align="right"><? echo $today_in_qnty;?></td>
												<td style="word-wrap: break-word;word-break: break-all;" width="75" align="right"><? echo $total_in_qnty;?></td>
												<td style="word-wrap: break-word;word-break: break-all;" width="75" align="right"><? echo $today_out_qnty;?></td>
						                		<td style="word-wrap: break-word;word-break: break-all;" width="75" align="right"><? echo $total_out_qnty;?></td>
						                		<td style="word-wrap: break-word;word-break: break-all;" width="75" align="right"><? echo ($total_in_qnty - $total_out_qnty);?></td>
						                		<td style="word-wrap: break-word;word-break: break-all;" width="75" align="right"><? echo ($total_in_qnty - $total_out_qnty)-$row['reject_qty'];?></td>
												<td style="word-wrap: break-word;word-break: break-all;" width="75" align="right"><a href="##" onclick="openmypage_rej('<? echo $search_string;?>','reject_qty');"><?echo $row['reject_qty'];?></a>
											   </td>
												<td style="word-wrap: break-word;word-break: break-all;" width="75" align="center"><? echo $row['last_output']; ?></td>
												<td style="word-wrap: break-word;word-break: break-all;" width="75" align="center"><? echo $row['current_date']; ?></td>
												<td style="word-wrap: break-word;word-break: break-all;" width="75" align="center"><? echo $row['current_date']-$row['last_output']." days"; ?></td>
						                		<td style="word-wrap: break-word;word-break: break-all;" width="120"><? echo $row['remarks'];?></td>
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
				</table>
			</div>
		</fieldset>

    	<?


	}
	else
	{
		$poDataArr=array();
		$poData=sql_select("select a.job_no, a.buyer_name, a.style_ref_no, a.total_set_qnty as ratio, b.id, b.po_number, b.po_quantity from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name='$comapny_id'");
		foreach($poData as $row)
		{
			$poDataArr[$row[csf('id')]]['no']=$row[csf('po_number')];
			$poDataArr[$row[csf('id')]]['job_no']=$row[csf('job_no')];
			$poDataArr[$row[csf('id')]]['buyer_name']=$row[csf('buyer_name')];
			$poDataArr[$row[csf('id')]]['style_ref']=$row[csf('style_ref_no')];
			$poDataArr[$row[csf('id')]]['qty']=$row[csf('ratio')]*$row[csf('po_quantity')];
		}

		$prod_reso_allo=return_field_value("auto_update","variable_settings_production","company_name=$comapny_id and variable_list=23 and is_deleted=0 and status_active=1");
		if($prod_reso_allo!=1)
		{
			echo "<div style='width:1320px;' align='center'><font style='color:#FF0000; font-size:16px'>Set Variable Settings Yes For Production Resource Allocation</font></div>";
			die;
		}

		$inputDataArray=array();
		$prod_sql="select po_break_down_id, item_number_id, location, floor_id, sewing_line, production_quantity as qnty from pro_garments_production_mst where company_id='$comapny_id' and production_date=$txt_date and production_type=4 and status_active=1 and is_deleted=0";
		$dataArray=sql_select($prod_sql);
		foreach($dataArray as $row)
		{
			$inputDataArray[$row[csf('location')]][$row[csf('floor_id')]][$row[csf('sewing_line')]]['qnty']+=$row[csf('qnty')];
			$inputDataArray[$row[csf('location')]][$row[csf('floor_id')]][$row[csf('sewing_line')]]['po_id'].=$row[csf('po_break_down_id')].",";
			$inputDataArray[$row[csf('location')]][$row[csf('floor_id')]][$row[csf('sewing_line')]]['item_id'].=$row[csf('item_number_id')].",";
		}

		$sql="select a.id, a.location_id, a.floor_id, a.line_number, b.active_machine, b.pr_date, b.man_power, b.operator, b.helper, b.line_chief, b.target_per_hour, b.working_hour from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id and a.company_id=$comapny_id and a.location_id like '$location'  and a.id like '$line' and pr_date=$txt_date $floor_con3 order by a.location_id, a.floor_id, a.line_number";
		$result=sql_select($sql);
		ob_start();
		?>
        <fieldset style="width:1310px">
			<table width="1300" cellpadding="0" cellspacing="0">
				<tr class="form_caption">
					<td colspan="14" align="center"><strong><? echo $report_title; ?></strong></td>
				</tr>
				<tr class="form_caption">
					<td colspan="14" align="center"><strong><? echo $companyArr[$comapny_id]; ?></strong></td>
				</tr>
			</table>
        	<table id="table_header_1" class="rpt_table" width="1300" cellpadding="0" cellspacing="0" border="1" rules="all">
				<thead>
					<th width="40">SL</th>
					<th width="80">Line No</th>
					<th width="70">Buyer</th>
					<th width="130">Order No</th>
					<th width="130">Style Ref.</th>
					<th width="150">Garments Item</th>
					<th width="80">Order Qty.</th>
					<th width="75">Machine Qty.</th>
					<th width="75">Worker</th>
					<th width="75">Operator</th>
					<th width="75">Helper</th>
                    <th width="75">Working Hour</th>
					<th width="120">Line Chief</th>
					<th>Input Qty.</th>
				</thead>
			</table>
			<div style="width:1300px; max-height:400px; overflow-y:scroll" id="scroll_body">
				<table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
                <?
					$i=1; $location_array=array(); $floor_array=array();
					foreach( $result as $row )
					{
						if(in_array($row[csf("location_id")], $location_array))
						{
							if(!in_array($row[csf("floor_id")], $floor_array))
							{
								if($i!=1)
								{
									echo '<tr bgcolor="#CCCCCC">
											<td colspan="7" align="right">Floor Total</td>
											<td align="right">'.$floor_machine_qnty.'</td>
											<td align="right">'.$floor_worker.'</td>
											<td align="right">'.$floor_operator.'</td>
											<td align="right">'.$floor_helper.'</td>
											<td align="right">'.$floor_working_hour.'</td>
											<td>&nbsp;</td>
											<td align="right">'.$floor_input_qnty.'</td>
										</tr>';

										$floor_machine_qnty=0;
										$floor_worker=0;
										$floor_operator=0;
										$floor_helper=0;
										$floor_working_hour=0;
										$floor_input_qnty=0;
								}

								echo '<tr bgcolor="#EFEFEF"><td colspan="14"><b>Floor name: '.$floorArr[$row[csf('floor_id')]].'; Location name: '.$locationArr[$row[csf('location_id')]].'</b></td></tr>';
								$k++;
								$floor_array[]=$row[csf("floor_id")];
							}
						}
						else
						{
							if($i!=1)
							{
								echo '<tr bgcolor="#CCCCCC">
											<td colspan="7" align="right">Floor Total</td>
											<td align="right">'.$floor_machine_qnty.'</td>
											<td align="right">'.$floor_worker.'</td>
											<td align="right">'.$floor_operator.'</td>
											<td align="right">'.$floor_helper.'</td>
											<td align="right">'.$floor_working_hour.'</td>
											<td>&nbsp;</td>
											<td align="right">'.$floor_input_qnty.'</td>
										</tr>
										<tr bgcolor="#E9F3FF">
											<td colspan="7" align="right">Location Total</td>
											<td align="right">'.$location_machine_qnty.'</td>
											<td align="right">'.$location_worker.'</td>
											<td align="right">'.$location_operator.'</td>
											<td align="right">'.$location_helper.'</td>
											<td align="right">'.$location_working_hour.'</td>
											<td>&nbsp;</td>
											<td align="right">'.$location_input_qnty.'</td>
										</tr>
										';

								$floor_machine_qnty=0;
								$floor_worker=0;
								$floor_operator=0;
								$floor_helper=0;
								$floor_working_hour=0;
								$floor_input_qnty=0;

								$location_machine_qnty=0;
								$location_worker=0;
								$location_operator=0;
								$location_helper=0;
								$location_working_hour=0;
								$location_input_qnty=0;
							}

						  echo '<tr bgcolor="#EFEFEF"><td colspan="14"><b>Floor name: '.$floorArr[$row[csf('floor_id')]].'; Location name: '.$locationArr[$row[csf('location_id')]].'</b></td></tr>';

							$location_array[]=$row[csf("location_id")];
							$floor_array=array();
							$floor_array[]=$row[csf("floor_id")];
						}

						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

						$sewing_line='';
						$line_number=explode(",",$row[csf('line_number')]);
						foreach($line_number as $val)
						{
							if($sewing_line=='') $sewing_line=$lineArr[$val]; else $sewing_line.=",".$lineArr[$val];
						}

						$po_no=''; $style_ref=''; $gmts_item=''; $buyer=''; $po_qnty=0;
						$po_id=array_unique(explode(",",substr($inputDataArray[$row[csf('location_id')]][$row[csf('floor_id')]][$row[csf('id')]]['po_id'],0,-1)));
						foreach($po_id as $val)
						{
							if($po_no=='')
							{
								$po_no=$poDataArr[$val]['no'];
								$buyer=$buyerArr[$poDataArr[$val]['buyer_name']];
								$style_ref=$poDataArr[$val]['style_ref'];
							}
							else
							{
								$po_no.=",".$poDataArr[$val]['no'];
								$buyer.=",".$buyerArr[$poDataArr[$val]['buyer_name']];
								$style_ref.=",".$poDataArr[$val]['style_ref'];
							}
							$po_qnty+=$poDataArr[$val]['qty'];
						}

						$item_id=array_unique(explode(",",substr($inputDataArray[$row[csf('location_id')]][$row[csf('floor_id')]][$row[csf('id')]]['item_id'],0,-1)));
						foreach($item_id as $gmts_id)
						{
							if($gmts_item=='') $gmts_item=$garments_item[$gmts_id]; else $gmts_item.=",".$garments_item[$gmts_id];
						}

						$buyer=array_filter(array_unique(explode(",",$buyer)));
						$style_ref=array_filter(array_unique(explode(",",$style_ref)));
						$input_qnty=$inputDataArray[$row[csf('location_id')]][$row[csf('floor_id')]][$row[csf('id')]]['qnty'];
					?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                        	<td width="40"><? echo $i; ?></td>
                            <td width="80"><p><? echo $sewing_line; ?>&nbsp;</p></td>
                            <td width="70"><p><? echo implode(",",$buyer); ?>&nbsp;</p></td>
                            <td width="130"><p><? echo $po_no; ?>&nbsp;</p></td>
                            <td width="130"><p><? echo implode(",",$style_ref); ?>&nbsp;</p></td>
                            <td width="150"><p><? echo $gmts_item; ?>&nbsp;</p></td>
                            <td width="80" align="right"><p>&nbsp;<? echo $po_qnty; ?></p></td>
                            <td width="75" align="right">&nbsp;<? echo $row[csf('active_machine')]; ?></th>
                            <td width="75" align="right">&nbsp;<? echo $row[csf('man_power')]; ?></th>
                            <td width="75" align="right">&nbsp;<? echo $row[csf('operator')]; ?></th>
                            <td width="75" align="right">&nbsp;<? echo $row[csf('helper')]; ?></td>
                            <td width="75" align="right">&nbsp;<? echo $row[csf('working_hour')]; ?></td>
                            <td width="120"><? echo $row[csf('line_chief')]; ?>&nbsp;</td>
                            <td align="right">&nbsp;<? echo $input_qnty; ?></td>
                        </tr>
                    <?
						$i++;

						$floor_machine_qnty+=$row[csf('active_machine')];
						$floor_worker+=$row[csf('man_power')];
						$floor_operator+=$row[csf('operator')];
						$floor_helper+=$row[csf('helper')];
						$floor_working_hour+=$row[csf('working_hour')];
						$floor_input_qnty+=$input_qnty;

						$location_machine_qnty+=$row[csf('active_machine')];
						$location_worker+=$row[csf('man_power')];
						$location_operator+=$row[csf('operator')];
						$location_helper+=$row[csf('helper')];
						$location_working_hour+=$row[csf('working_hour')];
						$location_input_qnty+=$input_qnty;

						$tot_machine_qnty+=$row[csf('active_machine')];
						$tot_worker+=$row[csf('man_power')];
						$tot_operator+=$row[csf('operator')];
						$tot_helper+=$row[csf('helper')];
						$tot_working_hour+=$row[csf('working_hour')];
						$tot_input_qnty+=$input_qnty;
					}

					if(count($result)>0)
					{
						echo '<tr bgcolor="#CCCCCC">
								<td colspan="7" align="right">Floor Total</td>
								<td align="right">'.$floor_machine_qnty.'</td>
								<td align="right">'.$floor_worker.'</td>
								<td align="right">'.$floor_operator.'</td>
								<td align="right">'.$floor_helper.'</td>
								<td align="right">'.$floor_working_hour.'</td>
								<td>&nbsp;</td>
								<td align="right">'.$floor_input_qnty.'</td>
							</tr>
							<tr bgcolor="#E9F3FF">
								<td colspan="7" align="right">Location Total</td>
								<td align="right">'.$location_machine_qnty.'</td>
								<td align="right">'.$location_worker.'</td>
								<td align="right">'.$location_operator.'</td>
								<td align="right">'.$location_helper.'</td>
								<td align="right">'.$location_working_hour.'</td>
								<td>&nbsp;</td>
								<td align="right">'.$location_input_qnty.'</td>
							</tr>
							';
					}
				?>
					<tfoot>
                        <th colspan="7" align="right">Grand Total</th>
                        <th align="right"><? echo $tot_machine_qnty; ?></th>
                        <th align="right"><? echo $tot_worker; ?></th>
                        <th align="right"><? echo $tot_operator; ?></th>
                        <th align="right"><? echo $tot_helper; ?></th>
                        <th align="right"><? echo $tot_working_hour; ?></th>
                        <th>&nbsp;</th>
                        <th align="right"><? echo $tot_input_qnty; ?></th>
					</tfoot>
				</table>
			</div>
		</fieldset>

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
	$filename=$user_id."_".$name.".xls";
	echo "$total_data####$filename";
	exit();
}

if($action=="prev_qnty")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);

	if($prod_type==4) $caption="Input"; else $caption="Production";

	?>
	<script>

		function print_window()
		{
			document.getElementById('scroll_body').style.overflow="auto";
			document.getElementById('scroll_body').style.maxHeight="none";

			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');

			d.close();
			document.getElementById('scroll_body').style.overflowY="scroll";
			document.getElementById('scroll_body').style.maxHeight="230px";
		}

	</script>
		<div style="width:455px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
		<fieldset style="width:440px; margin-left:17px">
			<div id="report_container">
				<table border="1" class="rpt_table" rules="all" width="420" cellpadding="0" cellspacing="0">
					<thead>
	                	<th width="50">SL</th>
	                    <th width="120"><? echo $caption; ?> Date</th>
	                    <th><? echo $caption; ?> Qnty</th>
					</thead>
	             </table>
	             <div style="width:437px; max-height:320px; overflow-y:scroll" id="scroll_body">
	                 <table border="1" class="rpt_table" rules="all" width="420" cellpadding="0" cellspacing="0">
	                    <?
	                    $i=1; $total_qnty=0;
	                    $sql="SELECT production_date, sum(production_quantity) AS qnty from pro_garments_production_mst
	                    where po_break_down_id in(".$po_id.") and item_number_id='".$item_id."' and location='".$location."' and floor_id='".$floor_id."' and sewing_line='".$sewing_line."' and prod_reso_allo='".$prod_reso_allo."' and production_type='$prod_type' and production_date<'$prod_date' and is_deleted=0 and status_active=1 group by production_date";
	                    $result=sql_select($sql);
	                    $result=sql_select($sql);
	        			foreach($result as $row)
	                    {
	                        if ($i%2==0)
	                            $bgcolor="#E9F3FF";
	                        else
	                            $bgcolor="#FFFFFF";

	                        $total_qnty+=$row[csf('qnty')];
	                    ?>
	                        <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
	                            <td width="50"><? echo $i; ?></td>
	                            <td width="120" align="center"><? echo change_date_format($row[csf('production_date')]); ?></td>
	                            <td align="right"><? echo $row[csf('qnty')]; ?>&nbsp;</td>
	                        </tr>
	                    <?
	                    $i++;
	                    }
	                    ?>
	                    <tfoot>
	                        <th colspan="2" align="right">Total</th>
	                        <th align="right"><? echo $total_qnty; ?>&nbsp;</th>
	                    </tfoot>
	                </table>
	            </div>
	        </div>
		</fieldset>
	<?
	exit();
}

if($action=="tot_prod")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);

	?>
	<script>

		function print_window()
		{
			document.getElementById('scroll_body').style.overflow="auto";
			document.getElementById('scroll_body').style.maxHeight="none";

			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');

			d.close();
			document.getElementById('scroll_body').style.overflowY="scroll";
			document.getElementById('scroll_body').style.maxHeight="230px";
		}

	</script>
		<div style="width:455px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
		<fieldset style="width:340px; margin-left:70px">
			<div id="report_container">
				<table border="1" class="rpt_table" rules="all" width="320" cellpadding="0" cellspacing="0">
					<thead>
	                	<th width="50">SL</th>
	                    <th width="120">Production Date</th>
	                    <th>Production Qnty</th>
					</thead>
	             </table>
	             <div style="width:337px; max-height:270px; overflow-y:scroll" id="scroll_body">
	                 <table border="1" class="rpt_table" rules="all" width="320" cellpadding="0" cellspacing="0">
	                    <?
	                    $i=1; $total_qnty=0;
	                    $sql="SELECT production_date, sum(production_quantity) AS qnty from pro_garments_production_mst
	                    where po_break_down_id in (".$po_id.") and item_number_id='".$item_id."' and location='".$location."' and floor_id='".$floor_id."' and sewing_line='".$sewing_line."' and prod_reso_allo='".$prod_reso_allo."' and production_type='$prod_type' and production_date<='$prod_date' and is_deleted=0 and status_active=1 group by production_date";
	                    // echo $sql;
	                    $result=sql_select($sql);
	        			foreach($result as $row)
	                    {
	                        if ($i%2==0)
	                            $bgcolor="#E9F3FF";
	                        else
	                            $bgcolor="#FFFFFF";

	                        $total_qnty+=$row[csf('qnty')];
	                    ?>
	                        <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
	                            <td width="50"><? echo $i; ?></td>
	                            <td width="120" align="center"><? echo change_date_format($row[csf('production_date')]); ?></td>
	                            <td align="right"><? echo $row[csf('qnty')]; ?>&nbsp;</td>
	                        </tr>
	                    <?
	                    $i++;
	                    }
	                    ?>
	                    <tfoot>
	                        <th colspan="2" align="right">Total</th>
	                        <th align="right"><? echo $total_qnty; ?>&nbsp;</th>
	                    </tfoot>
	                </table>
	            </div>
	        </div>
		</fieldset>
	<?
	exit();
}

if($action=="today_prod")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
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
		<div style="width:980px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
		<fieldset style="width:970px; margin-left:5px">
			<div id="report_container" >
				<table border="1" class="rpt_table" rules="all" width="970" cellpadding="0" cellspacing="0">
	                <?

					if($db_type==0)
					{
						$dataArray=sql_select("select TIME_FORMAT( d.prod_start_time, '%H:%i' ) as prod_start_time, TIME_FORMAT( d.prod_start_time, '%H' ) as start_hour, TIME_FORMAT( d.prod_start_time, '%i' ) as start_min from prod_resource_mst a, prod_resource_dtls b, prod_resource_dtls_time d where a.id=b.mst_id and b.mast_dtl_id=d.mast_dtl_id and d.shift_id=1 and b.pr_date='$prod_date' and a.id='$sewing_line'");
						$prod_start_hour=$dataArray[0][csf('prod_start_time')];
						if($prod_start_hour=="") $prod_start_hour="08:00";
						$start_time=explode(":",$prod_start_hour);
						$hour=$start_time[0]; $minutes=$start_time[1]; $last_hour=23;
						$start_hour_arr=array(); $s=1;

						$start_hour=$prod_start_hour;
						for($j=$hour;$j<$last_hour;$j++)
						{
							$start_hour=add_time($start_hour,60);
							$start_hour_arr[$j+1]=$start_hour;
						}
						$start_hour_arr[$j+1]='23:59:59';

						$sql="SELECT ";
						foreach($start_hour_arr as $val)
						{
							$z++;
							if($s==1)
							{
								$sql.=" sum(case when production_hour<='$val' then production_quantity else 0 end) AS am$z ";
							}
							else
							{
								$sql.=", sum(case when production_hour>'$prev_hour' and production_hour<='$val' then production_quantity else 0 end) AS am$z ";
							}

							$prev_hour=$val;
							$s++;
						}

						$sql.="from pro_garments_production_mst where po_break_down_id in(".$po_id.") and item_number_id='".$item_id."' and location='".$location."' and floor_id='".$floor_id."' and sewing_line='".$sewing_line."' and prod_reso_allo='".$prod_reso_allo."' and production_type='$prod_type' and production_date='$prod_date' and is_deleted=0 and status_active=1";
						//echo $sql;die;
						/*$sql="SELECT
							sum(case when production_hour>'00:00' and  production_hour<='01:00' then  production_quantity else 0 end ) AS am1,
							sum(case when production_hour>'01:00' and  production_hour<='02:00' then production_quantity else 0 end ) AS am2,
							sum(case when production_hour>'02:00' and  production_hour<='03:00' then production_quantity else 0 end ) AS am3,
							sum(case when production_hour>'03:00' and  production_hour<='04:00' then production_quantity else 0 end ) AS am4,
							sum(case when production_hour>'04:00' and  production_hour<='05:00' then production_quantity else 0 end ) AS am5,
							sum(case when production_hour>'05:00' and  production_hour<='06:00' then production_quantity else 0 end ) AS am6,
							sum(case when production_hour>'06:00' and  production_hour<='07:00' then production_quantity else 0 end ) AS am7,
							sum(case when production_hour>'07:00' and  production_hour<='08:00' then production_quantity else 0 end ) AS am8,
							sum(case when production_hour>'08:00' and  production_hour<='09:00' then production_quantity else 0 end ) AS am9,
							sum(case when production_hour>'09:00' and  production_hour<='10:00' then production_quantity else 0 end ) AS am10,
							sum(case when production_hour>'10:00' and  production_hour<='11:00' then production_quantity else 0 end ) AS am11,
							sum(case when production_hour>'11:00' and  production_hour<='12:00' then production_quantity else 0 end ) AS pm12,
							sum(case when production_hour>'12:00' and  production_hour<='13:00' then production_quantity else 0 end ) AS pm13,
							sum(case when production_hour>'13:00' and  production_hour<='14:00' then production_quantity else 0 end ) AS pm14,
							sum(case when production_hour>'14:00' and  production_hour<='15:00' then production_quantity else 0 end ) AS pm15,
							sum(case when production_hour>'15:00' and  production_hour<='16:00' then production_quantity else 0 end ) AS pm16,
							sum(case when production_hour>'16:00' and  production_hour<='17:00' then production_quantity else 0 end ) AS pm17,
							sum(case when production_hour>'17:00' and  production_hour<='18:00' then production_quantity else 0 end ) AS pm18,
							sum(case when production_hour>'18:00' and  production_hour<='19:00' then production_quantity else 0 end ) AS pm19,
							sum(case when production_hour>'19:00' and  production_hour<='20:00' then production_quantity else 0 end ) AS pm20,
							sum(case when production_hour>'20:00' and  production_hour<='21:00' then production_quantity else 0 end ) AS pm21,
							sum(case when production_hour>'21:00' and  production_hour<='22:00' then production_quantity else 0 end ) AS pm22,
							sum(case when production_hour>'22:00' and  production_hour<='23:00' then production_quantity else 0 end ) AS pm23,
							sum(case when production_hour>'23:00' and  production_hour<='23:59' then production_quantity else 0 end ) AS pm24

							 from pro_garments_production_mst
							where po_break_down_id in(".$po_id.") and item_number_id='".$item_id."' and location='".$location."' and floor_id='".$floor_id."' and sewing_line='".$sewing_line."' and prod_reso_allo='".$prod_reso_allo."' and production_type='$prod_type' and production_date='$prod_date' and is_deleted=0 and status_active=1";*/
					}
					else
					{
						$dataArray=sql_select("select TO_CHAR(d.prod_start_time,'HH24:MI') as prod_start_time, TO_CHAR(d.prod_start_time,'HH24') as start_hour, TO_CHAR(d.prod_start_time,'MI') as start_min, TO_CHAR(d.lunch_start_time,'HH24:MI') as lunch_start_time from prod_resource_mst a, prod_resource_dtls b, prod_resource_dtls_time d where a.id=b.mst_id and b.mast_dtl_id=d.mast_dtl_id and d.shift_id=1 and b.pr_date='$prod_date' and a.id='$sewing_line'");
						$prod_start_hour=$dataArray[0][csf('prod_start_time')];
						if($prod_start_hour=="") $prod_start_hour="08:00";
						$start_time=explode(":",$prod_start_hour);
						$hour=$start_time[0]; $minutes=$start_time[1]; $last_hour=23;
						$start_hour_arr=array(); $s=1;

						$start_hour=$prod_start_hour;
						for($j=$hour;$j<$last_hour;$j++)
						{
							$start_hour=add_time($start_hour,60);
							$start_hour_arr[$j+1]=$start_hour;
						}
						$start_hour_arr[$j+1]='23:59:59';

						$sql="SELECT ";
						foreach($start_hour_arr as $val)
						{
							$z++;
							if($s==1)
							{
								$sql.="sum(case when TO_CHAR(production_hour,'HH24:MI:SS')<='$val' then production_quantity else 0 end) AS am$z ";
							}
							else
							{
								$sql.=", sum(case when TO_CHAR(production_hour,'HH24:MI:SS')>'$prev_hour' and TO_CHAR(production_hour,'HH24:MI:SS')<='$val' then production_quantity else 0 end) AS am$z ";
							}

							$prev_hour=$val;
							$s++;
						}

						$sql.="from pro_garments_production_mst where po_break_down_id in(".$po_id.") and item_number_id='".$item_id."' and location='".$location."' and floor_id='".$floor_id."' and sewing_line='".$sewing_line."' and prod_reso_allo='".$prod_reso_allo."' and production_type='$prod_type' and production_date='$prod_date' and is_deleted=0 and status_active=1";
						//echo $sql;
					}
					// echo $sql;
					$result=sql_select($sql);
					foreach($result as $row);
					//$total_qnty=$row[csf('am1')]+$row[csf('am2')]+$row[csf('am3')]+$row[csf('am4')]+$row[csf('am5')]+$row[csf('am6')]+$row[csf('am7')]+$row[csf('am8')]+$row[csf('am9')]+$row[csf('am10')]+$row[csf('am11')]+$row[csf('pm12')]+$row[csf('pm13')]+$row[csf('pm14')]+$row[csf('pm15')]+$row[csf('pm16')]+$row[csf('pm17')]+$row[csf('pm18')]+$row[csf('pm19')]+$row[csf('pm20')]+$row[csf('pm21')]+$row[csf('pm22')]+$row[csf('pm23')]+$row[csf('pm24')];
					// bgcolor="#E9F3FF"
					echo '<thead><tr>';
					$x=1;
					foreach($start_hour_arr as $val)
					{
						if($x<13)
						{
							echo '<th width="70">'.$val.'</th>';
							$x++;
						}
					}
					echo '</tr></thead><tr bgcolor="#E9F3FF">';

					$x=1; $total_qnty=0;
					foreach($start_hour_arr as $val)
					{
						if($x<13)
						{
							echo '<td width="70" align="right">'.$row[csf('am'.$x)].'&nbsp;&nbsp;</td>';
							$total_qnty+=$row[csf('am'.$x)];
							$x++;
						}
					}
					echo '</tr>';

					array_splice($start_hour_arr,0, 12);
					$x=13;
					if(count($start_hour_arr)>0)
					{
						echo '<thead><tr>';
						foreach($start_hour_arr as $val)
						{
							echo '<th width="70">'.$val.'</th>';
							$x++;
						}
						$x=13;
						echo '</tr></thead><tr bgcolor="#E9F3FF">';
						foreach($start_hour_arr as $val)
						{
							echo '<td width="70" align="right">'.$row[csf('am'.$x)].'&nbsp;&nbsp;</td>';
							$total_qnty+=$row[csf('am'.$x)];
							$x++;
						}
						echo '</tr>';
					}
					?>
	                <tr><td colspan="12"><strong>Total: &nbsp;&nbsp;<? echo  $total_qnty;?> </strong></td></tr>
				</table>
	        </div>
		</fieldset>
	<?
	exit();
}

if($action=="remarks_popup")
{
		echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	    extract($_REQUEST);
		//echo $company_id;
		//$sewing_line=explode("*",$sewing_line);
		//$sewing_line=implode(",",$sewing_line);
		$po_id=explode("*",$po_id);
		$po_id=implode(",",$po_id);
	    $sql_line_remark=sql_select("select remarks,production_hour from pro_garments_production_mst where company_id=".$company_id." and  floor_id=$floor_id and sewing_line in($sewing_line) and po_break_down_id in($po_id) and production_date='".$prod_date."' and status_active=1 and is_deleted=0 order by production_hour");
		?>
		<fieldset style="width:520px;  ">
            <div id="report_container">
                    <table border="1" class="rpt_table" rules="all" width="500" cellpadding="0" cellspacing="0" align="center">
                        <thead>
                            <th width="40">SL</th>
                            <th width="460">Remarks</th>
                        </thead>
                        <tbody>
                        <?
						$i=1;
                        foreach($sql_line_remark as $inf)
						{
						 if ($i%2==0)    $bgcolor="#E9F3FF";
                         else            $bgcolor="#FFFFFF";
						 if(trim($inf[csf('remarks')])!="")
						 {
						 ?>
						   <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                            <td width="30"><? echo $i; ?></td>
                            <td align="left"><? echo $inf[csf('remarks')]; ?>&nbsp;</td>


                        </tr>
						<?
						$i++;
						 }

						}


						?>
                        </tbody>


                    </table>
            </div>
        </fieldset>

              <?
}

if($action=="tot_smv_used")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$fstinput_date=$prod_type;

	$prod_resource_array=array();

	$dataArray=sql_select("select b.pr_date, b.man_power, b.working_hour, b.smv_adjust, b.smv_adjust_type from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id and a.company_id=$company_id and a.id='$sewing_line'");

	foreach($dataArray as $row)
	{
		$prod_resource_array[$row[csf('pr_date')]]['smv']=$row[csf('man_power')]*$row[csf('working_hour')]*60;
		$prod_resource_array[$row[csf('pr_date')]]['mp']=$row[csf('man_power')];
		$prod_resource_array[$row[csf('pr_date')]]['wh']=$row[csf('working_hour')];
		$prod_resource_array[$row[csf('pr_date')]]['smv_adjust']=$row[csf('smv_adjust')];
		$prod_resource_array[$row[csf('pr_date')]]['smv_adjust_type']=$row[csf('smv_adjust_type')];
	}
	?>
	<script>

		function print_window()
		{
			document.getElementById('scroll_body').style.overflow="auto";
			document.getElementById('scroll_body').style.maxHeight="none";

			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');

			d.close();
			document.getElementById('scroll_body').style.overflowY="scroll";
			document.getElementById('scroll_body').style.maxHeight="230px";
		}

	</script>
		<div style="width:680px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
		<fieldset style="width:680px; margin-left:3px">
			<div id="report_container">
				<table border="1" class="rpt_table" rules="all" width="660" cellpadding="0" cellspacing="0">
					<thead>
	                	<th width="40">SL</th>
	                    <th width="90">Production Date</th>
	                    <th width="70">Manpower</th>
	                    <th width="80">Working Hour</th>
	                    <th width="80">SMV</th>
	                    <th width="80">Adj. Type</th>
	                    <th width="80">Adj. SMV</th>
	                    <th>Actual SMV</th>
					</thead>
	             </table>
	             <div style="width:678px; max-height:280px; overflow-y:scroll" id="scroll_body">
	                 <table border="1" class="rpt_table" rules="all" width="660" cellpadding="0" cellspacing="0">
	                    <?
	                    $i=1; $total_smv_used=0;
	                    $sql="select b.date_calc from lib_capacity_calc_mst a, lib_capacity_calc_dtls b where a.id=b.mst_id and a.comapny_id='$company_id' and b.date_calc between '$fstinput_date' and '$prod_date' and day_status=1";
	                    $result=sql_select($sql);
	        			foreach($result as $row)
	                    {
	                        if ($i%2==0)
	                            $bgcolor="#E9F3FF";
	                        else
	                            $bgcolor="#FFFFFF";

							$total_adjustment=0;
							$smv_adjustmet_type=$prod_resource_array[$row[csf('date_calc')]]['smv_adjust_type'];
							if(str_replace("'","",$smv_adjustmet_type)==1) $total_adjustment=$prod_resource_array[$row[csf('date_calc')]]['smv_adjust'];
							if(str_replace("'","",$smv_adjustmet_type)==2) $total_adjustment=($prod_resource_array[$row[csf('date_calc')]]['smv_adjust'])*(-1);

							$day_smv=$prod_resource_array[$row[csf('date_calc')]]['smv']+$total_adjustment;
	                        $total_smv_used+=$day_smv;
	                    ?>
	                        <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
	                            <td width="40"><? echo $i; ?></td>
	                            <td width="90" align="center"><? echo change_date_format($row[csf('date_calc')]); ?></td>
	                            <td width="70" align="center"><? echo $prod_resource_array[$row[csf('date_calc')]]['mp']; ?>&nbsp;</td>
	                            <td width="80" align="center"><? echo $prod_resource_array[$row[csf('date_calc')]]['wh']; ?>&nbsp;</td>
	                            <td width="80" align="right"><? echo $prod_resource_array[$row[csf('date_calc')]]['smv']; ?>&nbsp;</td>
	                            <td width="80" align="center"><? echo $increase_decrease[$prod_resource_array[$row[csf('date_calc')]]['smv_adjust_type']]; ?>&nbsp;</td>
	                            <td width="80" align="right"><? echo $prod_resource_array[$row[csf('date_calc')]]['smv_adjust']; ?>&nbsp;</td>
	                            <td align="right"><? echo number_format($day_smv,2); ?>&nbsp;</td>
	                        </tr>
	                    <?
	                    $i++;
	                    }
	                    ?>
	                    <tfoot>
	                        <th colspan="7" align="right">Total</th>
	                        <th align="right"><? echo number_format($total_smv_used,2); ?>&nbsp;</th>
	                    </tfoot>
	                </table>
	            </div>
	        </div>
		</fieldset>
	<?
	exit();
}
