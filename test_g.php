 <? 
 function csf($data) // checked 3
 {
	 global $db_type;
	 if ($db_type == 0 || $db_type == 1) {
		 return strtolower($data);
	 } else {
		 return strtoupper($data);
	 }
 
 }
 function return_library_array($query, $id_fld_name, $data_fld_name, $new_conn="") {
	/*$query=explode("where", $query);
	$nameArray=sql_select( $query[0] );*/
	$nameArray = sql_select($query, '', $new_conn);
	$new_array=[];
	foreach ($nameArray as $result) {
		if($result[csf($data_fld_name)]=="MnS") $result[csf($data_fld_name)]="M&S";
		else if($result[csf($data_fld_name)]=="HnM") $result[csf($data_fld_name)]="H&M";
		else if($result[csf($data_fld_name)]=="CnA") $result[csf($data_fld_name)]="C&A";
		$new_array[$result[csf($id_fld_name)]] = $result[csf($data_fld_name)];
		print_r($id_fld_name);die;
	}
	
	return $new_array;
}
 function disconnect($con)
 {
	 //$discdb = mssql_close($con);
	 $discdb =oci_close($con);
	 if(!$discdb)
	 {
		 trigger_error("Problem disconnecting database");
	 }
 }
 
function connect()
{ 
	$con = oci_pconnect('PLATFORMERPV3', 'PLATFORMERPV3', '//182.160.107.70:6935/logicdb');
	if(!$con)
	{
		trigger_error("Problem connecting to server");
	}
	return $con;
}
function sql_select($strQuery, $is_single_row="", $new_conn="", $un_buffered="", $connection="")
{
	
	if ( $new_conn!="" )
	{
		$new_conn=explode("*",$new_conn);
		$con_select = oci_connect($new_conn[1], $new_conn[2], $new_conn[0]);
	}
	else
	{
		
		if($connection==""){
			$con_select = connect();
		}else{
			$con_select = $connection;
		}
	}
	//echo $con_select;die;
	$result = oci_parse($con_select, $strQuery);
	oci_execute($result);
	$summ=oci_fetch_assoc($result);
	
	$rows = array();
	 while($summ=oci_fetch_assoc($result))
	 {
		if($is_single_row==1)
		{
			$rows[] = $summ;
			if($connection=="") disconnect($con_select);
			return $rows;

			die;
		}
		else
		{
		$rows[] = $summ;
		}
	 }
	if($connection=="")  disconnect($con_select);
	return $rows;
	 //echo $row['mychars']->load(); for clob data type, mychars is clob
	die;
}
$conn = oci_pconnect('PLATFORMERPV3', 'PLATFORMERPV3', '//182.160.107.70:6935/logicdb');
if (!$conn) {
    $e = oci_error();
    trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
}

//$stid = sql_select('SELECT ID, BUYER_NAME FROM lib_buyer');

//$data_currier_per = sql_select("select commercial_cost_method, commercial_cost_percent, copy_quotation as based_on from variable_order_tracking where company_name=3  and variable_list=57 and status_active=1 and is_deleted=0");

$data_currier_per = return_library_array("select YARN_TYPE_ID,YARN_TYPE_SHORT_NAME from lib_yarn_type where is_deleted=0 and status_active=1 order by YARN_TYPE_SHORT_NAME", "YARN_TYPE_ID", "YARN_TYPE_SHORT_NAME");
//$yarn_type=(count($data_currier_per))?$data_currier_per:$yarn_type_for_entry;
print_r($data_currier_per);

die;
ini_set('display_errors',1);
//include('includes/common.php');
//$con = connect();
$con = oci_pconnect('PLATFORMERPV3', 'PLATFORMERPV3', '//182.160.107.70:6935/logicdb');
 echo $con."test2";die;
 die;
 echo load_html_head_contents("Graph", "", "", $popup, $unicode, $multi_select, 1);
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
	
	//show_graph( "settings_value", "data_value", "column", "chartdiv", "", "", 1, 400, 750 )
	
</script>



<table width="1050" cellpadding="0" cellspacing="0">
    	
        
        <tr>
        	
        	<td align="center" height="400" width="1050">
       		  <div id="chartdiv" style="width:1050px; height:400px; background-color:#FFFFFF"></div>
            </td>
             
  </tr>
        
        
	</table>
    
    <script src="ext_resource/hschart/hschart.js"></script>


    <script>
	<?
	
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

		$catg="[";
		for($i=0;$i<=11;$i++)
		{
			if($i!=11) $catg .="'".date("M",strtotime($yr_mon_part[$i]))."',"; else $catg .="'".date("M",strtotime($yr_mon_part[$i]))."']";
		}
		      if($db_type==0) $year_field="tn.TRANSACTION_DATE"; else $year_field="to_char(tn.TRANSACTION_DATE,'YYYY-MM-DD')";
		$sql_yarn=sql_select("  Select distinct yc.id as id,yc.YARN_COUNT as YARN_COUNT
								 From LIB_YARN_COUNT yc,PRODUCT_DETAILS_MASTER pm, inv_transaction tn
								 Where pm.YARN_COUNT_ID=yc.id
								 And tn.PROD_ID=pm.id
								 and tn.ITEM_CATEGORY=pm.ITEM_CATEGORY_ID
								 and $year_field like '".$yr_mon_part[$i]."-%"."'
								 and pm.ITEM_CATEGORY_ID=1
								 order by 1");
		$k=1;
		$data .="[";
		foreach($sql_yarn as $row_yarn)
		{
			
			$yname=$row_yarn[csf('YARN_COUNT')];
			$data .="{ name: '".$row_yarn[csf('YARN_COUNT')]."', data:[";
			for($i=0;$i<=11;$i++)
			{
				$value=0;
				if($db_type==0) $year_field="tn.TRANSACTION_DATE"; else $year_field="to_char(tn.TRANSACTION_DATE,'YYYY-MM-DD')";
				 
				 $sql="Select distinct round(avg(ORDER_RATE),4) povalue
						 From LIB_YARN_COUNT yc,PRODUCT_DETAILS_MASTER pm, inv_transaction tn
						 Where pm.YARN_COUNT_ID=yc.id
						 And tn.PROD_ID=pm.id
						 and tn.ITEM_CATEGORY=pm.ITEM_CATEGORY_ID
						 and pm.ITEM_CATEGORY_ID=1
						 and yc.is_deleted=0 and yc.status_active=1 
						 and pm.is_deleted=0 and pm.status_active=1 
						 and tn.is_deleted=0 and tn.status_active=1
						 and yc.id='".$row_yarn[csf('id')]."'						
						 and $year_field like '".$yr_mon_part[$i]."-%"."'
						  order by 1 ";
			
				$row=sql_select($sql);
				$value=$row[0][csf('povalue')];
				if( $i!=11)  $data .=number_format( $value,0,'.','').","; else $data .=number_format( $value,0,'.','').""; 
			}
			 if(count($sql_yarn)!=$k) $data .="], stack: 'none'}, "; else $data .="], stack: 'none'}] ";
			 $k++;
		}
		$data_qnt .="[";
		
		$data_qnt .="{ name: '".$yname."', data:[";
		//$data_qnt_stck .="[{ name: 'Avg Yarn Rate', data:[";
		
		$data_val_stck .="[{ name: 'Avg Yarn Rate', data:[";
		
		foreach($tot_for_graph as $key=>$value )
		{
			if( $i!=11)  $data_qnt .=number_format( $value,0,'.','').","; else $data_qnt .=number_format( $value,0,'.','')."";
			
			if( $i!=11)  $data_val_stck .=number_format( $conf_tot_for_graph_stack_val[$key],0,'.','').","; else $data_val_stck .=number_format( $conf_tot_for_graph_stack_val[$key],0,'.','')."";
		}
		 
		 $data_qnt .="], stack: 'none'}] ";
		// $data_qnt_stck .="], stack: 'conf'}, ";
		// $data_val_stck .="], stack: 'conf', color: 'green'}, ";
		 
		//$data_qnt_stck .="{ name: 'Projected', data:[";
		//$data_val_stck .="{ name: 'Projected', data:[";
		/*foreach($tot_for_graph as $key=>$value )
		{
			//if( $i!=11)  $data_qnt .=number_format( $value,0,'.','').","; else $data_qnt .=number_format( $value,0,'.','')."";
			if( $i!=11)  $data_qnt_stck .=number_format( $proj_tot_for_graph_stack[$key],0,'.','').","; else $data_qnt_stck .=number_format( $proj_tot_for_graph_stack[$key],0,'.','')."";
			if( $i!=11)  $data_val_stck .=number_format( $proj_tot_for_graph_stack_val[$key],0,'.','').","; else $data_val_stck .=number_format( $proj_tot_for_graph_stack_val[$key],0,'.','')."";
		}*/
		//$data_qnt_stck .="], stack: 'conf'}] ";
		$data_val_stck .="], stack: 'conf', color: 'green'}] ";
	 
?> 
   
	
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
		//show_graph( "settings_qnty", "data_qnty", "column", "chartdiv", "", "", 1, 400, 800 )
		//show_graph( "settings_value", "data_value", "column", "chartdiv", "", "", 1, 400, 750 );
		hs_homegraph_stack(1) 
	}	
	
	function hs_homegraph( gtype ) 
	{
		//gtype: 1=Value column chart,  2=Qnty  column chart,  3=Stack value column chart, 4=stack qnty column chart
		var data_qnty=<? echo $data_qnt; ?>;
		var data=<? echo $data; ?>;
		if(gtype==1)
		{
			var ddd=data;
			var msg="Avg Values"
		}
		
		$('#chartdiv').highcharts({

			chart: {
				type: 'column'
			},
	
			title: {
				text: ' <? echo "Monthly AVG Yarn Price"; ?> '
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
						this.series.name + ': ' + this.y + '<br/>' ;
						//+ 'Total: ' + this.point.stackTotal;
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
			 var msg="Avg Values";
		 }
		
		 
		$('#chartdiv').highcharts({

			chart: {
				type: 'column'
			},
	
			title: {
				text: ' <? echo "Monthly AVG Yarn Price"; ?> '
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
						this.series.name + ': ' + this.y + '<br/>' 
						+ 'Total: ' + this.point.stackTotal;
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
    
    
    <?
	
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

		$catg="[";
		for($i=0;$i<=11;$i++)
		{
			if($i!=11) $catg .="'".date("M",strtotime($yr_mon_part[$i]))."',"; else $catg .="'".date("M",strtotime($yr_mon_part[$i]))."']";
		}
		      if($db_type==0) $year_field="tn.TRANSACTION_DATE"; else $year_field="to_char(tn.TRANSACTION_DATE,'YYYY-MM-DD')";
			  
			 $v_sql_select="  Select distinct yc.id as id,yc.YARN_COUNT as YARN_COUNT
								 From LIB_YARN_COUNT yc,PRODUCT_DETAILS_MASTER pm, inv_transaction tn
								 Where pm.YARN_COUNT_ID=yc.id
								 And tn.PROD_ID=pm.id
								 and tn.ITEM_CATEGORY=pm.ITEM_CATEGORY_ID
								 and $year_field like '".$yr_mon_part[$i]."-%"."'
								 and pm.ITEM_CATEGORY_ID=1
								 order by 1";
			echo $v_sql_select;					 
		$sql_yarn=sql_select($v_sql_select);
		$k=1;
		$data .="[";
		foreach($sql_yarn as $row_yarn)
		{
			
			$yname=$row_yarn[csf('YARN_COUNT')];
			$data .="{ name: '".$row_yarn[csf('YARN_COUNT')]."', data:[";
			for($i=0;$i<=11;$i++)
			{
				$value=0;
				if($db_type==0) $year_field="tn.TRANSACTION_DATE"; else $year_field="to_char(tn.TRANSACTION_DATE,'YYYY-MM-DD')";
				 
				 $sql="Select distinct round(avg(ORDER_RATE),4) povalue
						 From LIB_YARN_COUNT yc,PRODUCT_DETAILS_MASTER pm, inv_transaction tn
						 Where pm.YARN_COUNT_ID=yc.id
						 And tn.PROD_ID=pm.id
						 and tn.ITEM_CATEGORY=pm.ITEM_CATEGORY_ID
						 and pm.ITEM_CATEGORY_ID=1
						 and yc.is_deleted=0 and yc.status_active=1 
						 and pm.is_deleted=0 and pm.status_active=1 
						 and tn.is_deleted=0 and tn.status_active=1
						 and yc.id='".$row_yarn[csf('id')]."'						
						 and $year_field like '".$yr_mon_part[$i]."-%"."'
						  order by 1 ";
			   echo $sql;
				$row=sql_select($sql);
				$value=$row[0][csf('povalue')];
				if( $i!=11)  $data .=number_format( $value,0,'.','').","; else $data .=number_format( $value,0,'.','').""; 
			}
			 if(count($sql_yarn)!=$k) $data .="], stack: 'none'}, "; else $data .="], stack: 'none'}] ";
			 $k++;
		}
		$data_qnt .="[";
		
		$data_qnt .="{ name: '".$yname."', data:[";
		//$data_qnt_stck .="[{ name: 'Avg Yarn Rate', data:[";
		
		$data_val_stck .="[{ name: 'Avg Yarn Rate', data:[";
		
		foreach($tot_for_graph as $key=>$value )
		{
			if( $i!=11)  $data_qnt .=number_format( $value,0,'.','').","; else $data_qnt .=number_format( $value,0,'.','')."";
			
			if( $i!=11)  $data_val_stck .=number_format( $conf_tot_for_graph_stack_val[$key],0,'.','').","; else $data_val_stck .=number_format( $conf_tot_for_graph_stack_val[$key],0,'.','')."";
		}
		 
		 $data_qnt .="], stack: 'none'}] ";
		// $data_qnt_stck .="], stack: 'conf'}, ";
		// $data_val_stck .="], stack: 'conf', color: 'green'}, ";
		 
		//$data_qnt_stck .="{ name: 'Projected', data:[";
		//$data_val_stck .="{ name: 'Projected', data:[";
		/*foreach($tot_for_graph as $key=>$value )
		{
			//if( $i!=11)  $data_qnt .=number_format( $value,0,'.','').","; else $data_qnt .=number_format( $value,0,'.','')."";
			if( $i!=11)  $data_qnt_stck .=number_format( $proj_tot_for_graph_stack[$key],0,'.','').","; else $data_qnt_stck .=number_format( $proj_tot_for_graph_stack[$key],0,'.','')."";
			if( $i!=11)  $data_val_stck .=number_format( $proj_tot_for_graph_stack_val[$key],0,'.','').","; else $data_val_stck .=number_format( $proj_tot_for_graph_stack_val[$key],0,'.','')."";
		}*/
		//$data_qnt_stck .="], stack: 'conf'}] ";
		$data_val_stck .="], stack: 'conf', color: 'green'}] ";
	 
?> 