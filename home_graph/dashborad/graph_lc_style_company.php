<? 
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
echo load_html_head_contents("Graph", "../../", "", 1, $unicode, $multi_select, '');
$type=1; //1= publish ship date;
?>
<link rel="stylesheet" href="../../home_css/styles.css">


<?

	
	$no_of_company='';
	if($_SESSION['logic_erp']["data"]=="")
	{ 
		if($db_type==0) 
		{
			$manufacturing_company=return_field_value("group_concat(comp.id)","lib_company as comp","comp.core_business=1 and comp.status_active=1 and comp.is_deleted=0 $company_cond");
		}
		else
		{
			$manufacturing_company= return_field_value("LISTAGG(comp.id, ', ') WITHIN GROUP (ORDER BY comp.id) company","lib_company comp", "comp.core_business=1 and comp.status_active=1 and comp.is_deleted=0 $company_cond","company");
		}
		
		$no_of_company=count(explode(",",$manufacturing_company));
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
		$exfactory_data=sql_select("select po_break_down_id,country_id,
		sum(CASE WHEN entry_form!=85 THEN ex_factory_qnty ELSE 0 END) as ex_factory_qnty,
		sum(CASE WHEN entry_form=85 THEN ex_factory_qnty ELSE 0 END) as return_qnty,
		MAX(ex_factory_date) as ex_factory_date from pro_ex_factory_mst  where 1=1  and status_active=1 and is_deleted=0 group by po_break_down_id,country_id");
		foreach($exfactory_data as $row)
		{
			$exFactory_arr[$row[csf('po_break_down_id')]][$row[csf('country_id')]]=$row[csf('ex_factory_qnty')]-$row[csf('return_qnty')];
		}
		
		
		
		
		$i=1; $html='<tbody>'; $html2='<tbody>';
		
//*************************************************************************************		
		$where_con=" and (";
		$con=0;
		foreach($yr_mon_part as $key=>$val)
		{
			if($db_type==0) 
			{	if($type==1){
					if($con==0){$where_con.=" b.pub_shipment_date like '".$val."-%"."' ";}
					else{$where_con.=" or b.pub_shipment_date like '".$val."-%"."'";}
				}
				else
				{
					if($con==0){$where_con.=" a.country_ship_date like '".$val."-%"."' ";}
					else{$where_con.=" or a.country_ship_date like '".$val."-%"."'";}
				}
				$con=1;
			}
			else
			{
				if($type==1){
					if($con==0){$where_con.=" to_char(b.pub_shipment_date,'YYYY-MM-DD') like '".$val."-%"."'";}
					else{$where_con.=" or to_char(b.pub_shipment_date,'YYYY-MM-DD') like '".$val."-%"."'";}
				}
				else
				{
					if($con==0){$where_con.=" to_char(a.country_ship_date,'YYYY-MM-DD') like '".$val."-%"."'";}
					else{$where_con.=" or to_char(a.country_ship_date,'YYYY-MM-DD') like '".$val."-%"."'";}
				}
				
				$con=1;
			}
			
		}
		$where_con.=" ) ";

		if($db_type==0) 
		{	
			if($type==1){
				$group_con =" group by b.id, c.company_name,a.country_id,b.pub_shipment_date";
			}
			else
			{
				$group_con =" group by b.id, c.company_name,a.country_id,a.country_ship_date";
			}
		}
		else
		{
		
			if($type==1){
				$group_con ="  group by c.company_name,b.id, c.total_set_qnty,b.unit_price,b.pub_shipment_date";
			}
			else
			{
				$group_con ="  group by c.company_name,b.id, a.country_id,c.total_set_qnty,b.unit_price,a.country_ship_date";
			}
		
		}


		if($db_type==0) 
		{
			if($type==1){
				$sql="select 
				c.company_name,
				DATE_FORMAT(b.pub_shipment_date,'%Y-%m') as yr_mon,
				b.id as po_id, c.total_set_qnty as ratio, b.unit_price, a.country_id, 
				sum(CASE WHEN b.is_confirmed=1 THEN b.po_total_price ELSE 0 END) AS 'confpoval', 
				sum(CASE WHEN b.is_confirmed=2 THEN b.po_total_price ELSE 0 END) AS 'projpoval' ,
				sum(CASE WHEN b.is_confirmed=1 THEN b.po_total_price ELSE 0 END) AS 'confpoqty', 
				sum(CASE WHEN b.is_confirmed=2 THEN b.po_total_price ELSE 0 END) AS 'projpoqty' 
				
				from wo_po_break_down as b, wo_po_details_master as c where b.job_no_mst=c.job_no and c.company_name in($manufacturing_company) and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 $where_con $group_con";
			}
			else
			{
				$sql="select 
				c.company_name,
				DATE_FORMAT(a.country_ship_date,'%Y-%m') as yr_mon,
				b.id as po_id, c.total_set_qnty as ratio, b.unit_price, a.country_id, 
				sum(CASE WHEN b.is_confirmed=1 THEN a.order_total ELSE 0 END) AS 'confpoval', 
				sum(CASE WHEN b.is_confirmed=2 THEN a.order_total ELSE 0 END) AS 'projpoval' ,
				sum(CASE WHEN b.is_confirmed=1 THEN a.order_quantity ELSE 0 END) AS 'confpoqty', 
				sum(CASE WHEN b.is_confirmed=2 THEN a.order_quantity ELSE 0 END) AS 'projpoqty' 
				
				from wo_po_color_size_breakdown as a, wo_po_break_down as b, wo_po_details_master as c where a.po_break_down_id=b.id and b.job_no_mst=c.job_no and c.company_name in($manufacturing_company) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 $where_con $group_con";
			}
		}
		else
		{
			if($type==1){
				$sql="select c.company_name,to_char(b.pub_shipment_date,'YYYY-MM') as yr_mon,b.id as po_id, c.total_set_qnty as ratio, b.unit_price, 
				sum(CASE WHEN b.is_confirmed=1 THEN b.po_total_price ELSE 0 END) AS confpoval, 
				sum(CASE WHEN b.is_confirmed=2 THEN b.po_total_price ELSE 0 END) AS projpoval ,
				sum(CASE WHEN b.is_confirmed=1 THEN b.po_total_price ELSE 0 END) AS confpoqty, 
				sum(CASE WHEN b.is_confirmed=2 THEN b.po_total_price ELSE 0 END) AS projpoqty 
				
				from wo_po_break_down b, wo_po_details_master c where b.job_no_mst=c.job_no and c.company_name in($manufacturing_company) and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 $where_con $group_con";
			}
			else
			{
				
				$sql="select c.company_name,to_char(a.country_ship_date,'YYYY-MM') as yr_mon,b.id as po_id, c.total_set_qnty as ratio, b.unit_price, a.country_id, 
				sum(CASE WHEN b.is_confirmed=1 THEN a.order_total ELSE 0 END) AS confpoval, 
				sum(CASE WHEN b.is_confirmed=2 THEN a.order_total ELSE 0 END) AS projpoval ,
				sum(CASE WHEN b.is_confirmed=1 THEN a.order_quantity ELSE 0 END) AS confpoqty, 
				sum(CASE WHEN b.is_confirmed=2 THEN a.order_quantity ELSE 0 END) AS projpoqty 
				
				from wo_po_color_size_breakdown a, wo_po_break_down b, wo_po_details_master c where a.po_break_down_id=b.id and b.job_no_mst=c.job_no and c.company_name in($manufacturing_company) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 $where_con $group_con";
			}
		}
		

      
	$result=sql_select($sql);
	foreach($result as $rows){
		$dataArr[$rows[csf('yr_mon')]][]=$rows;
	}
	
	
	
	unset($result);

		
	
	
	
//*********************************************************************		
		
		foreach($yr_mon_part as $key=>$val)
		{
			
			
			$confPoQty=0; $projPoQty=0; $confPoVal=0; $projPoVal=0; $exFactoryQty=0; $exFactoryVal=0;
			foreach($dataArr[$val] as $row)
			{ 
				$confPoQty+=$row[csf('confpoqty')]; 
				$projPoQty+=$row[csf('projpoqty')];
				
				$confPoVal+=$row[csf('confpoval')]; 
				$projPoVal+=$row[csf('projpoval')];
				
				$exFactoryQty+=$exFactory_arr[$row[csf('po_id')]][$row[csf('country_id')]];
				$exFactoryVal+=($exFactory_arr[$row[csf('po_id')]][$row[csf('country_id')]]/$row[csf('ratio')])*$row[csf('unit_price')];
				
			
			
			$orderDataArr[$row[csf('company_name')]][$val][csf('povalue')]+=($row[csf('confpoval')]+$row[csf('projpoval')]);
			$orderDataArr[$row[csf('company_name')]][$val][csf('poqty')]+=($row[csf('confpoqty')]+$row[csf('projpoqty')]);
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
		// var_dump($orderDataArr);
		
		
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
			if($i!=11) $catg .="'".date("M",strtotime($yr_mon_part[$i].'-01'))."',"; else $catg .="'".date("M",strtotime($yr_mon_part[$i].'-01'))."']";
		}
		
		
		$capacity_in_value_arr=return_library_array("select company_name,capacity_in_value from variable_settings_commercial where variable_list=5 ", "company_name","capacity_in_value");
	
		
		
		$sql_comp=sql_select("select comp.id as id, comp.company_name,company_short_name from lib_company comp where comp.core_business=1 and comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.id asc");
		$k=1;
		$data .="["; 
		$data_qnt .="[";
		$com=0;
		foreach($sql_comp as $row_comp)
		{
			$val=$capacity_in_value_arr[$row_comp[csf('id')]];
			if ($capacity!="")$capacity=$capacity.", ".$row_comp[csf('company_short_name')].": $ ".number_format($val,2,'.',',');
			else $capacity="Capacity: ".$row_comp[csf('company_short_name')].": $ ".number_format($val,2,'.',',');
			$cname=$row_comp[csf('company_short_name')];
			$data .="{ name: '".$row_comp[csf('company_short_name')]."', data:[";
			$data_qnt .="{ name: '".$row_comp[csf('company_short_name')]."', data:[";
			for($i=0;$i<=11;$i++)
			{
				$value=0;
				if($db_type==0) $year_field="b.pub_shipment_date"; else $year_field="to_char(b.pub_shipment_date,'YYYY-MM-DD')";
				
				
				$value=$orderDataArr[$row_comp[csf('id')]][$yr_mon_part[$i]][csf('povalue')];
				$qty=$orderDataArr[$row_comp[csf('id')]][$yr_mon_part[$i]][csf('poqty')];
				
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
		
		
		$data_qnt_stck .="[{ name: 'Confirmed', data:[";
		$data_val_stck .="[{ name: 'Confirmed', data:[";
		foreach($tot_for_graph as $key=>$value )
		{
			if( $i!=11)  $data_qnt_stck .=number_format( $conf_tot_for_graph_stack[$key],0,'.','').","; else $data_qnt_stck .=number_format( $conf_tot_for_graph_stack[$key],0,'.','')."";
			if( $i!=11)  $data_val_stck .=number_format( $conf_tot_for_graph_stack_val[$key],0,'.','').","; else $data_val_stck .=number_format( $conf_tot_for_graph_stack_val[$key],0,'.','')."";
		}
		 $data_qnt_stck .="], stack: 'conf'}, ";
		 $data_val_stck .="], stack: 'conf', color: 'green'}, ";
		 
		 
		 
		$data_qnt_stck .="{ name: 'Projected', data:[";
		$data_val_stck .="{ name: 'Projected', data:[";
		foreach($tot_for_graph as $key=>$value )
		{
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
		$_SESSION['logic_erp']["no_of_company"]=$no_of_company;
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
		$no_of_company=$_SESSION['logic_erp']["no_of_company"];
	}
	

?>

<table width="100%" cellpadding="0" cellspacing="0" style="margin-top:2px;">


<tr height="445">
    <td width="80%" align="center" valign="top">
    <?
    if($no_of_company>1)
    {
    ?>
    <table width="910" cellpadding="0" cellspacing="0" align="center">
       
        <tr>
            <td align="center" height="445" width="910">
                <div id="chartdiv" style="width:400; height:445px; background-color:#FFFFFF"></div>
            </td>
        </tr>
       
    </table>
    <?
    }
    else
    {
    ?>
    <table width="1000" cellpadding="0" cellspacing="0">
        <tr>
            <td height="30" valign="middle" align="center" colspan="2">
                <font size="2" color="#4D4D4D"> <strong><span id="caption_text"></span> <? // echo "$start_yr"."-"."$end_yr"; ?></strong></font>
            </td>
            <td colspan="2" rowspan="2" valign="top" align="center"> 
                <div style="margin-left:5px; margin-top:45px">
                    <table class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" width="300" id="tableQty">
                        <thead>
                            <th width="50">Month</th>
                            <th>Proj.</th>
                            <th>Conf.</th>
                            <th>Total</th>
                            <th>Ship Out</th>
                            <th>%</th>
                        </thead>
                        <? echo $html; ?>
                    </table>
                    <table class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" width="300" id="tableVal">
                        <thead>
                            <th width="50">Month</th>
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
            <td width="8" bgcolor=" "></td>
            <td align="center" height="445" width="700">
                <div id="chartdiv" style="width:400px; height:445px; background-color:#FFFFFF"></div>
            </td>
        </tr>
    </table>
    <?
    }
    ?> 
    </td>
</tr>


</table>
</div>

<script src="ext_resource/hschart/hschart.js"></script>

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


   // General
   background2: '#FF0000'
   
};

// Apply the theme
Highcharts.setOptions(Highcharts.theme);

var ccount='<? echo $com; ?>';
	
	window.onload = function()
	{
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
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
<script>

$(document).ready(function(){
    $('#my_div div').each(function(graph_grp) {
	  	$(this).attr('onMouseOver',"hover_effect(this)");
		$(this).attr('onMouseOut',"mouseout_effect(this)");
	});
});
 
function hover_effect( divclass )
{
	var cls= $(divclass).attr('class').split(" ");
   	$("."+cls[0]+" img").css( "-webkit-transform"," scale(1.3)" );
   	$("."+cls[0]+" img").css('transform', 'scale(1.3)'); 
}

function mouseout_effect( divclass )
{
	var cls= $(divclass).attr('class').split(" ");
   	$("."+cls[0]+" img").removeAttr( 'style' );
}


</script>

<?php
function add_month($orgDate,$mon)
{
	$cd = strtotime($orgDate);
	$retDAY = date('Y-m-d', mktime(0,0,0,date('m',$cd)+$mon,1,date('Y',$cd)));
	return $retDAY;
}


?>