<?
session_start();
include('../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$sessionUnit_id=$_SESSION['logic_erp']['company_id'];

if($action=="show_dtls_listview_bundle")
{
	$data=explode("_",$data);

	$sql_cut=sql_select("select a.job_no, b.color_id, b.roll_data, a.id, b.id as dtls_id from ppl_cut_lay_mst a, ppl_cut_lay_dtls b where a.cutting_no='".$data[0]."' and a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 ");
	$job_no				=$sql_cut[0][csf("job_no")];
	$color_id			=$sql_cut[0][csf("color_id")];
	$consumption_string	=$sql_cut[0][csf("roll_data")];
	$mst_id				=$sql_cut[0][csf("id")];
	$dtls_id			=$sql_cut[0][csf("dtls_id")];
	list($shortName,$year,$job_prifix)=explode('-',$job_no);
	//echo $consumption_string;die;
	
	$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );

	
	/*$job_sql=sql_select("select c.short_name as BUYER_NAME, a.po_number as PO_NUMBER, a.id as ID from wo_po_break_down  a,wo_po_details_master b ,lib_buyer c where b.job_no='".$job_no."' and a.job_no_mst=b.job_no   and b.buyer_name=c.id
					");
	$jbp_arr=array();
	foreach($job_sql as $jval)
	{
		$jbp_arr["buyer_name"]=$jval["BUYER_NAME"];
		$jbp_arr[$jval["ID"]]=$jval["PO_NUMBER"];
	}*/

	$table_width=1140;
	$div_width=$table_width+20;

	?>	
   
       <table cellpadding="0" width="<?php echo $div_width;?>" cellspacing="0" border="1" class="rpt_table" rules="all">
            <thead>
            	<tr>
                    <th width="20" rowspan="2">SL</th>
                    <th width="100" rowspan="2">Bundle No</th>
                    <th width="100" rowspan="2"title="Barcode No">QR Code No</th>
                    <th width="120" rowspan="2"> G. Color</th>
                    <th width="50" rowspan="2">Size</th>
                    <th width="70" rowspan="2" >Bundle Qty. (Pcs)</th>
                    <th width="80" colspan="2">GMT No</th>
                    <th width="100" rowspan="2">Knitting Floor</th>
                    <th width="90" rowspan="2">Job No</th>
                    <th width="65" rowspan="2">Buyer</th>
                    <th width="90" rowspan="2">Order No</th>
                    <th width="100" rowspan="2">Gmts. Item</th>
                    <th width="100" rowspan="2">Country</th>
                    <th rowspan="2">-</th>
                </tr>
                <tr>
                	<th width="40">From</th>
                    <th width="40">To</th>
                </tr>
            </thead>
        </table>
	<div style="width:<?php echo $div_width;?>px;max-height:250px;overflow-y:scroll" align="left"> 
           
        <table cellpadding="0" width="<?php echo $table_width;?>" cellspacing="0" border="1" class="rpt_table"  rules="all" id="tbl_details"> 
           <tbody> 
		<?php   
			$i=1;	
			$total_production_qnty=0;
			$barcode_no="'".implode("','",explode(",",$data[1]))."'";
			if($data[1]!="")
			$barcode_cond=" c.barcode_no in (".$barcode_no.") ";
			
			$sqlResult =sql_select("select b.* , a.gmt_item_id, a.color_id, c.machine_id, c.bundle_qty, c.production_qnty, c.color_size_break_down_id from ppl_cut_lay_dtls a, ppl_cut_lay_bundle b, pro_garments_production_dtls c where $barcode_cond and c.production_type=86 and b.mst_id=$mst_id and b.dtls_id=$dtls_id and c.bundle_no=b.bundle_no and a.id=b.dtls_id and a.color_id=$color_id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
			$sizeidArr=array(); $countryidArr=array();  $poIDArr=array();
			foreach($sqlResult as $row)
			{
				$sizeidArr[$row[csf('size_id')]]=$row[csf('size_id')];
				$countryidArr[$row[csf('country_id')]]=$row[csf('country_id')];
				$poIDArr[$row[csf('order_id')]]=$row[csf('order_id')];
			}
			
			$job_sql=sql_select("select c.short_name as BUYER_NAME, a.po_number as PO_NUMBER, a.id as ID, b.JOB_NO from wo_po_break_down  a,wo_po_details_master b ,lib_buyer c where a.job_no_mst=b.job_no   and b.buyer_name=c.id and a.id in (".implode(",",$poIDArr).")");
			$jbp_arr=array();
			foreach($job_sql as $jval)
			{
				$jbp_arr["buyer_name"]['']=$jval["BUYER_NAME"];
				$jbp_arr[$jval["ID"]]['po']=$jval["PO_NUMBER"];
				$jbp_arr[$jval["ID"]]['job']=$jval["JOB_NO"];
			}
			$size_library=return_library_array( "select id,size_name from lib_size where 1=1 ".where_con_using_array($sizeidArr,1,'id')."", "id", "size_name");
			
			$country_library=return_library_array( "select id,country_name from lib_country where 1=1 ".where_con_using_array($countryidArr,1,'id')."", "id", "country_name" );

			foreach($sqlResult as $selectResult)
			{
				if ($i%2==0)  $bgcolor="#E9F3FF";  else $bgcolor="#FFFFFF";
 			?>
                <tr bgcolor="<?=$bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="" > 
                    <td width="20" align="center"><? echo $i; ?></td>
                    <td width="100" align="center" style="word-break:break-all"><?=$selectResult[csf('bundle_no')]; ?></td>
                    <td width="100" align="center" style="word-break:break-all"><?=$selectResult[csf('barcode_no')]; ?></td>
                    <td width="120" align="center" style="word-break:break-all"><?=$color_library[$selectResult[csf('color_id')]]; ?></td>
                    <td width="50" align="center" style="word-break:break-all"><?=$size_library[$selectResult[csf('size_id')]]; ?></td>
                    <td width="70" align="right"><?=$selectResult[csf('production_qnty')]; ?></td>
                    <td width="40" align="right"><?=$selectResult[csf('number_start')]; ?></td>
                    <td width="40" align="right"><?=$selectResult[csf('number_end')]; ?></td>
                    <td width="100" align="right"><? //=$selectResult[csf('number_end')]; ?></td>
                    <td width="90" align="center" style="word-break:break-all"><?=$jbp_arr[$selectResult[csf('order_id')]]['job']; ?></td>
                    <td width="65" align="center" style="word-break:break-all"><?=$jbp_arr["buyer_name"]['']; ?></td>
                    <td width="90" align="center" style="word-break:break-all"><?=$jbp_arr[$selectResult[csf('order_id')]]['po']; ?></td>
                    		
                    <td width="100" align="center" style="word-break:break-all"><?=$garments_item[$selectResult[csf('gmt_item_id')]]; ?></td>
                    <td width="100" align="center" style="word-break:break-all"><?=$country_library[$selectResult[csf('country_id')]]; ?></td>
                    <td><input type="button" value="-" name="minusButton[]" id="minusButton_<?=$i;  ?>" style="width:30px" class="formbuttonplasminus" onClick="fnc_minusRow('<?=$i; ?>');"/>
                        <input type="hidden" id="txt_color_id_<?=$i; ?>" name="txt_color_id[]" style="width:80px;" value="<?=$selectResult[csf('color_id')]; ?>">
                        <input type="hidden" id="txt_size_id_<?=$i; ?>" name="txt_size_id[]" style="width:80px;" value="<?=$selectResult[csf('size_id')]; ?>">
						<input type="hidden" id="txt_order_id_<?=$i; ?>" name="txt_order_id[]" style="width:80px;" class="text_boxes" value="<?=$selectResult[csf('order_id')]; ?>">
                       	<input type="hidden" id="txt_gmt_item_id_<?=$i; ?>" name="txt_gmt_item_id[]" style="width:80px;" class="text_boxes" value="<?=$selectResult[csf('gmt_item_id')]; ?>">
                        <input type="hidden" id="txt_country_id_<?=$i; ?>" name="txt_country_id[]" style="width:80px;" class="text_boxes" value="<?=$selectResult[csf('country_id')]; ?>">
                     	<input type="hidden" id="txt_barcode_<?=$i; ?>" name="txt_barcode[]" style="width:80px;" class="text_boxes" value="<?=$selectResult[csf('barcode_no')]; ?>"> 
                        <input type="hidden" id="txt_colorsize_id_<?=$i; ?>" name="txt_colorsize_id[]" style="width:80px;" class="text_boxes" value="<?=$selectResult[csf('color_size_break_down_id')]; ?>">
                        <input type="hidden" id="txt_dtls_id_<?=$i; ?>" name="txt_dtls_id[]" style="width:80px;" class="text_boxes" value="">
                       	<input type="hidden" id="trId_<?=$i; ?>" name="trId[]" value="<?=$i; ?>">
                    	<input type="hidden" name="isRescan[]" id="isRescan_<?=$i; ?>" value="<?=0;?>"/>
                	</td>
                </tr>
            <?php
                $i++;
                $total_bundle_qty+=$selectResult[csf('production_qnty')];
			}
			?>
            </tbody>
            <tfoot>
            	<tr id="bundle_footer">
                    <th colspan="5">Total</th>
                    <th id="total_bundle_qty"><?php echo $total_bundle_qty; ?></th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                </tr>
            </tfoot>
		</table>
	</div>
	<?
	exit();
}

if($action=="populate_bundle_data")
{
	$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
	$size_library=return_library_array( "select id,size_name from lib_size", "id", "size_name"  );
	$country_library=return_library_array( "select id,country_name from lib_country", "id", "country_name" );
	$data=explode("**",$data);
	$i=$data[1]+1;
	
	//echo $i;die;
	$sql_cut=sql_select("select a.job_no, b.color_id, b.roll_data, a.id, b.id as dtls_id from ppl_cut_lay_mst a, ppl_cut_lay_dtls b
							where a.cutting_no='".$data[3]."' and a.id=b.mst_id and a.status_active=1 and a.is_deleted=0");
	$job_no				=$sql_cut[0][csf("job_no")];
	$color_id			=$sql_cut[0][csf("color_id")];
	$consumption_string	=$sql_cut[0][csf("roll_data")];
	$mst_id				=$sql_cut[0][csf("id")];
	$dtls_id			=$sql_cut[0][csf("dtls_id")];
	list($shortName,$year,$job_prifix)=explode('-',$job_no);

	
	$job_sql=sql_select("select c.short_name as BUYER_NAME, a.po_number as PO_NUMBER, a.id as ID from wo_po_break_down  a, wo_po_details_master b, lib_buyer c where b.job_no='".$job_no."' and a.job_no_mst=b.job_no and b.buyer_name=c.id");
	$jbp_arr=array();
	foreach($job_sql as $jval)
	{
		$jbp_arr["buyer_name"]=$jval["BUYER_NAME"];
		$jbp_arr[$jval["ID"]]=$jval["PO_NUMBER"];
	}
	
	$table_width	=1140;
	$div_width		=$table_width+20;
	$oldbarcode_no="'".implode("','",explode(",",$data[4]))."'";
	$barcode_no="'".implode("','",explode(",",$data[0]))."'";
	if($data[0]!="")
	$oldbarcode_cond=" and c.barcode_no not in (".$oldbarcode_no.") ";
	$barcode_cond=" and c.barcode_no in (".$barcode_no.") ";
	
	$total_production_qnty=0;
	$sqlResult =sql_select("select b.* , a.gmt_item_id, a.color_id, c.machine_id, c.bundle_qty, c.production_qnty, c.color_size_break_down_id
					from ppl_cut_lay_dtls a, ppl_cut_lay_bundle b, pro_garments_production_dtls c 
					where c.production_type=86 and a.id=b.dtls_id and b.barcode_no=c.barcode_no $barcode_cond $oldbarcode_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0");
	
	$poIdArr=array();			
	foreach($sqlResult as $row)
	{
		$poIdArr[$row["ORDER_ID"]]=$row["ORDER_ID"];
	}
	
	$job_sql=sql_select("select c.short_name as BUYER_NAME, a.po_number as PO_NUMBER, a.id as ID, b.JOB_NO from wo_po_break_down  a, wo_po_details_master b, lib_buyer c where a.id in (".implode(",",$poIdArr).") and a.job_no_mst=b.job_no and b.buyer_name=c.id");
	$jbp_arr=array();
	foreach($job_sql as $jval)
	{
		$jbp_arr["buyer_name"]=$jval["BUYER_NAME"];
		$jbp_arr[$jval["ID"]]=$jval["PO_NUMBER"];
		$job_no				=$jval[csf("JOB_NO")];
	}
	foreach($sqlResult as $selectResult)
	{
		if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
		$total_production_qnty+=$selectResult[csf('production_qnty ')]; 	
	?>
		<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="" > 
            <td width="20" align="center"><? echo $i; ?></td>
            <td width="100" align="center"><? echo $selectResult[csf('bundle_no')]; ?></td>
            <td width="100" align="center"><? echo $selectResult[csf('barcode_no')]; ?></td>
            <td width="120" align="center" style="word-break:break-all"><p><?php  echo $color_library[$selectResult[csf('color_id')]]; ?></p></td>
            <td width="50" align="center"><?php  echo $size_library[$selectResult[csf('size_id')]]; ?></td>
            <td width="70" align="right"><?php  echo $selectResult[csf('production_qnty')]; ?></td>
            <td width="40" align="right"><?=$selectResult[csf('number_start')]; ?></td>
            <td width="40" align="right"><?=$selectResult[csf('number_end')]; ?></td>
            <td width="100" align="right"><? //=$selectResult[csf('number_end')]; ?></td>
            <td width="90" align="center" style="word-break:break-all"><?=$job_no; ?></td>
            <td width="65" align="center" style="word-break:break-all"><?=$jbp_arr["buyer_name"]; ?></td>
            <td width="90" align="center" style="word-break:break-all"><?=$jbp_arr[$selectResult[csf('order_id')]]; ?></td>
                    
            <td width="100" align="center" style="word-break:break-all"><?=$garments_item[$selectResult[csf('gmt_item_id')]]; ?></td>
            <td width="100" align="center" style="word-break:break-all"><?=$country_library[$selectResult[csf('country_id')]]; ?></td>
            <td>
             	<input type="button" value="-" name="minusButton[]" id="minusButton_<?=$i; ?>" style="width:30px" class="formbuttonplasminus" onClick="fnc_minusRow('<?=$i; ?>');"/>
                <input type="hidden" id="txt_color_id_<?=$i; ?>" name="txt_color_id[]" style="width:80px;" value="<?=$selectResult[csf('color_id')]; ?>">
                <input type="hidden" id="txt_size_id_<?=$i; ?>" name="txt_size_id[]" style="width:80px;" value="<?=$selectResult[csf('size_id')]; ?>">
				<input type="hidden" id="txt_order_id_<?=$i; ?>" name="txt_order_id[]" style="width:80px;" value="<?=$selectResult[csf('order_id')]; ?>">
               	<input type="hidden" id="txt_gmt_item_id_<?=$i; ?>" name="txt_gmt_item_id[]" style="width:80px;" value="<?=$selectResult[csf('gmt_item_id')]; ?>">
                <input type="hidden" id="txt_country_id_<? echo $i; ?>" name="txt_country_id[]" style="width:80px;" value="<?php echo $selectResult[csf('country_id')]; ?>">
             	<input type="hidden" id="txt_barcode_<? echo $i; ?>" name="txt_barcode[]" style="width:80px;" value="<?php echo $selectResult[csf('barcode_no')]; ?>"> 
                <input type="hidden" id="txt_colorsize_id_<? echo $i; ?>" name="txt_colorsize_id[]" style="width:80px;" value="<?php echo $selectResult[csf('color_size_break_down_id')]; ?>">
                <input type="hidden" id="txt_dtls_id_<? echo $i; ?>" name="txt_dtls_id[]" style="width:80px;" class="text_boxes" value="">
               	<input type="hidden" id="trId_<? echo $i; ?>" name="trId[]" value="<?php echo $i; ?>">
                <input type="hidden" name="isRescan[]" id="isRescan_<?=$i; ?>" value="<?=0;?>"/>
        	</td>
        </tr>
	<?php
		$i++;
	}

	exit();
}

if($action=="challan_duplicate_check")
{
	$exdata=explode("_",$data);
	if($exdata[1]==1)
	{
		$firstinssql="select b.BARCODE_NO from pro_gmts_cutting_qc_mst a, pro_garments_production_dtls b where a.id=b.delivery_mst_id and a.CUTTING_QC_NO='$exdata[0]' and b.production_type=86 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
		$firstinssqlArr=sql_select($firstinssql);
		$exdata[0]="";
		foreach($firstinssqlArr as $row)
		{
			if($exdata[0]=="") $exdata[0]=$row['BARCODE_NO']; else $exdata[0].=','.$row['BARCODE_NO'];
		}
	}
	//echo $exdata[0];
	
	$bundle_no="'".implode("','",explode(",",$exdata[0]))."'";
	$msg=1;
	
	$bundle_count=count(explode(",",$bundle_no)); $bundle_nos_cond="";
	$bundle_nos_cond=" and f.barcode_no in ($bundle_no)";
	//echo "select a.cutting_qc_no, b.bundle_no from  pro_gmts_delivery_mst a, pro_garments_production_dtls b where a.id=b.delivery_mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $bundle_nos_cond";
	//$result=sql_select("SELECT a.cutting_qc_no, b.barcode_no from  pro_gmts_cutting_qc_mst a, pro_gmts_cutting_qc_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $bundle_nos_cond");
	
	$result=sql_select("SELECT a.cutting_qc_no, f.barcode_no
    FROM pro_gmts_cutting_qc_mst a, pro_garments_production_dtls f
    where a.garments_nature=100 and f.PRODUCTION_TYPE=87 and a.id=f.delivery_mst_id and a.status_active=1 and a.is_deleted=0 and f.status_active=1 and f.is_deleted=0 $bundle_nos_cond group by a.cutting_qc_no, f.barcode_no");

	$datastr=""; $newbarcode="";
	if(count($result)>0)
	{
		foreach ($result as $row)
		{ 
			$msg=2;
			$datastr=$row[csf('barcode_no')]."*".$row[csf('cutting_qc_no')];
			//if($newbarcode=="") $newbarcode=$row[csf('barcode_no')]; else $newbarcode.=','.$row[csf('barcode_no')];
		}
	}
	if($search_lot_no=="") $search_lot_no=0;
	echo rtrim($msg)."_".rtrim($datastr)."_".$search_lot_no."_".rtrim($exdata[0]);
	exit();
}

if($action=='populate_data_from_barcode')
{
	$exdata=explode("_",$data);
	$barcode_no="'".implode("','",explode(",",$exdata[1]))."'";
	$cutNo=$exdata[0];
	if($cutNo!="") $cutNoCond=" and c.cutting_no='$cutNo'"; else $cutNoCond="";
	
	$data_array=sql_select("SELECT a.cutting_qc_no, a.id, a.floor_id, a.location_id, a.production_type, a.serving_company, a.service_location , a.company_id, c.cutting_no, c.job_no, a.production_source, sum(b.production_qnty) as production_qty from pro_gmts_cutting_qc_mst a, pro_garments_production_dtls b, ppl_cut_lay_mst c where b.barcode_no in ($barcode_no) $cutNoCond and a.id=b.delivery_mst_id and b.cut_no=c.cutting_no and a.production_type=86 and b.production_type=86 and a.status_active=1  and b.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.is_deleted=0 group by a.cutting_qc_no, a.id, a.floor_id, a.location_id, a.production_type, a.serving_company, a.service_location, a.company_id, c.cutting_no, c.job_no, a.production_source ");
	
	foreach ($data_array as $row)
	{ 
		echo "document.getElementById('txt_lot_ratio').value 				= '".$row[csf("cutting_no")]."';\n";
		echo "document.getElementById('txt_job_no').value 					= '".$row[csf("job_no")]."';\n";
		echo "document.getElementById('cbo_company_name').value 			= '".$row[csf("company_id")]."';\n";
		echo "document.getElementById('cbo_source').value 					= '".$row[csf("production_source")]."';\n";
		echo "load_drop_down( 'requires/bundle_receive_distribution_point_controller', ".($row[csf("production_source")])."+'_'+".($row[csf("company_id")]).", 'load_drop_down_knitting_com','knitting_com');";
		
		if ($row[csf("production_source")] == 1) {
			$knitting_com= create_drop_down("cbo_working_company", 130, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0  order by comp.company_name", "id,company_name", 1, "--Select Knit Company--", $row[csf("company_id")], "load_location();", 1);
		} else if ($row[csf("production_source")] == 3) {
			$knitting_com= create_drop_down("cbo_working_company", 130, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=20 and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name", "id,supplier_name", 1, "--Select Knit Company--", 0, "load_location();");
		} else {
			$knitting_com= create_drop_down("cbo_working_company", 130, $blank_array, "", 1, "--Select Knit Company--", 0, "load_location();");
		}
		
		if ($row[csf("production_source")] == 1)
		{
			$workingloaction=create_drop_down( "cbo_working_location", 130, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='".$row[csf("working_company_id")]."' order by location_name","id,location_name", 1, "-- Select Location --", $selected, "",1 );
			$workingFloor=create_drop_down( "cbo_floor", 130, "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and location_id='".$row[csf("working_location_id")]."' and production_process in (2) order by floor_name","id,floor_name", 1, "-- Select Floor --", $selected, "",1 );
		}
		else
		{
			$workingloaction= create_drop_down("cbo_working_location", 130, $blank_array, "", 1, "-- Select Location --", 0, "");
			$workingFloor= create_drop_down("cbo_floor", 130, $blank_array, "", 1, "-- Select Floor --", 0, "");
		}
		
		$lc_location=create_drop_down( "cbo_location", 130, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='".$row[csf("company_id")]."' order by location_name","id,location_name", 1, "-- Select Location --", $selected, "",1 );
		
		echo "document.getElementById('location_td').innerHTML = '".$lc_location."';\n";
		echo "document.getElementById('knitting_com').innerHTML = '".$knitting_com."';\n";
		echo "document.getElementById('working_location_td').innerHTML = '".$workingloaction."';\n";
		echo "document.getElementById('floor_td').innerHTML = '".$workingFloor."';\n";
		echo "document.getElementById('cbo_working_company').value 			= '".$row[csf("serving_company")]."';\n";
		echo "load_location(".$row[csf("production_source")].");\n";
		echo "document.getElementById('cbo_working_location').value 		= '".$row[csf("service_location")]."';\n";
		echo "load_drop_down( 'requires/bundle_receive_distribution_point_controller', ".$row[csf("service_location")].", 'load_drop_down_floor', 'floor_td' );";
		echo "document.getElementById('cbo_floor').value  					= '".($row[csf("floor_id")])."';\n";
		echo "load_drop_down( 'requires/bundle_receive_distribution_point_controller', '".$row[csf('company_id')]."', 'load_drop_down_lc_location', 'location_td' );\n";
		echo "document.getElementById('cbo_location').value 					= '".$row[csf("location_id")]."';\n";
		exit();
	}
}

if($action=="populate_bundle_data_rescan")
{
	$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
	$size_library=return_library_array( "select id,size_name from lib_size", "id", "size_name"  );
	$country_library=return_library_array( "select id,country_name from lib_country", "id", "country_name" );
	$data=explode("**",$data);
	$i=$data[1]+1;
	
	//echo $i;die;
	$sql_cut=sql_select("select a.job_no, b.color_id, b.roll_data, a.id, b.id as dtls_id from ppl_cut_lay_mst a, ppl_cut_lay_dtls b
							where a.cutting_no='".$data[3]."' and a.id=b.mst_id and a.status_active=1 and a.is_deleted=0");
	$job_no				=$sql_cut[0][csf("job_no")];
	$color_id			=$sql_cut[0][csf("color_id")];
	$consumption_string	=$sql_cut[0][csf("roll_data")];
	$mst_id				=$sql_cut[0][csf("id")];
	$dtls_id			=$sql_cut[0][csf("dtls_id")];
	list($shortName,$year,$job_prifix)=explode('-',$job_no);

	
	$job_sql=sql_select("select c.short_name as BUYER_NAME, a.po_number as PO_NUMBER, a.id as ID from wo_po_break_down  a,wo_po_details_master b ,lib_buyer c
						 
						 where b.job_no='".$job_no."' and  a.job_no_mst=b.job_no and b.buyer_name=c.id ");
	$jbp_arr=array();
	foreach($job_sql as $jval)
	{
		$jbp_arr["buyer_name"]=$jval["BUYER_NAME"];
		$jbp_arr[$jval["ID"]]=$jval["PO_NUMBER"];
	}
	
	$table_width	=1300;
	$div_width		=$table_width+20;
	$barcode_no="'".implode("','",explode(",",$data[0]))."'";
	if($data[0]!="")
	$barcode_cond=" c.barcode_no in (".$barcode_no.") ";
	
	$total_production_qnty=0;
	$sqlResult =sql_select("select b.* ,a.gmt_item_id, a.color_id, c.machine_id, c.bundle_qty, b.size_qty, c.color_size_break_down_id
									from ppl_cut_lay_dtls a, ppl_cut_lay_bundle b, pro_garments_production_dtls c 
									where $barcode_cond and c.production_type=87 and b.mst_id=$mst_id and b.dtls_id=$dtls_id and c.bundle_no=b.bundle_no and a.id=b.dtls_id and a.color_id=$color_id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");

	foreach($sqlResult as $selectResult)
	{
		if ($i%2==0)  $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
		$total_production_qnty+=$selectResult[csf('production_qnty ')]; 	
		?>
		<tr bgcolor="<?=$bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="" > 
            <td width="20" align="center"><? echo $i; ?></td>
            <td width="100" align="center" style="word-break:break-all"><?=$selectResult[csf('bundle_no')]; ?></td>
            <td width="100" align="center" style="word-break:break-all"><?=$selectResult[csf('barcode_no')]; ?></td>
            <td width="120" align="center" style="word-break:break-all"><?=$color_library[$selectResult[csf('color_id')]]; ?></td>
            <td width="50" align="center" style="word-break:break-all"><?=$size_library[$selectResult[csf('size_id')]]; ?></td>
            <td width="70" align="right"><?=$selectResult[csf('production_qnty')]; ?></td>
            <td width="40" align="right"><?=$selectResult[csf('number_start')]; ?></td>
            <td width="40" align="right"><?=$selectResult[csf('number_end')]; ?></td>
            <td width="100" align="right"><? //=$selectResult[csf('number_end')]; ?></td>
            <td width="90" align="center" style="word-break:break-all"><?=$job_no; ?></td>
            <td width="65" align="center" style="word-break:break-all"><?=$jbp_arr["buyer_name"]; ?></td>
            <td width="90" align="center" style="word-break:break-all"><?=$jbp_arr[$selectResult[csf('order_id')]]; ?></td>
                    
            <td width="100" align="center" style="word-break:break-all"><?=$garments_item[$selectResult[csf('gmt_item_id')]]; ?></td>
            <td width="100" align="center" style="word-break:break-all"><?=$country_library[$selectResult[csf('country_id')]]; ?></td>
            <td><input type="button" value="-" name="minusButton[]" id="minusButton_<?=$i;  ?>" style="width:30px" class="formbuttonplasminus" onClick="fnc_minusRow('<?=$i; ?>');"/>
                <input type="hidden" id="txt_color_id_<?=$i; ?>" name="txt_color_id[]" style="width:80px;" value="<?=$selectResult[csf('color_id')]; ?>">
                <input type="hidden" id="txt_size_id_<?=$i; ?>" name="txt_size_id[]" style="width:80px;" value="<?=$selectResult[csf('size_id')]; ?>">
                <input type="hidden" id="txt_order_id_<?=$i; ?>" name="txt_order_id[]" style="width:80px;" class="text_boxes" value="<?=$selectResult[csf('order_id')]; ?>">
                <input type="hidden" id="txt_gmt_item_id_<?=$i; ?>" name="txt_gmt_item_id[]" style="width:80px;" class="text_boxes" value="<?=$selectResult[csf('gmt_item_id')]; ?>">
                <input type="hidden" id="txt_country_id_<?=$i; ?>" name="txt_country_id[]" style="width:80px;" class="text_boxes" value="<?=$selectResult[csf('country_id')]; ?>">
                <input type="hidden" id="txt_barcode_<?=$i; ?>" name="txt_barcode[]" style="width:80px;" class="text_boxes" value="<?=$selectResult[csf('barcode_no')]; ?>"> 
                <input type="hidden" id="txt_colorsize_id_<?=$i; ?>" name="txt_colorsize_id[]" style="width:80px;" class="text_boxes" value="<?=$selectResult[csf('color_size_break_down_id')]; ?>">
                <input type="hidden" id="txt_dtls_id_<?=$i; ?>" name="txt_dtls_id[]" style="width:80px;" class="text_boxes" value="">
                <input type="hidden" id="trId_<?=$i; ?>" name="trId[]" value="<?=$i; ?>">
                <input type="hidden" name="isRescan[]" id="isRescan_<?=$i; ?>" value="<?=1;?>"/>
            </td>
        </tr>
	<?php
		$i++;
	}

	exit();
}

if($action=="show_dtls_listview_bundle_rescan")
{

	$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
	$size_library=return_library_array( "select id,size_name from lib_size", "id", "size_name"  );
	$country_library=return_library_array( "select id,country_name from lib_country", "id", "country_name" );
	$machine_library=return_library_array( "select id,machine_no from lib_machine_name where category_id=1 and status_active=1 and is_deleted=0 and is_locked=0  order by seq_no", "id", "machine_no" );

	$data=explode("_",$data);

	$sql_cut=sql_select("SELECT a.job_no, b.color_id, b.roll_data, a.id, b.id as dtls_id from ppl_cut_lay_mst a, ppl_cut_lay_dtls b where a.cutting_no='".$data[0]."' and a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 ");
	$job_no				=$sql_cut[0][csf("job_no")];
	$color_id			=$sql_cut[0][csf("color_id")];
	$consumption_string	=$sql_cut[0][csf("roll_data")];
	$mst_id				=$sql_cut[0][csf("id")];
	$dtls_id			=$sql_cut[0][csf("dtls_id")];
	list($shortName,$year,$job_prifix)=explode('-',$job_no);
	//echo $consumption_string;die;

	
	$job_sql=sql_select("SELECT c.short_name as BUYER_NAME, a.po_number as PO_NUMBER, a.id as ID from wo_po_break_down  a,wo_po_details_master b ,lib_buyer c where b.job_no='".$job_no."' and a.job_no_mst=b.job_no   and b.buyer_name=c.id
					");
	$jbp_arr=array();
	foreach($job_sql as $jval)
	{
		$jbp_arr["buyer_name"]=$jval["BUYER_NAME"];
		$jbp_arr[$jval["ID"]]=$jval["PO_NUMBER"];
	}
	

	$table_width=1140;
	$div_width=$table_width+20;
	?>	
       <table cellpadding="0" width="<?php echo $div_width;?>" cellspacing="0" border="1" class="rpt_table" rules="all">
            <thead>
            	<tr>
                    <th width="20" rowspan="2">SL</th>
                    <th width="100" rowspan="2">Bundle No</th>
                    <th width="100" rowspan="2"title="Barcode No">QR Code No</th>
                    <th width="120" rowspan="2"> G. Color</th>
                    <th width="50" rowspan="2">Size</th>
                    <th width="70" rowspan="2" >Bundle Qty. (Pcs)</th>
                    <th width="80" colspan="2">GMT No</th>
                    <th width="100" rowspan="2">Knitting Floor</th>
                    <th width="90" rowspan="2">Job No</th>
                    <th width="65" rowspan="2">Buyer</th>
                    <th width="90" rowspan="2">Order No</th>
                    <th width="100" rowspan="2">Gmts. Item</th>
                    <th width="100" rowspan="2">Country</th>
                    <th rowspan="2">-</th>
                </tr>
                <tr>
                	<th width="40">From</th>
                    <th width="40">To</th>
                </tr>
            </thead>
        </table>		
	<div style="width:<?php echo $div_width;?>px;max-height:250px;overflow-y:scroll"  align="left"> 
           
        <table cellpadding="0" width="<?php echo $table_width;?>"  cellspacing="0"  border="1"  class="rpt_table"  rules="all"  id="tbl_details"> 
           <tbody> 
		<?php  
			$i=1;	
			$total_production_qnty=0;
			$barcode_no="'".implode("','",explode(",",$data[1]))."'";
			if($data[1]!="")
			$barcode_cond=" c.barcode_no in (".$barcode_no.") ";
			
			$sqlResult =sql_select("SELECT b.bundle_no, b.barcode_no, b.order_id, b.size_id, b.country_id, a.gmt_item_id, a.color_id, sum(c.production_qnty) as production_qnty, c.color_size_break_down_id, b.id as bundle_id from ppl_cut_lay_dtls a, ppl_cut_lay_bundle b, pro_garments_production_dtls c where $barcode_cond and c.production_type=87 and b.mst_id=$mst_id and b.dtls_id=$dtls_id and c.bundle_no=b.bundle_no and a.id=b.dtls_id and a.color_id=$color_id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.bundle_no,b.barcode_no,b.order_id,b.size_id,b.country_id , a.gmt_item_id, a.color_id, c.color_size_break_down_id,b.id");
			foreach ($sqlResult as $key => $value) 
			{
				$input_qty_arr[$val[csf('barcode_no')]]+=$val[csf('production_qnty')];
				$input_barcode_arr[]=$val[csf('barcode_no')];
				$total_input[$val[csf('barcode_no')]]+=$val[csf('production_qnty')];
			}

			// ============== getting receive bundle ======================
			$receive_sql="SELECT a.barcode_no, sum(a.production_qnty+a.alter_qty+a.spot_qty) as qty,c.id as bundle_id from ppl_cut_lay_bundle c, pro_garments_production_dtls a, wo_po_color_size_breakdown b where c.barcode_no=a.barcode_no and a.production_type=87 and a.color_size_break_down_id=b.id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and $barcode_cond and c.mst_id=$mst_id and c.dtls_id=$dtls_id and b.color_number_id=$color_id 
			group by a.barcode_no ,c.id order by a.barcode_no asc";
			// echo $receive_sql;die;
			$receive_result = sql_select($receive_sql);
			foreach ($receive_result as $row)
			{
				$output_qty_arr[$row[csf('barcode_no')]]+=$row[csf('qty')];
				$output_barcode_arr[]=$row[csf('barcode_no')];
				$total_output[$row[csf('barcode_no')]]+=$row[csf('qty')];
			}

			foreach($sqlResult as $selectResult)
			{
				if ($i%2==0)  $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                $production_qnty = $selectResult[csf('production_qnty')] - $output_qty_arr[$selectResult[csf('barcode_no')]];
 			?>
                <tr bgcolor="<?=$bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="" > 
                    <td width="20" align="center"><? echo $i; ?></td>
                    <td width="100" align="center" style="word-break:break-all"><?=$selectResult[csf('bundle_no')]; ?></td>
                    <td width="100" align="center" style="word-break:break-all"><?=$selectResult[csf('barcode_no')]; ?></td>
                    <td width="120" align="center" style="word-break:break-all"><?=$color_library[$selectResult[csf('color_id')]]; ?></td>
                    <td width="50" align="center" style="word-break:break-all"><?=$size_library[$selectResult[csf('size_id')]]; ?></td>
                    <td width="70" align="right"><?=$selectResult[csf('production_qnty')]; ?></td>
                    <td width="40" align="right"><?=$selectResult[csf('number_start')]; ?></td>
                    <td width="40" align="right"><?=$selectResult[csf('number_end')]; ?></td>
                    <td width="100" align="right"><? //=$selectResult[csf('number_end')]; ?></td>
                    <td width="90" align="center" style="word-break:break-all"><?=$job_no; ?></td>
                    <td width="65" align="center" style="word-break:break-all"><?=$jbp_arr["buyer_name"]; ?></td>
                    <td width="90" align="center" style="word-break:break-all"><?=$jbp_arr[$selectResult[csf('order_id')]]; ?></td>
                    <td width="100" align="center" style="word-break:break-all"><?=$garments_item[$selectResult[csf('gmt_item_id')]]; ?></td>
                    <td width="100" align="center" style="word-break:break-all"><?=$country_library[$selectResult[csf('country_id')]]; ?></td>
                    <td><input type="button" value="-" name="minusButton[]" id="minusButton_<?=$i;  ?>" style="width:30px" class="formbuttonplasminus" onClick="fnc_minusRow('<?=$i; ?>');"/>
                        <input type="hidden" id="txt_color_id_<?=$i; ?>" name="txt_color_id[]" style="width:80px;" value="<?=$selectResult[csf('color_id')]; ?>">
                        <input type="hidden" id="txt_size_id_<?=$i; ?>" name="txt_size_id[]" style="width:80px;" value="<?=$selectResult[csf('size_id')]; ?>">
						<input type="hidden" id="txt_order_id_<?=$i; ?>" name="txt_order_id[]" style="width:80px;" class="text_boxes" value="<?=$selectResult[csf('order_id')]; ?>">
                       	<input type="hidden" id="txt_gmt_item_id_<?=$i; ?>" name="txt_gmt_item_id[]" style="width:80px;" class="text_boxes" value="<?=$selectResult[csf('gmt_item_id')]; ?>">
                        <input type="hidden" id="txt_country_id_<?=$i; ?>" name="txt_country_id[]" style="width:80px;" class="text_boxes" value="<?=$selectResult[csf('country_id')]; ?>">
                     	<input type="hidden" id="txt_barcode_<?=$i; ?>" name="txt_barcode[]" style="width:80px;" class="text_boxes" value="<?=$selectResult[csf('barcode_no')]; ?>"> 
                        <input type="hidden" id="txt_colorsize_id_<?=$i; ?>" name="txt_colorsize_id[]" style="width:80px;" class="text_boxes" value="<?=$selectResult[csf('color_size_break_down_id')]; ?>">
                        <input type="hidden" id="txt_dtls_id_<?=$i; ?>" name="txt_dtls_id[]" style="width:80px;" class="text_boxes" value="">
                       	<input type="hidden" id="trId_<?=$i; ?>" name="trId[]" value="<?=$i; ?>">
                    	<input type="hidden" name="isRescan[]" id="isRescan_<?=$i; ?>" value="<?=1;?>"/>
                	</td>
                </tr>
            <?php
                $i++;
                $total_bundle_qty+=$production_qnty;
			}
			?>
            </tbody>
            <tfoot>
            	<tr id="bundle_footer">
                    <th colspan="5">Total</th>
                    <th id="total_bundle_qty"><?php echo $total_bundle_qty; ?></th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                </tr>
            </tfoot>
		</table>
	</div>
	<?
	exit();
}

if($action=="show_dtls_listview_bundle_rescan2")
{
	$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
	$size_library=return_library_array( "select id,size_name from lib_size", "id", "size_name"  );
	$country_library=return_library_array( "select id,country_name from lib_country", "id", "country_name" );
	$machine_library=return_library_array( "select id,machine_no from lib_machine_name where category_id=1 and status_active=1 and is_deleted=0 and is_locked=0  order by seq_no", "id", "machine_no" );

	$data=explode("**",$data);
	$sql_cut=sql_select("SELECT a.job_no, b.color_id, b.roll_data, a.id, b.id as dtls_id from ppl_cut_lay_mst a, ppl_cut_lay_dtls b,ppl_cut_lay_bundle c where c.barcode_no='".$data[0]."' and a.id=b.mst_id and c.mst_id=a.id and b.id=c.dtls_id and a.status_active=1 and a.is_deleted=0 ");
	$job_no				=$sql_cut[0][csf("job_no")];
	$color_id			=$sql_cut[0][csf("color_id")];
	$consumption_string	=$sql_cut[0][csf("roll_data")];
	$mst_id				=$sql_cut[0][csf("id")];
	$dtls_id			=$sql_cut[0][csf("dtls_id")];
	list($shortName,$year,$job_prifix)=explode('-',$job_no);
	//echo $consumption_string;die;

	
	$job_sql=sql_select("SELECT c.short_name as BUYER_NAME, a.po_number as PO_NUMBER, a.id as ID from wo_po_break_down  a,wo_po_details_master b ,lib_buyer c where b.job_no='".$job_no."' and a.job_id=b.id   and b.buyer_name=c.id
					");
	$jbp_arr=array();
	foreach($job_sql as $jval)
	{
		$jbp_arr["buyer_name"]=$jval["BUYER_NAME"];
		$jbp_arr[$jval["ID"]]=$jval["PO_NUMBER"];
	}
	

	$table_width=1380;
	$div_width=$table_width+20;
	
	$i=1;	
	$total_production_qnty=0;
	$barcode_no="'".implode("','",explode(",",$data[0]))."'";
	if($data[0]!="")
	$barcode_cond=" c.barcode_no in (".$barcode_no.") ";

	
	$sqlResult =sql_select("SELECT b.id, b.bundle_no, b.barcode_no, b.order_id, b.size_id, b.country_id, a.gmt_item_id, a.color_id, sum(c.production_qnty) as production_qnty, c.color_size_break_down_id from ppl_cut_lay_dtls a, ppl_cut_lay_bundle b, pro_garments_production_dtls c where $barcode_cond and c.production_type=87 and b.mst_id=$mst_id and b.dtls_id=$dtls_id and c.bundle_no=b.bundle_no and a.id=b.dtls_id and a.color_id=$color_id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.bundle_no,b.barcode_no,b.order_id,b.size_id,b.country_id , a.gmt_item_id, a.color_id, c.color_size_break_down_id");
	//echo "SELECT b.bundle_no,b.barcode_no,b.order_id,b.size_id,b.country_id , a.gmt_item_id, sum(c.production_qnty) as production_qnty, c.color_size_break_down_id from ppl_cut_lay_dtls a, ppl_cut_lay_bundle b, pro_garments_production_dtls c where $barcode_cond and c.production_type=86 and b.mst_id=$mst_id and b.dtls_id=$dtls_id and c.bundle_no=b.bundle_no and a.id=b.dtls_id and a.color_id=$color_id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.bundle_no,b.barcode_no,b.order_id,b.size_id,b.country_id , a.gmt_item_id, c.color_size_break_down_id";
	foreach ($sqlResult as $key => $value) 
	{
		$input_qty_arr[$val[csf('barcode_no')]]+=$val[csf('production_qnty')];
		$input_barcode_arr[]=$val[csf('barcode_no')];
		$total_input[$val[csf('barcode_no')]]+=$val[csf('production_qnty')];
	}

	// ============== getting receive bundle ======================
	$receive_sql="SELECT a.barcode_no, sum(a.production_qnty+a.alter_qty+a.spot_qty) as qty,c.id as bundle_id from ppl_cut_lay_bundle c, pro_garments_production_dtls a, wo_po_color_size_breakdown b where c.barcode_no=a.barcode_no and a.production_type=87 and a.color_size_break_down_id=b.id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and $barcode_cond and c.mst_id=$mst_id and c.dtls_id=$dtls_id and b.color_number_id=$color_id 
	group by a.barcode_no ,c.id
	order by a.barcode_no asc";
	// echo $receive_sql;die;
	$receive_result = sql_select($receive_sql);
	foreach ($receive_result as $row)
	{
		$output_qty_arr[$row[csf('barcode_no')]]+=$row[csf('qty')];
		$output_barcode_arr[]=$row[csf('barcode_no')];
		$total_output[$row[csf('barcode_no')]]+=$row[csf('qty')];
	}

	foreach($sqlResult as $selectResult)
	{
		if ($i%2==0)  $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
        $production_qnty = $selectResult[csf('production_qnty')] - $output_qty_arr[$selectResult[csf('barcode_no')]];
		?>
        <tr bgcolor="<?=$bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="" > 
            <td width="20" align="center"><? echo $i; ?></td>
            <td width="100" align="center" style="word-break:break-all"><?=$selectResult[csf('bundle_no')]; ?></td>
            <td width="100" align="center" style="word-break:break-all"><?=$selectResult[csf('barcode_no')]; ?></td>
            <td width="120" align="center" style="word-break:break-all"><?=$color_library[$selectResult[csf('color_id')]]; ?></td>
            <td width="50" align="center" style="word-break:break-all"><?=$size_library[$selectResult[csf('size_id')]]; ?></td>
            <td width="70" align="right"><?=$production_qnty; ?></td>
            <td width="40" align="right"><?=$selectResult[csf('number_start')]; ?></td>
            <td width="40" align="right"><?=$selectResult[csf('number_end')]; ?></td>
            <td width="100" align="right"><? //=$selectResult[csf('number_end')]; ?></td>
            <td width="90" align="center" style="word-break:break-all"><?=$job_no; ?></td>
            <td width="65" align="center" style="word-break:break-all"><?=$jbp_arr["buyer_name"]; ?></td>
            <td width="90" align="center" style="word-break:break-all"><?=$jbp_arr[$selectResult[csf('order_id')]]; ?></td>
                    
            <td width="100" align="center" style="word-break:break-all"><?=$garments_item[$selectResult[csf('gmt_item_id')]]; ?></td>
            <td width="100" align="center" style="word-break:break-all"><?=$country_library[$selectResult[csf('country_id')]]; ?></td>
            <td><input type="button" value="-" name="minusButton[]" id="minusButton_<?=$i;  ?>" style="width:30px" class="formbuttonplasminus" onClick="fnc_minusRow('<?=$i; ?>');"/>
                <input type="hidden" id="txt_color_id_<?=$i; ?>" name="txt_color_id[]" style="width:80px;" value="<?=$selectResult[csf('color_id')]; ?>">
                <input type="hidden" id="txt_size_id_<?=$i; ?>" name="txt_size_id[]" style="width:80px;" value="<?=$selectResult[csf('size_id')]; ?>">
                <input type="hidden" id="txt_order_id_<?=$i; ?>" name="txt_order_id[]" style="width:80px;" class="text_boxes" value="<?=$selectResult[csf('order_id')]; ?>">
                <input type="hidden" id="txt_gmt_item_id_<?=$i; ?>" name="txt_gmt_item_id[]" style="width:80px;" class="text_boxes" value="<?=$selectResult[csf('gmt_item_id')]; ?>">
                <input type="hidden" id="txt_country_id_<?=$i; ?>" name="txt_country_id[]" style="width:80px;" class="text_boxes" value="<?=$selectResult[csf('country_id')]; ?>">
                <input type="hidden" id="txt_barcode_<?=$i; ?>" name="txt_barcode[]" style="width:80px;" class="text_boxes" value="<?=$selectResult[csf('barcode_no')]; ?>"> 
                <input type="hidden" id="txt_colorsize_id_<?=$i; ?>" name="txt_colorsize_id[]" style="width:80px;" class="text_boxes" value="<?=$selectResult[csf('color_size_break_down_id')]; ?>">
                <input type="hidden" id="txt_dtls_id_<?=$i; ?>" name="txt_dtls_id[]" style="width:80px;" class="text_boxes" value="">
                <input type="hidden" id="trId_<?=$i; ?>" name="trId[]" value="<?=$i; ?>">
                <input type="hidden" name="isRescan[]" id="isRescan_<?=$i; ?>" value="<?=1;?>"/>
            </td>
        </tr>
    	<?php
        $i++;
        $total_bundle_qty+=$production_qnty;
	}
	exit();
}

if($action=="load_mst_data")
{
	$data=explode("__",$data);
	//echo $data;die;
 	$ex_data = "'".implode("','",explode(",",$data[0]))."'";	

	$bundle_count=count(explode(",",$ex_data)); $bundle_nos_cond="";
	if($db_type==2 && $bundle_count>400)
	{
		$bundle_nos_cond=" and (";
		$bundleArr=array_chunk(explode(",",$ex_data),399);
		foreach($bundleArr as $bundleNos)
		{
			$bundleNos=implode(",",$bundleNos);
			$bundle_nos_cond.=" c.barcode_no in($bundleNos) or ";
		}
		$bundle_nos_cond=chop($bundle_nos_cond,'or ');
		$bundle_nos_cond.=")";
	}
	else
	{
		$bundle_nos_cond=" and c.barcode_no in ($ex_data)";
	}

	$txt_order_no = "%".trim($ex_data[0])."%";
	$size_arr=return_library_array( "select id, size_name from lib_size",'id','size_name');
	$color_arr=return_library_array( "select id, color_name from lib_color", "id", "color_name");

	$sql_mst_data=sql_select("SELECT a.company_id, a.location_id,a.floor_id, a.serving_company,  a.service_location, a.production_source, a.cutting_qc_date
	from pro_gmts_cutting_qc_mst a, pro_garments_production_dtls c where a.id=c.delivery_mst_id $bundle_nos_cond $str_cond  and c.production_type=87 and a.status_active=1 and a.is_deleted=0  and c.status_active=1 and c.is_deleted=0 group by a.company_id, a.location_id,a.floor_id, a.serving_company,  a.service_location, a.production_source, a.cutting_qc_date");

	if(count($sql_mst_data)>0)
	{
		foreach($sql_mst_data as $val)
		{
			echo "load_drop_down( 'requires/bundle_receive_distribution_point_controller', '".$val[csf('company_id')]."', 'load_drop_down_lc_location', 'location_td' );\n";

			echo "load_drop_down( 'requires/bundle_receive_distribution_point_controller', '".$val[csf('serving_company')]."', 'load_drop_down_location', 'working_location_td' );\n";

			echo "load_drop_down( 'requires/bundle_receive_distribution_point_controller','".$val[csf('service_location')]."', 'load_drop_down_floor', 'floor_td');\n";

			if($val[csf('production_source')]==1) {$serv_comp=$company_arr[$val[csf('serving_company')]]; }
			else { $serv_comp=$supplier_arr[$val[csf('serving_company')]];}
			$location=$location_arr[$val[csf('location_id')]];
			$floor=$floor_arr[$val[csf('floor_id')]];
			echo "$('#cbo_source').val('".$val[csf('production_source')]."');\n";
			echo "$('#cbo_working_company').val('".$serv_comp."');\n";
			echo "$('#cbo_company_name').val(".$val[csf('company_id')].");\n";
			echo "$('#cbo_floor').val(".$val[csf('floor_id')].");\n";
			echo "$('#cbo_working_location').val(".$val[csf('service_location')].");\n";
			echo "$('#txt_receive_date').val('".change_date_format($val[csf('cutting_qc_date')])."');\n";
		}
	}
	else
		echo "alert('All Bundle must be under Selected Company, Working Company, Location, Floor. Please Check');\n";

	exit();
}

if ($action=="load_drop_down_floor")
{
	
 	echo create_drop_down( "cbo_floor", 130, "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and location_id='$data' and production_process in (2) order by floor_name","id,floor_name", 1, "-- Select Floor --", $selected, "",1 );
	exit();     	 
} 

if ($action == "load_drop_down_knitting_com") {
	$data = explode("_", $data);
	$company_id = $data[1];
	//$company_id
	if ($data[0] == 1) {
		echo create_drop_down("cbo_working_company", 130, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0  order by comp.company_name", "id,company_name", 1, "--Select Knit Company--", $company_id, "load_location();", 1);
	} else if ($data[0] == 3) {
		echo create_drop_down("cbo_working_company", 130, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=20 and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name", "id,supplier_name", 1, "--Select Knit Company--", 0, "load_location();");
	} else {
		echo create_drop_down("cbo_working_company", 130, $blank_array, "", 1, "--Select Knit Company--", 0, "load_location();");
	}
	exit();
}

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_working_location", 130, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' order by location_name","id,location_name", 1, "-- Select Location --", $selected, "load_drop_down('requires/bundle_receive_distribution_point_controller', this.value, 'load_drop_down_floor', 'floor_td' );",1 );
	exit();   
}

if ($action=="load_drop_down_lc_location")
{
	echo create_drop_down( "cbo_location", 130, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' order by location_name","id,location_name", 1, "-- Select Location --", $selected, "",1 );
	exit();   
}

if($action=="bundle_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	list($shortName,$ryear,$lot_prifix)=explode('-',$lot_ratio);
	if($ryear=="") 	$ryear=date("Y",time());
	else 			$ryear=("20$ryear")*1;
	//echo $company_id;die;
	?>
	<script>
		var selected_id = new Array();
		
		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		
		function js_set_value( str) 
		{

			if( $("#hidden_lot_ratio").val()!="" &&   $("#hidden_lot_ratio").val()!=$('#txt_individual_name' + str).val() ) {
				alert("Lot Ratio Mixed Not Allow.Previous Selected Lot Ratio "+$('#txt_individual_name' + str).val());
				return;
			}

			toggle( document.getElementById( 'search' + str ), '#FFFFCC' );
			
			if( jQuery.inArray( $('#txt_individual' + str).val(), selected_id ) == -1 ) {
				selected_id.push( $('#txt_individual' + str).val() );
				$('#hidden_lot_ratio').val( $('#txt_individual_name' + str).val() );	
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == $('#txt_individual' + str).val() ) break;
				}
				selected_id.splice( i, 1 );
				
				if(selected_id.length==0 && $('#hidden_lot_ratio_pre').val()=="")
					$('#hidden_lot_ratio').val('');

			}
			var id = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
			}
			id = id.substr( 0, id.length - 1 );
			
			$('#hidden_bundle_nos').val( id );
				 
		}
		
		function fnc_close()
		{	
			//return;
			parent.emailwindow.hide();
			//alert($('#hidden_bundle_nos').val())
		}
		
		function reset_hide_field()
		{
			//$('#hidden_bundle_nos').val( '' );return;
			selected_id = new Array();
		}
    </script>
	</head>
	<body>
	<div align="center" style="width:100%;" >
		<form name="searchwofrm"  id="searchwofrm">
			<fieldset style="width:810px;">
			<legend></legend>           
	            <table cellpadding="0" cellspacing="0" width="550" border="1" rules="all" class="rpt_table">
	                <thead>
	                	<th>Company</th>
	                    <th>Lot Ratio Year</th>
	                    <th>Job No</th>                  
	                    <th class="must_entry_caption">Ratio No</th>
	                    <th>Bundle No</th>
	                    <th><input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
	                        <input type="hidden" name="hidden_bundle_nos" id="hidden_bundle_nos"> 
	                    </th>
	                </thead>
	                <tr class="general">
	                	<td>
						<? 
	                        $sql_com="select id, company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name";
	                        echo create_drop_down( "cbo_company_name", 140, $sql_com, "id,company_name", 1,"-- Select --", $sessionUnit_id,"",0 );
	                    ?>
	                    </td>
	                    <td align="center"><? echo create_drop_down( "cbo_lot_year", 60, $year,'', "", '-- Select --',$ryear, "" ); ?></td>  				
	                    <td align="center"><input type="text" style="width:100px" class="text_boxes" name="txt_job_no" id="txt_job_no" /></td> 				
	                    <td><input type="text" name="txt_lot_no" id="txt_lot_no" style="width:100px" value="<?php if($lot_prifix) echo $lot_prifix*1; ?>" class="text_boxes" /></td>
	                    <td><input type="hidden" name="hidden_lot_ratio"  value="<?php echo $lot_ratio; ?>" id="hidden_lot_ratio"  />

				            <input type="hidden"  name="hidden_lot_ratio_pre" value="<?php echo $lot_ratio; ?>" id="hidden_lot_ratio_pre"  />
				            <input type="text" name="bundle_no" id="bundle_no" style="width:100px" class="text_boxes" />
	                    </td>  		
	            		<td align="center">
	                     	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view (document.getElementById('cbo_company_name').value+'_'+document.getElementById('bundle_no').value+'_'+'<? echo trim($bundleNo,','); ?>'+'_'+document.getElementById('txt_job_no').value+'_'+document.getElementById('txt_lot_no').value+'_'+document.getElementById('cbo_lot_year').value+'_'+'<? echo trim($lot_ratio,','); ?>','create_bundle_search_list_view','search_div','bundle_receive_distribution_point_controller','setFilterGrid(\'tbl_list_search\',-1);reset_hide_field();')" style="width:100px;" />
	                     </td>
	                </tr>
	           </table>
	           <div style="width:100%; margin-top:5px; margin-left:10px" id="search_div" align="left"></div>
			</fieldset>
		</form>
	</div>
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	<script>
		if($("#hidden_lot_ratio").val()!="")
		{
			show_list_view (document.getElementById('cbo_company_name').value+'_'+document.getElementById('bundle_no').value+'_'+'<? echo trim($bundleNo,','); ?>'+'_'+document.getElementById('txt_job_no').value+'_'+document.getElementById('txt_lot_no').value+'_'+document.getElementById('cbo_lot_year').value+'_'+'<? echo trim($lot_ratio,','); ?>','create_bundle_search_list_view','search_div','bundle_receive_distribution_point_controller','setFilterGrid(\'tbl_list_search\',-1);reset_hide_field();');
		}
	</script>
	</html>
	<?
	exit();
}

if($action=="create_bundle_search_list_view")
{
 	$ex_data 				= explode("_",$data);
	$company 				= $ex_data[0];
	$selectedBuldle			="'".implode("','",explode(",",$ex_data[2]))."'";
	$job_no					=$ex_data[3];
	$lot_no					=$ex_data[4];
	$syear 					= substr($ex_data[5],2);
	$full_lot_no			=$ex_data[7];
	
	if(trim($ex_data[1]))	$bundle_no_cond = " and a.bundle_no='".trim($ex_data[1])."'";

	if( trim($ex_data[0])=='' || trim($ex_data[0])==0)
	{
		echo "<h2 style='color:#D00; text-align:center;'>Please Select  Company First. </h2>";
		exit();
	}

	if( trim($ex_data[1])=='' && trim($ex_data[3])==''  && trim($ex_data[4])=='')
	{
		echo "<h2 style='color:#D00; text-align:center;'>Please Select Job No Or  Lot No Or Bundle No. </h2>";
		exit();
	} 
	
	$cutCon=''; $receiveCon='';
	if ($lot_no != '') 
	{
		$cutCon = " and a.cut_no like'%".$lot_no."%'";
    }
    if ($full_lot_no != '') 
	{
		$cutCon='';
		$cutCon = " and a.cut_no='".$full_lot_no."'";
    }
	
	if($job_no!='') 
		$jobCon=" and b.job_no_mst like '%$job_no%'";
	else 
		$jobCon="";
	if(str_replace("'","",$selectedBuldle)!=="")
		$selected_bundle_cond=" and a.bundle_no not in (".$selectedBuldle.")";

	$scanned_bundle_arr=return_library_array("SELECT a.bundle_no, a.bundle_no from pro_garments_production_mst b, pro_garments_production_dtls a
													
												where  b.id=a.mst_id and  a.production_type=87 and b.production_type=87 and b.status_active=1 and b.is_deleted=0 $bundle_no_cond $cutCon ", 'bundle_no', 'bundle_no');
	foreach(explode(",",$selectedBuldle) as $bn)
	{
		$scanned_bundle_arr[$bn]=$bn;	
	}
	
	if($db_type==2) $group_field="LISTAGG(CAST(a.body_part_ids AS VARCHAR2(100)),',') WITHIN GROUP ( ORDER BY a.id) as body_part_ids"; 
	else if($db_type==0) $group_field="group_concat(distinct a.body_part_ids ) as body_part_ids";

	
	?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1000" class="rpt_table">
        
        <thead>
            <th width="40">SL</th>
            <th width="50">Year</th>
            <th width="50">Job No</th>
            <th width="90">Order No</th>
            <th width="150">Gmts Item</th>
            <th width="110">Country</th>
            <th width="150">Color</th>
            <th width="50">Size</th>
            <th width="90">Lot Ratio No</th>
            <th width="90">Bundle No</th>
            <th>Bundle Qty.</th>
        </thead>
	</table>
	<div style=" width:1020px; max-height:210px; overflow-y:scroll" id="list_container_batch" align="left">
        	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1000" class="rpt_table" id="tbl_list_search">  
        	<?
			$i=1;
			$sql="select b.job_no_mst ,a.cut_no,b.color_number_id, b.item_number_id ,b.size_number_id , a.bundle_no, b.po_break_down_id  , b.country_id ,   c.size_qty ,a.barcode_no,c.mst_id,$group_field
                 from ppl_cut_lay_bundle c, pro_garments_production_dtls a, wo_po_color_size_breakdown b
                 where  
             		c.barcode_no=a.barcode_no and  a.production_type=86 and a.color_size_break_down_id=b.id  $selected_bundle_cond  $jobCon $cutCon $bundle_no_cond and b.status_active=1 and  b.is_deleted=0 and  a.status_active=1 and  a.is_deleted=0 
                group by  b.job_no_mst ,a.cut_no, b.color_number_id , b.item_number_id  , b.size_number_id  , a.bundle_no, b.po_break_down_id  , b.country_id , c.size_qty , a.barcode_no , c.mst_id  
                order by  b.job_no_mst, a.cut_no, length(a.bundle_no) asc, a.bundle_no asc";
			// echo $sql;
			$result = sql_select($sql);	
 
			foreach ($result as $val)
			{
				$po_id_arr[$val[csf('po_break_down_id')]] 		=$val[csf('po_break_down_id')];
				$color_id_arr[$val[csf('color_number_id')]] 	=$val[csf('color_number_id')];
				$size_id_arr[$val[csf('size_number_id')]] 		=$val[csf('size_number_id')];
				$country_id_arr[$val[csf('country_id')]] 		=$val[csf('country_id')];
				$cutting_id_arr[$val[csf('mst_id')]] 			=$val[csf('mst_id')];
			}

			$size_arr=return_library_array( "select id, size_name from lib_size where id in (".implode(',', $size_id_arr).")",'id','size_name');
			$color_arr=return_library_array( "select id, color_name from lib_color where id in (".implode(',', $color_id_arr).")", "id", "color_name");
			$country_arr=return_library_array( "select id, country_name from lib_country where id in (".implode(',', $country_id_arr).")",'id','country_name');
			$po_number_arr=return_library_array( "select id, po_number from wo_po_break_down where id in (".implode(',', $po_id_arr).")",'id','po_number');
			$cutting_bodypart_arr=return_library_array( "select id, body_part_string from ppl_cut_lay_mst where id in (".implode(',', $cutting_id_arr).")",'id','body_part_string');

			foreach ($result as $row)
			{  
				
				if($scanned_bundle_arr[$row[csf('bundle_no')]]=="")
				{
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						
					list($shortName,$year,$job)=explode('-',$row[csf('job_no_mst')]);
					if(empty($wet_sheet_bodypart[$row[csf('mst_id')]]))
					{
						$cutting_bodypart_string=$cutting_bodypart_arr[$row[csf('mst_id')]];
						foreach(explode(',', $cutting_bodypart_string) as $bodypart_id)
						{
							if($bodypart_id!=14) $wet_sheet_bodypart[$row[csf('mst_id')]][$bodypart_id]=$time_weight_panel[$bodypart_id];				
						}
					}

					$receive_bodypart_string=$row[csf('body_part_ids')];
					$receive_bodypart_arr=array();
					foreach(explode(',', $receive_bodypart_string) as $rbodypart_id)
					{
						if($rbodypart_id!=14) $receive_bodypart_arr[$rbodypart_id]=$time_weight_panel[$rbodypart_id];				
					}
				
					$non_receive_bodypart = array_diff($wet_sheet_bodypart[$row[csf('mst_id')]], $receive_bodypart_arr);
			
					if(empty($non_receive_bodypart))
					{
					?>
						<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i; ?>)"> 
							<td width="40"><? echo $i; ?>
								 <input type="hidden" name="txt_individual" id="txt_individual<?php echo $i; ?>" value="<?php echo $row[csf('barcode_no')]; ?>"/>
	                            <input type="hidden" name="txt_individual_name" id="txt_individual_name<?php echo $i; ?>" value="<?php echo $row[csf('cut_no')]; ?>"/>
							</td>
							<td width="50" align="center"><p><? echo $year; ?></p></td>
							<td width="50" align="center"><p><? echo $job*1; ?></p></td>
							<td width="90"><p><? echo $po_number_arr[$row[csf('po_break_down_id')]]; ?></p></td>
							<td width="150"><p><? echo $garments_item[$row[csf('item_number_id')]]; ?></p></td>
							<td width="110"><p><? echo $country_arr[$row[csf('country_id')]]; ?></p></td>
							<td width="150"><p><? echo $color_arr[$row[csf('color_number_id')]]; ?></p></td>
							<td width="50"><p><? echo $size_arr[$row[csf('size_number_id')]]; ?></p></td>
							<td width="90"><? echo $row[csf('cut_no')]; ?></td>
							<td width="90"><? echo $row[csf('bundle_no')]; ?></td>
							<td align="right"><? echo $row[csf('size_qty')]; ?></td>
						</tr>
					<?
						$i++;
					}
				}
			}
        	?>
        </table>
    </div>
    <table width="1000">
        <tr>
            <td align="center" >
               <span style="float:left;"> 
                    <input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()" />
                    Check / Uncheck All
               </span>
                <input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" />
            </td>
        </tr>
    </table>
<?	
	exit();	
}

// ==================== for rescan =============================

if($action=="bundle_popup_rescan")
{
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	list($shortName,$ryear,$lot_prifix)=explode('-',$lot_ratio);
	if($ryear=="") 	$ryear=date("Y",time());
	else 			$ryear=("20$ryear")*1;
	//echo $company_id;die;
	?>
	<script>
		var selected_id = new Array();
		
		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		
		function js_set_value( str) 
		{

			if( $("#hidden_lot_ratio").val()!="" &&   $("#hidden_lot_ratio").val()!=$('#txt_individual_name' + str).val() ) {
				alert("Lot Ratio Mixed Not Allow.Previous Selected Lot Ratio "+$('#txt_individual_name' + str).val());
				return;
				
			}

			toggle( document.getElementById( 'search' + str ), '#FFFFCC' );
			
			if( jQuery.inArray( $('#txt_individual' + str).val(), selected_id ) == -1 ) {
				selected_id.push( $('#txt_individual' + str).val() );
				$('#hidden_lot_ratio').val( $('#txt_individual_name' + str).val() );	
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == $('#txt_individual' + str).val() ) break;
				}
				selected_id.splice( i, 1 );
				
				if(selected_id.length==0 && $('#hidden_lot_ratio_pre').val()=="")
					$('#hidden_lot_ratio').val('');

			}
			var id = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
			}
			id = id.substr( 0, id.length - 1 );
			
			$('#hidden_bundle_nos').val( id );
				 
		}
		
		function fnc_close()
		{	
			parent.emailwindow.hide();
			//alert($('#hidden_bundle_nos').val())
		}
		
		function reset_hide_field()
		{
			//$('#hidden_bundle_nos').val( '' );return;
			selected_id = new Array();
		}
    </script>
	</head>
	<body>
	<div align="center" style="width:100%;" >
		<form name="searchwofrm"  id="searchwofrm">
			<fieldset style="width:810px;">
			<legend></legend>           
	            <table cellpadding="0" cellspacing="0" width="550" border="1" rules="all" class="rpt_table">
	                <thead>
	                	<th>Company</th>
	                    <th>Lot Ratio Year</th>
	                    <th>Job No</th>                  
	                    <th class="must_entry_caption">Ratio No</th>
	                    <th>Bundle No</th>
	                    <th>
	                    	<input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
	                        <input type="hidden" name="hidden_bundle_nos" id="hidden_bundle_nos"> 
	                    </th>
	                </thead>
	                <tr class="general">
	                	<td>
						<? 
	                        $sql_com="select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name";
	                        echo create_drop_down( "cbo_company_name",140, $sql_com,"id,company_name", 1,"-- Select --", $sessionUnit_id,"",0 );
	                    ?>
	                    </td>
	                    <td align="center"><? echo create_drop_down( "cbo_lot_year", 60, $year,'', "", '-- Select --',$ryear,"" ); ?></td>  				
	                    <td align="center"><input type="text"  style="width:100px" class="text_boxes" name="txt_job_no" id="txt_job_no" /></td> 				
	                    <td>
	                        <input type="text" name="txt_lot_no" id="txt_lot_no" style="width:100px"  value="<?php if($lot_prifix) echo $lot_prifix*1; ?>" class="text_boxes" />
	                    </td>  		
	                    <td><input type="hidden" name="hidden_lot_ratio" value="<?php echo $lot_ratio; ?>" id="hidden_lot_ratio"  />

				            <input type="hidden" name="hidden_lot_ratio_pre"  value="<?php echo $lot_ratio; ?>" id="hidden_lot_ratio_pre"  />
				            <input type="text" name="bundle_no" id="bundle_no" style="width:100px" class="text_boxes" />
	                    </td>  		
	            		<td align="center">
	                     	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view (document.getElementById('cbo_company_name').value+'_'+document.getElementById('bundle_no').value+'_'+'<? echo trim($bundleNo,','); ?>'+'_'+document.getElementById('txt_job_no').value+'_'+document.getElementById('txt_lot_no').value+'_'+document.getElementById('cbo_lot_year').value+'_'+'<? echo trim($lot_ratio,','); ?>','create_bundle_rescan_search_list_view','search_div','bundle_receive_distribution_point_controller','setFilterGrid(\'tbl_list_search\',-1);reset_hide_field();')" style="width:100px;" />
	                     </td>
	                </tr>
	           </table>
	           <div style="width:100%; margin-top:5px; margin-left:10px" id="search_div" align="left"></div>
			</fieldset>
		</form>
	</div>
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	<script>
		if($("#hidden_lot_ratio").val()!="")
		{
			show_list_view (document.getElementById('cbo_company_name').value+'_'+document.getElementById('bundle_no').value+'_'+'<? echo trim($bundleNo,','); ?>'+'_'+document.getElementById('txt_job_no').value+'_'+document.getElementById('txt_lot_no').value+'_'+document.getElementById('cbo_lot_year').value+'_'+'<? echo trim($lot_ratio,','); ?>','create_bundle_search_list_view','search_div','bundle_receive_distribution_point_controller','setFilterGrid(\'tbl_list_search\',-1);reset_hide_field();')
		}
	</script>
	</html>
	<?
	exit();
}

if($action=="create_bundle_rescan_search_list_view")
{
 	$ex_data 				= explode("_",$data);
	$company 				= $ex_data[0];
	$selectedBuldle			="'".implode("','",explode(",",$ex_data[2]))."'";
	$job_no					=$ex_data[3];
	$lot_no					=$ex_data[4];
	$syear 					= substr($ex_data[5],2);
	$full_lot_no			=$ex_data[7];
	
	if(trim($ex_data[1]))	$bundle_no_cond = " and a.bundle_no='".trim($ex_data[1])."'";

	if( trim($ex_data[0])=='' || trim($ex_data[0])==0)
	{
		echo "<h2 style='color:#D00; text-align:center;'>Please Select  Company First. </h2>";
		exit();
	}

	if( trim($ex_data[1])=='' && trim($ex_data[3])==''  && trim($ex_data[4])=='')
	{
		echo "<h2 style='color:#D00; text-align:center;'>Please Select Job No Or  Lot No Or Bundle No. </h2>";
		exit();
	} 	
	
	$cutCon=''; $receiveCon='';
	if ($lot_no != '') 
	{
		$cutCon = " and a.cut_no like'%".$lot_no."%'";
    }
    if ($full_lot_no != '') 
	{
		$cutCon='';
		$cutCon = " and a.cut_no='".$full_lot_no."'";
    }

 
	
	if($job_no!='') 
		$jobCon=" and b.job_no_mst like '%$job_no%'";
	else 
		$jobCon="";
	if(str_replace("'","",$selectedBuldle)!=="")
		$selected_bundle_cond=" and a.bundle_no not in (".$selectedBuldle.")";

	// $scanned_bundle_arr=return_library_array("SELECT a.bundle_no, a.bundle_no from pro_garments_production_mst b, pro_garments_production_dtls a where b.id=a.mst_id and a.production_type=86 and b.production_type=86 and b.status_active=1 and b.is_deleted=0 $bundle_no_cond $cutCon ", 'bundle_no', 'bundle_no'); 
	foreach(explode(",",$selectedBuldle) as $bn)
	{
		$scanned_bundle_arr[$bn]=$bn;	
	}
	
	if($db_type==2) $group_field="LISTAGG(CAST(a.body_part_ids AS VARCHAR2(100)),',') WITHIN GROUP ( ORDER BY a.id) as body_part_ids"; 
	else if($db_type==0) $group_field="group_concat(distinct a.body_part_ids ) as body_part_ids";

	$sql="SELECT b.job_no_mst , a.cut_no, b.color_number_id , b.item_number_id  , b.size_number_id  , a.bundle_no, b.po_break_down_id  , b.country_id , c.size_qty , a.barcode_no, c.mst_id, $group_field from ppl_cut_lay_bundle c, pro_garments_production_dtls a, wo_po_color_size_breakdown b where c.barcode_no=a.barcode_no and a.production_type=87 and a.color_size_break_down_id=b.id $selected_bundle_cond  $jobCon $cutCon $bundle_no_cond and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 group by b.job_no_mst , a.cut_no, b.color_number_id , b.item_number_id  , b.size_number_id  , a.bundle_no, b.po_break_down_id  , b.country_id , c.size_qty , a.barcode_no , c.mst_id order by b.job_no_mst, a.cut_no, length(a.bundle_no) asc, a.bundle_no asc"; 
	// echo $sql;
	$result = sql_select($sql);	

	foreach ($result as $val)
	{
		$po_id_arr[$val[csf('po_break_down_id')]] 		=$val[csf('po_break_down_id')];
		$color_id_arr[$val[csf('color_number_id')]] 	=$val[csf('color_number_id')];
		$size_id_arr[$val[csf('size_number_id')]] 		=$val[csf('size_number_id')];
		$country_id_arr[$val[csf('country_id')]] 		=$val[csf('country_id')];
		$cutting_id_arr[$val[csf('mst_id')]] 			=$val[csf('mst_id')];
	}
	// =====================================================
	$sql_in="SELECT sum(a.production_qnty) as input_qty , a.barcode_no from pro_garments_production_dtls a, wo_po_color_size_breakdown b where a.production_type=87 and a.color_size_break_down_id=b.id $selected_bundle_cond  $jobCon $cutCon $bundle_no_cond and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 group by a.barcode_no"; 
	// echo $sql;
	$in_result = sql_select($sql_in);	

	foreach ($in_result as $val)
	{
		$input_qty_arr[$val[csf('barcode_no')]]+=$val[csf('input_qty')];
		$input_barcode_arr[]=$val[csf('barcode_no')];
		$total_input[$val[csf('barcode_no')]]+=$val[csf('input_qty')];
	}

	$size_arr=return_library_array( "select id, size_name from lib_size where id in (".implode(',', $size_id_arr).")",'id','size_name');
	$color_arr=return_library_array( "select id, color_name from lib_color where id in (".implode(',', $color_id_arr).")", "id", "color_name");
	$country_arr=return_library_array( "select id, country_name from lib_country where id in (".implode(',', $country_id_arr).")",'id','country_name');
	$po_number_arr=return_library_array( "select id, po_number from wo_po_break_down where id in (".implode(',', $po_id_arr).")",'id','po_number');
	$cutting_bodypart_arr=return_library_array( "select id, body_part_string from ppl_cut_lay_mst where id in (".implode(',', $cutting_id_arr).")",'id','body_part_string');

	// ============== getting receive bundle ======================
	$receive_sql="SELECT a.barcode_no, sum(a.production_qnty+a.alter_qty+a.spot_qty) as qty from ppl_cut_lay_bundle c, pro_garments_production_dtls a, wo_po_color_size_breakdown b where c.barcode_no=a.barcode_no and a.production_type=87 and a.color_size_break_down_id=b.id $selected_bundle_cond  $jobCon $cutCon $bundle_no_cond and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 group by b.job_no_mst , a.cut_no, b.color_number_id , b.item_number_id  , b.size_number_id  , a.bundle_no, b.po_break_down_id  , b.country_id , c.size_qty , a.barcode_no , c.mst_id order by b.job_no_mst, a.cut_no, length(a.bundle_no) asc, a.bundle_no asc";
	// echo $receive_sql;die;
	$receive_result = sql_select($receive_sql);
	foreach ($receive_result as $row){
		$output_qty_arr[$row[csf('barcode_no')]]+=$row[csf('qty')];
		$output_barcode_arr[]=$row[csf('barcode_no')];
		$total_output[$row[csf('barcode_no')]]+=$row[csf('qty')];
	}
	
	?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="840" class="rpt_table">
        <thead>
            <th width="20">SL</th>
            <th width="50">Year</th>
            <th width="50">Job No</th>
            <th width="90">Order No</th>
            <th width="100">Gmts Item</th>
            <th width="100">Country</th>
            <th width="100">Color</th>
            <th width="50">Size</th>
            <th width="90">Lot Ratio No</th>
            <th width="80">Bundle No</th>
            <th>Bundle Qty.</th>
        </thead>
	</table>
	<div style=" width:860px; max-height:210px; overflow-y:scroll" id="list_container_batch" align="left">
        	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="840" class="rpt_table" id="tbl_list_search">  
        	<?
			$i=1;		
			// echo "<pre>";print_r($output_barcode_arr);echo "<pre>";
			foreach ($result as $row)
			{  
				$rescan_qty=$input_qty_arr[$row[csf('barcode_no')]]-$output_qty_arr[$row[csf('barcode_no')]];
				$rescan_qty_total=$total_input[$row[csf('barcode_no')]]-$total_output[$row[csf('barcode_no')]];
				// echo $input_qty_arr[$row[csf('barcode_no')]]."**".$output_qty_arr[$row[csf('barcode_no')]]."<br>";
				if($scanned_bundle_arr[$row[csf('bundle_no')]]=="" && $rescan_qty>0 && $rescan_qty_total>0 && in_array($row[csf('barcode_no')],$output_barcode_arr) )
				{
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						
					list($shortName,$year,$job)=explode('-',$row[csf('job_no_mst')]);
					if(empty($wet_sheet_bodypart[$row[csf('mst_id')]]))
					{
						$cutting_bodypart_string=$cutting_bodypart_arr[$row[csf('mst_id')]];
						foreach(explode(',', $cutting_bodypart_string) as $bodypart_id)
						{
							if($bodypart_id!=14) $wet_sheet_bodypart[$row[csf('mst_id')]][$bodypart_id]=$time_weight_panel[$bodypart_id];				
						}
					}

					$receive_bodypart_string=$row[csf('body_part_ids')];
					$receive_bodypart_arr=array();
					foreach(explode(',', $receive_bodypart_string) as $rbodypart_id)
					{
						if($rbodypart_id!=14) $receive_bodypart_arr[$rbodypart_id]=$time_weight_panel[$rbodypart_id];				
					}
				
					$non_receive_bodypart = array_diff($wet_sheet_bodypart[$row[csf('mst_id')]], $receive_bodypart_arr);
					
					// if(empty($non_receive_bodypart))
					// {
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i; ?>)"> 
							<td width="20"><? echo $i; ?>
								<input type="hidden" name="txt_individual" id="txt_individual<?php echo $i; ?>" value="<?php echo $row[csf('barcode_no')]; ?>"/>
	                            <input type="hidden" name="txt_individual_name" id="txt_individual_name<?php echo $i; ?>" value="<?php echo $row[csf('cut_no')]; ?>"/>
							</td>
							<td width="50" align="center"><p><? echo $year; ?></p></td>
							<td width="50" align="center"><p><? echo $job*1; ?></p></td>
							<td width="90"><p><? echo $po_number_arr[$row[csf('po_break_down_id')]]; ?></p></td>
							<td width="100"><p><? echo $garments_item[$row[csf('item_number_id')]]; ?></p></td>
							<td width="100"><p><? echo $country_arr[$row[csf('country_id')]]; ?></p></td>
							<td width="100"><p><? echo $color_arr[$row[csf('color_number_id')]]; ?></p></td>
							<td width="50"><p><? echo $size_arr[$row[csf('size_number_id')]]; ?></p></td>
							<td width="90"><? echo $row[csf('cut_no')]; ?></td>
							<td width="80"><? echo $row[csf('bundle_no')]; ?></td>
							<td align="right"><? echo $rescan_qty; ?></td>
						</tr>
						<?
						$i++;
					// }
				}
			}
        	?>
           
        </table>
    </div>
    <table width="840">
        <tr>
            <td align="center" >
               <span style="float:left;"><input type="checkbox" name="check_all" id="check_all" onClick="check_all_data();" />
                    Check / Uncheck All
               </span>
                <input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" />
            </td>
        </tr>
    </table>
	<?	
	exit();	
}

if ($action == "check_if_barcode_receive")
{
	$barcodeData = '';
	$sql="select barcode_no from pro_garments_production_dtls where barcode_no='$data' and production_type=88 and status_active=1 and is_deleted=0";
	//echo $sql;die;
	
	$data_array = sql_select($sql);
	//print_r($data_array);die;
	foreach ($data_array as $row)
	{
		$barcodeData = $row[csf("barcode_no")];
		echo $row[csf("barcode_no")];
		exit();
	}
	echo $barcodeData;
	exit();
}

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	if ($operation==0) // Insert Here----------------------------------------------------------
	{
		$con 			= connect();
		for($j=1;$j<=$tot_row;$j++)
        {   
            $barcodeCheck="barcodeNo_".$j;       
            $barcodeCheckArr[$$barcodeCheck]=$$barcodeCheck;       
        }
            
        $barcode 		="'".implode("','",$barcodeCheckArr)."'";

        $receive_sql="	SELECT c.barcode_no, c.bundle_no from pro_garments_production_mst a, pro_garments_production_dtls c where a.id=c.mst_id and a.production_type=87 and c.barcode_no in ($barcode)  and c.production_type=87 and c.status_active=1 and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0 "; 
        //and (c.is_rescan=0 or c.is_rescan is null)

        $receive_result = sql_select($receive_sql);
        foreach ($receive_result as $row)
        {           
            $duplicate_bundle[$row[csf('barcode_no')]]=$row[csf('barcode_no')];
        }

 		$field_array_qc_mst="id, garments_nature, cut_qc_prefix, cut_qc_prefix_no, cutting_qc_no, cutting_no, job_no, location_id, floor_id, company_id, cutting_qc_date, production_source, serving_company, service_location, production_type, remarks, inserted_by, insert_date, status_active, is_deleted";

	    $field_array_qc_dtls="id, mst_id, order_id, country_id, color_id, size_id, color_size_id, bundle_no, barcode_no, bundle_qty, is_rescan, inserted_by, insert_date, status_active, is_deleted";

		$field_array_mst="	id, delivery_mst_id, cut_no, company_id, garments_nature, challan_no, po_break_down_id, item_number_id, country_id, production_source, serving_company, location, production_date, production_quantity, production_type, entry_break_down_type, remarks, floor_id, inserted_by, insert_date, status_active, is_deleted";

		$new_system_id = explode("*", return_next_id_by_sequence("", "pro_gmts_cutting_qc_mst",$con,1,$cbo_company_name,'RDP',0,date("Y",time()),0,0,87,0,0 ));
		$qc_id = return_next_id_by_sequence(  "pro_gmts_cutting_qc_mst_seq",  "pro_gmts_cutting_qc_mst", $con );
		$qc_dtls_id = return_next_id_by_sequence(  "pro_gmts_cutting_qc_dtls_seq",   "pro_gmts_cutting_qc_dtls", $con );
		
		$data_arra_cutt_mst="(".$qc_id.", 100, '".$new_system_id[1]."', ".(int)$new_system_id[2].", '".$new_system_id[0]."',".$txt_lot_ratio.",".$txt_job_no.",".$cbo_location.",".$cbo_floor.",".$cbo_company_name.",".$txt_receive_date.",".$cbo_source.",".$cbo_working_company.",".$cbo_working_location.",87,".$txt_remarks.",".$user_id.",'".$pc_date_time."',1,0)";
		$challan_no=(int)$new_system_id[2];						
		$mstArr=array(); $dtlsArr=array(); $colorSizeArr=array(); $mstIdArr=array(); 
		
		for($j=1;$j<=$tot_row;$j++)
		{ 	
			$bundleNo 		="bundleNo_".$j;
			$barcodeNo 		="barcodeNo_".$j;			
			$orderId 		="orderId_".$j;
			$gmtsitemId 	="gmtsitemId_".$j;
			$countryId 		="countryId_".$j;
			$colorId 		="colorId_".$j;
			$sizeId 		="sizeId_".$j;
			$colorSizeId 	="colorSizeId_".$j;
			$checkRescan 	="isRescan_".$j;
			$qty 			="qty_".$j;
			$isRescan 		="isRescan_".$j;
			if($$isRescan==1 || $$isRescan==2)
			{				
				$mstArr[$$orderId][$$gmtsitemId][$$countryId]+=$$qty;
				$colorSizeArr[$$bundleNo] 					=$$orderId."**".$$gmtsitemId."**".$$countryId;
				$dtlsArr[$$bundleNo]['qc_pass'] 			+=$$qty;
				
				$dtlsArrColorSize[$$bundleNo] 				=$$colorSizeId;
				$bundleRescanArr[$$bundleNo]				=$$isRescan;
				$bundleBarcodeArr[$$bundleNo] 				=$$barcodeNo;

				if($data_array_qc_detls!='') $data_array_qc_detls.=",";
					 
				$data_array_qc_detls.="(".$qc_dtls_id.",".$qc_id.",".$$orderId.",".$$countryId.",".$$colorId.",".$$sizeId.",".$$colorSizeId.",'".$$bundleNo."','".$$barcodeNo."','".trim($$qty)."','".$$isRescan."',".$user_id.",'".$pc_date_time."',1,0)";

				$qc_dtls_id = return_next_id_by_sequence(  "pro_gmts_cutting_qc_dtls_seq","pro_gmts_cutting_qc_dtls", $con );
			}
			else
			{
				if($duplicate_bundle[$$barcodeNo]=='')
	            {
					$mstArr[$$orderId][$$gmtsitemId][$$countryId]+=$$qty;
					$colorSizeArr[$$bundleNo] 					=$$orderId."**".$$gmtsitemId."**".$$countryId;
					$dtlsArr[$$bundleNo]['qc_pass'] 			+=$$qty;
					
					$dtlsArrColorSize[$$bundleNo] 				=$$colorSizeId;
					$bundleRescanArr[$$bundleNo]				=$$isRescan;
					$bundleBarcodeArr[$$bundleNo] 				=$$barcodeNo;

					if($data_array_qc_detls!='') $data_array_qc_detls.=",";
						 
					$data_array_qc_detls.="(".$qc_dtls_id.", ".$qc_id.",".$$orderId.",".$$countryId.",".$$colorId.",".$$sizeId.",".$$colorSizeId.",'".$$bundleNo."','".$$barcodeNo."','".trim($$qty)."','".$$isRescan."',".$user_id.",'".$pc_date_time."',1,0)";

					$qc_dtls_id = return_next_id_by_sequence(  "pro_gmts_cutting_qc_dtls_seq","pro_gmts_cutting_qc_dtls", $con );
				}
			}
			//  echo "10**insert into ppl_cut_lay_bundle_reject($field_array_breakdown_reject)values".$data_array_breakdown_reject;die;
		}
	
		// echo "10**$data_array_qc_detls";die();
		foreach($mstArr as $orderId=>$orderData)
		{
			foreach($orderData as $gmtsItemId=>$gmtsItemIdData)
			{
				foreach($gmtsItemIdData as $countryId=>$qty)
				{
					$id= return_next_id_by_sequence(  "pro_gar_production_mst_seq", "pro_garments_production_mst", $con );
					if($data_array_mst!="") $data_array_mst.=",";
					$data_array_mst.="(".$id.",".$qc_id.",".$txt_lot_ratio.",".$cbo_company_name.",100,'".$txt_rec_challan_no."',".$orderId.",".$gmtsItemId.",".$countryId.",".$cbo_source.",".$cbo_working_company.",".$cbo_location.",".$txt_receive_date.",".$qty.",87,3,".$txt_remarks.",".$cbo_floor.",".$user_id.",'".$pc_date_time."',1,0)";
					$mstIdArr[$orderId][$gmtsItemId][$countryId]=$id;
					//$id = $id+1;
				}
			}
		}
		
		$field_array_dtls="	id, delivery_mst_id, mst_id, production_type, color_size_break_down_id, production_qnty, cut_no, bundle_no, barcode_no, is_rescan, status_active, is_deleted";
		
		foreach($dtlsArr as $bundle_no=>$bundle_data)
		{
			$colorSizedData 		=explode("**",$colorSizeArr[$bundle_no]);
			$gmtsMstId 				=$mstIdArr[$colorSizedData[0]][$colorSizedData[1]][$colorSizedData[2]];
			if($data_array_dtls!="") $data_array_dtls.=",";

			$dtls_id = return_next_id_by_sequence("pro_gar_production_dtls_seq",  "pro_garments_production_dtls", $con );

			$data_array_dtls.= "(".$dtls_id.",".$qc_id.",".$gmtsMstId.",87,'".$dtlsArrColorSize[$bundle_no]."','".$bundle_data['qc_pass']."',".$txt_lot_ratio.",'".$bundle_no."','".$bundleBarcodeArr[$bundle_no]."','".$bundleRescanArr[$bundle_no]."',1,0)"; 
		}
		
		$flag=$rID_mst=$rID_dtls =$rID=$dtlsrID=1;
		$rID_mst=sql_insert("pro_gmts_cutting_qc_mst",$field_array_qc_mst,$data_arra_cutt_mst,1);
		if($rID_mst==1) $flag=1; else $flag=0;
		$rID_dtls=sql_insert("pro_gmts_cutting_qc_dtls",$field_array_qc_dtls,$data_array_qc_detls,0);
		if($rID_dtls==1 && $flag==1) $flag=1; else $flag=0;
		$rID=sql_insert("pro_garments_production_mst",$field_array_mst,$data_array_mst,1);
		if($rID==1 && $flag==1) $flag=1; else $flag=0;
		$dtlsrID=sql_insert("pro_garments_production_dtls",$field_array_dtls,$data_array_dtls,1);
		if($dtlsrID==1 && $flag==1) $flag=1; else $flag=0;
		
		// echo "10**insert into pro_garments_production_mst($field_array_mst)values".$data_array_mst;die;
		// echo "10**insert into pro_garments_production_dtls($field_array_dtls)values".$data_array_dtls;die;
		//echo "10**".$rID_mst."**".$rID."**".$dtlsrID."**".$rID_dtls."**".$flag;die;
		
		if($db_type==1 || $db_type==2 )
		{
			if($flag==1)
			{
				oci_commit($con); 
				echo "0**".$qc_id."**".str_replace("'","",$new_system_id[0]);
			}
			else
			{
				oci_rollback($con);
				echo "10**";
			}
		}
		//check_table_status( 160,0);
		disconnect($con);
		die;
		//check_table_status( $_SESSION['menu_id'],0);
	}
  	else if ($operation==1) // Update Here End------------------------------------------------------
	{
 		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
	
		$mst_id=str_replace("'","",$txt_system_id);
		$txt_chal_no=explode("-",str_replace("'","",$txt_system_no));
		$challan_no=(int) $txt_chal_no[3];

		$field_array_delivery="delivery_date*updated_by*update_date";
		$data_array_delivery="".$txt_receive_date."*".$user_id."*'".$pc_date_time."'";
	
		for($j=1;$j<=$tot_row;$j++)
        {   
            $barcodeCheck="barcodeNo_".$j;
            $barcodeCheckArr[$$barcodeCheck]=$$barcodeCheck; 
        }
 
        $barcode="'".implode("','",$barcodeCheckArr)."'";
        $receive_sql="SELECT c.barcode_no,c.bundle_no from pro_garments_production_mst a,pro_garments_production_dtls c where a.id=c.mst_id and a.production_type=87 and c.barcode_no  in ($barcode)  and c.production_type=87 and c.status_active=1 and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and a.delivery_mst_id!=$mst_id and c.delivery_mst_id!=$mst_id and (c.is_rescan=0 or c.is_rescan is null)"; 
        $receive_result = sql_select($receive_sql);
        foreach ($receive_result as $row)
        {           
            $duplicate_bundle[$row[csf('barcode_no')]]=$row[csf('barcode_no')];
        }

		$field_array_qc_mst="cutting_qc_date*remarks*updated_by*update_date";
		$field_array_qc_dtls="id, mst_id, order_id, country_id, color_id, size_id, color_size_id, bundle_no, barcode_no, bundle_qty, is_rescan, inserted_by, insert_date, status_active, is_deleted";
		
		$field_array_mst="id, delivery_mst_id, cut_no, company_id, garments_nature, challan_no, po_break_down_id, item_number_id, country_id, production_source, serving_company, location, production_date, production_quantity, production_type, entry_break_down_type, remarks, floor_id, inserted_by, insert_date, status_active, is_deleted";
		
		$field_array_dtls="id, delivery_mst_id, mst_id, production_type, color_size_break_down_id, production_qnty, cut_no, bundle_no, barcode_no, is_rescan, status_active, is_deleted";

		$data_arra_cutt_mst=$txt_receive_date."*".$txt_remarks."*".$user_id."*'".$pc_date_time."'";
		$mstArr=array(); $dtlsArr=array(); $colorSizeArr=array(); $mstIdArr=array();

		for($j=1;$j<=$tot_row;$j++)
		{ 	
			$bundleNo 		="bundleNo_".$j;
			$barcodeNo 		="barcodeNo_".$j;			
			$orderId 		="orderId_".$j;
			$gmtsitemId 	="gmtsitemId_".$j;
			$countryId 		="countryId_".$j;
			$colorId 		="colorId_".$j;
			$sizeId 		="sizeId_".$j;
			$colorSizeId 	="colorSizeId_".$j;
			$checkRescan 	="isRescan_".$j;
			$qty 			="qty_".$j;
			$isRescan 		="isRescan_".$j;

			if($$isRescan==1 || $$isRescan==2)
			{
				$mstArr[$$orderId][$$gmtsitemId][$$countryId]+=$$qty;
				$colorSizeArr[$$bundleNo] 					=$$orderId."**".$$gmtsitemId."**".$$countryId;
				$dtlsArr[$$bundleNo]['qc_pass'] 			+=$$qty;
				
				$dtlsArrColorSize[$$bundleNo] 				=$$colorSizeId;
				$bundleRescanArr[$$bundleNo]				=$$isRescan;
				$bundleBarcodeArr[$$bundleNo] 				=$$barcodeNo;
				$deleted_bundle_arr[$$bundleNo] 			=$$bundleNo;
				if($data_array_qc_detls!='') $data_array_qc_detls.=",";
				$qc_dtls_id = return_next_id_by_sequence(  "pro_gmts_cutting_qc_dtls_seq","pro_gmts_cutting_qc_dtls", $con );	 
				$data_array_qc_detls.="(".$qc_dtls_id.",".$mst_id.",".$$orderId.",".$$countryId.",".$$colorId.",".$$sizeId.",".$$colorSizeId.",'".$$bundleNo."','".$$barcodeNo."','".trim($$qty)."','".$$isRescan."',".$user_id.",'".$pc_date_time."',1,0)";
			}
			else
			{
				if($duplicate_bundle[$$barcodeNo]=='')
	            {
					$mstArr[$$orderId][$$gmtsitemId][$$countryId]+=$$qty;
					$colorSizeArr[$$bundleNo] 					=$$orderId."**".$$gmtsitemId."**".$$countryId;
					$dtlsArr[$$bundleNo]['qc_pass'] 			+=$$qty;
					
					$dtlsArrColorSize[$$bundleNo] 				=$$colorSizeId;
					$bundleRescanArr[$$bundleNo]				=$$isRescan;
					$bundleBarcodeArr[$$bundleNo] 				=$$barcodeNo;
					$deleted_bundle_arr[$$bundleNo] 			=$$bundleNo;
					if($data_array_qc_detls!='') $data_array_qc_detls.=",";
					$qc_dtls_id = return_next_id_by_sequence(  "pro_gmts_cutting_qc_dtls_seq","pro_gmts_cutting_qc_dtls", $con );	 
					$data_array_qc_detls.="(".$qc_dtls_id.",".$mst_id.",".$$orderId.",".$$countryId.",".$$colorId.",".$$sizeId.",".$$colorSizeId.",'".$$bundleNo."','".$$barcodeNo."','".trim($$qty)."','".$$isRescan."',".$user_id.",'".$pc_date_time."',1,0)";
				}
			}
			// echo "10**insert into ppl_cut_lay_bundle_reject($field_array_breakdown_reject)values".$data_array_breakdown_reject;die;
		}
		//print_r($dtlsArr);die;
		$unique_mst_arr=array();
		foreach($mstArr as $orderId=>$orderData)
		{
			foreach($orderData as $gmtsItemId=>$gmtsItemIdData)
			{
				foreach($gmtsItemIdData as $countryId=>$qc_qty)
				{
					$id= return_next_id_by_sequence(  "pro_gar_production_mst_seq", "pro_garments_production_mst", $con );
					if($data_array_mst!="") $data_array_mst.=",";
					$data_array_mst.="(".$id.",".$mst_id.",".$txt_lot_ratio.",".$cbo_company_name.",".$garments_nature.",'".$challan_no."',".$orderId.",".$gmtsItemId.",".$countryId.",".$cbo_source.",".$cbo_working_company.",".$cbo_location.",".$txt_receive_date.",".$qc_qty.",87,3,".$txt_remarks.",".$cbo_floor.",".$user_id.",'".$pc_date_time."',1,0)";
					$mstIdArr[$orderId][$gmtsItemId][$countryId]=$id;
				}
			}
		}
		
		foreach($dtlsArr as $bundle_no=>$bundle_data)
		{
			$colorSizedData 		=explode("**",$colorSizeArr[$bundle_no]);
			$gmtsMstId 				=$mstIdArr[$colorSizedData[0]][$colorSizedData[1]][$colorSizedData[2]];
			if($data_array_dtls!="") $data_array_dtls.=",";

			$dtls_id = return_next_id_by_sequence(  "pro_gar_production_dtls_seq",  "pro_garments_production_dtls", $con );

			$data_array_dtls.= "(".$dtls_id.",".$mst_id.",".$gmtsMstId.",87,'".$dtlsArrColorSize[$bundle_no]."','".$bundle_data['qc_pass']."',".$txt_lot_ratio.",'".$bundle_no."','".$bundleBarcodeArr[$bundle_no]."','".$bundleRescanArr[$bundle_no]."',1,0)"; 
		}
		$flag=$delete =$delete_dtls =$delete_qc =$rID_mst_qc=$rID_dtls=$rID=$dtlsrID=1;
		
		$delete = execute_query("update pro_garments_production_mst set status_active=0,is_deleted=1,updated_by=$user_id,update_date='".$pc_date_time."' WHERE delivery_mst_id=$mst_id and production_type=87 and status_active=1 and is_deleted=0");
		if($delete==1) $flag=1; else $flag=0;
		$delete_dtls = execute_query("update pro_garments_production_dtls set status_active=0,is_deleted=1 WHERE delivery_mst_id=$mst_id and production_type=87 and status_active=1 and is_deleted=0");
		if($delete_dtls==1 && $flag==1) $flag=1; else $flag=0;
		$delete_qc = execute_query("update pro_gmts_cutting_qc_dtls set status_active=0,is_deleted=1,updated_by=$user_id,update_date='".$pc_date_time."' WHERE mst_id=$mst_id and status_active=1 and is_deleted=0");
		if($delete_qc==1 && $flag==1) $flag=1; else $flag=0;
	
		$rID_mst_qc=sql_update("pro_gmts_cutting_qc_mst",$field_array_qc_mst,$data_arra_cutt_mst,"id",$mst_id,1);
		if($rID_mst_qc==1 && $flag==1) $flag=1; else $flag=0;
		$rID_dtls=sql_insert("pro_gmts_cutting_qc_dtls",$field_array_qc_dtls,$data_array_qc_detls,0);
		if($rID_dtls==1 && $flag==1) $flag=1; else $flag=0;
		$rID=sql_insert("pro_garments_production_mst",$field_array_mst,$data_array_mst,1);
		if($rID==1 && $flag==1) $flag=1; else $flag=0;
		$dtlsrID=sql_insert("pro_garments_production_dtls",$field_array_dtls,$data_array_dtls,1);
		if($dtlsrID==1 && $flag==1) $flag=1; else $flag=0;
		
		//echo "10**insert into ppl_cut_lay_bundle_reject($field_array_breakdown_reject)values".$data_array_breakdown_reject;die;
		
		
		// echo "10**insert into pro_garments_production_dtls($field_array_dtls)values".$data_array_dtls;die;
		//echo "10**".bulk_update_sql_statement( "pro_gmts_knitting_issue_dtls", "id", $field_array_color_dtls, $data_array_color_dtls, $color_dtls_id_arr );die;	
		//echo "10**".$flag.'--'.$delete.'--'.$delete_dtls.'--'.$delete_qc.'--'.$rID_mst_qc.'--'.$rID_dtls.'--'.$rID.'--'.$dtlsrID; oci_rollback($con);die;
		//echo "10**".$dtlsrID;oci_rollback($con);die;
		

		if($db_type==1 || $db_type==2 )
		{
			if($flag==1)
			{
				oci_commit($con); 
				echo "1**".$mst_id."**".str_replace("'","",$txt_system_no)."**".implode(',',$non_delete_arr);
				 
			}
			else
			{
				oci_rollback($con);
				echo "10**";
				 
			}
		}
		//check_table_status( 160,0);
		disconnect($con);
		die;
	}
	else if ($operation==2)  // Delete Here---------------------------------------------------------- 
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
		
		$rID = sql_delete("pro_garments_production_mst","updated_by*update_date*status_active*is_deleted","".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1",'id ',$txt_mst_id,1);
		$dtlsrID = sql_delete("pro_garments_production_dtls","status_active*is_deleted","0*1",'mst_id',$txt_mst_id,1);
		$mst_id=str_replace("'","",$txt_system_id);
		
 		if($db_type==2 || $db_type==1 )
		{
			if($rID)
			{
				oci_commit($con);   
				echo "2**".$mst_id."**".str_replace("'","",$txt_challan_no); 
			}
			else
			{
				oci_rollback($con);
				echo "10**".$mst_id."**".str_replace("'","",$txt_challan_no); 
			}
		}
		disconnect($con);
		die;
	}
}

if($action=='populate_data_from_qc')
{
	$data_array=sql_select("SELECT id, cut_qc_prefix, cut_qc_prefix_no, cutting_qc_no, cutting_no, job_no, company_id, cutting_qc_date, status_active, is_deleted, location_id, floor_id, production_source, serving_company, remarks, service_location from pro_gmts_cutting_qc_mst where id=$data and status_active=1 and is_deleted=0 order by id desc");
	
	foreach ($data_array as $row)
	{ 
		echo "document.getElementById('txt_system_no').value 				= '".$row[csf("cutting_qc_no")]."';\n";
		echo "document.getElementById('txt_system_id').value 				= '".$row[csf("id")]."';\n";
		echo "document.getElementById('txt_lot_ratio').value 				= '".$row[csf("cutting_no")]."';\n";
		echo "document.getElementById('txt_job_no').value 					= '".$row[csf("job_no")]."';\n";
		echo "document.getElementById('cbo_company_name').value 			= '".$row[csf("company_id")]."';\n";
		echo "document.getElementById('cbo_source').value 					= '".$row[csf("production_source")]."';\n";
		echo "load_drop_down( 'requires/bundle_receive_distribution_point_controller', ".($row[csf("production_source")])."+'_'+".($row[csf("company_id")]).", 'load_drop_down_knitting_com','knitting_com');";
		
		echo "document.getElementById('cbo_working_company').value 			= '".$row[csf("serving_company")]."';\n";
		
		echo "load_location(".$row[csf("production_source")].");\n";
		echo "document.getElementById('cbo_working_location').value 		= '".$row[csf("service_location")]."';\n";
		echo "load_drop_down( 'requires/bundle_receive_distribution_point_controller', ".$row[csf("service_location")].", 'load_drop_down_floor', 'floor_td' );";
		echo "document.getElementById('cbo_floor').value  					= '".($row[csf("floor_id")])."';\n";
		echo "load_drop_down( 'requires/bundle_receive_distribution_point_controller', '".$row[csf('company_id')]."', 'load_drop_down_lc_location', 'location_td' );\n";

		echo "document.getElementById('cbo_location').value 				= '".$row[csf("location_id")]."';\n";
		echo "document.getElementById('txt_receive_date').value 			= '".change_date_format($row[csf("cutting_qc_date")])."';\n"; 
		exit();
	}
}

if($action=="distissue_number_popup")
{
  	echo load_html_head_contents("First Inspection Info","../../../", 1, 1, '','1','');
	extract($_REQUEST);
?>
	<script>
		function js_set_system_value(strCon ) 
		{
		document.getElementById('hidd_distissuemst_id').value=strCon;
		parent.emailwindow.hide();
		}
    </script>
</head>
<body>
    <div align="center" style="width:100%; overflow-y:hidden;" >
    <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
        <table width="950" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
            <thead>
                <tr>                	 
                    <th width="130">Company name</th>
                    <th width="80">Dist. Issue No</th>
                    <th width="80">Lot Ratio No</th>
                    <th width="80">Job No</th>
                    <th width="100">Order No</th>
                    <th width="100">QR Code</th>
                    <th width="130" colspan="2">Date Range</th>
                    <th><input type="reset" name="re_button" id="re_button" value="Reset" style="width:70px" class="formbutton"  /></th>           
                </tr>
            </thead>
            <tbody>
                <tr class="general">                    
                    <td><? echo create_drop_down( "cbo_company_name", 130, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --",'', ""); ?></td>
                    <td><input name="txt_cut_qc" id="txt_cut_qc" class="text_boxes" style="width:70px"  placeholder="Write"/></td>
                    <td>
                        <input type="text" id="txt_cut_no" name="txt_cut_no" style="width:70px"  class="text_boxes" placeholder="Write"/>
                        <input type="hidden" id="hidd_distissuemst_id" name="hidd_distissuemst_id" />
                    </td>
                    <td><input name="txt_job_search" id="txt_job_search" class="text_boxes" style="width:70px"  placeholder="Write"/></td>
                    <td><input name="txt_order_search" id="txt_order_search" class="text_boxes" style="width:70px" placeholder="Write" /></td>
                    <td><input name="txt_qr_search" id="txt_qr_search" class="text_boxes" style="width:90px" placeholder="Write" /></td>
                    <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" placeholder="From Date" /></td>
                    <td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px" placeholder="To Date" /></td>
                    <td>
                    <input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('txt_cut_no').value+'_'+document.getElementById('txt_job_search').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('txt_cut_qc').value+'_'+document.getElementById('txt_order_search').value+'_'+document.getElementById('txt_qr_search').value, 'create_dist_issue_list_view', 'search_div', 'bundle_receive_distribution_point_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:70px;" />				
                    </td>
                </tr>
                <tr>                  
                	<td align="center" valign="middle" colspan="9"><? echo load_month_buttons(1); ?></td>
                </tr>   
            </tbody>
        </table>
	</form>
    <div align="center" valign="top" id="search_div"> </div> 
</div>    
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if($action=="create_dist_issue_list_view")
{
    $ex_data = explode("_",$data);
	$company = $ex_data[0];	
	$cutting_no = $ex_data[1];
	$job_no = $ex_data[2];
	$from_date = $ex_data[3];
	$to_date = $ex_data[4];
	$cut_year= $ex_data[5];
	$system_no= $ex_data[6];
	$order_no= $ex_data[7];
	$qr_no= $ex_data[8];
	if(str_replace("'","",$company)==0) { echo "Please select company First"; die;}
    if($db_type==2)
	{ 
		$year_cond=" and extract(year from a.insert_date)=$cut_year"; $year=" extract(year from a.insert_date) as year";
	}
    else if($db_type==0) 
	{ $year_cond=" and SUBSTRING_INDEX(a.insert_date, '-', 1)=$cut_year"; $year=" SUBSTRING_INDEX(a.insert_date, '-', 1) as year";}
	if(str_replace("'","",$company)==0) $conpany_cond=""; else $conpany_cond="and a.company_id=".str_replace("'","",$company)."";
	if(str_replace("'","",$cutting_no)=="") $cut_cond=""; else $cut_cond="and b.cut_num_prefix_no='".str_replace("'","",$cutting_no)."'  ";
	if(str_replace("'","",$job_no)=="") $job_cond=""; else $job_cond="and c.job_no_prefix_num='".str_replace("'","",$job_no)."'";
	if(str_replace("'","",$system_no)=="") $system_cond=""; else $system_cond="and a.cut_qc_prefix_no=".trim($system_no)." $year_cond";
	if(str_replace("'","",$order_no)=="") $order_cond=""; else $order_cond=" and d.po_number='".str_replace("'","",$order_no)."'";
	if(trim($qr_no)!="") $barcode_no=" and f.barcode_no ='".trim($qr_no)."'"; else $barcode_no="";
	
	if( $from_date!="" && $to_date!="" )
	{
		if($db_type==0)
		{
			$sql_cond= " and a.cutting_qc_date  between '".change_date_format($from_date,'yyyy-mm-dd')."' and '".change_date_format($to_date,'yyyy-mm-dd')."'";
		}
		if($db_type==2)
		{
			$sql_cond= " and a.cutting_qc_date  between '".change_date_format($from_date,'yyyy-mm-dd','-',1)."' and '".change_date_format($to_date,'yyyy-mm-dd','-',1)."'";
		}
	}
	
	$sql_order="SELECT a.id, a.cutting_no, a.cut_qc_prefix_no, a.cutting_qc_no, a.table_no, a.job_no, a.cutting_qc_date, c.job_no_prefix_num, b.cut_num_prefix_no, $year, d.po_number
    FROM pro_gmts_cutting_qc_mst a, ppl_cut_lay_mst b, ppl_cut_lay_dtls e, wo_po_details_master c, wo_po_break_down d, pro_garments_production_dtls f
    where a.garments_nature=100 and a.production_type=86 and f.production_type=86 and a.cutting_no=b.cutting_no and b.job_no=c.job_no and c.job_no=d.job_no_mst and a.id=f.delivery_mst_id $conpany_cond $cut_cond $job_cond $sql_cond $order_cond $system_cond $barcode_no and a.status_active=1 and a.is_deleted=0 and f.status_active=1 and f.is_deleted=0 and b.id=e.mst_id group by a.id, a.cutting_no, a.cut_qc_prefix_no, a.cutting_qc_no, a.table_no, a.job_no, a.cutting_qc_date, c.job_no_prefix_num, b.cut_num_prefix_no, a.insert_date, d.po_number order by a.id DESC";
//echo $sql_order;die;
	echo create_list_view("list_view", "Knitting QC No,Year,Lot Ratio No,Job No,Order No,Knitting QC Date","60,60,60,80,100,80","750","270",0, $sql_order , "js_set_system_value", "cutting_qc_no", "", 1, "0,0,0,0,0,0", $arr, "cut_qc_prefix_no,year,cut_num_prefix_no,job_no,po_number,cutting_qc_date", "","setFilterGrid('list_view',-1)","0,0,0,0,0,3") ;	
	exit();
}

if($action=='populate_data_from_distissue')
{
	$data_array=sql_select("SELECT id, cut_qc_prefix, cut_qc_prefix_no, cutting_qc_no, cutting_no, job_no, company_id, cutting_qc_date, status_active, is_deleted, location_id, floor_id, production_source, serving_company, remarks, service_location, operator_id, inspector_id, supervisor_id,loss_min from pro_gmts_cutting_qc_mst where id=$data and status_active=1 and is_deleted=0 order by id desc");

	foreach ($data_array as $row)
	{ 
		echo "document.getElementById('txt_first_insp_no').value 			= '".$row[csf("cutting_qc_no")]."';\n";
		echo "document.getElementById('txt_system_id').value 				= '".$row[csf("id")]."';\n";
		echo "document.getElementById('txt_lot_ratio').value 				= '".$row[csf("cutting_no")]."';\n";
		echo "document.getElementById('txt_job_no').value 					= '".$row[csf("job_no")]."';\n";
		echo "document.getElementById('cbo_company_name').value 			= '".$row[csf("company_id")]."';\n";
		echo "document.getElementById('cbo_source').value 					= '".$row[csf("production_source")]."';\n";
		echo "load_drop_down( 'requires/bundle_receive_distribution_point_controller', ".($row[csf("production_source")])."+'_'+".($row[csf("company_id")]).", 'load_drop_down_knitting_com','knitting_com');";
		
		echo "document.getElementById('cbo_working_company').value 			= '".$row[csf("serving_company")]."';\n";
		
		echo "load_location(".$row[csf("production_source")].");\n";
		echo "document.getElementById('cbo_working_location').value 		= '".$row[csf("service_location")]."';\n";
		echo "load_drop_down( 'requires/bundle_receive_distribution_point_controller', ".$row[csf("service_location")].", 'load_drop_down_floor', 'floor_td' );";
		echo "document.getElementById('cbo_floor').value  					= '".($row[csf("floor_id")])."';\n";
		echo "load_drop_down( 'requires/bundle_receive_distribution_point_controller', '".$row[csf('company_id')]."', 'load_drop_down_lc_location', 'location_td' );\n";

		echo "document.getElementById('cbo_location').value 				= '".$row[csf("location_id")]."';\n";
		echo "document.getElementById('txt_receive_date').value 				    = '".change_date_format($row[csf("cutting_qc_date")])."';\n"; 
		exit();
	}
}

if($action=="system_number_popup")
{
  	echo load_html_head_contents("Batch Info","../../../", 1, 1, '','1','');
	extract($_REQUEST);
?>
	<script>
		function js_set_system_value(strCon ) 
		{
		document.getElementById('update_mst_id').value=strCon;
		parent.emailwindow.hide();
		}
    </script>
</head>
<body>
<div align="center" style="width:100%; overflow-y:hidden;" >
<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
            <table width="950" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
            <thead>
                <tr>                	 
                    <th width="130">Company name</th>
                    <th width="80">Issue No</th>
                    <th width="80">Lot Ratio No</th>
                    <th width="80">Job No</th>
                    <th width="100">Order No</th>
                    <th width="100">QR Code</th>
                    <th width="130" colspan="2">Date Range</th>
                    <th><input type="reset" name="re_button" id="re_button" value="Reset" style="width:70px" class="formbutton"  /></th>           
                </tr>
            </thead>
            <tbody>
                  <tr class="general">                    
                        <td><? echo create_drop_down( "cbo_company_name", 130, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --",'', ""); ?></td>
                        <td><input name="txt_cut_qc" id="txt_cut_qc" class="text_boxes" style="width:70px"  placeholder="Write"/></td>
                        <td>
                                <input type="text" id="txt_cut_no" name="txt_cut_no" style="width:70px"  class="text_boxes" placeholder="Write"/>
                                <input type="hidden" id="update_mst_id" name="update_mst_id" />
                        </td>
                        <td><input name="txt_job_search" id="txt_job_search" class="text_boxes" style="width:70px"  placeholder="Write"/></td>
                        <td><input name="txt_order_search" id="txt_order_search" class="text_boxes" style="width:70px" placeholder="Write" /></td>
                        <td><input name="txt_qr_search" id="txt_qr_search" class="text_boxes" style="width:90px" placeholder="Write" /></td>
                        <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" placeholder="From Date" /></td>
                        <td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px" placeholder="To Date" /></td>
                        <td>
                               <input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('txt_cut_no').value+'_'+document.getElementById('txt_job_search').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('txt_cut_qc').value+'_'+document.getElementById('txt_order_search').value+'_'+document.getElementById('txt_qr_search').value, 'create_system_search_list_view', 'search_div', 'bundle_receive_distribution_point_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:70px;" />				
                        </td>
                 </tr>
        		 <tr>                  
                    <td align="center" valign="middle" colspan="9"><? echo load_month_buttons(1); ?></td>
                </tr>   
            </tbody>
         </tr>         
      </table> 
     <div align="center" valign="top" id="search_div"> </div>  
  </form>
</div>    
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if($action=="create_system_search_list_view")
{
    $ex_data = explode("_",$data);
	$company = $ex_data[0];	
	$cutting_no = $ex_data[1];
	$job_no = $ex_data[2];
	$from_date = $ex_data[3];
	$to_date = $ex_data[4];
	$cut_year= $ex_data[5];
	$system_no= $ex_data[6];
	$order_no= $ex_data[7];
	$qr_no= $ex_data[8];
	if(str_replace("'","",$company)==0) { echo "Please select company First"; die;}
    if($db_type==2)
	{ 
		$year_cond=" and extract(year from a.insert_date)=$cut_year"; $year=" extract(year from a.insert_date) as year";
	}
    else if($db_type==0) 
	{ $year_cond=" and SUBSTRING_INDEX(a.insert_date, '-', 1)=$cut_year"; $year=" SUBSTRING_INDEX(a.insert_date, '-', 1) as year";}
	if(str_replace("'","",$company)==0) $conpany_cond=""; else $conpany_cond="and a.company_id=".str_replace("'","",$company)."";
	if(str_replace("'","",$cutting_no)=="") $cut_cond=""; else $cut_cond="and b.cut_num_prefix_no='".str_replace("'","",$cutting_no)."'  ";
	if(str_replace("'","",$job_no)=="") $job_cond=""; else $job_cond="and c.job_no_prefix_num='".str_replace("'","",$job_no)."'";
	if(str_replace("'","",$system_no)=="") $system_cond=""; else $system_cond="and a.cut_qc_prefix_no=".trim($system_no)." $year_cond";
	if(str_replace("'","",$order_no)=="") $order_cond=""; else $order_cond=" and d.po_number='".str_replace("'","",$order_no)."'";
	if(trim($qr_no)!="") $barcode_no=" and f.barcode_no ='".trim($qr_no)."'"; else $barcode_no="";
	
	if( $from_date!="" && $to_date!="" )
	{
		if($db_type==0)
		{
			$sql_cond= " and a.cutting_qc_date  between '".change_date_format($from_date,'yyyy-mm-dd')."' and '".change_date_format($to_date,'yyyy-mm-dd')."'";
		}
		if($db_type==2)
		{
			$sql_cond= " and a.cutting_qc_date  between '".change_date_format($from_date,'yyyy-mm-dd','-',1)."' and '".change_date_format($to_date,'yyyy-mm-dd','-',1)."'";
		}
	}
	
	$sql_order="SELECT a.id, a.cutting_no, a.cut_qc_prefix_no, a.cutting_qc_no, a.table_no, a.job_no, a.cutting_qc_date, c.job_no_prefix_num, b.cut_num_prefix_no, $year, d.po_number
    FROM pro_gmts_cutting_qc_mst a, ppl_cut_lay_mst b, ppl_cut_lay_dtls e, wo_po_details_master c, wo_po_break_down d, pro_garments_production_dtls f
    where a.garments_nature=100 and a.production_type=87 and a.cutting_no=b.cutting_no and b.job_no=c.job_no and c.job_no=d.job_no_mst and a.id=f.delivery_mst_id $conpany_cond $cut_cond $job_cond $sql_cond $order_cond $system_cond $barcode_no and a.status_active=1 and a.is_deleted=0 and f.status_active=1 and f.is_deleted=0 and b.id=e.mst_id group by a.id, a.cutting_no, a.cut_qc_prefix_no, a.cutting_qc_no, a.table_no, a.job_no, a.cutting_qc_date, c.job_no_prefix_num, b.cut_num_prefix_no, a.insert_date, d.po_number order by a.id DESC";
//echo $sql_order;die;
	echo create_list_view("list_view", "Issue No,Year,Lot Ratio No,Job No,Order No,Issue Date","60,60,60,80,100,80","750","270",0, $sql_order , "js_set_system_value", "id", "", 1, "0,0,0,0,0,0", $arr, "cut_qc_prefix_no,year,cut_num_prefix_no,job_no,po_number,cutting_qc_date", "","setFilterGrid('list_view',-1)","0,0,0,0,0,3") ;	
	exit();
}

if($action=="show_dtls_listview_update")
{
	$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name");
	$size_library=return_library_array( "select id,size_name from lib_size", "id", "size_name");
	$country_library=return_library_array( "select id,country_name from lib_country", "id", "country_name");

	$data=explode("_",$data);

	$sql_cut=sql_select("SELECT a.job_no, b.color_id, b.roll_data, a.id, b.id as dtls_id from ppl_cut_lay_mst a, ppl_cut_lay_dtls b where a.cutting_no='".$data[0]."' and a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 ");
	$job_no				=$sql_cut[0][csf("job_no")];
	$color_id			=$sql_cut[0][csf("color_id")];
	$consumption_string	=$sql_cut[0][csf("roll_data")];
	$mst_id				=$sql_cut[0][csf("id")];
	$dtls_id			=$sql_cut[0][csf("dtls_id")];
	list($shortName,$year,$job_prifix)=explode('-',$job_no);
	//echo $consumption_string;die;
	
	$job_sql=sql_select("SELECT c.short_name as BUYER_NAME, a.po_number as PO_NUMBER, a.id as ID from wo_po_break_down  a,wo_po_details_master b ,lib_buyer c where b.job_no='".$job_no."' and a.job_no_mst=b.job_no   and b.buyer_name=c.id ");
	$jbp_arr=array();
	foreach($job_sql as $jval)
	{
		$jbp_arr["buyer_name"]=$jval["BUYER_NAME"];
		$jbp_arr[$jval["ID"]]=$jval["PO_NUMBER"];
	}

	$table_width=1140;
	$div_width=$table_width+20;
	?>	
       <table cellpadding="0"width="<?php echo $div_width;?>"cellspacing="0"border="1"class="rpt_table"rules="all">
            <thead>
            	<tr>
                    <th width="20" rowspan="2">SL</th>
                    <th width="100" rowspan="2">Bundle No</th>
                    <th width="100" rowspan="2"title="Barcode No">QR Code No</th>
                    <th width="120" rowspan="2"> G. Color</th>
                    <th width="50" rowspan="2">Size</th>
                    <th width="70" rowspan="2" >Bundle Qty. (Pcs)</th>
                    <th width="80" colspan="2">GMT No</th>
                    <th width="100" rowspan="2">Knitting Floor</th>
                    <th width="90" rowspan="2">Job No</th>
                    <th width="65" rowspan="2">Buyer</th>
                    <th width="90" rowspan="2">Order No</th>
                    <th width="100" rowspan="2">Gmts. Item</th>
                    <th width="100" rowspan="2">Country</th>
                    <th rowspan="2">-</th>
                </tr>
                <tr>
                	<th width="40">From</th>
                    <th width="40">To</th>
                </tr>
            </thead>
        </table>
	<div style="width:<?=$div_width;?>px;max-height:250px;overflow-y:scroll" align="left"> 
        <table cellpadding="0"width="<?=$table_width;?>"cellspacing="0"border="1"class="rpt_table"rules="all"id="tbl_details"> <tbody>
		<?php  
			$i=1; $total_production_qnty=0;			
			$sqlResult =sql_select("SELECT b.* , a.gmt_item_id, a.color_id, c.bundle_qty, c.production_qnty, c.color_size_break_down_id, c.mst_id, c.is_rescan from ppl_cut_lay_dtls a, ppl_cut_lay_bundle b, pro_garments_production_dtls c where a.id=b.dtls_id and c.barcode_no=b.barcode_no and c.delivery_mst_id=$data[1] and c.production_type=87 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0");
			
		foreach($sqlResult as $row )
		{
			$gmt_mst_id[$row[csf('mst_id')]]=$row[csf('mst_id')];
		}
		//print_r($gmt_mst_id);die;	
		
			foreach($sqlResult as $selectResult)
			{
				if ($i%2==0)  $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
 			   ?>
                <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="" > 
                    <td width="20" align="center"><? echo $i; ?></td>
                    <td width="100" align="center"><p><? echo $selectResult[csf('bundle_no')]; ?></p></td>
                    <td width="100" align="center"><p><? echo $selectResult[csf('barcode_no')]; ?></p></td>
                    <td width="120" align="center" style="word-break:break-all"><p><?php  echo $color_library[$selectResult[csf('color_id')]]; ?></p></td>
                    <td width="50" align="center"><?php  echo $size_library[$selectResult[csf('size_id')]]; ?></td>
                    <td width="70" align="right"><?php  echo $selectResult[csf('production_qnty')]; ?></td>
                    
                    <td width="40" align="right"><?=$selectResult[csf('number_start')]; ?></td>
                    <td width="40" align="right"><?=$selectResult[csf('number_end')]; ?></td>
                    <td width="100" align="right"><? //=$selectResult[csf('number_end')]; ?></td>
                    <td width="90" align="center"><p><? echo $job_prifix*1; ?></p></td>
                    <td width="65" align="center"><?php echo $jbp_arr["buyer_name"]; ?></td>
                    <td width="90" align="center"><?php  echo $jbp_arr[$selectResult[csf('order_id')]]; ?></td>
                    		
                    <td width="100" align="center"><p><?php echo $garments_item[$selectResult[csf('gmt_item_id')]]; ?></p></td>
                    <td width="100" align="center"><p><? echo $country_library[$selectResult[csf('country_id')]]; ?></p></td>
                    <td><input type="button" value="-" name="minusButton[]" id="minusButton_<?=$i; ?>" style="width:30px" class="formbuttonplasminus" onClick="fnc_minusRow('<?=$i; ?>');"/>
                        <input type="hidden" id="txt_color_id_<?=$i; ?>" name="txt_color_id[]" style="width:80px;" value="<?=$selectResult[csf('color_id')]; ?>">
                        <input type="hidden" id="txt_size_id_<?=$i; ?>" name="txt_size_id[]" style="width:80px;" value="<?=$selectResult[csf('size_id')]; ?>">
						<input type="hidden" id="txt_order_id_<?=$i; ?>" name="txt_order_id[]" style="width:80px;" value="<?=$selectResult[csf('order_id')]; ?>">
                       	<input type="hidden" id="txt_gmt_item_id_<?=$i; ?>" name="txt_gmt_item_id[]" style="width:80px;" value="<?=$selectResult[csf('gmt_item_id')]; ?>">
                        <input type="hidden" id="txt_country_id_<?=$i; ?>" name="txt_country_id[]" style="width:80px;" value="<?=$selectResult[csf('country_id')]; ?>">
                     	<input type="hidden" id="txt_barcode_<?=$i; ?>" name="txt_barcode[]" style="width:80px;" value="<?=$selectResult[csf('barcode_no')]; ?>"> 
                        <input type="hidden" id="txt_colorsize_id_<?=$i; ?>" name="txt_colorsize_id[]" style="width:80px;" value="<?=$selectResult[csf('color_size_break_down_id')]; ?>">
                        <input  type="hidden" id="txt_dtls_id_<?=$i; ?>" name="txt_dtls_id[]" style="width:80px;" class="text_boxes" value="">
                       	<input type="hidden" id="trId_<?=$i; ?>" name="trId[]" value="<?=$i; ?>">
                    	<input type="hidden" name="isRescan[]" id="isRescan_<?=$i; ?>" value="<?=$selectResult[csf('is_rescan')]; ?>"/>  
                	</td>
                </tr>
            <?php
                $i++;
                $total_bundle_qty+=$selectResult[csf('production_qnty')];
			}
			?>
            </tbody>
            <tfoot>
            	<tr id="bundle_footer">
                    <th colspan="5">Total</th>
                    <th id="total_bundle_qty"><?php echo $total_bundle_qty; ?></th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                </tr>
            </tfoot>
		</table>
	</div>
	<?
	exit();
}

if($action=="load_report_format")
{
	$print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$data."' and module_id=7 and report_id in(50) and is_deleted=0 and status_active=1");		 
	echo trim($print_report_format);	
	exit();
}
?>