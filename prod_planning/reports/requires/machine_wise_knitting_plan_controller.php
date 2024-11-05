<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');

$user_name=$_SESSION['logic_erp']['user_id'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$permission=$_SESSION['page_permission'];

if ($action=="overlapped_popup")
{
	echo load_html_head_contents("Overlapped Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
?> 
	<script>
	
		function fnc_close()
		{
			var plan_ids='';
			var row_num=$('#tbl_list_search tbody tr').length;
			for(var j=1; j<=row_num; j++)
			{
				var plan_id=$('#planId_'+j).text();
				if($('#check_'+j).is(':checked'))
				{
					if(plan_ids=="")
					{
						plan_ids=plan_id;
					}
					else
					{
						plan_ids+=","+plan_id;
					}
				}
			}
			
			$('#hidden_plan_ids').val( plan_ids );
			parent.emailwindow.hide();
		}
	
    </script>

</head>

<body>
<div align="center" style="width:350px;">
	<form name="searchwofrm"  id="searchwofrm" autocomplete=off>
		<fieldset style="width:100%;">
		<legend>Overlapped Data</legend>           
            <table cellpadding="0" cellspacing="0" width="340" class="rpt_table" border="1" rules="all" id="tbl_list_search">
                <thead>
                	<th><input type="hidden" name="hidden_plan_ids" id="hidden_plan_ids" class="text_boxes" value=""></th>
                    <th>Machine Plan Id</th>
                    <th>Plan Qty</th>
                </thead>
                <tbody>
					<?
                    $overlapped_datas=explode(",",$overlapped_data);
                    $i=0;
                    foreach($overlapped_datas as $datas)
                    {
                        $i++;
                        if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                        $data=explode("_",$datas);
                        echo '<tr bgcolor="'.$bgcolor.'"><td align="center" valign="middle"><input type="checkbox" name="check[]" id="check_'.$i.'"></td>';
                        echo '<td id="planId_'.$i.'">'.$data[0].'</td>';
                        echo '<td align="right">'.$data[2].'</td>';
                        echo '</tr>';
                    }
                    ?>
                </tbody>
           </table>
           <table width="340" cellspacing="0" cellpadding="0" style="border:none" align="center">
                <tr>
                    <td align="center" valign="bottom">
                        <input type="button" name="close" onClick="fnc_close()" class="formbutton" value="Close" style="width:100px" />
                    </td>
                </tr>
            </table>
		</fieldset>
	</form>
</div>
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if($action=="load_drop_down_buyer")
{
	$data=explode("_",$data);
	if($data[1]==1)
	{
		echo create_drop_down( "cbo_buyer_name", 140, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name",1, "-- Select Buyer --", "0", "",0 );
	}
	else if($data[1]==2)
	{
		echo create_drop_down( "cbo_buyer_name", 140, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='".$data[0]."' and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond group by buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" ,0); 
	}
	else 
	{
		echo create_drop_down( "cbo_buyer_name", 140, $blank_array,"",1, "-- Select Buyer --", 0, "" );
	}
	
	exit();
}

if ($action=="load_drop_down_floor")
{
	echo create_drop_down( "cbo_floor_id", 160, "select a.id, a.floor_name from lib_prod_floor a, lib_machine_name b where a.id=b.floor_id and b.category_id=1 and b.company_id=$data and b.status_active=1 and b.is_deleted=0 $location_cond group by a.id, a.floor_name order by a.floor_name","id,floor_name", 1, "-- Select Floor --", 0, "","" );
  exit();	 
}

if($action=="report_generate")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 

	$datediff=datediff('d',str_replace("'","",$txt_date_from),str_replace("'","",$txt_date_to));
	
	if(str_replace("'","",$cbo_floor_id)==0) $floor_cond=""; else $floor_cond=" and floor_id=$cbo_floor_id";
	
	$floor_arr=return_library_array( "select id, floor_name from lib_prod_floor",'id','floor_name');
	
	$machine_data_array=array();
	$machine_data=sql_select("select id, floor_id, machine_no, dia_width, gauge, prod_capacity from lib_machine_name where company_id=$cbo_company_name and category_id=1 and status_active=1 and is_deleted=0 $floor_cond order by floor_id, dia_width");//, cast(machine_no as unsigned)
	foreach($machine_data as $row)
	{
		$machine_data_array[$row[csf('id')]]['no']=$row[csf('machine_no')];
		$machine_data_array[$row[csf('id')]]['floor']=$row[csf('floor_id')];
		$machine_data_array[$row[csf('id')]]['dia']=$row[csf('dia_width')];
		$machine_data_array[$row[csf('id')]]['gg']=$row[csf('gauge')];
		$machine_data_array[$row[csf('id')]]['capacity']=$row[csf('prod_capacity')];
	}
	
	$tbl_width=410+$datediff*60;
	
	$date_array=array(); $months_array=array(); $months_width_array=array(); $header_tr=''; $s=1;
	for($j=0;$j<$datediff;$j++)
	{
		$newdate =add_date(str_replace("'","",$txt_date_from),$j);
		$month=date("M Y",strtotime($newdate));
		$dayname=substr(date("D", strtotime($newdate)),0,1);
		$date_array[$j]=$newdate;
		if($s==$datediff) $width=""; else $width="width=60";
		$header_tr.='<td '.$width.' class="top_headerss">'.date("d",strtotime($newdate)).'<br>'.$dayname.'</td>';
		
		$months_array[$month]+=1;
		$months_width_array[$month]+=60;
		$s++;
	}  
	//print_r($months_array);
	ob_start();
	?>
	<fieldset style="width:<? echo $tbl_width+20; ?>px;">
		<table cellpadding="0" cellspacing="0" width="<? echo $tbl_width; ?>">
			<tr>
			   <td align="center" width="100%" colspan="<? echo $datediff+5; ?>" style="font-size:16px"><strong><? echo $report_title; ?></strong></td>
			</tr>
		</table>	
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $tbl_width; ?>"  >
			<thead>
            	<tr>
                    <th rowspan="2" width="40" class="top_header">SL</th>
                    <th rowspan="2" width="100" class="top_header">Floor No</th>
                    <th rowspan="2" width="70" class="top_header">Machine Dia</th>
                    <th rowspan="2" width="70" class="top_header">Machine GG</th>
                    <th rowspan="2" width="70" class="top_header">Machine No</th>
                    <?
                    foreach($months_array as $month=>$days)
                    {
                        echo '<th colspan="'.$days.'" width="'.$months_width_array[$month].'" class="top_header">'.$month.'</th>';
                    }
                    ?>
                </tr>
                <tr>
					<? echo $header_tr; ?>
                </tr>
			</thead>
				<tbody>
				<? 
                    $i=1; $machine_date_array=array(); $tot_capacity=0; $tot_qnty_array=array();
                    $dataArray=sql_select("select a.capacity, a.no_of_days, a.start_date, a.distribution_qnty, a.end_date, b.dtls_id, b.machine_id, b.distribution_date, b.fraction_date, sum(b.days_complete) as days_complete, sum(b.qnty) as qnty, 'Y' as status, b.machine_plan_id from ppl_planning_info_machine_dtls a, ppl_entry_machine_datewise b where a.id=b.machine_plan_id and a.is_sales=1 and b.is_sales=1 group by b.machine_id, b.distribution_date, b.dtls_id, b.fraction_date, b.machine_plan_id, a.no_of_days, a.start_date, a.end_date, a.capacity, a.distribution_qnty");
					foreach ($dataArray as $row)
					{
						$distribution_date=date("Y-m-d",strtotime($row[csf('distribution_date')]));
						$machine_date_array[$row[csf('machine_id')]][$distribution_date]['st']=$row[csf('status')]; 
						$machine_date_array[$row[csf('machine_id')]][$distribution_date]['fr']=$row[csf('fraction_date')];
						$machine_date_array[$row[csf('machine_id')]][$distribution_date]['dc']=$row[csf('days_complete')]; 
						$machine_date_array[$row[csf('machine_id')]][$distribution_date]['qnty']=$row[csf('qnty')]; 
						$machine_date_array[$row[csf('machine_id')]][$distribution_date]['dtls_id']=$row[csf('dtls_id')];
						$machine_date_array[$row[csf('machine_id')]][$distribution_date]['machine_plan_id']=$row[csf('machine_plan_id')];
						$machine_date_array[$row[csf('machine_id')]][$distribution_date]['machine_plan_ids'].=$row[csf('machine_plan_id')].",";
						$machine_date_array[$row[csf('machine_id')]][$distribution_date]['overlapped_data'].=$row[csf('machine_plan_id')]."_".$row[csf('dtls_id')]."_".$row[csf('qnty')]."_".$row[csf('no_of_days')]."_".$row[csf('start_date')]."_".$row[csf('end_date')]."_".$row[csf('capacity')].",";
						$machine_date_array[$row[csf('machine_id')]][$distribution_date]['duration']=$row[csf('no_of_days')];
						$machine_date_array[$row[csf('machine_id')]][$distribution_date]['start_date']=$row[csf('start_date')];
						$machine_date_array[$row[csf('machine_id')]][$distribution_date]['end_date']=$row[csf('end_date')];
						$machine_date_array[$row[csf('machine_id')]][$distribution_date]['capacity']=$row[csf('capacity')];
						$machine_date_array[$row[csf('machine_id')]][$distribution_date]['plan_qty']=$row[csf('distribution_qnty')];
					}
					
					/*$capacity_arr=array();
					$dataArray=sql_select("select dtls_id, machine_id, capacity from ppl_planning_info_machine_dtls");
					foreach ($dataArray as $row)
					{
						$capacity_arr[$row[csf('machine_id')]][$row[csf('dtls_id')]]=$row[csf('capacity')]; 
					}*/
					//var_dump($machine_date_array);
                    foreach($machine_data_array as $key=>$val)
                    {
                        if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$tot_capacity+=$machine_data_array[$key]['capacity'];
						
                        ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>"> 
                            <td class="left_td_header"><p><? echo $i; ?></td>
                            <td class="left_td_header"><p><? echo $floor_arr[$machine_data_array[$key]['floor']]; ?>&nbsp;</p></td>
                            <td class="left_td_header"><p><? echo $machine_data_array[$key]['dia']; ?>&nbsp;</p></td>
                            <td class="left_td_header"><p><? echo $machine_data_array[$key]['gg']; ?>&nbsp;</p></td>
                            <td class="left_header"><p><? echo $machine_data_array[$key]['no']; ?>&nbsp;</p></td>
                            <?
							$s=1; $prev_plan_id='';
							foreach($date_array as $date)
							{
								if($s==count($date_array)) $width=""; else $width="width=90";
								
								$class="verticalStripes1"; $td_color=''; $suffix=""; $is_planned=0; $is_partial=0; $style=''; $radious=''; $is_overlapped=0; $overlapped_data=0; 
								
								$qnty=$machine_date_array[$key][$date]['qnty'];
								$dtls_id=$machine_date_array[$key][$date]['dtls_id'];
								$machine_plan_id=$machine_date_array[$key][$date]['machine_plan_id'];
								$start_date=$machine_date_array[$key][$date]['start_date'];
								$end_date=$machine_date_array[$key][$date]['end_date'];
								$duration=$machine_date_array[$key][$date]['duration'];
								$plan_qty=$machine_date_array[$key][$date]['plan_qty'];
								
								$planDtlsData=$key."**".$date."**".$dtls_id."**".$machine_plan_id;
								if($qnty>0)
								{
									$is_planned=1; 
									$capacity=$machine_date_array[$key][$date]['capacity'];
									
									$machine_plan_ids=array_unique(explode(",",chop($machine_date_array[$key][$date]['machine_plan_ids'],',')));
									if(count($machine_plan_ids)>1)
									{
										$overlapped_data=chop($machine_date_array[$key][$date]['overlapped_data'],',');
										$is_overlapped=1; 
										$class="verticalStripes1 verticalStripes1_crossed";
										if($prev_plan_id!=$machine_plan_id) 
										{
											$style='style="border-radius:50% 0 0 50%; background:repeating-linear-gradient(45deg, #606dbc, #606dbc 2px, #465298 2px, #465298 4px)"';
											$prev_plan_id=$machine_plan_id;
										}
										else
										{
											$style='style="background:repeating-linear-gradient(45deg, #606dbc, #606dbc 2px, #465298 2px, #465298 4px)"';
										}
									}
									else
									{
										$class="verticalStripes1 verticalStripes1_plan";
										if($prev_plan_id!=$machine_plan_id) 
										{
											$style='style="border-radius:50% 0 0 50%;"';
											$prev_plan_id=$machine_plan_id;
										}
										else
										{
											$style='';
										}
									}
									
									if($machine_date_array[$key][$date]['fr']==1) { $is_partial=1; }
								}
								else
								{
									$capacity=$machine_data_array[$key]['capacity'];	
								}
								
								//if($s==$datediff) $width=""; else $width="width=60"; '.$width.'
							    //echo '<td align="right" program_id="id" bgcolor="'.$td_color.'" '.$width.' class="'.$class.'"><a href="##" style="color:#000" onclick="openmypage('.$machine_date_array[$key][$date]['dtls_id'].')">'.$qnty.'</a>&nbsp;'.$suffix.'</td>';
								
								$placeholder='<span style="font-size:8px; color:#333;">'.date("d-m",strtotime($date))."<br>".$machine_data_array[$key]['no'].'</span>';
								$tdate=date("dmY",strtotime($date)); $idd="-".$key."-".$tdate;
								$start_td_id="tdbody-".$key."-".date("dmY",strtotime($start_date));
								
								echo '<td id="tdbody'.$idd.'" name="tdbody'.$i.'_'.$s.'" align="center" onMouseOver="showmenu(this.id)" plan_group="'.$is_planned.'" planDtls="'.$planDtlsData.'" duration="'.$duration.'" dtls_id="'.$dtls_id.'" plan_id="'.$machine_plan_id.'" start_date="'.change_date_format($start_date).'" end_date="'.change_date_format($end_date).'" is_partial="'.$is_partial.'" today_plan_qnty="'.$qnty.'" capacity="'.$capacity.'" plan_qnty="'.$plan_qty.'" isnew="0" upd_id="'.$machine_plan_id.'" is_overlapped="'.$is_overlapped.'" overlapped_data="'.$overlapped_data.'" start_td_id="'.$start_td_id.'" class="'.$class.'" '.$style.'>'.$placeholder.'</td>';
								
								$tot_qnty_array[$date]+=$qnty;
								
								$s++;
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
	</fieldset>      
<?
	exit();
}

if($action=="plan_deails")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	
	$color_array=return_library_array( "select id, color_name from lib_color", "id", "color_name"  );
	$buyer_array=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );

?>
	<fieldset style="width:570px; margin-left:7px">
    	<b>Order Details</b>
        <table border="1" class="rpt_table" rules="all" width="550" cellpadding="0" cellspacing="0">
            <thead>
                <th width="40">SL</th>
                <th width="120">Job No</th>
                <th width="130">Buyer</th>
                <th width="140">Order No</th>
                <th>Shipment Date</th>
            </thead>
         </table>
         <div style="width:567px; max-height:170px; overflow-y:scroll" id="scroll_body">
             <table border="1" class="rpt_table" rules="all" width="550" cellpadding="0" cellspacing="0">
                <?
                $i=1;
                $sql="select a.buyer_id, b.job_no_mst, b.po_number, b.pub_shipment_date from ppl_planning_entry_plan_dtls a, wo_po_break_down b where a.po_id=b.id and a.dtls_id=$program_id order by b.id, b.pub_shipment_date";
                $result=sql_select($sql);
                foreach($result as $row)
                {
                    if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
                ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                        <td width="40"><? echo $i; ?></td>
                        <td width="120"><p><? echo $row[csf('job_no_mst')]; ?></p></td>
                        <td width="130"><p><? echo $buyer_array[$row[csf('buyer_id')]]; ?></p></td>
                        <td width="140"><p><? echo $row[csf('po_number')]; ?></p></td>
                        <td align="center"><? echo change_date_format($row[csf('pub_shipment_date')]); ?></td>
                    </tr>
                <?
                $i++;
                }
                ?>
            </table>
        </div>	
        <br />
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
             <?
			 	$query="select a.fabric_desc, a.gsm_weight, b.fabric_dia, b.color_id, b.color_range, b.start_date, b.end_date, b.stitch_length from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b where a.id=b.mst_id and b.id=$program_id";
                $dataArray=sql_select($query);
				$color='';
				$color_id=explode(",",$dataArray[0][csf('color_id')]);
				foreach($color_id as $val)
				{
					if($color=='') $color=$color_array[$val]; else $color.=",".$color_array[$val];
				}
			?>
            <tr bgcolor="#FFFFFF">
                <td width="70"><p><? echo $dataArray[0][csf('fabric_dia')]; ?>&nbsp;</p></td>
                <td width="60"><p><? echo $dataArray[0][csf('gsm_weight')]; ?>&nbsp;</p></td>
                <td width="160"><p><? echo $dataArray[0][csf('fabric_desc')]; ?>&nbsp;</p></td>
                <td width="60"><p><? echo $dataArray[0][csf('stitch_length')]; ?>&nbsp;</p></td>
                <td width="90"><p><? echo $color_range[$dataArray[0][csf('color_range')]]; ?>&nbsp;</p></td>
                <td><p><? echo $color; ?>&nbsp;</p></td>
            </tr>
         </table>
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

if($action=="booking_item_details_popup")
{
	echo load_html_head_contents("Planning Info Entry", "../../../", 1, 1,'','','');
	extract($_REQUEST);
?>
<script>

	//if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php"; 
	var permission='<? echo $permission; ?>';

	function show_details(type)
	{
		if(form_validation('cbo_company_name','Company')==false)
		{
			return;
		}
		
		if(type==2)
		{
			if(form_validation('txt_booking_no','Booking No.')==false)
			{
				return;
			}
		}
		
		var data="action=booking_item_details"+get_submitted_data_string('cbo_company_name*cbo_within_group*cbo_buyer_name*hide_job_id*txt_booking_no*cbo_planning_status',"../../../")+'&type='+type;
		
		freeze_window(5);
		http.open("POST","machine_wise_knitting_plan_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_show_details_reponse;
		//show_list_view(cbo_company_id, 'booking_item_details', 'list_container_fabric_desc', 'requires/machine_wise_knitting_plan_controller', '');
	}

	function fn_show_details_reponse()
	{
		if(http.readyState == 4) 
		{
			var response=trim(http.responseText);
			$('#list_container_fabric_desc').html(response);
			set_all_onclick();
			show_msg('18');
			release_freezing();
		}
	}
		
	function openmypage_job()
	{
		if(form_validation('cbo_company_name','Company Name')==false)
		{
			return;
		}
		
		var companyID = $("#cbo_company_name").val();
		var buyerID = $("#cbo_buyer_name").val();
		var page_link='machine_wise_knitting_plan_controller.php?action=style_ref_search_popup&companyID='+companyID+'&buyerID='+buyerID;
		var title='Style Ref./ Job No. Search';
		
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=630px,height=370px,center=1,resize=1,scrolling=0','../../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var job_no=this.contentDoc.getElementById("hide_job_no").value;
			var job_id=this.contentDoc.getElementById("hide_job_id").value;
			
			$('#txt_job_no').val(job_no);
			$('#hide_job_id').val(job_id);	 
		}
	}
	
	function openmypage_booking()
	{
		if(form_validation('cbo_company_name','Company Name')==false)
		{
			return;
		}
		
		var companyID = $("#cbo_company_name").val();
		var page_link='machine_wise_knitting_plan_controller.php?action=booking_no_search_popup&companyID='+companyID;
		var title='Booking Search';
		
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=770px,height=370px,center=1,resize=1,scrolling=0','../../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var booking_no=this.contentDoc.getElementById("hidden_booking_no").value;
			
			$('#txt_booking_no').val(booking_no);
		}
	}
	
	function openmypage_prog()
	{
		var type=$('#txt_type').val();
		if(type==2)
		{
			alert("Not Allow");	
			return;
		}
		var tot_row=$('#tbl_list_search tbody tr').length;
		var data=''; var i=0; var selected_row=0; var currentRowColor=''; var booking_no=''; var body_part_id=''; var fabric_typee=''; var buyer_id=''; var job_id=''; var dia='';
		var gsm=''; var desc=''; var booking_qnty=0; var plan_id=''; var determination_id=''; var job_dtls_id=''; var within_group=''; var color_type_id='';
		
		var companyID=$('#company_id').val();
		
		for(var j=1; j<=tot_row; j++)
		{
			currentRowColor=document.getElementById('tr_' + j ).style.backgroundColor;
			if(currentRowColor=='yellow')
			{
				i++;
				selected_row++;
				
				if(data=='')
				{
					data=$('#bookingNo_'+j).val()+"**"
					+$('#job_id_'+j).val()+"**"
					+$('#withinGroup_'+j).val()+"**"
					+$('#job_dtls_id_'+j).val()+"**"
					+$('#buyer_id_'+j).val()+"**"
					+$('#body_part_id_'+j).val()+"**"
					+$('#fabric_typee_'+j).val()+"**"
					+$('#desc_'+j).text()+"**"
					+$('#gsm_weight_'+j).text()+"**"
					+$('#dia_width_'+j).text()+"**"
					+$('#determination_id_'+j).val()+"**"
					+$('#booking_qnty_'+j).text()+"**"
					+$('#color_type_id_'+j).val();
				}
				else
				{
					data+="_"+$('#bookingNo_'+j).val()+"**"
					+$('#job_id_'+j).val()+"**"
					+$('#withinGroup_'+j).val()+"**"
					+$('#job_dtls_id_'+j).val()+"**"
					+$('#buyer_id_'+j).val()+"**"
					+$('#body_part_id_'+j).val()+"**"
					+$('#fabric_typee_'+j).val()+"**"
					+$('#desc_'+j).text()+"**"
					+$('#gsm_weight_'+j).text()+"**"
					+$('#dia_width_'+j).text()+"**"
					+$('#determination_id_'+j).val()+"**"
					+$('#booking_qnty_'+j).text()+"**"
					+$('#color_type_id_'+j).val();
				}
				
				booking_no=$('#bookingNo_'+j).val();
				gsm=$('#gsm_weight_'+j).text();
				dia=$('#dia_width_'+j).text();
				desc=$('#desc_'+j).text();
				within_group=$('#withinGroup_'+j).val();
				buyer_id=$('#buyer_id_'+j).val();
				job_id=$('#job_id_'+j).val();
				determination_id=$('#determination_id_'+j).val();
				body_part_id=$('#body_part_id_'+j).val();
				color_type_id=$('#color_type_id_'+j).val();
				fabric_typee=$('#fabric_typee_'+j).val();

				if(plan_id=='')	plan_id=$('#plan_id_'+j).text();
				
				if(job_dtls_id=='') job_dtls_id=$('#job_dtls_id_'+j).val(); else job_dtls_id+=","+$('#job_dtls_id_'+j).val();
				
				booking_qnty=booking_qnty*1+$('#booking_qnty_'+j).text()*1;
			}
		}
		
		if(selected_row<1)
		{
			alert("Please Select At Least One Item");
			return;
		}
		
		var page_link='machine_wise_knitting_plan_controller.php?action=prog_qnty_popup&gsm='+gsm+'&dia='+dia+'&desc='+desc+'&within_group='+within_group+'&job_id='+job_id+'&booking_qnty='+booking_qnty+'&companyID='+companyID+'&data="'+data+'"'+'&plan_id='+plan_id+'&determination_id='+determination_id+'&booking_no='+booking_no+'&body_part_id='+body_part_id+'&fabric_type='+fabric_typee+'&buyer_id='+buyer_id+'&job_dtls_id='+job_dtls_id+'&color_type_id='+color_type_id;
		var title='Program Qnty Info';
		
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=940px,height=430px,center=1,resize=1,scrolling=0','../../');
		/*emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var program_qnty=this.contentDoc.getElementById("txt_program_qnty").value;
			
		}*/
	}
	
	function selected_row(rowNo)
	{
		var color=document.getElementById('tr_' + rowNo ).style.backgroundColor;
		var bookingNo=$('#bookingNo_'+rowNo).val();
		var determinationId=$('#determination_id_'+rowNo).val();
		var widthDiaType=$('#fabric_typee_'+rowNo).val();
		var gsm=$('#gsm_weight_'+rowNo).text();
		var fabricDia=$('#dia_width_'+rowNo).text();
		var plan_id=$('#plan_id_'+rowNo).text();
		var color_type_id=$('#color_type_id_'+rowNo).val();
		var job_id=$('#job_id_'+rowNo).val();
		
		var stripe_or_not='';
		
		if(color_type_id==2 || color_type_id==3 || color_type_id==4)
		{
			stripe_or_not=1;//1 means stripe yes
		}
		else
		{
			stripe_or_not=0;//0 means stripe no
		}
		
		var currentRowColor=''; var check='';
		if(color!='yellow')
		{
			var tot_row=$('#tbl_list_search tbody tr').length;
			for(var i=1; i<=tot_row; i++)
			{ 
				if(i!=rowNo)
				{
					currentRowColor=document.getElementById('tr_' + i ).style.backgroundColor;
					if(currentRowColor=='yellow')
					{
						var bookingNoCur=$('#bookingNo_'+i).val();
						var determinationIdCur=$('#determination_id_'+i).val();
						var widthDiaTypeCur=$('#fabric_typee_'+i).val();
						var gsmCur=$('#gsm_weight_'+i).text();
						var fabricDiaCur=$('#dia_width_'+i).text();
						var plan_idCur=$('#plan_id_'+i).text();
						var color_type_idCur=$('#color_type_id_'+i).val();
						var job_idCur=$('#job_id_'+i).val();
						
						var stripe_or_notCur='';
						if(color_type_idCur==2 || color_type_idCur==3 || color_type_idCur==4)
						{
							stripe_or_notCur=1;//1 means stripe yes
						}
						else
						{
							stripe_or_notCur=0;//0 means stripe no
						}
						
						if(plan_id=="" || plan_idCur=="")
						{
							if(!(bookingNo==bookingNoCur && determinationId==determinationIdCur && widthDiaType==widthDiaTypeCur && gsm==gsmCur && fabricDia==fabricDiaCur && stripe_or_not==stripe_or_notCur && job_id==job_idCur))
							{
								alert("Please Select Same Description");
								return;
							}
						}
						else
						{
							if(!(plan_id==plan_idCur && bookingNo==bookingNoCur && determinationId==determinationIdCur && widthDiaType==widthDiaTypeCur && gsm==gsmCur && fabricDia==fabricDiaCur && stripe_or_not==stripe_or_notCur && job_id==job_idCur))
							{
								alert("Please Select Same Description and Same Plan ID");
								return;
							}
						}
					}
				}
			}

			$('#tr_' + rowNo).css('background-color','yellow');
		}
		else
		{
			var reqsn_found_or_not=$('#reqsn_found_or_not_'+rowNo).val();
			if(reqsn_found_or_not==0)
			{
				$('#tr_' + rowNo).css('background-color','#FFFFCC');
			}
			else
			{
				alert("Requisition Found Against This Planning. So Change Not Allowed");
				return;
			}
		}
	}
	
	function delete_prog()
	{ 
		var program_ids = ""; var total_tr=$('#tbl_list_search tr').length;
		for(i=1; i<total_tr; i++)
		{
			try 
			{
				if ($('#tbl_'+i).is(":checked"))
				{
					program_id = $('#promram_id_'+i).val();
					if(program_ids=="") program_ids= program_id; else program_ids +=','+program_id;
				}
			}
			catch(e) 
			{
				//got error no operation
			}
		}
		
		if(program_ids=="")
		{
			alert("Please Select At Least One Program");
			return;
		}
		
		var data="action=delete_program&operation="+operation+'&program_ids='+program_ids+get_submitted_data_string('cbo_company_name',"../");
	
		freeze_window(operation);
		
		http.open("POST","requires/machine_wise_knitting_plan_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange=fnc_delete_prog_Reply_info;
		//alert(program_ids);
	}
	
	function fnc_delete_prog_Reply_info()
	{
		if(http.readyState == 4) 
		{ 
			var reponse=trim(http.responseText).split('**');	

			show_msg(trim(reponse[0]));
			
			if(reponse[0]==2)
			{
				fnc_remove_tr();
			}
			
			release_freezing();	
		}
	}
	
	function fnc_remove_tr()
	{
		var tot_row=$('#tbl_list_search tr').length;
		for(var i=1;i<=tot_row;i++)
		{
			try 
			{
				if($('#tbl_'+i).is(':checked'))
				{
					$('#tr_'+i).remove();
				}
			}
			catch(e) 
			{
				//got error no operation
			}
		}
	}
	
	function fnc_update(i)
	{
		var prog_qty=$('#prog_qty_'+i).val();
		var program_id=$('#promram_id_'+i).val();
		var data="action=update_program&operation="+operation+'&program_id='+program_id+'&prog_qty='+prog_qty;
		freeze_window(operation);
		http.open("POST","requires/machine_wise_knitting_plan_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange=fnc_update_prog_Reply_info;
	}
	
	function fnc_update_prog_Reply_info()
	{
		if(http.readyState == 4) 
		{ 
			var response=trim(http.responseText);	
			if(response==20)
			{
				alert("Program Qty Cannot Be Less Than Knitting Qty.");
				release_freezing();	
				return;
			}
			show_msg(response);
			release_freezing();	
		}
	}
	
	function active_inactive()
	{
		reset_form('','','txt_job_no*hide_job_id*txt_booking_no','','','');
		
		var within_group= $('#cbo_within_group').val();
		var company_id=document.getElementById('cbo_company_name').value;

		if(within_group==1)
		{
			$('#txt_booking_no').attr('onDblClick','openmypage_booking();');	
			$('#txt_booking_no').attr('placeholder','Browse Or Write');
			$('#txt_booking_no').removeAttr('disabled','disabled');
		}
		else
		{
			$('#txt_booking_no').removeAttr('onDblClick','onDblClick');
			$('#txt_booking_no').attr('placeholder','');
			$('#txt_booking_no').attr('disabled','disabled');
		}
		
		if(company_id==0)
		{
			$("#cbo_buyer_name option[value!='0']").remove();
		}
		else
		{
			load_drop_down('machine_wise_knitting_plan_controller',company_id+'_'+within_group, 'load_drop_down_buyer', 'buyer_td' );
		}
	}
	
	function fnc_close()
	{
		var data='';
		$('#selected_data').val(data);
		parent.emailwindow.hide();
	}
	
</script>
</head>

<body onLoad="set_hotkey();">
	<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../../../",$permission,1); ?>
		 <form name="palnningEntry_1" id="palnningEntry_1"> 
         <h3 style="width:1110px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3> 
         <div id="content_search_panel">      
             <fieldset style="width:1110px;">
                 <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                        <thead>
                            <th class="must_entry_caption">Company Name</th>
                            <th>Within Group</th>
                            <th>Buyer Name</th>
                            <th>Job No.</th>
                            <th>Sales/Booking No.</th>
                            <th>Planning Status</th>
                            <th><input type="reset" name="res" id="res" value="Reset" onClick="reset_form('palnningEntry_1','list_container_fabric_desc','','','')" class="formbutton" style="width:100px" /></th>
                        </thead>
                        <tbody>
                            <tr class="general">
                                <td> 
                                    <?
                                        echo create_drop_down( "cbo_company_name", 140, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $company_id, "active_inactive();",1 );
                                    ?>
                                </td>
                                <td>
									<?php echo create_drop_down( "cbo_within_group", 110, $yes_no,"", 0, "-- Select --", 0, "active_inactive();" ); ?>
                                </td>
                                <td id="buyer_td">
                                    <? 
                                       // echo create_drop_down( "cbo_buyer_name", 140, $blank_array,"", 1, "-- Select Buyer --", $selected, "",0,"" );
										echo create_drop_down( "cbo_buyer_name", 140, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name",1, "-- Select Buyer --", "0", "",0 );
                                    ?>
                                </td>
                                <td>
                                    <input type="text" name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:130px" placeholder="Browse" onDblClick="openmypage_job();" autocomplete="off" readonly>
                                    <input type="hidden" name="hide_job_id" id="hide_job_id" readonly>
                                </td>
                                <td>
                                    <input type="text" name="txt_booking_no" id="txt_booking_no" class="text_boxes" style="width:100px" placeholder="Browse Or Write" onDblClick="openmypage_booking();">
                                </td>
                                <td> 
                                    <? echo create_drop_down( "cbo_planning_status", 100, $planning_status,"", 0, "", $selected,"","", "1,2" ); ?>
                                </td>
                                <td>
                                	<input type="button" value="Show" name="show" id="show" class="formbutton" style="width:100px" onClick="show_details(1)"/>
                                    &nbsp;
                                    <input type="button" value="Revised Booking" name="show" id="show" class="formbutton" style="width:105px" onClick="show_details(2)"/>
                                </td>                	
                            </tr>
                        </tbody>
                    </table>
                </fieldset>
            </div>
            <div style="width:100%;margin-top:2px;">
                <input type="button" value="Click For Program" name="generate" id="generate" class="formbuttonplasminus" style="width:150px" onClick="openmypage_prog()"/>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <input type="hidden" value="" id="selected_data" />
                <input type="button" value="Close" name="close" id="close" class="formbuttonplasminus" style="width:150px" onClick="fnc_close()"/>
            </div>
		</form>
	</div>
    <div id="list_container_fabric_desc"></div>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>   
<?
exit();
}

if($action=="booking_item_details")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 

	$company_name=str_replace("'","",$cbo_company_name);
	$within_group=str_replace("'","",$cbo_within_group);
	$buyer_name=str_replace("'","",$cbo_buyer_name);
	$planning_status=str_replace("'","",$cbo_planning_status);
	
	$job_no_cond=""; $booking_cond="";	
	if(str_replace("'","",$hide_job_id)!="")
	{
		$job_no_cond="and a.id in(".str_replace("'","",$hide_job_id).")";
	}
	
	$txt_booking="%".str_replace("'","",trim($txt_booking_no));
	if(str_replace("'","",trim($txt_booking_no))!="")
	{
		$booking_cond="and a.booking_no like '$txt_booking'";
	}
	
	if($within_group==0) $within_group_cond=""; else $within_group_cond=" and a.within_group=$within_group";
	if($buyer_name==0) $buyer_id_cond=""; else $buyer_id_cond=" and a.buyer_id=$buyer_name";
	
	$company_arr=return_library_array( "select id,company_short_name from lib_company",'id','company_short_name');
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name" );
	$machine_arr=return_library_array( "select id, machine_no from lib_machine_name",'id','machine_no');
	
	$composition_arr=array();
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array=sql_select($sql_deter);
	if(count($data_array)>0)
	{
		foreach( $data_array as $row )
		{
			if(array_key_exists($row[csf('id')],$composition_arr))
			{
				$composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
			}
			else
			{
				$composition_arr[$row[csf('id')]]=$row[csf('construction')].", ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
			}
		}
	}
	
	$program_data_array=array(); $booking_program_arr=array();
	if($db_type==0)
	{
		$sql_plan="select mst_id, booking_no, po_id, yarn_desc as job_dtls_id, body_part_id, fabric_desc, gsm_weight, dia, color_type_id, group_concat(dtls_id) as prog_no, sum(program_qnty) as program_qnty from ppl_planning_entry_plan_dtls where status_active=1 and is_deleted=0 and is_sales=1 group by mst_id, booking_no, po_id, body_part_id, fabric_desc, gsm_weight, dia, color_type_id";
	}
	else
	{
		$sql_plan="select mst_id, booking_no, po_id, yarn_desc as job_dtls_id, body_part_id, fabric_desc, gsm_weight, dia, color_type_id, LISTAGG(dtls_id, ',') WITHIN GROUP (ORDER BY dtls_id) as prog_no, sum(program_qnty) as program_qnty from ppl_planning_entry_plan_dtls where status_active=1 and is_deleted=0 and is_sales=1 group by mst_id, booking_no, po_id, yarn_desc, body_part_id, fabric_desc, gsm_weight, dia, color_type_id";
	}
	//echo $sql_plan;
	$res_plan=sql_select($sql_plan);
	foreach($res_plan as $rowPlan)
	{
		$program_data_array[$rowPlan[csf('booking_no')]][$rowPlan[csf('po_id')]][$rowPlan[csf('job_dtls_id')]][$rowPlan[csf('body_part_id')]][$rowPlan[csf('fabric_desc')]][$rowPlan[csf('gsm_weight')]][$rowPlan[csf('dia')]][$rowPlan[csf('color_type_id')]]['mst_id']=$rowPlan[csf('mst_id')];
		$program_data_array[$rowPlan[csf('booking_no')]][$rowPlan[csf('po_id')]][$rowPlan[csf('job_dtls_id')]][$rowPlan[csf('body_part_id')]][$rowPlan[csf('fabric_desc')]][$rowPlan[csf('gsm_weight')]][$rowPlan[csf('dia')]][$rowPlan[csf('color_type_id')]]['prog_no']=$rowPlan[csf('prog_no')];
		$program_data_array[$rowPlan[csf('booking_no')]][$rowPlan[csf('po_id')]][$rowPlan[csf('job_dtls_id')]][$rowPlan[csf('body_part_id')]][$rowPlan[csf('fabric_desc')]][$rowPlan[csf('gsm_weight')]][$rowPlan[csf('dia')]][$rowPlan[csf('color_type_id')]]['program_qnty']=$rowPlan[csf('program_qnty')];
		
		$booking_program_arr[$rowPlan[csf('booking_no')]].=$rowPlan[csf('prog_no')].",";
	}
	
	if($type==2)
	{
		$knit_qnty_array=return_library_array( "select a.booking_id, sum(b.grey_receive_qnty) as knitting_qnty from inv_receive_master a, pro_grey_prod_entry_dtls b where a.id=b.mst_id and a.item_category=13 and a.entry_form=2 and a.receive_basis=2 and b.status_active=1 and b.is_deleted=0 group by a.booking_id", "booking_id","knitting_qnty");
		
		if($db_type==0)
		{
			$sql="select a.id, a.company_id, a.item_category, a.fabric_source, a.booking_type, a.is_short, a.booking_no, a.booking_date, a.job_no, a.buyer_id, a.is_approved, b.construction, b.copmposition, b.pre_cost_fabric_cost_dtls_id, b.po_break_down_id, b.gsm_weight, b.dia_width, sum(b.grey_fab_qnty) as qnty, c.style_ref_no from wo_booking_mst a, wo_booking_dtls b, wo_po_details_master c where a.booking_no=b.booking_no and b.job_no=c.job_no and a.company_id=$company_name and a.item_category=2 and a.fabric_source=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.grey_fab_qnty>0 $booking_cond group by a.booking_no, b.po_break_down_id, b.pre_cost_fabric_cost_dtls_id, b.dia_width order by cast(b.dia_width as unsigned),a.booking_no";// and a.buyer_id like '$buyer_name'
		}
		else
		{
			$sql="select a.id, a.company_id, a.item_category, a.fabric_source, a.booking_type, a.is_short, a.booking_no, a.booking_date, a.job_no, a.buyer_id, a.is_approved, b.construction, b.copmposition, b.pre_cost_fabric_cost_dtls_id, b.po_break_down_id, b.gsm_weight, b.dia_width, sum(b.grey_fab_qnty) as qnty, c.style_ref_no from wo_booking_mst a, wo_booking_dtls b, wo_po_details_master c where a.booking_no=b.booking_no and b.job_no=c.job_no and a.company_id=$company_name and a.item_category=2 and a.fabric_source=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.grey_fab_qnty>0 $booking_cond group by a.id, a.company_id, a.fabric_source, a.booking_type, a.is_short, a.booking_no, a.booking_date, a.job_no, a.buyer_id, a.is_approved, a.item_category, b.construction, b.copmposition, b.pre_cost_fabric_cost_dtls_id, b.po_break_down_id, b.gsm_weight, b.dia_width, c.style_ref_no order by b.dia_width,a.booking_no";	
		}
		//echo $sql;die;
		$found_prog_no=''; $booking_no=''; $not_found_prog_array=array(); $bookingType=array();
		$nameArray=sql_select( $sql );
		foreach ($nameArray as $row)
		{
			$plan_id='';
			$gsm=$costing_per_id_library[$row[csf('pre_cost_fabric_cost_dtls_id')]]['gsm'];
			$dia=$row[csf('dia_width')]; 
			$desc=$costing_per_id_library[$row[csf('pre_cost_fabric_cost_dtls_id')]]['desc'];
			$determination_id=$costing_per_id_library[$row[csf('pre_cost_fabric_cost_dtls_id')]]['determination_id'];
			$color_type_id=$costing_per_id_library[$row[csf('pre_cost_fabric_cost_dtls_id')]]['color_type'];
			
			$update_id=$program_data_array[$row[csf('booking_no')]][$row[csf('po_break_down_id')]][$row[csf('pre_cost_fabric_cost_dtls_id')]][$costing_per_id_library[$row[csf('pre_cost_fabric_cost_dtls_id')]]['body_part']][$desc][$gsm][$row[csf('dia_width')]][$color_type_id]['id'];
			$program_qnty=$program_data_array[$row[csf('booking_no')]][$row[csf('po_break_down_id')]][$row[csf('pre_cost_fabric_cost_dtls_id')]][$costing_per_id_library[$row[csf('pre_cost_fabric_cost_dtls_id')]]['body_part']][$desc][$gsm][$row[csf('dia_width')]][$color_type_id]['program_qnty'];
			$plan_id=$program_data_array[$row[csf('booking_no')]][$row[csf('po_break_down_id')]][$row[csf('pre_cost_fabric_cost_dtls_id')]][$costing_per_id_library[$row[csf('pre_cost_fabric_cost_dtls_id')]]['body_part']][$desc][$gsm][$row[csf('dia_width')]][$color_type_id]['mst_id'];
			$prog_no=$program_data_array[$row[csf('booking_no')]][$row[csf('po_break_down_id')]][$row[csf('pre_cost_fabric_cost_dtls_id')]][$costing_per_id_library[$row[csf('pre_cost_fabric_cost_dtls_id')]]['body_part']][$desc][$gsm][$row[csf('dia_width')]][$color_type_id]['prog_no'];
			
			if($prog_no!="")
			{
				//$prog_no=implode(",",array_unique(explode(",",$prog_no)));
				$found_prog_no.=$prog_no.",";
			}
			
			$booking_no=$row[csf('booking_no')];
			$bookingType[$row[csf('booking_no')]][1]=$row[csf('booking_type')];
			$bookingType[$row[csf('booking_no')]][2]=$row[csf('is_short')];
		}
		
		$found_prog_no=array_unique(explode(",",substr($found_prog_no,0,-1)));
		$booking_program_no=array_unique(explode(",",substr($booking_program_arr[$booking_no],0,-1)));
		
		$not_found_prog_array=array_diff($booking_program_no,$found_prog_no);
		if(count($not_found_prog_array)>0)
		{
			if($db_type==0)
			{
				$plan_details_array=return_library_array( "select dtls_id, group_concat(distinct(po_id)) as po_id from ppl_planning_entry_plan_dtls where dtls_id in(".implode(",",$not_found_prog_array).") group by dtls_id", "dtls_id", "po_id"  );
			}
			else
			{
				$plan_details_array=return_library_array( "select dtls_id, LISTAGG(po_id, ',') WITHIN GROUP (ORDER BY po_id) as po_id from ppl_planning_entry_plan_dtls where dtls_id in(".implode(",",$not_found_prog_array).") group by dtls_id", "dtls_id", "po_id"  );
			}
			
			$po_array=array();
			$costing_sql=sql_select("select a.job_no, a.style_ref_no, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name=$company_name");
			foreach($costing_sql as $row)
			{
				$po_array[$row[csf('id')]]['no']=$row[csf('po_number')];
				$po_array[$row[csf('id')]]['job_no']=$row[csf('job_no')]; 
				$po_array[$row[csf('id')]]['style_ref']=$row[csf('style_ref_no')]; 
			}
		?>
        	<fieldset style="width:2410px;">
            	<input type="button" value="Delete Program" name="generate" id="generate" class="formbutton" style="width:150px" onClick="delete_prog()"/>
                <input type="hidden" value="<? echo $type; ?>" name="txt_type" id="txt_type">
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="2600" class="rpt_table" >
                    <thead>
                        <th width="40"></th>
                        <th width="40">SL</th>
                        <th width="100">Party Name</th>
                        <th width="60">Program No</th>
                        <th width="80">Program Date</th>
                        <th width="80">Start Date</th>
                        <th width="80">T.O.D</th>
                        <th width="70">Buyer</th>
                        <th width="110">Booking No</th>
                        <th width="90">Job No</th>
                        <th width="130">Order No</th>
                        <th width="110">Style</th>
                        <th width="80">Dia / GG</th>
                        <th width="100">Distribution Qnty</th>
                        <th width="80">M/C no</th>
                        <th width="70">Status</th>
                        <th width="140">Fabric Desc.</th>
                        <th width="100">Color Range</th>
                        <th width="100">Color Type</th>
                        <th width="80">Stitch Length</th>
                        <th width="80">Sp. Stitch Length</th>
                        <th width="80">Draft Ratio</th>
                        <th width="70">Fabric Gsm</th>
                        <th width="70">Fabric Dia</th>
                        <th width="80">Width/Dia Type</th>
                        <th width="100">Program Qnty</th>
                        <th width="100">Knitting Qnty</th>
                        <th width="130">Remarks</th>
                        <th></th>
                    </thead>
                </table>  
                <div style="width:2600px; overflow-y:scroll; max-height:330px;" id="scroll_body">
                    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="2580" class="rpt_table" id="tbl_list_search">    
					<?
						$i=1;
                        $sql="select a.company_id, a.buyer_id, a.booking_no, a.body_part_id, a.color_type_id, a.fabric_desc, a.gsm_weight, a.dia, a.width_dia_type, b.id, b.knitting_source, b.knitting_party, b.color_id, b.color_range, b.machine_dia, b.machine_gg, b.program_qnty, b.program_date, b.stitch_length, b.spandex_stitch_length, b.draft_ratio, b.machine_id, b.distribution_qnty, b.status, b.start_date, b.end_date, b.remarks from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b, ppl_planning_entry_plan_dtls c where a.id=b.mst_id and b.id=c.dtls_id and b.id in(".implode(",",$not_found_prog_array).") and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 group by b.id, a.company_id, a.buyer_id, a.booking_no, a.body_part_id, a.color_type_id, a.fabric_desc, a.gsm_weight, a.dia, a.width_dia_type, b.knitting_source, b.knitting_party, b.color_id, b.color_range, b.machine_dia, b.machine_gg, b.program_qnty, b.program_date, b.stitch_length, b.spandex_stitch_length, b.draft_ratio, b.machine_id, b.distribution_qnty, b.status, b.start_date, b.end_date, b.remarks order by b.machine_dia, b.machine_gg, b.id";		
						//echo $sql;
						$nameArray=sql_select( $sql );
						foreach ($nameArray as $row)
						{
							if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						   
							$machine_dia_gg=$row[csf('machine_dia')].'X'.$row[csf('machine_gg')];
							
							$machine_no='';
							$machine_id=explode(",",$row[csf("machine_id")]);
							foreach($machine_id as $val)
							{
								if($machine_no=='') $machine_no=$machine_arr[$val]; else $machine_no.=",".$machine_arr[$val];
							}

							$po_id=array_unique(explode(",",$plan_details_array[$row[csf('id')]]));
							$po_no=''; $style_ref=''; $job_no='';
							
							foreach($po_id as $val)
							{
								if($po_no=='') $po_no=$po_array[$val]['no']; else $po_no.=",".$po_array[$val]['no'];
								if($style_ref=='') $style_ref=$po_array[$val]['style_ref'];
								if($job_no=='') $job_no=$po_array[$val]['job_no'];
							}
							
							$knitting_qnty=$knit_qnty_array[$row[csf('id')]];
							if($knitting_qnty>0) $disabled="disabled='disabled'"; else $disabled=""; 
							
							if($row[csf('knitting_source')]==1) $knitting_source=$company_arr[$row[csf('knitting_party')]];
							else if($row[csf('knitting_source')]==3) $knitting_source=$supllier_arr[$row[csf('knitting_party')]];
							else $knitting_source="&nbsp;";
									
							if(!in_array($machine_dia_gg, $machine_dia_gg_array))
							{
							?>
								<tr bgcolor="#EFEFEF">
									<td colspan="29"><b>Machine Dia:- <?php echo $machine_dia_gg; ?></b></td>
								</tr>
							<?
								$machine_dia_gg_array[]=$machine_dia_gg;
							}
							
							$pre='';
							if($bookingType[$row[csf('booking_no')]][1]!=4)
							{
								if($bookingType[$row[csf('booking_no')]][2]==1) 
								{
									$pre="(S)";
								}
								else 
								{
									$pre="(M)"; 
								}
							}
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>"> 
								<td width="40" align="center" valign="middle">
									<input type="checkbox" id="tbl_<? echo $i;?>" name="check[]" <? echo $disabled; ?>/>
									<input id="promram_id_<? echo $i;?>" name="promram_id[]" type="hidden" value="<? echo $row[csf('id')]; ?>" />
								</td> 
								<td width="40"><? echo $i; ?></td>
								<td width="100"><p><? echo $knitting_source; ?></p></td>
								<td width="60" align="center"><a href='##' onClick="generate_report2(<? echo $row[csf('company_id')].",".$row[csf('id')]; ?>)"><? echo $row[csf('id')]; ?></a>&nbsp;</td>
								<td width="80" align="center">&nbsp;<? echo change_date_format($row[csf('program_date')]); ?></td>
								<td width="80" align="center">&nbsp;<? if($row[csf('start_date')]!="0000-00-00") echo change_date_format($row[csf('start_date')]); ?></td>
								<td width="80" align="center">&nbsp;<? if($row[csf('end_date')]!="0000-00-00") echo change_date_format($row[csf('end_date')]); ?></td>
								<td width="70"><p><? echo $buyer_arr[$row[csf('buyer_id')]]; ?></p></td>
                                <td width="110"><p><? echo $row[csf('booking_no')].$pre; ?></p></td>
								<td width="90"><p><? echo $job_no; ?></p></td>
								<td width="130"><div style="word-wrap:break-word; width:129px"><? echo $po_no; ?></div></td>
								<td width="110"><p><? echo $style_ref; ?></p></td>
								<td width="80"><p><? echo $machine_dia_gg; ?></p></td>
								<td align="right" width="100"><? echo number_format($row[csf('distribution_qnty')],2); ?></td>
								<td width="80"><p>&nbsp;&nbsp;<? echo $machine_no; ?></p></td>
								<td width="70"><p><? echo $knitting_program_status[$row[csf('status')]]; ?>&nbsp;</p></td>
								<td width="140"><p><? echo $row[csf('fabric_desc')]; ?></p></td>
								<td width="100"><p><? echo $color_range[$row[csf('color_range')]] ?>&nbsp;</p></td>
								<td width="100"><p><? echo $color_type[$row[csf('color_type_id')]]; ?>&nbsp;</p></td>
								<td width="80"><p><? echo $row[csf('stitch_length')]; ?>&nbsp;</p></td>
								<td width="80"><p><? echo $row[csf('spandex_stitch_length')]; ?>&nbsp;</p></td>
								<td align="right" width="80"><? echo number_format($row[csf('draft_ratio')],2); ?>&nbsp;</td>
								<td width="70"><p><? echo $row[csf('gsm_weight')]; ?>&nbsp;</p></td>
								<td width="70"><p><? echo $row[csf('dia')]; ?>&nbsp;</p></td>
								<td width="80"><? echo $fabric_typee[$row[csf('width_dia_type')]]; ?>&nbsp;</td>
								<td align="right" width="100">
									<? //echo number_format($row[csf('program_qnty')],2); ?>
                                    <input type="text" class="text_boxes_numeric" name="prog_qty[]" id="prog_qty_<? echo $i; ?>" value="<? echo $row[csf('program_qnty')]; ?>" style="width:80px" />
                                </td>
								<td align="right" width="100"><? echo number_format($knitting_qnty,2); ?></td>
								<td width="130"><p><? echo $row[csf('remarks')]; ?>&nbsp;</p></td>
                                <td align="center"><input type="button" value="Update" onClick="fnc_update(<? echo $i; ?>);" class="formbutton" style="width:80px"></td>
							</tr>
							<?
							
							$sub_tot_program_qnty+=$row[csf('program_qnty')];
							$sub_tot_knitting_qnty+=$knitting_qnty;
							
							$tot_program_qnty+=$row[csf('program_qnty')];
							$tot_knitting_qnty+=$knitting_qnty;
							
							$i++;
						}
						?>
					</table>
				</div>
			</fieldset>   
        <?    
		}
		else
		{
			echo "<div style='width:1100px' align='center'><font style='color:#F00; font-size:17px; font-weight:bold'>No Program Found.</font></div>";
		}
	}
	else
	{
		$sql="select a.id, a.company_id, a.within_group, a.job_no, a.sales_booking_no, a.booking_id, a.buyer_id, a.style_ref_no, a.booking_date, b.id as dtls_id, b.body_part_id, b.color_type_id, b.fabric_desc, b.determination_id, b.gsm_weight, b.dia, b.width_dia_type, b.color_range_id, b.color_id, b.finish_qty, b.grey_qty from fabric_sales_order_mst a, fabric_sales_order_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_id=$company_name $booking_cond $job_no_cond $buyer_id_cond $within_group_cond order by b.dia";
		//echo $sql;die;
		?>
		<form name="palnningEntry_2" id="palnningEntry_2">
			<fieldset style="width:1282px;">
				<legend>Fabric Description Details</legend>	
            	<input type="hidden" value="<? echo $type; ?>" name="txt_type" id="txt_type">
				<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1282" class="rpt_table" >
					<thead>
						<th width="40">SL</th>
						<th width="50">Plan Id</th>
						<th width="60">Prog. No</th>
						<th width="100">Sales/ Booking No</th>
						<th width="70">Booking Date</th>
						<th width="60">Buyer</th>
						<th width="105">Job No</th>
						<th width="100">Style</th>
						<th width="80">Body Part</th>
						<th width="70">Color Type</th>
						<th width="140">Fabric Desc.</th>
						<th width="50">Gsm</th>
						<th width="50">Dia</th>
						<th width="70">Width/Dia Type</th>
						<th width="70">Booking Qnty</th>
						<th width="70">Prog. Qnty</th>
						<th>Balance Prog. Qnty</th>
					</thead>
				</table>
				<div style="width:1282px; overflow-y:scroll; max-height:280px;" id="buyer_list_view" align="center">
					<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1265" class="rpt_table" id="tbl_list_search">
						<tbody>
							<? 
								$i=1; $k=1; $z=1; $dia_array=array();
								$nameArray=sql_select( $sql );
								foreach ($nameArray as $row)
								{
									$plan_id='';
									$gsm=$row[csf('gsm_weight')];
									$dia=$row[csf('dia')]; 
									$desc=$row[csf('fabric_desc')];
									$determination_id=$row[csf('determination_id')];
									
									$program_qnty=$program_data_array[$row[csf('sales_booking_no')]][$row[csf('id')]][$row[csf('dtls_id')]][$row[csf('body_part_id')]][$desc][$gsm][$dia][$row[csf('color_type_id')]]['program_qnty'];
									
									$plan_id=$program_data_array[$row[csf('sales_booking_no')]][$row[csf('id')]][$row[csf('dtls_id')]][$row[csf('body_part_id')]][$desc][$gsm][$dia][$row[csf('color_type_id')]]['mst_id'];
									
									$prog_no=$program_data_array[$row[csf('sales_booking_no')]][$row[csf('id')]][$row[csf('dtls_id')]][$row[csf('body_part_id')]][$desc][$gsm][$dia][$row[csf('color_type_id')]]['prog_no'];
									
									$prog_no=implode(",",array_unique(explode(",",$prog_no)));
									$balance_qnty=$row[csf('grey_qty')]-$program_qnty;
									
									if(($planning_status==2 && $balance_qnty<=0) || ($planning_status==1 && $balance_qnty>0))
									{
										if($z%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
									
										if(!in_array($dia, $dia_array))
										{
											if ($k!=1)
											{
											?>
												<tr bgcolor="#CCCCCC" id="tr_<? echo $i; ?>">
													<td colspan="14" align="right"><b>Sub Total</b></td>
													<td align="right"><b><? echo number_format($total_dia_qnty,2,'.',''); ?></b></td>
													<td align="right"><b><? echo number_format($total_program_qnty,2,'.',''); ?></b></td>
													<td align="right"><b><? echo number_format($total_balance,2,'.',''); ?></b></td>
												</tr>
											<?
												$total_dia_qnty = 0;
												$total_program_qnty = 0;
												$total_balance = 0;
												$i++;
											}
											
										?>
											<tr bgcolor="#EFEFEF" id="tr_<? echo $i; ?>">
												<td colspan="17">
													<b>Dia/Width:- <?php echo $dia; ?></b>
												</td>
											</tr>
										<?
											$dia_array[]=$row[csf('dia')];
											$k++;
											$i++;
										}
										
										if($row[csf('within_group')]==1)
											$buyer=$company_arr[$row[csf('buyer_id')]]; 
										else
											$buyer=$buyer_arr[$row[csf('buyer_id')]];
										
										$reqsn_found_or_not=0;
										
										?>
										<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none;cursor:pointer" onClick="selected_row('<? echo $i; ?>')" id="tr_<? echo $i; ?>"> 
											<td width="40"><? echo $z; ?></td>
											<td width="50" id="plan_id_<? echo $i; ?>"><p><? echo $plan_id; ?>&nbsp;</p></td>
											<td width="60" id="prog_no_<? echo $i; ?>"><p>
											<? 
												$print_program_no="";
												$prog_no_arr=array_unique(explode(",",$prog_no));
												foreach($prog_no_arr as $prog_no)
												{
													$print_program_no.="<a href='##' onclick=\"generate_report2(".$row[csf('company_id')].",".$prog_no.")\">".$prog_no."</a>,";
												}
												$print_program_no=chop($print_program_no,",");
												echo $print_program_no; 
											?>
                                            &nbsp;</p></td>
											<td width="100" id="booking_no_<? echo $i; ?>"><p><? echo $row[csf('sales_booking_no')]; ?></p></td>
											<td width="70" align="center"><? echo change_date_format($row[csf('booking_date')]); ?></td>
											<td width="60"><p><? echo $buyer; ?></p></td>
											<td width="105"><p><? echo $row[csf('job_no')]; ?></p></td>
											<td width="100"><p><? echo $row[csf('style_ref_no')]; ?></p></td>
											<td width="80"><p><? echo $body_part[$row[csf('body_part_id')]]; ?></p></td>
											<td width="70"><p><? echo $color_type[$row[csf('color_type_id')]]; ?></p></td>
											<td width="140" id="desc_<? echo $i; ?>"><p><? echo $desc; ?></p></td>
											<td width="50" id="gsm_weight_<? echo $i; ?>"><p><? echo $gsm; ?></p></td>
											<td width="50" id="dia_width_<? echo $i; ?>"><p><? echo $dia; ?></p></td>
											<td width="70"><? echo $fabric_typee[$row[csf('width_dia_type')]]; ?></td>
											<td align="right" id="booking_qnty_<? echo $i; ?>" width="70"><? echo number_format($row[csf('grey_qty')],2,'.',''); ?></td>
											<td align="right" width="70">&nbsp;<? if($program_qnty>0) echo number_format($program_qnty,2,'.',''); ?></td>
											<td align="right"><? echo number_format($balance_qnty,2,'.',''); ?></td>
											<input type="hidden" name="buyer_id[]" id="buyer_id_<? echo $i; ?>" value="<? echo $row[csf('buyer_id')]; ?>" />
											<input type="hidden" name="body_part_id[]" id="body_part_id_<? echo $i; ?>" value="<? echo $row[csf('body_part_id')]; ?>" />
											<input type="hidden" name="color_type_id[]" id="color_type_id_<? echo $i; ?>" value="<? echo $row[csf('color_type_id')]; ?>" />
											<input type="hidden" name="determination_id[]" id="determination_id_<? echo $i; ?>" value="<? echo $determination_id; ?>" />
											<input type="hidden" name="fabric_typee[]" id="fabric_typee_<? echo $i; ?>" value="<? echo $row[csf('width_dia_type')]; ?>" />
											<input type="hidden" name="job_id[]" id="job_id_<? echo $i; ?>" value="<? echo $row[csf('id')]; ?>" />
                                            <input type="hidden" name="job_dtls_id[]" id="job_dtls_id_<? echo $i; ?>" value="<? echo $row[csf('dtls_id')]; ?>" />
											<input type="hidden" name="withinGroup[]" id="withinGroup_<? echo $i; ?>" value="<? echo $row[csf('within_group')]; ?>" />
                                            <input type="hidden" name="bookingNo[]" id="bookingNo_<? echo $i; ?>" value="<? echo $row[csf('sales_booking_no')]; ?>" />
                                            <input type="hidden" name="reqsn_found_or_not[]" id="reqsn_found_or_not_<? echo $i; ?>" value="<? echo $reqsn_found_or_not; ?>" />
										</tr>
										<?
										
										$total_dia_qnty+=$row[csf('qnty')];
										$total_program_qnty+=$program_qnty;
										$total_balance+=$balance_qnty;
										
										$total_qnty+=$row[csf('qnty')];
										$grand_total_program_qnty+=$program_qnty;
										$grand_total_balance+=$balance_qnty;
										
										$i++;
										$z++;
									}
								}
								
								if($i>1)
								{
								?>
									<tr bgcolor="#CCCCCC" id="tr_<? echo $i; ?>">
										<td colspan="14" align="right"><b>Sub Total</b></td>
										<td align="right"><b><? echo number_format($total_dia_qnty,2,'.',''); ?></b></td>
										<td align="right"><b><? echo number_format($total_program_qnty,2,'.',''); ?></b></td>
										<td align="right"><b><? echo number_format($total_balance,2,'.',''); ?></b></td>
									</tr>
								<?
								}
							?>
						</tbody>
						<tfoot>
							<th colspan="14" align="right">Grand Total<input type="hidden" name="company_id" id="company_id" value="<? echo $company_name; ?>" /></th>
							<th align="right"><? echo number_format($total_qnty,2,'.',''); ?></th>
							<th align="right"><? echo number_format($grand_total_program_qnty,2,'.',''); ?></th>
							<th align="right"><? echo number_format($grand_total_balance,2,'.',''); ?></th>
						</tfoot>
					</table>
				</div>
			</fieldset>
		</form>         
	<?
	}
	exit();	
}

if($action=="prog_qnty_popup")
{
	echo load_html_head_contents("Program Qnty Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$current_date=date("d-m-Y");
	
	//echo "select id, machine_dia, machine_gg, fabric_dia, stitch_length from fabric_mapping where mst_id=$determination_id and status_active=1 and is_deleted=0";
	$dataArray=sql_select("select id, machine_dia, machine_gg, fabric_dia, stitch_length from fabric_mapping where mst_id=$determination_id and status_active=1 and is_deleted=0");
	//echo $data;die;echo $permission; 
	?>
     
	<script>
		var permission='<? echo $permission; ?>';
		
		function openpage_machine()
		{
			/*if(form_validation('txt_machine_dia','Machine Dia')==false )
			{
				return;
			}*/
			
			var save_string=$('#save_data').val();
			var txt_machine_dia=$('#txt_machine_dia').val();
			var update_dtls_id=$('#update_dtls_id').val();
			
			var page_link='machine_wise_knitting_plan_controller.php?action=machine_info_popup&save_string='+save_string+'&companyID='+<? echo $companyID; ?>+'&txt_machine_dia='+txt_machine_dia+'&update_dtls_id='+update_dtls_id;
			var title='Machine Info';
			
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=840px,height=300px,center=1,resize=1,scrolling=0','../../');
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0];
				var hidden_machine_no=this.contentDoc.getElementById("hidden_machine_no").value;
				var hidden_machine_id=this.contentDoc.getElementById("hidden_machine_id").value;
				var save_string=this.contentDoc.getElementById("save_string").value;
				var hidden_machine_capacity=this.contentDoc.getElementById("hidden_machine_capacity").value;
				var hidden_distribute_qnty=this.contentDoc.getElementById("hidden_distribute_qnty").value;
				var hidden_min_date=this.contentDoc.getElementById("hidden_min_date").value;
				var hidden_max_date=this.contentDoc.getElementById("hidden_max_date").value;
				
				$('#txt_machine_no').val(hidden_machine_no);
				$('#machine_id').val(hidden_machine_id);	
				$('#save_data').val(save_string);
				$('#txt_machine_capacity').val(hidden_machine_capacity);
				$('#txt_distribution_qnty').val(hidden_distribute_qnty);
				$('#txt_start_date').val(hidden_min_date);
				$('#txt_end_date').val(hidden_max_date);
				
				//var days_req=hidden_distribute_qnty*1/hidden_machine_capacity*1;
				//$('#txt_days_req').val(days_req.toFixed(2));
				days_req();
			}
		}
		
		function days_req()
		{
			txt_start_date=$('#txt_start_date').val();
			txt_end_date=$('#txt_end_date').val();
			
			if(txt_start_date!="" && txt_end_date!="")
			{
				var days_req=date_diff('d',txt_start_date,txt_end_date);
				$('#txt_days_req').val(days_req+1);
			}
			else
			{
				$('#txt_days_req').val('');
			}
		}
		
		function openpage_color()
		{
			var hidden_color_id=$('#hidden_color_id').val();
			var page_link='machine_wise_knitting_plan_controller.php?action=color_info_popup&companyID='+<? echo $companyID; ?>+'&job_id='+'<? echo $job_id; ?>'+'&job_dtls_id='+'<? echo $job_dtls_id; ?>'+'&booking_no='+'<? echo $booking_no; ?>'+'&dia='+'<? echo $dia; ?>'+'&hidden_color_id='+hidden_color_id;
			var title='Color Info';
			
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=430px,height=300px,center=1,resize=1,scrolling=0','../../');
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0];
				var hidden_color_no=this.contentDoc.getElementById("txt_selected").value;
				var hidden_color_id=this.contentDoc.getElementById("txt_selected_id").value;
				
				$('#txt_color').val(hidden_color_no);
				$('#hidden_color_id').val(hidden_color_id);
			}
		}
		
		function fnc_program_entry(operation)
		{
			//cbo_knitting_source*cbo_knitting_party*Knitting Source*Knitting Party*	
			if(form_validation('txt_machine_dia*txt_machine_gg*txt_program_qnty','Machine Dia*Machine GG*Program Qnty')==false )
			{
				return;
			}
			
			var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_knitting_source*cbo_knitting_party*txt_color*txt_machine_dia*txt_machine_gg*txt_program_qnty*txt_stitch_length*txt_spandex_stitch_length*txt_draft_ratio*machine_id*txt_machine_capacity*txt_distribution_qnty*cbo_knitting_status*txt_start_date*txt_end_date*txt_program_date*cbo_feeder*txt_remarks*save_data*updateId*update_dtls_id*cbo_color_range*cbo_dia_width_type*hidden_color_id*txt_fabric_dia*cbo_location_name*hidden_advice_data',"../../../")+'&companyID='+<? echo $companyID; ?>+'&gsm='+'<? echo $gsm; ?>'+'&dia='+'<? echo $dia; ?>'+'&desc='+'<? echo $desc; ?>'+'&determination_id='+<? echo $determination_id; ?>+'&booking_no='+'<? echo $booking_no; ?>'+'&data='+<? echo $data; ?>+'&body_part_id='+<? echo $body_part_id; ?>+'&color_type_id='+<? echo $color_type_id; ?>+'&fabric_typee='+<? echo $fabric_type; ?>+'&tot_booking_qnty='+<? echo $booking_qnty; ?>+'&buyer_id='+<? echo $buyer_id; ?>+'&within_group='+<? echo $within_group; ?>;
			
			freeze_window(operation);
			
			http.open("POST","machine_wise_knitting_plan_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange=fnc_program_entry_Reply_info;
		}
	
		function fnc_program_entry_Reply_info()
		{
			if(http.readyState == 4) 
			{
				//release_freezing();return;//alert(http.responseText);
				var reponse=trim(http.responseText).split('**');	
					
				show_msg(reponse[0]);
				
				if((reponse[0]==0 || reponse[0]==1 || reponse[0]==2))
				{
					reset_form('programQnty_1','','','txt_program_date,<? echo $current_date;?>','','');
					$('#updateId').val(reponse[1]);
					show_list_view(reponse[1], 'planning_info_details', 'list_view', 'machine_wise_knitting_plan_controller', '' ) ;
					set_button_status(0, permission, 'fnc_program_entry',1);	
				}
				release_freezing();	
			}
		}
		
		function active_inactive()
		{
			var knitting_source=document.getElementById('cbo_knitting_source').value;
			
			reset_form('','','txt_machine_no*machine_id*txt_machine_capacity*txt_distribution_qnty*txt_days_req*cbo_location_name','txt_program_date,<? echo $current_date; ?>','','');
			if(knitting_source==1)
			{
				document.getElementById('txt_machine_no').disabled=false;
				document.getElementById('cbo_location_name').disabled=false;
			}
			else
			{
				document.getElementById('txt_machine_no').disabled=true;
				document.getElementById('cbo_location_name').disabled=true;
			}
		}
		
		/*function openpage_feeder()
		{
			var no_of_feeder_data=$('#hidden_no_of_feeder_data').val();
			var color_type_id=<?// echo $color_type_id; ?>;
			
			if(!(color_type_id==2 || color_type_id==3 || color_type_id==4))
			{
				alert("Only for Stripe");
				return;
			}
			
			var page_link='machine_wise_knitting_plan_controller.php?action=feeder_info_popup&no_of_feeder_data='+no_of_feeder_data+'&pre_cost_id='+'<?echo $pre_cost_id; ?>';
			var title='Stripe Measurement Info';
			
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=640px,height=300px,center=1,resize=1,scrolling=0','../');
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0];
				var hidden_no_of_feeder_data=this.contentDoc.getElementById("hidden_no_of_feeder_data").value;
				
				$('#hidden_no_of_feeder_data').val(hidden_no_of_feeder_data);
			}
		}*/
		
		function openpage_advice()
		{
			var hidden_advice_data=$('#hidden_advice_data').val();
			
			var page_link='machine_wise_knitting_plan_controller.php?action=advice_info_popup&hidden_advice_data='+hidden_advice_data;
			var title='Advice Info';
			
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=440px,height=200px,center=1,resize=1,scrolling=0','../');
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0];
				var advice_data=this.contentDoc.getElementById("txt_advice").value;
				$('#hidden_advice_data').val(advice_data);
			}
		}

    </script>

</head>

<body>
<div align="center">
	<? echo load_freeze_divs ("../../../",$permission,1); ?>
	<form name="programQnty_1" id="programQnty_1">
		<fieldset style="width:900px;">
        	<table border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="890" align="center">
                <thead>
                    <th width="300">Fabric Description</th>
                    <th width="80">GSM</th>
                    <th width="80">Dia</th>
                    <th>Booking Qnty</th>
                </thead>
                <tr bgcolor="#FFFFFF">
                    <td><p><? echo $desc; ?></p></td>
                    <td><? echo $gsm; ?>&nbsp;</td>
                    <td><? echo $dia; ?>&nbsp;</td>
                    <td align="right"><? echo number_format($booking_qnty,2); ?></td>
                </tr>
            </table>
        </fieldset> 
        <fieldset style="width:900px; margin-top:5px;">
            <legend>New Entry</legend>
            <table width="900" align="center" border="0">
                <tr>
                    <td>Knitting Source</td>
                    <td>
						<?
							echo create_drop_down("cbo_knitting_source",152,$knitting_source,"", 1, "-- Select --", 0,"active_inactive();load_drop_down( 'machine_wise_knitting_plan_controller', this.value+'**'+$companyID, 'load_drop_down_knitting_party','knitting_party');",0,'1,3');
                        ?>
                    </td>
                    <td>Knitting Party</td>
                    <td id="knitting_party">
						<?
                        	echo create_drop_down( "cbo_knitting_party", 177, $blank_array,"",1, "--Select Knit Party--", 1, "" );
                        ?>
                    </td>
                    <td>Color</td>
                    <td>
                    	<input type="text" name="txt_color" id="txt_color" class="text_boxes" style="width:140px;" placeholder="Browse" onClick="openpage_color();" readonly/>
                        <input type="hidden" name="hidden_color_id" id="hidden_color_id" readonly/>
                    </td>
                </tr>
                <tr>
                	<td>Color Range</td>                                              
                    <td> 
                        <?
                        	echo create_drop_down( "cbo_color_range", 152, $color_range,"",1, "-- Select --", 0, "" );
                        ?>
                    </td>
                    <td class="must_entry_caption">Machine Dia</td>
                    <td>
                        <input type="text" name="txt_machine_dia" id="txt_machine_dia" class="text_boxes_numeric" style="width:60px;" maxlength="3" title="Maximum 3 Character" value="<? echo $dataArray[0][csf('machine_dia')]; ?>"/>
                         <?
                        	echo create_drop_down( "cbo_dia_width_type", 100, $fabric_typee,"",1, "-- Select --", $fabric_type, "" );
                        ?>
                    </td>
                    <td class="must_entry_caption">Machine GG</td>
                    <td>
                        <input type="text" name="txt_machine_gg" id="txt_machine_gg" class="text_boxes" style="width:140px;" value="<? echo $dataArray[0][csf('machine_gg')]; ?>"/>
                    </td>
                </tr>
                <tr>
                	<td>Finish Fabric Dia</td>
                    <td>
                        <input type="text" name="txt_fabric_dia" id="txt_fabric_dia" class="text_boxes" style="width:140px;" value="<? echo $dataArray[0][csf('fabric_dia')]; ?>"/>
                    </td>
                	<td class="must_entry_caption">Program Qnty</td>
                    <td>
                        <input type="text" name="txt_program_qnty" id="txt_program_qnty" class="text_boxes_numeric" style="width:165px;"/>
                    </td>
                    <td>Program Date</td>                                              
                   	<td> 
                        <input type="text" name="txt_program_date" id="txt_program_date" class="datepicker" style="width:140px" value="<? echo $current_date; ?>" readonly>
                	</td>
                </tr>
                <tr>
                	<td>Stitch Length</td>
                    <td>
                        <input type="text" name="txt_stitch_length" id="txt_stitch_length" class="text_boxes" style="width:140px;" value="<? echo $dataArray[0][csf('stitch_length')]; ?>"/>
                    </td>
                    <td>Spandex Stitch Length</td>
                    <td>
                        <input type="text" name="txt_spandex_stitch_length" id="txt_spandex_stitch_length" class="text_boxes" style="width:165px;"/>
                    </td>
                    <td>Draft Ratio</td>
                    <td>
                        <input type="text" name="txt_draft_ratio" id="txt_draft_ratio" class="text_boxes_numeric" style="width:140px;"/>
                    </td>
                </tr>
                <tr>
                    <td>Machine No</td>
                    <td>
                        <input type="text" name="txt_machine_no" id="txt_machine_no" class="text_boxes" placeholder="Double Click For Search" style="width:140px;" onDblClick="openpage_machine();" disabled="disabled" readonly/>
                        <input type="hidden" name="machine_id" id="machine_id" class="text_boxes" readonly/>
                    </td>
                    <td>Machine Capacity</td>
                    <td>
                        <input type="text" name="txt_machine_capacity" id="txt_machine_capacity" placeholder="Display" class="text_boxes_numeric" style="width:165px;" disabled="disabled"/>
                    </td>
                    <td>Distribution Qnty</td>
                    <td>
                        <input type="text" name="txt_distribution_qnty" id="txt_distribution_qnty" placeholder="Display" class="text_boxes_numeric" style="width:65px;" disabled="disabled"/>
                        <input type="text" name="txt_days_req" id="txt_days_req" placeholder="Days Req." class="text_boxes_numeric" style="width:60px;" disabled="disabled"/>
                    </td>
                </tr>
                <tr>
                	<td>Start Date</td>                                              
                   	<td> 
                        <input type="text" name="txt_start_date" id="txt_start_date" class="datepicker" style="width:140px" value="<? echo $start_date; ?>" readonly >
                	</td>
                    <td>End Date</td>                                              
                   	<td> 
                        <input type="text" name="txt_end_date" id="txt_end_date" class="datepicker" style="width:165px" value="<? echo $end_date; ?>" readonly>
                	</td>
                    <td>Status</td>
                    <td>
                        <?
                        	echo create_drop_down( "cbo_knitting_status", 152, $knitting_program_status,"",1, "--Select Status--", 0, "" );
                        ?>
                    </td>
                </tr>
                <tr>
                    <td>Feeder</td>                                              
                    <td> 
                        <?
							$feeder=array(1=>"Full Feeder",2=>"Half Feeder");
                        	echo create_drop_down( "cbo_feeder", 152, $feeder,"",1, "--Select Feeder--", 0, "" );
                        ?>
                    </td>
                    <td>
                    	<b>Program No.</b>
                    	<!--<input type="button" name="feeder" class="formbuttonplasminus" value="No Of Feeder" onClick="openpage_feeder();" style="width:100px; display:none" />
                        <input type="hidden" name="hidden_no_of_feeder_data" id="hidden_no_of_feeder_data" class="text_boxes">
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Program No.</b>-->
                    </td>
                    <td><input type="text" name="txt_program_no" id="txt_program_no" class="text_boxes" placeholder="Display" disabled style="width:165px"></td>
                    <td>Remarks</td>                                              
                    <td> 
                        <input type="text" name="txt_remarks" id="txt_remarks" class="text_boxes" style="width:140px">
                    </td>
                </tr>
                <tr>
                    <td>Location </td>                                              
                    <td id="location_td">
						<? 
							echo create_drop_down("cbo_location_name", 152, "select id,location_name from lib_location where company_id='$companyID' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --", 0, "");
                        ?>
                    </td>
                    <td>
                    	<input type="button" name="feeder" class="formbuttonplasminus" value="Advice" onClick="openpage_advice();" style="width:100px" />
                        <input type="hidden" name="hidden_advice_data" id="hidden_advice_data" class="text_boxes">
                    </td>
                </tr>
                <tr>
                    <td colspan="4" align="right" class="button_container">
						<? 
							echo load_submit_buttons($permission, "fnc_program_entry", 0,0,"reset_form('programQnty_1','','','txt_start_date,$start_date*txt_end_date,$end_date*txt_program_date,$current_date','','updateId*txt_color');",1);
                        ?>
                     </td>
                     <td colspan="2" align="left" valign="top" class="button_container">   
                        <input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="parent.emailwindow.hide();" style="width:100px;"/>
                        <input type="hidden" name="save_data" id="save_data" class="text_boxes">
                        <input type="hidden" name="updateId" id="updateId" class="text_boxes" value="<? echo str_replace("'",'',$plan_id); ?>">
                        <input type="hidden" name="update_dtls_id" id="update_dtls_id" class="text_boxes">
                    </td>	  
                </tr>
             </table>
		</fieldset>
        <div id="list_view" style="margin-top:5px">
        	<?
			if(str_replace("'",'',$plan_id)!="")
			{
			?>
				<script>
					show_list_view('<? echo str_replace("'",'',$plan_id); ?>', 'planning_info_details', 'list_view', 'machine_wise_knitting_plan_controller', '' ) ;
                </script>
            <?
			}
			?>
        </div>
	</form>
</div>
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
	exit(); 
}

if($action=="planning_info_details")
{
	$company_arr=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	$supllier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');

	$sql="select id, knitting_source, knitting_party, color_range, machine_dia, machine_gg, program_qnty, stitch_length, spandex_stitch_length, draft_ratio, status, program_date from ppl_planning_info_entry_dtls where mst_id=$data and status_active = '1' and is_deleted = '0'";
?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="900" class="rpt_table">
		<thead>
			<th width="90">Knitting Source</th>
			<th width="100">Knitting Company</th>
			<th width="90">Color Range</th>
			<th width="70">Machine Dia</th>
			<th width="70">Machine GG</th>
			<th width="80">Program Qnty</th>
			<th width="75">Stitch Length</th>
			<th width="80">Span. Stitch Length</th>
			<th width="70">Draft Ratio</th>
            <th width="75">Program Date</th>
			<th>Status</th>
		</thead>
	</table>
	<div style="width:900px; max-height:140px; overflow-y:scroll" id="list_container_batch" align="left">	 
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="882" class="rpt_table" id="tbl_list_search">  
		<?
			$i=1;
			$result=sql_select($sql);
			foreach ($result as $row)
			{  
				if ($i%2==0)  
					$bgcolor="#E9F3FF";
				else
					$bgcolor="#FFFFFF";	 
				if($row[csf('knitting_source')]==1)
					$knit_party=$company_arr[$row[csf('knitting_party')]]; 
				else
					$knit_party=$supllier_arr[$row[csf('knitting_party')]];
			?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="get_php_form_data(<? echo $row[csf('id')]; ?>,'populate_data_from_planning_info', 'machine_wise_knitting_plan_controller' );"> 
					<td width="90"><p><? echo $knitting_source[$row[csf('knitting_source')]]; ?></p></td>
					<td width="100"><p><? echo $knit_party; ?></p></td>             
					<td width="90"><p><? echo $color_range[$row[csf('color_range')]]; ?></p></td>
					<td width="70"><p><? echo $row[csf('machine_dia')]; ?></p></td>
					<td width="70"><? echo $row[csf('machine_gg')]; ?></td>
					<td width="80" align="right"><? echo number_format($row[csf('program_qnty')],2); ?></td>
					<td width="75"><p><? echo $row[csf('stitch_length')]; ?></p></td>
					<td width="80"><p><? echo $row[csf('spandex_stitch_length')]; ?></p></td>
					<td width="70" align="right"><? echo number_format($row[csf('draft_ratio')],2); ?></td>
                    <td width="75" align="right"><? echo change_date_format($row[csf('program_date')]); ?></td>
					<td><p><? echo $knitting_program_status[$row[csf('status')]]; ?></p></td>
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

if($action=="populate_data_from_planning_info")
{
	$machine_arr=return_library_array( "select id, machine_no from lib_machine_name",'id','machine_no');
	$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
	$sql="select id, knitting_source, knitting_party, color_id, color_range, machine_dia, width_dia_type, machine_gg, fabric_dia, program_qnty, stitch_length, spandex_stitch_length, draft_ratio, machine_id, machine_capacity, distribution_qnty, status, start_date, end_date, program_date, feeder, remarks, save_data, no_fo_feeder_data, location_id, advice from ppl_planning_info_entry_dtls where id=$data";
	$data_array=sql_select($sql);
	foreach ($data_array as $row)
	{ 
		echo "document.getElementById('cbo_knitting_source').value 			= '".$row[csf("knitting_source")]."';\n";
		
		echo "load_drop_down('machine_wise_knitting_plan_controller', ".$row[csf("knitting_source")]."+'**'+".$row[csf("knitting_party")]."+'**1', 'load_drop_down_knitting_party','knitting_party');\n";
		
		$color='';
		$color_id=explode(",",$row[csf("color_id")]);
		foreach($color_id as $val)
		{
			if($color=="") $color=$color_library[$val]; else $color.=",".$color_library[$val];
		}
		
		echo "document.getElementById('knitting_party').value 				= '".$row[csf("knitting_party")]."';\n";
		echo "document.getElementById('txt_color').value 					= '".$color."';\n";
		echo "document.getElementById('hidden_color_id').value 				= '".$row[csf("color_id")]."';\n";
		echo "document.getElementById('cbo_color_range').value 				= '".$row[csf("color_range")]."';\n";
		echo "document.getElementById('txt_machine_dia').value 				= '".$row[csf("machine_dia")]."';\n";
		echo "document.getElementById('cbo_dia_width_type').value 			= '".$row[csf("width_dia_type")]."';\n";
		echo "document.getElementById('txt_machine_gg').value 				= '".$row[csf("machine_gg")]."';\n";
		echo "document.getElementById('txt_fabric_dia').value 				= '".$row[csf("fabric_dia")]."';\n";
		echo "document.getElementById('txt_program_qnty').value 			= '".$row[csf("program_qnty")]."';\n";
		echo "document.getElementById('txt_stitch_length').value 			= '".$row[csf("stitch_length")]."';\n";
		echo "document.getElementById('txt_spandex_stitch_length').value 	= '".$row[csf("spandex_stitch_length")]."';\n";
		echo "document.getElementById('txt_draft_ratio').value 				= '".$row[csf("draft_ratio")]."';\n";
		
		echo "active_inactive();\n";
		
		echo "document.getElementById('machine_id').value 					= '".$row[csf("machine_id")]."';\n";
		$machine_no='';
		$machine_id=explode(",",$row[csf("machine_id")]);
		foreach($machine_id as $val)
		{
			if($machine_no=='') $machine_no=$machine_arr[$val]; else $machine_no.=",".$machine_arr[$val];
		}
		
		echo "document.getElementById('txt_machine_no').value 				= '".$machine_no."';\n";
		echo "document.getElementById('txt_machine_capacity').value 		= '".$row[csf("machine_capacity")]."';\n";
		echo "document.getElementById('txt_distribution_qnty').value 		= '".$row[csf("distribution_qnty")]."';\n";

		echo "document.getElementById('cbo_knitting_status').value 			= '".$row[csf("status")]."';\n";
		echo "document.getElementById('txt_start_date').value 				= '".change_date_format($row[csf("start_date")])."';\n";
		echo "document.getElementById('txt_end_date').value 				= '".change_date_format($row[csf("end_date")])."';\n";
		echo "document.getElementById('txt_program_date').value 			= '".change_date_format($row[csf("program_date")])."';\n";
		echo "document.getElementById('cbo_feeder').value 					= '".$row[csf("feeder")]."';\n";
		echo "document.getElementById('txt_remarks').value 					= '".$row[csf("remarks")]."';\n";
		
		$save_data='';
		$data_machine_array=sql_select("select id, machine_id, dia, capacity, distribution_qnty, no_of_days, start_date, end_date from ppl_planning_info_machine_dtls where dtls_id='$data' and status_active=1 and is_deleted=0");
		foreach($data_machine_array as $row_m)
		{ 
			$start_date=change_date_format($row_m[csf("start_date")]);
			$end_date=change_date_format($row_m[csf("end_date")]);
			
			if($save_data=="")
			{
				$save_data=$row_m[csf("machine_id")]."_".$row_m[csf("dia")]."_".$row_m[csf("capacity")]."_".$row_m[csf("distribution_qnty")]."_".$row_m[csf("no_of_days")]."_".$start_date."_".$end_date."_".$row_m[csf("id")];
			}
			else
			{
				$save_data.=",".$row_m[csf("machine_id")]."_".$row_m[csf("dia")]."_".$row_m[csf("capacity")]."_".$row_m[csf("distribution_qnty")]."_".$row_m[csf("no_of_days")]."_".$start_date."_".$end_date."_".$row_m[csf("id")];
			}
		}
			
		echo "document.getElementById('save_data').value 					= '".$save_data."';\n";//$row[csf("save_data")]
		echo "document.getElementById('cbo_location_name').value 			= '".$row[csf("location_id")]."';\n";
		echo "document.getElementById('hidden_advice_data').value 			= '".$row[csf("advice")]."';\n";
		echo "document.getElementById('update_dtls_id').value 				= '".$row[csf("id")]."';\n";
		echo "document.getElementById('txt_program_no').value 				= '".$row[csf("id")]."';\n";
		echo "days_req();\n";
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_program_entry',1);\n";  
		exit();
	}
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
			$start_date = change_date_format( str_replace("'","",trim($start_date)),"yyyy-mm-dd","");
			$end_date = change_date_format( str_replace("'","",trim($end_date)),"yyyy-mm-dd","");
		}
	 	else
		{
			$start_date = change_date_format( str_replace("'","",trim($start_date)),'','',1);
			$end_date = change_date_format( str_replace("'","",trim($end_date)),'','',1);
		}
		
		$id='';
		
		if(str_replace("'",'',$updateId)=="")
		{
			$id=return_next_id( "id","ppl_planning_info_entry_mst", 1 ) ;
					 
			$field_array="id, company_id, within_group, buyer_id, booking_no, body_part_id, color_type_id, determination_id, fabric_desc, gsm_weight, dia, width_dia_type, is_sales, inserted_by, insert_date";
			$data_array="(".$id.",".$companyID.",".$within_group.",".$buyer_id.",'".$booking_no."',".$body_part_id.",".$color_type_id.",".$determination_id.",'".$desc."','".$gsm."','".$dia."',".$fabric_typee.",1,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		}
		else
		{
			$id=str_replace("'",'',$updateId);
			$flag=1;
		}
		//echo "10**".$flag;die;
		
		$dtls_id=return_next_id( "id","ppl_planning_info_entry_dtls", 1 ) ;
		$field_array_dtls="id, mst_id, knitting_source, knitting_party, color_id, color_range, machine_dia, width_dia_type, machine_gg, fabric_dia, program_qnty, stitch_length, spandex_stitch_length, draft_ratio,  machine_id, machine_capacity, distribution_qnty, status, start_date, end_date, program_date, feeder, remarks, save_data, location_id, advice, is_sales, inserted_by, insert_date"; 
		
		$data_array_dtls="(".$dtls_id.",".$id.",".$cbo_knitting_source.",".$cbo_knitting_party.",".$hidden_color_id.",".$cbo_color_range.",".$txt_machine_dia.",".$cbo_dia_width_type.",".$txt_machine_gg.",".$txt_fabric_dia.",".$txt_program_qnty.",".$txt_stitch_length.",".$txt_spandex_stitch_length.",".$txt_draft_ratio.",".$machine_id.",".$txt_machine_capacity.",".$txt_distribution_qnty.",".$cbo_knitting_status.",".$txt_start_date.",".$txt_end_date.",".$txt_program_date.",".$cbo_feeder.",".$txt_remarks.",".$save_data.",".$cbo_location_name.",".$hidden_advice_data.",1,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')"; 
		
		$plan_dtls_id=return_next_id( "id","ppl_planning_entry_plan_dtls", 1 ) ;
		$field_array_plan_dtls="id, mst_id, dtls_id, company_id, within_group, buyer_id, booking_no, po_id, body_part_id, color_type_id, determination_id, fabric_desc, gsm_weight, dia, width_dia_type, color_id, yarn_desc, program_qnty, is_sales, inserted_by, insert_date";
		
		$data=str_replace("'","",$data);
		if($data!="")
		{
			$data=explode("_",$data);
			for($i=0;$i<count($data);$i++)
			{
				$plan_data=explode("**",$data[$i]);	
				$booking_no=$plan_data[0];
				$job_id=$plan_data[1];
				$withinGroup=$plan_data[2];
				$job_dtls_id=$plan_data[3];
				$buyer_id=$plan_data[4];
				$body_part_id=$plan_data[5];
				$dia_width_type=$plan_data[6];
				$desc=$plan_data[7];
				$gsm_weight=$plan_data[8];
				$dia_width=$plan_data[9];
				$determination_id=$plan_data[10];
				$booking_qnty=$plan_data[11];
				$color_type_id=$plan_data[12];
				
				if($db_type==0)
				{
					$start_date=change_date_format($start_date, "yyyy-mm-dd", "-");
					$end_date=change_date_format($end_date, "yyyy-mm-dd", "-");
				}
				else
				{
					$start_date=change_date_format($start_date,'','',1);
					$end_date=change_date_format($end_date,'','',1);
				}
				
				$perc=($booking_qnty/$tot_booking_qnty)*100;
				$prog_qnty=($perc*str_replace("'",'',$txt_program_qnty))/100;
				
				if($data_array_plan_dtls!="") $data_array_plan_dtls.=",";
					
				$data_array_plan_dtls.="(".$plan_dtls_id.",".$id.",".$dtls_id.",".$companyID.",".$withinGroup.",".$buyer_id.",'".$booking_no."',".$job_id.",".$body_part_id.",".$color_type_id.",".$determination_id.",'".$desc."',".$gsm.",'".$dia_width."',".$dia_width_type.",0,".$job_dtls_id.",".$prog_qnty.",1,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				
				$plan_dtls_id=$plan_dtls_id+1;
			}
		}
		
		$machine_dtls_id=return_next_id( "id","ppl_planning_info_machine_dtls", 1 ) ;
		$field_array_machine_dtls="id, mst_id, dtls_id, machine_id, dia, capacity, distribution_qnty, no_of_days, start_date, end_date, is_sales, inserted_by, insert_date";
		
		$machine_dtls_datewise_id=return_next_id( "id","ppl_entry_machine_datewise", 1 ) ;
		$field_array_machine_dtls_datewise="id, mst_id, dtls_id, machine_id, distribution_date, fraction_date, days_complete, qnty, machine_plan_id, is_sales, inserted_by, insert_date";
		
		$save_data=str_replace("'","",$save_data);
		if($save_data!="")
		{
			$save_data=explode(",",$save_data);
			for($i=0;$i<count($save_data);$i++)
			{
				$machine_wise_data=explode("_",$save_data[$i]);	
				$machine_id=$machine_wise_data[0];
				$dia=$machine_wise_data[1];
				$capacity=$machine_wise_data[2];
				$qnty=$machine_wise_data[3];
				$noOfDays=$machine_wise_data[4];
				
				$dateWise_qnty=0; $bl_qnty=$qnty;
				
				if($machine_wise_data[5]!="") $startDate=date("Y-m-d",strtotime($machine_wise_data[5]));
				if($machine_wise_data[6]!="") $endDate=date("Y-m-d",strtotime($machine_wise_data[6]));
				
				if($startDate!="" && $endDate!="")
				{
					$sCurrentDate=date("Y-m-d",strtotime("-1 day", strtotime($startDate))); $days=$noOfDays; $fraction=0; $days_complete=0;
					while($sCurrentDate < $endDate)
					{ 
						$sCurrentDate = date("Y-m-d", strtotime("+1 day", strtotime($sCurrentDate))); 
						if($days>=1)
						{
							$fraction=0;
							$days_complete=1;
							$dateWise_qnty=$capacity;
						}
						else 
						{
							$fraction=1;
							$days_complete=$days;
							$dateWise_qnty=$bl_qnty;
						}
						
						$days=$days-1;
						$bl_qnty=$bl_qnty-$capacity;
						
						if($db_type==0) $curr_date=$sCurrentDate; else $curr_date=change_date_format($sCurrentDate,'','',1);  
						
						if($data_array_machine_dtls_datewise!="") $data_array_machine_dtls_datewise.=",";
						$data_array_machine_dtls_datewise.="(".$machine_dtls_datewise_id.",".$id.",".$dtls_id.",'".$machine_id."','".$curr_date."','".$fraction."','".$days_complete."','".$dateWise_qnty."','".$machine_dtls_id."',1,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')"; 
						$machine_dtls_datewise_id=$machine_dtls_datewise_id+1;
					}
				}
				
				if($db_type==0)
				{
					$mstartDate=$startDate;
					$mendDate=$endDate;
				}
				else
				{
					$mstartDate=change_date_format($startDate,'','',1);
					$mendDate=change_date_format($endDate,'','',1);
				}
				
				if($data_array_machine_dtls!="") $data_array_machine_dtls.=",";
				$data_array_machine_dtls.="(".$machine_dtls_id.",".$id.",".$dtls_id.",'".$machine_id."','".$dia."','".$capacity."','".$qnty."','".$noOfDays."','".$mstartDate."','".$mendDate."',1,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')"; 
				$machine_dtls_id=$machine_dtls_id+1;
			}
		}
		
		/*$feeder_dtls_id=return_next_id( "id","ppl_planning_feeder_dtls", 1 ) ;
		$field_array_feeder_dtls="id, mst_id, dtls_id, pre_cost_id, color_id, stripe_color_id, no_of_feeder, inserted_by, insert_date";
		
		$hidden_no_of_feeder_data=str_replace("'","",$hidden_no_of_feeder_data);
		if($hidden_no_of_feeder_data!="")
		{
			$hidden_no_of_feeder_data=explode(",",$hidden_no_of_feeder_data);
			for($i=0;$i<count($hidden_no_of_feeder_data);$i++)
			{
				$color_wise_data=explode("_",$hidden_no_of_feeder_data[$i]);	
				$pre_cost_id=$color_wise_data[0];
				$color_id=$color_wise_data[1];
				$stripe_color_id=$color_wise_data[2];
				$no_of_feeder=$color_wise_data[3];
				
				if($data_array_feeder_dtls!="") $data_array_feeder_dtls.=",";
					
				$data_array_feeder_dtls.="(".$feeder_dtls_id.",".$id.",".$dtls_id.",'".$pre_cost_id."','".$color_id."','".$stripe_color_id."','".$no_of_feeder."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')"; 
				
				$feeder_dtls_id=$feeder_dtls_id+1;
			}
		}*/
		
		if(str_replace("'",'',$updateId)=="")
		{
			$rID=sql_insert("ppl_planning_info_entry_mst",$field_array,$data_array,0);
			if($rID) $flag=1; else $flag=0;
		}
		else
		{
			$flag=1;
		}
		
		//echo "10**insert into ppl_planning_info_entry_dtls (".$field_array_dtls.") Values ".$data_array_dtls."";die;
		$rID2=sql_insert("ppl_planning_info_entry_dtls",$field_array_dtls,$data_array_dtls,1);
		if($flag==1) 
		{
			if($rID2) $flag=1; else $flag=0; 
		} 
		
		if($data!="")
		{
			if($data_array_plan_dtls!="")
			{
				//echo "10**insert into ppl_planning_entry_plan_dtls (".$field_array_plan_dtls.") Values ".$data_array_plan_dtls."";die;
				$rIDdtls=sql_insert("ppl_planning_entry_plan_dtls",$field_array_plan_dtls,$data_array_plan_dtls,0);
				if($flag==1) 
				{
					if($rIDdtls) $flag=1; else $flag=0; 
				}  
			}
		}
		
		if($save_data!="")
		{
			if($data_array_machine_dtls!="")
			{
				//echo "10**insert into ppl_planning_info_machine_dtls (".$field_array_machine_dtls.") Values ".$data_array_machine_dtls."";die;
				$rID3=sql_insert("ppl_planning_info_machine_dtls",$field_array_machine_dtls,$data_array_machine_dtls,0);
				if($flag==1) 
				{
					if($rID3) $flag=1; else $flag=0; 
				}  
			}
			
			if($data_array_machine_dtls_datewise!="")
			{
				//echo "10**insert into ppl_entry_machine_datewise (".$field_array_machine_dtls_datewise.") Values ".$data_array_machine_dtls_datewise."";die;
				$rID4=sql_insert("ppl_entry_machine_datewise",$field_array_machine_dtls_datewise,$data_array_machine_dtls_datewise,0);
				if($flag==1) 
				{
					if($rID4) $flag=1; else $flag=0; 
				}  
			}
		}
		//echo "10**".$flag;die;
		/*if($hidden_no_of_feeder_data!="")
		{
			if($data_array_feeder_dtls!="")
			{
				//echo "10**insert into ppl_planning_feeder_dtls (".$field_array_feeder_dtls.") Values ".$data_array_feeder_dtls."";die;
				$rID5=sql_insert("ppl_planning_feeder_dtls",$field_array_feeder_dtls,$data_array_feeder_dtls,0);
				if($flag==1) 
				{
					if($rID5) $flag=1; else $flag=0; 
				}  
			} 
		}*/
		
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "0**".$id."**0";
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "5**0**0";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				oci_commit($con);
				echo "0**".$id."**0";
			}
			else
			{
				oci_rollback($con);
				echo "5**0**0";
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
		
		$color_id=0;
		$field_array_update="knitting_source*knitting_party*color_id*color_range*machine_dia*width_dia_type*machine_gg*fabric_dia*program_qnty*stitch_length*spandex_stitch_length*draft_ratio*machine_id*machine_capacity*distribution_qnty*status*start_date*end_date*program_date*feeder*remarks*save_data*location_id*advice*updated_by*update_date";
		
		$data_array_update=$cbo_knitting_source."*".$cbo_knitting_party."*".$hidden_color_id."*".$cbo_color_range."*".$txt_machine_dia."*".$cbo_dia_width_type."*".$txt_machine_gg."*".$txt_fabric_dia."*".$txt_program_qnty."*".$txt_stitch_length."*".$txt_spandex_stitch_length."*".$txt_draft_ratio."*".$machine_id."*".$txt_machine_capacity."*".$txt_distribution_qnty."*".$cbo_knitting_status."*".$txt_start_date."*".$txt_end_date."*".$txt_program_date."*".$cbo_feeder."*".$txt_remarks."*".$save_data."*".$cbo_location_name."*".$hidden_advice_data."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		
		$plan_dtls_id=return_next_id( "id","ppl_planning_entry_plan_dtls", 1 ) ;
		
		$field_array_plan_dtls="id, mst_id, dtls_id, company_id, within_group, buyer_id, booking_no, po_id, body_part_id, color_type_id, determination_id, fabric_desc, gsm_weight, dia, width_dia_type, color_id, yarn_desc, program_qnty, is_sales, inserted_by, insert_date";
		
		$data=str_replace("'","",$data);
		if($data!="")
		{
			$data=explode("_",$data);
			for($i=0;$i<count($data);$i++)
			{
				$plan_data=explode("**",$data[$i]);	
				$booking_no=$plan_data[0];
				$job_id=$plan_data[1];
				$withinGroup=$plan_data[2];
				$job_dtls_id=$plan_data[3];
				$buyer_id=$plan_data[4];
				$body_part_id=$plan_data[5];
				$dia_width_type=$plan_data[6];
				$desc=$plan_data[7];
				$gsm_weight=$plan_data[8];
				$dia_width=$plan_data[9];
				$determination_id=$plan_data[10];
				$booking_qnty=$plan_data[11];
				$color_type_id=$plan_data[12];
				
				$perc=($booking_qnty/$tot_booking_qnty)*100;
				$prog_qnty=($perc*str_replace("'",'',$txt_program_qnty))/100;
				
				if($db_type==0)
				{
					$start_date=change_date_format($start_date, "yyyy-mm-dd", "-");
					$end_date=change_date_format($end_date, "yyyy-mm-dd", "-");
				}
				else
				{
					$start_date=change_date_format($start_date,'','',1);
					$end_date=change_date_format($end_date,'','',1);
				}
				
				if($data_array_plan_dtls!="") $data_array_plan_dtls.=",";
					
				$data_array_plan_dtls.="(".$plan_dtls_id.",".$updateId.",".$update_dtls_id.",".$companyID.",".$withinGroup.",".$buyer_id.",'".$booking_no."',".$job_id.",".$body_part_id.",".$color_type_id.",".$determination_id.",'".$desc."',".$gsm.",'".$dia_width."',".$dia_width_type.",0,".$job_dtls_id.",".$prog_qnty.",1,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				
				$plan_dtls_id=$plan_dtls_id+1;
			}
		}
		
		$machine_dtls_id=return_next_id( "id","ppl_planning_info_machine_dtls", 1 ) ;
		$field_array_machine_dtls="id, mst_id, dtls_id, machine_id, dia, capacity, distribution_qnty, no_of_days, start_date, end_date, is_sales, inserted_by, insert_date";
		$field_array_machine_dtls_update="machine_id*dia*capacity*distribution_qnty*no_of_days*start_date*end_date*updated_by*update_date";
		
		$machine_dtls_datewise_id=return_next_id( "id","ppl_entry_machine_datewise", 1 ) ;
		$field_array_machine_dtls_datewise="id, mst_id, dtls_id, machine_id, distribution_date, fraction_date, days_complete, qnty, machine_plan_id, is_sales, inserted_by, insert_date";
		
		$save_data=str_replace("'","",$save_data);
		if($save_data!="")
		{
			$save_data=explode(",",$save_data);
			for($i=0;$i<count($save_data);$i++)
			{
				$machine_wise_data=explode("_",$save_data[$i]);	
				$machine_id=$machine_wise_data[0];
				$dia=$machine_wise_data[1];
				$capacity=$machine_wise_data[2];
				$qnty=$machine_wise_data[3];
				$noOfDays=$machine_wise_data[4];
				$dtls_id=$machine_wise_data[7];
				
				$dateWise_qnty=0; $bl_qnty=$qnty;
				
				if($machine_wise_data[5]!="") $startDate=date("Y-m-d",strtotime($machine_wise_data[5]));
				if($machine_wise_data[6]!="") $endDate=date("Y-m-d",strtotime($machine_wise_data[6]));
				
				if($db_type==0)
				{
					$mstartDate=$startDate;
					$mendDate=$endDate;
				}
				else
				{
					$mstartDate=change_date_format($startDate,'','',1);
					$mendDate=change_date_format($endDate,'','',1);
				}
				
				if($dtls_id=="")
				{
					if($data_array_machine_dtls!="") $data_array_machine_dtls.=",";
					$data_array_machine_dtls.="(".$machine_dtls_id.",".$updateId.",".$update_dtls_id.",'".$machine_id."','".$dia."','".$capacity."','".$qnty."','".$noOfDays."','".$mstartDate."','".$mendDate."',1,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')"; 
					
					$machine_plan_id=$machine_dtls_id;
					$machine_dtls_id=$machine_dtls_id+1;
				}
				else
				{
					$dtlsId_arr[]=$dtls_id;
					$data_array_update_dtls[$dtls_id]=explode("*",($machine_id."*'".$dia."'*'".$capacity."'*'".$qnty."'*'".$noOfDays."'*'".$mstartDate."'*'".$mendDate."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
					$machine_plan_id=$dtls_id;
				}
				
				if($startDate!="" && $endDate!="")
				{
					$sCurrentDate=date("Y-m-d",strtotime("-1 day", strtotime($startDate))); $days=$noOfDays; $fraction=0; $days_complete=0;
					
					while($sCurrentDate < $endDate)
					{  
						$sCurrentDate = date("Y-m-d", strtotime("+1 day", strtotime($sCurrentDate))); 
						
						if($days>=1)
						{
							$fraction=0;
							$days_complete=1;
							$dateWise_qnty=$capacity;
						}
						else 
						{
							$fraction=1;
							$days_complete=$days;
							$dateWise_qnty=$bl_qnty;
						}

						$days=$days-1;
						$bl_qnty=$bl_qnty-$capacity;
						
						if($db_type==0) $curr_date=$sCurrentDate; else $curr_date=change_date_format($sCurrentDate,'','',1); 
						
						if($data_array_machine_dtls_datewise!="") $data_array_machine_dtls_datewise.=",";
						
						$data_array_machine_dtls_datewise.="(".$machine_dtls_datewise_id.",".$updateId.",".$update_dtls_id.",'".$machine_id."','".$curr_date."','".$fraction."','".$days_complete."','".$dateWise_qnty."','".$machine_plan_id."',1,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')"; 
						$machine_dtls_datewise_id=$machine_dtls_datewise_id+1;
					}
				}
			}
		}
		
		/*$feeder_dtls_id=return_next_id( "id","ppl_planning_feeder_dtls", 1 ) ;
		$field_array_feeder_dtls="id, mst_id, dtls_id, pre_cost_id, color_id, stripe_color_id, no_of_feeder, inserted_by, insert_date";
		
		$hidden_no_of_feeder_data=str_replace("'","",$hidden_no_of_feeder_data);
		if($hidden_no_of_feeder_data!="")
		{
			$hidden_no_of_feeder_data=explode(",",$hidden_no_of_feeder_data);
			for($i=0;$i<count($hidden_no_of_feeder_data);$i++)
			{
				$color_wise_data=explode("_",$hidden_no_of_feeder_data[$i]);	
				$pre_cost_id=$color_wise_data[0];
				$color_id=$color_wise_data[1];
				$stripe_color_id=$color_wise_data[2];
				$no_of_feeder=$color_wise_data[3];
				
				if($data_array_feeder_dtls!="") $data_array_feeder_dtls.=",";
					
				$data_array_feeder_dtls.="(".$feeder_dtls_id.",".$updateId.",".$update_dtls_id.",'".$pre_cost_id."','".$color_id."','".$stripe_color_id."','".$no_of_feeder."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')"; 
				
				$feeder_dtls_id=$feeder_dtls_id+1;
			}
		}*/
		
		//Query Execution Start
		$delete=execute_query( "delete from ppl_planning_entry_plan_dtls where dtls_id=$update_dtls_id",0);
		if($delete) $flag=1; else $flag=0;
		
		$rID=sql_update("ppl_planning_info_entry_dtls",$field_array_update,$data_array_update,"id",$update_dtls_id,1);
		if($flag==1) 
		{
			if($rID) $flag=1; else $flag=0; 
		} 
		
		if($data!="")
		{
			if($data_array_plan_dtls!="")
			{
			//	echo "10**insert into ppl_planning_entry_plan_dtls (".$field_array_plan_dtls.") Values ".$data_array_plan_dtls."";die;
				$rID2=sql_insert("ppl_planning_entry_plan_dtls",$field_array_plan_dtls,$data_array_plan_dtls,0);
				if($flag==1) 
				{
					if($rID2) $flag=1; else $flag=0; 
				}  
			}
		}
			
		/*$deletem=execute_query( "delete from ppl_planning_info_machine_dtls where dtls_id=$update_dtls_id",0);
		if($flag==1) 
		{
			if($deletem) $flag=1; else $flag=0; 
		}*/
		
		$delete_datewise=execute_query( "delete from ppl_entry_machine_datewise where dtls_id=$update_dtls_id",0);
		if($flag==1) 
		{
			if($delete_datewise) $flag=1; else $flag=0; 
		}
		
		if($save_data!="")
		{
			if($data_array_machine_dtls!="")
			{
				//echo"insert into ppl_planning_info_machine_dtls (".$field_array_machine_dtls.") Values ".$data_array_machine_dtls."";die;
				$rID3=sql_insert("ppl_planning_info_machine_dtls",$field_array_machine_dtls,$data_array_machine_dtls,0);
				if($flag==1) 
				{
					if($rID3) $flag=1; else $flag=0; 
				}  
			}
			
			if(count($data_array_update_dtls)>0)
			{
				$rID_update=execute_query(bulk_update_sql_statement("ppl_planning_info_machine_dtls","id",$field_array_machine_dtls_update, $data_array_update_dtls,$dtlsId_arr ));
				if($flag==1)
				{
					if($rID_update) $flag=1; else $flag=0;
				}
			}
			
			if($data_array_machine_dtls_datewise!="")
			{
				//echo "10**insert into ppl_entry_machine_datewise (".$field_array_machine_dtls_datewise.") Values ".$data_array_machine_dtls_datewise."";die;
				$rID4=sql_insert("ppl_entry_machine_datewise",$field_array_machine_dtls_datewise,$data_array_machine_dtls_datewise,0);
				if($flag==1) 
				{
					if($rID4) $flag=1; else $flag=0; 
				} 
			}
		}
		
		/*$delete_feeder=execute_query( "delete from ppl_planning_feeder_dtls where dtls_id=$update_dtls_id",0);
		if($flag==1) 
		{
			if($delete_feeder) $flag=1; else $flag=0; 
		}
		
		if($hidden_no_of_feeder_data!="")
		{
			if($data_array_feeder_dtls!="")
			{
				//echo "10**insert into ppl_planning_feeder_dtls (".$field_array_feeder_dtls.") Values ".$data_array_feeder_dtls."";die;
				$rID5=sql_insert("ppl_planning_feeder_dtls",$field_array_feeder_dtls,$data_array_feeder_dtls,0);
				if($flag==1) 
				{
					if($rID5) $flag=1; else $flag=0; 
				}  
			}
		}*/
		
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'","",$updateId)."**0";
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "6**0**1";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				oci_commit($con);  
				echo "1**".str_replace("'","",$updateId)."**0";
			}
			else
			{
				oci_rollback($con); 
				echo "6**0**1";
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==2)
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		$field_array_update="status_active*is_deleted*updated_by*update_date";
		
		$data_array_update="0*1*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		
		$rID=sql_update("ppl_planning_info_entry_dtls",$field_array_update,$data_array_update,"id",$update_dtls_id,0);
		if($rID) $flag=1; else $flag=0;
		
		$rID2=sql_update("ppl_planning_entry_plan_dtls",$field_array_update,$data_array_update,"dtls_id",$update_dtls_id,0);
		if($flag==1)
		{
			if($rID2) $flag=1; else $flag=0; 
		}
		
		$delete=execute_query( "delete from ppl_planning_info_machine_dtls where dtls_id=$update_dtls_id",0);
		if($flag==1) 
		{
			if($delete) $flag=1; else $flag=0; 
		}
		
		$delete_datewise=execute_query( "delete from ppl_entry_machine_datewise where dtls_id=$update_dtls_id",0);
		if($flag==1) 
		{
			if($delete_datewise) $flag=1; else $flag=0; 
		}
		
		/*$delete_feeder=execute_query( "delete from ppl_planning_feeder_dtls where dtls_id=$update_dtls_id",1);
		if($flag==1) 
		{
			if($delete_feeder) $flag=1; else $flag=0; 
		}*/
		
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "2**".str_replace("'","",$updateId)."**0";
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "7**0**1";
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				oci_commit($con);
				echo "2**".str_replace("'","",$updateId)."**0";
			}
			else
			{
				oci_rollback($con);
				echo "7**0**1";
			}
		}
		disconnect($con);
		die;
	}
}

if($action=="advice_info_popup")
{
	echo load_html_head_contents("Advice Info", "../../", 1, 1,'','','');
	extract($_REQUEST); 
	?>
    
</head>

<body>
<div style="width:430px;" align="center">
	<form name="searchwofrm"  id="searchwofrm">
		<fieldset style="width:400px; margin-top:10px;">
        	<input type="hidden" name="hidden_advice_data" id="hidden_advice_data" class="text_boxes" value="">
             <table cellspacing="0" cellpadding="0" border="1" rules="all" width="400" class="rpt_table" >
                <tr>
               		<td><textarea name="txt_advice" id="txt_advice" class="text_area" style="width:385px; height:120px;"><? echo $hidden_advice_data; ?></textarea></td>
                </tr>
            </table>
            <table width="400" id="tbl_close">
                 <tr>
                    <td align="center" >
                        <input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="parent.emailwindow.hide();" style="width:100px" />
                    </td>
                </tr>
            </table>
		</fieldset>
	</form>
</div>
</body>           
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
	exit();
}

if($action=="machine_info_popup")
{
	echo load_html_head_contents("Machine Info", "../../../", 1, 1,'','','');
	extract($_REQUEST); 
	?>
    
    <script>
	
		$(document).ready(function(e) {
            setFilterGrid('tbl_list_search',-1);
        });
		
		function calculate_qnty(tr_id)
		{
			var distribution_qnty=$('#txt_distribution_qnty_'+tr_id).val()*1;
			if(distribution_qnty>0)
			{
				$('#search' + tr_id).css('background-color','yellow');
			}
			else
			{
				$('#search' + tr_id).css('background-color','#FFFFCC');
			}
			
			calculate_total_qnty('txt_distribution_qnty_','txt_total_distribution_qnty');
		}
		
		function calculate_total_qnty(field_id,total_field_id)
		{
			var tot_row=$("#tbl_list_search tbody tr").length-1;

			var ddd={ dec_type:2, comma:0, currency:''}
			
			math_operation( total_field_id, field_id, "+", tot_row,ddd );

		}
		
		function fnc_close()
		{
			var save_string=''; var allMachineId=''; var allMachineNo=''; var tot_capacity=''; var tot_distribution_qnty=''; var min_date=''; var max_date='';
			var tot_row=$("#tbl_list_search tbody tr").length-1;
			
			for(var i=1; i<=tot_row; i++)
			{
				var machineId=$('#txt_individual_id'+i).val();
				var machineNo=$('#txt_individual'+i).val();
				var capacity=$('#txt_capacity_'+i).val();
				var distributionQnty=$('#txt_distribution_qnty_'+i).val();
				var noOfDays=$('#txt_noOfDays_'+i).val();
				var startDate=$('#txt_startDate_'+i).val();
				var endDate=$('#txt_endDate_'+i).val();
				var dtls_id=$('#dtls_id_'+i).val();
				
				if(distributionQnty*1>0)
				{
					if(save_string=="")
					{
						save_string=machineId+"_"+machineNo+"_"+capacity+"_"+distributionQnty+"_"+noOfDays+"_"+startDate+"_"+endDate+"_"+dtls_id;
						allMachineId=machineId;
						allMachineNo=machineNo;
					}
					else
					{
						save_string+=","+machineId+"_"+machineNo+"_"+capacity+"_"+distributionQnty+"_"+noOfDays+"_"+startDate+"_"+endDate+"_"+dtls_id;
						allMachineId+=","+machineId;
						allMachineNo+=","+machineNo;
					}
					
					if(min_date=='')
					{
						min_date=startDate;
					}
					
					if(date_compare(min_date, startDate )==false)
					{
						min_date=startDate;
					}
					
					if(date_compare(min_date, endDate )==false)
					{
						min_date=endDate;
					}
					
					if(max_date=='')
					{
						max_date=startDate;
					}
					
					if(date_compare(max_date, startDate)==true)
					{
						max_date=startDate;
					}
					
					if(date_compare(max_date, endDate)==true)
					{
						max_date=endDate;
					}
					
					tot_capacity=tot_capacity*1+capacity*1;
					tot_distribution_qnty=tot_distribution_qnty*1+distributionQnty*1;
				}
			}
			
			$('#hidden_machine_id').val(allMachineId);	
			$('#hidden_machine_no').val(allMachineNo);	
			$('#save_string').val( save_string );
			$('#hidden_machine_capacity').val( tot_capacity );
			$('#hidden_distribute_qnty').val( tot_distribution_qnty );
			$('#hidden_min_date').val( min_date );
			$('#hidden_max_date').val( max_date );
			
			parent.emailwindow.hide();
		}
		
		function fn_add_date_field(row_no)
		{
			var distribute_qnty=$('#txt_distribution_qnty_'+row_no).val()*1;
			
			if(distribute_qnty==0 || distribute_qnty<0) 
			{
				alert("Please Insert Distribution Qnty First.");
				$('#txt_startDate_'+row_no).val('');
				$('#txt_distribution_qnty_'+row_no).focus();
				return;
			}

			if($('#txt_startDate_'+row_no).val()!="")
			{
				var days_req=$('#txt_noOfDays_'+row_no).val();
				
				days_req=Math.ceil(days_req);
				if(days_req>0)
				{
					days_req=days_req-1;
					$("#txt_endDate_"+row_no).val(add_days($('#txt_startDate_'+row_no).val(),days_req));
				}
				
				var txt_startDate=$('#txt_startDate_'+row_no).val();
				var txt_endDate=$('#txt_endDate_'+row_no).val();
				var machine_id=$('#txt_individual_id'+row_no).val();
				
				var data=machine_id+"**"+txt_startDate+"**"+txt_endDate+"**"+'<? echo $update_dtls_id; ?>';
				var response=return_global_ajax_value( data, 'date_duplication_check', '', 'machine_wise_knitting_plan_controller');
				var response=response.split("_");
				//alert(response);return;
				if(response[0]!=0)
				{
					alert("Date Overlaping for this machine. Dates Are ("+response[1]+").");
					$('#txt_startDate_'+row_no).val('');
					$('#txt_endDate_'+row_no).val('');
					return;
				}
			}
		}
		
		function calculate_noOfDays(row_no)
		{
			var distribute_qnty=$('#txt_distribution_qnty_'+row_no).val();
			var machine_capacity=$('#txt_capacity_'+row_no).val();
			
			var days_req=distribute_qnty*1/machine_capacity*1;
			$('#txt_noOfDays_'+row_no).val(days_req.toFixed(2));
			
			if(distribute_qnty*1>0)
			{
				fn_add_date_field(row_no);
			}
			else
			{
				$('#txt_noOfDays_'+row_no).val('');
				$('#txt_startDate_'+row_no).val('');
				$('#txt_endDate_'+row_no).val('');
			}
		}
		
    </script>

</head>

<body>
<div style="width:830px;">
	<form name="searchwofrm"  id="searchwofrm">
		<fieldset style="width:820px; margin-top:10px; margin-left:5px">
        	<input type="hidden" name="save_string" id="save_string" class="text_boxes" value="">
         	<input type="hidden" name="hidden_machine_id" id="hidden_machine_id" class="text_boxes" value=""> 
            <input type="hidden" name="hidden_machine_no" id="hidden_machine_no" class="text_boxes" value="">  
        	<input type="hidden" name="hidden_machine_capacity" id="hidden_machine_capacity" class="text_boxes" value="">   
            <input type="hidden" name="hidden_distribute_qnty" id="hidden_distribute_qnty" class="text_boxes" value="">  
            <input type="hidden" name="hidden_min_date" id="hidden_min_date" class="text_boxes" value="">   
			<input type="hidden" name="hidden_max_date" id="hidden_max_date" class="text_boxes" value="">      
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="800" class="rpt_table" >
                <thead>
                    <th width="40">SL</th>
                    <th width="80">Floor</th> 
                    <th width="60">Machine No</th>               
                    <th width="60">Dia</th>
                    <th width="60">GG</th>
                    <th width="80">Group</th>
                    <th width="90">Capacity</th>
                    <th width="90">Distribution Qnty</th> 
                    <th width="60">No. Of Days</th> 
                    <th width="80">Start Date</th> 
                    <th>End Date</th> 
                </thead>
            </table>
            <div style="width:818px; overflow-y:scroll; max-height:220px;" id="buyer_list_view">
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="800" class="rpt_table" id="tbl_list_search">
                    <tbody>
                    <?
                        $qnty_array=array();
                        $save_string=explode(",",$save_string);
        
                        for($i=0;$i<count($save_string);$i++)
                        {
                            $machine_wise_data=explode("_",$save_string[$i]);
                            $machine_id=$machine_wise_data[0];
                            $capacity=$machine_wise_data[2];
                            $distribution_qnty=$machine_wise_data[3];
                            $noOfDays=$machine_wise_data[4];
                            $startDate=$machine_wise_data[5];
                            $endDate=$machine_wise_data[6];
							$dtls_id=$machine_wise_data[7];
                            
                            $qnty_array[$machine_id]['capacity']=$capacity;
                            $qnty_array[$machine_id]['distribution']=$distribution_qnty;
                            $qnty_array[$machine_id]['noOfDays']=$noOfDays;
                            $qnty_array[$machine_id]['startDate']=$startDate;
                            $qnty_array[$machine_id]['endDate']=$endDate;
							$qnty_array[$machine_id]['dtls_id']=$dtls_id;
                        }
						
                        $floor_arr=return_library_array( "select id, floor_name from lib_prod_floor",'id','floor_name');
						
                        $sql="select id, machine_no, dia_width, gauge, machine_group, prod_capacity, floor_id from lib_machine_name where company_id=$companyID and category_id=1 and status_active=1 and is_deleted=0 order by seq_no";// and dia_width='$txt_machine_dia'
                        $result = sql_select($sql);
                        
                        $i=1; $tot_capacity=0; $tot_distribution_qnty=0;
                        foreach($result as $row)
                        {
                            if ($i%2==0)  
                                $bgcolor="#E9F3FF";
                            else
                                $bgcolor="#FFFFFF";
                            
                            $capacity=$qnty_array[$row[csf('id')]]['capacity'];
                            if($capacity=="")
                            {
                                $capacity=$row[csf('prod_capacity')];
                            }
                            
                            $distribution_qnty=$qnty_array[$row[csf('id')]]['distribution'];
                            
                            if($distribution_qnty>0) $bgcolor="yellow"; else $bgcolor=$bgcolor;
                            
                            $noOfDays=$qnty_array[$row[csf('id')]]['noOfDays'];
                            $startDate=$qnty_array[$row[csf('id')]]['startDate'];
                            $endDate=$qnty_array[$row[csf('id')]]['endDate'];
							$dtls_id=$qnty_array[$row[csf('id')]]['dtls_id'];
                            
                            $tot_capacity+=$capacity; 
                            $tot_distribution_qnty+=$distribution_qnty;
                            
                            ?>
                            <tr valign="middle" bgcolor="<? echo $bgcolor; ?>" id="search<? echo $i;?>"> 
                                <td width="40" align="center"><? echo $i; ?>
                                    <input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i ?>" value="<? echo $row[csf('id')]; ?>"/> 
                                    <input type="hidden" name="txt_individual" id="txt_individual<?php echo $i ?>" value="<? echo $row[csf('machine_no')]; ?>"/> 
                                </td>	
                                <td width="80"><p><? echo $floor_arr[$row[csf('floor_id')]]; ?></p></td>     
                                <td width="60"><p><? echo $row[csf('machine_no')]; ?></p></td>               
                                <td width="60" align="center"><p><? echo $row[csf('dia_width')]; ?></p></td>
                                <td width="60" align="center"><p><? echo $row[csf('gauge')]; ?></p></td>
                                <td width="80" align="center"><p><? echo $row[csf('machine_group')]; ?></p></td>
                                <td width="90" align="center">
                                     <input type="text" name="txt_capacity[]" id="txt_capacity_<? echo $i; ?>" class="text_boxes_numeric" style="width:75px" value="<? echo $capacity; ?>" onKeyUp="calculate_total_qnty('txt_capacity_','txt_total_capacity');calculate_noOfDays(<? echo $i; ?>);"/>
                                </td>                    
                                <td align="center" width="90">
                                    <input type="text" name="txt_distribution_qnty[]" id="txt_distribution_qnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:75px" value="<? echo $distribution_qnty; ?>" onKeyUp="calculate_qnty(<? echo $i; ?>);calculate_noOfDays(<? echo $i; ?>);"/>
                                </td>
                                <td align="center" width="60">
                                    <input type="text" name="txt_noOfDays[]" id="txt_noOfDays_<? echo $i; ?>" class="text_boxes_numeric" style="width:45px" value="<? echo $noOfDays; ?>" onKeyUp="calculate_noOfDays(<? echo $i; ?>);" disabled="disabled"/>
                                </td> 
                                <td align="center" width="80">
                                    <input type="text" name="txt_startDate[]" id="txt_startDate_<? echo $i; ?>" class="datepicker" style="width:67px" value="<? echo $startDate; ?>" onChange="fn_add_date_field(<? echo $i; ?>);"/>
                                </td> 
                                <td align="center">
                                    <input type="text" name="txt_endDate[]" id="txt_endDate_<? echo $i; ?>" class="datepicker" style="width:67px" value="<? echo $endDate; ?>" disabled="disabled"/>
                                    <input type="hidden" name="dtls_id[]" id="dtls_id_<? echo $i; ?>" value="<? echo $dtls_id; ?>" disabled="disabled"/>
                                </td>
                            </tr>
                        <?
                        $i++;
                        }
                    ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="6" align="right"><b>Total</b></th>
                            <th align="center"><input type="text" name="txt_total_capacity" id="txt_total_capacity" class="text_boxes_numeric" style="width:75px" readonly disabled="disabled" value="<? echo $tot_capacity; ?>"/></th>
                            <th align="center"><input type="text" name="txt_total_distribution_qnty" id="txt_total_distribution_qnty" class="text_boxes_numeric" style="width:75px" readonly disabled="disabled" value="<? echo $tot_distribution_qnty; ?>"/></th>
                            <th>&nbsp;</th>
                            <th>&nbsp;</th>
                            <th>&nbsp;</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <table width="700" id="tbl_close">
                 <tr>
                    <td align="center" >
                        <input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" />
                    </td>
                </tr>
            </table>
		</fieldset>
	</form>
</div>
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
	exit();
}

if($action=="date_duplication_check")
{
	$data=explode("**",$data);
	$machine_id=$data[0];
	if($db_type==0)
	{
		$startDate=change_date_format(trim($data[1]),"yyyy-mm-dd","");
		$endDate=change_date_format(trim($data[2]),"yyyy-mm-dd","");
	}
	else
	{
		$startDate=change_date_format(trim($data[1]),'','',1);
		$endDate=change_date_format(trim($data[2]),'','',1);	
	}
	$update_dtls_id=$data[3];
	
	if($update_dtls_id=="")
	{
		$sql="select distribution_date, sum(days_complete) as days_complete from ppl_entry_machine_datewise where machine_id='$machine_id' and distribution_date between '$startDate' and '$endDate' group by distribution_date";
	}
	else
	{
		$sql="select distribution_date, sum(days_complete) as days_complete from ppl_entry_machine_datewise where machine_id='$machine_id' and distribution_date between '$startDate' and '$endDate' and dtls_id<>$update_dtls_id group by distribution_date";
	}
	//echo $sql;die;
	$data_array=sql_select($sql);
	$data='';
	if(count($data_array)>0)
	{
		foreach($data_array as $row)
		{
			if($row[csf('days_complete')] >= 1)
			{
				if($data=='') $data=change_date_format($row[csf('distribution_date')]); else $data.=",".change_date_format($row[csf('distribution_date')]);
			}
		}
		
		if($data=='') echo "0_"; else echo "1"."_".$data;
	}
	else
	{
		echo "0_";
	}
	
	exit();	
}

if($action=="color_info_popup")
{
	echo load_html_head_contents("Color Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
    
    <script>
	
		$(document).ready(function(e) {
            setFilterGrid('tbl_list_search',-1);
			set_all();
        });
		
		 var selected_id = new Array, selected_name = new Array();
		
		function check_all_data()
		{
			var tbl_row_count = $('#tbl_list_search tbody tr').length;
			tbl_row_count = tbl_row_count - 1;
			
			for( var i = 1; i <= tbl_row_count; i++ ) {
				js_set_value( i );
			}
		}
		
		function toggle( x, origColor ) 
		{
			var newColor = 'yellow';
			if ( x.style ) 
			{
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		
		function set_all()
		{
			var old=document.getElementById('txt_color_row_id').value;
			if(old!="")
			{   
				old=old.split(",");
				for(var i=0; i<old.length; i++)
				{  
					js_set_value( old[i] ) 
				}
			}
		}

		function js_set_value( str) 
		{
			toggle( document.getElementById( 'search' + str ), '#FFFFCC' );
			 
			if( jQuery.inArray( $('#txt_individual_id' + str).val(), selected_id ) == -1 ) {
				selected_id.push( $('#txt_individual_id' + str).val() );
				selected_name.push( $('#txt_individual' + str).val() );
				
			}
			else
			{
				for( var i = 0; i < selected_id.length; i++ )
				{
					if( selected_id[i] == $('#txt_individual_id' + str).val() ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
			}
			var id =''; var name = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + ',';
			}
			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );
			
			$('#txt_selected_id').val( id );
			$('#txt_selected').val( name );
		}
    </script>

</head>

<body>
<div align="center" style="width:390px;">
	<form name="searchwofrm"  id="searchwofrm">
		<fieldset style="width:380px; margin-top:10px; margin-left:20px">
            <div>
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="360" class="rpt_table" >
                    <thead>
                        <th width="40">SL</th>
                        <th width="160">Color</th>               
                        <th>Qnty</th> 
                        <input type="hidden" name="txt_selected_id" id="txt_selected_id" value="" />
                    	<input type="hidden" name="txt_selected"  id="txt_selected" value="" />  
                    </thead>
                </table>
                <div style="width:360px; overflow-y:scroll; max-height:210px;" id="buyer_list_view" align="center">
                    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="342" class="rpt_table" id="tbl_list_search">
                    	<tbody>
						<? 
							$hidden_color_id=explode(",",$hidden_color_id);
							$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
							
							$sql="select color_id, sum(grey_qty) as qty from fabric_sales_order_dtls where status_active=1 and is_deleted=0 and mst_id=$job_id and id in($job_dtls_id) group by color_id";
                            $result = sql_select($sql);
							
							$i=1; $tot_qnty=0;
                            foreach($result as $row)
                            {
                                if ($i%2==0)  
                                    $bgcolor="#E9F3FF";
                                else
                                    $bgcolor="#FFFFFF";
									
                                $tot_qnty+=$row[csf('qty')];
								
								if(in_array($row[csf('color_id')],$hidden_color_id)) 
								{
									if($color_row_id=="") $color_row_id=$i; else $color_row_id.=",".$i;
								}
											
                                ?>
                                <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i; ?>)"> 
                                    <td width="40" align="center"><? echo $i; ?>
                                     	<input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i; ?>" value="<? echo $row[csf('color_id')]; ?>"/>	
                         				<input type="hidden" name="txt_individual" id="txt_individual<?php echo $i; ?>" value="<? echo $color_library[$row[csf('color_id')]]; ?>"/>
                                    </td>	
                                    <td width="160"><p><? echo $color_library[$row[csf('color_id')]]; ?></p></td>               
                                    <td align="right"><? echo number_format($row[csf('qty')],2); ?></td>
                                </tr>
                            <?
                            $i++;
                            }
                        ?>
                        	<input type="hidden" name="txt_color_row_id" id="txt_color_row_id" value="<?php echo $color_row_id; ?>"/>
                    	</tbody>
                    	<tfoot>
                        	<tr>
                                <th colspan="2" align="right"><b>Total</b></th>
                                <th align="right"><? echo number_format($tot_qnty,2); ?></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <div style="width:100%; margin-left:10px; margin-top:5px"> 
                <div style="width:43%; float:left" align="left">
                    <input type="checkbox" name="check_all" id="check_all" onClick="check_all_data();" /> Check / Uncheck All
                </div>
                <div style="width:57%; float:left" align="left">
                    <input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px" />
                </div>
            </div>
		</fieldset>
	</form>
</div>
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
	exit();
}

if($action=="style_ref_search_popup")
{
	echo load_html_head_contents("Style Reference / Job No. Info", "../../../", 1, 1,'','','');
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
				js_set_value( i );
			}
		}
		
		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		
		function js_set_value( str ) {
			
			toggle( document.getElementById( 'search' + str ), '#FFFFCC' );
			 
			if( jQuery.inArray( str, selected_id ) == -1 ) {
				selected_id.push(  $('#txt_job_id' + str).val() );
				selected_name.push(  $('#txt_job_no' + str).val() );
				
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == $('#txt_job_id' + str).val() ) break;
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
		<fieldset style="width:600px;">
            <table width="590" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
            	<thead>
                	<th>Within Group</th>
                    <th>Buyer Name</th>
                    <th>Search By</th>
                    <th id="search_by_td_up" width="170">Please Enter Job No</th>
                    <th><input type="reset" name="button" class="formbutton" value="Reset"  style="width:90px;"></th> 
                    <input type="hidden" name="hide_job_no" id="hide_job_no" value="" />
                    <input type="hidden" name="hide_job_id" id="hide_job_id" value="" />
                </thead>
                <tbody>
                	<tr>
                    	<td align="center">	
							<?
                                echo create_drop_down( "cbo_within_group", 100, $yes_no,"",1, "--Select--",'', "load_drop_down( 'machine_wise_knitting_plan_controller',".$companyID."+'_'+this.value, 'load_drop_down_buyer', 'buyer_td' );",0 );
                            ?>
                        </td> 
                        <td id="buyer_td">
							<? 
                                echo create_drop_down( "cbo_buyer_name", 140, $blank_array,"", 1, "-- Select Buyer --", $selected, "",0,"" );
                            ?>
                        </td>
                        <td align="center">	
                    	<?
                       		$search_by_arr=array(1=>"Job No",2=>"Style Ref");
							$dd="change_search_event(this.value, '0*0', '0*0', '../../') ";							
							echo create_drop_down( "cbo_search_by", 110, $search_by_arr,"",0, "--Select--", "",$dd,0 );
						?>
                        </td>     
                        <td align="center" id="search_by_td">				
                            <input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />	
                        </td> 	
                        <td align="center">
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_within_group').value+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value, 'create_job_search_list_view', 'search_div', 'machine_wise_knitting_plan_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:90px;" />
                    </td>
                    </tr>
            	</tbody>
           	</table>
            <div style="margin-top:05px" id="search_div"></div>
		</fieldset>
	</form>
</div>
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit(); 
}

if($action=="create_job_search_list_view")
{
	$data=explode('**',$data);
	
	$company_arr=return_library_array( "select id,company_short_name from lib_company",'id','company_short_name');
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	
	$company_id =$data[0];
	$within_group=$data[1];
	$buyer_id =$data[2];
	$search_by =$data[3];
	$search_string=trim($data[4]);
	
	$search_field_cond='';
	if($search_string!="")
	{
		if($search_by==1)
		{
			$search_field_cond=" and job_no like '%".$search_string."'";
		}
		else
		{
			$search_field_cond=" and LOWER(style_ref_no) like LOWER('".$search_string."%')";
		}
	}
		
	if($within_group==0) $within_group_cond=""; else $within_group_cond=" and within_group=$within_group";
	if($buyer_id==0) $buyer_id_cond=""; else $buyer_id_cond=" and buyer_id=$buyer_id";
	
	if($db_type==0) $year_field="YEAR(insert_date) as year"; 
	else if($db_type==2) $year_field="to_char(insert_date,'YYYY') as year";
	else $year_field="";//defined Later
	
	$sql = "select id, $year_field, job_no_prefix_num, job_no, within_group, sales_booking_no, booking_date, buyer_id, style_ref_no, location_id from fabric_sales_order_mst where status_active=1 and is_deleted=0 and company_id=$company_id $within_group_cond $search_field_cond $buyer_id_cond order by id"; 
	//echo $sql;//die;
	$result = sql_select($sql);
	?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="600" class="rpt_table">
        <thead>
            <th width="40">SL</th>
            <th width="70">Job No.</th>
            <th width="60">Year</th>
            <th width="80">Within Group</th>
            <th width="70">Buyer</th>               
            <th width="120">Sales/ Booking No</th>
            <th>Style Ref.</th>
        </thead>
	</table>
	<div style="width:600px; max-height:260px; overflow-y:scroll" id="list_container_batch" align="left">	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="580" class="rpt_table" id="tbl_list_search">  
        <?
            $i=1;
            foreach ($result as $row)
            {  
                if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					 
                if($row[csf('within_group')]==1)
					$buyer=$company_arr[$row[csf('buyer_id')]]; 
				else
					$buyer=$buyer_arr[$row[csf('buyer_id')]];
        	?>
                <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value(<? echo $i; ?>);" id="search<? echo $i; ?>"> 
                    <td width="40"><? echo $i; ?>
                    	<input type="hidden" name="txt_job_id" id="txt_job_id<?php echo $i ?>" value="<? echo $row[csf('id')]; ?>"/>	
                        <input type="hidden" name="txt_job_no" id="txt_job_no<?php echo $i ?>" value="<? echo $row[csf('job_no')]; ?>"/>	
                    </td>
                    <td width="70"><p>&nbsp;<? echo $row[csf('job_no_prefix_num')]; ?></p></td>
                    <td width="60" align="center"><p><? echo $row[csf('year')]; ?></p></td>
                    <td width="80"><p><? echo $yes_no[$row[csf('within_group')]]; ?>&nbsp;</p></td>
                    <td width="70"><p><? echo $buyer; ?>&nbsp;</p></td>
                    <td width="120"><p><? echo $row[csf('sales_booking_no')]; ?></p></td>               
                    <td><p><? echo $row[csf('style_ref_no')]; ?></p></td>
                </tr>
        	<?
            $i++;
            }
        	?>
        </table>
    </div>
    <table width="600" cellspacing="0" cellpadding="0" style="border:none" align="center">
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
<?	
	exit();	
}

if ($action=="booking_no_search_popup")
{
	echo load_html_head_contents("Booking Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
?> 
	<script>
	
		function js_set_value(booking_no)
		{
			$('#hidden_booking_no').val(booking_no);
			parent.emailwindow.hide();
		}
	
    </script>

</head>

<body>
<div align="center" style="width:750px;">
	<form name="searchwofrm"  id="searchwofrm" autocomplete=off>
		<fieldset style="width:100%;">
		<legend>Enter search words</legend>           
            <table cellpadding="0" cellspacing="0" width="745" class="rpt_table" border="1" rules="all">
                <thead>
                    <th>Buyer</th>
                    <th>Booking Date</th>
                    <th>Search By</th>
                    <th id="search_by_td_up" width="150">Please Enter Booking No</th>
                    <th>
                    	<input type="reset" name="reset" id="reset" value="Reset" style="width:90px" class="formbutton" />
                        <input type="hidden" name="txt_company_id" id="txt_company_id" class="text_boxes" value="<? echo $companyID; ?>">
                    	<input type="hidden" name="hidden_booking_no" id="hidden_booking_no" class="text_boxes" value="">  
                    </th> 
                </thead>
                <tr>
                    <td align="center">
                    	<?
							echo create_drop_down( "cbo_buyer_name", 140, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Buyer --", $selected, "",'' ); 
						?>       
                    </td>
                    <td align="center">
                    	<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" readonly>To
					  	<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" readonly>
					</td>
                    <td align="center">	
                    	<?
                       		$search_by_arr=array(1=>"Booking No",2=>"Job No");
							$dd="change_search_event(this.value, '0*0', '0*0', '../../') ";							
							echo create_drop_down( "cbo_search_by", 100, $search_by_arr,"",0, "--Select--", "",$dd,0 );
						?>
                    </td>                 
                    <td align="center" id="search_by_td">				
                        <input type="text" style="width:130px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />	
                    </td> 						
            		<td align="center">
                     	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_company_id').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value, 'create_booking_search_list_view', 'search_div', 'machine_wise_knitting_plan_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:90px;" />
                     </td>
                </tr>
                <tr>
                	<td colspan="5" align="center"  valign="middle"><? echo load_month_buttons(1); ?></td>
                </tr>
           </table>
          <div style="width:100%; margin-top:5px; margin-left:3px" id="search_div" align="left"></div> 
		</fieldset>
	</form>
</div>
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if($action=="create_booking_search_list_view")
{
	$data = explode("_",$data);
	
	$search_string="%".trim($data[0])."%";
	$search_by=$data[1];
	$company_id =$data[2];
	$buyer_id=$data[3];
	$date_from=trim($data[4]);
	$date_to=trim($data[5]);
	
	if($buyer_id==0)
	{
		$buyer_id_cond="";
	}
	else
	{
		$buyer_id_cond=" and a.company_id=$buyer_id";
	}
	
	$search_field_cond="";
	if(trim($data[0])!="")
	{
		if($search_by==1)
			$search_field_cond="and a.booking_no like '$search_string'";
		else
			$search_field_cond="and a.job_no like '$search_string'";
	}
	
	$date_cond='';
	if($date_from!="" && $date_to!="")
	{
		if($db_type==0)
		{
			$date_cond="and a.booking_date between '".change_date_format(trim($date_from), "yyyy-mm-dd", "-")."' and '".change_date_format(trim($date_to), "yyyy-mm-dd", "-")."'";
		}
		else
		{
			$date_cond="and a.booking_date between '".change_date_format(trim($date_from),'','',1)."' and '".change_date_format(trim($date_to),'','',1)."'";
		}
	}
	
	$company_arr=return_library_array( "select id,company_short_name from lib_company",'id','company_short_name');
	$po_arr=return_library_array( "select id,po_number from wo_po_break_down",'id','po_number');
	
	$sql= "SELECT a.id, a.booking_no, a.booking_date, a.company_id, a.delivery_date, a.currency_id, a.po_break_down_id, b.job_no, b.style_ref_no FROM wo_booking_mst a, wo_po_details_master b WHERE a.job_no=b.job_no and a.pay_mode=5 and a.fabric_source=2 and a.supplier_id=$company_id and a.status_active =1 and a.is_deleted =0 and a.item_category=2 $buyer_id_cond $search_field_cond $date_cond group by a.id, a.booking_no,a.booking_date,a.company_id,a.delivery_date,a.currency_id,a.po_break_down_id,b.job_no,b.style_ref_no";
	//echo $sql;die;

	?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="740" class="rpt_table">
        <thead>
            <th width="40">SL</th>
            <th width="80">Buyer</th>
            <th width="120">Booking No</th>
            <th width="90">Job No</th>
            <th width="120">Style Ref.</th>
            <th width="80">Booking Date</th>     
            <th>PO No.</th>             
        </thead>
	</table>
	<div style="width:740px; max-height:270px; overflow-y:scroll" id="list_container_batch" align="left">	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="720" class="rpt_table" id="tbl_list_search">  
        <?
            $i=1;
			$result = sql_select($sql);
            foreach ($result as $row)
            { 
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				
				if($row[csf('po_break_down_id')]!="")
				{
					$po_no='';
					$po_ids=explode(",",$row[csf('po_break_down_id')]);
					foreach($po_ids as $po_id)
					{
						if($po_no=="") $po_no=$po_arr[$po_id]; else $po_no.=",".$po_arr[$po_id];
					}
				}	
				
			?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $row[csf('booking_no')]; ?>')"> 
					<td width="40"><? echo $i; ?></td>
					<td width="80" align="center"><p><? echo $company_arr[$row[csf('company_id')]]; ?>&nbsp;</p></td>
					<td width="120"><p><? echo $row[csf('booking_no')]; ?></p></td>
					<td width="90" align="center"><p><? echo $row[csf('job_no')]; ?>&nbsp;</p></td>
					<td width="120"><p><? echo $row[csf('style_ref_no')]; ?>&nbsp;</p></td>
					<td width="80" align="center"><? echo change_date_format($row[csf('booking_date')]); ?></td>               
					<td><p><? echo $po_no; ?>&nbsp;</p></td>
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

if($action=="load_drop_down_knitting_party")
{
	$data=explode("**",$data);
	if($data[0]==1)
	{
		echo create_drop_down( "cbo_knitting_party", 177, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name",1, "--Select Knit Party--", $data[1], "","" );
	}
	else if($data[0]==3)
	{	
		if($data[2]==1) $selected_id=$data[1]; else $selected_id=0;
		echo create_drop_down( "cbo_knitting_party", 177, "select a.id, a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=20 and a.status_active=1 and a.is_deleted=0 group by a.id, a.supplier_name order by a.supplier_name","id,supplier_name", 1, "--Select Knit Party--", $selected_id, "" );
	}
	else
	{
		echo create_drop_down( "cbo_knitting_party", 177, $blank_array,"",1, "--Select Knit Party--", 0, "" );
	}
	exit();
}
?>