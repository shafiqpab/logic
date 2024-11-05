<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../../includes/common.php');

if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];

if($action=="load_drop_down_buyer")
{
	$data=explode("_",$data);
	if($data[1]==1) $party="1,3,21,90"; else $party="80";
	$company_id = $data[0];
	echo create_drop_down( "cbo_buyer_id", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company in ($company_id) $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in ($party)) group by buy.id, buy.buyer_name order by buy.buyer_name","id,buyer_name", 1, "--Select Buyer--", $selected, "load_drop_down( 'requires/shade_and_shrinkage_report_controller',this.value+'_'+1, 'load_drop_down_brand', 'brand_td' );","" );
	exit();
}

if ($action=="load_drop_down_brand")
{
	$data_arr = explode("_", $data);
	if($data_arr[1] == 1) $width=140; else $width=150;
	echo create_drop_down( "cbo_brand_id", $width, "select id, brand_name from lib_buyer_brand brand where buyer_id='$data_arr[0]' and status_active =1 and is_deleted=0 order by brand_name ASC","id,brand_name", 1, "--Brand--", $selected, "" );
	exit();
}


if($action=="job_no_popup")
{
	echo load_html_head_contents("Job Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
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
    <input type='hidden' id='hide_job_no' name="hide_job_no" />
    <input type='hidden' id='hide_job_id' name="hide_job_id" />
	<?
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	
	$company_id=str_replace("'","",$companyID);
	$buyer_id=str_replace("'","",$buyer_name);
	$year_id=str_replace("'","",$cbo_year_id);
	$search_type=str_replace("'","",$search_type);
	//$month_id=$data[5];
	//echo $month_id;
	$sql_cond="";
	if($buyer_id>0) $sql_cond .=" and a.buyer_name=$buyer_id";
	if($buyer_id>0) $sql_cond2 =" and a.buyer_id=$buyer_id";
	
	if($db_type==0) $year_field_by="year(a.insert_date)";
	else if($db_type==2) $year_field_by="to_char(a.insert_date,'YYYY')";
	else $year_field_by="";
	
	if($year_id!=0) $year_cond=" and $year_field_by='$year_id'"; else $year_cond="";
	
	if($search_type==1)
	{
		$arr=array (0=>$buyer_arr);
		$sql= "select a.id as id, a.job_no, a.job_no_prefix_num, a.buyer_name, a.style_ref_no, $year_field_by as year from wo_po_details_master a where a.status_active=1 and a.is_deleted=0 and a.company_name=$company_id $sql_cond $year_cond order by id DESC";
		echo create_list_view("list_view", "Buyer Name,Job No,Year,Style Ref. No", "170,130,100","610","350",0, $sql , "js_set_value", "id,job_no_prefix_num", "", 1, "buyer_name,0,0,0", $arr , "buyer_name,job_no_prefix_num,year,style_ref_no", "","setFilterGrid('list_view',-1)",'0,0,0,0','') ;
		exit();
	}
	else if($search_type==2)
	{
		$arr=array (0=>$buyer_arr);
		$sql= "select a.id as id, a.job_no, a.job_no_prefix_num, a.buyer_name, a.style_ref_no, $year_field_by as year, b.id as po_id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_name=$company_id $sql_cond $year_cond order by id DESC";
		echo create_list_view("list_view", "Buyer Name,Job No,Year,Style Ref. No,Order No", "170,70,70,100","610","350",0, $sql , "js_set_value", "job_no,style_ref_no", "", 1, "buyer_name,0,0,0,0", $arr , "buyer_name,job_no_prefix_num,year,style_ref_no,po_number", "","setFilterGrid('list_view',-1)",'0,0,0,0,0','') ;
		exit();
	}
	
}



if($action=="report_generate")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$rpt_type=str_replace("'","",$rpt_type);
	$cbo_company_id=str_replace("'","",$cbo_company_id);
	$cbo_buyer_id=str_replace("'","",$cbo_buyer_id);
	$cbo_store_id=str_replace("'","",$cbo_store_id);
	//echo $cbo_company_id."=".$cbo_store_id;die;
	$cbo_year=str_replace("'","",$cbo_year);
	$txt_job_no=str_replace("'","",$txt_job_no);
	$txt_job_id=str_replace("'","",$txt_job_id);
	$txt_order_no=str_replace("'","",$txt_order_no);
	$txt_order_id=str_replace("'","",$txt_order_id); 
	$txt_consigment_no=str_replace("'","",$txt_consigment_no);
	$txt_style_ref_no=str_replace("'","",$txt_style_ref_no);
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);

	$sql_cond="";
	//if($cbo_buyer_id > 0) $sql_cond=" and a.buyer_name=$cbo_buyer_id";

	if($txt_job_no > 0) $sql_cond=" and a.buyer_name=$cbo_buyer_id";

	if($cbo_year > 0)$year_cond=" and to_char(insert_date,'YYYY')=$cbo_year"; 

	$job_no_arr=sql_select( "select job_no from wo_po_details_master where JOB_NO_PREFIX_NUM='$txt_job_no' and company_name='$cbo_company_id' $year_cond and status_active=1 and is_deleted=0");
	$txt_job_no=$job_no_arr[0][csf("job_no")];

	if($cbo_company_id!=0)$company_cond=" and a.company_id='$cbo_company_id'"; 
	if($txt_job_no >0 || $txt_job_no!="")$job_no_cond=" and a.job_no='$txt_job_no'"; 
	if($txt_consigment_no >0)$consigment_cond=" and a.consigment='$txt_consigment_no'"; 
	if($txt_style_ref_no >0)$style_ref_cond=" and a.style_ref='$txt_style_ref_no'"; 
	
	if( $txt_date_from !="" && $txt_date_to !="")
	{
		if($cbo_based_on==1) $sql_cond.= " and b.transaction_date between '$txt_date_from' and '$txt_date_to'"; else $sql_cond.= " and b.insert_date between '$txt_date_from' and '$txt_date_to'";
	}
	$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name"  );
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$color_arr=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );	
	$season_arr=return_library_array( "select id, season_name from lib_buyer_season", "id", "season_name"  );
	$brand_arr=return_library_array( "select id, brand_name from LIB_BUYER_BRAND", "id", "brand_name"  );
	
	$composition_arr=array(); $constructtion_arr=array();
	

	$pattern_sql="SELECT mst_id,color_id,pattern_no,length_max,length_min,width_max,width_min from woven_shrink_shade_pattern_br where STATUS_ACTIVE=1 and IS_DELETED=0  and  length_max IS NOT NULL";
	$pattern_data=sql_select($pattern_sql);
	foreach($pattern_data as $val){
		$pattern_data_arr[$val[csf("mst_id")]][$val[csf("color_id")]]['length_max']=$val[csf("length_max")];
		$pattern_data_arr[$val[csf("mst_id")]][$val[csf("color_id")]]['length_min']=$val[csf("length_min")];
		$pattern_data_arr[$val[csf("mst_id")]][$val[csf("color_id")]]['width_max']=$val[csf("width_max")];
		$pattern_data_arr[$val[csf("mst_id")]][$val[csf("color_id")]]['width_min']=$val[csf("width_min")];
		

		$pattern_no[$val[csf("mst_id")]][$val[csf("color_id")]][$val[csf("length_max")]][$val[csf("length_min")]][$val[csf("width_max")]][$val[csf("width_min")]]['pattern']=$val[csf("pattern_no")];

		$pattern_arr[$val[csf("pattern_no")]]="P".$val[csf("pattern_no")];

		$patternDataArr[$val[csf("mst_id")]][$val[csf("pattern_no")]]=array(
			length_max=>$val[csf("length_max")],
			length_min=>$val[csf("length_min")],
			width_max=>$val[csf("width_max")],
			width_min=>$val[csf("width_min")]

		);
		// $pattern
	}

	// 	  echo "<pre>";
	//    print_r($pattern_arr);
	$sql="SELECT a.sys_number,a.company_id, a.job_no, a.buyer_id, a.brand_id, a.season_name, a.season_year, a.style_ref, a.gmts_color_id, a.consigment, a.tolarence_per, b.ID, b.MST_ID, b.ccl_no,b.intellocut_roll_no, b.roll_no, b.length_yds, b.WIDTH, b.SHADE, b.before_wash_length_cm, b.beforew_wash_width_cm, b.after_wash_length_cm, b.after_wash_width_cm,a.insert_date,a.id, a.remarks as REMARKS, b.before_wash_gsm as BEFORE_WASH_GSM, b.after_wash_gsm as AFTER_WASH_GSM FROM woven_shrink_shade_mst a, woven_shrink_shade_dtls b WHERE b.STATUS_ACTIVE=1 and b.IS_DELETED=0 and a.id=b.MST_ID and a.STATUS_ACTIVE=1 and a.IS_DELETED=0 $company_cond $job_no_cond $consigment_cond $style_ref_cond order by a.job_no, a.gmts_color_id,a.sys_number,b.id asc";

	// echo $sql;
		$data=sql_select($sql);
		// echo "<pre>";
		//  print_r($data);
		$i=1;
		foreach($data as $val){

			 $job_no=$val[csf("job_no")];
			 $job_wise_arr[$val[csf("job_no")]][$val[csf("gmts_color_id")]][$val[csf("sys_number")]][$val[csf("ccl_no")]]['ccl_no']=$val[csf("ccl_no")];
			 $job_wise_arr[$val[csf("job_no")]][$val[csf("gmts_color_id")]][$val[csf("sys_number")]][$val[csf("ccl_no")]]['roll_lenght_yds'] +=$val[csf("length_yds")];	

		
				$job_wise_arr[$val[csf("job_no")]][$val[csf("gmts_color_id")]][$val[csf("sys_number")]][$val[csf("ccl_no")]]['intellocut_roll_no']=$val[csf("intellocut_roll_no")] ;
				$job_wise_arr[$val[csf("job_no")]][$val[csf("gmts_color_id")]][$val[csf("sys_number")]][$val[csf("ccl_no")]]['roll_no']=$val[csf("roll_no")] ;
				$job_wise_arr[$val[csf("job_no")]][$val[csf("gmts_color_id")]][$val[csf("sys_number")]][$val[csf("ccl_no")]]['shade']=$val[csf("shade")] ;
				$job_wise_arr[$val[csf("job_no")]][$val[csf("gmts_color_id")]][$val[csf("sys_number")]][$val[csf("ccl_no")]]['before_wash_length_cm']=$val[csf("before_wash_length_cm")] ;
				$job_wise_arr[$val[csf("job_no")]][$val[csf("gmts_color_id")]][$val[csf("sys_number")]][$val[csf("ccl_no")]]['beforew_wash_width_cm']=$val[csf("beforew_wash_width_cm")] ;
				$job_wise_arr[$val[csf("job_no")]][$val[csf("gmts_color_id")]][$val[csf("sys_number")]][$val[csf("ccl_no")]]['after_wash_length_cm']=$val[csf("after_wash_length_cm")] ;
				$job_wise_arr[$val[csf("job_no")]][$val[csf("gmts_color_id")]][$val[csf("sys_number")]][$val[csf("ccl_no")]]['after_wash_width_cm']=$val[csf("after_wash_width_cm")] ;
				$job_wise_arr[$val[csf("job_no")]][$val[csf("gmts_color_id")]][$val[csf("sys_number")]][$val[csf("ccl_no")]]['mst_id']=$val[csf("id")] ;
				$job_wise_arr[$val[csf("job_no")]][$val[csf("gmts_color_id")]][$val[csf("sys_number")]][$val[csf("ccl_no")]]['before_wash_gsm']=$val["BEFORE_WASH_GSM"] ;
				$job_wise_arr[$val[csf("job_no")]][$val[csf("gmts_color_id")]][$val[csf("sys_number")]][$val[csf("ccl_no")]]['after_wash_gsm']=$val["AFTER_WASH_GSM"] ;


				$job_sys_no_wise_arr[$val[csf("job_no")]][$val[csf("gmts_color_id")]][$val[csf("sys_number")]]['insert_date']=$val[csf("insert_date")] ;
				$job_sys_no_wise_arr[$val[csf("job_no")]][$val[csf("gmts_color_id")]][$val[csf("sys_number")]]['remarks']=$val["REMARKS"] ;
				$job_sys_no_wise_arr[$val[csf("job_no")]][$val[csf("gmts_color_id")]][$val[csf("sys_number")]]['style_ref']=$val[csf("style_ref")] ;
				$job_sys_no_wise_arr[$val[csf("job_no")]][$val[csf("gmts_color_id")]][$val[csf("sys_number")]]['brand_id']=$val[csf("brand_id")] ;
				$job_sys_no_wise_arr[$val[csf("job_no")]][$val[csf("gmts_color_id")]][$val[csf("sys_number")]]['buyer_id']=$val[csf("buyer_id")] ;
				$job_sys_no_wise_arr[$val[csf("job_no")]][$val[csf("gmts_color_id")]][$val[csf("sys_number")]]['tolarence_per']=$val[csf("tolarence_per")] ;
				$job_sys_no_wise_arr[$val[csf("job_no")]][$val[csf("gmts_color_id")]][$val[csf("sys_number")]]['consigment']=$val[csf("consigment")] ;
			
			
				$shade_arr[$val[csf("job_no")]][$val[csf("gmts_color_id")]][$val[csf("sys_number")]][$val[csf("shade")]]=$val[csf("shade")] ;
				$shade_yds[$val[csf("job_no")]][$val[csf("gmts_color_id")]][$val[csf("sys_number")]][$val[csf("shade")]]['yds'] +=$val[csf("length_yds")];	
				$shade_roll[$val[csf("job_no")]][$val[csf("gmts_color_id")]][$val[csf("sys_number")]][$val[csf("shade")]]['roll_no'] +=1;	
		}
		    // echo "<pre>";print_r($shade_arr);//die;
		
		
		
		//print_r($issue_rate_amnt_arr);
		ob_start();
		?>

		<fieldset style="width:1280px;" align="center">
			<table cellpadding="0" cellspacing="0" width="1200">
				<tr class="form_caption" style="border:none;">
					<td align="center" width="100%" colspan="12" style="font-size:18px"><strong><? echo " Shrinkage and Shade Report"; ?></strong></td>
				</tr>
				<tr  class="form_caption" style="border:none;">
					<td align="center" width="100%" colspan="12" style="font-size:16px"><strong><?echo $company_arr[str_replace("'","",$cbo_company_id)]; ?></strong></td>
				</tr>
				<tr  class="form_caption" style="border:none;">
					<td align="center" width="100%" colspan="12" style="font-size:14px"><strong> <?// if($date_from!="") echo "Upto : ".change_date_format(str_replace("'","",$txt_date_from)) ;?></strong></td>
				</tr>
			</table>

		
			
			<div style="width:1280px; max-height:350px; overflow-y:scroll;" align="center" style="margin-left:20px" id="scroll_body">
				
					<?
					$i=1; 

					

				foreach($job_wise_arr as $job_id=>$gmts_data){
					foreach($gmts_data as $gmts_id=>$sys_data){
						foreach($sys_data as $sys_id=>$ccl_data){
							?>
							<br>
				<table cellpadding="0" cellspacing="0" border="0" rules="all" class="rpt_table" width="1200">
				<tr>
					<td width="120"><b>Job No</b></td>
					<td width="120"><?=$job_no;?></td>
					<td width="100"><b>Buyer</b></td>
					<td width="100"><?=$buyer_arr[$job_sys_no_wise_arr[$job_id][$gmts_id][$sys_id]['buyer_id']];?></td>
					<td width="120"><b>Brand</b></td>
					<td width="120"><?=$brand_arr[$job_sys_no_wise_arr[$job_id][$gmts_id][$sys_id]['brand_id']];?></td>
					<td width="120"><b>Merch Style Ref.</b></td>
					<td width="120"><?=$job_sys_no_wise_arr[$job_id][$gmts_id][$sys_id]['style_ref'];?></td>
					<td width="120"><b>Sys Number</b></td>
					<td width="120"><?=$sys_id;?></td>									
				</tr>	
				<tr>
					<td width="120" ><b>Consigment</b></td>
					<td width="120" ><?=$job_sys_no_wise_arr[$job_id][$gmts_id][$sys_id]['consigment'];?></td>
					<td width="100" ><b>Gmt Color</b></td>
					<td width="100" ><?=$color_arr[$gmts_id];?></td>
					<td width="120" ><b>Tolarence</b></td>
					<td width="120" ><?=$job_sys_no_wise_arr[$job_id][$gmts_id][$sys_id]['tolarence_per'];$tolarence_val=$job_sys_no_wise_arr[$job_id][$gmts_id][$sys_id]['tolarence_per'];?></td>
					<td width="120" ><b>Insert Date</b></td>
					<td width="120" ><?=$job_sys_no_wise_arr[$job_id][$gmts_id][$sys_id]['insert_date'];?></td>
					<td width="120" ><b>Remarks</b></td>
					<td width="120" ><?=$job_sys_no_wise_arr[$job_id][$gmts_id][$sys_id]['remarks'];?></td>
									
				</tr>			
			</table>
			

				<table width="1200" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">            	
				<thead>
                	<tr>
						
                    	<th width="30" rowspan="2">SL No</th>
                        <th width="100" rowspan="2">Consigment Roll</th>
                        <th width="100" rowspan="2">Manual Roll No</th>
                        <th width="50" rowspan="2">Roll No</th>
                        <th width="100" rowspan="2">Roll Length[Yds]</th>
                        <th width="60" rowspan="2">Shade</th> 
                        <th colspan="2" >Before Wash</th>
                        <th colspan="2" >After Wash</th>
                        <th colspan="2" >Shrinkage %</th>                        
                        <th width="60" rowspan="2">Pattern No</th>
                        <th colspan="4">GSM</th>
                                      
                    </tr>
					<tr>
                    	
                        <th width="70" >Length [CM]</th>
                        <th width="70" >Width [CM]</th>
                        <th width="70" >Length [CM]</th>
                        <th width="70" >Width [CM]</th>
                        <th width="70" >Length </th>
                        <th width="70" >Width</th>                       
						<th width="70" >GSM Before Wash</th>
                        <th width="70" >GSM After Wash</th>
                        <th width="70" >Variance</th>                      
                        <th >Variance %</th>                      
                    </tr>
					
				</thead>
			</table><?
							$max_length=$min_length=$max_width=$min_width='';
							foreach($ccl_data as $ccl_no=>$val){
								$shrinkage_length=(($val['after_wash_length_cm']-$val['before_wash_length_cm'])/$val['before_wash_length_cm'])*100;
								$shrinkage_width=(($val['after_wash_width_cm']-$val['beforew_wash_width_cm'])/$val['before_wash_length_cm'])*100;


								// if($shrinkage_length >$max_length){$max_length=$shrinkage_length;}
								// if($max_length > $shrinkage_length){$min_length=$shrinkage_length;}
								// if($shrinkage_width >$max_width){$max_width=$shrinkage_width;}
								// if($max_width > $shrinkage_width){$min_width=$shrinkage_width;}
								if($max_length){
									if($shrinkage_length >$max_length){$max_length=$shrinkage_length;}
									if($min_length > $shrinkage_length){$min_length=$shrinkage_length;}
								}else{
									$max_length=$min_length=$shrinkage_length;
								}
				
								if($max_width){
									if($shrinkage_width >$max_width){$max_width=$shrinkage_width;}
									if($min_width > $shrinkage_width){$min_width=$shrinkage_width;}
								}else{
									$max_width=$min_width=$shrinkage_width;
								}

								$p_length_max=$pattern_data_arr[$val["mst_id"]][$gmts_id]['length_max'];
							    $p_length_min=$pattern_data_arr[$val["mst_id"]][$gmts_id]['length_min'];
								$p_width_max=$pattern_data_arr[$val["mst_id"]][$gmts_id]['width_max'];
								$p_width_min=$pattern_data_arr[$val["mst_id"]][$gmts_id]['width_min'];

								$GSMVariance=(($val['after_wash_gsm']-$val['before_wash_gsm'])/$val['before_wash_gsm'])*100;
								$chkGSMVariance=ltrim($GSMVariance,'-');
								if($chkGSMVariance>$tolarence_val){$variance_color='red';}else{$variance_color='';}
							
				?>
				
					<table width="1200" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" id="table_body" > 
							<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
								<td width="30"><?=$i;?></td>
								<td width="100"><?=$val['ccl_no'];?></td>
								<td width="100"><?=$val['intellocut_roll_no'];?></td>
								<td width="50"><?=$val['roll_no'];?></td>
								<td width="100"><?=$val['roll_lenght_yds'];?></td>
								<td width="60" align="center"><?=$val['shade'];?></td> 
								<td width="70" align="right"><?=$val['before_wash_length_cm'];?></td>
								<td width="70" align="right"><?=$val['beforew_wash_width_cm'];?></td>
								<td width="70" align="right"><?=$val['after_wash_length_cm'];?></td>                         
								<td width="70" align="right"><?=$val['after_wash_width_cm'];?></td>
								<td width="70" align="right"><?=number_format($shrinkage_length, 2,'.','');?></td>
								<td width="70" align="right"><?=number_format($shrinkage_width, 2,'.','');?></td>
									
								<? 
								//  L=	-1.20
							    //  W=	-4.40
								// 	[1] => 
								// 		[length_max] => -2.4
								// 		[length_min] => -1.6
								// 		[width_max] => -4.8
								// 		[width_min] => -4
							
								// [2] 
								// 		[length_max] => -1.59
								// 		[length_min] => -.08
								// 		[width_max] => -4.8
								// 		[width_min] => -4
								



								// foreach($patternDataArr[$val["mst_id"]] as $pattern_no=>$drows){
								// 		if( ($shrinkage_length <= $drows[length_max]) && ($shrinkage_length >= $drows[length_min])
								// 			&& ($shrinkage_width <= $drows[width_max]) && ($shrinkage_width >= $drows[width_min])
								// 		){echo $pattern_arr[$pattern_no]."=>";

								// 			$patternNo[$sys_id][$pattern_arr[$pattern_no]]['patternNo'] +=1;
								// 			$patternNo[$sys_id][$pattern_arr[$pattern_no]]['yds'] +=$val['roll_lenght_yds'];
								// 			break;}else{
								// 				$pattern_arr[$pattern_no]."=>";
								// 			}
								// }


								$pattern_name='';
								foreach($patternDataArr[$val["mst_id"]] as $pattern_no=>$drows){
									if(($shrinkage_length <= $drows[length_max]) && ($shrinkage_length >= $drows[length_min]) 	&& ($shrinkage_width <= $drows[width_max]) && ($shrinkage_width >= $drows[width_min]) ){
										// echo $pattern_arr[$pattern_no]."<br>";
										$pattern_name= $pattern_arr[$pattern_no];
										$patternNo[$sys_id][$pattern_arr[$pattern_no]]['patternNo'] +=1;
										$patternNo[$sys_id][$pattern_arr[$pattern_no]]['yds'] +=$val['roll_lenght_yds'];
										break;}
									
								}

										
								?>
								<td width="60" align="center" bgcolor="<?php echo ($pattern_name=='' ? 'red' : '');?>" title="<?=$p_length_max.'_'.$p_length_min.'_'.$p_width_max.'_'.$p_width_min;?>">
									<? echo $pattern_name;?>
								</td>
								<td width="70" align="right"><?=$val['before_wash_gsm'];?></td>
								<td width="70" align="right"><?=$val['after_wash_gsm'];?></td>
								<td width="70" align="right"><?=number_format($val['after_wash_gsm']-$val['before_wash_gsm'],2);?></td>             
								<td align="right" bgcolor="<?=$variance_color;?>"><?echo number_format($GSMVariance,2);?></td>             
								
							</tr>
							</table>
					
						<?
						$i++;	
					
						
					
							}
							?>

				<table  width="1160" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" id="table_body">
				<table width="380" cellpadding="0" cellspacing="0" style="float:left" border="1" rules="all" class="rpt_table" > 
                    <thead>                                        
					<th width="70" colspan="5" style="background:#ff9900" >Pattern Summary</th> 								
                    </thead>
					<thead>
						<th width="70" style="background:#ff9900">Pattern No</th>
					   <th width="80" style="background:#ff9900">Length (Warp)</th>
					   <th width="80" style="background:#ff9900">Width (Weft)</th>
					   <th width="80" style="background:#ff9900">No Of Rolls</th>
					   <th width="70" style="background:#ff9900">Yds</th>
				   </thead>
						<?
						ksort($patternNo[$sys_id]);
						foreach($patternNo[$sys_id] as $pid => $val){
						
							?>
								<tr>
 									 <td width="70" align="center"><b><?=$pid;?></b></td>
									 <td width="80" ></td>
									 <td width="80" >&nbsp;</td>
									 <td width="80" ><?=$val['patternNo'];?></td>
									 <td width="70" ><?=$val['yds'];?></td>
								</tr>
						<?}
						
						?>
 		
						<tr>
                </table> 
					<table width="200" cellpadding="0" cellspacing="0" style="float:left" border="1" rules="all" class="rpt_table" > 
                    <thead>                                        
                        <th width="70" colspan="3" style="background:#3399ff">Shade Summary</th>
								
                    </thead>
					<thead>
					   <th width="60" style="background:#3399ff">Shade </th>
					   <th width="60" style="background:#3399ff">Rolls</th>
					   <th width="80" style="background:#3399ff">Yds</th>
				   </thead>
						<?
						// [$val[csf("shade")]]
						ksort($shade_arr[$job_id][$gmts_id][$sys_id]);
						foreach($shade_arr[$job_id][$gmts_id][$sys_id] as $shadeid => $shade_data){
						
							?>
								<tr>
 									 <td width="60" align="center"><b><?=$shade_data;?></b></td>
									 <td width="60" ><?=$shade_roll[$job_id][$gmts_id][$sys_id][$shadeid]['roll_no'];?></td>
									 <td width="80" ><?=$shade_yds[$job_id][$gmts_id][$sys_id][$shadeid]['yds'];?></td>
								</tr>
						<?}
						
						?>
 		
						<tr>
						

					
                </table> 
				<table width="240" cellpadding="0" cellspacing="0" border="1" rules="all" style="float:left" class="rpt_table">

						<thead>					
						<th width="80" colspan="4" style="background:#ff9933"  align="center">Max To Min</th>			
					  </thead>						
						<tr>
							<td width="70" align="center"><b>Length</b></td>
							<td width="60"  align="center"><?=$max_length;?></td>
							<td width="40" align="center">To</td>
							<td width="60"  align="center"><?=$min_length;?></td>
						</tr>
						<tr>
							<td width="70" align="center"><b>Width</b></td>
							<td width="60"  align="center"><?=$max_width;?></td>
							<td width="40" align="center">To</td>
							<td width="60"  align="center"><?=$min_width;?></td>
						</tr>
               		 </table> 
						
				<table>
						
				<?
							
						   }
						 }
						}
					?>
			
            </div>  
		</fieldset>
        <?
    $html = ob_get_contents();
    ob_clean();
    foreach (glob($user_id."*.xls") as $filename) {
    	@unlink($filename);
    }
    //---------end------------//
    $name=time();
    $filename=$user_id."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');	
    $is_created = fwrite($create_new_doc, $html);
    echo "$html**$filename**$rpt_type";
    exit();
}


?>