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

if($action=="load_drop_down_supplier")
{
	//$sql="select a.id, a.supplier_name from lib_supplier a, lib_supplier_tag_company b where a.status_active =1 and a.is_deleted=0 and a.id=b.supplier_id and b.tag_company='$data' and a.id in (select supplier_id from lib_supplier_party_type where party_type in (4)) order by a.supplier_name";

	$sql="select a.id, a.supplier_name from lib_supplier a, lib_supplier_tag_company b where a.status_active =1 and a.is_deleted=0 and a.id=b.supplier_id and b.tag_company='$data' order by a.supplier_name";
	echo create_drop_down( "cbo_supplier_id", 120, $sql,"id,supplier_name", 1, "--Select Supplier--", $selected, "" );   	 
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

/*if($action=="wo_no_popup")
{
  	echo load_html_head_contents("Order Info","../../../", 1, 1, '','1','');
	extract($_REQUEST);
	$ex_data=explode('_',$data);
	$company_id=$ex_data[0];
	$buyer_id=$ex_data[1];
	$year_id=$ex_data[2];
	$category_id=$ex_data[3];
	$wo_type=$ex_data[4];
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
	                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_buyer_id').value+'_'+<? echo $company_id; ?>+'_'+document.getElementById('txt_search_common').value+'_'+<? echo $year_id; ?>+'_'+<? echo $category_id; ?>+'_'+<? echo $wo_type; ?>, 'create_wo_search_list_view', 'search_div', 'wo_or_fabric_booking_report_controller', 'setFilterGrid(\'list_view\',-1);')" style="width:100px;" />
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
					<th width="80">WO No </th>
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
		if ($data[5]==2) $wo_type_cond=" and entry_form=41"; else if ($data[5]==4) $wo_type_cond=" and entry_form=42"; else $wo_type_cond="";
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
					<th width="80">WO No </th>
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
*/

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
	extract($_REQUEST);
	$cbo_company=str_replace("'","",$cbo_company_id);
	$cbo_buyer=str_replace("'","",$cbo_buyer_id);
	$cbo_supplier=str_replace("'","",$cbo_supplier_id);
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
	$type=str_replace("'","",$type);
	
		
	if ($cbo_company==0) $company_id=""; else $company_id=" and a.company_id=$cbo_company";
	if ($cbo_buyer) $buyer_id=" and a.buyer_id=$cbo_buyer"; else $buyer_id=""; 
	if ($cbo_supplier) $supplier_cond=" and a.supplier_id=$cbo_supplier"; else $supplier_cond=""; 

	if ($wo_no) $wo_no_cond=" and a.booking_no_prefix_num='$wo_no'"; else $wo_no_cond="";  
	if ($year_id)  $wo_no_cond.=" and a.booking_year='$year_id'"; else $wo_no_cond.=""; 
	
	if ($job_no)  $job_num=" and b.job_no='$job_no'"; else $job_num="";
	if ($hidd_po) $po_id_cond=" and b.po_break_down_id in ( $hidd_po )"; else $po_id_cond="";  
	
	
	if($db_type==0)
	{
		if( $date_from=="" && $date_to=="" ) $booking_date_cond=""; else $booking_date_cond=" and a.booking_date between '".$date_from."' and '".$date_to."'";
	}
	if($db_type==2)
	{

		if( $date_from=="" && $date_to=="" ) $booking_date_cond=""; else $booking_date_cond=" and a.booking_date between '".change_date_format($date_from,'dd-mm-yyyy','-',1)."' and '".change_date_format($date_to,'dd-mm-yyyy','-',1)."'";
	}

	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name");
	$supplierArr=return_library_array( "select id,supplier_name from lib_supplier", "id", "supplier_name");	
	$buyerArr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name");
	$style_ref_no_arr=return_library_array( "select job_no, style_ref_no from wo_po_details_master", "job_no", "style_ref_no");

	if($type==0)
	{
		if($db_type==0)
		{
			$select_program = "group_concat(b.program_no) as program_no";
			$select_po_id = "group_concat(b.po_break_down_id) as po_break_down_id";
		}
		else
		{
			$select_program = "listagg(b.program_no,',') within group (order by b.program_no) as program_no";
			$select_po_id = "listagg(b.po_break_down_id,',') within group (order by b.po_break_down_id) as po_break_down_id";
		}
			$sql="select a.company_id,a.booking_no, a.pay_mode, a.booking_date,a.is_approved, $select_program, b.job_no, $select_po_id ,
			a.supplier_id, a.buyer_id, sum(b.wo_qnty) as wo_qnty, b.rate,d.color_type_id,d.construction, d.composition
			from wo_booking_mst a, wo_booking_dtls b,  wo_pre_cost_fab_conv_cost_dtls c, wo_pre_cost_fabric_cost_dtls d
			where a.booking_no = b.booking_no and b.pre_cost_fabric_cost_dtls_id = c.id and c.fabric_description = d.id and a.booking_type = 3 
			and a.item_category = 12 and a.process = 1 and a.status_active=1 and b.status_active=1  and c.is_deleted=0
			$booking_date_cond $company_id $buyer_id $wo_no_cond $job_num $po_id_cond $supplier_cond
			group by  a.company_id,a.booking_no, a.pay_mode, a.booking_date, b.job_no, a.buyer_id,a.is_approved,a.supplier_id, b.rate,d.color_type_id,d.construction, d.composition";
			
			$sql_result=sql_select($sql); 
			$job_arr = array();
			foreach ($sql_result as $val) 
			{
				$job_arr[$val[csf("job_no")]] = $val[csf("job_no")];
			}

			$job_nos = "'".implode("','", array_filter($job_arr))."'";
			if($job_nos=="") $job_nos=0;
			$jobCond = $job_nos_cond = ""; 
			$job_nos_arr=explode(",",$job_nos);
			if($db_type==2 && count($job_nos_arr)>999)
			{
				$job_nos_chunk=array_chunk($job_nos_arr,999) ;
				foreach($job_nos_chunk as $chunk_arr)
				{
					$jobCond.=" a.job_no in(".implode(",",$chunk_arr).") or ";	
				}
						
				$job_nos_cond.=" and (".chop($jobCond,'or ').")";			
				
			}
			else
			{ 	
				$job_nos_cond=" and a.job_no in($job_nos)";
			}


			$job_sql = sql_select("select a.job_no, a.style_ref_no, b.id, b.po_number
				from wo_po_details_master a, wo_po_break_down b
				where a.job_no = b.job_no_mst and b.status_active = 1 and a.status_active = 1 $job_nos_cond");

			foreach ($job_sql as $value) 
			{
				$style_ref_no_arr[$value[csf("job_no")]] = $value[csf("style_ref_no")];
				$order_no_arr[$value[csf("id")]] = $value[csf("po_number")];

			}
			
			$print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$cbo_company."' and module_id=2 and report_id=65 and is_deleted=0 and status_active=1");
			//echo $print_report_format;die;
			$format_ids=explode(",",$print_report_format);
			$row_id=$format_ids[0];
			$report_action="show_service_booking_report";
			if($row_id=15)  $report_action="show_service_booking_report2";
			ob_start();
			?>
			<fieldset>
				<table width="1780"  cellspacing="0" >
					<tr class="form_caption" style="border:none;">
						<td align="center" style="border:none; font-size:18px;" colspan="16">
							<? echo $company_library[str_replace("'","",$cbo_company_id)]; ?>                                
						</td>
					</tr>
					<tr class="form_caption" style="border:none;">
						<td align="center" style="border:none;font-size:16px; font-weight:bold"  colspan="16"> <? echo $report_title; ?></td>
					</tr>
					<tr class="form_caption" style="border:none;">
						<td align="center" style="border:none;font-size:12px; font-weight:bold"  colspan="16"> <? if( $date_from!="" && $date_to!="" ) echo "From ".change_date_format($date_from)." To ".change_date_format($date_to);?></td>
					</tr>
				</table>
				<table width="1780" cellspacing="0" border="1" class="rpt_table" rules="all">
					<thead>
						<tr>
						<th width="30">SL</th>    
						<th width="100"><p style="word-break: break-all;word-wrap: break-word;">Work Order Date</p></th>
						<th width="120"><p style="word-break: break-all;word-wrap: break-word;">Work Order No</p></th>
						<th width="150">Program No</th>
						<th width="120">Job No.</th>
						<th width="120">Buyer</th>
						<th width="120">Style Ref.</th>
						<th width="120">Order No</th>
						<th width="120">Sub Con.Party Name</th>
						<th width="100">Color/Category</th>
						<th width="110">Fabric Type</th>
						<th width="150">Composition</th>
						<th width="100"><p style="word-break: break-all;word-wrap: break-word;">Booking Qnty(kg)</p></th>
						<th width="80">Rate(tk)</th>
						<th width="100">Total Amount</th>
						<th width="100"><p style="word-break: break-all;word-wrap: break-word;">Approval Status</p></th>
						</tr>
					</thead>
				</table>
				<div style="max-height:350px; overflow-y:scroll; width:1798px" id="scroll_body" >
					<table cellspacing="0" cellpadding="0" border="1" class="rpt_table"  width="1780" rules="all" id="table_body" >
					<?
					
					$i=1;
					foreach($sql_result as $row)
					{
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$supplier_str="";
						if($row[csf("pay_mode")]==3 || $row[csf("pay_mode")]==5) $supplier_str=$company_library[$row[csf("supplier_id")]]; else  $supplier_str=$supplierArr[$row[csf("supplier_id")]];
						
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
							<td width="30"><? echo $i;?></td>	
							<td width="100" align="center"><p><? echo change_date_format($row[csf("booking_date")]); ?></p></td>
							<td width="120" align="center"><p style="word-break: break-all;word-wrap: break-word;"><a href="##" onClick="generate_trim_report('<? echo $row[csf("booking_no")];?>','<?php echo $row[csf("company_id")];?>','<?php echo $report_action;?>')"><? echo $row[csf("booking_no")]; ?></a></p></td><td width="150" align="center">
								<p style="word-break: break-all;word-wrap: break-word;">
									<? 
										echo implode(",",array_filter(array_unique(explode(",", $row[csf("program_no")]))));
										//echo $row[csf("program_no")]; 
									?>
								</p>
							</td>
							<td width="120" align="center"><p><? echo $row[csf("job_no")]; ?></p></td>
							<td width="120" align="center"><p style="word-break: break-all;word-wrap: break-word;"><? echo $buyerArr[$row[csf("buyer_id")]]; ?></p></td>
							<td width="120" align="center"><p style="word-break: break-all;word-wrap: break-word;"><? echo $style_ref_no_arr[$row[csf("job_no")]]; ?></p></td>
							<td width="120" align="center">
								<p style="word-break: break-all;word-wrap: break-word;">
									<? 
										$po_number="";
										foreach (array_filter(array_unique(explode(",", $row[csf("po_break_down_id")]))) as $val) 
										{
											$po_number .= $order_no_arr[$val].",";
										}
										echo chop($po_number,","); 
									?>
								</p>
							</td>
							<td width="120" align="center"><p style="word-break: break-all;word-wrap: break-word;"><? echo $supplier_str; ?></p></td>
							<td width="100" align="center"><p style="word-break: break-all;word-wrap: break-word;"><? echo $color_type[$row[csf("color_type_id")]]; ?></p></td>
							<td width="110" align="center"><p style="word-break: break-all;word-wrap: break-word;"><? echo $row[csf("construction")]; ?></p></td>
							<td width="150" align="center"><p style="word-break: break-all;word-wrap: break-word;"><? echo $row[csf("composition")]; ?></p></td>
							<td width="100" align="right"><p style="word-break: break-all;word-wrap: break-word;"><? echo number_format($row[csf("wo_qnty")],2); ?></p></td>
							<td width="80" align="right"><p style="word-break: break-all;word-wrap: break-word;"><? echo number_format($row[csf("rate")],2); ?></p></td>
							<td width="100" align="right"><p style="word-break: break-all;word-wrap: break-word;"><? echo number_format($row[csf("wo_qnty")]*$row[csf("rate")],2); ?></p></td>
							<td width="100" align="center"><p style="word-break: break-all;word-wrap: break-word;"><? echo $approval_type_arr[$row[csf("is_approved")]]; ?></p></td>
						</tr>
						<?
						$i++;
						$total_wo_qnty +=  $row[csf("wo_qnty")];
						$total_wo_amt +=  $row[csf("wo_qnty")]*$row[csf("rate")];
					}
					?>
					</table>
					<table rules="all" class="rpt_table" width="1780" cellspacing="0" cellpadding="0" border="1">
						<tfoot>
							<tr>
								<th align="right" width="30"><p>&nbsp;</p></th>
								<th align="right" width="100"><p>&nbsp;</p></th>
								<th align="right" width="120"><p>&nbsp;</p></th>
								<th align="right" width="150"><p>&nbsp;</p></th>
								<th align="right" width="120"><p>&nbsp;</p></th>
								<th align="right" width="120"><p>&nbsp;</p></th>
								<th align="right" width="120"><p>&nbsp;</p></th>
								<th align="right" width="120"><p>&nbsp;</p></th>
								<th align="right" width="120"><p>&nbsp;</p></th>
								<th align="right" width="100"><p>&nbsp;</p></th>
								<th align="right" width="110"><p>&nbsp;</p></th>
								<th align="right" width="150"><p>Total : </p></th>
								<th align="right" width="100" id="value_total_wo_qnty"><p style="word-break: break-all;word-wrap: break-word;"><? echo number_format($total_wo_qnty,2,".",""); ?></p></th>
								<th align="right" width="80"><p>&nbsp;</p></th>
								<th align="right" width="100" id="value_total_wo_amnt" title="<? echo $total_wo_amt;?>"><p style="word-break: break-all;word-wrap: break-word;"><? echo number_format($total_wo_amt,2,".",""); ?></p></th>
								<th width="100"><p>&nbsp;</p></th>
							</tr>
						</tfoot>
					</table>
				</div>

			</fieldset>
			<?
	}
	else if($type==1)
	{
		if($db_type==0)
		{
			$select_program = "group_concat(b.program_no) as program_no";
			$select_po_id = "group_concat(b.po_break_down_id) as po_break_down_id";
		}
		else
		{
			$select_program = "listagg(b.program_no,',') within group (order by b.program_no) as program_no";
			$select_po_id = "listagg(b.po_break_down_id,',') within group (order by b.po_break_down_id) as po_break_down_id";
		}
			$sql="select a.company_id,a.booking_no, a.pay_mode, a.booking_date,a.is_approved, $select_program, b.job_no, $select_po_id ,a.supplier_id, a.buyer_id, sum(b.wo_qnty) as wo_qnty, b.rate,a.process,b.pre_cost_fabric_cost_dtls_id,a.exchange_rate
			from wo_booking_mst a, wo_booking_dtls b,  wo_pre_cost_fab_conv_cost_dtls c, wo_pre_cost_fabric_cost_dtls d
			where a.booking_no = b.booking_no and b.pre_cost_fabric_cost_dtls_id = c.id and c.fabric_description = d.id and a.booking_type = 3 and a.item_category = 12  and a.status_active=1 and b.status_active=1 and a.process = 1
			$booking_date_cond $company_id $buyer_id $wo_no_cond $job_num $po_id_cond $supplier_cond
			group by  a.company_id,a.booking_no, a.pay_mode, a.booking_date, b.job_no, a.buyer_id,a.is_approved,a.supplier_id, b.rate,a.process,b.pre_cost_fabric_cost_dtls_id,a.exchange_rate";
			
			$sql_result=sql_select($sql); 
			$job_arr = array();
			$program_no_arr=array();
			$booking_no_arr=array();
			foreach ($sql_result as $val) 
			{
				$job_arr[$val[csf("job_no")]] = $val[csf("job_no")];
				if(!empty($val[csf("program_no")]))
				{
					$prog_str=explode(",", $val[csf("program_no")]);
					foreach ($prog_str as $key => $pro_no) {
						array_push($program_no_arr, $pro_no);
					}
					
				}
				if(!empty($val[csf("booking_no")]))
				{
					array_push($booking_no_arr, $val[csf("booking_no")]);
					
				}
				
			}
			$prog_cond=where_con_using_array(array_unique($program_no_arr),0,"b.id");
			$job_cond=where_con_using_array(array_unique($job_arr),1,"job_no");
			$booking_cond=where_con_using_array(array_unique($booking_no_arr),1,"a.booking_no");


			$sql_rcv="SELECT SUM (d.grey_receive_qnty) AS QNTY, a.booking_no, a.recv_number,a.id
					    FROM inv_receive_master a, inv_transaction c,pro_grey_prod_entry_dtls d 
					   WHERE     a.id = c.mst_id
					   		 and a.id=d.mst_id 
					   		 and c.id=d.trans_id
					         and a.entry_form in (22,23, 37)
					         and a.status_active = 1
					         and a.is_deleted = 0
					         and c.status_active = 1
					         and c.is_deleted = 0
					         and d.status_active = 1
					         and d.is_deleted = 0
					         $booking_cond
					GROUP BY a.booking_no, a.recv_number,a.id";
			//echo $sql_rcv;
			$recv_res=sql_select($sql_rcv);
			$booking_wise_rcv=array();
			$rcv_id_arr=array();
		    foreach ($recv_res as $row) {
		    	$booking_wise_rcv[$row[csf('booking_no')]]['qnty']+=$row['QNTY'];
		    	$booking_wise_rcv[$row[csf('booking_no')]]['recv_number'].=$row[csf('recv_number')]."***";
		    	$booking_wise_rcv[$row[csf('booking_no')]]['id'].=$row[csf('id')]."***";
		    	array_push($rcv_id_arr, $row[csf('id')]);
		    }
		    $booking_cond=str_replace("a.booking_no", "a.service_booking_no", $booking_cond);

					$sql_roll_recv="SELECT 
					         SUM (
					            CASE WHEN a.entry_form=2 and c.entry_form=2 THEN c.qc_pass_qnty ELSE 0 END)
					            AS QC_PASS_QTY,
					         SUM (
					            CASE WHEN a.entry_form=58 and c.entry_form=58 THEN c.qnty ELSE 0 END)
					            AS QTY,
					            a.service_booking_no,
					            c.barcode_no
					  FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c
					 WHERE     a.id = b.mst_id
					       AND b.id = c.dtls_id
					       AND a.entry_form in( 2,58)
					       AND c.entry_form in (2,58)
					       AND c.status_active = 1
					       AND c.is_deleted = 0
					       $booking_cond 
					 GROUP BY a.service_booking_no,
					            c.barcode_no";
					//echo $sql_roll_recv;
				$roll_recv_res=sql_select($sql_roll_recv);
				$barcode_no_arr=array();
				$booking_data_from_barcode=array();
				foreach ($roll_recv_res as $row) 
				{
					$booking_wise_rcv[$row[csf('service_booking_no')]]['qnty']+=$row['QC_PASS_QTY'];
					$booking_wise_rcv[$row[csf('service_booking_no')]]['qnty']+=$row['QTY'];
					array_push($barcode_no_arr, $row[csf('barcode_no')]);
					$booking_data_from_barcode[$row[csf('barcode_no')]]=$row[csf('service_booking_no')];
		    		//$booking_wise_rcv[$row[csf('service_booking_no')]]['recv_number'].=$row[csf('recv_number')]."***";
				}
				
				$barcode_cond=where_con_using_array(array_unique($barcode_no_arr),1,"c.barcode_no");

				$bar_rcv_sql=sql_select("SELECT a.id,
							       a.recv_number,
							       c.barcode_no
							  FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c
							 WHERE     a.id = b.mst_id
							       AND b.id = c.dtls_id
							       AND a.entry_form = 58
							       AND c.entry_form = 58
							       AND c.status_active = 1
							       AND c.is_deleted = 0
							       $barcode_cond
							     group by a.id,
							       a.recv_number,
							       c.barcode_no");
				foreach ($bar_rcv_sql as $row) {
					$booking_no=$booking_data_from_barcode[$row[csf('barcode_no')]];
					$booking_wise_rcv[$booking_no]['id'].=$row[csf('id')]."***";
					$booking_wise_rcv[$booking_no]['recv_number'].=$row[csf('recv_number')]."***";
					array_push($rcv_id_arr, $row[csf('id')]);
				}
				$rcv_id_cond=where_con_using_array(array_unique($rcv_id_arr),0,"receive_id");
				$sql_bill=sql_select("SELECT receive_id, challan_no FROM subcon_outbound_bill_dtls WHERE  status_active=1 and is_deleted=0 $rcv_id_cond group by receive_id, challan_no");
			   $bill_data=array();

			   foreach ($sql_bill as $row) {
			   		$bill_data[$row[csf('receive_id')]].=$row[csf('challan_no')]."***";
			   }
			   $booking_cond=str_replace("a.service_booking_no", "b.work_order_no", $booking_cond);
			   $acceptance_res=sql_select("SELECT b.work_order_no,
						       b.work_order_id,
						       b.work_order_dtls_id,
						       a.pi_number,
						       a.id,
						       d.invoice_no
						  FROM com_pi_master_details a,
						       com_pi_item_details b,
						       com_btb_lc_master_details c,
						       com_import_invoice_mst d,
						       com_btb_lc_pi e
						 WHERE       a.id = b.pi_id
						       AND c.id = d.btb_lc_id
						       AND a.id = e.pi_id
						       AND c.id = e.com_btb_lc_master_details_id
						       AND a.status_active = 1
						       AND a.is_deleted = 0
						       AND b.status_active = 1
						       AND b.is_deleted = 0
						       AND c.status_active = 1
						       AND c.is_deleted = 0
						       AND d.status_active = 1
						       AND d.is_deleted = 0
						       AND e.status_active = 1
						       AND e.is_deleted = 0
						        $booking_cond
						        ");
				//echo  $acceptance;
			
			foreach ($acceptance_res as $row) {
				$booking_wise_rcv[$row[csf('work_order_no')]]['invoice_no'].=$row[csf('invoice_no')]."***";
			}
			
			$booking_arr_prog = return_library_array("SELECT a.booking_no as booking_no,b.id as id FROM ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b
			 WHERE     a.id = b.mst_id
			       AND b.status_active = 1
			       AND b.is_deleted = 0
			       $prog_cond","id","booking_no");

			$wo_pre_cost_fab_conv_cost_dtls_id=sql_select("select id,fabric_description,cons_process from wo_pre_cost_fab_conv_cost_dtls  where 1=1 $job_cond ");
				foreach( $wo_pre_cost_fab_conv_cost_dtls_id as $row_wo_pre_cost_fab_conv_cost_dtls_id)
				{
					if($row_wo_pre_cost_fab_conv_cost_dtls_id[csf("fabric_description")]!=0)
					{

						$fabric_description=sql_select("select id,body_part_id,color_type_id,fabric_description from  wo_pre_cost_fabric_cost_dtls
						where  id='".$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("fabric_description")]."'");
						list($fabric_description_row)=$fabric_description;
						$fabric_description_array[$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("id")]]=$body_part[$fabric_description_row[csf("body_part_id")]].',
						'.$color_type[$fabric_description_row[csf("color_type_id")]].', '.$fabric_description_row[csf("fabric_description")];
					}
					if($row_wo_pre_cost_fab_conv_cost_dtls_id[csf("fabric_description")]==0)
					{

						$fabric_description_string="";
						$fabric_description=sql_select("select id,body_part_id,color_type_id,fabric_description from  wo_pre_cost_fabric_cost_dtls
						where  job_no='$job_no'");
						foreach( $fabric_description as $fabric_description_row)
				        {
						$fabric_description_string.=$body_part[$fabric_description_row[csf("body_part_id")]].', '.$color_type[$fabric_description_row[csf("color_type_id")]].', '.$fabric_description_row[csf("fabric_description")]." and ";
						}
						$fabric_description_array[$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("id")]].=rtrim($fabric_description_string,"and ");
					}
				}
			
			// echo "<pre>";
			// print_r($booking_arr_prog);
			// echo "</pre>";
			$job_nos = "'".implode("','", array_filter($job_arr))."'";
			if($job_nos=="") $job_nos=0;
			$jobCond = $job_nos_cond = ""; 
			$job_nos_arr=explode(",",$job_nos);
			if($db_type==2 && count($job_nos_arr)>999)
			{
				$job_nos_chunk=array_chunk($job_nos_arr,999) ;
				foreach($job_nos_chunk as $chunk_arr)
				{
					$jobCond.=" a.job_no in(".implode(",",$chunk_arr).") or ";	
				}
						
				$job_nos_cond.=" and (".chop($jobCond,'or ').")";			
				
			}
			else
			{ 	
				$job_nos_cond=" and a.job_no in($job_nos)";
			}


			$job_sql = sql_select("select a.job_no, a.style_ref_no, b.id, b.po_number
				from wo_po_details_master a, wo_po_break_down b
				where a.job_no = b.job_no_mst and b.status_active = 1 and a.status_active = 1 $job_nos_cond");

			foreach ($job_sql as $value) 
			{
				$style_ref_no_arr[$value[csf("job_no")]] = $value[csf("style_ref_no")];
				$order_no_arr[$value[csf("id")]] = $value[csf("po_number")];

			}
			
			$print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$cbo_company."' and module_id=2 and report_id=65 and is_deleted=0 and status_active=1");
			//echo $print_report_format;die;
			$format_ids=explode(",",$print_report_format);
			$row_id=$format_ids[0];
			$report_action="show_service_booking_report";
			if($row_id=15)  $report_action="show_service_booking_report2";
			ob_start();
			?>
			<fieldset>
				<table width="1780"  cellspacing="0" >
					<tr class="form_caption" style="border:none;">
						<td align="center" style="border:none; font-size:18px;" colspan="16">
							<? echo $company_library[str_replace("'","",$cbo_company_id)]; ?>                                
						</td>
					</tr>
					<tr class="form_caption" style="border:none;">
						<td align="center" style="border:none;font-size:16px; font-weight:bold"  colspan="16"> <? echo $report_title; ?></td>
					</tr>
					<tr class="form_caption" style="border:none;">
						<td align="center" style="border:none;font-size:12px; font-weight:bold"  colspan="16"> <? if( $date_from!="" && $date_to!="" ) echo "From ".change_date_format($date_from)." To ".change_date_format($date_to);?></td>
					</tr>
				</table>
				<table width="1780" cellspacing="0" border="1" class="rpt_table" rules="all">
					<thead>
						<tr>
						<th width="30">SL</th>    
						<th width="120">Party Name</th>
						<th width="120">Process</th>
						<th width="120"><p style="word-break: break-all;word-wrap: break-word;">Service Booking No.</p></th>
						<th width="100"><p style="word-break: break-all;word-wrap: break-word;">Service Booking Date.</p></th>
						<th width="120">Buyer</th>
						<th width="120">Job No.</th>
						<th width="150">Fabrics Booking No.</th>
						
						
						
						<th width="120">P.O No.</th>
						
						
						<th width="250">Fabrics Description</th>
						<th width="100"><p style="word-break: break-all;word-wrap: break-word;">WO Quantity</p></th>
						<th width="100"><p style="word-break: break-all;word-wrap: break-word;">Grey Fabrics<br>Received Qty</p></th>

						<th width="80">Rate(tk)</th>
						<th width="100">Total Amount</th>
						<th width="100"><p style="word-break: break-all;word-wrap: break-word;">Paid Status</p></th>
						</tr>
					</thead>
				</table>
				<div style="max-height:350px; overflow-y:scroll; width:1798px" id="scroll_body" >
					<table cellspacing="0" cellpadding="0" border="1" class="rpt_table"  width="1780" rules="all" id="table_body" >
					<?
					
					$i=1;
					foreach($sql_result as $row)
					{
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$supplier_str="";
						if($row[csf("pay_mode")]==3 || $row[csf("pay_mode")]==5) $supplier_str=$company_library[$row[csf("supplier_id")]]; else  $supplier_str=$supplierArr[$row[csf("supplier_id")]];
						$rcv_qnty=$booking_wise_rcv[$row[csf('booking_no')]]['qnty'];
						$recv_number=implode(", ", array_unique(explode("***", chop($booking_wise_rcv[$row[csf('booking_no')]]['recv_number'],"***"))));
						$paid_unpaid_status="Unpaid";
						if(!empty($booking_wise_rcv[$row[csf('booking_no')]]['invoice_no']))
						{
							$paid_unpaid_status="Paid";
						}

						if($row[csf("exchange_rate")]*1>0) $rate=$row[csf("rate")]*$row[csf("exchange_rate")];
						else $rate=$row[csf("rate")];
						
						//$rcv_id=implode(",", array_unique(explode("***", chop($booking_wise_rcv[$row[csf('booking_no')]]['id'],"***"))));
						/*
						foreach (array_unique(explode("***", chop($booking_wise_rcv[$row[csf('booking_no')]]['id'],"***"))) as $key => $val) {
							
							if(!empty($bill_data[$val]))
							{
								$paid_unpaid_status="Paid";
								break;
							}
						}
						*/
						
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
							<td width="30"><? echo $i;?></td>
							<td width="120" align="center"><p style="word-break: break-all;word-wrap: break-word;"><? echo $supplier_str; ?></p></td>	
							<td width="120" align="center"><p><? echo $conversion_cost_head_array[$row[csf("process")]]; ?></p></td>
							<td width="120" align="center">
								<p style="word-break: break-all;word-wrap: break-word;"><? echo $row[csf("booking_no")]; ?></p>
							</td>
							<td width="100" align="center"><p><? echo change_date_format($row[csf("booking_date")]); ?></p></td>
							<td width="120" align="center"><p style="word-break: break-all;word-wrap: break-word;"><? echo $buyerArr[$row[csf("buyer_id")]]; ?></p></td>
							<td width="120" align="center"><p><? echo $row[csf("job_no")]; ?></p></td>
							<td width="150" align="center">
								<p style="word-break: break-all;word-wrap: break-word;">
									<? 
										$prog_arr=array_filter(array_unique(explode(",", $row[csf("program_no")])));
										$fb_str='';
										foreach ($prog_arr as $key => $pro_n) {
											$fb_str.=$booking_arr_prog[$pro_n]."***";
										}
										;
										echo implode(", ", array_unique(explode("***", chop($fb_str,"***"))));
										//echo $row[csf("program_no")]; 
									?>
								</p>
							</td>
							
							
							
							<td width="120" align="center">
								<p style="word-break: break-all;word-wrap: break-word;">
									<? 
										$po_number="";
										foreach (array_filter(array_unique(explode(",", $row[csf("po_break_down_id")]))) as $key => $val) 
										{
											$po_number .= $order_no_arr[$val].",";
											//echo "<pre>".$val."=".$order_no_arr[$val]."</pre>";
										}
										//echo $row[csf("po_break_down_id")]."h";
										echo chop($po_number,","); 
									?>
								</p>
							</td>
							
							
							<td width="250" align="center"><p style="word-break: break-all;word-wrap: break-word;"><? echo  $fabric_description_array[$row[csf('pre_cost_fabric_cost_dtls_id')]]; ?></p></td>
							<td width="100" align="right"><p style="word-break: break-all;word-wrap: break-word;"><? echo number_format($row[csf("wo_qnty")],2); ?></p></td>
							<td width="100" align="right" title="<?=$recv_number;?>"><p style="word-break: break-all;word-wrap: break-word;"><?=number_format($rcv_qnty,2);?></p></td>
							<td width="80" align="right" title="<?='rate: '.$row[csf('rate')].' , exchange_rate: '.$row[csf('exchange_rate')];?>"><p style="word-break: break-all;word-wrap: break-word;"><? echo number_format($rate,2); ?></p></td>
							<td width="100" align="right"><p style="word-break: break-all;word-wrap: break-word;"><? echo number_format($rcv_qnty*$rate,2); ?></p></td>
							<td width="100" align="center"><p style="word-break: break-all;word-wrap: break-word;"><?=$paid_unpaid_status;?></p></td>
						</tr>
						<?
						$i++;
						
						
						$total_wo_qnty +=  $row[csf("wo_qnty")];
						$total_wo_amt +=  $rcv_qnty*$rate;
					}
					?>
					</table>
					<table rules="all" class="rpt_table" width="1780" cellspacing="0" cellpadding="0" border="1">
						<tfoot>
							<tr>
								<th align="right" width="30"><p>&nbsp;</p></th>
								<th align="right" width="120"><p>&nbsp;</p></th>
								<th align="right" width="120"><p>&nbsp;</p></th>
								<th align="right" width="120"><p>&nbsp;</p></th>
								<th align="right" width="100"><p>&nbsp;</p></th>
								<th align="right" width="120"><p>&nbsp;</p></th>
								<th align="right" width="120"><p>&nbsp;</p></th>
								<th align="right" width="150"><p>&nbsp;</p></th>
								
								<th align="right" width="120"><p>&nbsp;</p></th>
								
								
								<th align="right" width="250"><p>Total : </p></th>
								<th align="right" width="100" id="value_total_wo_qnty"><p style="word-break: break-all;word-wrap: break-word;"><? echo number_format($total_wo_qnty,2,".",""); ?></p></th>
								<th align="right" width="100" id="value_total_recv_qnty"><p style="word-break: break-all;word-wrap: break-word;"><? echo number_format(0,2,".",""); ?></p></th>
								<th align="right" width="80"><p>&nbsp;</p></th>
								<th align="right" width="100" id="value_total_wo_amnt" title="<? echo $total_wo_amt;?>"><p style="word-break: break-all;word-wrap: break-word;"><? echo number_format($total_wo_amt,2,".",""); ?></p></th>
								<th width="100"><p>&nbsp;</p></th>
							</tr>
						</tfoot>
					</table>
				</div>

			</fieldset>
			<?
	}
	
	
	$html = ob_get_contents();
    ob_clean();
    //$new_link=create_delete_report_file( $html, 2, $delete, "../../../" );
    foreach (glob("*.xls") as $filename) 
    {
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
