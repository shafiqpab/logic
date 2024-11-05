<?
header('Content-type:text/html; charset=utf-8');
session_start();
require_once('../../../includes/common.php');
require_once('../../../includes/class4/class.conditions.php');
require_once('../../../includes/class4/class.reports.php');
require_once('../../../includes/class4/class.fabrics.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_id", 140, "select id,location_name from lib_location where status_active=1 and is_deleted=0 and company_id='$data' 
	order by location_name","id,location_name", 1, "-- Select Location --", $selected, "",0 ); 
	//get_php_form_data( this.value, 'eval_multi_select', 'requires/daily_line_wise_fabric_req_report_controller' );
	//load_drop_down( 'requires/daily_line_wise_fabric_req_report_controller',document.getElementById('cbo_floor_id').value+'_'+this.value+'_'+document.getElementById('cbo_company_id').value, 'load_drop_down_line', 'line_td' ); ;get_php_form_data( this.value, 'eval_multi_line_select', 'requires/daily_line_wise_fabric_req_report_controller' );
	//load_drop_down( 'requires/daily_line_wise_fabric_req_report_controller', this.value, 'load_drop_down_floor', 'floor_td' );get_php_form_data( this.value, 'eval_multi_select', 'requires/daily_line_wise_fabric_req_report_controller' );
	exit();    	 
}

if ($action=="load_drop_down_floor")
{
	echo create_drop_down( "cbo_floor_id", 120, "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and production_process=5 
	and location_id='$data' order by floor_name","id,floor_name", 0, "-- Select Floor --", $selected, "",0 );
	//load_drop_down( 'requires/daily_line_wise_fabric_req_report_controller', this.value+'_'+document.getElementById('cbo_location_id').value+'_'+document.getElementById('cbo_company_id').value, 'load_drop_down_line', 'line_td' );     	 	
	exit();    	 
}

if ($action == "eval_multi_select")
{
	echo "set_multiselect('cbo_floor_id','0','0','','0');\n";
	//echo "set_multiselect('cbo_line','0','0','','0');\n";
	exit();
}

if ($action=="load_drop_down_line")
{
	$explode_data = explode("_",$data);
	$prod_reso_allo=return_field_value("auto_update","variable_settings_production","company_name=$explode_data[2] and variable_list=23 and is_deleted=0 and status_active=1");
	$txt_date = $explode_data[3];
	$cond="";
	if($prod_reso_allo==1)
	{
		$line_library=return_library_array( "select id,line_name from lib_sewing_line", "id", "line_name"  );
		$line_array=array();
		if($txt_date=="")
		{
			if( $explode_data[0]==0 && $explode_data[1]!=0 ) $cond = " and location_id= $explode_data[1]";
			if( $explode_data[0]!=0 ) $cond = " and floor_id in($explode_data[0])";
			$line_data=sql_select("select id, line_number from prod_resource_mst where is_deleted=0 $cond");
		}
		else
		{
			if( $explode_data[0]==0 && $explode_data[1]!=0 ) $cond = " and a.location_id= $explode_data[1]";
			if( $explode_data[0]!=0 ) $cond = " and a.floor_id in($explode_data[0])";
			if($db_type==0)
			{
				$line_data=sql_select("select a.id, a.line_number from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id and b.pr_date='".change_date_format($txt_date,'yyyy-mm-dd')."' and a.is_deleted=0 and b.is_deleted=0 $cond group by a.id, a.line_number");
			}
			else
			{
				$line_data=sql_select("select a.id, a.line_number from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id and b.pr_date='".change_date_format($txt_date,'','',1)."' and a.is_deleted=0 and b.is_deleted=0 $cond group by a.id, a.line_number");	

			}
		}
		
		foreach($line_data as $row)
		{
			$line='';
			$line_number=explode(",",$row[csf('line_number')]);
			foreach($line_number as $val)
			{
				if($line=='') $line=$line_library[$val]; else $line.=",".$line_library[$val];
			}
			$line_array[$row[csf('id')]]=$line;
		}
		echo create_drop_down( "cbo_line", 120,$line_array,"", 0, "-- Select Line --", $selected, "",0,0 );
	}
	else
	{
		if( $explode_data[0]==0 && $explode_data[1]!=0 ) $cond = " and location_name= $explode_data[1]";
		if( $explode_data[0]!=0 ) $cond = " and floor_name in($explode_data[0])";
		echo create_drop_down( "cbo_line", 120, "select id,line_name from lib_sewing_line where is_deleted=0 and status_active=1 and floor_name!=0 $cond order by line_name","id,line_name", 0, "-- Select Line --", $selected, "",0,0 );
	}
	exit();
}

if ($action == "eval_multi_line_select")
{
	// echo "set_multiselect('cbo_line','0','0','','0');\n";
    exit();
}

if($action=="line_popup")
{		  
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
	<script>
		var selected_id = new Array;
		var selected_name = new Array;
		
		function check_all_data() 
		{
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
		
		function toggle( x, origColor )
		{
			var newColor = 'yellow';
			if ( x.style ) { 
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		
		function js_set_value( strCon ) 
		{ 
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
			else 
			{
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
			id= id.substr( 0, id.length - 1 );
			name= name.substr( 0, name.length - 1 ); 
			
			$('#txt_selected_id').val( id );
			$('#txt_selected').val( name ); 
		}
	</script>
      <script>
   		 
		 $(document).ready(function(e) {
            setFilterGrid('list_view',-1);
        });
	</script>
     <div>
	<?
	//echo $company;die;
	if($company==0)
		$company_name="";
	else
		$company_name=" and a.company_id in($company)";//job_no
	
	if($buyer==0)
		$buyer_name="";
	else
		$buyer_name="and b.buyer_name=$buyer";
		
	$prod_reso_allo=return_field_value("auto_update","variable_settings_production","company_name in($company) and variable_list=23 and is_deleted=0 and status_active=1");
	if($prod_reso_allo==1)
	{
		$line_library=return_library_array( "select id,line_name from lib_sewing_line", "id", "line_name"  );
		$line_array=array();
		if($txt_date=="")
		{
			if($location!=0) $cond = " and a.location_id = $location";
			if($hidden_floor_id!='' ) $cond.= " and a.floor_id in(".$hidden_floor_id.")";
			$line_data="select a.id,a.line_number from prod_resource_mst a where a.is_deleted=0 $company_name $cond order by floor_id";
		}
		else
		{
			if($location!=0) $cond = " and a.location_id = $location";
			if($hidden_floor_id!='' ) $cond.= " and a.floor_id in(".$hidden_floor_id.")";
			$line_data="select a.id, a.line_number from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id  and b.pr_date='".change_date_format($txt_date, "", "",1)."' and a.is_deleted=0 and b.is_deleted=0 and a.company_id in($company) $cond";
		}
		
		$lineDataArr = return_library_array($line_data,"id","line_number"); 
		$html='	
		<table class="rpt_table" rules="all" width="310" cellspacing="0" cellpadding="0" border="0">
		    <thead>
		        <tr>
		            <th width="50">SL No</th>
		            <th>Line </th>
		        </tr>
		    </thead>
		</table>
		<div id="" style="max-height:310px; width:320px; overflow-y:scroll">
		<table id="list_view" class="rpt_table" rules="all" width="288" height="" cellspacing="0" cellpadding="0" border="0">
		    <tbody>';
		        $sl=1;
				foreach($lineDataArr as $id=>$line){
					$lineDR=array();
					foreach(explode(',',$line) as $dr){
						$lineDR[$dr]=$line_library[$dr];	
					}
					
				$bgcolor=($sl%2==0)?'#FFFFFF':'#E9F3FF';
				$jsfunction="js_set_value('".$sl.'_'.$id.'_'.implode(',',$lineDR)."')";
				$html.='
				<tr id="tr_'.$sl.'" onclick="'.$jsfunction.'" style="cursor:pointer" height="20" bgcolor="'.$bgcolor.'">
		            <td width="50">'.$sl.'</td>
		            <td>'.implode(',',$lineDR).'</td>
		        </tr>';
				$sl++;
				}
		    
			$html.='</tbody>
		</table></div>';
		echo $html;	
		?>
        <div class="check_all_container">
			<div style="width:100%">
				<div style="width:50%; float:left" align="left">
					<input id="check_all" name="check_all" onclick="check_all_data()" type="checkbox">
					Check / Uncheck All
				</div>
				<div style="width:50%; float:left" align="left">
					<input id="close" class="formbutton" name="close" onclick="parent.emailwindow.hide();" value="Close" style="width:100px" type="button">
				</div>
				</div>
			</div>
		</div>
        <?	
	}
	else
	{
		if($location!=0) $cond = " and location_name = $location";
		if($hidden_floor_id!='' ) $cond.= " and floor_name in(".$hidden_floor_id.")";
		$line_data="select id,line_name from lib_sewing_line where is_deleted=0 and status_active=1 and floor_name!=0 and company_name in($company) $cond order by line_name";
		echo create_list_view("list_view", "Line No","250","300","310",0, $line_data , "js_set_value", "id,line_name", "", 1, "0", $arr, "line_name", "","","0","",1) ;	
	}
		
	echo "<input type='hidden' id='txt_selected_id' />";
	echo "<input type='hidden' id='txt_selected' />";
	exit();
	?>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</div>
	<?
}

if($action=="floor_popup")
{		  
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
	<script>
		var selected_id = new Array;
		var selected_name = new Array;
		
		function check_all_data() 
		{
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
			else 
			{
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
			id= id.substr( 0, id.length - 1 );
			name= name.substr( 0, name.length - 1 ); 
			
			$('#txt_selected_id').val( id );
			$('#txt_selected').val( name ); 
		}

		$(document).ready(function(e) {
			setFilterGrid('list_view',-1);
		});
	</script>
     <div>
	<?
		//echo $company;die;
		if($company==0) $company_name=""; else $company_name=" and a.company_id in($company)";//job_no
		//$floor_name_library=return_library_array( "select id,floor_name from lib_prod_floor", "id", "floor_name"  );
		//echo $location.'DDD';
		if($location!=0) $cond = " and a.location_id= $location";
		$floor_data="select a.id,a.floor_name from lib_prod_floor a where a.is_deleted=0 and production_process = 5 $company_name $cond";
		
		$floorDataArr = return_library_array($floor_data,"id","floor_name"); 
		$html='	
		<table class="rpt_table" rules="all" width="310" cellspacing="0" cellpadding="0" border="0">
		    <thead>
		        <tr>
		            <th width="50">SL No</th>
		            <th>Floor </th>
		        </tr>
		    </thead>
		</table>
		<div id="" style="max-height:310px; width:320px; overflow-y:scroll">
		<table id="list_view" class="rpt_table" rules="all" width="288" height="" cellspacing="0" cellpadding="0" border="0">
		    <tbody>';
		        $sl=1;
				foreach($floorDataArr as $id=>$fname){
					
					
				$bgcolor=($sl%2==0)?'#FFFFFF':'#E9F3FF';
				$jsfunction="js_set_value('".$sl.'_'.$id.'_'.$fname."')";
				$html.='
				<tr id="tr_'.$sl.'" onclick="'.$jsfunction.'" style="cursor:pointer" height="20" bgcolor="'.$bgcolor.'">
		            <td width="50">'.$sl.'</td>
		            <td>'.$fname.'</td>
		        </tr>';
				$sl++;
				}
		    
			$html.='</tbody>
		</table></div>';
		echo $html;	
		?>
        <div class="check_all_container">
			<div style="width:100%">
				<div style="width:50%; float:left" align="left">
					<input id="check_all" name="check_all" onclick="check_all_data()" type="checkbox">
					Check / Uncheck All
				</div>
				<div style="width:50%; float:left" align="left">
					<input id="close" class="formbutton" name="close" onclick="parent.emailwindow.hide();" value="Close" style="width:100px" type="button">
				</div>
				</div>
			</div>
		</div>
        <?	
		echo "<input type='hidden' id='txt_selected_id' />";
		echo "<input type='hidden' id='txt_selected' />";
	
	
	exit();
	?>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
  
    </div>
    <?
}

if($action=="report_generate") 
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	if(str_replace("'","",$cbo_company_id)==0) $company_cond=""; else $company_cond="and b.serving_company in(".str_replace("'","",$cbo_company_id).")";
	if(str_replace("'","",$cbo_location_id)==0) $location_cond=""; else $location_cond="and a.location_id=".str_replace("'","",$cbo_location_id)."";
	if(str_replace("'","",$hidden_floor_id)==0) $floor_cond=""; else $floor_cond="and a.floor_id in(".str_replace("'","",$hidden_floor_id).")";
	if(str_replace("'","",$hidden_line_id)==0) $line_cond=""; else $line_cond="and a.id in(".str_replace("'","",$hidden_line_id).")";
	$companyArr = return_library_array("select id,company_name from lib_company","id","company_name"); 
	//$countryArr = return_library_array("select id,country_name from lib_country","id","country_name"); 
	$colorArr = return_library_array("select id,color_name from lib_color","id","color_name"); 
	$buyerArr = return_library_array("select id,short_name from lib_buyer","id","short_name"); 
	$locationArr = return_library_array("select id,location_name from lib_location","id","location_name"); 
	$floorArr = return_library_array("select id,floor_name from lib_prod_floor","id","floor_name"); 
	$lineArr = return_library_array("select a.id,a.line_name from lib_sewing_line a","id","line_name"); 
	$prod_reso_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');
	//echo $floor_cond.'DDDD';die;

	$comapny_id=str_replace("'","",$cbo_company_id);
	$txt_date_from=str_replace("'","",$txt_date_from);
	//$txt_date_to=str_replace("'","",$txt_date_to);
	// $today_date=date("Y-m-d");
	//$txt_producting_day="".str_replace("'","",$txt_date)."";
	
	if($txt_date_from)
	{
		if($db_type==0)
		{
			$date_from=change_date_format($txt_date_from,'yyyy-mm-dd');
			//$date_to=change_date_format($txt_date_to,'yyyy-mm-dd');
			//$dates_cond="and b.pr_date BETWEEN '$date_from' AND '$date_to'";
			$dates_cond="and b.pr_date='".$date_from."'";
		}
		if($db_type==2)
		{
			$date_from=change_date_format($txt_date_from,'','',1);
			//$date_to=change_date_format($txt_date_to,'','',1);
			//$dates_cond="and b.pr_date BETWEEN '$date_from' AND '$date_to'";
			$dates_cond="and b.pr_date='".$date_from."'";
		}
	}

	$prod_reso_allo=return_field_value("auto_update","variable_settings_production","company_name=$comapny_id and variable_list=23 and is_deleted=0 
	and status_active=1");
	if($prod_reso_allo[0]==1)
	{
		$prod_resource_array=array();

	 	$dataArray_sql="select a.id,a.resource_num, a.location_id, a.floor_id, a.line_number, b.active_machine, b.pr_date, b.man_power, b.operator, b.helper,b.smv_adjust,b.smv_adjust_type, b.line_chief, b.target_per_hour, b.working_hour,c.from_date,c.to_date,c.capacity,d.po_id,d.gmts_item_id,d.color_id,d.target_per_line,d.operator,d.helper from prod_resource_mst a, prod_resource_dtls b,prod_resource_dtls_mast c,prod_resource_color_size d where a.id=c.mst_id and c.id=b.mast_dtl_id and d.dtls_id=c.id and a.id=d.mst_id and b.mast_dtl_id=d.dtls_id and a.company_id=$comapny_id and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0 and d.is_deleted=0 and d.is_deleted=0 $floor_cond $location_cond $line_cond $dates_cond order by  b.pr_date,a.floor_id,d.color_id";
	
		$dataArray_result = sql_select($dataArray_sql);
		$all_po_id=="";
		$line_date_arr=array();
		foreach($dataArray_result as $row)
		{
			if($all_po_id=="")
				$all_po_id=$row[csf("po_id")];
			else
				$all_po_id.=",".$row[csf("po_id")];
			
			if($row[csf('pr_date')]!="")
			{
				$prod_resource_arr[$row[csf('floor_id')]][$row[csf('po_id')]][$row[csf('color_id')]][$row[csf('line_number')]]['target_per_hour']=$row[csf('target_per_hour')];
				$prod_resource_arr[$row[csf('floor_id')]][$row[csf('po_id')]][$row[csf('color_id')]][$row[csf('line_number')]]['working_hour']=$row[csf('working_hour')];
				$prod_resource_arr[$row[csf('floor_id')]][$row[csf('po_id')]][$row[csf('color_id')]][$row[csf('line_number')]]['target_per_line']=$row[csf('target_per_line')];
				$prod_resource_arr[$row[csf('floor_id')]][$row[csf('po_id')]][$row[csf('color_id')]][$row[csf('line_number')]]['operator']=$row[csf('operator')];
				$prod_resource_arr[$row[csf('floor_id')]][$row[csf('po_id')]][$row[csf('color_id')]][$row[csf('line_number')]]['helper']=$row[csf('helper')];
				$prod_resource_arr[$row[csf('floor_id')]][$row[csf('po_id')]][$row[csf('color_id')]][$row[csf('line_number')]]['pr_date']=$row[csf('pr_date')];
				$all_po_id_arr[$row[csf('po_id')]]=$row[csf('po_id')];
				$line_date_arr[$row[csf('pr_date')]]=$row[csf('pr_date')];
			}
		}
	}
	
	$poIds=chop($all_po_id,','); $po_cond_for_in=""; $po_cond_for_in2="";
	$po_ids=count(array_unique(explode(",",$all_po_id)));
	if($db_type==2 && $po_ids>1000)
	{
		$po_cond_for_in=" and (";
		$po_cond_for_in2=" and (";
		$poIdsArr=array_chunk(explode(",",$poIds),999);
		foreach($poIdsArr as $ids)
		{
			$ids=implode(",",$ids);
			$po_cond_for_in.=" c.po_break_down_id in($ids) or"; 
			$po_cond_for_in2.=" po_id in($ids) or"; 
		}
		$po_cond_for_in=chop($po_cond_for_in,'or ');
		$po_cond_for_in.=")";
		$po_cond_for_in2=chop($po_cond_for_in2,'or ');
		$po_cond_for_in2.=")";
	}
	else
	{
		$poIds=implode(",",array_unique(explode(",",$all_po_id)));
		$po_cond_for_in=" and c.po_break_down_id in($poIds)";
		$po_cond_for_in2=" and po_id in($poIds)";
	}
	$condition= new condition();
	$condition->company_name("=$comapny_id");
		//echo $all_po_id.'dd';die;
	if($all_po_id!='')
	{
		$condition->po_id_in("$all_po_id");
	}
	
	$condition->init();
	$fabric= new fabric($condition);
	//echo $fabric->getQuery();die;
	$fabric_costing_arr=$fabric->getQtyArray_by_FabriccostidGmtsItemOrderAndGmtscolor_knitAndwoven_greyAndfinish();
	//echo "<pre>";
	//print_r($fabric_costing_arr);
	//$all_po_id=
	 $color_sql="SELECT  a.buyer_name,a.style_ref_no as style,a.job_no,a.job_no_prefix_num as job_id,b.id as po_id,b.po_number,b.grouping as ref_no,b.file_no, c.country_id,c.item_number_id as item_id,c.color_number_id as color_id, (c.order_quantity) as color_qnty,c.plan_cut_qnty
	from  wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c  
	where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id and a.status_active=1 and b.status_active=1 and c.status_active=1 $po_cond_for_in order by b.id";
	$color_sql_res = sql_select($color_sql);
	$color_qnty_arr = array();$po_arr = array();
	foreach ($color_sql_res as $row) 
	{
		$color_qnty_arr[$row[csf('po_id')]][$row[csf('color_id')]][$row[csf('item_id')]] += $row[csf('color_qnty')];
		$color_qnty_arr2[$row[csf('po_id')]][$row[csf('color_id')]][$row[csf('item_id')]] += $row[csf('plan_cut_qnty')];
		$po_arr[$row[csf('po_id')]]['job'] = $row[csf('job_no')];
		$po_arr[$row[csf('po_id')]]['style'] = $row[csf('style')];
		$po_arr[$row[csf('po_id')]]['ref_no'] = $row[csf('ref_no')];
		$po_arr[$row[csf('po_id')]]['file_no'] = $row[csf('file_no')];
		$po_arr[$row[csf('po_id')]]['buyer_name'] = $row[csf('buyer_name')];
		$po_arr[$row[csf('po_id')]]['item_id'] = $row[csf('item_id')];
		$po_arr[$row[csf('po_id')]]['po_no'] = $row[csf('po_number')];
	}
	
	//getQtyArray_by_FabriccostidGmtsItemOrderAndGmtscolor_knitAndwoven_greyAndfinish
	
	$prodcostDataArray="select a.id,a.item_number_id,c.cons,c.color_number_id,c.po_break_down_id as po_id
	from wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_avg_con_dtls c where
	c.pre_cost_fabric_cost_dtls_id=a.id and c.cons>0  and a.status_active=1 and c.status_active=1  $po_cond_for_in order by a.id";
	$resultfab_arr = sql_select($prodcostDataArray);
	foreach($resultfab_arr as $row)
	{
		//echo $prodRow[csf('knit_charge')].',';
		//$fab_purchase_knit=array_sum($fabric_costing_arr['knit']['finish'][$row[csf('id')]][$row[csf('item_number_id')]][$row[csf('po_id')]][$row[csf('color_number_id')]]);
		//$fab_purchase_woven=array_sum($fabric_costing_arr['woven']['finish'][$row[csf('id')]][$row[csf('item_number_id')]][$row[csf('po_id')]][$row[csf('color_number_id')]]);
		//	if(is_infinite($fab_purchase_knit) || is_nan($fab_purchase_knit)){$fab_purchase_knit=0;}
		//$fab_purchase_woven=array_sum($fabric_costing_arr['woven']['finish'][$row[csf('po_id')]]);
		//$prodcostArray[$row[csf('po_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]]['fin']+=$row[csf('cons')];
		//$prodcostArray[$row[csf('po_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]]['fin'] += $fab_purchase_knit+$fab_purchase_woven;
		$prodcostArray[$row[csf('po_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]]['PreId'].= $row[csf('id')].',';
	}	
		
	$date_row=count($line_date_arr);
	$width=1200+$date_row*80;
	//echo $width;			
	//echo "<pre>";print_r($rowspan_arr);die;
	//wo_pre_cos_fab_co_avg_con_dtls
	ob_start();
	?>
	<style>
		hr { 
		display: block;
		margin-top: 0.5em;
		margin-bottom: 0.5em;
		margin-left: auto;
		margin-right: auto;
		border-style: inset;
		border-width:1px;
		border-color:#333;
    } 
    </style>
	<div style="width: <? echo $width;?>px;">
        <table width="<? echo $width;?>" cellpadding="0" cellspacing="0"> 
            <tr class="form_caption">
                <td colspan="20" align="center"><strong><? echo $report_title; ?></strong></td> 
            </tr>
            <tr class="form_caption">
                <td colspan="20" align="center"><strong><? echo $companyArr[$comapny_id]; ?></strong></td> 
            </tr>
            <tr class="form_caption">
                <td colspan="20" align="center"><strong><? if($date_from!='') echo "Date:  ".$date_from.' To '.$date_to;else echo " "; ?></strong></td> 
            </tr>
        </table>
        <? 
		$po_row_span_arr=array();$color_fab_cons_arr=array();
		foreach($prod_resource_arr as $floor_id=>$floor_data )
		{
			foreach($floor_data as $po_id=>$po_data )
			{
				$po_row_span=0;
				foreach($po_data as $color_id=>$color_data )
				{
					$color_row_span=0;
					foreach($color_data as $line_id=>$row )
					{
						$po_row_span++;$color_row_span++;
					}

					$po_row_span_arr[$floor_id][$po_id]=$po_row_span;
					$color_row_span_arr[$floor_id][$po_id][$color_id]=$color_row_span;
					//$color_fab_cons_arr[$floor_id][$po_id][$color_id]=$tot_fab_cons;
				}
			}
		}
		//print_r($color_fab_cons_arr);
		//echo $date_row;die;
		?>
        <table id="table_header_1" class="rpt_table" width="<? echo $width;?>" cellpadding="0" cellspacing="0" border="1" rules="all">
            <thead>
                <tr>
                    <th width="30">SL No</th>
                    <th width="100">Buyer</th>
                    <th width="">Job</th>
                    <th width="70">File No</th>
                    <th width="100">Item</th>
                    <th width="70">Ref. No</th>
                    <th width="100">Style</th>
                    <th width="100">Order No</th>
                    <th width="100">Garments Color</th>
                    <th width="80">Color Qty (Pcs)</th>
                    <th width="80">TTL consumption</th>
                    <th width="80">Allowcated Line Name</th>
                    <th width="120">Plan vs Actual</th>
                    <? foreach($line_date_arr as $mon_day) 
                    { ?>
                    <th width="80"><? echo change_date_format($mon_day);?></th>
                    <?
                    }
                    ?>
                </tr>
            </thead>
        </table>
        <div style="width:<? echo $width+20;?>px; max-height:400px; overflow-y:scroll" id="scroll_body">
        <table class="rpt_table" width="<? echo $width;?>" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
            <tbody>
            <?php
            $lineArr = return_library_array("select a.id,a.line_name from lib_sewing_line a","id","line_name"); 
            $total_color_po_qty=$total_fab_fin_qty=$total_fab_req_qty=0;
            $i=1;
            foreach($prod_resource_arr as $floor_id=>$floor_data )
            {
				$floor_color_qty=$floor_fab_fin_qty=$floor_fab_req_qty=0;
				foreach($floor_data as $po_id=>$po_data )
				{
					$p=1;
					foreach($po_data as $color_id=>$color_data )
					{
						$c=1;$tot_fab_cons=0;
						foreach($color_data as $line_id=>$row )
						{
							if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							$po_row_span=$po_row_span_arr[$floor_id][$po_id];
							$color_row_span=$color_row_span_arr[$floor_id][$po_id][$color_id];		
							?>
							<tr bgcolor="<?=$bgcolor;?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor;?>')" id="tr_<?=$i; ?>" style="font-size:12px">
							<?php
							//echo "d,";
							// $grand_poly_wip+=$poly_wip;
							$target_per_hour=$row['target_per_hour'];
							$working_hour=$row['working_hour'];
							
							$target_per_line=$row['target_per_line'];
							$sewing_plan_qty= $target_per_hour*$working_hour;
							
							$line_arr=explode(",",$line_id);
							$line_name="";
							foreach($line_arr as $line_id)
							{
								$line_name .= ($line_name == "") ? $lineArr[$line_id] : ",".$lineArr[$line_id];
							}	

							if($p==1) 
							{
							?>
							<td width="30" rowspan="<? echo $po_row_span?>"><? echo $i; ?></td>
							<td width="100" rowspan="<? echo $po_row_span?>"><? echo $buyerArr[$po_arr[$po_id]['buyer_name']] ;// echo $row['job_no_mst']; ?></td>
							<td width="" rowspan="<? echo $po_row_span?>"><? echo $po_arr[$po_id]['job'] ;//$row['style_ref_no']?></td>
							<td width="70" rowspan="<? echo $po_row_span?>"><?  echo $po_arr[$po_id]['file_no']; ?></td>
							<td width="100" rowspan="<? echo $po_row_span?>"><div style="word-break: break-all"><? echo $garments_item[$po_arr[$po_id]['item_id']]; ?></div></td>
							<td width="70" rowspan="<? echo $po_row_span?>"><? echo $po_arr[$po_id]['ref_no']; ?></td>
							<td width="100" rowspan="<? echo $po_row_span?>"><? echo $po_arr[$po_id]['style']; ?></td>
							<td width="100" title="POId=<? echo $po_id;?>" rowspan="<? echo $po_row_span?>"><? echo $po_arr[$po_id]['po_no'];//echo $colorArr[$color_id]; ?></td>
							<?
							}
							
							if($c==1) 
							{
								//$fab_finish_cons= $prodcostArray[$po_id][$po_arr[$po_id]['item_id']][$color_id]['fin'];
								$PreIds= rtrim($prodcostArray[$po_id][$po_arr[$po_id]['item_id']][$color_id]['PreId'],',');
								//echo $PreIds.'d'.$fab_finish_cons;
								$PreIdsArr=array_unique(explode(",",$PreIds));
								//$tot_fab_cons=0;
								foreach($PreIdsArr as $fid)
								{
									//	echo $fid.',';
									$fab_purchase_knit=array_sum($fabric_costing_arr['knit']['finish'][$fid][$po_arr[$po_id]['item_id']][$po_id][$color_id]);
									$fab_purchase_woven=array_sum($fabric_costing_arr['woven']['finish'][$fid][$po_arr[$po_id]['item_id']][$po_id][$color_id]);
									//echo $fab_purchase_knit.', ';
									$tot_fab_cons+=$fab_purchase_knit+$fab_purchase_woven;
								}
								
								$plan_color_qnty=$color_qnty_arr2[$po_id][$color_id][$po_arr[$po_id]['item_id']];
								$fab_finish_cons=number_format(($tot_fab_cons/$plan_color_qnty)*12, 4, '.', ''); 
								?>
								<td width="100" rowspan="<? echo $color_row_span?>" align="center"><div style="word-break: break-all"><? echo $colorArr[$color_id]; ?></div></td>
								<td width="80" rowspan="<? echo $color_row_span?>" align="right"><? echo number_format($color_qnty_arr[$po_id][$color_id][$po_arr[$po_id]['item_id']],0); ?></td>
								<td width="80" rowspan="<? echo $color_row_span?>" align="right" title="Budget Fab Fin.Cons"><? echo number_format($fab_finish_cons,4); ?></td>
								<?
							}
							?>
							<td width="80" align="center" title="LineId=<? echo $line_id;?>"><? echo $line_name; ?></td>
							<td width="120" align="center" title="<? echo "Working Hr=".$working_hour."Target Hr=".$target_per_hour;;?>"> Sewing Plan Qty per Day<hr>Fabrics Required</td>
							<? 
							
							foreach($line_date_arr as $mon_day) 
							{  //change_date_format
								$fab_req_qty=$sewing_plan_qty*$fab_finish_cons/12;
								?>
								
								<td width="80" align="right" title="ProdDate=<? echo $mon_day;?>, Sewing Plan=Target per hour(<? echo $target_per_hour; ?>)*Working hour(<? echo $working_hour; ?>), Fab.Req=Sewing Plan*TTL  Cons/12">&nbsp<? echo number_format($sewing_plan_qty,2);?>;<hr>&nbsp;<? echo number_format($fab_req_qty,2);?></td>
								<?
							}
							?>
							</tr>
							<? 	
							$i++;$c++;$p++;
							$floor_color_qty+=$color_qnty_arr[$po_id][$color_id][$po_arr[$po_id]['item_id']];
							$floor_fab_fin_qty+=$fab_finish_cons;
							$floor_fab_req_qty+=$fab_req_qty;
							
							$total_color_po_qty+=$color_qnty_arr[$po_id][$color_id][$po_arr[$po_id]['item_id']];
							$total_fab_fin_qty+=$fab_finish_cons;
							$total_fab_req_qty+=$fab_req_qty;
						}
					}
				}
            ?>
            <tr class="tbl_bottom">
            <td colspan="9" align="right"><? echo $floorArr[$floor_id];?> &nbsp;Floor Total</td>
            
            <td width="80" align="right"><? echo number_format($floor_color_qty,0); ?></td>
            <td width="80" align="right"><? echo number_format($floor_fab_fin_qty,4); ?></td>
            <td width="80" align="right"></td>
            <td width="80" align="right"></td>
            <? foreach($line_date_arr as $mon_day) 
            {  //change_date_format ?>
            <td width="80" align="right" title="Fabric Required"><? echo number_format($floor_fab_req_qty,2); ?></td>
            <?
            }
            ?>
            </tr>
            <?php
            }
            ?>
            </tbody>
            <tfoot>
            <tr>
            <th colspan="9" align="right">Grand Total</th>
            <th width="80"><? echo number_format($total_color_po_qty,0); ?></th>
            <th width="80"><? echo number_format($total_fab_fin_qty,4); ?></th>
            <th width="80"></th>
            <th width="80"></th>
            <? foreach($line_date_arr as $mon_day) 
            {  //change_date_format ?>
            <th width="80" align="right"><? echo number_format($total_fab_req_qty,2); ?></th>
            <?
            }
            ?>
            </tr>
            </tfoot>
        </table>
        </div>
       </div>
    <br/>
    <br/>
    <?php

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
?>