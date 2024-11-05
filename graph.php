 <? 
	session_start(); 
 	echo load_html_head_contents("Graph", "", "", $popup, $unicode, $multi_select, 1);
 //include('includes/common.php');
 	function add_month($orgDate,$mon){
		$cd = strtotime($orgDate);
		$retDAY = date('Y-m-d', mktime(0,0,0,date('m',$cd)+$mon,1,date('Y',$cd)));
 		return $retDAY;
	}

?>
<style>
.stack_company
{
	visibility:visible;
}

</style>

<script>
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
	
	//show_graph( "settings_value", "data_value", "column", "chartdiv", "", "", 1, 400, 750 )
	
</script>
<?
	
	//echo $no_of_company.Fuad;
	//$manufacturing_company=3;
	//$no_of_company=1;
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
		//$no_of_company=1;
		$date=date("Y",time());
		$month_prev=add_month(date("Y-m-d",time()),-3);
		//echo $month_prev;
		$month_next=add_month(date("Y-m-d",time()),8);
		//echo $month_next;
		
		$start_yr=date("Y",strtotime($month_prev));
		$end_yr=date("Y",strtotime($month_next));
		for($e=0;$e<=11;$e++)
		{
			$tmp=add_month(date("Y-m-d",strtotime($month_prev)),$e);
			$yr_mon_part[$e]=date("Y-m",strtotime($tmp));
			 //echo "<br>$yr_mon_part[$i]";
		}
		
		$exFactory_arr=array();
		$data_arr=sql_select( "select po_break_down_id, country_id, sum(ex_factory_qnty) as ex_factory_qnty from pro_ex_factory_mst where status_active=1 and is_deleted=0 group by po_break_down_id, country_id");
		foreach($data_arr as $row)
		{
			$exFactory_arr[$row[csf('po_break_down_id')]][$row[csf('country_id')]]=$row[csf('ex_factory_qnty')];
		}
		
		$i=1; $html='<tbody>'; $html2='<tbody>';
		foreach($yr_mon_part as $key=>$val)
		{
			if($db_type==0) 
			{
				$sql="select b.id as po_id, c.total_set_qnty as ratio, b.unit_price, a.country_id, sum(CASE WHEN b.is_confirmed=1 THEN a.order_total ELSE 0 END) AS 'confpoval', sum(CASE WHEN b.is_confirmed=2 THEN a.order_total ELSE 0 END) AS 'projpoval' ,sum(CASE WHEN b.is_confirmed=1 THEN a.order_quantity ELSE 0 END) AS 'confpoqty', sum(CASE WHEN b.is_confirmed=2 THEN a.order_quantity ELSE 0 END) AS 'projpoqty' from wo_po_color_size_breakdown as a, wo_po_break_down as b, wo_po_details_master as c where a.po_break_down_id=b.id and b.job_no_mst=c.job_no and c.company_name in($manufacturing_company) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and a.country_ship_date like '".$val."-%"."' group by b.id, a.country_id";
			}
			else
			{
				$sql="select b.id as po_id, c.total_set_qnty as ratio, b.unit_price, a.country_id, sum(CASE WHEN b.is_confirmed=1 THEN a.order_total ELSE 0 END) AS confpoval, sum(CASE WHEN b.is_confirmed=2 THEN a.order_total ELSE 0 END) AS projpoval ,sum(CASE WHEN b.is_confirmed=1 THEN a.order_quantity ELSE 0 END) AS confpoqty, sum(CASE WHEN b.is_confirmed=2 THEN a.order_quantity ELSE 0 END) AS projpoqty from wo_po_color_size_breakdown a, wo_po_break_down b, wo_po_details_master c where a.po_break_down_id=b.id and b.job_no_mst=c.job_no and c.company_name in($manufacturing_company) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and to_char(a.country_ship_date,'YYYY-MM-DD') like '".$val."-%"."' group by b.id, a.country_id,c.total_set_qnty,b.unit_price";	 
			}
			//echo $sql; //die;
			$result=sql_select($sql);
			$confPoQty=0; $projPoQty=0; $confPoVal=0; $projPoVal=0; $exFactoryQty=0; $exFactoryVal=0;
			foreach($result as $row)
			{ 
				$confPoQty+=$row[csf('confpoqty')]; 
				$projPoQty+=$row[csf('projpoqty')];
				
				$confPoVal+=$row[csf('confpoval')]; 
				$projPoVal+=$row[csf('projpoval')];
				
				$unit_price=$row[csf('unit_price')]/$row[csf('ratio')];
				$exFactoryQty+=$exFactory_arr[$row[csf('po_id')]][$row[csf('country_id')]];
				$exFactoryVal+=$exFactory_arr[$row[csf('po_id')]][$row[csf('country_id')]]*$unit_price;
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
				<th align="right">'.number_format($totConfQty,0).'</th>
				<th align="right">'.number_format($grandTotVal,0).'</th>
				<th align="right">'.number_format($totExFactoryVal,0).'</th>
				<th align="right">'.number_format($totPercVal,2).'</th></tfoot>'; 

		$catg="[";
		for($i=0;$i<=11;$i++)
		{
			if($i!=11) $catg .="'".date("M",strtotime($yr_mon_part[$i]))."',"; else $catg .="'".date("M",strtotime($yr_mon_part[$i]))."']";
		}
		
		$sql_comp=sql_select("select comp.id as id, comp.company_name,company_short_name from lib_company comp where comp.core_business=1 and comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.id asc");
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
				if($db_type==0) $year_field="a.country_ship_date"; else $year_field="to_char(a.country_ship_date,'YYYY-MM-DD')";
				
				$sql="select sum(a.order_total) AS povalue, sum(a.order_quantity) AS poqty from wo_po_color_size_breakdown a, wo_po_break_down b, wo_po_details_master c where a.po_break_down_id=b.id and b.job_no_mst=c.job_no and c.company_name='".$row_comp[csf('id')]."' and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and $year_field like '".$yr_mon_part[$i]."-%"."'";
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
<div style="margin-top:5px"><a href="##" onClick='hs_homegraph(1)'>Value Wise</a>&nbsp;&nbsp;<a href="##" onClick='hs_homegraph(2)'>Quantity Wise</a>&nbsp;&nbsp;<a href="##" onClick='hs_homegraph_stack(1)'>Stack Value Chart</a>&nbsp;&nbsp;<a href="##" onClick='hs_homegraph_stack(2)'>Stack Quantity Chart</a>&nbsp;&nbsp;<a href="index.php?g=1">Dash Board</a>&nbsp;&nbsp;<a href="index.php?g=2">Today Hourly Prod.</a>&nbsp;&nbsp;<a href="index.php?g=3">Trend Monthly</a>&nbsp;&nbsp;<a href="index.php?g=4">Trend Daily</a>&nbsp;&nbsp;<a href="##"><span onclick="show_summary();">Order Summary (Qty)</span></a>&nbsp;&nbsp;<a href="##"><span onclick="show_summary_val();">Order Summary (Value)</span></a></div>
<?
if($no_of_company>1)
{
?>
    <table width="1110" cellpadding="0" cellspacing="0">
        <tr>
            <td height="20" align="center" colspan="2">
                <font size="2" color="#4D4D4D"><strong><span id="caption_text"></span></strong></font>
            </td>
            <!-- <td colspan="2" rowspan="2" valign="top" align="center"> 
            <br />
                <a href="##" onClick='show_graph( "settings_value", "data_value", "column", "chartdiv", "", "", 1, 400, 750 )'>Value Wise</a>&nbsp;&nbsp;<a href="##" onClick='show_graph( "settings_qnty", "data_qnty", "column", "chartdiv", "", "", 1, 400, 750 )'>Quantity Wise</a>&nbsp;&nbsp;<a href="index.php?g=1">Dash Board</a>
                
                 &nbsp;&nbsp;<a href="##" onClick="set_stack_graph(0)">Stack Chart</a><br>
                 <a href="##" onClick="set_intimates_qnty_graph()">Product Category WIse Quantity</a><br><a href="##" onClick="set_intimates_value_graph()">Product Category WIse Value</a><br><br><br> 
                 <div class="stack_company" id="stack_company" style="display:none">
                 <?//echo create_drop_down( "cbo_company_name", 172, "select company_name, id from lib_company where core_business=1 and status_active=1 and is_deleted=0 order by company_name asc","id,company_name", 1, "All Company", $selected, "set_stack_graph( this.value )" );
                ?>
                </div>
             </td>--> 
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
                        <td align="right" valign="top" width="310"> <img src="images/logic/logic_bottom_logo.png" height="65" width="300" /> 
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
            <td width="8" bgcolor=""></td> <!--#00CCFF-->
            <td></td>
        </tr>
        <tr>
            <td colspan="2">
                <table width="100%">
                    <tr>
                        <td width="150"></td>
                        <td  align="right" valign="top">Copyright</td>
                        <td align="right" valign="top" width="310"> <img src="images/logic/logic_bottom_logo.png" height="65" width="300" /> 
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
	/*  
	function set_stack_graph( str )
	{
		 document.getElementById('stack_company').style.visibility="visible";
		 document.getElementById('cbo_company_name').value=str;
		 
		var sel = document.getElementById('cbo_company_name');
		var myTest  =sel.options[sel.selectedIndex].text;
		document.getElementById('caption_text').innerHTML="Monthly Order Status (Quantity Wise) of : "+ sel.options[sel.selectedIndex].text;
		var params = 
		{
			bgcolor:"#FFFFFF"
		};
		// alert(str);
		var flashVars = 
		{
			path: "ext_resource/amcharts/flash/", 
			settings_file: "settings_stack.php?comp="+str,
			data_file: "data_stack.php?comp="+str
			
		};        
		// change 8 to 80 to test javascript version            
		if (swfobject.hasFlashPlayerVersion("8"))
		{
			swfobject.embedSWF("ext_resource/amcharts/flash/amcolumn.swf", "chartdiv", "1000", "400", "8.0.0", "../../../amcharts/flash/expressInstall.swf", flashVars, params);
		}
		else
		{
			// Note, as this example loads external data, JavaScript version might only work on server
			var amFallback = new AmCharts.AmFallback();
			amFallback.pathToImages = "../../../amcharts/javascript/images/";
			amFallback.settingsFile = flashVars.settings_file;
			amFallback.dataFile = flashVars.data_file;				
			amFallback.type = "column";
			amFallback.write("chartdiv");
		}
	}
	
	
	
	function set_value_graph()
	{
		document.getElementById('stack_company').style.visibility="hidden";
		document.getElementById('caption_text').innerHTML="Monthly Order Status (Value Wise) for Year: ";
		var params = 
		{
			bgcolor:"#CCCCCC"
		};
		
		var flashVars = 
		{
			path: "ext_resource/amcharts/flash/", 
			settings_file: "settings_value.php",
			data_file: "data_value.php"
		};        
		// change 8 to 80 to test javascript version            
		if ( swfobject.hasFlashPlayerVersion("8") )
		{
			swfobject.embedSWF("ext_resource/amcharts/flash/amcolumn.swf", "chartdiv", "800", "400", "8.0.0", "../../../amcharts/flash/expressInstall.swf", flashVars, params);
		}
		else
		{
			// Note, as this example loads external data, JavaScript version might only work on server
			var amFallback = new AmCharts.AmFallback();
			amFallback.pathToImages = "../../../amcharts/javascript/images/";
			amFallback.settingsFile = flashVars.settings_file;
			amFallback.dataFile = flashVars.data_file;				
			amFallback.type = "column";
			amFallback.write("chartdiv");
		} 
	}
	*/
	window.onload = function()
	{
		//hs_homegraph_stack(1)
		hs_homegraph(1);
		//show_graph( "settings_qnty", "data_qnty", "column", "chartdiv", "", "", 1, 400, 800 )
		/*if( ccount*1>1 )
			hs_homegraph(1);//show_graph( "settings_value", "data_value", "column", "chartdiv", "", "", 1, 400, 750 );
		else
			hs_homegraph_stack(1);*/
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