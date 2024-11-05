<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');
$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];


/*$color_picker_arr=array(
					1=>"#FF0F00",
					2=>"#FF6600",
					3=>"#FF9E01",
					4=>"#FCD202",
					5=>"#F8FF01",
					6=>"#B0DE09",
					7=>"#04D215",
					8=>"#0D8ECF",
					9=>"#0D52D1",
					10=>"#2A0CD0",
					11=>"#8A0CCF",
					12=>"#FF0F00",
					13=>"#FF6600",
					14=>"#FF9E01",
					15=>"#FCD202",
					16=>"#F8FF01",
					17=>"#B0DE09",
					18=>"#04D215",
					19=>"#0D8ECF",
					20=>"#0D52D1",
					21=>"#2A0CD0",
					22=>"#8A0CCF",
					23=>"#FF0F00",
					24=>"#FF6600",
					25=>"#FF9E01",
					26=>"#FCD202",
					27=>"#F8FF01",
					28=>"#B0DE09",
					29=>"#04D215",
					30=>"#0D8ECF",
					31=>"#0D52D1"
					
);*/

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_id", 150, "select id,location_name from lib_location where status_active=1 and is_deleted=0 and company_id='$data' 
	order by location_name","id,location_name", 1, "-- Select Location --", $selected, "load_drop_down( 'requires/daily_efficiency_report_controller', this.value, 'load_drop_down_floor', 'floor_td' );get_php_form_data( this.value, 'eval_multi_select', 'requires/daily_efficiency_report_controller' );",0 ); 
	exit();    	 
}

if ($action=="load_drop_down_floor")
{
	echo create_drop_down( "cbo_floor_id", 130, "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and production_process=5 
	and location_id='$data' order by floor_name","id,floor_name", 1, "-- Select Floor --", $selected, "",0 );     	 	
	exit();    	 
}
if ($action == "eval_multi_select") {
    echo "set_multiselect('cbo_floor_id','0','0','','0');\n";
    exit();
}


if($action=="line_search_popup")
{		  
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	?>
	<script>
		var selected_id = new Array;
		var selected_name = new Array;
		
    	function check_all_data() {

			var tbl_row_count = document.getElementById( 'list_view' ).rows.length;
			tbl_row_count = tbl_row_count - 0;
			for( var i = 1; i <= tbl_row_count; i++ )
			 {
				var onclickString = $('#tr_' + i).attr('onclick');
				var paramArr = onclickString.split("'");
				var functionParam = paramArr[1];
				js_set_value( functionParam );
			 }
		}
		
		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) { 
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		
		function js_set_value( strCon ) 
		{
			//alert(strCon)
			var splitSTR = strCon.split("_");
			var str = splitSTR[0];
			var selectID = splitSTR[1];
			var selectDESC = splitSTR[2];
			//$('#txt_individual_id' + str).val(splitSTR[1]);
			//$('#txt_individual' + str).val('"'+splitSTR[2]+'"');
			
			toggle( document.getElementById( 'tr_' + str ), '#FFFFCC' );
			
			if( jQuery.inArray( selectID, selected_id ) == -1 ) {
				selected_id.push( selectID );
				selected_name.push( selectDESC );					
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == selectID ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 ); 
			}
			var id = ''; var name = ''; var job = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + ','; 
			}
			id 		= id.substr( 0, id.length - 1 );
			name 	= name.substr( 0, name.length - 1 ); 
			
			$('#txt_selected_id').val( id );
			$('#txt_selected').val( name ); 
		}
		
		function fn_onClosed()
		{
			parent.emailwindow.hide();
		}
    </script>
	<?
	extract($_REQUEST);
	//echo $company;die;
	$line_library=return_library_array( "select id,line_name from lib_sewing_line", "id", "line_name");
	if($company==0) $company_name=""; else $company_name=" and b.company_name=$company";//job_no
	
	if($buyer==0) $buyer_name=""; else $buyer_name="and b.buyer_name=$buyer";
	$prod_reso_allo=return_field_value("auto_update","variable_settings_production","company_name=$company and variable_list=23 and is_deleted=0 and status_active=1");
	$cond ="";
    if($prod_reso_allo==1)
	{
		$line_array=array();
		if($txt_date=="")
		{
			$data_format="";
		}
		else
		{
			if($db_type==0)	$data_format="and b.pr_date='".change_date_format($txt_date,'yyyy-mm-dd')."'";
			if($db_type==2)	$data_format="and b.pr_date='".change_date_format($txt_date,'','',1)."'";
		}
		if( $location!=0 ) $cond .= " and a.location_id= $location";
		if( $floor_id!=0 ) $cond.= " and a.floor_id= $floor_id";
		
		$line_sql="select a.id, a.line_number from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id $data_format and a.is_deleted=0 and b.is_deleted=0 $cond group by a.id, a.line_number";
		$line_sql_result=sql_select($line_sql);
		
		?>
            <input type='hidden' id='txt_selected_id' />
            <input type='hidden' id='txt_selected' />
            <table cellspacing="0" width="250"  border="1" rules="all" class="rpt_table" >
            	<thead>
                	<th width="30"></th>
                    <th width="200">Line Name</th>
                </thead>
            </table>
            <div style="width:250px; max-height:350px; overflow-y:scroll" id="scroll_body" >          
        		<table cellspacing="0" width="230"  border="1" rules="all" class="rpt_table" id="list_view" >
                <? $i=1;
				 foreach($line_sql_result as $row)
				 {
        			$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
        
					$line_val='';
					$line_id=explode(",",$row[csf('line_number')]);
					foreach($line_id as $line_id)
					{
						if($line_val=="") $line_val=$line_library[$line_id]; else $line_val.=','.$line_library[$line_id];
					}
					?>
                	<tr bgcolor="<? echo $bgcolor ; ?>" id="tr_<? echo $i; ?>" onClick="js_set_value('<? echo $i.'_'.$row[csf('id')].'_'.$line_val; ?>')" style="cursor:pointer;">
                    	<td><? echo $i;?></td>
                        <td><? echo $line_val;?></td>
                    </tr>
                 <?
				 $i++;
				 }
				 ?>
              </table>
           </div>
        <table width="250">
            <tr align="center">
                <td><input type="button" name="btn_close" class="formbutton" style="width:100px" value="Close" onClick="fn_onClosed()" /></td>
            </tr>
        </table>
        <?
	}
	else
	{
		if( $location!=0  ) $cond = " and location_name= $location";
		if( $floor_id!=0 ) $cond.= " and floor_name= $floor_id";
		$line_data="select id,line_name from lib_sewing_line where is_deleted=0 and status_active=1 and floor_name!=0 $cond order by line_name";
		echo create_list_view("list_view", "Line No","250","300","310",0, $line_data , "js_set_value", "id,line_name", "", 1, "0", $arr, "line_name", 
		"","setFilterGrid('list_view',-1)","0","",1) ;	
		echo "<input type='hidden' id='txt_selected_id' />";
		echo "<input type='hidden' id='txt_selected' />";
	}
	exit();
}

if($action=="report_generate")
{ 
?>
	<style type="text/css">
		.block_div { 
				width:auto;
				height:auto;
				text-wrap:normal;
				vertical-align:bottom;
				display: block;
				position: !important; 
				-webkit-transform: rotate(-90deg);
				-moz-transform: rotate(-90deg);
		}
		
		.wordBreak{
			word-break:break-all;
		}
	  
	</style> 
<?
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$t_date="".str_replace("'","",$txt_date)."";
	if($db_type==2)	$first_date=date('01-M-Y', strtotime($t_date));
	else			$first_date=date('Y-m-01', strtotime($t_date));
		
	$comapny_id=str_replace("'","",$cbo_company_id);
    $today_date=date("Y-m-d");
	$txt_producting_day="".str_replace("'","",$txt_date)."";
	if(str_replace("'","",$cbo_location_id)!=0) $location_cond=" and a.location_id=$cbo_location_id"; else $location_cond="";
	if(str_replace("'","",$cbo_floor_id)!=0) 	 $floor_cond=" and a.floor_id=$cbo_floor_id"; else $floor_cond="";
	if(str_replace("'","",$hidden_line_id)!="")  $line_cond=" and a.resource_id in(".str_replace("'","",$hidden_line_id).")"; else $line_cond="";
	//cbo_location_id*cbo_floor_id*hidden_line_id
	//***************************************************************************************************************************
	$company_arr=return_library_array( "select id, company_name from lib_company where id=$comapny_id", "id", "company_name"  );
	
	

	// echo $txt_date_from; die;
	ob_start();
	?>
	<fieldset>
    <div>
            <table width="1880"  cellspacing="0"   >
                    <tr class="form_caption" style="border:none;">
                           <td colspan="30" align="center" style="border:none;font-size:14px; font-weight:bold" > Daily Efficiency Report</td>
                     </tr>
                    <tr style="border:none;">
                            <td colspan="30" align="center" style="border:none; font-size:16px; font-weight:bold">
                            Company Name:<? echo $company_arr[str_replace("'","",$comapny_id)]; ?>                                
                            </td>
                      </tr>
                      <tr style="border:none;">
                            <td colspan="30" align="center" style="border:none;font-size:12px; font-weight:bold">
                            <? echo "Production Date: ".$txt_producting_day; ?>
                            </td>
                      </tr>
                </table>
                 <br />

        <table id="table_header_1" class="rpt_table" width="2350" cellpadding="0" cellspacing="0" border="1" rules="all">
            <thead>
                <tr height="50">
                    <th width="80">Line No.</th>
                    <th width="50">SMV</th>
                    <th width="80">Buyer Name</th>
                    <th width="120">Style</th>
                    <th width="120">Item</th>
                    <th width="80">Order Quantity</th>
                    <th width="80">Order cut Plan Qty</th>
                    <th width="60">Ttl MP</th>
                    <th width="60">General Hours</th>
                    <th width="70">Adjusted Hours</th>
                    <th width="70">Day SAH Achvd.</th>
                    <th width="70">Total Spend Hrs</th>
                    <th width="80">Last style Last pcs Output date</th>
                    
                    <th width="70">Plan Target/ day</th>
                    <th width="70">Plan Target</th>
                    <th width="70">Forecast Target</th>
                    <th width="70">Day Input</th>
                    <th width="70">Day Output</th>
                    <th width="70">Running Days</th>
                    <th width="70">Production Days</th>
                    <th width="70">Cum. Input</th>
                    <th width="70">Cum. Output</th>
                    
                    <th width="60">WIP</th>
                    <th width="60">Plan Eff.</th>
                    <th width="60">Forecast/ Plan2 Eff.</th>
                    <th width="60">Day Achvd. Eff. </th>
                    <th width="70">Month Avg .Line eff. (up to date)</th>
                    <th width="70">Floor Eff.</th>
                    <th width="70">Floor eff. (Month to date)</th>
                    <th width="">Remarks</th>

                </tr>
            </thead>
        </table>
        <div style="width:2350px; max-height:400px; overflow-y:scroll" id="scroll_body">
            <table class="rpt_table wordBreak" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" id="resource_allocation_tbody">
            	<tbody > 
				<?php

	            $sql_result=sql_select("select a.*, m.total_target as total_target_mst,m.remarks from pro_resource_ava_min_mst m,pro_resource_ava_min_dtls a where m.id=a.mst_id and  m.resource_id=a.resource_id and m.production_date=a.production_date and a.company_id=$cbo_company_id and a.production_date between '".$first_date."' and $txt_date   and a.status_active=1 and a.is_deleted=0 $location_cond $floor_cond $line_cond order by a.production_date,a.floor_name,a.line_name");
	
	            $production_data_arr=array();
	            $production_target_arr=array();
				$floor_total_production=array();
				foreach($sql_result as $val)
				{
					
					if(strtotime($t_date)==strtotime($val[csf('production_date')]))
					{
						$job_item=$val[csf('garments_item_name')]."_".$val[csf('order_ids')];
						$production_data_arr[$val[csf('floor_name')]][$val[csf('line_name')]][$job_item]['resource_id']=$val[csf('resource_id')];
						$production_data_arr[$val[csf('floor_name')]][$val[csf('line_name')]][$job_item]['item_smv']=$val[csf('item_smv')];
						$production_data_arr[$val[csf('floor_name')]][$val[csf('line_name')]][$job_item]['buyer_name']=$val[csf('buyer_name')];
						$production_data_arr[$val[csf('floor_name')]][$val[csf('line_name')]][$job_item]['ponumber']=$val[csf('ponumber')];
						$production_data_arr[$val[csf('floor_name')]][$val[csf('line_name')]][$job_item]['gmts_item']=$val[csf('garments_item_name')];
						$production_data_arr[$val[csf('floor_name')]][$val[csf('line_name')]][$job_item]['gmtitem_id']=$val[csf('gmtitem_id')];
						$production_data_arr[$val[csf('floor_name')]][$val[csf('line_name')]][$job_item]['man_powers']=$val[csf('man_powers')];
						$production_data_arr[$val[csf('floor_name')]][$val[csf('line_name')]][$job_item]['hourly_target']=$val[csf('hourly_target')];												 				
						$production_data_arr[$val[csf('floor_name')]][$val[csf('line_name')]][$job_item]['capacity']=$val[csf('capacity')];
						$production_data_arr[$val[csf('floor_name')]][$val[csf('line_name')]][$job_item]['horking_hour']=$val[csf('horking_hour')];
						$production_data_arr[$val[csf('floor_name')]][$val[csf('line_name')]][$job_item]['total_target']=$val[csf('total_target')];
						$production_data_arr[$val[csf('floor_name')]][$val[csf('line_name')]][$job_item]['total_produced']=$val[csf('total_produced')];
						$production_data_arr[$val[csf('floor_name')]][$val[csf('line_name')]][$job_item]['variance_pceces']=$val[csf('variance_pceces')];
						$production_data_arr[$val[csf('floor_name')]][$val[csf('line_name')]][$job_item]['efficency_min']=$val[csf('efficency_min')];
						$production_data_arr[$val[csf('floor_name')]][$val[csf('line_name')]][$job_item]['produced_min']=$val[csf('produced_min')];
						$production_data_arr[$val[csf('floor_name')]][$val[csf('line_name')]][$job_item]['job_no_mst']=$val[csf('job_no_mst')];
						$production_data_arr[$val[csf('floor_name')]][$val[csf('line_name')]][$job_item]['order_type']=$val[csf('is_self_order')];
						//$production_data_arr[$val[csf('floor_name')]][$val[csf('line_name')]][$job_item]['forcust_prod_min']=($val[csf('total_target')]*1)*$val[csf('item_smv')]*60;
						$production_data_arr[$val[csf('floor_name')]][$val[csf('line_name')]][$job_item]['order_ids']=$val[csf('order_ids')];
						$production_data_arr[$val[csf('floor_name')]][$val[csf('line_name')]][$job_item]['remarks']=$val[csf('remarks')];

						$production_target_arr[$val[csf('floor_name')]][$val[csf('line_name')]][$job_item]['target']=$val[csf('total_target_mst')];
						//  line efficiency
						$line_total_production[$val[csf('floor_name')]][$val[csf('line_name')]]['forcust_prod_min']+=($val[csf('total_target')]*1)*$val[csf('item_smv')];
						$line_total_production[$val[csf('floor_name')]][$val[csf('line_name')]]['produced_min']+=$val[csf('produced_min')];
						$line_total_production[$val[csf('floor_name')]][$val[csf('line_name')]]['efficency_min']+=$val[csf('efficency_min')];
						$line_total_production[$val[csf('floor_name')]][$val[csf('line_name')]]['total_produced']+=$val[csf('total_produced')];
						
						// flore efficiency 
						$floor_total_production[$val[csf('floor_name')]]['efficency_min']+=$val[csf('efficency_min')];
						$floor_total_production[$val[csf('floor_name')]]['produced_min']+=$val[csf('produced_min')];
						//$floor_total_production[$val[csf('floor_name')]]['total_row']+=1;
					}
					$monthly_total_production[$val[csf('production_date')]]['efficency_min']+=$val[csf('efficency_min')];
					$monthly_total_production[$val[csf('production_date')]]['produced_min']+=$val[csf('produced_min')];
					
					$month_line_total_production[$val[csf('floor_name')]][$val[csf('line_name')]]['efficency_min']+=$val[csf('efficency_min')];
					$month_line_total_production[$val[csf('floor_name')]][$val[csf('line_name')]]['produced_min']+=$val[csf('produced_min')];
					
					$floor_month_total_production[$val[csf('floor_name')]]['efficency_min']+=$val[csf('efficency_min')];
					$floor_month_total_production[$val[csf('floor_name')]]['produced_min']+=$val[csf('produced_min')];
					if($val[csf('is_self_order')]==1)
					{
						$all_order_arr[$val[csf('order_ids')]]=$val[csf('order_ids')];
					}
					else
					{
						$all_subcon_order_arr[$val[csf('order_ids')]]=$val[csf('order_ids')];
					}
					
				}
				
				foreach($production_data_arr as $floor_name=>$floor_val)
				{
					 foreach($floor_val as $line_name=>$line_val)
					 {
						 foreach($line_val as $job_details=>$val)
						{
							$floor_total_production[$floor_name]['total_row']+=1;
						}
					 }
				}
				
				
	         	//company_id, production_date,  mst_id, location_id, floor_id,  resource_id, buyer_id, order_ids,  gmtitem_id, ponumber, location_name,  floor_name, line_name, buyer_name, file_no, reference_no, garments_item_name,  item_smv, operators, helpers,  man_powers, hourly_target, capacity,  horking_hour, total_target, total_produced,  variance_pceces, efficency_min, produced_min, 
				
				$sql_order_data=sql_select("select b.style_ref_no,a.job_no_mst, a.id,a.po_quantity,a.plan_cut from wo_po_break_down a, wo_po_details_master b where a.job_no_mst=b.job_no and a.status_active=1 and a.is_deleted=0 and  b.status_active=1 and b.is_deleted=0 and a.id in (".implode(",",$all_order_arr).")");
				foreach($sql_order_data as $row)
				{
					//$job_item=$row[csf('job_no_mst')]."*".$row[csf('id')];
					$job_information_data['self_order'][$row[csf('id')]]['style_ref_no']=$row[csf('style_ref_no')];
					$job_information_data['self_order'][$row[csf('id')]]['plan_cut']=$row[csf('plan_cut')];
					$job_information_data['self_order'][$row[csf('id')]]['po_quantity']=$row[csf('po_quantity')];
				}
			
				$order_sql=sql_select( "select c.id as id, c.job_no_mst, c.order_quantity,c.cust_style_ref from subcon_ord_mst a, subcon_ord_dtls c where a.subcon_job=c.job_no_mst and a.status_active=1 and c.status_active=1 and  c.id in (".implode(",",$all_subcon_order_arr).")  ");
				
				foreach($order_sql as $val)
				{
					//$job_item=$row[csf('job_no_mst')]."*".$row[csf('id')];
					$job_information_data['subcon_order'][$val[csf('id')]]['style_ref_no']=$val[csf('cust_style_ref')];
					$job_information_data['subcon_order'][$val[csf('id')]]['plan_cut']=$val[csf('order_quantity')];
					$job_information_data['subcon_order'][$val[csf('id')]]['po_quantity']=$val[csf('order_quantity')];
				}
		
				$sql_po_production=sql_select("select a.item_number_id,a.sewing_line,b.job_no_mst,a.po_break_down_id,min( case when a.production_type=4 then a.production_date else null end) as first_input, sum( case when a.production_type=4 then a.production_quantity  else 0 end) as input_qty,sum( case when a.production_type=4 and  a.production_date=$txt_date  then a.production_quantity else 0 end) as today_input_qty,sum( case when a.production_type=5 then a.production_quantity  else 0 end) as output_qty from pro_garments_production_mst a,wo_po_break_down b where a.po_break_down_id=b.id and a.production_type in (4,5) and a.production_date<=$txt_date and a.po_break_down_id in (".implode(",",$all_order_arr).") and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group  by a.item_number_id,a.sewing_line,b.job_no_mst,a.po_break_down_id");
				foreach($sql_po_production as $row)
				{
					//$job_item=$row[csf('job_no_mst')]."_".$row[csf('garments_item_name')];
					$po_information_data['self_order'][$row[csf('item_number_id')]][$row[csf('po_break_down_id')]][$row[csf('sewing_line')]]['first_input']=$row[csf('first_input')];
					$po_information_data['self_order'][$row[csf('item_number_id')]][$row[csf('po_break_down_id')]][$row[csf('sewing_line')]]['today_input_qty']=$row[csf('today_input_qty')];
					$po_information_data['self_order'][$row[csf('item_number_id')]][$row[csf('po_break_down_id')]][$row[csf('sewing_line')]]['total_input_qty']=$row[csf('input_qty')];
					$po_information_data['self_order'][$row[csf('item_number_id')]][$row[csf('po_break_down_id')]][$row[csf('sewing_line')]]['total_output_qty']=$row[csf('output_qty')];
				}


				$sql_self_pro_date=sql_select("select a.item_number_id,a.po_break_down_id,a.sewing_line,a.production_date from pro_garments_production_mst a where  a.production_type=5 and a.production_date<=$txt_date and a.po_break_down_id in (".implode(",",$all_order_arr).") and a.status_active=1 and a.is_deleted=0  group  by a.item_number_id,a.po_break_down_id,a.sewing_line,a.production_date");
				$line_date_arr=array();
				foreach ($sql_self_pro_date as $p_val)
				{
					$line_date_arr['self_order'][$p_val[csf('sewing_line')]][$p_val[csf('item_number_id')]][$p_val[csf('po_break_down_id')]][$p_val[csf('production_date')]]=$p_val[csf('production_date')];
				}
				//print_r($line_date_arr);die;

				$sql_sub_production=sql_select("select a.gmts_item_id,a.line_id,a.order_id,min( case when a.production_type=1 then a.production_date else null end) as first_input, sum( case when a.production_type=1 then a.production_qnty  else 0 end) as input_qty,sum( case when a.production_type=1 and  a.production_date=$txt_date  then a.production_qnty else 0 end) as today_input_qty,sum( case when a.production_type=2 then a.production_qnty  else 0 end) as output_qty from subcon_gmts_prod_dtls a where  a.production_type in (1,2) and a.production_date<=$txt_date and a.order_id in (".implode(",",$all_subcon_order_arr).") and a.status_active=1 and a.is_deleted=0  group  by a.gmts_item_id,a.line_id,a.order_id");

				foreach($sql_sub_production as $val)
				{
					//$job_item=$val[csf('job_no_mst')]."_".$val[csf('garments_item_name')];
					$po_information_data['subcon_order'][$val[csf('gmts_item_id')]][$val[csf('order_id')]][$val[csf('line_id')]]['first_input']=$val[csf('first_input')];
					$po_information_data['subcon_order'][$val[csf('gmts_item_id')]][$val[csf('order_id')]][$val[csf('line_id')]]['today_input_qty']=$val[csf('today_input_qty')];
					$po_information_data['subcon_order'][$val[csf('gmts_item_id')]][$val[csf('order_id')]][$val[csf('line_id')]]['total_input_qty']=$val[csf('input_qty')];
					$po_information_data['subcon_order'][$val[csf('gmts_item_id')]][$val[csf('order_id')]][$val[csf('line_id')]]['total_output_qty']=$val[csf('output_qty')];
				}


				$sql_sub_pro_date=sql_select("select a.gmts_item_id,a.line_id,a.order_id,a.production_date from subcon_gmts_prod_dtls a where  a.production_type=1 and a.production_date<=$txt_date and a.order_id in (".implode(",",$all_subcon_order_arr).") and a.status_active=1 and a.is_deleted=0  group  by a.gmts_item_id,a.line_id,a.order_id,a.production_date");

				foreach ($sql_sub_pro_date as $s_val)
				{
					$line_date_arr['subcon_order'][$s_val[csf('line_id')]][$s_val[csf('gmts_item_id')]][$s_val[csf('order_id')]][$s_val[csf('production_date')]]=$s_val[csf('production_date')];

				}
                $i=1; 
            	foreach($production_data_arr as $floor_name=>$floor_val)
				{
					//echo count($floor_val); 
					 foreach($floor_val as $line_name=>$line_val)
					 {
						 unset($check_line_arr);
						 
						 if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						//$floor_flag=0;
						foreach($line_val as $job_details=>$val)
					 	{
							$flag=0;
							$order_type=$val['order_type'];
							if($order_type==1)  $order_string="self_order";
							else 				$order_string="subcon_order";

							?>
								<tr bgcolor='<?php echo $bgcolor; ?>' valign="middle">
                                <?php 
								
								if(!in_array($line_name,$check_line_arr))
								{
									$check_line_arr[]=$line_name;
									$flag=1;
								?>
									<td width="80" align="center" rowspan="<?php echo count($line_val); ?>"><?php echo $line_name; ?></td>
								<?php
								}
								
								?>
								<td width="50" align="center"><?php echo $val['item_smv']; ?></td>
								<td width="80" align="center"> <?php echo $val['buyer_name']; ?></td>
								<td width="120" align="center"><?php echo $job_information_data[$order_string][$val['order_ids']]['style_ref_no']; ?> </td>
								<td width="120" align="center"><p><?php echo $val['gmts_item']; ?></p></td>
								<td width="80" align="right"><?php echo $job_information_data[$order_string][$val['order_ids']]['po_quantity']; ?></td>
								<td width="80" align="right"><p><?php echo $job_information_data[$order_string][$val['order_ids']]['plan_cut']; ?></p></td>
								
                                <?php 
								if($flag==1)
								{
									$adjust_hour=0;
									$adjust_hour=($line_total_production[$floor_name][$line_name]['efficency_min']/60)-($val['man_powers']*$val['horking_hour']);
									$adjust_target_per=($line_total_production[$floor_name][$line_name]['efficency_min']/60)/($val['man_powers']*$val['horking_hour']);
								?>
                                	<td width="60" align="right" rowspan="<?php echo count($line_val); ?>"><p><?php echo $val['man_powers']; ?></p></td>
									<td width="60" align="right" rowspan="<?php echo count($line_val); ?>"><p><?php echo $val['horking_hour']; ?><p/> </td>
									<td width="70" align="right" rowspan="<?php echo count($line_val); ?>"><p><?php echo number_format($adjust_hour,2); ?></p></td>
									<td width="70" align="right" rowspan="<?php echo count($line_val); ?>">	  <?php	echo number_format(($line_total_production[$floor_name][$line_name]['produced_min']/60),2);?></td>
                                    <td width="70" align="right" rowspan="<?php echo count($line_val); ?>">	  <?php	echo number_format(($line_total_production[$floor_name][$line_name]['efficency_min']/60),2);?></td>
                                <?php 
									
									$total_man_power+=$val['man_powers'];
									$total_working_hour+=$val['horking_hour'];
									$total_adjust_hour+=$adjust_hour;
									$total_produced_min+=$line_total_production[$floor_name][$line_name]['produced_min'];
									$total_used_min+=$line_total_production[$floor_name][$line_name]['efficency_min'];
									$total_forecust_min+=$line_total_production[$floor_name][$line_name]['forcust_prod_min'];
									//line_date_arr
								}
								?>
								<td width="80" align="center"><?php echo change_date_format($po_information_data[$order_string][$val['gmtitem_id']][$val['order_ids']][$val['resource_id']]['first_input']); ?></td>

								<td width="70" align="center">From P.B<?php //echo $val[csf('total_produced')]; ?></td>
                                <?php 
								if($flag==1)
								{
									$total_target=$production_target_arr[$floor_name][$line_name][$job_details]['target'];
									$adjust_target=$adjust_target_per*$total_target;

								?>
									<td width="70" align="right" rowspan="<?php echo count($line_val); ?>"><?php echo ($total_target); ?></td>
									<td width="70" align="right" rowspan="<?php echo count($line_val); ?>"><?php echo number_format($adjust_target,0); ?></td>
                                <?php 
									$grand_total_target+=$total_target;
									$grand_adjust_target+=$adjust_target;
								}
								?>
								<td width="70" align="right"><?php echo $po_information_data[$order_string][$val['gmtitem_id']][$val['order_ids']][$val['resource_id']]['today_input_qty']; ?></td>
                                <td width="70" align="right"><?php echo $val['total_produced']; ?></td>
								<td width="70" align="right">
								<?php
								//echo date("d-m-Y",strtotime($po_information_data[$order_id]['first_input'])).$txt_date;
								if($po_information_data[$order_string][$val['gmtitem_id']][$val['order_ids']][$val['resource_id']]['first_input']=="")
								{
									echo 0;
								}
								else
								{
									echo datediff("d",date("d-m-Y",strtotime($po_information_data[$order_string][$val['gmtitem_id']][$val['order_ids']][$val['resource_id']]['first_input'])),str_replace("'","",$txt_date));
								}
								 ?></td>
								<td width="70" align="right"><?php echo count($line_date_arr[$order_string][$val['resource_id']][$val['gmtitem_id']][$val['order_ids']]); ?></td>
								<td width="70" align="right"><?php echo $po_information_data[$order_string][$val['gmtitem_id']][$val['order_ids']][$val['resource_id']]['total_input_qty']; ?></td>
								<td width="70" align="right"><?php echo $po_information_data[$order_string][$val['gmtitem_id']][$val['order_ids']][$val['resource_id']]['total_output_qty']; ?></td>
								<td width="60" align="right"><?php echo $po_information_data[$order_string][$val['gmtitem_id']][$val['order_ids']][$val['resource_id']]['total_input_qty']-$po_information_data[$order_string][$val['gmtitem_id']][$val['order_ids']][$val['resource_id']]['total_output_qty']; ?></td>
                                <td align="right" width="60"><?php //echo $val[csf('capacity')]; ?></td>
                                 <?php 
								if($flag==1)
								{
								?>
									<td width="60" align="right" rowspan="<?php echo count($line_val); ?>"><?php echo number_format((($line_total_production[$floor_name][$line_name]['forcust_prod_min']*100)/$line_total_production[$floor_name][$line_name]['efficency_min']),2)."%"; ?></td>
                                    <td width="60" align="right" rowspan="<?php echo count($line_val); ?>"><?php echo number_format((($line_total_production[$floor_name][$line_name]['produced_min']*100)/$line_total_production[$floor_name][$line_name]['efficency_min']),2)."%"; ?></td>
                                    <td width="70" align="right" rowspan="<?php echo count($line_val); ?>"><?php echo number_format((($month_line_total_production[$floor_name][$line_name]['produced_min']*100)/$month_line_total_production[$floor_name][$line_name]['efficency_min']),2)."%"; ?></td>
                                    
                                 	
                                <?php 
									$month_line_total_produced_min+=$month_line_total_production[$floor_name][$line_name]['produced_min'];
									$month_line_total_used_min+=$month_line_total_production[$floor_name][$line_name]['efficency_min'];
									$graph_line_data_arr[$line_name]=number_format((($line_total_production[$floor_name][$line_name]['produced_min']*100)/$line_total_production[$floor_name][$line_name]['efficency_min']),2,'.', '');
								}
							
								if(!in_array($floor_name,$check_floor_arr))
								{
									$check_floor_arr[]=$floor_name;
										
								?>
									 <td width="70" align="right" rowspan="<?php echo $floor_total_production[$floor_name]['total_row']; ?>"><?php echo number_format((($floor_total_production[$floor_name]['produced_min']*100)/$floor_total_production[$floor_name]['efficency_min']),2)."%"; ?></td>
                                     <td width="70" align="right" rowspan="<?php echo $floor_total_production[$floor_name]['total_row']; ?>"><?php  echo number_format((($floor_month_total_production[$floor_name]['produced_min']*100)/$floor_month_total_production[$floor_name]['efficency_min']),2)."%"; ?></td>
								<?php
									//$graph_line_data_arr[$line_name]=number_format((($line_total_production[$floor_name][$line_name]['produced_min']*100)/$line_total_production[$floor_name][$line_name]['efficency_min']),2);
									$total_forecust_min+=$floor_total_production[$floor_name]['forcust_prod_min'];
									$floor_total_produced_min+=$floor_total_production[$floor_name]['produced_min'];
									$floor_total_used_min+=$floor_total_production[$floor_name]['efficency_min'];
									$floor_month_total_produced_min+=$floor_month_total_production[$floor_name]['produced_min'];
									$floor_month_total_used_min+=$floor_month_total_production[$floor_name]['efficency_min'];
								}
								
								
								
								if($flag==1)
								{

								?>
									<td width="" align="center" rowspan="<?php echo count($line_val); ?>"><?php echo $val['remarks']; ?></td>
								<?php
								}
								?>
							</tr>
							<?php
							$total_order_qty+=$job_information_data[$order_string][$val['order_ids']]['po_quantity'];
							$total_plan_qty+=$job_information_data[$order_string][$val['order_ids']]['plan_cut'];
							$total_tdoay_input_qty+=$po_information_data[$order_string][$val['gmtitem_id']][$val['order_ids']][$val['resource_id']]['today_input_qty'];
							$total_tdoay_output_qty+=$val['total_produced'];
							$grand_input_qty+=$po_information_data[$order_string][$val['gmtitem_id']][$val['order_ids']][$val['resource_id']]['total_input_qty'];
							$grand_output_qty+=$po_information_data[$order_string][$val['gmtitem_id']][$val['order_ids']][$val['resource_id']]['total_output_qty'];
							//$total_helper+=$val[csf('helpers')];
							
							//$total_capacity+=$val[csf('capacity')];
							//
							//$total_terget+=($val[csf('horking_hour')]*$val[csf('hourly_target')]);
							//$grand_total_product+=$val[csf('total_produced')];
							//$variance_pecess+=$val[csf('variance_pceces')];
							//$gnd_avable_min+=$val[csf('efficency_min')];
							//$gnd_product_min+=$val[csf('produced_min')];
					 	}
						$i++;
						
					}
				}
    		?>
   				</tbody>
                <tfoot>
                   <tr>
                        <th width="450" align="right" colspan="5">Total</th>
                        <th width="80" align="right"><?php echo $total_order_qty; ?></th>
                        <th width="80" align="right"><?php echo $total_plan_qty; ?></th>
                        <th width="60" align="right"><?php echo $total_man_power; ?></th>
                        <th width="60" align="right"><?  echo $total_working_hour; ?>&nbsp;</th>
                        <th width="70" align="right"><? echo number_format($total_adjust_hour,2); ?>&nbsp;</th>
                        <th width="70" align="right"><? echo number_format(($total_produced_min/60),2); ?>&nbsp;</th>
                        <th width="70" align="right"><? echo number_format(($total_used_min/60),2); ?>&nbsp;</th>
                        <th width="80" align="right"><? //echo $grand_total_product; ?>&nbsp;</th>
                        <th width="70" align="right"><? //echo $variance_pecess; ?>&nbsp;</th>
                        <th width="70" align="right"><? echo $grand_total_target; ?>&nbsp;</th>
                        <th width="70" align="right"><? echo number_format($grand_adjust_target,0); ?>&nbsp;</th>
                        <th width="70" align="right"><? echo $total_tdoay_input_qty; ?>&nbsp;</th>
                        <th width="70" align="right"><? echo $total_tdoay_output_qty; ?>&nbsp;</th>
                        <th width="70" align="right"><? // echo $gnd_total_tgt_h; ?>&nbsp;</th>
                        <th width="70" align="right"><? // echo $gnd_total_tgt_h; ?>&nbsp;</th>
                        <th width="70" align="right"><? echo $grand_input_qty; ?>&nbsp;</th>
                        <th width="70" align="right"><? echo $grand_output_qty; ?>&nbsp;</th>
                        <th width="60" align="right"><? echo $grand_input_qty-$grand_output_qty; ?>&nbsp;</th>
                        <th width="60" align="right"><? //echo $grand_input_qty-$grand_output_qty; ?>&nbsp;</th>
                        <th width="60" align="right"><? echo  number_format((($total_forecust_min*100)/$total_used_min),2)."%"; ?>&nbsp;</th>
                        <th width="60" align="right"><? echo  number_format((($total_produced_min*100)/$total_used_min),2)."%"; ?>&nbsp;</th>
                        <th width="60" align="right"><? echo  number_format((($month_line_total_produced_min*100)/$month_line_total_used_min),2)."%"; ?>&nbsp;</th>
                        <th width="60" align="right"><? echo  number_format((($floor_total_produced_min*100)/$floor_total_used_min),2)."%"; ?>&nbsp;</th>
                        <th width="70" align="right"><? echo  number_format((($floor_month_total_produced_min*100)/$floor_month_total_used_min),2)."%"; ?>&nbsp;</th>
                         <th width="" align="right">&nbsp;</th>
                    </tr>
                </tfoot>
            </table>
         </div>
    </div>  
	</fieldset>
    <br />
    <fieldset style="width:20%;">
    	<div id="chartdiv"></div> 
    </fieldset>
    <br/>
    <fieldset style="width:20%;">
    	<div id="chart_monthly_div"></div>
    </fieldset>
           <?php
		   
			$min_width=200;
			$width=0;$c_sl=1;					
			$chart_data='[';
			foreach($graph_line_data_arr as $line=>$value)
			{
				$chart_data.="{Line: '".$line."',Percentage: $value,color:'".$color_picker_arr[$c_sl]."'},";
				$width=$width+50;
				$c_sl++;
			}
			$chart_data=rtrim($chart_data,',');
			$chart_data.=']';
			if($width<$min_width) $width=$min_width;
			
			//echo $chart_data;die;
			// monthly chart
			
			$min_month_width=200;
			$width_month=0;					
			$chart_month_data='[';
			foreach($monthly_total_production as $pr_date=>$pr_value)
			{
			//echo $pr_value['produced_min']."**".$pr_value['efficency_min'];	die;
				$pr_date=date("d-M",strtotime($pr_date));
				$daily_efficiency=number_format((($pr_value['produced_min']*100)/$pr_value['efficency_min']),2);
				$chart_month_data.="{Date: '".$pr_date."',Percentage: $daily_efficiency},";
				$width_month=$width_month+50;
			}
			$chart_month_data=rtrim($chart_month_data,',');
			$chart_month_data.=']';
			if($width_month<$min_month_width) $width_month=$min_month_width;
			
		?>
		
 		 <style>
			#chartdiv {
				width		: <?php echo $width; ?>px;
				height		: 300px;
				font-size	: 11px;
			}
			
			#chart_monthly_div {
				width		: <?php echo $width_month; ?>px;
				height		: 300px;
				font-size	: 11px;
			}					
		</style>
        <script >
		
		var chart_data=<? echo $chart_data; ?>;
		var chart = AmCharts.makeChart( "chartdiv", {
		  "type": "serial",
		  "theme": "light",
		  "dataProvider":chart_data ,
		  "color" : "#111111",
		  "valueAxes": [ {
			"gridColor": "#0000FF",
			"gridAlpha": 0.2,
			"dashLength": 0
		  } ],
		  "gridAboveGraphs": true,
		  "startDuration": 1,
		  "graphs": [ {
			"balloonText": "[[category]]: <b>[[value]]</b>",
			"fillAlphas": 0.8,
			"lineAlpha": 0.2,
			"type": "column",
			"valueField": "Percentage"
		  } ],
		  "chartCursor": {
			"categoryBalloonEnabled": false,
			"cursorAlpha": 0,
			"zoomable": false
		  },
		  "categoryField": "Line",
		  "categoryAxis": {
			"gridPosition": "start",
			"gridAlpha": 0,
			"tickPosition": "start",
			"tickLength": 20
		  },
		  "export": {
			"enabled": true
		  }
		
		} );
		
		
		var chart_data=<? echo $chart_month_data; ?>;
		var chart = AmCharts.makeChart( "chart_monthly_div", {
		  "type": "serial",
		  "theme": "light",
		  "dataProvider":chart_data ,
		  "valueAxes": [ {
			"gridColor": "#FF0000",
			"gridAlpha": 0.2,
			"dashLength": 0
		  } ],
		  "gridAboveGraphs": true,
		  "startDuration": 1,
		  "graphs": [ {
			"balloonText": "[[category]]: <b>[[value]]</b>",
			"fillAlphas": 0.8,
			"lineAlpha": 0.2,
			"type": "column",
			"valueField": "Percentage"
		  } ],
		  "chartCursor": {
			"categoryBalloonEnabled": false,
			"cursorAlpha": 0,
			"zoomable": false
		  },
		  "categoryField": "Date",
		  "categoryAxis": {
			"gridPosition": "start",
			"labelRotation": 90,
			"gridAlpha": 0,
			"tickPosition": "start",
			"tickLength": 10
		  },
		  "export": {
			"enabled": true
		  }
		
		} );
	</script>
<? 
	foreach (glob("$user_id*.xls") as $filename) 
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename,'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	echo "$total_data####$filename";   
	exit();      
}






if($action=="distribute_available_minit")
{
	echo load_html_head_contents("FOB Info", "../../", 1, 1,'','','');
	extract($_REQUEST);
?>
	
     <script>
	
		function calculate_available_minit(value)
		{
			var total_available_minit=0;
			var max_available_minit=$("#hidden_available_min").val();
			$("#table_available_minit").find('tbody tr').each(function()
			{
				
				var	avialable_minit=$(this).find('input[name="txt_available_min[]"]').val()*1;
				total_available_minit+=avialable_minit;
				if(total_available_minit>max_available_minit)
				{
					total_available_minit=total_available_minit-avialable_minit;
					$(this).find('input[name="txt_available_min[]"]').val('');
				}
			});
			
			
			$("#total_available_minit").text(total_available_minit);
		}
		
		function popup_close()
		{
			var max_available_minit=$("#hidden_available_min").val()*1;
			var total_available_minit=$("#total_available_minit").text()*1;
			var	po_info="";
			if(max_available_minit==total_available_minit)
			{
				
				$("#table_available_minit").find('tbody tr').each(function()
				{
					var	avialable_minit=$(this).find('input[name="txt_available_min[]"]').val()*1;
					var	po_id=$(this).find('input[name="txt_po_id[]"]').val()*1;
					if(po_info!='')
					{
						po_info=po_info+","+po_id+"*"+avialable_minit;
					}
					else
					{
						po_info=po_id+"*"+avialable_minit;
					}
				});
				
				$("#po_available_minutes").val(po_info);
				parent.emailwindow.hide();
			}
			else
			{
				alert("Total available minutes must be equal to line available minutes. ");
			}
		}
	</script>	
    <fieldset style="width:1020px; ">
		<input type="hidden" id="po_available_minutes" name="po_available_minutes" />
		<div id="report_container">
        	<h3>Line Avaliable Minute: <?php echo $available_minit; ?> </h3>
			<table border="1" class="rpt_table" rules="all" width="1000" cellpadding="0" cellspacing="0" align="center">
	
                <thead>
                	<th width="30">SL</th>
                    <th width="120">Buyer Name</th>
                    <th width="120">Order No</th>
                    <th width="80">File No</th>
                    <th width="80">Ref. No</th>
                    <th width="120">Garments Item</th>
                    <th width="50">SMV</th>
                    <th width="100">Prod. Qty.</th>
                    <th width="100">Produced minutes</th>
                    <th width="100">Used Minutes</th>
				</thead>
                </table>
                <table border="1" class="rpt_table" rules="all" width="1000" cellpadding="0" cellspacing="0" align="center" id="table_available_minit">
                    <tbody>
						<?
                        $all_po_information=explode(",",$po_iinformation);
						$all_pre_po_available_min=explode(",",$pre_po_available_min);
						$pre_po_avai_arr=array();
						foreach($all_pre_po_available_min as $single_available_min)
						{
							$single_available_min_arr= explode("*",$single_available_min);
							$pre_po_avai_arr[$single_available_min_arr[0]]=$single_available_min_arr[1];
						}
						//print_r($pre_po_avai_arr);
						
                        $k=1;	
                        foreach($all_po_information as $single_po_information)
                        {
                            if($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                            $single_po_info_arr= explode("*",$single_po_information);
                                
                       ?>
                          <tr style="font:'Arial Narrow';" align="center" bgcolor="<? echo $bgcolor;?>"  id="tr_<? echo $k; ?>">
                            <td width="30"><? echo $k; ?></td>
                            <td width="120" style="word-wrap:break-word; word-break: break-all; text-align:left" ><? echo $single_po_info_arr[3]; ?></td>
                            <td width="120" style="word-wrap:break-word; word-break: break-all; text-align:left" ><? echo $single_po_info_arr[1]; ?></td>
                            <td width="80" style="word-wrap:break-word; word-break: break-all; text-align:right" ><? echo $single_po_info_arr[5]; ?></td>
                            <td width="80" style="word-wrap:break-word; word-break: break-all; text-align:center" ><? echo $single_po_info_arr[6];?></td>
                            <td width="120" style="word-wrap:break-word; word-break: break-all; text-align:center" ><? echo $garments_item[$single_po_info_arr[4]];?></td>
                            <td width="50" style="word-wrap:break-word; word-break: break-all; text-align:right" ><? echo  $single_po_info_arr[7]; ?></td> 
                            <td width="100" style="word-wrap:break-word; word-break: break-all; text-align:right" ><? echo  $single_po_info_arr[9]; ?></td> 
                            <td width="100" style="word-wrap:break-word; word-break: break-all; text-align:right" ><? echo  $single_po_info_arr[8]; ?></td> 
                            <td width="100" style="word-wrap:break-word; word-break: break-all; text-align:right" >
                                <input type="text" class="text_boxes_numeric" id="txt_available_min<? echo $k; ?>" name="txt_available_min[]" style="width:80px" onkeyup="calculate_available_minit(this.value)" value="<?php echo $pre_po_avai_arr[$single_po_info_arr[0]];?>" />
                                
                                <input type="hidden" class="text_boxes_numeric" id="txt_po_id<? echo $k; ?>" name="txt_po_id[]" value="<?php echo $single_po_info_arr[0] ;?>" />
                            </td> 
                        </tr>
                        <?
                        $total_produced_qty+=$single_po_info_arr[9];
						$total_produced_min+=$single_po_info_arr[8];
						$total_available_min+=$pre_po_avai_arr[$single_po_info_arr[0]];
                        $k++;
                        }
                        ?>
                    </tbody>
                    <tfoot>
                        <tr class="tbl_bottom" >
                            <td colspan="7"><input type="hidden" class="text_boxes" id="hidden_available_min" name="hidden_available_min" value="<?php echo $available_minit;?>" /> Total </td>
                            <td align="right" id=""> <? echo $total_produced_qty;?></td>
                            <td align="right" id=""> <? echo $total_produced_min;?></td>
                            <td align="right" id="total_available_minit"> <? echo $total_available_min;?></td>
                        </tr>
                         <tr >
                            <td colspan="10" align="center"><input type="button" class="formbutton"  value="Close" style="width:100px" onclick="popup_close()" /> </td>
                        </tr>
                </tfoot>
                </table>
         </div>

     <script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</fieldset>
                
                
<?
	exit();
}


?>