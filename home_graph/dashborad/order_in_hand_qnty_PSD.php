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
<style>.stack_company{visibility:visible;}</style>
 
 
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
		
		
		
		
		//$prod_sql= "SELECT a.po_break_down_id, a.item_number_id, a.production_type,b.production_qnty as  production_quantity, a.embel_name, a.re_production_qty, b.reject_qty as reject_qnty from pro_garments_production_mst a,pro_garments_production_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and a.production_type=b.production_type and b.is_deleted=0  $poIds_cond $location $floor";
		
		
		
		
		foreach($yr_mon_part as $key=>$val)
		{
			if($db_type==0) 
			{
			
				$sql="select b.id as po_id, c.total_set_qnty as ratio, b.unit_price, 
				sum(CASE WHEN b.is_confirmed=1 THEN b.po_total_price ELSE 0 END) AS confpoval, 
				sum(CASE WHEN b.is_confirmed=2 THEN b.po_total_price ELSE 0 END) AS projpoval ,
				sum(CASE WHEN b.is_confirmed=1 THEN (b.po_quantity*c.total_set_qnty) ELSE 0 END) AS confpoqty, 
				sum(CASE WHEN b.is_confirmed=2 THEN (b.po_quantity*c.total_set_qnty) ELSE 0 END) AS projpoqty 
				from wo_po_break_down as b, wo_po_details_master as c where b.job_no_mst=c.job_no and c.company_name in($manufacturing_company) and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and b.pub_shipment_date like '".$val."-%"."' group by b.id";

			}
			else
			{
				$sql="select b.id as po_id, c.total_set_qnty as ratio, b.unit_price,
				sum(CASE WHEN b.is_confirmed=1 THEN b.po_total_price ELSE 0 END) AS confpoval, 
				sum(CASE WHEN b.is_confirmed=2 THEN b.po_total_price ELSE 0 END) AS projpoval ,
				sum(CASE WHEN b.is_confirmed=1 THEN (b.po_quantity*c.total_set_qnty) ELSE 0 END) AS confpoqty, 
				sum(CASE WHEN b.is_confirmed=2 THEN (b.po_quantity*c.total_set_qnty) ELSE 0 END) AS projpoqty 
				from wo_po_break_down b, wo_po_details_master c where b.job_no_mst=c.job_no and c.company_name in($manufacturing_company) and  b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and to_char(b.pub_shipment_date,'YYYY-MM-DD') like '".$val."-%"."' group by b.id, c.total_set_qnty,b.unit_price";	 
			}
			$result=sql_select($sql);
			
			
			$confPoQty=0; $projPoQty=0; $confPoVal=0; $projPoVal=0; $exFactoryQty=0; $exFactoryVal=0;
			foreach($result as $row)
			{ 
				$confPoQty+=$row[csf('confpoqty')]; 
				$projPoQty+=$row[csf('projpoqty')];
				
				$confPoVal+=$row[csf('confpoval')]; 
				$projPoVal+=$row[csf('projpoval')];
				
				$unit_price=$row[csf('unit_price')]/$row[csf('ratio')];
				$exFactoryQty+=$exFactory_arr[$row[csf('po_id')]];
				$exFactoryVal+=$exFactory_arr[$row[csf('po_id')]]*$unit_price;
			}
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
			$html.='<td>'.date("M",strtotime($val))."'".date("y",strtotime($val)).'</td>
					<td align="right">'.number_format($projPoQty,0).'</td>
					<td align="right">'.number_format($confPoQty,0).'</td>
					<td align="right">'.number_format($totQty,0).'</td>
					<td align="right">'.number_format($exFactoryQty,0).'</td>
					<td align="right">'.number_format($perc,2).'</td>';
			$html.='</tr>';
			
			$html2.='<tr bgcolor="'.$bgcolor.'" onClick="change_color('."'tr_".$i."','".$bgcolor."'".')" id="tr_'.$i.'">';
			$html2.='<td>'.date("M",strtotime($val))."'".date("y",strtotime($val)).'</td>
					<td align="right">'.number_format($projPoVal,0).'</td>
					<td align="right">'.number_format($confPoVal,0).'</td>
					<td align="right">'.number_format($totVal,0).'</td>
					<td align="right">'.number_format($exFactoryVal,0).'</td>
					<td align="right">'.number_format($perc_val,2).'</td>';
			$html2.='</tr>';
			
			$totProjQty+=$projPoQty;
			$totConfQty+=$confPoQty;  
			$totExFactoryQty+=$exFactoryQty; 
			$grandTotQty+=$totQty;
			
			$totProjVal+=$projPoVal;
			$totConfVal+=$confPoVal;  
			$totExFactoryVal+=$exFactoryVal; 
			$grandTotVal+=$totVal;

			$i++;
		}
		
		$totPerc=($totExFactoryQty/$grandTotQty)*100;
		$html.='</tr></tbody><tfoot><th>Total</th>'; 
        $html.='<th align="right">'.number_format($totProjQty,0).'</th>
				<th align="right">'.number_format($totConfQty,0).'</th>
				<th align="right">'.number_format($grandTotQty,0).'</th>
                <th align="right">'.number_format($totExFactoryQty,0).'</th>
				<th align="right">'.number_format($totPerc,2).'</th></tfoot>'; 
		
		$totPercVal=($totExFactoryVal/$grandTotVal)*100;		
        $html2.='</tr></tbody><tfoot><th>Total</th>'; 
        $html2.='<th align="right">'.number_format($totProjVal,0).'</th>
				<th align="right">'.number_format($totConfVal,0).'</th>
				<th align="right">'.number_format($grandTotVal,0).'</th>
				<th align="right">'.number_format($totExFactoryVal,0).'</th>
				<th align="right">'.number_format($totPercVal,2).'</th></tfoot>'; 

		$catg="[";
		for($i=0;$i<=11;$i++)
		{
			if($i!=11) $catg .="'".date("M",strtotime($yr_mon_part[$i]))."',"; else $catg .="'".date("M",strtotime($yr_mon_part[$i]))."']";
		}
		
		$sql_comp=sql_select("select comp.id as id, comp.company_name,company_short_name from lib_company comp where comp.core_business=1 and comp.status_active=1 and comp.is_deleted=0 $str_comp $company_cond order by comp.id asc");
		$k=1;
		$data .="["; 
		$data_qnt .="[";
		$com=0;
		foreach($sql_comp as $row_comp)
		{
			$val=return_field_value("capacity_in_value", "variable_settings_commercial", "company_name like '".$row_comp[csf('id')]."' and variable_list=5");
			if ($capacity!="")$capacity=$capacity.", ".$row_comp[csf('company_short_name')].": $ ".number_format($val,2,'.',',');
			else $capacity="Capacity: ".$row_comp[csf('company_short_name')].": $ ".number_format($val,2,'.',',');
			$cname=$row_comp[csf('company_short_name')];
			$data .="{ name: '".$row_comp[csf('company_short_name')]."', data:[";
			$data_qnt .="{ name: '".$row_comp[csf('company_short_name')]."', data:[";
			
			
			for($i=0;$i<=11;$i++)
			{
				$value=0;
				if($db_type==0) $year_field="b.pub_shipment_date"; else $year_field="to_char(b.pub_shipment_date,'YYYY-MM-DD')";
				/*$sql="select sum(a.order_total) AS povalue, sum(a.order_quantity) AS poqty from wo_po_color_size_breakdown a, wo_po_break_down b, wo_po_details_master c where a.po_break_down_id=b.id and b.job_no_mst=c.job_no and c.company_name='".$row_comp[csf('id')]."' and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and $year_field like '".$yr_mon_part[$i]."-%"."'";*/
				$sql="select sum(b.po_total_price) AS povalue, sum(b.po_quantity*c.total_set_qnty) AS poqty from wo_po_break_down b, wo_po_details_master c where b.job_no_mst=c.job_no and c.company_name='".$row_comp[csf('id')]."' and  b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and $year_field like '".$yr_mon_part[$i]."-%"."'";
				
				
				
				
				$row=sql_select($sql);
				$value=$row[0][csf('povalue')];
				$qty=$row[0][csf('poqty')];
				
				if( $i!=11) 
				{
					$data .=number_format( $value,0,'.','').",";
					$data_qnt .=number_format( $qty,0,'.','').",";
				}
				else 
				{
					$data .=number_format( $value,0,'.','').""; 
					$data_qnt .=number_format( $qty,0,'.','').""; 
				}
			}
			if(count($sql_comp)!=$k) 
			{
				 $data .="], stack: 'none'}, ";
				 $data_qnt .="], stack: 'none'}, ";
			}
			else 
			{
				$data .="], stack: 'none'}] ";
				$data_qnt .="], stack: 'none'}] ";
			}
			$k++;
			$com++;
		}
		
		//$data_qnt .="[";
		//$data_qnt .="{ name: '".$cname."', data:[";
		$data_qnt_stck .="[{ name: 'Confirmed', data:[";
		
		$data_val_stck .="[{ name: 'Confirmed', data:[";
		
		foreach($tot_for_graph as $key=>$value )
		{
			//if( $i!=11)  $data_qnt .=number_format( $value,0,'.','').","; else $data_qnt .=number_format( $value,0,'.','')."";
			if( $i!=11)  $data_qnt_stck .=number_format( $conf_tot_for_graph_stack[$key],0,'.','').","; else $data_qnt_stck .=number_format( $conf_tot_for_graph_stack[$key],0,'.','')."";
			if( $i!=11)  $data_val_stck .=number_format( $conf_tot_for_graph_stack_val[$key],0,'.','').","; else $data_val_stck .=number_format( $conf_tot_for_graph_stack_val[$key],0,'.','')."";
		}
		 
		// $data_qnt .="], stack: 'none'}] ";
		 $data_qnt_stck .="], stack: 'conf'}, ";
		 $data_val_stck .="], stack: 'conf', color: 'green'}, ";
		 
		$data_qnt_stck .="{ name: 'Projected', data:[";
		$data_val_stck .="{ name: 'Projected', data:[";
		foreach($tot_for_graph as $key=>$value )
		{
			//if( $i!=11)  $data_qnt .=number_format( $value,0,'.','').","; else $data_qnt .=number_format( $value,0,'.','')."";
			if( $i!=11)  $data_qnt_stck .=number_format( $proj_tot_for_graph_stack[$key],0,'.','').","; else $data_qnt_stck .=number_format( $proj_tot_for_graph_stack[$key],0,'.','')."";
			if( $i!=11)  $data_val_stck .=number_format( $proj_tot_for_graph_stack_val[$key],0,'.','').","; else $data_val_stck .=number_format( $proj_tot_for_graph_stack_val[$key],0,'.','')."";
		}
		$data_qnt_stck .="], stack: 'conf'}] ";
		$data_val_stck .="], stack: 'conf', color: 'red'}] ";
		
		$_SESSION['logic_erp']["data"]=$data;
		$_SESSION['logic_erp']["data_qnt"]=$data_qnt;
		$_SESSION['logic_erp']["capacity"]=$capacity;
		$_SESSION['logic_erp']["catg"]=$catg;
		$_SESSION['logic_erp']["data_qnt_stck"]=$data_qnt_stck;
		$_SESSION['logic_erp']["data_val_stck"]=$data_val_stck;
		
		$_SESSION['logic_erp']["data_summ_qty"]=$html;
		$_SESSION['logic_erp']["data_summ_val"]=$html2;
	}
	else
	{
		$data=$_SESSION['logic_erp']["data"];
		$data_qnt=$_SESSION['logic_erp']["data_qnt"];
		$capacity=$_SESSION['logic_erp']["capacity"];
		$catg=$_SESSION['logic_erp']["catg"];
		$data_qnt_stck=$_SESSION['logic_erp']["data_qnt_stck"];
		$data_val_stck=$_SESSION['logic_erp']["data_val_stck"];
		
		$html=$_SESSION['logic_erp']["data_summ_qty"];
		$html2=$_SESSION['logic_erp']["data_summ_val"];
	}
	//echo $data_qnt_stck;
 
?>

<?
if($no_of_company>1)
{
?>
    <table width="1110" cellpadding="0" cellspacing="0">
        <tr>
            <td height="20" align="center" colspan="2">
                <font size="2" color="#4D4D4D"><strong><span id="caption_text"></span></strong></font>
            </td>
           
        </tr>
        
            <tr>
                <td width="8">
                <td align="center" height="400" width="1100">
                    <div id="chartdiv" style="width:1100px; height:400px; background-color:#FFFFFF"></div>
                </td>
            </tr>
        
        <tr>
            <td colspan="2"></td>
        </tr>
        <tr>
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
        </tr>
    </table>
<?
}
else
{
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
                            <th>Total</th>
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
        <tr>
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
        </tr>
    </table>
<?
}
?>   
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
		
		if(lnk=='order_in_hand_qnty_PSD')
			hs_homegraph(2);
		else if(lnk=='order_in_hand_val')
			hs_homegraph(1);
		else if(lnk=='stack_qnty')
			hs_homegraph_stack(2)
		else if(lnk=='stack_value')
			hs_homegraph_stack(1)
		else
			hs_homegraph_stack(1);
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
	
	function hs_homegraph_stack( gtype )
	{
		//gtype: 1=Value column chart,  2=Qnty  column chart,  3=Stack value column chart, 4=stack qnty column chart
		 
		 if(gtype==1)
		 {
			 var datas=<? echo $data_val_stck; ?>;
			 var msg="Total Values";
			 var cur="USD";
			 
			 $('#tableQty').hide();
			 $('#tableVal').show();
		 }
		 else
		 {
			 var datas=<? echo $data_qnt_stck; ?>;
			 var msg="Total Qnty";
			 var cur="PCS";
			 
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
					return '<b>' + this.x + '</b><br/>' +
						this.series.name + ': '+cur+" " + this.y + '<br/>' 
						+ 'Total: '+cur+" " + this.point.stackTotal;
				}
			},
	
			plotOptions: {
				column: {
					stacking: 'normal'
				}
			},
		
			series: datas
		});
		
		
	}
	</script>