<?
header('Content-type:text/html; charset=utf-8');
session_start();
if ($_SESSION['logic_erp']['user_id'] == "") header("location:login.php");

include('../../includes/common.php');
$user_id = $_SESSION['logic_erp']["user_id"];
$data = $_REQUEST['data'];
$action = $_REQUEST['action'];

$company_arr=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
$country_library=return_library_array( "select id,country_name from lib_country", "id", "country_name"  );
$color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
$location_arr=return_library_array( "select id, location_name from lib_location",'id','location_name');
$floor_arr=return_library_array( "select id, floor_name from lib_prod_floor",'id','floor_name');
$size_arr=return_library_array( "select id, size_name from lib_size",'id','size_name');
$sewing_line_arr=return_library_array( "select id, line_name from lib_sewing_line",'id','line_name');
$item_arr=return_library_array( "select id, item_name from lib_garment_item",'id','item_name');
$prod_reso_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');
if($action=="load_report_format")
{
	$print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$data."' and module_id=7 and report_id=56 and is_deleted=0 and status_active=1");
	echo trim($print_report_format);
	exit();

}


if ($action=="load_drop_down_working_location")
{
	//echo $data;die;
	echo create_drop_down( "wc_location_id", 130, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --", $selected, "load_drop_down( 'requires/finish_barcode_generate_controller', document.getElementById('wc_location_id').value+'_'+document.getElementById('working_company_id').value, 'load_drop_down_wc_company_location_wise_floor', 'wc_floor_td' )" );		
	exit(); 

}
if($action=="load_drop_down_lc_company_location"){
	//echo $data;die;
	echo create_drop_down( "lc_location_id", 130, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --", $selected, "" );		
	exit(); 
}

if($action=="load_drop_down_wc_company_location_wise_floor"){
	//echo $data;die;
	$data=explode("_", $data);
	$company_id=$data[1];
	$location_id=$data[0];
	$sql="select id,floor_name from lib_prod_floor where company_id=$company_id and location_id=$location_id and status_active =1 and is_deleted=0 order by floor_name";
	//echo $sql;die;
	echo create_drop_down("wc_floor", 130, $sql,"id,floor_name", 1, "-- Select Location --", $selected, "" );		
	exit(); 
}

if($action=="generate_report"){
	//echo "test";die;
	//echo load_html_head_contents("Finish Barcode Generate", "../../", 1, 1, '', '');

	$process = array( &$_POST );
	//print_r($process);die;
	extract(check_magic_quote_gpc( $process ));
	$working_company_id = str_replace("'", "", $working_company_id);
	$wc_location_id = str_replace("'", "", $wc_location_id);
	$lc_company_id = str_replace("'", "", $lc_company_id);
	$lc_location_id = str_replace("'", "", $lc_location_id);
	$wc_floor = str_replace("'", "", $wc_floor);
	$txt_line_no_hidden = str_replace("'", "", $txt_line_no_hidden);
	$txt_line_no = str_replace("'", "", $txt_line_no);
	$wc_location_condition='';
	//echo $txt_date_to.'__'.$txt_date_from;die;
		
	if ($wc_location_id != 0)
	{
		$wc_location_condition = " and a.location = ".$wc_location_id." ";
	}
	else
	{
		$wc_location_condition = "";
	}
	
	$working_company_condition = '';
	if ($working_company_id != 0)
	{
		$working_company_condition = " and a.serving_company = ".$working_company_id." ";
	}else{
		$working_company_condition="";
	}


	
	$lc_company_condition = '';
	if ($lc_company_id != 0)
	{
		$lc_company_condition = " and a.company_id = ".$lc_company_id." ";
	}else{
		$lc_company_condition="";
	}
	$txt_line_no_condition = '';
	if ($txt_line_no_hidden != "" && $txt_line_no!="")
	{
		$txt_line_no_condition = " and a.sewing_line = ".$txt_line_no_hidden." ";
	}else{
		$txt_line_no_condition="";
	}

	$production_date_condition='';
	

	if (str_replace("'", "", $txt_date_from) != "" || str_replace("'", "", $txt_date_to) != "") {
		if ($db_type == 0) {
			$start_date = change_date_format(str_replace("'", "", $txt_date_from), "yyyy-mm-dd", "");
			$end_date = change_date_format(str_replace("'", "", $txt_date_to), "yyyy-mm-dd", "");
		} else if ($db_type == 2) {
			$start_date = change_date_format(str_replace("'", "", $txt_date_from), "", "", 1);
			$end_date = change_date_format(str_replace("'", "", $txt_date_to), "", "", 1);
		}
		$production_date_condition = " and a.production_date between '$start_date' and '$end_date'";
		
	}


	$wc_floor_condition='';

	if ($wc_floor != 0)
	{
		$wc_floor_condition = " and a.floor_id = ".$wc_floor." ";
	}else{
		$wc_floor_condition="";
	}
	
	
	//echo $sql;die;
	$date_cond=($db_type==2)? " TO_CHAR(a.production_hour,'HH24:MI') as production_hour " : " TIME_FORMAT( production_hour, '%H:%i' ) as production_hour ";
	if($db_type == 0){
		
		$sql = "select a.production_date,a.prod_reso_allo,a.po_break_down_id,a.challan_no, a.serving_company,a.location,d.grouping ,f.style_ref_no,d.po_number,a.item_number_id,a.floor_id,b.color_type_id ,c.color_number_id,a.sewing_line,a.country_id,$date_cond,sum(b.production_qnty) as production_qnty,c.size_number_id
	from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown c, wo_po_break_down d,wo_po_details_master f 
	where a.id=b.mst_id and b.color_size_break_down_id=c.id and c.po_break_down_id=d.id and d.id=a.po_break_down_id and d.job_no_mst=f.job_no and a.production_type=5 and a.entry_break_down_type=3 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1  and c.size_number_id is not null and length(c.color_number_id)>0 
	$working_company_condition	$wc_location_condition $wc_floor_condition $lc_company_condition  $txt_line_no_condition $production_date_condition
	group by a.production_date,a.prod_reso_allo,a.po_break_down_id,a.challan_no, a.serving_company,a.location,f.style_ref_no,d.po_number,a.item_number_id,a.floor_id,b.color_type_id ,c.color_number_id,a.sewing_line,a.country_id,production_hour,c.size_number_id,d.grouping";
	}else if($db_type == 2){
		$sql = "select a.production_date,a.prod_reso_allo,a.po_break_down_id,a.challan_no, a.serving_company,a.location,d.grouping ,f.style_ref_no,d.po_number,a.item_number_id,a.floor_id,b.color_type_id ,c.color_number_id,a.sewing_line,a.country_id,$date_cond,sum(b.production_qnty) as production_qnty,c.size_number_id
	from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown c, wo_po_break_down d,wo_po_details_master f 
	where a.id=b.mst_id and b.color_size_break_down_id=c.id and c.po_break_down_id=d.id and d.id=a.po_break_down_id and d.job_no_mst=f.job_no and a.production_type=5 and a.entry_break_down_type=3 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1  and c.size_number_id is not null and c.color_number_id is not null  
	$working_company_condition	$wc_location_condition $wc_floor_condition $lc_company_condition  $txt_line_no_condition $production_date_condition
	group by a.production_date,a.prod_reso_allo,a.po_break_down_id,a.challan_no, a.serving_company,a.location,f.style_ref_no,d.po_number,a.item_number_id,a.floor_id,b.color_type_id ,c.color_number_id,a.sewing_line,a.country_id,production_hour,c.size_number_id,d.grouping";
	}
	$result=sql_select($sql);

	
	?>
	<div align="center" style="width:100%;">		
		<fieldset style="width:1310px;">
			
        	
    		<table cellpadding="0" width="1290" cellspacing="0" border="1" id="scanning_tbl" class="rpt_table" rules="all" style="text-align: center;">    		<thead>
	                <th width="30">SL</th>
	                <th width="60">Production<br>Date</th>
	                <th width="50">Ch. No</th>
	                <th width="130">W. Company</th>
	                <th width="70">W. Com. <br>Location</th>
	                <th width="80">Internal <br>Reff.</th>
	                <th width="60">Style Reff.</th>
	                <th width="80">Order No</th>
	                <th width="100">Item Name</th>
	                <th width="70">Country</th>
	                <th width="60">Color Type</th>
	                <th width="70">GMT Color</th>
	                <th width="50">Floor</th>
	                <th width="60">Sewing Line</th>
	                <th width="70">Rep. Hour</th>
	                <th width="50">GMT Size</th>
	                <th width="80">QC Pass. qty</th>
	                <th width="60">QR Code Generate</th>
	                <th>QR Code</th>               
            	</thead>	
        		<tbody>
        			<?php 
        				$i=1;
        				foreach ($result as $row) {
        					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
        					$po_break_down_id=$row[csf('po_break_down_id')];
        					$challan_no=$row[csf('challan_no')];
        					$country_id=$row[csf('country_id')];
        					$company_id=$row[csf('serving_company')];
        					$location_id=$row[csf('location')];
        					$floor_id=$row[csf('floor_id')];
        					$size_id=$row[csf('size_number_id')];
        					$color_type_id=$row[csf('color_type_id')];
        					$color_id= $row[csf('color_number_id')];
        					$item_id=$row[csf('item_number_id')];
        					$sewing_line='';
        					$line_id='';
							if($row[csf('prod_reso_allo')]==1)
							{
								$line_number=explode(",",$prod_reso_arr[$row[csf('sewing_line')]]);
								foreach($line_number as $val)
								{
									if($sewing_line==''){
										$sewing_line=$sewing_line_arr[$val];
										$line_id=$val;
									}
									else {
										$sewing_line.=",".$sewing_line_arr[$val];
										$line_id.=",".$val;
									}

								}
							}
							else{
								$sewing_line=$sewing_line_arr[$row[csf('sewing_line')]];
								$line_id=$row[csf('sewing_line')];
							} 
        					
        					$production_date=$row[csf('production_date')];
        					$production_hour=$row[csf('production_hour')];
        					$qnty=$row[csf('production_qnty')];
        					$s="select * from finish_barcode  where po_break_down_id=$po_break_down_id and color_id=$color_id and country_id=$country_id and size_id=$size_id and challan_no=$challan_no and company_id=$company_id and color_type_id=$color_type_id and floor_id=$floor_id and item_id=$item_id and line_id=$line_id and production_hour='$production_hour' and production_date='$production_date' and status_active=1 and is_deleted=0";
        					
        					
	        				$res=sql_select($s);
	        				$count=count($res);
	        				$view_disabled=false;
	        				$generate_disabled=false;
	        				if($count>0){
	        					
	        					$generate_disabled=true;
	        				}else{
	        					$view_disabled=true;
	        				}
	        				?>
		        			<tr bgcolor="<? echo $bgcolor; ?>"  id="tr_<? echo $i; ?>">
		        				<td ><p><? echo $i;?></p></td>
		        				<td ><p><?php echo change_date_format($row[csf('production_date')]);?>&nbsp;</p></td>
		        				<td ><p><?php echo $row[csf('challan_no')];?></p></td>
		        				<td ><p><?php echo $company_arr[$row[csf('serving_company')]];?></p></td>
		        				<td ><p><?php echo $location_arr[$row[csf('location')]];?></p></td>
		        				<td ><p><?php echo $row[csf('grouping')];?></p></td>
		        				<td ><p><?php echo $row[csf('style_ref_no')];?></p></td>
		        				<td ><p><?php echo $row[csf('po_number')];?></p></td>
		        				<td ><p><?php echo $item_arr[$row[csf('item_number_id')]];?></p></td>
		        				<td ><p><? echo $country_library[$row[csf('country_id')]];?></p></td>
		        				<td ><p><? echo $color_type[$row[csf('color_type_id')]]; ?></p></td>
		        				<td ><p><?php echo $color_arr[$row[csf('color_number_id')]]; ?></p></td>
		                        <td ><p><?php echo $floor_arr[$row[csf('floor_id')]]; ?></p></td>
		        				<td ><p><?php echo $sewing_line;?></p>
		        				</td>
		                        <td ><p><? echo $row[csf('production_hour')]; ?></p></td>
		                        <td ><p><? echo $size_arr[$row[csf('size_number_id')]];?></p></td>
		                        <td ><p><? echo $row[csf('production_qnty')] ?></p></td>                        
		                        <td ><p>
		                        <a style="cursor: pointer;border: outset 1px #66CC00;text-decoration: none;width:70px;height: 60px;" id="generate_<?php echo $i;?>"   class="formbutton <? if($generate_disabled){ echo 'formbutton_disabled';}?>" href="javascript:generate_barcode('<? echo $po_break_down_id;?>','<? echo $challan_no;?>','<? echo $country_id; ?>','<? echo $company_id; ?>','<? echo $location_id; ?>','<? echo $floor_id; ?>','<? echo $size_id; ?>','<? echo $color_type_id; ?>','<? echo $color_id; ?>','<? echo $item_id; ?>','<? echo $line_id; ?>','<? echo $production_date; ?>','<? echo $production_hour; ?>','<? echo $qnty; ?>','<? echo $i; ?>')">Generate</a></p>
		                        </td>
		                        <td><p>
		                        <a style="cursor: pointer;border: outset 1px #66CC00;text-decoration: none;width:70px;height: 60px;"  id="view_<?php echo $i;?>"   class="formbutton <? if($view_disabled){ echo 'formbutton_disabled';}?>" href="javascript:view_barcode('<? echo $po_break_down_id;?>','<? echo $challan_no;?>','<? echo $country_id; ?>','<? echo $company_id; ?>','<? echo $location_id; ?>','<? echo $floor_id; ?>','<? echo $size_id; ?>','<? echo $color_type_id; ?>','<? echo $color_id; ?>','<? echo $item_id; ?>','<? echo $line_id; ?>','<? echo $production_date; ?>','<? echo $production_hour; ?>')">View</a></p>
		                        </td>        				
		        			</tr>
		        			<?php $i++;
		        		}

		        		?>

        		</tbody>
    	   </table>
        
    </fieldset>
       
    </div>

     <script type="text/javascript">
    	setFilterGrid("scanning_tbl",-1);
    </script>
  <?php   
  exit(); 
	
}



if($action=="generate_barcode"){
	// echo load_html_head_contents("Generate Barcode", "../../", 1, 1,$unicode,'','');


	$process = array( &$_POST );
	//print_r($process);die;
	//echo "test";die;
	extract(check_magic_quote_gpc( $process ));
	$working_company_id = str_replace("'", "", $working_company_id);
	$wc_location_id = str_replace("'", "", $wc_location_id);
	$lc_company_id = str_replace("'", "", $lc_company_id);
	$lc_location_id = str_replace("'", "", $lc_location_id);
	$wc_floor = str_replace("'", "", $wc_floor);
	$txt_line_no_hidden = str_replace("'", "", $txt_line_no_hidden);
	$wc_location_condition='';

	// po_break_down_id,challan_no,country_id,company_id,location_id,floor_id,size_id,color_type_id,color_id,item_id,line_id,production_date,production_hour,qnty

	$po_break_down_id = str_replace("'", "", $po_break_down_id);
	$challan_no = str_replace("'", "", $challan_no);
	$country_id = str_replace("'", "", $country_id);
	$company_id = str_replace("'", "", $company_id);
	$location_id = str_replace("'", "", $location_id);
	$floor_id = str_replace("'", "", $floor_id);
	$size_id = str_replace("'", "", $size_id);
	$color_type_id = str_replace("'", "", $color_type_id);
	$color_id = str_replace("'", "", $color_id);
	$item_id = str_replace("'", "", $item_id);
	$line_id = str_replace("'", "", $line_id);
	$qnty = str_replace("'", "", $qnty);
	//echo $country_id.'__'.$po_number;die;
	

	if($po_break_down_id ){
		$field_array="id,po_break_down_id,challan_no, country_id,company_id,location_id,floor_id,size_id,color_type_id,color_id,item_id,line_id,qrcode_year,qrcode_suffix,qrcode,production_date,production_hour,status_active,is_deleted, inserted_by, insert_date";
		$data_array="";
		$con=connect();
		$id=return_next_id("id", "finish_barcode",$con);
		$qrcode_year=date("y",time());
		
        $res_year=sql_select("select * from finish_barcode  where qrcode_year=$qrcode_year and  status_active=1 and is_deleted=0 order by id desc,qrcode_suffix desc");
        $year_count=count($res_year);
        if($year_count==0){
        	// if no data in current year then start qrcode from 1
        	$suffix=1;
        }else{
        	foreach ($res_year as $row) {
        		$suffix=$row[csf('QRCODE_SUFFIX')];
        		break;
        	}
        	$suffix++;
        	
        	
        }
		for($j=1;$j<=$qnty;$j++){
			
			$qrcode="";
			
			

			$qrcode = $qrcode_year  . str_pad($suffix, 10, "0", STR_PAD_LEFT);		
			 $data_array .= "(".$id.",".$po_break_down_id.",'".$challan_no."',".$country_id.",".$company_id.",".$location_id.",".$floor_id.",".$size_id.",".$color_type_id.",".$color_id.",".$item_id.",'".$line_id."',".$qrcode_year.",".$suffix.",".$qrcode.",'".$production_date."','".$production_hour."',1,0,".$user_id.",'".$pc_date_time."'"."),";
			 $suffix++;
			 $id++;

		}

		if($data_array!=''){
			 $data_array=chop($data_array,',');
			//echo $data_array;die;
			// echo "insert into finish_barcode (".$field_array.") values ".$data_array;die; 
			$rID=sql_insert("finish_barcode",$field_array,$data_array,1);
		}
		if($db_type==0)
		{
			if($rID )
			{
				mysql_query("COMMIT");
				echo "0**".$g_id;
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**".$g_id;
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID)
			{
				oci_commit($con);
				echo "0**".$g_id;
			}
			else
			{
				oci_rollback($con);
				echo "10**".$g_id;
			}
		}
		unset($_POST);
		disconnect($con);
		exit();

	}else{
		echo "10**".$g_id;
		exit(); 
	}
	
		
	
  
}
if($action=="print_selected_barcode")
{
	
	?>
	 <table width="580" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" style="margin:0 auto;" id="print_barcode_table">
            <thead>
                
                <tr>
                    <th width="15%">SL</th>
                    <th width="30%">Sys Chh.</th>
                    <th width="25%">GMT Size</th>
                    <th width="30%">Barcode No</th>
                   
                    
                </tr>
            </thead>
            <tbody id="view_barcode_table">
            <?
		
			$result=sql_select("select * from finish_barcode where  id in($data) and status_active=1 and is_deleted=0 order by id asc");
			$i=1; 
			foreach($result as $row)
			{
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$dzn_qnty='';
				
			?>
            	<tr bgcolor="<? echo $bgcolor; ?>" 
            		>
                    <td><? echo $i++; ?>&nbsp;</td>
                    <td ><? echo $row[csf('challan_no')]; ?>&nbsp;</td>
                    <td ><? echo $size_arr[$row[csf('size_id')]]; ?>&nbsp;</td>
                    <td ><? echo $row[csf('qrcode')]; ?>&nbsp;</td>
                   
                </tr>
         
           <? }?>
           </tbody>
        </table>
  <?php 
	exit();
}


if($action=="view_barcode"){
	echo load_html_head_contents("Barcode info", "../../", 1, 1,$unicode,'','');

 	extract($_REQUEST);
	//echo $po_break_down_id."*".$tot_po_qnty;die;

	//echo $ratio;die;


?>
<script>

	function window_close()
	{
		parent.emailwindow.hide();
	}
	function new_window()
	{
		// document.getElementById('print').style.overflow="auto";
		// document.getElementById('print').style.maxHeight="none";
		
		var program_ids = "";
        var total_tr = $('#print_barcode_table tbody tr').length;
        for (i = 1; i <=total_tr; i++) {
            try {
                if ($('#tbl_' + i).is(":checked")) {
                    program_id = $('#promram_id_' + i).val();
                    if (program_ids == "") program_ids = program_id; else program_ids += ',' + program_id;
                }
            }
            catch (e) {
                //got error no operation
            }
        }

        if (program_ids == "") {
            alert("Please Select At Least One Barcode");
            return;
        }
        print_report(program_ids, "print_selected_barcode", "finish_barcode_generate_controller");
		//$('#view_barcode_table tr:first').hide();
		
		
		// var w = window.open("Surprise", "#");
		// var d = w.document.open();
		// d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+'<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css" /></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
		// d.close(); 

		// document.getElementById('print').style.overflowY="scroll";
		// document.getElementById('print').style.maxHeight="280px";
		
		
		// $('#view_barcode_table tr:first').show();
		
	}

</script>
<center><input  type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px;text-align: center;"/>
	</center>
<fieldset style="width:600px;text-align: center;margin:0 auto;" id="print" >

<legend>Barcode Pop Up</legend>


    <div style="width:100%;" id="report_container">
       <table width="580" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" style="margin:0 auto;" id="print_barcode_table">
            <thead>
                
                <tr>
                    <th width="15%">SL</th>
                    <th width="25%">Sys Chh.</th>
                    <th width="25%">GMT Size</th>
                    <th width="25%">Barcode No</th>
                    <th width="15%">Check Box</th>
                    
                </tr>
            </thead>
            <tbody id="view_barcode_table">
            <?
		
			$result=sql_select("select * from finish_barcode  where po_break_down_id=$po_break_down_id and color_id=$color_id and country_id=$country_id and size_id=$size_id and challan_no=$challan_no and company_id=$company_id and color_type_id=$color_type_id and floor_id=$floor_id and item_id=$item_id and line_id='$line_id' and production_hour='$production_hour' and production_date='$production_date' and status_active=1 and is_deleted=0 order by id asc");
			$i=1; 
			foreach($result as $row)
			{
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$dzn_qnty='';
				
			?>
            	<tr bgcolor="<? echo $bgcolor; ?>" 
            		>
                    <td><? echo $i++; ?>&nbsp;</td>
                    <td ><? echo $row[csf('challan_no')]; ?>&nbsp;</td>
                    <td ><? echo $size_arr[$row[csf('size_id')]]; ?>&nbsp;</td>
                    <td ><? echo $row[csf('qrcode')]; ?></td>
                   <td ><input type="checkbox" id="tbl_<? echo $i; ?>" name="check[]"/>
                        <input id="promram_id_<? echo $i; ?>" name="promram_id[]" type="hidden"
                                                    value="<? echo $row[csf('id')]; ?>"/></td>
                </tr>
         
           <? }?>
           </tbody>
        </table>
        </div>
    </fieldset>
    <script src="../../includes/functions_bottom.js" type="text/javascript"></script>
    <script type="text/javascript">
    	setFilterGrid("view_barcode_table",-1);
    </script>
<?

	exit();

}
if($action=="line_popup"){

		echo load_html_head_contents("Popup Info","../../", 1, 1, $unicode);
		$sql="select prod_reso_allo,sewing_line from pro_garments_production_mst where status_active=1 and is_deleted=0 and length(sewing_line)>0 group by prod_reso_allo,sewing_line";
		//echo $sql;die;
		$res=sql_select($sql);
		?>
			
			<script type="text/javascript">
				function js_set_value(id,line_name)
				{
					//alert(id);
					$("#txt_line_no1").val(line_name);
					$("#txt_line_no_hidden1").val(id);
			  		parent.emailwindow.hide();
			 	}
			</script>
        
        
        	<table cellpadding="0" width="100%" cellspacing="0" border="1"  class="rpt_table" rules="all">
        		<thead>
                <th width="30%">SL</th>
                <th width="70%">Line Name</th>
                
               
            </thead>
        	</table>
    		<table cellpadding="0" width="100%" cellspacing="0" border="1" id="list_view_line" class="rpt_table" rules="all">
    			
        		<tbody>
        			<?php 
        				$i=1;
        				foreach ($res as $row) {
        					 if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
        					$id=$row[csf('sewing_line')];
        					//$line_name=$row[csf('line_name')];
        					$sewing_line='';
        					$line_id='';
							if($row[csf('prod_reso_allo')]==1)
							{
								$line_number=explode(",",$prod_reso_arr[$row[csf('sewing_line')]]);
								foreach($line_number as $val)
								{
									if($sewing_line==''){
										$sewing_line=$sewing_line_arr[$val];
										$line_id=$val;
									}
									else {
										$sewing_line.=",".$sewing_line_arr[$val];
										$line_id.=",".$val;
									}

								}
							}
							else{
								$sewing_line=$sewing_line_arr[$row[csf('sewing_line')]];
								$line_id=$row[csf('sewing_line')];
							}
        			?>
        			<tr bgcolor="<? echo $bgcolor; ?>"  id="tr_<? echo $i; ?>" onClick="js_set_value('<? echo $id;?>','<? echo $sewing_line;?>');">
        				<td width="30%" ><? echo $i++;?></td>
        				<td width="70%"><?php echo $sewing_line;?>

        				
        			</tr>
        		<?php }?>
        		</tbody>
    	   </table>
    	   <input type="hidden"  id="txt_line_no_hidden1">
    	   <input type="hidden"  id="txt_line_no1">
    	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
        <script type="text/javascript">
        	setFilterGrid('list_view_line',-1);
        </script>
   
<?	
	exit();
}


if ($action == "load_drop_down_po")
{
	$data = explode("**", $data);
	$booking_no = $data[0];
	$color_id = $data[1];
	$is_sales = $data[2];
	$sales_id = $data[3];
	if($is_sales == 1)
	{
		echo create_drop_down("cboPoNo_1", 130, "SELECT id, job_no FROM fabric_sales_order_mst WHERE id='$sales_id' and status_active=1 and is_deleted=0", "id,job_no", 1, "-- Select Po Number --", '0', "load_item_desc(this.value,this.id );", '', "", "", "", "", "", "", "cboPoNo[]");
	}
	else
	{
		echo create_drop_down("cboPoNo_1", 130, "SELECT a.id, a.po_number FROM wo_po_break_down a, wo_booking_dtls b WHERE a.id=b.po_break_down_id and b.booking_no='$booking_no' and b.fabric_color_id='$color_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.po_number", "id,po_number", 1, "-- Select Po Number --", '0', "load_item_desc(this.value,this.id );", '', "", "", "", "", "", "", "cboPoNo[]");
	}
	
	exit();
}
?>
