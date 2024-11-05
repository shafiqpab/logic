<? 
session_start();
include('../../includes/common.php');
echo load_html_head_contents("Graph", "../../", "", $popup, $unicode, $multi_select, 1);
function add_month($orgDate,$mon){
	$cd = strtotime($orgDate);
	$retDAY = date('Y-m-d', mktime(0,0,0,date('m',$cd)+$mon,1,date('Y',$cd)));
	return $retDAY;
}

extract($_REQUEST);
$m= base64_decode($m);
list($company,$pro_company,$location)=explode("__",$cp);

if($company!=0) $str_comp=" and comp.id=$company";
$_SESSION['logic_erp']["data"]='';

?>
 
<script>
 var lnk='<? echo $m; ?>';
	function change_color(v_id,e_color)
	{
		var clss;
		$('td').click(function() {
			var myCol = $(this).index();
			clss='res'+myCol;
		
		});
		
		if (document.getElementById(v_id).bgColor=="#33CC00")
		{
			document.getElementById(v_id).bgColor=e_color;
			$('.'+clss).removeAttr('bgColor');
		}
		else
		{
			document.getElementById(v_id).bgColor="#33CC00";
			$('.'+clss).attr('bgColor','#33CC00');
		}
	}
	
	function show_summary()
	{
		page_link='summary_popup.php?action=summary_popup';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe',page_link,'Detail View', 'width=800px, height=400px, center=1, resize=0, scrolling=0','');
	}
	
	function show_summary_val()
	{
		page_link='summary_popup.php?action=summary_popup_value';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe',page_link,'Detail View', 'width=800px, height=400px, center=1, resize=0, scrolling=0','');
	}
	
</script>
<?
	if($db_type==0) 
	{
		$manufacturing_company=return_field_value("group_concat(comp.id)","lib_company as comp","comp.core_business=1 and comp.status_active=1 and comp.is_deleted=0 $str_comp $company_cond");
	}
	else
	{
		$manufacturing_company= return_field_value("LISTAGG(comp.id, ', ') WITHIN GROUP (ORDER BY comp.id) company","lib_company comp", "comp.core_business=1 and comp.status_active=1 and comp.is_deleted=0 $str_comp $company_cond","company");
	}
	
	$no_of_company=count(explode(",",$manufacturing_company));
	
	if( $_SESSION['logic_erp']["data"]=="")
	{
		$date=date("Y",time());
		$month_prev=add_month(date("Y-m-d",time()),-3);
		$month_next=add_month(date("Y-m-d",time()),8);
		
		$start_yr=date("Y",strtotime($month_prev));
		$end_yr=date("Y",strtotime($month_next));
		for($e=0;$e<=11;$e++)
		{
			$tmp=add_month(date("Y-m-d",strtotime($month_prev)),$e);
			$yr_mon_part[$e]=date("Y-m",strtotime($tmp));
		}
		
		$exFactory_arr=array();
		$data_arr=sql_select( "select po_break_down_id, sum(ex_factory_qnty) as ex_factory_qnty from pro_ex_factory_mst where status_active=1 and is_deleted=0 group by po_break_down_id");
		foreach($data_arr as $row)
		{
			$exFactory_arr[$row[csf('po_break_down_id')]]=$row[csf('ex_factory_qnty')];
		}
		
		$i=1; $html='<tbody>'; $html2='<tbody>';
		
			
	
	
	if($db_type==0)
	{
		$prev_date = date('Y-m-d', strtotime($month_prev));
		$next_date = date("Y-m-",strtotime($month_next)).cal_days_in_month(CAL_GREGORIAN, date("m",strtotime($month_next)), date("Y",strtotime($month_next)));
	}
	else
	{
		$prev_date = change_date_format(date('Y-m-d', strtotime($month_prev)),'','',1);
		$next_date = change_date_format(date("Y-m-",strtotime($month_next)).cal_days_in_month(CAL_GREGORIAN, date("m",strtotime($month_next)), date("Y",strtotime($month_next))),'','',1);
	}
	
	$sqlProduction="select b.COMPANY_ID, a.PUB_SHIPMENT_DATE , sum(b.PRODUCTION_QUANTITY) as PRODUCTION_QUANTITY from wo_po_break_down a,PRO_GARMENTS_PRODUCTION_MST b 
	where a.id=b.PO_BREAK_DOWN_ID and b.PRODUCTION_TYPE=5 and a.IS_DELETED=0 and a.STATUS_ACTIVE=1 and b.IS_DELETED=0 and b.STATUS_ACTIVE=1 AND b.COMPANY_ID=$company and a.PUB_SHIPMENT_DATE between '$prev_date' and '$next_date'
	group by b.COMPANY_ID, a.PUB_SHIPMENT_DATE";
	$sqlProductionResult=sql_select($sqlProduction);
	$productionDataArr=array();
	foreach($sqlProductionResult as $row)
	{ 
		$key=date('Y-m',strtotime($row[PUB_SHIPMENT_DATE]));
		$productionDataArr[$key][SEWING_OUTPUT]+=$row[PRODUCTION_QUANTITY];
	}
	
	
	if($db_type==0) 
	{
	
		$sql="select b.PUB_SHIPMENT_DATE,b.id as PO_ID, c.total_set_qnty as RATIO, b.UNIT_PRICE,
		sum(CASE WHEN b.is_confirmed=1 THEN b.po_total_price ELSE 0 END) AS CONFPOVAL, 
		sum(CASE WHEN b.is_confirmed=2 THEN b.po_total_price ELSE 0 END) AS PROJPOVAL ,
		sum(CASE WHEN b.is_confirmed=1 THEN (b.po_quantity*c.total_set_qnty) ELSE 0 END) AS CONFPOQTY, 
		sum(CASE WHEN b.is_confirmed=2 THEN (b.po_quantity*c.total_set_qnty) ELSE 0 END) AS PROJPOQTY 
		from wo_po_break_down as b, wo_po_details_master as c where b.job_no_mst=c.job_no and c.company_name=$company and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and b.pub_shipment_date between '$prev_date' and '$next_date' group by b.id";

	}
	else
	{
		$sql="select b.PUB_SHIPMENT_DATE,b.id as PO_ID, c.total_set_qnty as RATIO, b.UNIT_PRICE,
		sum(CASE WHEN b.is_confirmed=1 THEN b.po_total_price ELSE 0 END) AS CONFPOVAL, 
		sum(CASE WHEN b.is_confirmed=2 THEN b.po_total_price ELSE 0 END) AS PROJPOVAL ,
		sum(CASE WHEN b.is_confirmed=1 THEN (b.po_quantity*c.total_set_qnty) ELSE 0 END) AS CONFPOQTY, 
		sum(CASE WHEN b.is_confirmed=2 THEN (b.po_quantity*c.total_set_qnty) ELSE 0 END) AS PROJPOQTY 
		from wo_po_break_down b, wo_po_details_master c where b.job_no_mst=c.job_no and c.company_name=$company and  b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and b.pub_shipment_date between '$prev_date' and '$next_date' group by b.id,b.pub_shipment_date, c.total_set_qnty,b.unit_price";	 
	}
			
	$result=sql_select($sql);
	$orderDataArr=array();
	foreach($result as $row)
	{ 
		$key=date('Y-m',strtotime($row[PUB_SHIPMENT_DATE]));
		$orderDataArr[$key][CONFPOVAL]+=$row[CONFPOVAL];
		$orderDataArr[$key][PROJPOVAL]+=$row[PROJPOVAL];
		$orderDataArr[$key][CONFPOQTY]+=$row[CONFPOQTY];
		$orderDataArr[$key][PROJPOQTY]+=$row[PROJPOQTY];
		$orderDataArr[$key][EXFACTORYQTY]+=$exFactory_arr[$row[PO_ID]];
		$orderDataArr[$key][EXFACTORYVAL]+=$exFactory_arr[$row[PO_ID]]*($row[UNIT_PRICE]/$row[RATIO]);
	}
		
		//print_r($yr_mon_part);die;
		
		foreach($yr_mon_part as $key=>$val)
		{
			$confPoQty=$orderDataArr[$val][CONFPOQTY]; 
			$projPoQty=$orderDataArr[$val][PROJPOQTY]; 
			$confPoVal=$orderDataArr[$val][CONFPOVAL]; 
			$projPoVal=$orderDataArr[$val][PROJPOVAL]; 
			$exFactoryQty=$orderDataArr[$val][EXFACTORYQTY]; 
			$exFactoryVal=$orderDataArr[$val][EXFACTORYVAL];
			
			
			$conf_tot_for_graph_stack[$key]=$confPoQty;
			$proj_tot_for_graph_stack[$key]=$projPoQty;
			
			$conf_tot_for_graph_stack_val[$key]=$confPoVal;
			$proj_tot_for_graph_stack_val[$key]=$projPoVal;
			
			$totQty=$projPoQty+$confPoQty;
			$perc=($exFactoryQty/$totQty)*100;
			$tot_for_graph[$key]=$totQty;
			
			$totVal=$projPoVal+$confPoVal;
			$perc_val=($exFactoryVal/$totVal)*100;
			$tot_for_graph_val[$key]=$totVal;
			
			if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			
			$html.='<tr bgcolor="'.$bgcolor.'" onClick="change_color('."'tr_".$i."','".$bgcolor."'".')" id="tr_'.$i.'">';
			$html.='<td align="center">'.date("M",strtotime($val))."-".date("y",strtotime($val)).'</td>
					<td align="right">'.fn_number_format($projPoQty,0).'</td>
					<td align="right">'.fn_number_format($confPoQty,0).'</td>
					<td align="right">'.fn_number_format($totQty,0).'</td>
					<td align="right">'.fn_number_format($productionDataArr[$val][SEWING_OUTPUT],0).'</td>
					<td align="right">'.fn_number_format($totQty-$productionDataArr[$val][SEWING_OUTPUT],0).'</td>
					
					<td align="right">'.fn_number_format($exFactoryQty,0).'</td>
					<td align="right">'.fn_number_format($perc,2).'</td>';
			$html.='</tr>';
			
			$html2.='<tr bgcolor="'.$bgcolor.'" onClick="change_color('."'tr_".$i."','".$bgcolor."'".')" id="tr_'.$i.'">';
			$html2.='<td>'.date("M",strtotime($val))."'".date("y",strtotime($val)).'</td>
					<td align="right">'.fn_number_format($projPoVal,0).'</td>
					<td align="right">'.fn_number_format($confPoVal,0).'</td>
					<td align="right">'.fn_number_format($totVal,0).'</td>
					<td align="right">'.fn_number_format($exFactoryVal,0).'</td>
					<td align="right">'.fn_number_format($perc_val,2).'</td>';
			$html2.='</tr>';
			
			$totProjQty+=$projPoQty;
			$totConfQty+=$confPoQty;  
			$totExFactoryQty+=$exFactoryQty; 
			$grandTotQty+=$totQty;
			$grandTotSewOutputQty+=$productionDataArr[$val][SEWING_OUTPUT];
			
			$totProjVal+=$projPoVal;
			$totConfVal+=$confPoVal;  
			$totExFactoryVal+=$exFactoryVal; 
			$grandTotVal+=$totVal;

			$i++;
		}
		
		$totPerc=($totExFactoryQty/$grandTotQty)*100;
		$html.='</tr></tbody><tfoot><th>Total</th>'; 
        $html.='<th align="right">'.fn_number_format($totProjQty,0).'</th>
				<th align="right">'.fn_number_format($totConfQty,0).'</th>
				<th align="right">'.fn_number_format($grandTotQty,0).'</th>
				<th align="right">'.fn_number_format($grandTotSewOutputQty,0).'</th>
				<th align="right">'.fn_number_format($grandTotQty-$grandTotSewOutputQty,0).'</th>
                <th align="right">'.fn_number_format($totExFactoryQty,0).'</th>
				<th align="right">'.fn_number_format($totPerc,2).'</th></tfoot>'; 
		
		$totPercVal=($totExFactoryVal/$grandTotVal)*100;		
        $html2.='</tr></tbody><tfoot><th>Total</th>'; 
        $html2.='<th align="right">'.fn_number_format($totProjVal,0).'</th>
				<th align="right">'.fn_number_format($totConfVal,0).'</th>
				<th align="right">'.fn_number_format($grandTotVal,0).'</th>
				<th align="right">'.fn_number_format($totExFactoryVal,0).'</th>
				<th align="right">'.fn_number_format($totPercVal,2).'</th></tfoot>'; 

		
		
		$catg="[";
		for($i=0;$i<=11;$i++)
		{
			if($i!=11) $catg .="'".date("M",strtotime($yr_mon_part[$i]))."',"; else $catg .="'".date("M",strtotime($yr_mon_part[$i]))."']";
		}
		
		$sql_comp=sql_select("select comp.id as id, comp.company_name,company_short_name from lib_company comp where comp.core_business=1 and comp.status_active=1 and comp.is_deleted=0 $str_comp $company_cond order by comp.id asc");
		
		$lib_variable_settings=return_library_array( "select company_name, capacity_in_value from variable_settings_commercial where  variable_list=5", "company_name", "capacity_in_value"  );
		
		$data=0;
		$k=1;
		//$data .="["; 
		$data_qnt .="[";
		$com=0;
		foreach($sql_comp as $row_comp)
		{
			$val=$lib_variable_settings[$row_comp[csf('id')]];
			$capacity="Capacity: ".$row_comp[csf('company_short_name')].": $ ".fn_number_format($val,2,'.',',');

			
			//$data .="{ name: '".$row_comp[csf('company_short_name')]."', data:[";
			$data_qnt .="{ name: '".$row_comp[csf('company_name')]."', data:[";
			
			for($i=0;$i<=11;$i++)
			{
				//$value=$orderDataArr[$yr_mon_part[$i]][CONFPOVAL]+$orderDataArr[$yr_mon_part[$i]][PROJPOVAL];
				$qty=$orderDataArr[$yr_mon_part[$i]][CONFPOQTY]+$orderDataArr[$yr_mon_part[$i]][PROJPOQTY];
	
				if( $i!=11) 
				{
					//$data .=fn_number_format( $value,0,'.','').",";
					$data_qnt .=fn_number_format( $qty,0,'.','').",";
				}
				else 
				{
					//$data .=fn_number_format( $value,0,'.','').""; 
					$data_qnt .=fn_number_format( $qty,0,'.','').""; 
				}
			}
			
			//$data .="], stack: 'none'}] ";
			$data_qnt .="], stack: 'none'}, ";
			
			
			//--------------------------production
			
			$data_qnt .="{ name: 'Sewing Balance Qty', data:[";
			
			for($i=0;$i<=11;$i++)
			{
				$balance_qty=(($orderDataArr[$yr_mon_part[$i]][CONFPOQTY]+$orderDataArr[$yr_mon_part[$i]][PROJPOQTY])-$productionDataArr[$yr_mon_part[$i]][SEWING_OUTPUT]);
	
				if( $i!=11) 
				{
					$data_qnt .=fn_number_format( $balance_qty,0,'.','').",";
				}
				else 
				{
					$data_qnt .=fn_number_format( $balance_qty,0,'.','').""; 
				}
			}
			$data_qnt .="], stack: 'none'}] ";
		}
		
		$_SESSION['logic_erp']["data"]=$data;
		$_SESSION['logic_erp']["data_qnt"]=$data_qnt;
		$_SESSION['logic_erp']["capacity"]=$capacity;
		$_SESSION['logic_erp']["catg"]=$catg;
		
		$_SESSION['logic_erp']["data_summ_qty"]=$html;
		$_SESSION['logic_erp']["data_summ_val"]=$html2;
	}
	else
	{
		$data=$_SESSION['logic_erp']["data"];
		$data_qnt=$_SESSION['logic_erp']["data_qnt"];
		$capacity=$_SESSION['logic_erp']["capacity"];
		$catg=$_SESSION['logic_erp']["catg"];
		
		$html=$_SESSION['logic_erp']["data_summ_qty"];
		$html2=$_SESSION['logic_erp']["data_summ_val"];
	}
	//echo $data_qnt_stck;

?>


	<table width="1050" cellpadding="0" cellspacing="0">
        <tr>
            <td height="30" valign="middle" align="center" colspan="2">
                <font size="2" color="#4D4D4D"> <strong><span id="caption_text"></span> <? // echo "$start_yr"."-"."$end_yr"; ?></strong></font>
            </td>
            <td colspan="2" rowspan="2" valign="top" align="center"> 
                <div style="margin-left:5px; margin-top:45px">
                    <table class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" width="360" id="tableQty">
                        <thead>
                            <th width="55">Month</th>
                            <th>Proj.</th>
                            <th>Conf.</th>
                            <th>Total Order Qty</th>
                            <th>Total Sewing Qty</th>
                            <th>Sewing Balance Qty</th>
                            <th>Ship Out</th>
                            <th>%</th>
                        </thead>
                        <? echo $html; ?>
                    </table>
                    <table class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" width="360" id="tableVal">
                        <thead>
                            <th width="55">Month</th>
                            <th>Proj.</th>
                            <th>Conf.</th>
                            <th>Total</th>
                            <th>Ship Out</th>
                            <th>%</th>
                        </thead>
                        <? echo $html2; ?>
                    </table>
                </div>
            </td>
        </tr>
        <tr>
            <td width="8" bgcolor=" ">
            <td align="center" height="400" width="750">
                <div id="chartdiv" style="width:750px; height:400px; background-color:#FFFFFF"></div>
            </td>
             
        </tr>
        <tr>
            <td height="8" colspan="2" bgcolor=" "></td>
            <td width="8" bgcolor=""></td>
            <td></td>
        </tr>
        <!-- <tr>
            <td colspan="2">
                <table width="100%">
                    <tr>
                        <td width="150"></td>
                        <td  align="right" valign="top">Copyright</td>
                        <td align="right" valign="top" width="310"><img src="../../images/logic/logic_bottom_logo.png" height="65" width="300" /> 
                        </td>
                    </tr>
                </table>
            </td>
             <td colspan="7" ></td>
        </tr> -->
    </table>
  
<script src="../ext_resource/hschart/hschart.js"></script>

<script>

Highcharts.theme = {
   colors: ["#7cb5ec", "#f7a35c", "#90ee7e", "#7798BF", "#aaeeee", "#ff0066", "#eeaaee",
      "#55BF3B", "#DF5353", "#7798BF", "#aaeeee"],
   chart: {
      backgroundColor: null, //null
      style: {
         fontFamily: "Dosis, sans-serif"
      }
   },
   title: {
      style: {
         fontSize: '16px',
         fontWeight: 'bold',
         textTransform: 'uppercase'
      }
   },
   tooltip: {
      borderWidth: 0,
      backgroundColor: 'rgba(219,219,216,0.8)',
      shadow: false
   },
   legend: {
      itemStyle: {
         fontWeight: 'bold',
         fontSize: '13px'
      }
   },
   xAxis: {
      gridLineWidth: 1,
	  
      labels: {
         style: {
            fontSize: '12px'
         }
      }
   },
   yAxis: {
      minorTickInterval: 'auto',
	  
      title: {
         style: {
            textTransform: 'uppercase'
         }
      },
      labels: {
         style: {
            fontSize: '12px'
         }
      }
   },
   plotOptions: {
      candlestick: {
         lineColor: '#404048'
      }
   },
   background2: '#FF0000'
   
};

// Apply the theme
Highcharts.setOptions(Highcharts.theme);

var chtype='<? echo $m; ?>';
var ccount='<? echo $com; ?>';
	
	window.onload = function()
	{
		
		if(lnk=='monthly_order_qty_vs_sewing_balance_qty')
			hs_homegraph(2);
		else if(lnk=='order_in_hand_val')
			hs_homegraph(1);
	}
	
	function hs_homegraph( gtype ) 
	{
		//gtype: 1=Value column chart,  2=Qnty  column chart,  3=Stack value column chart, 4=stack qnty column chart
		var data_qnty=<? echo $data_qnt; ?>;
		var data=<? echo $data; ?>;
		if(gtype==1)
		{
			var ddd=data;
			var msg="Total Values"
			var uom=" USD";
			
			$('#tableQty').hide();
			$('#tableVal').show();
		}
		else
		{
			var ddd=data_qnty; 
			var msg="Total Pcs"
			var uom=" PCS";
			
			$('#tableQty').show();
			$('#tableVal').hide();
		}
		$('#chartdiv').highcharts({

			chart: {
				type: 'column'
			},
	
			title: {
				text: ' <? echo $capacity; ?> '
			},
	
			xAxis: {
				categories: <? echo $catg; ?>
			},
	
			yAxis: {
				allowDecimals: false,
				min: 0,
				title: {
					text: msg
				}
			},
	
			tooltip: {
				formatter: function () {
					return '<b>' + this.x + '</b> ' +
						 ': ' + this.y + uom +'<br/>' ;
						//+ 'Total: ' + this.point.stackTotal;  this.series.name + ': ' + this.y + uom +'<br/>' ;
				}
			},
	
			plotOptions: {
				column: {
					stacking: false //'normal'
				}
			},
		
			series: ddd
		});
		
		
	}
	

	</script>