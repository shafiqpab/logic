<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');

$user_name=$_SESSION['logic_erp']['user_id'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="load_drop_down_floor")
{
	echo create_drop_down( "cbo_floor_id", 120, "select a.id, a.floor_name from lib_prod_floor a, lib_machine_name b where a.id=b.floor_id and b.category_id=1 and b.company_id=$data and b.status_active=1 and b.is_deleted=0 $location_cond group by a.id, a.floor_name order by a.floor_name","id,floor_name", 1, "-- Select Floor --", 0, "","" );
  exit();	 
}

if($action=="machine_no_popup")
{
	echo load_html_head_contents("PO Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$im_data=explode('_',$data);
	//print_r ($im_data);
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
				name += selected_name[i] + ',';
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
	if($im_data[1])
	{
		$floor_cond = " and b.id=$im_data[1]";
	}
	$sql = "select a.id, a.machine_no, a.brand, a.origin, a.machine_group, b.floor_name  from lib_machine_name a, lib_prod_floor b where a.floor_id=b.id and a.category_id=1 and a.company_id=$im_data[0] $floor_cond and a.status_active=1 and a.is_deleted=0 and a.is_locked=0 order by a.machine_no, b.floor_name ";
	//echo  $sql;
	
	echo create_list_view("tbl_list_search", "Machine Name,Machine Group,Floor Name", "200,110,110","450","350",0, $sql , "js_set_value", "id,machine_no", "", 1, "0,0,0", $arr , "machine_no,machine_group,floor_name", "",'setFilterGrid(\'tbl_list_search\',-1);','0,0,0','',1) ;
	
   exit(); 
}

if($action=="report_generate")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 

	$datediff=datediff('d',str_replace("'","",$txt_date_from),str_replace("'","",$txt_date_to));
	
	if(str_replace("'","",$cbo_floor_id)==0)
	{
		$floor_cond="";
		$floor_cond_knit="";
	}
	else
	{
		$floor_cond=" and floor_id=$cbo_floor_id";
		$floor_cond_knit=" and b.floor_id=$cbo_floor_id";
	}

	if(str_replace("'","",$txt_machine_id)==0)
	{
		$machine_cond="";
	}
	else
	{
		$machine_cond=" and id in (".str_replace("'","",$txt_machine_id).")";
	}
	
	$floor_arr=return_library_array( "select id, floor_name from lib_prod_floor",'id','floor_name');
	
	$machine_data_array=array();
	$machine_data=sql_select("select id, floor_id, machine_no,brand, dia_width, gauge, prod_capacity from lib_machine_name where company_id=$cbo_company_name and category_id=1 and status_active=1 and is_deleted=0 $floor_cond $machine_cond order by floor_id, dia_width");
	foreach($machine_data as $row)
	{
		$machine_data_array[$row[csf('id')]]['no']=$row[csf('machine_no')];
		$machine_data_array[$row[csf('id')]]['floor']=$row[csf('floor_id')];
		$machine_data_array[$row[csf('id')]]['dia']=$row[csf('dia_width')];
		$machine_data_array[$row[csf('id')]]['gg']=$row[csf('gauge')];
		$machine_data_array[$row[csf('id')]]['brand']=$row[csf('brand')];
		$machine_data_array[$row[csf('id')]]['capacity']=$row[csf('prod_capacity')];
	}
	
	
	//$sql = "SELECT a.booking_id, a.receive_date, b.machine_no_id, b.machine_dia,b.machine_gg, b.brand_id, b.floor_id, b.grey_receive_qnty from inv_receive_master a, pro_grey_prod_entry_dtls b where a.id=b.mst_id and company_id=$cbo_company_name $floor_cond_knit and a.entry_form =2 and b.status_active=1 and b.is_deleted=0";

	if($type==1)
	{
		$knit_sales_cond = " and c.is_sales=0";
		$sales_cond = " and is_sales=0";
	}
	else if($type==2)
	{
		$knit_sales_cond = " and c.is_sales=1";
		$sales_cond = " and is_sales=1";
	}


	$sql ="SELECT a.booking_id, a.receive_date, b.machine_no_id, b.machine_dia,b.machine_gg, b.brand_id, b.floor_id,b.grey_receive_qnty 
	from inv_receive_master a,pro_grey_prod_entry_dtls b, ppl_planning_info_entry_dtls c 
	where a.id=b.mst_id and a.booking_id=c.id and a.receive_basis=2 and company_id=$cbo_company_name $floor_cond_knit 
	and a.entry_form =2 and b.status_active=1 and b.is_deleted=0 $knit_sales_cond";

	$knit_pro_data=sql_select($sql);
	foreach($knit_pro_data as $row)
	{
		$key=$row[csf('machine_no_id')].'**'.$row[csf('floor_id')];
		$receive_date_date=date("Y-m-d",strtotime($row[csf('receive_date')]));
		$knit_data_arr[$key][$receive_date_date]+=$row[csf('grey_receive_qnty')];
	}
	
	//var_dump($knit_data_arr);
	
	$tbl_width=740+$datediff*70;
	ob_start();
	?>
	<fieldset style="width:<? echo $tbl_width+20; ?>px;">
		<table cellpadding="0" cellspacing="0" width="<? echo $tbl_width; ?>">
			<tr>
			   <td align="center" width="100%" colspan="<? echo $datediff+5; ?>" style="font-size:16px"><strong><? echo $report_title; ?></strong></td>
			</tr>
		</table>	
		<table style="margin-left:1px" cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $tbl_width; ?>" class="rpt_table" >
			<thead>
				<th width="40">SL</th>
				<th width="100">Floor No</th>
				<th width="90">Machine No</th>
				<th width="90">Machine Dia</th>
				<th width="90">Machine GG</th>
                <th width="90">Machine Brand</th>
                <th width="100"><div  style="word-wrap:break-word; width:100px">Basic Capacity<br/><i style="font-size:9px; font-weight:100">(As Per Library)</i></div></th>
				<th width="90">Particulars</th>
				<?
					$date_array=array();
					$s=1;
                    for($j=0;$j<$datediff;$j++)
					{
                        $newdate =add_date(str_replace("'","",$txt_date_from),$j);
						$date_array[$j]=$newdate;
						if($s==$datediff) $width=""; else $width="width=70";
						echo '<th '.$width.'>'.change_date_format($newdate).'</th>';
                   		$s++;
				    }  
				?>
			</thead>
		</table>
		<div style="width:<? echo $tbl_width; ?>px; overflow-y:scroll; max-height:330px;" id="buyer_list_view" align="center">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $tbl_width-18; ?>" class="rpt_table" id="tbl_list_search">
				<tbody>
				<? 
                    $i=1; $machine_date_array=array(); $tot_capacity=0; $tot_qnty_array=array();$machine_date_array2=array();
                    $dataArray=sql_select("select dtls_id, machine_id, distribution_date, fraction_date, sum(days_complete) as days_complete, sum(qnty) as qnty, 'Y' as status from ppl_entry_machine_datewise where status_active=1 $sales_cond group by machine_id, distribution_date, dtls_id, fraction_date");
					foreach ($dataArray as $row)
					{
						$distribution_date=date("Y-m-d",strtotime($row[csf('distribution_date')]));
						$machine_date_array[$row[csf('machine_id')]][$distribution_date]['st']=$row[csf('status')]; 
						$machine_date_array[$row[csf('machine_id')]][$distribution_date]['fr']=$row[csf('fraction_date')];
						$machine_date_array[$row[csf('machine_id')]][$distribution_date]['dc']=$row[csf('days_complete')]; 
						$machine_date_array[$row[csf('machine_id')]][$distribution_date]['qnty']+=$row[csf('qnty')]; 
						if($machine_date_array[$row[csf('machine_id')]][$distribution_date]['dtls_id'] == ""){
							$machine_date_array[$row[csf('machine_id')]][$distribution_date]['dtls_id']=$row[csf('dtls_id')];	
						}else{
							$machine_date_array[$row[csf('machine_id')]][$distribution_date]['dtls_id'] .= ",".$row[csf('dtls_id')];
						}
					}
					$capacity_arr=array();
					$dataArray=sql_select("select dtls_id, machine_id, capacity from ppl_planning_info_machine_dtls where status_active=1 $sales_cond");
					foreach ($dataArray as $row)
					{
						$capacity_arr[$row[csf('machine_id')]][$row[csf('dtls_id')]]=$row[csf('capacity')]; 
					}

					$prog_ids='';
                    foreach($machine_data_array as $key=>$val)
                    {
                        if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$tot_capacity+=$machine_data_array[$key]['capacity'];
						$brand=$machine_data_array[$key]['brand'];
                        ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>"> 
                            <td width="40" rowspan="3"><p><? echo $i; ?></td>
                            <td width="100" rowspan="3" style="word-wrap:break-word; width:100px"><p><? echo $floor_arr[$machine_data_array[$key]['floor']]; ?></p></td>
                            <td width="90" rowspan="3"><p><? echo $machine_data_array[$key]['no']; ?></p></td>
                            <td width="90" rowspan="3"><p><? echo $machine_data_array[$key]['dia']; ?>&nbsp;</p></td>
                            <td width="90" rowspan="3"><p><? echo $machine_data_array[$key]['gg']; ?>&nbsp;</p></td>
                            <td width="90" rowspan="3"><p><? echo $machine_data_array[$key]['brand']; ?>&nbsp;</p></td>
                            <td width="100" rowspan="3" align="right"><? echo number_format($machine_data_array[$key]['capacity'],2,'.',''); ?>&nbsp;</td>
                            <td width="90" height="30"><p>Plan Capacity</p></td>
                            <?
							$s=1; $program_ids= ""; 
							foreach($date_array as $date)
							{
								$no_of_program = 0;
								$program_ids = chop($machine_date_array[$key][$date]['dtls_id'],",");
								$capacity="";
								foreach (explode(",", $program_ids) as $program_id ) 
								{
									$capacity += $capacity_arr[$key][$program_id];
								}
								
								$no_of_program += count(explode(",", $program_ids));
								if($no_of_program >0)
								{
									$capacity = $capacity/$no_of_program;
								}

								$tot_capacity_arr[$date]+=$capacity;
								echo '<td align="right"><a href="##" style="color:#000">'.$capacity.'</a>&nbsp;</td>';
							}  
							?>
						</tr>
						<tr>
                            <td width="90" height="30"><p>Plan</p></td>
                            <?
							$s=1;
							foreach($date_array as $date)
							{
								if($s==count($date_array)) $width=""; else $width="width=70";
								$qnty=$machine_date_array[$key][$date]['qnty'];
								if($machine_date_array[$key][$date]['fr']==1)
								{
									$suffix="<br>(".number_format($machine_date_array[$key][$date]['dc'],2)." days)&nbsp;";
								}
								else 
								{
									$suffix="";
								}
								if($machine_date_array[$key][$date]['dtls_id']=='' || $machine_date_array[$key][$date]['dtls_id']==0)
								{
									if($qnty==0 || $qnty=='')
									{
									 $summary_without_prod_mc.=$key.',';
										
									}
								}
								$td_color='';

								$program_ids = chop($machine_date_array[$key][$date]['dtls_id'],",");
								$capacity = "";
								foreach (explode(",", $program_ids) as $program_id ) 
								{
									$capacity += $capacity_arr[$key][$program_id];
								}

								if($qnty>0)
								{
									if($qnty>=$capacity) 
									{
										$td_color='green';
									}
									else if($qnty<$capacity) 
									{
										$td_color='yellow';
									}
								}
								else 
								{
									$td_color='red';
								}
								$qnty=number_format($qnty,2);
								echo '<td align="right" bgcolor="'.$td_color.'" '.$width.'><a href="##" style="color:#000" onclick="openmypage(\''.$machine_date_array[$key][$date]['dtls_id'].'\','.$type.')">'.$qnty.'</a>&nbsp;'.$suffix.'</td>';
								$tot_qnty_array[$date]+=$qnty;
								$s++;
							}  
							?>
						</tr>
                        
						<tr>
                            <td width="90" height="30"><p>Actual Prod.</p></td>
                            <?
							$s=1;
							foreach($date_array as $date)
							{
								$index=$key.'**'.$machine_data_array[$key]['floor'];
								echo '<td align="right">'.number_format($knit_data_arr[$index][$date],2).'</td>';
								$tot_actual_qnty_array[$date]+=number_format($knit_data_arr[$index][$date],2);
							}
							?>
						</tr>
                        
                        <?
                        $i++;
                    }
                ?>
				</tbody>
        	</table>	
		</div>
        <table style="margin-left:1px" cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $tbl_width-18; ?>" class="rpt_table" >
            <tfoot>
                <tr>
                	<th width="40">&nbsp;</th>
                    <th width="100">&nbsp;</th>
                    <th width="90">&nbsp;</th>
                    <th width="90">&nbsp;</th>
                    <th width="90">&nbsp;</th>
                    <th align="right" width="90">Capacity Total</th>
                    <th align="right" width="100" id="value_capacity"><? echo number_format($tot_capacity,2,'.',''); ?>&nbsp;</th>
                    <th width="90">&nbsp;</th>
                    <?
					$s=1;
                    foreach($date_array as $date)
                    {
						if($s==count($date_array)) $width=""; else $width="width=70";
                        echo '<th align="right" '.$width.' id="value_qnty_'.$s.'">'.number_format($tot_capacity_arr[$date],2,'.','').'&nbsp;</th>';
						$s++;
                    }  
                    ?>
                </tr>
                <tr>
                    <th colspan="6" align="right">%</th>
                    <th align="right">&nbsp;</th>
                    <th width="90">&nbsp;</th>
                    <?
                    foreach($date_array as $date)
                    {
                        $perc=($tot_capacity_arr[$date]*100)/$tot_capacity;
                        echo '<th align="right">'.number_format($perc,2,'.','').'&nbsp;</th>';
                    }  
                    ?>
                </tr>
				<tr>
                	<th width="40">&nbsp;</th>
                    <th width="100">&nbsp;</th>
                    <th width="90">&nbsp;</th>
                    <th width="90">&nbsp;</th>
                    <th width="90">&nbsp;<? //echo $prog_ids;?></th>
                    <th align="right" width="90">Plan Total</th>
                    <th align="right" width="100" id="value_capacity2"><? echo number_format($tot_capacity,2,'.',''); ?>&nbsp;</th>
                    <th width="90">&nbsp;</th>
                    <?
					$s=1;
                    foreach($date_array as $date)
                    {
						//$tot_qnty_array[$date]
						if($s==count($date_array)) $width=""; else $width="width=70";
                        echo '<th align="right" '.$width.' id="value_qnty2_'.$s.'">'.number_format($tot_qnty_array[$date],2,'.','').'&nbsp;</th>';
						$s++;
                    }  
                    ?>
                </tr>
                <tr>
                    <th colspan="6" align="right">%</th>
                    <th align="right">&nbsp;</th>
                    <th width="90">&nbsp;</th>
                    <?
                    foreach($date_array as $date)
                    {
                        $perc=($tot_qnty_array[$date]*100)/$tot_capacity;
                        echo '<th align="right">'.number_format($perc,2,'.','').'&nbsp;</th>';
                    }  
                    ?>
                </tr>
				  
				<tr>
                	<th width="40">&nbsp;</th>
                    <th width="100">&nbsp;</th>
                    <th width="90">&nbsp;</th>
                    <th width="90">&nbsp;</th>
                    <th width="90">&nbsp;<? //echo $prog_ids;?></th>
                    <th align="right" width="90">Actual  Total</th>
                    <th align="right" width="100" id="value_capacity3"><? echo number_format($tot_capacity,2,'.',''); ?>&nbsp;</th>
                    <th width="90">&nbsp;</th>
                    <?
					$s=1;
                    foreach($date_array as $date)
                    {
						
						if($s==count($date_array)) $width=""; else $width="width=70";
                        echo '<th align="right" '.$width.' id="value_qnty3_'.$s.'">'.number_format($tot_actual_qnty_array[$date],2,'.','').'&nbsp;</th>';
						$s++;
                    }  
                    ?>
                </tr>
                <tr>
                    <th colspan="6" align="right">%</th>
                    <th align="right">&nbsp;</th>
                    <th width="90">&nbsp;</th>
                    <?
                    foreach($date_array as $date)
                    {
                        $perc=($tot_actual_qnty_array[$date]*100)/$tot_capacity;
                        echo '<th align="right">'.number_format($perc,2,'.','').'&nbsp;</th>';
                    }  
                    ?>
                </tr>
            </tfoot>
        </table>
          
        
	</fieldset> 
    <br/>
    <table style="margin-left:1px" cellspacing="0" cellpadding="0" border="1" rules="all" width="330" class="rpt_table">
          <caption><b style="float:left"> Without Program MC List :</b></caption>
          <thead>
                <th width="30">SL No</th>
                <th  width="100">M/C No</th>
                <th  width="100">MC Dia</th>
                <th width="">MC Brand</th>
            </thead>
             </table>
             
             <table style="margin-left:1px" cellspacing="0" cellpadding="0" border="1" rules="all" width="330" class="rpt_table" id="summary_tbl">
           
             <?
			 $mc_arr=array_unique(explode(",",substr($summary_without_prod_mc,0,-1)));
			$k=1;
			 foreach($mc_arr as $mc_id)
			 {
				  if($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			 ?>
             <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('trs_<? echo $k; ?>','<? echo $bgcolor; ?>')" id="trs_<? echo $k; ?>"> 
                 <td width="30"><? echo  $k;?> </td>
                 <td  width="100"><p><? echo  $machine_data_array[$mc_id]['no'];?> </p></td>
                 <td  width="100"><p><? echo  $machine_data_array[$mc_id]['dia'];?></p></td>
                 <td><p><? echo  $machine_data_array[$mc_id]['brand'];?></p> </td>
             </tr>
             
             <?
			 $k++;
			 }
			 ?>
            </tbody>
            <tfoot>
            <tr>
            <th colspan="4">&nbsp; </th>
            </tr>
            </tfoot>
            </table>     
	<?

	foreach (glob("$user_name*.xls") as $filename) 
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_name."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	$filename="requires/".$user_name."_".$name.".xls";
	echo "$total_data####$filename####".count($date_array);
	exit();
}

if($action=="plan_deails")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	
	$color_array=return_library_array( "select id, color_name from lib_color", "id", "color_name"  );
	$buyer_array=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );

	if($type==1)
	{
		$sales_cond= " and b.is_sales=0";
	}
	else if($type==2)
	{
		$sales_cond= " and b.is_sales=1";
	}

?>
	<fieldset style="width:790px; margin-left:7px">
	<?
		if($type==1)
		{
	?>
    	<b>Order Details</b>
        <table border="1" class="rpt_table" rules="all" width="770" cellpadding="0" cellspacing="0">
            <thead>
                <th width="40">SL</th>
                <th width="120">Job No</th>
                <th width="130">Buyer</th>
                <th width="140">Order No</th>
                <th width="70">File No</th>
                <th width="80">Ref. No</th>
                <th width="70">Prog. No</th>
                <th>Shipment Date</th>
            </thead>
         </table>
         <div style="width:787px; max-height:170px; overflow-y:scroll" id="scroll_body">
             <table border="1" class="rpt_table" rules="all" width="770" cellpadding="0" cellspacing="0">
                <?
				//ppl_planning_info_entry_dtls
                $i=1;
                $sql="select a.buyer_id,a.dtls_id as prog_id, b.grouping as ref_no,b.file_no,b.job_no_mst, b.po_number, b.pub_shipment_date from ppl_planning_entry_plan_dtls a, wo_po_break_down b where a.po_id=b.id and a.dtls_id in ($program_id) order by b.id, b.pub_shipment_date";
                $result=sql_select($sql);
                foreach($result as $row)
                {
                    if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
                ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                        <td width="40"><? echo $i; ?></td>
                        <td width="120"><p><? echo $row[csf('job_no_mst')]; ?></p></td>
                        <td width="130"><p><? echo $buyer_array[$row[csf('buyer_id')]]; ?></p></td>
                        <td width="140"><p><? echo $row[csf('po_number')]; ?></p></td>
                        <td width="70"><p><? echo $row[csf('file_no')]; ?></p></td>
                        <td width="80"><p><? echo $row[csf('ref_no')]; ?></p></td>
                        <td width="70"><p><? echo $row[csf('prog_id')]; ?></p></td>
                        <td align="center"><? echo change_date_format($row[csf('pub_shipment_date')]); ?></td>
                    </tr>
                <?
                $i++;
                }
                ?>
            </table>
        </div>	
        <br />
		<?
			$query="select a.fabric_desc, a.gsm_weight, b.fabric_dia, b.color_id, b.color_range, b.start_date, b.end_date, b.stitch_length from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b where a.id=b.mst_id and b.id in ($program_id) $sales_cond";
			$dataArray=sql_select($query);
			$color='';
			$color_id=explode(",",$dataArray[0][csf('color_id')]);
			foreach($color_id as $val)
			{
				if($color=='') $color=$color_array[$val]; else $color.=",".$color_array[$val];
			}
			?>
        <b>Fabric Details</b>
        <table border="1" class="rpt_table" rules="all" width="570" cellpadding="0" cellspacing="0">
            <thead>
                <th width="70">Fabric Dia</th>
                <th width="60">GSM</th>
                <th width="160">Description</th>
                <th width="60">Stitch Length</th>
                <th width="90">Color Range</th>
                <th>Fabric Color</th>
            </thead>
            <tr bgcolor="#FFFFFF">
                <td width="70"><p><? echo $dataArray[0][csf('fabric_dia')]; ?>&nbsp;</p></td>
                <td width="60"><p><? echo $dataArray[0][csf('gsm_weight')]; ?>&nbsp;</p></td>
                <td width="160"><p><? echo $dataArray[0][csf('fabric_desc')]; ?>&nbsp;</p></td>
                <td width="60"><p><? echo $dataArray[0][csf('stitch_length')]; ?>&nbsp;</p></td>
                <td width="90"><p><? echo $color_range[$dataArray[0][csf('color_range')]]; ?>&nbsp;</p></td>
                <td><p><? echo $color; ?>&nbsp;</p></td>
            </tr>
         </table>
		 <?
		}
		else
		{
			$supplier_details = return_library_array("select id, supplier_name from lib_supplier", "id", "supplier_name");
			$company_arr 	= return_library_array("select id,company_name from lib_company", "id", "company_name");
			$color_library = return_library_array("select id,color_name from lib_color", "id", "color_name");

			/* $query="SELECT a.fabric_desc, a.gsm_weight, b.fabric_dia, b.color_id, b.color_range, b.start_date, b.end_date, b.stitch_length, 
			b.machine_dia, b.knitting_source, b.knitting_party, sum(c.program_qnty) as program_qnty
			from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b, ppl_planning_entry_plan_dtls c 
			where a.id=b.mst_id and b.id=c.dtls_id and b.status_active=1 and c.status_active=1 and b.id in ($program_id) $sales_cond
			group by a.fabric_desc, a.gsm_weight, b.fabric_dia, b.color_id, b.color_range, b.start_date, b.end_date, b.stitch_length, 
			b.machine_dia, b.knitting_source, b.knitting_party"; */

			$query="SELECT a.fabric_desc, a.gsm_weight, b.fabric_dia, b.color_id, b.color_range, b.start_date, b.end_date, b.stitch_length, 
			b.machine_dia, b.machine_gg, b.knitting_source, b.knitting_party, b.id as program_no, d.job_no as sales_order, sum(c.program_qnty) as program_qnty
			from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b, ppl_planning_entry_plan_dtls c, fabric_sales_order_mst d 
			where a.id=b.mst_id and b.id=c.dtls_id and c.po_id=d.id and b.status_active=1 and c.status_active=1 and b.id in ($program_id) $sales_cond
			group by a.fabric_desc, a.gsm_weight, b.fabric_dia, b.color_id, b.color_range, b.start_date, b.end_date, b.stitch_length, 
			b.machine_dia, b.machine_gg, b.knitting_source, b.knitting_party, b.id, d.job_no";
	
			$dataArray=sql_select($query);
			?>

					 				


			<b>Fabric Details</b>
			<table border="1" class="rpt_table" rules="all" width="930" cellpadding="0" cellspacing="0">
				<thead>
					<th width="90">Knitting Source</th>
					<th width="120">Knitting Company</th>
					<th width="90">Sales Order NO</th>
					<th width="90">Program No</th>
					<th width="90">Color</th>
					<th width="90">Color Range</th>
					<th width="90">Machine Dia</th>
					<th width="90">Machine GG</th>
					<th width="90">Program Qnty</th>
					<th width="90">Stitch Length</th>
				</thead>
				<?
					foreach ($dataArray as  $row) 
					{

						$knitting_factory='';
						if ($row[csf('knitting_source')] == 1)
							$knitting_factory = $company_arr[$row[csf('knitting_party')]] ;
						else if ($row[csf('knitting_source')] == 3)
							$knitting_factory = $supplier_details[$row[csf('knitting_party')]] ;


						$color_name="";
						$ex_color_id=array_unique(explode(",",$row[csf('color_id')]));
						foreach($ex_color_id as $color_id)
						{
							if($color_name=='')
								$color_name=$color_library[$color_id];
							else
								$color_name.=', '.$color_library[$color_id];
						}
						
					?>
					<tr bgcolor="#FFFFFF">
						<td width="90"><p><? echo $knitting_source[$row[csf('knitting_source')]]; ?>&nbsp;</p></td>
						<td width="120"><p><? echo $knitting_factory; ?>&nbsp;</p></td>
						<td width="90"><p><? echo $row[csf('sales_order')]; ?>&nbsp;</p></td>
						<td width="90"><p><? echo $row[csf('program_no')]; ?>&nbsp;</p></td>
						<td width="90"><p><? echo $color_name; ?>&nbsp;</p></td>
						<td width="90"><p><? echo $color_range[$row[csf('color_range')]]; ?>&nbsp;</p></td>
						<td width="90"><p><? echo $row[csf('machine_dia')]; ?>&nbsp;</p></td>
						<td width="90"><p><? echo $row[csf('machine_gg')]; ?>&nbsp;</p></td>
						<td width="90"><p><? echo $row[csf('program_qnty')]; ?>&nbsp;</p></td>
						<td width="90"><p><? echo $row[csf('stitch_length')]; ?>&nbsp;</p></td>
					</tr>
					<?
					}
				?>
			</table>

			<?
		}
		 ?>
         <br />
         <b>TNA Details</b>
         <table border="1" class="rpt_table" rules="all" width="350" cellpadding="0" cellspacing="0">
            <thead>
                <th width="170">Kniting Start Date</th>
                <th>Kniting End Date</th>
            </thead>
            <tr bgcolor="#FFFFFF">
                <td align="center"><p><? if($dataArray[0][csf('start_date')]!="0000-00-00") echo change_date_format($dataArray[0][csf('start_date')]); ?>&nbsp;</p></td>
                <td align="center"><p><? if($dataArray[0][csf('end_date')]!="0000-00-00") echo change_date_format($dataArray[0][csf('end_date')]); ?>&nbsp;</p></td>
            </tr>
         </table>
	</fieldset>   
<?
exit();
}
?>