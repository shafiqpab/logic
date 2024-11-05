<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Dash Board.
Functionality	:	
JS Functions	:
Created by		:	Fuad 
Creation date 	: 	
Updated by 		: 		
Update date		: 		   
QC Performed BY	:		
QC Date			:	
Comments		:
*/

session_start();
require_once('../../includes/common.php');
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
echo load_html_head_contents("Graph", "../../", "", $popup, $unicode, $multi_select, 1);
 
//--------------------------------------------------------------------------------------------------------------------
if($action=="opendate_popup")
{
	?>
	<script>
		function js_set_value()
		{
			if( form_validation('txt_date_from*txt_date_to','Select Start Date*Select End Date')==false)
			{
				return;
			}
			else
			{
				parent.emailwindow.hide();
			}
		}
	</script>
    </head>
	<body>
        <div align="center" style="width:100%;" >
            <form name="keydate_1"  id="keydate_1" autocomplete="off">
                <table width="290" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
                    <thead>
                         <tr>
                            <th width="50%">Start Date</th>
                            <th>End Date</th>
                         </tr>
                  	</thead>
                    <tr>
                    	<td align="center"><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:130px" placeholder="Start Date"></td>
                    	<td align="center"><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:130px" placeholder="End Date"></td>
                    </tr>
                    <tr>
                    	<td colspan="2" align="center"><input type="button" name="button2" class="formbutton" value="Close" onClick="js_set_value( document.getElementById('txt_date_from').value )" style="width:70px;" /></td>
                    </tr>
                 </table>
             </form>
             </div>
	</body>           
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
    <?
	exit();
}


if($_REQUEST['m']=='dGV4dGlsZV9ncmFwaA=='){
	
	list($lcCompany,$location,$floor,$workingCompany)=explode('__',$_REQUEST['cp']);
	list($start_date,$end_date)=explode('__',$_REQUEST['date_data']);
	$company_library 	=return_library_array( "select id, company_name from lib_company where status_active=1 and is_deleted=0", "id", "company_name");
?>	
    <script src="../../Chart.js-master/Chart.js"></script>
    <div style="margin:10px 0 0 10px; width:99%; border-bottom:1px solid #666; text-align:center">
    	<H2><? echo $lcCompany?$company_library[$lcCompany]:$company_library[$workingCompany];?></H2>
    </div>
	
    <div style="margin:10px 0 0 10px; width:99%">
        <div style="width:32.5%; height:300px; float:left; position:relative; border:solid 1px">
            <table width="100%" data-toggle="tooltip" title="Dyeing Loading in Tons">
            	<tr>
                	<td align="center"><strong>Dyeing Loading in Tons</strong></td>
                </tr>
            </table>
            <canvas id="canvas" height="300" width="500"></canvas>
		</div>
        
        
        
        <div style="width:32.5%; height:300px; float:left; position:relative; margin-left:10px; border:solid 1px">
        	<table width="100%" title="Compensate Booking Loading Qty/Total Dyeing Loading Qty * 100">
            	<tr>
                	<td align="center"><strong>% of Compensation on Production</strong></td>
                </tr>
            </table>
            <canvas id="canvas2" height="300" width="500"></canvas>
		</div>
        
        
        <div style="width:32.5%; height:300px; float:left; position:relative; margin-left:10px; border:solid 1px">
        	<table width="100%" title="Dyeing Loading With Extension Batch /Total Dyeing Loading * 100">
            	<tr>
                	<td align="center"><strong>% of Ex-lot (Re-Process) on Production</strong></td>
                </tr>
            </table>
            <canvas id="canvas3" height="300" width="500"></canvas>
		</div>
        
       <div style="width:32.5%; height:300px;margin:10px 0 0 0; float:left; position:relative; border:solid 1px">
        	<table width="100%" title="Formula: total dyes chemical cost/Total batch weight">
            	<tr>
                	<td align="center"><strong>Dyes & Chemicals Cost Per Kg in Taka</strong></td>
                </tr>
            </table>
            <canvas id="canvas4" height="300" width="500"></canvas>
		</div>
        
        
       <!-- <div style="width:32.5%; height:300px;margin:10px 0 0 10px; float:left; position:relative; border:solid 1px">
        	<table width="100%">
            	<tr>
                	<td align="center">Knitting Process Loss</td>
                </tr>
            </table>
            <canvas id="canvas5" height="300" width="500"></canvas>
		</div>-->
        
        
	</div>
	<?
		
		
		
		$month_array=array();	
        $month_prev=date("Y-m-d",strtotime($start_date));
        $month_next=date("Y-m-d",strtotime($end_date));
		$remain_months=datediff( "m",$month_prev,$month_next);
		
		
        $start_yr=date("Y",strtotime($month_prev));
        $end_yr=date("Y",strtotime($month_next));
        for($e=0;$e<=$remain_months;$e++)
        {
            $tmp=add_month(date("Y-m-d",strtotime($month_prev)),$e);
            $yr_mon_part[$e]=date("Y-m",strtotime($tmp));
            //$month_array[$e]=date("M",strtotime($tmp))." '".date("y",strtotime($tmp));
            $month_array[$e]=date("M y",strtotime($tmp));
        }
         $month_array[$e]='Avg';
		
		$month_array= json_encode($month_array); 
	
		if($db_type==0)
		{
			$start_date=change_date_format($start_date,'YYYY-MM-DD');
			$end_date=change_date_format($end_date,'YYYY-MM-DD');
		}
		else if($db_type==2)
		{
			$start_date=change_date_format($start_date,'','',-1);
			$end_date=change_date_format($end_date,'','',-1);
		}
	
	//------------------------------------------------------------------------------------------------------
		
	
	//Daying Loading data.....................................start;
	if($workingCompany){$company_cond=" and a.service_company=$workingCompany";}else{$company_cond=" and a.company_id=$lcCompany";}
	if($start_date!="" && $end_date!=""){$sql_cond=" and a.process_end_date between '$start_date' and '$end_date'";}
	
	//$re_process_sql="select a.batch_ext_no,a.batch_id,a.process_end_date,b.production_qty as production_qty from pro_fab_subprocess a, pro_fab_subprocess_dtls b where a.id=b.mst_id $sql_cond $company_cond and a.load_unload_id = 1 and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0";
	
	
	
	$re_process_sql="select c.total_trims_weight,a.batch_ext_no,a.batch_id,a.process_end_date,sum(b.production_qty) as production_qty from pro_fab_subprocess a, pro_fab_subprocess_dtls b, pro_batch_create_mst c  where a.id=b.mst_id and a.batch_id=c.id  $sql_cond $company_cond and a.load_unload_id = 1  and c.batch_against in(1,2) and a.status_active=1 and a.is_deleted=0 and a.entry_form=35 and c.entry_form=0 and b.status_active=1 and b.is_deleted=0 and  c.status_active=1 and c.is_deleted=0 group by c.total_trims_weight,a.batch_ext_no,a.batch_id,a.process_end_date";
	
	
	
	
//"select f.insert_date,a.batch_no, a.batch_weight,a.id as batch_id, a.color_id, a.color_range_id, a.extention_no, a.total_trims_weight,(b.batch_qnty) as batch_qnty, b.item_description, b.po_id, b.prod_id, b.width_dia_type,d.job_no_prefix_num,d.buyer_id, d.po_buyer,f.remarks, f.shift_name, f.production_date as process_end_date,f.process_end_date as production_date, f.end_hours, f.floor_id, f.hour_unload_meter, f.water_flow_meter, f.end_minutes, f.machine_id, f.load_unload_id,f.fabric_type, f.result, a.booking_no, a.booking_without_order, d.season, d.style_ref_no , b.barcode_no, f.process_id ,f.ltb_btb_id,d.within_group from pro_batch_create_dtls b, fabric_sales_order_mst d, pro_fab_subprocess f, pro_batch_create_mst a where f.batch_id=a.id and a.company_id=1 and a.working_company_id='1' and f.process_end_date BETWEEN '01-Jun-2018' AND '23-Jun-2018' and to_char(a.insert_date,'YYYY')=2018 and a.entry_form=0 and a.id=b.mst_id and f.batch_id=b.mst_id and f.entry_form=35 and f.load_unload_id=2 and a.batch_against in(1,2) and b.po_id=d.id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and f.status_active=1 and f.is_deleted=0 and a.is_sales=1 and b.is_sales=1 order by f.process_end_date,f.machine_id ";	
	
	
	
	
	
	
	
	
	
	$re_process_sql_result = sql_select($re_process_sql);
	foreach($re_process_sql_result as $row){
		$monthKey=date("Y-m",strtotime($row[csf('process_end_date')]));
		$batch_qty_data_array[$monthKey]+=($row[csf('production_qty')]+$row[csf('total_trims_weight')]);
		$re_process_qty_data_array[$monthKey]+=$row[csf('production_qty')]+$row[csf('total_trims_weight')];
		if($row[csf('batch_ext_no')]!=''){
			$batch_ext_no_qty_data_array[$monthKey]+=$row[csf('production_qty')]+$row[csf('total_trims_weight')];
		}
		else
		{
			$batch_ext_no_qty_data_array[$monthKey]+=0;
		}
		
		$batch_id_arr[$row[csf('batch_id')]]=$row[csf('batch_id')];
		
	}
	
	foreach($yr_mon_part as $Ym){
		$loading_qty_array[]=number_format($batch_qty_data_array[$Ym]/1000,2,".","");
	}
	$loading_qty_array[]=number_format(array_sum($loading_qty_array)/($remain_months+1),2,".","");	
	
	//Daying Loading data.....................................end;
	
		
	//Re-Process data.....................................start;
	foreach($yr_mon_part as $Ym){
		$re_process_qty_array[]=number_format(($batch_ext_no_qty_data_array[$Ym]*100)/$re_process_qty_data_array[$Ym],2,".","");
	}
	$re_process_qty_array[]=number_format(array_sum($re_process_qty_array)/($remain_months+1),2,".","");	
	//Re-Processg data.....................................end;
		
        
	//% of Compensation on Production.....................................start;
	
	if($workingCompany){$company_cond=" and e.company_id=$workingCompany";}else{$company_cond=" and e.company_id=$lcCompany";}
	if($start_date!="" && $end_date!=""){$sql_cond=" and e.process_end_date between '$start_date' and '$end_date'";}
		
	
		
	$short_fb_sql=" select d.total_trims_weight,e.process_end_date,sum(f.production_qty) as production_qty
	 from 
		 wo_booking_mst a, 
		 fabric_sales_order_mst c, 
		 pro_batch_create_mst d, 
		 pro_fab_subprocess e, 
		 pro_fab_subprocess_dtls f
	 where
	  a.short_booking_type=2 and
	  a.booking_no=c.sales_booking_no and
	  c.sales_booking_no=d.booking_no and
	  d.batch_no=e.batch_no and
	  e.id=f.mst_id and
	  d.is_sales=1 and
	  a.status_active=1 and a.is_deleted=0 and
	  c.status_active=1 and c.is_deleted=0 and
	  d.status_active=1 and d.is_deleted=0 and
	  e.status_active=1 and e.is_deleted=0 and
	  f.status_active=1 and f.is_deleted=0 and
	  e.id=f.mst_id $sql_cond $company_cond and e.load_unload_id = 1
	 group by d.total_trims_weight,e.process_end_date
	  ";	
	  
	  
	$short_fb_qty_compensative_data_array=array();
	$short_fb_sql_result = sql_select($short_fb_sql);
	foreach($short_fb_sql_result as $row){
		$monthKey=date("Y-m",strtotime($row[csf('process_end_date')]));
		$short_fb_qty_compensative_data_array[$monthKey]+=$row[csf('production_qty')]+$row[csf('total_trims_weight')];
	}
	
	foreach($yr_mon_part as $Ym){
		
		$parcent_of_compensation_on_production_qty_array[]=number_format(($short_fb_qty_compensative_data_array[$Ym]/$batch_qty_data_array[$Ym])*100,2,".","");
	}
	
		$parcent_of_compensation_on_production_qty_avg=(array_sum($parcent_of_compensation_on_production_qty_array)/($remain_months+1));
		
		$parcent_of_compensation_on_production_qty_array[]=number_format($parcent_of_compensation_on_production_qty_avg,2,".","");	
	//% of Compensation on Production.....................................end;

		
		
		
		
	if($workingCompany){$company_cond=" and a.company_id=$workingCompany";}else{$company_cond=" and a.company_id=$lcCompany";}
	if($start_date!="" && $end_date!=""){$sql_cond=" and a.batch_date between '$start_date' and '$end_date'";}
		
		
		$batch_weight_sql="select a.batch_weight,a.batch_date from pro_batch_create_mst a where status_active=1 $sql_cond $company_cond";
		$batch_weight_sql_result = sql_select($batch_weight_sql);
		foreach($batch_weight_sql_result as $row){
			$monthKey=date("Y-m",strtotime($row[csf('batch_date')]));
			$batch_weight_data_array[$monthKey]+=$row[csf('batch_weight')];
		}
		
		//var_dump($batch_weight_data_array);
		
	////	
	if($workingCompany){$company_cond=" and a.company_id=$workingCompany";}else{$company_cond=" and a.company_id=$lcCompany";}
	if($start_date!="" && $end_date!=""){$sql_cond=" and a.transaction_date between '$start_date' and '$end_date'";}
	
	$dys_issue_sql="select 
                a.transaction_date,sum(case when a.transaction_type in(2,3,6) then a.cons_amount else 0 end) as cons_amount      
            from
                inv_transaction a
            where
                a.item_category in(5,6,7,23) and a.status_active=1 $company_cond $sql_cond group by a.transaction_date";	


		$dys_issue_sql_result = sql_select($dys_issue_sql);
		foreach($dys_issue_sql_result as $val){
			$monthKey=date("Y-m",strtotime($val[csf('transaction_date')]));
			$dyc_amount_data_array[$monthKey]+=$val[csf('cons_amount')];
		}


		
	////////////////	
		
		foreach($yr_mon_part as $Ym){
			$batch_weight_array[]=number_format($dyc_amount_data_array[$Ym]/$batch_weight_data_array[$Ym],2,".","");
		}
	
		$batch_weight_array[]=number_format(array_sum($batch_weight_array)/($remain_months+1),2,".","");
		
		
	
		
		//json---------------------------------------------------------------------------------
		$loading_qty_array= json_encode($loading_qty_array);
		$parcent_of_compensation_on_production_qty_array= json_encode($parcent_of_compensation_on_production_qty_array); 
		$re_process_qty_array= json_encode($re_process_qty_array); 
		$batch_weight_array= json_encode($batch_weight_array);
		
    ?>
    <script>
        var barChartData = {
            labels : <? echo $month_array; ?>,
            datasets : [
                {
                    fillColor : "rgba(252,177,40,0.99)",
                   //strokeColor : "rgba(151,187,205,0.8)",
                    highlightFill : "rgba(252,177,40,0.1)",
                    //highlightStroke : "rgba(151,187,205,1)",
                    data : <? echo $loading_qty_array; ?>
                }
            ]
        }
       
        var barChartData2 = {
            labels : <? echo $month_array; ?>,
            datasets : [
                {
                    fillColor : "rgba(236,21,40,0.99)",
                    //strokeColor : "rgba(220,220,220,0.8)",
                    highlightFill :  "rgba(236,21,40,0.1)",
                    //highlightStroke: "rgba(220,220,220,1)",
                    data : <? echo $parcent_of_compensation_on_production_qty_array; ?>
                }
            ]
        }
        
        var barChartData3 = {
            labels : <? echo $month_array; ?>,
            datasets : [
                {
                    fillColor : "rgba(100,220,220,0.99)",
                    //strokeColor : "rgba(220,220,220,0.8)",
                    highlightFill : "rgba(100,220,220,0.1)",
                    //highlightStroke: "rgba(220,220,220,1)",
                    data : <? echo $re_process_qty_array; ?>
                }
            ]
        }
        
        var barChartData4 = {
            labels : <? echo $month_array; ?>,
            datasets : [
                {
                    fillColor : "rgba(120,210,22,0.99)",
                    highlightFill : "rgba(120,210,22,0.1)",
                    data : <? echo $batch_weight_array; ?>
                }
            ]

        }
        
      /*  var barChartData5 = {
            labels : <? //echo $month_array; ?>,
            datasets : [
                {
                    fillColor : "rgba(100,100,220,0.99)",
                    highlightFill : "rgba(100,100,220,0.1)",
                    data : <? //echo $re_process_qty_array; ?>
                }
            ]
        }*/
	
    
        window.onload = function(){
            var ctx = document.getElementById("canvas").getContext("2d");
            window.myBar = new Chart(ctx).Bar(barChartData, {
                responsive : true
            });
            
            var ctx2 = document.getElementById("canvas2").getContext("2d");
            window.myBar = new Chart(ctx2).Bar(barChartData2, {
                responsive : true
            });
            
            var ctx3 = document.getElementById("canvas3").getContext("2d");
            window.myBar = new Chart(ctx3).Bar(barChartData3, {
                responsive : true
            });
            
			
            var ctx4 = document.getElementById("canvas4").getContext("2d");
            window.myBar = new Chart(ctx4).Bar(barChartData4, {
                responsive : true
            });
			
          /*  var ctx5 = document.getElementById("canvas5").getContext("2d");
            window.myLine = new Chart(ctx5).Bar(barChartData5, {
                responsive: true
            });*/
			
			
        }
		

    </script>
 
<?

}

function add_month($orgDate,$mon){
  $cd = strtotime($orgDate);
  $retDAY = date('Y-m-d', mktime(0,0,0,date('m',$cd)+$mon,1,date('Y',$cd)));
  return $retDAY;
}


?>
