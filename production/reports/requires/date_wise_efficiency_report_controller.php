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
	order by location_name","id,location_name", 1, "-- Select Location --", $selected, "load_drop_down( 'requires/date_wise_efficiency_report_controller', this.value, 'load_drop_down_floor', 'floor_td' );get_php_form_data( this.value, 'eval_multi_select', 'requires/date_wise_efficiency_report_controller' );",0 ); 
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
		
		$line_sql="select a.id, a.line_number from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id  and a.is_deleted=0 and b.is_deleted=0 $cond group by a.id, a.line_number";
		//echo $line_sql;
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
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);
	
	$comapny_id=str_replace("'","",$cbo_company_id);
    
    if ($txt_date_from != "" && $txt_date_to != "") {
		if ($db_type == 0) {
			$date_cond = " and a.production_date between '" . change_date_format(trim($txt_date_from), "yyyy-mm-dd") . "' and '" . change_date_format(trim($txt_date_to), "yyyy-mm-dd") . "'";
		} else {
			$date_cond = " and a.production_date between '" . change_date_format(trim($txt_date_from), '', '', 1) . "' and '" . change_date_format(trim($txt_date_to), '', '', 1) . "'";
		}
	} else {
		$date_cond = "";
	}
	
	if(str_replace("'","",$cbo_location_id)!=0) $location_cond=" and a.location_id=$cbo_location_id"; else $location_cond="";
	if(str_replace("'","",$cbo_floor_id)!=0) 	 $floor_cond=" and a.floor_id=$cbo_floor_id"; else $floor_cond="";
	if(str_replace("'","",$hidden_line_id)!="")  $line_cond=" and a.resource_id in(".str_replace("'","",$hidden_line_id).")"; else $line_cond="";
	//cbo_location_id*cbo_floor_id*hidden_line_id
	//***************************************************************************************************************************
	$company_arr=return_library_array( "select id, company_name from lib_company where id=$comapny_id", "id", "company_name"  );	

	// echo $txt_date_from; die;

    $sql_result=sql_select("SELECT a.*, m.total_target as total_target_mst,m.remarks from pro_resource_ava_min_mst m,pro_resource_ava_min_dtls a where m.id=a.mst_id and  m.resource_id=a.resource_id and m.production_date=a.production_date and a.company_id=$cbo_company_id $date_cond  and a.status_active=1 and a.is_deleted=0 $location_cond $floor_cond $line_cond order by a.production_date,a.floor_name,a.line_name");



   

    $production_data_arr=array();
    $production_target_arr=array();
	$floor_total_production=array();
	foreach($sql_result as $val)
	{		
		$job_item=$val[csf('garments_item_name')]."_".$val[csf('order_ids')];
		$production_data_arr[$val[csf('production_date')]][$val[csf('floor_name')]][$val[csf('line_name')]][$job_item]['resource_id']=$val[csf('resource_id')];
		$production_data_arr[$val[csf('production_date')]][$val[csf('floor_name')]][$val[csf('line_name')]][$job_item]['item_smv']=$val[csf('item_smv')];
		$production_data_arr[$val[csf('production_date')]][$val[csf('floor_name')]][$val[csf('line_name')]][$job_item]['buyer_name']=$val[csf('buyer_name')];
		$production_data_arr[$val[csf('production_date')]][$val[csf('floor_name')]][$val[csf('line_name')]][$job_item]['ponumber']=$val[csf('ponumber')];
		$production_data_arr[$val[csf('production_date')]][$val[csf('floor_name')]][$val[csf('line_name')]][$job_item]['gmts_item']=$val[csf('garments_item_name')];
		$production_data_arr[$val[csf('production_date')]][$val[csf('floor_name')]][$val[csf('line_name')]][$job_item]['gmtitem_id']=$val[csf('gmtitem_id')];
		$production_data_arr[$val[csf('production_date')]][$val[csf('floor_name')]][$val[csf('line_name')]][$job_item]['man_powers']=$val[csf('man_powers')];
		$production_data_arr[$val[csf('production_date')]][$val[csf('floor_name')]][$val[csf('line_name')]][$job_item]['hourly_target']=$val[csf('hourly_target')];												 				
		$production_data_arr[$val[csf('production_date')]][$val[csf('floor_name')]][$val[csf('line_name')]][$job_item]['capacity']=$val[csf('capacity')];
		$production_data_arr[$val[csf('production_date')]][$val[csf('floor_name')]][$val[csf('line_name')]][$job_item]['horking_hour']=$val[csf('horking_hour')];
		$production_data_arr[$val[csf('production_date')]][$val[csf('floor_name')]][$val[csf('line_name')]][$job_item]['total_target']=$val[csf('total_target')];
		$production_data_arr[$val[csf('production_date')]][$val[csf('floor_name')]][$val[csf('line_name')]][$job_item]['total_produced']=$val[csf('total_produced')];
		$production_data_arr[$val[csf('production_date')]][$val[csf('floor_name')]][$val[csf('line_name')]][$job_item]['variance_pceces']=$val[csf('variance_pceces')];
		$production_data_arr[$val[csf('production_date')]][$val[csf('floor_name')]][$val[csf('line_name')]][$job_item]['efficency_min']=$val[csf('efficency_min')];
		$production_data_arr[$val[csf('production_date')]][$val[csf('floor_name')]][$val[csf('line_name')]][$job_item]['produced_min']=$val[csf('produced_min')];
		$production_data_arr[$val[csf('production_date')]][$val[csf('floor_name')]][$val[csf('line_name')]][$job_item]['job_no_mst']=$val[csf('job_no_mst')];
		$production_data_arr[$val[csf('production_date')]][$val[csf('floor_name')]][$val[csf('line_name')]][$job_item]['order_type']=$val[csf('is_self_order')];
		//$production_data_arr[$val[csf('floor_name')]][$val[csf('line_name')]][$job_item]['forcust_prod_min']=($val[csf('total_target')]*1)*$val[csf('item_smv')]*60;
		$production_data_arr[$val[csf('production_date')]][$val[csf('floor_name')]][$val[csf('line_name')]][$job_item]['order_ids']=$val[csf('order_ids')];
		$production_data_arr[$val[csf('production_date')]][$val[csf('floor_name')]][$val[csf('line_name')]][$job_item]['remarks']=$val[csf('remarks')];
		

		$production_target_arr[$val[csf('production_date')]][$val[csf('floor_name')]][$val[csf('line_name')]][$job_item]['target']=$val[csf('total_target_mst')];
		//  line efficiency
		$line_total_production[$val[csf('production_date')]][$val[csf('floor_name')]][$val[csf('line_name')]]['forcust_prod_min']+=($val[csf('total_target')]*1)*$val[csf('item_smv')];
		$line_total_production[$val[csf('production_date')]][$val[csf('floor_name')]][$val[csf('line_name')]]['produced_min']+=$val[csf('produced_min')];
		$line_total_production[$val[csf('production_date')]][$val[csf('floor_name')]][$val[csf('line_name')]]['efficency_min']+=$val[csf('efficency_min')];
		$line_total_production[$val[csf('production_date')]][$val[csf('floor_name')]][$val[csf('line_name')]]['total_produced']+=$val[csf('total_produced')];
		
		// flore efficiency 
		$floor_total_production[$val[csf('production_date')]][$val[csf('floor_name')]]['efficency_min']+=$val[csf('efficency_min')];
		$floor_total_production[$val[csf('production_date')]][$val[csf('floor_name')]]['produced_min']+=$val[csf('produced_min')];
		//$floor_total_production[$val[csf('floor_name')]]['total_row']+=1;
		
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
	
	foreach($production_data_arr as $date=>$date_wise)
	{
		foreach ($date_wise as $floor_name=>$floor_val) 
		{
			foreach($floor_val as $line_name=>$line_val)
			 {
				 foreach($line_val as $job_details=>$val)
				{
					$floor_total_production[$date][$floor_name]['total_row']+=1;
				}
			 }
		}
		 
	}
	
	
 	//company_id, production_date,  mst_id, location_id, floor_id,  resource_id, buyer_id, order_ids,  gmtitem_id, ponumber, location_name,  floor_name, line_name, buyer_name, file_no, reference_no, garments_item_name,  item_smv, operators, helpers,  man_powers, hourly_target, capacity,  horking_hour, total_target, total_produced,  variance_pceces, efficency_min, produced_min, 
	
	$sql_order_data=sql_select("SELECT b.style_ref_no,a.job_no_mst, a.id,a.po_quantity,a.plan_cut from wo_po_break_down a, wo_po_details_master b where a.job_no_mst=b.job_no and a.status_active=1 and a.is_deleted=0 and  b.status_active=1 and b.is_deleted=0 and a.id in (".implode(",",$all_order_arr).")");
	foreach($sql_order_data as $row)
	{
		//$job_item=$row[csf('job_no_mst')]."*".$row[csf('id')];
		$job_information_data['self_order'][$row[csf('id')]]['style_ref_no']=$row[csf('style_ref_no')];
		$job_information_data['self_order'][$row[csf('id')]]['plan_cut']=$row[csf('plan_cut')];
		$job_information_data['self_order'][$row[csf('id')]]['po_quantity']=$row[csf('po_quantity')];
	}

	$order_sql=sql_select( "SELECT c.id as id, c.job_no_mst, c.order_quantity,c.cust_style_ref from subcon_ord_mst a, subcon_ord_dtls c where a.subcon_job=c.job_no_mst and a.status_active=1 and c.status_active=1 and  c.id in (".implode(",",$all_subcon_order_arr).")  ");
	
	foreach($order_sql as $val)
	{
		//$job_item=$row[csf('job_no_mst')]."*".$row[csf('id')];
		$job_information_data['subcon_order'][$val[csf('id')]]['style_ref_no']=$val[csf('cust_style_ref')];
		$job_information_data['subcon_order'][$val[csf('id')]]['plan_cut']=$val[csf('order_quantity')];
		$job_information_data['subcon_order'][$val[csf('id')]]['po_quantity']=$val[csf('order_quantity')];
	}

	$sql_po_production=sql_select("SELECT a.item_number_id,a.sewing_line,b.job_no_mst,a.production_date,a.po_break_down_id,min( case when a.production_type=4 then a.production_date else null end) as first_input, sum( case when a.production_type=4 then a.production_quantity  else 0 end) as input_qty,sum( case when a.production_type=4 $date_cond  then a.production_quantity else 0 end) as today_input_qty,sum( case when a.production_type=5 then a.production_quantity  else 0 end) as output_qty from pro_garments_production_mst a,wo_po_break_down b where a.po_break_down_id=b.id and a.production_type in (4,5) $date_cond and a.po_break_down_id in (".implode(",",$all_order_arr).") and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group  by a.item_number_id,a.sewing_line,b.job_no_mst,a.production_date,a.po_break_down_id");
	$po_information_data = array();
	foreach($sql_po_production as $row)
	{
		//$job_item=$row[csf('job_no_mst')]."_".$row[csf('garments_item_name')];
		$po_information_data['self_order'][$row[csf('production_date')]][$row[csf('item_number_id')]][$row[csf('po_break_down_id')]][$row[csf('sewing_line')]]['first_input']=$row[csf('first_input')];
		$po_information_data['self_order'][$row[csf('production_date')]][$row[csf('item_number_id')]][$row[csf('po_break_down_id')]][$row[csf('sewing_line')]]['today_input_qty']=$row[csf('today_input_qty')];
		$po_information_data['self_order'][$row[csf('production_date')]][$row[csf('item_number_id')]][$row[csf('po_break_down_id')]][$row[csf('sewing_line')]]['total_input_qty']=$row[csf('input_qty')];
		$po_information_data['self_order'][$row[csf('production_date')]][$row[csf('item_number_id')]][$row[csf('po_break_down_id')]][$row[csf('sewing_line')]]['total_output_qty']=$row[csf('output_qty')];
	}


	$sql_self_pro_date=sql_select("SELECT a.item_number_id,a.po_break_down_id,a.sewing_line,a.production_date from pro_garments_production_mst a where  a.production_type=5 $date_cond and a.po_break_down_id in (".implode(",",$all_order_arr).") and a.status_active=1 and a.is_deleted=0  group  by a.item_number_id,a.po_break_down_id,a.sewing_line,a.production_date");
	$line_date_arr=array();
	foreach ($sql_self_pro_date as $p_val)
	{
		$line_date_arr['self_order'][$p_val[csf('sewing_line')]][$p_val[csf('item_number_id')]][$p_val[csf('po_break_down_id')]][$p_val[csf('production_date')]]=$p_val[csf('production_date')];
	}
	//print_r($line_date_arr);die;

	$sql_sub_production=sql_select("SELECT a.gmts_item_id,a.line_id,a.production_date,a.order_id,min( case when a.production_type=1 then a.production_date else null end) as first_input, sum( case when a.production_type=1 then a.production_qnty  else 0 end) as input_qty,sum( case when a.production_type=1 and $date_cond  then a.production_qnty else 0 end) as today_input_qty,sum( case when a.production_type=2 then a.production_qnty  else 0 end) as output_qty from subcon_gmts_prod_dtls a where  a.production_type in (1,2) $date_cond and a.order_id in (".implode(",",$all_subcon_order_arr).") and a.status_active=1 and a.is_deleted=0  group  by a.gmts_item_id,a.line_id,a.production_date,a.order_id");

	foreach($sql_sub_production as $val)
	{
		//$job_item=$val[csf('job_no_mst')]."_".$val[csf('garments_item_name')];
		$po_information_data['subcon_order'][$val[csf('production_date')]][$val[csf('gmts_item_id')]][$val[csf('order_id')]][$val[csf('line_id')]]['first_input']=$val[csf('first_input')];
		$po_information_data['subcon_order'][$val[csf('production_date')]][$val[csf('gmts_item_id')]][$val[csf('order_id')]][$val[csf('line_id')]]['today_input_qty']=$val[csf('today_input_qty')];
		$po_information_data['subcon_order'][$val[csf('production_date')]][$val[csf('gmts_item_id')]][$val[csf('order_id')]][$val[csf('line_id')]]['total_input_qty']=$val[csf('input_qty')];
		$po_information_data['subcon_order'][$val[csf('production_date')]][$val[csf('gmts_item_id')]][$val[csf('order_id')]][$val[csf('line_id')]]['total_output_qty']=$val[csf('output_qty')];
	}
	// echo "<pre>";print_r($po_information_data);die();63 1729 12

	$sql_sub_pro_date=sql_select("SELECT a.gmts_item_id,a.line_id,a.order_id,a.production_date from subcon_gmts_prod_dtls a where  a.production_type=1 $date_cond and a.order_id in (".implode(",",$all_subcon_order_arr).") and a.status_active=1 and a.is_deleted=0  group  by a.gmts_item_id,a.line_id,a.order_id,a.production_date");

	foreach ($sql_sub_pro_date as $s_val)
	{
		$line_date_arr['subcon_order'][$s_val[csf('line_id')]][$s_val[csf('gmts_item_id')]][$s_val[csf('order_id')]][$s_val[csf('production_date')]]=$s_val[csf('production_date')];

	}
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

        <table id="table_header_1" class="rpt_table" width="2450" cellpadding="0" cellspacing="0" border="1" rules="all">
            <thead>
                <tr height="50">
                	<th width="70">Production Date</th>
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
        <div style="width:2450px; max-height:400px; overflow-y:scroll" id="scroll_body">
            <table class="rpt_table wordBreak" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" id="resource_allocation_tbody">
            	<tbody > 
				<?
                $i=1; 
            	foreach($production_data_arr as $production_date=>$date_wise)
				{
					foreach ($date_wise as $floor_name=>$floor_val) 
					{
						
					
						//echo count($floor_val); 
						 foreach($floor_val as $line_name=>$line_val)
						 {
							 //unset($check_line_arr);
							 $check_line_arr=array();
							 
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
										<td width="70" align="center"><?php echo change_date_format($production_date); ?></td>
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
										$adjust_hour=($line_total_production[$production_date][$floor_name][$line_name]['efficency_min']/60)-($val['man_powers']*$val['horking_hour']);
										$adjust_target_per=($line_total_production[$production_date][$floor_name][$line_name]['efficency_min']/60)/($val['man_powers']*$val['horking_hour']);
										?>
	                                	<td width="60" align="right" rowspan="<?php echo count($line_val); ?>"><p><?php echo $val['man_powers']; ?></p></td>
										<td width="60" align="right" rowspan="<?php echo count($line_val); ?>"><p><?php echo $val['horking_hour']; ?><p/> </td>
										<td width="70" align="right" rowspan="<?php echo count($line_val); ?>"><p><?php echo number_format($adjust_hour,2); ?></p></td>
										<td width="70" align="right" rowspan="<?php echo count($line_val); ?>">	  <?php	echo number_format(($line_total_production[$production_date][$floor_name][$line_name]['produced_min']/60),2);?></td>
	                                    <td width="70" align="right" rowspan="<?php echo count($line_val); ?>">	  <?php	echo number_format(($line_total_production[$production_date][$floor_name][$line_name]['efficency_min']/60),2);?></td>
	                                	<?php 
										
										$total_man_power+=$val['man_powers'];
										$total_working_hour+=$val['horking_hour'];
										$total_adjust_hour+=$adjust_hour;
										$total_produced_min+=$line_total_production[$production_date][$floor_name][$line_name]['produced_min'];
										$total_used_min+=$line_total_production[$production_date][$floor_name][$line_name]['efficency_min'];
										$total_forecust_min+=$line_total_production[$production_date][$floor_name][$line_name]['forcust_prod_min'];
										//line_date_arr
									}
									?>
									<td width="80" align="center"><?php echo change_date_format($po_information_data[$order_string][$production_date][$val['gmtitem_id']][$val['order_ids']][$val['resource_id']]['first_input']); ?></td>

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
									<td width="70" align="right"><?php echo $po_information_data[$order_string][$production_date][$val['gmtitem_id']][$val['order_ids']][$val['resource_id']]['today_input_qty']; ?></td>
	                                <td width="70" align="right"><?php echo $val['total_produced']; ?></td>
									<td width="70" align="right">
									<?php
									//echo date("d-m-Y",strtotime($po_information_data[$order_id]['first_input'])).$txt_date;
									if($po_information_data[$order_string][$production_date][$val['gmtitem_id']][$val['order_ids']][$val['resource_id']]['first_input']=="")
									{
										echo 0;
									}
									else
									{
										echo datediff("d",date("d-m-Y",strtotime($po_information_data[$order_string][$production_date][$val['gmtitem_id']][$val['order_ids']][$val['resource_id']]['first_input'])),str_replace("'","",$txt_date));
									}
									 ?></td>
									<td width="70" align="right"><?php echo count($line_date_arr[$order_string][$val['resource_id']][$val['gmtitem_id']][$val['order_ids']]); ?></td>
									<td width="70" align="right"><?php echo $po_information_data[$order_string][$production_date][$val['gmtitem_id']][$val['order_ids']][$val['resource_id']]['total_input_qty']; ?></td>
									<td width="70" align="right"><?php echo $po_information_data[$order_string][$production_date][$val['gmtitem_id']][$val['order_ids']][$val['resource_id']]['total_output_qty']; ?></td>
									<td width="60" align="right"><?php echo $po_information_data[$order_string][$production_date][$val['gmtitem_id']][$val['order_ids']][$val['resource_id']]['total_input_qty']-$po_information_data[$order_string][$production_date][$val['gmtitem_id']][$val['order_ids']][$val['resource_id']]['total_output_qty']; ?></td>
	                                <td align="right" width="60"><?php //echo $val[csf('capacity')]; ?></td>
	                                 <?php 
									if($flag==1)
									{
									?>
										<td width="60" align="right" rowspan="<?php echo count($line_val); ?>"><?php echo number_format((($line_total_production[$production_date][$floor_name][$line_name]['forcust_prod_min']*100)/$line_total_production[$production_date][$floor_name][$line_name]['efficency_min']),2)."%"; ?></td>
	                                    <td width="60" align="right" rowspan="<?php echo count($line_val); ?>"><?php echo number_format((($line_total_production[$production_date][$floor_name][$line_name]['produced_min']*100)/$line_total_production[$production_date][$floor_name][$line_name]['efficency_min']),2)."%"; ?></td>
	                                    <td width="70" align="right" rowspan="<?php echo count($line_val); ?>"><?php echo number_format((($month_line_total_production[$floor_name][$line_name]['produced_min']*100)/$month_line_total_production[$floor_name][$line_name]['efficency_min']),2)."%"; ?></td>
	                                    
	                                 	
	                                <?php 
										$month_line_total_produced_min+=$month_line_total_production[$floor_name][$line_name]['produced_min'];
										$month_line_total_used_min+=$month_line_total_production[$floor_name][$line_name]['efficency_min'];
										$graph_line_data_arr[$line_name]=number_format((($line_total_production[$production_date][$floor_name][$line_name]['produced_min']*100)/$line_total_production[$production_date][$floor_name][$line_name]['efficency_min']),2,'.', '');
									}
								
									if(!in_array($floor_name,$check_floor_arr))
									{
										$check_floor_arr[]=$floor_name;
											
									?>
										 <td width="70" align="right" rowspan="<?php echo $floor_total_production[$production_date][$floor_name]['total_row']; ?>"><?php echo number_format((($floor_total_production[$production_date][$floor_name]['produced_min']*100)/$floor_total_production[$production_date][$floor_name]['efficency_min']),2)."%"; ?></td>
	                                     <td width="70" align="right" rowspan="<?php echo $floor_total_production[$production_date][$floor_name]['total_row']; ?>"><?php  echo number_format((($floor_month_total_production[$floor_name]['produced_min']*100)/$floor_month_total_production[$floor_name]['efficency_min']),2)."%"; ?></td>
									<?php
										//$graph_line_data_arr[$line_name]=number_format((($line_total_production[$floor_name][$line_name]['produced_min']*100)/$line_total_production[$floor_name][$line_name]['efficency_min']),2);
										$total_forecust_min+=$floor_total_production[$production_date][$floor_name]['forcust_prod_min'];
										$floor_total_produced_min+=$floor_total_production[$production_date][$floor_name]['produced_min'];
										$floor_total_used_min+=$floor_total_production[$production_date][$floor_name]['efficency_min'];
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
								$total_tdoay_input_qty+=$po_information_data[$order_string][$production_date][$val['gmtitem_id']][$val['order_ids']][$val['resource_id']]['today_input_qty'];
								$total_tdoay_output_qty+=$val['total_produced'];
								$grand_input_qty+=$po_information_data[$order_string][$production_date][$val['gmtitem_id']][$val['order_ids']][$val['resource_id']]['total_input_qty'];
								$grand_output_qty+=$po_information_data[$order_string][$production_date][$val['gmtitem_id']][$val['order_ids']][$val['resource_id']]['total_output_qty'];
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
				}
    		?>
   				</tbody>
                <tfoot>
                   <tr>
                        <th width="450" align="right" colspan="6">Total</th>
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
   
	<? 
	// foreach (glob("$user_id*.xls") as $filename) 
	// {
	// 	if( @filemtime($filename) < (time()-$seconds_old) )
	// 	@unlink($filename);
	// }
	// //---------end------------//
	// $name=time();
	// $filename=$user_id."_".$name.".xls";
	// $create_new_doc = fopen($filename,'w');
	// $is_created = fwrite($create_new_doc,ob_get_contents());
	// echo "$total_data####$filename";   
	// exit();      
}

/**
 * Developer By Mr.  
 * Button Show2
 * Date 21-08-2023
 */
//Report Generate 2 Starts here

if($action=="report_generate2"){
	$process = array( &$_POST );

	$floor_library=return_library_array( "select id,floor_name from lib_prod_floor", "id", "floor_name"  ); 
	$line_library=return_library_array( "select id,line_name from lib_sewing_line ", "id", "line_name"  );  
	$prod_reso_line_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number'); 
	$lineArr = return_library_array("select id,line_name from lib_sewing_line order by id","id","line_name"); 
    // print_r($prod_reso_line_arr);
	extract(check_magic_quote_gpc( $process )); 
	$company_id = str_replace("'","",$cbo_company_id);
	$cbo_location = str_replace("'","",$cbo_location_id);
	$cbo_floor = str_replace("'","",$cbo_floor_id);
	$cbo_line = str_replace("'","",$hidden_line_id);
	$form_date = str_replace("'","",$txt_date_from);
	$to_date = str_replace("'","",$txt_date_to);

	$sql_cond="";
	$sql_cond .= ($company_id!=0) ? " and a.company_id=$company_id" : "";
	$sql_cond .= ($cbo_location!=0) ? " and a.location_id=$cbo_location" : "";
	$sql_cond .= ($cbo_floor!=0) ? " and a.floor_id=$cbo_floor" : "";
	$sql_cond .= ($cbo_line!=0) ? " and a.id  in ($cbo_line)" : "";
	$sql_cond .=  ($form_date && $to_date) ?" and c.pr_date between '".change_date_format($form_date,'dd-mm-yyyy','-',1)."' and '".change_date_format($to_date,'dd-mm-yyyy','-',1)."'"  : ""; 
	//$sql_cond4 .= ($company_id!=0) ? " and b.company_id=$company_id" : "";
	$sql_cond4 .=  ($form_date && $to_date) ?" and a.pr_date between '".change_date_format($form_date,'dd-mm-yyyy','-',1)."' and '".change_date_format($to_date,'dd-mm-yyyy','-',1)."'"  : ""; 
	$sql_cond2="";
	$sql_cond2 .= ($company_id!=0) ? " and b.company_id=$company_id" : "";
	$sql_cond2 .= ($cbo_location!=0) ? " and b.location=$cbo_location" : "";
	$sql_cond2 .= ($cbo_floor!=0) ? " and b.floor_id=$cbo_floor" : "";
	$sql_cond2 .= ($cbo_line!=0) ? " and b.sewing_line in ($cbo_line)" : "";
	$dateCond = ($form_date && $to_date) ?" b.production_date between '".change_date_format($form_date,'dd-mm-yyyy','-',1)."' and '".change_date_format($to_date,'dd-mm-yyyy','-',1)."'"  : ""; 
     $text_to_date = change_date_format($to_date,'dd-mm-yyyy','-',1);
	 $sql_cond_3="";
	 $sql_company_3 = ($company_id!=0) ? " and a.comapny_id=$company_id" : "";
	 $sql_cond_3 = ($form_date && $to_date) ?" and b.date_calc between '".change_date_format($form_date,'dd-mm-yyyy','-',1)."' and '".change_date_format($to_date,'dd-mm-yyyy','-',1)."'"  : ""; 
	 
      $SqlData = "SELECT a.id as line_id,a.company_id,a.location_id,a.floor_id, a.line_number,b.id, b.mst_id,b.target_per_hour,c.pr_date,b.working_hour,b.target_efficiency , b.man_power , b.capacity,d.sewing_line_serial
	  FROM
	    prod_resource_mst a ,
	    prod_resource_dtls_mast b,
		prod_resource_dtls c ,
		lib_sewing_line d
	  WHERE 
	  a.id=b.mst_id 
	  AND c.mst_id = a.id
	  and b.ID=c.MAST_DTL_ID
	 AND a.line_number = d.id
	  $sql_cond  order by d.sewing_line_serial";
	//  echo $SqlData ;die;
	 $line_cond = array();
	  $prod_eff_arr = array();
	  $per_day = array();
	  foreach(sql_select($SqlData) as $row){
		
		
		// echo "<pre>";
		// print_r($qi);die;

		$prod_eff_arr[$row['FLOOR_ID']][$row['LINE_ID']][strtotime($row['PR_DATE'])]['CAPACITY']=$row['CAPACITY']; 
		$prod_eff_arr[$row['FLOOR_ID']][$row['LINE_ID']][strtotime($row['PR_DATE'])]['TARGET_PER_HOUR']=$row['TARGET_PER_HOUR']; 
		$prod_eff_arr[$row['FLOOR_ID']][$row['LINE_ID']][strtotime($row['PR_DATE'])]['WORKING_HOUR'] =$row['WORKING_HOUR']; 
		$prod_eff_arr[$row['FLOOR_ID']][$row['LINE_ID']][strtotime($row['PR_DATE'])]['LINE_NUMBER']=$row['LINE_NUMBER']; 
		$prod_eff_arr[$row['FLOOR_ID']][$row['LINE_ID']][strtotime($row['PR_DATE'])]['TARGET_EFFICIENCY']=$row['TARGET_EFFICIENCY']; 
		// $prod_eff_arr[$row['FLOOR_ID']][$row['LINE_ID']][strtotime($row['PR_DATE'])]['NUMBER_OF_EMP']=$row['NUMBER_OF_EMP']; 
		// $prod_eff_arr[$row['FLOOR_ID']][$row['LINE_ID']][strtotime($row['PR_DATE'])]['ADJUST_HOUR']=$row['ADJUST_HOUR']; 
		// $prod_eff_arr[$row['FLOOR_ID']][$row['LINE_ID']][strtotime($row['PR_DATE'])]['OT_EMP_BRK_DWN']=$qi[3];
		$prod_eff_arr[$row['FLOOR_ID']][$row['LINE_ID']][strtotime($row['PR_DATE'])]['MAN_POWER']=$row['MAN_POWER']; 
	    $per_day[$row['FLOOR_ID']][$row['LINE_ID']]++;
		$line_cond[$row['LINE_ID']]  = $row['LINE_ID'];

	  }
	//   echo "<pre>";
	//  print_r($prod_eff_arr);die;
	  $line_cond_id = implode(",",$line_cond);
	
	  $sql_qi=sql_select("SELECT a.mst_id , a.number_of_emp, a.adjust_hour,a.ot_emp_brk_dwn,a.PR_DATE  FROM prod_resource_smv_adj a   where a.mst_id  in ($line_cond_id) and STATUS_ACTIVE=1 and  is_deleted=0 $sql_cond4");
	//  echo   $sql_qi;die;
	 $qc_eff_arr=array();
	  foreach($sql_qi as $r)
	  {
		 $qi=explode('__',$r['OT_EMP_BRK_DWN']);
		
		if( $qi[3]*1>0)
		{
			$qc_eff_arr[$r['MST_ID']][strtotime($r['PR_DATE'])]['NUMBER_OF_EMP'].=$r['NUMBER_OF_EMP'].","; 
			$qc_eff_arr[$r['MST_ID']][strtotime($r['PR_DATE'])]['ADJUST_HOUR']=$r['ADJUST_HOUR'].","; 
			$qc_eff_arr[$r['MST_ID']][strtotime($r['PR_DATE'])]['OT_EMP_BRK_DWN'].= $qi[3].",";
		} 
	  }
	//   echo $qi;
	//   echo "<pre>";
	// 	print_r($qc_eff_arr);die;

	
	$SqlInfo = "SELECT a.production_qnty,
	b.company_id,
	b.item_number_id,
	b.production_date,
	b.production_type,
	b.floor_id,
	b.po_break_down_id,
	b.sewing_line,
	a.color_size_break_down_id,
	d.style_ref_no,
	b.production_hour,
	TO_CHAR(b.production_hour,'HH24')  as  prod_hour 
     FROM pro_garments_production_dtls a,
     pro_garments_production_mst b , 
     wo_po_color_size_breakdown c,
     wo_po_details_master d
    WHERE a.mst_id = b.id
	and a.color_size_break_down_id = c.id
    and c.job_id = d.id
    AND b.production_type in(5) 
	AND a.status_active=1
	AND a.is_deleted=0
	AND b.status_active=1 
	AND b.is_deleted=0
	AND c.status_active=1
	AND c.is_deleted=0
	AND d.status_active=1
	AND d.is_deleted=0
	AND $dateCond $sql_cond2 ";
	//	echo $SqlInfo;die;
	$poIdArr =array();
      $sql_ex_info = sql_select($SqlInfo);
      $prod_data_arr = array();  
	  $all_style_arr= array();
	  $lc_com_array = array();
	  $count_of_style = array();
	  $hour = array();
	  $style_wise_po_arr = array();
	  $running_hour = array();
	  foreach($sql_ex_info as $rowData)
	  {
		$style_wise_po_arr[$rowData[csf('style_ref_no')]][$rowData[csf('po_break_down_id')]] = $rowData[csf('po_break_down_id')];
		$all_style_arr[$rowData['STYLE_REF_NO']] = $rowData['STYLE_REF_NO'];
		$lc_com_array[$rowData[csf('company_id')]] = $rowData[csf('company_id')];
		$poIdArr[$rowData[csf('po_break_down_id')]] = $rowData[csf('po_break_down_id')];
		$prod_data_arr[$rowData['FLOOR_ID']][$rowData['SEWING_LINE']][strtotime($rowData['PRODUCTION_DATE'])][$rowData['PO_BREAK_DOWN_ID']][$rowData['ITEM_NUMBER_ID']]['PrQty'] += $rowData['PRODUCTION_QNTY']; 
		$prod_data_arr[$rowData['FLOOR_ID']][$rowData['SEWING_LINE']][strtotime($rowData['PRODUCTION_DATE'])][$rowData['PO_BREAK_DOWN_ID']][$rowData['ITEM_NUMBER_ID']]['PROD_HOUR'] ++; 

		$prod_data_arr[$rowData['FLOOR_ID']][$rowData['SEWING_LINE']][strtotime($rowData['PRODUCTION_DATE'])][$rowData['PO_BREAK_DOWN_ID']][$rowData['ITEM_NUMBER_ID']]['STYLE_REF_NO'] = $rowData['STYLE_REF_NO'] ; 

		$count_of_style[$rowData['FLOOR_ID']][$rowData['SEWING_LINE']] ++ ;

		$running_hour[$rowData['FLOOR_ID']][$rowData['SEWING_LINE']][strtotime($rowData['PRODUCTION_DATE'])][$rowData['PO_BREAK_DOWN_ID']][$rowData['ITEM_NUMBER_ID']][$rowData['PROD_HOUR']] = $rowData['PROD_HOUR'] ;

	 }
		// echo "<pre>";
		//  print_r($prod_data_arr);die;

		//operation Bulletin starts here
	$style_cond = where_con_using_array($all_style_arr,1,"a.style_ref");
	$sqlgsd="SELECT a.PROCESS_ID,a.style_ref,a.gmts_item_id,b.id, b.mst_id, b.row_sequence_no, b.body_part_id, b.lib_sewing_id, b.resource_gsd, b.attachment_id, b.efficiency, b.total_smv, b.target_on_full_perc from PPL_GSD_ENTRY_MST a,ppl_gsd_entry_dtls b where a.id=b.mst_id $style_cond and a.bulletin_type=4 and b.is_deleted=0 order by b.row_sequence_no asc";
	// echo $sqlgsd;die;
	$gsd_res=sql_select($sqlgsd);
	$mst_id_arr = array();
	foreach($gsd_res as $row)
	{
		$mst_id_arr[$row['MST_ID']] = $row['MST_ID'];
	}
	$mst_id_cond = where_con_using_array($mst_id_arr,0,"a.gsd_mst_id");
	// ======================================================================
	$balanceDataArray=array();
	$blData=sql_select("SELECT a.id, gsd_dtls_id, smv, layout_mp,a.EFFICIENCY from ppl_balancing_mst_entry a, ppl_balancing_dtls_entry b where a.id=b.mst_id and a.balancing_page=1 $mst_id_cond and a.is_deleted=0 and b.is_deleted=0");
	foreach($blData as $row)
	{
		$balanceDataArray[$row[csf('gsd_dtls_id')]]['smv']=$row[csf('smv')];
		$balanceDataArray[$row[csf('gsd_dtls_id')]]['layout_mp']=$row[csf('layout_mp')];
		$balanceDataArray[$row[csf('gsd_dtls_id')]]['efficiency']=$row[csf('efficiency')];
	}
	
	$gsd_data_array = array();

	foreach($gsd_res as $slectResult)
	{
		if($balanceDataArray[$slectResult[csf('id')]]['smv']>0)	
		{
			$smv=$balanceDataArray[$slectResult[csf('id')]]['smv'];
		}
		else
		{
			$smv=$slectResult[csf('total_smv')];
		}
		
		$rescId=$slectResult[csf('resource_gsd')];
		$layOut=$balanceDataArray[$slectResult[csf('id')]]['layout_mp'];
		$efficiency = $balanceDataArray[$slectResult[csf('id')]]['efficiency'];
		
		if($rescId==40 || $rescId==41 || $rescId==43 || $rescId==44 || $rescId==48 || $rescId==68 || $rescId==69 || $rescId==70 || $rescId==147)
		{
			$helperSmv=$helperSmv+$smv;
			$helperMp=$helperMp+$layOut;
		}
		else if($rescId==53)
		{
			$fIMSmv=$fIMSmv+$smv;
			$fImMp=$fImMp+$layOut;
		}
		else if($rescId==54)
		{
			$fQISmv=$fQISmv+$smv;
			$fQiMp=$fQiMp+$layOut;
		}
		else if($rescId==55)
		{
			$polyHelperSmv=$polyHelperSmv+$smv;
			$polyHelperMp=$polyHelperMp+$layOut;
		}
		else if($rescId==56)
		{
			$pkSmv=$pkSmv+$smv;
			$pkMp=$pkMp+$layOut;
		}
		else if($rescId==90)
		{
			$htSmv=$htSmv+$smv;
			$htMp=$htMp+$layOut;
		}
		else if($rescId==176)
		{
			$imSmv=$imSmv+$smv;
			$imMp=$imMp+$layOut;
		}
		else
		{
			$machineSmv=$machineSmv+$smv;
			$machineMp=$machineMp+$layOut;
			
			$mpSumm[$rescId]+= $layOut;
		}
		$i++;
		$totMpSumm = $helperMp + $machineMp + $sQiMp + $fImMp + $fQiMp + $polyHelperMp + $pkMp + $htMp + $imMp;
		$totHpSumm = $helperMp + $sQiMp + $fImMp + $fQiMp + $polyHelperMp + $pkMp + $htMp + $imMp;
		// echo $helperMp."<br>";
		
		$gsd_data_array[$slectResult['STYLE_REF']][$slectResult['GMTS_ITEM_ID']]['operator'] = $machineMp;
		$gsd_data_array[$slectResult['STYLE_REF']][$slectResult['GMTS_ITEM_ID']]['sew_helper'] = $totHpSumm;
		$gsd_data_array[$slectResult['STYLE_REF']][$slectResult['GMTS_ITEM_ID']]['plan_man'] = $totMpSumm;
		$gsd_data_array[$slectResult['STYLE_REF']][$slectResult['GMTS_ITEM_ID']]['efficiency'] = $efficiency;
		$gsd_data_array[$slectResult['STYLE_REF']][$slectResult['GMTS_ITEM_ID']]['smv'] += $smv;
		
	}
	
	//    echo "<pre>";
	//    print_r($gsd_data_array);
        
		// operation Bulletin Ends here 
       /*===================================================================================== /
			/										smv sorce 										/
			/===================================================================================== */
			$lc_com_ids = implode(",",$lc_com_array);
			$poIds_cond = where_con_using_array($poIdArr,0,"b.id");
			$smv_source=return_field_value("smv_source","variable_settings_production","company_name in ($lc_com_ids) and variable_list=25 and   status_active=1 and is_deleted=0");
			//echo $smv_source;
			$item_smv_array=array();
			//if($smv_source=="") $smv_source=0; else $smv_source=$smv_source;
			if($smv_source==3) // from gsd enrty
			{
				$style_cond = where_con_using_array($all_style_arr,1,"a.style_ref");
				$sql_item="SELECT a.id,a.APPLICABLE_PERIOD,a.BULLETIN_TYPE,A.TOTAL_SMV,A.STYLE_REF,a.GMTS_ITEM_ID  from PPL_GSD_ENTRY_MST a where a.APPLICABLE_PERIOD <= '$text_to_date' and A.IS_DELETED=0 and A.STATUS_ACTIVE=1 $style_cond  group by a.id,a.APPLICABLE_PERIOD,a.BULLETIN_TYPE,A.TOTAL_SMV,A.STYLE_REF,a.GMTS_ITEM_ID ORDER BY a.GMTS_ITEM_ID ,a.APPLICABLE_PERIOD  DESC , a.id DESC";
				$gsdSqlResult=sql_select($sql_item);
				//echo $sql_item;die;// Has No Data

				foreach($gsdSqlResult as $rows)
				{
					
					foreach($style_wise_po_arr[$rows['STYLE_REF']] as $po_id)
					{
						
						if($item_smv_array[$po_id][$rows['GMTS_ITEM_ID']]=='')
						{
							$item_smv_array[$po_id][$rows['GMTS_ITEM_ID']]=$rows['TOTAL_SMV'];
							
						}
					}
				}
			}
			else
			{
				$sql_item="SELECT b.id, a.set_break_down, c.gmts_item_id, c.set_item_ratio, c.smv_pcs, c.smv_pcs_precost,c.smv_set from wo_po_details_master a,wo_po_break_down b, wo_po_details_mas_set_details c where b.job_id=a.id and b.job_id=c.job_id and a.company_name in($lc_com_ids) $poIds_cond and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1";
				// echo $sql_item;  not resutl for else cond.
				$resultItem=sql_select($sql_item);

				foreach($resultItem as $itemData)
				{
					if($smv_source==1)
					{
						$item_smv_array[$itemData[csf('id')]][$itemData[csf('gmts_item_id')]]=$itemData[csf('smv_pcs')];
					}
					if($smv_source==2)
					{
						$item_smv_array[$itemData[csf('id')]][$itemData[csf('gmts_item_id')]]=$itemData[csf('smv_pcs_precost')];
					}
				}
			}
			// echo "<pre>";print_r($item_smv_array);die;
		$rowspan_arr = array();
		$rowspan_arr2 = array();
		$data_array = array();// floor - line  
		$working_hour=0;
		$item_smv=0;
		$prod_hour="";
		// $prod_eff_arr[$row['FLOOR_ID']][$row['LINE_ID']][strtotime($row['PR_DATE'])]['MAN_POWER']=$row['MAN_POWER']; 
		foreach($prod_eff_arr as $floor_id => $floor_val)
		{
			
			foreach($floor_val as $sewing_line=>$sewing_val)
			{
				foreach($sewing_val as $production_date=>$v)
				{
					
					$data_array[$floor_id][$sewing_line]['LINE_NUMBER'] =  $v['LINE_NUMBER'];
					$data_array[$floor_id][$sewing_line]['TARGET_PER_HOUR'] +=  $v['TARGET_PER_HOUR'];
					$data_array[$floor_id][$sewing_line]['CAPACITY'] +=  $v['CAPACITY'];
					$data_array[$floor_id][$sewing_line]['WORKING_HOUR'] +=  $v['WORKING_HOUR'];
					$data_array[$floor_id][$sewing_line]['TARGET_EFFICIENCY'] +=  $v['TARGET_EFFICIENCY'];		
					$data_array[$floor_id][$sewing_line]['MAN_POWER'] +=  $v['MAN_POWER'];

					$data_array[$floor_id][$sewing_line]['NUMBER_OF_EMP']=rtrim($qc_eff_arr[$sewing_line][$production_date]['NUMBER_OF_EMP'],",");
					$data_array[$floor_id][$sewing_line]['ADJUST_HOUR']=rtrim($qc_eff_arr[$sewing_line][$production_date]['ADJUST_HOUR'],",");
					$data_array[$floor_id][$sewing_line]['OT_EMP_BRK_DWN'] =rtrim($qc_eff_arr[$sewing_line][$production_date]['OT_EMP_BRK_DWN'],",");
					;
					foreach($prod_data_arr[$floor_id][$sewing_line][$production_date] as $po_id=>$po_val)
					{
						foreach($po_val as $item_id=>$rowsV)
						{
							//echo "item = $item_id <br>" ;
							// $item_smv += $item_smv_array[$po_id][$item_id];
							//echo $sewing_line."=".$item_smv_array[$po_id][$item_id] ."*".$po_id."=".$item_id." <br>" ;
							$data_array[$floor_id][$sewing_line]['smv'] += $item_smv_array[$po_id][$item_id];
							$data_array[$floor_id][$sewing_line]['style_count'] ++;
							$data_array[$floor_id][$sewing_line]['achive_qty'] += $rowsV['PrQty'];
							 $data_array[$floor_id][$sewing_line]['PROD_HOUR'] = $rowsV['PROD_HOUR'];
							 $data_array[$floor_id][$sewing_line]['STYLE_REF_NO'] = $rowsV['STYLE_REF_NO'];
							 $running_h_arr=  $running_hour[$floor_id][$sewing_line][$production_date][$po_id][$item_id];
							 $running_hour_val = count($running_h_arr);
							 $data_array[$floor_id][$sewing_line]['RUNNING_HOUR'] =  $running_hour_val;
                             $data_array[$floor_id][$sewing_line]['ACT_SMV'] +=  $gsd_data_array[$rowsV['STYLE_REF_NO']][$item_id]['smv'];
							 $data_array[$floor_id][$sewing_line]['my_achive_qty'] += $rowsV['PrQty'] * $gsd_data_array[$rowsV['STYLE_REF_NO']][$item_id]['smv'];

						}
					}
				}
				$rowspan_arr[$floor_id]++;// for rowspan
				// for rowspan
			}
			
		}
		
		// echo "<pre>";
		// print_r($data_array);
		
	?>

<!--       
	<style>
		th{
			padding:6px;
			font-size:1rem !important;
		}
		td{
			padding:4px !important;
			font-size:0.8rem;
			
		}
	</style> -->
  <div style="width:1320px;margin:auto">
  <fieldset>
	<h1 style="text-align:center;margin-top:10px; padding:5px;font-size:1.7rem">Efficiency Report</h1>
	<h1 style="text-align:center;margin-top:4px; font-size:1.2rem">From  : <? echo $form_date  ?> To  <? echo $to_date  ?></h1> <br>
	
		<div style="width:1320px;height:400px; overflow-y:scroll">
		<table width="1300" style="padding:10px;" cellspacing="0" border="1" class="rpt_table" rules="all" id="table_header_1">
        <thead  style="font-weight: bold; background: #dddddd;position:sticky;top:0">
            <th>Floor Name</th>
            <th width="110">Line</th>
            <th>Target</th>
            <th>Achieved</th>
            <th>W/Hrs</th>
            <th width="110">Target. Efft.</th>
            <th title="produce_minute/avl_min">Ach. Eff</th>
            <th title="targer_eff-acv_effc">Effi. Diff%</th>
            <th width="110">Available Miniute</th>
            <th title="style_wise_production * style_wise_ob SMV" width="110">Product Minute</th>
            <th  width="110">AVG. SMV</th>
        </thead>
        <tbody>
			<? 
			$b=0 ;
			$total_loss = 0;
			$total_factory_avg_sum = 0 ;
			$total_eff_diff_last=0;
			$total_acv_eff_las=0;
			$total_target_eff_last =0;
			foreach($data_array as $floor_id=>$floor_val)
			{
				$total_target=$total_achived=$total_working=$total_eff=$total_avl_min=$total_prd_min=$total_acv_eff=$total_eff_dif=$total_Avg_smv= 0;
				$f=0;
				
				
				foreach($floor_val as $sewing_line=>$row)
				{
					
						$per_day_count = $per_day[$floor_id][$sewing_line] ;
						$prod_hour = $row['RUNNING_HOUR'];
						 //echo "***". $row['my_achive_qty'];
						$target_eff = $row['TARGET_EFFICIENCY'] ;
						$item_smv =  $row['ACT_SMV'];
						$new += $item_smv ;
						$style_count_smv = $data_array[$floor_id][$sewing_line]['style_count'];
						//echo $style_count_smv ;
						
						$number_emp=$row['NUMBER_OF_EMP'];
						$adjust_hour=$row['ADJUST_HOUR'];
						$qi_min=$row['OT_EMP_BRK_DWN'];
						// echo $qi_min."<br>";
						
						$total_ot_min=(($number_emp*$adjust_hour)*60);
						// echo $total_ot_min;
						$qc_ot_min=((($qi_min)*$adjust_hour)*60);
						// echo $qc_ot_min;
						$avl_min = (($row['MAN_POWER'] * $prod_hour * 60) + ($total_ot_min-$qc_ot_min));
						// echo $avl_min;

						$prod_minute = $row['my_achive_qty'] ;
						$acv_eff = $avl_min ? $prod_minute / $avl_min  : 0;
						$eff_diff = $acv_eff ? $target_eff-$acv_eff :0;
						$avg_smv =$item_smv ? $item_smv / $style_count_smv :0 ;
						
						$line_resource_mst_arr = explode(",",$prod_reso_line_arr[$sewing_line]);
						$line_name = "";
						foreach($line_resource_mst_arr as $resource_id)
						{
							$line_name .= $lineArr[$resource_id].", ";
						}
						$line_name = chop($line_name," , ");
                        $b++;
						if ($b%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
					
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_3nd<? echo $b; ?>','<? echo $bgcolor; ?>')" id="tr_3nd<? echo $b; ?>" style="height:20px">
							<? if($f==0) {?>
								<td rowspan="<? echo $rowspan_arr[$floor_id]; ?>" style="font-size:0.9rem;" valign="middle" align="center"><?  echo $floor_library[$floor_id]  ?></td>
							<? $f++ ;}?>
							<td title="<?=$sewing_line;?>"> <? echo $line_library[$row['LINE_NUMBER']] ;   ?></td>
							<td align="right"><? echo $row['CAPACITY'] ?></td>
							<td align="right"><? echo $row['achive_qty'] ; ?></td>
							<td align="right"><?  echo number_format($row['WORKING_HOUR'],2) ?></td>
							<td align="right"><? echo number_format($target_eff,2) ;  ?></td>
							<td align="right"><? echo   number_format($acv_eff,2) ?></td>
							<td align="right"><? echo number_format($eff_diff,2) ?></td>
							<td align="right"><? echo number_format($avl_min,2) ; ?></td>
							<td align="right"><? echo number_format($prod_minute,2) ?></td>
							<td align="right"><? echo number_format($avg_smv,2) ?></td>
						</tr>
											
						<?
							$total_target += $row['CAPACITY'];
							$total_achived += $row['achive_qty'];
							$total_working += $row['WORKING_HOUR'];
							$total_eff += $row['TARGET_EFFICIENCY'];
							$total_avl_min += $avl_min;
							$total_prd_min += $prod_minute;
							$total_acv_eff +=$acv_eff;
							$total_eff_dif += $eff_diff; 
							$total_Avg_smv += $avg_smv; 

							
						}
						
				   
				?>
                       <tr style="font-weight: bold; background: #dddddd;">
						<td colspan="2" align="center" ><?echo $floor_library[$floor_id] ?>  Total</td>				
						<td align="right"><? echo $total_target ?></td>
						<td align="right"><?echo $total_achived ?></td>
						<td align="right"><? echo number_format($total_working,2) ?></td>
						<td align="right"><? echo number_format($total_eff,2) ?></td>
						<td align="right"><? echo number_format($total_acv_eff,2) ?></td>
						<td align="right"><? echo number_format($total_eff_dif,2) ?></td>
						<td align="right"><? echo number_format($total_avl_min,2) ?></td>
						<td align="right"><? echo number_format($total_prd_min,2) ;?></td>
						<td align="right"><? echo number_format($total_Avg_smv,2) ?></td>
					</tr>
				<?
					$final_total_target += $total_target;
					$final_total_acv += $total_achived ;
					$final_total_wrh += $total_working ;
					$final_total_eff += $total_eff;
					$final_acv_eff += $total_acv_eff ;
					$final_total_eff_diff += $total_eff_dif;
					$final_total_avl_min += $total_avl_min;
					$final_total_prod_min += $total_prd_min ;
					$final_total_smv_avg += $total_Avg_smv;
                    //calculate summery parts
					$total_loss =  $final_total_target - $final_total_acv   ;
					$total_calcAcive = $final_total_acv ;
					//total factory of sum 
					$total_factory_avg_sum =$final_total_prod_min ? $final_total_prod_min / $final_total_acv :0 ;
					$total_acv_eff_last += $total_acv_eff;
					$total_eff_diff_last += $total_eff_dif;
					$total_target_eff_last += $total_eff ;
			}  
			
			?>
               <tr style="font-size:1.1rem !important;font-weight:bold;" align="right">
				  <td  align="right" colspan="2"  > Total </td>
				 
				  <td align="right"> <? echo number_format($final_total_target,2)  ?> </td>
				  <td align="right">  <? echo number_format($final_total_acv,2)  ?>  </td>
				  <td align="right">  <? echo number_format($final_total_wrh,2)  ?>  </td>
				  <td align="right">  <? echo number_format($final_total_eff,2)  ?>  </td>
				  <td align="right">  <? echo number_format($final_acv_eff,2)  ?>  </td>
				  <td align="right">  <? echo number_format($final_total_eff_diff,2)  ?>  </td>
				  <td align="right">  <? echo number_format($final_total_avl_min,2)  ?> </td>
				  <td align="right">  <? echo number_format($final_total_prod_min,2)  ?> </td>
				  <td align="right">  <? echo number_format($final_total_smv_avg,2)  ?> </td>
			   </tr>
			<?
			
			?>

        </tbody>  
        <tfoot>

        </tfoot>
    </table>    
	</div>
	</fieldset>
	</div>
     <br><br>
	 <br><br>
    <!--Summary Parts starts here-->
			<div class="main-div" style="width: 1000px;margin-left:110px">
			<div class="left-sidebar-div"  style="width: 500px">
				<h3 style="font-size:1.5rem !important">Summary Calculation Details</h3>
				<?  
					$sqlDay = "select a.comapny_id, a.year,a.location_id ,b.day_status, b.date_calc from LIB_CAPACITY_CALC_MST a ,
					LIB_CAPACITY_CALC_DTLS b
					where 
					a.id = b.mst_id 
					AND b.day_status = 1
					$sql_company_3
					$sql_cond_3";
					$no_of_day=0;
					$sql_exc = sql_select($sqlDay);
					$no_work_day_arr = array();
					foreach($sql_exc as $item){
						$no_of_day += $no_work_day_arr[$item['DATE_CALC']]++;
						
					}
					$per_day_production = $total_calcAcive ? $total_calcAcive / $no_of_day : 0;
					
				?>
				
                <table width="400" style="padding:10px;" cellspacing="0" border="1" class="rpt_table" rules="all" id="table_header_1">
					<thead>
                        <tr >
						  <td title="total_target - total_acv" style="font-size:1.1rem !important ;font-weight:bold">Loss </td>
						  <td style="font-size:1.1rem !important ;font-weight:bold"><? echo $total_loss ?></td>
						</tr>
						<tr>
						  <td title="total_acv / number of working days" style="font-size:1.1rem !important ;font-weight:bold">Per Day Production</td>
						  <td style="font-size:1.1rem !important ;font-weight:bold"> <? echo  $per_day_production ;  ?> </td>
						</tr>
						<tr>
						  <td style="font-size:1.1rem !important ;font-weight:bold">No. Of Working Day</td>
						  <td style="font-size:1.1rem !important ;font-weight:bold"> <? echo $no_of_day ?> </td>
						</tr>
					</thead>
					<tbody>

					</tbody>
				</table>
			</div>	
			<div class="summury-right-div">
				<table  style="padding:10px;width:50%;margin-top:20px" cellspacing="0" border="1" class="rpt_table" rules="all" id="table_header_1">
					<thead style="font-size:1.1rem !important ;font-weight:bold">
						<tr>
						  <td>SUMMARY</td>
						  <? 
							foreach($data_array as $floor_id=>$floor_val)
							{
								
								foreach($floor_val as $sewing_line=>$row) 
								{ ?>
                                    <td><?echo $floor_library[$floor_id] ?></td>
								<? }
							}
						  ?>
						</tr>
						<tr>
							<td>SAM Avg.</td>
							<? 
							$i = 0;
							
							foreach($data_array as $floor_id=>$floor_val)
							{
								
								foreach($floor_val as $sewing_line=>$row) 
								{ 
									
									$i += 1;

									// $per_day_count = $per_day[$floor_id][$sewing_line] ;

									// 	$prod_minute = $row['PrQty'] * $item_smv ;

										//$avg_smv = $item_smv / $per_day_count ;
										$style_count_smv = $data_array[$floor_id][$sewing_line]['style_count'];
										$item_smv =  $row['ACT_SMV'];
										$avg_smv = $item_smv ?  $item_smv / $style_count_smv :0 ;
										
									?>
                                    <td><?echo $avg_smv ?></td>
								<? }
							}
						  ?>
						</tr>
						<tr>
							<td title="total_produce_minute/total_acv_minute">Factory Avg. SAM</td>
							
							<td colspan="<?= $i?>" align="center">
                               <? echo $total_factory_avg_sum; ?>
							</td>
						</tr>
						<tr>
							<td>Change Over </td>
							<? 
							$i = 0;
							$total_style =0 ;
							foreach($data_array as $floor_id=>$floor_val)
							{
								
								foreach($floor_val as $sewing_line=>$row) 
								{ 
									
									$i += 1;

									$per_day_count = $per_day[$floor_id][$sewing_line] ;

										$prod_minute = $row['PrQty'] * $item_smv ;

										$avg_smv = $item_smv / $per_day_count ;
										$total_style += $count_of_style[$floor_id][$sewing_line];
										
									?>
                                    <td><?echo $total_style -1 ?></td>
								<? }
							}
						  ?>
						</tr>
					</thead>
				</table>
                <br><br><br>
				<table style="padding:10px;width:40%" cellspacing="0" border="1" class="rpt_table" rules="all" id="table_header_1">
					<thead style="font-size:1.1rem !important ;font-weight:bold">
						<tr>
							<td style="font-size:1.2rem !important ;font-weight:bold">Achieved Eff%</td>
							<td style="font-size:1.2rem !important ;font-weight:bold"><?  echo number_format($total_acv_eff_last,2) ; ?></td>
						</tr>
						<tr>
							<td title="total_effc_diff" style="font-size:1.2rem !important ;font-weight:bold">Loss</td>
							<td style="font-size:1.2rem !important ;font-weight:bold"><?  echo number_format($total_eff_diff_last,2) ; ?></td>
						</tr>
						<tr>
							<td title="total_target_eff." style="font-size:1.2rem !important ;font-weight:bold">We Should Acvd: </td>
							<td style="font-size:1.2rem !important ;font-weight:bold"><?  echo number_format($total_target_eff_last,2) ; ?></td>
						</tr>
					</thead>
				</table>
			</div>
			</div>
			

	 <!--Summary Parts ends  here-->



 <?
// $html = ob_get_contents() ;
// ob_clean();
// foreach (glob("$user_id*.xls") as $filename) 
// {
// 	//if( @filemtime($filename) < (time()-$seconds_old) )
// 	@unlink($filename);
// }
// //---------end------------//
// $name=time();
// $filename=$user_id."_".$name.".xls";
// $create_new_doc = fopen($filename,'w');
// $is_created = fwrite($create_new_doc,$html);
// echo "$html####$filename####1####$type";   
// exit();  
}





// Report generate 2 Ends here 



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