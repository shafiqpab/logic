<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
include('../../../includes/common.php');
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$permission=$_SESSION['page_permission'];
$user_id=$_SESSION['logic_erp']['user_id'];
$userCredential = sql_select("SELECT user_level FROM user_passwd where id=$user_id");
$user_level = $userCredential[0][csf('user_level')];

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_name", 180, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 $location_credential_cond order by location_name","id,location_name", 1, "-- Select --", $selected, "" );
	exit();
}
if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 180, "select distinct buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );
		exit();
}
if ($action=="load_drop_down_buyer_pop")
{
	if($data != 0)
	{
		echo create_drop_down( "cbo_buyer_id", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "load_drop_down( 'order_entry_controller', this.value+'*'+1, 'load_drop_down_season', 'season_td'); load_drop_down( 'order_entry_controller', this.value+'*'+1, 'load_drop_down_brand', 'brand_td');" );
		exit();
	}
	else{
		echo create_drop_down( "cbo_buyer_id", 140, "select distinct buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "load_drop_down( 'order_entry_controller', this.value+'*'+1, 'load_drop_down_season', 'season_td'); load_drop_down( 'order_entry_controller', this.value+'*'+1, 'load_drop_down_brand', 'brand_td');" );
		exit();
	}
}
if ($action=="job_popup11")
{
  	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
	<script>
		
		/* function js_set_value( job_data )
		{
			var all_data=job_data.split("_");
			document.getElementById('job_id').value=all_data[0];
			document.getElementById('job_no').value=all_data[1];
			parent.emailwindow.hide();
		} */

		var selected_id = new Array, selected_name = new Array();

		function check_all_data(is_checked)
		{
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;
			tbl_row_count = tbl_row_count - 1;

			for( var i = 1; i <= tbl_row_count; i++ ) {
				js_set_value( i,2 );
			}
		}

		function toggle( x, origColor )
		{
			var newColor = 'yellow';
			if ( x.style )
			{
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		var selected_id = new Array(); var selected_name = new Array();
		function js_set_value(str,all)
		{
			// alert(str)

			if(all==2){

				toggle( document.getElementById( 'search' + str ), '#FFFFCC' );

					if( jQuery.inArray( $('#po_id' + str).val(), selected_id ) == -1 )
					{
						selected_id.push( $('#po_id' + str).val() );
					}
					else
					{
						for( var i = 0; i < selected_id.length; i++ )
						{
							if( selected_id[i] == $('#po_id' + str).val() ) break;
						}
						selected_id.splice( i, 1 );

					
						
					}
					var id =''; 
					for( var i = 0; i < selected_id.length; i++ ) {
						id += selected_id[i] + ',';
					}
					id = id.substr( 0, id.length - 1 );
					$('#txt_selected_id').val( id );
					console.log(id);			
				
			}else{
				if($("#search"+str).css("display") !='none'){
					var select_row=0; var sp=1;
					var tbl_length =$('#tbl_list_search tr').length-1;
					var select_str=$('#job_no' + str).val();
					for(var i=1; i<=tbl_length; i++)
					{
						var string=$('#job_no' + i).val();
						if(select_str==string)
						{
							if(select_row==0)
							{
								select_row=i; sp=1;
							}
							else
							{
								select_row+=','+i; sp=2;
							}
						}
					}
					//alert(select_row)
					
					var exrow = new Array();
					if(sp==2) { exrow=select_row.split(','); var countrow=exrow.length; }
					else countrow=1;
					//alert(countrow)
					/*for(var m=0; m<countrow; m++)
					{
						if(sp==2) exrow[m]=exrow[m];
						else exrow[m]=select_row;
						
						toggle( document.getElementById( 'search' + exrow[m] ), '#FFFFCC' );
						if( jQuery.inArray( $('#po_id' + exrow[m]).val(), selected_id ) == -1 ) {
							
							selected_id.push( $('#po_id' + exrow[m]).val() );
						}
						else{
							for( var i = 0; i < selected_id.length; i++ ) {
								if( selected_id[i] == $('#po_id' + exrow[m]).val() ) break;						
							}
							selected_id.splice( i, 1 );
																	
						}
						
						if( jQuery.inArray( $('#job_no' + exrow[m]).val(), selected_name ) == -1 ) {
							
							selected_name.push( $('#job_no' + exrow[m]).val() );
						}
						else{
							for( var p = 0; p < selected_name.length; p++ ) {
								if( selected_name[p] == $('#job_no' + exrow[m]).val() ) break;						
							}
							selected_name.splice( p, 1 );
						}
					}*/
					
					for(var m=0; m<countrow; m++)
					{
						if(sp==2) exrow[m]=exrow[m];
						else exrow[m]=select_row;
						//alert(exrow[m])
						toggle( document.getElementById( 'search' + exrow[m] ), '#FFFFCC' );
						if( jQuery.inArray( $('#po_id' + exrow[m]).val(), selected_id ) == -1 ) {
							selected_id.push( $('#po_id' + exrow[m]).val() );
						}
						else{
							for( var i = 0; i < selected_id.length; i++ ) {
								if( selected_id[i] == $('#po_id' + exrow[m]).val() ) break;
							}
							
							selected_id.splice( i, 1 );
						}
						
						/*if( jQuery.inArray( $('#job_no' + exrow[m]).val(), selected_name ) == -1 ) {
							selected_name.push($('#job_no' + exrow[m]).val());
						}
						else{
							for( var q = 0; q < selected_name.length; q++ ) {
								if( selected_name[q] == $('#job_no' + exrow[m]).val() ) break;
							}
							selected_name.splice( i,1 );
						}*/
					}
					
					for(var m=0; m<countrow; m++)
					{
						if(sp==2) exrow[m]=exrow[m];
						else exrow[m]=select_row;
						//alert(exrow[m])
						//toggle( document.getElementById( 'search' + exrow[m] ), '#FFFFCC' );
						
						if( jQuery.inArray( $('#job_no' + exrow[m]).val(), selected_name ) == -1 ) {
							selected_name.push($('#job_no' + exrow[m]).val());
						}
						else{
							for( var q = 0; q < selected_name.length; q++ ) {
								if( selected_name[q] == $('#job_no' + exrow[m]).val() ) break;
							}
							selected_name.splice( i,1 );
						}
					}
					
					var id = ''; var job = ''; 
					for( var i = 0; i < selected_id.length; i++ ) {
						id += selected_id[i] + ',';
						//job += selected_name[i] + ',';
					}
					
					for( var i = 0; i < selected_name.length; i++ ) {
						job += selected_name[i] + ',';
					}
					id = id.substr( 0, id.length - 1 );
					job = job.substr( 0, job.length - 1 );
					
					$('#txt_selected_id').val( id );
					$('#txt_selected_name').val( job );
					console.log(job);
				}
			}
		}
    </script>
    </head>
    <body>
    <div align="center" style="width:100%;" >
    <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
        <table width="1080" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
            <thead>
            	<tr>
                	<th colspan="13" align="center"><? echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
                </tr>
                <tr>
                    <th width="140" class="must_entry_caption">Company Name</th>
                    <th width="140" class="must_entry_caption">Buyer Name</th>
                    <th width="70">Brand</th>
                    <th width="70">Season</th>
                    <th width="60">Season Year</th>
                    <th width="60">Job No</th>
                    <th width="90">Style Ref.</th>
                    <th width="80">M.Style/Internal Ref</th>
                    <th width="80">File No</th>
                    <th width="90">Order No</th>
                    <th width="130" colspan="2">Ship Date Range</th>
                    <th><input type="hidden" value="0" id="chk_job_wo_po"></th>
                </tr>
            </thead>
            <tr class="general">
                <td>
                    <input type="" id="txt_selected_id">
                    <input type="" id="txt_selected_name">
                    <input type="hidden" id="garments_nature" value="<?=$garments_nature; ?>">
					<? echo create_drop_down( "cbo_company_mst", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 and core_business not in(3) $company_cond order by company_name","id,company_name",1, "-- Select Company --", $cbo_company_name,"load_drop_down( 'order_entry_controller', this.value, 'load_drop_down_buyer_pop', 'buyer_pop_td' );",'' ); ?>
                </td>
        		<td id="buyer_pop_td"><? echo create_drop_down( "cbo_buyer_id", 140, "select distinct buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name","id,buyer_name",'', 1, "-- Select Buyer --","load_drop_down( 'order_entry_controller', this.value+'*'+1, 'load_drop_down_season', 'season_td'); load_drop_down( 'order_entry_controller', this.value+'*'+1, 'load_drop_down_brand', 'brand_td');" ); ?></td>
        		<td id="brand_td"><? echo create_drop_down( "cbo_brand_id", 70, $blank_array,'', 1, "Brand",$selected, "" ); ?>
        		<td id="season_td"><? echo create_drop_down( "cbo_season_id", 70, $blank_array,'', 1, "Season",$selected, "" ); ?></td>
        		<td><?=create_drop_down( "cbo_season_year", 50, create_year_array(),"", 1,"-Year-", 1, "",0,"" ); ?></td>
                <td><input name="txt_job_prifix" id="txt_job_prifix" class="text_boxes" style="width:50px"></td>
                <td><input name="txt_style" id="txt_style" class="text_boxes" style="width:80px"></td>
                <td><input name="txt_internal_ref" id="txt_internal_ref" class="text_boxes" style="width:70px"></td>
                <td><input name="txt_file_no" id="txt_file_no" class="text_boxes" style="width:70px"></td>
                <td><input name="txt_order_search" id="txt_order_search" class="text_boxes" style="width:80px"></td>
                <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:50px" placeholder="From Date"></td>
                <td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:50px" placeholder="To Date"></td>
                <td align="center">
                	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_id').value+'_'+document.getElementById('chk_job_wo_po').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('garments_nature').value+'_'+document.getElementById('txt_job_prifix').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('txt_order_search').value+'_'+document.getElementById('txt_style').value+'_'+document.getElementById('txt_internal_ref').value+'_'+document.getElementById('txt_file_no').value+'_'+document.getElementById('cbo_brand_id').value+'_'+document.getElementById('cbo_season_id').value+'_'+document.getElementById('cbo_season_year').value, 'create_po_search_list_view', 'search_div', 'order_color_size_update_controller', 'setFilterGrid(\'tbl_list_search\',-1)')" style="width:70px;" /></td>
        	</tr>
            <tr>
                <td align="center" colspan="13"><?=load_month_buttons(1); ?></td>
            </tr>
        </table>
        <div id="search_div"></div>
    </form>
    </div>
    </body>
    <script>
		load_drop_down('order_color_size_update_controller', <?=$cbo_company_name; ?>, 'load_drop_down_buyer_pop', 'buyer_pop_td' )
		document.getElementById('cbo_buyer_id').value=<?=$cbo_buyer_name; ?>
    </script>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
    exit();
}

if($action=="create_po_search_list_view11")
{
	$data=explode('_',$data);
	if($data[0]==0 && $data[1]==0)
	{
		echo "<span style='color:red; font-weight:bold; font-size:20px; text-align:center'>Please select company or buyer first.";
		die;
	}
	if ($data[0]!=0) $company=" and a.company_name='$data[0]'";
	if(str_replace("'","",$data[1])==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer="";
		}
		else $buyer="";
	}
	else $buyer=" and a.buyer_name='$data[1]'";
	$year_cond="";
	if($db_type==0)
	{
		//$year_cond=" and SUBSTRING_INDEX(a.insert_date, '-', 1)=$data[7]";
		if ($data[3]!="" &&  $data[4]!="") $shipment_date = "and b.pub_shipment_date between '".change_date_format($data[3], "yyyy-mm-dd", "-")."' and '".change_date_format($data[4], "yyyy-mm-dd", "-")."'"; else $shipment_date ="";
	}
	else if($db_type==2)
	{
		//$year_cond=" and to_char(a.insert_date,'YYYY')=$data[7]";
		if ($data[3]!="" &&  $data[4]!="") $shipment_date = "and b.pub_shipment_date between '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[4], "yyyy-mm-dd", "-",1)."'"; else $shipment_date ="";
	}

	$order_cond=""; $job_cond=""; $style_cond="";
	if($data[8]==1)
	{
		if (str_replace("'","",$data[6])!="") $job_cond=" and a.job_no_prefix_num='$data[6]'  $year_cond";
		if (trim($data[9])!="") $order_cond=" and b.po_number='$data[9]'  "; //else  $order_cond="";
		if (trim($data[10])!="") $style_cond=" and a.style_ref_no='$data[10]'  "; //else  $style_cond="";
	}
	else if($data[8]==4 || $data[8]==0)
	{
		if (str_replace("'","",$data[6])!="") $job_cond=" and a.job_no_prefix_num like '%$data[6]%'  $year_cond";
		if (trim($data[9])!="") $order_cond=" and b.po_number like '%$data[9]%'  ";
		if (trim($data[10])!="") $style_cond=" and a.style_ref_no like '%$data[10]%'  "; //else  $style_cond="";
	}
	else if($data[8]==2)
	{
		if (str_replace("'","",$data[6])!="") $job_cond=" and a.job_no_prefix_num like '$data[6]%'  $year_cond"; //else  $job_cond="";
		if (trim($data[9])!="") $order_cond=" and b.po_number like '$data[9]%'  ";
		if (trim($data[10])!="") $style_cond=" and a.style_ref_no like '$data[10]%'  "; //else  $style_cond="";
	}
	else if($data[8]==3)
	{
		if (str_replace("'","",$data[6])!="") $job_cond=" and a.job_no_prefix_num like '%$data[6]'  $year_cond"; //else  $job_cond="";
		if (trim($data[9])!="") $order_cond=" and b.po_number like '%$data[9]'  ";
		if (trim($data[10])!="") $style_cond=" and a.style_ref_no like '%$data[10]'  "; //else  $style_cond="";
	}

	$internal_ref = str_replace("'","",$data[11]);
	$file_no = str_replace("'","",$data[12]);
	if ($file_no=="") $file_no_cond=""; else $file_no_cond=" and b.file_no='".trim($file_no)."' ";
	if ($internal_ref=="") $internal_ref_cond=""; else $internal_ref_cond=" and b.grouping='".trim($internal_ref)."' ";

	if($data[13] !=0) $brand_cond = " and a.brand_id='$data[13]'"; else $brand_cond="";
	if($data[14] !=0) $season_cond = " and a.season_buyer_wise='$data[14]'"; else $season_cond="";
	if($data[15] !=0) $season_year_cond = " and a.season_year='$data[15]'"; else $season_year_cond="";

	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$brand_arr=return_library_array( "select id, brand_name from lib_buyer_brand",'id','brand_name');
	$company_arr=return_library_array( "select id, company_name from lib_company comp where status_active =1 and is_deleted=0 and core_business not in(3) $company_cond order by company_name",'id','company_name');
	$season_arr=return_library_array( "select id, season_name from lib_buyer_season",'id','season_name');

	$arr=array(2=>$buyer_arr,3=>$brand_arr,4=>$season_arr, 7=>$color_library,13=>$item_category);
	if($db_type==0)
	{
		$sql= sql_select("select a.id, a.job_no_prefix_num, a.set_break_down, a.job_no, a.buyer_name, a.style_ref_no, a.quotation_id, a.job_quantity, a.order_uom, a.order_repeat_no, a.brand_id, a.season_year,a.season_buyer_wise, a.body_wash_color, b.id as po_id,  b.po_number, b.po_quantity, b.shipment_date, a.garments_nature, b.grouping, b.file_no, DATEDIFF(pub_shipment_date,po_received_date) as date_diff, SUBSTRING_INDEX(a.`insert_date`, '-', 1) as year from wo_po_details_master a, wo_po_break_down b where a.id=b.job_id and a.garments_nature=$data[5] and a.status_active=1 and b.status_active=1 and b.is_deleted=0 $shipment_date $company $buyer $job_cond $order_cond $style_cond $file_no_cond $internal_ref_cond $year_cond $brand_cond $season_cond $season_year_cond order by a.job_no DESC");
	}
	else if($db_type==2)
	{
		$sql= sql_select("select a.id, a.job_no_prefix_num,a.set_break_down, a.job_no, a.buyer_name, a.style_ref_no, a.quotation_id,a.job_quantity,a.order_uom, a.order_repeat_no, a.brand_id, a.season_year, a.season_buyer_wise, a.body_wash_color, b.po_number, b.po_quantity, b.shipment_date, a.garments_nature, b.grouping, b.file_no, (pub_shipment_date - po_received_date) as  date_diff, to_char(a.insert_date,'YYYY') as year, b.id as po_id from wo_po_details_master a, wo_po_break_down b where a.id=b.job_id and a.garments_nature=$data[5] and a.status_active=1 and b.status_active=1 and b.is_deleted=0 $shipment_date $company $buyer $job_cond $order_cond $style_cond $file_no_cond $internal_ref_cond $year_cond $brand_cond $season_cond $season_year_cond order by a.job_no DESC");
	}
	?>
    <table width="1170" class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" >
        <thead>
            <tr>
                <th width="40">SL</th>
                <th width="40">Job No</th>
                <th width="40">Year</th>
                <th width="100">Buyer</th>
                <th width="70">Brand</th>
                <th width="75">Season</th>
                <th width="50">Season Year</th>
                <th width="100">Style Ref.</th>
                <th width="70">B/W Color</th>
                <th width="50">Quo. ID</th>
                <th width="70">Job Qty.</th>
                <th width="50">Repeat No</th>
                <th width="90">PO No.</th>
                <th width="70">PO Qty.</th>
                <th width="60">Shipment Date</th>
                <th width="50">Ref no</th>
                <th width="50">File No</th>
                <th width="50">Lead time</th>
            </tr>
        </thead>
    </table>
    <div style="max-height:280px; overflow-y:scroll; width:1190px">
        <table width="1170" class="rpt_table" id="tbl_list_search" border="1" rules="all">
        <?		
		$i=1;
		foreach($sql as $row)
		{
			if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
			?>
             <tr bgcolor="<?=$bgcolor; ?>" style="cursor:pointer;" class="tr_<?=$row[csf("id")]; ?>" id="search<?=$i;?>" onClick="js_set_value(<?=$i; ?>,1);">
			 	<td width="40"><?=$i; ?>
			 		<input type="hidden" name="job_id" id="job_id<?=$i; ?>" value="<?=$row[csf("id")]; ?>"/>
			 		<input type="hidden" name="po_id" id="po_id<?=$i; ?>" value="<?=$row[csf("po_id")]; ?>"/>
                    <input type="hidden" name="job_no" id="job_no<?=$i; ?>" value="<?=$row[csf("job_no")]; ?>"/>
			 	</td>
                <td width="40"><?= $row[csf('job_no_prefix_num')]  ?></td>
                <td width="40"><?= $row[csf('year')]  ?></td>
                <td width="100"><?= $buyer_arr[$row[csf('buyer_name')]]  ?></td>
                <td width="70"><?= $brand_arr[$row[csf('brand_id')]]  ?></td>
                <td width="75"><?= $season_arr[$row[csf('year')]]  ?></td>
                <td width="50"><?= $row[csf('season_year')]  ?></td>
                <td width="100"><?= $row[csf('style_ref_no')]  ?></td>
                <td width="70"><?= $row[csf('body_wash_color')]  ?></td>
                <td width="50"><?= $row[csf('quotation_id')]  ?></td>
                <td width="70"><?= $row[csf('job_quantity')]  ?></td>
                <td width="50"><?= $row[csf('order_repeat_no')]  ?></td>
                <td width="90"><?= $row[csf('po_number')]  ?></td>
                <td width="70"><?= $row[csf('po_quantity')]  ?></td>
                <td width="60"><?= $row[csf('shipment_date')]  ?></td>
                <td width="50"><?= $row[csf('grouping')]  ?></td>
                <td width="50"><?= $row[csf('file_no')]  ?></td>
                <td width="50"><?= $row[csf('date_diff')]  ?></td>
            </tr>
            <?
			$i++;
		}
		?>
        </table>
        <table width="1170" cellspacing="0" cellpadding="0" style="border:none" align="center">
        <tr>
            <td align="center" height="30" valign="bottom">
				<div style="width:100%"> 
					<div style="width:50%; float:left" align="left">
							<input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()" /> Check / Uncheck All
					</div>
                	<div style="width:50%; float:left" align="left">
                    <input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px" />
                </div>
				</div>
            </td>
        </tr>
	</table>
    </div>
    <?
	exit();
}

if ($action=="job_popup")
{
  	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
	<script>
			var selected_id = new Array, selected_name = new Array(); selected_attach_id = new Array();
			
			function toggle( x, origColor ) {
				var newColor = 'yellow';
				if ( x.style ) {
					x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
				}
			}

			function check_all_data()
			{
				var row_num=$('#list_view tr').length-1;
				for(var i=1;  i<=row_num;  i++)
				{
					if($("#tr_"+i).css("display") != "none")
					{
						$("#tr_"+i).click();
					}
				}
			}


			function js_set_value(id)
			{
				
					var str=id.split("_");
					
					toggle( document.getElementById( 'tr_' + str[0] ), '#FFFFFF' );
					var strdt=str[2];
					str=str[1];
					
					if( jQuery.inArray(  str , selected_id ) == -1 ) {
					
						selected_id.push( str );
						selected_name.push( strdt );
					}
					else {
						// alert(selected_name)
						
						for( var i = 0; i < selected_id.length; i++ ) {
							if( selected_id[i] == str  ) break;
						}
						// selected_id.splice( i, 1 );
						// selected_name.splice( i,1 );
					}
					var id = '';
					var ddd='';
					for( var i = 0; i < selected_id.length; i++ ) {
						id += selected_id[i] + ',';
						ddd += selected_name[i] + ',';
					}
					
					id = id.substr( 0, id.length - 1 );
					ddd = ddd.substr( 0, ddd.length - 1 );
					$('#job_id').val( id );
					$('#job_no').val( ddd );
					
			}
			//function js_set_value( job_data )
			// {
			// 	var all_data=job_data.split("_");
			// 	document.getElementById('job_id').value=all_data[0];
			// 	document.getElementById('job_no').value=all_data[1];
			// 	parent.emailwindow.hide();
			// }
    </script>
    </head>
    <body>
    <div align="center" style="width:100%;" >
    <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
        <table width="1080" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
            <thead>
            	<tr>
                	<th colspan="13" align="center"><? echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
                </tr>
                <tr>
                    <th width="140" class="must_entry_caption">Company Name</th>
                    <th width="140" class="must_entry_caption">Buyer Name</th>
                    <th width="70">Brand</th>
                    <th width="70">Season</th>
                    <th width="60">Season Year</th>
                    <th width="60">Job No</th>
                    <th width="90">Style Ref.</th>
                    <th width="80">M.Style/Internal Ref</th>
                    <th width="80">File No</th>
                    <th width="90">Order No</th>
                    <th width="130" colspan="2">Ship Date Range</th>
                    <th><input type="hidden" value="0" id="chk_job_wo_po"></th>
                </tr>
            </thead>
            <tr class="general">
                <td>
                    <input type="hidden" id="job_id">
                    <input type="hidden" id="job_no">
                    <input type="hidden" id="garments_nature" value="<?=$garments_nature; ?>">
					<? echo create_drop_down( "cbo_company_mst", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 and core_business not in(3) $company_cond order by company_name","id,company_name",1, "-- Select Company --", $cbo_company_name,"load_drop_down( 'order_entry_controller', this.value, 'load_drop_down_buyer_pop', 'buyer_pop_td' );",'' ); ?>
                </td>
        		<td id="buyer_pop_td"><? echo create_drop_down( "cbo_buyer_id", 140, "select distinct buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name","id,buyer_name",'', 1, "-- Select Buyer --","load_drop_down( 'order_entry_controller', this.value+'*'+1, 'load_drop_down_season', 'season_td'); load_drop_down( 'order_entry_controller', this.value+'*'+1, 'load_drop_down_brand', 'brand_td');" ); ?></td>
        		<td id="brand_td"><? echo create_drop_down( "cbo_brand_id", 70, $blank_array,'', 1, "Brand",$selected, "" ); ?>
        		<td id="season_td"><? echo create_drop_down( "cbo_season_id", 70, $blank_array,'', 1, "Season",$selected, "" ); ?></td>
        		<td><?=create_drop_down( "cbo_season_year", 50, create_year_array(),"", 1,"-Year-", 1, "",0,"" ); ?></td>
                <td><input name="txt_job_prifix" id="txt_job_prifix" class="text_boxes" style="width:50px"></td>
                <td><input name="txt_style" id="txt_style" class="text_boxes" style="width:80px"></td>
                <td><input name="txt_internal_ref" id="txt_internal_ref" class="text_boxes" style="width:70px"></td>
                <td><input name="txt_file_no" id="txt_file_no" class="text_boxes" style="width:70px"></td>
                <td><input name="txt_order_search" id="txt_order_search" class="text_boxes" style="width:80px"></td>
                <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:50px" placeholder="From Date"></td>
                <td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:50px" placeholder="To Date"></td>
                <td align="center">
                	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_id').value+'_'+document.getElementById('chk_job_wo_po').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('garments_nature').value+'_'+document.getElementById('txt_job_prifix').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('txt_order_search').value+'_'+document.getElementById('txt_style').value+'_'+document.getElementById('txt_internal_ref').value+'_'+document.getElementById('txt_file_no').value+'_'+document.getElementById('cbo_brand_id').value+'_'+document.getElementById('cbo_season_id').value+'_'+document.getElementById('cbo_season_year').value, 'create_po_search_list_view', 'search_div', 'order_color_size_update_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:70px;" /></td>
        	</tr>
            <tr>
                <td align="center" colspan="13"><?=load_month_buttons(1); ?></td>
            </tr>
        </table>
        <div id="search_div"></div>
    </form>
    </div>
    </body>
    <script>
		load_drop_down('order_color_size_update_controller', <?=$cbo_company_name; ?>, 'load_drop_down_buyer_pop', 'buyer_pop_td' )
		document.getElementById('cbo_buyer_id').value=<?=$cbo_buyer_name; ?>
    </script>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
    exit();
}

if($action=="create_po_search_list_view")
{
	$data=explode('_',$data);
	if($data[0]==0 && $data[1]==0)
	{
		echo "<span style='color:red; font-weight:bold; font-size:20px; text-align:center'>Please select company or buyer first.";
		die;
	}
	if ($data[0]!=0) $company=" and a.company_name='$data[0]'";
	if(str_replace("'","",$data[1])==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer="";
		}
		else $buyer="";
	}
	else $buyer=" and a.buyer_name='$data[1]'";
	if($db_type==0)
	{
		$year_cond=" and SUBSTRING_INDEX(a.insert_date, '-', 1)=$data[7]";
		if ($data[3]!="" &&  $data[4]!="") $shipment_date = "and b.pub_shipment_date between '".change_date_format($data[3], "yyyy-mm-dd", "-")."' and '".change_date_format($data[4], "yyyy-mm-dd", "-")."'"; else $shipment_date ="";
	}
	else if($db_type==2)
	{
		$year_cond=" and to_char(a.insert_date,'YYYY')=$data[7]";
		if ($data[3]!="" &&  $data[4]!="") $shipment_date = "and b.pub_shipment_date between '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[4], "yyyy-mm-dd", "-",1)."'"; else $shipment_date ="";
	}

	$order_cond=""; $job_cond=""; $style_cond="";
	if($data[8]==1)
	{
		if (str_replace("'","",$data[6])!="") $job_cond=" and a.job_no_prefix_num='$data[6]'  $year_cond";
		if (trim($data[9])!="") $order_cond=" and b.po_number='$data[9]'  "; //else  $order_cond="";
		if (trim($data[10])!="") $style_cond=" and a.style_ref_no='$data[10]'  "; //else  $style_cond="";
	}
	else if($data[8]==4 || $data[8]==0)
	{
		if (str_replace("'","",$data[6])!="") $job_cond=" and a.job_no_prefix_num like '%$data[6]%'  $year_cond";
		if (trim($data[9])!="") $order_cond=" and b.po_number like '%$data[9]%'  ";
		if (trim($data[10])!="") $style_cond=" and a.style_ref_no like '%$data[10]%'  "; //else  $style_cond="";
	}
	else if($data[8]==2)
	{
		if (str_replace("'","",$data[6])!="") $job_cond=" and a.job_no_prefix_num like '$data[6]%'  $year_cond"; //else  $job_cond="";
		if (trim($data[9])!="") $order_cond=" and b.po_number like '$data[9]%'  ";
		if (trim($data[10])!="") $style_cond=" and a.style_ref_no like '$data[10]%'  "; //else  $style_cond="";
	}
	else if($data[8]==3)
	{
		if (str_replace("'","",$data[6])!="") $job_cond=" and a.job_no_prefix_num like '%$data[6]'  $year_cond"; //else  $job_cond="";
		if (trim($data[9])!="") $order_cond=" and b.po_number like '%$data[9]'  ";
		if (trim($data[10])!="") $style_cond=" and a.style_ref_no like '%$data[10]'  "; //else  $style_cond="";
	}

	$internal_ref = str_replace("'","",$data[11]);
	$file_no = str_replace("'","",$data[12]);
	if ($file_no=="") $file_no_cond=""; else $file_no_cond=" and b.file_no='".trim($file_no)."' ";
	if ($internal_ref=="") $internal_ref_cond=""; else $internal_ref_cond=" and b.grouping='".trim($internal_ref)."' ";

	if($data[13] !=0) $brand_cond = " and a.brand_id='$data[13]'"; else $brand_cond="";
	if($data[14] !=0) $season_cond = " and a.season_buyer_wise='$data[14]'"; else $season_cond="";
	if($data[15] !=0) $season_year_cond = " and a.season_year='$data[15]'"; else $season_year_cond="";

	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$brand_arr=return_library_array( "select id, brand_name from lib_buyer_brand",'id','brand_name');
	$company_arr=return_library_array( "select id, company_name from lib_company comp where status_active =1 and is_deleted=0 and core_business not in(3) $company_cond order by company_name",'id','company_name');
	$season_arr=return_library_array( "select id, season_name from lib_buyer_season",'id','season_name');

	$arr=array(2=>$buyer_arr,3=>$brand_arr,4=>$season_arr, 7=>$color_library,13=>$item_category);
	if($db_type==0)
	{
		$sql= "select a.id, a.job_no_prefix_num,a.set_break_down, a.job_no, a.buyer_name, a.style_ref_no, a.quotation_id, a.job_quantity, a.order_uom, a.order_repeat_no, a.brand_id, a.season_year,a.season_buyer_wise, a.body_wash_color, b.po_number, b.po_quantity, b.shipment_date, a.garments_nature, b.grouping, b.file_no, DATEDIFF(pub_shipment_date,po_received_date) as date_diff, SUBSTRING_INDEX(a.`insert_date`, '-', 1) as year from wo_po_details_master a, wo_po_break_down b where a.id=b.job_id and a.garments_nature=$data[5] and a.status_active=1 and b.status_active=1 and b.is_deleted=0 $shipment_date $company $buyer $job_cond $order_cond $style_cond $file_no_cond $internal_ref_cond $year_cond $brand_cond $season_cond $season_year_cond order by a.job_no DESC";
	}
	else if($db_type==2)
	{
		$sql= "select a.id, a.job_no_prefix_num,a.set_break_down, a.job_no, a.buyer_name, a.style_ref_no, a.quotation_id,a.job_quantity,a.order_uom, a.order_repeat_no, a.brand_id, a.season_year, a.season_buyer_wise, a.body_wash_color, b.po_number, b.po_quantity, b.shipment_date, a.garments_nature, b.grouping, b.file_no, (pub_shipment_date - po_received_date) as  date_diff, to_char(a.insert_date,'YYYY') as year from wo_po_details_master a, wo_po_break_down b where a.id=b.job_id and a.garments_nature=$data[5] and a.status_active=1 and b.status_active=1 and b.is_deleted=0 $shipment_date $company $buyer $job_cond $order_cond $style_cond $file_no_cond $internal_ref_cond $year_cond $brand_cond $season_cond $season_year_cond order by a.job_no DESC";
	}


	// echo  create_list_view("list_view", "Job No,Year,Buyer,Brand,Season,Season Year,Style Ref.,B/W Color,Quo. ID,Job Qty.,Repeat No,PO No.,PO Qty.,Shipment Date,Ref no, File No,Lead time", "40,40,100,70,75,50,100,70,50,70,50,90,70,60,50,50,50","1170","300",0, $sql , "js_set_value", "id,job_no", "", 1, "0,0,buyer_name,brand_id,season_buyer_wise,0,0,body_wash_color,0,0,0,0,0,0,0,0,0", $arr , "job_no_prefix_num,year,buyer_name,brand_id,season_buyer_wise,season_year,style_ref_no,body_wash_color,quotation_id,job_quantity,order_repeat_no,po_number,po_quantity,shipment_date,grouping,file_no,date_diff", "",'','0,0,0,0,0,0,0,0,1,0,0,0,0,3,0,0,0');

	echo  create_list_view("list_view", "Job No,Year,Buyer,Brand,Season,Season Year,Style Ref.,B/W Color,Quo. ID,Job Qty.,Repeat No,PO No.,PO Qty.,Shipment Date,Ref no, File No,Lead time", "40,40,100,70,75,50,100,70,50,70,50,90,70,60,50,50,50","1170","280",0, $sql , "js_set_value", "id,job_no", "", 1, "0,0,buyer_name,brand_id,season_buyer_wise,0,0,body_wash_color,0,0,0,0,0,0,0,0,0", $arr , "job_no_prefix_num,year,buyer_name,brand_id,season_buyer_wise,season_year,style_ref_no,body_wash_color,quotation_id,job_quantity,order_repeat_no,po_number,po_quantity,shipment_date,grouping,file_no,date_diff", "",'setFilterGrid("list_view",-1);','0,0,0,0,0,0,0,0,1,0,0,0,0,3,0,0,0','',1) ;
	exit();
}

if($action=='report_generate'){
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$buyerArr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name");
	$country_arr=return_library_array( "select id,country_name from lib_country", "id", "country_name");
	$colorArr = return_library_array("select id,color_name from lib_color","id","color_name");
	$itemSizeArr = return_library_array("select id,size_name from  lib_size ","id","size_name");
	$order_id_array=explode(",", str_replace("'",'',$hidden_po_id));
	
	$color_size_data=sql_select("SELECT a.id as job_id, a.buyer_name, a.job_no_prefix_num, a.insert_date, a.style_ref_no, a.company_name, b.id as po_id, b.po_number, b.file_year, b.file_no, b.sc_lc, b.matrix_type,  c.id as color_size_id, c.country_id, c.color_number_id, c.size_number_id, c.order_quantity, c.order_rate, c.order_total, c.item_number_id, b.rfi_date,b.shipment_date,c.cartoon_qty,c.approx_cbm,c.approx_ship_mode,c.gross_weight,c.net_weight from wo_po_details_master a join wo_po_break_down b on a.id=b.job_id join wo_po_color_size_breakdown c on a.id=c.job_id and b.id=c.po_break_down_id where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 ".where_con_using_array($order_id_array,0,'a.id')."");
	

	foreach ($color_size_data as $data) {
		$job_id_arr[$data[csf('job_id')]] = $data[csf('job_id')];
		$company_id_arr[$data[csf('company_name')]] = $data[csf('company_name')];
	}
	$approved_arr=sql_select("SELECT approved, job_id from WO_PRE_COST_MST where  status_active=1 and is_deleted=0 ".where_con_using_array($job_id_arr,0,'job_id')."");
	$budget_approved=array();
	foreach ($approved_arr as $row) {
		if($row[csf('approved')]==1 || $row[csf('approved')]==3){
			$budget_approved[$row[csf('job_id')]]=1;
		}
		else{
			$budget_approved[$row[csf('job_id')]]=0;
		}
	}
	$wo_po_ratio_sql=sql_select("SELECT c.id as ratio_breakdown_id, c.country_id, c.gmts_item_id, c.color_id, c.size_id, c.po_id from wo_po_details_master a join wo_po_break_down b on a.id=b.job_id join wo_po_ratio_breakdown c on b.id=c.po_id where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 ".where_con_using_array($job_id_arr,0,'a.id')."");
	if(count($wo_po_ratio_sql)>0){
		foreach ($wo_po_ratio_sql as $row) {
			$key=$row[csf('po_id')].'*'.$row[csf('country_id')].'*'.$row[csf('gmts_item_id')].'*'.$row[csf('color_id')].'*'.$row[csf('size_id')];
			$ratio_id_arr[$key]=$row[csf('ratio_breakdown_id')];
		}
	}
	$ship_date_arr=sql_select("SELECT shiping_status, job_id from wo_po_break_down where  status_active=1 and is_deleted=0 and shiping_status=3".where_con_using_array($job_id_arr,0,'job_id')."");
	$sipping_approved=array();
	if ( count($ship_date_arr)>0)
	{
		foreach ($ship_date_arr as $row) {
			if($row[csf('shiping_status')]==3){
				$sipping_approved[$row[csf('job_id')]]=1;
			}
			else{
				$sipping_approved[$row[csf('job_id')]]=0;
			}
			$bgcolor="#ff0000";
		}
	}
	$file_year_sql="SELECT distinct(lc_year) as lc_sc_year from com_export_lc where status_active=1 and is_deleted=0 ".where_con_using_array($company_id_arr,0,'beneficiary_name')." union select distinct(sc_year) as lc_sc_year from com_sales_contract where status_active=1 and is_deleted=0 ".where_con_using_array($company_id_arr,0,'beneficiary_name')."";

	?>
		<table width="1320" cellspacing="0" cellpadding="0" border="1" class="rpt_table" rules="all">
			<thead>
				<tr>
					<td colspan="7" align="right">Rate/RFI Copy Level</td> 
                    <td width="80" align="center">Job</td>
                    <td width="60" align="center">PO</td>
                    <td width="40" align="center">Country</td>
                    <td width="40" align="center">Color</td>
                    <td width="40" align="center">Size</td>
                    <td colspan="10"></td>
				</tr>
				<tr>
					<th colspan="7"></th> 
                    <th width="80"><input type="radio" value="0" name="copy_rate" onClick="set_checkvalue(1);" id="chk_job"></th>
                    <th width="60"><input type="radio" value="0" name="copy_rate" onClick="set_checkvalue(2);" id="chk_po"></th>
                    <th width="40"><input type="radio" value="0" name="copy_rate" onClick="set_checkvalue(3);" id="chk_country"></th>
                    <th width="40"><input type="radio" value="0" name="copy_rate" onClick="set_checkvalue(4);" id="chk_color"></th>
                    <th width="40"><input type="radio" value="0" name="copy_rate" onClick="set_checkvalue(5);" id="chk_size"></th>
                    <th colspan="10">
                    </th>
				</tr>
                <tr>
                    <th width="30">SL</th>   
                    <th width="80">Buyer</th>
                    <th width="60">Job NO</th>
                    <th width="40">Job Year</th>
                    <th width="80">Style No</th>
                    <th width="80">PO No</th>
                    <th width="80">Country</th>
                    <th width="60">Color</th>
                    <th width="40">Size</th>
                    <th width="40">Qty.</th>
                    <th width="40">FOB Rate</th>
                    <th width="60">FOB Amount</th>
                    <th width="40">File Year</th>
                    <th width="60">File No</th>
                    <th width="60">SC/LC No</th>
					<th width="60">Org. ship Date</th>
                    <th width="60" title="Ready For Inspection">Inspection Date</th>
					<th width="60">Carton Qty</th>
					<th width="60">Approx CBM</th>
					<th width="60">Gross Weight</th>
					<th width="60">Net Weight</th>
					<th width="60">Approx Ship Mode</th>
                </tr>
            </thead>
            <tbody id="color_size_data">
            	<? $i++;
            	$disabled="";
            	foreach ($color_size_data as $row) {
            		$gmts_ratio_id=0;
            		if($budget_approved[$row[csf('job_id')]]==1){
            			$disabled="disabled";
            		}
            		if($row[csf('matrix_type')]==3){
            			$datakey=$row[csf('po_id')].'*'.$row[csf('country_id')].'*'.$row[csf('item_number_id')].'*'.$row[csf('color_number_id')].'*'.$row[csf('size_number_id')];
            			$gmts_ratio_id=$ratio_id_arr[$datakey];
            		}
            	 ?>
            		<tr>
            			<td style="background-color:<? echo $bgcolor ;?>"><?= $i;?></td>
            			<td style="background-color:<? echo $bgcolor ;?>"><?= $buyerArr[$row[csf('buyer_name')]];?></td>
            			<td style="background-color:<? echo $bgcolor ;?>"><?= $row[csf('job_no_prefix_num')]; ?></td>
            			<td style="background-color:<? echo $bgcolor ;?>"><?= date("Y", strtotime($row[csf('insert_date')]));?></td>
            			<td style="background-color:<? echo $bgcolor ;?>"><?= $row[csf('style_ref_no')];?></td>
            			<td style="background-color:<? echo $bgcolor ;?>"><?= $row[csf('po_number')];?>
            				<input type="hidden" id="poid_<?= $i ?>" value="<?= $row[csf('po_id')]?>">
            				<input type="hidden" id="jobid_<?= $i ?>" value="<?= $row[csf('job_id')]?>">
            				<input type="hidden" id="colorsizeid_<?= $i ?>" value="<?= $row[csf('color_size_id')]?>">
            				<input type="hidden" id="ratioid_<?= $i ?>" value="<?= $gmts_ratio_id?>">
            				<input type="hidden" id="approved_<?= $i ?>" value="<?= $budget_approved?>">
            			</td>
            			<td style="background-color:<? echo $bgcolor ;?>"><?= $country_arr[$row[csf('country_id')]];?>
            				<input type="hidden" id="countryid_<?= $i ?>" value="<?= $row[csf('country_id')]?>">
            			</td>
            			<td style="background-color:<? echo $bgcolor ;?>"><?= $colorArr[$row[csf('color_number_id')]];?>
            				<input type="hidden" id="gmtscolorid_<?= $i ?>" value="<?= $row[csf('color_number_id')]?>">
            			</td>
            			<td style="background-color:<? echo $bgcolor ;?>"><?= $itemSizeArr[$row[csf('size_number_id')]];?>
            				<input type="hidden" id="gmtssizesid_<?= $i ?>" value="<?= $row[csf('size_number_id')]?>">
            			</td>
            			<td style="background-color:<? echo $bgcolor ;?>"><?= $row[csf('order_quantity')];?>
            				<input type="hidden" id="gmtsqty_<?= $i ?>" value="<?= $row[csf('order_quantity')]?>">
            			</td>
            			<td style="background-color:<? echo $bgcolor ;?>"><input type="text" class="text_boxes_numeric" id="orderrate_<?= $i;?>" value="<?= $row[csf('order_rate')];?>" onChange="copy_value(this.value,'orderrate_',<?= $i ?>)" style="width:60px;" <? echo $disabled ?>></td>
            			<td style="background-color:<? echo $bgcolor ;?>"><input type="text" id="ordeamount_<?= $i;?>" class="text_boxes_numeric" style="width:60px;" value="<?= $row[csf('order_total')];?>" readonly></td>
            			<td style="background-color:<? echo $bgcolor ;?>"><? echo create_drop_down( "fileyear_".$i,80,$file_year_sql,"lc_sc_year,lc_sc_year", 1, "-- Select --",$row[csf('file_year')],"copy_value(this.value,'fileyear_',$i)"); ?>
            			</td>
            			<td style="background-color:<? echo $bgcolor ;?>"><input type="text" class="text_boxes" id="fileno_<?= $i;?>" value="<?= $row[csf('file_no')];?>" style="width:60px;" onChange="copy_value(this.value,'fileno_',<?= $i ?>)"></td>
            			<td style="background-color:<? echo $bgcolor ;?>"><input type="text" class="text_boxes" id="sclcno_<?= $i;?>" value="<?= $row[csf('sc_lc')];?>" style="width:60px;" onChange="copy_value(this.value,'sclcno_',<?= $i ?>)"></td>
						<td style="background-color:<? echo $bgcolor ;?>"><input type="text" class="datepicker" id="txt_org_shipment_date_<?= $i;?>" value="<?= change_date_format($row[csf('shipment_date')], "yyyy-mm-dd", "-");;?>" style="width:60px;" disable ></td>
            			<td style="background-color:<? echo $bgcolor ;?>"><input type="text" class="datepicker" id="txt_rfi_date_<?= $i;?>" value="<?= change_date_format($row[csf('rfi_date')], "yyyy-mm-dd", "-");?>" style="width:60px;" onChange="copy_value(this.value,'txt_rfi_date_',<?= $i ?>)"></td>
						<td style="background-color:<? echo $bgcolor ;?>"><input type="text" class="text_boxes_numeric" id="cartoon_qty_<?= $i;?>" value="<?= $row[csf('cartoon_qty')];?>" style="width:60px;" onChange="copy_value(this.value,'cartoon_qty_',<?= $i ?>)"></td>
						<td style="background-color:<? echo $bgcolor ;?>"><input type="text" class="text_boxes_numeric" id="approx_cbm_<?= $i;?>" value="<?= $row[csf('approx_cbm')];?>" style="width:60px;" onChange="copy_value(this.value,'approx_cbm_',<?= $i ?>)"></td>
						
						<td style="background-color:<? echo $bgcolor ;?>"><input type="text" class="text_boxes_numeric" id="gross_weight_<?= $i;?>" value="<?= $row[csf('gross_weight')];?>" style="width:60px;" onChange="copy_value(this.value,'gross_weight_',<?= $i ?>)"></td>

						<td style="background-color:<? echo $bgcolor ;?>"><input type="text" class="text_boxes_numeric" id="net_weight_<?= $i;?>" value="<?= $row[csf('net_weight')];?>" style="width:60px;" onChange="copy_value(this.value,'net_weight_',<?= $i ?>)"></td>

						<td style="background-color:<? echo $bgcolor ;?>"><? echo create_drop_down( "approx_ship_mode_".$i,80,$shipment_mode,"", 1, "-- Select --",$row[csf('approx_ship_mode')],""); ?>
            		</tr>
            	<? 
            		$total_qty+=$row[csf('order_quantity')];
            		$total_amount+=$row[csf('order_total')];
            		$i++;
            	} 
            	?>
            </tbody>
            <tfoot>
            	<tr>
            		<td></td>
            		<td></td>
            		<td></td>
            		<td></td>
            		<td></td>
            		<td></td>
            		<td></td>
            		<td></td>
            		<td></td>
            		<td><input type="text" class="text_boxes" id="total_qty" value="<?= $total_qty;?>" style="width:60px;" readonly></td>
            		<td></td>
            		<td><input type="text" class="text_boxes" id="total_amount" value="<?= $total_amount;?>" style="width:60px;" readonly></td>
            		<td></td>
            		<td></td>
            		<td></td>
            		<td></td>
					<td></td>
            		<td></td>
            		<td></td>
					<td></td>
					<td></td>
            	</tr>
            </tfoot>
		</table>

		<br>
		<? echo load_submit_buttons( $permission, "fnc_order_entry_details", 1,0 ,"",2); ?>
		<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	<?
}

if($action=='save_update_delete_dtls'){
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$con = connect();
	if ($operation==1)
	{
		$field_array_up="cartoon_qty*approx_cbm*gross_weight*net_weight*approx_ship_mode*order_rate*order_total*updated_by*update_date";
		$field_ratio_up="ratio_rate*updated_by*update_date";
		$po_field_array_up="file_year*file_no*sc_lc*updated_by*update_date";
		$pofield_array_up="unit_price*po_total_price*rfi_date";
		$counter=0;
		$rcounter=0;
		$rID1=1;
		for($m=1; $m<=$row_table; $m++)
		{
			$orderrate="orderrate_".$m;
			$ordeamount="ordeamount_".$m;
			$fileyear="fileyear_".$m;
			$fileno="fileno_".$m;
			$cartoonQty="cartoon_qty_".$m;
			$approxShipMode="approx_ship_mode_".$m;
			$approxCbm="approx_cbm_".$m;
			$gross_weight="gross_weight_".$m;
			$net_weight="net_weight_".$m;
			$sclcno="sclcno_".$m;
			$poid="poid_".$m;
			$colorsizeid="colorsizeid_".$m;
			$gmtsqty="gmtsqty_".$m;
			$ratioid="ratioid_".$m;
			$approved="approved_".$m;
			$txt_rfi_date="txt_rfi_date_".$m;
			$jobid="jobid_".$m;
			$ratio_id=str_replace("'",'',$$ratioid);
			$approved_id=str_replace("'",'',$$approved);
			$poidarr[str_replace("'",'',$$poid)]=str_replace("'",'',$$poid);
			$id_arr[]=str_replace("'",'',$$colorsizeid);
			$jobid_arr[str_replace("'",'',$$jobid)]=str_replace("'",'',$$jobid);
			$po_wise_rate[str_replace("'",'',$$poid)]['rate']+=str_replace("'",'',$$orderrate)*1;
			$po_wise_rate[str_replace("'",'',$$poid)]['counter']+=1;
			$po_wise_rate[str_replace("'",'',$$poid)]['qty']+=str_replace("'",'',$$gmtsqty)*1;
			$po_wise_rate[str_replace("'",'',$$poid)]['rfi']=$$txt_rfi_date;
			$data_array_up[str_replace("'",'',$$colorsizeid)] =explode("*",("".$$cartoonQty."*".$$approxCbm."*".$$gross_weight."*".$$net_weight."*".$$approxShipMode."*".$$orderrate."*".$$ordeamount."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
			if($ratio_id!=0){
				$id_ratioarr[]=$ratio_id;
				$data_ratio_up[$ratio_id] =explode("*",("".$$orderrate."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
				$rcounter++;
			}
			$counter++;
			if(str_replace("'",'',$$approved) !=1){
				if($data_array_up!="" && $counter==100){
					$rID1=execute_query(bulk_update_sql_statement("wo_po_color_size_breakdown", "id",$field_array_up,$data_array_up,$id_arr ));
					$counter=0;
					$id_arr=array();
					$data_array_up=array();
				}
				if( $data_ratio_up!="" && $rcounter==100){
					$rID1=execute_query(bulk_update_sql_statement("wo_po_ratio_breakdown", "id",$field_ratio_up,$data_ratio_up,$id_ratioarr ));
					$rcounter=0;
					$id_ratioarr=array();
					$data_ratio_up=array();
				}
			}
			
			$po_data_array_up="".$$fileyear."*".$$fileno."*".$$sclcno."*".$$txt_rfi_date."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		}
		if($data_array_up!="" && $counter!=100 && $approved_id != 1){
			$rID1=execute_query(bulk_update_sql_statement("wo_po_color_size_breakdown", "id",$field_array_up,$data_array_up,$id_arr ));
		}
		if( $data_ratio_up!="" && $rcounter!=100 && $approved_id != 1){
			$rID1=execute_query(bulk_update_sql_statement("wo_po_ratio_breakdown", "id",$field_ratio_up,$data_ratio_up,$id_ratioarr ));
		}
		foreach ($po_wise_rate as $po_id => $data) {
			$avg_rate=number_format($data['rate']/$data['counter'],4);
			$total_price=$avg_rate*$data['qty'];
			$poid_arr[]=$po_id;
			$rfi_date=$data['rfi'];
			$podata_array_up[$po_id] =explode("*",("".$avg_rate."*".$total_price."*".$rfi_date.""));
		}
		if($podata_array_up!=""){
			$rID3=execute_query(bulk_update_sql_statement("wo_po_break_down", "id",$pofield_array_up,$podata_array_up,$poid_arr ));
		}	
		if($po_data_array_up!='' && count($jobid_arr)>0){
			foreach ($jobid_arr as $job_id) {
				$rID2=sql_update("wo_po_break_down",$po_field_array_up,$po_data_array_up,"job_id",$job_id,1);
			}			
		}
		if($db_type==0)
		{
			if($rID1==1)
			{
				mysql_query("COMMIT");
				echo "1**".str_replace("'",'',$hidden_job_id);
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**".str_replace("'",'',$hidden_job_id);
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID1==1){
				oci_commit($con);
				echo "1**".str_replace("'",'',$hidden_job_id);
			}
			else{
				oci_rollback($con);
				echo "10**".str_replace("'",'',$hidden_job_id);
			}
		}
		disconnect($con);
		die;
	}
}

if($action=='report_generate2'){
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$buyerArr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name");
	$country_arr=return_library_array( "select id,country_name from lib_country", "id", "country_name");
	$colorArr = return_library_array("select id,color_name from lib_color","id","color_name");
	$itemSizeArr = return_library_array("select id,size_name from  lib_size ","id","size_name");
	$order_id_array=explode(",", str_replace("'",'',$hidden_po_id));
	
	$color_size_data=sql_select("SELECT a.id as job_id, a.buyer_name, a.job_no_prefix_num, a.insert_date, a.style_ref_no, a.company_name, b.id as po_id, b.po_number, b.file_year, b.file_no, b.sc_lc, b.matrix_type,  c.id as color_size_id, c.country_id, c.color_number_id, c.size_number_id, c.order_quantity, c.order_rate, c.order_total, c.item_number_id, b.rfi_date,b.shipment_date,c.cartoon_qty,c.approx_cbm,c.approx_ship_mode,c.gross_weight,c.net_weight,b.pub_shipment_date,b.po_received_date,c.country_ship_date from wo_po_details_master a join wo_po_break_down b on a.id=b.job_id join wo_po_color_size_breakdown c on a.id=c.job_id and b.id=c.po_break_down_id where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 ".where_con_using_array($order_id_array,0,'a.id')."");
	

	foreach ($color_size_data as $data) {
		$job_id_arr[$data[csf('job_id')]] = $data[csf('job_id')];
		$company_id_arr[$data[csf('company_name')]] = $data[csf('company_name')];
	}
	$ship_date_arr=sql_select("SELECT shiping_status, job_id from wo_po_break_down where  status_active=1 and is_deleted=0 and shiping_status=3".where_con_using_array($job_id_arr,0,'job_id')."");
	$sipping_approved=array();
	if ( count($ship_date_arr)>0)
	{
		foreach ($ship_date_arr as $row) {
			if($row[csf('shiping_status')]==3){
				$sipping_approved[$row[csf('job_id')]]=1;
			}
			else{
				$sipping_approved[$row[csf('job_id')]]=0;
			}
			$bgcolor="#ff0000";
		}
	}
	$approved_arr=sql_select("SELECT approved, job_id from WO_PRE_COST_MST where  status_active=1 and is_deleted=0 ".where_con_using_array($job_id_arr,0,'job_id')."");
	$budget_approved=array();
	foreach ($approved_arr as $row) {
		if($row[csf('approved')]==1 || $row[csf('approved')]==3){
			$budget_approved[$row[csf('job_id')]]=1;
		}
		else{
			$budget_approved[$row[csf('job_id')]]=0;
		}
	}
	$wo_po_ratio_sql=sql_select("SELECT c.id as ratio_breakdown_id, c.country_id, c.gmts_item_id, c.color_id, c.size_id, c.po_id from wo_po_details_master a join wo_po_break_down b on a.id=b.job_id join wo_po_ratio_breakdown c on b.id=c.po_id where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 ".where_con_using_array($job_id_arr,0,'a.id')."");
	if(count($wo_po_ratio_sql)>0){
		foreach ($wo_po_ratio_sql as $row) {
			$key=$row[csf('po_id')].'*'.$row[csf('country_id')].'*'.$row[csf('gmts_item_id')].'*'.$row[csf('color_id')].'*'.$row[csf('size_id')];
			$ratio_id_arr[$key]=$row[csf('ratio_breakdown_id')];
		}
	}
	$file_year_sql="SELECT distinct(lc_year) as lc_sc_year from com_export_lc where status_active=1 and is_deleted=0 ".where_con_using_array($company_id_arr,0,'beneficiary_name')." union select distinct(sc_year) as lc_sc_year from com_sales_contract where status_active=1 and is_deleted=0 ".where_con_using_array($company_id_arr,0,'beneficiary_name')."";

	?>
		<table width="1320" cellspacing="0" cellpadding="0" border="1" class="rpt_table" rules="all">
			<thead>
				<tr>
					<td colspan="9" align="right">Ship Date Copy Level</td> 
                    <td width="80" align="center">Job</td>
                    <td width="60" align="center">PO</td>
                    <td width="40" align="center">Country</td>
                    <td colspan="4"></td>
				</tr>
				<tr>
					<th colspan="9"></th> 
                    <th width="80"><input type="radio" value="0" name="copy_rate" onClick="set_checkvalue(1);" id="chk_job"></th>
                    <th width="60"><input type="radio" value="0" name="copy_rate" onClick="set_checkvalue(2);" id="chk_po"></th>
                    <th width="40"><input type="radio" value="0" name="copy_rate" onClick="set_checkvalue(3);" id="chk_country"></th>
                    <th colspan="4">
                    </th>
				</tr>
                <tr>
					<th width="30">SL</th>   
                    <th width="80">Buyer</th>
                    <th width="60">Job NO</th>
                    <th width="40">Job Year</th>
                    <th width="80">Style No</th>
                    <th width="80">PO No</th>
                    <th width="80">Country</th>
                    <th width="40">Qty.</th>
                    <th width="60">Po Received Date</th>
                    <th width="60">Publish Shipdate</th>
					<th width="60">PO Ship Date</th>
					<th width="60">Country Ship Date</th>
					<th width="40">FOB Rate</th>
                    <th width="60">FOB Amount</th>
                    <th width="40">File Year</th>
                    <th width="60">File No</th>
                </tr>
            </thead>
            <tbody id="color_size_data">
            	<? $i++;
            	$disabled="";
            	foreach ($color_size_data as $row) {
            		$gmts_ratio_id=0;
            		//if($budget_approved[$row[csf('job_id')]]==1 || $sipping_approved[$row[csf('job_id')]]==1){
					if($sipping_approved[$row[csf('job_id')]]==1 && $user_level!=2){
            			$disabled="disabled";
            		}
            		if($row[csf('matrix_type')]==3){
            			$datakey=$row[csf('po_id')].'*'.$row[csf('country_id')].'*'.$row[csf('item_number_id')].'*'.$row[csf('color_number_id')].'*'.$row[csf('size_number_id')];
            			$gmts_ratio_id=$ratio_id_arr[$datakey];
            		}
            	 ?>
            		<tr>
            			<td style="background-color:<? echo $bgcolor ;?>"><?= $i;?></td>
            			<td style="background-color:<? echo $bgcolor ;?>"><?= $buyerArr[$row[csf('buyer_name')]];?></td>
            			<td style="background-color:<? echo $bgcolor ;?>"><?= $row[csf('job_no_prefix_num')]; ?></td>
            			<td style="background-color:<? echo $bgcolor ;?>"><?= date("Y", strtotime($row[csf('insert_date')]));?></td>
            			<td style="background-color:<? echo $bgcolor ;?>"><?= $row[csf('style_ref_no')];?></td>
            			<td style="background-color:<? echo $bgcolor ;?>"><?= $row[csf('po_number')];?>
            				<input type="hidden" id="poid_<?= $i ?>" value="<?= $row[csf('po_id')]?>">
            				<input type="hidden" id="jobid_<?= $i ?>" value="<?= $row[csf('job_id')]?>">
            				<input type="hidden" id="colorsizeid_<?= $i ?>" value="<?= $row[csf('color_size_id')]?>">
            				<input type="hidden" id="ratioid_<?= $i ?>" value="<?= $gmts_ratio_id?>">
            				<input type="hidden" id="approved_<?= $i ?>" value="<?= $budget_approved?>">
            			</td>
            			<td style="background-color:<? echo $bgcolor ;?>"><?= $country_arr[$row[csf('country_id')]];?>
            				<input type="hidden" id="countryid_<?= $i ?>" value="<?= $row[csf('country_id')]?>">
            			</td>
            			<td style="background-color:<? echo $bgcolor ;?>"><?= $row[csf('order_quantity')];?>
            				<input type="hidden" id="gmtsqty_<?= $i ?>" value="<?= $row[csf('order_quantity')]?>">
            			</td>
						<td style="background-color:<? echo $bgcolor ;?>">
							<input type="text" class="datepicker" id="txt_po_received_date_<?= $i;?>" value="<?= change_date_format($row[csf('po_received_date')], "yyyy-mm-dd", "-");;?>" style="width:60px;" disabled >
						</td>
							<td style="background-color:<? echo $bgcolor ;?>"><input type="text" class="datepicker" id="txt_pub_shipment_date_<?= $i;?>" value="<?= change_date_format($row[csf('pub_shipment_date')], "yyyy-mm-dd", "-");?>" style="width:60px;"  <? echo $disabled ?>>
						</td>
						<td style="background-color:<? echo $bgcolor ;?>">
							<input type="text" class="datepicker" id="txt_org_shipment_date_<?= $i;?>" value="<?= change_date_format($row[csf('shipment_date')], "yyyy-mm-dd", "-");?>" style="width:60px;"  <? echo $disabled ?>>
						</td>
						<td style="background-color:<? echo $bgcolor ;?>">
							<input type="text" class="datepicker" id="txt_country_ship_date_<?= $i;?>" value="<?= change_date_format($row[csf('country_ship_date')], "yyyy-mm-dd", "-");?>" style="width:60px;"  <? echo $disabled ?>>
						</td>
            			<td style="background-color:<? echo $bgcolor ;?>">
							<input type="text" class="text_boxes_numeric" id="orderrate_<?= $i;?>" value="<?= $row[csf('order_rate')];?>" onChange="copy_value(this.value,'orderrate_',<?= $i ?>)" style="width:60px;" <? echo $disabled ?>>
						</td>
            			<td style="background-color:<? echo $bgcolor ;?>">
							<input type="text" id="ordeamount_<?= $i;?>" class="text_boxes_numeric" style="width:60px;" value="<?= $row[csf('order_total')];?>" readonly>
						</td>
            			<td style="background-color:<? echo $bgcolor ;?>">
							<? echo create_drop_down( "fileyear_".$i,80,$file_year_sql,"lc_sc_year,lc_sc_year", 1, "-- Select --",$row[csf('file_year')],"copy_value(this.value,'fileyear_',$i)"); ?>
            			</td>
            			<td style="background-color:<? echo $bgcolor ;?>">
							<input type="text" class="text_boxes" id="fileno_<?= $i;?>" value="<?= $row[csf('file_no')];?>" style="width:60px;" onChange="copy_value(this.value,'fileno_',<?= $i ?>)">
						</td>

            		</tr>
            	<? 
            		$total_qty+=$row[csf('order_quantity')];
            		$total_amount+=$row[csf('order_total')];
            		$i++;
            	} 
            	?>
            </tbody>
            <tfoot>
            	<tr>
            		<td></td>
            		<td></td>
            		<td></td>
            		<td></td>
            		<td></td>
            		<td></td>
            		<td></td>
            		<td><input type="text" class="text_boxes" id="total_qty" value="<?= $total_qty;?>" style="width:60px;" readonly></td>
            		<td></td>
            		<td></td>
            		<td></td>
            		<td></td>
            		<td><input type="text" class="text_boxes" id="total_amount" value="<?= $total_amount;?>" style="width:60px;" readonly></td>
					<td></td>
            		<td></td>
            	</tr>
            </tfoot>
		</table>

		<br>
		<? echo load_submit_buttons( $permission, "fnc_order_entry_details2", 1,0 ,"",2); ?>
		<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	<?
}

if($action=='save_update_delete_dtls2'){
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$con = connect();
	if ($operation==1)
	{
		$field_array_up="order_rate*order_total*country_ship_date*updated_by*update_date";
		$field_ratio_up="ratio_rate*updated_by*update_date";
		$po_field_array_up="file_year*file_no*pub_shipment_date*shipment_date*updated_by*update_date";
		$pofield_array_up="unit_price*po_total_price*pub_shipment_date*shipment_date";
		$counter=0;
		$rcounter=0;
		$rID1=1;
		for($m=1; $m<=$row_table; $m++)
		{
			$orderrate="orderrate_".$m;
			$ordeamount="ordeamount_".$m;
			$fileyear="fileyear_".$m;
			$fileno="fileno_".$m;
			$poid="poid_".$m;
			$colorsizeid="colorsizeid_".$m;
			$gmtsqty="gmtsqty_".$m;
			$ratioid="ratioid_".$m;
			$approved="approved_".$m;
			$jobid="jobid_".$m;
			$txt_pub_shipment_date="txt_pub_shipment_date_".$m;
			$txt_org_shipment_date="txt_org_shipment_date_".$m;
			$txt_country_ship_date="txt_country_ship_date_".$m;
			$ratio_id=str_replace("'",'',$$ratioid);
			$approved_id=str_replace("'",'',$$approved);
			$poidarr[str_replace("'",'',$$poid)]=str_replace("'",'',$$poid);
			$id_arr[]=str_replace("'",'',$$colorsizeid);
			$jobid_arr[str_replace("'",'',$$jobid)]=str_replace("'",'',$$jobid);
			$po_wise_rate[str_replace("'",'',$$poid)]['rate']+=str_replace("'",'',$$orderrate)*1;
			$po_wise_rate[str_replace("'",'',$$poid)]['counter']+=1;
			$po_wise_rate[str_replace("'",'',$$poid)]['qty']+=str_replace("'",'',$$gmtsqty)*1;
			$po_wise_rate[str_replace("'",'',$$poid)]['pub']=$$txt_pub_shipment_date;
			$po_wise_rate[str_replace("'",'',$$poid)]['org']=$$txt_org_shipment_date;
			$data_array_up[str_replace("'",'',$$colorsizeid)] =explode("*",("".$$orderrate."*".$$ordeamount."*".$$txt_country_ship_date."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
			if($ratio_id!=0){
				$id_ratioarr[]=$ratio_id;
				$data_ratio_up[$ratio_id] =explode("*",("".$$orderrate."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
				$rcounter++;
			}
			$counter++;
			if(str_replace("'",'',$$approved) !=1){
				if($data_array_up!="" && $counter==100){
					$rID1=execute_query(bulk_update_sql_statement("wo_po_color_size_breakdown", "id",$field_array_up,$data_array_up,$id_arr ));
					$counter=0;
					$id_arr=array();
					$data_array_up=array();
				}
				if( $data_ratio_up!="" && $rcounter==100){
					$rID1=execute_query(bulk_update_sql_statement("wo_po_ratio_breakdown", "id",$field_ratio_up,$data_ratio_up,$id_ratioarr ));
					$rcounter=0;
					$id_ratioarr=array();
					$data_ratio_up=array();
				}
			}

			$po_data_array_up="".$$fileyear."*".$$fileno."*".$$txt_pub_shipment_date."*".$$txt_org_shipment_date."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		}
		if($data_array_up!="" && $counter!=100 && $approved_id != 1){
			$rID1=execute_query(bulk_update_sql_statement("wo_po_color_size_breakdown", "id",$field_array_up,$data_array_up,$id_arr ));
		}
		if( $data_ratio_up!="" && $rcounter!=100 && $approved_id != 1){
			$rID1=execute_query(bulk_update_sql_statement("wo_po_ratio_breakdown", "id",$field_ratio_up,$data_ratio_up,$id_ratioarr ));
		}
		foreach ($po_wise_rate as $po_id => $data) {
			$avg_rate=number_format($data['rate']/$data['counter'],4);
			$total_price=$avg_rate*$data['qty'];
			$poid_arr[]=$po_id;
			$pub_date=$data['pub'];
			$org_date=$data['org'];
			$podata_array_up[$po_id] =explode("*",("".$avg_rate."*".$total_price."*".$pub_date."*".$org_date.""));
		}
		if($podata_array_up!=""){
			$rID3=execute_query(bulk_update_sql_statement("wo_po_break_down", "id",$pofield_array_up,$podata_array_up,$poid_arr ));
		}	
		if($po_data_array_up!='' && count($jobid_arr)>0){
			foreach ($jobid_arr as $job_id) {
				$rID2=sql_update("wo_po_break_down",$po_field_array_up,$po_data_array_up,"job_id",$job_id,1);
			}			
		}
		//echo "60**".bulk_update_sql_statement("wo_po_color_size_breakdown", "id",$field_array_up,$data_array_up,$id_arr );die;
		//echo "10**".$rID2.'='.$rID1.'='.$rID3; die;
		if($db_type==0)
		{
			if($rID1==1)
			{
				mysql_query("COMMIT");
				echo "1**".str_replace("'",'',$hidden_job_id);
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**".str_replace("'",'',$hidden_job_id);
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID1==1){
				oci_commit($con);
				echo "1**".str_replace("'",'',$hidden_job_id);
			}
			else{
				oci_rollback($con);
				echo "10**".str_replace("'",'',$hidden_job_id);
			}
		}
		disconnect($con);
		die;
	}
}