<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];


if ($action=="batch_popup")
{
	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$data=explode('_',$data);
	//print_r ($data);  
?>	
    <script>
		
		var selected_id = new Array;
		var selected_name = new Array;
		
    	function check_all_data() {
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;
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
				//alert(strCon);
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

</head>

<body>
<div align="center">
	<fieldset style="width:870px;margin-left:4px;">
        <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
            <table cellpadding="0" cellspacing="0" width="650" class="rpt_table">
                <thead>
                    <th>Batch Type</th>
                    <th>Search By</th>
                    <th>Search</th>
                    <th>
                        <input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
                        <input type="hidden" name="hidden_batch_id" id="hidden_batch_id" value="">
                    </th> 
                </thead>
                <tr class="general">
                <td align="center">	
							<?
                                echo create_drop_down( "cbo_batch_type", 150, $order_source,"",1, "--Select--",$data[1],"",1 );
                            ?>
                            </td>
                    <td align="center">	
                        <?
                            if($data[2]==2)
							{
								$show_con="2";
							}
							else
							{
								$show_con="1,2";
							}
							//echo $show_con;
							$search_by_arr=array(1=>"Batch No",2=>"Booking No");
                            echo create_drop_down( "cbo_search_by", 150, $search_by_arr,"",0, "--Select--", $show_con,$dd,0);
                        ?>
                    </td>                 
                    <td align="center">				
                        <input type="text" style="width:140px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />	
                    </td> 						
                    <td align="center">
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+<? echo $data[0]; ?>+'_'+<? echo $data[1]; ?>+'_'+<? echo $data[2]; ?>, 'create_batch_search_list_view', 'search_div', 'batch_dying_fnishing_cost_sales_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
                    </td>
                </tr>
            </table>
            <div id="search_div" style="margin-top:10px"></div>   
        </form>
    </fieldset>
   
</div>    
</body>           
<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?

}




if($action=="create_batch_search_list_view")
{
	$data=explode('_',$data);
	
	$search_string="%".trim($data[0])."%";
	$search_by =$data[1];
	$company_id =$data[2];
	$batch_type=$data[3];
	$booking_batch_type=$data[4];
	if($batch_type==0)
		$search_field_cond_batch=" and a.entry_form in (0,36)";
	else if($batch_type==1)
		$search_field_cond_batch=" and a.entry_form=0";
	else if($batch_type==2)
		$search_field_cond_batch=" and a.entry_form=36";
	else if($batch_type==3)
		$search_field_cond_batch=" and a.batch_against=2";
	if($search_by==1)
		$search_field='a.batch_no';
	else
		$search_field='a.booking_no';
		
	if($booking_batch_type==2)
	{
		$book_batch_cond="booking_no";	
	}
	else
	{
		$book_batch_cond="batch_no";		
	}
	
	
	// $book_batch_cond.'dd';
	$color_arr=return_library_array( "select id,color_name  from  lib_color", "id", "color_name"  );	
	$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
	$arr=array(5=>$batch_against,6=>$batch_for,8=>$color_arr);
	if($batch_type==2)
	{
		 $sql = "select a.id, a.batch_no, a.extention_no, a.batch_weight, a.total_trims_weight, a.batch_date, a.batch_against, a.batch_for, a.booking_no, a.color_id from pro_batch_create_mst a where  a.company_id=$company_id and $search_field like '$search_string' and a.status_active=1 and a.is_deleted=0 and a.batch_against<>0 $search_field_cond_batch order by a.batch_date"; 
	}
	else
	{
	 $sql = "select a.id, a.batch_no, a.extention_no, a.batch_weight, a.total_trims_weight, a.batch_date, a.batch_against, a.batch_for, a.booking_no, a.color_id from pro_batch_create_mst a,fabric_sales_order_mst b where a.sales_order_no=b.job_no and a.company_id=$company_id and $search_field like '$search_string' and a.status_active=1 and a.is_deleted=0 and a.batch_against<>0 $search_field_cond_batch order by a.batch_date"; 
	}
		 
	echo  create_list_view("tbl_list_search", "Batch No,Ext. No,Batch Weight,Total Trims Weight, Batch Date,Batch Against,Batch For, Booking No, Color", "100,70,80,80,80,80,85,105,80","860","250",0, $sql, "js_set_value", "id,$book_batch_cond", "", 1, "0,0,0,0,0,batch_against,batch_for,0,color_id", $arr, "batch_no,extention_no,batch_weight,total_trims_weight,batch_date,batch_against,batch_for,booking_no,color_id", "","setFilterGrid('tbl_list_search',-1)",'0,0,2,2,3,0,0,0,0',"",1);
    echo "<input type='hidden' id='txt_selected_id' />";
	echo "<input type='hidden' id='txt_selected' />";
exit();	
}

if($action=="sales_order_no_search_popup")
	{
		echo load_html_head_contents("Sales Order No  Info","../../../../", 1, 1, $unicode);
		extract($_REQUEST);
		?>
		<script>
			function js_set_value(job_no)
			{
				document.getElementById('hidden_job_no').value=job_no;
				parent.emailwindow.hide();
			}	
		</script>
	</head>
	<body>
		<div align="center">
			<form name="styleRef_form" id="styleRef_form">
				<fieldset style="width:0px;">
					<table cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
						<table cellpadding="0" cellspacing="0" width="600" border="1" rules="all" class="rpt_table">
							<thead>
								<th>Within Group</th>
								 <th>Search By</th>
								<th id="search_by_td_up">Search</th>
								<th>
									<input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
									<input type="hidden" name="hidden_job_no" id="hidden_job_no" value="">
									<input type="hidden" name="hidden_yearID" id="hidden_yearID" value="<? echo $yearID; ?>">

								</th> 
							</thead>
							<tr class="general">
								<td align="center">	
									<?
									echo create_drop_down( "cbo_within_group", 150, $yes_no,"",1, "--Select--", $cbo_within_group,$dd,0 );
									?>
								</td>  
								  <td align="center">	
								<?
									
									//echo $show_con;
									$search_by_arr=array(1=>"FSO No",2=>"Booking No",3=>"Style Ref");
									//$dd="change_search_event(this.value, '0*0*0', '0*0*0', '../../../../') ";							
									echo create_drop_down( "cbo_search_by", 150, $search_by_arr,"",0, "--Select--", "",$dd,0 );
									//echo create_drop_down( "cbo_search_by", 150, $search_by_arr,"",0, "--Select--", $show_con,$dd,0);
								?>
								</td>           
								<td align="center">				
									<input type="text" style="width:140px" class="text_boxes"  name="txt_search_common" id="txt_search_common" placeholder="Enter Sales Order No" />	
								</td> 						
								<td align="center">
									<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_within_group').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('hidden_yearID').value, 'create_sales_order_no_search_list', 'search_div', 'batch_dying_fnishing_cost_sales_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
								</td>
							</tr>
						</table>
						<div style="margin-top:15px" id="search_div"></div>
					</table>
				</fieldset>
			</form>
		</div>
	</body>           
	<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit(); 
}

if($action=="create_sales_order_no_search_list")
{
	$data 			= explode('_',$data);
	$sales_order_no = trim($data[0]);
	$within_group 	= $data[1];
	$search_by 		=  $data[2];
	$yearID 		=  $data[3];
	$company_arr=return_library_array( "select id,company_short_name from lib_company",'id','company_short_name');
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );
	//echo $search_by.'Dd';
	$location_arr 	= return_library_array("select id, location_name from lib_location",'id','location_name');

	if($db_type==0)
	{
		if($yearID!=0) $year_cond=" and YEAR(a.insert_date)=$yearID"; else $year_cond="";
	}
	else if($db_type==2)
	{
		if($yearID!=0) $year_cond=" and to_char(a.insert_date,'YYYY')=$yearID";  else $year_cond="";
	}
		if($search_by==1)
		{
			$sales_order_cond   = ($sales_order_no == "")?"":" and a.job_no_prefix_num =$sales_order_no";
		}
		else if($search_by==2) //Booking
		{
			$sales_order_cond   = ($sales_order_no == "")?"":" and a.sales_booking_no like '%$sales_order_no%'";
		}
		else if($search_by==3) //Srtyle
		{
			$sales_order_cond   = ($sales_order_no == "")?"":" and a.style_ref_no like '%$sales_order_no%'";
		}
	$within_group_cond  = ($within_group == 0)?"":" and a.within_group=$within_group";
	
	$year_field 		= ($db_type == 2)? "to_char(a.insert_date,'YYYY') as year":"YEAR(a.insert_date) as year";

	 $sql = "select a.id, $year_field, a.job_no_prefix_num, a.job_no, a.within_group, a.sales_booking_no,a.booking_date, a.buyer_id, a.style_ref_no, a.location_id from fabric_sales_order_mst a where a.status_active=1 and a.is_deleted=0 $within_group_cond $search_field_cond $sales_order_cond $year_cond order by a.id";
	$result = sql_select($sql);
	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="950" class="rpt_table">
		<thead>
			<th width="40">SL</th>
			<th width="90">Sales Order ID</th>
			<th width="110">Sales Order No</th>
			<th width="120">Booking No</th>
			<th width="80">Booking date</th>
			<th width="60">Year</th>
			<th width="80">Within Group</th>
			<th width="70">Buyer/Unit</th>			
			<th width="110">Style Ref.</th>
			<th>Location</th>
		</thead>
	</table>
	<div style="width:950px; max-height:260px; overflow-y:scroll" id="list_container_batch" align="left">	 
		<table cellspacing="0" cellpadding="3" border="1" rules="all" width="930" class="rpt_table" id="tbl_list_search">  
			<?
			$i=1;
			foreach ($result as $row)
			{  
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

				if($row[csf('within_group')]==1){
					$buyer=$company_arr[$row[csf('buyer_id')]]; 
				}else{
					$buyer=$buyer_arr[$row[csf('buyer_id')]];
				}
				$sales_order_no = $row[csf('job_no')];
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $sales_order_no; ?>');">
					<td width="40" align="center"><? echo $i; ?></td>
					<td width="90" align="center"><p>&nbsp;<? echo $row[csf('job_no_prefix_num')]; ?></p></td>
					<td width="110" align="center"><p>&nbsp;<? echo $row[csf('job_no')]; ?></p></td>
					<td width="120" align="center"><p><? echo $row[csf('sales_booking_no')]; ?></p></td>
					<td width="80" align="center"><? echo change_date_format($row[csf('booking_date')]); ?></td>
					<td width="60" align="center"><p><? echo $row[csf('year')]; ?></p></td>
					<td width="80" align="center"><? echo $yes_no[$row[csf('within_group')]]; ?></td>
					<td width="70" align="center" style="word-break: break-all; "><? echo $buyer; ?></td>					
					<td width="110" align="center"><p><? echo $row[csf('style_ref_no')]; ?></p></td>
					<td><p><? echo $location_arr[$row[csf('location_id')]]; ?></p></td>
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


if($action=="generate_report")
{
	 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	if ($cbo_company_name==0) $company_id =""; else $company_id =" and a.company_id='$cbo_company_name'";
	$from_date=str_replace("'","",$from_date);
	$to_date=str_replace("'","",$to_date);
	//echo $from_date;
	//$txt_ref_no=str_replace("'","",$txt_ref_no);
	//$txt_file_no=str_replace("'","",$txt_file_no);
	$txt_booking_no=str_replace("'","",$txt_booking_no);
	$txt_po_no=str_replace("'","",$txt_po_no);
	$txt_job=str_replace("'","",$txt_job);
	$batch_type=str_replace("'","",$batch_type);
	//echo $batch_type;die;
	/*$booking_data=explode(",",$txt_booking_no);
	foreach($booking_data as $book)
	{
		
	}*/
	if($txt_booking_no!='') $booking_no_cond="and b.booking_no like '%$txt_booking_no%' ";else $booking_no_cond="";
	if($txt_booking_no!='') $booking_no_cond2="and c.booking_no like '%$txt_booking_no%' ";else $booking_no_cond2="";
	
	//if($txt_file_no!='') $file_cond="and b.file_no=$txt_file_no";else $file_cond="";
	if($txt_job!='') $job_cond="and a.job_no_prefix_num=$txt_job";else $job_cond="";
	if($txt_po_no!='') $po_cond="and d.job_no LIKE '%$txt_po_no%'";else $po_cond="";
	
	//if($txt_ref_no!='') $ref_cond="and b.grouping='$txt_ref_no'";else $ref_cond="";
	//echo $ref_cond;
	$companyArr = return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0","id","company_name"); 
	$supplierArr = return_library_array("select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0","id","supplier_name"); 
	$color_arr=return_library_array( "select id,color_name  from  lib_color", "id", "color_name"  );
	$job_no_arr=return_library_array(" select  id ,job_no_mst from   wo_po_break_down", "id", "job_no_mst" );
	//$po_break_down_id_arr=return_library_array( "select booking_no,po_break_down_id from  wo_booking_mst ", "booking_no", "po_break_down_id" );
	$machine_name_arr=return_library_array("select  a.batch_id ,b.machine_no from   pro_fab_subprocess a, lib_machine_name b  where a.machine_id=b.id and a.entry_form=35 and  b.status_active in(1,2) and b.is_deleted=0", "batch_id", "machine_no" );
	$buyer_name_arr=return_library_array( "select a.booking_no,b.buyer_name from  wo_booking_mst a, lib_buyer b where a.buyer_id=b.id", "booking_no", "buyer_name" );
	$non_buyer_name_arr=return_library_array( "select a.booking_no,b.buyer_name from  wo_non_ord_samp_booking_mst a, lib_buyer b where a.buyer_id=b.id", "booking_no", "buyer_name" );
    $buyer_library=return_library_array( "select id,buyer_name from   lib_buyer", "id","buyer_name" );
   // $style_library=return_library_array( "select job_no,style_ref_no from   wo_po_details_master", "job_no","style_ref_no" );
	$po_data_subcon=sql_select("select a.mst_id,b.order_no,c.subcon_job, c.party_id,b.cust_style_ref from pro_batch_create_dtls a, subcon_ord_dtls b,
	subcon_ord_mst c where a.po_id=b.id and b.job_no_mst=c.subcon_job and a.status_active=1 and a.is_deleted=0  ");
	$sub_con_arr=array();
	foreach($po_data_subcon as $row)
	{
		if($sub_con_arr[$row[csf('mst_id')]][cust_style_ref]!="")
		{
		$sub_con_arr[$row[csf('mst_id')]][cust_style_ref].=",".$row[csf('cust_style_ref')];
		}
		else
		{
		$sub_con_arr[$row[csf('mst_id')]][cust_style_ref]=$row[csf('cust_style_ref')];	
		}
		
		if($sub_con_arr[$row[csf('mst_id')]][order_no]!="")
		{
		$sub_con_arr[$row[csf('mst_id')]][order_no].=",".$row[csf('order_no')];
		}
		else
		{
		$sub_con_arr[$row[csf('mst_id')]][order_no]=$row[csf('order_no')];	
		}
		
		if($sub_con_arr[$row[csf('mst_id')]][subcon_job]!="")
		{
		$sub_con_arr[$row[csf('mst_id')]][subcon_job].=",".$row[csf('subcon_job')];
		}
		else
		{
		$sub_con_arr[$row[csf('mst_id')]][subcon_job]=$row[csf('subcon_job')];	
		}
		if($sub_con_arr[$row[csf('mst_id')]][party_id]!="")
		{
		$sub_con_arr[$row[csf('mst_id')]][party_id].=",".$buyer_library[$row[csf('party_id')]];
		}
		else
		{
		$sub_con_arr[$row[csf('mst_id')]][party_id]=$buyer_library[$row[csf('party_id')]];	
		}
	}
	//print_r($sub_con_arr[646]);die;
	
	if($db_type==0) 
	{
	$from_date=change_date_format($from_date,'yyyy-mm-dd'); $to_date=change_date_format($to_date,'yyyy-mm-dd');
	}
    if($db_type==2) 
	{
	$from_date=change_date_format($from_date,'','',1);  $to_date=change_date_format($to_date,'','',1);
	}
	//echo $from_date;

	$batch_description_arr=array();
	/* 
	if(str_replace("'","",$batch_id)!="") $batch_cond=" and b.id in(".str_replace("'","",$batch_id).")";
    else  */
	if(str_replace("'","",$batch_id)!="" && str_replace("'","",$batch_no)!="") 
	{ 
		$batch_cond=" and b.id in(".str_replace("'","",$batch_id).")";
	}
	else if(str_replace("'","",$batch_no)!="" && str_replace("'","",$batch_id)=="") 
	{ 
		$batch_cond=" and b.batch_no like '%".$batch_no."%' ";
	}
    else    $batch_cond="";
	/* and b.re_dyeing_from=0 13-11-16 according to siddique sir dicission when search buyer order or subcontract all batch appear */
	if($batch_type==0)
	$search_field_cond_batch="and b.entry_form in (0,36) and b.re_dyeing_from=0";
	else if($batch_type==1)
	$search_field_cond_batch="and b.entry_form=0 ";
	else if($batch_type==2)
	$search_field_cond_batch="and b.entry_form=36";
	else 
	 $search_field_cond_batch="and b.entry_form in(0,36) and b.batch_against in(2) and  b.re_dyeing_from!=0 ";
	 
	 if($batch_type==2) //Subcon
	 {
		 $sales_cond="";
	 }
	 else $sales_cond="and b.is_sales=1";
	 //echo $batch_type.'='.$sales_cond;
	//$redyeing="and b.batch_against in(2)";
	//echo  $search_field_cond_batch;die;
	//$po_number_arr=return_library_array(" select  id ,po_number from   wo_po_break_down", "id", "po_number" );//sales_booking_no
	$sql_po=("select a.style_ref_no,d.id as sales_order_id,c.booking_no,b.po_number,b.job_no_mst,d.job_no as sales_no,d.within_group,d.buyer_id,d.po_buyer from wo_po_details_master a,wo_po_break_down b,wo_booking_dtls c,fabric_sales_order_mst d  where a.job_no=b.job_no_mst and   a.job_no=c.job_no and b.id=c.po_break_down_id and d.sales_booking_no=c.booking_no and   b.status_active=1 and b.is_deleted=0 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $booking_no_cond2  $job_cond $po_cond group by a.style_ref_no,d.id,b.po_number,b.job_no_mst,d.job_no,d.within_group,d.buyer_id,d.po_buyer,c.booking_no ");
	 
	  $res_po=sql_select($sql_po);
	   $all_po_id='';
	foreach($res_po as $row) //$job_no_arr
	{
		
		 $po_number_arr[$row[csf('sales_no')]]['job'].=$row[csf('job_no_mst')].',';
		 $po_number_arr[$row[csf('sales_no')]]['style'].=$row[csf('style_ref_no')].',';
		 $multi_job_arr[$row[csf('booking_no')]]['style']=$row[csf('style_ref_no')];
		 $multi_job_arr[$row[csf('booking_no')]]['job']=$row[csf('job_no_mst')];
		
		  
		 if($all_po_id=="") $all_po_id=$row[csf('sales_order_id')]; else $all_po_id.=",".$row[csf('sales_order_id')]; //echo $all_po_id;
	}
	//echo $all_po_id;
	if($txt_po_no!='' || $txt_job!='')
	{
		if($all_po_id!="") $po_idd="and po_id in($all_po_id)";else $po_idd="and po_id in(0)";
		//echo $po_idd;
		//echo "select LISTAGG(CAST( mst_id  AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY mst_id) as batch_id from pro_batch_create_dtls where status_active=1 and is_deleted=0 $po_idd";
		/*if($db_type==0) $group_con="group_concat(mst_id) as batch_id";
	else  $group_con="LISTAGG(CAST( mst_id  AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY mst_id) as batch_id";
		$batch_ids=return_field_value("$group_con","pro_batch_create_dtls","status_active=1 and is_deleted=0 $po_idd","batch_id");
		if($batch_ids!="") $batch_id_cond="and b.id in($batch_ids)"; else $batch_id_cond="";	*/	
		
//echo "select mst_id as batch_id from pro_batch_create_dtls  where   status_active=1 and is_deleted=0 $po_idd";die;
		$sql_batch=sql_select("select mst_id as batch_id from pro_batch_create_dtls  where   status_active=1 and is_deleted=0 $po_idd");
		 $all_batch_id='';
		foreach($sql_batch as $row) 
		{
			if($all_batch_id=="") $all_batch_id=$row[csf('batch_id')]; else $all_batch_id.=",".$row[csf('batch_id')];
		}
			$all_batch_ids=implode(",",array_unique(explode(",",$all_batch_id)));
		//echo $all_batch_ids;die;
		$batch_ids_cond="";
		if($all_batch_ids!="") 
		{
			//echo $po_id=substr($po_id,0,-1);
			if($db_type==0) { $batch_ids_cond="and b.id in(".$all_batch_ids.")"; }
			else
			{
				$bat_id=array_unique(explode(",",$all_batch_ids));
				if(count($bat_id)>1000)
				{
					$batch_ids_cond="and (";
					$bat_id=array_chunk($bat_id,1000);
					$z=0;
					foreach($bat_id as $id)
					{
						$id=implode(",",$id);
						if($z==0) $batch_ids_cond.=" b.id in(".$id.")";
						else $batch_ids_cond.=" or b.id in(".$id.")";
						$z++;
					}
					$batch_ids_cond.=")";
				}
				else { 
				
				$batch_ids_cond=" and b.id in(".$all_batch_ids.")"; }
			}
		}
	}
//print_r($batch_ids);die;
	
    if($from_date!="")
    {
		if(str_replace("'","",$cbo_value_with)==1)
		{
		 $date_cond=" and a.process_end_date between '".$from_date."' and '".$to_date."' ";
	
		 $sql=sql_select("select b.id,a.batch_no,b.sales_order_no,a.fabric_type,b.booking_without_order,a.process_end_date as batch_date,b.color_range_id,b.booking_no,b.batch_weight,b.color_id,b.entry_form, b.batch_against, b.re_dyeing_from
		 from pro_fab_subprocess  a,pro_batch_create_mst b  where  a.batch_id=b.id 
		 and a.entry_form in (35,38) and a.load_unload_id=2 and a.status_active=1 and a.is_deleted=0  and b.company_id=$cbo_company_name $sales_cond  $batch_cond $batch_ids_cond $date_cond $search_field_cond_batch $booking_no_cond order by a.process_end_date, b.id");
		}
		else if(str_replace("'","",$cbo_value_with)==2)
		{
		 $date_cond=" and b.batch_date between '".$from_date."' and '".$to_date."'";
		
		  $sql=sql_select("select b.id,b.batch_no,b.sales_order_no,b.booking_without_order,b.color_range_id,b.batch_date,b.booking_no,b.batch_weight,b.color_id,b.entry_form, b.batch_against, b.re_dyeing_from 
		  from  pro_batch_create_mst b where  b.company_id=$cbo_company_name    and b.status_active=1 and b.is_deleted=0 $batch_cond $sales_cond $batch_ids_cond $date_cond $search_field_cond_batch $booking_no_cond order by b.batch_date, b.id");
		  
		  
		/*  echo "select b.id,b.batch_no,b.sales_order_no,b.booking_without_order,b.color_range_id,b.batch_date,b.booking_no,b.batch_weight,b.color_id,b.entry_form, b.batch_against, b.re_dyeing_from 
		  from  pro_batch_create_mst b where  b.company_id=$cbo_company_name and b.is_sales=1  and b.status_active=1 and b.is_deleted=0 $batch_cond $batch_ids_cond $date_cond $search_field_cond_batch $booking_no_cond order by b.batch_date, b.id";*/
	 
		 
		
		}
   }
   else
   {
	 $sql=sql_select("select b.id,b.booking_without_order,b.batch_no,b.sales_order_no,b.color_range_id,b.batch_date,b.booking_no,b.batch_weight,b.color_id,b.entry_form, b.batch_against, b.re_dyeing_from 
	 from  pro_batch_create_mst b where b.company_id=$cbo_company_name   and b.status_active=1 and b.is_deleted=0 $sales_cond $batch_cond $batch_ids_cond $search_field_cond_batch $booking_no_cond order by b.batch_date, b.id");
	// echo "select b.id,b.booking_without_order,b.batch_no,b.sales_order_no,b.color_range_id,b.batch_date,b.booking_no,b.batch_weight,b.color_id,b.entry_form, b.batch_against, b.re_dyeing_from 
	// from  pro_batch_create_mst b where b.company_id=$cbo_company_name  and b.is_sales=1 and b.status_active=1 and b.is_deleted=0 $batch_cond $batch_ids_cond $search_field_cond_batch $booking_no_cond";
	
   }
 	/*echo "select b.id,b.booking_without_order,b.batch_no,b.color_range_id,b.batch_date,b.booking_no,b.batch_weight,b.color_id,b.entry_form, b.batch_against, b.re_dyeing_from 
	 from  pro_batch_create_mst b where b.company_id=$cbo_company_name and b.status_active=1 and b.is_deleted=0 $batch_cond $batch_ids_cond $search_field_cond_batch $booking_no_cond";die;*/
 
  	$reding_batch_id=$non_reding_batch_id=array();
    foreach($sql as $row)
	 {
		 if($row[csf("batch_against")]==2)
		 {
			 /*if($row[csf("re_dyeing_from")]>0)
			 {
				 $reding_batch_id[$row[csf("re_dyeing_from")]]=$row[csf("re_dyeing_from")]; 
			 }
			 else
			 {
				 $reding_batch_id[$row[csf("id")]]=$row[csf("id")]; 
			 }*/
			 $reding_batch_id[$row[csf("id")]]=$row[csf("id")];
			 
		 }
		 else
		 {
			 $non_reding_batch_id[$row[csf("id")]]=$row[csf("id")];
		 }
		 
		 $batch_id_arr[]=$row[csf("id")]; 
		 $batch_description_arr[$row[csf("id")]]['batch_date']=$row[csf("batch_date")];
		 $batch_description_arr[$row[csf("id")]]['booking_no']=$row[csf("booking_no")];
		 $batch_description_arr[$row[csf("id")]]['sales_order_no']=$row[csf("sales_order_no")];
		 $batch_description_arr[$row[csf("id")]]['without_order']=$row[csf("booking_without_order")];
		 $batch_description_arr[$row[csf("id")]]['batch_weight']=$row[csf("batch_weight")];
		 $batch_description_arr[$row[csf("id")]]['color_id']=$row[csf("color_id")];
		 $batch_description_arr[$row[csf("id")]]['entry_form']=$row[csf("entry_form")];
		 $batch_description_arr[$row[csf("id")]]['color_range']=$row[csf("color_range_id")];
		 $batch_description_arr[$row[csf("id")]]['fabric_type']=$row[csf("fabric_type")];
		 $batch_no_arr[]="'".$row[csf("batch_no")]."'";
		 $batch_arr[$row[csf("id")]]=$row[csf("batch_no")];
	 }
	 
	 
	// print_r($batch_id_arr);
	$date_cond2=" and a.process_end_date between '".$from_date."' and '".$to_date."' ";
	$fabric_sql=sql_select("select b.id,c.item_description,d.job_no as sales_no,d.within_group,d.po_buyer,d.buyer_id
	from  pro_batch_create_mst b,pro_batch_create_dtls c ,fabric_sales_order_mst d  where  c.mst_id=b.id and 
	   b.sales_order_id=d.id and c.status_active=1 and c.is_deleted=0 and b.company_id=$cbo_company_name $sales_cond ");
 	//and b.is_sales=1
  	 $fabric_type_arr=array();
     foreach($fabric_sql as $row)
	 {
		 $fabric_type_arr[$row[csf("id")]]['fabric_type'].=$row[csf("item_description")].',';
		
		 if($row[csf('within_group')]>0)
		 {
		  $po_number_arr[$row[csf('sales_no')]]['within_group']=$row[csf('within_group')];
		  $po_number_arr[$row[csf('sales_no')]]['po_buyer']=$row[csf('po_buyer')];
		  $po_number_arr[$row[csf('sales_no')]]['buyer_id']=$row[csf('buyer_id')];
		  }
	 }
	 
	 
	 
	 
	/* echo "<pre>";
	 print_r($reding_batch_id);
	 echo "<pre>";
	 print_r($non_reding_batch_id);die;*/
//	***************************************************************************************************************************************************
     $batch_id_sql=implode(",",$batch_id_arr);
    if($batch_id_sql=="") $batch_id_sql=0;
	//echo  $batch_id_sql;
  if($batch_type==3)
	{
		$re_dying_cond2=" and b.id in(".$batch_id_sql.")";
	}
	else if($batch_type==2)
	{
		$re_dying_cond3=" and b.id in(".$batch_id_sql.")";
	}
	else
	{
		$re_dying_cond=" and b.re_dyeing_from in(".$batch_id_sql.")";	
	}
	if($db_type==0)
	{
		if($batch_type==3)
		{
		 $sql_redying=sql_select("select b.id as id,group_concat(b.batch_date)  as batch_date,group_concat(b.extention_no)  as extention_no,b.re_dyeing_from from  pro_batch_create_mst b where b.re_dyeing_from!=0 and b.company_id=$cbo_company_name $re_dying_cond2 and b.status_active=1 and b.is_deleted=0 group by  b.id order by b.id");
		}
		else
		{
			 $sql_redying=sql_select("select group_concat(b.id) as id,group_concat(b.batch_date)  as batch_date,group_concat(b.extention_no)  as extention_no,b.re_dyeing_from from  pro_batch_create_mst b where b.re_dyeing_from!=0 and b.company_id=$cbo_company_name $re_dying_cond and b.status_active=1 and b.is_deleted=0 group by  b.re_dyeing_from order by b.re_dyeing_from");	
		}
	}
	else
	{
	
		if($batch_type==3)
		{	
			 $sql_redying=sql_select("select b.id as id,listagg((b.batch_date),',') within group (order by b.batch_date) as batch_date,listagg((b.extention_no),',') within group (order by b.extention_no) as extention_no
			  from  pro_batch_create_mst  b 
			  where b.re_dyeing_from!=0 and b.company_id=$cbo_company_name $re_dying_cond2 and b.status_active=1 and b.is_deleted=0  group by  b.id order by b.id");
			  
		}
		else
		{
			 $sql_redying=sql_select("select  b.id as re_dyeing_from,listagg((b.batch_date),',') within group (order by b.batch_date) as batch_date,listagg((b.extention_no),',') within group (order by b.extention_no) as extention_no,b.id 
			 from  pro_batch_create_mst b 
			 where   b.company_id=$cbo_company_name $re_dying_cond3 and b.status_active=1 and b.is_deleted=0  group by  b.id order by b.id");
			 
			 
			
		}
		
	}
	//echo  $sql_redying;
	
   //and b.batch_against=2
    $redying_details_arr=array();
    foreach($sql_redying as $re_row)
    {
		if($batch_type==3 || $batch_type==1 || $batch_type==0)
		{
			$redying_details_arr[$re_row[csf("id")]]['id']=$re_row[csf("id")];
			$redying_details_arr[$re_row[csf("id")]]['batch_date']=$re_row[csf("batch_date")];
			$redying_details_arr[$re_row[csf("id")]]['extention_no']=$re_row[csf("extention_no")];
		}
		else
		{
			$redying_details_arr[$re_row[csf("re_dyeing_from")]]['id']=$re_row[csf("id")];
			$redying_details_arr[$re_row[csf("re_dyeing_from")]]['batch_date']=$re_row[csf("batch_date")];
			$redying_details_arr[$re_row[csf("re_dyeing_from")]]['extention_no']=$re_row[csf("extention_no")];
		}
    }
	
	//echo "<pre>"; print_r($redying_details_arr);die;
	
	if($batch_type==3)
	{
		$sql_result=sql_select("select a.result,a.batch_id from pro_fab_subprocess a, pro_batch_create_mst b where b.id=a.batch_id and a.entry_form in (35,38) and a.load_unload_id=2  and a.company_id=$cbo_company_name $re_dying_cond2 and a.status_active=1 and a.is_deleted=0 order by a.batch_id");	
	}
	else
	{
		$sql_result=sql_select("select a.result,a.batch_id from pro_fab_subprocess a, pro_batch_create_mst b where b.id=a.batch_id and a.entry_form in (35,38) and a.load_unload_id=2  and a.company_id=$cbo_company_name $re_dying_cond and a.status_active=1 and a.is_deleted=0 order by a.batch_id");	
	
	}
	
	/*$sql_result=sql_select("select a.result,a.batch_id from pro_fab_subprocess a, pro_batch_create_mst b where b.id=a.batch_id and a.entry_form in (35,38) and a.load_unload_id=2  and a.company_id=$cbo_company_name and b.re_dyeing_from in (".$batch_id_sql.") and a.status_active=1 and a.is_deleted=0");*/
	
	$result_arr=array();
	foreach($sql_result as $value)
	{
		$result_arr[$value[csf('batch_id')]]=$value[csf('result')];	
	}
	
    $sql_dyes_cost =sql_select("select a.batch_no,c.sub_process,b.item_category,sum(b.cons_amount) as dyes_chemical_cost
    from inv_issue_master a, inv_transaction b,dyes_chem_issue_dtls c
    where a.id=b.mst_id and a.id=c.mst_id and b.id=c.trans_id and b.transaction_type=2 and a.entry_form=5 and a.batch_no  is not null  and   b.item_category in (5,6,7)
    group by a.batch_no,b.item_category,c.sub_process  "); 
	
	$dyes_chemical_arr=array();
	foreach($sql_dyes_cost as $val)
	{
		$sub_process=$val[csf("sub_process")];
		//$batch_no=explode(",",$val[csf("batch_no")]);
		if($sub_process!=92)
		{
			$dyes_chemical_arr[$val[csf("batch_no")]][$val[csf("item_category")]]['chemical_cost']+=$val[csf("dyes_chemical_cost")];
		}
		else
		{
			$dyes_chemical_arr[$val[csf("batch_no")]][$val[csf("item_category")]]['chemical_cost_finish']+=$val[csf("dyes_chemical_cost")];
		}
	}
	 
	//*********************************************************************************************************************************************************
	 if($db_type==0)
	 {
		 $sql_dtls =sql_select("select batch_no from inv_issue_master  where  entry_form=5 and status_active=1 and is_deleted=0 and batch_no<>'' order by batch_no");
	 }
	 else
	 {
		 $sql_dtls =sql_select("select batch_no from inv_issue_master  where  entry_form=5 and status_active=1 and is_deleted=0 and batch_no is not null order by batch_no");
	 }
	  


//echo "select batch_no from inv_issue_master  where  entry_form=5 and status_active=1 and is_deleted=0 and batch_no is not null"; die;
	foreach($sql_dtls as $inf)
	{
		if(strpos($inf[csf("batch_no")], ",")==true)
		{
			$multi_batch_ids=explode(",",$inf[csf("batch_no")]);
			foreach($multi_batch_ids as $multi_batch)
			{
				$multi_batch_chk[$multi_batch]=$multi_batch;
			}
		}
	}
	
	$multi_single_batch=array();
	foreach($sql_dtls as $inf)
	{
		if(strpos($inf[csf("batch_no")], ",")==true)
		{
			$multi_batch_id=explode(",",$inf[csf("batch_no")]);
			$total_batch_id=count($multi_batch_id);
			if(str_replace("'","",$batch_id)!="" && str_replace("'","",$batch_no)!="") 
			{ 
				$chk_multi_cond=$total_batch_id;
			}
			else if(str_replace("'","",$batch_no)!="" && str_replace("'","",$batch_id)=="") 
			{ 
				$chk_multi_cond=$total_batch_id;
			}
			else  $chk_multi_cond=1;
			$batch_match=0;
			foreach($multi_batch_id as $m_batch)
			{ 
				if(in_array($m_batch,$batch_id_arr)) {$issue_multi_batch_temp_arr[]=$m_batch;  $batch_match+=$chk_multi_cond;  }
			}
			if($batch_match==$total_batch_id) 
			{ 
				//echo "A";
				$issue_multi_batch_arr[]=$inf[csf("batch_no")]; 
				$multi_single_batch=array_merge($multi_single_batch, $issue_multi_batch_temp_arr); 
			}
		}
		else
		{
			//echo $inf[csf("batch_no")].'X';
			if(in_array($inf[csf("batch_no")],$batch_id_arr) && $multi_batch_chk[$inf[csf("batch_no")]]=="") 
			{ 
				$issue_single_batch_arr[$inf[csf("batch_no")]]=$inf[csf("batch_no")];
			} 
		}
	}
	//print_r($issue_single_batch_arr); echo "Aziz";die;
	$i=1;
	ob_start();	
	?>
	<div id="" align="center" style="height:auto; width:auto; margin:0 auto; padding:0;">
        <table width="2230px" cellpadding="0" cellspacing="0" id="caption" align="center">
            <thead>
                <tr style="border:none;">
                    <td colspan="21" align="center" class="form_caption" style="border:none;font-size:16px; font-weight:bold" ><? echo $report_title; ?></td 
                ></tr>
                <tr style="border:none;">
                    <td colspan="21" class="form_caption" align="center" style="border:none; font-size:14px;">
                       <b>Company Name : <? echo $companyArr[$cbo_company_name]; ?></b>                               
                    </td>
                </tr>
                <tr style="border:none;">
                    <td colspan="21" align="center" class="form_caption" style="border:none;font-size:12px; font-weight:bold">
                        <? if($from_date!="" || $to_date!="") echo "From : ".change_date_format($from_date,'dd-mm-yyyy')." To : ".change_date_format($to_date,'dd-mm-yyyy')."" ;?>
                    </td>
                </tr>
            </thead>
        </table>
 <div>
 
         
        <div style=" float:left;font-size:25px">Single Batch </div>
        <table width="2230" border="1" cellpadding="0" cellspacing="0" rules="all" class="rpt_table" id="caption" >
            <thead>
           		 <tr>
                    <th colspan="22">
                        <?
                        echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --","4" );
                        ?> 
                    </th>
                </tr>
                
                <tr>
                    <th rowspan="2" width="40">SL</th>
                    <th rowspan="2" width="80">Date</th>
                    <th rowspan="2" width="100">Machine No</th>
                    <th rowspan="2" width="100">Batch No</th>
                    <th rowspan="2" width="80">Ext. No</th>
                    <th rowspan="2" width="100">Buyer</th>
					<th rowspan="2" width="100">Job no</th>
                    <th rowspan="2" width="100">Style</th>
				    <th rowspan="2" width="100">Booking No</th>
                    <th rowspan="2" width="120">FSO No</th>
                    <th rowspan="2" width="130">Fabric Desc </th>
                    
                    <th rowspan="2" width="100">Color Name</th>
                    <th rowspan="2" width="100">Color Range</th>
                   
                    <th rowspan="2" width="100">Batch Weight(Kg)</th>
                    <th colspan="4"  width="">Dyeing Cost</th>
                    <th colspan="2" width="">Finishing Cost Detail</th>
                    <th rowspan="2" width="100">Total Cost (Tk)</th>
                    <th rowspan="2" width="">Total Per Kg Cost (Tk)</th>
                   
                </tr> 
                <tr>                         
                    <th width="100">Tot Chem Cost (Tk)</th>
                    <th width="100">Tot Dyes Cost (Tk)</th>
                    <th width="100"><p>Tot Chem + Dyes Cost (Tk)</p></th>
                    <th width="100">Cost Per Kg (Tk)</th>
                   
                    <th width="100">Tot Finishing Cost</th>
                    <th width="100">Tot Finishing Cost Per Kg (Tk)</th>
                  
                </tr> 
            </thead>
        </table>
        <div style="width:2250px; max-height:400px;  overflow-y:scroll" id="scroll_body">
         <table width="2230" border="1" cellpadding="0" cellspacing="0" rules="all" class="rpt_table" id="table_body_id" >
            <tbody>
       <?
	    //echo "<pre>";
		//print_r($issue_single_batch_arr);die;
		$total_batch_chemical_price=$tot_re_dying_chemical_cost=$total_batch_weight=$tot_re_dying_dyes_chemical_cost=0;
		foreach(array_unique($issue_single_batch_arr) as $b_id)
		{
			
			if($i%2==0)$bgcolor="#E9F3FF";  else $bgcolor="#FFFFFF";
		
			$job_no_data=rtrim($po_number_arr[$batch_description_arr[$b_id]['sales_order_no']]['job'],',');
			$style_ref=rtrim($po_number_arr[$batch_description_arr[$b_id]['sales_order_no']]['style'],',');
			$color_range_id=$batch_description_arr[$b_id]['color_range'];
			?>
            <tr bgcolor="<? echo $bgcolor; ?>" <? echo $stylecolor; ?> onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                <td width="40"><? echo $i; ?></td>								
                <td width="80"><? echo change_date_format($batch_description_arr[$b_id]['batch_date']); ?></td>
                <td width="100"><p><? echo $machine_name_arr[$b_id]; ?></p></td>                                 
                <td width="100"><p><a href="##"  onClick="subprocess_fabric_dtls('<? echo $b_id; ?>','<? echo $batch_arr[$b_id]; ?>','subprocess_fabrics_dtls_popup')"><p><? echo $batch_arr[$b_id]; ?></p></a><? //echo $batch_arr[$b_id]; ?></p></td>
                <td width="80"><p><? echo $redying_details_arr[$b_id]['extention_no']; ?></p></td>
                <?
                if($batch_description_arr[$b_id][entry_form]==36)
                {
                ?>
				 <td width="100"><p><? echo implode(",",array_unique(explode(",",$sub_con_arr[$b_id][party_id]))); ?></p></td> 
				   <td width="100" align="center"><p>
                <? 
                     echo implode(",",array_unique(explode(",",$sub_con_arr[$b_id][subcon_job])));
                ?></p></td>
                <td width="100" align="center"><p> <? echo   implode(",",array_unique(explode(",",$sub_con_arr[$b_id][cust_style_ref]))); ?></p></td>
				 <td width="100"><p><? // echo $b_id; ?></p></td>
                <td width="120"><p><? //echo  $batch_description_arr[$b_id]['sales_order_no'];; ?></p></td> 
               
                <?
                }
                else
                {
                ?>
                <td width="100"><p><?
				$sales_order_no=$batch_description_arr[$b_id]['sales_order_no'];
				$within_group=$po_number_arr[$sales_order_no]['within_group'];
                if($batch_description_arr[$b_id]['without_order']==1)
				{ 
					$buyerName=$non_buyer_name_arr[$batch_description_arr[$b_id]['booking_no']];
				}
				else if($batch_description_arr[$b_id]['without_order']==0 && $within_group==1)
				{
					$po_buyer=$po_number_arr[$sales_order_no]['po_buyer'];
					$buyerName=$buyer_library[$po_buyer];
				}
				else if($within_group==2)
				{
					$buyer_id=$po_number_arr[$sales_order_no]['buyer_id'];
					$buyerName=$buyer_library[$buyer_id];
					//echo $within_group.'='.$po_buyer.'x'.$batch_description_arr[$b_id]['without_order'];
				}
				//else $buyerName=$buyer_name_arr[$batch_description_arr[$b_id]['booking_no']];
                echo $buyerName//$buyer_name_arr[$batch_description_arr[$b_id]['booking_no']]; ?></p></td> 
				  <td width="100" align="center"><p>
                <? 
                echo implode(",",array_unique(explode(",",$job_no_data)));
                ?></p></td>
                <td width="100" align="center"><p> 
                <? 
                   echo implode(",",array_unique(explode(",",$style_ref)));
				    ?></p></td>
				  <td width="100"><p><? echo $batch_description_arr[$b_id]['booking_no']; ?></p></td>
				 <td width="120"><p><? echo  $sales_order_no; ?></p></td> 
                <?	
                }
				$fabric_type=rtrim($fabric_type_arr[$b_id]['fabric_type'],',');
				$fabric_dess=implode(",",array_unique(explode(",",$fabric_type)));
                ?>
				 <td width="130" align="center"><p><? echo $fabric_dess; //$fabric_type_for_dyeing?></p></td> 
                <td width="100" align="center"><p><? echo $color_arr[$batch_description_arr[$b_id]['color_id']]; ?></p></td> 
                <td width="100" align="center"><p><? echo $color_range[$color_range_id]; ?></p></td> 
              
                <td width="100" align="right"><p>
                <? echo $batch_description_arr[$b_id]['batch_weight']; $total_batch_weight+=$batch_description_arr[$b_id]['batch_weight'] ;?>
                </p></td> 
                <?
                $first_chemi_cost=$first_dyeing_cost=0;
               // if($non_reding_batch_id[$b_id]!="")
                //{
					//echo $dyes_chemical_arr[$b_id][5]['chemical_cost'].'='.$dyes_chemical_arr[$b_id][6]['chemical_cost'].'<br>';
                    $first_chemi_cost=$dyes_chemical_arr[$b_id][5]['chemical_cost']+$dyes_chemical_arr[$b_id][7]['chemical_cost'];
                    $first_dyeing_cost=$dyes_chemical_arr[$b_id][6]['chemical_cost'];
               // }
                $chemical_cost_finish=$dyes_chemical_arr[$b_id][5]['chemical_cost_finish']+$dyes_chemical_arr[$b_id][7]['chemical_cost_finish'];
                ?>
                <td width="100" align="right"><p>
                <?
                 echo number_format($first_chemi_cost,4,".",""); 
                 $total_chemical_cost+=$first_chemi_cost;
                 ?>
                </p></td>
                <td width="100" align="right"><p>
                <?  
                    echo number_format($first_dyeing_cost,4,".",""); 
                    $total_dyes_cost+=$first_dyeing_cost; 
                ?>
                </p></td>
                <td width="100" align="right"><p><a href="##"  onClick="fn_1st_batch('<? echo $b_id; ?>','1st_batch_dtls_popup')">
                <?
                 $batch_chemical_price=$first_chemi_cost+$first_dyeing_cost;
                  echo number_format($batch_chemical_price,4,".","");
                  $total_batch_chemical_price+=$batch_chemical_price;
                ?>
                </a></p></td>
                <td width="100" align="right" title="Tot Chemical+Dyes Cost/Batch Weight"><p><? echo number_format($batch_chemical_price/$batch_description_arr[$b_id]['batch_weight'],4,".",""); $tot_total_receive+=$totalReceive; ?></p></td>
               
               
                <td width="100" align="right" title="Category=Chemical and Auxilary Chemicals"><p>
				<a href="##"  onClick="fn_1st_batch('<? echo $b_id; ?>','1st_batch_dtls_popup_finish')">
                <?
                 echo number_format($chemical_cost_finish,4,".",""); 
                 $tot_re_dying_chemical_cost+=$chemical_cost_finish;
                 ?>
                </a> </p></td>
                <td width="100" align="right" title="Tot Finishing Cost/Tot Batch Weight"><p>
				
				<? echo number_format($chemical_cost_finish/$batch_description_arr[$b_id]['batch_weight'],4,".","");  $tot_re_dying_dyes_cost+=$chemical_cost_finish/$batch_description_arr[$b_id]['batch_weight']; ?></p></td>
               
                <td width="100" align="right" title="Chemical Cost+Dyes Cost+Finishing Cost"><? echo number_format($chemical_cost_finish+$batch_chemical_price,4,".",""); ?></td>
                <td width="" align="right" title="Tot Chemical+Dyes+Finish Cost/Tot Batch Weight"><? echo number_format(($chemical_cost_finish+$batch_chemical_price)/$batch_description_arr[$b_id]['batch_weight'],4,".","");  ?></td>
                
            </tr>
			<? 												
				 $i++; 				
		}
		?>
            </tbody>
      </table>
       <table width="2230" border="1" cellpadding="0" cellspacing="0" rules="all" class="rpt_table" id="table_body_footer" >
             <tfoot>
            	<tr>
                	<th width="40">&nbsp;</th>
                	<th width="80">&nbsp;</th>
                    <th width="100">&nbsp;</th>
                	<th width="100">&nbsp;</th>
                    <th width="80">&nbsp;</th>
                	<th width="100">&nbsp;</th>
                	<th width="100">&nbsp;</th>
                	<th width="100">&nbsp;</th>
                	<th width="100">&nbsp;</th>
                    <th width="120">&nbsp;</th>
                  
                    <th width="130">&nbsp;</th>
                     <th width="100">&nbsp;</th>
                	<th width="100">Total</th>
                    <th width="100" id="value_total_batch_weight_single"><? echo number_format($total_batch_weight,4); ?></th>
                	<th width="100" id="value_total_chemical_cost_single"><? echo number_format($total_chemical_cost,4); ?></th>
                	<th width="100" id="value_total_dyeing_cost_single"><? echo number_format($total_dyes_cost,4); ?></th>
                    <th width="100" id="value_total_chemical_price_single"><? echo number_format($total_batch_chemical_price,4); ?></th>
                    <th width="100" id=""><? echo number_format($total_batch_chemical_price/$total_batch_weight,4); ?></th>
                   
                    <th width="100" id="value_total_redying_chemic_oost_single"><? echo number_format($tot_re_dying_chemical_cost,4); ?></th>
                    <th width="100" id="value_total_redying_dying_cost_single"><p><? echo number_format($tot_re_dying_dyes_cost,4); ?></p></th>
                  
                    <th width="100" id="value_grand_total_cost_single"><? echo number_format($total_batch_chemical_price+$tot_re_dying_dyes_chemical_cost,4); ?></th>
                    <th width=""><? //echo number_format($total_batch_chemical_price,4); ?></th>
                   
                </tr>
            </tfoot>
        </table>
      
        </div>
      </div>
      
       <div>
       <div  align="left" style="font-size:25px">Multiple Batch</div>
       
        <table width="2240" border="1" cellpadding="0" cellspacing="0" rules="all" class="rpt_table" id="caption" >
        	<thead>
             <tr>
                    <th colspan="22">
                        <?
                        echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --","4" );
                        ?> 
                    </th>
                </tr>
                <tr>
                    <th rowspan="2" width="40">SL</th>
                    <th rowspan="2" width="80">Date</th>
                    <th rowspan="2" width="100">Machine No</th>
                    <th rowspan="2" width="100">Batch No</th>
                    <th rowspan="2" width="80">Ext. No</th>
                    <th rowspan="2" width="80">Buyer</th>
                    <th rowspan="2" width="100">Job No</th>
                    <th rowspan="2" width="100">Style</th>
                   
				 	<th rowspan="2" width="100">Booking No</th>
					<th rowspan="2" width="110">FSO No</th>
					<th rowspan="2" width="130">Fabric Desc </th>
                    <th rowspan="2" width="100">Color Name</th>
                    <th rowspan="2" width="100">Color Range</th>
                   
                    <th rowspan="2" width="80">Batch Weight (Kg)</th>
                    <th colspan="4"  width="430">Dyeing  Cost</th>
                    <th colspan="2" width="200"> Finishing Cost Detail</th>
                    <th rowspan="2" width="70">Total Cost (Tk)</th>
                    <th rowspan="2" width="">Total Per Kg Cost (Tk)</th>
                    
                </tr> 
                <tr>                         
                    <th width="100">Tot Chem Cost (Tk)</th>
                    <th width="110">Tot Dyes Cost (Tk)</th>
                    <th width="110">Tot Chem + Dyes Cost (Tk)</th>
                    <th width="110">Cost Per Kg(Tk)</th>
                   
                    <th width="100">Finishing Cost</th>
                    <th width="100">Finishing Cost Per Kg (Tk)</th> 
                  
                </tr> 
            </thead>
        </table>
        <div style="width:2260px; max-height:400px; overflow-y:scroll" id="">
        <table width="2240" border="1" cellpadding="0" cellspacing="0" rules="all" class="rpt_table" id="table_body_multibatch_id">
       <?
		$multi_batch_qty_total=$total_multi_batch_chemical_cost=$total_multi_batch_chemical_cost_finish=0;
		$j=1;
		foreach(array_unique($issue_multi_batch_arr) as $b_id)
		{
			if($j%2==0)$bgcolor="#E9F3FF";  else $bgcolor="#FFFFFF"; 
			?>
            <tr bgcolor="<? echo $bgcolor; ?>" <? echo $stylecolor; ?> onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                <td width="40"><? echo $j; ?></td>								
                <td width="80" style="word-break:break-all;">
                <?
                 $multiple_batch_id=explode(",",$b_id);
                 $batch_id_all="";
                 $muli_batch_floor_name="";$multi_batch_machine='';
                 $multi_batch_buyer_name=array();
                 $multi_batch_booking_no=array();
                 $multi_batch_job_no=array();
                 $multi_batch_style_no=array();
                 $multi_batch_color=""; $multi_batch_color_range="";
                 $mulib_batch_weight=0;
                 $re_dying_dyes_multi_batch_cost=$mult_batch_dyes_cost_total=$mult_batch_dyes_cost_total_finish=0;
                 $re_dying_dyes_multi_batch_chemical_cost=0;
                 $re_multi_batch_floor="";
                 $mult_batch_chemical_cost=$mult_batch_chemical_cost=0;
                 $mult_batch_dyes_cost=$mult_batch_dyes_cost_finish=0;
                 $mult_batch_dyes_chemical_cost=0;
                 $multiBatch_extension="";
                 $re_floor="";
                 $p=0;
                 $redying_batch_id=array();
                 $po_number_data_all=array();
                 $batch_date_arr=array();
                 $re_dying_multi_batch_chemical_cost=$re_dying_multi_batch_chemical_cost_finish=0;
                 $re_dying_multi_batch_dying_cost=0; 
                 $re_dying_multi_batch_dyes_chemical_cost=0;
                 $remulti_batch_date="";$batch_nos="";$sales_order_no="";$sales_order_num="";
                 $new_redying_arr=array();
                 $new_redying_arr1=array();
                 foreach($multiple_batch_id as $s_bid)
                 {
                      
					  $p++;
                      $batch_date_arr[]=$batch_description_arr[$s_bid]['batch_date'];
                      if($batch_id_all!="") $batch_id_all.=",".$batch_arr[$s_bid]; else $batch_id_all=$batch_arr[$s_bid];
					  if($batch_nos=="") $batch_nos="<a href='##' onClick=\"subprocess_fabric_dtls('".$s_bid."','".$batch_arr[$s_bid]."','subprocess_fabrics_dtls_popup')\"> ".$batch_arr[$s_bid]." </a>";else $batch_nos.=", "."<a href='##' onClick=\"subprocess_fabric_dtls('".$s_bid."','".$batch_arr[$s_bid]."','subprocess_fabrics_dtls_popup')\"> ".$batch_arr[$s_bid]." </a>";
                     // if($muli_batch_floor_name!="") $muli_batch_floor_name.=",".$floor_arr[$s_bid]; else $muli_batch_floor_name=$floor_arr[$s_bid];
                      
                      // for buyer name job style po*************************************************************************************
                      if($batch_description_arr[$s_bid][entry_form]==36)
                      {
                          $multi_batch_buyer_name[]=$sub_con_arr[$s_bid][party_id];
                          $multi_batch_job_no.=$sub_con_arr[$s_bid][subcon_job].',';
                        //  $po_number_data_all[]=$sub_con_arr[$s_bid][order_no];
                          $multi_batch_style_no.=$sub_con_arr[$s_bid][cust_style_ref].',';
                      }
                      else
                      {
                         // $all_po_ids=array_unique(explode(",",$po_break_down_id_arr[$batch_description_arr[$s_bid]['booking_no']])); 
						  $job_nos_data=rtrim($po_number_arr[$batch_description_arr[$s_bid]['sales_order_no']]['job'],',');
						  $multi_batch_job_no=implode(",",array_unique(explode(",",$job_nos_data)));
						  
						  $styles_ref=rtrim($po_number_arr[$batch_description_arr[$s_bid]['sales_order_no']]['style'],',');
						 $multi_batch_style_no=implode(",",array_unique(explode(",",$styles_ref)));
						 
						 
					
                         
						  
                          $multi_batch_booking_no[]=$batch_description_arr[$s_bid]['booking_no'];
                          $fabric_types=rtrim($fabric_type_arr[$s_bid]['fabric_type'],',');
						  
                      }
					  $sales_order_no=$batch_description_arr[$s_bid]['sales_order_no'];
					  $within_group=$po_number_arr[$sales_order_no]['within_group'];
					   if($batch_description_arr[$s_bid]['without_order']==1)
						  { 
						 	$multi_batch_buyer_name[]=$non_buyer_name_arr[$batch_description_arr[$s_bid]['booking_no']];
						  }
						  else if($batch_description_arr[$s_bid]['without_order']==0 && $within_group==1)
						  { 
						   $po_buyer=$po_number_arr[$sales_order_no]['po_buyer'];
						    $multi_batch_buyer_name[]=$buyer_library[$po_buyer];
						  }
						  else if($within_group==2)
						  {
						  	$buyer_id=$po_number_arr[$sales_order_no]['buyer_id'];
						    $multi_batch_buyer_name[]=$buyer_library[$buyer_id];
						  }
						  
						$sales_order_num.=$sales_order_no.',';
					 
                      $mulib_batch_weight+=$batch_description_arr[$s_bid]['batch_weight'];
                      //****************************************** for color **********************************************************
                      if($multi_batch_color!="") $multi_batch_color.=",".$color_arr[$batch_description_arr[$s_bid]['color_id']];
                      else $multi_batch_color=$color_arr[$batch_description_arr[$s_bid]['color_id']];
					  
                      if($multi_batch_color_range!="") $multi_batch_color_range.=",".$color_range[$batch_description_arr[$s_bid]['color_range']];
                      else $multi_batch_color_range=$color_range[$batch_description_arr[$s_bid]['color_range']];
					  
					  if($multi_batch_machine!="") $multi_batch_machine.=",".$machine_name_arr[$s_bid];
                      else $multi_batch_machine=$machine_name_arr[$s_bid];
					 
					  //print_r($redying_details_arr[$s_bid]['id']);
				if($redying_details_arr[$s_bid]['id']!="")
				{
                      if($multiBatch_extension!="") $multiBatch_extension.="*".$redying_details_arr[$s_bid]['extention_no'];
                      else $multiBatch_extension=$redying_details_arr[$s_bid]['extention_no'];
                }
					
		}
                
				// echo $mult_batch_chemical_cost.'=='.$mult_batch_chemical_cost_finish;
				$tot_chme_fin_cost=$dyes_chemical_arr[$b_id][5]['chemical_cost_finish']+$dyes_chemical_arr[$b_id][7]['chemical_cost_finish'];
				 //  
				  // echo $mult_batch_chemical_cost.'XX';
				   if($tot_chme_fin_cost>0)
				   {
					 $mult_batch_chemical_cost_finish+=$tot_chme_fin_cost;
					 $fin_s_bid=$b_id;
				   }
                 
				 
				$mult_batch_chemical_cost=$dyes_chemical_arr[$b_id][5]['chemical_cost']+$dyes_chemical_arr[$b_id][7]['chemical_cost'];
                 $mult_batch_dyes_cost=$dyes_chemical_arr[$b_id][6]['chemical_cost']; 
				 $mult_batch_dyes_cost_finish=$dyes_chemical_arr[$b_id][6]['chemical_cost_finish']; 
                 $mult_batch_dyes_chemical_cost=$mult_batch_chemical_cost+$mult_batch_dyes_cost;
				 $mult_batch_dyes_chemical_cost_finish=$mult_batch_chemical_cost_finish+$mult_batch_dyes_cost_finish;
                 $multi_batch_qty_total+=$mulib_batch_weight;
                 $mult_batch_chemical_cost_total+=$mult_batch_chemical_cost;
				 $mult_batch_chemical_cost_total_finish+=$mult_batch_chemical_cost_finish;
                 $mult_batch_dyes_cost_total+=$mult_batch_dyes_cost;
				 $mult_batch_dyes_cost_total_finish+=$mult_batch_dyes_cost_finish;
                 $mult_batch_dyes_chemical_cost_total+=$mult_batch_dyes_chemical_cost;
                 
                 $multi_date="";
                 foreach(array_unique($batch_date_arr) as $m_date)
                 {
                 if($multi_date!="") $multi_date.=",".change_date_format($m_date); else $multi_date=change_date_format($m_date);
                 }
				$sales_order_num=rtrim($sales_order_num,',');
				$sales_order_nos=implode(",",array_unique(explode(",",$sales_order_num)));
				
				$multi_style_ref="";$multi_job_no="";
				 foreach($multi_batch_booking_no as $bookingNo)
				 {
				 	if($multi_style_ref!="") $multi_style_ref.=",".$multi_job_arr[$bookingNo]['style'];else $multi_style_ref=$multi_job_arr[$bookingNo]['style'];
					if($multi_job_no!="") $multi_job_no.=",".$multi_job_arr[$bookingNo]['job'];else $multi_job_no=$multi_job_arr[$bookingNo]['job'];
		 			//$multi_job_arr[$bookingNo]['job'];
				 }
				 
                ?><p><? echo $multi_date; ?></p></td>  
                 <td width="100"><p><? echo implode(",",array_unique(explode(",",$multi_batch_machine))); ?></p> </td>                               
                <td width="100" ><p> <?
					 echo $batch_nos;
					 ?>
                    </p> </td>
                <td width="80" align="center"><p><? echo $multiBatch_extension; ?></p></td>
             
                <td width="80" align="center"><p><? echo implode(",",array_unique($multi_batch_buyer_name)); ?></p></td>  
               
                <td width="100" align="center"><p><? echo $multi_job_no; ?></p></td> 
                <td width="100" align="center"><p><? echo $multi_style_ref; ?></p></td> 
				<td width="100" align="center"><p><? echo implode(",",array_unique($multi_batch_booking_no)); ?></p></td> 
				<td width="110" align="center"><p><? echo $sales_order_nos; ?></p></td> 
                <td width="130" align="center"><p><? 
				
				$fabric_dess=implode(",",array_unique(explode(",",$fabric_types)));
				
				echo  $fabric_dess;?></p></td>
               
			    <td width="100" align="center"><p><? echo implode(",",array_unique(explode(",",$multi_batch_color))); ?></p></td>
                <td width="100" align="center"><p><? echo implode(",",array_unique(explode(",",$multi_batch_color_range))); ?></p></td> 
                 
                <td width="80" align="right"><p><? echo number_format($mulib_batch_weight,2); ?></p></td>
                <td width="100" align="right"><p><? echo number_format($mult_batch_chemical_cost,4);  ?></p></td>
                <td width="110" align="right"><p><? echo number_format($mult_batch_dyes_cost,4);  ?></p></td>
                <td width="110" align="right"><p><a href="##"  onClick="fn_1st_batch('<? echo $b_id; ?>','1st_batch_dtls_popup_multi_chem_dye')"><? echo number_format($mult_batch_dyes_chemical_cost,4);  ?> </a></p></td>
                <td width="110" align="right"><p><? echo number_format($mult_batch_dyes_chemical_cost/$mulib_batch_weight,4); ?></p></td>
                
                <td width="100" align="right"><p> <a href="##"  onClick="fn_1st_batch('<? echo $fin_s_bid; ?>','1st_batch_dtls_popup_finish_multi_chem_auxilary')">
                <? echo number_format($mult_batch_chemical_cost_finish,4); ?></a></p>
                </td>
                <td width="100" align="right" title="Finish Cost/Batch weight"><p><? echo number_format($mult_batch_chemical_cost_finish/$mulib_batch_weight,4); ?></p></td>
               
                <td width="70" align="right" title="Tot Chemical+Dye Cost+Finish Cost"><? echo  number_format($mult_batch_chemical_cost_finish+$mult_batch_dyes_chemical_cost,4);  ?></td>
                <td width="" align="right" title="Tot Chemical+Dye Cost+Finish Cost/Batch Weight"><? echo number_format(($mult_batch_chemical_cost_finish+$mult_batch_dyes_chemical_cost)/$mulib_batch_weight,4);; ?></td>
              
            </tr>
			<? 
			unset($new_redying_arr);
			unset($new_redying_arr1);
															
			$j++;
			$total_multi_batch_chemical_cost+=$mult_batch_chemical_cost; 
			$total_multi_batch_chemical_cost_finish+=$mult_batch_chemical_cost_finish; 				
		}
		?>
        </table>
        <table width="2240" border="1" cellpadding="0" cellspacing="0" rules="all" class="rpt_table" id="table_body_footer">
	         <tfoot>
            	<tr>
                	<th width="40">&nbsp;</th>
                	<th width="80">&nbsp;</th>
                    <th width="100">&nbsp;</th>
                	<th width="100">&nbsp;</th>
                    <th width="80">&nbsp;</th>
                    <th width="80">&nbsp;</th>
                	
                	<th width="100">&nbsp;</th>
                    <th width="100">&nbsp;</th>
                    <th width="100">&nbsp;</th>
					 <th width="110">&nbsp;</th>
                	<th width="130">&nbsp;</th>
                	<th width="100">&nbsp;</th>
                    <th width="100">&nbsp;</th>
                    <th width="80" id="value_total_batch_weight_multiple"><? echo number_format($multi_batch_qty_total,4); ?></th>
                    <th width="100" id="value_total_chemical_cost_multiple"><? echo number_format($total_multi_batch_chemical_cost,4); ?></th>
                	<th width="110" id="value_total_dyeing_cost_multiple"><? echo number_format($mult_batch_chemical_cost_total,4); ?></th>
                	<th width="110" id="value_total_chemical_price_multiple"><? echo number_format($mult_batch_dyes_cost_total,4); ?></th>
                    <th width="110" id=""><? echo number_format($mult_batch_dyes_chemical_cost_total/$multi_batch_qty_total,4); ?></th>
                    <th width="100" id="value_total_chemical_price_multiple_finish"><? echo number_format($total_multi_batch_chemical_cost_finish,4); ?></th>
                    
                    <th width="100" id=""><? echo number_format($total_multi_batch_chemical_cost_finish/$multi_batch_qty_total,4); ?></th>
                   
                    <th width="70" id="value_grand_total_cost_multiple"><? echo number_format($mult_batch_dyes_chemical_cost_total+$re_dying_multi_batch_dyes_chemical_cost_total,4); ?></th>
                    <th width="" id=""><? echo number_format(($mult_batch_dyes_chemical_cost_total+$re_dying_multi_batch_dyes_chemical_cost_total)/$multi_batch_qty_total,4); ?></th>
                   
                </tr>
            </tfoot>
        </table>
        </div>
      </div>
  </div>
    <?
    $html = ob_get_contents();
    ob_clean();
    //$new_link=create_delete_report_file( $html, 2, $delete, "../../../" );
    foreach (glob("*.xls") as $filename) {
    //if( @filemtime($filename) < (time()-$seconds_old) )
    @unlink($filename);
    }
    //---------end------------//
    $name=time();
    $filename=$user_id."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');	
    $is_created = fwrite($create_new_doc, $html);
    echo "$html**$filename"; 
    exit();

}


if($action=="1st_batch_dtls_popup")  
{
	echo load_html_head_contents("Batch Details", "../../../../", 1, 1,$unicode,'','');
	extract($_REQUEST);
	//echo $batch_id;die;
	$batch_non_redyeing_id=return_field_value("id","pro_batch_create_mst","status_active=1 and id in($batch_id)","id");
	if($batch_non_redyeing_id=="")die;
	
	//if($batch_non_redyeing_id!="") 
	$batch_cond=" and a.batch_no like '$batch_non_redyeing_id'";
	
	 $sql_dtls_dyes = "select c.id as prod_id, c.product_name_details, c.unit_of_measure, c.avg_rate_per_unit, sum(b.cons_amount) as cons_amount, sum(b.cons_quantity) as qnty
	from inv_issue_master a,inv_transaction b, product_details_master c,dyes_chem_issue_dtls d
	where a.id=b.mst_id and a.id=d.mst_id  and b.prod_id=c.id and d.product_id=c.id and d.trans_id=b.id and b.transaction_type=2 and d.sub_process!=92  and a.entry_form=5  and b.item_category in (6) and b.status_active=1  and a.batch_no  is not null $batch_cond group by c.id, c.product_name_details, c.unit_of_measure, c.avg_rate_per_unit"; 
	//echo $sql_dtls_dyes;die;
	$sql_result_dyes= sql_select($sql_dtls_dyes);
	
	/*echo   $sql_dtls_chemical ="select a.batch_no,c.sub_process,b.item_category,sum(b.cons_amount) as cons_amount
    from inv_issue_master a, inv_transaction b,dyes_chem_issue_dtls c
    where a.id=b.mst_id and a.id=c.mst_id and b.id=c.trans_id and b.transaction_type=2 and a.entry_form=5 and a.batch_no  is not null  and   b.item_category in (5,7) $batch_cond 
    group by a.batch_no,b.item_category,c.sub_process  "; */
	
	$sql_dtls_chemical = "select c.id as prod_id, c.product_name_details, c.unit_of_measure, c.avg_rate_per_unit, sum(b.cons_amount) as cons_amount, sum(b.cons_quantity) as qnty
	from inv_issue_master a,inv_transaction b, product_details_master c,dyes_chem_issue_dtls d
	where a.id=b.mst_id and b.id=d.trans_id  and a.id=d.mst_id and d.product_id=c.id and b.prod_id=d.product_id and d.sub_process!=92 and b.transaction_type=2 and b.item_category in (5,7) and a.entry_form=5  and b.status_active=1 and a.batch_no  is not null $batch_cond group by c.id, c.product_name_details, c.unit_of_measure, c.avg_rate_per_unit";
	//echo $sql_dtls_chemical;
	$sql_result_chemical= sql_select($sql_dtls_chemical);
	?>
    <script>
		function print_window()
		{
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTsD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
			'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_div').innerHTML+'</body</html>');
			d.close();
		}	
	</script>	
	<div style="width:770px; margin-left:30px;font-family:'Arial Narrow'; font-size:14px;" id="report_div">
      <div style="width:770px;" align="center">
	 <input  type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>
	 &nbsp;
     <div id="report_container"> </div></div>
       <?
         ob_start();
		?>
    
    <table align="center" cellspacing="0" width="770" border="1" rules="all" class="rpt_table" >
	<caption> <b style="float:left;"> Dyes</b></caption>
        <thead align="center">
    	   <tr>
                <th width="50">SL</th>
                <th width="50">Product Id</th>
                <th width="250">Item Description</th>
                <th width="100">UOM</th>
                <th width="100">Quantity</th>
                <th width="100">Avg. Rate</th> 
                <th>Amount(BDT)</th>
           </tr>
		</thead>
		<?  
		$i=1;
		foreach($sql_result_dyes as $row)
		{
			if ($i%2==0)  
				$bgcolor="#E9F3FF";
			else
				$bgcolor="#FFFFFF";
			?>
			<tbody>
				<tr bgcolor="<? echo $bgcolor; ?>">
					<td align="center"><? echo $i; ?></td>
					<td align="center"><? echo $row[csf("prod_id")]; ?></td>
					<td><? echo $row[csf("product_name_details")]; ?></td>
					<td align="center"><? echo $unit_of_measurement[$row[csf("unit_of_measure")]]; ?></td>
					<td align="right"><? echo number_format($row[csf("qnty")],4,'.',''); $total_dyeing_qnty+=$row[csf("qnty")]; ?></td>
					<td align="right"><? echo number_format($row[csf("avg_rate_per_unit")],4,'.',''); ?></td>
					<td align="right"><? $amount=0; $amount=$row[csf("cons_amount")]; echo  number_format($amount,6,'.',''); $total_dyeing_amount+=$amount; ?></td>
				</tr>
			</tbody>
			<? 
			$i++;
		}
		?>
        <tfoot>
        	<tr>
                <th colspan="4" align="right">Total:</th>
                <th align="right"><? echo number_format($total_dyeing_qnty,2,'.',''); ?></th>
                <th></th>
                <th align="right"><? echo number_format($total_dyeing_amount,2,'.',''); ?></th>
            </tr>
        </tfoot>
      </table>
      
     
    <table align="center" cellspacing="0" width="770" border="1" rules="all" class="rpt_table" >
	<caption> <b style="float:left;"> Chemical</b></caption>
        <thead align="center">
    	   <tr>
                <th width="50">SL</th>
                <th width="50">Product Id</th>
                <th width="250">Item Description</th>
                <th width="100">UOM</th>
                <th width="100">Quantity</th>
                <th width="100">Avg. Rate</th> 
                <th>Amount(BDT)</th>
           </tr>
		</thead>
		<?  
		foreach($sql_result_chemical as $row)
		{
			if ($i%2==0)  
				$bgcolor="#E9F3FF";
			else
				$bgcolor="#FFFFFF";
			?>
			<tbody>
				<tr bgcolor="<? echo $bgcolor; ?>">
					<td align="center"><? echo $i; ?></td>
					<td align="center"><? echo $row[csf("prod_id")]; ?></td>
					<td><? echo $row[csf("product_name_details")]; ?></td>
					<td align="center"><? echo $unit_of_measurement[$row[csf("unit_of_measure")]]; ?></td>
					<td align="right"><? echo number_format($row[csf("qnty")],4,'.',''); $total_chemical_qnty+=$row[csf("qnty")]; ?></td>
					<td align="right"><? echo number_format($row[csf("avg_rate_per_unit")],4,'.',''); ?></td>
					<td align="right"><? $amount=0; $amount=$row[csf("cons_amount")]; echo  number_format($amount,6,'.',''); $total_chemical_amount+=$amount; ?></td>
				</tr>
			</tbody>
			<? 
			$i++;
		}
		?>
        <tfoot>
        	<tr>
                <th colspan="4" align="right">Total:</th>
                <th align="right"><? echo number_format($total_chemical_qnty,2,'.',''); ?></th>
                <th></th>
                <th align="right"><? echo number_format($total_chemical_amount,2,'.',''); ?></th>
            </tr>
        </tfoot>
      </table>
    </div>
    <? 
	   
	$html=ob_get_contents();
	ob_flush();
	
	foreach (glob(""."*.xls") as $filename) 
	{
	   @unlink($filename);
	}
	
	//html to xls convert
	$name=time();
	$name=$user_id."_".$name.".xls";
	$create_new_excel = fopen(''.$name, 'w');	
	$is_created = fwrite($create_new_excel,$html);
	
	?>
      <input type="hidden" id="txt_excl_link" value="<? echo 'requires/'.$name; ?>" />
    <script>
	$(document).ready(function(e) {
				document.getElementById('report_container').innerHTML='<a href="<? echo $name?>" style="text-decoration:none"><input type="button" value="Excel Download" name="excel" id="excel" class="formbutton" style="width:90px;font-size:11px"/></a>&nbsp;&nbsp;';
		});	
	</script>
      </div> 
	  <?         
	exit();
}
if($action=="1st_batch_dtls_popup_multi_chem_dye")
{
	echo load_html_head_contents("Batch Details-Chemical Dyes", "../../../../", 1, 1,$unicode,'','');
	extract($_REQUEST);
	//echo $batch_id;die;
	//if($batch_non_redyeing_id!="") 
	if($batch_id!="") $batch_cond=" and a.batch_no like '$batch_id'";
	
	$sql_dtls_dyes = "select c.id as prod_id, c.product_name_details, c.unit_of_measure, c.avg_rate_per_unit, sum(b.cons_amount) as cons_amount, sum(b.cons_quantity) as qnty
	from inv_issue_master a,inv_transaction b, product_details_master c,dyes_chem_issue_dtls d
	where a.id=b.mst_id and a.id=d.mst_id  and b.prod_id=c.id and d.product_id=c.id and d.trans_id=b.id and b.transaction_type=2 and d.sub_process!=92  and a.entry_form=5  and b.item_category in (6) and b.status_active=1  and a.batch_no  is not null $batch_cond group by c.id, c.product_name_details, c.unit_of_measure, c.avg_rate_per_unit"; 
	//echo $sql_dtls_dyes;die;
	$sql_result_dyes= sql_select($sql_dtls_dyes);
	
	/*echo   $sql_dtls_chemical ="select a.batch_no,c.sub_process,b.item_category,sum(b.cons_amount) as cons_amount
    from inv_issue_master a, inv_transaction b,dyes_chem_issue_dtls c
    where a.id=b.mst_id and a.id=c.mst_id and b.id=c.trans_id and b.transaction_type=2 and a.entry_form=5 and a.batch_no  is not null  and   b.item_category in (5,7) $batch_cond 
    group by a.batch_no,b.item_category,c.sub_process  "; */
	
	$sql_dtls_chemical = "select c.id as prod_id, c.product_name_details, c.unit_of_measure, c.avg_rate_per_unit, sum(b.cons_amount) as cons_amount, sum(b.cons_quantity) as qnty
	from inv_issue_master a,inv_transaction b, product_details_master c,dyes_chem_issue_dtls d
	where a.id=b.mst_id and b.id=d.trans_id  and a.id=d.mst_id and d.product_id=c.id and b.prod_id=d.product_id and d.sub_process!=92 and b.transaction_type=2 and b.item_category in (5,7) and a.entry_form=5  and b.status_active=1 and a.batch_no  is not null $batch_cond group by c.id, c.product_name_details, c.unit_of_measure, c.avg_rate_per_unit";
	//echo $sql_dtls_chemical;
	$sql_result_chemical= sql_select($sql_dtls_chemical);
	?>
    <script>
		function print_window()
		{
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTsD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
			'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_div').innerHTML+'</body</html>');
			d.close();
		}	
	</script>	
	<div style="width:770px; margin-left:30px;font-family:'Arial Narrow'; font-size:14px;" id="report_div">
      <div style="width:770px;" align="center">
	 <input  type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>
	 &nbsp;
     <div id="report_container"> </div></div>
       <?
         ob_start();
		?>
    
    <table align="center" cellspacing="0" width="770" border="1" rules="all" class="rpt_table" >
	<caption> <b style="float:left;"> Dyes</b></caption>
        <thead align="center">
    	   <tr>
                <th width="50">SL</th>
                <th width="50">Product Id</th>
                <th width="250">Item Description</th>
                <th width="100">UOM</th>
                <th width="100">Quantity</th>
                <th width="100">Avg. Rate</th> 
                <th>Amount(BDT)</th>
           </tr>
		</thead>
		<?  
		$i=1;
		foreach($sql_result_dyes as $row)
		{
			if ($i%2==0)  
				$bgcolor="#E9F3FF";
			else
				$bgcolor="#FFFFFF";
			?>
			<tbody>
				<tr bgcolor="<? echo $bgcolor; ?>">
					<td align="center"><? echo $i; ?></td>
					<td align="center"><? echo $row[csf("prod_id")]; ?></td>
					<td><? echo $row[csf("product_name_details")]; ?></td>
					<td align="center"><? echo $unit_of_measurement[$row[csf("unit_of_measure")]]; ?></td>
					<td align="right"><? echo number_format($row[csf("qnty")],4,'.',''); $total_dyeing_qnty+=$row[csf("qnty")]; ?></td>
					<td align="right"><? echo number_format($row[csf("avg_rate_per_unit")],4,'.',''); ?></td>
					<td align="right"><? $amount=0; $amount=$row[csf("cons_amount")]; echo  number_format($amount,6,'.',''); $total_dyeing_amount+=$amount; ?></td>
				</tr>
			</tbody>
			<? 
			$i++;
		}
		?>
        <tfoot>
        	<tr>
                <th colspan="4" align="right">Total:</th>
                <th align="right"><? echo number_format($total_dyeing_qnty,2,'.',''); ?></th>
                <th></th>
                <th align="right"><? echo number_format($total_dyeing_amount,2,'.',''); ?></th>
            </tr>
        </tfoot>
      </table>
      
     
    <table align="center" cellspacing="0" width="770" border="1" rules="all" class="rpt_table" >
	<caption> <b style="float:left;"> Chemical</b></caption>
        <thead align="center">
    	   <tr>
                <th width="50">SL</th>
                <th width="50">Product Id</th>
                <th width="250">Item Description</th>
                <th width="100">UOM</th>
                <th width="100">Quantity</th>
                <th width="100">Avg. Rate</th> 
                <th>Amount(BDT)</th>
           </tr>
		</thead>
		<?  
		foreach($sql_result_chemical as $row)
		{
			if ($i%2==0)  
				$bgcolor="#E9F3FF";
			else
				$bgcolor="#FFFFFF";
			?>
			<tbody>
				<tr bgcolor="<? echo $bgcolor; ?>">
					<td align="center"><? echo $i; ?></td>
					<td align="center"><? echo $row[csf("prod_id")]; ?></td>
					<td><? echo $row[csf("product_name_details")]; ?></td>
					<td align="center"><? echo $unit_of_measurement[$row[csf("unit_of_measure")]]; ?></td>
					<td align="right"><? echo number_format($row[csf("qnty")],4,'.',''); $total_chemical_qnty+=$row[csf("qnty")]; ?></td>
					<td align="right"><? echo number_format($row[csf("avg_rate_per_unit")],4,'.',''); ?></td>
					<td align="right"><? $amount=0; $amount=$row[csf("cons_amount")]; echo  number_format($amount,6,'.',''); $total_chemical_amount+=$amount; ?></td>
				</tr>
			</tbody>
			<? 
			$i++;
		}
		?>
        <tfoot>
        	<tr>
                <th colspan="4" align="right">Total:</th>
                <th align="right"><? echo number_format($total_chemical_qnty,2,'.',''); ?></th>
                <th></th>
                <th align="right"><? echo number_format($total_chemical_amount,2,'.',''); ?></th>
            </tr>
        </tfoot>
      </table>
    </div>
    <? 
	   
	$html=ob_get_contents();
	ob_flush();
	
	foreach (glob(""."*.xls") as $filename) 
	{
	   @unlink($filename);
	}
	
	//html to xls convert
	$name=time();
	$name=$user_id."_".$name.".xls";
	$create_new_excel = fopen(''.$name, 'w');	
	$is_created = fwrite($create_new_excel,$html);
	
	?>
      <input type="hidden" id="txt_excl_link" value="<? echo 'requires/'.$name; ?>" />
    <script>
	$(document).ready(function(e) {
				document.getElementById('report_container').innerHTML='<a href="<? echo $name?>" style="text-decoration:none"><input type="button" value="Excel Download" name="excel" id="excel" class="formbutton" style="width:90px;font-size:11px"/></a>&nbsp;&nbsp;';
		});	
	</script>
      </div> 
	  <?         
	exit();
}
if($action=="1st_batch_dtls_popup_finish")
{
	echo load_html_head_contents("Batch Details", "../../../../", 1, 1,$unicode,'','');
	extract($_REQUEST);
	//echo $batch_id;die;
	$batch_non_redyeing_id=return_field_value("id","pro_batch_create_mst","status_active=1 and id in($batch_id)","id");
	if($batch_non_redyeing_id=="")die;
	
	//if($batch_non_redyeing_id!="") 
	$batch_cond=" and a.batch_no like '$batch_non_redyeing_id'";
	
	 $sql_dtls_dyes = "select c.id as prod_id, c.product_name_details, c.unit_of_measure, c.avg_rate_per_unit, sum(b.cons_amount) as cons_amount, sum(b.cons_quantity) as qnty
	from inv_issue_master a,inv_transaction b, product_details_master c,dyes_chem_issue_dtls d
	where a.id=b.mst_id and a.id=d.mst_id  and b.prod_id=c.id and d.product_id=c.id and d.trans_id=b.id and b.transaction_type=2 and d.sub_process=92  and a.entry_form=5  and b.item_category in (6) and b.status_active=1  and a.batch_no  is not null $batch_cond group by c.id, c.product_name_details, c.unit_of_measure, c.avg_rate_per_unit"; 
	//echo $sql_dtls_dyes;die;
	$sql_result_dyes= sql_select($sql_dtls_dyes);
	
	/*echo   $sql_dtls_chemical ="select a.batch_no,c.sub_process,b.item_category,sum(b.cons_amount) as cons_amount
    from inv_issue_master a, inv_transaction b,dyes_chem_issue_dtls c
    where a.id=b.mst_id and a.id=c.mst_id and b.id=c.trans_id and b.transaction_type=2 and a.entry_form=5 and a.batch_no  is not null  and   b.item_category in (5,7) $batch_cond 
    group by a.batch_no,b.item_category,c.sub_process  "; */
	
	$sql_dtls_chemical = "select c.id as prod_id, c.product_name_details, c.unit_of_measure, c.avg_rate_per_unit, sum(b.cons_amount) as cons_amount, sum(b.cons_quantity) as qnty
	from inv_issue_master a,inv_transaction b, product_details_master c,dyes_chem_issue_dtls d
	where a.id=b.mst_id and b.id=d.trans_id  and a.id=d.mst_id and d.product_id=c.id and b.prod_id=d.product_id and d.sub_process=92 and b.transaction_type=2 and b.item_category in (5,7) and a.entry_form=5  and b.status_active=1 and a.batch_no  is not null $batch_cond group by c.id, c.product_name_details, c.unit_of_measure, c.avg_rate_per_unit";
	//echo $sql_dtls_chemical;
	$sql_result_chemical= sql_select($sql_dtls_chemical);
	?>
    <script>
		function print_window()
		{
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTsD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
			'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_div').innerHTML+'</body</html>');
			d.close();
		}	
	</script>	
	<div style="width:770px; margin-left:30px;font-family:'Arial Narrow'; font-size:14px;" id="report_div">
     <div style="width:770px;" align="center">
	 <input  type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>
	 &nbsp;
     <div id="report_container"> </div></div>
       <?
         ob_start();
		?>
      
    <table align="center" cellspacing="0" width="770" border="1" rules="all" class="rpt_table" >
	<caption> <b style="float:left;"> Chemical and Auxilary Chemicals</b></caption>
        <thead align="center">
    	   <tr>
                <th width="50">SL</th>
                <th width="50">Product Id</th>
                <th width="250">Item Description</th>
                <th width="100">UOM</th>
                <th width="100">Quantity</th>
                <th width="100">Avg. Rate</th> 
                <th>Amount(BDT)</th>
           </tr>
		</thead>
		<?  
		$i=1;
		foreach($sql_result_chemical as $row)
		{
			if ($i%2==0)  
				$bgcolor="#E9F3FF";
			else
				$bgcolor="#FFFFFF";
			?>
			<tbody>
				<tr bgcolor="<? echo $bgcolor; ?>">
					<td align="center"><? echo $i; ?></td>
					<td align="center"><? echo $row[csf("prod_id")]; ?></td>
					<td><? echo $row[csf("product_name_details")]; ?></td>
					<td align="center"><? echo $unit_of_measurement[$row[csf("unit_of_measure")]]; ?></td>
					<td align="right"><? echo number_format($row[csf("qnty")],4,'.',''); $total_chemical_qnty+=$row[csf("qnty")]; ?></td>
					<td align="right"><? echo number_format($row[csf("avg_rate_per_unit")],4,'.',''); ?></td>
					<td align="right"><? $amount=0; $amount=$row[csf("cons_amount")]; echo  number_format($amount,6,'.',''); $total_chemical_amount+=$amount; ?></td>
				</tr>
			</tbody>
			<? 
			$i++;
		}
		?>
        <tfoot>
        	<tr>
                <th colspan="4" align="right">Total:</th>
                <th align="right"><? echo number_format($total_chemical_qnty,2,'.',''); ?></th>
                <th></th>
                <th align="right"><? echo number_format($total_chemical_amount,2,'.',''); ?></th>
            </tr>
        </tfoot>
      </table>
    </div>
    <?   
	$html=ob_get_contents();
	ob_flush();
	
	foreach (glob(""."*.xls") as $filename) 
	{
	   @unlink($filename);
	}
	
	//html to xls convert
	$name=time();
	$name=$user_id."_".$name.".xls";
	$create_new_excel = fopen(''.$name, 'w');	
	$is_created = fwrite($create_new_excel,$html);
	
	?>
      <input type="hidden" id="txt_excl_link" value="<? echo 'requires/'.$name; ?>" />
    <script>
	$(document).ready(function(e) {
				document.getElementById('report_container').innerHTML='<a href="<? echo $name?>" style="text-decoration:none"><input type="button" value="Excel Download" name="excel" id="excel" class="formbutton" style="width:90px;font-size:11px"/></a>&nbsp;&nbsp;';
		});	
	</script>
      </div> 
	  <?      
	exit();
}
if($action=="1st_batch_dtls_popup_finish_multi_chem_auxilary")
{
	echo load_html_head_contents("Batch Details-Chemical Auxilary", "../../../../", 1, 1,$unicode,'','');
	extract($_REQUEST);
	//echo $batch_id;die;
//	$batch_non_redyeing_id=return_field_value("id","pro_batch_create_mst","status_active=1 and id in($batch_id)","id");
	//if($batch_non_redyeing_id=="")die;
	
	//if($batch_non_redyeing_id!="") 
	$batch_cond=" and a.batch_no like '$batch_id'";
	
	 $sql_dtls_dyes = "select c.id as prod_id, c.product_name_details, c.unit_of_measure, c.avg_rate_per_unit, sum(b.cons_amount) as cons_amount, sum(b.cons_quantity) as qnty
	from inv_issue_master a,inv_transaction b, product_details_master c,dyes_chem_issue_dtls d
	where a.id=b.mst_id and a.id=d.mst_id  and b.prod_id=c.id and d.product_id=c.id and d.trans_id=b.id and b.transaction_type=2 and d.sub_process=92  and a.entry_form=5  and b.item_category in (6) and b.status_active=1  and a.batch_no  is not null $batch_cond group by c.id, c.product_name_details, c.unit_of_measure, c.avg_rate_per_unit"; 
	//echo $sql_dtls_dyes;die;
	$sql_result_dyes= sql_select($sql_dtls_dyes);
	
	/*echo   $sql_dtls_chemical ="select a.batch_no,c.sub_process,b.item_category,sum(b.cons_amount) as cons_amount
    from inv_issue_master a, inv_transaction b,dyes_chem_issue_dtls c
    where a.id=b.mst_id and a.id=c.mst_id and b.id=c.trans_id and b.transaction_type=2 and a.entry_form=5 and a.batch_no  is not null  and   b.item_category in (5,7) $batch_cond 
    group by a.batch_no,b.item_category,c.sub_process  "; */
	
	$sql_dtls_chemical = "select c.id as prod_id, c.product_name_details, c.unit_of_measure, c.avg_rate_per_unit, sum(b.cons_amount) as cons_amount, sum(b.cons_quantity) as qnty
	from inv_issue_master a,inv_transaction b, product_details_master c,dyes_chem_issue_dtls d
	where a.id=b.mst_id and b.id=d.trans_id  and a.id=d.mst_id and d.product_id=c.id and b.prod_id=d.product_id and d.sub_process=92 and b.transaction_type=2 and b.item_category in (5,7) and a.entry_form=5  and b.status_active=1 and a.batch_no  is not null $batch_cond group by c.id, c.product_name_details, c.unit_of_measure, c.avg_rate_per_unit";
	//echo $sql_dtls_chemical;
	$sql_result_chemical= sql_select($sql_dtls_chemical);
	?>
    <script>
		function print_window()
		{
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTsD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
			'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_div').innerHTML+'</body</html>');
			d.close();
		}	
	</script>	
	<div style="width:770px; margin-left:30px;font-family:'Arial Narrow'; font-size:14px;" id="report_div">
     <div style="width:770px;" align="center">
	 <input  type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>
	 &nbsp;
     <div id="report_container"> </div></div>
       <?
         ob_start();
		?>
      
    <table align="center" cellspacing="0" width="770" border="1" rules="all" class="rpt_table" >
	<caption> <b style="float:left;"> Chemical and Auxilary Chemicals</b></caption>
        <thead align="center">
    	   <tr>
                <th width="50">SL</th>
                <th width="50">Product Id</th>
                <th width="250">Item Description</th>
                <th width="100">UOM</th>
                <th width="100">Quantity</th>
                <th width="100">Avg. Rate</th> 
                <th>Amount(BDT)</th>
           </tr>
		</thead>
		<?  
		$i=1;
		foreach($sql_result_chemical as $row)
		{
			if ($i%2==0)  
				$bgcolor="#E9F3FF";
			else
				$bgcolor="#FFFFFF";
			?>
			<tbody>
				<tr bgcolor="<? echo $bgcolor; ?>">
					<td align="center"><? echo $i; ?></td>
					<td align="center"><? echo $row[csf("prod_id")]; ?></td>
					<td><? echo $row[csf("product_name_details")]; ?></td>
					<td align="center"><? echo $unit_of_measurement[$row[csf("unit_of_measure")]]; ?></td>
					<td align="right"><? echo number_format($row[csf("qnty")],4,'.',''); $total_chemical_qnty+=$row[csf("qnty")]; ?></td>
					<td align="right"><? echo number_format($row[csf("avg_rate_per_unit")],4,'.',''); ?></td>
					<td align="right"><? $amount=0; $amount=$row[csf("cons_amount")]; echo  number_format($amount,6,'.',''); $total_chemical_amount+=$amount; ?></td>
				</tr>
			</tbody>
			<? 
			$i++;
		}
		?>
        <tfoot>
        	<tr>
                <th colspan="4" align="right">Total:</th>
                <th align="right"><? echo number_format($total_chemical_qnty,2,'.',''); ?></th>
                <th></th>
                <th align="right"><? echo number_format($total_chemical_amount,2,'.',''); ?></th>
            </tr>
        </tfoot>
      </table>
    </div>
    <?   
	$html=ob_get_contents();
	ob_flush();
	
	foreach (glob(""."*.xls") as $filename) 
	{
	   @unlink($filename);
	}
	
	//html to xls convert
	$name=time();
	$name=$user_id."_".$name.".xls";
	$create_new_excel = fopen(''.$name, 'w');	
	$is_created = fwrite($create_new_excel,$html);
	
	?>
      <input type="hidden" id="txt_excl_link" value="<? echo 'requires/'.$name; ?>" />
    <script>
	$(document).ready(function(e) {
				document.getElementById('report_container').innerHTML='<a href="<? echo $name?>" style="text-decoration:none"><input type="button" value="Excel Download" name="excel" id="excel" class="formbutton" style="width:90px;font-size:11px"/></a>&nbsp;&nbsp;';
		});	
	</script>
      </div> 
	  <?      
	exit();
}
if($action=="total_batch_dtls_popup")
{
	echo load_html_head_contents("Batch Details", "../../../../", 1, 1,$unicode,'','');
	extract($_REQUEST);
	//echo $batch_id;die;
	$batch_non_redyeing_id=return_field_value("id","pro_batch_create_mst","batch_against<>2 and status_active=1 and id in($batch_id)","id");
	$batch_redyeing_id=return_field_value("id","pro_batch_create_mst","batch_against=2 and status_active=1 and id in($batch_id)","id");
	$batch_from_redyeing_id=return_field_value("id","pro_batch_create_mst","batch_against=2 and status_active=1 and re_dyeing_from in($batch_id)","id");
	$batch_cond="";$first_batch_cond=""; $reding_batch_cond="";$reding_batch_cond="";
	//echo $batch_non_redyeing_id."##".$batch_redyeing_id."##".$batch_redyeing_id;die;
	if($batch_non_redyeing_id=="" && $batch_redyeing_id=="" && $batch_redyeing_id=="")
	{
		echo "No Data Found";
		die;
	}
	else
	{
		$batch_prefix=" and( ";
		if($batch_non_redyeing_id!="") $batch_cond=" batch_id like '$batch_non_redyeing_id'";
		if($batch_cond!="")  $batch_cond.="or";
		if($batch_redyeing_id!="") $batch_cond.=" batch_id like '$batch_redyeing_id'";
		if($batch_cond!="")  $batch_cond.="or";
		if($batch_from_redyeing_id!="") $batch_cond.=" batch_id like '$batch_from_redyeing_id'";
		$batch_cond=chop($batch_cond,"or");
		$batch_sufix=" )";
		if($batch_redyeing_id!="") $reding_batch_cond=" batch_id like '$batch_redyeing_id'";
		if($reding_batch_cond!="")  $reding_batch_cond.="or";
		if($batch_from_redyeing_id!="") $reding_batch_cond.=" batch_id like '$batch_from_redyeing_id'";
		$reding_batch_cond=chop($reding_batch_cond,"or");
		
	}
	
	
	if($batch_non_redyeing_id!="")
	{
		$first_dyes_qnty_sql=sql_select("select c.id as prod_id, sum(b.cons_quantity) as qnty, sum(b.cons_amount) as amt
		from inv_transaction b, product_details_master c
		where b.prod_id=c.id and b.transaction_type=2 and b.item_category in (6) and b.status_active=1 and batch_id like '$batch_non_redyeing_id'
		group by c.id");
		$first_dyes_data=array();
		foreach($first_dyes_qnty_sql as $row)
		{
			$first_dyes_data[$row[csf("prod_id")]]["qnty"]=$row[csf("qnty")];
			$first_dyes_data[$row[csf("prod_id")]]["amt"]=$row[csf("amt")];
		}
	}
	
	if($batch_redyeing_id!="" || $batch_from_redyeing_id!="") 
	{
		$reding_dyes_qnty_sql=sql_select("select c.id as prod_id, sum(b.cons_quantity) as qnty, sum(b.cons_amount) as amt
		from inv_transaction b, product_details_master c
		where b.prod_id=c.id and b.transaction_type=2 and b.item_category in (6) and b.status_active=1 $batch_prefix $reding_batch_cond  $batch_sufix
		group by c.id");
		$reding_dyes_data=array();
		foreach($reding_dyes_qnty_sql as $row)
		{
			$reding_dyes_data[$row[csf("prod_id")]]["qnty"]=$row[csf("qnty")];
			$reding_dyes_data[$row[csf("prod_id")]]["amt"]=$row[csf("amt")];
		}
	}
	//echo "<pre>";print_r($reding_dyes_data);die;
	
	if($batch_non_redyeing_id!="")
	{
		$first_chemical_qnty_sql=sql_select("select c.id as prod_id, sum(b.cons_quantity) as qnty, sum(b.cons_amount) as amt
		from inv_transaction b, product_details_master c
		where b.prod_id=c.id and b.transaction_type=2 and b.item_category in in (5,7) and b.status_active=1 and batch_id like '$batch_non_redyeing_id'
		group by c.id");
		$first_chemical_data=array();
		foreach($first_chemical_qnty_sql as $row)
		{
			$first_chemical_data[$row[csf("prod_id")]]["qnty"]=$row[csf("qnty")];
			$first_chemical_data[$row[csf("prod_id")]]["amt"]=$row[csf("amt")];
		}
	}
	
	if($batch_redyeing_id!="" || $batch_from_redyeing_id!="") 
	{
		$reding_chemical_qnty_sql=sql_select("select c.id as prod_id, sum(b.cons_quantity) as qnty, sum(b.cons_amount) as amt
		from inv_transaction b, product_details_master c
		where b.prod_id=c.id and b.transaction_type=2 and b.item_category in (5,7) and b.status_active=1 $batch_prefix $reding_batch_cond  $batch_sufix
		group by c.id");
		$reding_chemical_data=array();
		foreach($reding_chemical_qnty_sql as $row)
		{
			$reding_chemical_data[$row[csf("prod_id")]]["qnty"]=$row[csf("qnty")];
			$reding_chemical_data[$row[csf("prod_id")]]["amt"]=$row[csf("amt")];
		}
	}
	
	
	$sql_dtls_dyes = "select c.id as prod_id, c.product_name_details, c.unit_of_measure, c.avg_rate_per_unit, sum(b.cons_quantity) as qnty
	from inv_transaction b, product_details_master c
	where b.prod_id=c.id and b.transaction_type=2 and b.item_category in (6) and b.status_active=1 $batch_prefix $batch_cond  $batch_sufix
	group by c.id, c.product_name_details, c.unit_of_measure, c.avg_rate_per_unit"; 
	//echo $sql_dtls_dyes;die;
	$sql_result_dyes= sql_select($sql_dtls_dyes);
	
	$sql_dtls_chemical = "select c.id as prod_id, c.product_name_details, c.unit_of_measure, c.avg_rate_per_unit, sum(b.cons_quantity) as qnty
	from inv_transaction b, product_details_master c
	where b.prod_id=c.id and b.transaction_type=2 and b.item_category in (5,7) and b.status_active=1 $batch_prefix $batch_cond  $batch_sufix 
	group by c.id, c.product_name_details, c.unit_of_measure, c.avg_rate_per_unit"; 
	//echo $sql_dtls_chemical;die;
	$sql_result_chemical= sql_select($sql_dtls_chemical);
	?>
    <script>
		function print_window()
		{
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTsD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
			'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_div').innerHTML+'</body</html>');
			d.close();
		}	
	</script>	
	<div style="width:870px; margin-left:10px" id="report_div">
     <!--<div style="width:870px;" align="center"><input  type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>-->
    <div style="width:870px; font-family:'Arial Narrow'; font-size:14px;">Dyes</div>
    <table align="center" cellspacing="0" width="870" border="1" rules="all" class="rpt_table" >
        <thead align="center">
    	   <tr>
                <th width="50" rowspan="2">SL</th>
                <th width="50" rowspan="2">Product Id</th>
                <th width="200" rowspan="2">Item Description</th>
                <th width="80" rowspan="2">UOM</th>
                <th colspan="3">First Dying</th>
                <th colspan="3">Subsequent Dyeing</th>
               
           </tr>
           <tr>
                <th width="80">Quantity</th>
                <th width="80">Avg. Rate</th> 
                <th width="80">Amount(BDT)</th>
                <th width="80">Quantity</th>
                <th width="80">Avg. Rate</th> 
                <th>Amount(BDT)</th>
           </tr>
		</thead>
		<?  
		foreach($sql_result_dyes as $row)
		{
			if ($i%2==0)  
				$bgcolor="#E9F3FF";
			else
				$bgcolor="#FFFFFF";
			$first_dyes_qnty=$first_dyes_data[$row[csf("prod_id")]]["qnty"];
			$first_dyes_amt=$first_dyes_data[$row[csf("prod_id")]]["amt"];
			$first_dyes_rate=$first_dyes_amt/$first_dyes_qnty;
			$reding_dyes_qnty=$reding_dyes_data[$row[csf("prod_id")]]["qnty"];
			$reding_dyes_amt=$reding_dyes_data[$row[csf("prod_id")]]["amt"];
			$reding_dyes_rate=$reding_dyes_amt/$reding_dyes_qnty;
			?>
			<tbody>
				<tr bgcolor="<? echo $bgcolor; ?>">
					<td align="center"><? echo $i; ?></td>
					<td align="center"><? echo $row[csf("prod_id")]; ?></td>
					<td><? echo $row[csf("product_name_details")]; ?></td>
					<td align="center"><? echo $unit_of_measurement[$row[csf("unit_of_measure")]]; ?></td>
					<td align="right"><? echo number_format($first_dyes_qnty,4,'.',''); $total_first_dyes_qnty+=$first_dyes_qnty; ?></td>
					<td align="right"><? echo number_format($first_dyes_rate,4,'.',''); ?></td>
					<td align="right"><? echo  number_format($first_dyes_amt,6,'.',''); $total_first_dyes_amt+=$first_dyes_amt; ?></td>
                    <td align="right"><? echo number_format($reding_dyes_qnty,4,'.',''); $total_reding_dyes_qnty+=$reding_dyes_qnty; ?></td>
					<td align="right"><? echo number_format($reding_dyes_rate,4,'.',''); ?></td>
					<td align="right"><? echo  number_format($reding_dyes_amt,6,'.',''); $total_reding_dyes_amt+=$reding_dyes_amt; ?></td>
				</tr>
			</tbody>
			<? 
			$i++;
			$first_dyes_qnty=0;
			$first_dyes_amt=0;
			$reding_dyes_qnty=0;
			$reding_dyes_amt=0;
		}
		?>
        <tfoot>
        	<tr>
                <th colspan="4" align="right">Total:</th>
                <th align="right"><? echo number_format($total_first_dyes_qnty,2,'.',''); ?></th>
                <th></th>
                <th align="right"><? echo number_format($total_first_dyes_amt,2,'.',''); ?></th>
                <th align="right"><? echo number_format($total_reding_dyes_qnty,2,'.',''); ?></th>
                <th></th>
                <th align="right"><? echo number_format($total_reding_dyes_amt,2,'.',''); ?></th>
            </tr>
        </tfoot>
      </table>
      
      <div style="width:870px; font-family:'Arial Narrow'; font-size:14px;">Chemical</div>
    <table align="center" cellspacing="0" width="870" border="1" rules="all" class="rpt_table" >
        <thead align="center">
    	   <tr>
                <th width="50" rowspan="2">SL</th>
                <th width="50" rowspan="2">Product Id</th>
                <th width="200" rowspan="2">Item Description</th>
                <th width="80" rowspan="2">UOM</th>
                <th colspan="3">First Dying</th>
                <th colspan="3">Subsequent Dyeing</th>
               
           </tr>
           <tr>
                <th width="80">Quantity</th>
                <th width="80">Avg. Rate</th> 
                <th width="80">Amount(BDT)</th>
                <th width="80">Quantity</th>
                <th width="80">Avg. Rate</th> 
                <th>Amount(BDT)</th>
           </tr>
		</thead>
		<?  
		foreach($sql_result_chemical as $row)
		{
			if ($i%2==0)  
				$bgcolor="#E9F3FF";
			else
				$bgcolor="#FFFFFF";
			$first_chemi_qnty=$first_chemical_data[$row[csf("prod_id")]]["qnty"];
			$first_chemi_amt=$first_chemical_data[$row[csf("prod_id")]]["amt"];
			$first_chemi_rate=$first_chemi_amt/$first_chemi_qnty;
			$reding_chemi_qnty=$reding_chemical_data[$row[csf("prod_id")]]["qnty"];
			$reding_chemi_amt=$reding_chemical_data[$row[csf("prod_id")]]["amt"];
			$reding_chemi_rate=$reding_chemi_amt/$reding_chemi_qnty;
			?>
			<tbody>
				<tr bgcolor="<? echo $bgcolor; ?>">
					<td align="center"><? echo $i; ?></td>
					<td align="center"><? echo $row[csf("prod_id")]; ?></td>
					<td><? echo $row[csf("product_name_details")]; ?></td>
					<td align="center"><? echo $unit_of_measurement[$row[csf("unit_of_measure")]]; ?></td>
					<td align="right"><? echo number_format($first_chemi_qnty,4,'.',''); $total_first_chemi_qnty+=$first_chemi_qnty; ?></td>
					<td align="right"><? echo number_format($first_chemi_rate,4,'.',''); ?></td>
					<td align="right"><? echo  number_format($first_chemi_amt,6,'.',''); $total_first_chemi_amt+=$first_chemi_amt; ?></td>
                    <td align="right"><? echo number_format($reding_chemi_qnty,4,'.',''); $total_reding_chemi_qnty+=$reding_chemi_qnty; ?></td>
					<td align="right"><? echo number_format($reding_chemi_rate,4,'.',''); ?></td>
					<td align="right"><? echo  number_format($reding_chemi_amt,6,'.',''); $total_reding_chemi_amt+=$reding_chemi_amt; ?></td>
				</tr>
			</tbody>
			<? 
			$i++;
		}
		?>
        <tfoot>
        	<tr>
                <th colspan="4" align="right">Total:</th>
                <th align="right"><? echo number_format($total_first_chemi_qnty,2,'.',''); ?></th>
                <th></th>
                <th align="right"><? echo number_format($total_first_chemi_amt,2,'.',''); ?></th>
                <th align="right"><? echo number_format($total_reding_chemi_qnty,2,'.',''); ?></th>
                <th></th>
                <th align="right"><? echo number_format($total_reding_chemi_amt,2,'.',''); ?></th>
            </tr>
        </tfoot>
      </table>
    </div>
    <?         
	exit();
}

if($action=="subprocess_fabrics_dtls_popup")
{
echo load_html_head_contents("Subprocess Details", "../../../../", 1, 1,$unicode,'','');
extract($_REQUEST);
//echo $batch_id;
//if($batch_id!='') $batch_con=" and LIKE a.batch_id";
$color_arr=return_library_array( "select id, color_name from lib_color", "id", "color_name");
$re_dyeing_batch_id=return_field_value("id","pro_batch_create_mst","batch_no='".$batch_no."' and batch_against=2 and extention_no!=0");
if ($batch_id!='') $batch_con=" and a.batch_id like '%$batch_id%'"; else $batch_con="and a.batch_id='0'";
if ($batch_id!='') $issue_batch_con=" and d.batch_id like '%$batch_id%'"; else $issue_batch_con="and d.batch_id='0'";
if ($re_dyeing_batch_id!='') $redyeing_batch_id=" and d.batch_id in($re_dyeing_batch_id)"; else $redyeing_batch_id="and d.batch_id in('0')";	
$po_no=''; $job_no='';$file_no='';$ref_no=''; $buyer_name='';
//echo $batch_type.'=='.$batch_id;
		if($batch_type==2)
		{
			$po_data=sql_select("select distinct b.order_no, c.subcon_job, c.party_id from pro_batch_create_dtls a, subcon_ord_dtls b, subcon_ord_mst c where a.po_id=b.id and b.job_no_mst=c.subcon_job and a.status_active=1 and a.is_deleted=0 and a.mst_id in(".$batch_id.") ");
			foreach($po_data as $row)
			{
				$po_no.=$row[csf('order_no')].","; 
				$job_no.=$row[csf('subcon_job')].",";
				$buyer_name.=$buyer_library[$row[csf('party_id')]].",";
			}
		}
		else
		{
			$po_data=sql_select("select distinct b.po_number,b.file_no,b.grouping as ref, c.job_no, c.buyer_name from pro_batch_create_dtls a, wo_po_break_down b, wo_po_details_master c where a.po_id=b.id and b.job_no_mst=c.job_no and a.status_active=1 and a.is_deleted=0 and a.mst_id in(".$batch_id.") ");
			//echo "select distinct b.po_number,b.file_no,b.grouping as ref, c.job_no, c.buyer_name from pro_batch_create_dtls a, wo_po_break_down b, wo_po_details_master c where a.po_id=b.id and b.job_no_mst=c.job_no and a.status_active=1 and a.is_deleted=0 and a.mst_id in(".$batch_id.") ";
			foreach($po_data as $row)
			{
				$po_no.=$row[csf('po_number')].","; 
				$job_no.=$row[csf('job_no')].",";
				$file_no.=$row[csf('file_no')].","; 
				$ref_no.=$row[csf('ref')].","; 
				//$buyer_name.=$buyer_library[$row[csf('buyer_name')]].",";
			}
			foreach(explode(",",$data_array[0][csf("buyer_id")]) as $buyer_id)
			{
				$buyer_name.=$buyer_library[$buyer_id].",";
			}
				
		}
		
	 $sql_isue_dtls = "select d.recipe_id,a.batch_no,
	 avg(b.cons_rate) as cons_rate, sum(b.cons_quantity) as cons_quantity, d.product_id as prod_id,
	 c.item_group_id, d.sub_process
	  from inv_issue_master a, inv_transaction b, product_details_master c, dyes_chem_issue_dtls d
	  where a.id=b.mst_id and b.id =d.trans_id and d.product_id=c.id and b.prod_id=c.id and b.transaction_type=2 and a.entry_form=5 and b.item_category in (5,6,7) $issue_batch_con  group by d.recipe_id,a.batch_no,d.product_id, c.item_group_id, d.sub_process order by d.sub_process "; 
	 // echo $sql_isue_dtls;die;
	  $sql_result_issue= sql_select($sql_isue_dtls);
	  $issue_data_arr=array();
	  foreach($sql_result_issue as $row)
	  {
		  $issue_data_arr[$row[csf('sub_process')]][$row[csf('prod_id')]][$row[csf('item_group_id')]][$row[csf('recipe_id')]]['issue_qty']=$row[csf('cons_quantity')]; 
		  $issue_data_arr[$row[csf('sub_process')]][$row[csf('prod_id')]][$row[csf('item_group_id')]][$row[csf('recipe_id')]]['cons_rate']=$row[csf('cons_rate')]; 
	  }
	  $group_arr=return_library_array( "select id,item_name from lib_item_group where item_category in (5,6,7) and status_active=1 and is_deleted=0",'id','item_name');
 	 $sql_dtls_req = "select a.id, a.requ_no, a.location_id, a.requisition_date,a.method, a.requisition_basis, a.batch_id, a.recipe_id, a.machine_id
	    from dyes_chem_issue_requ_mst a,  dyes_chem_issue_requ_dtls b, product_details_master c
	    where a.id=b.mst_id and b.product_id=c.id and b.item_category in (5,6,7) and a.entry_form=156 and b.req_qny_edit!=0 and c.item_category_id in (5,6,7)  $batch_con  group by  a.id,a.requ_no,a.location_id, a.requisition_date,a.method, a.requisition_basis, a.batch_id, a.recipe_id, a.machine_id order by a.id";
	// echo $sql_dtls;//die;
	$sql_dtls_req= sql_select($sql_dtls_req);
	foreach($sql_dtls_req as $row)
	{
		$requ_no_array[$row[csf("id")]]=$row[csf("requ_no")];
		$requ_array[$row[csf("id")]]['batch_id']=$row[csf("batch_id")];
	}
	?>
    <script>
		function print_window()
		{
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTsD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
			'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_div').innerHTML+'</body</html>');
			d.close();
		}	
	</script>	
      <div> 
	<div style="width:870px; margin-left:30px" id="report_div">
     <div style="width:870px;" align="center">
        	<input  type="button" value="Print Preview" onClick="print_window()" style="width:90px"  class="formbutton"/> &nbsp;
           <div id="report_container"> </div>
        </div>
        <?
		  ob_start();
		  foreach($requ_no_array as  $req_id=>$reqNo)
		  {
			$batch_ids=$requ_array[$req_id]['batch_id'];
			$batch_weight=0;
			if($db_type==0) 
			{
				$data_array=sql_select("select group_concat(buyer_id) as buyer_id,group_concat(id) as recipe_id, group_concat(batch_id) as batch_id, sum(total_liquor) as total_liquor, group_concat(concat_ws(':',batch_ratio,liquor_ratio) order by id) as ratio, sum(case when entry_form=60 then new_batch_weight end) as batch_weight, group_concat(case when entry_form<>60 then batch_id end) as batch_id_rec_main from pro_recipe_entry_mst where batch_id in($batch_ids)");
			}
			else
			{
				$data_array=sql_select("select listagg(buyer_id,',') within group (order by buyer_id) as buyer_id,listagg(id,',') within group (order by id) as recipe_id, listagg(batch_id,',') within group (order by batch_id) as batch_id, sum(total_liquor) as total_liquor, listagg(batch_ratio || ':' || liquor_ratio,',') within group (order by id) as ratio, sum(case when entry_form=60 then new_batch_weight end) as batch_weight, listagg(case when entry_form<>60 then batch_id end,',') within group (order by batch_id) as batch_id_rec_main from pro_recipe_entry_mst where  batch_id in($batch_ids)");
			 //	echo "select listagg(buyer_id,',') within group (order by buyer_id) as buyer_id,listagg(id,',') within group (order by id) as recipe_id, listagg(batch_id,',') within group (order by batch_id) as batch_id, sum(total_liquor) as total_liquor, listagg(batch_ratio || ':' || liquor_ratio,',') within group (order by id) as ratio, sum(case when entry_form=60 then new_batch_weight end) as batch_weight, listagg(case when entry_form<>60 then batch_id end,',') within group (order by batch_id) as batch_id_rec_main from pro_recipe_entry_mst where  batch_id in($batch_ids)";
			}
			$batch_weight=$data_array[0][csf("batch_weight")];
			$batch_id_rec_main=implode(",",array_unique(explode(",",$data_array[0][csf("batch_id_rec_main")])));
			if($batch_id_rec_main=="") $batch_id_rec_main=0;
			if($db_type==0) 
			{
				$batchdata_array=sql_select("select group_concat(sales_order_no) as sales_order_no,group_concat(booking_no) as booking_no,group_concat(batch_no) as batch_no, sum(case when id in($batch_id_rec_main) then batch_weight end) as batch_weight, group_concat(distinct color_id) as color_id,group_concat(distinct color_range_id) as color_range_id from pro_batch_create_mst where id in($batch_ids) and is_sales=1");
			}
			else //color_range_id
			{
				$batchdata_array=sql_select("select listagg(CAST(sales_order_no AS VARCHAR2(4000)),',') within group (order by sales_order_no) as sales_order_no,listagg(CAST(booking_no AS VARCHAR2(4000)),',') within group (order by booking_no) as booking_no,listagg(CAST(batch_no AS VARCHAR2(4000)),',') within group (order by booking_no) as batch_no, listagg(color_id ,',') within group (order by color_id) as color_id,listagg(color_range_id ,',') within group (order by color_range_id) as color_range_id, sum(case when id in($batch_id_rec_main) then batch_weight end) as batch_weight from pro_batch_create_mst where id in($batch_ids) and is_sales=1");	
				
			}
			$color_id=array_unique(explode(",",$batchdata_array[0][csf('color_id')]));
			$color_name='';
			foreach($color_id as $color)
			{
			if($color_name=='' ) $color_name=$color_arr[$color]; else $color_name.=",".$color_arr[$color];
			}
			$color_range_id=array_unique(explode(",",$batchdata_array[0][csf('color_range_id')]));
			$color_ranges='';
			foreach($color_range_id as $rang_id)
			{
			//$color_ranges.=$color_range[$rang_id].",";
			if($color_ranges=='' ) $color_ranges=$color_range[$rang_id]; else $color_ranges.=",".$color_range[$rang_id];
			}
			
			$color_range=substr($color_ranges,0,-1);
			$sql_dtls = "select a.requ_no,a.requisition_date,a.requisition_basis, a.batch_id, a.recipe_id, b.id,
			b.sub_process, b.item_category, b.dose_base, b.ratio, b.recipe_qnty, b.adjust_percent, b.adjust_type, b.required_qnty, b.req_qny_edit,
			c.item_description, c.item_group_id, c.sub_group_name, c.item_size, c.unit_of_measure,c.avg_rate_per_unit,c.id as prod_id
			from dyes_chem_issue_requ_mst a,  dyes_chem_issue_requ_dtls b, product_details_master c
			where a.id=b.mst_id and b.product_id=c.id and a.id in($req_id) and b.item_category in (5,6,7) and a.entry_form=156 and b.req_qny_edit!=0 and c.item_category_id in (5,6,7)  order by b.id, b.seq_no";
			// echo $sql_dtls;//die;
			$sql_result= sql_select($sql_dtls);
			$sub_process_array=array();
			$sub_process_tot_rec_array=array();
			$sub_process_tot_req_array=array();
			$sub_process_tot_value_array=array();
			
			foreach($sql_result as $row)
			{
				$sub_process_tot_rec_array[$row[csf("sub_process")]]+=$row[csf("recipe_qnty")];
			}
		?>
	<div style="100%"><!--Req No Start here-->
     <table width="870" cellspacing="0" align="center"  >
	 <caption align="center"> <b>Req. no: <? echo $reqNo; ?></b></caption>
	 <tr>
        	<td width="100" valign="top"><strong>FSO. No:</strong></td><td width="160px"><? echo implode(",",array_unique(explode(",",$batchdata_array[0][csf('sales_order_no')]))) ?></td>
            <td width="90"><strong>Recipe No:</strong></td> <td><? echo $sql_result[0][csf('recipe_id')]; ?></td>
            <td width="90"><strong>Booking No:</strong></td><td width="160px"><? echo implode(",",array_unique(explode(",",$batchdata_array[0][csf('booking_no')]))); ?></td>
       </tr>
	   
        <tr>
        	<td width="100"><strong>Issue Date:</strong></td><td width="160px"><? echo change_date_format($sql_result[0][csf('requisition_date')]); ?></td>
            <td width="90"><strong>Issue Basis</strong></td> <td><? echo $receive_basis_arr[$sql_result[0][csf('requisition_basis')]]; ?></td>
            
            <td width="90"><strong></strong></td><td width="160px"><? //echo implode(",",array_unique(explode(",",$buyer_name))); ?></td>
       </tr>
        <tr>
            <td><strong>Buyer Order:</strong></td> <td><? echo $po_no; ?></td>
           <td><strong>Batch No</strong></td><td><? echo $batchdata_array[0][csf('batch_no')]; ?></td>
           <td><strong>Batch Weight</strong></td><td><? echo number_format(/*$batch_weight + as per implement and common team mamun decision*/$batchdata_array[0][csf('batch_weight')],2); ?></td>
        </tr>
        <tr>
            <td><strong>File No</strong></td><td><? echo $file_no; ?></td>
            <td><strong>Ref. No:</strong></td><td><? echo $ref_no; ?></td>
            <td><strong>Batch Color:</strong></td><td><? echo $color_name; ?></td>
        </tr>
        <tr>
            <td><strong>Color Range</strong></td><td><? echo $color_range; ?></td>
            <td></td><td><? //echo $data_array[0][csf("total_liquor")]; ?></td>
            <td></td><td><? //echo $data_array[0][csf("ratio")]; ?></td>
        </tr>
       
    </table>
    
    <table align="center" cellspacing="0" width="870" border="1" rules="all" class="rpt_table" >
        <thead bgcolor="#dddddd" align="center">
    	   <tr bgcolor="#EFEFEF">   
                <th width="30">SL</th>
                <th width="80" >Item Cat.</th>
                <th width="100" >Item Group</th>
                <th width="150">Item Description</th>
              
                <th width="50" >UOM</th>
                <th width="100" >Dose Base</th> 
                <th width="40" >Ratio</th>
                <th width="60" >Recipe Qty.</th>
                <th width="50" >Adj%</th>
                <th width="60" >Adj Type</th> 
               
                <th width="80" >Iss. Qty.</th>
              
                <th width="70" >Unit Price</th>
                <th width="" >Amount(BDT)</th>
                </tr>
		</thead>
	<?  
 	
	?>
                
	<?
	//var_dump($sub_process_tot_req_array);
	$i=1; $k=1; $recipe_qnty_sum=0; $req_qny_edit_sum=0; $recipe_qnty=0; $req_qny_edit=0; $req_value_sum=0;
	$req_value_grand=0; $recipe_qnty_grand=0; $req_qny_edit_grand=0;
	$tot_sub_ratio=0;
	foreach($sql_result as $row)
	{
		if ($i%2==0)  
			$bgcolor="#E9F3FF";
		else
			$bgcolor="#FFFFFF";
			if (!in_array($row[csf("sub_process")],$sub_process_array) )
			{
				$sub_process_array[]=$row[csf('sub_process')];
				if($k!=1)
				{
					?>
                    <tr>
						<td colspan="6" align="right"><strong>Total :</strong></td>
						<td align="center"><?php echo number_format($tot_sub_ratio,6,'.','');$tot_sub_ratio=0; ?></td>
                        <td align="right"><?php  echo number_format($recipe_qnty_sum,6,'.',''); ?></td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>                		
                        <td align="right"><?php  echo number_format($req_qny_issue_sum,6,'.',''); ?></td>
                        <td>&nbsp;</td>
                	
                        <td align="right"><?php  echo number_format($amount_req_value_sum,6,'.',''); ?></td>
                    </tr> 
			 <? 
			} 
			$recipe_qnty_sum=0;
			$req_qny_issue_sum=0;
			$amount_req_value_sum=0;
			$k++; 
			?>
            <tr bgcolor="#CCCCCC">
                <th colspan="13"><strong><? echo $dyeing_sub_process[$row[csf("sub_process")]]; ?></strong></th>
            </tr> 
            
        <? 
		}
		
		
		$issue_qty=$issue_data_arr[$row[csf("sub_process")]][$row[csf('prod_id')]][$row[csf('item_group_id')]][$row[csf('recipe_id')]]['issue_qty'];
		$issue_cons_rate=$issue_data_arr[$row[csf("sub_process")]][$row[csf('prod_id')]][$row[csf('item_group_id')]][$row[csf('recipe_id')]]['cons_rate'];
		?>
        <tbody>
			<tr bgcolor="<? echo $bgcolor; ?>">
                <td align="center"><? echo $i; ?></td>
                <td><? echo $item_category[$row[csf("item_category")]];  //echo $row[csf("sub_process")]; ?></td>
                <td><? echo $group_arr[$row[csf("item_group_id")]]; ?></td>
              
                <td><? echo $row[csf("item_description")].' '.$row[csf("item_size")]; ?></td>
                
                <td align="center"><? echo $unit_of_measurement[$row[csf("unit_of_measure")]]; ?></td>
                <td><? echo $dose_base[$row[csf("dose_base")]]; ?></td>
                <td align="center"><? echo number_format($row[csf("ratio")],6,'.',''); ?></td>
                <td align="right"><? echo number_format($row[csf("recipe_qnty")],6,'.',''); ?></td>
                <td align="center"><? echo $row[csf("adjust_percent")]; ?></td>
                <td><? echo $increase_decrease[$row[csf("adjust_type")]]; ?></td>
              
                <td align="right"><? echo number_format($issue_qty,6,'.',''); ?></td>
                
                <td align="right"><? echo  number_format($issue_cons_rate,6,'.',''); ?></td>
                <td align="right"><? $amount_req_value=$issue_qty*$issue_cons_rate; echo number_format($amount_req_value,6,'.',''); ?></td>
			</tr>
        </tbody>
		<? $i++;
        $recipe_qnty_sum +=$row[csf('recipe_qnty')];
        $req_qny_issue_sum +=$issue_qty;
		$amount_req_value_sum +=$amount_req_value;
		$tot_sub_ratio+=$row[csf("ratio")];
		$tot_grand_ratio+=$row[csf("ratio")];
		$recipe_qnty_grand +=$row[csf('recipe_qnty')];
        $req_qny_issue_grand +=$issue_qty;
		$amount_req_value_grand +=$amount_req_value;
        }
		foreach ($sub_process_tot_rec_array as $val_rec)
		{
			 $totval_rec=$val_rec;
		}
		/*foreach ($sub_process_tot_req_array as $val_req)
		{
			 $totval_req=$val_req;
		}
		foreach ($sub_process_tot_value_array as $req_value)
		{
			 $tot_req_value=$req_value;
		}*/
		
		//$recipe_qnty_grand +=$val_rec;
        //$req_qny_edit_grand +=$val_req;
		//$req_value_grand +=$req_value;
		?>
           <tr>
		  		 <td colspan="6" align="right"><strong>Total :</strong></td>
				<td align="center"><?php echo number_format($tot_sub_ratio,6,'.','');$tot_sub_ratio=0; ?></td>
                <td align="right"><?php echo number_format($totval_rec,6,'.',''); ?></td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                
                <td align="right"><?php echo number_format($req_qny_issue_sum,6,'.',''); ?></td>
               
                <td>&nbsp;</td>
                <td align="right"><?php echo number_format($amount_req_value_sum,6,'.',''); ?></td>
            </tr> 
             <tr>
			 <td colspan="6" align="right"><strong> Grand Total :</strong></td>
				<td align="right"><?php echo number_format($tot_grand_ratio,6,'.',''); ?></td>
                <td align="right"><?php echo number_format($recipe_qnty_grand,6,'.',''); ?></td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
               
                <td align="right"><?php echo number_format($req_qny_issue_grand,6,'.',''); ?></td>
                <td>&nbsp;</td>
                
                <td align="right"><?php echo number_format($amount_req_value_grand,6,'.',''); ?></td>
            </tr> 
               <tr>
                <td colspan="11" align="right"><strong> Cost Per Kg :</strong></td>
                <td colspan="2" align="right"><?php echo number_format($amount_req_value_grand/($batchdata_array[0][csf('batch_weight')]),6,'.','');
				$amount_req_value_grand=0;$req_qny_issue_grand=0;$recipe_qnty_grand=0;$amount_req_value_sum=0;$req_qny_issue_sum=0;
				 ?></td>
            </tr>                          
      </table>
	  </div> <!--Req No End-->
        <br><br>
      	<?
	}  
			
	  if($db_type==2) $rec_grp_con="listagg(id,',') within group (order by id) as id";
	  else  $rec_grp_con="group_concat(id)  as id";
	 $recipe_idss=$sql_result[0][csf('recipe_id')];//return_field_value("$rec_grp_con","pro_recipe_entry_mst","batch_id in(".$batch_id.") and  dyeing_re_process=1  and entry_form=60 ","id"); 	
	   $new_batch_weight=return_field_value("new_batch_weight","pro_recipe_entry_mst","batch_id in(".$batch_id.")  ");
		$extention_no=return_field_value("max(extention_no) as extention_no","pro_batch_create_mst","batch_no='".$batch_no."' and batch_against=2 and extention_no!=0","extention_no");
		
		if($db_type==2)
			{
			 $sql_dtls="select a.id, a.item_category_id as item_category, a.item_group_id, a.sub_group_name, a.item_description, a.item_size, a.unit_of_measure, b.id as dtls_id, b.sub_process_id as sub_process, b.prod_id, b.item_lot, b.dose_base, b.ratio, b.adj_type, b.adj_perc, b.adj_qnty, b.new_item, b.new_batch_weight, b.new_total_liquor,d.id as recipe_id  from product_details_master a, pro_recipe_entry_dtls b,pro_recipe_entry_mst d where d.id=b.mst_id and a.id=b.prod_id and d.batch_id in($batch_id)  and b.status_active=1 and b.is_deleted=0 and d.entry_form=60 and a.item_category_id in(5,6,7) and a.status_active=1 and a.is_deleted=0 and b.ratio>0 and d.dyeing_re_process=1 order by b.sub_process_id, b.seq_no";
			}
			else if($db_type==0)
			{
				$sql_dtls="select a.id, a.item_category_id as item_category, a.item_group_id, a.sub_group_name, a.item_description, a.item_size, a.unit_of_measure,b.id as dtls_id, b.sub_process_id as sub_process, b.prod_id, b.item_lot, b.dose_base, b.ratio, b.adj_type, b.adj_perc, b.adj_qnty, b.new_item, b.new_batch_weight, b.new_total_liquor,d.id as recipe_id  from product_details_master a, pro_recipe_entry_dtls b,pro_recipe_entry_mst d where d.id=b.mst_id  and a.id=b.prod_id  and d.batch_id in($batch_id)  and b.status_active=1 and b.is_deleted=0  and a.item_category_id in(5,6,7) and d.entry_form=60 and d.dyeing_re_process=1  and a.status_active=1 and a.is_deleted=0 and b.ratio>0 order by b.sub_process_id, b.seq_no DESC";
			}	
			$sql_result_top= sql_select($sql_dtls);

	if(count($sql_result_top)>0)
	{
		foreach ($sql_result_top as  $row) 
		{
			$result_top_recipe_arr[$row[csf('recipe_id')]]=$row[csf('recipe_id')];
		}

		$result_top_recipe_arr = array_filter(array_unique($result_top_recipe_arr));
		if(count($result_top_recipe_arr)>0)
		{
			$result_top_recipe_ids = implode(",", $result_top_recipe_arr);
			$topRCond = $all_top_recipe_cond = "";

			if($db_type==2 && count($result_top_recipe_arr)>999)
			{
				$result_top_recipe_arr_chunk=array_chunk($result_top_recipe_arr,999) ;
				foreach($result_top_recipe_arr_chunk as $chunk_arr)
				{
					$topRCond.=" mst_id in(".implode(",",$chunk_arr).") or ";
				}

				$all_top_recipe_cond.=" and (".chop($topRCond,'or ').")";

			}
			else
			{
				$all_top_recipe_cond=" and mst_id in($result_top_recipe_ids)";
			}
		}
		?>
        
     <table width="870" cellspacing="0" align="center"  >
        <tr>
        	<td width="100"><strong>Batch Ext. No:</strong></td><td width="160px"><? echo $extention_no; ?></td>
            <td width="200"><strong>Topping Batch Weight:
</strong></td> <td><? echo $new_batch_weight; ?></td>
            
            <td width="90">&nbsp;</td><td width="160px"><? //echo implode(",",array_unique(explode(",",$buyer_name))); ?></td>
       </tr>
       </table>
       
        <table align="center" cellspacing="0" width="870" border="1" rules="all" class="rpt_table" >
        <thead bgcolor="#dddddd" align="center">
        <caption> Topping</caption>
    	   <tr bgcolor="#EFEFEF">   
                <th width="30">SL</th>
                <th width="80" >Item Cat.</th>
                <th width="100" >Item Group</th>
                <th width="150">Item Description</th>
              
                <th width="50" >UOM</th>
                <th width="100" >Dose Base</th> 
                <th width="40" >Ratio</th>
                <th width="60" >Recipe Qty.</th>
                <th width="50" >Adj%</th>
                <th width="60" >Adj Type</th> 
                <th width="80" >Iss. Qty.</th>
                <th width="70" >Unit Price</th>
                <th width="" >Amount(BDT)</th>
                </tr>
		</thead>
	<?  
 

	$ratio_arr=array();
	$prevRatioData=sql_select( "select prod_id,mst_id as recipe_id,  sub_process_id, ratio from pro_recipe_entry_dtls where status_active=1 and is_deleted=0 $all_top_recipe_cond");
	foreach($prevRatioData as $prevRow)
	{
		$ratio_arr[$prevRow[csf('sub_process_id')]][$prevRow[csf('prod_id')]][$prevRow[csf('recipe_id')]]=$prevRow[csf('ratio')];
	}
	//var_dump($sub_process_tot_req_array);
	$i=1; $k=1; $recipe_qnty_sum=0; $req_qny_edit_sum=0; $recipe_qnty=0; $amount_req_value_grand_top=0; $req_value_sum=0;
	$req_value_grand=0; $recipe_qnty_grand=0; $req_qny_issue_grand_top=0;
	
	foreach($sql_result_top as $row)
	{
		if ($i%2==0)  
			$bgcolor="#E9F3FF";
		else
			$bgcolor="#FFFFFF";
			//echo $row[csf('sub_process')].'='.$row[csf('prod_id')].'='.$row[csf('item_group_id')].'='.$row[csf('recipe_id')];
			//if (!in_array($row[csf("sub_process")],$sub_process_array) )
			if ($sub_process_array[$row[csf("sub_process")]] =="" )
			{
				$sub_process_array[$row[csf("sub_process")]]=$row[csf('sub_process')];
				if($k!=1)
				{
					?>
                    <tr>
                        <td colspan="7" align="right"><strong>Total :</strong></td>
                        <td align="right">D<?php  echo number_format($recipe_qnty_sum,6,'.',''); ?></td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                		
                        <td align="right"><?php  echo number_format($req_qny_issue_sum,6,'.',''); ?></td>
                        <td>&nbsp;</td>
                	
                        <td align="right"><?php  echo number_format($amount_req_value_sum,6,'.',''); ?></td>
                    </tr> 
			 <? 
			} 
			$recipe_qnty_sum=0;
			$req_qny_issue_sum=0;
			$amount_req_value_sum=0;
			$k++; 
			?>
            <tr bgcolor="#CCCCCC">
                <th colspan="13"><strong><? echo $dyeing_sub_process[$row[csf("sub_process")]]; ?></strong></th>
            </tr> 
            
        <? 
		}
		
		////$issue_data_arr[$row[csf('sub_process')]][$row[csf('prod_id')]][$row[csf('item_group_id')]][$row[csf('recipe_id')]]['issue_qty']
		$issue_qty=$issue_data_arr[$row[csf("sub_process")]][$row[csf('prod_id')]][$row[csf('item_group_id')]][$row[csf('recipe_id')]]['issue_qty'];
		$issue_cons_rate=$issue_data_arr[$row[csf("sub_process")]][$row[csf('prod_id')]][$row[csf('item_group_id')]][$row[csf('recipe_id')]]['cons_rate'];
		$total_liquor=$row[csf("new_total_liquor")];
		$batch_weight=$row[csf("new_batch_weight")];
		$ratio=$row[csf("ratio")];
		$dose_base_id=$row[csf("dose_base")];
		$adj_type=$row[csf("adj_type")];
		$prod_id=$row[csf("prod_id")];
		if($row[csf("new_item")]==1) 
			{
				$prev_ratio='';
				$recipe_qty='';
				$adj_type='';
			}
			else 
			{
				//echo $row[csf("recipe_id")];
				$prev_ratio=$ratio_arr[$row[csf("sub_process")]][$prod_id][$row[csf("recipe_id")]];
				if($dose_base_id==1) $recipe_qty=number_format(($total_liquor*$prev_ratio)/1000,4);
				else if($dose_base_id==2) $recipe_qty=number_format(($new_batch_weight*$prev_ratio)/100,4);
				$adj_type=$increase_decrease[$adj_type];
				//$ratio=$adj_perc;
			}
		?>
        <tbody>
			<tr bgcolor="<? echo $bgcolor; ?>">
                <td align="center"><? echo $i; ?></td>
                <td><? echo $item_category[$row[csf("item_category")]];  //echo $row[csf("sub_process")]; ?></td>
                <td><? echo $group_arr[$row[csf("item_group_id")]]; ?></td>
              
                <td><? echo $row[csf("item_description")].' '.$row[csf("item_size")]; ?></td>
                
                <td align="center"><? echo $unit_of_measurement[$row[csf("unit_of_measure")]]; ?></td>
                <td><? echo $dose_base[$row[csf("dose_base")]]; ?></td>
                <td align="center"><? echo number_format($ratio,6,'.',''); ?></td>
                <td align="right"><? echo $recipe_qty; ?></td>
                <td align="center"><? echo $row[csf("adj_perc")]; ?></td>
                <td><? echo $adj_type; ?></td>
              
                <td align="right"><? echo number_format($issue_qty,6,'.',''); ?></td>
                
                <td align="right"><? echo  number_format($issue_cons_rate,6,'.',''); ?></td>
                <td align="right"><? $amount_req_value=$issue_qty*$issue_cons_rate; echo number_format($amount_req_value,6,'.',''); ?></td>
			</tr>
        </tbody>
		<? $i++;
        $recipe_qnty_sum +=$recipe_qty;
        $req_qny_issue_sum +=$issue_qty;
		$amount_req_value_sum +=$amount_req_value;
		
		$recipe_qnty_grand +=$recipe_qty;
        $req_qny_issue_grand_top +=$issue_qty;
		$amount_req_value_grand_top +=$amount_req_value;
        }
		
		?>
           <tr>
                <td colspan="7" align="right"><strong>Total :</strong></td>
                <td align="right"><?php echo number_format($recipe_qnty_sum,6,'.',''); ?></td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                
                <td align="right"><?php echo number_format($req_qny_issue_sum,6,'.',''); ?></td>
               
                <td>&nbsp;</td>
                <td align="right"><?php echo number_format($amount_req_value_sum,6,'.',''); ?></td>
            </tr> 
             <tr>
                <td colspan="7" align="right"><strong> Grand Total :</strong></td>
                <td align="right"><?php echo number_format($recipe_qnty_grand,6,'.',''); ?></td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
               
                <td align="right"><?php echo number_format($req_qny_issue_grand_top,6,'.',''); ?></td>
                <td>&nbsp;</td>
                
                <td align="right"><?php echo number_format($amount_req_value_grand_top,6,'.',''); ?></td>
            </tr> 
               <tr style="">
                <td colspan="11" align="right"><strong> Cost Per Kg :</strong></td>
                <td colspan="2" align="right"><?php echo number_format($amount_req_value_grand_top/($new_batch_weight),6,'.',''); ?></td>
            </tr>                          
      	</table>
	  </div> <!--Req No End-->
		 
      </div>
          
	<?
	}
	 
    $html=ob_get_contents();
			ob_flush();
			
			foreach (glob(""."*.xls") as $filename) 
			{
			   @unlink($filename);
			}
			
			//html to xls convert
			$name=time();
			$name=$user_id."_".$name.".xls";
			$create_new_excel = fopen(''.$name, 'w');	
			$is_created = fwrite($create_new_excel,$html);
	
	?>
      <input type="hidden" id="txt_excl_link" value="<? echo 'requires/'.$name; ?>" />
    <script>
	$(document).ready(function(e) {
				document.getElementById('report_container').innerHTML='<a href="<? echo $name?>" style="text-decoration:none"><input type="button" value="Excel Download" name="excel" id="excel" class="formbutton" style="width:90px;font-size:11px"/></a>&nbsp;&nbsp;';
		});	
	</script>
      </div> 
    <?
	exit();
	
}
?>