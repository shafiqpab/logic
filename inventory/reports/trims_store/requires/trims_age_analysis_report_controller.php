<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$companyArr = return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0","id","company_name"); 
$trim_group= return_library_array( "select id, item_name from lib_item_group",'id','item_name');

if ($action=="item_description_popup")
{
	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$data=explode('_',$data);
	//print_r ($data);  
?>	
    <script>
	 var selected_id = new Array, selected_name = new Array(); selected_attach_id = new Array();
	 
	function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		
    	function check_all_data() {
			var tbl_row_count = document.getElementById( 'list_view' ).rows.length;
			tbl_row_count = tbl_row_count - 1;
			for( var i = 1; i <= tbl_row_count; i++ ) {
				var onclickString = $('#tr_' + i).attr('onclick');
				var paramArr = onclickString.split("'");
				var functionParam = paramArr[1];
				js_set_value( functionParam );
				
			}
		}
		
		
		
	function js_set_value(id)
	{
		var str=id.split("_");
		toggle( document.getElementById( 'tr_' + str[0] ), '#FFFFFF' );
		var strdt=str[2];
		str=str[1];
	
		if( jQuery.inArray(  str , selected_id ) == -1 ) {
			selected_id.push( str );
			selected_name.push( strdt );
		}
		else {
			for( var i = 0; i < selected_id.length; i++ ) {
				if( selected_id[i] == str  ) break;
			}
			selected_id.splice( i, 1 );
			selected_name.splice( i,1 );
		}
		var id = '';
		var ddd='';
		for( var i = 0; i < selected_id.length; i++ ) {
			id += selected_id[i] + ',';
			ddd += selected_name[i] + ',';
		}
		id = id.substr( 0, id.length - 1 );
		ddd = ddd.substr( 0, ddd.length - 1 );
		$('#item_desc_id').val( id );
		$('#item_desc_val').val( ddd );
	} 
		  
	</script>
     <input type="hidden" id="item_desc_id" />
     <input type="hidden" id="item_desc_val" />
 <?
	if ($data[1]==0) $item_name =""; else $item_name =" and item_group_id in($data[1])";
	$sql="SELECT id, item_category_id, product_name_details from product_details_master where item_category_id=4 and status_active=1 and is_deleted=0 $item_name"; 
	$arr=array(0=>$item_category);
	echo  create_list_view("list_view", "Description,Product ID", "300,150","450","300",0, $sql , "js_set_value", "id,product_name_details", "", 1, "0,0,0", $arr , "product_name_details,id", "",'setFilterGrid("list_view",-1);','0,0,0,0,0,0','',1) ;
	exit();
}

if($action=="generate_report")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$cbo_item_group=str_replace("'",'',$cbo_item_group);
	$txt_item_description_id=str_replace("'",'',$txt_item_description_id);
	$txt_date_from=str_replace("'",'',$txt_date_from);
	$cbo_company_name=str_replace("'",'',$cbo_company_name);
	
	
	//------------------------------ageing colum 
	$curr_date=str_replace("'","",$txt_date_from);
	$range=str_replace("'","",$txt_range);
	$colum=str_replace("'","",$txt_no_col);
	$c=0;
	for($col=1; $col<=$colum; $col++){ 
		$to=$range*$col;
		$from=$range*$c;
		if($colum==1){$caption = 'Above '.$from.' Days';}
		else if($col==1){$caption = $from.'-'.$to.' Days';}
		else if($col==$colum){$caption = 'Above '.$from.' Days';}
		else{$caption =  ($from+1).'-'.$to.' Days';}
		//$caption_arr[$caption]=$caption;
		$caption_arr[$to]=$caption;
		$c++;
	}
	$end_key = end(array_keys($caption_arr));
	//----------------------------------
	
	
	
	
	if ($cbo_company_name==0) $company_id =""; else $company_id =" and a.company_id='$cbo_company_name'";
	
	if ($cbo_item_group=="") 
	{
		$items_group=""; 
		$item="";
	}
	else 
	{
		$items_group=" and b.prod_id in ($cbo_item_group)";
		$item=" and b.item_group_id in ($cbo_item_group)";
	}

	if ($txt_item_description_id=='') 
	{
		$item_description=""; 
		$prod_cond="";
	}
	else 
	{
		$item_description=" and b.prod_id in ($txt_item_description_id)";
		$prod_cond=" and b.id in ($txt_item_description_id)";
	}
	
	$data_array=array();
	$trnasactionData=sql_select("Select b.id,min(a.transaction_date) as receive_date, max(transaction_date) as max_date,
	sum(case when a.transaction_type in(1,4) then a.cons_quantity else 0 end) as rcv_total_opening,
	sum(case when a.transaction_type in(2,3) then a.cons_quantity else 0 end) as iss_total_opening,
	sum(case when a.transaction_type in(1) then a.cons_quantity else 0 end) as receive,
	sum(case when a.transaction_type in(3) then a.cons_quantity else 0 end) as receive_return,
	sum(case when a.transaction_type in(4) then a.cons_quantity else 0 end) as issue_return,
	sum(case when a.transaction_type in(2) then a.cons_quantity else 0 end) as issue
	from inv_transaction a, product_details_master b
	where a.prod_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.item_category_id=4 and a.item_category=4 and a.order_id=0 $company_id $item $prod_cond group by b.id order by b.id ASC");	
	
	
	
	foreach($trnasactionData as $row)
	{
		$data_array[$row[csf("id")]]['rcv_total_opening']=$row[csf("rcv_total_opening")];
		$data_array[$row[csf("id")]]['iss_total_opening']=$row[csf("iss_total_opening")];
		$data_array[$row[csf("id")]]['max_date']=$row[csf("max_date")];
		$ageOfDays = datediff("d",$row[csf("receive_date")],$curr_date);
		$age_qty_issue_arr[$row[csf("id")]]=($row[csf("issue")]-$row[csf("issue_return")]);
		$receiv_blance=($row[csf("receive")]+$row[csf("issue_return")]);
	
		$data_array[$row[csf("id")]]['age_of_days']=$ageOfDays;
	
	
		foreach($caption_arr as $key=>$value){
			$start=($key-$range);
			if($start <= $ageOfDays && $key >= $ageOfDays){
				$age_qty_arr[$row[csf("id")]][$key]+=$receiv_blance;
				break;
			}
			elseif($end_key < $ageOfDays){
				$age_qty_arr[$row[csf("id")]][$end_key]+=$receiv_blance;
				break;
			}
			
		}
	
	
	}
	
	
	foreach($age_qty_issue_arr as $pid=>$issueValue)
	{
		$restValue=$issueValue;
		foreach(array_reverse($caption_arr,true) as $key=>$value){
			
			if($age_qty_arr[$pid][$key]<$restValue && $age_qty_arr[$pid][$key]>0){
				$restValue=$restValue-$age_qty_arr[$pid][$key];
				$age_qty_arr[$pid][$key]=0;
			}
			else if($age_qty_arr[$pid][$key])
			{
				$age_qty_arr[$pid][$key]-=$restValue;break;
			}
			
		}
		
		
	}
	
	
	
	$table_width=($colum*50)+900;
	$colspan=9+$colum;	
	ob_start();	
	?>
    <div>
        <table width="<? echo $table_width; ?>" border="1" cellpadding="2" cellspacing="0"> 
            <thead>
                <tr class="form_caption">
                    <td colspan="<? echo $colspan; ?>" align="center" style="border:none;font-size:16px; font-weight:bold" ><? echo $report_title; ?></td> 
                </tr>
                <tr class="form_caption">
                    <td colspan="<? echo $colspan; ?>" align="center" style="border:none; font-size:14px;">
                       <b>Company Name : <? echo $companyArr[$cbo_company_name]; ?></b>                               
                    </td>
                </tr>
                <tr class="form_caption">
                    <td colspan="<? echo $colspan; ?>" align="center" style="border:none;font-size:12px; font-weight:bold">
                        <? echo "From : ".change_date_format($txt_date_from)." To : ".change_date_format($txt_date_from);?>
                    </td>
                </tr>
            </thead>
        </table>
        <table width="<? echo $table_width; ?>" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
        	<thead>
               <tr>
                    <th rowspan="2" width="40">SL</th>
                    <th rowspan="2" width="60">Prod.ID</th>
                    <th colspan="2">Description</th>
                    <th rowspan="2" width="110">Stock</th>
                    <th colspan="<? echo $colum;?>">Ageing Range</th>
                    <th rowspan="2" width="80">Avg. Rate (TK.)</th>
                    <th rowspan="2" width="100">Amount</th>
                    <th rowspan="2" width="80">Age(Days)</th>
                    <th rowspan="2">DOH</th>
               </tr> 
               <tr>                         
                    <th width="120">Item Group</th>
                    <th width="180">Item Description</th>
                    <? foreach($caption_arr as $fill_value){ ?>
                    <th width="50"><? echo $fill_value;?></th>
                    <? } ?>
               </tr> 
            </thead>
        </table>
        <div style="width:<? echo $table_width+18; ?>px; max-height:350px; overflow-y:scroll" id="scroll_body" > 
            <table width="<? echo $table_width; ?>" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_body">
            <?
              $sql="select b.id, b.item_description,b.item_group_id, b.current_stock, b.avg_rate_per_unit from product_details_master b where b.status_active=1 and b.is_deleted=0 and b.company_id=$cbo_company_name and b.item_category_id=4 $item $prod_cond order by b.id"; 	
                $result = sql_select($sql);
				$i=1; $total_amount=0;
                foreach($result as $row)
                {
                   $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF"; 
					
					$daysOnHand = datediff("d",$data_array[$row[csf("id")]]['max_date'],date("Y-m-d")); 
                    $stock=$data_array[$row[csf("id")]]['rcv_total_opening']-$data_array[$row[csf("id")]]['iss_total_opening'];
					if($stock>0){	
						$amount=$stock*$row[csf("avg_rate_per_unit")];
						$total_amount+=$amount;
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" <? echo $stylecolor; ?> onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                            <td width="40"><? echo $i; ?></td>	
                            <td width="60" align="center"><p><? echo $row[csf("id")]; ?></p></td>
                            <td width="120"><p><? echo $trim_group[$row[csf('item_group_id')]]; ?></p></td>
                            <td width="180"><p><? echo $row[csf("item_description")]; ?></p></td>  
                            <td width="110" align="right"><p><? echo number_format($stock,2); ?></p></td>
						   <? foreach($caption_arr as $key=>$fill_value){ ?>
                            <td width="50" align="right"><p>
                                <? 
                                    echo round($age_qty_arr[$row[csf("id")]][$key]);
                                    //$sub_tot_age_qty_arr[$yarn_key][$key]+=$age_qty_arr[$row[csf("id")]][$key];
									//$grand_tot_age_qty_arr[$key]+=$sub_tot_age_qty_arr[$yarn_key][$key];
									$grand_tot_age_qty_arr[$key]+=$age_qty_arr[$row[csf("id")]][$key];
                                ?>
                            </p></td>
                           <? } ?>

                            <td width="80" align="right"><? echo number_format($row[csf("avg_rate_per_unit")],2); ?></td>
                            <td width="100" align="right"><p><? echo number_format($amount,2); ?></p></td>
                            <td width="80" align="center"><? echo $data_array[$row[csf("id")]]['age_of_days']; ?></td>
                            <td align="center"><? echo $daysOnHand; ?></td>
						</tr>
						<? 
						
						$total_stock+=$stock;
						$i++;
					}
				}
				?>
            </table>
		</div> 
        <table width="<? echo $table_width; ?>" border="1" cellpadding="0" cellspacing="0" class="tbl_bottom" rules="all" > 
           <tr>
                <td width="40">&nbsp;</td>
                <td width="60">&nbsp;</td> 
                <td width="120">&nbsp;</td> 
                <td width="180" align="right">Total</td>
                <td width="110" align="right" id="total_stock_td"><? echo number_format($total_stock,2); ?></td>
				<? $s=1;foreach($caption_arr as $key=>$fill_value){ ?>
                <td width="50" align="right" id="age_<? echo $s++;?>"><p><? echo round($grand_tot_age_qty_arr[$key]);?></p></td>
                <? } ?>
                <td width="80">&nbsp;</td>
                <td width="100" align="right" id="total_amount_id"><? echo number_format($total_amount,2); ?></td>
                <td width="80">&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
        </table>
        </div>
    <?
	$html = ob_get_contents();
    ob_clean();
    foreach (glob("*.xls") as $filename) {
    @unlink($filename);
    }
    //---------end------------//
    $name=time();
    $filename=$user_id."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');	
    $is_created = fwrite($create_new_doc, $html);
    echo "$html**$filename**$s"; 
    exit();
}
?>