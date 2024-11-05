<? 
include('../../../includes/common.php');
session_start();
extract($_REQUEST);
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$date=date('Y-m-d');
$user_id=$_SESSION['logic_erp']['user_id'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
$buyer_library=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );
$color_Arr_library=return_library_array( "select id,color_name from lib_color", "id", "color_name" );	

$country_library=return_library_array( "select id,country_name from lib_country", "id", "country_name"  );

$location_library=return_library_array( "select id,location_name from lib_location", "id", "location_name"  );
$floor_group_library=return_library_array( "select id,group_name from lib_prod_floor", "id", "group_name"  );

$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier where status_active=1 and is_deleted=0 order by supplier_name",'id','supplier_name');
//echo $data;
if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_id", 120, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' order by location_name","id,location_name", 1, "-Select Location-", $selected, "",0 );
	exit();     	 
}
if($action=="job_popup")
{
	echo load_html_head_contents("Job Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	//echo $company_id;
	?>
	
	<script>
    function js_set_value(id)
    {
		//alert(id);
		document.getElementById('selected_id').value=id;
		parent.emailwindow.hide();
    }
		</script>
		</head>
		<body>
		<div align="center" style="width:820px;">
        <form name="styleRef_form" id="styleRef_form">
		<fieldset style="width:800px;">
            <table width="800" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
            	<thead>
                    <th>Company</th>
                    <th>Buyer</th>
                    <th>Search By</th>
                    <th id="search_by_td_up" width="170">Please Enter Job No</th>
                    <th><input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;" onClick="reset_form('styleRef_form','search_div','','','','');"></th> 					<input type="hidden" id="selected_id" name="selected_id" />
                </thead>
                <tbody>
                	<tr class="general">
                    	<td align="center"> 

							<?
                            //echo "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 id=$company_id order by company_name";
                                echo create_drop_down( "cbo_company_name", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 and id=$company_id $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected,"",1 );
                            ?>
                        </td>
                        <td align="center">
                        	 <?
								echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$company_id $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer_name,"",0 );
								// if($buyer>0) $buy_cond=" and a.id=$buyer";
								// echo create_drop_down( "cbo_buyer_name", 140, "select a.id,a.buyer_name from lib_buyer a where a.status_active=1 and a.is_deleted=0 $buy_cond order by a.buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "",0,"" );
							?>
                        </td>                 
                        <td align="center">	
                    	<?
                       		$search_by_arr=array(1=>"Job No",2=>"Style Ref");
							$dd="change_search_event(this.value, '0*0', '0*0', '../../') ";							
							echo create_drop_down( "cbo_search_by", 130, $search_by_arr,"",0, "--Select--", "",$dd,0 );
						?>
                        </td>     
                        <td align="center" id="search_by_td">				
                            <input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />	
                        </td> 	
                        <td align="center">
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view (document.getElementById('cbo_company_name').value+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_year; ?>', 'job_popup_search_list_view', 'search_div', 'piece_rate_wo_report_controller', 'setFilterGrid(\'table_body2\',-1)');" style="width:100px;" />
                    </td>
                    </tr>
            	</tbody>
           	</table>
            <div style="margin-top:15px" id="search_div"></div>
		</fieldset>
	</form>
    </div>
    </body>           
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit(); 
}

if ($action=="job_popup_search_list_view")
{
  	
	extract($_REQUEST);
	list($company_id,$buyer_id,$search_type,$search_value,$cbo_year)=explode('**',$data);
	if($company_id==0)
	{
		echo "Please Select Company Name";
		die;
	}
	
	if($search_type==1 && $search_value!=''){
		$search_con=" and a.job_no like('%$search_value')";	
	}
	else if($search_type==2 && $search_value!=''){
		$search_con=" and a.style_ref_no like('%$search_value%')";	
	}

	if($buyer_id==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_cond="";
		}
		else
		{
			$buyer_cond="";
		}
	}
	else
	{
		$buyer_cond=" and a.buyer_name=$buyer_id";
	}
	
	if(trim($cbo_year)!=0) 
	{
		if($db_type==0)
		{
			$year_cond=" and YEAR(a.insert_date)=$cbo_year";
		}
		else
		{
			$year_cond=" and to_char(a.insert_date,'YYYY')=$cbo_year";	
		}
	}
	else $year_cond="";
	
	if($db_type==2)
	{
		$group_field="LISTAGG(CAST(b.po_number AS VARCHAR2(4000)),',') WITHIN GROUP ( ORDER BY b.po_number) as po_number";
		$year_field="to_char(a.insert_date,'YYYY')";
	} 
	else if($db_type==0) 
	{
		$group_field="group_concat(distinct b.po_number ) as po_number";
		$year_field="YEAR(a.insert_date)";
	}

	$arr=array (2=>$company_arr,3=>$buyer_arr);
	$sql= "SELECT a.id, a.job_no_prefix_num, a.job_no, a.company_name,a.buyer_name,a.style_ref_no,$year_field as year , $group_field
	from wo_po_details_master a,  wo_po_break_down b 
	where a.job_no=b.job_no_mst and b.status_active in(1,2,3) and a.company_name=$company_id $buyer_cond $year_cond $search_con 
	group by a.id, a.job_no_prefix_num, a.job_no, a.company_name,a.buyer_name,a.style_ref_no,a.insert_date
	order by a.id";
	//echo $sql;//die;
	$rows=sql_select($sql);
	?>
    <table width="800" border="1" rules="all" class="rpt_table">
        <thead>
            <tr>
                <th width="30">SL</th>
                <th width="120">Company</th>
                <th width="120">Buyer</th>
                <th width="50">Year</th>
                <th width="120">Job no</th>
                <th width="120">Style</th>
                <th>Po number</th>
                
            </tr>
       </thead>
    </table>
    <div style="max-height:820px; overflow:auto;">
    <table id="table_body2" width="800" border="1" rules="all" class="rpt_table">
     <? $rows=sql_select($sql);
         $i=1;
         foreach($rows as $data)
         {
			if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			$po_num=implode(",",array_unique(explode(",",$data[csf('po_number')])));
			?>
			<tr bgcolor="<? echo  $bgcolor;?>" onClick="js_set_value('<? echo $data[csf('id')]; ?>'+'_'+'<? echo $data[csf('job_no')]; ?>')" style="cursor:pointer;">
                <td width="30" align="center"><? echo $i; ?></td>
                <td width="120"><p><? echo $company_arr[$data[csf('company_name')]]; ?></p></td>
                <td width="120"><p><? echo $buyer_library[$data[csf('buyer_name')]]; ?></p></td>
                <td align="center" width="50"><p><? echo $data[csf('year')]; ?></p></td>
                <td width="120"><p><? echo $data[csf('job_no')]; ?></p></td>
                <td width="120"><p><? echo $data[csf('style_ref_no')]; ?></p></td>
                <td><p><? echo $po_num; ?></p></td>
			</tr>
			<? 
			$i++; 
		} 
		?>
    </table>
    </div>
    <?
	exit();
}

//order wise browse------------------------------//
if($action=="order_wise_search")
{		  
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	?>
	<script>
		
		var selected_id = new Array;
		var selected_name = new Array;
		
    	function check_all_data() {
			var tbl_row_count = document.getElementById( 'list_view' ).rows.length;
			tbl_row_count = tbl_row_count - 0;
			for( var i = 1; i <= tbl_row_count; i++ ) {
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
    </script>
	<?
	extract($_REQUEST);
	//echo $job_no;//die;
	if(str_replace("'","",$company_id)!="")  $company_name="and b.company_id=$company_id";
    if (str_replace("'","",$job_no)!="") $job_cond="and a.job_no_mst='".$job_no."'";
	$sql = "SELECT distinct a.id,b.sys_number,a.job_no_mst,a.po_number from wo_po_break_down a, piece_rate_wo_mst b,piece_rate_wo_dtls c where 
	b.id=c.mst_id and c.order_id=a.id
	and a.status_active in(1,2,3)  $company_name $job_cond ";
	//echo $sql;//die;
	echo create_list_view("list_view","Job No,Order Number, WO Number","120,120,120","550","310",0, $sql , "js_set_value", "id,po_number", "", 1, "0", $arr, "job_no_mst,po_number,sys_number", "","setFilterGrid('list_view',-1)","0","",1) ;	
	echo "<input type='hidden' id='txt_selected_id' />";
	echo "<input type='hidden' id='txt_selected' />";
	exit();
}
//Work order wise browse------------------------------//
if($action=="work_order_wise_search")
{		  
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	?>
	<script>
		
		var selected_id = new Array;
		var selected_name = new Array;
		
    	function check_all_data() {
			var tbl_row_count = document.getElementById( 'list_view' ).rows.length;
			tbl_row_count = tbl_row_count - 0;
			for( var i = 1; i <= tbl_row_count; i++ ) {
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
    </script>
	<?
	extract($_REQUEST);
	//echo $job_no;//die;
	if(str_replace("'","",$company_id)!="")  $company_name="and b.company_id=$company_id";
    if (str_replace("'","",$job_no)!="") $job_cond="and a.job_no_mst='".$job_no."'";
	$sql = "SELECT distinct a.id,b.sys_number,a.job_no_mst,a.po_number from wo_po_break_down a, piece_rate_wo_mst b,piece_rate_wo_dtls c where 
	b.id=c.mst_id and c.order_id=a.id
	and a.status_active in(1,2,3)  $company_name $job_cond";
	//echo $sql;//die;
	echo create_list_view("list_view","Job No,Order Number, WO Number","120,120,120","550","310",0, $sql , "js_set_value", "id,sys_number", "", 1, "0", $arr, "job_no_mst,po_number,sys_number", "","setFilterGrid('list_view',-1)","0","",1) ;	
	echo "<input type='hidden' id='txt_selected_id' />";
	echo "<input type='hidden' id='txt_selected' />";
	exit();
}
 
if($action=="report_generate")
{
	?>
		<style type="text/css">
            .block_div 
            { width:auto; height:auto; text-wrap:normal; vertical-align:bottom; display: block; position: !important; -webkit-transform: rotate(-90deg); -moz-transform: rotate(-90deg); }
	          
	    </style> 
	<?
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$cbo_company_id=str_replace("'","",$cbo_company_id); 
	$cbo_location_id=str_replace("'","",$cbo_location_id); 
	$cbo_service_id=str_replace("'","",$cbo_service_id);
	$cbo_buyer_name=str_replace("'","",$cbo_buyer_name); 
	$txt_job_no=str_replace("'","",$txt_job_no); 
	$hidden_job_id=str_replace("'","",$hidden_job_id); 
	$txt_order_no=str_replace("'","",$txt_order_no);
	$hidden_order_id=str_replace("'","",$hidden_order_id); 
	$txt_wo_no=str_replace("'","",$txt_wo_no); 
	$hidden_txt_wo_id=str_replace("'","",$hidden_txt_wo_id); 
	$cbo_rate_for=str_replace("'","",$cbo_rate_for);

	 //echo "Company".$cbo_company_id."<br>Lo ".$cbo_location_id."<br> Ser ".$cbo_service_id."<br> Buy ".$cbo_buyer_name."<br> JOb ".$txt_job_no.	"<br> hid Job ".$hidden_job_id."<br>Order ".$txt_order_no."<br>Hid Order ".$hidden_order_id."<br>Wo No ".$txt_wo_no."<br>hid Wo ".$hidden_txt_wo_id."<br>Rate for ".$cbo_rate_for;

	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);

	$sql_cond	= "";
	if($cbo_company_id>0) $sql_cond.=" AND a.company_id=$cbo_company_id";
	if($cbo_location_id>0) $sql_cond.=" AND a.location=$cbo_location_id";
	if($cbo_buyer_name>0) $sql_cond.=" AND b.buyer_id=$cbo_buyer_name";
	if($cbo_service_id>0) $sql_cond.=" AND a.service_provider_id=$cbo_service_id";
	if($txt_job_no!="") $sql_cond.=" AND c.job_no_mst='$txt_job_no'";
	if($txt_order_no!="") $sql_cond.=" AND c.po_number='$txt_order_no'";
	if($hidden_order_id!="") $sql_cond.=" AND c.id=$hidden_order_id";
	if($txt_wo_no!="") $sql_cond.=" AND a.sys_number='$txt_wo_no'";
	if($cbo_rate_for>0) $sql_cond.=" AND a.rate_for=$cbo_rate_for";
	//echo $sql_cond;die;

	if($db_type==0)
	{
		$buyer_group="group_concat(distinct(b.buyer_id)) as buyer_id";
		$po_number_group="group_concat(distinct(c.po_number)) as po_number";
		$style_ref="group_concat(distinct(b.style_ref)) as style_ref";
		$color_group="group_concat(distinct(d.color_id)) as color_id";
		$ship_date_group="group_concat(distinct(c.shipment_date)) as shipment_date";
	}
	else
	{ 
		$buyer_group="listagg(cast(b.buyer_id as varchar(4000)),',') within group(order by b.buyer_id) as buyer_id";
		$po_number_group="listagg(cast(c.po_number as varchar(4000)),',') within group(order by c.po_number) as po_number";
		$style_ref="listagg(cast(b.style_ref as varchar(4000)),',') within group(order by b.style_ref) as style_ref";

		$color_group="listagg(cast(d.color_id as varchar(4000)),',') within group(order by d.color_id) as color_id";
		$ship_date_group="listagg(cast(c.shipment_date as varchar(4000)),',') within group(order by c.shipment_date) as shipment_date";
	}

	$sql ="SELECT a.sys_number,a.rate_for,a.wo_date,a.company_id,
         a.service_provider_id,SUM (d.wo_qty)AS wo_qty_pcs,SUM(d.order_qty) as order_qty,b.wo_qty,b.uom,AVG(b.avg_rate) as rate,b.amount,$buyer_group,$po_number_group,$style_ref,$color_group,$ship_date_group 
		 FROM piece_rate_wo_mst  a,
         piece_rate_wo_dtls    b,
         wo_po_break_down      c,
         piece_rate_wo_qty_dtls d
   		WHERE a.id = b.mst_id
         AND b.order_id = c.id
         AND b.id = d.dtls_id
         AND a.wo_date BETWEEN '$txt_date_from' AND '$txt_date_to' $sql_cond
		GROUP BY a.sys_number,a.wo_date,a.company_id,a.service_provider_id,b.wo_qty,b.uom,b.amount,a.rate_for"; 
		 //echo $sql;//."<br>".$sql_cond;
		$sql_res=sql_select($sql);
		// echo "<pre>";
		// print_r($sql_res);
	?>

	<div >
		<table cellspacing="0" style="width:1440px;margin-top: 20px; margin-bottom:5px">
				<tr class="form_caption" style="border:none;">
					
					<td colspan="17" align="center" style="border:none; font-size:16px;">
						Company Name : <? echo $company_arr[$cbo_company_id]; ?>                                
					</td>
				</tr>
				<tr class="form_caption" style="border:none;">
					<td colspan="17" align="center" style="border:none;font-size:12px; font-weight:bold" >
						<? echo "From:&nbsp;".$txt_date_from."&nbsp;To&nbsp;".$txt_date_to;?>
					</td>
				</tr>
			</table>
		<table cellspacing="0" border="1" class="rpt_table" rules="all" id="" style="width:1460px;">
			<thead>
				<tr>
					<th width="40">SL</th>
					<th width="120">WO NO</th>
					<th width="80">WO FOR</th>
					<th width="100">WO DATE</th>
					<th width="120">COMPANY</th>
					<th width="80">PARTY</th>
					<th width="80">BUYER</th>
					<th width="80">ORDER NO</th>
					<th width="80">STYLE</th>
					<th width="80">COLOR</th>
					<th width="80">Ship. Date</th>
					<th width="120">ORD QTY(Pcs)</th>
					<th width="80">WO QTY(PCS)</th>
					<th width="80">WO QTY</th>
					<th width="80">UOM</th>
					<th width="80">RATE</th>
					<th width="80">AMOUNT</th>
				</tr>
			</thead>
		</table>
		<div style="max-height:425px; overflow-y:scroll; width:1480px;" id="scroll_body">
			<table  border="1" class="rpt_table"  width="1460px" rules="all" id="table_body" >
				
				<tbody>
					<?
					$i=1;
						foreach ($sql_res as $row) 
						{
							?>
							<tr>
							<td width="40"><?=$i;?></td>
							<td align="center" width="120"><?=$row[csf("sys_number")];?></td>
							<td align="center" width="80"><?=$rate_for[$row[csf("rate_for")]];?></td>
							<td align="center" width="100"><?=$row[csf("wo_date")];?></td>
							<td align="center" width="120"><?=$company_arr[$row[csf("company_id")]];?></td>
							<td align="center" width="80"><?=$supplier_arr[$row[csf("service_provider_id")]];?></td>
							<td align="center" width="80">
								<?
								$buyer="";
								$buyer_gr=array_unique(explode(",",chop($row[csf("buyer_id")],',')));
								foreach ($buyer_gr as $value) {
									$buyer.=$buyer_library[$value].", ";
								}
								echo rtrim($buyer,", ");
							?>
							</td>
							<td align="center" width="80"><?=implode(", ",array_unique(explode(",",chop($row[csf("po_number")],','))));?></td>
							<td align="center" width="80"><?=implode(", ",array_unique(explode(",",chop($row[csf("style_ref")],','))));?></td>
							<td align="center" width="80">
							<?

					
								$color="";
								$color_gr=array_unique(explode(",",chop($row[csf("color_id")],',')));
								foreach ($color_gr as $value) {
									$color.=$color_Arr_library[$value].", ";
								}
								echo rtrim($color,", ");
								?>
								</td>
							<td align="center" width="80"><?=implode(", ",array_unique(explode(",",chop($row[csf("shipment_date")],','))));?></td>
							<td align="center" width="120"><?=$row[csf("order_qty")];?></td>
							<td align="center" width="80"><?=$row[csf("wo_qty_pcs")];?></td>
							<td align="center" width="80"><?=$row[csf("wo_qty")];?></td>
							<td align="center" width="80"><?=$unit_of_measurement[$row[csf("uom")]];?></td>
							<td align="center" width="80"><?=$row[csf("rate")];?></td>
							<td align="center" width="80"><?=$row[csf("amount")];?></td>
							</tr>
							<?
							$i++;
						}

					?>
				</tbody>
			</table>
		</div>
	</div>
	<?
	//***************************************************************************************************************************
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

