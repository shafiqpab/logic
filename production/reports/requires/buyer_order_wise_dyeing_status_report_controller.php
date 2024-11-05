<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
extract($_REQUEST);
$permission=$_SESSION['page_permission'];

include('../../../includes/common.php');

$user_name = $_SESSION['logic_erp']["user_id"];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
$company_arr=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
$yarn_count_details=return_library_array( "select id,yarn_count from lib_yarn_count", "id", "yarn_count");
$supllier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name");

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond order by buyer_name","id,buyer_name", 0, "-- All Buyer --", $selected, "" ,0); 
	exit();
}

if($action=="buyer_name_search_popup")
{
	echo load_html_head_contents("Buyer Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
     
	<script>
		
		var selected_id = new Array; var selected_name = new Array;
		
		function check_all_data()
		{
			var tbl_row_count = document.getElementById( 'list_view' ).rows.length;
			tbl_row_count = tbl_row_count - 1;

			for( var i = 1; i <= tbl_row_count; i++ )
			{
				$('#tr_'+i).trigger('click'); 
			}
		}
		
		function toggle( x, origColor ) 
		{
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		
		function js_set_value( str ) {
			
			if (str!="") str=str.split("_");
			 
			toggle( document.getElementById( 'tr_' + str[0] ), '#FFFFCC' );
			 
			if( jQuery.inArray( str[1], selected_id ) == -1 ) {
				selected_id.push( str[1] );
				selected_name.push( str[2] );
				
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == str[1] ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
			}
			var id = ''; var name = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + '*';
			}
			
			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );
			
			$('#hide_buyer_id').val( id );
			$('#hide_buyer_name').val( name );
		}
	
    </script>

	<input type="hidden" id="hide_buyer_id" name="hide_buyer_id">
    <input type="hidden" id="hide_buyer_name" name="hide_buyer_name">
<? 
	$arr=array();  
	$sql="select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active=1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$companyID' and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond order by buyer_name";
	
	echo create_list_view("list_view","Buyer Name","300","370","280",0,$sql,"js_set_value","id,buyer_name",'',1,0,$arr,"buyer_name",'','setFilterGrid("list_view",-1);','0','',1);

	exit();	 
}

if($action=="order_no_search_popup")
{
	echo load_html_head_contents("Order No Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
     
	<script>
		
		var selected_id = new Array; var selected_name = new Array;
		
		function check_all_data()
		{
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;
			tbl_row_count = tbl_row_count - 1;

			for( var i = 1; i <= tbl_row_count; i++ )
			{
				$('#tr_'+i).trigger('click'); 
			}
		}
		
		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		
		function js_set_value( str ) {
			
			if (str!="") str=str.split("_");
			 
			toggle( document.getElementById( 'tr_' + str[0] ), '#FFFFCC' );
			 
			if( jQuery.inArray( str[1], selected_id ) == -1 ) {
				selected_id.push( str[1] );
				selected_name.push( str[2] );
				
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == str[1] ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
			}
			var id = ''; var name = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + '*';
			}
			
			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );
			
			$('#hide_order_id').val( id );
			$('#hide_order_no').val( name );
		}
	
    </script>

</head>

<body>
<div align="center">
	<form name="styleRef_form" id="styleRef_form">
		<fieldset style="width:780px;">
            <table width="770" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
            	<thead>
                    <th>Buyer</th>
                    <th>Search By</th>
                    <th id="search_by_td_up" width="170">Please Enter Order No</th>
                    <th>Shipment Date</th>
                    <th><input type="reset" name="button" class="formbutton" value="Reset"  style="width:100px;"></th> 
                    <input type="hidden" name="hide_order_no" id="hide_order_no" value="" />
                    <input type="hidden" name="hide_order_id" id="hide_order_id" value="" />
                </thead>
                <tbody>
                	<tr>
                        <td align="center">
                        	 <? 
								echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",0,"",0 );
							?>
                        </td>                 
                        <td align="center">	
                    	<?
                       		$search_by_arr=array(1=>"Order No",2=>"Style Ref",3=>"Job No");
							$dd="change_search_event(this.value, '0*0*0', '0*0*0', '../../') ";							
							echo create_drop_down( "cbo_search_by", 110, $search_by_arr,"",0, "--Select--", "",$dd,0 );
						?>
                        </td>     
                        <td align="center" id="search_by_td">				
                            <input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />	
                        </td> 
                        <td align="center">
                            <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" readonly>To
                            <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" readonly>
                        </td>	
                        <td align="center">
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+document.getElementById('txt_date_from').value+'**'+document.getElementById('txt_date_to').value, 'create_order_no_search_list_view', 'search_div', 'buyer_order_wise_dyeing_status_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
                    	</td>
                    </tr>
                    <tr>
                        <td colspan="5" height="20" valign="middle"><? echo load_month_buttons(1); ?></td>
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

if($action=="create_order_no_search_list_view")
{
	$data=explode('**',$data);
	$company_id=$data[0];
	
	if($data[1]==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		$buyer_id_cond=" and a.buyer_name=$data[1]";
	}
	
	$search_by=$data[2];
	$search_string="%".trim($data[3])."%";

	if($search_by==1) 
		$search_field="b.po_number"; 
	else if($search_by==2) 
		$search_field="a.style_ref_no"; 	
	else 
		$search_field="a.job_no";
		
	$start_date =trim($data[4]);
	$end_date =trim($data[5]);	
	
	if($start_date!="" && $end_date!="")
	{
		if($db_type==0)
		{
			$date_cond="and b.pub_shipment_date between '".change_date_format($start_date,"yyyy-mm-dd", "-")."' and '".change_date_format($end_date,"yyyy-mm-dd", "-")."'";
		}
		else
		{
			$date_cond="and b.pub_shipment_date between '".change_date_format($start_date,'','',1)."' and '".change_date_format($end_date,'','',1)."'";	
		}
	}
	else
	{
		$date_cond="";
	}
	
	if($db_type==0) $year_field="YEAR(a.insert_date) as year,"; 
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year,";
	else $year_field="";//defined Later
	
	$arr=array(0=>$company_arr,1=>$buyer_arr);
		
	$sql= "select b.id, $year_field a.job_no, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no, b.po_number, b.pub_shipment_date from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_name=$company_id and $search_field like '$search_string' $buyer_id_cond $date_cond order by b.id, b.pub_shipment_date";
		
	echo create_list_view("tbl_list_search", "Company,Buyer Name,Year,Job No,Style Ref. No, Po No, Shipment Date", "80,130,50,60,130,130","760","220",0, $sql , "js_set_value", "id,po_number", "", 1, "company_name,buyer_name,0,0,0,0,0", $arr , "company_name,buyer_name,year,job_no_prefix_num,style_ref_no,po_number,pub_shipment_date", "",'','0,0,0,0,0,0,3','',1) ;
	
   exit(); 
}

if($action=="batch_no_search_popup")
{
	echo load_html_head_contents("Batch No Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>
		var selected_id = new Array; var selected_name = new Array;
		function check_all_data()
		{
			var tbl_row_count = document.getElementById( 'tbl_list' ).rows.length;
			tbl_row_count = tbl_row_count - 1;

			for( var i = 1; i <= tbl_row_count; i++ )
			{
				$('#tr_'+i).trigger('click'); 
			}
		}
		
		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		
		function js_set_value( str ) {
			//alert(str);
			if (str!="") str=str.split("_");
			toggle( document.getElementById( 'tr_' + str[0] ), '#FFFFCC' );
			if( jQuery.inArray( str[1], selected_id ) == -1 ) {
				selected_id.push( str[1] );
				selected_name.push( str[2] );
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == str[1] ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
			}
			var id = ''; var name = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + '*';
			}
			
			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );
			
			$('#hide_batch_id').val( id );
			$('#hide_batch_no').val( name );
		}
    </script>
    </head>
    <body>
    <div align="center">
        <form name="styleRef_form" id="styleRef_form">
            <fieldset style="width:760px;">
                <table width="750" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table">
                    <thead>
                      
                        <th>Batch No </th>
                        <th>Batch Date</th>
                        <th><input type="reset" name="button" class="formbutton" value="Reset"  style="width:100px;"></th> 
                        <input type="hidden" name="hide_batch_no" id="hide_batch_no" value="" />
                        <input type="hidden" name="hide_batch_id" id="hide_batch_id" value="" />
                    </thead>
                    <tbody>
                        <tr>
                            <td align="center" id="search_by_td">				
                                <input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />	
                            </td> 
                            <td align="center">
                                <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" readonly>To
                                <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" readonly>
                            </td>	
                            <td align="center">
                                <input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('txt_search_common').value+'**'+document.getElementById('txt_date_from').value+'**'+document.getElementById('txt_date_to').value+'**'+'<? echo $orderID; ?>', 'create_batch_no_search_list_view', 'search_div', 'buyer_order_wise_dyeing_status_report_controller', 'setFilterGrid(\'tbl_list\',-1)');" style="width:100px;" />
                            </td>
                        </tr>
                        <tr>
                            <td colspan="5" height="20" valign="middle"><? echo load_month_buttons(1); ?></td>
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

if($action=="create_batch_no_search_list_view")
{
	$data=explode('**',$data);
	$company_id=$data[0];
	$batch_no=$data[1];
	$order_id=$data[4];
	$search_string="%".trim($data[3])."%";
	
	if ($batch_no=="") $batch_no_cond=""; else $batch_no_cond=" and a.batch_no in ('$batch_no') "; 
	
	//if ($order_id=="") $order_id_cond=""; else $order_id_cond=" and b.batch_no in ('$order_id') "; 
	
	$start_date =trim($data[2]);
	$end_date =trim($data[3]);	
	if($start_date!="" && $end_date!="")
	{
		if($db_type==0)
		{
			$date_cond="and a.batch_date between '".change_date_format($start_date,"yyyy-mm-dd", "-")."' and '".change_date_format($end_date,"yyyy-mm-dd", "-")."'";
		}
		else
		{
			$date_cond="and a.batch_date between '".change_date_format($start_date,'','',1)."' and '".change_date_format($end_date,'','',1)."'";	
		}
	}
	else
	{
		$date_cond="";
	}
	
	$sql="select a.id, a.batch_no, a.extention_no, a.batch_for, a.booking_no, a.color_id, a.batch_weight from pro_batch_create_mst a where a.company_id=$company_id and a.is_deleted=0 and a.entry_form!=36 and a.status_active=1 $date_cond $batch_no_cond order by a.id DESC";	
	$arr=array(2=>$color_library,4=>$batch_for);

	echo  create_list_view("tbl_list", "Batch No,Ext,Color,Booking,Batch For,Batch Weight", "120,30,100,150,100,70","700","230",0, $sql, "js_set_value", "id,batch_no", "", 1, "0,0,color_id,0,batch_for,0", $arr , "batch_no,extention_no,color_id,booking_no,batch_for,batch_weight", "",'','0','',1) ;
	exit();
}

if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	$company_name= str_replace("'","",$cbo_company_name);
	//echo $type;
	//$type = str_replace("'","",$cbo_type);
	if($type==1)
	{
		if(str_replace("'","",$hide_buyer_id)==0)
		{
			if ($_SESSION['logic_erp']["data_level_secured"]==1)
			{
				if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
			}
			else
			{
				$buyer_id_cond="";
			}
		}
		else
		{
			$buyer_id_cond=" and a.buyer_name in (".str_replace("'","",$hide_buyer_id).")";
		}
	
		$txt_job_no=str_replace("'","",$txt_job_no);
		if(trim($txt_job_no)!="") $job_no="%".trim($txt_job_no); else $job_no="%%";
		
		$cbo_year=str_replace("'","",$cbo_year);
		if(trim($cbo_year)!=0) 
		{
			if($db_type==0) 
			{
				$year_cond=" and YEAR(a.insert_date)=$cbo_year";
				$year_field=", YEAR(a.insert_date) as year ";
			}
			else if($db_type==2) 
			{
				$year_cond=" and to_char(a.insert_date,'YYYY')=$cbo_year";
				$year_field=", to_char(a.insert_date,'YYYY') as year ";
			}
			else 
			{
				$year_cond=""; $year_field="";
			}
		}
		else $year_cond="";
		
		if(str_replace("'","",trim($txt_order_no))=="")
		{
			$po_cond="";
		}
		else
		{
			if(str_replace("'","",$hide_order_id)!="")
			{
				$po_id=str_replace("'","",$hide_order_id);
				$po_cond="and b.id in(".$po_id.")";
			}
			else
			{
				$po_number=trim(str_replace("'","",$txt_order_no))."%";
				$po_cond="and b.po_number like '$po_number'";
			}
		}
	
		/*$planDateArr=array();
		
		if($db_type==0)
		{
			$planData=sql_select("select b.po_id, min(case when a.start_date!='0000-00-00' then a.start_date end) as plan_start_date, max(a.end_date) as plan_end_date from ppl_planning_info_entry_dtls a, ppl_planning_entry_plan_dtls b where a.id=b.dtls_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 group by b.po_id");
		}
		else
		{
			$planData=sql_select("select b.po_id, min(case when a.start_date is not null then a.start_date end) as plan_start_date, max(a.end_date) as plan_end_date from ppl_planning_info_entry_dtls a, ppl_planning_entry_plan_dtls b where a.id=b.dtls_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 group by b.po_id");	
		}
		foreach($planData as $planRow)
		{
			$planDateArr[$planRow[csf('po_id')]]['start']=$planRow[csf('plan_start_date')];
			$planDateArr[$planRow[csf('po_id')]]['end']=$planRow[csf('plan_end_date')];
		}*/
	
		$trans_qnty_arr=array(); $grey_prod_qnty_arr=array(); $actualDateArr=array();
		$dataArrayTrans=sql_select("select po_breakdown_id, 
								sum(CASE WHEN entry_form ='37' THEN quantity ELSE 0 END) AS grey_receive,
								sum(CASE WHEN entry_form ='68' THEN quantity ELSE 0 END) AS grey_receive_rollwise,
								sum(CASE WHEN entry_form ='15' and trans_type=5 THEN quantity ELSE 0 END) AS transfer_in_qnty_knit,
								sum(CASE WHEN entry_form ='15' and trans_type=6 THEN quantity ELSE 0 END) AS transfer_out_qnty_knit
								from order_wise_pro_details where status_active=1 and is_deleted=0 and entry_form in(15,37,68) group by po_breakdown_id");
		foreach($dataArrayTrans as $row)
		{
			$trans_qnty_arr[$row[csf('po_breakdown_id')]]=$row[csf('transfer_in_qnty_knit')]-$row[csf('transfer_out_qnty_knit')];
			$grey_prod_qnty_arr[$row[csf('po_breakdown_id')]]=$row[csf('grey_receive')]+$row[csf('grey_receive_rollwise')];
		}
			
		$sql_grey_prod="select min(a.receive_date) as prod_start_date, max(a.receive_date) as prod_end_date, c.po_breakdown_id from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(7,66) and c.entry_form in(7,66) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by c.po_breakdown_id";
		$dataArrayGreyProd=sql_select($sql_grey_prod);
		foreach($dataArrayGreyProd as $greyRow)
		{
			$actualDateArr[$greyRow[csf('po_breakdown_id')]]['prod']['start']=date("Y-m-d",strtotime($greyRow[csf('prod_start_date')]));
			$actualDateArr[$greyRow[csf('po_breakdown_id')]]['prod']['end']=date("Y-m-d",strtotime($greyRow[csf('prod_end_date')]));
		}
		
		$sql_grey_purchase="select c.po_breakdown_id, sum(c.quantity) as grey_purchase_qnty from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and a.receive_basis<>9 and a.entry_form in(37,68) and c.entry_form in(37,68) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by c.po_breakdown_id";
		$dataArrayGreyPurchase=sql_select($sql_grey_purchase);
		foreach($dataArrayGreyPurchase as $greyRow)
		{
			$greyPurchaseQntyArray[$greyRow[csf('po_breakdown_id')]]=$greyRow[csf('grey_purchase_qnty')];
		}
	
		$transData=sql_select("select c.po_breakdown_id, min(a.transfer_date) as actual_trans_start_date, max(a.transfer_date) as actual_trans_end_date from inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and a.item_category=2 and a.transfer_criteria=4 and c.trans_type=5 and c.entry_form=15 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by c.po_breakdown_id");
		foreach($transData as $transRow)
		{
			$actualDateArr[$transRow[csf('po_breakdown_id')]]['trans']['start']=date("Y-m-d",strtotime($transRow[csf('actual_trans_start_date')]));
			$actualDateArr[$transRow[csf('po_breakdown_id')]]['trans']['end']=date("Y-m-d",strtotime($transRow[csf('actual_trans_end_date')]));
		}
		
		$booking_qnty_arr=return_library_array( "select b.po_break_down_id, sum(b.fin_fab_qnty) as qnty from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.item_category in(2) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.po_break_down_id", "po_break_down_id", "qnty"  );
		
		$yarn_iss_arr=array();  $start_prod_date_arr=array();
		$yarn_iss_data=sql_select("select b.po_breakdown_id, a.transaction_date, sum(b.quantity) as iss_qnty from inv_transaction a, order_wise_pro_details b where a.id=b.trans_id and a.item_category=13 and a.transaction_type=2 and b.entry_form in(16,61) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.po_breakdown_id, a.transaction_date");
		foreach($yarn_iss_data as $row)
		{
			$trans_date=date("Y-m-d",strtotime($row[csf('transaction_date')]));
			$yarn_iss_arr[$row[csf('po_breakdown_id')]][$trans_date]=$row[csf('iss_qnty')];
		}
		
		$date_yarn_iss_data=sql_select("select b.po_breakdown_id, min(a.transaction_date) as min_prod_date, max(a.transaction_date) as max_prod_date, sum(b.quantity) as iss_qnty from inv_transaction a, order_wise_pro_details b where a.id=b.trans_id and a.item_category=13 and a.transaction_type=2 and b.entry_form in(16,61) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.po_breakdown_id");
		foreach($date_yarn_iss_data as $row)
		{
			$start_prod_date_arr[$row[csf('po_breakdown_id')]]['min']=date("Y-m-d",strtotime($row[csf('min_prod_date')]));
			$start_prod_date_arr[$row[csf('po_breakdown_id')]]['max']=date("Y-m-d",strtotime($row[csf('max_prod_date')]));
		}
		//print_r($start_prod_date_arr);
		
		$fin_prod_date_arr=array(); $fin_date_arr=array(); $finish_prod_arr=array();
		$prodfinData=sql_select("select c.po_breakdown_id, a.receive_date, sum(c.quantity) as prod_qnty from inv_receive_master a, pro_finish_fabric_rcv_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(7,66) and c.entry_form in(7,66) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by c.po_breakdown_id, a.receive_date");
		foreach($prodfinData as $row)
		{
			$prodfinData=date("Y-m-d",strtotime($row[csf('receive_date')]));
			$fin_prod_date_arr[$row[csf('po_breakdown_id')]][$prodfinData]=$row[csf('prod_qnty')];
			$finish_prod_arr[$row[csf('po_breakdown_id')]]+=$row[csf('prod_qnty')];
		}
		//print_r($fin_prod_date_arr);
		$prodfinDtData=sql_select("select c.po_breakdown_id, min(a.receive_date) as min_prod_date, max(a.receive_date) as max_prod_date, sum(c.quantity) as prod_qnty from inv_receive_master a, pro_finish_fabric_rcv_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(7,66) and c.entry_form in(7,66) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by c.po_breakdown_id");
		foreach($prodfinDtData as $row)
		{
			$fin_date_arr[$row[csf('po_breakdown_id')]]['min']=date("Y-m-d",strtotime($row[csf('min_prod_date')]));
			$fin_date_arr[$row[csf('po_breakdown_id')]]['max']=date("Y-m-d",strtotime($row[csf('max_prod_date')]));
		}
		//print_r($fin_date_arr);
		
		
		$prod_arr=array();  $dye_prod_date_arr=array();
		$prodData=sql_select("select c.po_id as po_breakdown_id, a.process_end_date as receive_date, sum(c.batch_qnty) as prod_qnty from pro_fab_subprocess a, pro_batch_create_mst b, pro_batch_create_dtls c where a.batch_id=b.id and a.load_unload_id=2 and a.entry_form=35 and a.result=1 and b.id=c.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by c.po_id, a.process_end_date");
		foreach($prodData as $row)
		{
			$trans_date=date("Y-m-d",strtotime($row[csf('receive_date')]));
			$prod_arr[$row[csf('po_breakdown_id')]][$trans_date]=$row[csf('prod_qnty')];
		}
		//print_r($prod_arr);
		
		$prodDateData=sql_select("select c.po_id as po_breakdown_id, min(a.process_end_date) as min_prod_date, max(a.process_end_date) as max_prod_date, sum(c.batch_qnty) as prod_qnty from pro_fab_subprocess a, pro_batch_create_mst b, pro_batch_create_dtls c where a.batch_id=b.id and a.load_unload_id=2 and a.entry_form=35 and a.result=1 and b.id=c.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by c.po_id");
		foreach($prodDateData as $row)
		{
			$dye_prod_date_arr[$row[csf('po_breakdown_id')]]['min']=date("Y-m-d",strtotime($row[csf('min_prod_date')]));
			$dye_prod_date_arr[$row[csf('po_breakdown_id')]]['max']=date("Y-m-d",strtotime($row[csf('max_prod_date')]));
		}
		
		$prod_trans_arr=array();
		$transData=sql_select("select c.po_breakdown_id, a.transfer_date, sum(case when c.trans_type=5 then c.quantity else 0 end) as in_qnty, sum(case when c.trans_type=6 then c.quantity else 0 end) as out_qnty from inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and a.item_category=13 and a.transfer_criteria=4 and c.entry_form=13 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by c.po_breakdown_id, a.transfer_date");
		foreach($transData as $row)
		{
			$trans_date=date("Y-m-d",strtotime($row[csf('transfer_date')]));
			$prod_trans_arr[$row[csf('po_breakdown_id')]][$trans_date]=$row[csf('in_qnty')]-$row[csf('out_qnty')];
		}
		
		$start_date=str_replace("'","",trim($txt_date_from));
		$end_date=str_replace("'","",trim($txt_date_to));
		
		$today=date("Y-m-d");
		if($txt_job_no!="") $job_no_cond=" and a.job_no_prefix_num='$txt_job_no'"; else $job_no_cond="";
		
		if($start_date!="" && $end_date!="")
		{
			$booking_qnty_arr=return_library_array("select b.po_break_down_id as po_id, sum(b.fin_fab_qnty) as qnty from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.item_category in(2,13) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.po_break_down_id",'po_id','qnty');
			$tna_search=1;
			$sql="select a.job_no_prefix_num, a.job_no, a.company_name, a.buyer_name, b.id as po_id, b.po_number, b.pub_shipment_date, t.task_start_date, t.task_finish_date $year_field from wo_po_details_master a, wo_po_break_down b, tna_process_mst t where a.job_no=b.job_no_mst and b.id=t.po_number_id and a.company_name=$company_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and t.is_deleted=0 and t.status_active=1 and t.task_number=61 and t.task_start_date between '$start_date' and '$end_date' $buyer_id_cond $job_no_cond $po_cond $year_cond group by b.id, b.po_number, b.pub_shipment_date, a.job_no, a.job_no_prefix_num, a.company_name, a.buyer_name, a.insert_date, t.task_start_date, t.task_finish_date order by a.buyer_name, a.job_no";
		}
		else
		{
			$tna_array=array();
			$tna_sql=sql_select("select po_number_id, task_start_date, task_finish_date from tna_process_mst where task_number=61 and is_deleted=0 and status_active=1");
			foreach($tna_sql as $row)
			{
				$tna_array[$row[csf('po_number_id')]]['start_d']=$row[csf('task_start_date')];
				$tna_array[$row[csf('po_number_id')]]['finish_d']=$row[csf('task_finish_date')];
			}
			$tna_search=0;
			$sql="select a.job_no_prefix_num, a.job_no, a.company_name, a.buyer_name, b.id as po_id, b.po_number, b.pub_shipment_date, sum(d.fin_fab_qnty) as qnty $year_field from wo_po_details_master a, wo_po_break_down b, wo_booking_mst c, wo_booking_dtls d where a.job_no=b.job_no_mst and b.id=d.po_break_down_id and a.job_no=c.job_no and c.booking_no=d.booking_no and a.company_name=$company_name and c.item_category in(2,13) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and d.fin_fab_qnty>0 $buyer_id_cond $job_no_cond $po_cond $year_cond group by b.id, b.po_number, b.pub_shipment_date, a.job_no, a.job_no_prefix_num, a.company_name, a.buyer_name, a.insert_date order by a.buyer_name, a.job_no";
		}
		//echo $sql;//die;
		ob_start();
		?>
		<div style="width:100%; margin-top:5px;" align="center">
			<fieldset style="width:1270px;">
            	<table width="1250" cellpadding="0" cellspacing="0"> 
                    <tr class="form_caption">
                        <td colspan="16" align="center"><strong><? echo $report_title; ?></strong></td> 
                    </tr>
                </table>
				<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1250" class="rpt_table" align="left">
					<thead>
                    	<tr>
                            <th width="40" rowspan="2">SL</th>
                            <th width="55" rowspan="2">Job No</th>
                            <th width="40" rowspan="2">Year</th>
                            <th width="100" rowspan="2">Order No</th>
                            <th width="70" rowspan="2">Shipment Date</th>
                            <th width="180" colspan="2">TNA</th>
                            <th width="180" colspan="2">Actual</th>
                            <th width="90" rowspan="2">Required Qty<br/><font style="font-size:9px; font-weight:100">(As Per Booking)</font></th>
                            <th width="80" rowspan="2">Finish Production</th>
                            <th width="50" rowspan="2">Prod. %</th>
                            <th width="80" rowspan="2">Grey Receive/ Purchase</th>
                            <th width="80" rowspan="2">Net Transfer</th>
                            <th width="90" rowspan="2">Finish Available</th>
                            <th rowspan="2">Balance</th>
                        </tr>
                        <tr>
                        	<th width="80">Start Date</th>
                            <th width="100">End Date</th>
                            <th width="80">Start Date</th>
                            <th width="100">End Date</th>
                        </tr>
					</thead>
				</table>
				<div style="width:1270px; overflow-y:scroll; max-height:450px;" id="scroll_body" align="left">
					<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1250" class="rpt_table" id="tbl_list_search" align="left">
						<? 
							$i=1; $buyer_array=array(); $po_array=array();
							$nameArray=sql_select( $sql );
							foreach ($nameArray as $row)
							{
								$tna_start_date=''; $tna_end_date='';
								if($tna_search==0)
								{
									if($tna_array[$row[csf('po_id')]]['start_d']!="0000-00-00" && $tna_array[$row[csf('po_id')]]['start_d']!="")
									{
										$tna_start_date=change_date_format($tna_array[$row[csf('po_id')]]['start_d']);
									}
									
									if($tna_array[$row[csf('po_id')]]['finish_d']!="0000-00-00" && $tna_array[$row[csf('po_id')]]['finish_d']!="")
									{
										$tna_end_date=change_date_format($tna_array[$row[csf('po_id')]]['finish_d']);
									}
									$req_qnty=$row[csf('qnty')];
								}
								else
								{
									if($row[csf('task_start_date')]!="0000-00-00" && $row[csf('task_start_date')]!="")
									{
										$tna_start_date=change_date_format($row[csf('task_start_date')]);
									}
									
									if($row[csf('task_finish_date')]!="0000-00-00" && $row[csf('task_finish_date')]!="")
									{
										$tna_end_date=change_date_format($row[csf('task_finish_date')]);
									}
									
									$req_qnty=$booking_qnty_arr[$row[csf('po_id')]];
								}
								//echo $tna_end_date;
								if($req_qnty>0)
								{
									if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
									
									if(!in_array($row[csf('buyer_name')], $buyer_array))
									{
										if($i!=1)
										{
										?>
											<tr bgcolor="#CCCCCC">
												<td colspan="9" align="right"><b>Buyer Total</b></td>
												<td align="right"><b><? echo number_format($buyer_req_qnty,2,'.',''); ?></b></td>
												<td align="right"><b><? echo number_format($buyer_prod_qnty,2,'.',''); ?></b></td>
                                                <td align="right">&nbsp;<b><? //echo number_format($tot_prod_percent,2,'.',''); ?></b></td>
												<td align="right"><b><? echo number_format($buyer_recv_qnty,2,'.',''); ?></b></td>
												<td align="right"><b><? echo number_format($buyer_net_trans_qnty,2,'.',''); ?></b></td>
												<td align="right"><b><? echo number_format($buyer_available_qnty,2,'.',''); ?></b></td>
												<td align="right"><b><? echo number_format($buyer_balance,2,'.',''); ?></b></td>
											</tr>
										<?
											$buyer_req_qnty = 0;
											$buyer_prod_qnty = 0;
											$buyer_recv_qnty = 0;
											$buyer_net_trans_qnty = 0;
											$buyer_available_qnty = 0;
											$buyer_balance = 0;
										}
									?>
										<tr bgcolor="#EFEFEF">
											<td colspan="16">
												<b>Buyer Name:- <?php echo $buyer_arr[$row[csf('buyer_name')]]; ?></b>
											</td>
										</tr>
									<?
										$buyer_array[]=$row[csf('buyer_name')];
									}
									
									$prod_qnty=$finish_prod_arr[$row[csf('po_id')]];//$grey_prod_qnty_arr[$row[csf('po_id')]];
									$recv_qnty=$greyPurchaseQntyArray[$row[csf('po_id')]];
									$net_trans_qnty=$trans_qnty_arr[$row[csf('po_id')]];
									$available_qnty=$prod_qnty+$recv_qnty+$net_trans_qnty;
									$balance_qnty=$req_qnty-$available_qnty;
									$tot_prod_percent=$prod_qnty/$req_qnty*100;
									
									$actual_start_date=''; $actual_end_date='';
									
									$prod_start_date=$actualDateArr[$row[csf('po_id')]]['prod']['start'];
									$prod_end_date=$actualDateArr[$row[csf('po_id')]]['prod']['end'];
									
									$min_prod_date=$start_prod_date_arr[$row[csf('po_id')]]['min'];
									$max_prod_date=$start_prod_date_arr[$row[csf('po_id')]]['max'];
									
									$min_fin_prod_date=$fin_date_arr[$row[csf('po_id')]]['min'];
									$max_fin_prod_date=$fin_date_arr[$row[csf('po_id')]]['max'];

									$min_dye_prod_date=$dye_prod_date_arr[$row[csf('po_id')]]['min'];
									$max_dye_prod_date=$dye_prod_date_arr[$row[csf('po_id')]]['max'];

									//$trans_start_date=$actualDateArr[$row[csf('po_id')]]['trans']['start'];
									//$trans_end_date=$actualDateArr[$row[csf('po_id')]]['trans']['end'];
									
									//$actual_start_date=$min_dye_prod_date;
									if($min_fin_prod_date<$min_dye_prod_date) $actual_start_date=$min_dye_prod_date; else $actual_start_date=$min_fin_prod_date;
									
									if($max_dye_prod_date>$max_fin_prod_date) $actual_end_date=$max_dye_prod_date; else $actual_end_date=$max_fin_prod_date;
									
									//if($actual_start_date!="") $actual_start_date=change_date_format($min_prod_date);
									//if($actual_end_date!="") $actual_end_date=change_date_format($max_prod_date);
									
									//$actual_start_date=$actual_start_date;
									//$actual_end_date=$actual_end_date;

									$pub_shipment_date=change_date_format($row[csf('pub_shipment_date')]);
									
									$shipment_date=change_date_format($row[csf('pub_shipment_date')], "yyyy-mm-dd", "-");
									$yet_to_ship_days=datediff( d, $today, $shipment_date);
									
									$booking_qnty=$booking_qnty_arr[$row[csf('po_id')]];
									$dateDiff_trans=datediff( d, $actual_start_date, $tna_end_date);
									
									$tna_wise_iss_qnty=$yarn_iss_arr[$row[csf('po_id')]][$tna_start_date];
									
									$tna_wise_prod_qnty=$prod_arr[$row[csf('po_id')]][$tna_start_date];
									$tna_wise_prod_trans_qnty=$prod_trans_arr[$row[csf('po_id')]][$tna_start_date];
									$tna_wise_grey_availlable_qnty=$tna_wise_prod_qnty+$tna_wise_prod_trans_qnty;
									
									if($dateDiff_trans>0)
									{
										for($k=0;$k<$dateDiff_trans;$k++)
										{
											$newdate=add_date($min_prod_date,$k);
											$tna_wise_iss_qnty+=$yarn_iss_arr[$row[csf('po_id')]][$newdate];
											$tna_wise_grey_availlable_qnty+=$prod_arr[$row[csf('po_id')]][$newdate]+$prod_trans_arr[$row[csf('po_id')]][$newdate];
											$tna_wise_finish_availlable_qnty+=$fin_prod_date_arr[$row[csf('po_id')]][$newdate]+$prod_trans_arr[$row[csf('po_id')]][$newdate];
										}
									}
									$tna_wise_iss_perc=number_format(($tna_wise_iss_qnty/$booking_qnty)*100,2);
									$tna_wise_prod_perc=number_format(($tna_wise_grey_availlable_qnty/$booking_qnty)*100,2);
									$tna_wise_fin_prod_perc=number_format(($tna_wise_finish_availlable_qnty/$booking_qnty)*100,2);
	
									$dateDiff_actual=datediff( d, $actual_start_date, $tna_end_date);
									//$actual_iss_qnty=$yarn_iss_arr[$row[csf('po_id')]][$actual_start_date];
									
									//$actual_prod_qnty=$prod_arr[$row[csf('po_id')]][$actual_start_date];
									//$actual_wise_prod_trans_qnty=$prod_trans_arr[$row[csf('po_id')]][$actual_start_date];
									//$actual_grey_availlable_qnty=$actual_prod_qnty+$actual_wise_prod_trans_qnty;
									//echo $actual_start_date;
									if($dateDiff_actual>0)
									{
										for($k=0;$k<$dateDiff_actual;$k++)
										{
											$newdate=add_date($actual_start_date,$k);
											$actual_iss_qnty+=$yarn_iss_arr[$row[csf('po_id')]][$newdate];
											$actual_grey_availlable_qnty+=$prod_arr[$row[csf('po_id')]][$newdate]+$prod_trans_arr[$row[csf('po_id')]][$newdate];
											$actual_finish_availlable_qnty+=$fin_prod_date_arr[$row[csf('po_id')]][$newdate]+$prod_trans_arr[$row[csf('po_id')]][$newdate];
										}
									}
									//echo $actual_grey_availlable_qnty;
									$actual_iss_perc=number_format(($actual_iss_qnty/$booking_qnty)*100,2);
									$actual_prod_perc=number_format(($actual_grey_availlable_qnty/$booking_qnty)*100,2);
									$actual_fin_prod_perc=number_format(($actual_finish_availlable_qnty/$booking_qnty)*100,2);
									
									$tdColor_tna_yarn=''; $tdColor_plan_yarn=''; $tdColor_actual_yarn='';
									$tdColor_tna_prod=''; $tdColor_plan_prod=''; $tdColor_actual_prod='';
									
									$tna_end_date_for_comp=change_date_format($tna_end_date, "yyyy-mm-dd", "-");
									//$plan_end_date_for_comp=change_date_format($plan_end_date, "yyyy-mm-dd", "-");
									$actual_end_date_for_comp=change_date_format($actual_end_date, "yyyy-mm-dd", "-");
									
									if($today>$tna_end_date_for_comp) 
									{
										if($tna_wise_iss_qnty<$booking_qnty) $tdColor_tna_yarn='red';
										if($tna_wise_grey_availlable_qnty<$booking_qnty) $tdColor_tna_prod='red';
									}
									
									
									if($actual_end_date_for_comp>$tna_end_date_for_comp) 
									{
										if($actual_iss_qnty<$booking_qnty) $tdColor_actual_yarn='red';
										if($actual_grey_availlable_qnty<$booking_qnty) $tdColor_actual_prod='red';
									}
									
									?>
									<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>"> 
										<td width="40"><? echo $i; ?></td>
										<td width="55">&nbsp;&nbsp;<? echo $row[csf('job_no_prefix_num')]; ?></td>
										<td width="40" align="center"><? echo $row[csf('year')]; ?>&nbsp;</td>
										<td width="100"><p><? echo $row[csf('po_number')]; ?></p></td>
                                        <td width="70" align="center"><p><a href="##" onClick="openmypage_greyAvailable('<? echo $row[csf('po_id')]; ?>','grey_yarn_info','<? echo $tna_start_date."_".$tna_end_date."_".$actual_start_date."_".$actual_end_date."_".$pub_shipment_date; ?>')"><? echo $pub_shipment_date; ?></a></p></td>
										<td width="80" align="center">&nbsp;<? echo $tna_start_date; ?></td>
										<td width="100" align="center">&nbsp;<? echo $tna_end_date; ?></td>
                                        <td width="80" align="center">&nbsp;<? echo change_date_format($actual_start_date); ?></td>
										<td width="100" align="center">&nbsp;<? echo change_date_format($actual_end_date); ?></td>
										<td align="right" width="90"><? echo number_format($req_qnty,2,'.',''); ?></td>
										<td align="right" width="80"><? echo number_format($prod_qnty,2,'.',''); ?></td>
                                        <td align="right" width="50"><? echo number_format($tot_prod_percent,2,'.',''); ?></td>
										<td align="right" width="80"><? echo number_format($recv_qnty,2,'.',''); ?></td>
										<td align="right" width="80"><? echo number_format($net_trans_qnty,2,'.',''); ?></td>
										<td align="right" width="90"><a href="##" onClick="openmypage_greyAvailable('<? echo $row[csf('po_id')]; ?>','grey_available','')"><? echo number_format($available_qnty,2,'.',''); ?></a></td>
										<td align="right"><? echo number_format($balance_qnty,2,'.',''); ?></td>
									</tr>
                                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_2<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2<? echo $i; ?>"> 
										<td width="40">&nbsp;</td>
										<td width="55">&nbsp;&nbsp;</td>
										<td width="40" align="center">&nbsp;</td>
										<td width="100">&nbsp;</td>
                                        <td width="70" align="center" rowspan="3">Yet To Ship:<br><? echo $yet_to_ship_days." days"; ?></a>&nbsp;</td>
										<td width="80" align="center">Grey Delivered:</td>
										<td width="100" align="right" bgcolor="<? echo $tdColor_tna_yarn; ?>"><? echo number_format($tna_wise_iss_qnty,2)." Kg/ ".$tna_wise_iss_perc."%"; ?></td>
                                        <td width="80" align="center">Grey Delivered:</td>
										<td width="100" align="right" bgcolor="<? echo $tdColor_actual_yarn; ?>"><? echo number_format($actual_iss_qnty,2)." Kg/ ".$actual_iss_perc."%"; ?></td>
										<td align="right" width="90">&nbsp;</td>
										<td align="right" width="80">&nbsp;</td>
                                        <td align="right" width="50">&nbsp;</td>
										<td align="right" width="80">&nbsp;</td>
										<td align="right" width="80">&nbsp;</td>
										<td align="right" width="90">&nbsp;</td>
										<td align="right">&nbsp;</td>
									</tr>
                                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_3<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_3<? echo $i; ?>"> 
										<td width="40">&nbsp;</td>
										<td width="55">&nbsp;&nbsp;</td>
										<td width="40" align="center">&nbsp;</td>
										<td width="100">&nbsp;</td>
										<td width="80" align="center">Dyeing Production:</td>
										<td width="100" align="right" bgcolor="<? echo $tdColor_tna_prod; ?>"><? echo number_format($tna_wise_grey_availlable_qnty,2)." Kg/ ".$tna_wise_prod_perc."%"; ?></td>
                                        <td width="80" align="center">Dyeing Production:</td>
										<td width="100" align="right" bgcolor="<? //echo $tdColor_actual_prod; ?>"><? echo number_format($actual_grey_availlable_qnty,2)." Kg/ ".$actual_prod_perc."%"; ?></td>
										<td align="right" width="90">&nbsp;</td>
										<td align="right" width="80">&nbsp;</td>
                                        <td align="right" width="50">&nbsp;</td>
										<td align="right" width="80">&nbsp;</td>
										<td align="right" width="80">&nbsp;</td>
										<td align="right" width="90">&nbsp;</td>
										<td align="right">&nbsp;</td>
									</tr>
                                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_4<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_4<? echo $i; ?>"> 
										<td width="40">&nbsp;</td>
										<td width="55">&nbsp;&nbsp;</td>
										<td width="40" align="center">&nbsp;</td>
										<td width="100">&nbsp;</td>
										<td width="80" align="center">Finish Production:</td>
										<td width="100" align="right" bgcolor="<? echo $tdColor_tna_prod; ?>"><? echo number_format($tna_wise_finish_availlable_qnty,2)." Kg/ ".$tna_wise_fin_prod_perc."%"; ?></td>
                                        <td width="80" align="center">Finish Production:</td>
										<td width="100" align="right" bgcolor="<? //echo $tdColor_actual_prod; ?>"><? echo number_format($actual_finish_availlable_qnty,2)." Kg/ ".$actual_fin_prod_perc."%"; ?></td>
										<td align="right" width="90">&nbsp;</td>
										<td align="right" width="80">&nbsp;</td>
                                        <td align="right" width="50">&nbsp;</td>
										<td align="right" width="80">&nbsp;</td>
										<td align="right" width="80">&nbsp;</td>
										<td align="right" width="90">&nbsp;</td>
										<td align="right">&nbsp;</td>
									</tr>
									<?
									
									$total_req_qnty+=$req_qnty;
									$total_prod_qnty+=$prod_qnty;
									$total_recv_qnty+=$recv_qnty;
									$total_net_trans_qnty+=$net_trans_qnty;
									$total_available_qnty+=$available_qnty;
									$total_balance+=$balance_qnty;
									
									$buyer_req_qnty+=$req_qnty;
									$buyer_prod_qnty+=$prod_qnty;
									$buyer_recv_qnty+=$recv_qnty;
									$buyer_net_trans_qnty+=$net_trans_qnty;
									$buyer_available_qnty+=$available_qnty;
									$buyer_balance+=$balance_qnty;
		
									$i++;
								}
							}
							
							if($i>1)
							{
								$total_prod_percent_buyer=$buyer_req_qnty/$buyer_prod_qnty*100;
							?>
								<tr bgcolor="#CCCCCC" id="tr_<? echo $i; ?>">
									<td colspan="9" align="right"><b>Buyer Total</b></td>
									<td align="right"><b><? echo number_format($buyer_req_qnty,2,'.',''); ?></b></td>
                                    <td align="right"><b><? echo number_format($buyer_prod_qnty,2,'.',''); ?></b></td>
                                    <td align="right"><b>&nbsp;<? //echo number_format($total_prod_percent_buyer,2,'.',''); ?></b></td>
                                    <td align="right"><b><? echo number_format($buyer_recv_qnty,2,'.',''); ?></b></td>
                                    <td align="right"><b><? echo number_format($buyer_net_trans_qnty,2,'.',''); ?></b></td>
                                    <td align="right"><b><? echo number_format($buyer_available_qnty,2,'.',''); ?></b></td>
                                    <td align="right"><b><? echo number_format($buyer_balance,2,'.',''); ?></b></td>
								</tr>
							<?
							}
							$total_grand_prod_percent=$total_req_qnty/$total_prod_qnty*100;
						?>
						<tfoot>
							<th colspan="9" align="right">Grand Total</th>
							<th align="right"><? echo number_format($total_req_qnty,2,'.',''); ?></th>
							<th align="right"><? echo number_format($total_prod_qnty,2,'.',''); ?></th>
                            <th align="right">&nbsp;<? //echo number_format($total_grand_prod_percent,2,'.',''); ?></th>
                            <th align="right"><? echo number_format($total_recv_qnty,2,'.',''); ?></th>
                            <th align="right"><? echo number_format($total_net_trans_qnty,2,'.',''); ?></th>
                            <th align="right"><? echo number_format($total_available_qnty,2,'.',''); ?></th>
							<th align="right"><? echo number_format($total_balance,2,'.',''); ?></th>
						</tfoot>
					</table>
				</div>
			</fieldset>
		</div>
	<?	
	}
	else if ($type==2)
	{
		?>
		<div>
        <table width="2580px" cellpadding="0" cellspacing="0" id="caption" align="center">
            <tr>
               <td align="center" width="100%" colspan="31" class="form_caption" ><strong style="font-size:18px"><? echo $company_arr[$company_name];?></strong></td>
            </tr> 
            <tr>  
               <td align="center" width="100%" colspan="31" class="form_caption" ><strong style="font-size:14px"><? echo $report_title; ?> (Fabric Wise)</strong></td>
            </tr>
        </table>
        <table width="2580" border="1" rules="all" class="rpt_table" cellpadding="0" cellspacing="0">
        	<thead>
                <th width="30">SL</th>
                <th width="70">Buyer</th>
                <th width="60">Job No</th>
                <th width="60">Year</th>
                <th width="120">Style Ref.</th>
                <th width="120">Order No</th>
                <th width="70">Ship Date</th>
                <th width="70">TNA Start Date</th>
                <th width="70">TNA End Date</th>
                <th width="200">Fabric Const. Comp.</th>
                <th width="90">Fabric Color</th>
                <th width="80">Finish Req. Qty.</th>
                <th width="60">Fin. Dia</th>
                <th width="60">Fin. Gsm</th>
                <th width="90">Dia/Width Type</th>
                <th width="80">Batch</th>
                <th width="40">Ext.</th>
                <th width="70">Batch Date</th>
                <th width="90">Batch Color</th>
                <th width="80">Batch Qty.</th>
                <th width="70">Lot</th>
                <th width="60">LTB/BTB</th>                
                <th width="80">Unload Date & Time</th>
                <th width="60">Time Used</th>
                <th width="120">Re-process</th>
                <th width="100">RFT</th>
                <th width="70">Dyeing MC No</th>
                <th width="80">Issue to Store (Kg) & %</th>
                <th width="90">Balance (Kg) & %</th>
                <th width="80">Finish Stock (Kg)</th>
                <th>Issue to Cutting (Kg) & %</th>
            </thead>
        </table>
	<div style="width:2600px; overflow-y:scroll; max-height:275px;" id="scroll_body" >
        <table width="2580" border="1" rules="all" class="rpt_table" cellpadding="0" cellspacing="0" id="table_body">
        <?
		$buyer_short_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
		
		$tna_array=array();
		$tna_sql=sql_select("select po_number_id, task_start_date, task_finish_date from tna_process_mst where task_number=61 and is_deleted=0 and status_active=1");
		foreach($tna_sql as $row)
		{
			$tna_array[$row[csf('po_number_id')]]['start_d']=$row[csf('task_start_date')];
			$tna_array[$row[csf('po_number_id')]]['finish_d']=$row[csf('task_finish_date')];
		}
		
		$src_batch_no= str_replace("'","",$txt_batch_no);
		if($src_batch_no!="") $batch_cond=" and b.batch_no='$src_batch_no' "; else $batch_cond="";
		$batch_arr=array(); $batch_prod_arr=array();
		$sql_batch="select b.id, b.batch_no, b.extention_no, b.batch_date, b.color_id, b.booking_no, b.color_range_id, b.dur_req_hr, b.dur_req_min, c.prod_id, c.po_id, c.width_dia_type, sum(c.batch_qnty) as batch_qnty
		from pro_batch_create_mst b, pro_batch_create_dtls c where b.entry_form=0 and b.id=c.mst_id and b.company_id='$company_name' and b.status_active=1 and b.is_deleted=0 $batch_cond
		group by b.id, b.batch_no, b.extention_no, b.batch_date, b.color_id, b.booking_no, b.color_range_id, b.dur_req_hr, b.dur_req_min, c.prod_id, c.po_id, c.width_dia_type order by c.po_id, b.color_id";
		$batch_result=sql_select($sql_batch);
		foreach ($batch_result as $row)
		{
			$batch_arr[$row[csf('id')]][$row[csf('po_id')]][$row[csf('prod_id')]]['batch_no']=$row[csf('batch_no')];
			$batch_arr[$row[csf('id')]][$row[csf('po_id')]][$row[csf('prod_id')]]['color_id']=$row[csf('color_id')];
			$batch_arr[$row[csf('id')]][$row[csf('po_id')]][$row[csf('prod_id')]]['width_dia_type']=$row[csf('width_dia_type')];
			$batch_arr[$row[csf('id')]][$row[csf('po_id')]][$row[csf('prod_id')]]['batch_no']=$row[csf('batch_no')];
			$batch_arr[$row[csf('id')]][$row[csf('po_id')]][$row[csf('prod_id')]]['ext']=$row[csf('extention_no')];
			$batch_arr[$row[csf('id')]][$row[csf('po_id')]][$row[csf('prod_id')]]['batch_date']=$row[csf('batch_date')];
			$batch_arr[$row[csf('id')]][$row[csf('po_id')]][$row[csf('prod_id')]]['batch_qnty']=$row[csf('batch_qnty')];
			$batch_arr[$row[csf('id')]][$row[csf('po_id')]][$row[csf('prod_id')]]['id']=$row[csf('id')];
			
			$batch_prod_arr[$row[csf('po_id')]][$row[csf('color_id')]][$row[csf('id')]][$row[csf('prod_id')]]=$row[csf('prod_id')];
		}
		//var_dump($batch_prod_arr);
		$fabric_desc_arr=array();
		$prodData=sql_select("select id, product_name_details, gsm, dia_width, lot from product_details_master");
		foreach($prodData as $row)
		{
			$fabric_desc_arr[$row[csf('id')]]['desc']=$row[csf('product_name_details')];
			$fabric_desc_arr[$row[csf('id')]]['gsm']=$row[csf('gsm')];
			$fabric_desc_arr[$row[csf('id')]]['dia']=$row[csf('dia_width')];
			//$fabric_desc_arr[$row[csf('id')]]['lot']=$row[csf('lot')];
		}
		
		if($db_type==0)
		{
			$lot_cond="group_concat(a.yarn_lot)";
		}
		else if($db_type==2)
		{
			$lot_cond="listagg(cast(a.yarn_lot as varchar2(4000)),',') within group (order by a.yarn_lot)";
		}
		$production_arr=array();//$yarn_lot="";
		//echo "select prod_id, yarn_lot as yarn_lot from pro_grey_prod_entry_dtls where status_active=1 and is_deleted=0";
		$productionData=sql_select("select a.prod_id, $lot_cond as yarn_lot, b.po_breakdown_id from pro_grey_prod_entry_dtls a, order_wise_pro_details b where a.id=b.dtls_id  and b.entry_form=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.prod_id, b.po_breakdown_id");//and  a.prod_id in (4837,4848)
		foreach($productionData as $row)
		{
			
			//if($yarn_lot=="") $yarn_lot=$row[csf('yarn_lot')]; else $yarn_lot.='**'.$row[csf('yarn_lot')];
			$lot=implode(",",array_unique(explode(',',$row[csf('yarn_lot')])));
			$production_arr[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]]['lot']=$lot;
		}
		//var_dump($production_arr);
		$bookingDataArr=array();
		$sql_wo=sql_select("select b.po_break_down_id, b.fabric_color_id, b.construction, b.copmposition, b.gsm_weight, b.dia_width, sum(b.fin_fab_qnty) as qnty from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.company_id=$company_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.po_break_down_id, b.fabric_color_id, b.construction, b.copmposition, b.gsm_weight, b.dia_width");
		foreach($sql_wo as $woRow)
		{
			$bookingDataArr[$woRow[csf('po_break_down_id')]][trim($woRow[csf('construction')])][trim($woRow[csf('copmposition')])][$woRow[csf('gsm_weight')]][$woRow[csf('dia_width')]][$woRow[csf('fabric_color_id')]]=$woRow[csf('qnty')];
		}
		
		$machine_arr=array();
		$machine_sql=sql_select("select id, machine_no, prod_capacity from lib_machine_name where status_active=1 and is_deleted=0");
		foreach($machine_sql as $row)
		{
			$machine_arr[$row[csf('id')]]['mc_no']=$row[csf('machine_no')];
			$machine_arr[$row[csf('id')]]['capacity']=$row[csf('prod_capacity')];
		}
		
		$dye_prod_load_arr=array();
		$sql_dye_load_prod=sql_select("select batch_id, process_end_date, end_hours, end_minutes from pro_fab_subprocess where entry_form=35 and load_unload_id=1 and company_id='$company_name' and status_active=1 and is_deleted=0 order by id");
		foreach($sql_dye_load_prod as $row)
		{
			$dye_prod_load_arr[$row[csf('batch_id')]]['end_date']=$row[csf('process_end_date')];
			$dye_prod_load_arr[$row[csf('batch_id')]]['time']=$row[csf('end_hours')].':'.$row[csf('end_minutes')];
		}
		
		$dye_prod_arr=array();
		$sql_dye_prod=sql_select("select batch_id, production_date, process_id, ltb_btb_id, machine_id, end_hours, end_minutes, result, remarks from pro_fab_subprocess where entry_form=35 and load_unload_id=2 and company_id='$company_name' and status_active=1 and is_deleted=0 order by id");
		foreach($sql_dye_prod as $row)
		{
			$unload_time=$row[csf('end_hours')].':'.$row[csf('end_minutes')];
			$load_date=$dye_prod_load_arr[$row[csf('batch_id')]]['end_date'];
			$load_time=$dye_prod_load_arr[$row[csf('batch_id')]]['time'];
			
			$new_date_time_unload=($row[csf('production_date')].' '.$unload_time.':'.'00');
			$new_date_time_load=($load_date.' '.$load_time.':'.'00');
			$total_time=datediff(n,$new_date_time_load,$new_date_time_unload);
							
			$dye_prod_arr[$row[csf('batch_id')]]['ltb_btb']=$row[csf('ltb_btb_id')];
			$dye_prod_arr[$row[csf('batch_id')]]['date']=$row[csf('production_date')];
			$dye_prod_arr[$row[csf('batch_id')]]['time']=$unload_time;
			$dye_prod_arr[$row[csf('batch_id')]]['process_id']=$row[csf('process_id')];
			$dye_prod_arr[$row[csf('batch_id')]]['result']=$row[csf('result')];
			$dye_prod_arr[$row[csf('batch_id')]]['machine_id']=$row[csf('machine_id')];
			$dye_prod_arr[$row[csf('batch_id')]]['used_time']=floor($total_time/60).":".$total_time%60;
		}
		$ltb_btb=array(1=>'BTB',2=>'LTB');
		
		$finDeliveryArray=array();
		$del_qty=sql_select("select order_id, batch_id, color_id, product_id, sum(current_delivery) as delivery_qty from pro_grey_prod_delivery_dtls where entry_form in(54,67) and status_active=1 and is_deleted=0 group by order_id, batch_id, color_id, product_id");
		foreach($del_qty as $row)
		{
			$finDeliveryArray[$row[csf("order_id")]][$row[csf("batch_id")]][$row[csf("color_id")]][$row[csf("product_id")]]=$row[csf("delivery_qty")];
		}
		
		$data_trns_array=array();
		$trnasactionData=sql_select("Select b.po_breakdown_id, a.batch_id,
		sum(case when b.trans_type=1 then b.quantity else 0 end) as receive,
		sum(case when b.trans_type=2 then b.quantity else 0 end) as issue,
		sum(case when b.trans_type=3 then b.quantity else 0 end) as rec_return,
		sum(case when b.trans_type=4 then b.quantity else 0 end) as issue_return
		
		from pro_finish_fabric_rcv_dtls a, order_wise_pro_details b, inv_transaction c
		where a.id=b.dtls_id and b.trans_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.item_category in (2,3) group by b.po_breakdown_id, a.batch_id");
		foreach($trnasactionData as $row)
		{
			$data_trns_array[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]]['receive']=$row[csf("receive")];
			$data_trns_array[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]]['issue_return']=$row[csf("issue_return")];
			$data_trns_array[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]]['issue']=$row[csf("issue")];
			$data_trns_array[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]]['rec_return']=$row[csf("rec_return")];
		}
		
		if($db_type==0)
		{
			$year_field="YEAR(a.insert_date)";
			if($cbo_year!=0) $job_year_cond=" and YEAR(a.insert_date)=$cbo_year"; else $job_year_cond="";
		}
		else if($db_type==2)
		{
			$year_field="to_char(a.insert_date,'YYYY')";
			if($cbo_year!=0) $job_year_cond=" and to_char(a.insert_date,'YYYY')=$cbo_year";  else $job_year_cond="";
		}
		else $year_field="";//defined Later
		$srch_batch_no= str_replace("'","",$txt_batch_no);
		$srch_buyer_id= str_replace("'","",$hide_buyer_id);
		$srch_job_no= str_replace("'","",$txt_job_no);
		$srch_order_no= str_replace("'","",$txt_order_no);
		$srch_batch_no= str_replace("'","",$txt_batch_no);
		
		$tna_date_from= str_replace("'","",$txt_date_from);
		$tna_date_to= str_replace("'","",$txt_date_to);
		
		if($srch_buyer_id!="") $buyer_cond=" and a.buyer_name in ($srch_buyer_id)"; else $buyer_cond="";
		if($srch_job_no!="") $job_no_cond=" and a.job_no_prefix_num='$srch_job_no'"; else $job_no_cond="";
		if($srch_order_no!="") $order_no_cond=" and b.po_number LIKE '%$srch_order_no%'"; else $order_no_cond="";
		$srch_batch_order="";
		if ($srch_batch_no!="")
		{
			$batch_order_arr=return_library_array("select b.id, c.po_id from pro_batch_create_mst b, pro_batch_create_dtls c, wo_po_break_down d where c.po_id=d.id and b.entry_form=0 and b.id=c.mst_id and b.company_id='$company_name' and d.shiping_status<>3 and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and b.batch_no='$srch_batch_no' ",'id','po_id');
			$batch_order_id=array_unique($batch_order_arr); $order_id="";
			foreach($batch_order_id as $val)
			{
				if($order_id=="") $order_id=$val; else $order_id.=','.$val;
			}
			$srch_batch_order=" and b.id in ($order_id)";
		}
		else
		$srch_batch_order="";
		
		$srch_tna_order="";
		if($tna_date_from!="" && $tna_date_to!="")
		{
			$tna_order_arr=return_library_array("select a.id, a.po_number_id from tna_process_mst a, wo_po_break_down b where a.po_number_id=b.id and a.task_number=61 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.shiping_status<>3 and a.task_start_date between '$tna_date_from' and '$tna_date_to' ",'id','po_number_id');
			$order_tna_id=array_unique($tna_order_arr); $po_id="";
			foreach($order_tna_id as $val)
			{
				if($po_id=="") $po_id=$val; else $po_id.=','.$val;
			}
			
			if($po_id!="") 
			{
				$po_id=substr($po_id,0,-1);
				if($db_type==0) $srch_tna_order="and b.id in(".$po_id.")";
				else
				{
					$po_ids=explode(",",$po_id);
					if(count($po_ids)>1000)
					{
						$srch_tna_order="and (";
						$po_ids=array_chunk($po_ids,1000);
						$z=0;
						foreach($po_ids as $id)
						{
							$id=implode(",",$id);
							if($z==0) $srch_tna_order.=" b.id in(".$id.")";
							else $srch_tna_order.=" or b.id  in(".$id.")";
							$z++;
						}
						$srch_tna_order.=")";
					}
					else $srch_tna_order="and b.id in(".$po_id.")";
				}
			}
		}
		else
		$srch_tna_order="";
		
		$ship_date_from= str_replace("'","",$txt_date_from_ship);
		$ship_date_to= str_replace("'","",$txt_date_to_ship);
		
		if($ship_date_from!="" && $ship_date_to!="") $ship_date_cond=" and b.pub_shipment_date between '$ship_date_from' and '$ship_date_to'"; else $ship_date_cond="";
			
		$sql="select a.job_no_prefix_num, a.job_no, a.company_name, a.buyer_name, $year_field as year, a.style_ref_no, b.id as po_id, b.po_number, b.pub_shipment_date from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name=$company_name and b.shiping_status<>3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $job_year_cond $buyer_cond $job_no_cond $order_no_cond $srch_batch_order $srch_tna_order $ship_date_cond order by a.buyer_name, a.job_no";//

		$i=1; 
		$nameArray=sql_select($sql);
		foreach ($nameArray as $row)
		{
			if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			
			$tna_start_date=''; $tna_end_date='';
			if($tna_array[$row[csf('po_id')]]['start_d']!="0000-00-00" && $tna_array[$row[csf('po_id')]]['start_d']!="")
			{
				$tna_start_date=change_date_format($tna_array[$row[csf('po_id')]]['start_d']);
			}
			
			if($tna_array[$row[csf('po_id')]]['finish_d']!="0000-00-00" && $tna_array[$row[csf('po_id')]]['finish_d']!="")
			{
				$tna_end_date=change_date_format($tna_array[$row[csf('po_id')]]['finish_d']);
			}
			$count_prod_id=count($batch_prod_arr[$row[csf('po_id')]]);
			$po_id=array_unique(explode(',',$batch_prod_arr[$row[csf('po_id')]]));
			$z=1;
			foreach($batch_prod_arr as $poid=>$po_data)
			{
				if($poid!="")
				{
					foreach($po_data as $color_id=>$color_data)
					{
						foreach($color_data as $batch_id=>$batch_data)
						{
							foreach($batch_data as $prod_id)
							{
								// echo $fabric_desc_arr[$prod_id]['desc'];
								 
								$fabric_des=explode(",",$fabric_desc_arr[$prod_id]['desc']);
								
								$fabric_construction=$fabric_des[0];
								$fabric_composition=$fabric_des[1];
								//$color_id=$batch_arr[$row[csf('po_id')]][$prod_id]['color_id'];
								$batch_id=$batch_arr[$batch_id][$row[csf('po_id')]][$prod_id]['id'];
								$sub_process_id=explode(',',$dye_prod_arr[$batch_id]['process_id']);
								$sub_process_name="";
								foreach($sub_process_id as $val)
								{
									if($sub_process_name=="") $sub_process_name=$conversion_cost_head_array[$val]; else $sub_process_name.=', '.$conversion_cost_head_array[$val];
								}
								
								if($dye_prod_arr[$batch_id]['result']==1)
								{
									$shade="Shade Matched";	
								}
								else
								{
									$shade="Shade Not Matched";
								}
								$req_qty=$bookingDataArr[$row[csf('po_id')]][trim($fabric_construction)][trim($fabric_composition)][$fabric_desc_arr[$prod_id]['gsm']][$fabric_desc_arr[$prod_id]['dia']][$color_id];
								$receive_qty=$data_trns_array[$row[csf("po_id")]][$batch_id]['receive'];
								$issue_rtn_qty=$data_trns_array[$row[csf("po_id")]][$batch_id]['issue_return'];
								$issue_qty=$data_trns_array[$row[csf("po_id")]][$batch_id]['issue'];
								$receive_rtn_qty=$data_trns_array[$row[csf("po_id")]][$batch_id]['rec_return'];
								
								$finish_stock=($receive_qty+$issue_rtn_qty)-($issue_qty+$receive_rtn_qty);
								?>
									<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
										<td width="30"><? if($z==1) echo $i; ?></td>
										<td width="70"><p><? if($z==1) echo $buyer_short_arr[$row[csf('buyer_name')]]; ?></p></td>
										<td width="60"><? if($z==1) echo $row[csf('job_no_prefix_num')]; ?></td>
										<td width="60"><? if($z==1) echo $row[csf('year')]; ?></td>
										<td width="120"><p><? if($z==1) echo $row[csf('style_ref_no')]; ?></p></td>
										<td width="120"><p><? if($z==1) echo $row[csf('po_number')]; ?></p></td>
										<td width="70"><p><? if($z==1) echo change_date_format($row[csf('pub_shipment_date')]); ?></p></td>
										<td width="70"><? if($z==1) echo $tna_start_date; ?></td>
										<td width="70"><? if($z==1) echo $tna_end_date; ?></td>
										
										<td width="200"><p><? echo $fabric_desc_arr[$prod_id]['desc']; ?></p></td>
										<td width="90"><p><? echo $color_library[$color_id]; ?></p></td>
										<td width="80" align="right"><? echo number_format($req_qty,2); $total_req_qty+=$req_qty; ?></td>
										<td width="60" align="center"><? echo $fabric_desc_arr[$prod_id]['dia']; ?></td>
										<td width="60" align="center"><? echo $fabric_desc_arr[$prod_id]['gsm']; ?></td>
										<td width="90"><? echo $fabric_typee[$batch_arr[$batch_id][$row[csf('po_id')]][$prod_id]['width_dia_type']]; ?></td>
										<td width="80"><p><? echo $batch_arr[$batch_id][$row[csf('po_id')]][$prod_id]['batch_no']; ?></p></td>
										<td width="40" align="center"><p><? echo $batch_arr[$batch_id][$row[csf('po_id')]][$prod_id]['ext']; ?></p></td>
										<td width="70"><? echo ($batch_arr[$batch_id][$row[csf('po_id')]][$prod_id]['batch_date'] == '0000-00-00' || $batch_arr[$batch_id][$row[csf('po_id')]][$prod_id]['batch_date'] == '' ? '' : change_date_format($batch_arr[$batch_id][$row[csf('po_id')]][$prod_id]['batch_date'])); ?></td>
										<td width="90"><p><? echo $color_library[$color_id]; ?></p></td>
										<td width="80" align="right"><? $batch_qnty=$batch_arr[$batch_id][$row[csf('po_id')]][$prod_id]['batch_qnty']; echo number_format($batch_qnty,2); $total_batch_qty+=$batch_qnty; ?></td>
										<td width="70"><p><? echo $production_arr[$row[csf('po_id')]][$prod_id]['lot'];//$fabric_desc_arr[$prod_id]['lot']; ?></p></td>
										<td width="60"><? echo $ltb_btb[$dye_prod_arr[$batch_id]['ltb_btb']]; ?></td>
										<td width="80" align="center"><? echo ($dye_prod_arr[$batch_id]['date'] == '0000-00-00' || $dye_prod_arr[$batch_id]['date'] == '' ? '' : change_date_format($dye_prod_arr[$batch_id]['date'])).'<br>'.$dye_prod_arr[$batch_id]['time']; ?></td>
										<td width="60" align="center"><? echo $dye_prod_arr[$batch_id]['used_time']; ?></td>
										<td width="120"><p><? echo $sub_process_name; ?></p></td>
										<td width="100"><p><? echo $shade; ?></p></td>
										<td width="70" align="center"><p><? echo $machine_arr[$dye_prod_arr[$batch_id]['machine_id']]['mc_no']; ?></p></td>
										<td width="80" align="right"><? 
											$finsih_delivery=$finDeliveryArray[$row[csf('po_id')]][$batch_id][$color_id][$prod_id];//$finDeliveryArray[$row[csf('po_id')]][$color_id];
											$issue_store_per=($finsih_delivery/$batch_qnty)*100;
											echo number_format($finsih_delivery,2).'<br>'.number_format($issue_store_per,2).' %'; $total_finish_delivery_qty+=$finsih_delivery; ?></td>
										<td width="90" align="right"><? 
											$issue_balance=$batch_qnty-$finsih_delivery;
											$issue_balance_per=($issue_balance/$batch_qnty)*100;
											echo number_format($issue_balance,2).'<br>'.number_format($issue_balance_per,2).' %'; $total_issue_balance+=$issue_balance; ?></td>
										<td width="80" align="right"><? echo number_format($finish_stock,2); $total_finish_stock+=$finish_stock; ?></td>
										<td align="right"><? echo number_format($issue_qty,2); $total_issue_qty+=$issue_qty; ?></td>
									</tr>
								 <? 
								 if($z==1) $i++; 
								 $z++;
							}
						}
					 }
				 }
			}
		}
		?>
        </table>
        </div>
        <table width="2580" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" > 
           <tr class="tbl_bottom">
                <td width="30">&nbsp;</td>
                <td width="70">&nbsp;</td>
                <td width="60">&nbsp;</td>
                <td width="60">&nbsp;</td>
                <td width="120">&nbsp;</td>
                <td width="120">&nbsp;</td>
                <td width="70">&nbsp;</td>
                <td width="70">&nbsp;</td>
                <td width="70">&nbsp;</td>
                <td width="200">&nbsp;</td>
                <td width="90" align="right">Total</td>
                <td width="80" align="right" id="total_req_qty"><? echo number_format($total_req_qty,2); ?></td>
                <td width="60">&nbsp;</td>
                <td width="60">&nbsp;</td>
                <td width="90">&nbsp;</td>
                <td width="80">&nbsp;</td>
                <td width="40">&nbsp;</td>
                <td width="70">&nbsp;</td>
                <td width="90">&nbsp;</td>
                <td width="80" align="right" id="total_batch_qty"><? echo number_format($total_batch_qty,2); ?></td>
                <td width="70">&nbsp;</td>
                <td width="60">&nbsp;</td>
                <td width="80">&nbsp;</td>
                <td width="60">&nbsp;</td>
                <td width="120">&nbsp;</td>
                <td width="100">&nbsp;</td>
                <td width="70">&nbsp;</td>
                <td width="80" align="right" id="total_finish_delivery_qty"><? echo number_format($total_finish_delivery_qty,2); ?></td>
                <td width="90" align="right" id="total_issue_balance"><? echo number_format($total_issue_balance,2); ?></td>
                <td width="80" align="right" id="total_finish_stock"><? echo number_format($total_finish_stock,2); ?></td>
                <td align="right" id="total_issue_qty"><? echo number_format($total_issue_qty,2); ?></td>
            </tr>
        </table>
        </div>
        <?
	}
	else if ($type==3)
	{
		?>
		<div>
        <table width="2700px" cellpadding="0" cellspacing="0" id="caption" align="center">
            <tr>
               <td align="center" width="100%" colspan="31" class="form_caption" ><strong style="font-size:18px"><? echo $company_arr[$company_name];?></strong></td>
            </tr> 
            <tr>  
               <td align="center" width="100%" colspan="31" class="form_caption" ><strong style="font-size:14px"><? echo $report_title; ?> (Color Wise)</strong></td>
            </tr>
        </table>
        <table width="2700" border="1" rules="all" class="rpt_table" cellpadding="0" cellspacing="0">
        	<thead>
                <th width="30">SL</th>
                <th width="70">Buyer</th>
                <th width="60">Job No</th>
                <th width="60">Year</th>
                <th width="120">Style Ref.</th>
                <th width="120">Order No</th>
                <th width="70">Ship Date</th>
                <th width="70">TNA Start Date</th>
                <th width="70">TNA End Date</th>
                <th width="200">Fabric Const. Comp.</th>
                <th width="90">Fabric Color</th>
                <th width="90">Dia/Width Type</th>
                <th width="80">Batch</th>
                <th width="40">Ext.</th>
                <th width="70">Batch Date</th>
                <th width="90">Batch Color</th>
                <th width="80">Batch Qty.</th>
                <th width="70">Lot</th>
                <th width="60">LTB/BTB</th>                
                <th width="80">Unload Date & Time</th>
                <th width="60">Time Used</th>
                <th width="120">Re-process</th>
                <th width="100">RFT</th>
                <th width="70">Dyeing MC No</th>
                <th width="80">MC Capacity</th>                
                <th width="90">MC Utilization</th>
                <th width="80">Finish Production</th>
                <th width="80">Process Loss</th>
                <th width="80">Fin. Issue to Store (Kg) & %</th>
                <th width="90">Balance (Kg) & %</th>
                <th width="80">Issue to Cutting (Kg) & %</th>
                <th>Finish Stock (Kg)</th>
            </thead>
        </table>
	<div style="width:2720px; overflow-y:scroll; max-height:275px;" id="scroll_body" >
        <table width="2700" border="1" rules="all" class="rpt_table" cellpadding="0" cellspacing="0" id="table_body">
        <?
		$buyer_short_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
		
		$tna_array=array();
		$tna_sql=sql_select("select po_number_id, task_start_date, task_finish_date from tna_process_mst where task_number=61 and is_deleted=0 and status_active=1");
		foreach($tna_sql as $row)
		{
			$tna_array[$row[csf('po_number_id')]]['start_d']=$row[csf('task_start_date')];
			$tna_array[$row[csf('po_number_id')]]['finish_d']=$row[csf('task_finish_date')];
		}
		
		$src_batch_no= str_replace("'","",$txt_batch_no);
		if($src_batch_no!="") $batch_cond=" and b.batch_no='$src_batch_no' "; else $batch_cond="";
		$batch_arr=array(); //$batch_prod_arr=array();
		if($db_type==0)
		{
			$prod_id_cond="group_concat(c.prod_id)";
			$width_dia_type_cond="group_concat(c.width_dia_type)";
		}
		else if($db_type==2)
		{
			$prod_id_cond="listagg(cast(c.prod_id as varchar2(4000)),',') within group (order by c.prod_id)";
			$width_dia_type_cond="listagg(cast(c.width_dia_type as varchar2(4000)),',') within group (order by c.width_dia_type)";
		}
		$sql_batch="select b.id, b.batch_no, b.extention_no, b.batch_date, b.color_id, b.booking_no, b.color_range_id, b.dur_req_hr, b.dur_req_min, $prod_id_cond as prod_id, c.po_id, $width_dia_type_cond as width_dia_type, sum(c.batch_qnty) as batch_qnty
		from pro_batch_create_mst b, pro_batch_create_dtls c where b.entry_form=0 and b.id=c.mst_id and b.company_id='$company_name' and b.status_active=1 and b.is_deleted=0 $batch_cond 
		group by b.id, b.batch_no, b.extention_no, b.batch_date, b.color_id, b.booking_no, b.color_range_id, b.dur_req_hr, b.dur_req_min, c.po_id";
		$batch_result=sql_select($sql_batch);
		foreach ($batch_result as $row)
		{
			$batch_arr[$row[csf('po_id')]][$row[csf('id')]]['batch_no']=$row[csf('batch_no')];
			$batch_arr[$row[csf('po_id')]][$row[csf('id')]]['color_id']=$row[csf('color_id')];
			$batch_arr[$row[csf('po_id')]][$row[csf('id')]]['width_dia_type']=$row[csf('width_dia_type')];
			$batch_arr[$row[csf('po_id')]][$row[csf('id')]]['batch_no']=$row[csf('batch_no')];
			$batch_arr[$row[csf('po_id')]][$row[csf('id')]]['ext']=$row[csf('extention_no')];
			$batch_arr[$row[csf('po_id')]][$row[csf('id')]]['batch_date']=$row[csf('batch_date')];
			$batch_arr[$row[csf('po_id')]][$row[csf('id')]]['batch_qnty']=$row[csf('batch_qnty')];
			$batch_arr[$row[csf('po_id')]][$row[csf('id')]]['id']=$row[csf('id')];
			
			$batch_prod_arr[$row[csf('po_id')]].=$row[csf('po_id')].'**'.$row[csf('color_id')].'**'.$row[csf('id')].'**'.$row[csf('prod_id')].'***';
		}
		//var_dump($batch_prod_arr);
		$fabric_desc_arr=array();
		$prodData=sql_select("select id, product_name_details, gsm, dia_width, lot from product_details_master");
		foreach($prodData as $row)
		{
			$fabric_desc_arr[$row[csf('id')]]['desc']=$row[csf('product_name_details')];
			$fabric_desc_arr[$row[csf('id')]]['gsm']=$row[csf('gsm')];
			$fabric_desc_arr[$row[csf('id')]]['dia']=$row[csf('dia_width')];
			//$fabric_desc_arr[$row[csf('id')]]['lot']=$row[csf('lot')];
		}
		
		if($db_type==0)
		{
			$lot_cond="group_concat(a.yarn_lot)";
		}
		else if($db_type==2)
		{
			$lot_cond="listagg(cast(a.yarn_lot as varchar2(4000)),',') within group (order by a.yarn_lot)";
		}
		$production_arr=array();//$yarn_lot="";
		//echo "select a.color_id, $lot_cond as yarn_lot, b.po_breakdown_id from pro_grey_prod_entry_dtls a, order_wise_pro_details b where a.id=b.dtls_id and b.entry_form=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.color_id, b.po_breakdown_id";
		$productionData=sql_select("select a.color_id, $lot_cond as yarn_lot, b.po_breakdown_id from pro_grey_prod_entry_dtls a, order_wise_pro_details b where a.id=b.dtls_id and b.entry_form=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.color_id, b.po_breakdown_id");
		foreach($productionData as $row)
		{
			//if($yarn_lot=="") $yarn_lot=$row[csf('yarn_lot')]; else $yarn_lot.='**'.$row[csf('yarn_lot')];
			$lot=implode(",",array_unique(explode(',',$row[csf('yarn_lot')])));
			$production_arr[$row[csf('po_breakdown_id')]][$row[csf('color_id')]]['lot']=$lot;
		}
		//var_dump($production_arr);die;
		$bookingDataArr=array();
		$sql_wo=sql_select("select b.po_break_down_id, b.fabric_color_id, b.construction, b.copmposition, b.gsm_weight, b.dia_width, sum(b.fin_fab_qnty) as qnty from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.company_id=$company_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.po_break_down_id, b.fabric_color_id, b.construction, b.copmposition, b.gsm_weight, b.dia_width");
		foreach($sql_wo as $woRow)
		{
			$bookingDataArr[$woRow[csf('po_break_down_id')]][$woRow[csf('fabric_color_id')]]=$woRow[csf('qnty')];
		}
		
		$machine_arr=array();
		$machine_sql=sql_select("select id, machine_no, prod_capacity from lib_machine_name where status_active=1 and is_deleted=0");
		foreach($machine_sql as $row)
		{
			$machine_arr[$row[csf('id')]]['mc_no']=$row[csf('machine_no')];
			$machine_arr[$row[csf('id')]]['capacity']=$row[csf('prod_capacity')];
		}
		
		$finProductionArray=array();
		$prod_qty_sql=sql_select("select a.po_breakdown_id, b.batch_id, b.color_id, sum(a.quantity) as prod_qty from order_wise_pro_details a, pro_finish_fabric_rcv_dtls b where a.entry_form in(7,66) and a.dtls_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.po_breakdown_id, b.batch_id, b.color_id");
		foreach($prod_qty_sql as $row)
		{
			$finProductionArray[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("color_id")]]=$row[csf("prod_qty")];
		}
		//var_dump($finProductionArray);
		$finDeliveryArray=array();
		$del_qty=sql_select("select a.order_id, b.batch_id, b.color_id, sum(a.current_delivery) as delivery_qty from pro_grey_prod_delivery_dtls a, pro_finish_fabric_rcv_dtls b where a.entry_form in(54,67) and a.sys_dtls_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.order_id, b.batch_id, b.color_id");
		foreach($del_qty as $row)
		{
			$finDeliveryArray[$row[csf("order_id")]][$row[csf("batch_id")]][$row[csf("color_id")]]=$row[csf("delivery_qty")];
		}
		//var_dump($finDeliveryArray);
		
		$finIssueArray=array();
		$issueQty_sql=sql_select("select b.po_breakdown_id, a.batch_id, b.color_id, sum(b.quantity) as issue_qty from inv_finish_fabric_issue_dtls a,  order_wise_pro_details b where b.entry_form in(18,71) and a.id=b.dtls_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.po_breakdown_id, a.batch_id, b.color_id");
		foreach($issueQty_sql as $row)
		{
			$finIssueArray[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("color_id")]]=$row[csf("issue_qty")];
		}
		//var_dump($finDeliveryArray);
		
		$dye_prod_load_arr=array();
		$sql_dye_load_prod=sql_select("select batch_id, process_end_date, end_hours, end_minutes from pro_fab_subprocess where entry_form=35 and load_unload_id=1 and company_id='$company_name' and status_active=1 and is_deleted=0 order by id");
		foreach($sql_dye_load_prod as $row)
		{
			$dye_prod_load_arr[$row[csf('batch_id')]]['end_date']=$row[csf('process_end_date')];
			$dye_prod_load_arr[$row[csf('batch_id')]]['time']=$row[csf('end_hours')].':'.$row[csf('end_minutes')];
		}
		
		$dye_prod_arr=array();
		$sql_dye_prod=sql_select("select batch_id, production_date, process_id, ltb_btb_id, machine_id, end_hours, end_minutes, result, remarks from pro_fab_subprocess where entry_form=35 and load_unload_id=2 and company_id='$company_name' and status_active=1 and is_deleted=0 order by id");
		foreach($sql_dye_prod as $row)
		{
			$unload_time=$row[csf('end_hours')].':'.$row[csf('end_minutes')];
			$load_date=$dye_prod_load_arr[$row[csf('batch_id')]]['end_date'];
			$load_time=$dye_prod_load_arr[$row[csf('batch_id')]]['time'];
			
			$new_date_time_unload=($row[csf('production_date')].' '.$unload_time.':'.'00');
			$new_date_time_load=($load_date.' '.$load_time.':'.'00');
			$total_time=datediff(n,$new_date_time_load,$new_date_time_unload);
							
			$dye_prod_arr[$row[csf('batch_id')]]['ltb_btb']=$row[csf('ltb_btb_id')];
			$dye_prod_arr[$row[csf('batch_id')]]['date']=$row[csf('production_date')];
			$dye_prod_arr[$row[csf('batch_id')]]['time']=$unload_time;
			$dye_prod_arr[$row[csf('batch_id')]]['process_id']=$row[csf('process_id')];
			$dye_prod_arr[$row[csf('batch_id')]]['result']=$row[csf('result')];
			$dye_prod_arr[$row[csf('batch_id')]]['machine_id']=$row[csf('machine_id')];
			$dye_prod_arr[$row[csf('batch_id')]]['used_time']=floor($total_time/60).":".$total_time%60;
		}
		$ltb_btb=array(1=>'BTB',2=>'LTB');
		
		if($db_type==0)
		{
			$year_field="YEAR(a.insert_date)";
			if($cbo_year!=0) $job_year_cond=" and YEAR(a.insert_date)=$cbo_year"; else $job_year_cond="";
		}
		else if($db_type==2)
		{
			$year_field="to_char(a.insert_date,'YYYY')";
			if($cbo_year!=0) $job_year_cond=" and to_char(a.insert_date,'YYYY')=$cbo_year";  else $job_year_cond="";
		}
		else $year_field="";//defined Later
		$srch_batch_no= str_replace("'","",$txt_batch_no);
		$srch_buyer_id= str_replace("'","",$hide_buyer_id);
		$srch_job_no= str_replace("'","",$txt_job_no);
		$srch_order_no= str_replace("'","",$txt_order_no);
		$srch_batch_no= str_replace("'","",$txt_batch_no);
		
		$tna_date_from= str_replace("'","",$txt_date_from);
		$tna_date_to= str_replace("'","",$txt_date_to);
		
		if($srch_buyer_id!="") $buyer_cond=" and a.buyer_name in ($srch_buyer_id)"; else $buyer_cond="";
		if($srch_job_no!="") $job_no_cond=" and a.job_no_prefix_num='$srch_job_no'"; else $job_no_cond="";
		if($srch_order_no!="") $order_no_cond=" and b.po_number LIKE '%$srch_order_no%'"; else $order_no_cond="";
		$srch_batch_order="";
		if ($srch_batch_no!="")
		{
			$batch_order_arr=return_library_array("select b.id, c.po_id from pro_batch_create_mst b, pro_batch_create_dtls c, wo_po_break_down d where c.po_id=d.id and b.entry_form=0 and b.id=c.mst_id and b.company_id='$company_name' and d.shiping_status<>3 and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and b.batch_no='$srch_batch_no' ",'id','po_id');
			$batch_order_id=array_unique($batch_order_arr); $order_id="";
			foreach($batch_order_id as $val)
			{
				if($order_id=="") $order_id=$val; else $order_id.=','.$val;
			}
			$srch_batch_order=" and b.id in ($order_id)";
		}
		else
		$srch_batch_order="";
		
		$srch_tna_order="";
		if($tna_date_from!="" && $tna_date_to!="")
		{
			$tna_order_arr=return_library_array("select a.id, a.po_number_id from tna_process_mst a, wo_po_break_down b where a.po_number_id=b.id and a.task_number=61 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.shiping_status<>3 and a.task_start_date between '$tna_date_from' and '$tna_date_to' ",'id','po_number_id');
			$order_tna_id=array_unique($tna_order_arr); $po_id="";
			foreach($order_tna_id as $val)
			{
				if($po_id=="") $po_id=$val; else $po_id.=','.$val;
			}
			
			if($po_id!="") 
			{
				$po_id=substr($po_id,0,-1);
				if($db_type==0) $srch_tna_order="and b.id in(".$po_id.")";
				else
				{
					$po_ids=explode(",",$po_id);
					if(count($po_ids)>1000)
					{
						$srch_tna_order="and (";
						$po_ids=array_chunk($po_ids,1000);
						$z=0;
						foreach($po_ids as $id)
						{
							$id=implode(",",$id);
							if($z==0) $srch_tna_order.=" b.id in(".$id.")";
							else $srch_tna_order.=" or b.id  in(".$id.")";
							$z++;
						}
						$srch_tna_order.=")";
					}
					else $srch_tna_order="and b.id in(".$po_id.")";
				}
			}
		}
		else
		$srch_tna_order="";
		
		$ship_date_from= str_replace("'","",$txt_date_from_ship);
		$ship_date_to= str_replace("'","",$txt_date_to_ship);
		
		if($ship_date_from!="" && $ship_date_to!="") $ship_date_cond=" and b.pub_shipment_date between '$ship_date_from' and '$ship_date_to'"; else $ship_date_cond="";
			
		$sql="select a.job_no_prefix_num, a.job_no, a.company_name, a.buyer_name, $year_field as year, a.style_ref_no, b.id as po_id, b.po_number, b.pub_shipment_date from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name=$company_name and b.shiping_status<>3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $job_year_cond $buyer_cond $job_no_cond $order_no_cond $srch_batch_order $srch_tna_order $ship_date_cond order by a.job_no";//

		$i=1; 
		$nameArray=sql_select($sql);
		foreach ($nameArray as $row)
		{
			if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			
			$tna_start_date=''; $tna_end_date='';
			if($tna_array[$row[csf('po_id')]]['start_d']!="0000-00-00" && $tna_array[$row[csf('po_id')]]['start_d']!="")
			{
				$tna_start_date=change_date_format($tna_array[$row[csf('po_id')]]['start_d']);
			}
			
			if($tna_array[$row[csf('po_id')]]['finish_d']!="0000-00-00" && $tna_array[$row[csf('po_id')]]['finish_d']!="")
			{
				$tna_end_date=change_date_format($tna_array[$row[csf('po_id')]]['finish_d']);
			}
			//$count_prod_id=count($batch_prod_arr[$row[csf('po_id')]]);
			$po_id=array_filter(array_unique(explode('***',$batch_prod_arr[$row[csf('po_id')]])));
			//echo $all_po_count=count($po_id);
			$z=1;
			foreach($po_id as $podata)
			{
				/*$all_dia_count=count(explode(",",$count_dia));*/
				$all_data=explode('**',$podata);
				$poid=$all_data[0];
				$batch_id=$all_data[2];
				$color_id=$all_data[1];
				$prod_id=$all_data[3];
				//echo $poid.'<br>';
				//print_r($all_data);
				if($poid!="")
				{
					/*foreach($po_data as $color_id=>$color_data)
					{
						foreach($color_data as $batch_id=>$prod_id)
						{*/
							//echo $prod_id;
							$fabric_des_detls="";
							$fabric_des_id=array_unique(explode(",",$prod_id));
							foreach($fabric_des_id as $id)
							{
								if($fabric_des_detls=="") $fabric_des_detls=$fabric_desc_arr[$id]['desc']; else $fabric_des_detls.='<br>'.$fabric_desc_arr[$id]['desc'];
							}
							
							$width_dia_name="";
							$width_dia_type_id=array_unique(explode(",",$batch_arr[$row[csf('po_id')]][$batch_id]['width_dia_type']));
							foreach($width_dia_type_id as $type_id)
							{
								if($width_dia_name=="") $width_dia_name=$fabric_typee[$type_id]; else $width_dia_name.='<br>'.$fabric_typee[$type_id];
							}
							
							//$color_id=$batch_arr[$row[csf('po_id')]][$color_id]['color_id'];
							//$batch_id=$batch_arr[$row[csf('po_id')]][$color_id]['id'];
							$sub_process_id=explode(',',$dye_prod_arr[$batch_id]['process_id']);
							$sub_process_name="";
							foreach($sub_process_id as $val)
							{
								if($sub_process_name=="") $sub_process_name=$conversion_cost_head_array[$val]; else $sub_process_name.=', '.$conversion_cost_head_array[$val];
							}
							
							if($dye_prod_arr[$batch_id]['result']==1)
							{
								$shade="Shade Matched";	
							}
							else
							{
								$shade="Shade Not Matched";
							}
							
							$receive_qty=$data_trns_array[$row[csf("po_id")]][$batch_id]['receive'];
							$issue_rtn_qty=$data_trns_array[$row[csf("po_id")]][$batch_id]['issue_return'];
							$issue_qty=$finIssueArray[$row[csf("po_id")]][$batch_id][$color_id];
							$receive_rtn_qty=$data_trns_array[$row[csf("po_id")]][$batch_id]['rec_return'];
							
							//$finish_stock=($receive_qty+$issue_rtn_qty)-($issue_qty+$receive_rtn_qty);
							?>
								<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
									<td width="30"><? if($z==1) echo $i; ?></td>
									<td width="70"><p><? if($z==1) echo $buyer_short_arr[$row[csf('buyer_name')]]; ?></p></td>
									<td width="60"><? if($z==1) echo $row[csf('job_no_prefix_num')]; ?></td>
									<td width="60"><? if($z==1) echo $row[csf('year')]; ?></td>
									<td width="120"><p><? if($z==1) echo $row[csf('style_ref_no')]; ?></p></td>
									<td width="120"><p><? if($z==1) echo $row[csf('po_number')]; ?></p></td>
									<td width="70"><p><? if($z==1) echo change_date_format($row[csf('pub_shipment_date')]); ?></p></td>
									<td width="70"><? if($z==1) echo $tna_start_date; ?></td>
									<td width="70"><? if($z==1) echo $tna_end_date; ?></td>
									
									<td width="200"><p><? echo $fabric_des_detls; ?></p></td>
									<td width="90"><p><? echo $color_library[$color_id]; ?></p></td>
									<td width="90"><? echo $width_dia_name; ?></td>
									<td width="80"><p><? echo $batch_arr[$row[csf('po_id')]][$batch_id]['batch_no']; ?></p></td>
									<td width="40" align="center"><p><? echo $batch_arr[$row[csf('po_id')]][$batch_id]['ext']; ?></p></td>
									<td width="70"><? echo ($batch_arr[$row[csf('po_id')]][$batch_id]['batch_date'] == '0000-00-00' || $batch_arr[$row[csf('po_id')]][$batch_id]['batch_date'] == '' ? '' : change_date_format($batch_arr[$row[csf('po_id')]][$batch_id]['batch_date'])); ?></td>
									<td width="90"><p><? echo $color_library[$color_id]; ?></p></td>
									<td width="80" align="right"><? $batch_qnty=$batch_arr[$row[csf('po_id')]][$batch_id]['batch_qnty']; echo number_format($batch_qnty,2); $total_batch_qty+=$batch_qnty; ?></td>
									<td width="70"><p><? echo $production_arr[$row[csf('po_id')]][$color_id]['lot'];//$fabric_desc_arr[$color_id]['lot']; ?></p></td>
									<td width="60"><? echo $ltb_btb[$dye_prod_arr[$batch_id]['ltb_btb']]; ?></td>
									<td width="80" align="center"><? echo ($dye_prod_arr[$batch_id]['date'] == '0000-00-00' || $dye_prod_arr[$batch_id]['date'] == '' ? '' : change_date_format($dye_prod_arr[$batch_id]['date'])).'<br>'.$dye_prod_arr[$batch_id]['time']; ?></td>
									<td width="60" align="center"><? echo $dye_prod_arr[$batch_id]['used_time']; ?></td>
									<td width="120"><p><? echo $sub_process_name; ?></p></td>
									<td width="100"><p><? echo $shade; ?></p></td>
									<td width="70" align="center"><p><? echo $machine_arr[$dye_prod_arr[$batch_id]['machine_id']]['mc_no']; ?></p></td>
									<td width="80" align="right"><? echo number_format($machine_arr[$dye_prod_arr[$batch_id]['machine_id']]['capacity'],2); ?></td>
									<td width="90" align="right"><? $mc_utilization=($batch_qnty/$machine_arr[$dye_prod_arr[$batch_id]['machine_id']]['capacity'])*100; echo number_format($mc_utilization,2).' %'; ?></td>
                                    <td width="80" align="right"><? $finProduction=$finProductionArray[$row[csf('po_id')]][$batch_id][$color_id]; echo number_format($finProduction,2); $totalFinProduction+=$finProduction; ?></td>
                                    <td width="80" align="right"><? $finProcessLoss=$batch_qnty-$finProduction; echo number_format($finProcessLoss,2); $totalFinProcessLoss+=$finProcessLoss; ?></td>
									<td width="80" align="right"><? 
										$finsih_delivery=$finDeliveryArray[$row[csf('po_id')]][$batch_id][$color_id];
										//$finDeliveryArray[$row[csf('po_id')]][$color_id];
										$issue_store_per=($finsih_delivery/$finProduction)*100;
										echo number_format($finsih_delivery,2).'<br>'.number_format($issue_store_per,2).' %'; $total_finish_delivery_qty+=$finsih_delivery; ?></td>
									<td width="90" align="right"><? 
										$issue_balance=$finProduction-$finsih_delivery;
										$issue_balance_per=($issue_balance/$finProduction)*100;
										echo number_format($issue_balance,2).'<br>'.number_format($issue_balance_per,2).' %'; $total_issue_balance+=$issue_balance; ?></td>
									<td width="80" align="right"><? echo number_format($issue_qty,2); $total_issue_qty+=$issue_qty; ?></td>
                                    <td align="right"><? $finish_stock=$finsih_delivery-$issue_qty; echo number_format($finish_stock,2); $total_finish_stock+=$finish_stock; ?></td>
								</tr>
							 <?
							 if($z==1) $i++; 
							 $z++;
/*						}
					}
*/				}
			}
		}
		?>
        </table>
        </div>
        <table width="2700" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" > 
           <tr class="tbl_bottom">
                <td width="30">&nbsp;</td>
                <td width="70">&nbsp;</td>
                <td width="60">&nbsp;</td>
                <td width="60">&nbsp;</td>
                <td width="120">&nbsp;</td>
                <td width="120">&nbsp;</td>
                <td width="70">&nbsp;</td>
                <td width="70">&nbsp;</td>
                <td width="70">&nbsp;</td>
                <td width="200">&nbsp;</td>
                <td width="90" align="right">Total</td>
                <td width="90">&nbsp;</td>
                <td width="80">&nbsp;</td>
                <td width="40">&nbsp;</td>
                <td width="70">&nbsp;</td>
                <td width="90">&nbsp;</td>
                <td width="80" align="right" id="total_batch_qty"><? echo number_format($total_batch_qty,2); ?></td>
                <td width="70">&nbsp;</td>
                <td width="60">&nbsp;</td>
                <td width="80">&nbsp;</td>
                <td width="60">&nbsp;</td>
                <td width="120">&nbsp;</td>
                <td width="100">&nbsp;</td>
                <td width="70">&nbsp;</td>
                <td width="80">&nbsp;</td>
                <td width="90">&nbsp;</td>
                <td width="80" align="right" id="total_finish_production_qty"><? echo number_format($total_finish_delivery_qty,2); ?></td>
                <td width="80" align="right" id="total_finish_processLoss_qty"><? echo number_format($total_finish_delivery_qty,2); ?></td>
                <td width="80" align="right" id="total_finish_delivery_qty"><? echo number_format($total_finish_delivery_qty,2); ?></td>
                <td width="90" align="right" id="total_issue_balance"><? echo number_format($total_issue_balance,2); ?></td>
                <td width="80" align="right" id="total_issue_qty"><? echo number_format($total_issue_qty,2); ?></td>
                <td align="right" id="total_finish_stock"><? echo number_format($total_finish_stock,2); ?></td>
            </tr>
        </table>
        </div>
        <?
	}
	foreach (glob("../../../ext_resource/tmp_report/$user_name*.xls") as $filename) 
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$html=ob_get_contents();
	$name=time();
	$filename="../../../ext_resource/tmp_report/".$user_name."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,$html);
	$filename="../../ext_resource/tmp_report/".$user_name."_".$name.".xls";
	echo "$total_data####$filename";
	exit();
}

if($action=="grey_yarn_info")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	
	$data=explode("_",$data);
	
	if($db_type==0)
	{
		$tna_start_date=change_date_format($data[0], "yyyy-mm-dd", "-");
		$tna_end_date=change_date_format($data[1], "yyyy-mm-dd", "-");
		$plan_start_date=change_date_format($data[2], "yyyy-mm-dd", "-");
		$plan_end_date=change_date_format($data[3], "yyyy-mm-dd", "-");
		$actual_start_date=change_date_format($data[4], "yyyy-mm-dd", "-");
		$actual_end_date=change_date_format($data[5], "yyyy-mm-dd", "-");
	}
	else
	{
		$tna_start_date=change_date_format($data[0],'','',1);
		$tna_end_date=change_date_format($data[1],'','',1);
		$plan_start_date=change_date_format($data[2],'','',1);
		$plan_end_date=change_date_format($data[3],'','',1);
		$actual_start_date=change_date_format($data[4],'','',1);
		$actual_end_date=change_date_format($data[5],'','',1);
	}
	
	$pub_shipment_date=change_date_format($data[6], "yyyy-mm-dd", "-");
	$today=date("Y-m-d");
	$yet_to_ship_days=datediff( d, $today, $pub_shipment_date);
	
	$booking_qnty=return_field_value("sum(b.grey_fab_qnty) as qnty","wo_booking_mst a, wo_booking_dtls b","a.booking_no=b.booking_no and a.item_category in(2,13) and b.po_break_down_id='$po_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0",'qnty');
	
	$yarn_iss_data=sql_select("select sum(case when a.transaction_date<='$tna_end_date' then b.quantity else 0 end) as tna_wise_iss_qnty, sum(case when a.transaction_date between '$tna_start_date' and '$plan_end_date' then b.quantity else 0 end) as plan_wise_iss_qnty, sum(case when a.transaction_date between '$tna_start_date' and '$actual_end_date' then b.quantity else 0 end) as actual_iss_qnty from inv_transaction a, order_wise_pro_details b where a.id=b.trans_id and a.item_category=1 and a.transaction_type=2 and b.po_breakdown_id='$po_id' and b.entry_form=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
	
	$tna_wise_iss_qnty=$yarn_iss_data[0][csf('tna_wise_iss_qnty')];
	$plan_wise_iss_qnty=$yarn_iss_data[0][csf('plan_wise_iss_qnty')];
	$actual_iss_qnty=$yarn_iss_data[0][csf('actual_iss_qnty')];
	
	$prodData=sql_select("select sum(case when a.receive_date<='$tna_end_date' then c.quantity else 0 end) as tna_wise_prod_qnty, sum(case when a.receive_date between '$tna_start_date' and '$plan_end_date' then c.quantity else 0 end) as plan_wise_prod_qnty, sum(case when a.receive_date between '$tna_start_date' and '$actual_end_date' then c.quantity else 0 end) as actual_prod_qnty from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id='$po_id' and a.entry_form in(2,22) and c.entry_form in(2,22) and a.receive_basis<>9 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0");
	
	$tna_wise_prod_qnty=$prodData[0][csf('tna_wise_prod_qnty')];
	$plan_wise_prod_qnty=$prodData[0][csf('plan_wise_prod_qnty')];
	$actual_prod_qnty=$prodData[0][csf('actual_prod_qnty')];
	
	$transData=sql_select("select sum(case when a.transfer_date<='$tna_end_date' and c.trans_type=5 then c.quantity else 0 end) as tna_wise_trans_in_qnty, sum(case when a.transfer_date between '$tna_start_date' and '$plan_end_date' and c.trans_type=5 then c.quantity else 0 end) as plan_wise_trans_in_qnty, sum(case when a.transfer_date between '$tna_start_date' and '$actual_end_date' and c.trans_type=5 then c.quantity else 0 end) as actual_trans_in_qnty, sum(case when a.transfer_date<='$tna_end_date' and c.trans_type=6 then c.quantity else 0 end) as tna_wise_trans_out_qnty, sum(case when a.transfer_date between '$tna_start_date' and '$plan_end_date' and c.trans_type=6 then c.quantity else 0 end) as plan_wise_trans_out_qnty, sum(case when a.transfer_date between '$tna_start_date' and '$actual_end_date' and c.trans_type=6 then c.quantity else 0 end) as actual_trans_out_qnty from inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id='$po_id' and a.item_category=13 and a.transfer_criteria=4 and c.entry_form=13 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by c.po_breakdown_id");
	
	$tna_wise_grey_availlable_qnty=$tna_wise_prod_qnty+$transData[0][csf('tna_wise_trans_in_qnty')]-$transData[0][csf('tna_wise_trans_out_qnty')];
	$plan_wise_grey_availlable_qnty=$plan_wise_prod_qnty+$transData[0][csf('plan_wise_trans_in_qnty')]-$transData[0][csf('plan_wise_trans_out_qnty')];
	$actual_grey_availlable_qnty=$actual_prod_qnty+$transData[0][csf('actual_trans_in_qnty')]-$transData[0][csf('actual_trans_out_qnty')];
	
	$tna_wise_iss_perc=($tna_wise_iss_qnty/$booking_qnty)*100;
	$plan_wise_iss_perc=($plan_wise_iss_qnty/$booking_qnty)*100;
	$actual_iss_perc=($actual_iss_qnty/$booking_qnty)*100;
	
	$tna_wise_prod_perc=($tna_wise_grey_availlable_qnty/$booking_qnty)*100;
	$plan_wise_prod_perc=($plan_wise_grey_availlable_qnty/$booking_qnty)*100;
	$actual_prod_perc=($actual_grey_availlable_qnty/$booking_qnty)*100;
	
?>
	<fieldset style="width:730px; margin-left:5px">
        <table border="1" class="rpt_table" rules="all" width="720" cellpadding="0" cellspacing="0">
        	<thead>
                <tr>
                    <th width="100" rowspan="2">Yet To Ship</th>
                    <th width="200" colspan="2">TNA</th>
                    <th width="200" colspan="2">Plan</th>
                    <th width="200" colspan="2">Actual</th>
                </tr>
                <tr>
                    <th width="100">Yarn Delivered</th>
                    <th width="100">Grey Production</th>
                    <th width="100">Yarn Delivered</th>
                    <th width="100">Grey Production</th>
                    <th width="100">Yarn Delivered</th>
                    <th width="100">Grey Production</th>
                </tr>
            </thead>
            <tr bgcolor="#FFFFFF">
                <td align="center"><? echo $yet_to_ship_days." Days"; ?></td>
                <td align="right"><? echo number_format($tna_wise_iss_qnty,2); ?>&nbsp;</td>
                <td align="right"><? echo number_format($tna_wise_grey_availlable_qnty,2); ?>&nbsp;</td>
                <td align="right"><? echo number_format($plan_wise_iss_qnty,2); ?>&nbsp;</td>
                <td align="right"><? echo number_format($plan_wise_grey_availlable_qnty,2); ?>&nbsp;</td>
                <td align="right"><? echo number_format($actual_iss_qnty,2); ?>&nbsp;</td>
                <td align="right"><? echo number_format($actual_grey_availlable_qnty,2); ?>&nbsp;</td>
            </tr>
            <tr bgcolor="#E9F3FF">
                <td align="center">In %</td>
                <td align="right"><? echo number_format($tna_wise_iss_perc,2); ?>&nbsp;</td>
                <td align="right"><? echo number_format($tna_wise_prod_perc,2); ?>&nbsp;</td>
                <td align="right"><? echo number_format($plan_wise_iss_perc,2); ?>&nbsp;</td>
                <td align="right"><? echo number_format($plan_wise_prod_perc,2); ?>&nbsp;</td>
                <td align="right"><? echo number_format($actual_iss_perc,2); ?>&nbsp;</td>
                <td align="right"><? echo number_format($actual_prod_perc,2); ?>&nbsp;</td>
            </tr>
         </table>
	</fieldset>   
<?
exit();
}

if($action=="grey_available")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	
	$tna_start_date=''; $tna_end_date=''; $tna_start_date_print=''; $tna_end_date_print='';
	$tnaData=sql_select("select task_start_date, task_finish_date from tna_process_mst where task_number=60 and po_number_id='$po_id' and is_deleted=0 and status_active=1");
	if($tnaData[0][csf('task_start_date')]!="0000-00-00" && $tnaData[0][csf('task_start_date')]!="")
	{
		$tna_start_date=date("Y-m-d",strtotime($tnaData[0][csf('task_start_date')]));
		$tna_start_date_print=change_date_format($tnaData[0][csf('task_start_date')]);
	}
	
	if($tnaData[0][csf('task_finish_date')]!="0000-00-00" && $tnaData[0][csf('task_finish_date')]!="")
	{
		$tna_end_date=date("Y-m-d",strtotime($tnaData[0][csf('task_finish_date')]));
		$tna_end_date_print=change_date_format($tnaData[0][csf('task_finish_date')]);
	}
	$tna_days=datediff( d, $tna_start_date, $tna_end_date);
	
	$plan_start_date=''; $plan_end_date=''; $plan_start_date_print=''; $plan_end_date_print='';
	
	if($db_type==0)
	{
		$planData=sql_select("select min(case when a.start_date!='0000-00-00' then a.start_date end) as plan_start_date, max(a.end_date) as plan_end_date from ppl_planning_info_entry_dtls a, ppl_planning_entry_plan_dtls b where a.id=b.dtls_id and b.po_id='$po_id' and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1");
	}
	else
	{
		$planData=sql_select("select min(case when a.start_date is not null then a.start_date end) as plan_start_date, max(a.end_date) as plan_end_date from ppl_planning_info_entry_dtls a, ppl_planning_entry_plan_dtls b where a.id=b.dtls_id and b.po_id='$po_id' and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1");
	}
	
	if($planData[0][csf('plan_start_date')]!="0000-00-00" && $planData[0][csf('plan_start_date')]!="")
	{
		$plan_start_date=date("Y-m-d",strtotime($planData[0][csf('plan_start_date')]));
		$plan_start_date_print=change_date_format($planData[0][csf('plan_start_date')]);
	}
	
	if($planData[0][csf('plan_end_date')]!="0000-00-00" && $planData[0][csf('plan_end_date')]!="")
	{
		$plan_end_date=date("Y-m-d",strtotime($planData[0][csf('plan_end_date')]));
		$plan_end_date_print=change_date_format($planData[0][csf('plan_end_date')]);
	}
	$plan_days=datediff(d,$plan_start_date, $plan_end_date);

	$actual_start_date=''; $actual_end_date=''; $actual_start_date_print=''; $actual_end_date_print='';
	$prodData=sql_select("select min(a.receive_date) as actual_start_date, max(a.receive_date) as actual_end_date from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id='$po_id' and a.entry_form in(2,22) and c.entry_form in(2,22) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0");
	
	if($prodData[0][csf('actual_start_date')]!="0000-00-00" && $prodData[0][csf('actual_start_date')]!="")
	{
		$actual_start_date=date("Y-m-d",strtotime($prodData[0][csf('actual_start_date')]));
		$actual_start_date_print=change_date_format($prodData[0][csf('actual_start_date')]);
	}
	
	if($prodData[0][csf('actual_end_date')]!="0000-00-00" && $prodData[0][csf('actual_end_date')]!="")
	{
		$actual_end_date=date("Y-m-d",strtotime($prodData[0][csf('actual_end_date')]));
		$actual_end_date_print=change_date_format($prodData[0][csf('actual_end_date')]);
	}
	
	$actual_trans_start_date=''; $actual_trans_end_date='';
	$transData=sql_select("select min(a.transfer_date) as actual_trans_start_date, max(a.transfer_date) as actual_trans_end_date from inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id='$po_id' and a.item_category=13 and a.transfer_criteria=4 and c.trans_type=5 and c.entry_form=13 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
	
	if($transData[0][csf('actual_trans_start_date')]!="0000-00-00" && $transData[0][csf('actual_trans_start_date')]!="")
	{
		$actual_trans_start_date=date("Y-m-d",strtotime($transData[0][csf('actual_trans_start_date')])); 
	}
	
	if($transData[0][csf('actual_trans_end_date')]!="0000-00-00" && $transData[0][csf('actual_trans_end_date')]!="")
	{
		$actual_trans_end_date=date("Y-m-d",strtotime($transData[0][csf('actual_trans_end_date')]));
	}

	if($actual_trans_start_date<$actual_start_date && $actual_trans_start_date!="") 
	{
		$actual_start_date=$actual_trans_start_date;
		$actual_start_date_print=change_date_format($transData[0][csf('actual_trans_start_date')]);
	}

	if($actual_trans_end_date>$actual_end_date) 
	{
		$actual_end_date=$actual_trans_end_date;
		$actual_end_date_print=change_date_format($transData[0][csf('actual_trans_end_date')]);
	}
	
	$actual_prod_days=datediff(d,$actual_start_date, $actual_end_date);
	
	$bg_color_plan=""; $bg_color_actual=""; $bg_color_plan_end=""; $bg_color_actual_end=""; $bg_color_deviation=""; $bg_color_actual_deviation="";
	
	if($plan_start_date>$tna_start_date)
	{
		$bg_color_plan="red";
	}
	
	if($actual_start_date>$tna_start_date)
	{
		$bg_color_actual="red";
	}
	
	if($plan_end_date>$tna_end_date)
	{
		$bg_color_plan_end="red";
	}
	
	if($actual_end_date>$tna_end_date)
	{
		$bg_color_actual_end="red";
	}
	
	/*$plan_deviation=$tna_days-$plan_days; 
	$actual_deviation=$tna_days-$actual_prod_days;*/
	if($plan_end_date!="" && $tna_end_date!="")
	{
		$plan_deviation=datediff(d,$plan_end_date, $tna_end_date);
		if($plan_deviation<=0) $plan_deviation=$plan_deviation-1;
		//$plan_deviation=$tna_end_date-$plan_end_date;
	}
	if($plan_end_date!="" && $actual_end_date!="")
	{
		$actual_deviation=datediff(d,$actual_end_date,$tna_end_date);
		if($actual_deviation<=0) $actual_deviation=$actual_deviation-1;
		//$actual_deviation=$plan_end_date-$actual_end_date;
	}
	
	
	if($plan_deviation<0)
	{
		$bg_color_deviation="red";
	}
	
	if($actual_deviation<0)
	{
		$bg_color_actual_deviation="red";
	}
	
?>
	<fieldset style="width:480px; margin-left:5px">
        <table border="1" class="rpt_table" rules="all" width="475" cellpadding="0" cellspacing="0">
            <thead>
                <th width="120">Particulars</th>
                <th width="100">Start Date</th>
                <th width="100">End Date</th>
                <th width="70">Days</th>
                <th>Deviation</th>
            </thead>
            <tr bgcolor="#E9F3FF">
                <td>As Per TNA</td>
                <td align="center"><? echo $tna_start_date_print; ?>&nbsp;</td>
                <td align="center"><? echo $tna_end_date_print; ?>&nbsp;</td>
                <td align="right" style="padding-right:5px">&nbsp;<? echo $tna_days; ?></td>
                <td align="right">&nbsp;</td>
            </tr>
            <tr bgcolor="#FFFFFF">
                <td>As Knitting Plan</td>
                <td align="center" bgcolor="<? echo $bg_color_plan; ?>"><? echo $plan_start_date_print; ?>&nbsp;</td>
                <td align="center" bgcolor="<? echo $bg_color_plan_end; ?>"><? echo $plan_end_date_print; ?>&nbsp;</td>
                <td align="right" style="padding-right:5px">&nbsp;<? if($plan_days>0) echo $plan_days; ?></td>
               <td align="right" bgcolor="<? echo $bg_color_deviation; ?>" style="padding-right:5px">&nbsp;<? if($plan_deviation!=0 && $plan_days>0) echo $plan_deviation; ?></td>
            </tr>
            <tr bgcolor="#E9F3FF">
                <td>As Per Actual Production</td>
                <td align="center" bgcolor="<? echo $bg_color_actual; ?>"><? echo $actual_start_date_print; ?>&nbsp;</td>
                <td align="center" bgcolor="<? echo $bg_color_actual_end; ?>"><? echo $actual_end_date_print; ?>&nbsp;</td>
                <td align="right" style="padding-right:5px">&nbsp;<? if($actual_prod_days>0) echo $actual_prod_days; ?></td>
                <td align="right" bgcolor="<? echo $bg_color_actual_deviation; ?>" style="padding-right:5px">&nbsp;<? if($actual_deviation!=0 && $actual_prod_days>0) echo $actual_deviation; ?></td>
            </tr>
         </table>
	</fieldset>   
<?
exit();
}
?>