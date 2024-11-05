<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_id", 120, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name", 1, "--Select Buyer--", $selected, "" );   	 
	exit();
}

if($action=="job_no_popup")
{
	echo load_html_head_contents("Job Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	list($companyID,$buyer_name,$cbo_year_id)=explode('_',$data);
	?>
	<script>
	
	function js_set_value( job_id )
	{
		//alert(po_id)
		document.getElementById('txt_job_id').value=job_id;
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
                    <th><input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;" onClick="reset_form('styleRef_form','search_div','','','','');"></th> 					<input type="hidden" name="hide_job_id" id="hide_job_id" value="" />
                    <input type="hidden" name="hide_job_no" id="hide_job_no" value="" />
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
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_year_id; ?>', 'job_no_popup_search_list_view', 'search_div', 'wo_or_fabric_booking_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
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


if ($action=="job_no_popup_search_list_view")
{
	//echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	list($companyID,$buyer_name,$search_type,$search_value,$cbo_year_id)=explode('**',$data);
?>	
     <input type="hidden" id="txt_job_id" />
 <?
	if ($companyID==0) $company_id=""; else $company_id=" and company_name=$companyID";
	if ($buyer_name==0) $buyer_id=""; else $buyer_id=" and buyer_name=$buyer_name";
	if($db_type==0)
	{
		if ($cbo_year_id==0) $year_id_cond=""; else $year_id_cond=" and YEAR(insert_date)=$cbo_year_id";
	}
	elseif($db_type==2)
	{
		if ($cbo_year_id==0) $year_id_cond=""; else $year_id_cond=" and TO_CHAR(insert_date,'YYYY')=$cbo_year_id";
	}
	
	if($db_type==0)
	{
		$year=" YEAR(insert_date) as year";
	}
	elseif($db_type==2)
	{
		$year=" TO_CHAR(insert_date,'YYYY') as year";
	}
	
	if($search_type==1 && $search_value!=''){
		$search_con=" and job_no like('%$search_value')";	
	}
	else if($search_type==2 && $search_value!=''){
		$search_con=" and style_ref_no like('%$search_value%')";	
	}
	
	
	
	$buyerArr = return_library_array("select id,buyer_name from lib_buyer ","id","buyer_name");
	
	$sql= "select id, job_no, $year, job_no_prefix_num, style_ref_no, buyer_name, style_description from wo_po_details_master where status_active=1 and is_deleted=0 $company_id $buyer_id $search_con $year_id_cond order by id DESC";
	
	$arr=array(3=>$buyerArr);
	echo  create_list_view("list_view", "Job No,Year,Style Ref.,Buyer,Style Description", "70,70,130,140,170","630","320",0, $sql , "js_set_value", "id,job_no", "", 1, "0,0,0,buyer_name,0", $arr , "job_no_prefix_num,year,style_ref_no,buyer_name,style_description", "",'setFilterGrid("list_view",-1);','0,0,0,0,0','') ;
	
	exit();
}

if($action=="wo_no_popup")
{
  	echo load_html_head_contents("Order Info","../../../", 1, 1, '','1','');
	extract($_REQUEST);
	$ex_data=explode('_',$data);
	$company_id=$ex_data[0];
	$buyer_id=$ex_data[1];
	$year_id=$ex_data[2];
	$category_id=$ex_data[3];
	// $wo_type=$ex_data[4];
?>
	<script>
		function js_set_value(wo_id,wo_no)
		{
			document.getElementById('txt_wo_no').value=wo_no;
			document.getElementById('txt_wo_id').value=wo_id;
			parent.emailwindow.hide();
		}
    </script>
</head>
<body>
	<fieldset style="width:620px;margin-left:10px">
        <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
            <table cellpadding="0" cellspacing="0" border="1" rules="all" width="620" class="rpt_table">
                <thead>
                    <th>Buyer</th>
                    <th>Search</th>
                    <th>
                        <input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
                        <input type="hidden" name="txt_wo_no" id="txt_wo_no" value="" style="width:50px">
                        <input type="hidden" name="txt_wo_id" id="txt_wo_id" value="" style="width:50px">
                    </th> 
                </thead>
                <tr class="general">
                    <td align="center">
                        <?
							echo create_drop_down( "cbo_buyer_id", 130, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$company_id' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name", 1, "--Select Buyer--", $buyer_id, "" );   	 
                        ?>       
                    </td>
                    <td align="center">				
                        <input type="text" style="width:150px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />	
                    </td> 						
                    <td align="center">
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_buyer_id').value+'_'+<? echo $company_id; ?>+'_'+document.getElementById('txt_search_common').value+'_'+<? echo $year_id; ?>+'_'+<? echo $category_id; ?>, 'create_wo_search_list_view', 'search_div', 'service_receive_status_report_controller', 'setFilterGrid(\'list_view\',-1);')" style="width:100px;" />
                    </td>
                </tr>
            </table>
            <div id="search_div" style="margin-top:10px"></div>   
        </form>
    </fieldset>
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}

if ($action=="create_wo_search_list_view")
{
	$data=explode('_',$data);//var_dump($data);
	$item_category=$data[4];
	if($item_category==2 || $item_category==3 || $item_category==4 || $item_category==12)
	{
		if ($data[0]==0) $buyer_id=""; else $buyer_id=" and buyer_id=$data[0]";
		if ($data[1]==0) $company_id=""; else $company_id=" and company_id=$data[1]";
		if ($data[2]==0) $search_wo=""; else $search_wo=" and booking_no_prefix_num=$data[2]";
		
		if($db_type==0)
		{
			if ($data[3]==0) $year_id_cond=""; else $year_id_cond=" and YEAR(insert_date)=$data[3]";
		}
		elseif($db_type==2)
		{
			if ($data[3]==0) $year_id_cond=""; else $year_id_cond=" and TO_CHAR(insert_date,'YYYY')=$data[3]";
		}
		
		if ($data[4]==0) $category_id_cond=""; else $category_id_cond=" and item_category=$data[4]";
		if ($data[5]==1 || $data[5]==2)  $wo_type_cond=" and booking_type in (1,2) and is_short='$data[5]'"; else $wo_type_cond="";
		if ($data[5]==3) $wo_type_cond_sam="  and booking_type=4"; else $wo_type_cond_sam="";
		
		if($db_type==0)
		{
			$year=" YEAR(insert_date) as year";
		}
		elseif($db_type==2)
		{
			$year=" TO_CHAR(insert_date,'YYYY') as year";
		}
		
		$buyerArr = return_library_array("select id,buyer_name from lib_buyer ","id","buyer_name");
		if($data[5]==0)
		{
			$sql= "select id, booking_no, $year, booking_no_prefix_num, booking_date, buyer_id, booking_type, is_short, 0 as type from wo_booking_mst where status_active=1 and is_deleted=0 $company_id $buyer_id $year_id_cond $search_wo $category_id_cond $wo_type_cond $wo_type_cond_sam
			union all
			SELECT id, booking_no, $year, booking_no_prefix_num, booking_date, buyer_id, booking_type, is_short, 1 as type from wo_non_ord_samp_booking_mst where status_active=1 and is_deleted=0 $company_id $buyer_id $year_id_cond $search_wo $category_id_cond order by id Desc";
		}
		else if ($data[5]==1 || $data[5]==2 || $data[5]==3)
		{
			$sql= "select id, booking_no, $year, booking_no_prefix_num, booking_date, buyer_id, booking_type, is_short, 0 as type from wo_booking_mst where status_active=1 and is_deleted=0 $company_id $buyer_id $year_id_cond $search_wo $category_id_cond $wo_type_cond $wo_type_cond_sam order by id Desc";
		}
		else
		{
			$sql= "SELECT id, booking_no, $year, booking_no_prefix_num, booking_date, buyer_id, booking_type, is_short, 1 as type from wo_non_ord_samp_booking_mst where status_active=1 and is_deleted=0 $company_id $buyer_id $year_id_cond $search_wo $category_id_cond order by id Desc";
		}
		//echo $sql;
	?>
		<div>
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="600" class="rpt_table" >
				<thead>
					<th width="30">SL</th>
					<th width="80">WO No</th>
					<th width="80">Year</th>
					<th width="130">WO Type</th>
					<th width="150">Buyer</th>
					<th width="100">WO Date</th>
				</thead>
			</table>
			<div style="width:618px; overflow-y:scroll; max-height:300px;" id="" align="center">
				<table cellspacing="0" cellpadding="0" border="1" rules="all" width="600" class="rpt_table" id="list_view" >
				<?
					$i=1;
					$nameArray=sql_select( $sql );
					foreach ($nameArray as $selectResult)
					{
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						
						if ($selectResult[csf("type")]==0)
						{	
							if ($selectResult[csf("booking_type")]==1 || $selectResult[csf("booking_type")]==2)
							{
								if ($selectResult[csf("is_short")]==1)
								{
									$wo_type="Short";
								}
								else
								{
									$wo_type="Main";
								}
							}
							elseif($selectResult[csf("booking_type")]==4)
							{
								$wo_type="Sample With Order";
							}
						}
						else
						{
							$wo_type="Sample Non Order";
						}					
					?>
						<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $selectResult[csf('id')]; ?>,'<? echo $selectResult[csf('booking_no')]; ?>')"> 
							<td width="30" align="center"><? echo $i; ?></td>	
							<td width="80" align="center"><p><? echo $selectResult[csf('booking_no_prefix_num')]; ?></p></td>
							<td width="80" align="center"><? echo $selectResult[csf('year')]; ?></td>
							<td width="130"><p><? echo $wo_type; ?></p></td>
							<td width="150"><p><? echo $buyerArr[$selectResult[csf('buyer_id')]]; ?></p></td>
							<td  width="100" align="center"><? echo change_date_format($selectResult[csf('booking_date')]); ?></td>	
						</tr>
					<?
						$i++;
					}
				?>
				</table>
			</div>
		</div>           
		<?
	}
	else if($item_category==24 || $item_category==31)
	{
		if ($data[0]==0) $buyer_id=""; else $buyer_id=" and buyer_id=$data[0]";
		if ($data[1]==0) $company_id=""; else $company_id=" and company_id=$data[1]";
		if ($data[2]==0) $search_wo=""; else $search_wo=" and yarn_dyeing_prefix_num=$data[2]";
		
		if($db_type==0)
		{
			if ($data[3]==0) $year_id_cond=""; else $year_id_cond=" and YEAR(insert_date)=$data[3]";
		}
		elseif($db_type==2)
		{
			if ($data[3]==0) $year_id_cond=""; else $year_id_cond=" and TO_CHAR(insert_date,'YYYY')=$data[3]";
		}
		
		if ($data[4]==0) $category_id_cond=""; else $category_id_cond=" and item_category_id=24";
		if ($data[5]==2) $wo_type_cond=" and entry_form=79"; else if ($data[5]==4) $wo_type_cond=" and entry_form=179"; else $wo_type_cond="";
		//if ($data[5]==3) $wo_type_cond_sam="  and booking_type=4"; else $wo_type_cond_sam="";
		
		if($db_type==0) $year=" YEAR(insert_date) as year";
		elseif($db_type==2) $year=" TO_CHAR(insert_date,'YYYY') as year";
		
		$supplierArr = return_library_array("select id,supplier_name from lib_supplier","id","supplier_name");
		
		if($item_category==24){
		$sql= "select id, ydw_no, $year, yarn_dyeing_prefix_num, booking_date, supplier_id, entry_form from wo_yarn_dyeing_mst where status_active=1 and is_deleted=0 $company_id $year_id_cond $search_wo $category_id_cond $wo_type_cond order by id Desc";
		}
		else if($item_category==31)
		{
		$sql= "select id, labtest_no as ydw_no, $year, labtest_prefix_num as yarn_dyeing_prefix_num, wo_date as booking_date, supplier_id, entry_form from wo_labtest_mst where status_active=1 and is_deleted=0 $company_id $year_id_cond $search_wo $wo_type_cond order by id Desc";
		}
		
	?>
		<div>
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="600" class="rpt_table" >
				<thead>
					<th width="30">SL</th>
					<th width="80">WO No</th>
					<th width="80">Year</th>
					<th width="130">WO Type</th>
					<th width="150">Supplier</th>
					<th width="100">WO Date</th>
				</thead>
			</table>
			<div style="width:618px; overflow-y:scroll; max-height:300px;" id="" align="center">
				<table cellspacing="0" cellpadding="0" border="1" rules="all" width="600" class="rpt_table" id="list_view" >
				<?
					$i=1;
					$nameArray=sql_select( $sql );
					foreach ($nameArray as $selectResult)
					{
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						
						if($selectResult[csf("entry_form")]==41) $wo_type="Order";
						else if($selectResult[csf("entry_form")]==42) $wo_type="Non Order";
						else $wo_type="";				
					?>
						<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $selectResult[csf('id')]; ?>,'<? echo $selectResult[csf('ydw_no')]; ?>')"> 
							<td width="30" align="center"><? echo $i; ?></td>	
							<td width="80" align="center"><p><? echo $selectResult[csf('yarn_dyeing_prefix_num')]; ?></p></td>
							<td width="80" align="center"><? echo $selectResult[csf('year')]; ?></td>
							<td width="130"><p><? echo $wo_type; ?></p></td>
							<td width="150"><p><? echo $supplierArr[$selectResult[csf('supplier_id')]]; ?></p></td>
							<td  width="100" align="center"><? echo change_date_format($selectResult[csf('booking_date')]); ?></td>	
						</tr>
					<?
						$i++;
					}
				?>
				</table>
			</div>
		</div>           
		<?
	}
	exit();
}


if($action=="po_no_popup")
{

	echo load_html_head_contents("Order No Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	list($companyID,$buyer,$job)=explode('_',$data);
	?>
     
	<script>
		
	var selected_id = new Array, selected_name = new Array(); selected_attach_id = new Array();
	 
	function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		
	function js_set_value(id)
	{
		//alert (id)
		var str=id.split("_");
		toggle( document.getElementById( 'tr_' + str[0] ), '#FFFFFF' );
		var strdt=str[2];
		str=str[1];
	
		if( jQuery.inArray(  str , selected_id ) == -1 ) {
			selected_id.push( str );
			selected_name.push( strdt );
		}
		else {
			for( var i = 0; i < selected_id.length; i++ ) {
				if( selected_id[i] == str  ) break;
			}
			selected_id.splice( i, 1 );
			selected_name.splice( i,1 );
		}
		var id = '';
		var ddd='';
		for( var i = 0; i < selected_id.length; i++ ) {
			id += selected_id[i] + ',';
			ddd += selected_name[i] + ',';
		}
		id = id.substr( 0, id.length - 1 );
		ddd = ddd.substr( 0, ddd.length - 1 );
		$('#txt_po_id').val( id );
		$('#txt_po_val').val( ddd );
	} 
		  
	
    </script>

</head>

<body>
<div align="center">
	<form name="styleRef_form" id="styleRef_form">
		<fieldset>
            <table cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
            	<thead>
                    <th>Buyer</th>
                    <th>Search By</th>
                    <th id="search_by_td_up" width="130">Please Enter Order No</th>
                    <th>Shipment Date</th>
                    <th><input type="reset" name="button" class="formbutton" value="Reset"  style="width:100px;"></th> 
                    <input type="hidden" name="hide_order_no" id="hide_order_no" value="" />
                    <input type="hidden" name="hide_order_id" id="hide_order_id" value="" />
                </thead>
                <tbody>
                	<tr>
                        <td align="center">
                        	 <? 
								echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer,"",0 );
							?>
                        </td>                 
                        <td align="center">	
                    	<?
                       		$search_by_arr=array(1=>"Order No",2=>"Style Ref",3=>"Job No");
							$dd="change_search_event(this.value, '0*0*0', '0*0*0', '../../') ";							
							echo create_drop_down( "cbo_search_by", 80, $search_by_arr,"",0, "--Select--", "",$dd,0 );
						?>
                        </td>     
                        <td align="center" id="search_by_td">				
                            <input type="text" style="width:80px" class="text_boxes" name="txt_search_common" id="txt_search_common" />	
                        </td> 
                        <td align="center">
                            <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" readonly>To
                            <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px" readonly>
                        </td>	
                        <td align="center">
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+'<? echo $job; ?>'+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+document.getElementById('txt_date_from').value+'**'+document.getElementById('txt_date_to').value, 'po_no_popup_list_view', 'search_div', 'wo_or_fabric_booking_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
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


if ($action=="po_no_popup_list_view")
{
	//echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	//echo $data;
	list($company,$buyer,$job,$search_type,$search_value,$start_date,$end_date)=explode('**',$data);
	//print_r ($data);
	
?>	
     <input type="hidden" id="txt_po_id" />
     <input type="hidden" id="txt_po_val" />
     <?
	 if ($company==0) $company_name=""; else $job_num=" and a.company_name='$company'";
	 if ($buyer==0) $buyer_name=""; else $buyer_name=" and a.buyer_name='$buyer'";
	 if ($job=="") $job_num=""; else $job_num=" and b.job_no_mst='$job'";
	
	if($search_type==1 && $search_value!=''){
		$search_con=" and b.po_number like('%".trim($search_value)."')";	
	}
	elseif($search_type==2 && $search_value!=''){
		$search_con=" and a.style_ref_no like('%".trim($search_value)."')";		
	}
	elseif($search_type==3 && $search_value!=''){
		$search_con=" and b.job_no_mst like('%".trim($search_value)."')";		
	}
	
	if($start_date!="" && $end_date!="")
	{
		if($db_type==0)
		{
			$date_cond="and b.pub_shipment_date between '".change_date_format(trim($start_date),"yyyy-mm-dd")."' and '".change_date_format(trim($end_date),"yyyy-mm-dd")."'";
		}
		else
		{
			$date_cond="and b.pub_shipment_date between '".change_date_format(trim($start_date),'','',1)."' and '".change_date_format(trim($end_date),'','',1)."'";	
		}
	}
	else
	{
		$date_cond="";
	}
	
	
	
	$sql= "select b.id, b.po_number, b.job_no_mst, b.pub_shipment_date,c.gmts_item_id from wo_po_details_master a, wo_po_break_down  b, wo_po_details_mas_set_details c where a.job_no=b.job_no_mst and a.job_no=c.job_no and a.status_active=1 and a.is_deleted=0 $job_num $company_name $buyer_name $search_con $date_cond order by b.id Desc";
	
	$arr=array(3=>$garments_item);
	echo  create_list_view("list_view", "PO No.,Job No.,Pub Shipment Date,Item Name", "100,100,80,150","450","240",0, $sql , "js_set_value", "id,po_number", "", 1, "0,0,0,gmts_item_id", $arr , "po_number,job_no_mst,pub_shipment_date,gmts_item_id", "",'setFilterGrid("list_view",-1);','0,0,3,0','',1) ;
	exit();	 
}

if ($action=="report_generate")
{
	//extract($_REQUEST);
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$cbo_company=str_replace("'","",$cbo_company_id);
	$cbo_buyer=str_replace("'","",$cbo_buyer_id);
	$cbo_category=str_replace("'","",$cbo_category_id);
	$wo_type=str_replace("'","",$cbo_wo_type);
	$year_id=str_replace("'","",$cbo_year_id);
	$job_year_id=str_replace("'","",$cbo_job_year_id);
	$wo_no=str_replace("'","",$txt_wo_no);
	$wo_id=str_replace("'","",$hidd_wo_id);
	
	$job_no=str_replace("'","",$txt_job_no);
	$hidd_job=str_replace("'","",$hidd_job_id);
	$po_no=str_replace("'","",$txt_po_no);
	$hidd_po=str_replace("'","",$hidd_po_id);
	$date_from=str_replace("'","",$txt_date_from);
	$date_to=str_replace("'","",$txt_date_to);
	$file_no=str_replace("'","",$txt_file_no);
	$internal_ref=str_replace("'","",$txt_internal_ref);
	$txt_date_category=str_replace("'","",$txt_date_category);
	$cbo_order_status=str_replace("'","",$cbo_order_status);
	
	
	if($cbo_category==2 || $cbo_category==3 || $cbo_category==4 || $cbo_category==12 || $cbo_category==25)
	{	
		if($db_type==0)
		{
			if ($year_id==0) $year_id_cond=""; else $year_id_cond=" and YEAR(a.insert_date)=$year_id";
			if ($year_id==0) $year_id_cond_sam=""; else $year_id_cond_sam=" and YEAR(s.insert_date)=$year_id";
		}
		elseif($db_type==2)
		{ 
			if ($year_id==0) $year_id_cond=""; else $year_id_cond=" and TO_CHAR(a.insert_date,'YYYY')=$year_id";
			if ($year_id==0) $year_id_cond_sam=""; else $year_id_cond_sam=" and TO_CHAR(s.insert_date,'YYYY')=$year_id";
		}
		
		if ($cbo_company==0) $company_id=""; else $company_id=" and a.company_id=$cbo_company";
		if ($cbo_company==0) $company_id_sam=""; else $company_id_sam=" and s.company_id=$cbo_company";
		if ($cbo_buyer==0) $buyer_id=""; else $buyer_id=" and a.buyer_id=$cbo_buyer";
		if ($cbo_buyer==0) $buyer_id_sam=""; else $buyer_id_sam=" and s.buyer_id=$cbo_buyer";
		if ($cbo_category==0) $category_id=""; else $category_id=" and a.item_category=$cbo_category";
		if ($cbo_category==0) $category_id_sam=""; else $category_id_sam=" and s.item_category=$cbo_category";
		if ($wo_no=="") $wo_no_cond=""; else $wo_no_cond=" and a.booking_no='$wo_no'";
		if ($wo_no=="") $wo_no_cond_sam=""; else $wo_no_cond_sam=" and s.booking_no='$wo_no'";
		
		if ($wo_type==1 || $wo_type==2)  $wo_type_cond=" and a.booking_type in (1,2) and a.is_short='$wo_type'"; else $wo_type_cond="";
		if ($wo_type==3) $wo_type_cond_sam="  and a.booking_type=4"; else $wo_type_cond_sam="";
		
		if ($job_no=="") $job_num=""; else $job_num=" and b.job_no='$job_no'";
		//if ($po_no=='') $po_no_no=""; else $po_no_no=" and d.po_number in ($po_no)";
		if ($hidd_po=="") $po_id=""; else $po_id=" and d.id in ( $hidd_po )";
		
		//if ($hidd_po=="") $po_id_cond=""; else $po_id_cond=" and b.po_break_down_id in ( $hidd_po )";
		
		if ($file_no=="") $file_no_cond=""; else $file_no_cond=" and d.file_no='".trim($file_no)."' "; 
		if ($internal_ref=="") $internal_ref_cond=""; else $internal_ref_cond=" and d.grouping='".trim($internal_ref)."' "; 
		//echo $file_no_cond.'=='.$internal_ref_cond;die;
		
		if($db_type==0)
		{
			if($txt_date_category==1){
				if( $date_from=="" && $date_to=="" ) $booking_date_cond=""; else $booking_date_cond=" and a.booking_date between '".$date_from."' and '".$date_to."'";
				if( $date_from=="" && $date_to=="" ) $booking_date_cond_samp=""; else $booking_date_cond_samp=" and s.booking_date between '".$date_from."' and '".$date_to."'";
			}
			else if($txt_date_category==2){
				if( $date_from=="" && $date_to=="" ) $booking_date_cond=""; else $booking_date_cond=" and a.delivery_date between '".$date_from."' and '".$date_to."'";
				if( $date_from=="" && $date_to=="" ) $booking_date_cond_samp=""; else $booking_date_cond_samp=" and s.delivery_date between '".$date_from."' and '".$date_to."'";
			}
		}
		if($db_type==2)
		{
			if($txt_date_category==1){
				if( $date_from=="" && $date_to=="" ) $booking_date_cond=""; else $booking_date_cond=" and a.booking_date between '".change_date_format($date_from,'dd-mm-yyyy','-',1)."' and '".change_date_format($date_to,'dd-mm-yyyy','-',1)."'";
				if( $date_from=="" && $date_to=="" ) $booking_date_cond_samp=""; else $booking_date_cond_samp=" and s.booking_date between '".change_date_format($date_from,'dd-mm-yyyy','-',1)."' and '".change_date_format($date_to,'dd-mm-yyyy','-',1)."'";
			}
			else if($txt_date_category==2){
				if( $date_from=="" && $date_to=="" ) $booking_date_cond=""; else $booking_date_cond=" and a.delivery_date between '".change_date_format($date_from,'dd-mm-yyyy','-',1)."' and '".change_date_format($date_to,'dd-mm-yyyy','-',1)."'";
				if( $date_from=="" && $date_to=="" ) $booking_date_cond_samp=""; else $booking_date_cond_samp=" and s.delivery_date between '".change_date_format($date_from,'dd-mm-yyyy','-',1)."' and '".change_date_format($date_to,'dd-mm-yyyy','-',1)."'";
			
			}
		}
	
		$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name");
		$supplierArr=return_library_array( "select id,supplier_name from lib_supplier", "id", "supplier_name");	
		$buyerArr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name");
		$nonStyleArr=return_library_array( "select id, style_ref_no from sample_development_mst", "id", "style_ref_no");		
		if ($cbo_category==4) 
		{
			$colspan=19;
			$tbl_width=1870;
		}
		else if ($cbo_category==12)
		{
			$colspan=22;
			$tbl_width=2070;
		}
		else
		{
			$colspan=21;
			$tbl_width=1970;
		}
		
		  /*$sql="select a.job_no,d.id as po_id from wo_po_details_master a, wo_po_break_down d where a.job_no=d.job_no_mst and a.status_active=1 and a.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $internal_ref_cond  $file_no_cond  group by a.job_no,d.id";
			$sql_result=sql_select($sql);
			$po_id_data="";$k=0;
			foreach($sql_result as $row)
			{
				if($k==0)
				{
					$po_data_cond=$row[csf('po_id')];
					 $job_data_cond=$row[csf('job_no')];
				}
				else
				{
					 $po_data_cond.=",".$row[csf('po_id')];
					 $job_data_cond.=",".$row[csf('job_no')];
				} 
			} //echo $job_data_cond;
			if($file_no_cond!="" || $internal_ref!="")
			{  
			 $po_data_file_ref=" and b.po_break_down_id in($po_data_cond) "; 
			 $job_data_file_ref=" and b.job_no in('$job_data_cond') "; 
			}
			else
			{
				$po_data_file_ref="";
				$job_data_file_ref="";
			}*/
			
		/*if($db_type==0)
		{
			 $job_sql="select a.job_no_prefix_num, a.job_no, Year(a.insert_date) as year, a.style_ref_no, a.garments_nature, 
			group_concat(b.id) as id,
			group_concat(b.po_number) as po_number,
			group_concat(b.file_no) as file_no,
			group_concat(b.grouping) as grouping,
			max(b.pub_shipment_date) as max_ship_date,
			min(b.pub_shipment_date) as min_ship_date
			
			from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.job_no_prefix_num, a.job_no, a.insert_date, a.style_ref_no, a.garments_nature";
		}
		elseif($db_type==2)
		{
			$job_sql="select a.job_no_prefix_num, a.job_no, TO_CHAR(a.insert_date,'YYYY') as year, a.style_ref_no, a.garments_nature, 
			LISTAGG(CAST(b.id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.id) as id,
			LISTAGG(CAST(b.po_number AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.po_number) as po_number,
			LISTAGG(CAST(b.file_no AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.file_no) as file_no,
			LISTAGG(CAST(b.grouping AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.grouping) as grouping,
			max(b.pub_shipment_date) as max_ship_date,
			min(b.pub_shipment_date) as min_ship_date
			from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.job_no_prefix_num, a.job_no, a.insert_date, a.style_ref_no, a.garments_nature";
		}*/
		
		/*$job_sql="select c.job_no_prefix_num, c.job_no, c.buyer_name, TO_CHAR(c.insert_date,'YYYY') as year, c.style_ref_no, c.garments_nature, d.id as po_break_down_id,
			d.po_number as po_number, d.file_no as file_no, d.grouping as grouping, d.pub_shipment_date
			from wo_po_details_master c, wo_po_break_down d where c.job_no=d.job_no_mst and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0";
		
		$job_sql_result=sql_select($job_sql);$ship_date_arr=array();
		foreach($job_sql_result as $row)
		{
			//$order_no=implode(",",array_unique(explode(",",$row[csf("po_number")])));
			//$order_id=implode(",",array_unique(explode(",",$row[csf("id")])));
			//$file_no=implode(",",array_unique(explode(",",$row[csf("file_no")])));
			//$int_ref=implode(",",array_unique(explode(",",$row[csf("grouping")])));
	
			$job_array[$row[csf("po_break_down_id")]]['job']=$row[csf("job_no_prefix_num")];
			$job_array[$row[csf("po_break_down_id")]]['job_no']=$row[csf("job_no")];
			$job_array[$row[csf("po_break_down_id")]]['buyer']=$row[csf("buyer_name")];
			$job_array[$row[csf("po_break_down_id")]]['year']=$row[csf("year")];
			$job_array[$row[csf("po_break_down_id")]]['style_ref_no']=$row[csf("style_ref_no")];
			$job_array[$row[csf("po_break_down_id")]]['garments_nature']=$row[csf("garments_nature")];
			$job_array[$row[csf("po_break_down_id")]]['id']=$row[csf("po_break_down_id")];
			$job_array[$row[csf("po_break_down_id")]]['po_number']=$row[csf("po_number")];
			$job_array[$row[csf("po_break_down_id")]]['file']=$row[csf("file_no")];
			$job_array[$row[csf("po_break_down_id")]]['ref']=$row[csf("grouping")];
			$job_array[$row[csf("po_break_down_id")]]['pub_shipment_date']=change_date_format($row[csf("pub_shipment_date")]);
		}
		*/
		
		$emb_item_array=array();
		
		$emb_sql="select a.emb_name, b.item_number_id, c.job_no_mst from wo_pre_cost_embe_cost_dtls a, wo_po_color_size_breakdown b, wo_po_break_down c where a.job_no=b.job_no_mst and c.id=b.po_break_down_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
		$emb_sql_result=sql_select($emb_sql);
		foreach($emb_sql_result as $row)
		{
			$emb_item_array[$row[csf("job_no_mst")]]['emb_name']=$row[csf("emb_name")];
			$emb_item_array[$row[csf("job_no_mst")]]['item_number_id']=$row[csf("item_number_id")];
		}
		//var_dump($job_array);
		if($cbo_category==2 || $cbo_category==3)
		{
			 $print_report_format_ids=return_field_value("format_id","lib_report_template","template_name=".$cbo_company."  and module_id=2 and report_id=1 and is_deleted=0 and status_active=1");
			$format_ids=explode(",",$print_report_format_ids);
				
		}
		if($cbo_category==4)
		{
			 $print_report_format_ids=return_field_value("format_id","lib_report_template","template_name=".$cbo_company."  and module_id=2 and report_id in(5,6) and is_deleted=0 and status_active=1");
			$format_ids=explode(",",$print_report_format_ids);
			//$last_print_button=array_shift($format_ids);
			//print_r($format_ids);	
		}
		if($cbo_category==12) // Service Fab. Booking.
		{
		$print_report_format_ids=return_field_value("format_id","lib_report_template","template_name ='".$cbo_company."'   and module_id=2 and report_id=11 and is_deleted=0 and status_active=1");
		$format_ids=explode(",",$print_report_format_ids);
		}
		
		$first_print_button=array_shift($format_ids);
		
	  //echo $first_print_button.'gg';
		ob_start();
		?>
		<fieldset>
			<table width="<? echo $tbl_width; ?>"  cellspacing="0" >
				<tr class="form_caption" style="border:none;">
					<td align="center" style="border:none; font-size:18px;" colspan="<? echo $colspan; ?>">
						<? echo $company_library[str_replace("'","",$cbo_company_id)]; ?>                                
					</td>
				</tr>
				<tr class="form_caption" style="border:none;">
					<td align="center" style="border:none;font-size:16px; font-weight:bold"  colspan="<? echo $colspan; ?>"> <? echo $report_title; if($cbo_category!=0) echo " For ".$item_category[$cbo_category]; ?></td>
				</tr>
				<tr class="form_caption" style="border:none;">
					<td align="center" style="border:none;font-size:12px; font-weight:bold"  colspan="<? echo $colspan; ?>"> <? if( $date_from!="" && $date_to!="" ) echo "From ".change_date_format($date_from)." To ".change_date_format($date_to);?></td>
				</tr>
			</table>
			<table width="<? echo $tbl_width; ?>" cellspacing="0" border="1" class="rpt_table" rules="all">
				<thead>
					<th width="30">SL</th>    
					<th width="60">Transaction Date</th>
                    <?
					if ($cbo_category==12)
					{
						?>
                        <th width="100">Process</th>
                        <?
					}
					?>
					<th width="60">Wo Year</th>
					<th width="70">Wo Date</th>
					<th width="70">Wo Last Update</th>
					<th width="70">Delivery Date</th>
                    <th width="70">Lead Time</th>
					<th width="100">Wo Type</th>
					<th width="100">Ready To Approved</th>
					<th width="80">Approval Status</th>
					<th width="100">Supplier</th>
					<th width="100">Buyer</th>
					<th width="70">Job No.</th>
					<th width="60">Job Year</th>
					<th width="100">Style Ref.</th>
					<th width="100">Internal Ref.</th>
					<th width="100">File No.</th>
					<th width="100">Order No</th>
                    <th width="100">Shipment Date</th>
					<th width="80">Item Category</th>
					<th width="70">First Receive Date</th>
					<?
						if ($cbo_category==4)
						{
							?>
								<th width="100">Wo Amount</th>
							<?
						}
						else
						{
							?>
								<th width="100">Wo Qty (Fin.)</th>
								<th width="100">Wo Qty (Grey)</th>
							<?
						}
					?>
					<th>Remarks</th>
				</thead>
			</table>
			<div style="max-height:350px; overflow-y:scroll; width:<? echo $tbl_width+20; ?>px" id="scroll_body" >
				<table cellspacing="0" cellpadding="0" border="1" class="rpt_table"  width="<? echo $tbl_width; ?>" rules="all" id="table_body" >
				<?
				
				//----------------------
				if($cbo_category==2 || $cbo_category==3 || $cbo_category==4 ||  $cbo_category==25)
				{
					
					if($cbo_category==2)
					{
						$booking_check_prod=array();
						$prod_1st_sql=sql_select("select a.id as book_id, d.booking_without_order, d.receive_date as receive_date, c.cons_quantity as rcv_qnty from  wo_booking_mst a, pro_batch_create_mst b , inv_transaction c, inv_receive_master d where a.id=b.booking_no_id and b.id=c.pi_wo_batch_no and c.mst_id=d.id and a.item_category in(2,3)  and c.pi_wo_batch_no>0 and c.status_active=1 and c.item_category in(2,3) and c.transaction_type=1");
						$first_rec_date_prod=array();
						foreach($prod_1st_sql as $row)
						{
							//$first_rec_date_prod[$row[csf("book_id")]]=$row[csf("receive_date")];
							$receive_data_pord[$row[csf("book_id")]][$row[csf("booking_without_order")]].=$row[csf("receive_date")].",";
							$receive_qnty_data_prod[$row[csf("book_id")]][$row[csf("booking_without_order")]][$row[csf("receive_date")]]+=$row[csf("rcv_qnty")];
							if($booking_check[$row[csf("book_id")]]=="")
							{
								$first_rec_date_prod[$row[csf("book_id")]]=$row[csf("receive_date")];
								$booking_check_prod[$row[csf("book_id")]]=$row[csf("receive_date")];
							}
						}
						
						
						$sql_receive="select b.booking_id, b.receive_date, b.booking_without_order, c.cons_quantity as rcv_qnty from inv_receive_master b, inv_transaction c where b.id=c.mst_id and c.transaction_type=1 and b.item_category in(2,3) and b.company_id=$cbo_company and b.receive_basis in(12,2)";
						//echo $sql_receive ;die;	
						$receive_result=sql_select($sql_receive);
						$receive_data=$receive_qnty_data=$booking_check=array();
						foreach($receive_result as $row)
						{
							$receive_data[$row[csf("booking_id")]][$row[csf("booking_without_order")]].=$row[csf("receive_date")].",";
							$receive_qnty_data[$row[csf("booking_id")]][$row[csf("booking_without_order")]][$row[csf("receive_date")]]+=$row[csf("rcv_qnty")];
							if($booking_check[$row[csf("booking_id")]]=="")
							{
								$first_rec_date[$row[csf("booking_id")]]=$row[csf("receive_date")];
								$booking_check[$row[csf("booking_id")]]=$row[csf("receive_date")];
							}
						}
						
					}
					else
					{
						
						/*$sql="select a.id,a.booking_type,min(b.receive_date) as receive_date from wo_booking_mst a,inv_receive_master b where a.booking_no=b.booking_no and a.item_category in(4) and b.company_id=$cbo_company and b.receive_basis=2 group by a.id,a.booking_no,a.booking_type";	
						$sql_result=sql_select($sql);
						foreach($sql_result as $row)
						{
							$first_rec_date[$row[csf("id")]]=$row[csf("receive_date")];
						}*/
						
						$sql="select b.pi_wo_batch_no, min(a.receive_date) as receive_date from inv_receive_master a, inv_transaction b where a.id=b.mst_id and a.entry_form=24 and  b.item_category in(4) and b.company_id=$cbo_company  and b.transaction_type=1 and b.pi_wo_batch_no>0 and a.receive_basis in(2,12) group by b.pi_wo_batch_no";	
						$sql_result=sql_select($sql);
						foreach($sql_result as $row)
						{
							$first_rec_date[$row[csf("pi_wo_batch_no")]]=$row[csf("receive_date")];
						}
					}
				}
				
				$cbo_fabric_source=str_replace("'","",$cbo_fabric_source);
				$fabric_source_cond="";$fabric_source_cond_nonOrder="";
				if($cbo_category==2)
				{ 
					if($cbo_fabric_source>0) 
					{
						$fabric_source_cond=" and a.fabric_source=$cbo_fabric_source"; 
						$fabric_source_cond_nonOrder=" and s.fabric_source=$cbo_fabric_source"; 
					}
					
				}
				
				//var_dump($receive_qnty_data_prod);die;
				
				/*if($db_type==0)
				{	
					if($wo_type==0)
					{
						if($job_no!='' || $po_no!='')
						{
							$sql="Select a.id as booking_id, a.booking_type, a.is_short, a.booking_no, Year(a.insert_date) as year_book, a.booking_no_prefix_num, a.fabric_source, a.booking_date,a.delivery_date, a.is_approved, a.ready_to_approved, a.supplier_id, a.buyer_id, a.item_category, sum(b.fin_fab_qnty) as fin_fab_qnty, sum(b.grey_fab_qnty) as grey_fab_qnty, sum(b.wo_qnty) as wo_qnty,sum(b.amount) as amount, max(b.uom) as uom, group_concat(b.job_no) as job_no, group_concat(b.po_break_down_id) as po_break_down_id, 0 as type 
							from wo_booking_mst a, wo_booking_dtls b 
							where a.booking_no=b.booking_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $company_id $buyer_id ".set_user_lavel_filtering(' and a.buyer_id','buyer_id')." $category_id $job_num $po_id $booking_date_cond $po_id_cond $wo_no_cond $po_data_file_ref $fabric_source_cond 
							group by  a.id, a.booking_type, a.is_short, a.booking_no, a.insert_date, a.booking_no_prefix_num, a.booking_date,a.delivery_date, a.is_approved, a.supplier_id, a.buyer_id, a.item_category order by a.booking_no";//die;
						}
						else
						{
							 $sql="Select a.id as booking_id, a.booking_type, a.is_short, a.booking_no, Year(a.insert_date) as year_book, a.booking_no_prefix_num, a.fabric_source, a.booking_date,a.delivery_date, a.is_approved, a.ready_to_approved, a.supplier_id, a.buyer_id, a.item_category, sum(b.fin_fab_qnty) as fin_fab_qnty, sum(b.grey_fab_qnty) as grey_fab_qnty, sum(b.wo_qnty) as wo_qnty,sum(b.amount) as amount, max(b.uom) as uom, group_concat(b.job_no) as job_no, group_concat(b.po_break_down_id) as po_break_down_id, 0 as type 
							from wo_booking_mst a, wo_booking_dtls b 
							where a.booking_no=b.booking_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $company_id $buyer_id ".set_user_lavel_filtering(' and a.buyer_id','buyer_id')."  $category_id $job_num $po_id $booking_date_cond $wo_no_cond $job_data_file_ref $fabric_source_cond
							group by a.id, a.booking_type, a.is_short, a.booking_no, a.insert_date, a.booking_no_prefix_num, a.booking_date,a.delivery_date, a.is_approved, a.supplier_id, a.buyer_id, a.item_category 
							 union all
							 SELECT s.id as booking_id, s.booking_type, s.is_short, s.booking_no, Year(s.insert_date) as year_book, s.booking_no_prefix_num, s.fabric_source, s.booking_date,s.delivery_date, s.is_approved, s.ready_to_approved, s.supplier_id, s.buyer_id, s.item_category, sum(t.finish_fabric) as fin_fab_qnty, sum(t.grey_fabric) as grey_fab_qnty, sum(t.trim_qty) as wo_qnty,sum(t.amount) as amount, t.uom, '' as job_no, '' as po_break_down_id, 1 as type 
							 FROM wo_non_ord_samp_booking_mst s, wo_non_ord_samp_booking_dtls t 
							 WHERE s.booking_no=t.booking_no and s.status_active =1 and s.is_deleted=0  and t.status_active =1 and t.is_deleted=0 $company_id_sam $buyer_id_sam ".set_user_lavel_filtering(' and s.buyer_id','buyer_id')." $category_id_sam $booking_date_cond_samp $wo_no_cond_sam $fabric_source_cond_nonOrder 
							 group by s.id, s.booking_type, s.is_short, s.booking_no, s.insert_date, s.booking_no_prefix_num, s.booking_date,s.delivery_date, s.is_approved, s.supplier_id, s.buyer_id, s.item_category";//die;
						}
					}
					else if ($wo_type==1 || $wo_type==2 || $wo_type==3)
					{
						 $sql="Select a.id as booking_id, a.booking_type, a.is_short, a.booking_no, Year(a.insert_date) as year_book, a.booking_no_prefix_num, a.fabric_source, a.booking_date,a.delivery_date, a.is_approved, a.ready_to_approved, a.supplier_id, a.buyer_id, a.item_category, sum(b.fin_fab_qnty) as fin_fab_qnty, sum(b.grey_fab_qnty) as grey_fab_qnty, sum(b.wo_qnty) as wo_qnty,sum(b.amount) as amount, max(b.uom) as uom, group_concat(b.job_no) as job_no, group_concat(b.po_break_down_id) as po_break_down_id, 0 as type 
						 from wo_booking_mst a, wo_booking_dtls b, wo_po_details_master c, wo_po_break_down d 
						 where a.booking_no=b.booking_no and b.po_break_down_id=d.id and d.job_no_mst=c.job_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1  $company_id $buyer_id ".set_user_lavel_filtering(' and a.buyer_id','buyer_id')."  $category_id $job_num $po_id $booking_date_cond $wo_no_cond $wo_type_cond $wo_type_cond_sam $file_no_cond $internal_ref_cond $fabric_source_cond
						 group by a.id, a.booking_type, a.is_short, a.booking_no, a.insert_date, a.booking_no_prefix_num, a.booking_date,a.delivery_date, a.is_approved, a.supplier_id, a.buyer_id, a.item_category order by a.booking_no";//die;
					}
					else
					{
						 $sql="SELECT s.id as booking_id, s.booking_type, s.is_short, s.booking_no, Year(s.insert_date) as year_book, s.booking_no_prefix_num, s.fabric_source, s.booking_date,s.delivery_date, s.is_approved, s.ready_to_approved, s.supplier_id, s.buyer_id, s.item_category, sum(t.finish_fabric) as fin_fab_qnty, sum(t.grey_fabric) as grey_fab_qnty, sum(t.trim_qty) as wo_qnty,sum(t.amount) as amount, t.uom, '' as job_no, '' as po_break_down_id, 1 as type  
						FROM wo_non_ord_samp_booking_mst s, wo_non_ord_samp_booking_dtls t 
						WHERE s.booking_no=t.booking_no and s.status_active =1 and s.is_deleted=0  and t.status_active =1 and t.is_deleted=0 $company_id_sam $buyer_id_sam ".set_user_lavel_filtering(' and s.buyer_id','buyer_id')."  $category_id_sam $booking_date_cond_samp $wo_no_cond_sam  $fabric_source_cond_nonOrder
						group by s.id, s.booking_type, s.is_short, s.booking_no, s.insert_date, s.booking_no_prefix_num, s.booking_date,s.delivery_date, s.is_approved, s.supplier_id, s.buyer_id, s.item_category";//die;
					}
				}
				else if ($db_type==2)
				{
					if($wo_type==0)
					{
						if($job_no!='' || $hidd_po!='')
						{
							$sql="Select a.id as booking_id, a.booking_type, a.is_short, a.booking_no, TO_CHAR(a.insert_date,'YYYY') as year_book, a.booking_no_prefix_num, a.fabric_source, a.booking_date,a.delivery_date, a.is_approved, a.ready_to_approved, a.supplier_id, a.buyer_id, a.item_category, sum(b.fin_fab_qnty) as fin_fab_qnty, sum(b.grey_fab_qnty) as grey_fab_qnty, sum(b.wo_qnty) as wo_qnty,sum(b.amount) as amount, max(b.uom) as uom, LISTAGG(CAST(b.job_no AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.job_no) as job_no, LISTAGG(CAST(b.po_break_down_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.po_break_down_id) as po_break_down_id,  0 as type 
							from wo_booking_mst a, wo_booking_dtls b 
							where a.booking_no=b.booking_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $company_id $buyer_id ".set_user_lavel_filtering(' and a.buyer_id','buyer_id')." $category_id $job_num  $booking_date_cond $wo_no_cond $po_id_cond $po_data_file_ref $fabric_source_cond 
							group by a.id, a.booking_type, a.is_short, a.booking_no, a.insert_date, a.booking_no_prefix_num, a.fabric_source, a.booking_date,a.delivery_date, a.is_approved, a.ready_to_approved, a.supplier_id, a.buyer_id, a.item_category order by a.booking_no";//die;
						}
						else
						{
							  $sql="Select a.id as booking_id, a.booking_type, a.is_short, a.booking_no, TO_CHAR(a.insert_date,'YYYY') as year_book, a.booking_no_prefix_num, a.fabric_source, a.booking_date,a.delivery_date, a.is_approved, a.ready_to_approved, a.supplier_id, a.buyer_id, a.item_category, sum(b.fin_fab_qnty) as fin_fab_qnty, sum(b.grey_fab_qnty) as grey_fab_qnty, sum(b.wo_qnty) as wo_qnty,sum(b.amount) as amount, max(b.uom) as uom, LISTAGG(CAST(b.job_no AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.job_no) as job_no, LISTAGG(CAST(b.po_break_down_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.po_break_down_id) as po_break_down_id, 0 as type 
							from wo_booking_mst a, wo_booking_dtls b 
							where a.booking_no=b.booking_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $company_id $buyer_id ".set_user_lavel_filtering(' and a.buyer_id','buyer_id')." $category_id $job_num $po_id $booking_date_cond $wo_no_cond $job_data_file_ref $fabric_source_cond 
							group by a.id, a.booking_type, a.is_short, a.booking_no, a.insert_date, a.booking_no_prefix_num, a.fabric_source, a.booking_date,a.delivery_date, a.is_approved, a.ready_to_approved, a.supplier_id, a.buyer_id, a.item_category 
							 union all
							 SELECT s.id as booking_id, s.booking_type, s.is_short, s.booking_no, TO_CHAR(s.insert_date,'YYYY') as year_book, s.booking_no_prefix_num, s.fabric_source, s.booking_date,s.delivery_date, s.is_approved, s.ready_to_approved, s.supplier_id, s.buyer_id, s.item_category, sum(t.finish_fabric) as fin_fab_qnty, sum(t.grey_fabric) as grey_fab_qnty, sum(t.trim_qty) as wo_qnty,sum(t.amount) as amount, max(t.uom) as uom, null as job_no, null as po_break_down_id, 1 as type 
							 FROM wo_non_ord_samp_booking_mst s, wo_non_ord_samp_booking_dtls t 
							 WHERE s.booking_no=t.booking_no and s.status_active =1 and s.is_deleted=0 and t.status_active =1 and t.is_deleted=0 $company_id_sam $buyer_id_sam ".set_user_lavel_filtering(' and s.buyer_id','buyer_id')." $category_id_sam $booking_date_cond_samp $wo_no_cond_sam  $fabric_source_cond_nonOrder
							 group by s.id, s.booking_type, s.is_short, s.booking_no, s.insert_date, s.booking_no_prefix_num, s.fabric_source, s.booking_date,s.delivery_date, s.is_approved, s.ready_to_approved, s.supplier_id, s.buyer_id, s.item_category";//die;//LISTAGG(CAST(b.job_no AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.job_no) as job_no,
						}
					}
					else
					{
						if ($wo_type==1 || $wo_type==2 || $wo_type==3)
						{
							$sql="Select a.id as booking_id, a.booking_type, a.is_short, a.booking_no, TO_CHAR(a.insert_date,'YYYY') as year_book, a.booking_no_prefix_num, a.fabric_source, a.booking_date,a.delivery_date, a.is_approved, a.ready_to_approved, a.supplier_id, a.buyer_id, a.item_category, sum(b.fin_fab_qnty) as fin_fab_qnty, sum(b.grey_fab_qnty) as grey_fab_qnty, sum(b.wo_qnty) as wo_qnty,sum(b.amount) as amount, max(b.uom) as uom, LISTAGG(CAST(b.job_no AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.job_no) as job_no, LISTAGG(CAST(b.po_break_down_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.po_break_down_id) as po_break_down_id, 0 as type 
							 from wo_booking_mst a, wo_booking_dtls b, wo_po_details_master c, wo_po_break_down d 
							 where a.booking_no=b.booking_no and b.po_break_down_id=d.id and d.job_no_mst=c.job_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1  $company_id $buyer_id ".set_user_lavel_filtering(' and a.buyer_id','buyer_id')." $category_id $job_num $po_id $booking_date_cond $wo_no_cond $wo_type_cond $wo_type_cond_sam $file_no_cond $internal_ref_cond $fabric_source_cond
							 group by a.id, a.booking_type, a.is_short, a.booking_no, a.insert_date, a.booking_no_prefix_num, a.fabric_source, a.booking_date,a.delivery_date, a.is_approved, a.ready_to_approved, a.supplier_id, a.buyer_id, a.item_category order by a.booking_no";//die;
						}
						else
						{
							 $sql="SELECT s.id as booking_id, s.booking_type, s.is_short, s.booking_no, TO_CHAR(s.insert_date,'YYYY') as year_book, s.booking_no_prefix_num, s.fabric_source, s.booking_date,s.delivery_date, s.is_approved, s.ready_to_approved, s.supplier_id, s.buyer_id, s.item_category, sum(t.finish_fabric) as fin_fab_qnty, sum(t.grey_fabric) as grey_fab_qnty, sum(t.trim_qty) as wo_qnty,sum(t.amount) as amount, max(t.uom) as uom, null as job_no, null as po_break_down_id, 1 as type  
							FROM wo_non_ord_samp_booking_mst s, wo_non_ord_samp_booking_dtls t 
							WHERE s.booking_no=t.booking_no and s.status_active =1 and s.is_deleted=0 and t.status_active =1 and t.is_deleted=0 $company_id_sam $buyer_id_sam ".set_user_lavel_filtering(' and s.buyer_id','buyer_id')." $category_id_sam $booking_date_cond_samp $wo_no_cond_sam  $fabric_source_cond_nonOrder
							group by s.id, s.booking_type, s.is_short, s.booking_no, s.insert_date, s.booking_no_prefix_num, s.fabric_source, s.booking_date,s.delivery_date, s.is_approved, s.ready_to_approved, s.supplier_id, s.buyer_id, s.item_category";//die;
						}
						 
					}
				}*/
				
				
				if($db_type==0)
				{
					$year_book=" year(a.insert_date) as year_book";
					$year_book_non=" year(s.insert_date) as year_book";
					$select_job_year=" year(c.insert_date) as job_year"; 
				}
				else
				{
					$year_book=" TO_CHAR(a.insert_date,'YYYY') as year_book";
					$year_book_non=" TO_CHAR(s.insert_date,'YYYY') as year_book";
					$select_job_year=" TO_CHAR(c.insert_date,'YYYY') as job_year";
				}

				$wo_last_date_sql=sql_select("select id,update_date from wo_booking_mst where is_deleted=0 and  status_active=1");
				foreach($wo_last_date_sql as $val)
				{
					$wo_last_date_arr[$val[csf("id")]]=$val[csf("update_date")];
				}
 				
				if($wo_type==0)
				{
					if($job_no!='' || $hidd_po!='')
					{
						$sql="Select a.id as booking_id, a.booking_type, a.is_short, a.booking_no, $year_book, a.booking_no_prefix_num, a.fabric_source, a.booking_date,a.delivery_date, a.is_approved, a.ready_to_approved, a.supplier_id, a.buyer_id, a.item_category, b.process, b.fin_fab_qnty as fin_fab_qnty, b.grey_fab_qnty as grey_fab_qnty, b.wo_qnty as wo_qnty, b.amount as amount, b.uom as uom, c.job_no_prefix_num, c.job_no, $select_job_year, c.style_ref_no, c.garments_nature, d.id as po_break_down_id, d.po_number as po_number, d.file_no as file_no, d.grouping as grouping, d.pub_shipment_date, 0 as type 
						from wo_booking_mst a, wo_booking_dtls b, wo_po_details_master c, wo_po_break_down d  
						where a.booking_no=b.booking_no and b.po_break_down_id=d.id and d.job_no_mst=c.job_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and d.status_active=$cbo_order_status $company_id $buyer_id ".set_user_lavel_filtering(' and a.buyer_id','buyer_id')." $category_id $booking_date_cond $wo_no_cond $fabric_source_cond  $job_num $po_id $internal_ref_cond  $file_no_cond";//die;
					}
					else
					{
						  $sql="Select a.id as booking_id, a.booking_type, a.is_short, a.booking_no, $year_book, a.booking_no_prefix_num, a.fabric_source, a.booking_date,a.delivery_date, a.is_approved, a.ready_to_approved, a.supplier_id, a.buyer_id, a.item_category, b.process, b.fin_fab_qnty as fin_fab_qnty, b.grey_fab_qnty as grey_fab_qnty, b.wo_qnty as wo_qnty, b.amount as amount, b.uom as uom, c.job_no_prefix_num, c.job_no, $select_job_year, c.style_ref_no, c.garments_nature, d.id as po_break_down_id, d.po_number as po_number, d.file_no as file_no, d.grouping as grouping, d.pub_shipment_date, 0 as type 
						from wo_booking_mst a, wo_booking_dtls b, wo_po_details_master c, wo_po_break_down d
						where a.booking_no=b.booking_no and b.po_break_down_id=d.id and d.job_no_mst=c.job_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and d.status_active=$cbo_order_status $company_id $buyer_id ".set_user_lavel_filtering(' and a.buyer_id','buyer_id')." $category_id $job_num $po_id $booking_date_cond $wo_no_cond $fabric_source_cond  $job_num $po_id $internal_ref_cond  $file_no_cond 
						 union all
						 SELECT s.id as booking_id, s.booking_type, s.is_short, s.booking_no, $year_book_non, s.booking_no_prefix_num, s.fabric_source, s.booking_date,s.delivery_date, s.is_approved, s.ready_to_approved, s.supplier_id, s.buyer_id, s.item_category, null as process, t.finish_fabric as fin_fab_qnty, t.grey_fabric as grey_fab_qnty, t.trim_qty as wo_qnty, t.amount as amount, t.uom as uom, null as job_no_prefix_num, null as job_no, null as job_year, null as style_ref_no, null as garments_nature, 0 as po_break_down_id, null as po_number, null as file_no, null as grouping, null as pub_shipment_date, 1 as type 
						 FROM wo_non_ord_samp_booking_mst s, wo_non_ord_samp_booking_dtls t 
						 WHERE s.booking_no=t.booking_no and s.status_active =1 and s.is_deleted=0 and t.status_active =1 and t.is_deleted=0 $company_id_sam $buyer_id_sam ".set_user_lavel_filtering(' and s.buyer_id','buyer_id')." $category_id_sam $booking_date_cond_samp $wo_no_cond_sam  $fabric_source_cond_nonOrder ";//die;//LISTAGG(CAST(b.job_no AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.job_no) as job_no,
					}
				}
				else
				{
					if ($wo_type==1 || $wo_type==2 || $wo_type==3)
					{
						$sql="Select a.id as booking_id, a.booking_type, a.is_short, a.booking_no, $year_book, a.booking_no_prefix_num, a.fabric_source, a.booking_date,a.delivery_date, a.is_approved, a.ready_to_approved, a.supplier_id, a.buyer_id, a.item_category, b.process, b.fin_fab_qnty as fin_fab_qnty, b.grey_fab_qnty as grey_fab_qnty, b.wo_qnty as wo_qnty, b.amount as amount, b.uom as uom, c.job_no_prefix_num, c.job_no, $select_job_year, c.style_ref_no, c.garments_nature, d.id as po_break_down_id, d.po_number as po_number, d.file_no as file_no, d.grouping as grouping, d.pub_shipment_date,  0 as type 
						 from wo_booking_mst a, wo_booking_dtls b, wo_po_details_master c, wo_po_break_down d 
						 where a.booking_no=b.booking_no and b.po_break_down_id=d.id and d.job_no_mst=c.job_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.is_deleted=0 and c.status_active=1 and d.status_active=$cbo_order_status  $company_id $buyer_id ".set_user_lavel_filtering(' and a.buyer_id','buyer_id')." $category_id $job_num $po_id $booking_date_cond $wo_no_cond $wo_type_cond $wo_type_cond_sam $file_no_cond $internal_ref_cond $fabric_source_cond order by a.booking_no";//die;
					}
					else
					{
						 $sql="SELECT s.id as booking_id, s.booking_type, s.is_short, s.booking_no, $year_book_non, s.booking_no_prefix_num, s.fabric_source, s.booking_date, s.delivery_date, s.is_approved, s.ready_to_approved, s.supplier_id, s.buyer_id, s.item_category, null as process, t.finish_fabric as fin_fab_qnty, t.grey_fabric as grey_fab_qnty, t.trim_qty as wo_qnty, t.amount as amount, t.uom as uom, null as job_no_prefix_num, null as job_no, null as job_year, null as style_ref_no, null as garments_nature, 0 as po_break_down_id, null as po_number, null as file_no, null as grouping, null as pub_shipment_date, 1 as type  
						FROM wo_non_ord_samp_booking_mst s, wo_non_ord_samp_booking_dtls t 
						WHERE s.booking_no=t.booking_no and s.status_active =1 and s.is_deleted=0 and t.status_active =1 and t.is_deleted=0 $company_id_sam $buyer_id_sam ".set_user_lavel_filtering(' and s.buyer_id','buyer_id')." $category_id_sam $booking_date_cond_samp $wo_no_cond_sam  $fabric_source_cond_nonOrder";//die;
					}
					 
				}
				//echo $sql;die;
				$job_check=$order_check=array();
				$sql_result=sql_select($sql); 
				$booking_data=array();
				foreach($sql_result as $row)
				{
					$booking_data[$row[csf("booking_id")]]["booking_id"]=$row[csf("booking_id")];
					$booking_data[$row[csf("booking_id")]]["booking_type"]=$row[csf("booking_type")];
					$booking_data[$row[csf("booking_id")]]["is_short"]=$row[csf("is_short")];
					$booking_data[$row[csf("booking_id")]]["booking_no"]=$row[csf("booking_no")];
					$booking_data[$row[csf("booking_id")]]["year_book"]=$row[csf("year_book")];
					$booking_data[$row[csf("booking_id")]]["booking_no_prefix_num"]=$row[csf("booking_no_prefix_num")];
					
					$booking_data[$row[csf("booking_id")]]["fabric_source"]=$row[csf("fabric_source")];
					$booking_data[$row[csf("booking_id")]]["booking_date"]=$row[csf("booking_date")];
					$booking_data[$row[csf("booking_id")]]["update_date"]=$row[csf("update_date")];
					$booking_data[$row[csf("booking_id")]]["delivery_date"]=$row[csf("delivery_date")];
					$booking_data[$row[csf("booking_id")]]["is_approved"]=$row[csf("is_approved")];
					$booking_data[$row[csf("booking_id")]]["ready_to_approved"]=$row[csf("ready_to_approved")];
					$booking_data[$row[csf("booking_id")]]["supplier_id"]=$row[csf("supplier_id")];
					$booking_data[$row[csf("booking_id")]]["buyer_id"]=$row[csf("buyer_id")];
					$booking_data[$row[csf("booking_id")]]["item_category"]=$row[csf("item_category")];
					$booking_data[$row[csf("booking_id")]]["type"]=$row[csf("type")];
					$booking_data[$row[csf("booking_id")]]["process"].=$row[csf("process")].",";
					
					$booking_data[$row[csf("booking_id")]]["fin_fab_qnty"]+=$row[csf("fin_fab_qnty")];
					$booking_data[$row[csf("booking_id")]]["grey_fab_qnty"]+=$row[csf("grey_fab_qnty")];
					$booking_data[$row[csf("booking_id")]]["wo_qnty"]+=$row[csf("wo_qnty")];
					$booking_data[$row[csf("booking_id")]]["amount"]+=$row[csf("amount")];
					$booking_data[$row[csf("booking_id")]]["uom"]=$row[csf("uom")];
					if($job_check[$row[csf("booking_id")]][$row[csf("job_no")]]=="")
					{
						$booking_data[$row[csf("booking_id")]]["job_no"].=$row[csf("job_no")].",";
						$booking_data[$row[csf("booking_id")]]["job_no_prefix_num"].=$row[csf("job_no_prefix_num")].",";
						$booking_data[$row[csf("booking_id")]]["job_year"].=$row[csf("job_year")].",";
						$booking_data[$row[csf("booking_id")]]["style_ref_no"].=$row[csf("style_ref_no")].",";
						$booking_data[$row[csf("booking_id")]]["style_ref_no"].=$row[csf("style_ref_no")].",";
						
						$job_check[$row[csf("booking_id")]][$row[csf("job_no")]]=$row[csf("job_no")];
					}
					if($order_check[$row[csf("booking_id")]][$row[csf("po_break_down_id")]]=="")
					{
						$booking_data[$row[csf("booking_id")]]["po_break_down_id"].=$row[csf("po_break_down_id")].",";
						$booking_data[$row[csf("booking_id")]]["po_number"].=$row[csf("po_number")].",";
						$booking_data[$row[csf("booking_id")]]["file_no"].=$row[csf("file_no")].",";
						$booking_data[$row[csf("booking_id")]]["grouping"].=$row[csf("grouping")].",";
						$booking_data[$row[csf("booking_id")]]["pub_shipment_date"].=change_date_format($row[csf("pub_shipment_date")]).",";
						
						$order_check[$row[csf("booking_id")]][$row[csf("po_break_down_id")]]=$row[csf("po_break_down_id")];
					}
				}
				
				
				//var_dump($booking_data);die;
				
				//echo $sql;
				//echo  $po_id ;die;
				$i=1;$summer_data=array();$job_check=array();
				foreach($booking_data as $row)
				{
					if($row[("item_category")]==2 || $cbo_category==3)
					{
						if($row[("fabric_source")]==1 || $row[("fabric_source")]==2)
						{
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							if ($row[("type")]==0)
							{	
								if ($row[("booking_type")]==1 || $row[("booking_type")]==2  || $row[("booking_type")]==3  || $row[("booking_type")]==6)
								{
									if ($row[("is_short")]==1)
									{
										$wo_type="Short";
										$wo_typw_id=1;
									}
									else
									{
										$wo_type="Main";
										$wo_typw_id=2;
									}
								}
								elseif($row[("booking_type")]==4)
								{
									$wo_type="Sample With Order";
									$wo_typw_id=3;
								}
							}
							else
							{
								$wo_type="Sample Non Order";
								$wo_typw_id=4;
							}
							
							// echo $wo_typw_id.'ssd';
							
							$job_no=$style_ref_no=$po_number=$file=$ref=$job_number="";
							
							/*$p=1;
							$po_no_arr=array_unique(explode(",",chop($row[("po_break_down_id")],",")));
							foreach($po_no_arr as $po_id)
							{
								if($job_check[$row[("booking_no_prefix_num")]][$job_array[$po_id]['job']]=="")
								{
									$job_check[$row[("booking_no_prefix_num")]][$job_array[$po_id]['job']]=$job_array[$po_id]['job'];
									$job_no.=$job_array[$po_id]['job'].",";
									$job_number.=$job_array[$po_id]['job_no'].",";
									$style_ref_no.=$job_array[$po_id]['style_ref_no'].",";
									$buyer=$job_array[$po_id]['buyer'];
									$year=$job_array[$po_id]['year'];
									$garments_nature=$job_array[$po_id]['garments_nature'];
								}
								$po_number.=$job_array[$po_id]['po_number'].",";
								if($job_array[$po_id]['file']!="") $file.=$job_array[$po_id]['file'].",";
								if($job_array[$po_id]['ref']!="") $ref.=$job_array[$po_id]['ref'].",";
								
								if($p==1)
								{
									$min_ship_date=$job_array[$po_id]['pub_shipment_date'];
									$max_ship_date=$job_array[$po_id]['pub_shipment_date'];
								}
								
								if($min_ship_date>$job_array[$po_id]['pub_shipment_date'])
								{
									$min_ship_date=$job_array[$po_id]['pub_shipment_date'];
								}
								if($max_ship_date<$job_array[$po_id]['pub_shipment_date'])
								{
									$max_ship_date=$job_array[$po_id]['pub_shipment_date'];
								}
								$p++;
								
							}*/
							
							$p=1;
							$ship_date_arr=array_unique(explode(",",chop($row[("pub_shipment_date")],",")));
							foreach($ship_date_arr as $ship_date)
							{
								if($p==1)
								{
									$min_ship_date=$ship_date;
									$max_ship_date=$ship_date;
								}
								
								if(strtotime($min_ship_date)>strtotime($ship_date))
								{
									$min_ship_date=$ship_date;
								}
								if(strtotime($max_ship_date)<strtotime($ship_date))
								{
									$max_ship_date=$ship_date;
								}
								$p++;
								
							}
							
							
							
							
							$all_po_id=implode(",",array_unique(explode(",",chop($row[("po_break_down_id")],","))));
							$job_no=implode(",",array_unique(explode(",",chop($row[("job_no_prefix_num")],","))));
							$job_number=implode(",",array_unique(explode(",",chop($row[("job_no")],","))));
							$job_year=implode(",",array_unique(explode(",",chop($row[("job_year")],","))));
							$style_ref_no=implode(",",array_unique(explode(",",chop($row[("style_ref_no")],","))));
							$po_number=implode(",",array_unique(explode(",",chop($row[("po_number")],","))));
							$file=implode(",",array_unique(explode(",",chop($row[("file_no")],","))));
							$ref=implode(",",array_unique(explode(",",chop($row[("grouping")],","))));
							
							
						
							if($cbo_category==2 || $cbo_category==3)
							{
								//(type,booking_no,company_id,order_id,fabric_nature,fabric_source,job_no,approved,category,is_short,emb_name,item_number_id,supplier_id,action_type,i)
								
							   if($first_print_button==1)
								{
									if($wo_typw_id==2) $action_type="show_fabric_booking_report_gr";
									else $action_type="show_fabric_booking_report"; 
									$variable="<input type='button' value='FB GR' onClick=\"generate_fabric_report('".$wo_typw_id."','".$row[("booking_no")]."','".$cbo_company."','".$all_po_id."','".$cbo_category."','".$row[("fabric_source")]."','".$job_number."','".$row[("is_approved")]."','".$cbo_category."','".$row[("is_short")]."','".$emb_item_array[$job_number]['emb_name']."','".$emb_item_array[$job_number]['item_number_id']."','".$action_type."','".$i."')\" style='width:50px;' class='formbutton' name='print_gr' id='print_gr' />";			
								 }
								if($first_print_button==2)
								{
									if($wo_typw_id==2) $action_type="show_fabric_booking_report";
									else $action_type="show_fabric_booking_report"; 
									$variable="<input type='button' value='PB F1' onClick=\"generate_fabric_report('".$wo_typw_id."','".$row[("booking_no")]."','".$cbo_company."','".$all_po_id."','".$cbo_category."','".$row[("fabric_source")]."','".$job_number."','".$row[("is_approved")]."','".$cbo_category."','".$row[("is_short")]."','".$emb_item_array[$job_number]['emb_name']."','".$emb_item_array[$job_number]['item_number_id']."','".$action_type."','".$i."')\" style='width:50px;' class='formbutton' name='print_pbf' id='print_f1' />";			
								 }
								if($first_print_button==3)
								{
									if($wo_typw_id==2) $action_type="show_fabric_booking_report3";
									else $action_type="show_fabric_booking_report"; 
									$variable="<input type='button' value='PB F2' onClick=\"generate_fabric_report('".$wo_typw_id."','".$row[("booking_no")]."','".$cbo_company."','".$all_po_id."','".$cbo_category."','".$row[("fabric_source")]."','".$job_number."','". $row[("is_approved")]."','".$cbo_category."','".$row[("is_short")]."','".$emb_item_array[$job_number]['emb_name']."','".$emb_item_array[$job_number]['item_number_id']."','".$action_type."','".$i."')\" style='width:50px;' name='print_pb' id='print_f2' class='formbutton' />";	   }
								if($first_print_button==4)
								{
									if($wo_typw_id==2) $action_type="show_fabric_booking_report1";
									else $action_type="show_fabric_booking_report"; 
									$variable="<input type='button' value='PFC F1' onClick=\"generate_fabric_report('".$wo_typw_id."','".$row[("booking_no")]."','".$cbo_company."','".$all_po_id."','".$cbo_category."','".$row[("fabric_source")]."','".$job_number."','". $row[("is_approved")]."','".$cbo_category."','".$row[("is_short")]."','".$emb_item_array[$job_number]['emb_name']."','".$emb_item_array[$job_number]['item_number_id']."','".$action_type."','".$i."')\" style='width:50px;' name='print_p' id='print_pfc' class='formbutton' />";	   }
							if($first_print_button==5)
								{
									if($wo_typw_id==2) $action_type="show_fabric_booking_report2";
									else $action_type="show_fabric_booking_report";  
									$variable="<input type='button' value='PFC F2' onClick=\"generate_fabric_report('".$wo_typw_id."','".$row[("booking_no")]."','".$cbo_company."','".$all_po_id."','".$cbo_category."','".$row[("fabric_source")]."','".$job_number."','". $row[("is_approved")]."','".$cbo_category."','".$row[("is_short")]."','".$emb_item_array[$job_number]['emb_name']."','".$emb_item_array[$job_number]['item_number_id']."','".$action_type."','".$i."')\" style='width:50px;' name='print_pfc' id='print_f2' class='formbutton' />";	    }
								if($first_print_button==6)
								{
									if($wo_typw_id==2) $action_type="show_fabric_booking_report4";
									else $action_type="show_fabric_booking_report"; 
									$variable="<input type='button' value='FB F1' onClick=\"generate_fabric_report('".$wo_typw_id."','".$row[("booking_no")]."','".$cbo_company."','".$all_po_id."','".$cbo_category."','".$row[("fabric_source")]."','".$job_number."','". $row[("is_approved")]."','".$cbo_category."','".$row[("is_short")]."','".$emb_item_array[$job_number]['emb_name']."','".$emb_item_array[$job_number]['item_number_id']."','".$action_type."','".$i."')\" style='width:50px;' name='print_fb' id='print_gr' class='formbutton' />";	   }
								if($first_print_button==7)
								{
									if($wo_typw_id==2) $action_type="show_fabric_booking_report5";
									else $action_type="show_fabric_booking_report"; 
									$variable="<input type='button' value='FB F2' onClick=\"generate_fabric_report('".$wo_typw_id."','".$row[("booking_no")]."','".$cbo_company."','".$all_po_id."','".$cbo_category."','".$row[("fabric_source")]."','".$job_number."','". $row[("is_approved")]."','".$cbo_category."','".$row[("is_short")]."','".$emb_item_array[$job_number]['emb_name']."','".$emb_item_array[$job_number]['item_number_id']."','".$action_type."','".$i."')\" style='width:50px;' name='print_fb' id='print_fbf2'  class='formbutton' />";
								}
								
							} //Fabric Booking End
							
						
							//echo $row[("fabric_source")];die;
						
							if($row[("fabric_source")]==2)
							{
								$rcv_date_arr=array_unique(explode(",",chop($receive_data[$row[("booking_id")]][$row[("type")]],",")));
								if($row[("booking_type")]==1 && $row[("is_short")]==2)
								{
									$summer_data[1]["book_qnty"]+=$row[("fin_fab_qnty")];
									
									foreach($rcv_date_arr as $rcv_date)
									{
										if($rcv_date<=$row[("delivery_date")])
										{
											$summer_data[1]["rcv_qnty_intime"]+=$receive_qnty_data[$row[("booking_id")]][$row[("type")]][$rcv_date];
										}
										$summer_data[1]["rcv_qnty"]+=$receive_qnty_data[$row[("booking_id")]][$row[("type")]][$rcv_date];
									}
								}
								else if($row[("booking_type")]==1 && $row[("is_short")]==1)
								{
									$summer_data[2]["book_qnty"]+=$row[("fin_fab_qnty")];
									foreach($rcv_date_arr as $rcv_date)
									{
										if($rcv_date<=$row[("delivery_date")])
										{
											$summer_data[2]["rcv_qnty_intime"]+=$receive_qnty_data[$row[("booking_id")]][$row[("type")]][$rcv_date];
										}
										$summer_data[2]["rcv_qnty"]+=$receive_qnty_data[$row[("booking_id")]][$row[("type")]][$rcv_date];
									}
								}
								if($row[("booking_type")]==4)
								{
									$summer_data[3]["book_qnty"]+=$row[("fin_fab_qnty")];
									foreach($rcv_date_arr as $rcv_date)
									{
										if($rcv_date<=$row[("delivery_date")])
										{
											$summer_data[3]["rcv_qnty_intime"]+=$receive_qnty_data[$row[("booking_id")]][$row[("type")]][$rcv_date];
										}
										$summer_data[3]["rcv_qnty"]+=$receive_qnty_data[$row[("booking_id")]][$row[("type")]][$rcv_date];
									}
								}
							}
							else if($row[("fabric_source")]==1)
							{
								$rcv_date_arr=array_unique(explode(",",chop($receive_data_pord[$row[("booking_id")]][$row[("type")]],",")));
								if($row[("booking_type")]==1 && $row[("is_short")]==2)
								{
									$summer_data[1]["book_qnty"]+=$row[("fin_fab_qnty")];
									
									foreach($rcv_date_arr as $rcv_date)
									{
										if($rcv_date<=$row[("delivery_date")])
										{
											$summer_data[1]["rcv_qnty_intime"]+=$receive_qnty_data_prod[$row[("booking_id")]][$row[("type")]][$rcv_date];
										}
										$summer_data[1]["rcv_qnty"]+=$receive_qnty_data_prod[$row[("booking_id")]][$row[("type")]][$rcv_date];
									}
								}
								else if($row[("booking_type")]==1 && $row[("is_short")]==1)
								{
									$summer_data[2]["book_qnty"]+=$row[("fin_fab_qnty")];
									foreach($rcv_date_arr as $rcv_date)
									{
										if($rcv_date<=$row[("delivery_date")])
										{
											$summer_data[2]["rcv_qnty_intime"]+=$receive_qnty_data_prod[$row[("booking_id")]][$row[("type")]][$rcv_date];
										}
										$summer_data[2]["rcv_qnty"]+=$receive_qnty_data_prod[$row[("booking_id")]][$row[("type")]][$rcv_date];
									}
								}
								
								if($row[("booking_type")]==4)
								{
									$summer_data[3]["book_qnty"]+=$row[("fin_fab_qnty")];
									foreach($rcv_date_arr as $rcv_date)
									{
										if($rcv_date<=$row[("delivery_date")])
										{
											$summer_data[3]["rcv_qnty_intime"]+=$receive_qnty_data_prod[$row[("booking_id")]][$row[("type")]][$rcv_date];
										}
										$summer_data[3]["rcv_qnty"]+=$receive_qnty_data_prod[$row[("booking_id")]][$row[("type")]][$rcv_date];
									}
								}
							}
							//echo $variable;
							//<a href='#report_details' onClick="generate_fabric_booking_report('<? echo $row[("booking_no")];','show_trim_booking_report');"></a>
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
								<td width="30"><? echo $i;?></td>
								<td width="60" align="center"><? //echo $variable; ?> <? echo change_date_format($row[("delivery_date")]);; //echo $row[("booking_no_prefix_num")];?></td>	
								<td width="60" align="center"><p><? echo $row[("year_book")]; ?></td>
								<td width="70" align="center"><p><? echo change_date_format($row[("booking_date")]); ?></p></td>
								<td width="70" align="center"><p><? echo change_date_format($wo_last_date_arr[$row[("booking_id")]]); ?></p></td>
								<td width="70" align="center"><p><? echo change_date_format($row[("delivery_date")]); ?></p></td>
								<td width="70" align="center"><p>
								<? 
								$lead_time=0;
								if($row[("delivery_date")]!="" && $row[("delivery_date")]!="0000-00-00")
								{
									$lead_time=datediff("d", $row[("booking_date")], $row[("delivery_date")]);
								}
								if($lead_time>1 || $lead_time<-1) echo $lead_time." Days"; else echo $lead_time." Day";
								?></p></td>
								<td width="100"><p><? echo $wo_type;?></p></td>
								<td width="100" align="center"><? echo $yes_no[$row[("ready_to_approved")]]; ?></td>
								<td width="80"><p><? echo $approval_type_arr[$row[("is_approved")]]; ?></p></td>
								<td width="100"><p><? echo $supplierArr[$row[("supplier_id")]]; ?></p></td>
								<td width="100"><p><? echo $buyerArr[$row[("buyer_id")]]; ?></p></td>
								<td width="70"><? echo $job_no;//$row[("job_no_prefix_num")]; ?></td>
								<td width="60"><? echo $job_year; ?></td>
								<td width="100"><p><? if ($row[("type")]==0) echo $style_ref_no; else echo $nonStyleArr[$style_ref_no]; ?></p></td>
								<td width="100"><p><? echo $ref; ?></p></td>
								<td width="100"><p><? echo $file; ?></p></td>
								<td width="100"><p><? echo $po_number; ?></p></td>
								<td width="100"><p>
								<?
								if ($row[("type")]==0)
								{
									echo "Max - ". $max_ship_date;
									$order_count=explode(",",$po_number);
									if(count($order_count)>1) echo "<br> Min  - " . $min_ship_date; 
								}
								?></p></td>
								<td width="80"><p><? echo $item_category[$row[("item_category")]]; ?></p></td>
								<? 
								//booking_id
								$td_color="";
								if($row[("booking_type")]==2)
								{
									if($row[("delivery_date")]!="" && $row[("delivery_date")]!="0000-00-00" && $first_rec_date[$row[("booking_id")]]!="" && $first_rec_date[$row[("booking_id")]]!="0000-00-00")
									{
										if(strtotime($first_rec_date[$row[("booking_id")]])>strtotime($row[("delivery_date")])) $td_color='bgcolor="#FF0000"'; else $td_color='';
									}
									$first_rcv_date=change_date_format($first_rec_date[$row[("booking_id")]]);
								}
								else if($row[("booking_type")]==1 || $row[("booking_type")]==4)
								{
									if($row[("delivery_date")]!="" && $row[("delivery_date")]!="0000-00-00" && $first_rec_date_prod[$row[("booking_id")]]!="" && $first_rec_date_prod[$row[("booking_id")]]!="0000-00-00")
									{
										if(strtotime($first_rec_date_prod[$row[("booking_id")]])>strtotime($row[("delivery_date")])) $td_color='bgcolor="#FF0000"'; else $td_color='';
									}
									$first_rcv_date=change_date_format($first_rec_date_prod[$row[("booking_id")]]);
								}
								 //echo $row[("booking_type")]."***".$row[("type")] ;
								?>
								<td width="70" title="<? echo "Booking Id : ".$row[("booking_id")]."==".$row[("booking_type")];  ?>" align="center" <? echo $td_color; ?> ><p><? echo $first_rcv_date; ?></p></td>
								<?
									if ($cbo_category==4)
									{
										?>
											<td width="100" align="right"><? echo number_format($row[("amount")],2); ?></td>
										<?
									}
									else
									{
										?>
											<td width="100" align="right"><? echo number_format($row[("fin_fab_qnty")],2); ?></td>
											<td width="100" align="right"><? echo number_format($row[("grey_fab_qnty")],2); ?></td>
										<?
									}
								?>
								<td width=""><? //echo $row[("po_number")]; ?></td>
							</tr>
							<?
							$tot_wo_qnty+=$row[("amount")];
							$tot_fin_fab_qnty+=$row[("fin_fab_qnty")];
							$tot_grey_fab_qnty+=$row[("grey_fab_qnty")];
							$i++;
						
						}
					}
					else
					{
						
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						if ($row[("type")]==0)
						{	
							if ($row[("booking_type")]==1 || $row[("booking_type")]==2  || $row[("booking_type")]==3  || $row[("booking_type")]==6)
							{
								if ($row[("is_short")]==1)
								{
									$wo_type="Short";
									$wo_typw_id=1;
								}
								else
								{
									$wo_type="Main";
									$wo_typw_id=2;
								}
							}
							elseif($row[("booking_type")]==4)
							{
								$wo_type="Sample With Order";
								$wo_typw_id=3;
							}
						}
						else
						{
							$wo_type="Sample Non Order";
							$wo_typw_id=4;
						}
						
						// echo $wo_typw_id.'ssd';
						$job_no=$style_ref_no=$po_number=$file=$ref=$job_number="";
						$p=1;
						$ship_date_arr=array_unique(explode(",",chop($row[("pub_shipment_date")],",")));
						foreach($ship_date_arr as $ship_date)
						{
							if($p==1)
							{
								$min_ship_date=$ship_date;
								$max_ship_date=$ship_date;
							}
							
							if($min_ship_date>$ship_date)
							{
								$min_ship_date=$ship_date;
							}
							if($max_ship_date<$ship_date)
							{
								$max_ship_date=$ship_date;
							}
							$p++;
							
						}
						
						$all_po_id=implode(",",array_unique(explode(",",chop($row[("po_break_down_id")],","))));
						$job_no=implode(",",array_unique(explode(",",chop($row[("job_no_prefix_num")],","))));
						$job_number=implode(",",array_unique(explode(",",chop($row[("job_no")],","))));
						$style_ref_no=implode(",",array_unique(explode(",",chop($row[("style_ref_no")],","))));
						$po_number=implode(",",array_unique(explode(",",chop($row[("po_number")],","))));
						$file=implode(",",array_unique(explode(",",chop($row[("file_no")],","))));
						$ref=implode(",",array_unique(explode(",",chop($row[("grouping")],","))));
					   $job_year=implode(",",array_unique(explode(",",chop($row[("job_year")],","))));
					
						if($cbo_category==4) //Accessories Start
						{
							if($first_print_button==13)
							{ 
							 $variable="<input type='button' value='PB' onClick=\"generate_fabric_report('".$wo_typw_id."','".$row[("booking_no")]."','".$cbo_company."','".$all_po_id."','".$cbo_category."','".$row[("fabric_source")]."','".$job_number."','". $row[("is_approved")]."','".$cbo_category."','".$row[("is_short")]."','".$emb_item_array[$job_number]['emb_name']."','".$emb_item_array[$job_number]['item_number_id']."','show_trim_booking_report','".$i."')\" style='width:50px;' name='print_booking1' id='print_booking1'  class='formbutton' />";	   }
							if($first_print_button==14)
							{ 
							 $variable="<input type='button' value='PB1' onClick=\"generate_fabric_report('".$wo_typw_id."','".$row[("booking_no")]."','".$cbo_company."','".$all_po_id."','".$cbo_category."','".$row[("fabric_source")]."','".$job_number."','". $row[("is_approved")]."','".$cbo_category."','".$row[("is_short")]."','".$emb_item_array[$job_number]['emb_name']."','".$emb_item_array[$job_number]['item_number_id']."','show_trim_booking_report1','".$i."')\" style='width:50px;' name='print_booking2' id='print_booking2'  class='formbutton' />";	  
							}
							
							if($first_print_button==15)
							{ 
							 $variable="<input type='button' value='PB3' onClick=\"generate_fabric_report('".$wo_typw_id."','".$row[("booking_no")]."','".$cbo_company."','".$all_po_id."','".$cbo_category."','".$row[("fabric_source")]."','".$job_number."','". $row[("is_approved")]."','".$cbo_category."','".$row[("is_short")]."','".$emb_item_array[$job_number]['emb_name']."','".$emb_item_array[$job_number]['item_number_id']."','show_trim_booking_report','".$i."')\" style='width:50px;' name='print_booking3' id='print_booking3'  class='formbutton' />";	  
							}
							if($first_print_button==16)
							{ 
							 $variable="<input type='button' value='PB3' onClick=\"generate_fabric_report('".$wo_typw_id."','".$row[("booking_no")]."','".$cbo_company."','".$all_po_id."','".$cbo_category."','".$row[("fabric_source")]."','".$job_number."','". $row[("is_approved")]."','".$cbo_category."','".$row[("is_short")]."','".$emb_item_array[$job_number]['emb_name']."','".$emb_item_array[$job_number]['item_number_id']."','show_trim_booking_report','".$i."')\" style='width:50px;' name='print_booking3' id='print_booking3'  class='formbutton' />";	  
							}
							if($first_print_button==17)
							{ 
							 $variable="<input type='button' value='PB' onClick=\"generate_fabric_report('".$wo_typw_id."','".$row[("booking_no")]."','".$cbo_company."','".$all_po_id."','".$cbo_category."','".$row[("fabric_source")]."','".$job_number."','". $row[("is_approved")]."','".$cbo_category."','".$row[("is_short")]."','".$emb_item_array[$job_number]['emb_name']."','".$emb_item_array[$job_number]['item_number_id']."','show_trim_booking_report','".$i."')\" style='width:50px;' name='print_booking1' id='print_booking1'  class='formbutton' />";	  
							}
							if($first_print_button==18)
							{ 
							 $variable="<input type='button' value='PB1' onClick=\"generate_fabric_report('".$wo_typw_id."','".$row[("booking_no")]."','".$cbo_company."','".$all_po_id."','".$cbo_category."','".$row[("fabric_source")]."','".$job_number."','". $row[("is_approved")]."','".$cbo_category."','".$row[("is_short")]."','".$emb_item_array[$job_number]['emb_name']."','".$emb_item_array[$job_number]['item_number_id']."','show_trim_booking_report1','".$i."')\" style='width:50px;' name='print_booking2' id='print_booking4'  class='formbutton' />";	  
							}
							if($first_print_button==19)
							{ 
							 $variable="<input type='button' value='PB2' onClick=\"generate_fabric_report('".$wo_typw_id."','".$row[("booking_no")]."','".$cbo_company."','".$all_po_id."','".$cbo_category."','".$row[("fabric_source")]."','".$job_number."','". $row[("is_approved")]."','".$cbo_category."','".$row[("is_short")]."','".$emb_item_array[$job_number]['emb_name']."','".$emb_item_array[$job_number]['item_number_id']."','show_trim_booking_report2','".$i."')\" style='width:50px;' name='print_booking3' id='print_booking4'  class='formbutton' />";	  
							}
						}
						else if($cbo_category==25)
						{
							$variable="<input type='button' value='Print Actual' onClick=\"generate_fabric_report('".$wo_typw_id."','".$row[("booking_no")]."','".$cbo_company."','".$all_po_id."','".$cbo_category."','".$row[("fabric_source")]."','".$job_number."','". $row[("is_approved")]."','".$cbo_category."','".$row[("is_short")]."','".$emb_item_array[$job_number]['emb_name']."','".$emb_item_array[$job_number]['item_number_id']."','show_trim_booking_report1','".$i."','".$row[("supplier_id")]."')\" style='width:60px;' name='print_booking3' id='print_booking4'  class='formbutton' />";
						}
						else if($cbo_category==12) //Service Fab. Booking...
						{
							
							if($first_print_button==11)
							{ 
							 $variable="<input type='button' value='PB' onClick=\"generate_fabric_report('".$wo_typw_id."','".$row[("booking_no")]."','".$cbo_company."','".$all_po_id."','".$cbo_category."','".$row[("fabric_source")]."','".$job_number."','". $row[("is_approved")]."','".$cbo_category."','".$row[("is_short")]."','".$emb_item_array[$job_number]['emb_name']."','".$emb_item_array[$job_number]['item_number_id']."','show_trim_booking_report','".$i."')\" style='width:50px;' name='print_booking3' id='print_booking4'  class='formbutton' />";	  
							}
							if($first_print_button==12)
							{ 
							 $variable="<input type='button' value='PB1' onClick=\"generate_fabric_report('".$wo_typw_id."','".$row[("booking_no")]."','".$cbo_company."','".$all_po_id."','".$cbo_category."','".$row[("fabric_source")]."','".$job_number."','". $row[("is_approved")]."','".$cbo_category."','".$row[("is_short")]."','".$emb_item_array[$job_number]['emb_name']."','".$emb_item_array[$job_number]['item_number_id']."','show_trim_booking_report1','".$i."')\" style='width:50px;' name='print_booking3' id='print_booking4'  class='formbutton' />";	  
							}
							if($first_print_button==59)
							{ 
							 $variable="<input type='button' value='BPKW' onClick=\"generate_fabric_report('".$wo_typw_id."','".$row[("booking_no")]."','".$cbo_company."','".$all_po_id."','".$cbo_category."','".$row[("fabric_source")]."','".$job_number."','". $row[("is_approved")]."','".$cbo_category."','".$row[("is_short")]."','".$emb_item_array[$job_number]['emb_name']."','".$emb_item_array[$job_number]['item_number_id']."','show_trim_booking_report2','".$i."')\" style='width:50px;' name='print_booking3' id='print_booking4'  class='formbutton' />";	  
							}
							
						}
						//$conversion_cost_head_array
						$process_id_all=array_unique(explode(",",chop($row[("process")],",")));
						$all_process="";
						foreach($process_id_all as $process_id)
						{
							$all_process.=$conversion_cost_head_array[$process_id].",";
						}
						$all_process=chop($all_process,",");
						
						//<input type="button" value="Print Actual" onClick="generate_trim_report('show_trim_booking_report1')"  style="width:80px" name="print_booking" id="print_booking" class="formbutton" />
					
						//echo $row[("fabric_source")];die;
						//echo $variable;
						//<a href='#report_details' onClick="generate_fabric_booking_report('<? echo $row[("booking_no")];','show_trim_booking_report');"></a>
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
							<td width="30"><? echo $i;?></td>
							<td width="60" align="center"><? echo $variable; ?> <? echo $row[("booking_no_prefix_num")];?></td>	
                            <?
							if ($cbo_category==12)
							{
								?>
                                <td width="100"><p><? echo $all_process; ?>&nbsp;</p></td>
                                <?
							}
							?>
							
                            <td width="60" align="center"><p><? echo $row[("year_book")]; ?>&nbsp;</p></td>
							<td width="70" align="center"><p><? echo change_date_format($row[("booking_date")]); ?>&nbsp;</p></td>
							<td width="70" align="center"><p><? echo change_date_format($row[("delivery_date")]); ?>&nbsp;</p></td>
							<td width="70" align="center"><p>
							<?
							$lead_time=0;
							if($row[("delivery_date")]!="" && $row[("delivery_date")]!="0000-00-00")
							{
								$lead_time=datediff("d", $row[("booking_date")], $row[("delivery_date")]);
							}
							if($lead_time>1 || $lead_time<-1) echo $lead_time." Days"; else echo $lead_time." Day";
							?>&nbsp;</p></td>
							<td width="100"><p><? echo $wo_type;?>&nbsp;</p></td>
							<td width="100" align="center"><? echo $yes_no[$row[("ready_to_approved")]]; ?></td>
							<td width="80"><p><? echo $approval_type_arr[$row[("is_approved")]]; ?>&nbsp;</p></td>
							<td width="100"><p><? echo $supplierArr[$row[("supplier_id")]]; ?>&nbsp;</p></td>
							<td width="100"><p><? echo $buyerArr[$row[("buyer_id")]]; ?>&nbsp;</p></td>
							<td width="70"><p><? echo $job_no;//$row[("job_no_prefix_num")]; ?>&nbsp;</p></td>
							<td width="60"><? echo $job_year;// $year; ?></td>
							<td width="100"><p><? if ($row[("type")]==0) echo $style_ref_no; else echo $nonStyleArr[$style_ref_no]; ?>&nbsp;</p></td>
							<td width="100"><p><? echo $ref; ?>&nbsp;</p></td>
							<td width="100"><p><? echo $file; ?>&nbsp;</p></td>
							<td width="100"><p><? echo $po_number; ?>&nbsp;</p></td>
							<td width="100"><p>
							<?
							if ($row[("type")]==0)
							{
								echo "Max - ". $max_ship_date;
								$order_count=explode(",",$po_number);
								if(count($order_count)>1) echo "<br> Min  - " . $min_ship_date; 
							}
							?>&nbsp;</p></td>
							<td width="80"><p><? echo $item_category[$row[("item_category")]]; ?>&nbsp;</p></td>
							<? 
							//booking_id
							$td_color="";
							
							/*if($row[("booking_type")]==2)
							{
								if($row[("delivery_date")]!="" && $row[("delivery_date")]!="0000-00-00" && $first_rec_date[$row[("booking_id")]]!="" && $first_rec_date[$row[("booking_id")]]!="0000-00-00")
								{
									if(strtotime($first_rec_date[$row[("booking_id")]])>strtotime($row[("delivery_date")])) $td_color='bgcolor="#FF0000"'; else $td_color='';
								}
								$first_rcv_date=change_date_format($first_rec_date[$row[("booking_id")]]);
							}
							else if($row[("booking_type")]==1)
							{
								if($row[("delivery_date")]!="" && $row[("delivery_date")]!="0000-00-00" && $first_rec_date_prod[$row[("booking_id")]]!="" && $first_rec_date_prod[$row[("booking_id")]]!="0000-00-00")
								{
									if(strtotime($first_rec_date_prod[$row[("booking_id")]])>strtotime($row[("delivery_date")])) $td_color='bgcolor="#FF0000"'; else $td_color='';
								}
								$first_rcv_date=change_date_format($first_rec_date_prod[$row[("booking_id")]]);
							}*/
							
							if(strtotime($first_rec_date[$row[("booking_id")]])>strtotime($row[("delivery_date")])) $td_color='bgcolor="#FF0000"'; else $td_color='';
							$first_rcv_date=change_date_format($first_rec_date[$row[("booking_id")]]);
							
							 //echo $row[("booking_type")]."***".$row[("type")] ;
							?>
							<td width="70" title="<? echo "Booking Id : ".$row[("booking_id")]; ?>" align="center" <? echo $td_color; ?> ><p><? echo $first_rcv_date;  ?></p></td>
							<?
								if ($cbo_category==4)
								{
									?>
										<td width="100" align="right"><? echo number_format($row[("amount")],2); ?></td>
									<?
								}
								else
								{
									?>
										<td width="100" align="right"><? echo number_format($row[("fin_fab_qnty")],2); ?></td>
										<td width="100" align="right"><? echo number_format($row[("grey_fab_qnty")],2); ?></td>
									<?
								}
							?>
							<td width=""><? //echo $row[("po_number")]; ?></td>
						</tr>
						<?
						$tot_wo_qnty+=$row[("amount")];
						$tot_fin_fab_qnty+=$row[("fin_fab_qnty")];
						$tot_grey_fab_qnty+=$row[("grey_fab_qnty")];
						$i++;
					
					
					}
					
					
				}
				?>
				</table>
				<table class="rpt_table" width="<? echo $tbl_width; ?>" cellpadding="0" cellspacing="0" border="1" rules="all">
					<tfoot>
						<th width="30">&nbsp;</th>
						<th width="60">&nbsp;</th>
                        <?
						if ($cbo_category==12)
						{
							?>
                            <th width="100">&nbsp;</th>
                            <?
						}
						?>
                        
						<th width="60">&nbsp;</th>
						<th width="70">&nbsp;</th>
						<th width="70">&nbsp;</th>
                        <th width="70">&nbsp;</th>
                        <th width="70">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="70">&nbsp;</th>
						<th width="60">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="100">&nbsp;</th>
                        <th width="100">&nbsp;</th>
						<th width="80">Total</th>
						<th width="70">&nbsp;</th>
						<?
							if ($cbo_category==4)
							{
								?>
									<th width="100" align="right" id="value_tot_fin_fab_qnty"><? echo number_format($tot_wo_qnty,2); ?></th>
								<?
							}
							else
							{
								?>
									<th width="100" align="right" id="value_tot_fin_fab_qnty"><? echo number_format($tot_fin_fab_qnty,2); ?></th>
									<th width="100" align="right" id="value_tot_grey_fab_qnty"><? echo number_format($tot_grey_fab_qnty,2); ?></th>
								<?
							}
						?>    
						<th width="">&nbsp;</th>
					</tfoot>
				</table>
			</div>
            <br>
            <?
			
			if ($cbo_category==2)
			{
				?>
                <table class="rpt_table" width="600" cellpadding="0" cellspacing="0" border="1" rules="all">
                	<thead>
                    	<tr bgcolor="#CCCCCC">
                                <td colspan="6" align="center" style="font-size:16px; font-weight:bold;">Finish Fabric OTD Summary</td>
                            </tr>
                    	<tr>
                        	<th width="100">WO Type</th>
                            <th width="100">Required Qty.</th>
                            <th width="100">On Time Rcvd Qty</th>
                            <th width="100">Total Rcvd Qty</th>
                            <th width="100">Balance</th>
                            <th >OTD %</th>
                        </tr>
                    </thead>
                    <tbody>
                    	<?
						$wo_type_arr=array(1=>"Main",2=>"Short",3=>"Sample");
						foreach($summer_data as $wo_type=>$val)
						{
							$balance=$val["book_qnty"]-$val["rcv_qnty"];
							$otd_percent=(($val["rcv_qnty_intime"]/$val["book_qnty"])*100);
							?>
                            <tr>
                                <td><? echo $wo_type_arr[$wo_type]; ?></td>
                                <td align="right"><? echo number_format($val["book_qnty"],2); $tot_book_qnty+=$val["book_qnty"]; ?></td>
                                <td align="right"><? echo number_format($val["rcv_qnty_intime"],2); $tot_rcv_qnty_intime+=$val["rcv_qnty_intime"];  ?></td>
                                <td align="right"><? echo number_format($val["rcv_qnty"],2); $tot_rcv_qnty+=$val["rcv_qnty"];  ?></td>
                                <td align="right"><? echo number_format($balance,2); $tot_balance+=$balance;  ?></td>
                                <td align="right"><? echo number_format($otd_percent,2); ?></td>
                            </tr>
                            <?
						}
						?>
                    </tbody>
                    <tfoot>
                    	<tr>
                        	<th>Total</th>
                            <th><? echo number_format($tot_book_qnty,2); ?></th>
                            <th><? echo number_format($tot_rcv_qnty_intime,2); ?></th>
                            <th><? echo number_format($tot_rcv_qnty,2); ?></th>
                            <th><? echo number_format($tot_balance,2); ?></th>
                            <th>&nbsp;</th>
                        </tr>
                    </tfoot>
                </table>
                <?
			}
			?>
		</fieldset>
		<?
	}
	else if ($cbo_category==24)// Service Yarn
	{
		if($db_type==0)
		{
			if ($year_id==0) $year_id_cond=""; else $year_id_cond=" and YEAR(a.insert_date)=$year_id";
		}
		elseif($db_type==2)
		{ 
			if ($year_id==0) $year_id_cond=""; else $year_id_cond=" and TO_CHAR(a.insert_date,'YYYY')=$year_id";
		}
		
		if ($cbo_company==0) $company_id=""; else $company_id=" and a.company_id=$cbo_company";
		if ($cbo_buyer==0) $supplier_cond=""; else $supplier_cond=" and a.supplier_id=$cbo_buyer";
		if ($cbo_category==0) $category_id=""; else $category_id=" and a.item_category=$cbo_category";
		if ($wo_no=="") $wo_no_cond=""; else $wo_no_cond=" and a.ydw_no='$wo_no'";
		
		if ($wo_type==2) $wo_type_cond=" and a.entry_form=41"; else if ($wo_type==4) $wo_type_cond=" and a.entry_form=42"; else $wo_type_cond="";
		
		if ($job_no=="") $job_num=""; else $job_num=" and b.job_no='$job_no'";
		//if ($job_no=="") $job_num_mst=""; else $job_num_mst=" and b.job_no_mst=$job_no";
		if ($po_no=='') $po_no_no=""; else $po_no_no=" and d.po_number in ($po_no)";
		if ($hidd_po=="") $po_id=""; else $po_id=" and d.id in ( $hidd_po )";
		
		if ($hidd_po=="") $po_id_cond=""; else $po_id_cond=" and b.po_break_down_id in ( $hidd_po )";
		
		if ($file_no=="") $file_no_cond=""; else $file_no_cond=" and d.file_no='".trim($file_no)."' "; 
		if ($internal_ref=="") $internal_ref_cond=""; else $internal_ref_cond=" and d.grouping='".trim($internal_ref)."' "; 
		//echo $file_no_cond.'=='.$internal_ref_cond;die;
		
		if($db_type==0)
		{
			if($txt_date_category==1) 
			{	
				if( $date_from=="" && $date_to=="" ) $booking_date_cond=""; else $booking_date_cond=" and a.booking_date between '".$date_from."' and '".$date_to."'";
			}
			else 
			{
				if($txt_date_category==2) if( $date_from=="" && $date_to=="" ) $booking_date_cond=""; else $booking_date_cond=" and a.delivery_date between '".$date_from."' and '".$date_to."'";
			}
		}
		if($db_type==2)
		{
			if($txt_date_category==1) 
			{
				if( $date_from=="" && $date_to=="" ) $booking_date_cond=""; else $booking_date_cond=" and a.booking_date between '".change_date_format($date_from,'dd-mm-yyyy','-',1)."' and '".change_date_format($date_to,'dd-mm-yyyy','-',1)."'";
			}
			else if($txt_date_category==2) 
			{
				if( $date_from=="" && $date_to=="" ) $booking_date_cond=""; else $booking_date_cond=" and a.delivery_date between '".change_date_format($date_from,'dd-mm-yyyy','-',1)."' and '".change_date_format($date_to,'dd-mm-yyyy','-',1)."'";
			}
		}
	
	
		$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name");
		$supplierArr=return_library_array( "select id,supplier_name from lib_supplier", "id", "supplier_name");	
		$buyerArr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name");
		$nonStyleArr=return_library_array( "select id, style_ref_no from sample_development_mst", "id", "style_ref_no");		
		
		$sql="select a.job_no,d.id as po_id from wo_po_details_master a, wo_po_break_down d where a.job_no=d.job_no_mst and a.status_active=1 and a.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $internal_ref_cond  $file_no_cond  group by a.job_no,d.id";
		$sql_result=sql_select($sql);
		$po_id_data="";$k=0;
		foreach($sql_result as $row)
		{
			if($k==0)
			{
				$po_data_cond=$row[csf('po_id')];
				 $job_data_cond=$row[csf('job_no')];
			}
			else
			{
				 $po_data_cond.=",".$row[csf('po_id')];
				 $job_data_cond.=",".$row[csf('job_no')];
			} 
		} //echo $job_data_cond;
		if($file_no_cond!="" || $internal_ref!="")
		{  
		 $po_data_file_ref=" and b.po_break_down_id in($po_data_cond) "; 
		 $job_data_file_ref=" and b.job_no in('$job_data_cond') "; 
		}
		else
		{
			$po_data_file_ref="";
			$job_data_file_ref="";
		}
		
		if($db_type==0)
		{
			$job_sql="select a.job_no_prefix_num, a.job_no, a.buyer_name, Year(a.insert_date) as year, a.style_ref_no, a.garments_nature, 
			group_concat(b.id) as id,
			group_concat(b.po_number) as po_number,
			group_concat(b.file_no) as file_no,
			group_concat(b.grouping) as grouping,
			max(b.pub_shipment_date) as max_ship_date,
			min(b.pub_shipment_date) as min_ship_date
			from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.job_no_prefix_num, a.job_no, a.buyer_name, a.insert_date, a.style_ref_no, a.garments_nature";
		}
		elseif($db_type==2)
		{
			$job_sql="select a.job_no_prefix_num, a.job_no, a.buyer_name, TO_CHAR(a.insert_date,'YYYY') as year, a.style_ref_no, a.garments_nature, 
			LISTAGG(CAST(b.id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.id) as id,
			LISTAGG(CAST(b.po_number AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.po_number) as po_number,
			LISTAGG(CAST(b.file_no AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.file_no) as file_no,
			LISTAGG(CAST(b.grouping AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.grouping) as grouping,
			max(b.pub_shipment_date) as max_ship_date,
			min(b.pub_shipment_date) as min_ship_date
			from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.job_no_prefix_num, a.job_no, a.buyer_name, a.insert_date, a.style_ref_no, a.garments_nature";
		}
		
		$job_sql_result=sql_select($job_sql);
		foreach($job_sql_result as $row)
		{
			$order_no=implode(",",array_unique(explode(",",$row[csf("po_number")])));
			$order_id=implode(",",array_unique(explode(",",$row[csf("id")])));
			$file_no=implode(",",array_unique(explode(",",$row[csf("file_no")])));
			$int_ref=implode(",",array_unique(explode(",",$row[csf("grouping")])));
	
			$job_array[$row[csf("job_no")]]['job']=$row[csf("job_no_prefix_num")];
			$job_array[$row[csf("job_no")]]['job_no']=$row[csf("job_no")];
			$job_array[$row[csf("job_no")]]['buyer']=$row[csf("buyer_name")];
			$job_array[$row[csf("job_no")]]['year']=$row[csf("year")];
			$job_array[$row[csf("job_no")]]['style_ref_no']=$row[csf("style_ref_no")];
			$job_array[$row[csf("job_no")]]['garments_nature']=$row[csf("garments_nature")];
			$job_array[$row[csf("job_no")]]['max_ship_date']=$row[csf("max_ship_date")];
			$job_array[$row[csf("job_no")]]['min_ship_date']=$row[csf("min_ship_date")];
			$job_array[$row[csf("job_no")]]['id']=$order_id;
			$job_array[$row[csf("job_no")]]['po_number']=$order_no;
			$job_array[$row[csf("job_no")]]['file']=$file_no;
			$job_array[$row[csf("job_no")]]['ref']=$int_ref;
		}
		
		$emb_item_array=array();
		
		$emb_sql="select a.emb_name, b.item_number_id, c.job_no_mst from wo_pre_cost_embe_cost_dtls a, wo_po_color_size_breakdown b, wo_po_break_down c where a.job_no=b.job_no_mst and c.id=b.po_break_down_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
		$emb_sql_result=sql_select($emb_sql);
		foreach($emb_sql_result as $row)
		{
			$emb_item_array[$row[csf("job_no_mst")]]['emb_name']=$row[csf("emb_name")];
			$emb_item_array[$row[csf("job_no_mst")]]['item_number_id']=$row[csf("item_number_id")];
		}
		//var_dump($job_array);
		
		ob_start();
		?>
		<fieldset style="width:1910px">
			<table width="1890"  cellspacing="0" >
				<tr class="form_caption" style="border:none;">
					<td align="center" style="border:none; font-size:18px;" colspan="20">
						<? echo $company_library[str_replace("'","",$cbo_company_id)]; ?>                                
					</td>
				</tr>
				<tr class="form_caption" style="border:none;">
					<td align="center" style="border:none;font-size:16px; font-weight:bold" colspan="20"> <? echo $report_title; if($cbo_category!=0) echo " For ".$item_category[$cbo_category]; ?></td>
				</tr>
				<tr class="form_caption" style="border:none;">
					<td align="center" style="border:none;font-size:12px; font-weight:bold"  colspan="20"> <? if( $date_from!="" && $date_to!="" ) echo "From ".change_date_format($date_from)." To ".change_date_format($date_to);?></td>
				</tr>
			</table>
            <table class="rpt_table" width="1890" cellpadding="0" cellspacing="0" border="1" rules="all">
				<thead>
					<th width="30">SL</th>    
					<th width="60">Wo No</th>
					<th width="60">Wo Year</th>
					<th width="70">Wo Date</th>
					<th width="70">Deli. Date</th>
                    <th width="70">Lead Time</th>
					<th width="100">Wo Type</th>
					<th width="100">Ready To App.</th>
					<th width="80">App. Status</th>
					<th width="100">Supplier</th>
					<th width="100">Buyer</th>
					<th width="110">Job No.</th>
					<th width="60">Job Year</th>
					<th width="100">Style Ref.</th>
					<th width="100">Inter. Ref.</th>
					<th width="100">File No.</th>
					<th width="100">Order No</th>
                    <th width="100">Shipment Date</th>
					<th width="80">Item Cate.</th>
					<th width="70">First Rec. Date</th>
                    <th width="100">Wo Qty</th>
					<th>Remarks</th>
				</thead>
			</table>
			<div style="max-height:350px; overflow-y:scroll; width:1910px" id="scroll_body" >
				<table cellspacing="0" cellpadding="0" border="1" class="rpt_table"  width="1890" rules="all" id="table_body" >
				<?
				if($cbo_category==24)
				{
					$sql="select a.id, a.ydw_no, min(b.receive_date) as receive_date from wo_yarn_dyeing_mst a,inv_receive_master b where a.ydw_no=b.booking_no and b.item_category=1 and b.company_id=$cbo_company and b.receive_basis=2 and b.receive_purpose=2 group by a.id, a.ydw_no";	
					$sql_result=sql_select($sql);
					foreach($sql_result as $row)
					{
						$first_rec_date[$row[csf("id")]]=$row[csf("receive_date")];
					}
				}
				if($db_type==0)
				{	
					$sql="Select a.id as booking_id, a.entry_form, a.ydw_no, Year(a.insert_date) as year_book, a.yarn_dyeing_prefix_num, a.booking_date, a.delivery_date, a.supplier_id, b.job_no, b.uom, sum(b.yarn_wo_qty) as wo_qnty from wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $company_id $supplier_cond and a.item_category_id=24 $job_num $booking_date_cond $wo_no_cond $wo_type_cond group by a.id, a.entry_form, a.ydw_no, a.insert_date, a.yarn_dyeing_prefix_num, a.booking_date, a.delivery_date, a.supplier_id, b.job_no, b.uom order by a.id Desc";
				}
				else if ($db_type==2)
				{
					$sql="Select a.id as booking_id, a.entry_form, a.ydw_no, TO_CHAR(a.insert_date,'YYYY') as year_book, a.yarn_dyeing_prefix_num, a.booking_date, a.delivery_date, a.supplier_id, b.job_no, b.uom, sum(b.yarn_wo_qty) as wo_qnty from wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $company_id $supplier_cond and a.item_category_id=24 $job_num $booking_date_cond $wo_no_cond $wo_type_cond group by a.id, a.entry_form, a.ydw_no, a.insert_date, a.yarn_dyeing_prefix_num, a.booking_date, a.delivery_date, a.supplier_id, b.job_no, b.uom order by a.id Desc";
				}
				//echo $sql; //die;
				//echo  $po_id ;die;
				$sql_result=sql_select($sql); $i=1;
				foreach($sql_result as $row)
				{
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					if ($row[csf("entry_form")]==41) $wo_type="Order";
					else if ($row[csf("entry_form")]==42) $wo_type="Non Order";
					$job_number=implode(",",array_unique(explode(",",$row[csf("job_no")])));
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
						<td width="30"><? echo $i;?></td>
						<td width="60" align="center"><? echo $row[csf("yarn_dyeing_prefix_num")]; ?></td>	<!--<a href='#report_details' onClick="generate_fabric_report('<? //echo $row[csf("entry_form")]; ?>','<? //echo $row[csf("ydw_no")]; ?>','<? //echo $cbo_company; ?>','<? //echo $row[csf("job_no")]; ?>','<? //echo "24"; ?>','<? //echo $cbo_category; ?>');"></a>-->
						<td width="60" align="center"><p><? echo $row[csf("year_book")]; ?></td>
						<td width="70"><div style="word-wrap:break-word; width:70px"><? echo change_date_format($row[csf("booking_date")], "", "",0); ?></div></td>
						<td width="70"><div style="word-wrap:break-word; width:70px"><? echo change_date_format($row[csf("delivery_date")]); ?></div></td>
                        
                        <td width="70"><div style="word-wrap:break-word; width:70px">
						<? 
						$lead_time=0;
						if($row[csf("delivery_date")]!="" && $row[csf("delivery_date")]!="0000-00-00")
						{
							$lead_time=datediff("d", $row[csf("booking_date")], $row[csf("delivery_date")]);
						}
						if($lead_time>1 || $lead_time<-1) echo $lead_time." Days"; else echo $lead_time." Day"; 
						?></div></td>
                        
						<td width="100"><p><? echo $wo_type;?></p></td>
						<td width="100" align="center"><? echo $yes_no[$row[csf("ready_to_approved")]]; ?></td>
						<td width="80"><p><? echo $approval_type_arr[$row[csf("is_approved")]]; ?></p></td>
						<td width="100"><div style="word-wrap:break-word; width:100px"><? echo $supplierArr[$row[csf("supplier_id")]]; ?></div></td>
						<td width="100"><p><? echo $buyerArr[$job_array[$job_number]['buyer']]; ?></p></td>
						<td width="110"><div style="word-wrap:break-word; width:110px"><? echo $row[csf("job_no")];//$row[csf("job_no_prefix_num")]; ?></div></td>
						<td width="60"><? echo $job_array[$job_number]['year']; ?></td>
						<td width="100"><div style="word-wrap:break-word; width:100px"><? echo $job_array[$job_number]['style_ref_no']; ?></div></td>
						<td width="100"><div style="word-wrap:break-word; width:100px"><? echo $job_array[$job_number]['ref']; ?></div></td>
						<td width="100"><div style="word-wrap:break-word; width:100px"><? echo $job_array[$job_number]['file']; ?></div></td>
						<td width="100"><div style="word-wrap:break-word; width:100px"><? echo $job_array[$job_number]['po_number']; ?></div></td>
                        <td width="100"><div style="word-wrap:break-word; width:100px">
						<? 
						if($job_array[$job_number]['max_ship_date']!="" && $job_array[$job_number]['max_ship_date']!="0000-00-00") echo "Max - ".change_date_format($job_array[$job_number]['max_ship_date']);
						//if($job_array[$job_number]['max_ship_date']!=$job_array[$job_number]['min_ship_date']) echo "<br> " . change_date_format($job_array[$job_number]['min_ship_date']); 
						
						$order_count=explode(",",$job_array[$job_number]['po_number']);
						if(count($order_count)>1) echo "<br> Min - " . change_date_format($job_array[$job_number]['min_ship_date']);
						 
						?></div></td>
						<td width="80"><div style="word-wrap:break-word; width:80px"><? echo $item_category[24]; ?></div></td>
						<td width="70" align="center"><p><? echo change_date_format($first_rec_date[$row[csf("booking_id")]]); ?></p></td>
						<td width="100" align="right"><? echo number_format($row[csf("wo_qnty")],2); ?></td>
						<td><? //echo $row[csf("po_number")]; ?></td>
					</tr>
					<?
					$tot_wo_qnty+=$row[csf("wo_qnty")];
					$i++;
				}
				?>
				</table>
				<table class="rpt_table" width="1890" cellpadding="0" cellspacing="0" border="1" rules="all">
					<tfoot>
						<th width="30">&nbsp;</th>
						<th width="60">&nbsp;</th>
						<th width="60">&nbsp;</th>
						<th width="70">&nbsp;</th>
						<th width="70">&nbsp;</th>
                        <th width="70">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="110">&nbsp;</th>
						<th width="60">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="100">&nbsp;</th>
                        <th width="100">&nbsp;</th>
						<th width="80">Total</th>
						<th width="70">&nbsp;</th>
                        <th width="100" align="right" id="tot_fin_fab_qnty"><? echo number_format($tot_wo_qnty,2); ?></th>
						<th>&nbsp;</th>
					</tfoot>
				</table>
			</div>
		</fieldset>
		<?
	}
	
	else if ($cbo_category==31)//31=Services Lab Test
	{
		if($db_type==0)
		{
			if ($year_id==0) $year_id_cond=""; else $year_id_cond=" and YEAR(a.insert_date)=$year_id";
		}
		elseif($db_type==2)
		{ 
			if ($year_id==0) $year_id_cond=""; else $year_id_cond=" and TO_CHAR(a.insert_date,'YYYY')=$year_id";
		}
		
		if ($cbo_company==0) $company_id=""; else $company_id=" and a.company_id=$cbo_company";
		if ($wo_no=="") $wo_no_cond=""; else $wo_no_cond=" and a.labtest_no like '%$wo_no'";
		if ($job_no=="") $job_num=""; else $job_num=" and b.job_no='$job_no'";
		if ($hidd_po=='') $po_no_no=""; else $po_no_no=" and c.order_id in ($hidd_po)";
		
		
		if ($cbo_buyer==0) $supplier_cond=""; else $supplier_cond=" and a.supplier_id=$cbo_buyer";
		if ($cbo_category==0) $category_id=""; else $category_id=" and a.item_category=$cbo_category";
		if ($wo_type==2) $wo_type_cond=" and a.entry_form=79"; else if ($wo_type==4) $wo_type_cond=" and a.entry_form=179"; else $wo_type_cond="";
		
		//if ($job_no=="") $job_num_mst=""; else $job_num_mst=" and b.job_no_mst=$job_no";
		if ($hidd_po=="") $po_id=""; else $po_id=" and d.id in ( $hidd_po )";
		
		if ($hidd_po=="") $po_id_cond=""; else $po_id_cond=" and b.po_break_down_id in ( $hidd_po )";
		
		if ($file_no=="") $file_no_cond=""; else $file_no_cond=" and d.file_no='".trim($file_no)."' "; 
		if ($internal_ref=="") $internal_ref_cond=""; else $internal_ref_cond=" and d.grouping='".trim($internal_ref)."' "; 
		//echo $file_no_cond.'=='.$internal_ref_cond;die;
		
		if($db_type==0)
		{
			if($txt_date_category==1) 
			{	
				if( $date_from=="" && $date_to=="" ) $booking_date_cond=""; else $booking_date_cond=" and a.wo_date between '".$date_from."' and '".$date_to."'";
			}
			else 
			{
				if($txt_date_category==2) if( $date_from=="" && $date_to=="" ) $booking_date_cond=""; else $booking_date_cond=" and a.delivery_date between '".$date_from."' and '".$date_to."'";
			}
		}
		if($db_type==2)
		{
			if($txt_date_category==1) 
			{
				if( $date_from=="" && $date_to=="" ) $booking_date_cond=""; else $booking_date_cond=" and a.wo_date between '".change_date_format($date_from,'dd-mm-yyyy','-',1)."' and '".change_date_format($date_to,'dd-mm-yyyy','-',1)."'";
			}
			else if($txt_date_category==2) 
			{
				if( $date_from=="" && $date_to=="" ) $booking_date_cond=""; else $booking_date_cond=" and a.delivery_date between '".change_date_format($date_from,'dd-mm-yyyy','-',1)."' and '".change_date_format($date_to,'dd-mm-yyyy','-',1)."'";
			}
		}
	
	
		$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name");
		$supplierArr=return_library_array( "select id,supplier_name from lib_supplier", "id", "supplier_name");	
		$buyerArr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name");
		$nonStyleArr=return_library_array( "select id, style_ref_no from sample_development_mst", "id", "style_ref_no");		
		
		$sql="select a.job_no,d.id as po_id from wo_po_details_master a, wo_po_break_down d where a.job_no=d.job_no_mst and a.status_active=1 and a.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $internal_ref_cond  $file_no_cond  group by a.job_no,d.id";
		$sql_result=sql_select($sql);
		$po_id_data="";$k=0;
		foreach($sql_result as $row)
		{
			if($k==0)
			{
				$po_data_cond=$row[csf('po_id')];
				 $job_data_cond=$row[csf('job_no')];
			}
			else
			{
				 $po_data_cond.=",".$row[csf('po_id')];
				 $job_data_cond.=",".$row[csf('job_no')];
			} 
		} //echo $job_data_cond;
		
		
		if($file_no_cond!="" || $internal_ref!="")
		{  
		 $po_data_file_ref=" and c.po_break_down_id in($po_data_cond) "; 
		 $job_data_file_ref=" and b.job_no in('$job_data_cond') "; 
		}
		else
		{
			$po_data_file_ref="";
			$job_data_file_ref="";
		}
		
		
		
	
			$job_sql="select a.job_no_prefix_num, a.job_no, a.buyer_name, TO_CHAR(a.insert_date,'YYYY') as year, a.style_ref_no, a.garments_nature, 
			LISTAGG(CAST(b.id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.id) as id,
			LISTAGG(CAST(b.po_number AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.po_number) as po_number,
			LISTAGG(CAST(b.file_no AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.file_no) as file_no,
			LISTAGG(CAST(b.grouping AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.grouping) as grouping,
			max(b.pub_shipment_date) as max_ship_date,
			min(b.pub_shipment_date) as min_ship_date
			from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.job_no_prefix_num, a.job_no, a.buyer_name, a.insert_date, a.style_ref_no, a.garments_nature";
		
		$job_sql_result=sql_select($job_sql);
		foreach($job_sql_result as $row)
		{
			$order_no=implode(",",array_unique(explode(",",$row[csf("po_number")])));
			$order_id=implode(",",array_unique(explode(",",$row[csf("id")])));
			$file_no=implode(",",array_unique(explode(",",$row[csf("file_no")])));
			$int_ref=implode(",",array_unique(explode(",",$row[csf("grouping")])));
	
			$job_array[$row[csf("job_no")]]['job']=$row[csf("job_no_prefix_num")];
			$job_array[$row[csf("job_no")]]['job_no']=$row[csf("job_no")];
			$job_array[$row[csf("job_no")]]['buyer']=$row[csf("buyer_name")];
			$job_array[$row[csf("job_no")]]['year']=$row[csf("year")];
			$job_array[$row[csf("job_no")]]['style_ref_no']=$row[csf("style_ref_no")];
			$job_array[$row[csf("job_no")]]['garments_nature']=$row[csf("garments_nature")];
			$job_array[$row[csf("job_no")]]['max_ship_date']=$row[csf("max_ship_date")];
			$job_array[$row[csf("job_no")]]['min_ship_date']=$row[csf("min_ship_date")];
			$job_array[$row[csf("job_no")]]['id']=$order_id;
			$job_array[$row[csf("job_no")]]['po_number']=$order_no;
			$job_array[$row[csf("job_no")]]['file']=$file_no;
			$job_array[$row[csf("job_no")]]['ref']=$int_ref;
		}
		
		$emb_item_array=array();
		
		$emb_sql="select a.emb_name, b.item_number_id, c.job_no_mst from wo_pre_cost_embe_cost_dtls a, wo_po_color_size_breakdown b, wo_po_break_down c where a.job_no=b.job_no_mst and c.id=b.po_break_down_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
		$emb_sql_result=sql_select($emb_sql);
		foreach($emb_sql_result as $row)
		{
			$emb_item_array[$row[csf("job_no_mst")]]['emb_name']=$row[csf("emb_name")];
			$emb_item_array[$row[csf("job_no_mst")]]['item_number_id']=$row[csf("item_number_id")];
		}
		//var_dump($job_array);
		
		ob_start();
		?>
		<fieldset style="width:2110px">
			<table width="2090"  cellspacing="0" >
				<tr class="form_caption" style="border:none;">
					<td align="center" style="border:none; font-size:18px;" colspan="21">
						<? echo $company_library[str_replace("'","",$cbo_company_id)]; ?>                                
					</td>
				</tr>
				<tr class="form_caption" style="border:none;">
					<td align="center" style="border:none;font-size:16px; font-weight:bold" colspan="22"> <? echo $report_title; if($cbo_category!=0) echo " For ".$item_category[$cbo_category]; ?></td>
				</tr>
				<tr class="form_caption" style="border:none;">
					<td align="center" style="border:none;font-size:12px; font-weight:bold"  colspan="22"> <? if( $date_from!="" && $date_to!="" ) echo "From ".change_date_format($date_from)." To ".change_date_format($date_to);?></td>
				</tr>
			</table>
            <table class="rpt_table" width="2090" cellpadding="0" cellspacing="0" border="1" rules="all">
				<thead>
					<th width="30">SL</th>    
					<th width="60">Wo No</th>
					<th width="60">Wo Year</th>
					<th width="70">Wo Date</th>
					<th width="70">Deli. Date</th>
                    <th width="70">Lead Time</th>
					<th width="100">Wo Type</th>
					<th width="100">Ready To App.</th>
					<th width="80">App. Status</th>
					<th width="100">Supplier</th>
					<th width="100">Buyer</th>
					<th width="110">Job No.</th>
					<th width="60">Job Year</th>
					<th width="100">Style Ref.</th>
					<th width="100">Inter. Ref.</th>
					<th width="100">Transation Ref.</th>
					<th width="100">Style No</th>
					<th width="100">File No.</th>
					<th width="100">Order No</th>
                    <th width="100">Shipment Date</th>
					<th width="80">Item Cate.</th>
					<th width="70">First Rec. Date</th>
                    <th width="100">WO value</th>
					<th>Remarks</th>
				</thead>
			</table>
			<div style="max-height:350px; overflow-y:scroll; width:2110px" id="scroll_body" >
				<table cellspacing="0" cellpadding="0" border="1" class="rpt_table"  width="2090" rules="all" id="table_body" >
				<?
				if($cbo_category==24)
				{
					$sql="select a.id, a.ydw_no, min(b.receive_date) as receive_date from wo_yarn_dyeing_mst a,inv_receive_master b where a.ydw_no=b.booking_no and b.item_category=1 and b.company_id=$cbo_company and b.receive_basis=2 and b.receive_purpose=2 group by a.id, a.ydw_no";	
					$sql_result=sql_select($sql);
					foreach($sql_result as $row)
					{
						$first_rec_date[$row[csf("id")]]=$row[csf("receive_date")];
					}
				}
				if ($wo_type==2)
					{
					$sql="Select max(a.currency) as currency, a.id as booking_id, a.entry_form, TO_CHAR(a.insert_date,'YYYY') as year_book, a.labtest_prefix_num, a.wo_date, a.delivery_date, a.supplier_id, a.ready_to_approved, b.job_no,b.referance_no, sum(c.wo_value) as wo_value from wo_labtest_mst a, wo_labtest_dtls b,wo_labtest_order_dtls c where a.id=b.mst_id and a.id=c.mst_id and b.id=c.dtls_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $company_id $job_num $booking_date_cond $wo_no_cond $wo_type_cond $po_no_no $year_id_cond group by a.id, a.entry_form, a.insert_date, a.labtest_prefix_num, a.wo_date, a.delivery_date, a.supplier_id,a.ready_to_approved,b.referance_no,  b.job_no order by a.id Desc";
					}
				
				else
				{
					$sql="Select max(a.currency) as currency, a.id as booking_id, a.entry_form, TO_CHAR(a.insert_date,'YYYY') as year_book, a.labtest_prefix_num, a.wo_date, a.delivery_date, a.supplier_id, a.ready_to_approved, b.job_no,b.referance_no,sum(b.wo_value) as wo_value,b.style_no from wo_labtest_mst a, wo_labtest_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $company_id $job_num $booking_date_cond $wo_no_cond $wo_type_cond $po_no_no $year_id_cond group by a.id, a.entry_form, a.insert_date, a.labtest_prefix_num, a.wo_date, a.delivery_date, a.supplier_id,a.ready_to_approved,b.referance_no,  b.job_no,b.style_no order by a.id Desc";

				}
				  
				  
				   
				//echo  $po_id ;die;
				$sql_result=sql_select($sql); $i=1;
				foreach($sql_result as $row)
				{
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					if ($row[csf("entry_form")]==79) $wo_type="Order";
					else if ($row[csf("entry_form")]==179) $wo_type="Non Order";
					$job_number=implode(",",array_unique(explode(",",$row[csf("job_no")])));
					
					
					
						//function generate_fabric_report(type,booking_no,company_id,order_id,fabric_nature,fabric_source,job_no,approved,category,is_short,emb_name,item_number_id,action_type,i,supplier_id)
						 //$variable="<input type='button' onClick=\"generate_fabric_report('','".$row[csf("booking_id")]."','".$cbo_company."','".$row[csf("currency")]."','".$cbo_category."','".change_date_format($row[csf("wo_date")])."','','','".$cbo_category."','','','','show_trim_booking_report','')\" style='width:50px;' name='print_p' id='print_pfc' class='formbutton' />".$row[csf("labtest_prefix_num")]."";	  
						 $variable="<a  href='##'  onClick=\"generate_fabric_report('','".$row[csf("booking_id")]."','".$cbo_company."','".$row[csf("currency")]."','".$cbo_category."','".change_date_format($row[csf("wo_date")])."','','','".$cbo_category."','','','','show_trim_booking_report','')\">".$row[csf("labtest_prefix_num")]."  <a/>";	 					
					
					
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
						<td width="30"><? echo $i;?></td>
						<td width="60" align="center"><? echo $variable; ?></td>
						<td width="60" align="center"><p><? echo $row[csf("year_book")]; ?></td>
						<td width="70"><div style="word-wrap:break-word; width:70px"><? echo change_date_format($row[csf("wo_date")], "", "",0); ?></div></td>
						<td width="70"><div style="word-wrap:break-word; width:70px"><? echo change_date_format($row[csf("delivery_date")]); ?></div></td>
                        <td width="70"><div style="word-wrap:break-word; width:70px">
						<? 
						$lead_time=0;
						if($row[csf("delivery_date")]!="" && $row[csf("delivery_date")]!="0000-00-00")
						{
							$lead_time=datediff("d", $row[csf("wo_date")], $row[csf("delivery_date")]);
						}
						if($lead_time>1 || $lead_time<-1) echo $lead_time." Days"; else echo $lead_time." Day"; 
						?></div></td>
						<td width="100"><? echo  $wo_type?></td>
						<td width="100" align="center"><? echo $yes_no[$row[csf("ready_to_approved")]]; ?></td>
						<td width="80"><p><? echo $approval_type_arr[$row[csf("is_approved")]]; ?></p></td>
						<td width="100"><div style="word-wrap:break-word; width:100px"><? echo $supplierArr[$row[csf("supplier_id")]]; ?></div></td>
						<td width="100"><p><? echo $buyerArr[$job_array[$job_number]['buyer']]; ?></p></td>
						<td width="110"><div style="word-wrap:break-word; width:110px"><? echo $row[csf("job_no")];//$row[csf("job_no_prefix_num")]; ?></div></td>
						<td width="60"><? echo $job_array[$job_number]['year']; ?></td>
						<td width="100"><div style="word-wrap:break-word; width:100px"><? echo $job_array[$job_number]['style_ref_no']; ?></div></td>
						<td width="100"><div style="word-wrap:break-word; width:100px"><? echo $job_array[$job_number]['ref']; ?></div></td>
						<td width="100"><div style="word-wrap:break-word; width:100px"><? echo $row[csf("referance_no")]; ?></div></td>
						<td width="100"><div style="word-wrap:break-word; width:100px"><? echo $row[csf("style_no")]; ?></div></td>
						<td width="100"><div style="word-wrap:break-word; width:100px"><? echo $job_array[$job_number]['file']; ?></div></td>
						<td width="100"><div style="word-wrap:break-word; width:100px"><? echo $job_array[$job_number]['po_number']; ?></div></td>
                        <td width="100"><div style="word-wrap:break-word; width:100px">
						<? 
						if($job_array[$job_number]['max_ship_date']!="" && $job_array[$job_number]['max_ship_date']!="0000-00-00") echo "Max - ".change_date_format($job_array[$job_number]['max_ship_date']);
						//if($job_array[$job_number]['max_ship_date']!=$job_array[$job_number]['min_ship_date']) echo "<br> " . change_date_format($job_array[$job_number]['min_ship_date']); 
						
						$order_count=explode(",",$job_array[$job_number]['po_number']);
						if(count($order_count)>1) echo "<br> Min - " . change_date_format($job_array[$job_number]['min_ship_date']);
						 
						?></div></td>
						<td width="80"><div style="word-wrap:break-word; width:80px"><? echo $item_category[$cbo_category]; ?></div></td>
						<td width="70" align="center"><p><? echo change_date_format($first_rec_date[$row[csf("booking_id")]]); ?></p></td>
						<td width="100" align="right"><? echo number_format($row[csf("wo_value")],2); ?></td>
						<td><? //echo $row[csf("po_number")]; ?></td>
					</tr>
					<?
					$tot_wo_qnty+=$row[csf("wo_value")];
					$i++;
				}
				?>
				</table>
				<table class="rpt_table" width="2090" cellpadding="0" cellspacing="0" border="1" rules="all">
					<tfoot>
						<th width="30">&nbsp;</th>
						<th width="60">&nbsp;</th>
						<th width="60">&nbsp;</th>
						<th width="70">&nbsp;</th>
						<th width="70">&nbsp;</th>
                        <th width="70">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="110">&nbsp;</th>
						<th width="60">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="100">&nbsp;</th>
                        <th width="100">&nbsp;</th>
						<th width="80">Total</th>
						<th width="70">&nbsp;</th>
                        <th width="100" align="right"><? echo number_format($tot_wo_qnty,2); ?></th>
						<th>&nbsp;</th>
					</tfoot>
				</table>
			</div>
		</fieldset>
		<?
	}

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



?>
