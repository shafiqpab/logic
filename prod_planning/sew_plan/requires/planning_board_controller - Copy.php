<?
/*
 .testdiv:after {
    content:'\A';
    position:absolute;
    background:#009900;
    top:0; bottom:0;
    left:0; 
    width:0%;
    -webkit-animation: filler 2s ease-in-out;
    -moz-animation: filler 2s ease-in-out;
    animation: filler 2s ease-in-out;
	border-radius:10px;
	 
}

@-webkit-keyframes filler {
    0% {
        width:0;
    }
}
@-moz-keyframes filler {
    0% {
        width:0;
    }
}
@keyframes filler {
    0% {
        width:0;
    }
}



.testdiv {
  background: -webkit-repeating-linear-gradient(135deg, #666, #666 25%, #5b5b5b 25%, #5b5b5b 50%, #666 50%) top left fixed;
  background: repeating-linear-gradient(-45deg, #FF0000, #FF0000 5px, #FFFFFF 2px, #999999 10px, #666 1px) top left fixed;
  background-size: 30px 30px;
}
.ssss .current {
  width: 25%;
  -webkit-animation: width 5s infinite;
  animation: width 5s infinite;
  background: -webkit-repeating-linear-gradient(135deg, #465298, #465298 25%, #3f4988 25%, #3f4988 50%, #465298 50%) top left fixed;
  background: repeating-linear-gradient(-45deg, #465298, #465298 25%, #3f4988 25%, #3f4988 50%, #465298 50%) top left fixed;
  background-size: 30px 30px;
}
 
@-webkit-keyframes width {
  0% {
    width: 0%;
  }
  100% {
    width: 100%;
  }
}

@keyframes width {
  0% {
    width: 0%;
  }
  100% {
    width: 100%;
  }
}
*/
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$permission=$_SESSION['page_permission'];

//************************************ Start*************************************************
$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
$size_library=return_library_array( "select id,size_name from lib_size", "id", "size_name"  );
if($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 170,"select a.id,a.buyer_name from  lib_buyer a, lib_buyer_tag_company b where a.id=b.buyer_id and b.tag_company=$data and a.status_active=1 and a.is_deleted=0","id,buyer_name", 1, "-- Select Buyer --", 0, "" );

}
if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_name", 150, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select --", $selected, "" );		 
}

 
if($action=="show_plan_details")
{
	$data=explode("__",$data);
 
	$line_array=return_library_array("select id,line_name from lib_sewing_line where company_name='$data[0]' and location_name='$data[1]' and id<31 order by id ", "id","line_name",1);
	//$line_array=return_library_array("select id,line_name from lib_sewing_line where company_name='$data[0]' and location_name='$data[1]' and id<31 order by id ", "id","line_name",1);
	if( $data[2]=="") $data[2]=date("d-m-Y",time());
	$todate=date("Y-m-d", strtotime($data[2]));
	$days_forward=200;
	$width=(30*$days_forward)+550;
	$last_date=add_date($todate,$days_forward);
	//echo $last_date;
	if($db_type==2)
	{
		$todate = change_date_format( str_replace("'","",trim($todate)),'','',1);
		$last_date = change_date_format( str_replace("'","",trim($last_date)),'','',1);
	}
//	echo change_date_format( str_replace("'","",trim($last_date)),'','',0);

	 $sql="select id,line_id,po_break_down_id,plan_id,start_date,start_hour,end_date,end_hour,duration,plan_qnty,comp_level,first_day_output,increment_qty,terget from  ppl_sewing_plan_board where (start_date between '".$todate."' and '".$last_date."'  or  end_date between '".$todate."' and '".$last_date."') or ( start_date < '".$todate."' and end_date> '".$last_date."') order by po_break_down_id";
	$sql_data=sql_select($sql);
	$m=0;
	foreach($sql_data as $rows)
	{
		$m++;
		$crossed_plan=0;
				$actual_start_date=$rows[csf("start_date")];
				$actual_end_date=$rows[csf("end_date")];
			//if Any plan cross the dash board, before or after starts
			 if(strtotime(change_date_format( str_replace("'","",trim($rows[csf("start_date")])),'','',0)) < strtotime(change_date_format( str_replace("'","",trim($todate)),'','',0)))
			{
				$dur=datediff("d",change_date_format( str_replace("'","",trim($rows[csf("start_date")])),'','',0),change_date_format( str_replace("'","",trim($todate)),'','',0));
				$actual_start_date=$rows[csf("start_date")];
				$rows[csf("start_date")]=$todate;
				$rows[csf("duration")]=$rows[csf("duration")]-$dur+1;
				$crossed_plan=1;
			}
			if(strtotime(change_date_format( str_replace("'","",trim($rows[csf("end_date")])),'','',0)) > strtotime(change_date_format( str_replace("'","",trim($last_date)),'','',0)))
			{
				$dur=datediff("d",change_date_format( str_replace("'","",trim($rows[csf("end_date")])),'','',0),change_date_format( str_replace("'","",trim($last_date)),'','',0));
				 $actual_end_date=$rows[csf("end_date")];
				 $rows[csf("end_date")]=$last_date;
				 $rows[csf("duration")]=$rows[csf("duration")]-$dur+1;
				 $crossed_plan=1;
			}
			//if Any plan cross the dash board, before or after ends
			  
		$str_po_plan=$rows[csf("po_break_down_id")]."__".$rows[csf("plan_id")];
		if( $po_break_down_array[$rows[csf("po_break_down_id")]]=="" )
		{
			$po_break_down_array[$rows[csf("po_break_down_id")]]=$rows[csf("po_break_down_id")];
			if( $m==1 )
			{
				$str_data_plan=$rows[csf("line_id")]."**".change_date_format( str_replace("'","",trim($rows[csf("start_date")])),'','',0).".".$rows[csf("start_hour")]."**".$rows[csf("plan_qnty")]."**".$rows[csf("duration")]."**".$rows[csf("id")]."**0"."**".$crossed_plan."**".$actual_start_date."**".$actual_end_date;
				$str_data_comp=$rows[csf("comp_level")]."**".$rows[csf("first_day_output")]."**".$rows[csf("increment_qty")]."**".$rows[csf("terget")]."**".$rows[csf("po_break_down_id")]."**".$rows[csf("plan_id")];
			}
			else // New PO
			{
				if($single_string=="") $single_string .=$str_po_plan."______".$str_data_plan."______".$str_data_comp;
				else $single_string .="**__**".$str_po_plan."______".$str_data_plan."______".$str_data_comp;
				
				$str_data_plan =$rows[csf("line_id")]."**".change_date_format( str_replace("'","",trim($rows[csf("start_date")])),'','',0).".".$rows[csf("start_hour")]."**".$rows[csf("plan_qnty")]."**".$rows[csf("duration")]."**".$rows[csf("id")]."**0"."**".$crossed_plan."**".$actual_start_date."**".$actual_end_date;
				$str_data_comp =$rows[csf("comp_level")]."**".$rows[csf("first_day_output")]."**".$rows[csf("increment_qty")]."**".$rows[csf("terget")]."**".$rows[csf("po_break_down_id")]."**".$rows[csf("plan_id")]."**".$crossed_plan;
			}			
		}
		else
		{
			$str_data_plan .="****".$rows[csf("line_id")]."**".change_date_format( str_replace("'","",trim($rows[csf("start_date")])),'','',0).".".$rows[csf("start_hour")]."**".$rows[csf("plan_qnty")]."**".$rows[csf("duration")]."**".$rows[csf("id")]."**0"."**".$crossed_plan."**".$actual_start_date."**".$actual_end_date;
		}
		if($m==count($sql_data))
		{
			if($single_string=="") $single_string .=$str_po_plan."______".$str_data_plan."______".$str_data_comp;
			else $single_string .="**__**".$str_po_plan."______".$str_data_plan."______".$str_data_comp;
		}
		
		for($k=0; $k<$rows[csf("duration")]; $k++)
		{
			$dates=add_date($rows[csf("start_date")],$k);
			$po_plan_info[$rows[csf("po_break_down_id")]][$rows[csf("line_id")]][change_date_format( str_replace("'","",trim($dates)),'','',0)]=$rows[csf("id")];
		}
	}
//	echo $po_plan_info[2426][$rows[csf("line_id")]][change_date_format( str_replace("'","",trim($dates)),'','',0)]
	 //List all offdays
	$sql="select a.mst_id,a.month_id,a.date_calc,a.day_status,comapny_id,capacity_source,location_id from  lib_capacity_calc_dtls a, lib_capacity_calc_mst b where b.id=a.mst_id and date_calc between '".$todate."' and '".$last_date."' and comapny_id='$data[0]' and location_id='$data[1]'  and day_status=2";
	//and date_calc between '".$todate."' and '".$last_date."' and comapny_id='$data[0]' and location_id='$data[1]'
	$sql_data=sql_select($sql);
	foreach($sql_data as $rows)
	{
		$day_status[change_date_format( str_replace("'","",trim($rows[csf("date_calc")])),'','',0)]=$rows[csf("day_status")];
		$day_status_days[change_date_format( str_replace("'","",trim($rows[csf("date_calc")])),'','',0)]=change_date_format( str_replace("'","",trim($rows[csf("date_calc")])),'','',0);
	}
	
	//List all off days ends
	 
	if( count($po_break_down_array)>0 )
	{
		$sql="select po_break_down_id,sum(production_quantity) as production_quantity,production_date, sewing_line,company_id,location from   pro_garments_production_mst where production_type=5 and po_break_down_id in (".implode(",",$po_break_down_array).") and status_active=1 and is_deleted=0 and production_date between '".$todate."' and '".$last_date."' group by production_date,po_break_down_id,sewing_line, company_id,location order by sewing_line,po_break_down_id,production_date";
		//and date_calc between '".$todate."' and '".$last_date."' and comapny_id='$data[0]' and location_id='$data[1]'
		$sql_data=sql_select($sql);
		$k=0;
		foreach($sql_data as $rows)
		{
			//echo $rows[csf("production_quantity")]."==";
			$production_details[$rows[csf("sewing_line")]][$rows[csf("po_break_down_id")]][$po_plan_info[$rows[csf("po_break_down_id")]][$rows[csf("sewing_line")]][change_date_format( str_replace("'","",trim($rows[csf("production_date")])),'','',0)]]['prod_qnty']+=$rows[csf("production_quantity")];
			
			$production_details_arr[$po_plan_info[$rows[csf("po_break_down_id")]][$rows[csf("sewing_line")]][change_date_format( str_replace("'","",trim($rows[csf("production_date")])),'','',0)]] += $rows[csf("production_quantity")];
		}
	}
	foreach($production_details_arr as $id=>$val)
	{
		if($production_details_string=="")
			$production_details_string=$id."_".$val;
		else
			$production_details_string .="**".$id."_".$val;
	}
 //print_r($production_details_string); 
	
	//print_r($day_status);
	 
	$tot_month=datediff("m",$todate,$last_date)+1;
	for( $i=0; $i<$tot_month; $i++ )
	{
		$next_month=add_month($todate,$i);
		$ldays=cal_days_in_month(CAL_GREGORIAN, date("m",strtotime($next_month)), date("Y",strtotime($next_month)))."-".date("m",strtotime($next_month))."-". date("Y",strtotime($next_month)); // 31
		
		if($i==0) $days[$i]=datediff("d", $todate, $ldays);
		else if($i==$tot_month-1) $days[$i]=datediff("d", "01-".date("m",strtotime($next_month))."-". date("Y",strtotime($next_month)), $last_date);
		else $days[$i]= cal_days_in_month(CAL_GREGORIAN, date("m",strtotime($next_month)), date("Y",strtotime($next_month)));
			//$days[$i]= 
	}
	 
	?>
    <table cellpadding="0" width="<? echo $width; ?>"  cellspacing="0" border="1" rules="all" id="bodycont" >
 	<tr>
    	<td id="display_pos" style="display:none" colspan="50" align="left" height="34">
			ss
        </td>        
    </tr>
	<tr>
    	<td class="top_header">
        <input type="button" class="formbutton" style="width:120px;" onClick="fnc_save_planning()" value="Save Plan">
        
        <input type="hidden" id="off_day_data" value="<? echo implode(", ",($day_status_days));?>">
        <input type="hidden" id="old_plan_data" value="<? echo  $single_string;?>">
        <input type="hidden" id="txt_production_data" value="<? echo $production_details_string; ?>">
        <input type="hidden" id="txt_overlaaped_id">
        
        </td>
         <?  	for($j=0; $j<count($days); $j++)
				{
					$next_month=add_month($todate,$j);
					$tdate=date("d-M",strtotime(add_date($todate,$j-1)));
					?>
					<td class="top_header" colspan="<? echo $days[$j]; ?>" ><? echo  date("M Y",strtotime($next_month)); ?></td>
					<?
				}
			?>
    </tr>
	<tr>
    	<td class="top_headerss">Line</td>
         <?  //style="width:150px"
		 		$width=30;
		 		 
				for($j=1; $j<$days_forward; $j++)
				{
					$tdate=date("d",strtotime(add_date($todate,$j-1)));
					if($day_status[date("d-m-Y",strtotime(add_date($todate,$j-1)))]==2) $head_class="top_headerss_off"; else $head_class="top_headerss";
					?>
					<td class="<? echo $head_class; ?>" style="width:<? echo $width."px"; ?>"><? echo $tdate; ?></td>
					<?
				}
			?>
    </tr>
	<? 
		//for($i=0; $i<25; $i++)
		foreach( $line_array as $i=>$line)
		{
			?>
            <tr>
            <?
				for( $j=0; $j<$days_forward; $j++ )
				{
					if( $j==0 ) { $width=""; $idd=""; $text="Line ".$line; $tdcls="left_header";  } else { $tdate=date("dmY",strtotime(add_date($todate,$j-1))); $idd="-".$i."-".$tdate; $width="20"; $text="&nbsp;"; $tdcls="verticalStripes1"; }
					
					if($day_status[date("d-m-Y",strtotime(add_date($todate,$j-1)))]==2) $tdcls="verticalStripes_off";
					
					?>
					<td  class="<? echo $tdcls; ?>" id="tdbody<? echo $idd; ?>" name="tdbody<? echo $i.$j; ?>"><? echo $text; ?></td>
					<?
				}
			?>
            </tr>
            <?
		}
	?>
</table>
    <?
}

if ($action=="order_popup")
{
  	echo load_html_head_contents("Popup Info","../../", 1, 1, $unicode);
	extract($_REQUEST);
	$line_array=return_library_array("select id,line_name from lib_sewing_line where id<31 order by id ", "id","line_name",1);
	$pdate=substr($pdate,0,2)."-".substr($pdate,2,2)."-".substr($pdate,4,4);
	
	//$complexity_level=array(1=>"Basic",2=>"Simply Complex", 3=>"Highly Complex");
							 
	$complexity_level_data[1]['fdout']=1000;
	$complexity_level_data[1]['increment']=100;
	$complexity_level_data[1]['target']=1200;
	$complexity_level_data[2]['fdout']=800;
	$complexity_level_data[2]['increment']=100;
	$complexity_level_data[2]['target']=1200;
	$complexity_level_data[3]['fdout']=600;
	$complexity_level_data[3]['increment']=100;
	$complexity_level_data[3]['target']=1200; ///complexity_levels
	
							 
?>
     
	<script>
	<?
	$line_array_js= json_encode($line_array); 
	echo "var line_array = ". $line_array_js . ";\n";
	
	$complexity_levels= json_encode($complexity_level_data); 
	echo "var complexity_levels = ". $complexity_levels . ";\n";
	
	?>
	function set_checkvalue()
	{
		if(document.getElementById('chk_job_wo_po').value==0) document.getElementById('chk_job_wo_po').value=1;
		else document.getElementById('chk_job_wo_po').value=0;
	}
	
	function js_set_value( job_no )
	{
		$('#search_div').css('visibility','collapse');
		$('#search_panel').css('visibility','collapse');
		$('#search_div_line').css('visibility','visible');
		var jdata=job_no.split("_"); //job_no,id,po_quantity,shipment_date,po_number,shipment_date,style_ref_no
		$('#order').html(jdata[4]);
		$('#shipdate').html(jdata[5]);
		$('#orderqnty').html(jdata[2]);
		//$('#search_div_line').html(jdata[4]);
	 
						
		 document.getElementById('selected_job').value=job_no;
		//parent.emailwindow.hide();
	}
	function show_hide( fm )
	{
		if(fm==1)
		{
			$('#search_div_line').css('visibility','collapse');
			$('#search_div_lag').css('visibility','visible');
		}
		else if(fm==2)
		{
			var orderinfo= document.getElementById('selected_job').value;
			var planData="";
			var rows =document.getElementById('tbl_line').rows.length-2;
			for( var k=1; k<rows; k++ )
			{
				if(planData!="") planData=planData+"****";
				planData=planData+$('#cbo_line_selection__'+k).val()+"**"+$('#txt_line_date__'+k).html()+"**"+$('#txt_plan_qnty__'+k).html()+"**0**0**1";
			}
			var jdata= document.getElementById('selected_job').value.split("_"); //job_no,id,po_quantity,shipment_date,po_number,shipment_date,style_ref_no
			 
			planData=orderinfo+"______"+planData+"______"+$('#cbo_complexity_selection').val( )+"**"+$('#txt_first_day').val( )+"**"+$('#txt_increment').val( )+"**"+$('#txt_target').val( )+"**"+jdata[1]+"**0"; 
			document.getElementById('selected_job').value=planData;
			parent.emailwindow.hide();
			//alert(planData);
			/*jQuery("#tbl_line tbody tr").each(function(e) {
				jQuery(this +" td").each(function(e) {
					alert($(this).html());
				});
				alert('asdasd'); 
			});*/
			
		}
	}
	function add_line()
	{
		var rowCount =document.getElementById('tbl_line').rows.length-2;
		if (rowCount%2==0)  
			var bgcolor="#E9F3FF";
		else
			var bgcolor="#FFFFFF";
		 var row_idss= $('#up_row_id').val();
		 if ( row_idss=="" ) var rowCount=(rowCount*1); else rowCount=row_idss;
		
		var new_html='<tr  bgcolor="'+ bgcolor +'" id="row_' + rowCount + '" onclick="set_update_row(' + rowCount + ',0)" style="cursor:pointer">'
					+ '<td id="txt_line' + rowCount + '" width="20">'+ line_array[$('#cbo_line_selection').val()] +'</td>'	
					+ '<td id="txt_line_date__' + rowCount + '" width="100">'+ $('#txt_line_date').val() +'</td>'
					+ '<td id="txt_plan_qnty__' + rowCount + '" width="100">'+ $('#txt_plan_qnty').val() +'</td>'
					+'<td id="blank_' + rowCount + ' onclick="set_update_row(' + rowCount + ',1)"><input type="button" name="" value="Clear"  id="btn_clear_' + rowCount + '"  class="formbutton" onclick="set_update_row(' + rowCount + ',1)"/><input type="hidden" id="cbo_line_selection__' + rowCount + '" value="'+$('#cbo_line_selection').val()+'" /></td></tr>';
			if( row_idss=="")			
					$("#tbl_line tbody").append(new_html);
			else
				$('#row_' + rowCount).replaceWith(new_html);
				$('#up_row_id').val('');
	}
	
	function set_update_row(id, is_clear)
	{ 
		if(is_clear==0)
		{
			$('#up_row_id').val(id);
			$("#row_"+id +" td").each(function() {
				var tdid2=$(this).attr('id');
				var tdid=tdid2.split("__");
				
				if(!tdid[1] && tdid[1]!='undefined')
					var d=1;//alert("d=="+tdid[1]);
				else
				{
					$('#'+tdid[0]).val($(this).html());
				}
				// $(this).html('');
			});
			$('#cbo_line_selection').val($('#cbo_line_selection__'+id).val());
			
		}
		else
		{
			$("#row_"+id +" td").each(function() {
				 $(this).html('');
			});
			$('#up_row_id').val('');
		}
	}
	
	function open_back( fm )
	{
		if(fm==1)
		{
			$('#search_div').css('visibility','visible');
			$('#search_panel').css('visibility','visible');
			$('#search_div_line').css('visibility','collapse');
		}
		else if(fm==2)
		{
			$('#search_div_line').css('visibility','visible');
			//$('#').css('visibility','visible');
			$('#search_div_lag').css('visibility','collapse');
		}
	}
	function fill_complexity(vid)
	{  
		$('#txt_first_day').val( complexity_levels[vid]['fdout'] );
		$('#txt_increment').val( complexity_levels[vid]['increment'] );
		$('#txt_target').val( complexity_levels[vid]['target'] ); 
	}
	
    </script>

</head>

<body>
<div align="center" style="width:100%;" >
<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
	<table width="850" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center">
    	<tr>
        	<td align="center" width="100%">
            <div id="search_panel">
            	<table  cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center">
                        <thead>
                         <th width="150" colspan="3"> </th>
                        	<th>
                              <?
                               echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --" );
                              ?>
                            </th>
                          <th width="150" colspan="2"> </th>
                        </thead>
                    <thead>                	 
                        <th width="150">Company Name</th>
                        <th width="150">Buyer Name</th>
                        <th width="80">Job No</th>
                        <th width="120">Order No</th>
                        <th width="200">Ship Date Range</th>
                        <th><input type="checkbox" value="0" onClick="set_checkvalue()" id="chk_job_wo_po">Job Without PO</th>           
                    </thead>
        			<tr>
                    	<td> 
                        <input type="hidden" id="selected_job">
                        <input type="hidden" id="garments_nature" value="<? echo $garments_nature; ?>">
							<? 
								echo create_drop_down( "cbo_company_mst", 150, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name",1, "-- Select Company --", '',"load_drop_down( 'planning_board_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );" );
							?>
                    </td>
                   	<td id="buyer_td">
                     <? 
						echo create_drop_down( "cbo_buyer_name", 172, $blank_array,'', 1, "-- Select Buyer --" );
					?>	</td>
                    <td><input name="txt_job_prifix" id="txt_job_prifix" class="text_boxes" style="width:80px"></td>
                    <td><input name="txt_order_search" id="txt_order_search" class="text_boxes" style="width:120px"></td>
                    <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px">
					  <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px">
					 </td> 
            		 <td align="center">
                     <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('chk_job_wo_po').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('garments_nature').value+'_'+document.getElementById('txt_job_prifix').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('txt_order_search').value, 'create_po_search_list_view', 'search_div', 'planning_board_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" /></td>
        		</tr>
                
                <tr>
                    <td  align="center" colspan="6" height="40" valign="middle">
                     <? 
                    echo create_drop_down( "cbo_year_selection", 70, $year,"", 1, "-- Select --", date('Y'), "",0 );		
                    ?>
                    <? echo load_month_buttons();  ?>
                    </td>
                 </tr>
             </table>
             </div>
          </td>
        </tr>
        
        <tr>
            <td align="center" valign="top" id=""> 
				<div style="visibility:visible" id="search_div"></div>
                <div style="visibility:collapse" id="search_div_line">
                	<table width="630">
                    	<tr>
                        	<td width="100">Order No</td><td width="130" id="order"></td>
                            <td width="100">Shipment Date</td><td width="100" id="shipdate"></td>
                            <td width="100">Order Quantity</td><td id="orderqnty"></td>
                        </tr>
                    </table>
                	<table width="350" class="rpt_table" id="tbl_line">
                    	<thead>
                         
                        <tr>
                            <th width="120"> Line No</th>
                             <th width="90">Start Date</th>
                             <th width="90">Quantity</th>
                             <th>
                             	 <input type="hidden" id="up_row_id" />
                                 <input type="button" class="formbutton" style="width:80px" value="Go Back" onClick="open_back(1)" /> 
                            </th>
                        </tr>
                    	<tr>
                             <th width="120"><? echo create_drop_down( "cbo_line_selection", 110, $line_array, "", 1, "-- Select --", $pline, "",0 ); ?></th>
                             <th width="90"><input type="text" class="datepicker" value="<? echo $pdate; ?>" id="txt_line_date" style="width:80px" /></th>
                             <th width="90"><input type="text" class="text_boxes_numeric" id="txt_plan_qnty" style="width:80px" /></th>
                             <th>
                             	<input type="button" class="formbutton" style="width:80px" value="Add" onClick="add_line()" /> 
                            </th>
                        </tr>
                        </thead>
                        <tbody></tbody>
                        <tfoot>
                        	 
                            	<th colspan="2" align="left">
                                <input type="button" class="formbutton" style="width:80px" value="Close" onClick="show_hide(1)" /></th>
                                <th colspan="2" align="left"></th>
                             
                        </tfoot>
                        
                    </table>
                
                
                </div>
                <div style="visibility:collapse" id="search_div_lag">
                	<table width="450" class="rpt_table" id="tbl_lag">
                    	<thead>
                        <tr>
                            <th width="130">Complexity Level</th>
                             <th width="120">First Day Output</th>
                             <th width="90">Increment</th>
                             <th width="90">Target</th>
                             <th><input type="button" class="formbutton" style="width:80px" value="Go Back" onClick="open_back(2)" /> </th>
                        </tr>
                    	<tr>
                             <th width="120">
							 <? 
							 echo create_drop_down( "cbo_complexity_selection", 110, $complexity_level, "", 1, "-- Select --",'', "fill_complexity(this.value)",0 );  
							 ?></th>
                             <th width="90"><input type="text" class="text_boxes_numeric" id="txt_first_day" style="width:80px" /></th>
                             <th width="90"><input type="text" class="text_boxes_numeric" id="txt_increment" style="width:80px" /></th>
                             <th width="90"><input type="text" class="text_boxes_numeric" id="txt_target" style="width:80px" /></th>
                             <th>
                             	<input type="button" class="formbutton" style="width:80px" value="Add" onClick="add_line()" /> 
                            </th>
                        </tr>
                        </thead>
                        <tbody></tbody>
                        <tfoot>
                        	 
                            	<th colspan="4" align="center"><input type="button" class="formbutton" style="width:80px" value="Close" onClick="show_hide(2)" /></th>
                            
                        </tfoot>
                        
                    </table>
                
                
                </div>
            </td>
        </tr>
    </table>    
     
    </form>
   </div>
</body>           
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}


if($action=="create_po_search_list_view")
{
	//echo $data;die;
	$data=explode('_',$data);
	if ($data[0]!=0) $company=" and a.company_name='$data[0]'"; else { echo "Please Select Company First."; die; }
	if ($data[1]!=0) $buyer=" and a.buyer_name='$data[1]'"; else $buyer="";//{ echo "Please Select Buyer First."; die; }
	
	
	
	if($db_type==0)
	{
	$year_cond=" and SUBSTRING_INDEX(a.`insert_date`, '-', 1)=$data[7]";
	if ($data[3]!="" &&  $data[4]!="") $shipment_date = "and b.pub_shipment_date between '".change_date_format($data[3], "yyyy-mm-dd", "-")."' and '".change_date_format($data[4], "yyyy-mm-dd", "-")."'"; else $shipment_date ="";
	}
	if($db_type==2)
	{
	$year_cond=" and to_char(a.insert_date,'YYYY')=$data[7]";
	if ($data[3]!="" &&  $data[4]!="") $shipment_date = "and b.pub_shipment_date between '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[4], "yyyy-mm-dd", "-",1)."'"; else $shipment_date ="";
	}
	$order_cond="";
	$job_cond=""; 
	if($data[8]==1)
		{
		  if (str_replace("'","",$data[6])!="") $job_cond=" and a.job_no_prefix_num='$data[6]'  $year_cond";
		  if (trim($data[9])!="") $order_cond=" and b.po_number='$data[9]'  "; //else  $order_cond=""; 
		}
	
	if($data[8]==4 || $data[8]==0)
		{
		  if (str_replace("'","",$data[6])!="") $job_cond=" and a.job_no_prefix_num like '%$data[6]%'  $year_cond"; //else  $job_cond=""; 
		  if (trim($data[9])!="") $order_cond=" and b.po_number like '%$data[9]%'  ";
		}
	
	if($data[8]==2)
		{
		  if (str_replace("'","",$data[6])!="") $job_cond=" and a.job_no_prefix_num like '$data[6]%'  $year_cond"; //else  $job_cond=""; 
		  if (trim($data[9])!="") $order_cond=" and b.po_number like '$data[9]%'  ";
		}
	
	if($data[8]==3)
		{
		  if (str_replace("'","",$data[6])!="") $job_cond=" and a.job_no_prefix_num like '%$data[6]'  $year_cond"; //else  $job_cond=""; 
		  if (trim($data[9])!="") $order_cond=" and b.po_number like '%$data[9]'  ";
		}
	
	
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$arr=array (2=>$comp,3=>$buyer_arr,9=>$item_category);
	if ($data[2]==0)
	{
		if($db_type==0)
		{
	 		$sql= "select a.job_no_prefix_num, a.job_no,a.company_name,a.buyer_name,a.style_ref_no,a.job_quantity, b.po_number,b.po_quantity,b.shipment_date,a.garments_nature,b.plan_cut,SUBSTRING_INDEX(a.`insert_date`, '-', 1) as year from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.garments_nature=$data[5] and a.status_active=1 and b.status_active=1 $shipment_date $company $buyer $job_cond $order_cond order by a.job_no";
		}
	 	if($db_type==2)
		{
	 		$sql= "select a.job_no_prefix_num, a.job_no,a.company_name,a.buyer_name,a.style_ref_no,a.job_quantity, b.po_number,b.po_quantity,b.shipment_date,a.garments_nature,b.plan_cut,to_char(a.insert_date,'YYYY') as year,b.id from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.garments_nature=2 and a.status_active=1 and b.status_active=1 $shipment_date $company $buyer $job_cond $order_cond order by a.job_no";
		}
		//echo $sql;die;
		//echo $sql;
		 echo  create_list_view("list_view", "Job No,Year,Company,Buyer Name,Style Ref. No,Job Qty.,PO number,PO Quantity,Shipment Date,Gmts Nature", "50,60,120,100,100,90,90,90,80,80","1000","320",0, $sql , "js_set_value", "job_no,id,plan_cut,shipment_date,po_number,shipment_date,style_ref_no", "", 1, "0,0,company_name,buyer_name,0,0,0,0,0,garments_nature", $arr , "job_no_prefix_num,year,company_name,buyer_name,style_ref_no,job_quantity,po_number,plan_cut,shipment_date,garments_nature", "",'','0,0,0,0,0,1,0,1,3,0');
	}
	else
	{
		$arr=array (2=>$comp,3=>$buyer_arr,5=>$item_category);
		if($db_type==0)
		{
		$sql= "select a.job_no_prefix_num,a.job_no,a.company_name,a.buyer_name,a.style_ref_no,a.garments_nature,SUBSTRING_INDEX(a.`insert_date`, '-', 1) as year from wo_po_details_master a where a.job_no not in( select distinct job_no_mst from wo_po_break_down where status_active=1 and is_deleted=0 ) and a.garments_nature=$data[5] and a.status_active=1   and a.is_deleted=0 $company $buyer order by a.job_no";
		}
		if($db_type==2)
		{
		$sql= "select a.job_no_prefix_num,a.job_no,a.company_name,a.buyer_name,a.style_ref_no,a.garments_nature,to_char(a.insert_date,'YYYY') as year from wo_po_details_master a where a.job_no not in( select distinct job_no_mst from wo_po_break_down where status_active=1 and is_deleted=0 ) and a.garments_nature=$data[5] and a.status_active=1   and a.is_deleted=0 $company $buyer order by a.job_no";
		}
		
		
		echo  create_list_view("list_view", "Job No,Year,Company,Buyer Name,Style Ref. No,Gmts Nature", "90,60,120,100,100,90","1000","320",0, $sql , "js_set_value", "job_no", "", 1, "0,0,company_name,buyer_name,0,garments_nature", $arr , "job_no_prefix_num,year,company_name,buyer_name,style_ref_no,garments_nature", "",'','0,0,0,0,0,0');
	}
} 

if($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	if ($operation==0)  // Insert Here
	{
		$con = connect();
		if($db_type==0) mysql_query("BEGIN");
		
		$id=return_next_id( "id","ppl_sewing_plan_board", 1 ) ;
		$field_array="id,line_id,po_break_down_id,plan_id,start_date,start_hour,end_date,end_hour,duration,plan_qnty,comp_level,first_day_output,increment_qty,terget,inserted_by";
		$field_array_up="line_id*po_break_down_id*plan_id*start_date*start_hour*end_date*end_hour*duration*plan_qnty*comp_level*first_day_output*increment_qty*terget*updated_by";
		
		//"&num_row="+total_plan+"&poid="+poid+"&cmpid="++"&="+cmpstart+"&="+cmpinc+"&="+cmptarg+"&="+lineid+"&="+startdate+"&="+duratin+"&="+planqty+"&="+enddate
		$poid=explode("**",$poid);
		$cmpid=explode("**",$cmpid);
		$cmpstart=explode("**",$cmpstart);
		$cmpinc=explode("**",$cmpinc);
		$cmptarg=explode("**",$cmptarg);
		$lineid=explode("**",$lineid);
		$startdate=explode("**",$startdate);
		$starttime=explode("**",$starttime);
		$duratin=explode("**",$duratin);
		$planqty=explode("**",$planqty);
		$enddate=explode("**",$enddate);
		$updid=explode("**",$updid);
		$planid=explode("**",$planid);
		$isnew=explode("**",$isnew);
		$isedited=explode("**",$isedited);
		
		  
		
		//$poid=explode("**",$poid);
		//$poid=explode("**",$poid);
		for($i=0; $i<$num_row; $i++)
		{
			if( ( $isnew[$i]*1 )!=0 )
			{
				// echo $duratin[$i]; die;
				if($i==0) $id=return_next_id( "id","ppl_sewing_plan_board", 1 ) ; else $id++;
				if($data_array!="") $data_array.=",";
				$startdate[$i]=change_date_format( $startdate[$i],'','',1);
				$enddate[$i]=change_date_format( $enddate[$i],'','',1);
				$duratin[$i]=$duratin[$i]*1;
				$data_array.="(".$id.",".$lineid[$i].",".$poid[$i].",'".$id."','".$startdate[$i]."','".$starttime[$i]."','".$enddate[$i]."','0','".$duratin[$i]."','".$planqty[$i]."','".$cmpid[$i]."','".$cmpstart[$i]."','".$cmpinc[$i]."','".$cmptarg[$i]."',".$_SESSION['logic_erp']['user_id'].")"; 
			}
			else if( ( $isedited[$i]*1 )!=0 )
			{
				//if($data_array!="") $data_array.=",";
				//$data_array_up.="(".$id.",".$lineid[$i].",".$poid[$i].",'".$id."','".$startdate[$i]."','".$starttime[$i]."','".$enddate[$i]."','0','".$duratin[$i]."','".$planqty[$i]."','".$cmpid[$i]."','".$cmpstart[$i]."','".$cmpinc[$i]."','".$cmptarg[$i]."',".$_SESSION['logic_erp']['user_id'].")";
				$startdate[$i]=change_date_format( $startdate[$i],'','',1);
				$enddate[$i]=change_date_format( $enddate[$i],'','',1);
				$dur=datediff("d",$startdate[$i],$enddate[$i]);
				
				//$duratin[$i]=$duratin[$i]*1;
				$id_arr[]=$updid[$i];
				$data_array_up[$updid[$i]] =explode("*",("".$lineid[$i]."*".$poid[$i]."*'".$id."'*'".$startdate[$i]."'*'".$starttime[$i]."'*'".$enddate[$i]."'*'0'*'".$dur."'*'".$planqty[$i]."'*'".$cmpid[$i]."'*'".$cmpstart[$i]."'*'".$cmpinc[$i]."'*'".$cmptarg[$i]."'*".$_SESSION['logic_erp']['user_id'].""));
				
			}
		}
	
		if(count( $id_arr )>0)
		{
			$rID_up=execute_query(bulk_update_sql_statement( "ppl_sewing_plan_board", "id", $field_array_up, $data_array_up, $id_arr ));
		}
		else $rID_up=1;
			
	 	if($data_array!="")
			$rID=sql_insert("ppl_sewing_plan_board",$field_array,$data_array,1);
		else $rID=1;
			// echo "0**".$rID."=".$data_array; die;
		 
		
		//oci_commit($con);
		
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
			if( $rID && $rID_up )
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
}

if($action=="show_planning_details_bottom")
{
	//echo $data;
	$data=explode("__",$data);
	$company=return_library_array("select id,company_name from lib_company","id","company_name");
	$location=return_library_array("select id,location_name from lib_location","id","location_name");
	
	$sql_po= "select  a.job_no,a.company_name,a.location_name,a.buyer_name,a.style_ref_no,a.job_quantity, b.po_number,b.po_quantity,b.shipment_date,a.garments_nature,b.plan_cut,b.po_received_date,pub_shipment_date from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id=".$data[1]."";
	 
	$sql_data=sql_select($sql_po);
	foreach($sql_data as $row)
	{
		
		$podtls['company']=$company[$row[csf("company_name")]];
		$podtls['location_name']=$location[$row[csf("location_name")]];
		$podtls['job_no']=$row[csf("job_no")];
		$podtls['style_ref_no']=$row[csf("style_ref_no")];
		$podtls['po_number']=$row[csf("po_number")];
		$podtls['po_received_date']=$row[csf("po_received_date")];
		$podtls['shipment_date']=$row[csf("shipment_date")];
		$podtls['po_quantity']=$row[csf("po_quantity")];
		$podtls['plan_cut']=$row[csf("plan_cut")];
		$podtls['pub_shipment_date']=$row[csf("pub_shipment_date")];
	}
	$sql="select task_number,task_start_date,task_finish_date,actual_start_date,actual_finish_date from tna_process_mst where po_number_id=".$data[1]." and task_number in (60,64,84,70,86) order by task_number";
	$sql_data=sql_select($sql);
	foreach($sql_data as $row)
	{
		$tnadata[$row[csf("task_number")]]['start']=$row[csf("actual_start_date")];
		$tnadata[$row[csf("task_number")]]['end']=$row[csf("actual_finish_date")];
	}
	
	$sql="select task_number,task_start_date,task_finish_date,actual_start_date,actual_finish_date from tna_process_mst where po_number_id=".$data[1]." and task_number in (60,64,84,70,86) order by task_number";
	$sql_data=sql_select($sql);
	foreach($sql_data as $row)
	{
		$tnadata[$row[csf("task_number")]]['start']=$row[csf("actual_start_date")];
		$tnadata[$row[csf("task_number")]]['end']=$row[csf("actual_finish_date")];
	}
	
	$sql="SELECT po_break_down_id,sum(plan_cut_qnty) as plan_cut_qnty,sum(kint_fin_fab_qnty) as kint_fin_fab_qnty,sum(kint_grey_fab_qnty) as kint_grey_fab_qnty,sum(woven_fin_fab_qnty) as woven_fin_fab_qnty,sum(woven_grey_fab_qnty) as woven_grey_fab_qnty,sum(yarn_qnty) as yarn_qnty,sum(conv_qnty) as conv_qnty,sum(trim_qty) as trim_qty,sum(emb_cons) as emb_cons, sum(wash_cons) as wash_cons,sum(kint_grey_fab_qnty_prod) as kint_grey_fab_qnty_prod,sum(kint_fin_fab_qnty_prod) as kint_fin_fab_qnty_prod,sum(wash_cons) as wash_cons,sum(emb_cons) as emb_cons FROM wo_bom_process WHERE  po_break_down_id in ( ".$data[1]."  ) group by po_break_down_id";
	$data_array=sql_select($sql);
	foreach($data_array as $row)
	{ 
		$tna_task_update_data[60]['reqqnty']=$row[csf("kint_grey_fab_qnty_prod")];
		$tna_task_update_data[64]['reqqnty']=$row[csf("kint_fin_fab_qnty_prod")];
		//$tna_task_update_data[70]['reqqnty']=$row[csf("kint_grey_fab_qnty")];
		$tna_task_update_data[84]['reqqnty']=$podtls['po_quantity'];
		$tna_task_update_data[86]['reqqnty']=$podtls['po_quantity'];
	}
	//60	=> "Gray Fabric Production To Be Done",	
	//64	=> "Finish Fabric Production To Be Done",
	//70	=> "Sewing Trims To Be In-house",	
	//84	=> "Cutting To Be Done",		
	//86	=> "Sewing To Be Done",	
	
	$sql = "SELECT po_break_down_id, min(production_date) as mind,max(production_date) as maxd, production_type,sum(production_quantity) as production_quantity,embel_name FROM  pro_garments_production_mst  WHERE po_break_down_id in (  ".$data[1]." )   group by po_break_down_id,production_type,embel_name";
	$result = sql_select( $sql );
	foreach( $result as $row ) 
	{
		$tsktype=0;
		if ($row[csf("production_type")]==1) $tsktype=84;
		else if ($row[csf("production_type")]==3) $tsktype=85;
		else if ($row[csf("production_type")]==5) $tsktype=86;
		else if ($row[csf("production_type")]==7) $tsktype=87;
		else if ($row[csf("production_type")]==8) $tsktype=88; 
		else if ($row[csf("production_type")]==10) $tsktype=87;
		
		$tna_task_update_data[$tsktype]['max_start_date']=$row[csf("maxd")];
		$tna_task_update_data[$tsktype]['min_start_date']=$row[csf("mind")];
	//	$tna_task_update_data[$tsktype]['quantity']=$row[csf("production_quantity")];
		$tna_task_update_data[$tsktype]['doneqnty']=$row[csf("production_quantity")];
		//$tna_task_update_data[$tsktype]['reqqnty']=$po_order_details['po_quantity'];
	} 
	
	$production_days=return_field_value( "count(distinct(production_date)) as id", "pro_garments_production_mst", "po_break_down_id=".$data[1]." and production_type=5 group by  po_break_down_id", "id" );
	$daily_production=$tna_task_update_data[86]['doneqnty']/$production_days;
	
	$sql = "SELECT b.po_breakdown_id,b.entry_form, min(a.receive_date) mindate, max(a.receive_date) maxdate, sum(quantity) as prod_qntry 
FROM inv_receive_master a,  order_wise_pro_details b, pro_grey_prod_entry_dtls c where  c.mst_id=a.id and c.id=b.dtls_id and b.entry_form in ( 2 ) and b.po_breakdown_id in (  ".$data[1]." ) group by b.po_breakdown_id,b.entry_form order by b.po_breakdown_id";
	$data_array=sql_select($sql);
	foreach($data_array as $row)
	{
		$tna_task_update_data[60]['max_start_date']=$row[csf("maxdate")];
		$tna_task_update_data[60]['min_start_date']=$row[csf("mindate")];
		$tna_task_update_data[60]['doneqnty']=$row[csf("prod_qntry")]; 
	}
	
	$sql = "SELECT b.po_breakdown_id,b.entry_form, min(a.receive_date) mindate, max(a.receive_date) maxdate, sum(quantity) as prod_qntry 
FROM inv_receive_master a,  order_wise_pro_details b, pro_finish_fabric_rcv_dtls c where  c.mst_id=a.id and c.id=b.dtls_id and b.entry_form in ( 7 ) and b.po_breakdown_id in  (  ".$data[1]." ) group by b.po_breakdown_id,b.entry_form order by b.po_breakdown_id";

	$data_array=sql_select($sql);
	foreach($data_array as $row)
	{
		$tna_task_update_data[64]['max_start_date']=$row[csf("maxdate")];
		$tna_task_update_data[64]['min_start_date']=$row[csf("mindate")];
		$tna_task_update_data[64]['doneqnty']=$row[csf("prod_qntry")]; 
	}
	
	$sql = "SELECT b.po_breakdown_id, min(a.transaction_date) mindate, max(a.transaction_date) maxdate, sum(quantity) as prod_qntry,d.trim_type 
FROM inv_transaction a,  order_wise_pro_details b, product_details_master c , lib_item_group d
where a.prod_id=c.id and b.trans_id=a.id and c.item_group_id=d.id and d.trim_type in (1,2) and b.entry_form in ( 24 ) and b.po_breakdown_id in (  ".$data[1]." ) group by b.po_breakdown_id,d.trim_type order by b.po_breakdown_id";

	$data_array=sql_select($sql);
	foreach($data_array as $row)
	{
		$entry=($row[csf("trim_type")] == 1 ? 70 : 71);
		
		$tna_task_update_data[$entry]['max_start_date']=$row[csf("maxdate")];
		$tna_task_update_data[$entry]['min_start_date']=$row[csf("mindate")];
		$tna_task_update_data[$entry]['doneqnty']=$row[csf("prod_qntry")]; 
	}
	
	$line_enganed=return_field_value( "count(distinct(line_id)) as id", "ppl_sewing_plan_board", "po_break_down_id=".$data[1]." group by  po_break_down_id", "id" );
	
	$balance=$podtls['plan_cut']-$tna_task_update_data[86]['doneqnty'];
	$days_required=ceil($balance/$daily_production);
	$to_be_end=add_date(date("Y-m-d",time()),$days_required);
	
	$late_early=datediff("d",$to_be_end,$podtls['pub_shipment_date']);
	if($late_early<3)
	{
		$color="red";
	}
	$late_early=$late_early-2;
	?>
    <table width="100%" id="tbl_footer" cellspacing="0" cellpadding="2" border="1" rules="all">
                	<tr class="plan_foot_header">
                    	<td width="100" align="center">Company Name</td>
                        <td width="100" align="center">Location Name</td>
                        <td width="100" align="center">Job No</td>
                        <td width="100" align="center">Style Ref</td>
                        <td width="100" align="center">Order No</td>
                        <td width="100" align="center">Receive Date</td>
                        <td width="100" align="center">Shipment Date</td>
                        <td width="100" align="center">Order Quantity</td>
                        <td align="center">Planned Cut <span style="position:absolute; background-color:#CCC; border:1px solid #90F; right:2px; top:2px; color:red"><a style="text-decoration:none;color:red" href="##" onClick='$("#footer").hide(1000);'>&nbsp;X&nbsp;</a></span></td>
                    </tr>
                	<tr>
                    	<td  id="cid" align="center"><?php echo $podtls['company']; ?></td>
                        <td  id="cid" align="center"><?php echo $podtls['location_name']; ?></td>
                        <td  id="cid" align="center"><?php echo $podtls['job_no']; ?></td>
                        <td  id="cid" align="center"><?php echo $podtls['style_ref_no']; ?></td>
                        <td  id="cid" align="center"><?php echo $podtls['po_number']; ?></td>
                        <td  id="cid" align="center"><?php echo $podtls['po_received_date']; ?></td>
                        <td  id="cid" align="center"><?php echo $podtls['pub_shipment_date']; ?></td>
                        <td  id="cid" align="center"><?php echo $podtls['po_quantity']; ?></td>
                        <td  id="cid" align="center"><?php echo $podtls['plan_cut']; ?></td>
                    </tr>
                    
                    <tr>
                    	<td  align="center" colspan="9">
                        	<table width="100%" cellspacing="0" cellpadding="0"  border="1" rules="all">
                            	<tr>
                                	<td></td>
                                    <td>Knitting Start</td>
                                    <td>Knitting End</td>
                                    <td>Fin. Fab. Prod. Start</td>
                                    <td>Fin. Fab. Prod. End</td>
                                    <td>Cut Start</td>
                                    <td>Cut End</td>
                                    <td>Sew Trim Rev. Start</td>
                                    <td>Sew Trim Rev. End</td>
                                    <td>Sew. Start</td>
                                    <td>Sew. End</td>
                                    <td>Line Engaged</td>
                                    <td>Sew Prod./Day</td>
                                    <td>To Be End</td>
                                    <td>Early / Late By</td>
                                    <td>Suggestion</td>
                                </tr>
                                <tr>
                                	<td>As Per TNA</td>
                                    <td><?php echo $tnadata[60]['start']; ?></td>
                                    <td><?php echo $tnadata[60]['end']; ?></td>
                                    <td><?php echo $tnadata[64]['start']; ?></td>
                                    <td><?php echo $tnadata[64]['end']; ?></td>
                                    <td><?php echo $tnadata[84]['start']; ?></td>
                                    <td><?php echo $tnadata[84]['end']; ?></td>
                                    <td><?php echo $tnadata[70]['start']; ?></td>
                                    <td><?php echo $tnadata[70]['end']; ?></td>
                                    <td><?php echo $tnadata[86]['start']; ?></td>
                                    <td><?php echo $tnadata[86]['end']; ?></td>
                                    <td align="center" rowspan="4"><?php echo $line_enganed; ?></td>
                                    <td rowspan="4" align="center"><?php echo $daily_production; ?></td>
                                    <td rowspan="4" align="center" bgcolor="<? echo $color; ?>"><?php echo $to_be_end; ?></td>
                                    <td rowspan="4"align="center"><?php echo $late_early; ?></td>
                                    <td rowspan="4" align="center"></td>
                                </tr>
                                <tr>
                                	<td>Qty. Required</td>
                                    <td align="right" colspan="2"><?php echo $tna_task_update_data[60]['reqqnty']; ?></td>
                                     
                                    <td align="right" colspan="2"><?php echo $tna_task_update_data[64]['reqqnty']; ?></td>
                                     
                                    <td align="right" colspan="2"><?php echo $tna_task_update_data[84]['reqqnty']; ?></td>
                                     
                                    <td align="right" colspan="2"><?php echo $tna_task_update_data[70]['reqqnty']; ?></td>
                                     
                                    <td align="right" colspan="2"><?php echo $tna_task_update_data[86]['reqqnty']; ?></td>
                                      
                                </tr>
                                <tr>
                                	<td>Qty. Available</td>
                                    <td align="right" colspan="2"><?php echo $tna_task_update_data[60]['doneqnty']; ?></td> 
                                    <td align="right" colspan="2"><?php echo $tna_task_update_data[64]['doneqnty']; ?></td> 
                                    <td align="right" colspan="2"><?php echo $tna_task_update_data[84]['doneqnty']; ?></td> 
                                    <td align="right" colspan="2"><?php echo $tna_task_update_data[70]['doneqnty']; ?></td> 
                                    <td align="right" colspan="2"><?php echo $tna_task_update_data[86]['doneqnty']; ?></td> 
                                     
                                </tr>
                                <tr>
                                	<td>Qty. Balance</td>
                                    <td align="right" colspan="2"><?php echo $tna_task_update_data[60]['reqqnty']-$tna_task_update_data[60]['doneqnty']; ?></td> 
                                    <td align="right" colspan="2"><?php echo $tna_task_update_data[64]['reqqnty']-$tna_task_update_data[64]['doneqnty']; ?></td> 
                                    <td align="right" colspan="2"><?php echo $tna_task_update_data[84]['reqqnty']-$tna_task_update_data[84]['doneqnty']; ?></td> 
                                    <td align="right" colspan="2"><?php echo $tna_task_update_data[70]['reqqnty']-$tna_task_update_data[70]['doneqnty']; ?></td> 
                                    <td align="right" colspan="2"><?php echo $tna_task_update_data[86]['reqqnty']-$tna_task_update_data[86]['doneqnty']; ?></td> 
                                    
                                </tr>
                            </table>
                            
                        </td>
                    </tr>
                    
                    
                    
                </table>   
    <?
	//print_r($tnadata);
	//echo "==".$podtls['company'];
	die;
	  
}
function add_month($orgDate,$mon){
  $cd = strtotime($orgDate);
  //$retDAY = date('Y-m-d', mktime(0,0,0,date('m',$cd)+$mon,date('d',$cd),date('Y',$cd)));
  $retDAY = date('Y-m-d', mktime(0,0,0,date('m',$cd)+$mon,1,date('Y',$cd)));
  return $retDAY;
}
?>