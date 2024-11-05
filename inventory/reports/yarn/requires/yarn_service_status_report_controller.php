<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../../includes/common.php');

$user_id=$_SESSION['logic_erp']['user_id'];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 130, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );
	exit();
}

if ($action=="load_drop_down_supplier")
{
	echo create_drop_down( "cbo_supplier_name", 130, "select c.supplier_name,c.id from lib_supplier_tag_company a, lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$data' and b.party_type=2 and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by c.supplier_name","id,supplier_name", 1, "--Select Supplier--", $selected, "" );
	exit();
}

if($action=="work_no_popup")
{
	echo load_html_head_contents("WO No Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>
			var selected_id = new Array; var selected_name = new Array;
		
		function check_all_data()
		{
			var tbl_row_count = document.getElementById( 'tbl_list_div' ).rows.length;
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
		
		function js_set_value2( str ) {
			
			if (str!="") str=str.split("_");
			//alert(str);
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
				name += selected_name[i] + ',';
				
			}
			
			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );
			
			
			$('#hide_wo_id').val( id );
			$('#hide_wo_no').val( name );
		
		
		}
    </script>

</head>

<body>
<div align="center">
	<form name="styleRef_form" id="styleRef_form">
		<fieldset style="width:680px;">
            <table width="670" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
            	<thead>
                    <th>Buyer</th>
                    <th>Supplier</th>
                   
                    <th><input type="reset" name="button" class="formbutton" value="Reset"  style="width:100px;"></th> 
                    <input type="hidden" name="hide_wo_no" id="hide_wo_no" value="" />
                    <input type="hidden" name="hide_wo_id" id="hide_wo_id" value="" />
                  
                </thead>
                <tbody>
                	<tr>
                        <td align="center">
                        	 <? 
								echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$cbo_buyer_name,"",0 );
							?>
                        </td>   
                                     
                       <td align="center">
                        	 <? 
								echo create_drop_down("cbo_supplier", 170, "select c.supplier_name,c.id from lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$companyID' and b.party_type =2 and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by supplier_name", "id,supplier_name", 1, "-- Select --", 0, "", 0);
							?>
                        </td>   
                        <td align="center">
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_supplier').value, 'create_wo_no_search_list_view', 'search_div', 'yarn_service_status_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
                    </td>
                    </tr>
            	</tbody>
           	</table>
            <div style="margin-top:15px" id="search_div"></div>
		</fieldset>
	</form>
</div>
</body>           
<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
	exit(); 
}

if($action=="create_wo_no_search_list_view")
{
	$data=explode('**',$data);
	$company_id=$data[0];
	$buyer_name=$data[1];
	$cbo_supplier=$data[2];	
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$supplier_arr = return_library_array("select id, supplier_name from lib_supplier", 'id', 'supplier_name');
	if($data[1]==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="")
			{ 
				$buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")";
				$buyer_id_cond2=" and buyer_po in (".$_SESSION['logic_erp']["buyer_id"].")";
			}
			else 
			{ 
				$buyer_id_cond="";$buyer_id_cond2="";
			}
		}
		else
		{
			$buyer_id_cond="";$buyer_id_cond2="";
		}
	}
	else
	{
		$buyer_id_cond=" and buyer_name=$data[1]";
		$buyer_id_cond2=" and buyer_po=$data[1]";
	}
	
	$search_by=$data[2];
	$search_string="%".trim($data[3])."%";

	if($db_type==0) $year_field=" YEAR(insert_date) as year"; 
	else if($db_type==2) $year_field="  to_char(insert_date,'YYYY') as year";
	else $year_field="";

	if($cbo_supplier!=0) $supplier_cond="and supplier_id=$cbo_supplier"; else $supplier_cond="";
	
	 $sql ="(select id,$year_field,company_id as company_id,supplier_id,currency as currency_id, yarn_dyeing_prefix_num as wopi_prefix, booking_date as wo_date, delivery_date
			from wo_yarn_dyeing_mst
			where  status_active=1 and is_deleted=0 and company_id='$company_id' $supplier_cond group by id,insert_date,company_id,supplier_id,currency,yarn_dyeing_prefix_num,booking_date,delivery_date) 
			union
			(
			select id,$year_field,company_name as company_id,null  as supplier_id,currency_id as currency_id,wo_number_prefix_num as wopi_prefix,wo_date as wo_date,delivery_date 
			from wo_non_order_info_mst
			where status_active=1 and is_deleted=0 and entry_form=144 and company_name='$company_id' and pay_mode!=2 $supplier_cond  group by id,insert_date,company_name,currency_id,wo_number_prefix_num,wo_date,delivery_date ) order by id";
		
	$sqlResult=sql_select($sql);
	?>
    <div>
     		 <fieldset>
            <form name="searchprocessfrm_1" id="searchprocessfrm_1" autocomplete="off">
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="670" class="rpt_table">
                    <thead>
                    <th width="30">SL</th>
                    <th width="50">Year</th>
                    <th width="130">Company</th> 
                    <th width="60">WO No</th>
                    <th width="80">WO Date.</th>
                    <th width="80">Delivery Date </th>
                    <th width="130">Supplier </th>
                    <th width="">Currecny</th>
                    </thead>
                </table>
                    <div style="width:670px; overflow-y:scroll; max-height:280px;" id="buyer_list_view" align="center">
                    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="650" class="rpt_table" id="tbl_list_search">
                    <?
					$i=1;
                    foreach($sqlResult as $row )
					{
						if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
						
						$data=$i.'_'.$row[csf('id')].'_'.$row[csf('wopi_prefix')];
						//echo $data;
					?>
                    	<tr id="tr_<?php echo $i; ?>" bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"  onClick="js_set_value2('<? echo $data;?>')">
                          <td width="30" align="center"><?php echo $i; ?>
                          <td width="50"><p><? echo $row[csf('year')]; ?></p></td>
                          <td width="130"><p><? echo $company_arr[$row[csf('company_id')]]; ?></p></td>
                          <td width="60"><p><? echo $row[csf('wopi_prefix')]; ?></p></td>
                          <td width="80"><p><? echo change_date_format($row[csf('wo_date')]); ?></p></td>
                          <td width="80"><p><? echo change_date_format($row[csf('delivery_date')]); ?></p></td>
                          <td width="130"><p><? echo $supplier_arr[$row[csf('supplier_id')]]; ?></p></td>
                          <td width=""><p><? echo $currency[$row[csf('currency_id')]]; ?></p></td>
                       </tr>
                       <?
					   $i++;
					}
					   ?>
                    </table>
                     </div>
                     <table width="600" cellspacing="0" cellpadding="0" style="border:none" align="center">
                    <tr>
                        <td align="center" height="30" valign="bottom">
                            <div style="width:100%">
                                <div style="width:50%; float:left" align="left">
                                    <input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()"/>
                                    Check / Uncheck All
                                </div>
                                <div style="width:50%; float:left" align="left">
                                    <input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px"/>
                                </div>
                            </div>
                        </td>
                    </tr>
                </table>
                    </form>
                     </fieldset>
                    </div>
                    
    <?
	
   exit(); 
} 

if($action=="job_no_popup")
{
	echo load_html_head_contents("Job Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>

	<script>
		function js_set_value(str)
		{
			var splitData = str.split("_");
			//alert (str);
			$("#hide_job_id").val(splitData[0]);
			$("#hide_job_no").val(splitData[1]);
			parent.emailwindow.hide();
		}

    </script>
	</head>
	<body>
	<div align="center">
		<form name="styleRef_form" id="styleRef_form">
			<fieldset style="width:580px;">
	            <table width="570" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
	            	<thead>
	                    <th>Buyer</th>
	                    <th>Search By</th>
	                    <th id="search_by_td_up" width="170">Please Enter Job No</th>
	                    <th><input type="reset" name="button" class="formbutton" value="Reset"  style="width:100px;"></th>
	                    <input type="hidden" name="hide_job_no" id="hide_job_no" value="" />
	                    <input type="hidden" name="hide_job_id" id="hide_job_id" value="" />
	                </thead>
	                <tbody>
	                	<tr>
	                        <td align="center">
	                        	 <?
									echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer_name,"",0 );
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
	                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_year; ?>', 'create_job_no_search_list_view', 'search_div', 'yarn_service_status_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
	                    </td>
	                    </tr>
	            	</tbody>
	           	</table>
	            <div style="margin-top:15px" id="search_div"></div>
			</fieldset>
		</form>
	</div>
	</body>
	<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if($action=="create_job_no_search_list_view")
{
	$data=explode('**',$data);
	$company_id=$data[0];

	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');

	if($data[1]==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		$buyer_id_cond=" and buyer_name=$data[1]";
	}

	$search_by=$data[2];
	$search_string="%".trim($data[3])."%";

	if($search_by==2) $search_field="style_ref_no"; else $search_field="job_no";

	$arr=array (0=>$company_arr,1=>$buyer_arr);
	if($db_type==0) $insert_year="year(insert_date)";
	if($db_type==2) $insert_year="to_char(insert_date,'yyyy')";


	if($db_type==0)
	{
		if($data[4]!=0) $year_cond=" and YEAR(insert_date)=$data[4]"; else $year_cond="";
	}
	else if($db_type==2)
	{
		$year_field_con=" and to_char(insert_date,'YYYY')";
		if($data[4]!=0) $year_cond=" $year_field_con=$data[4]"; else $year_cond="";
	}

	$sql= "select id, job_no, job_no_prefix_num, company_name, buyer_name, style_ref_no, $insert_year as year from wo_po_details_master where status_active=1 and is_deleted=0 and company_name=$company_id and $search_field like '$search_string' $buyer_id_cond $year_cond order by job_no";

	echo create_list_view("tbl_list_search", "Company,Buyer Name,Job No,Year,Style Ref. No", "120,130,80,60","600","240",0, $sql , "js_set_value", "id,job_no_prefix_num", "", 1, "company_name,buyer_name,0,0,0", $arr , "company_name,buyer_name,job_no_prefix_num,year,style_ref_no", "",'','0,0,0,0,0') ;

   exit();
}

if($action=="report_generate")
{
	$process = array( &$_POST );
    //var_dump($process);
	extract(check_magic_quote_gpc( $process ));

	$company_arr=return_library_array( "SELECT id, company_name from lib_company where status_active=1 and is_deleted=0 ",'id','company_name');//and id =$cbo_company_name 

	$lot_prod_arr=return_library_array( "select id, lot from product_details_master where status_active=1 and is_deleted=0",'id','lot');

	$buyer_arr=return_library_array( "SELECT id, buyer_name from lib_buyer where status_active=1 and is_deleted=0",'id','buyer_name');
	$supplier_arr=return_library_array( "SELECT id, short_name from lib_supplier where status_active=1 and is_deleted=0",'id','short_name');
	$supplier_long_arr = return_library_array("select id, supplier_name from lib_supplier", 'id', 'supplier_name');

    if(str_replace("'","",$cbo_buyer_name)==0)
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
		$buyer_id_cond=" and a.buyer_name=$cbo_buyer_name";
	}

    $job_no=str_replace("'","",$txt_job_no);
	if($job_no=="")
	{
		$job_no_cond="";
	}
	else
	{
		$job_no_cond="and a.job_no_prefix_num ='$job_no'";
	}

    $from_date=str_replace("'","",$txt_date_from);
	$to_date=str_replace("'","",$txt_date_to);

	if($db_type==0)
	{
		$date_from=change_date_format($from_date,'yyyy-mm-dd');
		$date_to=change_date_format($to_date,'yyyy-mm-dd');
	}
	else if($db_type==2)
	{
		$date_from=change_date_format($from_date,'','',1);
		$date_to=change_date_format($to_date,'','',1);
	}
	else
	{
		$date_from="";
		$date_to="";
	}
	$cbo_wo_type=str_replace("'","",$cbo_wo_type);

	$date_trans_cond="";$date_trans_non_cond=""; $date_cond="";
	if($cbo_wo_type==1)// With Order
	{
		if($date_from!="" && $date_to!="") $date_cond=" and c.booking_date between '".$date_from."' and '".$date_to."'"; else $date_cond="";
	}
	else
	{
		if($date_from!="" && $date_to!="") $date_cond=" and a.booking_date between '".$date_from."' and '".$date_to."'"; else $date_cond="";
	}

	if($date_from!="" && $date_to!="") $date_trans_cond=" and a.transaction_date between '".$date_from."' and '".$date_to."'"; else $date_trans_cond="";

	if($date_from!="" && $date_to!="") $date_trans_non_cond=" and b.transaction_date between '".$date_from."' and '".$date_to."'"; else $date_trans_non_cond="";

    $supplier_id=str_replace("'","",$cbo_supplier_name);
	if($cbo_wo_type==1)// With Order
	{
		if($supplier_id==0) $supplier_id_cond=""; else  $supplier_id_cond="and c.supplier_id='$supplier_id'";
	}
	else
	{
		if($supplier_id==0) $supplier_id_cond=""; else  $supplier_id_cond="and a.supplier_id='$supplier_id'";
	}
	
	
    $txt_wo_no=str_replace("'","",$txt_wo_no);
	
	// $search_cond='';
	// if ($txt_wo_no=="") 
	// {
	// 	$search_cond.="";
	// }
	// else
	// {
	// 	$search_cond.=" and c.ydw_no LIKE '%$txt_wo_no%'";
	// } 

	// $search_cond="";
	// if($txt_wo_no!='')
	// {
	// 	$search_cond="and c.yarn_dyeing_prefix_num in($txt_wo_no)";
	// 	//if($hide_wo_id!='') $wo_id_cond="and c.id in($hide_wo_id)";else  $wo_id_cond="";
	// }
	// else
	// {
	// 	$search_cond.="";
	// }

    

    if($cbo_wo_type==1)// With Order
	{
		$wo_type_cond = "and c.booking_without_order=1";
		$search_cond="";
		if($txt_wo_no!='')
		{
			$search_cond="and c.yarn_dyeing_prefix_num in($txt_wo_no)";
			//if($hide_wo_id!='') $wo_id_cond="and c.id in($hide_wo_id)";else  $wo_id_cond="";
		}
		else
		{
			$search_cond.="";
		}
	}
    else if($cbo_wo_type==2) {// Non Order 
		$wo_type_cond = "and a.booking_without_order=2";

		$search_cond="";
		if($txt_wo_no!='')
		{
			$search_cond="and a.yarn_dyeing_prefix_num in($txt_wo_no)";
			//if($hide_wo_id!='') $wo_id_cond="and c.id in($hide_wo_id)";else  $wo_id_cond="";
		}
		else
		{
			$search_cond.="";
		}
	}else{
		$wo_type_cond = "";
	}

    if($txt_internal_ref!="")
	{
		$jobno =  return_field_value("job_no_mst","wo_po_break_down" ,"status_active=1 and is_deleted=0 and grouping=$txt_internal_ref group by job_no_mst","job_no_mst");
		if($jobno!="")
		{
			$internal_ref_job_cond = "and a.job_no = '$jobno'";
		}
	}
	
    if($type==1)
	{
		$color_prod_arr=return_library_array( "select id, color from product_details_master",'id','color');
		$job_arr=return_library_array( "select id, job_no_mst from wo_po_break_down",'id','job_no_mst');
		$brand_name_arr=return_library_array( "select id, brand_name from  lib_brand",'id','brand_name');
		$company_short_arr=return_library_array( "select id, company_short_name from  lib_company",'id','company_short_name');

        if($db_type==0)
		{
		
			$sql_main="SELECT a.job_no, a.job_no_prefix_num, a.buyer_name, a.style_ref_no, c.id, c.ydw_no, c.supplier_id,c.booking_without_order,c.service_type,group_concat(distinct(b.product_id)) as product_id, sum(b.yarn_wo_qty) as qnty from wo_po_details_master a, wo_yarn_dyeing_dtls b, wo_yarn_dyeing_mst c where a.id=b.job_no_id and b.mst_id=c.id and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.entry_form in (94) and c.company_id=$cbo_company_name $buyer_id_cond $job_no_cond $date_cond $supplier_id_cond1 $search_cond $wo_type_cond $internal_ref_job_cond group by a.id, c.id, a.job_no, a.job_no_prefix_num, a.buyer_name, a.style_ref_no, c.ydw_no, c.supplier_id,c.booking_without_order,c.service_type order by a.job_no,c.id";
		}
		else if($db_type==2)
		{
			if($cbo_wo_type==1)
			{
				$sql_main="SELECT a.job_no, a.job_no_prefix_num, a.buyer_name, a.style_ref_no, c.id, c.ydw_no, c.supplier_id,c.booking_without_order,c.service_type,listagg(b.product_id,',') within group (order by b.product_id) as product_id, sum(b.yarn_wo_qty) as qnty from wo_po_details_master a, wo_yarn_dyeing_dtls b, wo_yarn_dyeing_mst c where a.id=b.job_no_id and b.mst_id=c.id and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.entry_form in (94) and c.company_id=$cbo_company_name $buyer_id_cond $job_no_cond $date_cond $supplier_id_cond $search_cond $wo_type_cond $internal_ref_job_cond group by a.id, c.id, a.job_no, a.job_no_prefix_num, a.buyer_name, a.style_ref_no, c.ydw_no, c.supplier_id,c.booking_without_order,c.service_type order by a.job_no,c.id";
			}
			else
			{
				$sql_main = "SELECT a.id, a.yarn_dyeing_prefix_num, a.ydw_no, a.company_id,a.service_type,listagg(b.product_id,',') within group (order by b.product_id) as product_id, sum(b.yarn_wo_qty) as qnty, a.supplier_id, a.booking_date, a.delivery_date, a.currency, a.ecchange_rate, a.pay_mode,a.source, a.attention,TO_CHAR(a.insert_date,'YYYY') as year from wo_yarn_dyeing_mst a join wo_yarn_dyeing_dtls b on a.id=b.mst_id left join sample_development_mst d on  b.job_no_id=d.id where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and a.entry_form=94 and b.entry_form=94 and a.company_id=$cbo_company_name $wo_type_cond $search_cond $date_cond $supplier_id_cond group by a.id, a.yarn_dyeing_prefix_num, a.ydw_no, a.company_id,a.service_type, a.supplier_id, a.booking_date, a.delivery_date, a.currency, a.ecchange_rate, a.pay_mode,a.source, a.attention,a.insert_date order by a.id DESC";
			}
			
		}
        //echo $sql_main;
        $main_query_result = sql_select($sql_main);

        if(count($main_query_result)>0)
		{
			foreach($main_query_result as $row)
			{
				$job_no_arr[] = "'".$row[csf('job_no')]."'";
				$work_order_id_arr[] = $row[csf('id')];

				if($row[csf('product_id')]!="")
				{
					$grey_product_id_arr[] = $row[csf('product_id')];	
				}
			}

			$job_no_string = implode(',',array_unique($job_no_arr));
			$work_order_ids_string = implode(',',array_unique($work_order_id_arr));
			$grey_product_id_string = implode(',',array_unique($grey_product_id_arr));
		}
		else
		{
			echo "<br><center><span style='color:red; font-size:20px; font-weight:bolder;'>Data Not Fond.</span></center>";
			die();
		}

		if($cbo_wo_type==1)
        {
			if($job_no_string!="")
			{
				if($db_type==0)
				{
					$order_sql = "SELECT job_no_mst, group_concat(distinct(po_number)) as order_no,group_concat(distinct(grouping)) as internal_ref from wo_po_break_down where status_active=1 and is_deleted=0 and job_no_mst in($job_no_string) group by job_no_mst";

					$order_resutl = sql_select($order_sql);
					foreach ($order_resutl as $row) {
						$order_arr[$row[csf('job_no_mst')]]['internal_ref'] = $row[csf('internal_ref')];
					}
				}
				else if($db_type==2)
				{
					$order_sql = "SELECT job_no_mst,listagg(po_number,',') within group (order by po_number) as order_no,listagg(grouping,',') within group (order by po_number) as internal_ref from wo_po_break_down where status_active=1 and is_deleted=0 and job_no_mst in($job_no_string) group by job_no_mst";

					$order_resutl = sql_select($order_sql);
					foreach ($order_resutl as $row) {
						$order_arr[$row[csf('job_no_mst')]]['internal_ref'] = $row[csf('internal_ref')];
					}				
				}
			}

			if($job_no_string!="" && $work_order_ids_string!="")
			{
				if($db_type==0)
				{
				
					
				}
				else if($db_type==2)
				{
					if($grey_product_id_string!="")
					{
						
						$grey_product_id_string_cond = "and a.prod_id in($grey_product_id_string)";

					}
				}

				$job_no_string_cond = "and a.job_no in($job_no_string)";
				$work_order_ids_string_cond = "and b.booking_id in($work_order_ids_string)";

				$grey_issue_sql = "select b.id, a.job_no,a.dyeing_color_id,a.cons_quantity as issue_qnty, b.booking_id from inv_transaction a, inv_issue_master b where a.mst_id=b.id and b.entry_form=3 and b.issue_basis=1 and b.issue_purpose in(12,15,38,46,50,51) and a.company_id=$cbo_company_name and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and a.item_category=1 and a.transaction_type=2 $work_order_ids_string_cond $job_no_string_cond $grey_product_id_string_cond $date_trans_cond ";
				//echo $grey_issue_sql;
				$issueDataArr = sql_select($grey_issue_sql);

				$issue_arr=array();
				foreach($issueDataArr as $row)
				{
					$issue_arr[$row[csf('job_no')]][$row[csf('booking_id')]] += $row[csf('issue_qnty')];
					
					$issue_id_arr[] = $row[csf('id')];
				} 
				unset($issueDataArr);

				$issue_id_string = implode(',',array_unique($issue_id_arr));

				if($issue_id_string!="") 
				{
					$issueRet_arr_sql= "select c.id as trans_id,c.quantity as issue_ret_qnty,c.reject_qty as issue_reject_qty, b.booking_id, d.job_no_mst,e.color,e.lot from inv_transaction a, inv_receive_master b, order_wise_pro_details c, wo_po_break_down d,product_details_master e where a.mst_id = b.id and a.id = c.trans_id and c.po_breakdown_id = d.id and a.prod_id=e.id and b.entry_form = 9 and a.item_category=1 and a.transaction_type=4 and b.receive_basis=1 and a.company_id = $cbo_company_name and a.issue_id in($issue_id_string) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted = 0 and d.status_active = 1 and d.is_deleted = 0 and c.entry_form = 9 and c.trans_type = 4 $work_order_ids_string_cond $grey_product_id_string_cond $date_trans_cond";		     	  
					//echo $issueRet_arr_sql;
					$issue_return_Data_Arr=sql_select($issueRet_arr_sql);

					$issue_ret_delivery=array(); 
					$trans_check=array();
					foreach($issue_return_Data_Arr as $val)
					{
						if($trans_check[$val[csf("trans_id")]]=="")
						{
							$issue_ret_delivery[$val[csf('job_no_mst')]][$val[csf('booking_id')]]+=$val[csf('issue_ret_qnty')]+$val[csf('issue_reject_qty')];
						}
					}				
					unset($issue_return_Data_Arr);

				}

				// ==  new recieve dyed yarn 
				$mrrRcvSql="select a.job_no,a.brand_id,sum(a.cons_quantity) as recv_qnty,b.id as mrr_rcv_id, b.booking_id,b.supplier_id,c.id as product_id, c.lot from inv_transaction a, inv_receive_master b, product_details_master c where a.mst_id=b.id and a.prod_id=c.id and b.entry_form=1 and b.receive_basis=2 and b.receive_purpose in(12,15,38,46,50,51) and a.company_id=$cbo_company_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.item_category=1 and a.transaction_type=1 and a.job_no in($job_no_string) and b.booking_id in($work_order_ids_string) and c.dyed_type=1 $date_trans_cond group by a.job_no,a.brand_id,b.id,b.booking_id,b.supplier_id,c.id,c.lot"; //and a.prod_id=19184 
				//echo $mrrRcvSql;
				$mrrRcvDataArr=sql_select($mrrRcvSql);

				$recv_arr=array();
				$lot_recv_arr=array();
				$lot_arr=array(); 
				$suplier_arr=array(); 
				foreach($mrrRcvDataArr as $row)
				{
					$recv_arr[$row[csf('job_no')]][$row[csf('booking_id')]][$row[csf('lot')]] += $row[csf('recv_qnty')];
					$lot_recv_arr[$row[csf('job_no')]][$row[csf('booking_id')]]['lot']+= $row[csf('recv_qnty')];
					$lot_arr[$row[csf('job_no')]][$row[csf('booking_id')]]['lot'].=$row[csf('lot')].",";				
					$suplier_arr[$row[csf('job_no')]][$row[csf('booking_id')]]['supplier_id']=$row[csf('supplier_id')];	
					
					$dyed_product_id_arr[] = $row[csf('product_id')];
				}
				//var_dump($suplier_arr);
				unset($mrrRcvDataArr);

				$dyed_product_id_string = implode(',',array_unique($dyed_product_id_arr));

				if($dyed_product_id_string!="") // knitting issue 
				{
					$knitting_issue_sql= "select b.prod_id, b.po_breakdown_id, sum(b.quantity) as issue_qnty, c.id as issue_id, c.knit_dye_source, c.knit_dye_company, c.booking_id,c.issue_purpose from inv_transaction a, order_wise_pro_details b, inv_issue_master c where a.mst_id=c.id and a.id=b.trans_id and b.entry_form=3 and a.company_id=$cbo_company_name and c.issue_purpose in(1) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.item_category=1 and a.transaction_type=2 and a.prod_id in($dyed_product_id_string) $date_trans_cond group by c.id, b.prod_id, b.po_breakdown_id, c.knit_dye_source, c.knit_dye_company, c.booking_id,c.issue_purpose";
					//echo $knitting_issue_sql;

					$issueDatalotArr = sql_select($knitting_issue_sql);

					$lot_wise_qnty_array=array();
					$lot_wise_total_qnty_array=array();
					$party_arr=array();
					$source_arr=array();
					foreach($issueDatalotArr as $row)
					{		
									
						$lot_wise_qnty_array[$job_arr[$row[csf('po_breakdown_id')]]][$lot_prod_arr[$row[csf('prod_id')]]]['lot']+=$row[csf('issue_qnty')].",";				
						$lot_wise_total_qnty_array[$job_arr[$row[csf('po_breakdown_id')]]][$lot_prod_arr[$row[csf('prod_id')]]]['lot']+=$row[csf('issue_qnty')];
						$party_arr[$job_arr[$row[csf('po_breakdown_id')]]][$lot_prod_arr[$row[csf('prod_id')]]][$row[csf('issue_qnty')]]=$row[csf('knit_dye_company')];
						$source_arr[$job_arr[$row[csf('po_breakdown_id')]]][$lot_prod_arr[$row[csf('prod_id')]]][$row[csf('issue_qnty')]]=$row[csf('knit_dye_source')];
									
						$dyed_yarn_issue_id[] = $row[csf('issue_id')];

					}
					//var_dump($lot_wise_qnty_array);
					unset($issueDatalotArr);

					$kning_issue_id_string = implode(',',array_unique($dyed_yarn_issue_id));

					if($kning_issue_id_string!="")
					{
						$issueRetKnitingArr="select a.prod_id,sum(c.quantity) as issue_return,sum(c.reject_qty) as issue_reject_qty,b.booking_no,e.job_no,f.color,f.lot from inv_transaction a, inv_receive_master b, order_wise_pro_details c,wo_po_break_down d, wo_po_details_master e,product_details_master f where a.mst_id =b.id and a.id = c.trans_id and c.po_breakdown_id = d.id and d.job_no_mst = e.job_no and f.id =a.prod_id and b.entry_form = 9 and a.item_category = 1 and a.transaction_type= 4 and a.prod_id in($dyed_product_id_string) and b.issue_id in($kning_issue_id_string) group by a.prod_id,b.booking_no,e.job_no,f.color,f.lot";
						//echo $issueRetKnitingArr;
						$issueRetKnitingDataArr=sql_select($issueRetKnitingArr);
						
						$knitting_issure_return_arr = array();
						foreach($issueRetKnitingDataArr as $row)
						{
							$knitting_issure_return_arr[$row[csf('job_no')]][$lot_prod_arr[$row[csf('prod_id')]]]['lot'] += $row[csf('issue_return')]+$row[csf('issue_reject_qty')];
						}
						//var_dump($knitting_issure_return_arr);
						unset($issueRetKnitingDataArr);
					}
				}
			}
		}
		else
		{
			if($work_order_ids_string!="")
			{
				if($db_type==0)
				{
				
					
				}
				else if($db_type==2)
				{
					if($grey_product_id_string!="")
					{
						
						$grey_product_id_string_cond = "and a.prod_id in($grey_product_id_string)";

					}
				}

				$job_no_string_cond = "and a.job_no in($job_no_string)";
				$work_order_ids_string_cond = "and b.booking_id in($work_order_ids_string)";

				$grey_issue_sql = "select b.id, a.job_no,a.dyeing_color_id,a.cons_quantity as issue_qnty, b.booking_id from inv_transaction a, inv_issue_master b where a.mst_id=b.id and b.entry_form=3 and b.issue_basis=1 and b.issue_purpose in(12,15,38,46,50,51) and a.company_id=$cbo_company_name and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and a.item_category=1 and a.transaction_type=2 $work_order_ids_string_cond $grey_product_id_string_cond $date_trans_cond ";
				//echo $grey_issue_sql;
				$issueDataArr = sql_select($grey_issue_sql);

				$issue_arr=array();
				foreach($issueDataArr as $row)
				{
					$issue_arr[$row[csf('booking_id')]]['issue_qnty'] += $row[csf('issue_qnty')];
					
					$issue_id_arr[] = $row[csf('id')];
				} 
				//var_dump($issue_arr);
				unset($issueDataArr);

				$issue_id_string = implode(',',array_unique($issue_id_arr));

				if($issue_id_string!="") 
				{
					$issueRet_arr_sql = "select a.id as mst_id, a.recv_number_prefix_num,a.challan_no, a.recv_number, a.company_id, a.supplier_id, a.receive_date, a.item_category, a.recv_number, a.knitting_source, a.knitting_company,  b.id, b.cons_quantity, b.cons_reject_qnty,a.booking_id, c.lot from inv_receive_master a, inv_transaction b left join product_details_master c on b.prod_id=c.id where a.id=b.mst_id and b.item_category=1 and b.transaction_type=4 and a.company_id = $cbo_company_name $date_trans_non_cond and b.prod_id in($grey_product_id_string) and a.issue_id in($issue_id_string) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by a.id desc";

					// $issueRet_arr_sql= "select c.id as trans_id,c.quantity as issue_ret_qnty,c.reject_qty as issue_reject_qty, b.booking_id, d.job_no_mst,e.color,e.lot from inv_transaction a, inv_receive_master b, order_wise_pro_details c, wo_po_break_down d,product_details_master e where a.mst_id = b.id and a.id = c.trans_id and c.po_breakdown_id = d.id and a.prod_id=e.id and b.entry_form = 9 and a.item_category=1 and a.transaction_type=4 and b.receive_basis=1 and a.company_id = $cbo_company_name and a.issue_id in($issue_id_string) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted = 0 and d.status_active = 1 and d.is_deleted = 0 and c.entry_form = 9 and c.trans_type = 4 $work_order_ids_string_cond $grey_product_id_string_cond $date_trans_cond";		     	  
					//echo $issueRet_arr_sql;
					$issue_return_Data_Arr=sql_select($issueRet_arr_sql);

					$issue_ret_delivery=array(); 
					$trans_check=array();
					foreach($issue_return_Data_Arr as $val)
					{
						if($trans_check[$val[csf("trans_id")]]=="")
						{
							$issue_ret_delivery[$val[csf('booking_id')]]['return_qty']+=$val[csf('cons_quantity')]+$val[csf('cons_reject_qnty')];
						}
					}
					//var_dump($issue_ret_delivery);				
					unset($issue_return_Data_Arr);

				}

				// ==  new recieve dyed yarn 
				$mrrRcvSql="select a.job_no,a.brand_id,sum(a.cons_quantity) as recv_qnty,b.id as mrr_rcv_id, b.booking_id,b.supplier_id,c.id as product_id, c.lot from inv_transaction a, inv_receive_master b, product_details_master c where a.mst_id=b.id and a.prod_id=c.id and b.entry_form=1 and b.receive_basis=2 and b.receive_purpose in(12,15,38,46,50,51) and a.company_id=$cbo_company_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.item_category=1 and a.transaction_type=1 and b.booking_id in($work_order_ids_string) and c.dyed_type=1 $date_trans_cond group by a.job_no,a.brand_id,b.id,b.booking_id,b.supplier_id,c.id,c.lot"; //and a.prod_id=19184 
				//echo $mrrRcvSql;
				$mrrRcvDataArr=sql_select($mrrRcvSql);

				$recv_arr=array();
				$lot_recv_arr=array();
				$lot_arr=array(); 
				$suplier_arr=array(); 
				foreach($mrrRcvDataArr as $row)
				{
					$recv_arr[$row[csf('booking_id')]][$row[csf('lot')]] += $row[csf('recv_qnty')];
					$lot_recv_arr[$row[csf('booking_id')]]['lot']+= $row[csf('recv_qnty')];
					//$lot_arr[$row[csf('job_no')]][$row[csf('booking_id')]]['lot'].=$row[csf('lot')].",";				
					$lot_arr[$row[csf('booking_id')]]['lot'].=$row[csf('lot')].",";				
					$suplier_arr[$row[csf('job_no')]][$row[csf('booking_id')]]['supplier_id']=$row[csf('supplier_id')];	
					
					$dyed_product_id_arr[] = $row[csf('product_id')];
				}
				//var_dump($suplier_arr);
				unset($mrrRcvDataArr);

				$dyed_product_id_string = implode(',',array_unique($dyed_product_id_arr));

				if($dyed_product_id_string!="") // knitting issue 
				{					
					$knitting_issue_sql= "select b.prod_id, b.po_breakdown_id, sum(b.quantity) as issue_qnty, c.id as issue_id, c.knit_dye_source, c.knit_dye_company, c.booking_id,c.issue_purpose from inv_transaction a, order_wise_pro_details b, inv_issue_master c where a.mst_id=c.id and a.id=b.trans_id and b.entry_form=3 and a.company_id=$cbo_company_name and c.issue_purpose in(1) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.item_category=1 and a.transaction_type=2 and a.prod_id in($dyed_product_id_string) $date_trans_cond group by c.id, b.prod_id, b.po_breakdown_id, c.knit_dye_source, c.knit_dye_company, c.booking_id,c.issue_purpose";
					//echo $knitting_issue_sql;

					$issueDatalotArr = sql_select($knitting_issue_sql);

					$lot_wise_qnty_array=array();
					$lot_wise_total_qnty_array=array();
					$party_arr=array();
					$source_arr=array();
					foreach($issueDatalotArr as $row)
					{		
									
						$lot_wise_qnty_array[$lot_prod_arr[$row[csf('prod_id')]]]['lot']+=$row[csf('issue_qnty')].",";				
						$lot_wise_total_qnty_array[$job_arr[$row[csf('po_breakdown_id')]]][$lot_prod_arr[$row[csf('prod_id')]]]['lot']+=$row[csf('issue_qnty')];
						$party_arr[$job_arr[$row[csf('po_breakdown_id')]]][$lot_prod_arr[$row[csf('prod_id')]]][$row[csf('issue_qnty')]]=$row[csf('knit_dye_company')];
						$source_arr[$job_arr[$row[csf('po_breakdown_id')]]][$lot_prod_arr[$row[csf('prod_id')]]][$row[csf('issue_qnty')]]=$row[csf('knit_dye_source')];
									
						$dyed_yarn_issue_id[] = $row[csf('issue_id')];

					}
					//var_dump($lot_wise_qnty_array);
					unset($issueDatalotArr);

					$kning_issue_id_string = implode(',',array_unique($dyed_yarn_issue_id));

					if($kning_issue_id_string!="")
					{
						$issueRetKnitingArr="select a.prod_id,sum(c.quantity) as issue_return,sum(c.reject_qty) as issue_reject_qty,b.booking_no,e.job_no,f.color,f.lot from inv_transaction a, inv_receive_master b, order_wise_pro_details c,wo_po_break_down d, wo_po_details_master e,product_details_master f where a.mst_id =b.id and a.id = c.trans_id and c.po_breakdown_id = d.id and d.job_no_mst = e.job_no and f.id =a.prod_id and b.entry_form = 9 and a.item_category = 1 and a.transaction_type= 4 and a.prod_id in($dyed_product_id_string) and b.issue_id in($kning_issue_id_string) group by a.prod_id,b.booking_no,e.job_no,f.color,f.lot";
						//echo $issueRetKnitingArr;
						$issueRetKnitingDataArr=sql_select($issueRetKnitingArr);
						
						$knitting_issure_return_arr = array();
						foreach($issueRetKnitingDataArr as $row)
						{
							$knitting_issure_return_arr[$lot_prod_arr[$row[csf('prod_id')]]]['lot'] += $row[csf('issue_return')]+$row[csf('issue_reject_qty')];
						}
						//var_dump($knitting_issure_return_arr);
						unset($issueRetKnitingDataArr);
					}
				}
			}
		}
        
		

        if($cbo_wo_type==1)
        {
            $tblwidth = 1472;
            $colspan=7;
        }
        else
        {
            $tblwidth = 1112;
            $colspan=3;
        }
        ob_start();
        ?>
        <fieldset style="width: <? echo ($tblwidth+28).'px';?>">
			<table cellpadding="0" cellspacing="0" width="<? echo $tblwidth+18;?>">
				<tr>
				   <td align="center" width="100%" colspan="17" style="font-size:16px"><strong><? echo "Yarn Service Status Report"; ?></strong></td>
				</tr>
				<tr>
				   <td align="center" width="100%" colspan="17" style="font-size:16px"><strong><? echo $company_arr[str_replace("'","",$cbo_company_name)]; ?></strong></td>
				</tr>
			</table>
            <table width="<? echo $tblwidth;?>" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
				<thead>
					<tr>
						<th width="40" rowspan="2">SL</th>
                        <? if($cbo_wo_type==1) {?>
						<th width="80" rowspan="2">Job No</th>
                        <th width="80" rowspan="2">Internal Ref.</th>
                        <th width="80" rowspan="2">Buyer Name</th>
                        <th width="80" rowspan="2">Style No</th>
                        <? }?>
                        <th width="110" rowspan="2">WO No</th>
                        <th width="80" rowspan="2">Service Type</th>
                        <th width="300" colspan="3">Grey Yarn</th>
                        <th width="400" colspan="4">Services Yarn</th>
                        <th width="300" colspan="2">S/Y Delivery for Knitting </th>
					</tr>
                    <tr>
						<th width="100">WO Qty</th>
						<th width="100">Delivery</th>
                        <th width="100">Balance</th>
                        <th width="100">Lot/Batch</th>
						<th width="100">Received Qty.</th>
                        <th width="100">Receive Balance</th>
						<th width="100">Service Party</th>
                        <th width="100">Issue To Knitting</th>
                        <th >Issue Balance</th>
                    </tr>
                </thead>
            </table>
            <div style="<? echo ($tblwidth+18).'px';?>; overflow-y: scroll; max-height:380px;" id="scroll_body">
				<table width="<? echo $tblwidth;?>" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
                <?
                $i=1; $z=1;  $total_wo_qnty=0; $tot_delivery_qnty=0; $total_grey_bl_qnty=0;$total_rcv_qnty=0; $total_dyed_bl_qnty=0;$delivery_qnty=0;$grey_balance=0;
				$total_rcv_balance=0;$issue_balance_total=0;

				$rowspan_array=array();
				$rowspan_lot_array=array();
                foreach($main_query_result as $row)
				{                    
                    if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

                    $internal_ref  = implode(",",array_unique(explode(",", trim($order_arr[$row[csf('job_no')]]['internal_ref']))));
					if($cbo_wo_type==1)
					{
						$delivery_qnty=$issue_arr[$row[csf('job_no')]][$row[csf('id')]] - $issue_ret_delivery[$row[csf('job_no')]][$row[csf('id')]];
						
					}
					else
					{
						$delivery_qnty=$issue_arr[$row[csf('id')]]['issue_qnty'] - $issue_ret_delivery[$row[csf('id')]]['return_qty'];
					}
					$grey_balance=$row[csf('qnty')]-$delivery_qnty;
                   
                   

					//$lotspan=array_unique(explode(",", chop($lot_arr[$row[csf('job_no')]][$row[csf('id')]]['lot'],",") ));
					$lot_data= "";
					if($cbo_wo_type==1)
					{
						$lot_data=array_unique(explode(",", chop($lot_arr[$row[csf('job_no')]][$row[csf('id')]]['lot'],",") ));
					}
					else
					{
						$lot_data=array_unique(explode(",", chop($lot_arr[$row[csf('id')]]['lot'],",") ));
					}
					
					//var_dump($lot_data);
					$s=0;$k=0;
					$x=0;
					foreach($lot_data as $lotspan_id)
					{ 
						$lotspan = array_unique(explode(",", chop($lot_wise_qnty_array[$lotspan_id]['lot'],",") ));
						foreach($lotspan as $lot_span)
						{ 
							$rowspan_lot_array[$lotspan_id][$i]+=1;
							$k++;
						}
						$x++;
						//$rowspan_array[$lot_span]+=1;
						//$k++;
					}
					
					
					

					$x=0;$rcv_qnty=0;$tol_lot_rcv=0;$lot_wise_total_issue_qnty=0;$lot_wise_total_return_qnty=0;
					$lot_wise_knitting =array();
					foreach($lot_data as $lot_val)
					{
						
						if($cbo_wo_type==1)
						{
							$rcv_qnty=$recv_arr[$row[csf('job_no')]][$row[csf('id')]][$lot_val];
							$tol_lot_rcv = $lot_recv_arr[$row[csf('job_no')]][$row[csf('id')]]['lot'];
							$lot_wise_knitting = array_unique(explode(",", chop($lot_wise_qnty_array[$row[csf('job_no')]][$lot_val]['lot'],",") ));
							$lot_wise_total_issue_qnty = $lot_wise_total_qnty_array[$row[csf('job_no')]][$lot_val]['lot'];
							$lot_wise_total_return_qnty=$knitting_issure_return_arr[$row[csf('job_no')]][$lot_val]['lot'];
						}
						else
						{
							$rcv_qnty=$recv_arr[$row[csf('id')]][$lot_val];
							$tol_lot_rcv = $lot_recv_arr[$row[csf('id')]]['lot'];
							$lot_wise_knitting = array_unique(explode(",", chop($lot_wise_qnty_array[$lot_val]['lot'],",") ));
							$lot_wise_total_issue_qnty = $lot_wise_total_qnty_array[$lot_val]['lot'];
							$lot_wise_total_return_qnty=$knitting_issure_return_arr[$lot_val]['lot'];
						}
						
						$rcv_qnty_total+=$recv_arr[$row[csf('job_no')]][$row[csf('id')]][$lot_val];
						
						$rcv_balance = $row[csf('qnty')]-$tol_lot_rcv;

						$supplier_name = $supplier_arr[$suplier_arr[$row[csf('job_no')]][$row[csf('id')]]['supplier_id']];

						//echo $lot_wise_data = $lot_wise_qnty_array['28610'][$lot_val]['lot'];
						// $issue_qnty_lot=$lot_wise_qnty_array[$row[csf('job_no')]][$color_id][$lot_val]['lot'] - $knitting_issure_return_arr[$row[csf('job_no')]][$color_id][$lot_val];
						//$issue_qnty_lot=$lot_wise_qnty_array[$row[csf('job_no')]][$lot_val]['lot'];
						//var_dump($row[csf('job_no')]);
					
						
						//$tot_issue = $lot_wise_total_issue_qnty-$lot_wise_total_return_qnty;
						//$issue_balance = $rcv_qnty-$tot_issue;	
					
						$m=0;
						foreach($lot_wise_knitting as $lot)
						{
							//var_dump($lot);
							$party = $party_arr[$row[csf('job_no')]][$lot_val][$lot];
							$source = $source_arr[$row[csf('job_no')]][$lot_val][$lot];
							$knittinig_party="";
							if ($source==1)
							{
								$knittinig_party=$company_short_arr[$party];
							}
							else
							{
								$knittinig_party=$supplier_arr[$party];
							}

							$Knit_balance = $lot-$lot_wise_total_return_qnty;
							//$Knit_balance = $lot;
							$issue_balance = $rcv_qnty-$Knit_balance;	
							
						?>
							<tr valign="middle" bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $z; ?>','<? echo $bgcolor; ?>')" id="tr<? echo $z;?>">
								<?
								if($s==0){?>
								<td width="40" rowspan="<? echo $k; ?>"><? echo $i; ?></td>
								<? if($cbo_wo_type==1) { ?>    
								<td width="80" rowspan="<? echo $k; ?>"><p><? echo $row[csf('job_no')]; ?></p></td>
								<td width="80" rowspan="<? echo $k; ?>"><p><? echo $internal_ref; ?>&nbsp;</p></td>
								<td width="80" rowspan="<? echo $k; ?>"><p><? echo $buyer_arr[$row[csf('buyer_name')]]; ?>&nbsp;</p></td>
								<td width="80" rowspan="<? echo $k; ?>" title="<? echo $row[csf('style_ref_no')]; ?>"><p><? echo substr($row[csf('style_ref_no')],0,10); ?>&nbsp;</p></td>
								<? } ?>
								<td width="110" rowspan="<? echo $k; ?>"><p><? echo $row[csf('ydw_no')]; ?></p></td>
								<td width="80" align="center" rowspan="<? echo $k; ?>"><p><? echo $yarn_issue_purpose[$row[csf('service_type')]] ?></p></td> 
								<td width="100"  align="right" rowspan="<? echo $k; ?>"><? echo number_format($row[csf('qnty')],2,'.',''); ?></td>
								<td width="100" align="right" rowspan="<? echo $k; ?>">
								<? 
									if($cbo_wo_type==1)
									{
										if($delivery_qnty>0)
										{
											?>
											<a style="text-decoration: none;" href='#report_details' onClick="openmypage('<? echo $row[csf('job_no')]; ?>','<? echo $row[csf('id')]; ?>','<? echo '0'; ?>','<? echo $lot_val; ?>','issue_popup','<? echo $cbo_wo_type; ?>');"><? echo number_format($delivery_qnty,2,'.',''); ?></a> 

											<?
										}
										else
										{
											echo number_format($delivery_qnty,2,'.','');	
										}
									}
									else
									{
										if($delivery_qnty>0)
										{
											?>
											<a style="text-decoration: none;" href='#report_details' onClick="openmypage('<? echo 0; ?>','<? echo $row[csf('id')]; ?>','<? echo '0'; ?>','<? echo $lot_val; ?>','issue_popup','<? echo $cbo_wo_type; ?>');"><? echo number_format($delivery_qnty,2,'.',''); ?></a> 

											<?
										}
										else
										{
											echo number_format($delivery_qnty,2,'.','');	
										}
									}
									
								?>
							</td>
								<td width="100" align="right" rowspan="<? echo $k; ?>"><? echo number_format($grey_balance,2,'.',''); ?></td>
								<?
								$total_wo_qnty+=$row[csf('qnty')];
								$tot_delivery_qnty+=$delivery_qnty;
								$total_grey_bl_qnty+=$grey_balance;
								}
								
							
								if($m==0){?>
								<td width="100" align="center" rowspan="<? echo $rowspan_lot_array[$lot_val][$i] ; ?>"><? echo $lot_val; ?>&nbsp;</td>
								
								<td width="100" align="right" rowspan="<? echo $rowspan_lot_array[$lot_val][$i]; ?>">
								<?
									if($cbo_wo_type==1)
									{
										if($rcv_qnty>0)
										{
											?>
											<a style="text-decoration: none;" href='#report_details' onClick="openmypage('<? echo $row[csf('job_no')]; ?>','<? echo $row[csf('id')]; ?>','<? echo 0; ?>','<? echo $lot_val; ?>','receive_popup','<? echo $cbo_wo_type; ?>');"><? echo number_format($rcv_qnty,2,'.',''); ?></a> 

											<?
										}
										else
										{
											echo number_format($rcv_qnty,2,'.','');	
										}
									}
									else
									{
										if($rcv_qnty>0)
										{
											?>
											<a style="text-decoration: none;" href='#report_details' onClick="openmypage('<? echo 0; ?>','<? echo $row[csf('id')]; ?>','<? echo 0; ?>','<? echo $lot_val; ?>','receive_popup','<? echo $cbo_wo_type; ?>');"><? echo number_format($rcv_qnty,2,'.',''); ?></a> 

											<?
										}
										else
										{
											echo number_format($rcv_qnty,2,'.','');	
										}
									} 
									
								?>
								</td>
								<? }?>
								<?
								if($s==0){ ?>
								<td width="100" rowspan="<? echo $k; ?>" align="right"><? echo number_format($rcv_balance,2,'.','');	 ?></td>
								<td width="100" rowspan="<? echo $k; ?>" align="center">
								<?
								if($supplier_name)
								{
									echo $supplier_name;
								}
								else
								{
									echo $supplier_long_arr[$row[csf('supplier_id')]];
								}
								?>
								</td>
								<?
								$total_rcv_balance+=$rcv_balance;

								}
								
								?>
								<td width="100" align="right">
								<? 
									if($cbo_wo_type==1)
									{
										if($Knit_balance>0)
										{
											?>
											<a style="text-decoration: none;" href='#report_details' onClick="openmypage('<? echo $row[csf('job_no')]; ?>','<? echo $row[csf('id')]; ?>','<? echo 0; ?>','<? echo $lot_val; ?>','yd_issue_popup','<? echo $cbo_wo_type; ?>');"><? echo number_format($Knit_balance,2,'.',''); ?></a>
											<?
										}
										else
										{
											echo number_format($Knit_balance,2,'.','');
										}
									}
									else
									{
										if($Knit_balance>0)
										{
											?>
											<a style="text-decoration: none;" href='#report_details' onClick="openmypage('<? echo 0; ?>','<? echo $row[csf('id')]; ?>','<? echo $row[csf('job_no')]; ?>','<? echo $lot_val; ?>','yd_issue_popup','<? echo $cbo_wo_type; ?>');"><? echo number_format($Knit_balance,2,'.',''); ?></a>
											<?
										}
										else
										{
											echo number_format($Knit_balance,2,'.','');
										}
									}
									
									?>
								<?
								$tot_lot+= $Knit_balance;
								?>&nbsp;</td>
								
								<? if($m==0){?>
								
								<td align="right" rowspan="<? echo $rowspan_lot_array[$lot_val][$i]; ?>"><? echo  number_format($issue_balance,2,'.','');
								 ?>&nbsp;</td>
								<?
								$issue_balance_total+=$issue_balance;
								 } ?>
								
							</tr> 
						<?
						
							$m++;
							$s++;
							$z++;
							
						}
					$total_rcv_qnty+=$rcv_qnty;
					
					$x++;
					}
                    $i++;
					
                }
                 ?>
                <tfoot>
					<th colspan="<? echo $colspan;?>" align="right">Total</th>
					<th align="right"><?php echo number_format($total_wo_qnty,2); ?>&nbsp;</th>
					<th align="right"><?php echo number_format($tot_delivery_qnty,2); ?>&nbsp;</th>
					<th align="right"><?php echo number_format($total_grey_bl_qnty,2); ?>&nbsp;</th>
                    <th align="right">&nbsp;</th>
                    <th align="right"><?php echo number_format($total_rcv_qnty,2); ?>&nbsp;</th>
					<th align="right"><?php echo number_format($total_rcv_balance,2); ?>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th><?php echo number_format($tot_lot,2); ?>&nbsp;</th>
					<th align="right"><?php echo number_format($issue_balance_total,2); ?>&nbsp;</th>
				</tfoot>
                   
				</table> 
            </div>
        </fieldset>

        <?    
    }
	foreach (glob("$user_id*.xls") as $filename)
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	$filename=$user_id."_".$name.".xls";
	echo "$total_data####$filename";
	exit();
}

if($action=="issue_popup")
{
	echo load_html_head_contents("Issue Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	$count_arr=return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');
	$booking_array=return_library_array( "select id, ydw_no from wo_yarn_dyeing_mst",'id','ydw_no');
	if($trans_type==1)
	{
		$grey_issue_sql = "select b.issue_number,b.issue_date, b.id, a.job_no,a.dyeing_color_id,a.cons_quantity as issue_qnty, b.booking_id,b.booking_no,c.yarn_count_id,c.lot from inv_transaction a, inv_issue_master b,product_details_master c where a.mst_id=b.id and a.prod_id=c.id and b.entry_form=3 and b.issue_basis=1 and b.issue_purpose in(12,15,38,46,50,51) and a.company_id=$companyID and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and a.item_category=1 and a.transaction_type=2 and a.job_no='$job_no' and b.booking_id='$booking_id' ";
	}
	else
	{
		$grey_issue_sql = "select b.issue_number,b.issue_date, b.id, a.job_no,a.dyeing_color_id,a.cons_quantity as issue_qnty, b.booking_id,b.booking_no,c.yarn_count_id,c.lot from inv_transaction a, inv_issue_master b,product_details_master c where a.mst_id=b.id and a.prod_id=c.id and b.entry_form=3 and b.issue_basis=1 and b.issue_purpose in(12,15,38,46,50,51) and a.company_id=$companyID and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and a.item_category=1 and a.transaction_type=2  and b.booking_id='$booking_id' ";
	}
	
	//echo $grey_issue_sql;
	$issueDataArr=sql_select($grey_issue_sql);
	if($trans_type==1)
	{
		$colSpan=7;
		$width=570;
		$padding_left=235;
		$margin_left=3;
	}
	else
	{
		$colSpan=6;
		$width=480;
		$padding_left=140;
		$margin_left=50;
	}
	?>
    <fieldset style="width:<? echo $width.'px'?>; margin-left:<? echo $margin_left.'px'?>" align="center">
		<div id="scroll_body" align="center">
			<table border="1" class="rpt_table" rules="all" width="<? echo $width?>" cellpadding="0" cellspacing="0" align="center">
				<thead>
                	<tr>
                    	<th colspan="8">Issue Details</th>
                    </tr>
                	<tr>
                        <th width="20">Sl</th>
                        <th width="100">Issue Number</th>
						<? if($trans_type==1){?>
                        <th width="90">Job No</th>
						<? } ?>
                        <th width="60">Issue Date</th>
                        <th width="100">Booking No</th>
                        <th width="60">Count</th>
                        <th width="60">Lot</th>
                        <th>Issue Qty</th>
                    </tr>
				</thead>
                <tbody>
                <? $i=1;
				foreach($issueDataArr as $row)
				{
					//var_dump($row);
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					?>
					<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                        <td width="20"><p><? echo $i; ?></p></td>
                        <td width="100"><p><? echo $row[csf('issue_number')]; ?></p></td>
						<? if($trans_type==1){?>
                        <td width="90"><p><? echo $row[csf('job_no')]; ?></p></td>
						<? } ?>
                        <td width="60"><p><? echo change_date_format($row[csf('issue_date')]); ?></p></td>
                        <td width="100"><p><? echo $row[csf('booking_no')]; ?></p></td>
                        <td width="60"><p><? echo $count_arr[$row[csf('yarn_count_id')]]; ?></p></td>
                        <td width="60"><p><? echo $row[csf('lot')]; ?></p></td>
                        <td align="right"><p><? echo number_format($row[csf('issue_qnty')],2); ?></p></td>
                    </tr>
                    <?
					$tot_issue_qty+=$row[csf('issue_qnty')];
					$i++;
				}
				?>
                </tbody>
                <tfoot>
                	<tr class="tbl_bottom">
                    	<td colspan="<? echo $colSpan;?>" align="right">Total Issue</td>
                        <td align="right">&nbsp;<? echo number_format($tot_issue_qty,2); ?>&nbsp;</td>
                    </tr>
                </tfoot>
            </table>

         <?
                    $sqlcond = "";
                    if ($companyID){
                    $sqlcond .= "  and a.company_id = $companyID ";
                    }
                    if ($booking_id) {
                    $sqlcond .= "  and b.booking_id = '$booking_id' ";
                    }
                    if ($job_no) {
                    $sqlJobcond .= "  and d.job_no_mst = '$job_no' ";
                    }
					if($trans_type==1)
					{
						//group by b.booking_id,d.job_no_mst, a.transaction_date, b.recv_number
						$issueRet_arr_sql= "select c.id as trans_id,c.quantity as issue_ret_qnty,c.reject_qty as issue_reject_qty, b.booking_id, d.job_no_mst,b.receive_date,b.recv_number,e.lot,e.yarn_count_id from inv_transaction a, inv_receive_master b, order_wise_pro_details c, wo_po_break_down d,product_details_master e where a.mst_id = b.id and a.id = c.trans_id and c.po_breakdown_id = d.id and a.prod_id=e.id and b.entry_form = 9 and a.item_category=1 and a.transaction_type=4 and b.receive_basis=1  and a.status_active=1 and a.is_deleted=0 $sqlcond  $sqlJobcond and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted = 0 and d.status_active = 1 and d.is_deleted = 0 and c.entry_form = 9 and c.trans_type = 4 ";
					}
					else
					{
						//group by b.booking_id,d.job_no_mst, a.transaction_date, b.recv_number
						// $issueRet_arr_sql= "select c.id as trans_id,c.quantity as issue_ret_qnty,c.reject_qty as issue_reject_qty, b.booking_id, d.job_no_mst,b.receive_date,b.recv_number,e.lot,e.yarn_count_id from inv_transaction a, inv_receive_master b, order_wise_pro_details c, wo_po_break_down d,product_details_master e where a.mst_id = b.id and a.id = c.trans_id and c.po_breakdown_id = d.id and a.prod_id=e.id and b.entry_form = 9 and a.item_category=1 and a.transaction_type=4 and b.receive_basis=1  and a.status_active=1 and a.is_deleted=0 $sqlcond and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted = 0 and d.status_active = 1 and d.is_deleted = 0 and c.entry_form = 9 and c.trans_type = 4 ";

						$issueRet_arr_sql = "select a.id as mst_id, a.recv_number_prefix_num,a.challan_no, a.recv_number, a.company_id, a.supplier_id, a.receive_date, a.item_category, a.recv_number, a.knitting_source, a.knitting_company,  b.id, b.cons_quantity, b.cons_reject_qnty,a.booking_id,c.yarn_count_id, c.lot from inv_receive_master a, inv_transaction b left join product_details_master c on b.prod_id=c.id where a.id=b.mst_id and b.item_category=1 and b.transaction_type=4 and a.company_id = $companyID and a.booking_id=$booking_id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by a.id desc";

					}
					//echo $issueRet_arr_sql;
					$issueRetDataArr=sql_select($issueRet_arr_sql);

					$issue_return_data=array();
					if($trans_type==1)
					{
						foreach($issueRetDataArr as $row)
						{
							$issue_return_data[$row[csf("recv_number")]][$row[csf("job_no_mst")]]["booking_id"]=$row[csf("booking_id")];
							$issue_return_data[$row[csf("recv_number")]][$row[csf("job_no_mst")]]["recv_number"]=$row[csf("recv_number")];
							$issue_return_data[$row[csf("recv_number")]][$row[csf("job_no_mst")]]["job_no_mst"]=$row[csf("job_no_mst")];
							$issue_return_data[$row[csf("recv_number")]][$row[csf("job_no_mst")]]["receive_date"]=$row[csf("receive_date")];
							$issue_return_data[$row[csf("recv_number")]][$row[csf("job_no_mst")]]["count"]=$row[csf("yarn_count_id")];
							$issue_return_data[$row[csf("recv_number")]][$row[csf("job_no_mst")]]["lot"]=$row[csf("lot")];
							if($trans_check[$row[csf("trans_id")]]=="")
							{
								$trans_check[$row[csf("trans_id")]]=$row[csf("trans_id")];
								$issue_return_data[$row[csf("recv_number")]][$row[csf("job_no_mst")]]["issueRet_qnty"]+=$row[csf("issue_ret_qnty")];
								$issue_return_data[$row[csf("recv_number")]][$row[csf("job_no_mst")]]["issueRej_qnty"]+=$row[csf("issue_reject_qty")];
							}
						}
					}
					else
					{

						foreach($issueRetDataArr as $row)
						{
							$issue_return_data[$row[csf("recv_number")]]["booking_id"]=$row[csf("booking_id")];
							$issue_return_data[$row[csf("recv_number")]]["recv_number"]=$row[csf("recv_number")];
							$issue_return_data[$row[csf("recv_number")]]["receive_date"]=$row[csf("receive_date")];
							$issue_return_data[$row[csf("recv_number")]]["count"]=$row[csf("yarn_count_id")];
							$issue_return_data[$row[csf("recv_number")]]["lot"]=$row[csf("lot")];
							$issue_return_data[$row[csf("recv_number")]]["issueRet_qnty"]+=$row[csf("cons_quantity")];
							$issue_return_data[$row[csf("recv_number")]]["issueRej_qnty"]+=$row[csf("cons_reject_qnty")];
							
						}

					}
					//var_dump($issue_return_data);

					?>
                    <table border="1" class="rpt_table" rules="all" width="<? echo $width?>" cellpadding="0" cellspacing="0" align="center">
				<thead>
                	<tr>
                    	<th colspan="8">Issue Return Details</th>
                    </tr>
                	<tr>
                        <th width="20">Sl</th>
                        <th width="100">Issue. Ret. Number</th>
						<? if($trans_type==1){?>
                        <th width="90">Job No</th>
						<? } ?>
                        <th width="60">Ret. Date</th>
                        <th width="100">Booking No</th>
                        <th width="60">Count</th>
                        <th width="60">Lot</th>
                        <th>Ret. Qty</th>
                    </tr>
				</thead>
                <tbody>
                <? $k=1;
				if($trans_type==1)
				{
					foreach($issue_return_data as $rcv_num=>$rcv_data)
					{
						foreach($rcv_data as $job_no=>$row)
						{
							if ($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							?>
							<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $k;?>">
								<td width="20"><p><? echo $k; ?></p></td>
								<td width="100"><p><? echo $row[('recv_number')]; ?></p></td>
								<td width="90"><p><? echo $row[('job_no_mst')]; ?></p></td>
								<td width="60"><p><? echo change_date_format($row[('receive_date')]); ?></p></td>
								<td width="100" ><p><? echo $booking_array[$row[('booking_id')]]; ?></p></td>
								<td width="60"><p><? echo $count_arr[$row[('count')]]; ?></p></td>
								<td width="60"><p><? echo $row[('lot')]; ?></p></td>
								<td align="right"><p><?
								$totalRet = $row[('issueRet_qnty')]+$row[('issueRej_qnty')];
								echo number_format($totalRet,2); 
								?></p></td>
							</tr>
							<?
							$tot_issueRet_qnty+=$totalRet;
							$k++;
						}

					}
				}
				else
				{
					foreach($issue_return_data as $rcv_num=>$rcv_data)
					{
						
						if ($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $k;?>">
							<td width="20"><p><? echo $k; ?></p></td>
							<td width="100"><p><? echo $rcv_data[('recv_number')]; ?></p></td>
							<? if($trans_type==1){?>
							<td width="90"><p><? echo $rcv_data[('job_no_mst')]; ?></p></td>
							<? } ?>
							<td width="60"><p><? echo change_date_format($rcv_data[('receive_date')]); ?></p></td>
							<td width="100" ><p><? echo $booking_array[$rcv_data[('booking_id')]]; ?></p></td>
							<td width="60"><p><? echo $count_arr[$rcv_data[('count')]]; ?></p></td>
							<td width="60"><p><? echo $rcv_data[('lot')]; ?></p></td>
							<td align="right"><p><?
							$totalRet = $rcv_data[('issueRet_qnty')]+$rcv_data[('issueRej_qnty')];
							echo number_format($totalRet,2); 
							?></p></td>
						</tr>
						<?
						$tot_issueRet_qnty+=$totalRet;
						$k++;
					}
				}
				?>
                </tbody>
                <tfoot>
                	<tr class="tbl_bottom">
                    	<td colspan="<? echo $colSpan;?>" align="right">Total Issue Return</td>
                        <td align="right">&nbsp;<? echo number_format($tot_issueRet_qnty,2); ?>&nbsp;</td>
                    </tr>
                </tfoot>
				
            </table>
			<table border="1" class="rpt_table" rules="all" width="<? echo $width?>" cellpadding="0" cellspacing="0" align="center">
				<tfoot>
                	<tr class="tbl_bottom">
                    	<td colspan="<? echo $colSpan;?>" style="padding-left: 235px;" align="right">Grand Total</td>
                        <td align="right">&nbsp;<? echo number_format($tot_issue_qty-$tot_issueRet_qnty,2); ?>&nbsp;</td>
                    </tr>
                </tfoot>
			</table>
          </div>
      </fieldset>
	<?
    exit();
}

if($action=="receive_popup")
{
	echo load_html_head_contents("Receive Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	//$trans_type=explode(",",$trans_type);
	$booking_array=return_library_array( "select id, ydw_no from wo_yarn_dyeing_mst",'id','ydw_no');
	$count_arr=return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');

	if($lot) $lot_cond=" and c.lot='$lot'"; else $lot_cond=''; 

	//echo $lot_cond; die();
	?>
    <fieldset style="width:570px; margin-left:3px">
    <div id="scroll_body" align="center">
	    <?
		if($trans_type==1)
		{
			$recv_sql="select b.recv_number,b.receive_date, a.job_no,a.brand_id,sum(a.cons_quantity) as recv_qnty,b.id as mrr_rcv_id, b.booking_id,b.supplier_id,c.id as product_id, c.yarn_count_id,c.lot from inv_transaction a, inv_receive_master b, product_details_master c where a.mst_id=b.id and a.prod_id=c.id and b.entry_form=1 and b.receive_basis=2 and b.receive_purpose in(12,15,38,46,50,51) and a.company_id=$companyID and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.item_category=1 and a.transaction_type=1 and a.job_no='$job_no' and b.booking_id='$booking_id' and c.dyed_type=1 $lot_cond group by a.job_no,a.brand_id,b.id,b.booking_id,b.supplier_id,c.id,c.yarn_count_id,c.lot,b.recv_number,b.receive_date"; //and a.prod_id=19184 
			//echo $recv_sql;
		
			$recvDataArr=sql_select($recv_sql);
			?>
	        <table border="1" class="rpt_table" rules="all" width="570" cellpadding="0" cellspacing="0" align="center">
	            <thead>
	                <tr>
	                    <th colspan="8">Receive Details</th>
	                </tr>
	                <tr>
	                    <th width="30">Sl</th>
	                    <th width="100">Receive Number</th>
	                    <th width="75">Receive Date</th>
	                    <th width="100">Booking No</th>
	                    <th width="100">Lot</th>
	                    <th width="80">Count</th>
	                    <th >Receive Qty</th>
	                </tr>
	            </thead>
	            <tbody>
	            <? $i=1;
	            foreach($recvDataArr as $row)
	            {
	                if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
	                ?>
	                <tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
	                    <td width="30"><p><? echo $i; ?></p></td>
	                    <td width="100"><p><? echo $row[csf('recv_number')]; ?></p></td>
	                    <td width="75"><p><? echo change_date_format($row[csf('receive_date')]); ?></p></td>
	                    <td width="100" ><p><? echo $booking_array[$row[csf('booking_id')]]; ?></p></td>
	                    <td width="100" align="center"><p><? echo $row[csf('lot')]; ?></p></td>
	                    <td width="80" align="center"><p><? echo $count_arr[$row[csf('yarn_count_id')]]; ?></p></td>
	                    <td  align="right"><p><? echo number_format($row[csf('recv_qnty')],2); ?></p></td>
	                </tr>
	                <?
	                $tot_recv_qnty+=$row[csf('recv_qnty')];
	                $i++;
	            }
	            ?>
	            </tbody>
	            <tfoot>
	                <tr class="tbl_bottom">
	                    <td colspan="6" align="right">Total Receive</td>
	                    <td align="right">&nbsp;<? echo number_format($tot_recv_qnty,2); ?>&nbsp;</td>
	                </tr>
	            </tfoot>
	        </table>
	        <?
		}
		else
		{
			$recv_sql="select b.recv_number,b.receive_date, a.job_no,a.brand_id,sum(a.cons_quantity) as recv_qnty,b.id as mrr_rcv_id, b.booking_id,b.supplier_id,c.id as product_id, c.yarn_count_id,c.lot from inv_transaction a, inv_receive_master b, product_details_master c where a.mst_id=b.id and a.prod_id=c.id and b.entry_form=1 and b.receive_basis=2 and b.receive_purpose in(12,15,38,46,50,51) and a.company_id=$companyID and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.item_category=1 and a.transaction_type=1 and b.booking_id='$booking_id' and c.dyed_type=1 $lot_cond group by a.job_no,a.brand_id,b.id,b.booking_id,b.supplier_id,c.id,c.yarn_count_id,c.lot,b.recv_number,b.receive_date"; //and a.prod_id=19184 
			//echo $recv_sql;
		
			$recvDataArr=sql_select($recv_sql);
			?>
	        <table border="1" class="rpt_table" rules="all" width="570" cellpadding="0" cellspacing="0" align="center">
	            <thead>
	                <tr>
	                    <th colspan="8">Receive Details</th>
	                </tr>
	                <tr>
	                    <th width="30">Sl</th>
	                    <th width="100">Receive Number</th>
	                    <th width="75">Receive Date</th>
	                    <th width="100">Booking No</th>
	                    <th width="100">Lot</th>
	                    <th width="80">Count</th>
	                    <th >Receive Qty</th>
	                </tr>
	            </thead>
	            <tbody>
	            <? $i=1;
	            foreach($recvDataArr as $row)
	            {
	                if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
	                ?>
	                <tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
	                    <td width="30"><p><? echo $i; ?></p></td>
	                    <td width="100"><p><? echo $row[csf('recv_number')]; ?></p></td>
	                    <td width="75"><p><? echo change_date_format($row[csf('receive_date')]); ?></p></td>
	                    <td width="100" ><p><? echo $booking_array[$row[csf('booking_id')]]; ?></p></td>
	                    <td width="100" align="center"><p><? echo $row[csf('lot')]; ?></p></td>
	                    <td width="80" align="center"><p><? echo $count_arr[$row[csf('yarn_count_id')]]; ?></p></td>
	                    <td  align="right"><p><? echo number_format($row[csf('recv_qnty')],2); ?></p></td>
	                </tr>
	                <?
	                $tot_recv_qnty+=$row[csf('recv_qnty')];
	                $i++;
	            }
	            ?>
	            </tbody>
	            <tfoot>
	                <tr class="tbl_bottom">
	                    <td colspan="6" align="right">Total Receive</td>
	                    <td align="right">&nbsp;<? echo number_format($tot_recv_qnty,2); ?>&nbsp;</td>
	                </tr>
	            </tfoot>
	        </table>
	        <?
		}

        ?>
	</div>
	</fieldset>
	<?
    exit();
}

if($action=="yd_issue_popup")//Issue/Return
{
	echo load_html_head_contents("Receive Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	$booking_array=return_library_array( "select id, ydw_no from wo_yarn_dyeing_mst",'id','ydw_no');
	$po_id_arr=return_library_array( "select job_no_mst, id from wo_po_break_down",'job_no_mst','id');
	$company_long_arr=return_library_array( "select id, company_name from  lib_company",'id','company_name');
	$supplier_arr=return_library_array( "SELECT id, supplier_name from lib_supplier where status_active=1 and is_deleted=0",'id','supplier_name');
	$po_id=$po_id_arr[$job_no];

	if($lot==0) $lot_cond=''; else $lot_cond=" and d.lot='$lot'";
	if($lot==0) $lotconds=''; else $lotconds=" and c.lot='$lot'";
	
	if($trans_type==1)
	{
		$issueDatalotSql = "select a.id as trans_id, e.job_no, a.cons_quantity, b.issue_number,b.knit_dye_company,b.knit_dye_source,b.issue_date, b.booking_no, a.requisition_no from inv_transaction a, inv_issue_master b, order_wise_pro_details c,  wo_po_break_down d, wo_po_details_master e, product_details_master f where a.mst_id =b.id and a.id = c.trans_id and c.po_breakdown_id = d.id and d.job_no_mst = e.job_no and b.entry_form = 3 and a.item_category = 1 and a.prod_id = f.id and a.transaction_type= 2 and f.item_category_id = 1 and b.issue_purpose = 1 and b.issue_basis in (1,3) and e.job_no = '$job_no' and  f.lot = '$lot' order by a.id";
	}
	else
	{
		$issueDatalotSql = "select a.id as trans_id, e.job_no, a.cons_quantity, b.issue_number,b.knit_dye_company,b.knit_dye_source,b.issue_date, b.booking_no, a.requisition_no from inv_transaction a, inv_issue_master b, order_wise_pro_details c,  wo_po_break_down d, wo_po_details_master e, product_details_master f where a.mst_id =b.id and a.id = c.trans_id and c.po_breakdown_id = d.id and d.job_no_mst = e.job_no and b.entry_form = 3 and a.item_category = 1 and a.prod_id = f.id and a.transaction_type= 2 and f.item_category_id = 1 and b.issue_purpose = 1 and b.issue_basis in (1,3)  and  f.lot = '$lot' order by a.id";
	}
   

	//echo $issueDatalotSql;
    $issueDatalotArr= sql_select($issueDatalotSql);

	if($trans_type==1)
	{
		$colSpan=6;
		$width=570;
		$padding_left=3;
		$margin_left=3;
	}
	else
	{
		$colSpan=5;
		$width=470;
		$padding_left=3;
		$margin_left=60;
		$padding_left_grand = 150;
	}

	?>
    <fieldset style="width:<? echo $width.'px';?>; margin-left:<? echo $margin_left.'px';?>">
		<div id="scroll_body" align="center">
			<table border="1" class="rpt_table" rules="all" width="<? echo $width;?>" cellpadding="0" cellspacing="0" align="center">
				<thead>
                	<tr>
                    	<th colspan="8">Issue Details</th>
                    </tr>
                	<tr>
                        <th width="30">Sl</th>
                        <th width="100">Issue Number</th>
						<? if($trans_type==1){ ?>
                        <th width="100">Job No</th>
						<? } ?>
                        <th width="100">Knitting Party</th>
                        <th width="75">Issue Date</th>
                        <th width="80" >Booking / Requisition</th>
                        <th>Issue Qty</th>
                    </tr>
				</thead>
                <tbody>
                <? $i=1;$tot_issue_qnty=0;
				foreach($issueDatalotArr as $row)
				{
					$knittinig_party="";
					if ($row[csf('knit_dye_source')]==1)
					{
						$knittinig_party=$company_long_arr[$row[csf('knit_dye_company')]];
					}
					else
					{
						$knittinig_party=$supplier_arr[$row[csf('knit_dye_company')]];
					}
						
                    $booking_requisition = "";
                    if($row[csf('booking_no')]){
                        $booking_requisition = $row[csf('booking_no')];
                    }else if($row[csf('requisition_no')]){
                        $booking_requisition = $row[csf('requisition_no')];
                    }
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					//$rec_ret_qty=$lotRecRet_arr[$row[csf('job_no')]][$row[csf('color')]][$row[csf('lot')]];
					?>
					<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                        <td width="30"><p><? echo $i; ?></p></td>
                        <td width="100"><p><? echo $row[csf('issue_number')]; ?></p></td>
						<? if($trans_type==1){ ?>
                        <td width="100"><p><? echo $row[csf('job_no')]; ?></p></td>
						<? } ?>
                        <td width="100"><p><? echo $knittinig_party; ?></p></td>
                        <td width="75"><p><? echo change_date_format($row[csf('issue_date')]); ?></p></td>
                        <td width="80" align="center"><p><? echo $booking_requisition; ?></p></td>
                        <td align="right"><p><? echo number_format($row[csf('cons_quantity')],2); ?></p></td>
                    </tr>
                    <?
					$tot_issue_qnty+=$row[csf('cons_quantity')];
					$i++;
				}
				?>
                </tbody>
                <tfoot>
                	<tr class="tbl_bottom">
                    	<td colspan="<? echo $colSpan;?>" align="right">Total Issue : &nbsp;</td>
                        <td align="right">&nbsp;<? echo number_format($tot_issue_qnty,2); ?>&nbsp;</td>
                    </tr>
                </tfoot>
            </table>
            <br/>
			<?
            $lotconds = "";
            if($lot != ""){ $lotconds .= " and f.lot = '$lot'";}

			$booking_job_arr=return_library_array( "select mst_id, job_no from wo_yarn_dyeing_dtls where status_active=1 and is_deleted=0",'mst_id','job_no');
			if($trans_type==1)
			{	
            $issueRetDataArr="select a.id as trans_id, e.job_no, b.recv_number, b.receive_date, b.knitting_source, b.knitting_company, a.cons_quantity,a.cons_reject_qnty,b.booking_no from inv_transaction a, inv_receive_master b, order_wise_pro_details c,  wo_po_break_down d, wo_po_details_master e, product_details_master f where a.mst_id =b.id and a.id = c.trans_id and c.po_breakdown_id = d.id and d.job_no_mst = e.job_no and b.entry_form = 9 and a.item_category = 1 and a.prod_id = f.id and a.transaction_type= 4 and f.item_category_id = 1  $lotconds and e.job_no = '$job_no' order by a.id";
			}
			else
			{
				$issueRetDataArr="select a.id as trans_id, e.job_no, b.recv_number, b.receive_date, b.knitting_source, b.knitting_company, a.cons_quantity,a.cons_reject_qnty,b.booking_no from inv_transaction a, inv_receive_master b, order_wise_pro_details c,  wo_po_break_down d, wo_po_details_master e, product_details_master f where a.mst_id =b.id and a.id = c.trans_id and c.po_breakdown_id = d.id and d.job_no_mst = e.job_no and b.entry_form = 9 and a.item_category = 1 and a.prod_id = f.id and a.transaction_type= 4 and f.item_category_id = 1  $lotconds order by a.id";
			}
		
			//echo $issueRetDataArr;
			$issueRetData = sql_select($issueRetDataArr);
	 		?>

            <table border="1" class="rpt_table" rules="all" width="<? echo $width;?>" cellpadding="0" cellspacing="0" align="center">
				<thead>
                	<tr>
                    	<th colspan="8">Issue Return Details</th>
                    </tr>
                	<tr>
                        <th width="30">Sl</th>
                        <th width="100">Issue. Ret. Number</th>
						<? if($trans_type==1){ ?>
                        <th width="100">Job No</th>
						<? } ?>
                        <th width="100">Knitting Party</th>
                        <th width="75">Ret. Date</th>
                        <th width="80" >Booking/ <br> Requisition</th>
                        <th>Ret. Qty</th>
                    </tr>
				</thead>
                <tbody>
                <? $k=1;
				foreach($issueRetData as $row)
				{
					$knittinig_party="";
					if ($row[csf('knitting_source')]==1)
					{
						$knittinig_party=$company_long_arr[$row[csf('knitting_company')]];
					}
					else
					{
						$knittinig_party=$supplier_arr[$row[csf('knitting_company')]];
					}
					if ($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

					$totalReturnQty = $row[csf('cons_quantity')]+$row[csf('cons_quantity')];
					?>
					<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? //echo $k; ?>','<? echo $bgcolor;?>')" id="tr_<? //echo $k;?>">
                        <td width="30"><p><? echo $k; ?></p></td>
                        <td width="100"><p><? echo $row[csf('recv_number')]; ?></p></td>
						<? if($trans_type==1){ ?>
                        <td width="100"><p><? echo $row[csf('job_no')]; ?></p></td>
						<? } ?>
                        <td width="100"><p><? echo $knittinig_party; ?></p></td>
                        <td width="75"><p><? echo change_date_format($row[csf('receive_date')]); ?></p></td>
                        <td width="80" align="center"><p><? echo $row[csf('booking_no')]; ?></p></td>
                        <td align="right"><p><? echo number_format($totalReturnQty,2); ?></p></td>
                    </tr>
                    <?
					$tot_issRet_qnty+=$totalReturnQty;
					$k++;
				}
				?>
                </tbody>
                <tfoot>
                	<tr class="tbl_bottom">
                    	<td colspan="<? echo $colSpan;?>" align="right">Total Issue Return : &nbsp;</td>
                        <td align="right">&nbsp;<? echo number_format($tot_issRet_qnty,2); ?>&nbsp;</td>
                    </tr>
                </tfoot>
            </table>
          </div>
      </fieldset>
	  <fieldset style="width:<? echo $width.'px';?>; margin-left:<? echo $margin_left.'px'?>">
		<table border="1" class="rpt_table" rules="all" width="<? echo $width;?>" cellpadding="0" cellspacing="0" align="center">
			<tfoot>
				<tr class="tbl_bottom">
					<td colspan="5" align="right" style="padding-left:<? echo $padding_left_grand.'px';?>">Grand Total : &nbsp;</td>
					<td align="right">&nbsp;<? echo number_format($tot_issue_qnty-$tot_issRet_qnty,2); ?>&nbsp;</td>
				</tr>
			</tfoot>
		</table>
	  </fieldset>
	<?
    exit();
}