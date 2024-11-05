<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');
include('../../../includes/class4/class.conditions.php');
include('../../../includes/class4/class.reports.php');
include('../../../includes/class4/class.emblishments.php');
include('../../../includes/class4/class.washes.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_id", 130, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name", 1, "--Select Buyer--", $selected, "" );
	exit();
}

if($action=="load_drop_down_supplier")
{
	$data=explode('_',$data);
	if($data[1]==1)//Company load
	{
		 echo create_drop_down( "cbo_supplier", 130, "select id,company_name from lib_company where status_active=1 and is_deleted=0 order by company_name", "id,company_name",1, "-- Select Company --", "", "",0,"" );
	}
	else
	{
	echo create_drop_down( "cbo_supplier", 130, "select a.id, a.supplier_name from lib_supplier a, lib_supplier_tag_company b where a.status_active =1 and a.is_deleted=0 and a.id=b.supplier_id and b.tag_company='$data[0]' and a.id in (select supplier_id from lib_supplier_party_type where party_type in (4,5)) order by a.supplier_name","id,supplier_name", 1, "--Select Supplier--", $selected, "" );
	}
	exit();
}

if($action=="company_wise_report_button_setting")
{
	extract($_REQUEST);

	$print_report_format=return_field_value("format_id"," lib_report_template","template_name ='".$data."'  and module_id=2 and report_id=251 and is_deleted=0 and status_active=1");
	
	//echo $print_report_format; disconnect($con); die;
	
	$print_report_format_arr=explode(",",$print_report_format);
	//print_r($print_report_format_arr);
	echo "$('#show_button1').hide();\n";
	echo "$('#show_button2').hide();\n";
	echo "$('#show_button3').hide();\n";
	echo "$('#show_button4').hide();\n";

	if($print_report_format != "")
	{
		foreach($print_report_format_arr as $id)
		{
			
			if($id==108){echo "$('#show_button1').show();\n";}
			if($id==195){echo "$('#show_button2').show();\n";}
			if($id==242){echo "$('#show_button3').show();\n";}	
			if($id==359){echo "$('#show_button4').show();\n";}						
		}
	}
	exit();	
}

if ($action=="item_popup")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$data=explode('_',$data);
	//print_r ($data);

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
			$("#tr_"+i).click();
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
		$('#txt_item_id').val( id );
		$('#txt_item_val').val( ddd );
	}

	</script>
     <input type="hidden" id="txt_item_id" />
     <input type="hidden" id="txt_item_val" />
     <?
	$sql="select id, item_name, trim_uom from lib_item_group where item_category=4 and is_deleted=0 and status_active=1 order by item_name";
	//echo  $sql;die;
	$arr=array(3=>$garments_item);
	echo  create_list_view("list_view", "Item Group", "150","450","360",0, $sql , "js_set_value", "id,item_name", "", 1, "0", $arr , "item_name", "",'setFilterGrid("list_view",-1);','0,0,3,0','',1) ;
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
	<fieldset style="width:630px;margin-left:10px">
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
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_buyer_id').value+'_'+<? echo $company_id; ?>+'_'+document.getElementById('txt_search_common').value+'_'+<? echo $year_id; ?>+'_'+<? echo $category_id; ?>+'_'+<? echo $wo_type; ?>, 'create_wo_search_list_view', 'search_div', 'wo_details_report_controller', 'setFilterGrid(\'list_view\',-1);')" style="width:100px;" />
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
	$data=explode('_',$data);
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
		SELECT id, booking_no, $year, booking_no_prefix_num, booking_date, buyer_id, booking_type, is_short, 1 as type from wo_non_ord_samp_booking_mst where status_active=1 and is_deleted=0 $company_id $buyer_id $year_id_cond $search_wo $category_id_cond";
	}
	else if ($data[5]==1 || $data[5]==2 || $data[5]==3)
	{
		$sql= "select id, booking_no, $year, booking_no_prefix_num, booking_date, buyer_id, booking_type, is_short, 0 as type from wo_booking_mst where status_active=1 and is_deleted=0 $company_id $buyer_id $year_id_cond $search_wo $category_id_cond $wo_type_cond $wo_type_cond_sam";
	}
	else
	{
		$sql= "SELECT id, booking_no, $year, booking_no_prefix_num, booking_date, buyer_id, booking_type, is_short, 1 as type from wo_non_ord_samp_booking_mst where status_active=1 and is_deleted=0 $company_id $buyer_id $year_id_cond $search_wo $category_id_cond";
	}
	//echo $sql;

	//$arr=array(3=>$buyerArr);
	//echo  create_list_view("list_view", "WO No,Year,WO Type,Buyer,WO Date", "70,70,130,140,170","630","320",0, $sql , "js_set_value", "id,booking_no", "", 1, "0,0,0,buyer_id,0", $arr , "booking_no_prefix_num,year,style_ref_no,buyer_id,booking_date", "",'setFilterGrid("list_view",-1);','0,0,0,0,3','') ;

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
        <div style="width:620px; overflow-y:scroll; max-height:300px;" id="" align="center">
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
	exit();
}
if($action=="job_no_popup")
{
	echo load_html_head_contents("Job Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	//echo $cbo_search_type;
	?>
	<script>
	var search_type='<? echo $cbo_search_type;?>';
	//alert(search_type);
		function js_set_value(str)
		{
			var splitData = str.split("_");
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
													
							echo create_drop_down( "cbo_search_by", 130, $search_by_arr,"",0, "--Select--", "",$dd,0 );//cbo_search_type
						?>
                        </td>     
                        <td align="center" id="search_by_td">				
                            <input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />	
                        </td> 	
                        <td align="center">
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $search_common; ?>'+'**'+'<? echo $cbo_search_type; ?>', 'create_job_no_search_list_view', 'search_div', 'wo_details_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
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

if($action=="create_job_no_search_list_view")
{
	$data=explode('**',$data);
	$company_id=$data[0];
	$search_common=$data[4];
	$cbo_search_type=$data[5];
	//echo $month_id;
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
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
	if($search_by==2) $search_field="a.style_ref_no"; else $search_field="a.job_no";
	//$year="year(insert_date)";
	if($db_type==0) $year_field="YEAR(a.insert_date) as year"; 
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
	else $year_field="";
	
	if($db_type==0)
	{
		if($year_id!=0) $year_cond=" and year(a.insert_date)=$year_id"; else $year_cond="";	
	}
	else if($db_type==2)
	{
		$year_field_con=" and to_char(a.insert_date,'YYYY')";
		if($year_id!=0) $year_cond="$year_field_con=$year_id"; else $year_cond="";	
	}
	if($cbo_search_type==1)//Job
	{
		$selete_data="id,job_no_prefix_num";	
	}
	else if($cbo_search_type==2) //Style
	{
		$selete_data="id,style_ref_no";	
	}
	
	$arr=array (0=>$company_arr,1=>$buyer_arr);
	
	$sql= "select a.id, a.job_no, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no, $year_field from wo_po_details_master a,wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and a.company_name=$company_id and $search_field like '$search_string' $buyer_id_cond $year_cond  group by a.id, a.job_no, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no,a.insert_date order by a.job_no desc";
	
	echo create_list_view("tbl_list_search", "Company,Buyer Name,Job No,Year,Style Ref.", "120,100,80,60,100","530","240",0, $sql , "js_set_value", "$selete_data", "", 1, "company_name,buyer_name,0,0,0,", $arr , "company_name,buyer_name,job_no_prefix_num,year,style_ref_no", "",'','0,0,0,0,0,0','') ;
	
	
	exit(); 
} // Job Search end

if ($action=="report_generate_bk")
{
	//extract($_REQUEST);
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$cbo_company=str_replace("'","",$cbo_company_id);
	$wo_type=str_replace("'","",$cbo_wo_type);
	$cbo_category=str_replace("'","",$cbo_category_id);
	$cbo_buyer=str_replace("'","",$cbo_buyer_id);
	$year_id=str_replace("'","",$cbo_year_id);
	$wo_no=str_replace("'","",$txt_wo_no);
	$wo_id=str_replace("'","",$hidd_wo_id);
	$date_from=str_replace("'","",$txt_date_from);
	$date_to=str_replace("'","",$txt_date_to);
	$cbo_supplier=str_replace("'","",$cbo_supplier);
	$txt_date_category=str_replace("'","",$txt_date_category);
	$hidd_item_id=str_replace("'","",$hidd_item_id);
	$txt_item_no=str_replace("'","",$txt_item_no);
	$cbo_search_type=str_replace("'","",$cbo_search_type);
	$txt_search_common=str_replace("'","",$txt_search_common);
	$txt_job_po_id=str_replace("'","",$txt_job_po_id);
	$cbo_within_group_id=str_replace("'","",$cbo_within_group_id);

	
	if($cbo_search_type==1)
	{
		if($txt_search_common!="" && $txt_job_po_id!="")
		{
			$job_cond="and a.id in($txt_job_po_id)";
		}
		else if($txt_search_common!="" && $txt_job_po_id=="")
		{
			$job_cond="and a.job_no_prefix_num=$txt_search_common";

		}
	}
	else if($cbo_search_type==2)
	{
		if($txt_search_common!="" && $txt_job_po_id!="")
		{
			$job_cond="and a.id in($txt_job_po_id)";
		}
		else if($txt_search_common!="" && $txt_job_po_id=="")
		{
			$job_cond="and a.style_ref_no='$txt_search_common'";

		}
	}else if($cbo_search_type==3)
	{
		
			if ($txt_search_common=="") $txt_search_common=""; else $internal_ref_cond=" and b.grouping='".trim($txt_search_common)."' ";
	
	
	}
	
	if($cbo_within_group_id==1)
	{
		$within_cond="and a.pay_mode in(3,5)";
	} 
	else if($cbo_within_group_id==2)
	{ 
	  $within_cond="and a.pay_mode not in(3,5)";
	}
	else $within_cond="";
	//die;

	$sql_cond="";

	if($db_type==0)
	{
		if ($year_id==0) $year_id_cond=""; else $year_id_cond=" and YEAR(a.insert_date)=$year_id";
	}
	elseif($db_type==2)
	{
		if ($year_id==0) $year_id_cond=""; else $year_id_cond=" and TO_CHAR(a.insert_date,'YYYY')=$year_id";
	}

	if ($cbo_company!=0) $sql_cond=" and a.company_id=$cbo_company";
	if ($cbo_buyer!=0) $sql_cond.=" and a.buyer_id=$cbo_buyer";
	if ($cbo_buyer!=0) $buyer_id_cond=" and a.buyer_name=$cbo_buyer";else $buyer_id_cond="";
	if ($cbo_category!=0)  $sql_cond.=" and a.item_category=$cbo_category";
	if ($wo_no!="")  $sql_cond.=" and a.booking_no like '%$wo_no%'";
	if ($cbo_supplier>0)  $sql_cond.=" and a.supplier_id=$cbo_supplier";

	if ($hidd_item_id=="") $item_id=""; else $item_id=" and b.trim_group in ( $hidd_item_id )";

	/*if ($wo_type==1 || $wo_type==2)  $sql_cond.=" and a.booking_type in (1,2) and a.is_short='$wo_type'";
	if ($wo_type==3) $sql_cond.="  and a.booking_type=4";*/

	//echo $file_no_cond.'=='.$internal_ref_cond;die;
	if($txt_date_category==1)
	{
		if($db_type==0)
		{
			if( $date_from=="" && $date_to=="" ) $booking_date_cond=""; else $booking_date_cond=" and a.booking_date between '".$date_from."' and '".$date_to."'";
		}
		if($db_type==2)
		{
			if( $date_from=="" && $date_to=="" ) $booking_date_cond=""; else $booking_date_cond=" and a.booking_date between '".change_date_format($date_from,'dd-mm-yyyy','-',1)."' and '".change_date_format($date_to,'dd-mm-yyyy','-',1)."'";
		}
	}
	else
	{
		if($db_type==0)
		{
			if( $date_from=="" && $date_to=="" ) $booking_date_cond=""; else $booking_date_cond=" and b.delivery_date between '".$date_from."' and '".$date_to."'";
		}
		if($db_type==2)
		{
			if( $date_from=="" && $date_to=="" ) $booking_date_cond=""; else $booking_date_cond=" and b.delivery_date between '".change_date_format($date_from,'dd-mm-yyyy','-',1)."' and '".change_date_format($date_to,'dd-mm-yyyy','-',1)."'";
		}
	}


	if($db_type==0) $select_year="year(a.insert_date) as job_year"; else if($db_type==2) $select_year="to_char(a.insert_date,'YYYY') as job_year";

	$lib_team_name_arr=return_library_array( "select id, team_name from lib_marketing_team", "id", "team_name"  );
	$lib_team_leader_name_arr=return_library_array( "select id, team_leader_name from lib_marketing_team", "id", "team_leader_name"  );
	$lib_team_member_arr = return_library_array("select id, team_member_name from lib_mkt_team_member_info", "id", "team_member_name");
	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name");
	$supplierArr=return_library_array( "select id,supplier_name from lib_supplier", "id", "supplier_name");
	$buyerArr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name");
	$trimsGroupArr=return_library_array( "select id, item_name from lib_item_group", "id", "item_name");
	$userArr=return_library_array( "select id, user_name from user_passwd", "id", "user_name");
	$trim_type= return_library_array("select id, trim_type from lib_item_group",'id','trim_type');
	//$nonStyleArr=return_library_array( "select id, style_ref_no from sample_development_mst", "id", "style_ref_no");
		if($txt_search_common!="")
		{
		$sql_po=sql_select("select  b.id,b.job_no_mst  from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst   and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1  and a.company_name=$cbo_company $buyer_id_cond  $job_cond $internal_ref_cond group by   b.id,b.job_no_mst order by b.id,b.job_no_mst");
		//echo "select  b.id,b.job_no_mst  from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst   and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1  and a.company_name=$cbo_company $buyer_id_cond  $job_cond group by   b.id,b.job_no_mst order by b.id,b.job_no_mst";die;
		foreach( $sql_po as $row)
		{
			//$sql_po_qty_country_wise_arr[$row[csf('id')]][$row[csf('country_id')]]=$row[csf('order_quantity_set')];
			$po_id_arr[$row[csf('id')]]=$row[csf('id')];
		}
		unset($sql_po);
		}
		if($txt_search_common!="")
		{
			//$po_idCond="and b.po_break_down_id in(".implode(",",$po_id_arr).")";
		    $po_idCond=where_con_using_array(array_unique($po_id_arr),0,"b.po_break_down_id");
		} 
		else $po_idCond="";
	
	if($cbo_category==4) //Accessories
	{
		if($wo_type==0)
		{
			
			$sql="select a.id as book_mst_id, a.is_approved, a.update_date, a.pay_mode, a.booking_no as booking_no, a.booking_date as wo_date, b.delivery_date, a.booking_type as wo_type, a.is_short, a.supplier_id as supplier_id, a.buyer_id as buyer_id, a.item_category as item_category, a.remarks as remarks,a.inserted_by as inserted_by, b.id as dtls_id, b.po_break_down_id as po_id, b.pre_cost_fabric_cost_dtls_id as pre_cost_fabric_cost_dtls_id, b.trim_group as trim_group, b.construction as construction, b.copmposition as copmposition, b.uom as wo_uom, b.wo_qnty as wo_qnty,(case when a.exchange_rate>0 then (b.rate/a.exchange_rate) else b.rate end) as wo_rate, 1 as type, b.brand_supplier
			from wo_booking_mst a, wo_booking_dtls b
			where a.booking_no=b.booking_no and a.booking_type in(2,5) and a.item_category=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1
		 and b.is_deleted=0  $po_idCond $item_id $sql_cond $booking_date_cond $within_cond
			union all
			select a.id as book_mst_id, a.is_approved,a.update_date,a.pay_mode,a.booking_no as booking_no, a.booking_date as wo_date, a.delivery_date, a.booking_type as wo_type, a.is_short, a.supplier_id as supplier_id, a.buyer_id as buyer_id, a.item_category as item_category, null as remarks,0 as inserted_by, b.id as dtls_id, 0 as po_id, 0 as pre_cost_fabric_cost_dtls_id, b.trim_group as trim_group, b.construction as construction, b.composition as copmposition, b.uom as wo_uom, b.trim_qty as wo_qnty,  (case when a.exchange_rate>0 then (b.rate/a.exchange_rate) else b.rate end) as wo_rate, 2 as type, b.barnd_sup_ref as brand_supplier
			from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b
			where a.booking_no=b.booking_no and a.booking_type in(2,3)  and a.item_category=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1
		 and b.is_deleted=0  $item_id $sql_cond $booking_date_cond $within_cond order by book_mst_id ,po_id";// and b.po_break_down_id=33350
		}
		else if ($wo_type==1 || $wo_type==2)
		{
			$sql="select a.id as book_mst_id,a.is_approved,a.update_date,a.pay_mode, a.booking_no as booking_no, a.booking_date as wo_date, b.delivery_date, a.booking_type as wo_type, a.is_short, a.supplier_id as supplier_id, a.buyer_id as buyer_id, a.item_category as item_category, a.remarks as remarks, a.inserted_by as inserted_by, b.id as dtls_id, b.po_break_down_id as po_id, b.pre_cost_fabric_cost_dtls_id as pre_cost_fabric_cost_dtls_id, b.trim_group as trim_group, b.construction as construction, b.copmposition as copmposition, b.uom as wo_uom, b.wo_qnty as wo_qnty, (case when a.exchange_rate>0 then (b.rate/a.exchange_rate) else b.rate end) as wo_rate,c.item_color, 1 as type, sum(c.cons) as cons
			from wo_booking_mst a, wo_booking_dtls b,wo_trim_book_con_dtls c
			where a.booking_no=b.booking_no and b.id= c.wo_trim_booking_dtls_id and a.booking_type in(2) and a.is_short=$wo_type and a.item_category=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1
		 	and b.is_deleted=0 $po_idCond $sql_cond $item_id $booking_date_cond $within_cond
			group by a.id,a.is_approved,a.update_date,a.pay_mode, a.booking_no, a.booking_date, b.delivery_date, a.booking_type, a.is_short, a.supplier_id, a.buyer_id, a.item_category, a.remarks, a.inserted_by, b.id, b.po_break_down_id, b.pre_cost_fabric_cost_dtls_id, b.trim_group, b.construction, b.copmposition, b.uom, b.wo_qnty, (case when a.exchange_rate>0 then (b.rate/a.exchange_rate) else b.rate end), c.item_color, 1 as type
			order by a.id";
		}
		else if ($wo_type==3)
		{
			$sql="select a.id as book_mst_id,a.is_approved,a.update_date,a.pay_mode, a.booking_no as booking_no, a.booking_date as wo_date, b.delivery_date, a.booking_type as wo_type, a.is_short, a.supplier_id as supplier_id, a.buyer_id as buyer_id, a.item_category as item_category, a.remarks as remarks,a.inserted_by as inserted_by, b.id as dtls_id, b.po_break_down_id as po_id, b.pre_cost_fabric_cost_dtls_id as pre_cost_fabric_cost_dtls_id, b.trim_group as trim_group, b.construction as construction, b.copmposition as copmposition, b.uom as wo_uom, b.wo_qnty as wo_qnty, (case when a.exchange_rate>0 then (b.rate/a.exchange_rate) else b.rate end) as wo_rate,c.item_color, 1 as type, sum(c.cons) as cons
			from wo_booking_mst a, wo_booking_dtls b,wo_trim_book_con_dtls c
			where a.booking_no=b.booking_no and b.id= c.wo_trim_booking_dtls_id and a.booking_type in(5) and a.item_category=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1
		 	and b.is_deleted=0 $po_idCond $item_id $sql_cond $booking_date_cond $within_cond
			group by a.id,a.is_approved,a.update_date,a.pay_mode, a.booking_no, a.booking_date, b.delivery_date, a.booking_type, a.is_short, a.supplier_id, a.buyer_id, a.item_category, a.remarks,a.inserted_by, b.id, b.po_break_down_id, b.pre_cost_fabric_cost_dtls_id, b.trim_group, b.construction, b.copmposition, b.uom, b.wo_qnty, (case when a.exchange_rate>0 then (b.rate/a.exchange_rate) else b.rate end),c.item_color, 1 as type
			order by a.id";
		}
		else
		{
			$sql="select a.id as book_mst_id,a.is_approved,a.update_date,a.pay_mode, a.booking_no as booking_no, a.booking_date as wo_date, a.delivery_date, a.booking_type as wo_type, a.is_short, a.supplier_id as supplier_id, a.buyer_id as buyer_id, a.item_category as item_category, null as remarks, b.id as dtls_id, 0 as po_id, 0 as pre_cost_fabric_cost_dtls_id, b.trim_group as trim_group, b.construction as construction, b.composition as copmposition, b.uom as wo_uom, b.trim_qty as wo_qnty, (case when a.exchange_rate>0 then (b.rate/a.exchange_rate) else b.rate end) as wo_rate,b.gmts_color , 2 as type
			from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b
			where a.booking_no=b.booking_no and a.booking_type in(2,3)  and a.item_category=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1
		 	and b.is_deleted=0 $item_id $sql_cond $booking_date_cond $within_cond
			group by a.id,a.is_approved,a.update_date,a.pay_mode, a.booking_no, a.booking_date, a.delivery_date, a.booking_type, a.is_short, a.supplier_id, a.buyer_id, a.item_category, null, b.id, 0, 0, b.trim_group, b.construction, b.composition, b.uom , b.trim_qty, (case when a.exchange_rate>0 then (b.rate/a.exchange_rate) else b.rate end),b.gmts_color, 2 as type";
		}
	}
	else if($cbo_category==25) //Emblishmnet
	{
		if($wo_type==0)
		{
			 $sql="select a.id as book_mst_id,a.currency_id, a.is_approved, a.update_date, a.pay_mode, a.booking_no as booking_no, a.booking_date as wo_date, b.delivery_date, a.booking_type as wo_type, a.is_short, a.supplier_id as supplier_id, a.buyer_id as buyer_id, a.item_category as item_category, a.remarks as remarks,a.inserted_by as inserted_by, b.id as dtls_id, b.po_break_down_id as po_id, b.pre_cost_fabric_cost_dtls_id as pre_cost_fabric_cost_dtls_id, b.emblishment_name,b.construction as construction, b.copmposition as copmposition, b.uom as wo_uom,b.gmts_color_id, b.wo_qnty as wo_qnty,b.rate,a.exchange_rate,(case when a.exchange_rate>0 then (b.rate/a.exchange_rate) else b.rate end) as wo_rate, 1 as type, b.trim_group as trim_group
			from wo_booking_mst a, wo_booking_dtls b
			where a.booking_no=b.booking_no and a.booking_type in(6) and b.wo_qnty>0 and a.item_category=$cbo_category and a.status_active=1 and a.is_deleted=0 and b.status_active=1
		 and b.is_deleted=0   $po_idCond  $sql_cond $booking_date_cond $within_cond
			";// and b.po_break_down_id=33350
		}
		else if ($wo_type==1 || $wo_type==2)
		{
			$sql="select a.id as book_mst_id,a.currency_id,a.is_approved,a.update_date,a.pay_mode, a.booking_no as booking_no, a.booking_date as wo_date, b.delivery_date, a.booking_type as wo_type, a.is_short, a.supplier_id as supplier_id, a.buyer_id as buyer_id, a.item_category as item_category, a.remarks as remarks, a.inserted_by as inserted_by, b.id as dtls_id, b.po_break_down_id as po_id, b.pre_cost_fabric_cost_dtls_id as pre_cost_fabric_cost_dtls_id, b.emblishment_name, b.construction as construction, b.copmposition as copmposition, b.uom as wo_uom,b.gmts_color_id, b.wo_qnty as wo_qnty,b.rate,a.exchange_rate,(case when a.exchange_rate>0 then (b.rate/a.exchange_rate) else b.rate end) as wo_rate, 1 as type,, b.trim_group as trim_group
			from wo_booking_mst a, wo_booking_dtls b
			where a.booking_no=b.booking_no and a.booking_type in(6) and b.wo_qnty>0 and a.is_short=$wo_type and a.item_category=$cbo_category and a.status_active=1 and a.is_deleted=0 and b.status_active=1
		 and b.is_deleted=0  $po_idCond  $sql_cond   $booking_date_cond $within_cond
			order by a.id";
		}
	}
	//echo $sql;die;

	ob_start();
	if($cbo_category==4) //Accessories
	{
		$sql_result=sql_select($sql);
		$all_po_id=""; $bookingIdArr=array();
		foreach($sql_result as $row)
		{
			if($all_po_id=="") $all_po_id=$row[csf("po_id")];else $all_po_id.=",".$row[csf("po_id")];
			//$emblish_arr[$row[csf("po_id")]]["po_id"]=$row[csf("po_id")];
			$bookingIdArr[$row[csf("booking_no")]]=$row[csf("book_mst_id")];
		}
		//echo $all_po_id.'ds';
		$poIds=chop($all_po_id,','); $po_cond_for_in=""; $po_cond_for_in2=""; $po_cond_for_in3=""; 
		$po_ids=count(array_unique(explode(",",$all_po_id)));
		
		$po_cond_for_in=where_con_using_array(array_unique(explode(",",$all_po_id)),0,"b.id");
		$po_cond_for_in2=where_con_using_array(array_unique(explode(",",$all_po_id)),0,"b.po_break_down_id");
		$po_cond_for_in3=where_con_using_array(array_unique(explode(",",$all_po_id)),0,"d.po_breakdown_id");
		$tna_po=where_con_using_array(array_unique(explode(",",$all_po_id)),0,"b.id");

		$sql_date="SELECT a.task_finish_date,a.po_number_id,a.task_number from tna_process_mst a, wo_po_break_down b where a.po_number_id = b.id and a.status_active=1 and b.is_deleted=0 and A.TASK_NUMBER in (70,71) $tna_po  group by a.task_finish_date,a.po_number_id,a.task_number,a.task_category";
		//echo $sql_date;

		$date_arr=array();

		$sql_result=sql_select($sql_date);
		foreach ($sql_result as $row) 
		{
			$date_arr[$row[csf('po_number_id')]][$row[csf('task_number')]]=$row[csf('task_finish_date')];
		}
			
		$order_sql=sql_select("select a.id as job_id, a.job_no, a.job_no_prefix_num, $select_year, a.style_ref_no, b.id as po_id, b.po_number,a.dealing_marchant,a.team_leader,b.grouping  from  wo_po_details_master a, wo_po_break_down b where a.id=b.job_id and a.status_active=1  and b.status_active=1 $po_cond_for_in $internal_ref_cond");
		//echo "select a.id as job_id, a.job_no, a.job_no_prefix_num, $select_year, a.style_ref_no, b.id as po_id, b.po_number,a.dealing_marchant,a.team_leader,b.grouping  from  wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and b.status_active=1 $po_cond_for_in";
		//grouping field
		$order_data_arr=array();
		//echo $order_sql[csf("po_id")];die;
		$i = 0;
		foreach($order_sql as $row)
		{
			$order_data_arr[$row[csf("po_id")]]["po_id"]=$row[csf("po_id")];
			$order_data_arr[$row[csf("po_id")]]["job_id"]=$row[csf("job_id")];
			$order_data_arr[$row[csf("po_id")]]["job_no"]=$row[csf("job_no")];
			$order_data_arr[$row[csf("po_id")]]["job_no_prefix_num"]=$row[csf("job_no_prefix_num")];
			$order_data_arr[$row[csf("po_id")]]["job_year"]=$row[csf("job_year")];
			$order_data_arr[$row[csf("po_id")]]["style_ref_no"]=$row[csf("style_ref_no")];
			$order_data_arr[$row[csf("po_id")]]["po_number"]=$row[csf("po_number")];
			$order_data_arr[$row[csf("po_id")]]["grouping"]=$row[csf("grouping")];
			$order_data_arr[$row[csf('po_id')]]['team_leader']=$row[csf('team_leader')];
			$order_data_arr[$row[csf('po_id')]]['dealing_marchant']=$row[csf('dealing_marchant')];
			//echo $order_data_arr[$row[csf("po_id")]]["po_number"];
		}
		unset($order_sql);
		$trims_sql=sql_select("select b.id as po_id, a.trim_group, sum(a.rate) as trims_rate from wo_pre_cost_trim_cost_dtls a, wo_po_break_down b where  a.job_no=b.job_no_mst and a.status_active=1 $po_cond_for_in group by b.id, a.trim_group");
		//echo "select b.id as po_id, a.trim_group, sum(a.rate) as trims_rate from wo_pre_cost_trim_cost_dtls a, wo_po_break_down b where  a.job_no=b.job_no_mst and a.status_active=1 $po_cond_for_in group by b.id, a.trim_group";
		$pre_cost_data_arr=array();
		foreach($trims_sql as $row)
		{
			$pre_cost_data_arr[$row[csf("po_id")]][$row[csf("trim_group")]]=$row[csf("trims_rate")];
		}
		unset($trims_sql);
		$description_sql="select b.wo_trim_booking_dtls_id, b.description, b.brand_supplier, b.item_color, b.item_size from  wo_trim_book_con_dtls b where b.status_active=1 $po_cond_for_in2";
		//echo $description_sql;
		$description_sql_result=sql_select($description_sql);
		$description_arr=array();
		foreach($description_sql_result as $row)
		{
			$description=trim($row[csf("description")]);
			$brand_supplier=trim($row[csf("brand_supplier")]);
			$item_size=trim($row[csf("item_size")]);
			if( ($description!=0 || $description!="") && $description_check[$row[csf("wo_trim_booking_dtls_id")]][$description]=="")
			{
				$description_check[$row[csf("wo_trim_booking_dtls_id")]][$description]=$description;
				$description_arr[$row[csf("wo_trim_booking_dtls_id")]]["description"].=$description."__";
			}
			if($brand_supplier_check[$row[csf("wo_trim_booking_dtls_id")]][$brand_supplier]=="")
			{
				$brand_supplier_check[$row[csf("wo_trim_booking_dtls_id")]][$brand_supplier]=$brand_supplier;
				$description_arr[$row[csf("wo_trim_booking_dtls_id")]]["brand_supplier"].=$brand_supplier."__";
			}
			
			if($item_color_check[$row[csf("wo_trim_booking_dtls_id")]][$row[csf("item_color")]]=="")
			{
				$item_color_check[$row[csf("wo_trim_booking_dtls_id")]][$row[csf("item_color")]]=$row[csf("item_color")];
				$description_arr[$row[csf("wo_trim_booking_dtls_id")]]["item_color"].=$row[csf("item_color")]."__";
			}
			
			if($item_size_check[$row[csf("wo_trim_booking_dtls_id")]][$item_size]=="")
			{
				$item_size_check[$row[csf("wo_trim_booking_dtls_id")]][$item_size]=$item_size;
				$description_arr[$row[csf("wo_trim_booking_dtls_id")]]["item_size"].=$item_size."__";
			}
		}
		unset($description_sql_result);
		$piBookingsql=sql_select( "select a.pi_id, a.work_order_id, a.item_group, b.po_break_down_id from com_pi_item_details a, wo_booking_dtls b where a.work_order_dtls_id=b.id and  a.item_category_id = 4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $po_cond_for_in2");
		$piBookingNo=array();
		foreach($piBookingsql as $row)
		{
			$piBookingNo[$row[csf("pi_id")]][$row[csf("po_break_down_id")]][$row[csf("item_group")]]=$row[csf("work_order_id")];
		}
		unset($piBookingsql);
	
		/*$rcv_qnty_sql="select b.mst_id, b.receive_basis, b.transaction_date as receive_date, c.booking_id as pi_wo_batch_no, b.booking_no, c.item_group_id, c.item_description, c.brand_supplier, d.po_breakdown_id as po_id, d.quantity as rcv_qnty, d.order_amount as rcv_value, e.work_order_id
		from inv_trims_entry_dtls c, order_wise_pro_details d, inv_transaction b 
		left join com_pi_item_details e on e.pi_id=b.pi_wo_batch_no
		where b.id=c.trans_id and c.id=d.dtls_id and c.trans_id=d.trans_id and b.transaction_type=1 and b.item_category=4 and d.entry_form=24 and d.status_active=1 and d.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $po_cond_for_in3 
		group by b.mst_id, b.receive_basis, b.transaction_date, c.booking_id, b.booking_no, c.item_group_id, c.item_description, c.brand_supplier, d.po_breakdown_id, d.quantity, d.order_amount, e.work_order_id";*/
		
		$rcv_qnty_sql="select b.mst_id, b.receive_basis, b.transaction_date as receive_date, c.booking_id as pi_wo_batch_no, b.booking_no, c.item_group_id, c.item_description, c.brand_supplier, d.po_breakdown_id as po_id, d.id as prop_id, d.quantity as rcv_qnty, d.order_amount as rcv_value, e.work_order_id, b.prod_id,c.item_color
		from inv_trims_entry_dtls c, order_wise_pro_details d, inv_transaction b 
		left join com_pi_item_details e on e.pi_id=b.pi_wo_batch_no
		where b.id=c.trans_id and c.id=d.dtls_id and c.trans_id=d.trans_id and b.transaction_type=1 and b.item_category=4 and d.entry_form=24 and d.status_active=1 and d.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $po_cond_for_in3";
		//echo $rcv_qnty_sql;
		$receive_qty_data=sql_select($rcv_qnty_sql);
		$rcv_data_po=array();
		$rcv_data_po_ontime=array();
		foreach($receive_qty_data as $row)
		{
			$woid=$row[csf('pi_wo_batch_no')];
			$itemgroup=$row[csf('item_group_id')];
			$po_id=$row[csf('po_id')];
			if($row[csf('receive_basis')]==1) $woid=$row[csf('work_order_id')];
			if($row[csf('receive_basis')]==12) $woid=$bookingIdArr[$row[csf("booking_no")]];
			//echo $woid.'='.$po_id.'='.$itemgroup.'='.$row[csf('item_description')].'='.$row[csf('brand_supplier')].'<br>';
			$item_description=trim($row[csf('item_description')]);
			
			$rcv_data_po[$woid][$po_id][$itemgroup][$item_description]["receive_date"].=$row[csf('receive_date')].",";
			$rcv_data_po[$woid][$po_id][$itemgroup][$item_description]["receive_basis"]=$row[csf('receive_basis')];
			
			//if($propo_id_check[$row[csf('prop_id')]]=="")
			if($propo_id_check[$row[csf('prop_id')]][$woid]=="")
			{
				$propo_id_check[$row[csf('prop_id')]][$woid]=$row[csf('prop_id')];
				$rcv_data_po[$woid][$po_id][$itemgroup][$item_description]["rcv_qnty"]+=$row[csf('rcv_qnty')];
				$rcv_data_po[$woid][$po_id][$itemgroup][$item_description]["rcv_value"]+=$row[csf('rcv_value')];

				//if($row[csf('item_color')]!=="" || $row[csf('item_color')] >0){ //For Group //FKTL-TB-22-05731
					if($row[csf('item_color')] >0){
					$rcv_data_po2[$woid][$po_id][$itemgroup][$item_description][$row[csf('item_color')]]["rcv_qnty"]+=$row[csf('rcv_qnty')];
					$rcv_data_po2[$woid][$po_id][$itemgroup][$item_description][$row[csf('item_color')]]["rcv_value"]+=$row[csf('rcv_value')];
					$rcv_data_po_ontime[$row[csf('receive_date')]][$woid][$po_id][$itemgroup][$item_description][$row[csf('item_color')]]["rcv_qnty"]+=$row[csf('rcv_qnty')];
				
					$rcv_data_po_ontime[$row[csf('receive_date')]][$woid][$po_id][$itemgroup][$item_description][$row[csf('item_color')]]["rcv_value"]+=$row[csf('rcv_value')];
				}else{
					$rcv_data_po_ontime[$row[csf('receive_date')]][$woid][$po_id][$itemgroup][$item_description]["rcv_qnty"]+=$row[csf('rcv_qnty')];
				
					$rcv_data_po_ontime[$row[csf('receive_date')]][$woid][$po_id][$itemgroup][$item_description]["rcv_value"]+=$row[csf('rcv_value')];
				}
				
			}
			
			if($row[csf('mst_id')] && $mst_id_check[$row[csf('receive_date')]][$woid][$po_id][$itemgroup][$item_description][$row[csf('mst_id')]]=="")
			{
				 $mst_id_check[$row[csf('receive_date')]][$woid][$po_id][$itemgroup][$item_description][$row[csf('mst_id')]]=$row[csf('mst_id')];
				//if($row[csf('item_color')]!=="" || $row[csf('item_color')] >0){
				if($row[csf('item_color')] >0){
				$rcv_data_po_ontime[$row[csf('receive_date')]][$woid][$po_id][$itemgroup][$item_description][$row[csf('item_color')]]["mst_id"].=$row[csf('mst_id')].",";
				 }else{
					$rcv_data_po_ontime[$row[csf('receive_date')]][$woid][$po_id][$itemgroup][$item_description]["mst_id"].=$row[csf('mst_id')].",";
				 }
			}
			
		}
		/*echo "<pre>";
		print_r($rcv_data_po_ontime); die;*/
		unset($receive_qty_data);
	
		$receive_qty_data_noorder=sql_select("select a.id as mst_id, a.receive_basis, a.receive_date, c.booking_id as pi_wo_batch_no, b.cons_quantity as rcv_qnty, c.item_group_id, c.item_description, c.brand_supplier, b.order_amount as rcv_value,c.item_color
		from inv_receive_master a, inv_transaction b, inv_trims_entry_dtls c
		where a.id=b.mst_id and b.id=c.trans_id and b.transaction_type=1  and b.pi_wo_batch_no>0 and b.item_category=4 and a.entry_form=24 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0");
		
		$rcv_data_nonOrder=array();
		$rcv_data_nonOrder_on_time=array();
		foreach($receive_qty_data_noorder as $row)
		{
			$woid=$row[csf('pi_wo_batch_no')];
			$itemgroup=$row[csf('item_group_id')];
			$item_description=trim($row[csf('item_description')]);
			if($row[csf('receive_basis')]==1) $woid=$piBookingNo[$woid][$po_id][$itemgroup];
		
			$rcv_data_nonOrder[$woid][$itemgroup][$item_description]["rcv_qnty"]+=$row[csf('rcv_qnty')];
			$rcv_data_nonOrder[$woid][$itemgroup][$item_description]["rcv_value"]+=$row[csf('rcv_value')];
			$rcv_data_nonOrder[$woid][$itemgroup][$item_description]["receive_date"].=$row[csf('receive_date')].",";
			
	
			$rcv_data_nonOrder_on_time[$row[csf('receive_date')]][$woid][$itemgroup][$item_description]["rcv_qnty"]+=$row[csf('rcv_qnty')];
			$rcv_data_nonOrder_on_time[$row[csf('receive_date')]][$woid][$itemgroup][$item_description]["rcv_value"]+=$row[csf('rcv_value')];
			if($row[csf('mst_id')] && $non_mst_id_check[$row[csf('receive_date')]][$woid][$itemgroup][$item_description][$row[csf('mst_id')]]=="")
			{
				$non_mst_id_check[$row[csf('receive_date')]][$woid][$itemgroup][$item_description][$row[csf('mst_id')]]=$row[csf('mst_id')];
				$rcv_data_nonOrder_on_time[$row[csf('receive_date')]][$woid][$itemgroup][$item_description]["mst_id"].=$row[csf('mst_id')].",";
			}
		}
		unset($receive_qty_data_noorder);
		// echo "<pre>";
		// print_r($rcv_data_nonOrder);
		//echo "test";die;
		?>
		<fieldset>
			<table width="2780"  cellspacing="0" >
				<tr class="form_caption" style="border:none;">
					<td align="center" style="border:none; font-size:18px;" colspan="22">
						<? echo $company_library[str_replace("'","",$cbo_company_id)]; ?>
					</td>
				</tr>
				<tr class="form_caption" style="border:none;">
					<td align="center" style="border:none;font-size:16px; font-weight:bold"  colspan="25"> <? echo $report_title ;?></td>
				</tr>
				<tr class="form_caption" style="border:none;">
					<td align="center" style="border:none;font-size:12px; font-weight:bold"  colspan="25"> <? echo "From ".change_date_format($date_from)." To ".change_date_format($date_to);?></td>
				</tr>
			</table>
			<table width="2940" cellspacing="0" border="1" class="rpt_table" rules="all">
				<thead>
					<th width="40">SL</th>
					<th width="110">WO No</th>
					<th width="70">Approved Date</th>
					<th width="110">Internal Ref No</th>
					<th width="70">WO Date</th>
					<th width="70">Delivery Date</th>
					<th width="70">Lead Time</th>
					<th width="90">WO Type</th>
					<th width="100">Supplier</th>
					<th width="100">Buyer</th>
					<th width="50">Job Year</th>
					<th width="50">Job No.</th>
					<th width="100">Style Ref.</th>
					<th width="100">Order No</th>
					<th width="120">Item Name</th>
					<th width="150">Description</th>
					<th width="100">Item Category</th>
					<th width="60">UOM</th>
					<th width="80">WO Qty</th>
					<th width="70">WO Unit price</th>
					<th width="80">WO value</th>
					<th width="70">Budget Unit price</th>
					<th width="80">Precost value</th>
					<th width="80" title="(Precost value - WO value)">Deference</th>
					<th width="80" title="(Deference / Precost value)*100">Deference %</th>
					<th width="80">On Time Receive</th>
					<th width="80">OTD%</th>
					<th width="80">Total Receive Qty</th>
					<th width="80">Receive Value</th>
					<th width="80">Receive Balance</th>
					<th width="120">Dealing Merchant</th>
					<th width="120">Team Leader</th>
					<th width="120">User Name</th>
					<th >Remarks</th>
				</thead>
			</table>
			<div style="max-height:350px; overflow-y:scroll; width:2960px" id="scroll_body" >
				<table cellspacing="0" cellpadding="0" border="1" class="rpt_table"  width="2940" rules="all" id="table_body" >
				<?
				
				$sql_result=sql_select($sql); $i=1;$item_group_sammary=array();
				$total_precost_value =0; $total_deference=0; $total_deference_per =0;
	
				foreach($sql_result as $row)
				{
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					if ($row[csf("type")]==1)
					{
						if ($row[csf("wo_type")]==2)
						{
							if ($row[csf("is_short")]==1)
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
						elseif($row[csf("wo_type")]==5)
						{
							$wo_type="Sample With Order";
							$wo_typw_id=3;
						}
					}
					else
					{
						$wo_type="Sample Without Order";
						$wo_typw_id=4;
					}
					$lead_time=datediff("d", $row[csf("wo_date")], $row[csf("delivery_date")]);
					//$job_number=implode(",",array_unique(explode(",",$row[csf("job_no")])));
					$rcv_qnty=$rcv_balance=$ontimeRcv=0;$rcv_mst_id=$po_id="";
	
					if($row[csf('type')]==1)
					{
						//echo $row[csf('book_mst_id')].'='.$row[csf("po_id")].'='.$row[csf("trim_group")].'='.$description_arr[$row[csf("dtls_id")]]["description"].'='.$description_arr[$row[csf("dtls_id")]]["brand_supplier"].'<br>';
						$rcv_qnty=$rcv_value=$ontimeRcv=0;$rcv_mst_id="";$prod_id="";
						$desc_all_arr=explode("__",chop($description_arr[$row[csf("dtls_id")]]["description"],"__"));
						foreach($desc_all_arr as $descript)
						{
							$rcv_qnty+=$rcv_data_po[$row[csf('book_mst_id')]][$row[csf("po_id")]][$row[csf("trim_group")]][$descript]["rcv_qnty"];
							$rcv_value+=$rcv_data_po[$row[csf('book_mst_id')]][$row[csf("po_id")]][$row[csf("trim_group")]][$descript]["rcv_value"];
						//echo $row[csf('book_mst_id')].'='.$row[csf('po_id')].'='.$row[csf('trim_group')].'='.$descript.'<br>';
							
							$rcv_date_arr=array();
							$rcv_date_arr=array_unique(explode(",",chop($rcv_data_po[$row[csf('book_mst_id')]][$row[csf("po_id")]][$row[csf('trim_group')]][$descript]["receive_date"],",")));
							foreach($rcv_date_arr as $rcv_date)
							{
								if($rcv_date!="" && $rcv_date!="0000-00-00")
								{               
									//if($row[csf("item_color")]!=="" || $row[csf("item_color")]>0) 
										
										if($row[csf("item_color")]>0){    
										$rcv_qnty=$rcv_data_po2[$row[csf('book_mst_id')]][$row[csf("po_id")]][$row[csf("trim_group")]][$descript][$row[csf("item_color")]]["rcv_qnty"];
										$rcv_value=$rcv_data_po2[$row[csf('book_mst_id')]][$row[csf("po_id")]][$row[csf("trim_group")]][$descript][$row[csf("item_color")]]["rcv_value"];  

										if(strtotime($rcv_date)<=strtotime($row[csf("delivery_date")]) && $rcv_data_po_ontime[$rcv_date][$row[csf('book_mst_id')]][$row[csf("po_id")]][$row[csf('trim_group')]][$descript][$row[csf("item_color")]]["rcv_qnty"]>0)
										{
											$ontimeRcv+=$rcv_data_po_ontime[$rcv_date][$row[csf('book_mst_id')]][$row[csf("po_id")]][$row[csf('trim_group')]][$descript][$row[csf("item_color")]]["rcv_qnty"];
										
											$rcv_mst_id.=chop($rcv_data_po_ontime[$rcv_date][$row[csf('book_mst_id')]][$row[csf("po_id")]][$row[csf('trim_group')]][$descript][$row[csf("item_color")]]["mst_id"],",").",";
										}      
									}else{

										if(strtotime($rcv_date)<=strtotime($row[csf("delivery_date")]) && $rcv_data_po_ontime[$rcv_date][$row[csf('book_mst_id')]][$row[csf("po_id")]][$row[csf('trim_group')]][$descript]["rcv_qnty"]>0)
										{
											$ontimeRcv+=$rcv_data_po_ontime[$rcv_date][$row[csf('book_mst_id')]][$row[csf("po_id")]][$row[csf('trim_group')]][$descript]["rcv_qnty"];
										
											$rcv_mst_id.=chop($rcv_data_po_ontime[$rcv_date][$row[csf('book_mst_id')]][$row[csf("po_id")]][$row[csf('trim_group')]][$descript]["mst_id"],",").",";
										}      
									} 
								}
							}
						}
					}
					else
					{
						$po_id=$row[csf("po_id")];
						$rcv_qnty=$rcv_value=$ontimeRcv=0;$rcv_mst_id="";
						$desc_all_arr=explode("__",chop($description_arr[$row[csf("dtls_id")]]["description"],"__"));
						foreach($desc_all_arr as $descript)
						{
							
								
								$rcv_qnty=$rcv_data_nonOrder[$row[csf('book_mst_id')]][$row[csf("trim_group")]][$descript]["rcv_qnty"];
								$rcv_value=$rcv_data_nonOrder[$row[csf('book_mst_id')]][$row[csf("trim_group")]][$descript]["rcv_value"];
								$rcv_date_arr=array();
								$rcv_date_arr=array_unique(explode(",",chop($rcv_data_nonOrder[$row[csf('book_mst_id')]][$row[csf('trim_group')]][$descript]["receive_date"],",")));
							

							foreach($rcv_date_arr as $rcv_date)
							{
								if($rcv_date!="" && $rcv_date!="0000-00-00")
								{
									
										if(strtotime($rcv_date)<=strtotime($row[csf("delivery_date")]) && $rcv_data_nonOrder_on_time[$rcv_date][$row[csf('book_mst_id')]][$row[csf('trim_group')]][$descript]["rcv_qnty"]>0)
										{
											$ontimeRcv+=$rcv_data_nonOrder_on_time[$rcv_date][$row[csf('book_mst_id')]][$row[csf('trim_group')]][$descript]["rcv_qnty"];
											$rcv_mst_id.=chop($rcv_data_nonOrder_on_time[$rcv_date][$row[csf('book_mst_id')]][$row[csf('trim_group')]][$descript]["mst_id"],",").",";
										}
									
								}
							}
						}
					}
					
					// $rcv_balance=$row[csf("wo_qnty")]-$rcv_qnty;
					$rcv_balance=$row[csf("wo_qnty")]-$rcv_qnty;
	
					$rcv_mst_id=chop($rcv_mst_id,",");
					$otd=0;
					$otd=(($ontimeRcv/$row[csf("wo_qnty")])*100);
	
					if($row[csf("wo_qnty")]>0 && $row[csf("trim_group")]>0)
					{
						$item_group_sammary[$row[csf("trim_group")]]["wo_qnty"]+=$row[csf("wo_qnty")];
						$item_group_sammary[$row[csf("trim_group")]]["wo_value"]+=$row[csf("wo_qnty")]*$row[csf("wo_rate")];
						$item_group_sammary[$row[csf("trim_group")]]["pre_value"]+=$row[csf("wo_qnty")]*$pre_cost_data_arr[$row[csf("po_id")]][$row[csf("trim_group")]];//*$pre_cost_data_arr[$row[csf("po_id")]][$row[csf("trim_group")]]
						$item_group_sammary[$row[csf("trim_group")]]["ontimeRcv"]+=$ontimeRcv;
						$item_group_sammary[$row[csf("trim_group")]]["rcv_qnty"]+=$rcv_qnty;
						$item_group_sammary[$row[csf("trim_group")]]["trim_group"]=$row[csf("trim_group")];
					}
	
					if($row[csf("supplier_id")]>0 && $row[csf("wo_qnty")]>0)
					{ //supplierArr 
						if($row[csf("pay_mode")]==3 || $row[csf("pay_mode")]==5)
						{
						 $supplier_wise_sammary[$row[csf("supplier_id")]]["supp_comp"]=$company_library[$row[csf("supplier_id")]];
						}
						else
						{
						 $supplier_wise_sammary[$row[csf("supplier_id")]]["supp_comp"]=$supplierArr[$row[csf("supplier_id")]];
						}
						$supplier_wise_sammary[$row[csf("supplier_id")]]["wo_qnty"]+=$row[csf("wo_qnty")];
						$supplier_wise_sammary[$row[csf("supplier_id")]]["wo_value"]+=$row[csf("wo_qnty")]*$row[csf("wo_rate")];
						$supplier_wise_sammary[$row[csf("supplier_id")]]["pre_value"]+=$row[csf("wo_qnty")]*$pre_cost_data_arr[$row[csf("po_id")]][$row[csf("trim_group")]];
						$supplier_wise_sammary[$row[csf("supplier_id")]]["ontimeRcv"]+=$ontimeRcv;
						$supplier_wise_sammary[$row[csf("supplier_id")]]["rcv_qnty"]+=$rcv_qnty;
						$supplier_wise_sammary[$row[csf("supplier_id")]]["supplier_id"]=$row[csf("supplier_id")];
					}
					if($row[csf("is_approved")]==1)
					{
						$approved_date=$row[csf("update_date")];
						$approved_date = date('Y-m-d', strtotime($approved_date));
					}
					else $approved_date='';
					
					if($row[csf("pay_mode")]==3 || $row[csf("pay_mode")]==5) $supplier_com=$company_library[$row[csf("supplier_id")]]; else $supplier_com=$supplierArr[$row[csf("supplier_id")]];
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
						<td width="40" align="center"><? echo $i;?></td>
						<td width="110"><p><? echo $row[csf("booking_no")]; ?></td>
						<td width="70"><p><? echo $approved_date; ?></td>
						<td width="110"><p><? echo $order_data_arr[$row[csf("po_id")]]["grouping"]; ?></td>
						<td width="70" align="center"><p><? echo change_date_format($row[csf("wo_date")]); ?>&nbsp;</p></td>
						<td width="70" align="center"><p><?
								$date_del="";
								if($trim_type[$row[csf('trim_group')]]==1)
								{
									$date_del=$date_arr[$row[csf('po_id')]][70];
								}
								else{
									$date_del=$date_arr[$row[csf('po_id')]][71];
								}
								if(empty($date_del))
								{
									$date_del=$row[csf("delivery_date")];
								}
						 		echo change_date_format($row[csf("delivery_date")]); 
						 ?>&nbsp;</p></td>
						<td width="70" align="center"><p><? if($lead_time>0 && $lead_time<2) echo $lead_time." Day"; elseif($lead_time>1) echo $lead_time." Days"; else echo "0 Day"; ?>&nbsp;</p></td>
						<td width="90"><p><?  echo $wo_type; ?>&nbsp;</p></td>
						<td width="100"><p><? echo $supplier_com; ?>&nbsp;</p></td>
						<td width="100"><p><? echo $buyerArr[$row[csf("buyer_id")]]; ?>&nbsp;</p></td>
						<td width="50" align="center"><p><? echo $order_data_arr[$row[csf("po_id")]]["job_year"]; ?>&nbsp;</p></td>
						<td width="50" align="center"><p><? echo $order_data_arr[$row[csf("po_id")]]["job_no_prefix_num"]; ?>&nbsp;</p></td>
						<td width="100"><p><? echo $order_data_arr[$row[csf("po_id")]]["style_ref_no"]; ?>&nbsp;</p></td>
						<td width="100"  style="word-break:break-all"><p><? echo $order_data_arr[$row[csf("po_id")]]["po_number"]; ?>&nbsp;</p></td>
						<td width="120"><p><? echo $trimsGroupArr[$row[csf("trim_group")]]; ?>&nbsp;</p></td>
						<td width="150" style="word-break:break-all"><p>
						<?
						$desc_trim=chop($description_arr[$row[csf("dtls_id")]]["description"],'__');
						$desc_trims=implode(",",array_unique(explode(",",$desc_trim)));
						echo $desc_trims; 
						?>&nbsp;</p></td>
						<td width="100"><p><? echo $item_category[$row[csf("item_category")]]; ?>&nbsp;</p></td>
						<td width="60"><p><? echo $unit_of_measurement[$row[csf("wo_uom")]]; ?>&nbsp;</p></td>
						<td width="80" align="right"><p><? echo number_format($row[csf("wo_qnty")],2,'.',''); ?></p></td>
						<td width="70" align="right"><p><? echo number_format($row[csf("wo_rate")],4,'.',''); ?>&nbsp;</p></td>
						<td width="80" align="right"><p><? $wo_value=$row[csf("wo_qnty")]*$row[csf("wo_rate")]; echo number_format($wo_value,4,'.',''); ?></p></td>
						<td width="70" align="right"><p><? echo number_format($pre_cost_data_arr[$row[csf("po_id")]][$row[csf("trim_group")]],4,'.',''); ?></p></td>
						<td width="80" align="right"><p><? $precost_value=$row[csf("wo_qnty")]*$pre_cost_data_arr[$row[csf("po_id")]][$row[csf("trim_group")]]; echo number_format($precost_value,4); ?></p></td>
						<td width="80" align="right"><p><? $deference = $precost_value-$wo_value; echo number_format($deference,2,'.',''); ?></p></td>
						<td width="80" align="right"><p><? $deference_per = ($deference/$precost_value)*100; echo number_format($deference_per,2,'.',''); ?></p></td>
						<td width="80" align="right" title="<?=$prod_id;?>"><p><a href='#report_details' onClick="openmypage_inhouse('<? echo $row[csf("book_mst_id")]; ?>','<? echo $row[csf("po_id")]; ?>','<? echo $row[csf("trim_group")]; ?>','<? echo chop($description_arr[$row[csf("dtls_id")]]["description"],"__"); ?>','<? echo $rcv_mst_id; ?>','1','<? echo $row[csf("delivery_date")]; ?>','<? echo $row[csf("item_color")]; ?>','booking_inhouse_info');"><? echo number_format($ontimeRcv,2,'.','');?></a> </p></td>
						<td width="80" align="right"><p><? echo number_format($otd,2);?></p></td>
						<td width="80" align="right"><p><a href='#report_details' onClick="openmypage_inhouse('<? echo $row[csf("book_mst_id")]; ?>','<? echo $row[csf("po_id")]; ?>','<? echo $row[csf("trim_group")]; ?>','<? echo chop($description_arr[$row[csf("dtls_id")]]["description"],"__"); ?>','<? echo $rcv_mst_id; ?>','2','<? echo $row[csf('type')] ?>','<? echo $row[csf("item_color")]; ?>','booking_inhouse_info');"><? echo number_format($rcv_qnty,2,'.','');?></a> </p></td>
						<td width="80" align="right"><p><? echo number_format($rcv_value,4,'.',''); ?></p></td>
						<td width="80" align="right"><p><? echo number_format($rcv_balance,2,'.',''); ?></p></td>
						<td width="120"><p><? echo $lib_team_member_arr[$order_data_arr[$row[csf("po_id")]]['dealing_marchant']]; ?></p></td>
						<td width="120"><p><? echo $lib_team_leader_name_arr[$order_data_arr[$row[csf("po_id")]]['team_leader']] ; ?></p></td>
						<td width="120" align="right"><p><? echo $userArr[$row[csf("inserted_by")]]; ?></p></td>
						<td ><p><? echo $row[csf("remarks")]; ?> &nbsp;</p></td>
					</tr>
					<?
					$tot_wo_qnty+=$row[csf("wo_qnty")];
					$tot_wo_value+=$wo_value;
					$tot_receive_qnty+=$rcv_qnty;
					$tot_receive_value+=$rcv_value;
					$tot_rcv_balance+=$rcv_balance;
					$total_ontime_rcv+=$ontimeRcv;
					$total_precost_value +=$precost_value;
					$total_deference += $deference;
					//$total_deference_per +=$deference_per;
	
					$i++;
				}
				?>
					<tfoot>
						<th >&nbsp;</th>
						<th >&nbsp;</th>
						<th >&nbsp;</th>
						<th >&nbsp;</th>
						<th >&nbsp;</th>
						<th >&nbsp;</th>
						<th >&nbsp;</th>
						<th >&nbsp;</th>
						<th >&nbsp;</th>
						<th >&nbsp;</th>
						<th >&nbsp;</th>
						<th >&nbsp;</th>
						<th >&nbsp;</th>
						<th >&nbsp;</th>
						<th >&nbsp;</th>
						<th >&nbsp;</th>
						<th >&nbsp;</th>
						<th >Total:</th>
						<th align="right"><? echo number_format($tot_wo_qnty,2) ;?></th>
						<th >&nbsp;</th>
						<th align="right"><? echo number_format($tot_wo_value,4) ;?></th>
						<th >&nbsp;</th>
						<th align="right"><? echo number_format($total_precost_value,4) ;?></th>
						<th align="right"><? echo number_format($total_deference,2) ;?></th>
						<th align="right"><? echo number_format(($total_deference/$total_precost_value)*100,2) ;?></th>
						<th align="right"><? echo number_format($total_ontime_rcv,2) ;?></th>
						<th >&nbsp;</th>
						<th align="right"><? echo number_format($tot_receive_qnty,2) ;?></th>
						<th align="right"><? echo number_format($tot_receive_value,4) ;?></th>
						<th align="right"><? echo number_format($tot_rcv_balance,2) ;?></th>
						<th >&nbsp;</th>
						<th >&nbsp;</th>
						<th >&nbsp;</th>
						<th >&nbsp;</th>
					</tfoot>
				</table>
			</div>
			<br>
			<table cellspacing="0" cellpadding="0" border="0" width="1750" rules="all">
				<tr>
					<td valign="top">
	
						<table cellspacing="0" cellpadding="0" border="1" class="rpt_table"  width="1050" rules="all">
	
							<thead>
								<tr bgcolor="#CCCCCC">
									<td colspan="11" align="center" style="font-size:16px; font-weight:bold;">Item Group Wise Summary</td>
								</tr>
								<tr>
									<th width="50">SL</th>
									<th width="150">Item Name</th>
									<th width="100">WO Qty</th>
									
									<th width="100">WO Value</th>
									<th width="100">Precost value</th>
									<th width="100">Difference</th>
									<th width="50">%</th>
									
									<th width="100">On Time Rcv Qty</th>
									<th width="100">Total Rcv Qty</th>
									<th width="100">Rcv Balance</th>
									<th>OTD %</th>
								</tr>
							</thead>
							<tbody>
								<?
								$k=1;
								//print_r($item_group_sammary);die;
								foreach($item_group_sammary as $item_grp_id=>$val)
								{
									$otd=(($val["ontimeRcv"]/$val["wo_qnty"])*100);
									$item_group_sammary[$item_grp_id]["otd"]=$otd;
								}
	
								foreach($item_group_sammary as $item_group=>$val)
								{
									$mid[$item_group]  = $val["otd"];
								}
								array_multisort($mid, SORT_DESC, $item_group_sammary);
	
								foreach($item_group_sammary as $val)
								{
									if ($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
									$rcv_bal=$val["wo_qnty"]-$val["rcv_qnty"];
								   //$otd=(($val["ontimeRcv"]/$val["wo_qnty"])*100);
									?>
									<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
										<td><? echo $k; ?></td>
										<td>
										<?
										//echo $trimsGroupArr[$item_group];
										echo $trimsGroupArr[$val["trim_group"]];
										?></td>
										<td align="right"><? echo number_format($val["wo_qnty"],2); ?></td>
										<td align="right"><? echo number_format($val["wo_value"],4); ?></td>
										<td align="right"><? echo number_format($val["pre_value"],2); ?></td>
										<td align="right"><? $different=$val["pre_value"]-$val["wo_value"];echo number_format($different,2); ?></td>
										<td align="right" title="Difference/Pre Cost*100"><? echo number_format((($different/$val["pre_value"])*100),2); ?></td>
												
										<td align="right"><? echo number_format($val["ontimeRcv"],2); ?></td>
										<td align="right"><? echo number_format($val["rcv_qnty"],2); ?></td>
										<td align="right"><? echo number_format($rcv_bal,2); ?></td>
										<td align="right"><? echo number_format($val["otd"],2); ?></td>
									</tr>
									<?
									$i++;$k++;
									$sum_tot_wo_qnty+=$val["wo_qnty"];
									$sum_tot_wo_value+=$val["wo_value"];
									$sum_tot_pre_cost+=$val["pre_value"];
									$sum_tot_different+=$val["pre_value"]-$val["wo_value"];
									$sum_tot_ontime_rcv+=$val["ontimeRcv"];
									$sum_tot_rcv_qnty+=$val["rcv_qnty"];
									$sum_tot_rcv_bal+=$rcv_bal;//$val["rcv_bal"];
								}
								$tot_otd=(($sum_tot_ontime_rcv/$sum_tot_wo_qnty)*100);
								?>
							</tbody>
							<tfoot>
								<tr>
									<th>&nbsp;</th>
									<th>Total:</th>
									<th align="right"><? echo number_format($sum_tot_wo_qnty,2); ?></th>
									<th align="right"><? echo number_format($sum_tot_wo_value,4); ?></th>
									<th align="right"><? echo number_format($sum_tot_pre_cost,2); ?></th>
									<th align="right"><? echo number_format($sum_tot_differentt,2); ?></th>
									<th align="right"><? echo number_format((($sum_tot_different/$sum_tot_pre_cost)*100),2); ?></th>
									<th align="right"><? echo number_format($sum_tot_ontime_rcv,2); ?></th>
									<th align="right"><? echo number_format($sum_tot_rcv_qnty,2); ?></th>
									<th align="right"><? echo number_format($sum_tot_rcv_bal,2); ?></th>
									<th align="right"><? echo number_format($tot_otd,2); ?></th>
								</tr>
							</tfoot>
						</table>
	
					</td>
					<td valign="top">&nbsp;</td>
					<td valign="top">
						<table cellspacing="0" cellpadding="0" border="1" class="rpt_table"  width="1050" rules="all">
	
							<thead>
								<tr bgcolor="#CCCCCC">
									<td colspan="11" align="center" style="font-size:16px; font-weight:bold;">Supplier Wise Summary</td>
								</tr>
								<tr>
									<th width="50">SL</th>
									<th width="150">Supplier Name</th>
									<th width="100">WO Qty</th>
									<th width="100">WO Value</th>
									<th width="100">Precost value</th>
									<th width="100">Difference</th>
									<th width="50">%</th>
									<th width="100">On Time Rcv Qty</th>
									<th width="100">Total Rcv Qty</th>
									<th width="100">Rcv Balance</th>
									<th>OTD %</th>
								</tr>
							</thead>
							<tbody>
								<?
								$m=1;
								foreach($supplier_wise_sammary as $supplier_id=>$val)
								{
									$otd=(($val["ontimeRcv"]/$val["wo_qnty"])*100);
									$supplier_wise_sammary[$supplier_id]["otd"]=$otd;
								}
	
								foreach($supplier_wise_sammary as $supplier_id=>$val)
								{
									$sid[$supplier_id]  = $val["otd"];
								}
								array_multisort($sid, SORT_DESC, $supplier_wise_sammary);
								foreach($supplier_wise_sammary as $val)
								{
									if ($m%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
									$rcv_bal=$val["wo_qnty"]-$val["rcv_qnty"];
									$otd=(($val["ontimeRcv"]/$val["wo_qnty"])*100);//$supplierArr[
									?>
									<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
										<td><? echo $m; ?></td>
										<td><? echo $val["supp_comp"]; ?></td>
										<td align="right"><? echo number_format($val["wo_qnty"],2); ?></td>
										 <td align="right"><? echo number_format($val["wo_value"],4); ?></td>
										 <td align="right"><? echo number_format($val["pre_value"],2); ?></td>
										  <td align="right"><? $different=$val["pre_value"]-$val["wo_value"];echo number_format($different,2); ?></td>
										   <td align="right"><? echo number_format((($different/$val["pre_value"])*100),2); ?></td>
										   
										  
										<td align="right"><? echo number_format($val["ontimeRcv"],2); ?></td>
										<td align="right"><? echo number_format($val["rcv_qnty"],2); ?></td>
										<td align="right"><? echo number_format($rcv_bal,2); ?></td>
										<td align="right"><? echo number_format($val["otd"],2); ?></td>
									</tr>
									<?
									$i++;$m++;
									$sup_tot_wo_qnty+=$val["wo_qnty"];
									 $sup_tot_wo_value+=$val["wo_value"];
									  $sup_tot_pre_value+=$val["pre_value"];
									   $sup_tot_different+=$different;
									   // $sup_tot_wo_qnty+=$val["wo_qnty"];
										
									$sup_tot_ontime_rcv+=$val["ontimeRcv"];
									$sup_tot_rcv_qnty+=$val["rcv_qnty"];
									$sup_tot_rcv_bal+=$rcv_bal;//$val["rcv_bal"];
								}
								$sup_tot_otd=(($sup_tot_ontime_rcv/$sup_tot_wo_qnty)*100);
								?>
							</tbody>
							<tfoot>
								<tr>
									<th>&nbsp;</th>
									<th>Total:</th>
									<th align="right"><? echo number_format($sup_tot_wo_qnty,2); ?></th>
									<th align="right"><? echo number_format($sup_tot_wo_value,4); ?></th>
									 <th align="right"><? echo number_format($sup_tot_pre_value,2); ?></th>
									  <th align="right"><? echo number_format($sup_tot_different,2); ?></th>
									  <th align="right"><? echo number_format((($sup_tot_different/$sup_tot_pre_value)*100),2); ?></th>
									
									<th align="right"><? echo number_format($sup_tot_ontime_rcv,2); ?></th>
									<th align="right"><? echo number_format($sup_tot_rcv_qnty,2); ?></th>
									<th align="right"><? echo number_format($sup_tot_rcv_bal,2); ?></th>
									<th align="right"><? echo number_format($sup_tot_otd,2); ?></th>
								</tr>
							</tfoot>
						</table>
					</td>
				</tr>
			</table>
		</fieldset>
		<?
	}
	else if($cbo_category==25) //Emblishment
	{
		$color_namerArr = return_library_array("select id,color_name from lib_color ","id","color_name");
		$sql_result=sql_select($sql);
			$all_po_id="";
			foreach($sql_result as $row)
			{
				if($all_po_id=="") $all_po_id=$row[csf("po_id")];else $all_po_id.=",".$row[csf("po_id")];
				//$emblish_arr[$row[csf("po_id")]]["po_id"]=$row[csf("po_id")];
			}
			//echo $all_po_id.'ds';
			$poIds=chop($all_po_id,','); $po_cond_for_in=""; //$order_cond1=""; $order_cond2=""; $precost_po_cond="";
			$po_ids=count(array_unique(explode(",",$all_po_id)));
			if($db_type==2 && $po_ids>1000)
			{
			$po_cond_for_in=" and (";
			$po_cond_for_in2=" and (";
			$poIdsArr=array_chunk(explode(",",$poIds),999);
			foreach($poIdsArr as $ids)
			{
			$ids=implode(",",$ids);
			//$poIds_cond.=" po_break_down_id in($ids) or ";
			$po_cond_for_in.=" b.id in($ids) or"; 
			$po_cond_for_in2.=" d.po_break_down_id in($ids) or"; 
			}
			$po_cond_for_in=chop($po_cond_for_in,'or ');
			$po_cond_for_in.=")";
			}
			else
			{
			$po_ids=implode(",",(array_unique(explode(",",$all_po_id))));
			$po_cond_for_in=" and b.id in($po_ids)";
		//	$po_cond_for_in2=" and d.po_break_down_id  in($po_ids)";
			}
			if(!empty($all_po_id))
			{
				$tna_po=$po_cond_for_in;
			}
			

			$sql_date="SELECT a.task_finish_date,a.po_number_id,a.task_number from tna_process_mst a, wo_po_break_down b where a.po_number_id = b.id and a.status_active=1 and b.is_deleted=0 and A.TASK_NUMBER in (70,71) $tna_po  group by a.task_finish_date,a.po_number_id,a.task_number,a.task_category";
			//echo $sql_date;

			$date_arr=array();

			$sql_result_date=sql_select($sql_date);
			foreach ($sql_result_date as $row) 
			{
				$date_arr[$row[csf('po_number_id')]][$row[csf('task_number')]]=$row[csf('task_finish_date')];
			}
			$order_sql=sql_select("select a.id as job_id, a.job_no, a.job_no_prefix_num, $select_year, a.style_ref_no, b.id as po_id, b.po_number,a.dealing_marchant,a.team_leader,b.grouping  from  wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and b.status_active=1 $po_cond_for_in");
			//grouping field
			$order_data_arr=array();
			//echo $order_sql[csf("po_id")];die;
			$i = 0;
			foreach($order_sql as $row)
			{
			$order_data_arr[$row[csf("po_id")]]["po_id"]=$row[csf("po_id")];
			$order_data_arr[$row[csf("po_id")]]["job_id"]=$row[csf("job_id")];
			$order_data_arr[$row[csf("po_id")]]["job_no"]=$row[csf("job_no")];
			$order_data_arr[$row[csf("po_id")]]["job_no_prefix_num"]=$row[csf("job_no_prefix_num")];
			$order_data_arr[$row[csf("po_id")]]["job_year"]=$row[csf("job_year")];
			$order_data_arr[$row[csf("po_id")]]["style_ref_no"]=$row[csf("style_ref_no")];
			$order_data_arr[$row[csf("po_id")]]["po_number"]=$row[csf("po_number")];
			$order_data_arr[$row[csf("po_id")]]["grouping"]=$row[csf("grouping")];
			$order_data_arr[$row[csf('po_id')]]['team_leader']=$row[csf('team_leader')];
			$order_data_arr[$row[csf('po_id')]]['dealing_marchant']=$row[csf('dealing_marchant')];
			//echo $order_data_arr[$row[csf("po_id")]]["po_number"];
			}
	
			$condition= new condition();
			$condition->company_name("=$cbo_company");
			if(str_replace("'","",$cbo_buyer)>0){
			$condition->buyer_name("=$cbo_buyer");
			}
			
			if($all_po_id!='')
			{
			$po_ids=implode(",",(array_unique(explode(",",$all_po_id))));
			$condition->po_id_in("$po_ids"); 
			}
			
			$condition->init();
			$emblishment= new emblishment($condition);
			$wash= new wash($condition);
			//echo $wash->getQuery();die;
			//echo $emblishment->getQuery(); die;
			$emblishment_qty_arr=$emblishment->getQtyArray_by_orderEmbnameAndEmbtype();
			$emblishment_amt_arr=$emblishment->getAmountArray_by_orderEmbnameAndEmbtype();
			$emblishment_wash_qty_arr=$wash->getQtyArray_by_orderEmbnameAndEmbtype();
			$emblishment_wash_amt_arr=$wash->getAmountArray_by_orderEmbnameAndEmbtype();
			//print_r($emblishment_wash_qty_arr);	
			$emb_sql=sql_select( "select c.id,c.emb_type,c.emb_name from  wo_po_break_down b,wo_pre_cost_embe_cost_dtls c where b.job_id=c.job_id and c.status_active=1 $po_cond_for_in");
			$pre_embl_arr=array();
			foreach($emb_sql as $row)
			{	
				$pre_embl_arr[$row[csf("id")]]["emb_type"]=$row[csf("emb_type")];
				$pre_embl_arr[$row[csf("id")]]["emb_name"]=$row[csf("emb_name")];
			}
			unset($emb_sql);
			
	?>
    <fieldset>
        <table width="2780"  cellspacing="0" >
            <tr class="form_caption" style="border:none;">
                <td align="center" style="border:none; font-size:18px;" colspan="22">
                    <? echo $company_library[str_replace("'","",$cbo_company_id)]; ?>
                </td>
            </tr>
            <tr class="form_caption" style="border:none;">
                <td align="center" style="border:none;font-size:16px; font-weight:bold"  colspan="25"> <? echo $report_title ;?></td>
            </tr>
            <tr class="form_caption" style="border:none;">
                <td align="center" style="border:none;font-size:12px; font-weight:bold"  colspan="25"> <? echo "From ".change_date_format($date_from)." To ".change_date_format($date_to);?></td>
            </tr>
        </table>
        <table width="2540" cellspacing="0" border="1" class="rpt_table" rules="all">
            <thead>
                <th width="40">SL</th>
                <th width="110">WO No</th>
                <th width="70">Approved Date</th>
                <th width="110">Internal Ref No</th>
                <th width="70">WO Date</th>
                <th width="70">Delivery Date</th>
                <th width="70">Lead Time</th>
                <th width="90">WO Type</th>
                <th width="100">Supplier</th>
                <th width="100">Buyer</th>
                <th width="50">Job Year</th>
                <th width="50">Job No.</th>
                <th width="100">Style Ref.</th>
                <th width="100">Order No</th>
                <th width="120">Emblishment Name</th>
                <th width="150">Emblishment Type</th>
                <th width="100">Item Category</th>
                <th width="60">Color</th>
                <th width="80">WO Qty</th>
                <th width="70">WO Unit price</th>
                <th width="80">WO value</th>
                <th width="70">Budget Unit price</th>
                <th width="80">Precost value</th>
                <th width="80" title="(Precost value - WO value)">Defference</th>
                <th width="80" title="(Defference / Precost value)*100">Defference %</th>
                
                <th width="120">Dealing Merchant</th>
                <th width="120">Team Leader</th>
                <th width="120">User Name</th>
                <th >Remarks</th>
            </thead>
        </table>
        <div style="max-height:350px; overflow-y:scroll; width:2560px" id="scroll_body" >
            <table cellspacing="0" cellpadding="0" border="1" class="rpt_table"  width="2540" rules="all" id="table_body" >
            <?
			
			$i=1;$item_group_sammary=array();
			$total_precost_value =0; $total_deference=0; $total_deference_per =0;

			foreach($sql_result as $row)
			{
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				
				if ($row[csf("wo_type")]==6)
					{
						if ($row[csf("is_short")]==1)
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
					
				$lead_time=datediff("d", $row[csf("wo_date")], $row[csf("delivery_date")]);
				$emb_type_id=$pre_embl_arr[$row[csf("pre_cost_fabric_cost_dtls_id")]]["emb_type"];
				$emb_name=$pre_embl_arr[$row[csf("id")]]["emb_name"]=$pre_embl_arr[$row[csf("pre_cost_fabric_cost_dtls_id")]]["emb_name"];
				if($emb_name==1)//Print
				{
				$emb_type=$emblishment_print_type[$pre_embl_arr[$row[csf("pre_cost_fabric_cost_dtls_id")]]["emb_type"]];
				}
				elseif($emb_name==2)//EMBRO
				{
				$emb_type=$emblishment_embroy_type[$pre_embl_arr[$row[csf("pre_cost_fabric_cost_dtls_id")]]["emb_type"]];
				}
				elseif($emb_name==4)//Spcial
				{
				$emb_type=$emblishment_spwork_type[$pre_embl_arr[$row[csf("pre_cost_fabric_cost_dtls_id")]]["emb_type"]];
				}
				elseif($emb_name==5)//Gmt
				{
				$emb_type=$emblishment_gmts_type[$pre_embl_arr[$row[csf("pre_cost_fabric_cost_dtls_id")]]["emb_type"]];
				}
				elseif($emb_name==3)//Wash
				{
				$emb_type=$emblishment_wash_type[$pre_embl_arr[$row[csf("pre_cost_fabric_cost_dtls_id")]]["emb_type"]];
				}
				
				if($emb_name!=3)//Without Wash
				{
				 $precost_qty=$emblishment_qty_arr[$row[csf("po_id")]][$emb_name][$emb_type_id];
				 $precost_value=$emblishment_amt_arr[$row[csf("po_id")]][$emb_name][$emb_type_id];
				}
				else 
				{
				 $precost_qty=$emblishment_wash_qty_arr[$row[csf("po_id")]][$emb_name][$emb_type_id];
				 $precost_value=$emblishment_wash_amt_arr[$row[csf("po_id")]][$emb_name][$emb_type_id];
				}
				$currency_id=$row[csf("currency_id")];
				if($currency_id==1) //TK exchange_rate
				{
					$wo_rate=$row[csf("rate")]/$row[csf("exchange_rate")];
				}
				else
				{
					$wo_rate=$row[csf("rate")];
				}
				
				
				if($row[csf("wo_qnty")]>0 && $emb_name>0)
				{
					$item_group_sammary[$emb_name]["wo_qnty"]+=$row[csf("wo_qnty")];
					$item_group_sammary[$emb_name]["wo_value"]+=$row[csf("wo_qnty")]*$wo_rate;
					$item_group_sammary[$emb_name]["pre_value"]+=$precost_value;
					//echo $precost_value.',';
					
					$item_group_sammary[$emb_name]["ontimeRcv"]+=$ontimeRcv;
					$item_group_sammary[$emb_name]["rcv_qnty"]+=$rcv_qnty;
					$item_group_sammary[$emb_name]["emblishment_name"]=$emb_name;
				}

				if($row[csf("supplier_id")]>0 && $row[csf("wo_qnty")]>0)
				{
					$supplier_wise_sammary[$row[csf("supplier_id")]]["wo_qnty"]+=$row[csf("wo_qnty")];
					$supplier_wise_sammary[$row[csf("supplier_id")]]["wo_value"]+=$row[csf("wo_qnty")]*$wo_rate;
					$supplier_wise_sammary[$row[csf("supplier_id")]]["pre_value"]+=$precost_value;
					
					$supplier_wise_sammary[$row[csf("supplier_id")]]["ontimeRcv"]+=$ontimeRcv;
					$supplier_wise_sammary[$row[csf("supplier_id")]]["rcv_qnty"]+=$rcv_qnty;
					$supplier_wise_sammary[$row[csf("supplier_id")]]["supplier_id"]=$row[csf("supplier_id")];
				}
				if($row[csf("is_approved")]==1)
				{
					$approved_date=$row[csf("update_date")];
					$approved_date = date('Y-m-d', strtotime($approved_date));
				}
				else $approved_date='';
				
				if($row[csf("pay_mode")]==3 || $row[csf("pay_mode")]==5) $supplier_com=$company_library[$row[csf("supplier_id")]]; else $supplier_com=$supplierArr[$row[csf("supplier_id")]];
				
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
                	<td width="40" align="center"><? echo $i;?></td>
                    <td width="110"><p><? echo $row[csf("booking_no")]; ?></td>
                    <td width="70"><p><? echo $approved_date; ?></td>
                    <td width="110"><p><? echo $order_data_arr[$row[csf("po_id")]]["grouping"]; ?></td>
                    <td width="70" align="center"><p><? echo change_date_format($row[csf("wo_date")]); ?>&nbsp;</p></td>
                    <td width="70" align="center"><p><?
                    	$date_del="";
						if($trim_type[$row[csf('trim_group')]]==1)
						{
							$date_del=$date_arr[$row[csf('po_id')]][70];
						}
						else{
							$date_del=$date_arr[$row[csf('po_id')]][71];
						}
						if(empty($date_del))
						{
							$date_del=$row[csf("delivery_date")];
						}
				 		echo change_date_format($row[csf("delivery_date")]); 
                     
                      ?>&nbsp;</p></td>
                    <td width="70" align="center"><p><? if($lead_time>0 && $lead_time<2) echo $lead_time." Day"; elseif($lead_time>1) echo $lead_time." Days"; else echo "0 Day"; ?>&nbsp;</p></td>
                    <td width="90"><p><?  echo $wo_type; ?>&nbsp;</p></td>
                    <td width="100"><p><? echo $supplier_com; ?>&nbsp;</p></td>
                    <td width="100"><p><? echo $buyerArr[$row[csf("buyer_id")]]; ?>&nbsp;</p></td>
                    <td width="50" align="center"><p><? echo $order_data_arr[$row[csf("po_id")]]["job_year"]; ?>&nbsp;</p></td>
                    <td width="50" align="center"><p><? echo $order_data_arr[$row[csf("po_id")]]["job_no_prefix_num"]; ?>&nbsp;</p></td>
                    <td width="100"><p><? echo $order_data_arr[$row[csf("po_id")]]["style_ref_no"]; ?>&nbsp;</p></td>
                    <td width="100"  style="word-break:break-all"><p><? echo $order_data_arr[$row[csf("po_id")]]["po_number"]; ?>&nbsp;</p></td>
                    <td width="120"><p><? echo $emblishment_name_array[$emb_name]; ?>&nbsp;</p></td>
                    <td width="150" style="word-break:break-all"><p><?
					
					echo $emb_type; ?>&nbsp;</p></td>
                    <td width="100"><p><? echo $item_category[$row[csf("item_category")]]; ?>&nbsp;</p></td>
                    <td width="60"><p><? echo $color_namerArr[$row[csf("gmts_color_id")]]; ?>&nbsp;</p></td>
                    <td width="80" align="right"><p><? echo number_format($row[csf("wo_qnty")],2,'.',''); ?></p></td>
                    <td width="70" align="right" title="USD Rate"><p><? echo number_format($wo_rate,2,'.',''); ?>&nbsp;</p></td>
                    <td width="80" align="right" ><p><? $wo_value=$row[csf("wo_qnty")]*$wo_rate; echo number_format($wo_value,4,'.',''); ?></p></td>
                    <td width="70" align="right" title="Pre Embl Qty=(<? echo $precost_qty;?>) "><p><? echo number_format($precost_value/$precost_qty,2,'.',''); ?></p></td>
                    <td width="80" align="right"><p><? //$precost_value=$row[csf("wo_qnty")]*$pre_cost_data_arr[$row[csf("po_id")]][$row[csf("trim_group")]];
					 $pre_cost_value=$precost_value;//$row[csf("wo_qnty")]*($precost_value/$precost_qty);
					 echo number_format($pre_cost_value,2); ?></p></td>
                    <td width="80" align="right"><p><? $deference = $pre_cost_value-$wo_value; echo number_format($deference,2,'.',''); ?></p></td>
                    <td width="80" align="right"><p><? $deference_per = ($deference/$pre_cost_value)*100; echo number_format($deference_per,2,'.',''); ?></p></td>
                   
                    <td width="120"><p><? echo $lib_team_member_arr[$order_data_arr[$row[csf("po_id")]]['dealing_marchant']]; ?></p></td>
                    <td width="120"><p><? echo $lib_team_leader_name_arr[$order_data_arr[$row[csf("po_id")]]['team_leader']] ; ?></p></td>
                    <td width="120" align="center"><p><? echo $userArr[$row[csf("inserted_by")]]; ?></p></td>
                    <td ><p><? echo $row[csf("remarks")]; ?> &nbsp;</p></td>
				</tr>
				<?
				$tot_wo_qnty+=$row[csf("wo_qnty")];
				$tot_wo_value+=$wo_value;
				$tot_receive_qnty+=$rcv_qnty;
				$tot_receive_value+=$rcv_value;
				$tot_rcv_balance+=$rcv_balance;
				$total_ontime_rcv+=$ontimeRcv;
				$total_precost_value +=$pre_cost_value;
				$total_deference += $deference;
				//$total_deference_per +=$deference_per;

				$i++;
			}
			?>
                <tfoot>
                    <th >&nbsp;</th>
                    <th >&nbsp;</th>
                    <th >&nbsp;</th>
                    <th >&nbsp;</th>
                    <th >&nbsp;</th>
                    <th >&nbsp;</th>
                    <th >&nbsp;</th>
                    <th >&nbsp;</th>
                    <th >&nbsp;</th>
                    <th >&nbsp;</th>
                    <th >&nbsp;</th>
                    <th >&nbsp;</th>
                    <th >&nbsp;</th>
                    <th >&nbsp;</th>
                    <th >&nbsp;</th>
                    <th >&nbsp;</th>
                    <th >&nbsp;</th>
                    <th >Total:</th>
                    <th id="value_tot_wo_qnty" align="right"><? echo number_format($tot_wo_qnty,2) ;?></th>
                    <th >&nbsp;</th>
                    <th id="value_tot_wo_value" align="right"><? echo number_format($tot_wo_value,4) ;?></th>
                    <th >&nbsp;</th>
                    <th id="value_tot_precost_value" align="right"><? echo number_format($total_precost_value,2) ;?></th>
                    <th id="value_tot_deference" align="right"><? echo number_format($total_deference,2) ;?></th>
                    <th id="value_tot_deference_per" align="right"><? echo number_format(($total_deference/$total_precost_value)*100,2) ;?></th>
                    
                    <th >&nbsp;</th>
                    <th >&nbsp;</th>
                    <th >&nbsp;</th>
                    <th >&nbsp;</th>
                </tfoot>
            </table>
        </div>
        <br>
        <table cellspacing="0" cellpadding="0" border="0" width="1500" rules="all">
        	<tr>
            	<td valign="top">

                    <table cellspacing="0" cellpadding="0" border="1" class="rpt_table"  width="860" rules="all">

                        <thead>
                        	<tr bgcolor="#CCCCCC">
                                <td colspan="8" align="center" style="font-size:16px; font-weight:bold;">Emblishment Name Wise Summary</td>
                            </tr>
                            <tr>
                                <th width="50">SL</th>
                                <th width="150">Item Name</th>
                                <th width="100">WO Qty</th>
                                <th width="100">WO Value</th>
                                <th width="100">Precost value</th>
                                <th width="100">Difference</th>
                                <th width="60">%</th>
                               
                            </tr>
                        </thead>
                        <tbody>
                            <?
                            $k=1;
							//print_r($item_group_sammary);die;
							foreach($item_group_sammary as $item_grp_id=>$val)
                            {
                               	$otd=(($val["ontimeRcv"]/$val["wo_qnty"])*100);
								$item_group_sammary[$item_grp_id]["otd"]=$otd;
							}

							foreach($item_group_sammary as $item_group=>$val)
							{
								$mid[$item_group]  = $val["otd"];
							}
							array_multisort($mid, SORT_DESC, $item_group_sammary);

                            foreach($item_group_sammary as $val)
                            {
                                if ($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                               // $rcv_bal=$val["wo_qnty"]-$val["rcv_qnty"];
                               //$otd=(($val["ontimeRcv"]/$val["wo_qnty"])*100);
                                ?>
                                <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
                                    <td><? echo $k; ?></td>
                                    <td>
									<?
									//echo $trimsGroupArr[$item_group];
									echo $emblishment_name_array[$val["emblishment_name"]];
									?></td>
                                    <td align="right"><? echo number_format($val["wo_qnty"],2); ?></td>
                                     <td align="right"><? echo number_format($val["wo_value"],4); ?></td>
                                     <td align="right"><? echo number_format($val["pre_value"],2); ?></td>
                                     <td align="right"><? $difference=$val["pre_value"]-$val["wo_value"];echo number_format($difference,2); ?></td>
                                     <td align="right" title="Difference/Pre value*100"><? echo number_format((($difference/$val["pre_value"])*100),2); ?></td>
                                   
                                </tr>
                                <?
                                $i++;$k++;
                                $sum_tot_wo_qnty+=$val["wo_qnty"];
								 $sum_tot_wo_value+=$val["wo_value"];
								  $sum_tot_pre_value+=$val["pre_value"];
								   $sum_tot_difference+=$difference;
								  
                                $sum_tot_ontime_rcv+=$val["ontimeRcv"];
                                $sum_tot_rcv_qnty+=$val["rcv_qnty"];
                                $sum_tot_rcv_bal+=$rcv_bal;//$val["rcv_bal"];
                            }
                            $tot_otd=(($sum_tot_ontime_rcv/$sum_tot_wo_qnty)*100);
                            ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th>&nbsp;</th>
                                <th>Total:</th>
                                <th align="right"><? echo number_format($sum_tot_wo_qnty,2); ?></th>
                                <th align="right"><? echo number_format($sum_tot_wo_value,4); ?></th>
                                <th align="right"><? echo number_format($sum_tot_pre_value,2); ?></th>
                                <th align="right"><? echo number_format($sum_tot_difference,2); ?></th>
                                <th align="right"><? echo number_format((($sum_tot_difference/$sum_tot_pre_value)*100),2); ?></th>
                                
                            </tr>
                        </tfoot>
                    </table>

                </td>
                <td valign="top">&nbsp;</td>
                <td valign="top">
                	<table cellspacing="0" cellpadding="0" border="1" class="rpt_table"  width="760" rules="all">

                        <thead>
                        	<tr bgcolor="#CCCCCC">
                                <td colspan="8" align="center" style="font-size:16px; font-weight:bold;">Supplier Wise Summary</td>
                            </tr>
                            <tr>
                                <th width="50">SL</th>
                                <th width="150">Supplier Name</th>
                                <th width="100">WO Qty</th>
                                <th width="100">WO Value</th>
                                <th width="100">Precost value</th>
                                <th width="100">Difference</th>
                                <th width="60">%</th>
                                
                               
                            </tr>
                        </thead>
                        <tbody>
                            <?
                            $m=1;
							foreach($supplier_wise_sammary as $supplier_id=>$val)
                            {
                                $otd=(($val["ontimeRcv"]/$val["wo_qnty"])*100);
								$supplier_wise_sammary[$supplier_id]["otd"]=$otd;
							}

							foreach($supplier_wise_sammary as $supplier_id=>$val)
							{
								$sid[$supplier_id]  = $val["otd"];
							}
							array_multisort($sid, SORT_DESC, $supplier_wise_sammary);
                            foreach($supplier_wise_sammary as $val)
                            {
                                if ($m%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                                $rcv_bal=$val["wo_qnty"]-$val["rcv_qnty"];
                                $otd=(($val["ontimeRcv"]/$val["wo_qnty"])*100);
                                ?>
                                <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
                                    <td><? echo $m; ?></td>
                                    <td><? echo $supplierArr[$val["supplier_id"]]; ?></td>
                                    <td align="right"><? echo number_format($val["wo_qnty"],2); ?></td>
                                    
                                    <td align="right"><? echo number_format($val["wo_value"],2); ?></td>
                                    <td align="right"><? echo number_format($val["pre_value"],2); ?></td>
                                    <td align="right"><? $diference=$val["wo_value"]-$val["pre_value"];echo number_format($diference,2); ?></td>
                                    <td align="right"><? echo number_format($val["wo_qnty"],2); ?></td>
                                   
                                </tr>
                                <?
                                $i++;$m++;
                                $sup_tot_wo_qnty+=$val["wo_qnty"];
								$sup_tot_wo_value+=$val["wo_value"];
								$sup_tot_pre_value+=$val["pre_value"];
								$sup_tot_diference+=$diference;
								
                                $sup_tot_ontime_rcv+=$val["ontimeRcv"];
                                $sup_tot_rcv_qnty+=$val["rcv_qnty"];
                                $sup_tot_rcv_bal+=$rcv_bal;//$val["rcv_bal"];
                            }
                            $sup_tot_otd=(($sup_tot_ontime_rcv/$sup_tot_wo_qnty)*100);
                            ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th>&nbsp;</th>
                                <th>Total:</th>
                                <th align="right"><? echo number_format($sup_tot_wo_qnty,2); ?></th>
                                <th align="right"><? echo number_format($sup_tot_wo_value,2); ?></th>
                                <th align="right"><? echo number_format($sup_tot_pre_value,2); ?></th>
                                <th align="right"><? echo number_format($sup_tot_diference,2); ?></th>
                                <th align="right"><? echo number_format((($sup_tot_diference/$sup_tot_pre_value)*100),2); ?></th>
                                
                               
                            </tr>
                        </tfoot>
                    </table>
                </td>
            </tr>
        </table>
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
if ($action=="report_generate")
{
	//extract($_REQUEST);
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$cbo_company=str_replace("'","",$cbo_company_id);
	$wo_type=str_replace("'","",$cbo_wo_type);
	$cbo_category=str_replace("'","",$cbo_category_id);
	$cbo_buyer=str_replace("'","",$cbo_buyer_id);
	$year_id=str_replace("'","",$cbo_year_id);
	$wo_no=str_replace("'","",$txt_wo_no);
	$wo_id=str_replace("'","",$hidd_wo_id);
	$date_from=str_replace("'","",$txt_date_from);
	$date_to=str_replace("'","",$txt_date_to);
	$cbo_supplier=str_replace("'","",$cbo_supplier);
	$txt_date_category=str_replace("'","",$txt_date_category);
	$hidd_item_id=str_replace("'","",$hidd_item_id);
	$txt_item_no=str_replace("'","",$txt_item_no);
	$cbo_search_type=str_replace("'","",$cbo_search_type);
	$txt_search_common=str_replace("'","",$txt_search_common);
	$txt_job_po_id=str_replace("'","",$txt_job_po_id);
	$cbo_within_group_id=str_replace("'","",$cbo_within_group_id);

	
	if($cbo_search_type==1)
	{
		if($txt_search_common!="" && $txt_job_po_id!="")
		{
			$job_cond="and a.id in($txt_job_po_id)";
		}
		else if($txt_search_common!="" && $txt_job_po_id=="")
		{
			$job_cond="and a.job_no_prefix_num=$txt_search_common";

		}
	}
	else if($cbo_search_type==2)
	{
		if($txt_search_common!="" && $txt_job_po_id!="")
		{
			$job_cond="and a.id in($txt_job_po_id)";
		}
		else if($txt_search_common!="" && $txt_job_po_id=="")
		{
			$job_cond="and a.style_ref_no='$txt_search_common'";

		}
	}else if($cbo_search_type==3)
	{
		
			if ($txt_search_common=="") $txt_search_common=""; else $internal_ref_cond=" and b.grouping='".trim($txt_search_common)."' ";
	
	
	}
	
	if($cbo_within_group_id==1)
	{
		$within_cond="and a.pay_mode in(3,5)";
	} 
	else if($cbo_within_group_id==2)
	{ 
	  $within_cond="and a.pay_mode not in(3,5)";
	}
	else $within_cond="";
	//die;

	$sql_cond="";

	if($db_type==0)
	{
		if ($year_id==0) $year_id_cond=""; else $year_id_cond=" and YEAR(a.insert_date)=$year_id";
	}
	elseif($db_type==2)
	{
		if ($year_id==0) $year_id_cond=""; else $year_id_cond=" and TO_CHAR(a.insert_date,'YYYY')=$year_id";
	}

	if ($cbo_company!=0) $sql_cond=" and a.company_id=$cbo_company";
	if ($cbo_buyer!=0) $sql_cond.=" and a.buyer_id=$cbo_buyer";
	if ($cbo_buyer!=0) $buyer_id_cond=" and a.buyer_name=$cbo_buyer";else $buyer_id_cond="";
	if ($cbo_category!=0)  $sql_cond.=" and a.item_category=$cbo_category";
	if ($wo_no!="")  $sql_cond.=" and a.booking_no like '%$wo_no%'";
	if ($cbo_supplier>0)  $sql_cond.=" and a.supplier_id=$cbo_supplier";

	if ($wo_no!="")  $booking_recv_cond=" and c.booking_no like '%$wo_no%'";
	else $booking_recv_cond="";
	if ($wo_no!="")  $booking_dtls_cond=" and booking_no like '%$wo_no%'";
	else $booking_dtls_cond="";

	if ($hidd_item_id=="") $item_id=""; else $item_id=" and b.trim_group in ( $hidd_item_id )";

	/*if ($wo_type==1 || $wo_type==2)  $sql_cond.=" and a.booking_type in (1,2) and a.is_short='$wo_type'";
	if ($wo_type==3) $sql_cond.="  and a.booking_type=4";*/

	//echo $file_no_cond.'=='.$internal_ref_cond;die;
	if($txt_date_category==1)
	{
		if($db_type==0)
		{
			if( $date_from=="" && $date_to=="" ) $booking_date_cond=""; else $booking_date_cond=" and a.booking_date between '".$date_from."' and '".$date_to."'";
		}
		if($db_type==2)
		{
			if( $date_from=="" && $date_to=="" ) $booking_date_cond=""; else $booking_date_cond=" and a.booking_date between '".change_date_format($date_from,'dd-mm-yyyy','-',1)."' and '".change_date_format($date_to,'dd-mm-yyyy','-',1)."'";
		}
	}
	else
	{
		if($db_type==0)
		{
			if( $date_from=="" && $date_to=="" ) $booking_date_cond=""; else $booking_date_cond=" and b.delivery_date between '".$date_from."' and '".$date_to."'";
		}
		if($db_type==2)
		{
			if( $date_from=="" && $date_to=="" ) $booking_date_cond=""; else $booking_date_cond=" and b.delivery_date between '".change_date_format($date_from,'dd-mm-yyyy','-',1)."' and '".change_date_format($date_to,'dd-mm-yyyy','-',1)."'";
		}
	}


	if($db_type==0) $select_year="year(a.insert_date) as job_year"; else if($db_type==2) $select_year="to_char(a.insert_date,'YYYY') as job_year";

	$lib_team_name_arr=return_library_array( "select id, team_name from lib_marketing_team", "id", "team_name"  );
	$lib_team_leader_name_arr=return_library_array( "select id, team_leader_name from lib_marketing_team", "id", "team_leader_name"  );
	$lib_team_member_arr = return_library_array("select id, team_member_name from lib_mkt_team_member_info", "id", "team_member_name");
	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name");
	$supplierArr=return_library_array( "select id,supplier_name from lib_supplier", "id", "supplier_name");
	$buyerArr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name");
	$trimsGroupArr=return_library_array( "select id, item_name from lib_item_group", "id", "item_name");
	$userArr=return_library_array( "select id, user_name from user_passwd", "id", "user_name");
	$trim_type= return_library_array("select id, trim_type from lib_item_group",'id','trim_type');
	//$nonStyleArr=return_library_array( "select id, style_ref_no from sample_development_mst", "id", "style_ref_no");
		if($txt_search_common!="")
		{
		$sql_po=sql_select("select  b.id,b.job_no_mst  from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst   and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1  and a.company_name=$cbo_company $buyer_id_cond  $job_cond $internal_ref_cond group by   b.id,b.job_no_mst order by b.id,b.job_no_mst");
		//echo "select  b.id,b.job_no_mst  from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst   and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1  and a.company_name=$cbo_company $buyer_id_cond  $job_cond group by   b.id,b.job_no_mst order by b.id,b.job_no_mst";die;
		foreach( $sql_po as $row)
		{
			//$sql_po_qty_country_wise_arr[$row[csf('id')]][$row[csf('country_id')]]=$row[csf('order_quantity_set')];
			$po_id_arr[$row[csf('id')]]=$row[csf('id')];
		}
		unset($sql_po);
		}
		if($txt_search_common!="")
		{
			//$po_idCond="and b.po_break_down_id in(".implode(",",$po_id_arr).")";
		    $po_idCond=where_con_using_array(array_unique($po_id_arr),0,"b.po_break_down_id");
		} 
		else $po_idCond="";
	
	if($cbo_category==4) //Accessories
	{
		if($wo_type==0)
		{
			
			$sql="select a.id as book_mst_id, a.is_approved, a.update_date, a.pay_mode, a.booking_no as booking_no, a.booking_date as wo_date, b.delivery_date, a.booking_type as wo_type, a.is_short, a.supplier_id as supplier_id, a.buyer_id as buyer_id, a.item_category as item_category, a.remarks as remarks,a.inserted_by as inserted_by, b.id as dtls_id, b.po_break_down_id as po_id, b.pre_cost_fabric_cost_dtls_id as pre_cost_fabric_cost_dtls_id, b.trim_group as trim_group, b.construction as construction, b.copmposition as copmposition, b.uom as wo_uom, b.wo_qnty as wo_qnty,(case when a.exchange_rate>0 then (b.rate/a.exchange_rate) else b.rate end) as wo_rate, 1 as type, b.brand_supplier
			from wo_booking_mst a, wo_booking_dtls b
			where a.booking_no=b.booking_no and a.booking_type in(2,5) and a.item_category=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1
		 and b.is_deleted=0  $po_idCond $item_id $sql_cond $booking_date_cond $within_cond
			union all
			select a.id as book_mst_id, a.is_approved,a.update_date,a.pay_mode,a.booking_no as booking_no, a.booking_date as wo_date, a.delivery_date, a.booking_type as wo_type, a.is_short, a.supplier_id as supplier_id, a.buyer_id as buyer_id, a.item_category as item_category, null as remarks,0 as inserted_by, b.id as dtls_id, 0 as po_id, 0 as pre_cost_fabric_cost_dtls_id, b.trim_group as trim_group, b.construction as construction, b.composition as copmposition, b.uom as wo_uom, b.trim_qty as wo_qnty,  (case when a.exchange_rate>0 then (b.rate/a.exchange_rate) else b.rate end) as wo_rate, 2 as type, b.barnd_sup_ref as brand_supplier
			from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b
			where a.booking_no=b.booking_no and a.booking_type in(2,3)  and a.item_category=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1
		 and b.is_deleted=0  $item_id $sql_cond $booking_date_cond $within_cond order by book_mst_id ,po_id";// and b.po_break_down_id=33350
		}
		else if ($wo_type==1 || $wo_type==2)
		{
			$sql="select a.id as book_mst_id,a.is_approved,a.update_date,a.pay_mode, a.booking_no as booking_no, a.booking_date as wo_date, b.delivery_date, a.booking_type as wo_type, a.is_short, a.supplier_id as supplier_id, a.buyer_id as buyer_id, a.item_category as item_category, a.remarks as remarks, a.inserted_by as inserted_by, b.id as dtls_id, b.po_break_down_id as po_id, b.pre_cost_fabric_cost_dtls_id as pre_cost_fabric_cost_dtls_id, b.trim_group as trim_group, b.construction as construction, b.copmposition as copmposition, b.uom as wo_uom, b.wo_qnty as wo_qnty, (case when a.exchange_rate>0 then (b.rate/a.exchange_rate) else b.rate end) as wo_rate,c.item_color, 1 as type, sum(c.cons) as cons, b.brand_supplier
			from wo_booking_mst a, wo_booking_dtls b,wo_trim_book_con_dtls c
			where a.booking_no=b.booking_no and b.id= c.wo_trim_booking_dtls_id and a.booking_type in(2) and a.is_short=$wo_type and a.item_category=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1
		 	and b.is_deleted=0 $po_idCond $sql_cond $item_id $booking_date_cond $within_cond
			group by a.id,a.is_approved,a.update_date,a.pay_mode, a.booking_no, a.booking_date, b.delivery_date, a.booking_type, a.is_short, a.supplier_id, a.buyer_id, a.item_category, a.remarks, a.inserted_by, b.id, b.po_break_down_id, b.pre_cost_fabric_cost_dtls_id, b.trim_group, b.construction, b.copmposition, b.uom, b.wo_qnty, (case when a.exchange_rate>0 then (b.rate/a.exchange_rate) else b.rate end), c.item_color, 1 as type, b.brand_supplier
			order by a.id";
		}
		else if ($wo_type==3)
		{
			$sql="select a.id as book_mst_id,a.is_approved,a.update_date,a.pay_mode, a.booking_no as booking_no, a.booking_date as wo_date, b.delivery_date, a.booking_type as wo_type, a.is_short, a.supplier_id as supplier_id, a.buyer_id as buyer_id, a.item_category as item_category, a.remarks as remarks,a.inserted_by as inserted_by, b.id as dtls_id, b.po_break_down_id as po_id, b.pre_cost_fabric_cost_dtls_id as pre_cost_fabric_cost_dtls_id, b.trim_group as trim_group, b.construction as construction, b.copmposition as copmposition, b.uom as wo_uom, b.wo_qnty as wo_qnty, (case when a.exchange_rate>0 then (b.rate/a.exchange_rate) else b.rate end) as wo_rate,c.item_color, 1 as type, sum(c.cons) as cons, b.brand_supplier
			from wo_booking_mst a, wo_booking_dtls b,wo_trim_book_con_dtls c
			where a.booking_no=b.booking_no and b.id= c.wo_trim_booking_dtls_id and a.booking_type in(5) and a.item_category=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1
		 	and b.is_deleted=0 $po_idCond $item_id $sql_cond $booking_date_cond $within_cond
			group by a.id,a.is_approved,a.update_date,a.pay_mode, a.booking_no, a.booking_date, b.delivery_date, a.booking_type, a.is_short, a.supplier_id, a.buyer_id, a.item_category, a.remarks,a.inserted_by, b.id, b.po_break_down_id, b.pre_cost_fabric_cost_dtls_id, b.trim_group, b.construction, b.copmposition, b.uom, b.wo_qnty, (case when a.exchange_rate>0 then (b.rate/a.exchange_rate) else b.rate end),c.item_color, 1 as type, b.brand_supplier
			order by a.id";
		}
		else
		{
			$sql="select a.id as book_mst_id,a.is_approved,a.update_date,a.pay_mode, a.booking_no as booking_no, a.booking_date as wo_date, a.delivery_date, a.booking_type as wo_type, a.is_short, a.supplier_id as supplier_id, a.buyer_id as buyer_id, a.item_category as item_category, null as remarks, b.id as dtls_id, 0 as po_id, 0 as pre_cost_fabric_cost_dtls_id, b.trim_group as trim_group, b.construction as construction, b.composition as copmposition, b.uom as wo_uom, b.trim_qty as wo_qnty, (case when a.exchange_rate>0 then (b.rate/a.exchange_rate) else b.rate end) as wo_rate,b.gmts_color , 2 as type
			from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b
			where a.booking_no=b.booking_no and a.booking_type in(2,3)  and a.item_category=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1
		 	and b.is_deleted=0 $item_id $sql_cond $booking_date_cond $within_cond
			group by a.id,a.is_approved,a.update_date,a.pay_mode, a.booking_no, a.booking_date, a.delivery_date, a.booking_type, a.is_short, a.supplier_id, a.buyer_id, a.item_category, null, b.id, 0, 0, b.trim_group, b.construction, b.composition, b.uom , b.trim_qty, (case when a.exchange_rate>0 then (b.rate/a.exchange_rate) else b.rate end),b.gmts_color, 2 as type";
		}
	}
	else if($cbo_category==25) //Emblishmnet
	{
		if($wo_type==0)
		{
			 $sql="select a.id as book_mst_id,a.currency_id, a.is_approved, a.update_date, a.pay_mode, a.booking_no as booking_no, a.booking_date as wo_date, b.delivery_date, a.booking_type as wo_type, a.is_short, a.supplier_id as supplier_id, a.buyer_id as buyer_id, a.item_category as item_category, a.remarks as remarks,a.inserted_by as inserted_by, b.id as dtls_id, b.po_break_down_id as po_id, b.pre_cost_fabric_cost_dtls_id as pre_cost_fabric_cost_dtls_id, b.emblishment_name,b.construction as construction, b.copmposition as copmposition, b.uom as wo_uom,b.gmts_color_id, b.wo_qnty as wo_qnty,b.rate,a.exchange_rate,(case when a.exchange_rate>0 then (b.rate/a.exchange_rate) else b.rate end) as wo_rate, 1 as type, b.trim_group as trim_group, b.brand_supplier
			from wo_booking_mst a, wo_booking_dtls b
			where a.booking_no=b.booking_no and a.booking_type in(6) and b.wo_qnty>0 and a.item_category=$cbo_category and a.status_active=1 and a.is_deleted=0 and b.status_active=1
		 and b.is_deleted=0   $po_idCond  $sql_cond $booking_date_cond $within_cond
			";// and b.po_break_down_id=33350
		}
		else if ($wo_type==1 || $wo_type==2)
		{
			$sql="select a.id as book_mst_id,a.currency_id,a.is_approved,a.update_date,a.pay_mode, a.booking_no as booking_no, a.booking_date as wo_date, b.delivery_date, a.booking_type as wo_type, a.is_short, a.supplier_id as supplier_id, a.buyer_id as buyer_id, a.item_category as item_category, a.remarks as remarks, a.inserted_by as inserted_by, b.id as dtls_id, b.po_break_down_id as po_id, b.pre_cost_fabric_cost_dtls_id as pre_cost_fabric_cost_dtls_id, b.emblishment_name, b.construction as construction, b.copmposition as copmposition, b.uom as wo_uom,b.gmts_color_id, b.wo_qnty as wo_qnty,b.rate,a.exchange_rate,(case when a.exchange_rate>0 then (b.rate/a.exchange_rate) else b.rate end) as wo_rate, 1 as type,, b.trim_group as trim_group, b.brand_supplier
			from wo_booking_mst a, wo_booking_dtls b
			where a.booking_no=b.booking_no and a.booking_type in(6) and b.wo_qnty>0 and a.is_short=$wo_type and a.item_category=$cbo_category and a.status_active=1 and a.is_deleted=0 and b.status_active=1
		 and b.is_deleted=0  $po_idCond  $sql_cond   $booking_date_cond $within_cond
			order by a.id";
		}
	}
	//echo $sql;die;

	ob_start();
	if($cbo_category==4) //Accessories
	{
		$sql_result=sql_select($sql);
		$all_po_id=""; $bookingIdArr=array();
		foreach($sql_result as $row)
		{
			if($all_po_id=="") $all_po_id=$row[csf("po_id")];else $all_po_id.=",".$row[csf("po_id")];
			//$emblish_arr[$row[csf("po_id")]]["po_id"]=$row[csf("po_id")];
			$bookingIdArr[$row[csf("booking_no")]]=$row[csf("book_mst_id")];
		}
		//echo $all_po_id.'ds';
		$poIds=chop($all_po_id,','); $po_cond_for_in=""; $po_cond_for_in2=""; $po_cond_for_in3=""; 
		$po_ids=count(array_unique(explode(",",$all_po_id)));
		

		$po_cond_for_in=where_con_using_array(array_unique(explode(",",$all_po_id)),0,"b.id");
		$po_cond_for_in2=where_con_using_array(array_unique(explode(",",$all_po_id)),0,"b.po_break_down_id");
		$po_cond_for_in3=where_con_using_array(array_unique(explode(",",$all_po_id)),0,"d.po_breakdown_id");
		$tna_po=where_con_using_array(array_unique(explode(",",$all_po_id)),0,"b.id");

		$sql_date="SELECT a.task_finish_date,a.po_number_id,a.task_number from tna_process_mst a, wo_po_break_down b where a.po_number_id = b.id and a.status_active=1 and b.is_deleted=0 and A.TASK_NUMBER in (70,71) $tna_po  group by a.task_finish_date,a.po_number_id,a.task_number,a.task_category";
		//echo $sql_date;

		$date_arr=array();

		$sql_result=sql_select($sql_date);
		foreach ($sql_result as $row) 
		{
			$date_arr[$row[csf('po_number_id')]][$row[csf('task_number')]]=$row[csf('task_finish_date')];
		}
			
		$order_sql=sql_select("select a.id as job_id, a.job_no, a.job_no_prefix_num, $select_year, a.style_ref_no, b.id as po_id, b.po_number,a.dealing_marchant,a.team_leader,b.grouping  from  wo_po_details_master a, wo_po_break_down b where a.id=b.job_id and a.status_active=1  and b.status_active=1 $po_cond_for_in $internal_ref_cond");
		//echo "select a.id as job_id, a.job_no, a.job_no_prefix_num, $select_year, a.style_ref_no, b.id as po_id, b.po_number,a.dealing_marchant,a.team_leader,b.grouping  from  wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and b.status_active=1 $po_cond_for_in";
		//grouping field
		$order_data_arr=array();
		//echo $order_sql[csf("po_id")];die;
		$i = 0;
		foreach($order_sql as $row)
		{
			$order_data_arr[$row[csf("po_id")]]["po_id"]=$row[csf("po_id")];
			$order_data_arr[$row[csf("po_id")]]["job_id"]=$row[csf("job_id")];
			$order_data_arr[$row[csf("po_id")]]["job_no"]=$row[csf("job_no")];
			$order_data_arr[$row[csf("po_id")]]["job_no_prefix_num"]=$row[csf("job_no_prefix_num")];
			$order_data_arr[$row[csf("po_id")]]["job_year"]=$row[csf("job_year")];
			$order_data_arr[$row[csf("po_id")]]["style_ref_no"]=$row[csf("style_ref_no")];
			$order_data_arr[$row[csf("po_id")]]["po_number"]=$row[csf("po_number")];
			$order_data_arr[$row[csf("po_id")]]["grouping"]=$row[csf("grouping")];
			$order_data_arr[$row[csf('po_id')]]['team_leader']=$row[csf('team_leader')];
			$order_data_arr[$row[csf('po_id')]]['dealing_marchant']=$row[csf('dealing_marchant')];
			//echo $order_data_arr[$row[csf("po_id")]]["po_number"];
		}
		unset($order_sql);
		$trims_sql=sql_select("select b.id as po_id, a.trim_group, sum(a.rate) as trims_rate from wo_pre_cost_trim_cost_dtls a, wo_po_break_down b where  a.job_no=b.job_no_mst and a.status_active=1 $po_cond_for_in group by b.id, a.trim_group");
		//echo "select b.id as po_id, a.trim_group, sum(a.rate) as trims_rate from wo_pre_cost_trim_cost_dtls a, wo_po_break_down b where  a.job_no=b.job_no_mst and a.status_active=1 $po_cond_for_in group by b.id, a.trim_group";
		$pre_cost_data_arr=array();
		foreach($trims_sql as $row)
		{
			$pre_cost_data_arr[$row[csf("po_id")]][$row[csf("trim_group")]]=$row[csf("trims_rate")];
		}
		unset($trims_sql);
		$description_sql="select b.wo_trim_booking_dtls_id, b.description, b.brand_supplier, b.item_color, b.item_size from  wo_trim_book_con_dtls b where b.status_active=1 $po_cond_for_in2 $booking_dtls_cond ";
		//echo $description_sql; die;
		$description_sql_result=sql_select($description_sql);
		$description_arr=array();
		foreach($description_sql_result as $row)
		{
			$description=trim($row[csf("description")]);
			$brand_supplier=trim($row[csf("brand_supplier")]);
			$item_size=trim($row[csf("item_size")]);
			if( ($description!=0 || $description!="") && $description_check[$row[csf("wo_trim_booking_dtls_id")]][$description]=="")
			{
				$description_check[$row[csf("wo_trim_booking_dtls_id")]][$description]=$description;
				$description_arr[$row[csf("wo_trim_booking_dtls_id")]]["description"].=$description."__";
			}
			if($brand_supplier_check[$row[csf("wo_trim_booking_dtls_id")]][$brand_supplier]=="")
			{
				$brand_supplier_check[$row[csf("wo_trim_booking_dtls_id")]][$brand_supplier]=$brand_supplier;
				$description_arr[$row[csf("wo_trim_booking_dtls_id")]]["brand_supplier"].=$brand_supplier."__";
			}
			
			if($item_color_check[$row[csf("wo_trim_booking_dtls_id")]][$row[csf("item_color")]]=="")
			{
				$item_color_check[$row[csf("wo_trim_booking_dtls_id")]][$row[csf("item_color")]]=$row[csf("item_color")];
				$description_arr[$row[csf("wo_trim_booking_dtls_id")]]["item_color"].=$row[csf("item_color")]."__";
			}
			
			if($item_size_check[$row[csf("wo_trim_booking_dtls_id")]][$item_size]=="")
			{
				$item_size_check[$row[csf("wo_trim_booking_dtls_id")]][$item_size]=$item_size;
				$description_arr[$row[csf("wo_trim_booking_dtls_id")]]["item_size"].=$item_size."__";
			}
		}
		unset($description_sql_result);
		$piBookingsql=sql_select( "select a.pi_id, a.work_order_id, a.item_group, b.po_break_down_id from com_pi_item_details a, wo_booking_dtls b where a.work_order_dtls_id=b.id and  a.item_category_id = 4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $po_cond_for_in2");
		$piBookingNo=array();
		foreach($piBookingsql as $row)
		{
			$piBookingNo[$row[csf("pi_id")]][$row[csf("po_break_down_id")]][$row[csf("item_group")]]=$row[csf("work_order_id")];
		}
		unset($piBookingsql);
		
		 $rcv_qnty_sql="select b.mst_id, b.receive_basis, b.transaction_date as receive_date, c.booking_id as pi_wo_batch_no, c.booking_no, c.item_group_id, c.item_description, c.brand_supplier, d.po_breakdown_id as po_id, d.id as prop_id, d.quantity as rcv_qnty, d.order_amount as rcv_value, e.work_order_id, b.prod_id,c.item_color from inv_trims_entry_dtls c, order_wise_pro_details d, inv_transaction b left join com_pi_item_details e on e.pi_id=b.pi_wo_batch_no where b.id=c.trans_id and c.id=d.dtls_id and c.trans_id=d.trans_id and b.transaction_type=1 and b.item_category=4 and d.entry_form=24 and d.status_active=1 and d.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $booking_recv_cond $po_cond_for_in3";
		//echo $rcv_qnty_sql; die;
		$receive_qty_data=sql_select($rcv_qnty_sql);
		$rcv_data_po=array();
		$rcv_data_po_ontime=array();$rrcv=0;
		foreach($receive_qty_data as $row)
		{
			$woid=$row[csf('pi_wo_batch_no')];
			$itemgroup=$row[csf('item_group_id')];
			$po_id=$row[csf('po_id')];
			if($row[csf('receive_basis')]==1) $woid=$row[csf('work_order_id')];
			if($row[csf('receive_basis')]==2) $woid=$bookingIdArr[$row[csf("booking_no")]];
			//echo $woid.'='.$po_id.'='.$itemgroup.'='.$row[csf('item_description')].'='.$row[csf('brand_supplier')].'<br>';
			$item_description=trim($row[csf('item_description')]);
			if($row[csf('brand_supplier')]=='') $row[csf('brand_supplier')]=0; // For Zero Index
			
			$rcv_data_po[$woid][$po_id][$itemgroup][$item_description]["receive_date"].=$row[csf('receive_date')].",";
			$rcv_data_po[$woid][$po_id][$itemgroup][$item_description]["receive_basis"]=$row[csf('receive_basis')];
			
			//if($propo_id_check[$row[csf('prop_id')]]=="")
			if($propo_id_check[$row[csf('prop_id')]][$woid]=="")
			{
				$propo_id_check[$row[csf('prop_id')]][$woid]=$row[csf('prop_id')];
				$rcv_data_po[$woid][$po_id][$itemgroup][$item_description][$row[csf('brand_supplier')]]["rcv_qnty"]+=$row[csf('rcv_qnty')];
				$rcv_data_po[$woid][$po_id][$itemgroup][$item_description][$row[csf('brand_supplier')]]["rcv_value"]+=$row[csf('rcv_value')];
				$rrcv+=$row[csf('rcv_qnty')];

				//if($row[csf('item_color')]!=="" || $row[csf('item_color')] >0){ //For Group //FKTL-TB-22-05731
					if($row[csf('item_color')] >0){
					$rcv_data_po2[$woid][$po_id][$itemgroup][$item_description][$row[csf('item_color')]][$row[csf('brand_supplier')]]["rcv_qnty"]+=$row[csf('rcv_qnty')];
					$rcv_data_po2[$woid][$po_id][$itemgroup][$item_description][$row[csf('item_color')]][$row[csf('brand_supplier')]]["rcv_value"]+=$row[csf('rcv_value')];
					$rcv_data_po_ontime[$row[csf('receive_date')]][$woid][$po_id][$itemgroup][$item_description][$row[csf('item_color')]][$row[csf('brand_supplier')]]["rcv_qnty"]+=$row[csf('rcv_qnty')];
				
					$rcv_data_po_ontime[$row[csf('receive_date')]][$woid][$po_id][$itemgroup][$item_description][$row[csf('item_color')]][$row[csf('brand_supplier')]]["rcv_value"]+=$row[csf('rcv_value')];
				}else{
					$rcv_data_po_ontime[$row[csf('receive_date')]][$woid][$po_id][$itemgroup][$item_description][$row[csf('brand_supplier')]]["rcv_qnty"]+=$row[csf('rcv_qnty')];
				
					$rcv_data_po_ontime[$row[csf('receive_date')]][$woid][$po_id][$itemgroup][$item_description][$row[csf('brand_supplier')]]["rcv_value"]+=$row[csf('rcv_value')];
				}
				
			}
			
			if($row[csf('mst_id')] && $mst_id_check[$row[csf('receive_date')]][$woid][$po_id][$itemgroup][$item_description][$row[csf('mst_id')]]=="")
			{
				 $mst_id_check[$row[csf('receive_date')]][$woid][$po_id][$itemgroup][$item_description][$row[csf('mst_id')]]=$row[csf('mst_id')];
				//if($row[csf('item_color')]!=="" || $row[csf('item_color')] >0){
				if($row[csf('item_color')] >0){
				$rcv_data_po_ontime[$row[csf('receive_date')]][$woid][$po_id][$itemgroup][$item_description][$row[csf('item_color')]][$row[csf('brand_supplier')]]["mst_id"].=$row[csf('mst_id')].",";
				 }else{
					$rcv_data_po_ontime[$row[csf('receive_date')]][$woid][$po_id][$itemgroup][$item_description][$row[csf('brand_supplier')]]["mst_id"].=$row[csf('mst_id')].",";
				 }
			}
			
		}
		//echo $rrcv.'D';
		/* echo "<pre>";
		print_r($rcv_data_po); die; */
		unset($receive_qty_data);
	
		$receive_qty_data_noorder=sql_select("select a.id as mst_id, a.receive_basis, a.receive_date, c.booking_id as pi_wo_batch_no, b.cons_quantity as rcv_qnty, c.item_group_id, c.item_description, c.brand_supplier, b.order_amount as rcv_value,c.item_color
		from inv_receive_master a, inv_transaction b, inv_trims_entry_dtls c
		where a.id=b.mst_id and b.id=c.trans_id and b.transaction_type=1  and b.pi_wo_batch_no>0 and b.item_category=4 and a.entry_form=24 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0");
		
		$rcv_data_nonOrder=array();
		$rcv_data_nonOrder_on_time=array();
		foreach($receive_qty_data_noorder as $row)
		{
			$woid=$row[csf('pi_wo_batch_no')];
			$itemgroup=$row[csf('item_group_id')];
			$item_description=trim($row[csf('item_description')]);
			if($row[csf('receive_basis')]==1) $woid=$piBookingNo[$woid][$po_id][$itemgroup];
		
			$rcv_data_nonOrder[$woid][$itemgroup][$item_description]["rcv_qnty"]+=$row[csf('rcv_qnty')];
			$rcv_data_nonOrder[$woid][$itemgroup][$item_description]["rcv_value"]+=$row[csf('rcv_value')];
			$rcv_data_nonOrder[$woid][$itemgroup][$item_description]["receive_date"].=$row[csf('receive_date')].",";
			
	
			$rcv_data_nonOrder_on_time[$row[csf('receive_date')]][$woid][$itemgroup][$item_description]["rcv_qnty"]+=$row[csf('rcv_qnty')];
			$rcv_data_nonOrder_on_time[$row[csf('receive_date')]][$woid][$itemgroup][$item_description]["rcv_value"]+=$row[csf('rcv_value')];
			if($row[csf('mst_id')] && $non_mst_id_check[$row[csf('receive_date')]][$woid][$itemgroup][$item_description][$row[csf('mst_id')]]=="")
			{
				$non_mst_id_check[$row[csf('receive_date')]][$woid][$itemgroup][$item_description][$row[csf('mst_id')]]=$row[csf('mst_id')];
				$rcv_data_nonOrder_on_time[$row[csf('receive_date')]][$woid][$itemgroup][$item_description]["mst_id"].=$row[csf('mst_id')].",";
			}
		}
		unset($receive_qty_data_noorder);
		// echo "<pre>";
		// print_r($rcv_data_nonOrder);
		//echo "test";die;
		?>
		<fieldset>
			<table width="2780"  cellspacing="0" >
				<tr class="form_caption" style="border:none;">
					<td align="center" style="border:none; font-size:18px;" colspan="22">
						<? echo $company_library[str_replace("'","",$cbo_company_id)]; ?>
					</td>
				</tr>
				<tr class="form_caption" style="border:none;">
					<td align="center" style="border:none;font-size:16px; font-weight:bold"  colspan="25"> <? echo $report_title ;?></td>
				</tr>
				<tr class="form_caption" style="border:none;">
					<td align="center" style="border:none;font-size:12px; font-weight:bold"  colspan="25"> <? echo "From ".change_date_format($date_from)." To ".change_date_format($date_to);?></td>
				</tr>
			</table>
			<table width="2940" cellspacing="0" border="1" class="rpt_table" rules="all">
				<thead>
					<th width="40">SL</th>
					<th width="110">WO No</th>
					<th width="70">Approved Date</th>
					<th width="110">Internal Ref No</th>
					<th width="70">WO Date</th>
					<th width="70">Delivery Date</th>
					<th width="70">Lead Time</th>
					<th width="90">WO Type</th>
					<th width="100">Supplier</th>
					<th width="100">Buyer</th>
					<th width="50">Job Year</th>
					<th width="50">Job No.</th>
					<th width="100">Style Ref.</th>
					<th width="100">Order No</th>
					<th width="120">Item Name</th>
					<th width="150">Description</th>
					<th width="100">Item Category</th>
					<th width="60">UOM</th>
					<th width="80">WO Qty</th>
					<th width="70">WO Unit price</th>
					<th width="80">WO value</th>
					<th width="70">Budget Unit price</th>
					<th width="80">Precost value</th>
					<th width="80" title="(Precost value - WO value)">Deference</th>
					<th width="80" title="(Deference / Precost value)*100">Deference %</th>
					<th width="80">On Time Receive</th>
					<th width="80">OTD%</th>
					<th width="80">Total Receive Qty</th>
					<th width="80">Receive Value</th>
					<th width="80">Receive Balance</th>
					<th width="120">Dealing Merchant</th>
					<th width="120">Team Leader</th>
					<th width="120">User Name</th>
					<th >Remarks</th>
				</thead>
			</table>
			<div style="max-height:350px; overflow-y:scroll; width:2960px" id="scroll_body" >
				<table cellspacing="0" cellpadding="0" border="1" class="rpt_table"  width="2940" rules="all" id="table_body" >
				<?
				
				$sql_result=sql_select($sql); $i=1;$item_group_sammary=array();
				$total_precost_value =0; $total_deference=0; $total_deference_per =0;
				//echo $sql; die;
				foreach($sql_result as $row)
				{
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					if ($row[csf("type")]==1)
					{
						if ($row[csf("wo_type")]==2)
						{
							if ($row[csf("is_short")]==1)
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
						elseif($row[csf("wo_type")]==5)
						{
							$wo_type="Sample With Order";
							$wo_typw_id=3;
						}
					}
					else
					{
						$wo_type="Sample Without Order";
						$wo_typw_id=4;
					}
					$lead_time=datediff("d", $row[csf("wo_date")], $row[csf("delivery_date")]);
					//$job_number=implode(",",array_unique(explode(",",$row[csf("job_no")])));
					$rcv_qnty=$rcv_balance=$ontimeRcv=0;$rcv_mst_id=$po_id="";
	
					if($row[csf('type')]==1)
					{
						if($row[csf('brand_supplier')]=='') $row[csf('brand_supplier')]=0; // For Zero Index
						$rcv_qnty=$rcv_value=$ontimeRcv=0;$rcv_mst_id="";$prod_id="";
						$desc_all_arr=array_unique(explode("__",chop($description_arr[$row[csf("dtls_id")]]["description"],"__")));
						foreach($desc_all_arr as $descript)
						{
							$rcv_qnty+=$rcv_data_po[$row[csf('book_mst_id')]][$row[csf("po_id")]][$row[csf("trim_group")]][$descript][$row[csf('brand_supplier')]]["rcv_qnty"];
							$rcv_value+=$rcv_data_po[$row[csf('book_mst_id')]][$row[csf("po_id")]][$row[csf("trim_group")]][$descript][$row[csf('brand_supplier')]]["rcv_value"];
							//  echo $rcv_qnty.'='.$row[csf('book_mst_id')].'='.$row[csf('po_id')].'='.$row[csf('trim_group')].'='.$descript.'<br>';
							
							$rcv_date_arr=array();
							/* echo '<pre>';
							print_r($rcv_data_po); die; */
							$rcv_date_arr=array_unique(explode(",",chop($rcv_data_po[$row[csf('book_mst_id')]][$row[csf("po_id")]][$row[csf('trim_group')]][$descript]["receive_date"],",")));
						//print_r($rcv_date_arr) ;
							foreach($rcv_date_arr as $rcv_date)
							{
								if($rcv_date!="" && $rcv_date!="0000-00-00")
								{               
									//if($row[csf("item_color")]!=="" || $row[csf("item_color")]>0) 
								
										
									if($row[csf("item_color")]>0){ 
										$rcv_qnty=$rcv_data_po2[$row[csf('book_mst_id')]][$row[csf("po_id")]][$row[csf("trim_group")]][$descript][$row[csf("item_color")]][$row[csf('brand_supplier')]]["rcv_qnty"];
										 
										$rcv_value=$rcv_data_po2[$row[csf('book_mst_id')]][$row[csf("po_id")]][$row[csf("trim_group")]][$descript][$row[csf("item_color")]][$row[csf('brand_supplier')]]["rcv_value"];  

										if(strtotime($rcv_date)<=strtotime($row[csf("delivery_date")]) && $rcv_data_po_ontime[$rcv_date][$row[csf('book_mst_id')]][$row[csf("po_id")]][$row[csf('trim_group')]][$descript][$row[csf("item_color")]]["rcv_qnty"]>0)
										{
											$ontimeRcv+=$rcv_data_po_ontime[$rcv_date][$row[csf('book_mst_id')]][$row[csf("po_id")]][$row[csf('trim_group')]][$descript][$row[csf("item_color")]][$row[csf('brand_supplier')]]["rcv_qnty"];
										
											$rcv_mst_id.=chop($rcv_data_po_ontime[$rcv_date][$row[csf('book_mst_id')]][$row[csf("po_id")]][$row[csf('trim_group')]][$descript][$row[csf("item_color")]][$row[csf('brand_supplier')]]["mst_id"],",").",";
										}      
									}
									else{

										if(strtotime($rcv_date)<=strtotime($row[csf("delivery_date")]) && $rcv_data_po_ontime[$rcv_date][$row[csf('book_mst_id')]][$row[csf("po_id")]][$row[csf('trim_group')]][$descript]["rcv_qnty"]>0)
										{
											$ontimeRcv+=$rcv_data_po_ontime[$rcv_date][$row[csf('book_mst_id')]][$row[csf("po_id")]][$row[csf('trim_group')]][$descript][$row[csf('brand_supplier')]]["rcv_qnty"];
										
											$rcv_mst_id.=chop($rcv_data_po_ontime[$rcv_date][$row[csf('book_mst_id')]][$row[csf("po_id")]][$row[csf('trim_group')]][$descript][$row[csf('brand_supplier')]]["mst_id"],",").",";
										}      
									} 
								}
							}
						}
					}
					else
					{
						$po_id=$row[csf("po_id")];
						$rcv_qnty=$rcv_value=$ontimeRcv=0;$rcv_mst_id="";
						$desc_all_arr=explode("__",chop($description_arr[$row[csf("dtls_id")]]["description"],"__"));
						foreach($desc_all_arr as $descript)
						{
							
								
								$rcv_qnty=$rcv_data_nonOrder[$row[csf('book_mst_id')]][$row[csf("trim_group")]][$descript]["rcv_qnty"];
								$rcv_value=$rcv_data_nonOrder[$row[csf('book_mst_id')]][$row[csf("trim_group")]][$descript]["rcv_value"];
								$rcv_date_arr=array();
								$rcv_date_arr=array_unique(explode(",",chop($rcv_data_nonOrder[$row[csf('book_mst_id')]][$row[csf('trim_group')]][$descript]["receive_date"],",")));
								//echo $rcv_qnty.'=A<br>';

							foreach($rcv_date_arr as $rcv_date)
							{
								if($rcv_date!="" && $rcv_date!="0000-00-00")
								{
									
										if(strtotime($rcv_date)<=strtotime($row[csf("delivery_date")]) && $rcv_data_nonOrder_on_time[$rcv_date][$row[csf('book_mst_id')]][$row[csf('trim_group')]][$descript]["rcv_qnty"]>0)
										{
											$ontimeRcv+=$rcv_data_nonOrder_on_time[$rcv_date][$row[csf('book_mst_id')]][$row[csf('trim_group')]][$descript]["rcv_qnty"];
											$rcv_mst_id.=chop($rcv_data_nonOrder_on_time[$rcv_date][$row[csf('book_mst_id')]][$row[csf('trim_group')]][$descript]["mst_id"],",").",";
										}
									
								}
							}
						}
					}
					//echo $rcv_qnty.'<br>';
					// $rcv_balance=$row[csf("wo_qnty")]-$rcv_qnty;
					$rcv_balance=$row[csf("wo_qnty")]-$rcv_qnty;
	
					$rcv_mst_id=chop($rcv_mst_id,",");
					$otd=0;
					$otd=(($ontimeRcv/$row[csf("wo_qnty")])*100);
	
					if($row[csf("wo_qnty")]>0 && $row[csf("trim_group")]>0)
					{
						$item_group_sammary[$row[csf("trim_group")]]["wo_qnty"]+=$row[csf("wo_qnty")];
						$item_group_sammary[$row[csf("trim_group")]]["wo_value"]+=$row[csf("wo_qnty")]*$row[csf("wo_rate")];
						$item_group_sammary[$row[csf("trim_group")]]["pre_value"]+=$row[csf("wo_qnty")]*$pre_cost_data_arr[$row[csf("po_id")]][$row[csf("trim_group")]];//*$pre_cost_data_arr[$row[csf("po_id")]][$row[csf("trim_group")]]
						$item_group_sammary[$row[csf("trim_group")]]["ontimeRcv"]+=$ontimeRcv;
						$item_group_sammary[$row[csf("trim_group")]]["rcv_qnty"]+=$rcv_qnty;
						$item_group_sammary[$row[csf("trim_group")]]["trim_group"]=$row[csf("trim_group")];
					}
	
					if($row[csf("supplier_id")]>0 && $row[csf("wo_qnty")]>0)
					{ //supplierArr 
						if($row[csf("pay_mode")]==3 || $row[csf("pay_mode")]==5)
						{
						 $supplier_wise_sammary[$row[csf("supplier_id")]]["supp_comp"]=$company_library[$row[csf("supplier_id")]];
						}
						else
						{
						 $supplier_wise_sammary[$row[csf("supplier_id")]]["supp_comp"]=$supplierArr[$row[csf("supplier_id")]];
						}
						$supplier_wise_sammary[$row[csf("supplier_id")]]["wo_qnty"]+=$row[csf("wo_qnty")];
						$supplier_wise_sammary[$row[csf("supplier_id")]]["wo_value"]+=$row[csf("wo_qnty")]*$row[csf("wo_rate")];
						$supplier_wise_sammary[$row[csf("supplier_id")]]["pre_value"]+=$row[csf("wo_qnty")]*$pre_cost_data_arr[$row[csf("po_id")]][$row[csf("trim_group")]];
						$supplier_wise_sammary[$row[csf("supplier_id")]]["ontimeRcv"]+=$ontimeRcv;
						$supplier_wise_sammary[$row[csf("supplier_id")]]["rcv_qnty"]+=$rcv_qnty;
						$supplier_wise_sammary[$row[csf("supplier_id")]]["supplier_id"]=$row[csf("supplier_id")];
					}
					if($row[csf("is_approved")]==1)
					{
						$approved_date=$row[csf("update_date")];
						$approved_date = date('Y-m-d', strtotime($approved_date));
					}
					else $approved_date='';
					
					if($row[csf("pay_mode")]==3 || $row[csf("pay_mode")]==5) $supplier_com=$company_library[$row[csf("supplier_id")]]; else $supplier_com=$supplierArr[$row[csf("supplier_id")]];
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
						<td width="40" align="center"><? echo $i;?></td>
						<td width="110"><p><? echo $row[csf("booking_no")]; ?></td>
						<td width="70"><p><? echo $approved_date; ?></td>
						<td width="110"><p><? echo $order_data_arr[$row[csf("po_id")]]["grouping"]; ?></td>
						<td width="70" align="center"><p><? echo change_date_format($row[csf("wo_date")]); ?>&nbsp;</p></td>
						<td width="70" align="center"><p><?
								$date_del="";
								if($trim_type[$row[csf('trim_group')]]==1)
								{
									$date_del=$date_arr[$row[csf('po_id')]][70];
								}
								else{
									$date_del=$date_arr[$row[csf('po_id')]][71];
								}
								if(empty($date_del))
								{
									$date_del=$row[csf("delivery_date")];
								}
						 		echo change_date_format($row[csf("delivery_date")]); 
						 ?>&nbsp;</p></td>
						<td width="70" align="center"><p><? if($lead_time>0 && $lead_time<2) echo $lead_time." Day"; elseif($lead_time>1) echo $lead_time." Days"; else echo "0 Day"; ?>&nbsp;</p></td>
						<td width="90"><p><?  echo $wo_type; ?>&nbsp;</p></td>
						<td width="100"><p><? echo $supplier_com; ?>&nbsp;</p></td>
						<td width="100"><p><? echo $buyerArr[$row[csf("buyer_id")]]; ?>&nbsp;</p></td>
						<td width="50" align="center"><p><? echo $order_data_arr[$row[csf("po_id")]]["job_year"]; ?>&nbsp;</p></td>
						<td width="50" align="center"><p><? echo $order_data_arr[$row[csf("po_id")]]["job_no_prefix_num"]; ?>&nbsp;</p></td>
						<td width="100"><p><? echo $order_data_arr[$row[csf("po_id")]]["style_ref_no"]; ?>&nbsp;</p></td>
						<td width="100"  style="word-break:break-all"><p><? echo $order_data_arr[$row[csf("po_id")]]["po_number"]; ?>&nbsp;</p></td>
						<td width="120"><p><? echo $trimsGroupArr[$row[csf("trim_group")]]; ?>&nbsp;</p></td>
						<td width="150" style="word-break:break-all"><p>
						<?
						$desc_trim=chop($description_arr[$row[csf("dtls_id")]]["description"],'__');
						$desc_trims=implode(",",array_unique(explode(",",$desc_trim)));
						echo $desc_trims; 
						?>&nbsp;</p></td>
						<td width="100"><p><? echo $item_category[$row[csf("item_category")]]; ?>&nbsp;</p></td>
						<td width="60"><p><? echo $unit_of_measurement[$row[csf("wo_uom")]]; ?>&nbsp;</p></td>
						<td width="80" align="right"><p><? echo number_format($row[csf("wo_qnty")],2,'.',''); ?></p></td>
						<td width="70" align="right"><p><? echo number_format($row[csf("wo_rate")],4,'.',''); ?>&nbsp;</p></td>
						<td width="80" align="right"><p><? $wo_value=$row[csf("wo_qnty")]*$row[csf("wo_rate")]; echo number_format($wo_value,4,'.',''); ?></p></td>
						<td width="70" align="right"><p><? echo number_format($pre_cost_data_arr[$row[csf("po_id")]][$row[csf("trim_group")]],4,'.',''); ?></p></td>
						<td width="80" align="right"><p><? $precost_value=$row[csf("wo_qnty")]*$pre_cost_data_arr[$row[csf("po_id")]][$row[csf("trim_group")]]; echo number_format($precost_value,4); ?></p></td>
						<td width="80" align="right"><p><? $deference = $precost_value-$wo_value; echo number_format($deference,2,'.',''); ?></p></td>
						<td width="80" align="right"><p><? $deference_per = ($deference/$precost_value)*100; echo number_format($deference_per,2,'.',''); ?></p></td>
						<td width="80" align="right" title="<?=$prod_id;?>"><p><a href='#report_details' onClick="openmypage_inhouse('<? echo $row[csf("book_mst_id")]; ?>','<? echo $row[csf("po_id")]; ?>','<? echo $row[csf("trim_group")]; ?>','<? echo chop($description_arr[$row[csf("dtls_id")]]["description"],"__"); ?>','<? echo $rcv_mst_id; ?>','1','<? echo $row[csf("delivery_date")]; ?>','<? echo $row[csf("item_color")]; ?>','booking_inhouse_info','<? echo $row[csf("brand_supplier")]; ?>');"><? echo number_format($ontimeRcv,2,'.','');?></a></p></td>
						<td width="80" align="right"><p><? echo number_format($otd,2);?></p></td>
						<td width="80" align="right"><p><a href='#report_details' onClick="openmypage_inhouse('<? echo $row[csf("book_mst_id")]; ?>','<? echo $row[csf("po_id")]; ?>','<? echo $row[csf("trim_group")]; ?>','<? echo chop($description_arr[$row[csf("dtls_id")]]["description"],"__"); ?>','<? echo $rcv_mst_id; ?>','2','<? echo $row[csf('type')] ?>','<? echo $row[csf("item_color")]; ?>','booking_inhouse_info','<? echo $row[csf("brand_supplier")]; ?>');"><? echo number_format($rcv_qnty,2,'.','');?></a> </p></td>
						<td width="80" align="right"><p><? echo number_format($rcv_value,4,'.',''); ?></p></td>
						<td width="80" align="right"><p><? echo number_format($rcv_balance,2,'.',''); ?></p></td>
						<td width="120"><p><? echo $lib_team_member_arr[$order_data_arr[$row[csf("po_id")]]['dealing_marchant']]; ?></p></td>
						<td width="120"><p><? echo $lib_team_leader_name_arr[$order_data_arr[$row[csf("po_id")]]['team_leader']] ; ?></p></td>
						<td width="120" align="right"><p><? echo $userArr[$row[csf("inserted_by")]]; ?></p></td>
						<td ><p><? echo $row[csf("remarks")]; ?> &nbsp;</p></td>
					</tr>
					<?
					$tot_wo_qnty+=$row[csf("wo_qnty")];
					$tot_wo_value+=$wo_value;
					$tot_receive_qnty+=$rcv_qnty;
					$tot_receive_value+=$rcv_value;
					$tot_rcv_balance+=$rcv_balance;
					$total_ontime_rcv+=$ontimeRcv;
					$total_precost_value +=$precost_value;
					$total_deference += $deference;
					//$total_deference_per +=$deference_per;
	
					$i++;
				}
				?>
					<tfoot>
						<th >&nbsp;</th>
						<th >&nbsp;</th>
						<th >&nbsp;</th>
						<th >&nbsp;</th>
						<th >&nbsp;</th>
						<th >&nbsp;</th>
						<th >&nbsp;</th>
						<th >&nbsp;</th>
						<th >&nbsp;</th>
						<th >&nbsp;</th>
						<th >&nbsp;</th>
						<th >&nbsp;</th>
						<th >&nbsp;</th>
						<th >&nbsp;</th>
						<th >&nbsp;</th>
						<th >&nbsp;</th>
						<th >&nbsp;</th>
						<th >Total:</th>
						<th align="right"><? echo number_format($tot_wo_qnty,2) ;?></th>
						<th >&nbsp;</th>
						<th align="right"><? echo number_format($tot_wo_value,4) ;?></th>
						<th >&nbsp;</th>
						<th align="right"><? echo number_format($total_precost_value,4) ;?></th>
						<th align="right"><? echo number_format($total_deference,2) ;?></th>
						<th align="right"><? echo number_format(($total_deference/$total_precost_value)*100,2) ;?></th>
						<th align="right"><? echo number_format($total_ontime_rcv,2) ;?></th>
						<th >&nbsp;</th>
						<th align="right"><? echo number_format($tot_receive_qnty,2) ;?></th>
						<th align="right"><? echo number_format($tot_receive_value,4) ;?></th>
						<th align="right"><? echo number_format($tot_rcv_balance,2) ;?></th>
						<th >&nbsp;</th>
						<th >&nbsp;</th>
						<th >&nbsp;</th>
						<th >&nbsp;</th>
					</tfoot>
				</table>
			</div>
			<br>
			<table cellspacing="0" cellpadding="0" border="0" width="1750" rules="all">
				<tr>
					<td valign="top">
	
						<table cellspacing="0" cellpadding="0" border="1" class="rpt_table"  width="1050" rules="all">
	
							<thead>
								<tr bgcolor="#CCCCCC">
									<td colspan="11" align="center" style="font-size:16px; font-weight:bold;">Item Group Wise Summary</td>
								</tr>
								<tr>
									<th width="50">SL</th>
									<th width="150">Item Name</th>
									<th width="100">WO Qty</th>
									
									<th width="100">WO Value</th>
									<th width="100">Precost value</th>
									<th width="100">Difference</th>
									<th width="50">%</th>
									
									<th width="100">On Time Rcv Qty</th>
									<th width="100">Total Rcv Qty</th>
									<th width="100">Rcv Balance</th>
									<th>OTD %</th>
								</tr>
							</thead>
							<tbody>
								<?
								$k=1;
								//print_r($item_group_sammary);die;
								foreach($item_group_sammary as $item_grp_id=>$val)
								{
									$otd=(($val["ontimeRcv"]/$val["wo_qnty"])*100);
									$item_group_sammary[$item_grp_id]["otd"]=$otd;
								}
	
								foreach($item_group_sammary as $item_group=>$val)
								{
									$mid[$item_group]  = $val["otd"];
								}
								array_multisort($mid, SORT_DESC, $item_group_sammary);
	
								foreach($item_group_sammary as $val)
								{
									if ($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
									$rcv_bal=$val["wo_qnty"]-$val["rcv_qnty"];
								   //$otd=(($val["ontimeRcv"]/$val["wo_qnty"])*100);
									?>
									<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
										<td><? echo $k; ?></td>
										<td>
										<?
										//echo $trimsGroupArr[$item_group];
										echo $trimsGroupArr[$val["trim_group"]];
										?></td>
										<td align="right"><? echo number_format($val["wo_qnty"],2); ?></td>
										<td align="right"><? echo number_format($val["wo_value"],4); ?></td>
										<td align="right"><? echo number_format($val["pre_value"],2); ?></td>
										<td align="right"><? $different=$val["pre_value"]-$val["wo_value"];echo number_format($different,2); ?></td>
										<td align="right" title="Difference/Pre Cost*100"><? echo number_format((($different/$val["pre_value"])*100),2); ?></td>
												
										<td align="right"><? echo number_format($val["ontimeRcv"],2); ?></td>
										<td align="right"><? echo number_format($val["rcv_qnty"],2); ?></td>
										<td align="right"><? echo number_format($rcv_bal,2); ?></td>
										<td align="right"><? echo number_format($val["otd"],2); ?></td>
									</tr>
									<?
									$i++;$k++;
									$sum_tot_wo_qnty+=$val["wo_qnty"];
									$sum_tot_wo_value+=$val["wo_value"];
									$sum_tot_pre_cost+=$val["pre_value"];
									$sum_tot_different+=$val["pre_value"]-$val["wo_value"];
									$sum_tot_ontime_rcv+=$val["ontimeRcv"];
									$sum_tot_rcv_qnty+=$val["rcv_qnty"];
									$sum_tot_rcv_bal+=$rcv_bal;//$val["rcv_bal"];
								}
								$tot_otd=(($sum_tot_ontime_rcv/$sum_tot_wo_qnty)*100);
								?>
							</tbody>
							<tfoot>
								<tr>
									<th>&nbsp;</th>
									<th>Total:</th>
									<th align="right"><? echo number_format($sum_tot_wo_qnty,2); ?></th>
									<th align="right"><? echo number_format($sum_tot_wo_value,4); ?></th>
									<th align="right"><? echo number_format($sum_tot_pre_cost,2); ?></th>
									<th align="right"><? echo number_format($sum_tot_differentt,2); ?></th>
									<th align="right"><? echo number_format((($sum_tot_different/$sum_tot_pre_cost)*100),2); ?></th>
									<th align="right"><? echo number_format($sum_tot_ontime_rcv,2); ?></th>
									<th align="right"><? echo number_format($sum_tot_rcv_qnty,2); ?></th>
									<th align="right"><? echo number_format($sum_tot_rcv_bal,2); ?></th>
									<th align="right"><? echo number_format($tot_otd,2); ?></th>
								</tr>
							</tfoot>
						</table>
	
					</td>
					<td valign="top">&nbsp;</td>
					<td valign="top">
						<table cellspacing="0" cellpadding="0" border="1" class="rpt_table"  width="1050" rules="all">
	
							<thead>
								<tr bgcolor="#CCCCCC">
									<td colspan="11" align="center" style="font-size:16px; font-weight:bold;">Supplier Wise Summary</td>
								</tr>
								<tr>
									<th width="50">SL</th>
									<th width="150">Supplier Name</th>
									<th width="100">WO Qty</th>
									<th width="100">WO Value</th>
									<th width="100">Precost value</th>
									<th width="100">Difference</th>
									<th width="50">%</th>
									<th width="100">On Time Rcv Qty</th>
									<th width="100">Total Rcv Qty</th>
									<th width="100">Rcv Balance</th>
									<th>OTD %</th>
								</tr>
							</thead>
							<tbody>
								<?
								$m=1;
								foreach($supplier_wise_sammary as $supplier_id=>$val)
								{
									$otd=(($val["ontimeRcv"]/$val["wo_qnty"])*100);
									$supplier_wise_sammary[$supplier_id]["otd"]=$otd;
								}
	
								foreach($supplier_wise_sammary as $supplier_id=>$val)
								{
									$sid[$supplier_id]  = $val["otd"];
								}
								array_multisort($sid, SORT_DESC, $supplier_wise_sammary);
								foreach($supplier_wise_sammary as $val)
								{
									if ($m%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
									$rcv_bal=$val["wo_qnty"]-$val["rcv_qnty"];
									$otd=(($val["ontimeRcv"]/$val["wo_qnty"])*100);//$supplierArr[
									?>
									<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
										<td><? echo $m; ?></td>
										<td><? echo $val["supp_comp"]; ?></td>
										<td align="right"><? echo number_format($val["wo_qnty"],2); ?></td>
										 <td align="right"><? echo number_format($val["wo_value"],4); ?></td>
										 <td align="right"><? echo number_format($val["pre_value"],2); ?></td>
										  <td align="right"><? $different=$val["pre_value"]-$val["wo_value"];echo number_format($different,2); ?></td>
										   <td align="right"><? echo number_format((($different/$val["pre_value"])*100),2); ?></td>
										   
										  
										<td align="right"><? echo number_format($val["ontimeRcv"],2); ?></td>
										<td align="right"><? echo number_format($val["rcv_qnty"],2); ?></td>
										<td align="right"><? echo number_format($rcv_bal,2); ?></td>
										<td align="right"><? echo number_format($val["otd"],2); ?></td>
									</tr>
									<?
									$i++;$m++;
									$sup_tot_wo_qnty+=$val["wo_qnty"];
									 $sup_tot_wo_value+=$val["wo_value"];
									  $sup_tot_pre_value+=$val["pre_value"];
									   $sup_tot_different+=$different;
									   // $sup_tot_wo_qnty+=$val["wo_qnty"];
										
									$sup_tot_ontime_rcv+=$val["ontimeRcv"];
									$sup_tot_rcv_qnty+=$val["rcv_qnty"];
									$sup_tot_rcv_bal+=$rcv_bal;//$val["rcv_bal"];
								}
								$sup_tot_otd=(($sup_tot_ontime_rcv/$sup_tot_wo_qnty)*100);
								?>
							</tbody>
							<tfoot>
								<tr>
									<th>&nbsp;</th>
									<th>Total:</th>
									<th align="right"><? echo number_format($sup_tot_wo_qnty,2); ?></th>
									<th align="right"><? echo number_format($sup_tot_wo_value,4); ?></th>
									 <th align="right"><? echo number_format($sup_tot_pre_value,2); ?></th>
									  <th align="right"><? echo number_format($sup_tot_different,2); ?></th>
									  <th align="right"><? echo number_format((($sup_tot_different/$sup_tot_pre_value)*100),2); ?></th>
									
									<th align="right"><? echo number_format($sup_tot_ontime_rcv,2); ?></th>
									<th align="right"><? echo number_format($sup_tot_rcv_qnty,2); ?></th>
									<th align="right"><? echo number_format($sup_tot_rcv_bal,2); ?></th>
									<th align="right"><? echo number_format($sup_tot_otd,2); ?></th>
								</tr>
							</tfoot>
						</table>
					</td>
				</tr>
			</table>
		</fieldset>
		<?
	}
	else if($cbo_category==25) //Emblishment
	{
		$color_namerArr = return_library_array("select id,color_name from lib_color ","id","color_name");
		$sql_result=sql_select($sql);
			$all_po_id="";
			foreach($sql_result as $row)
			{
				if($all_po_id=="") $all_po_id=$row[csf("po_id")];else $all_po_id.=",".$row[csf("po_id")];
				//$emblish_arr[$row[csf("po_id")]]["po_id"]=$row[csf("po_id")];
			}
			//echo $all_po_id.'ds';
			$poIds=chop($all_po_id,','); $po_cond_for_in=""; //$order_cond1=""; $order_cond2=""; $precost_po_cond="";
			$po_ids=count(array_unique(explode(",",$all_po_id)));
			if($db_type==2 && $po_ids>1000)
			{
			$po_cond_for_in=" and (";
			$po_cond_for_in2=" and (";
			$poIdsArr=array_chunk(explode(",",$poIds),999);
			foreach($poIdsArr as $ids)
			{
			$ids=implode(",",$ids);
			//$poIds_cond.=" po_break_down_id in($ids) or ";
			$po_cond_for_in.=" b.id in($ids) or"; 
			$po_cond_for_in2.=" d.po_break_down_id in($ids) or"; 
			}
			$po_cond_for_in=chop($po_cond_for_in,'or ');
			$po_cond_for_in.=")";
			}
			else
			{
			$po_ids=implode(",",(array_unique(explode(",",$all_po_id))));
			$po_cond_for_in=" and b.id in($po_ids)";
		//	$po_cond_for_in2=" and d.po_break_down_id  in($po_ids)";
			}
			if(!empty($all_po_id))
			{
				$tna_po=$po_cond_for_in;
			}
			

			$sql_date="SELECT a.task_finish_date,a.po_number_id,a.task_number from tna_process_mst a, wo_po_break_down b where a.po_number_id = b.id and a.status_active=1 and b.is_deleted=0 and A.TASK_NUMBER in (70,71) $tna_po  group by a.task_finish_date,a.po_number_id,a.task_number,a.task_category";
			//echo $sql_date;

			$date_arr=array();

			$sql_result_date=sql_select($sql_date);
			foreach ($sql_result_date as $row) 
			{
				$date_arr[$row[csf('po_number_id')]][$row[csf('task_number')]]=$row[csf('task_finish_date')];
			}
			$order_sql=sql_select("select a.id as job_id, a.job_no, a.job_no_prefix_num, $select_year, a.style_ref_no, b.id as po_id, b.po_number,a.dealing_marchant,a.team_leader,b.grouping  from  wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and b.status_active=1 $po_cond_for_in");
			//grouping field
			$order_data_arr=array();
			//echo $order_sql[csf("po_id")];die;
			$i = 0;
			foreach($order_sql as $row)
			{
			$order_data_arr[$row[csf("po_id")]]["po_id"]=$row[csf("po_id")];
			$order_data_arr[$row[csf("po_id")]]["job_id"]=$row[csf("job_id")];
			$order_data_arr[$row[csf("po_id")]]["job_no"]=$row[csf("job_no")];
			$order_data_arr[$row[csf("po_id")]]["job_no_prefix_num"]=$row[csf("job_no_prefix_num")];
			$order_data_arr[$row[csf("po_id")]]["job_year"]=$row[csf("job_year")];
			$order_data_arr[$row[csf("po_id")]]["style_ref_no"]=$row[csf("style_ref_no")];
			$order_data_arr[$row[csf("po_id")]]["po_number"]=$row[csf("po_number")];
			$order_data_arr[$row[csf("po_id")]]["grouping"]=$row[csf("grouping")];
			$order_data_arr[$row[csf('po_id')]]['team_leader']=$row[csf('team_leader')];
			$order_data_arr[$row[csf('po_id')]]['dealing_marchant']=$row[csf('dealing_marchant')];
			//echo $order_data_arr[$row[csf("po_id")]]["po_number"];
			}
	
			$condition= new condition();
			$condition->company_name("=$cbo_company");
			if(str_replace("'","",$cbo_buyer)>0){
			$condition->buyer_name("=$cbo_buyer");
			}
			
			if($all_po_id!='')
			{
			$po_ids=implode(",",(array_unique(explode(",",$all_po_id))));
			$condition->po_id_in("$po_ids"); 
			}
			
			$condition->init();
			$emblishment= new emblishment($condition);
			$wash= new wash($condition);
			//echo $wash->getQuery();die;
			//echo $emblishment->getQuery(); die;
			$emblishment_qty_arr=$emblishment->getQtyArray_by_orderEmbnameAndEmbtype();
			$emblishment_amt_arr=$emblishment->getAmountArray_by_orderEmbnameAndEmbtype();
			$emblishment_wash_qty_arr=$wash->getQtyArray_by_orderEmbnameAndEmbtype();
			$emblishment_wash_amt_arr=$wash->getAmountArray_by_orderEmbnameAndEmbtype();
			//print_r($emblishment_wash_qty_arr);	
			$emb_sql=sql_select( "select c.id,c.emb_type,c.emb_name from  wo_po_break_down b,wo_pre_cost_embe_cost_dtls c where b.job_id=c.job_id and c.status_active=1 $po_cond_for_in");
			$pre_embl_arr=array();
			foreach($emb_sql as $row)
			{	
				$pre_embl_arr[$row[csf("id")]]["emb_type"]=$row[csf("emb_type")];
				$pre_embl_arr[$row[csf("id")]]["emb_name"]=$row[csf("emb_name")];
			}
			unset($emb_sql);
			
	?>
    <fieldset>
        <table width="2780"  cellspacing="0" >
            <tr class="form_caption" style="border:none;">
                <td align="center" style="border:none; font-size:18px;" colspan="22">
                    <? echo $company_library[str_replace("'","",$cbo_company_id)]; ?>
                </td>
            </tr>
            <tr class="form_caption" style="border:none;">
                <td align="center" style="border:none;font-size:16px; font-weight:bold"  colspan="25"> <? echo $report_title ;?></td>
            </tr>
            <tr class="form_caption" style="border:none;">
                <td align="center" style="border:none;font-size:12px; font-weight:bold"  colspan="25"> <? echo "From ".change_date_format($date_from)." To ".change_date_format($date_to);?></td>
            </tr>
        </table>
        <table width="2540" cellspacing="0" border="1" class="rpt_table" rules="all">
            <thead>
                <th width="40">SL</th>
                <th width="110">WO No</th>
                <th width="70">Approved Date</th>
                <th width="110">Internal Ref No</th>
                <th width="70">WO Date</th>
                <th width="70">Delivery Date</th>
                <th width="70">Lead Time</th>
                <th width="90">WO Type</th>
                <th width="100">Supplier</th>
                <th width="100">Buyer</th>
                <th width="50">Job Year</th>
                <th width="50">Job No.</th>
                <th width="100">Style Ref.</th>
                <th width="100">Order No</th>
                <th width="120">Emblishment Name</th>
                <th width="150">Emblishment Type</th>
                <th width="100">Item Category</th>
                <th width="60">Color</th>
                <th width="80">WO Qty</th>
                <th width="70">WO Unit price</th>
                <th width="80">WO value</th>
                <th width="70">Budget Unit price</th>
                <th width="80">Precost value</th>
                <th width="80" title="(Precost value - WO value)">Defference</th>
                <th width="80" title="(Defference / Precost value)*100">Defference %</th>
                
                <th width="120">Dealing Merchant</th>
                <th width="120">Team Leader</th>
                <th width="120">User Name</th>
                <th >Remarks</th>
            </thead>
        </table>
        <div style="max-height:350px; overflow-y:scroll; width:2560px" id="scroll_body" >
            <table cellspacing="0" cellpadding="0" border="1" class="rpt_table"  width="2540" rules="all" id="table_body" >
            <?
			
			$i=1;$item_group_sammary=array();
			$total_precost_value =0; $total_deference=0; $total_deference_per =0;

			foreach($sql_result as $row)
			{
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				
				if ($row[csf("wo_type")]==6)
					{
						if ($row[csf("is_short")]==1)
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
					
				$lead_time=datediff("d", $row[csf("wo_date")], $row[csf("delivery_date")]);
				$emb_type_id=$pre_embl_arr[$row[csf("pre_cost_fabric_cost_dtls_id")]]["emb_type"];
				$emb_name=$pre_embl_arr[$row[csf("id")]]["emb_name"]=$pre_embl_arr[$row[csf("pre_cost_fabric_cost_dtls_id")]]["emb_name"];
				if($emb_name==1)//Print
				{
				$emb_type=$emblishment_print_type[$pre_embl_arr[$row[csf("pre_cost_fabric_cost_dtls_id")]]["emb_type"]];
				}
				elseif($emb_name==2)//EMBRO
				{
				$emb_type=$emblishment_embroy_type[$pre_embl_arr[$row[csf("pre_cost_fabric_cost_dtls_id")]]["emb_type"]];
				}
				elseif($emb_name==4)//Spcial
				{
				$emb_type=$emblishment_spwork_type[$pre_embl_arr[$row[csf("pre_cost_fabric_cost_dtls_id")]]["emb_type"]];
				}
				elseif($emb_name==5)//Gmt
				{
				$emb_type=$emblishment_gmts_type[$pre_embl_arr[$row[csf("pre_cost_fabric_cost_dtls_id")]]["emb_type"]];
				}
				elseif($emb_name==3)//Wash
				{
				$emb_type=$emblishment_wash_type[$pre_embl_arr[$row[csf("pre_cost_fabric_cost_dtls_id")]]["emb_type"]];
				}
				
				if($emb_name!=3)//Without Wash
				{
				 $precost_qty=$emblishment_qty_arr[$row[csf("po_id")]][$emb_name][$emb_type_id];
				 $precost_value=$emblishment_amt_arr[$row[csf("po_id")]][$emb_name][$emb_type_id];
				}
				else 
				{
				 $precost_qty=$emblishment_wash_qty_arr[$row[csf("po_id")]][$emb_name][$emb_type_id];
				 $precost_value=$emblishment_wash_amt_arr[$row[csf("po_id")]][$emb_name][$emb_type_id];
				}
				$currency_id=$row[csf("currency_id")];
				if($currency_id==1) //TK exchange_rate
				{
					$wo_rate=$row[csf("rate")]/$row[csf("exchange_rate")];
				}
				else
				{
					$wo_rate=$row[csf("rate")];
				}
				
				
				if($row[csf("wo_qnty")]>0 && $emb_name>0)
				{
					$item_group_sammary[$emb_name]["wo_qnty"]+=$row[csf("wo_qnty")];
					$item_group_sammary[$emb_name]["wo_value"]+=$row[csf("wo_qnty")]*$wo_rate;
					$item_group_sammary[$emb_name]["pre_value"]+=$precost_value;
					//echo $precost_value.',';
					
					$item_group_sammary[$emb_name]["ontimeRcv"]+=$ontimeRcv;
					$item_group_sammary[$emb_name]["rcv_qnty"]+=$rcv_qnty;
					$item_group_sammary[$emb_name]["emblishment_name"]=$emb_name;
				}

				if($row[csf("supplier_id")]>0 && $row[csf("wo_qnty")]>0)
				{
					$supplier_wise_sammary[$row[csf("supplier_id")]]["wo_qnty"]+=$row[csf("wo_qnty")];
					$supplier_wise_sammary[$row[csf("supplier_id")]]["wo_value"]+=$row[csf("wo_qnty")]*$wo_rate;
					$supplier_wise_sammary[$row[csf("supplier_id")]]["pre_value"]+=$precost_value;
					
					$supplier_wise_sammary[$row[csf("supplier_id")]]["ontimeRcv"]+=$ontimeRcv;
					$supplier_wise_sammary[$row[csf("supplier_id")]]["rcv_qnty"]+=$rcv_qnty;
					$supplier_wise_sammary[$row[csf("supplier_id")]]["supplier_id"]=$row[csf("supplier_id")];
				}
				if($row[csf("is_approved")]==1)
				{
					$approved_date=$row[csf("update_date")];
					$approved_date = date('Y-m-d', strtotime($approved_date));
				}
				else $approved_date='';
				
				if($row[csf("pay_mode")]==3 || $row[csf("pay_mode")]==5) $supplier_com=$company_library[$row[csf("supplier_id")]]; else $supplier_com=$supplierArr[$row[csf("supplier_id")]];
				
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
                	<td width="40" align="center"><? echo $i;?></td>
                    <td width="110"><p><? echo $row[csf("booking_no")]; ?></td>
                    <td width="70"><p><? echo $approved_date; ?></td>
                    <td width="110"><p><? echo $order_data_arr[$row[csf("po_id")]]["grouping"]; ?></td>
                    <td width="70" align="center"><p><? echo change_date_format($row[csf("wo_date")]); ?>&nbsp;</p></td>
                    <td width="70" align="center"><p><?
                    	$date_del="";
						if($trim_type[$row[csf('trim_group')]]==1)
						{
							$date_del=$date_arr[$row[csf('po_id')]][70];
						}
						else{
							$date_del=$date_arr[$row[csf('po_id')]][71];
						}
						if(empty($date_del))
						{
							$date_del=$row[csf("delivery_date")];
						}
				 		echo change_date_format($row[csf("delivery_date")]); 
                     
                      ?>&nbsp;</p></td>
                    <td width="70" align="center"><p><? if($lead_time>0 && $lead_time<2) echo $lead_time." Day"; elseif($lead_time>1) echo $lead_time." Days"; else echo "0 Day"; ?>&nbsp;</p></td>
                    <td width="90"><p><?  echo $wo_type; ?>&nbsp;</p></td>
                    <td width="100"><p><? echo $supplier_com; ?>&nbsp;</p></td>
                    <td width="100"><p><? echo $buyerArr[$row[csf("buyer_id")]]; ?>&nbsp;</p></td>
                    <td width="50" align="center"><p><? echo $order_data_arr[$row[csf("po_id")]]["job_year"]; ?>&nbsp;</p></td>
                    <td width="50" align="center"><p><? echo $order_data_arr[$row[csf("po_id")]]["job_no_prefix_num"]; ?>&nbsp;</p></td>
                    <td width="100"><p><? echo $order_data_arr[$row[csf("po_id")]]["style_ref_no"]; ?>&nbsp;</p></td>
                    <td width="100"  style="word-break:break-all"><p><? echo $order_data_arr[$row[csf("po_id")]]["po_number"]; ?>&nbsp;</p></td>
                    <td width="120"><p><? echo $emblishment_name_array[$emb_name]; ?>&nbsp;</p></td>
                    <td width="150" style="word-break:break-all"><p><?
					
					echo $emb_type; ?>&nbsp;</p></td>
                    <td width="100"><p><? echo $item_category[$row[csf("item_category")]]; ?>&nbsp;</p></td>
                    <td width="60"><p><? echo $color_namerArr[$row[csf("gmts_color_id")]]; ?>&nbsp;</p></td>
                    <td width="80" align="right"><p><? echo number_format($row[csf("wo_qnty")],2,'.',''); ?></p></td>
                    <td width="70" align="right" title="USD Rate"><p><? echo number_format($wo_rate,2,'.',''); ?>&nbsp;</p></td>
                    <td width="80" align="right" ><p><? $wo_value=$row[csf("wo_qnty")]*$wo_rate; echo number_format($wo_value,4,'.',''); ?></p></td>
                    <td width="70" align="right" title="Pre Embl Qty=(<? echo $precost_qty;?>) "><p><? echo number_format($precost_value/$precost_qty,2,'.',''); ?></p></td>
                    <td width="80" align="right"><p><? //$precost_value=$row[csf("wo_qnty")]*$pre_cost_data_arr[$row[csf("po_id")]][$row[csf("trim_group")]];
					 $pre_cost_value=$precost_value;//$row[csf("wo_qnty")]*($precost_value/$precost_qty);
					 echo number_format($pre_cost_value,2); ?></p></td>
                    <td width="80" align="right"><p><? $deference = $pre_cost_value-$wo_value; echo number_format($deference,2,'.',''); ?></p></td>
                    <td width="80" align="right"><p><? $deference_per = ($deference/$pre_cost_value)*100; echo number_format($deference_per,2,'.',''); ?></p></td>
                   
                    <td width="120"><p><? echo $lib_team_member_arr[$order_data_arr[$row[csf("po_id")]]['dealing_marchant']]; ?></p></td>
                    <td width="120"><p><? echo $lib_team_leader_name_arr[$order_data_arr[$row[csf("po_id")]]['team_leader']] ; ?></p></td>
                    <td width="120" align="center"><p><? echo $userArr[$row[csf("inserted_by")]]; ?></p></td>
                    <td ><p><? echo $row[csf("remarks")]; ?> &nbsp;</p></td>
				</tr>
				<?
				$tot_wo_qnty+=$row[csf("wo_qnty")];
				$tot_wo_value+=$wo_value;
				$tot_receive_qnty+=$rcv_qnty;
				$tot_receive_value+=$rcv_value;
				$tot_rcv_balance+=$rcv_balance;
				$total_ontime_rcv+=$ontimeRcv;
				$total_precost_value +=$pre_cost_value;
				$total_deference += $deference;
				//$total_deference_per +=$deference_per;

				$i++;
			}
			?>
                <tfoot>
                    <th >&nbsp;</th>
                    <th >&nbsp;</th>
                    <th >&nbsp;</th>
                    <th >&nbsp;</th>
                    <th >&nbsp;</th>
                    <th >&nbsp;</th>
                    <th >&nbsp;</th>
                    <th >&nbsp;</th>
                    <th >&nbsp;</th>
                    <th >&nbsp;</th>
                    <th >&nbsp;</th>
                    <th >&nbsp;</th>
                    <th >&nbsp;</th>
                    <th >&nbsp;</th>
                    <th >&nbsp;</th>
                    <th >&nbsp;</th>
                    <th >&nbsp;</th>
                    <th >Total:</th>
                    <th id="value_tot_wo_qnty" align="right"><? echo number_format($tot_wo_qnty,2) ;?></th>
                    <th >&nbsp;</th>
                    <th id="value_tot_wo_value" align="right"><? echo number_format($tot_wo_value,4) ;?></th>
                    <th >&nbsp;</th>
                    <th id="value_tot_precost_value" align="right"><? echo number_format($total_precost_value,2) ;?></th>
                    <th id="value_tot_deference" align="right"><? echo number_format($total_deference,2) ;?></th>
                    <th id="value_tot_deference_per" align="right"><? echo number_format(($total_deference/$total_precost_value)*100,2) ;?></th>
                    
                    <th >&nbsp;</th>
                    <th >&nbsp;</th>
                    <th >&nbsp;</th>
                    <th >&nbsp;</th>
                </tfoot>
            </table>
        </div>
        <br>
        <table cellspacing="0" cellpadding="0" border="0" width="1500" rules="all">
        	<tr>
            	<td valign="top">

                    <table cellspacing="0" cellpadding="0" border="1" class="rpt_table"  width="860" rules="all">

                        <thead>
                        	<tr bgcolor="#CCCCCC">
                                <td colspan="8" align="center" style="font-size:16px; font-weight:bold;">Emblishment Name Wise Summary</td>
                            </tr>
                            <tr>
                                <th width="50">SL</th>
                                <th width="150">Item Name</th>
                                <th width="100">WO Qty</th>
                                <th width="100">WO Value</th>
                                <th width="100">Precost value</th>
                                <th width="100">Difference</th>
                                <th width="60">%</th>
                               
                            </tr>
                        </thead>
                        <tbody>
                            <?
                            $k=1;
							//print_r($item_group_sammary);die;
							foreach($item_group_sammary as $item_grp_id=>$val)
                            {
                               	$otd=(($val["ontimeRcv"]/$val["wo_qnty"])*100);
								$item_group_sammary[$item_grp_id]["otd"]=$otd;
							}

							foreach($item_group_sammary as $item_group=>$val)
							{
								$mid[$item_group]  = $val["otd"];
							}
							array_multisort($mid, SORT_DESC, $item_group_sammary);

                            foreach($item_group_sammary as $val)
                            {
                                if ($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                               // $rcv_bal=$val["wo_qnty"]-$val["rcv_qnty"];
                               //$otd=(($val["ontimeRcv"]/$val["wo_qnty"])*100);
                                ?>
                                <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
                                    <td><? echo $k; ?></td>
                                    <td>
									<?
									//echo $trimsGroupArr[$item_group];
									echo $emblishment_name_array[$val["emblishment_name"]];
									?></td>
                                    <td align="right"><? echo number_format($val["wo_qnty"],2); ?></td>
                                     <td align="right"><? echo number_format($val["wo_value"],4); ?></td>
                                     <td align="right"><? echo number_format($val["pre_value"],2); ?></td>
                                     <td align="right"><? $difference=$val["pre_value"]-$val["wo_value"];echo number_format($difference,2); ?></td>
                                     <td align="right" title="Difference/Pre value*100"><? echo number_format((($difference/$val["pre_value"])*100),2); ?></td>
                                   
                                </tr>
                                <?
                                $i++;$k++;
                                $sum_tot_wo_qnty+=$val["wo_qnty"];
								 $sum_tot_wo_value+=$val["wo_value"];
								  $sum_tot_pre_value+=$val["pre_value"];
								   $sum_tot_difference+=$difference;
								  
                                $sum_tot_ontime_rcv+=$val["ontimeRcv"];
                                $sum_tot_rcv_qnty+=$val["rcv_qnty"];
                                $sum_tot_rcv_bal+=$rcv_bal;//$val["rcv_bal"];
                            }
                            $tot_otd=(($sum_tot_ontime_rcv/$sum_tot_wo_qnty)*100);
                            ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th>&nbsp;</th>
                                <th>Total:</th>
                                <th align="right"><? echo number_format($sum_tot_wo_qnty,2); ?></th>
                                <th align="right"><? echo number_format($sum_tot_wo_value,4); ?></th>
                                <th align="right"><? echo number_format($sum_tot_pre_value,2); ?></th>
                                <th align="right"><? echo number_format($sum_tot_difference,2); ?></th>
                                <th align="right"><? echo number_format((($sum_tot_difference/$sum_tot_pre_value)*100),2); ?></th>
                                
                            </tr>
                        </tfoot>
                    </table>

                </td>
                <td valign="top">&nbsp;</td>
                <td valign="top">
                	<table cellspacing="0" cellpadding="0" border="1" class="rpt_table"  width="760" rules="all">

                        <thead>
                        	<tr bgcolor="#CCCCCC">
                                <td colspan="8" align="center" style="font-size:16px; font-weight:bold;">Supplier Wise Summary</td>
                            </tr>
                            <tr>
                                <th width="50">SL</th>
                                <th width="150">Supplier Name</th>
                                <th width="100">WO Qty</th>
                                <th width="100">WO Value</th>
                                <th width="100">Precost value</th>
                                <th width="100">Difference</th>
                                <th width="60">%</th>
                                
                               
                            </tr>
                        </thead>
                        <tbody>
                            <?
                            $m=1;
							foreach($supplier_wise_sammary as $supplier_id=>$val)
                            {
                                $otd=(($val["ontimeRcv"]/$val["wo_qnty"])*100);
								$supplier_wise_sammary[$supplier_id]["otd"]=$otd;
							}

							foreach($supplier_wise_sammary as $supplier_id=>$val)
							{
								$sid[$supplier_id]  = $val["otd"];
							}
							array_multisort($sid, SORT_DESC, $supplier_wise_sammary);
                            foreach($supplier_wise_sammary as $val)
                            {
                                if ($m%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                                $rcv_bal=$val["wo_qnty"]-$val["rcv_qnty"];
                                $otd=(($val["ontimeRcv"]/$val["wo_qnty"])*100);
                                ?>
                                <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
                                    <td><? echo $m; ?></td>
                                    <td><? echo $supplierArr[$val["supplier_id"]]; ?></td>
                                    <td align="right"><? echo number_format($val["wo_qnty"],2); ?></td>
                                    
                                    <td align="right"><? echo number_format($val["wo_value"],2); ?></td>
                                    <td align="right"><? echo number_format($val["pre_value"],2); ?></td>
                                    <td align="right"><? $diference=$val["wo_value"]-$val["pre_value"];echo number_format($diference,2); ?></td>
                                    <td align="right"><? echo number_format($val["wo_qnty"],2); ?></td>
                                   
                                </tr>
                                <?
                                $i++;$m++;
                                $sup_tot_wo_qnty+=$val["wo_qnty"];
								$sup_tot_wo_value+=$val["wo_value"];
								$sup_tot_pre_value+=$val["pre_value"];
								$sup_tot_diference+=$diference;
								
                                $sup_tot_ontime_rcv+=$val["ontimeRcv"];
                                $sup_tot_rcv_qnty+=$val["rcv_qnty"];
                                $sup_tot_rcv_bal+=$rcv_bal;//$val["rcv_bal"];
                            }
                            $sup_tot_otd=(($sup_tot_ontime_rcv/$sup_tot_wo_qnty)*100);
                            ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th>&nbsp;</th>
                                <th>Total:</th>
                                <th align="right"><? echo number_format($sup_tot_wo_qnty,2); ?></th>
                                <th align="right"><? echo number_format($sup_tot_wo_value,2); ?></th>
                                <th align="right"><? echo number_format($sup_tot_pre_value,2); ?></th>
                                <th align="right"><? echo number_format($sup_tot_diference,2); ?></th>
                                <th align="right"><? echo number_format((($sup_tot_diference/$sup_tot_pre_value)*100),2); ?></th>
                                
                               
                            </tr>
                        </tfoot>
                    </table>
                </td>
            </tr>
        </table>
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
if ($action=="report_generate_2")
{
	//extract($_REQUEST);
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$cbo_company=str_replace("'","",$cbo_company_id);
	$wo_type=str_replace("'","",$cbo_wo_type);
	$cbo_category=str_replace("'","",$cbo_category_id);
	$cbo_buyer=str_replace("'","",$cbo_buyer_id);
	$year_id=str_replace("'","",$cbo_year_id);
	$wo_no=str_replace("'","",$txt_wo_no);
	$wo_id=str_replace("'","",$hidd_wo_id);
	$date_from=str_replace("'","",$txt_date_from);
	$date_to=str_replace("'","",$txt_date_to);
	$cbo_supplier=str_replace("'","",$cbo_supplier);
	$txt_date_category=str_replace("'","",$txt_date_category);
	$hidd_item_id=str_replace("'","",$hidd_item_id);
	$txt_item_no=str_replace("'","",$txt_item_no);
	$cbo_search_type=str_replace("'","",$cbo_search_type);
	$txt_search_common=str_replace("'","",$txt_search_common);
	$txt_job_po_id=str_replace("'","",$txt_job_po_id);
	$cbo_within_group_id=str_replace("'","",$cbo_within_group_id);

	
	if($cbo_search_type==1)
	{
		if($txt_search_common!="" && $txt_job_po_id!="")
		{
			$job_cond="and a.id in($txt_job_po_id)";
		}
		else if($txt_search_common!="" && $txt_job_po_id=="")
		{
			$job_cond="and a.job_no_prefix_num=$txt_search_common";

		}
	}
	else if($cbo_search_type==2)
	{
		if($txt_search_common!="" && $txt_job_po_id!="")
		{
			$job_cond="and a.id in($txt_job_po_id)";
		}
		else if($txt_search_common!="" && $txt_job_po_id=="")
		{
			$job_cond="and a.style_ref_no='$txt_search_common'";

		}
	}else if($cbo_search_type==3)
	{
		
			if ($txt_search_common=="") $txt_search_common=""; else $internal_ref_cond=" and b.grouping='".trim($txt_search_common)."' ";
	
	
	}
	
	if($cbo_within_group_id==1)
	{
		$within_cond="and a.pay_mode in(3,5)";
	} 
	else if($cbo_within_group_id==2)
	{ 
	  $within_cond="and a.pay_mode not in(3,5)";
	}
	else $within_cond="";
	//die;

	$sql_cond="";

	if($db_type==0)
	{
		if ($year_id==0) $year_id_cond=""; else $year_id_cond=" and YEAR(a.insert_date)=$year_id";
	}
	elseif($db_type==2)
	{
		if ($year_id==0) $year_id_cond=""; else $year_id_cond=" and TO_CHAR(a.insert_date,'YYYY')=$year_id";
	}

	if ($cbo_company!=0) $sql_cond=" and a.company_id=$cbo_company";
	if ($cbo_buyer!=0) $sql_cond.=" and a.buyer_id=$cbo_buyer";
	if ($cbo_buyer!=0) $buyer_id_cond=" and a.buyer_name=$cbo_buyer";else $buyer_id_cond="";
	if ($cbo_category!=0)  $sql_cond.=" and a.item_category=$cbo_category";
	if ($wo_no!="")  $sql_cond.=" and a.booking_no like '%$wo_no%'";
	if ($cbo_supplier>0)  $sql_cond.=" and a.supplier_id=$cbo_supplier";

	if ($hidd_item_id=="") $item_id=""; else $item_id=" and b.trim_group in ( $hidd_item_id )";

	/*if ($wo_type==1 || $wo_type==2)  $sql_cond.=" and a.booking_type in (1,2) and a.is_short='$wo_type'";
	if ($wo_type==3) $sql_cond.="  and a.booking_type=4";*/

	//echo $file_no_cond.'=='.$internal_ref_cond;die;
	if($txt_date_category==1)
	{
		if($db_type==0)
		{
			if( $date_from=="" && $date_to=="" ) $booking_date_cond=""; else $booking_date_cond=" and a.booking_date between '".$date_from."' and '".$date_to."'";
		}
		if($db_type==2)
		{
			if( $date_from=="" && $date_to=="" ) $booking_date_cond=""; else $booking_date_cond=" and a.booking_date between '".change_date_format($date_from,'dd-mm-yyyy','-',1)."' and '".change_date_format($date_to,'dd-mm-yyyy','-',1)."'";
		}
	}
	else
	{
		if($db_type==0)
		{
			if( $date_from=="" && $date_to=="" ) $booking_date_cond=""; else $booking_date_cond=" and b.delivery_date between '".$date_from."' and '".$date_to."'";
		}
		if($db_type==2)
		{
			if( $date_from=="" && $date_to=="" ) $booking_date_cond=""; else $booking_date_cond=" and b.delivery_date between '".change_date_format($date_from,'dd-mm-yyyy','-',1)."' and '".change_date_format($date_to,'dd-mm-yyyy','-',1)."'";
		}
	}


	if($db_type==0) $select_year="year(a.insert_date) as job_year"; else if($db_type==2) $select_year="to_char(a.insert_date,'YYYY') as job_year";

	$lib_team_name_arr=return_library_array( "select id, team_name from lib_marketing_team", "id", "team_name"  );
	$lib_team_leader_name_arr=return_library_array( "select id, team_leader_name from lib_marketing_team", "id", "team_leader_name"  );
	$lib_team_member_arr = return_library_array("select id, team_member_name from lib_mkt_team_member_info", "id", "team_member_name");
	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name");
	$supplierArr=return_library_array( "select id,supplier_name from lib_supplier", "id", "supplier_name");
	$buyerArr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name");
	$trimsGroupArr=return_library_array( "select id, item_name from lib_item_group", "id", "item_name");
	$userArr=return_library_array( "select id, user_name from user_passwd", "id", "user_name");
	$trim_type= return_library_array("select id, trim_type from lib_item_group",'id','trim_type');
	//$nonStyleArr=return_library_array( "select id, style_ref_no from sample_development_mst", "id", "style_ref_no");
		if($txt_search_common!="")
		{
		$sql_po=sql_select("select  b.id,b.job_no_mst  from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst   and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1  and a.company_name=$cbo_company $buyer_id_cond  $job_cond $internal_ref_cond group by   b.id,b.job_no_mst order by b.id,b.job_no_mst");
		//echo "select  b.id,b.job_no_mst  from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst   and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1  and a.company_name=$cbo_company $buyer_id_cond  $job_cond group by   b.id,b.job_no_mst order by b.id,b.job_no_mst";die;
		foreach( $sql_po as $row)
		{
			//$sql_po_qty_country_wise_arr[$row[csf('id')]][$row[csf('country_id')]]=$row[csf('order_quantity_set')];
			$po_id_arr[$row[csf('id')]]=$row[csf('id')];
		}
		unset($sql_po);
		}
		if($txt_search_common!="")
		{
			//$po_idCond="and b.po_break_down_id in(".implode(",",$po_id_arr).")";
		    $po_idCond=where_con_using_array(array_unique($po_id_arr),0,"b.po_break_down_id");
		} 
		else $po_idCond="";
	
	if($cbo_category==4) //Accessories
	{
		if($wo_type==0)
		{
			$sql="select a.id as book_mst_id, a.is_approved, a.update_date, a.pay_mode, a.booking_no as booking_no, a.booking_date as wo_date, b.delivery_date, a.booking_type as wo_type, a.is_short, a.supplier_id as supplier_id, a.buyer_id as buyer_id, a.item_category as item_category, a.remarks as remarks,a.inserted_by as inserted_by, b.id as dtls_id, b.po_break_down_id as po_id, b.pre_cost_fabric_cost_dtls_id as pre_cost_fabric_cost_dtls_id, b.trim_group as trim_group, b.construction as construction, b.copmposition as copmposition, b.uom as wo_uom, b.wo_qnty as wo_qnty,(case when a.exchange_rate>0 then (b.rate/a.exchange_rate) else b.rate end) as wo_rate, 1 as type
			from wo_booking_mst a, wo_booking_dtls b
			where a.booking_no=b.booking_no and a.booking_type in(2,5) and a.item_category=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1
		 and b.is_deleted=0  $po_idCond $item_id $sql_cond $booking_date_cond $within_cond
			union all
			select a.id as book_mst_id, a.is_approved,a.update_date,a.pay_mode,a.booking_no as booking_no, a.booking_date as wo_date, a.delivery_date, a.booking_type as wo_type, a.is_short, a.supplier_id as supplier_id, a.buyer_id as buyer_id, a.item_category as item_category, null as remarks,0 as inserted_by, b.id as dtls_id, 0 as po_id, 0 as pre_cost_fabric_cost_dtls_id, b.trim_group as trim_group, b.construction as construction, b.composition as copmposition, b.uom as wo_uom, b.trim_qty as wo_qnty,  (case when a.exchange_rate>0 then (b.rate/a.exchange_rate) else b.rate end) as wo_rate, 2 as type
			from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b
			where a.booking_no=b.booking_no and a.booking_type in(2,3)  and a.item_category=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1
		 and b.is_deleted=0  $item_id $sql_cond $booking_date_cond $within_cond order by book_mst_id ,po_id";// and b.po_break_down_id=33350
		}
		else if ($wo_type==1 || $wo_type==2)
		{
			$sql="select a.id as book_mst_id,a.is_approved,a.update_date,a.pay_mode, a.booking_no as booking_no, a.booking_date as wo_date, b.delivery_date, a.booking_type as wo_type, a.is_short, a.supplier_id as supplier_id, a.buyer_id as buyer_id, a.item_category as item_category, a.remarks as remarks, a.inserted_by as inserted_by, b.id as dtls_id, b.po_break_down_id as po_id, b.pre_cost_fabric_cost_dtls_id as pre_cost_fabric_cost_dtls_id, b.trim_group as trim_group, b.construction as construction, b.copmposition as copmposition, b.uom as wo_uom, b.wo_qnty as wo_qnty,(case when a.exchange_rate>0 then (b.rate/a.exchange_rate) else b.rate end) as wo_rate, 1 as type
			from wo_booking_mst a, wo_booking_dtls b
			where a.booking_no=b.booking_no and a.booking_type in(2) and a.is_short=$wo_type and a.item_category=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1
		 and b.is_deleted=0 $po_idCond $sql_cond $item_id $booking_date_cond $within_cond
			order by a.id";
		}
		else if ($wo_type==3)
		{
			$sql="select a.id as book_mst_id,a.is_approved,a.update_date,a.pay_mode, a.booking_no as booking_no, a.booking_date as wo_date, b.delivery_date, a.booking_type as wo_type, a.is_short, a.supplier_id as supplier_id, a.buyer_id as buyer_id, a.item_category as item_category, a.remarks as remarks,a.inserted_by as inserted_by, b.id as dtls_id, b.po_break_down_id as po_id, b.pre_cost_fabric_cost_dtls_id as pre_cost_fabric_cost_dtls_id, b.trim_group as trim_group, b.construction as construction, b.copmposition as copmposition, b.uom as wo_uom, b.wo_qnty as wo_qnty, (case when a.exchange_rate>0 then (b.rate/a.exchange_rate) else b.rate end) as wo_rate, 1 as type
			from wo_booking_mst a, wo_booking_dtls b
			where a.booking_no=b.booking_no and a.booking_type in(5) and a.item_category=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1
		 and b.is_deleted=0 $po_idCond $item_id $sql_cond $booking_date_cond $within_cond
			order by a.id";
		}
		else
		{
			$sql="select a.id as book_mst_id,a.is_approved,a.update_date,a.pay_mode, a.booking_no as booking_no, a.booking_date as wo_date, a.delivery_date, a.booking_type as wo_type, a.is_short, a.supplier_id as supplier_id, a.buyer_id as buyer_id, a.item_category as item_category, null as remarks, b.id as dtls_id, 0 as po_id, 0 as pre_cost_fabric_cost_dtls_id, b.trim_group as trim_group, b.construction as construction, b.composition as copmposition, b.uom as wo_uom, b.trim_qty as wo_qnty, (case when a.exchange_rate>0 then (b.rate/a.exchange_rate) else b.rate end) as wo_rate , 2 as type
			from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b
			where a.booking_no=b.booking_no and a.booking_type in(2,3)  and a.item_category=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1
		 and b.is_deleted=0 $item_id $sql_cond $booking_date_cond $within_cond";
		}
	}
	else if($cbo_category==25) //Emblishmnet
	{
		if($wo_type==0)
		{
			 $sql="select a.id as book_mst_id,a.currency_id, a.is_approved, a.update_date, a.pay_mode, a.booking_no as booking_no, a.booking_date as wo_date, b.delivery_date, a.booking_type as wo_type, a.is_short, a.supplier_id as supplier_id, a.buyer_id as buyer_id, a.item_category as item_category, a.remarks as remarks,a.inserted_by as inserted_by, b.id as dtls_id, b.po_break_down_id as po_id, b.pre_cost_fabric_cost_dtls_id as pre_cost_fabric_cost_dtls_id, b.emblishment_name,b.construction as construction, b.copmposition as copmposition, b.uom as wo_uom,b.gmts_color_id, b.wo_qnty as wo_qnty,b.rate,a.exchange_rate,(case when a.exchange_rate>0 then (b.rate/a.exchange_rate) else b.rate end) as wo_rate, 1 as type,, b.trim_group as trim_group
			from wo_booking_mst a, wo_booking_dtls b
			where a.booking_no=b.booking_no and a.booking_type in(6) and b.wo_qnty>0 and a.item_category=$cbo_category and a.status_active=1 and a.is_deleted=0 and b.status_active=1
		 and b.is_deleted=0   $po_idCond  $sql_cond $booking_date_cond $within_cond
			";// and b.po_break_down_id=33350
		}
		else if ($wo_type==1 || $wo_type==2)
		{
			$sql="select a.id as book_mst_id,a.currency_id,a.is_approved,a.update_date,a.pay_mode, a.booking_no as booking_no, a.booking_date as wo_date, b.delivery_date, a.booking_type as wo_type, a.is_short, a.supplier_id as supplier_id, a.buyer_id as buyer_id, a.item_category as item_category, a.remarks as remarks, a.inserted_by as inserted_by, b.id as dtls_id, b.po_break_down_id as po_id, b.pre_cost_fabric_cost_dtls_id as pre_cost_fabric_cost_dtls_id, b.emblishment_name, b.construction as construction, b.copmposition as copmposition, b.uom as wo_uom,b.gmts_color_id, b.wo_qnty as wo_qnty,b.rate,a.exchange_rate,(case when a.exchange_rate>0 then (b.rate/a.exchange_rate) else b.rate end) as wo_rate, 1 as type,, b.trim_group as trim_group
			from wo_booking_mst a, wo_booking_dtls b
			where a.booking_no=b.booking_no and a.booking_type in(6) and b.wo_qnty>0 and a.is_short=$wo_type and a.item_category=$cbo_category and a.status_active=1 and a.is_deleted=0 and b.status_active=1
		 and b.is_deleted=0  $po_idCond  $sql_cond   $booking_date_cond $within_cond
			order by a.id";
		}
	}
	//echo $sql;die;

	ob_start();
	if($cbo_category==4) //Accessories
	{
		// echo $sql;
		$sql_result=sql_select($sql);
		$all_po_id=""; $bookingIdArr=array();
		foreach($sql_result as $row)
		{
			if($all_po_id=="") $all_po_id=$row[csf("po_id")];else $all_po_id.=",".$row[csf("po_id")];
			//$emblish_arr[$row[csf("po_id")]]["po_id"]=$row[csf("po_id")];
			$bookingIdArr[$row[csf("booking_no")]]=$row[csf("book_mst_id")];
		}
		//echo $all_po_id.'ds';
		$poIds=chop($all_po_id,','); $po_cond_for_in=""; $po_cond_for_in2=""; $po_cond_for_in3=""; 
		$po_ids=count(array_unique(explode(",",$all_po_id)));
		// if($db_type==2 && $po_ids>1000)
		// {
		// 	$po_cond_for_in=" and (";
		// 	$po_cond_for_in2=" and (";
		// 	$poIdsArr=array_chunk(explode(",",$poIds),999);
		// 	foreach($poIdsArr as $ids)
		// 	{
		// 		$ids=implode(",",$ids);
		// 		//$poIds_cond.=" po_break_down_id in($ids) or ";
		// 		$po_cond_for_in.=" b.id in($ids) or"; 
		// 		$po_cond_for_in2.=" b.po_break_down_id in($ids) or"; 
		// 		$po_cond_for_in3.=" d.po_breakdown_id in($ids) or"; 
		// 		$tna_po.=" b.id in($ids) or";
		// 	}
		// 	$po_cond_for_in=chop($po_cond_for_in,'or ');
		// 	$po_cond_for_in.=")";
		// 	$po_cond_for_in2=chop($po_cond_for_in2,'or ');
		// 	$po_cond_for_in2.=")";

		// 	$po_cond_for_in3=chop($po_cond_for_in3,'or ');
		// 	$po_cond_for_in3.=")";

		// 	$tna_po=chop($tna_po,'or ');
		// 	$tna_po.=")";
		// }
		// else
		// {
		// 	$po_ids=implode(",",(array_unique(explode(",",$all_po_id))));
		// 	$po_cond_for_in=" and b.id in($po_ids)";
		// 	$po_cond_for_in2=" and b.po_break_down_id  in($po_ids)";
		// 	$po_cond_for_in3=" and d.po_breakdown_id  in($po_ids)";//po_break_down_id

		// 	$tna_po=" and  b.id  in($po_ids)";//po_break_down_id
		// }

		$po_cond_for_in=where_con_using_array(array_unique(explode(",",$all_po_id)),0,"b.id");
		$po_cond_for_in2=where_con_using_array(array_unique(explode(",",$all_po_id)),0,"b.po_break_down_id");
		$po_cond_for_in3=where_con_using_array(array_unique(explode(",",$all_po_id)),0,"d.po_breakdown_id");
		$tna_po=where_con_using_array(array_unique(explode(",",$all_po_id)),0,"b.id");

		$sql_date="SELECT a.task_finish_date,a.po_number_id,a.task_number from tna_process_mst a, wo_po_break_down b where a.po_number_id = b.id and a.status_active=1 and b.is_deleted=0 and A.TASK_NUMBER in (70,71) $tna_po  group by a.task_finish_date,a.po_number_id,a.task_number,a.task_category";
		//echo $sql_date;

		$date_arr=array();

		$sql_result=sql_select($sql_date);
		foreach ($sql_result as $row) 
		{
			$date_arr[$row[csf('po_number_id')]][$row[csf('task_number')]]=$row[csf('task_finish_date')];
		}
			
		$order_sql=sql_select("select a.id as job_id, a.job_no, a.job_no_prefix_num, $select_year, a.style_ref_no, b.id as po_id, b.po_number,a.dealing_marchant,a.team_leader,b.grouping,b.po_quantity  from  wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1  and b.status_active=1 $po_cond_for_in $internal_ref_cond");
		//echo "select a.id as job_id, a.job_no, a.job_no_prefix_num, $select_year, a.style_ref_no, b.id as po_id, b.po_number,a.dealing_marchant,a.team_leader,b.grouping  from  wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and b.status_active=1 $po_cond_for_in";
		//grouping field
		$order_data_arr=array();
		//echo $order_sql[csf("po_id")];die;
		$i = 0;
		foreach($order_sql as $row)
		{
			$order_data_arr[$row[csf("po_id")]]["po_id"]=$row[csf("po_id")];
			$order_data_arr[$row[csf("po_id")]]["job_id"]=$row[csf("job_id")];
			$order_data_arr[$row[csf("po_id")]]["job_no"]=$row[csf("job_no")];
			$order_data_arr[$row[csf("po_id")]]["job_no_prefix_num"]=$row[csf("job_no_prefix_num")];
			$order_data_arr[$row[csf("po_id")]]["job_year"]=$row[csf("job_year")];
			$order_data_arr[$row[csf("po_id")]]["style_ref_no"]=$row[csf("style_ref_no")];
			
			$order_data_arr[$row[csf("po_id")]]["grouping"]=$row[csf("grouping")];
			$order_data_arr[$row[csf('po_id')]]['team_leader']=$row[csf('team_leader')];
			$order_data_arr[$row[csf('po_id')]]['dealing_marchant']=$row[csf('dealing_marchant')];

		    $order_data_arr[$row[csf("po_id")]]["po_number"]=$row[csf("po_number")];
			$order_data_arr[$row[csf("po_id")]]["order_qty"]=$row[csf("po_quantity")];


			//echo $order_data_arr[$row[csf("po_id")]]["po_number"];
		}
		unset($order_sql);
		$trims_sql=sql_select("select b.id as po_id, a.trim_group, sum(a.rate) as trims_rate from wo_pre_cost_trim_cost_dtls a, wo_po_break_down b where  a.job_no=b.job_no_mst and a.status_active=1 $po_cond_for_in group by b.id, a.trim_group");
		//echo "select b.id as po_id, a.trim_group, sum(a.rate) as trims_rate from wo_pre_cost_trim_cost_dtls a, wo_po_break_down b where  a.job_no=b.job_no_mst and a.status_active=1 $po_cond_for_in group by b.id, a.trim_group";
		$pre_cost_data_arr=array();
		foreach($trims_sql as $row)
		{
			$pre_cost_data_arr[$row[csf("po_id")]][$row[csf("trim_group")]]=$row[csf("trims_rate")];
		}
		unset($trims_sql);
		$description_sql="select b.wo_trim_booking_dtls_id, b.description, b.brand_supplier, b.item_color, b.item_size from  wo_trim_book_con_dtls b where b.status_active=1 $po_cond_for_in2";
		//echo $description_sql;
		$description_sql_result=sql_select($description_sql);
		$description_arr=array();
		foreach($description_sql_result as $row)
		{
			if( ($row[csf("description")]!=0 || $row[csf("description")]!="") && $description_check[$row[csf("wo_trim_booking_dtls_id")]][$row[csf("description")]]=="")
			{
				$description_check[$row[csf("wo_trim_booking_dtls_id")]][$row[csf("description")]]=$row[csf("description")];
				$description_arr[$row[csf("wo_trim_booking_dtls_id")]]["description"].=$row[csf("description")]."__";
			}
			if($brand_supplier_check[$row[csf("wo_trim_booking_dtls_id")]][$row[csf("brand_supplier")]]=="")
			{
				$brand_supplier_check[$row[csf("wo_trim_booking_dtls_id")]][$row[csf("brand_supplier")]]=$row[csf("brand_supplier")];
				$description_arr[$row[csf("wo_trim_booking_dtls_id")]]["brand_supplier"].=$row[csf("brand_supplier")]."__";
			}
			
			if($item_color_check[$row[csf("wo_trim_booking_dtls_id")]][$row[csf("item_color")]]=="")
			{
				$item_color_check[$row[csf("wo_trim_booking_dtls_id")]][$row[csf("item_color")]]=$row[csf("item_color")];
				$description_arr[$row[csf("wo_trim_booking_dtls_id")]]["item_color"].=$row[csf("item_color")]."__";
			}
			
			if($item_size_check[$row[csf("wo_trim_booking_dtls_id")]][$row[csf("item_size")]]=="")
			{
				$item_size_check[$row[csf("wo_trim_booking_dtls_id")]][$row[csf("item_size")]]=$row[csf("item_size")];
				$description_arr[$row[csf("wo_trim_booking_dtls_id")]]["item_size"].=$row[csf("item_size")]."__";
			}
		}
		unset($description_sql_result);
		$piBookingsql=sql_select( "select a.pi_id, a.work_order_id, a.item_group, b.po_break_down_id from com_pi_item_details a, wo_booking_dtls b where a.work_order_dtls_id=b.id and  a.item_category_id = 4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $po_cond_for_in2");
		$piBookingNo=array();
		foreach($piBookingsql as $row)
		{
			$piBookingNo[$row[csf("pi_id")]][$row[csf("po_break_down_id")]][$row[csf("item_group")]]=$row[csf("work_order_id")];
		}
		unset($piBookingsql);
	
		/*$rcv_qnty_sql="select b.mst_id, b.receive_basis, b.transaction_date as receive_date, c.booking_id as pi_wo_batch_no, b.booking_no, c.item_group_id, c.item_description, c.brand_supplier, d.po_breakdown_id as po_id, d.quantity as rcv_qnty, d.order_amount as rcv_value, e.work_order_id
		from inv_trims_entry_dtls c, order_wise_pro_details d, inv_transaction b 
		left join com_pi_item_details e on e.pi_id=b.pi_wo_batch_no
		where b.id=c.trans_id and c.id=d.dtls_id and c.trans_id=d.trans_id and b.transaction_type=1 and b.item_category=4 and d.entry_form=24 and d.status_active=1 and d.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $po_cond_for_in3 
		group by b.mst_id, b.receive_basis, b.transaction_date, c.booking_id, b.booking_no, c.item_group_id, c.item_description, c.brand_supplier, d.po_breakdown_id, d.quantity, d.order_amount, e.work_order_id";*/
		
		$rcv_qnty_sql="select b.mst_id, b.receive_basis, b.transaction_date as receive_date, c.booking_id as pi_wo_batch_no, b.booking_no, c.item_group_id, c.item_description, c.brand_supplier, d.po_breakdown_id as po_id, d.id as prop_id, d.quantity as rcv_qnty, d.order_amount as rcv_value, e.work_order_id
		from inv_trims_entry_dtls c, order_wise_pro_details d, inv_transaction b 
		left join com_pi_item_details e on e.pi_id=b.pi_wo_batch_no
		where b.id=c.trans_id and c.id=d.dtls_id and c.trans_id=d.trans_id and b.transaction_type=1 and b.item_category=4 and d.entry_form=24 and d.status_active=1 and d.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $po_cond_for_in3";
		//echo $rcv_qnty_sql;
		$receive_qty_data=sql_select($rcv_qnty_sql);
		$rcv_data_po=array();
		$rcv_data_po_ontime=array();
		foreach($receive_qty_data as $row)
		{
			$woid=$row[csf('pi_wo_batch_no')];
			$itemgroup=$row[csf('item_group_id')];
			$po_id=$row[csf('po_id')];
			if($row[csf('receive_basis')]==1) $woid=$row[csf('work_order_id')];
			if($row[csf('receive_basis')]==12) $woid=$bookingIdArr[$row[csf("booking_no")]];
			//echo $woid.'='.$po_id.'='.$itemgroup.'='.$row[csf('item_description')].'='.$row[csf('brand_supplier')].'<br>';
			
			$rcv_data_po[$woid][$po_id][$itemgroup][$row[csf('item_description')]]["receive_date"].=$row[csf('receive_date')].",";
			$rcv_data_po[$woid][$po_id][$itemgroup][$row[csf('item_description')]]["receive_basis"]=$row[csf('receive_basis')];
			
			if($propo_id_check[$row[csf('prop_id')]]=="")
			{
				$propo_id_check[$row[csf('prop_id')]]=$row[csf('prop_id')];
				$rcv_data_po[$woid][$po_id][$itemgroup][$row[csf('item_description')]]["rcv_qnty"]+=$row[csf('rcv_qnty')];
				$rcv_data_po[$woid][$po_id][$itemgroup][$row[csf('item_description')]]["rcv_value"]+=$row[csf('rcv_value')];
				$rcv_data_po_ontime[$row[csf('receive_date')]][$woid][$po_id][$itemgroup][$row[csf('item_description')]]["rcv_qnty"]+=$row[csf('rcv_qnty')];
				$rcv_data_po_ontime[$row[csf('receive_date')]][$woid][$po_id][$itemgroup][$row[csf('item_description')]]["rcv_value"]+=$row[csf('rcv_value')];
			}
			
			if($row[csf('mst_id')] && $mst_id_check[$row[csf('receive_date')]][$woid][$po_id][$itemgroup][$row[csf('item_description')]][$row[csf('mst_id')]]=="")
			{
				 $mst_id_check[$row[csf('receive_date')]][$woid][$po_id][$itemgroup][$row[csf('item_description')]][$row[csf('mst_id')]]=$row[csf('mst_id')];
				$rcv_data_po_ontime[$row[csf('receive_date')]][$woid][$po_id][$itemgroup][$row[csf('item_description')]]["mst_id"].=$row[csf('mst_id')].",";
			}
			
		}
		/*echo "<pre>";
		print_r($rcv_data_po_ontime); die;*/
		unset($receive_qty_data);
	
		$receive_qty_data_noorder=sql_select("select a.id as mst_id, a.receive_basis, a.receive_date, c.booking_id as pi_wo_batch_no, b.cons_quantity as rcv_qnty, c.item_group_id, c.item_description, c.brand_supplier, b.order_amount as rcv_value
		from inv_receive_master a, inv_transaction b, inv_trims_entry_dtls c
		where a.id=b.mst_id and b.id=c.trans_id and b.transaction_type=1  and b.pi_wo_batch_no>0 and b.item_category=4 and a.entry_form=24 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0");
		
		$rcv_data_nonOrder=array();
		$rcv_data_nonOrder_on_time=array();
		foreach($receive_qty_data_noorder as $row)
		{
			$woid=$row[csf('pi_wo_batch_no')];
			$itemgroup=$row[csf('item_group_id')];
			if($row[csf('receive_basis')]==1) $woid=$piBookingNo[$woid][$po_id][$itemgroup];
			$rcv_data_nonOrder[$woid][$itemgroup][$row[csf('item_description')]]["rcv_qnty"]+=$row[csf('rcv_qnty')];
			$rcv_data_nonOrder[$woid][$itemgroup][$row[csf('item_description')]]["rcv_value"]+=$row[csf('rcv_value')];
			$rcv_data_nonOrder[$woid][$itemgroup][$row[csf('item_description')]]["receive_date"].=$row[csf('receive_date')].",";
	
			$rcv_data_nonOrder_on_time[$row[csf('receive_date')]][$woid][$itemgroup][$row[csf('item_description')]]["rcv_qnty"]+=$row[csf('rcv_qnty')];
			$rcv_data_nonOrder_on_time[$row[csf('receive_date')]][$woid][$itemgroup][$row[csf('item_description')]]["rcv_value"]+=$row[csf('rcv_value')];
			if($row[csf('mst_id')] && $non_mst_id_check[$row[csf('receive_date')]][$woid][$itemgroup][$row[csf('item_description')]][$row[csf('mst_id')]]=="")
			{
				$non_mst_id_check[$row[csf('receive_date')]][$woid][$itemgroup][$row[csf('item_description')]][$row[csf('mst_id')]]=$row[csf('mst_id')];
				$rcv_data_nonOrder_on_time[$row[csf('receive_date')]][$woid][$itemgroup][$row[csf('item_description')]]["mst_id"].=$row[csf('mst_id')].",";
			}
		}
		unset($receive_qty_data_noorder);
		//echo "test";die;
		?>
		<fieldset>
			<table width="2880"  cellspacing="0" >
				<tr class="form_caption" style="border:none;">
					<td align="center" style="border:none; font-size:18px;" colspan="22">
						<? echo $company_library[str_replace("'","",$cbo_company_id)]; ?>
					</td>
				</tr>
				<tr class="form_caption" style="border:none;">
					<td align="center" style="border:none;font-size:16px; font-weight:bold"  colspan="25"> <? echo $report_title ;?></td>
				</tr>
				<tr class="form_caption" style="border:none;">
					<td align="center" style="border:none;font-size:12px; font-weight:bold"  colspan="25"> <? echo "From ".change_date_format($date_from)." To ".change_date_format($date_to);?></td>
				</tr>
			</table>
			<table width="3040" cellspacing="0" border="1" class="rpt_table" rules="all">
				<thead>
					<th width="40">SL</th>
					<th width="110">WO No</th>
					<th width="70">Approved Date</th>
					<th width="110">Internal Ref No</th>
					<th width="70">WO Date</th>
					<th width="70">Delivery Date</th>
					<th width="70">Lead Time</th>
					<th width="90">WO Type</th>
					<th width="100">Supplier</th>
					<th width="100">Buyer</th>
					<th width="50">Job Year</th>
					<th width="50">Job No.</th>
					<th width="100">Style Ref.</th>
					<th width="100">Order No</th>
					<th width="100">Order qty</th>
					<th width="120">Item Name</th>
					<th width="150">Description</th>
					<th width="100">Item Category</th>
					<th width="60">UOM</th>
					<th width="80">WO Qty</th>
					<th width="70">WO Unit price</th>
					<th width="80">WO value</th>
					<th width="70">Budget Unit price</th>
					<th width="80">Precost value</th>
					<th width="80" title="(Precost value - WO value)">Deference</th>
					<th width="80" title="(Deference / Precost value)*100">Deference %</th>
					<th width="80">On Time Receive</th>
					<th width="80">OTD%</th>
					<th width="80">Total Receive Qty</th>
					<th width="80">Receive Value</th>
					<th width="80">Receive Balance</th>
					<th width="120">Dealing Merchant</th>
					<th width="120">Team Leader</th>
					<th width="120">User Name</th>
					<th >Remarks</th>
				</thead>
			</table>
			<div style="max-height:350px; overflow-y:scroll; width:3060px" id="scroll_body" >
				<table cellspacing="0" cellpadding="0" border="1" class="rpt_table"  width="3040" rules="all" id="table_body" >
				<?
				$sql_result=sql_select($sql); $i=1;$item_group_sammary=array();
				$total_precost_value =0; $total_deference=0; $total_deference_per =0;
				$r=0;
				foreach($sql_result as $row)
				{
					// echo $buyer_count."<br>";
					$job_no=$order_data_arr[$row[csf("po_id")]]["job_no_prefix_num"];
						$row_count[$row[csf("book_mst_id")]]+=1;
						$row_count2[$row[csf("book_mst_id")]][$job_no][$row[csf("po_id")]]+=1;
				}
			
				$po_id_arr=array();
				foreach($sql_result as $row)
				{
					// $supplier=$row[csf("supplier_id")];
					// $buyer_id=$row[csf("buyer_id")];
					// $buyer_count =count($row_count[$supplier][$buyer_id]);

					// echo $buyer_count."<br>";
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					if ($row[csf("type")]==1)
					{
						if ($row[csf("wo_type")]==2)
						{
							if ($row[csf("is_short")]==1)
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
						elseif($row[csf("wo_type")]==5)
						{
							$wo_type="Sample With Order";
							$wo_typw_id=3;
						}
					}
					else
					{
						$wo_type="Sample Without Order";
						$wo_typw_id=4;
					}
					$lead_time=datediff("d", $row[csf("wo_date")], $row[csf("delivery_date")]);
					//$job_number=implode(",",array_unique(explode(",",$row[csf("job_no")])));
					$rcv_qnty=$rcv_balance=$ontimeRcv=0;$rcv_mst_id=$po_id="";
	
					if($row[csf('type')]==1)
					{
						//echo $row[csf('book_mst_id')].'='.$row[csf("po_id")].'='.$row[csf("trim_group")].'='.$description_arr[$row[csf("dtls_id")]]["description"].'='.$description_arr[$row[csf("dtls_id")]]["brand_supplier"].'<br>';
						$rcv_qnty=$rcv_value=$ontimeRcv=0;$rcv_mst_id="";
						$desc_all_arr=explode("__",chop($description_arr[$row[csf("dtls_id")]]["description"],"__"));
						foreach($desc_all_arr as $descript)
						{
							$rcv_qnty+=$rcv_data_po[$row[csf('book_mst_id')]][$row[csf("po_id")]][$row[csf("trim_group")]][$descript]["rcv_qnty"];
							$rcv_value+=$rcv_data_po[$row[csf('book_mst_id')]][$row[csf("po_id")]][$row[csf("trim_group")]][$descript]["rcv_value"];
							
							$rcv_date_arr=array();
							$rcv_date_arr=array_unique(explode(",",chop($rcv_data_po[$row[csf('book_mst_id')]][$row[csf("po_id")]][$row[csf('trim_group')]][$descript]["receive_date"],",")));
							foreach($rcv_date_arr as $rcv_date)
							{
								if($rcv_date!="" && $rcv_date!="0000-00-00")
								{
									if(strtotime($rcv_date)<=strtotime($row[csf("delivery_date")]) && $rcv_data_po_ontime[$rcv_date][$row[csf('book_mst_id')]][$row[csf("po_id")]][$row[csf('trim_group')]][$descript]["rcv_qnty"]>0)
									{
										$ontimeRcv+=$rcv_data_po_ontime[$rcv_date][$row[csf('book_mst_id')]][$row[csf("po_id")]][$row[csf('trim_group')]][$descript]["rcv_qnty"];
										$rcv_mst_id.=chop($rcv_data_po_ontime[$rcv_date][$row[csf('book_mst_id')]][$row[csf("po_id")]][$row[csf('trim_group')]][$descript]["mst_id"],",").",";
									}
								}
							}
						}
					}
					else
					{
						$po_id=$row[csf("po_id")];
						$rcv_qnty=$rcv_value=$ontimeRcv=0;$rcv_mst_id="";
						$desc_all_arr=explode("__",chop($description_arr[$row[csf("dtls_id")]]["description"],"__"));
						foreach($desc_all_arr as $descript)
						{
							$rcv_qnty=$rcv_data_nonOrder[$row[csf('book_mst_id')]][$row[csf("trim_group")]][$descript]["rcv_qnty"];
							$rcv_value=$rcv_data_nonOrder[$row[csf('book_mst_id')]][$row[csf("trim_group")]][$descript]["rcv_value"];
							$rcv_date_arr=array();
							$rcv_date_arr=array_unique(explode(",",chop($rcv_data_nonOrder[$row[csf('book_mst_id')]][$row[csf('trim_group')]][$descript]["receive_date"],",")));
							foreach($rcv_date_arr as $rcv_date)
							{
								if($rcv_date!="" && $rcv_date!="0000-00-00")
								{
									if(strtotime($rcv_date)<=strtotime($row[csf("delivery_date")]) && $rcv_data_nonOrder_on_time[$rcv_date][$row[csf('book_mst_id')]][$row[csf('trim_group')]][$descript]["rcv_qnty"]>0)
									{
										$ontimeRcv+=$rcv_data_nonOrder_on_time[$rcv_date][$row[csf('book_mst_id')]][$row[csf('trim_group')]][$descript]["rcv_qnty"];
										$rcv_mst_id.=chop($rcv_data_nonOrder_on_time[$rcv_date][$row[csf('book_mst_id')]][$row[csf('trim_group')]][$descript]["mst_id"],",").",";
									}
								}
							}
						}
					}
					
					$rcv_balance=$row[csf("wo_qnty")]-$rcv_qnty;
	
					$rcv_mst_id=chop($rcv_mst_id,",");
					$otd=0;
					$otd=(($ontimeRcv/$row[csf("wo_qnty")])*100);
	
					if($row[csf("wo_qnty")]>0 && $row[csf("trim_group")]>0)
					{
						$item_group_sammary[$row[csf("trim_group")]]["wo_qnty"]+=$row[csf("wo_qnty")];
						$item_group_sammary[$row[csf("trim_group")]]["wo_value"]+=$row[csf("wo_qnty")]*$row[csf("wo_rate")];
						$item_group_sammary[$row[csf("trim_group")]]["pre_value"]+=$row[csf("wo_qnty")]*$pre_cost_data_arr[$row[csf("po_id")]][$row[csf("trim_group")]];//*$pre_cost_data_arr[$row[csf("po_id")]][$row[csf("trim_group")]]
						$item_group_sammary[$row[csf("trim_group")]]["ontimeRcv"]+=$ontimeRcv;
						$item_group_sammary[$row[csf("trim_group")]]["rcv_qnty"]+=$rcv_qnty;
						$item_group_sammary[$row[csf("trim_group")]]["trim_group"]=$row[csf("trim_group")];
					}
	
					if($row[csf("supplier_id")]>0 && $row[csf("wo_qnty")]>0)
					{ //supplierArr 
						if($row[csf("pay_mode")]==3 || $row[csf("pay_mode")]==5)
						{
						 $supplier_wise_sammary[$row[csf("supplier_id")]]["supp_comp"]=$company_library[$row[csf("supplier_id")]];
						}
						else
						{
						 $supplier_wise_sammary[$row[csf("supplier_id")]]["supp_comp"]=$supplierArr[$row[csf("supplier_id")]];
						}
						$supplier_wise_sammary[$row[csf("supplier_id")]]["wo_qnty"]+=$row[csf("wo_qnty")];
						$supplier_wise_sammary[$row[csf("supplier_id")]]["wo_value"]+=$row[csf("wo_qnty")]*$row[csf("wo_rate")];
						$supplier_wise_sammary[$row[csf("supplier_id")]]["pre_value"]+=$row[csf("wo_qnty")]*$pre_cost_data_arr[$row[csf("po_id")]][$row[csf("trim_group")]];
						$supplier_wise_sammary[$row[csf("supplier_id")]]["ontimeRcv"]+=$ontimeRcv;
						$supplier_wise_sammary[$row[csf("supplier_id")]]["rcv_qnty"]+=$rcv_qnty;
						$supplier_wise_sammary[$row[csf("supplier_id")]]["supplier_id"]=$row[csf("supplier_id")];
					}
					if($row[csf("is_approved")]==1)
					{
						$approved_date=$row[csf("update_date")];
						$approved_date = date('Y-m-d', strtotime($approved_date));
					}
					else $approved_date='';
					
					if($row[csf("pay_mode")]==3 || $row[csf("pay_mode")]==5) $supplier_com=$company_library[$row[csf("supplier_id")]]; else $supplier_com=$supplierArr[$row[csf("supplier_id")]];
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
						<td width="40" align="center"><? echo $i;?></td>
						<td width="110"><p><? echo $row[csf("booking_no")]; ?></td>
						<td width="70"><p><? echo $approved_date; ?></td>
						<td width="110"><p><? echo $order_data_arr[$row[csf("po_id")]]["grouping"]; ?></td>
						<td width="70" align="center"><p><? echo change_date_format($row[csf("wo_date")]); ?>&nbsp;</p></td>
						<td width="70" align="center"><p><?
								$date_del="";
								if($trim_type[$row[csf('trim_group')]]==1)
								{
									$date_del=$date_arr[$row[csf('po_id')]][70];
								}
								else{
									$date_del=$date_arr[$row[csf('po_id')]][71];
								}
								if(empty($date_del))
								{
									$date_del=$row[csf("delivery_date")];
								}
						 		echo change_date_format($row[csf("delivery_date")]); 
						 ?>&nbsp;</p></td>
						<td width="70" align="center"><p><? if($lead_time>0 && $lead_time<2) echo $lead_time." Day"; elseif($lead_time>1) echo $lead_time." Days"; else echo "0 Day"; ?>&nbsp;</p></td>
						<td width="90"><p><?  echo $wo_type; ?>&nbsp;</p></td>
						<td width="100"><p><? echo $supplier_com; ?>&nbsp;</p></td>
						<?if($booking_mst_id!=$row[csf("book_mst_id")]){?>
						<td width="100" rowspan="<?=$row_count[$row[csf("book_mst_id")]]?>"><p><? echo $buyerArr[$row[csf("buyer_id")]]; ?>&nbsp;</p></td>
						<td width="50" align="center"rowspan="<?=$row_count[$row[csf("book_mst_id")]]?>"><p><? echo $order_data_arr[$row[csf("po_id")]]["job_year"]; ?>&nbsp;</p></td>
						<td width="50" align="center"rowspan="<?=$row_count[$row[csf("book_mst_id")]]?>"><p><? echo $order_data_arr[$row[csf("po_id")]]["job_no_prefix_num"]; ?>&nbsp;</p></td>
						<td width="100"rowspan="<?=$row_count[$row[csf("book_mst_id")]]?>"><p><? echo $order_data_arr[$row[csf("po_id")]]["style_ref_no"]; ?>&nbsp;</p></td>
					
						<?	
					
						}
						if($po_id_arr[$row[csf("book_mst_id")]][$job_no][$row[csf("po_id")]]!=$row[csf("po_id")]){
							$job_no=$order_data_arr[$row[csf("po_id")]]["job_no_prefix_num"];	
						?>
						<td width="100" rowspan="<?=$row_count2[$row[csf("book_mst_id")]][$job_no][$row[csf("po_id")]]?>" style="word-break:break-all"><p><? echo $order_data_arr[$row[csf("po_id")]]["po_number"]; ?>&nbsp;</p></td>
						<td width="100" rowspan="<?=$row_count2[$row[csf("book_mst_id")]][$job_no][$row[csf("po_id")]]?>"  style="word-break:break-all"><p><? echo $order_data_arr[$row[csf("po_id")]]["order_qty"]; 
							$order_qty_total +=$order_data_arr[$row[csf("po_id")]]["order_qty"];
						?>&nbsp;</p></td>
						<?}?>
						<td width="120"><p><? echo $trimsGroupArr[$row[csf("trim_group")]]; ?>&nbsp;</p></td>
						<td width="150" style="word-break:break-all"><p>
						<?
						$desc_trim=chop($description_arr[$row[csf("dtls_id")]]["description"],'__');
						$desc_trims=implode(",",array_unique(explode(",",$desc_trim)));
						echo $desc_trims; 
						?>&nbsp;</p></td>
						<td width="100"><p><? echo $item_category[$row[csf("item_category")]]; ?>&nbsp;</p></td>
						<td width="60"><p><? echo $unit_of_measurement[$row[csf("wo_uom")]]; ?>&nbsp;</p></td>
						<td width="80" align="right"><p><? echo number_format($row[csf("wo_qnty")],2,'.',''); ?></p></td>
						<td width="70" align="right"><p><? echo number_format($row[csf("wo_rate")],2,'.',''); ?>&nbsp;</p></td>
						<td width="80" align="right"><p><? $wo_value=$row[csf("wo_qnty")]*$row[csf("wo_rate")]; echo number_format($wo_value,4,'.',''); ?></p></td>
						<td width="70" align="right"><p><? echo number_format($pre_cost_data_arr[$row[csf("po_id")]][$row[csf("trim_group")]],2,'.',''); ?></p></td>
						<td width="80" align="right"><p><? $precost_value=$row[csf("wo_qnty")]*$pre_cost_data_arr[$row[csf("po_id")]][$row[csf("trim_group")]]; echo number_format($precost_value,2); ?></p></td>
						<td width="80" align="right"><p><? $deference = $precost_value-$wo_value; echo number_format($deference,2,'.',''); ?></p></td>
						<td width="80" align="right"><p><? $deference_per = ($deference/$precost_value)*100; echo number_format($deference_per,2,'.',''); ?></p></td>
						<td width="80" align="right"><p><a href='#report_details' onClick="openmypage_inhouse('<? echo $row[csf("book_mst_id")]; ?>','<? echo $row[csf("po_id")]; ?>','<? echo $row[csf("trim_group")]; ?>','<? echo chop($description_arr[$row[csf("dtls_id")]]["description"],"__"); ?>','<? echo $rcv_mst_id; ?>','1','<? echo $row[csf("delivery_date")]; ?>','','booking_inhouse_info');"><? echo number_format($ontimeRcv,2,'.','');?></a> </p></td>
						<td width="80" align="right"><p><? echo number_format($otd,2);?></p></td>
						<td width="80" align="right"><p><a href='#report_details' onClick="openmypage_inhouse('<? echo $row[csf("book_mst_id")]; ?>','<? echo $row[csf("po_id")]; ?>','<? echo $row[csf("trim_group")]; ?>','<? echo chop($description_arr[$row[csf("dtls_id")]]["description"],"__"); ?>','<? echo $rcv_mst_id; ?>','2','<? echo $row[csf('type')] ?>','','booking_inhouse_info');"><? echo number_format($rcv_qnty,2,'.','');?></a> </p></td>
						<td width="80" align="right"><p><? echo number_format($rcv_value,2,'.',''); ?></p></td>
						<td width="80" align="right"><p><? echo number_format($rcv_balance,2,'.',''); ?></p></td>
						<td width="120"><p><? echo $lib_team_member_arr[$order_data_arr[$row[csf("po_id")]]['dealing_marchant']]; ?></p></td>
						<td width="120"><p><? echo $lib_team_leader_name_arr[$order_data_arr[$row[csf("po_id")]]['team_leader']] ; ?></p></td>
						<td width="120" align="right"><p><? echo $userArr[$row[csf("inserted_by")]]; ?></p></td>
						<td ><p><? echo $row[csf("remarks")]; ?> &nbsp;</p></td>
					</tr>
					<?
					$tot_wo_qnty+=$row[csf("wo_qnty")];
					$tot_wo_value+=$wo_value;
					$tot_receive_qnty+=$rcv_qnty;
					$tot_receive_value+=$rcv_value;
					$tot_rcv_balance+=$rcv_balance;
					$total_ontime_rcv+=$ontimeRcv;
					$total_precost_value +=$precost_value;
					$total_deference += $deference;
					//$total_deference_per +=$deference_per;
	
					$i++;
					$booking_mst_id=$row[csf("book_mst_id")];
					$job_no=$order_data_arr[$row[csf("po_id")]]["job_no_prefix_num"];	
					$po_id_arr[$row[csf("book_mst_id")]][$job_no][$row[csf("po_id")]]=$row[csf("po_id")];
						
				}
				?>
					
					<tfoot>
						<th >&nbsp;</th>
						<th >&nbsp;</th>
						<th >&nbsp;</th>
						<th >&nbsp;</th>
						<th >&nbsp;</th>
						<th >&nbsp;</th>
						<th >&nbsp;</th>
						<th >&nbsp;</th>
						<th >&nbsp;</th>
						<th >&nbsp;</th>
						<th >&nbsp;</th>
						<th >&nbsp;</th>
						<th >&nbsp;</th>
						<th >&nbsp;</th>
						<th >&nbsp;</th>				
						<th >&nbsp;</th>
						<th >&nbsp;</th>
						
						<th colspan="2">Per Psc Avg Rate :</th>
						<th  align="right" title="Total WO Value/Total Order Quantity"><?=number_format($tot_wo_value/$order_qty_total,2) ;?></th>
						<th >&nbsp;</th>
						<th >&nbsp;</th>
						<th >&nbsp;</th>
						<th >&nbsp;</th>
						<th >&nbsp;</th>
						<th >&nbsp;</th>
						<th >&nbsp;</th>
						<th >&nbsp;</th>
						<th >&nbsp;</th>
						<th >&nbsp;</th>
						<th >&nbsp;</th>
						<th >&nbsp;</th>
						<th >&nbsp;</th>
						<th >&nbsp;</th>
						<th >&nbsp;</th>
					</tfoot>
					
					<tfoot>
						<th >&nbsp;</th>
						<th >&nbsp;</th>
						<th >&nbsp;</th>
						<th >&nbsp;</th>
						<th >&nbsp;</th>
						<th >&nbsp;</th>
						<th >&nbsp;</th>
						<th >&nbsp;</th>
						<th >&nbsp;</th>
						<th >&nbsp;</th>
						<th >&nbsp;</th>
						<th >&nbsp;</th>
						<th >&nbsp;</th>
						<th >Order Total Qty</th>
						<th ><?=$order_qty_total;?></th>						
						<th >&nbsp;</th>
						<th >&nbsp;</th>
						<th >&nbsp;</th>
						<th >Total:</th>
						<th id="value_tot_wo_qnty" align="right"><? echo number_format($tot_wo_qnty,2) ;?></th>
						<th >&nbsp;</th>
						<th id="value_tot_wo_value" align="right"><? echo number_format($tot_wo_value,4) ;?></th>
						<th >&nbsp;</th>
						<th id="value_tot_precost_value" align="right"><? echo number_format($total_precost_value,2) ;?></th>
						<th id="value_tot_deference" align="right"><? echo number_format($total_deference,2) ;?></th>
						<th id="value_tot_deference_per" align="right"><? echo number_format(($total_deference/$total_precost_value)*100,2) ;?></th>
						<th id="value_tot_ontime_receive_qnty" align="right"><? echo number_format($total_ontime_rcv,2) ;?></th>
						<th >&nbsp;</th>
						<th id="value_tot_receive_qnty" align="right"><? echo number_format($tot_receive_qnty,2) ;?></th>
						<th id="value_tot_receive_value" align="right"><? echo number_format($tot_receive_value,2) ;?></th>
						<th id="value_tot_rcv_balance" align="right"><? echo number_format($tot_rcv_balance,2) ;?></th>
						<th >&nbsp;</th>
						<th >&nbsp;</th>
						<th >&nbsp;</th>
						<th >&nbsp;</th>
					</tfoot>
				</table>
			
			</div>
			<br>
			<table cellspacing="0" cellpadding="0" border="0" width="1750" rules="all">
				<tr>
					<td valign="top">
	
						<table cellspacing="0" cellpadding="0" border="1" class="rpt_table"  width="1050" rules="all">
	
							<thead>
								<tr bgcolor="#CCCCCC">
									<td colspan="11" align="center" style="font-size:16px; font-weight:bold;">Item Group Wise Summary</td>
								</tr>
								<tr>
									<th width="50">SL</th>
									<th width="150">Item Name</th>
									<th width="100">WO Qty</th>
									
									<th width="100">WO Value</th>
									<th width="100">Precost value</th>
									<th width="100">Difference</th>
									<th width="50">%</th>
									
									<th width="100">On Time Rcv Qty</th>
									<th width="100">Total Rcv Qty</th>
									<th width="100">Rcv Balance</th>
									<th>OTD %</th>
								</tr>
							</thead>
							<tbody>
								<?
								$k=1;
								//print_r($item_group_sammary);die;
								foreach($item_group_sammary as $item_grp_id=>$val)
								{
									$otd=(($val["ontimeRcv"]/$val["wo_qnty"])*100);
									$item_group_sammary[$item_grp_id]["otd"]=$otd;
								}
	
								foreach($item_group_sammary as $item_group=>$val)
								{
									$mid[$item_group]  = $val["otd"];
								}
								array_multisort($mid, SORT_DESC, $item_group_sammary);
	
								foreach($item_group_sammary as $val)
								{
									if ($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
									$rcv_bal=$val["wo_qnty"]-$val["rcv_qnty"];
								   //$otd=(($val["ontimeRcv"]/$val["wo_qnty"])*100);
									?>
									<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
										<td><? echo $k; ?></td>
										<td>
										<?
										//echo $trimsGroupArr[$item_group];
										echo $trimsGroupArr[$val["trim_group"]];
										?></td>
										<td align="right"><? echo number_format($val["wo_qnty"],2); ?></td>
										<td align="right"><? echo number_format($val["wo_value"],4); ?></td>
										<td align="right"><? echo number_format($val["pre_value"],2); ?></td>
										<td align="right"><? $different=$val["pre_value"]-$val["wo_value"];echo number_format($different,2); ?></td>
										<td align="right" title="Difference/Pre Cost*100"><? echo number_format((($different/$val["pre_value"])*100),2); ?></td>
												
										<td align="right"><? echo number_format($val["ontimeRcv"],2); ?></td>
										<td align="right"><? echo number_format($val["rcv_qnty"],2); ?></td>
										<td align="right"><? echo number_format($rcv_bal,2); ?></td>
										<td align="right"><? echo number_format($val["otd"],2); ?></td>
									</tr>
									<?
									$i++;$k++;
									$sum_tot_wo_qnty+=$val["wo_qnty"];
									$sum_tot_wo_value+=$val["wo_value"];
									$sum_tot_pre_cost+=$val["pre_value"];
									$sum_tot_different+=$val["pre_value"]-$val["wo_value"];
									$sum_tot_ontime_rcv+=$val["ontimeRcv"];
									$sum_tot_rcv_qnty+=$val["rcv_qnty"];
									$sum_tot_rcv_bal+=$rcv_bal;//$val["rcv_bal"];
								}
								$tot_otd=(($sum_tot_ontime_rcv/$sum_tot_wo_qnty)*100);
								?>
							</tbody>
							<tfoot>
								<tr>
									<th>&nbsp;</th>
									<th>Total:</th>
									<th align="right"><? echo number_format($sum_tot_wo_qnty,2); ?></th>
									<th align="right"><? echo number_format($sum_tot_wo_value,4); ?></th>
									<th align="right"><? echo number_format($sum_tot_pre_cost,2); ?></th>
									<th align="right"><? echo number_format($sum_tot_differentt,2); ?></th>
									<th align="right"><? echo number_format((($sum_tot_different/$sum_tot_pre_cost)*100),2); ?></th>
									<th align="right"><? echo number_format($sum_tot_ontime_rcv,2); ?></th>
									<th align="right"><? echo number_format($sum_tot_rcv_qnty,2); ?></th>
									<th align="right"><? echo number_format($sum_tot_rcv_bal,2); ?></th>
									<th align="right"><? echo number_format($tot_otd,2); ?></th>
								</tr>
							</tfoot>
						</table>
	
					</td>
					<td valign="top">&nbsp;</td>
					<td valign="top">
						<table cellspacing="0" cellpadding="0" border="1" class="rpt_table"  width="1050" rules="all">
	
							<thead>
								<tr bgcolor="#CCCCCC">
									<td colspan="11" align="center" style="font-size:16px; font-weight:bold;">Supplier Wise Summary</td>
								</tr>
								<tr>
									<th width="50">SL</th>
									<th width="150">Supplier Name</th>
									<th width="100">WO Qty</th>
									<th width="100">WO Value</th>
									<th width="100">Precost value</th>
									<th width="100">Difference</th>
									<th width="50">%</th>
									<th width="100">On Time Rcv Qty</th>
									<th width="100">Total Rcv Qty</th>
									<th width="100">Rcv Balance</th>
									<th>OTD %</th>
								</tr>
							</thead>
							<tbody>
								<?
								$m=1;
								foreach($supplier_wise_sammary as $supplier_id=>$val)
								{
									$otd=(($val["ontimeRcv"]/$val["wo_qnty"])*100);
									$supplier_wise_sammary[$supplier_id]["otd"]=$otd;
								}
	
								foreach($supplier_wise_sammary as $supplier_id=>$val)
								{
									$sid[$supplier_id]  = $val["otd"];
								}
								array_multisort($sid, SORT_DESC, $supplier_wise_sammary);
								foreach($supplier_wise_sammary as $val)
								{
									if ($m%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
									$rcv_bal=$val["wo_qnty"]-$val["rcv_qnty"];
									$otd=(($val["ontimeRcv"]/$val["wo_qnty"])*100);//$supplierArr[
									?>
									<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
										<td><? echo $m; ?></td>
										<td><? echo $val["supp_comp"]; ?></td>
										<td align="right"><? echo number_format($val["wo_qnty"],2); ?></td>
										 <td align="right"><? echo number_format($val["wo_value"],4); ?></td>
										 <td align="right"><? echo number_format($val["pre_value"],2); ?></td>
										  <td align="right"><? $different=$val["pre_value"]-$val["wo_value"];echo number_format($different,2); ?></td>
										   <td align="right"><? echo number_format((($different/$val["pre_value"])*100),2); ?></td>
										   
										  
										<td align="right"><? echo number_format($val["ontimeRcv"],2); ?></td>
										<td align="right"><? echo number_format($val["rcv_qnty"],2); ?></td>
										<td align="right"><? echo number_format($rcv_bal,2); ?></td>
										<td align="right"><? echo number_format($val["otd"],2); ?></td>
									</tr>
									<?
									$i++;$m++;
									$sup_tot_wo_qnty+=$val["wo_qnty"];
									 $sup_tot_wo_value+=$val["wo_value"];
									  $sup_tot_pre_value+=$val["pre_value"];
									   $sup_tot_different+=$different;
									   // $sup_tot_wo_qnty+=$val["wo_qnty"];
										
									$sup_tot_ontime_rcv+=$val["ontimeRcv"];
									$sup_tot_rcv_qnty+=$val["rcv_qnty"];
									$sup_tot_rcv_bal+=$rcv_bal;//$val["rcv_bal"];
								}
								$sup_tot_otd=(($sup_tot_ontime_rcv/$sup_tot_wo_qnty)*100);
								?>
							</tbody>
							<tfoot>
								<tr>
									<th>&nbsp;</th>
									<th>Total:</th>
									<th align="right"><? echo number_format($sup_tot_wo_qnty,2); ?></th>
									<th align="right"><? echo number_format($sup_tot_wo_value,4); ?></th>
									 <th align="right"><? echo number_format($sup_tot_pre_value,2); ?></th>
									  <th align="right"><? echo number_format($sup_tot_different,2); ?></th>
									  <th align="right"><? echo number_format((($sup_tot_different/$sup_tot_pre_value)*100),2); ?></th>
									
									<th align="right"><? echo number_format($sup_tot_ontime_rcv,2); ?></th>
									<th align="right"><? echo number_format($sup_tot_rcv_qnty,2); ?></th>
									<th align="right"><? echo number_format($sup_tot_rcv_bal,2); ?></th>
									<th align="right"><? echo number_format($sup_tot_otd,2); ?></th>
								</tr>
							</tfoot>
						</table>
					</td>
				</tr>
			</table>
		</fieldset>
		<?
	}
	else if($cbo_category==25) //Emblishment
	{
		$color_namerArr = return_library_array("select id,color_name from lib_color ","id","color_name");
		$sql_result=sql_select($sql);
			$all_po_id="";
			foreach($sql_result as $row)
			{
				if($all_po_id=="") $all_po_id=$row[csf("po_id")];else $all_po_id.=",".$row[csf("po_id")];
				//$emblish_arr[$row[csf("po_id")]]["po_id"]=$row[csf("po_id")];
			}
			//echo $all_po_id.'ds';
			$poIds=chop($all_po_id,','); $po_cond_for_in=""; //$order_cond1=""; $order_cond2=""; $precost_po_cond="";
			$po_ids=count(array_unique(explode(",",$all_po_id)));
			if($db_type==2 && $po_ids>1000)
			{
			$po_cond_for_in=" and (";
			$po_cond_for_in2=" and (";
			$poIdsArr=array_chunk(explode(",",$poIds),999);
			foreach($poIdsArr as $ids)
			{
			$ids=implode(",",$ids);
			//$poIds_cond.=" po_break_down_id in($ids) or ";
			$po_cond_for_in.=" b.id in($ids) or"; 
			$po_cond_for_in2.=" d.po_break_down_id in($ids) or"; 
			}
			$po_cond_for_in=chop($po_cond_for_in,'or ');
			$po_cond_for_in.=")";
			}
			else
			{
			$po_ids=implode(",",(array_unique(explode(",",$all_po_id))));
			$po_cond_for_in=" and b.id in($po_ids)";
		//	$po_cond_for_in2=" and d.po_break_down_id  in($po_ids)";
			}
			if(!empty($all_po_id))
			{
				$tna_po=$po_cond_for_in;
			}
			

			$sql_date="SELECT a.task_finish_date,a.po_number_id,a.task_number from tna_process_mst a, wo_po_break_down b where a.po_number_id = b.id and a.status_active=1 and b.is_deleted=0 and A.TASK_NUMBER in (70,71) $tna_po  group by a.task_finish_date,a.po_number_id,a.task_number,a.task_category";
			//echo $sql_date;

			$date_arr=array();

			$sql_result=sql_select($sql_date);
			foreach ($sql_result as $row) 
			{
				$date_arr[$row[csf('po_number_id')]][$row[csf('task_number')]]=$row[csf('task_finish_date')];
			}
			$order_sql=sql_select("select a.id as job_id, a.job_no, a.job_no_prefix_num, $select_year, a.style_ref_no, b.id as po_id, b.po_number,a.dealing_marchant,a.team_leader,b.grouping  from  wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and b.status_active=1 $po_cond_for_in");
			//grouping field
			$order_data_arr=array();
			//echo $order_sql[csf("po_id")];die;
			$i = 0;
			foreach($order_sql as $row)
			{
			$order_data_arr[$row[csf("po_id")]]["po_id"]=$row[csf("po_id")];
			$order_data_arr[$row[csf("po_id")]]["job_id"]=$row[csf("job_id")];
			$order_data_arr[$row[csf("po_id")]]["job_no"]=$row[csf("job_no")];
			$order_data_arr[$row[csf("po_id")]]["job_no_prefix_num"]=$row[csf("job_no_prefix_num")];
			$order_data_arr[$row[csf("po_id")]]["job_year"]=$row[csf("job_year")];
			$order_data_arr[$row[csf("po_id")]]["style_ref_no"]=$row[csf("style_ref_no")];
			$order_data_arr[$row[csf("po_id")]]["po_number"]=$row[csf("po_number")];
			$order_data_arr[$row[csf("po_id")]]["order_qty"]=$row[csf("po_quantity")];
			$order_data_arr[$row[csf("po_id")]]["grouping"]=$row[csf("grouping")];
			$order_data_arr[$row[csf('po_id')]]['team_leader']=$row[csf('team_leader')];
			$order_data_arr[$row[csf('po_id')]]['dealing_marchant']=$row[csf('dealing_marchant')];
			//echo $order_data_arr[$row[csf("po_id")]]["po_number"];
			}
	
			$condition= new condition();
			$condition->company_name("=$cbo_company");
			if(str_replace("'","",$cbo_buyer)>0){
			$condition->buyer_name("=$cbo_buyer");
			}
			
			if($all_po_id!='')
			{
			$po_ids=implode(",",(array_unique(explode(",",$all_po_id))));
			$condition->po_id_in("$po_ids"); 
			}
			
			$condition->init();
			$emblishment= new emblishment($condition);
			$wash= new wash($condition);
			//echo $wash->getQuery();die;
			//echo $emblishment->getQuery(); die;
			$emblishment_qty_arr=$emblishment->getQtyArray_by_orderEmbnameAndEmbtype();
			$emblishment_amt_arr=$emblishment->getAmountArray_by_orderEmbnameAndEmbtype();
			$emblishment_wash_qty_arr=$wash->getQtyArray_by_orderEmbnameAndEmbtype();
			$emblishment_wash_amt_arr=$wash->getAmountArray_by_orderEmbnameAndEmbtype();
			//print_r($emblishment_wash_qty_arr);	
			$emb_sql=sql_select( "select c.id,c.emb_type,c.emb_name from  wo_po_break_down b,wo_pre_cost_embe_cost_dtls c where b.job_id=c.job_id and c.status_active=1 $po_cond_for_in");
			$pre_embl_arr=array();
			foreach($emb_sql as $row)
			{	
				$pre_embl_arr[$row[csf("id")]]["emb_type"]=$row[csf("emb_type")];
				$pre_embl_arr[$row[csf("id")]]["emb_name"]=$row[csf("emb_name")];
			}
			unset($emb_sql);
			
	?>
    <fieldset>
        <table width="2880"  cellspacing="0" >
            <tr class="form_caption" style="border:none;">
                <td align="center" style="border:none; font-size:18px;" colspan="22">
                    <? echo $company_library[str_replace("'","",$cbo_company_id)]; ?>
                </td>
            </tr>
            <tr class="form_caption" style="border:none;">
                <td align="center" style="border:none;font-size:16px; font-weight:bold"  colspan="25"> <? echo $report_title ;?></td>
            </tr>
            <tr class="form_caption" style="border:none;">
                <td align="center" style="border:none;font-size:12px; font-weight:bold"  colspan="25"> <? echo "From ".change_date_format($date_from)." To ".change_date_format($date_to);?></td>
            </tr>
        </table>
        <table width="2540" cellspacing="0" border="1" class="rpt_table" rules="all">
            <thead>
                <th width="40">SL</th>
                <th width="110">WO No</th>
                <th width="70">Approved Date</th>
                <th width="110">Internal Ref No</th>
                <th width="70">WO Date</th>
                <th width="70">Delivery Date</th>
                <th width="70">Lead Time</th>
                <th width="90">WO Type</th>
                <th width="100">Supplier</th>
                <th width="100">Buyer</th>
                <th width="50">Job Year</th>
                <th width="50">Job No.</th>
                <th width="100">Style Ref.</th>
                <th width="100">Order No</th>
				<th width="100">Order qty</th>
                <th width="120">Emblishment Name</th>
                <th width="150">Emblishment Type</th>
                <th width="100">Item Category</th>
                <th width="60">Color</th>
                <th width="80">WO Qty</th>
                <th width="70">WO Unit price</th>
                <th width="80">WO value</th>
                <th width="70">Budget Unit price</th>
                <th width="80">Precost value</th>
                <th width="80" title="(Precost value - WO value)">Defference</th>
                <th width="80" title="(Defference / Precost value)*100">Defference %</th>
                
                <th width="120">Dealing Merchant</th>
                <th width="120">Team Leader</th>
                <th width="120">User Name</th>
                <th >Remarks</th>
            </thead>
        </table>
        <div style="max-height:350px; overflow-y:scroll; width:2660px" id="scroll_body" >
            <table cellspacing="0" cellpadding="0" border="1" class="rpt_table"  width="2640" rules="all" id="table_body" >
            <?
			
			$i=1;$item_group_sammary=array();
			$total_precost_value =0; $total_deference=0; $total_deference_per =0;
			foreach($sql_result as $row)
				{
					// echo $buyer_count."<br>";
					$job_no=$order_data_arr[$row[csf("po_id")]]["job_no_prefix_num"];
						$row_count[$row[csf("book_mst_id")]]+=1;
						$row_count2[$row[csf("book_mst_id")]][$job_no][$row[csf("po_id")]]+=1;
				}
			
				$po_id_arr=array();

			foreach($sql_result as $row)
			{
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				
				if ($row[csf("wo_type")]==6)
					{
						if ($row[csf("is_short")]==1)
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
					
				$lead_time=datediff("d", $row[csf("wo_date")], $row[csf("delivery_date")]);
				$emb_type_id=$pre_embl_arr[$row[csf("pre_cost_fabric_cost_dtls_id")]]["emb_type"];
				$emb_name=$pre_embl_arr[$row[csf("id")]]["emb_name"]=$pre_embl_arr[$row[csf("pre_cost_fabric_cost_dtls_id")]]["emb_name"];
				if($emb_name==1)//Print
				{
				$emb_type=$emblishment_print_type[$pre_embl_arr[$row[csf("pre_cost_fabric_cost_dtls_id")]]["emb_type"]];
				}
				elseif($emb_name==2)//EMBRO
				{
				$emb_type=$emblishment_embroy_type[$pre_embl_arr[$row[csf("pre_cost_fabric_cost_dtls_id")]]["emb_type"]];
				}
				elseif($emb_name==4)//Spcial
				{
				$emb_type=$emblishment_spwork_type[$pre_embl_arr[$row[csf("pre_cost_fabric_cost_dtls_id")]]["emb_type"]];
				}
				elseif($emb_name==5)//Gmt
				{
				$emb_type=$emblishment_gmts_type[$pre_embl_arr[$row[csf("pre_cost_fabric_cost_dtls_id")]]["emb_type"]];
				}
				elseif($emb_name==3)//Wash
				{
				$emb_type=$emblishment_wash_type[$pre_embl_arr[$row[csf("pre_cost_fabric_cost_dtls_id")]]["emb_type"]];
				}
				
				if($emb_name!=3)//Without Wash
				{
				 $precost_qty=$emblishment_qty_arr[$row[csf("po_id")]][$emb_name][$emb_type_id];
				 $precost_value=$emblishment_amt_arr[$row[csf("po_id")]][$emb_name][$emb_type_id];
				}
				else 
				{
				 $precost_qty=$emblishment_wash_qty_arr[$row[csf("po_id")]][$emb_name][$emb_type_id];
				 $precost_value=$emblishment_wash_amt_arr[$row[csf("po_id")]][$emb_name][$emb_type_id];
				}
				$currency_id=$row[csf("currency_id")];
				if($currency_id==1) //TK exchange_rate
				{
					$wo_rate=$row[csf("rate")]/$row[csf("exchange_rate")];
				}
				else
				{
					$wo_rate=$row[csf("rate")];
				}
				
				
				if($row[csf("wo_qnty")]>0 && $emb_name>0)
				{
					$item_group_sammary[$emb_name]["wo_qnty"]+=$row[csf("wo_qnty")];
					$item_group_sammary[$emb_name]["wo_value"]+=$row[csf("wo_qnty")]*$wo_rate;
					$item_group_sammary[$emb_name]["pre_value"]+=$precost_value;
					//echo $precost_value.',';
					
					$item_group_sammary[$emb_name]["ontimeRcv"]+=$ontimeRcv;
					$item_group_sammary[$emb_name]["rcv_qnty"]+=$rcv_qnty;
					$item_group_sammary[$emb_name]["emblishment_name"]=$emb_name;
				}

				if($row[csf("supplier_id")]>0 && $row[csf("wo_qnty")]>0)
				{
					$supplier_wise_sammary[$row[csf("supplier_id")]]["wo_qnty"]+=$row[csf("wo_qnty")];
					$supplier_wise_sammary[$row[csf("supplier_id")]]["wo_value"]+=$row[csf("wo_qnty")]*$wo_rate;
					$supplier_wise_sammary[$row[csf("supplier_id")]]["pre_value"]+=$precost_value;
					
					$supplier_wise_sammary[$row[csf("supplier_id")]]["ontimeRcv"]+=$ontimeRcv;
					$supplier_wise_sammary[$row[csf("supplier_id")]]["rcv_qnty"]+=$rcv_qnty;
					$supplier_wise_sammary[$row[csf("supplier_id")]]["supplier_id"]=$row[csf("supplier_id")];
				}
				if($row[csf("is_approved")]==1)
				{
					$approved_date=$row[csf("update_date")];
					$approved_date = date('Y-m-d', strtotime($approved_date));
				}
				else $approved_date='';
				
				if($row[csf("pay_mode")]==3 || $row[csf("pay_mode")]==5) $supplier_com=$company_library[$row[csf("supplier_id")]]; else $supplier_com=$supplierArr[$row[csf("supplier_id")]];
				
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
                	<td width="40" align="center"><? echo $i;?></td>
                    <td width="110"><p><? echo $row[csf("booking_no")]; ?></td>
                    <td width="70"><p><? echo $approved_date; ?></td>
                    <td width="110"><p><? echo $order_data_arr[$row[csf("po_id")]]["grouping"]; ?></td>
                    <td width="70" align="center"><p><? echo change_date_format($row[csf("wo_date")]); ?>&nbsp;</p></td>
                    <td width="70" align="center"><p><?
                    	$date_del="";
						if($trim_type[$row[csf('trim_group')]]==1)
						{
							$date_del=$date_arr[$row[csf('po_id')]][70];
						}
						else{
							$date_del=$date_arr[$row[csf('po_id')]][71];
						}
						if(empty($date_del))
						{
							$date_del=$row[csf("delivery_date")];
						}
				 		echo change_date_format($row[csf("delivery_date")]); 
                     
                      ?>&nbsp;</p></td>
                    <td width="70" align="center"><p><? if($lead_time>0 && $lead_time<2) echo $lead_time." Day"; elseif($lead_time>1) echo $lead_time." Days"; else echo "0 Day"; ?>&nbsp;</p></td>
                    <td width="90"><p><?  echo $wo_type; ?>&nbsp;</p></td>
                    <td width="100"><p><? echo $supplier_com; ?>&nbsp;</p></td>

					<?if($booking_mst_id!=$row[csf("book_mst_id")]){?>
						<td width="100" rowspan="<?=$row_count[$row[csf("book_mst_id")]]?>"><p><? echo $buyerArr[$row[csf("buyer_id")]]; ?>&nbsp;</p></td>
						<td width="50" align="center"rowspan="<?=$row_count[$row[csf("book_mst_id")]]?>"><p><? echo $order_data_arr[$row[csf("po_id")]]["job_year"]; ?>&nbsp;</p></td>
						<td width="50" align="center"rowspan="<?=$row_count[$row[csf("book_mst_id")]]?>"><p><? echo $order_data_arr[$row[csf("po_id")]]["job_no_prefix_num"]; ?>&nbsp;</p></td>
						<td width="100"rowspan="<?=$row_count[$row[csf("book_mst_id")]]?>"><p><? echo $order_data_arr[$row[csf("po_id")]]["style_ref_no"]; ?>&nbsp;</p></td>
					
						<?	
					
						}
						if($po_id_arr[$row[csf("book_mst_id")]][$job_no][$row[csf("po_id")]]!=$row[csf("po_id")]){
							$job_no=$order_data_arr[$row[csf("po_id")]]["job_no_prefix_num"];	
						?>
						<td width="100" rowspan="<?=$row_count2[$row[csf("book_mst_id")]][$job_no][$row[csf("po_id")]]?>" style="word-break:break-all"><p><? echo $order_data_arr[$row[csf("po_id")]]["po_number"]; ?>&nbsp;</p></td>
						<td width="100" rowspan="<?=$row_count2[$row[csf("book_mst_id")]][$job_no][$row[csf("po_id")]]?>"  style="word-break:break-all"><p><? echo $order_data_arr[$row[csf("po_id")]]["order_qty"]; 
							$order_qty_total +=$order_data_arr[$row[csf("po_id")]]["order_qty"];
						?>&nbsp;</p></td>
						<?}?>
      
                    <td width="120"><p><? echo $emblishment_name_array[$emb_name]; ?>&nbsp;</p></td>
                    <td width="150" style="word-break:break-all"><p><?
					
					echo $emb_type; ?>&nbsp;</p></td>
                    <td width="100"><p><? echo $item_category[$row[csf("item_category")]]; ?>&nbsp;</p></td>
                    <td width="60"><p><? echo $color_namerArr[$row[csf("gmts_color_id")]]; ?>&nbsp;</p></td>
                    <td width="80" align="right"><p><? echo number_format($row[csf("wo_qnty")],2,'.',''); ?></p></td>
                    <td width="70" align="right" title="USD Rate"><p><? echo number_format($wo_rate,2,'.',''); ?>&nbsp;</p></td>
                    <td width="80" align="right" ><p><? $wo_value=$row[csf("wo_qnty")]*$wo_rate; echo number_format($wo_value,4,'.',''); ?></p></td>
                    <td width="70" align="right" title="Pre Embl Qty=(<? echo $precost_qty;?>) "><p><? echo number_format($precost_value/$precost_qty,2,'.',''); ?></p></td>
                    <td width="80" align="right"><p><? //$precost_value=$row[csf("wo_qnty")]*$pre_cost_data_arr[$row[csf("po_id")]][$row[csf("trim_group")]];
					 $pre_cost_value=$precost_value;//$row[csf("wo_qnty")]*($precost_value/$precost_qty);
					 echo number_format($pre_cost_value,2); ?></p></td>
                    <td width="80" align="right"><p><? $deference = $pre_cost_value-$wo_value; echo number_format($deference,2,'.',''); ?></p></td>
                    <td width="80" align="right"><p><? $deference_per = ($deference/$pre_cost_value)*100; echo number_format($deference_per,2,'.',''); ?></p></td>
                   
                    <td width="120"><p><? echo $lib_team_member_arr[$order_data_arr[$row[csf("po_id")]]['dealing_marchant']]; ?></p></td>
                    <td width="120"><p><? echo $lib_team_leader_name_arr[$order_data_arr[$row[csf("po_id")]]['team_leader']] ; ?></p></td>
                    <td width="120" align="center"><p><? echo $userArr[$row[csf("inserted_by")]]; ?></p></td>
                    <td ><p><? echo $row[csf("remarks")]; ?> &nbsp;</p></td>
				</tr>
				<?
				$tot_wo_qnty+=$row[csf("wo_qnty")];
				$tot_wo_value+=$wo_value;
				$tot_receive_qnty+=$rcv_qnty;
				$tot_receive_value+=$rcv_value;
				$tot_rcv_balance+=$rcv_balance;
				$total_ontime_rcv+=$ontimeRcv;
				$total_precost_value +=$pre_cost_value;
				$total_deference += $deference;
				//$total_deference_per +=$deference_per;

				$i++;
				$booking_mst_id=$row[csf("book_mst_id")];
				$job_no=$order_data_arr[$row[csf("po_id")]]["job_no_prefix_num"];	
				$po_id_arr[$row[csf("book_mst_id")]][$job_no][$row[csf("po_id")]]=$row[csf("po_id")];
			}
			?>
			 <tfoot>
                    <th >&nbsp;</th>
                    <th >&nbsp;</th>
                    <th >&nbsp;</th>
                    <th >&nbsp;</th>
                    <th >&nbsp;</th>
                    <th >&nbsp;</th>
                    <th >&nbsp;</th>
                    <th >&nbsp;</th>
                    <th >&nbsp;</th>
                    <th >&nbsp;</th>
                    <th >&nbsp;</th>
                    <th >&nbsp;</th>
					<th >&nbsp;</th>
					<th >&nbsp;</th>
                    <th >&nbsp;</th>
                    <th >&nbsp;</th>
					<th >&nbsp;</th>                   
					<th colspan="2">Per Psc Avg Rate :</th>
                    <th align="right"><? echo number_format($tot_wo_qnty/$order_total_qty,2) ;?></th>
                    <th >&nbsp;</th>
                    <th >&nbsp;</th>
                    <th >&nbsp;</th>					
					<th >&nbsp;</th>
					<th >&nbsp;</th>
                    <th >&nbsp;</th>                    
                    <th >&nbsp;</th>
                    <th >&nbsp;</th>
                    <th >&nbsp;</th>
                    <th >&nbsp;</th>
                </tfoot>
                <tfoot>
                    <th >&nbsp;</th>
                    <th >&nbsp;</th>
                    <th >&nbsp;</th>
                    <th >&nbsp;</th>
                    <th >&nbsp;</th>
                    <th >&nbsp;</th>
                    <th >&nbsp;</th>
                    <th >&nbsp;</th>
                    <th >&nbsp;</th>
                    <th >&nbsp;</th>
                    <th >&nbsp;</th>
                    <th >&nbsp;</th>
                    <th >Total Order Qty</th>
                    <th ><?=$order_total_qty ?></th>
                    <th >&nbsp;</th>
                    <th >&nbsp;</th>
					<th >&nbsp;</th>
                    <th >&nbsp;</th>
                    <th >Total:</th>
                    <th id="value_tot_wo_qnty" align="right"><? echo number_format($tot_wo_qnty,2) ;?></th>
                    <th >&nbsp;</th>
                    <th id="value_tot_wo_value" align="right"><? echo number_format($tot_wo_value,4) ;?></th>
                    <th >&nbsp;</th>
					
                    <th id="value_tot_precost_value" align="right"><? echo number_format($total_precost_value,2) ;?></th>
                    <th id="value_tot_deference" align="right"><? echo number_format($total_deference,2) ;?></th>
                    <th id="value_tot_deference_per" align="right"><? echo number_format(($total_deference/$total_precost_value)*100,2) ;?></th>
                    
                    <th >&nbsp;</th>
                    <th >&nbsp;</th>
                    <th >&nbsp;</th>
                    <th >&nbsp;</th>
                </tfoot>
            </table>
        </div>
        <br>
        <table cellspacing="0" cellpadding="0" border="0" width="1500" rules="all">
        	<tr>
            	<td valign="top">

                    <table cellspacing="0" cellpadding="0" border="1" class="rpt_table"  width="860" rules="all">

                        <thead>
                        	<tr bgcolor="#CCCCCC">
                                <td colspan="8" align="center" style="font-size:16px; font-weight:bold;">Emblishment Name Wise Summary</td>
                            </tr>
                            <tr>
                                <th width="50">SL</th>
                                <th width="150">Item Name</th>
                                <th width="100">WO Qty</th>
                                <th width="100">WO Value</th>
                                <th width="100">Precost value</th>
                                <th width="100">Difference</th>
                                <th width="60">%</th>
                               
                            </tr>
                        </thead>
                        <tbody>
                            <?
                            $k=1;
							//print_r($item_group_sammary);die;
							foreach($item_group_sammary as $item_grp_id=>$val)
                            {
                               	$otd=(($val["ontimeRcv"]/$val["wo_qnty"])*100);
								$item_group_sammary[$item_grp_id]["otd"]=$otd;
							}

							foreach($item_group_sammary as $item_group=>$val)
							{
								$mid[$item_group]  = $val["otd"];
							}
							array_multisort($mid, SORT_DESC, $item_group_sammary);

                            foreach($item_group_sammary as $val)
                            {
                                if ($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                               // $rcv_bal=$val["wo_qnty"]-$val["rcv_qnty"];
                               //$otd=(($val["ontimeRcv"]/$val["wo_qnty"])*100);
                                ?>
                                <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
                                    <td><? echo $k; ?></td>
                                    <td>
									<?
									//echo $trimsGroupArr[$item_group];
									echo $emblishment_name_array[$val["emblishment_name"]];
									?></td>
                                    <td align="right"><? echo number_format($val["wo_qnty"],2); ?></td>
                                     <td align="right"><? echo number_format($val["wo_value"],4); ?></td>
                                     <td align="right"><? echo number_format($val["pre_value"],2); ?></td>
                                     <td align="right"><? $difference=$val["pre_value"]-$val["wo_value"];echo number_format($difference,2); ?></td>
                                     <td align="right" title="Difference/Pre value*100"><? echo number_format((($difference/$val["pre_value"])*100),2); ?></td>
                                   
                                </tr>
                                <?
                                $i++;$k++;
                                $sum_tot_wo_qnty+=$val["wo_qnty"];
								 $sum_tot_wo_value+=$val["wo_value"];
								  $sum_tot_pre_value+=$val["pre_value"];
								   $sum_tot_difference+=$difference;
								  
                                $sum_tot_ontime_rcv+=$val["ontimeRcv"];
                                $sum_tot_rcv_qnty+=$val["rcv_qnty"];
                                $sum_tot_rcv_bal+=$rcv_bal;//$val["rcv_bal"];
                            }
                            $tot_otd=(($sum_tot_ontime_rcv/$sum_tot_wo_qnty)*100);
                            ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th>&nbsp;</th>
                                <th>Total:</th>
                                <th align="right"><? echo number_format($sum_tot_wo_qnty,2); ?></th>
                                <th align="right"><? echo number_format($sum_tot_wo_value,4); ?></th>
                                <th align="right"><? echo number_format($sum_tot_pre_value,2); ?></th>
                                <th align="right"><? echo number_format($sum_tot_difference,2); ?></th>
                                <th align="right"><? echo number_format((($sum_tot_difference/$sum_tot_pre_value)*100),2); ?></th>
                                
                            </tr>
                        </tfoot>
                    </table>

                </td>
                <td valign="top">&nbsp;</td>
                <td valign="top">
                	<table cellspacing="0" cellpadding="0" border="1" class="rpt_table"  width="760" rules="all">

                        <thead>
                        	<tr bgcolor="#CCCCCC">
                                <td colspan="8" align="center" style="font-size:16px; font-weight:bold;">Supplier Wise Summary</td>
                            </tr>
                            <tr>
                                <th width="50">SL</th>
                                <th width="150">Supplier Name</th>
                                <th width="100">WO Qty</th>
                                <th width="100">WO Value</th>
                                <th width="100">Precost value</th>
                                <th width="100">Difference</th>
                                <th width="60">%</th>
                                
                               
                            </tr>
                        </thead>
                        <tbody>
                            <?
                            $m=1;
							foreach($supplier_wise_sammary as $supplier_id=>$val)
                            {
                                $otd=(($val["ontimeRcv"]/$val["wo_qnty"])*100);
								$supplier_wise_sammary[$supplier_id]["otd"]=$otd;
							}

							foreach($supplier_wise_sammary as $supplier_id=>$val)
							{
								$sid[$supplier_id]  = $val["otd"];
							}
							array_multisort($sid, SORT_DESC, $supplier_wise_sammary);
                            foreach($supplier_wise_sammary as $val)
                            {
                                if ($m%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                                $rcv_bal=$val["wo_qnty"]-$val["rcv_qnty"];
                                $otd=(($val["ontimeRcv"]/$val["wo_qnty"])*100);
                                ?>
                                <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
                                    <td><? echo $m; ?></td>
                                    <td><? echo $supplierArr[$val["supplier_id"]]; ?></td>
                                    <td align="right"><? echo number_format($val["wo_qnty"],2); ?></td>
                                    
                                    <td align="right"><? echo number_format($val["wo_value"],2); ?></td>
                                    <td align="right"><? echo number_format($val["pre_value"],2); ?></td>
                                    <td align="right"><? $diference=$val["wo_value"]-$val["pre_value"];echo number_format($diference,2); ?></td>
                                    <td align="right"><? echo number_format($val["wo_qnty"],2); ?></td>
                                   
                                </tr>
                                <?
                                $i++;$m++;
                                $sup_tot_wo_qnty+=$val["wo_qnty"];
								$sup_tot_wo_value+=$val["wo_value"];
								$sup_tot_pre_value+=$val["pre_value"];
								$sup_tot_diference+=$diference;
								
                                $sup_tot_ontime_rcv+=$val["ontimeRcv"];
                                $sup_tot_rcv_qnty+=$val["rcv_qnty"];
                                $sup_tot_rcv_bal+=$rcv_bal;//$val["rcv_bal"];
                            }
                            $sup_tot_otd=(($sup_tot_ontime_rcv/$sup_tot_wo_qnty)*100);
                            ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th>&nbsp;</th>
                                <th>Total:</th>
                                <th align="right"><? echo number_format($sup_tot_wo_qnty,2); ?></th>
                                <th align="right"><? echo number_format($sup_tot_wo_value,2); ?></th>
                                <th align="right"><? echo number_format($sup_tot_pre_value,2); ?></th>
                                <th align="right"><? echo number_format($sup_tot_diference,2); ?></th>
                                <th align="right"><? echo number_format((($sup_tot_diference/$sup_tot_pre_value)*100),2); ?></th>
                                
                               
                            </tr>
                        </tfoot>
                    </table>
                </td>
            </tr>
        </table>
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
if ($action=="report_generate_3")
{
	//extract($_REQUEST);
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$cbo_company=str_replace("'","",$cbo_company_id);
	$wo_type=str_replace("'","",$cbo_wo_type);
	$cbo_category=str_replace("'","",$cbo_category_id);
	$cbo_buyer=str_replace("'","",$cbo_buyer_id);
	$year_id=str_replace("'","",$cbo_year_id);
	$wo_no=str_replace("'","",$txt_wo_no);
	$wo_id=str_replace("'","",$hidd_wo_id);
	$date_from=str_replace("'","",$txt_date_from);
	$date_to=str_replace("'","",$txt_date_to);
	$cbo_supplier=str_replace("'","",$cbo_supplier);
	$txt_date_category=str_replace("'","",$txt_date_category);
	$hidd_item_id=str_replace("'","",$hidd_item_id);
	$txt_item_no=str_replace("'","",$txt_item_no);
	$cbo_search_type=str_replace("'","",$cbo_search_type);
	$txt_search_common=str_replace("'","",$txt_search_common);
	$txt_job_po_id=str_replace("'","",$txt_job_po_id);
	$cbo_within_group_id=str_replace("'","",$cbo_within_group_id);

	
	if($cbo_search_type==1)
	{
		if($txt_search_common!="" && $txt_job_po_id!="")
		{
			$job_cond="and a.id in($txt_job_po_id)";
		}
		else if($txt_search_common!="" && $txt_job_po_id=="")
		{
			$job_cond="and a.job_no_prefix_num=$txt_search_common";

		}
	}
	else if($cbo_search_type==2)
	{
		if($txt_search_common!="" && $txt_job_po_id!="")
		{
			$job_cond="and a.id in($txt_job_po_id)";
		}
		else if($txt_search_common!="" && $txt_job_po_id=="")
		{
			$job_cond="and a.style_ref_no='$txt_search_common'";

		}
	}else if($cbo_search_type==3)
	{
		
			if ($txt_search_common=="") $txt_search_common=""; else $internal_ref_cond=" and b.grouping='".trim($txt_search_common)."' ";
	
	
	}
	
	if($cbo_within_group_id==1)
	{
		$within_cond="and a.pay_mode in(3,5)";
	} 
	else if($cbo_within_group_id==2)
	{ 
	  $within_cond="and a.pay_mode not in(3,5)";
	}
	else $within_cond="";
	//die;

	$sql_cond="";

	if($db_type==0)
	{
		if ($year_id==0) $year_id_cond=""; else $year_id_cond=" and YEAR(a.insert_date)=$year_id";
	}
	elseif($db_type==2)
	{
		if ($year_id==0) $year_id_cond=""; else $year_id_cond=" and TO_CHAR(a.insert_date,'YYYY')=$year_id";
	}

	if ($cbo_company!=0) $sql_cond=" and a.company_id=$cbo_company";
	if ($cbo_buyer!=0) $sql_cond.=" and a.buyer_id=$cbo_buyer";
	if ($cbo_buyer!=0) $buyer_id_cond=" and a.buyer_name=$cbo_buyer";else $buyer_id_cond="";
	if ($cbo_category!=0)  $sql_cond.=" and a.item_category=$cbo_category";
	if ($wo_no!="")  $sql_cond.=" and a.booking_no like '%$wo_no%'";
	if ($cbo_supplier>0)  $sql_cond.=" and a.supplier_id=$cbo_supplier";

	if ($hidd_item_id=="") $item_id=""; else $item_id=" and b.trim_group in ( $hidd_item_id )";

	/*if ($wo_type==1 || $wo_type==2)  $sql_cond.=" and a.booking_type in (1,2) and a.is_short='$wo_type'";
	if ($wo_type==3) $sql_cond.="  and a.booking_type=4";*/

	//echo $file_no_cond.'=='.$internal_ref_cond;die;
	if($txt_date_category==1)
	{
		if($db_type==0)
		{
			if( $date_from=="" && $date_to=="" ) $booking_date_cond=""; else $booking_date_cond=" and a.booking_date between '".$date_from."' and '".$date_to."'";
		}
		if($db_type==2)
		{
			if( $date_from=="" && $date_to=="" ) $booking_date_cond=""; else $booking_date_cond=" and a.booking_date between '".change_date_format($date_from,'dd-mm-yyyy','-',1)."' and '".change_date_format($date_to,'dd-mm-yyyy','-',1)."'";
		}
	}
	else
	{
		if($db_type==0)
		{
			if( $date_from=="" && $date_to=="" ) $booking_date_cond=""; else $booking_date_cond=" and b.delivery_date between '".$date_from."' and '".$date_to."'";
		}
		if($db_type==2)
		{
			if( $date_from=="" && $date_to=="" ) $booking_date_cond=""; else $booking_date_cond=" and b.delivery_date between '".change_date_format($date_from,'dd-mm-yyyy','-',1)."' and '".change_date_format($date_to,'dd-mm-yyyy','-',1)."'";
		}
	}


	if($db_type==0) $select_year="year(a.insert_date) as job_year"; else if($db_type==2) $select_year="to_char(a.insert_date,'YYYY') as job_year";

	$lib_team_name_arr=return_library_array( "select id, team_name from lib_marketing_team", "id", "team_name"  );
	$lib_team_leader_name_arr=return_library_array( "select id, team_leader_name from lib_marketing_team", "id", "team_leader_name"  );
	$lib_team_member_arr = return_library_array("select id, team_member_name from lib_mkt_team_member_info", "id", "team_member_name");
	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name");
	$supplierArr=return_library_array( "select id,supplier_name from lib_supplier", "id", "supplier_name");
	$buyerArr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name");
	$trimsGroupArr=return_library_array( "select id, item_name from lib_item_group", "id", "item_name");
	$userArr=return_library_array( "select id, user_name from user_passwd", "id", "user_name");
	$trim_type= return_library_array("select id, trim_type from lib_item_group",'id','trim_type');
	//$nonStyleArr=return_library_array( "select id, style_ref_no from sample_development_mst", "id", "style_ref_no");
		if($txt_search_common!="")
		{
		$sql_po=sql_select("select  b.id,b.job_no_mst  from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst   and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1  and a.company_name=$cbo_company $buyer_id_cond  $job_cond $internal_ref_cond group by   b.id,b.job_no_mst order by b.id,b.job_no_mst");
		//echo "select  b.id,b.job_no_mst  from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst   and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1  and a.company_name=$cbo_company $buyer_id_cond  $job_cond group by   b.id,b.job_no_mst order by b.id,b.job_no_mst";die;
		foreach( $sql_po as $row)
		{
			//$sql_po_qty_country_wise_arr[$row[csf('id')]][$row[csf('country_id')]]=$row[csf('order_quantity_set')];
			$po_id_arr[$row[csf('id')]]=$row[csf('id')];
		}
		unset($sql_po);
		}
		if($txt_search_common!="")
		{
			//$po_idCond="and b.po_break_down_id in(".implode(",",$po_id_arr).")";
		    $po_idCond=where_con_using_array(array_unique($po_id_arr),0,"b.po_break_down_id");
		} 
		else $po_idCond="";
	
	if($cbo_category==4) //Accessories
	{
		if($wo_type==0)
		{
			$sql="select a.company_id,a.id as book_mst_id, a.is_approved, a.update_date, a.pay_mode, a.booking_no as booking_no, a.booking_date as wo_date, b.delivery_date, a.booking_type as wo_type, a.is_short, a.supplier_id as supplier_id, a.buyer_id as buyer_id, a.item_category as item_category, a.remarks as remarks,a.inserted_by as inserted_by, b.id as dtls_id, b.po_break_down_id as po_id, b.pre_cost_fabric_cost_dtls_id as pre_cost_fabric_cost_dtls_id, b.trim_group as trim_group, b.construction as construction, b.copmposition as copmposition, b.uom as wo_uom, b.wo_qnty as wo_qnty,(case when a.exchange_rate>0 then (b.rate/a.exchange_rate) else b.rate end) as wo_rate, 1 as type
			from wo_booking_mst a, wo_booking_dtls b
			where a.booking_no=b.booking_no and a.booking_type in(2,5) and a.item_category=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1
		 and b.is_deleted=0  $po_idCond $item_id $sql_cond $booking_date_cond $within_cond
			union all
			select a.company_id,a.id as book_mst_id, a.is_approved,a.update_date,a.pay_mode,a.booking_no as booking_no, a.booking_date as wo_date, a.delivery_date, a.booking_type as wo_type, a.is_short, a.supplier_id as supplier_id, a.buyer_id as buyer_id, a.item_category as item_category, null as remarks,0 as inserted_by, b.id as dtls_id, 0 as po_id, 0 as pre_cost_fabric_cost_dtls_id, b.trim_group as trim_group, b.construction as construction, b.composition as copmposition, b.uom as wo_uom, b.trim_qty as wo_qnty,  (case when a.exchange_rate>0 then (b.rate/a.exchange_rate) else b.rate end) as wo_rate, 2 as type
			from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b
			where a.booking_no=b.booking_no and a.booking_type in(2,3)  and a.item_category=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1
		 and b.is_deleted=0  $item_id $sql_cond $booking_date_cond $within_cond order by book_mst_id ,po_id";// and b.po_break_down_id=33350
		}
		else if ($wo_type==1 || $wo_type==2)
		{
			$sql="select a.company_id,a.id as book_mst_id,a.is_approved,a.update_date,a.pay_mode, a.booking_no as booking_no, a.booking_date as wo_date, b.delivery_date, a.booking_type as wo_type, a.is_short, a.supplier_id as supplier_id, a.buyer_id as buyer_id, a.item_category as item_category, a.remarks as remarks, a.inserted_by as inserted_by, b.id as dtls_id, b.po_break_down_id as po_id, b.pre_cost_fabric_cost_dtls_id as pre_cost_fabric_cost_dtls_id, b.trim_group as trim_group, b.construction as construction, b.copmposition as copmposition, b.uom as wo_uom, b.wo_qnty as wo_qnty,(case when a.exchange_rate>0 then (b.rate/a.exchange_rate) else b.rate end) as wo_rate, 1 as type
			from wo_booking_mst a, wo_booking_dtls b
			where a.booking_no=b.booking_no and a.booking_type in(2) and a.is_short=$wo_type and a.item_category=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1
		 and b.is_deleted=0 $po_idCond $sql_cond $item_id $booking_date_cond $within_cond
			order by a.id";
		}
		else if ($wo_type==3)
		{
			$sql="select a.company_id,a.id as book_mst_id,a.is_approved,a.update_date,a.pay_mode, a.booking_no as booking_no, a.booking_date as wo_date, b.delivery_date, a.booking_type as wo_type, a.is_short, a.supplier_id as supplier_id, a.buyer_id as buyer_id, a.item_category as item_category, a.remarks as remarks,a.inserted_by as inserted_by, b.id as dtls_id, b.po_break_down_id as po_id, b.pre_cost_fabric_cost_dtls_id as pre_cost_fabric_cost_dtls_id, b.trim_group as trim_group, b.construction as construction, b.copmposition as copmposition, b.uom as wo_uom, b.wo_qnty as wo_qnty, (case when a.exchange_rate>0 then (b.rate/a.exchange_rate) else b.rate end) as wo_rate, 1 as type
			from wo_booking_mst a, wo_booking_dtls b
			where a.booking_no=b.booking_no and a.booking_type in(5) and a.item_category=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1
		 and b.is_deleted=0 $po_idCond $item_id $sql_cond $booking_date_cond $within_cond
			order by a.id";
		}
		else
		{
			$sql="select a.company_id,a.id as book_mst_id,a.is_approved,a.update_date,a.pay_mode, a.booking_no as booking_no, a.booking_date as wo_date, a.delivery_date, a.booking_type as wo_type, a.is_short, a.supplier_id as supplier_id, a.buyer_id as buyer_id, a.item_category as item_category, null as remarks, b.id as dtls_id, 0 as po_id, 0 as pre_cost_fabric_cost_dtls_id, b.trim_group as trim_group, b.construction as construction, b.composition as copmposition, b.uom as wo_uom, b.trim_qty as wo_qnty, (case when a.exchange_rate>0 then (b.rate/a.exchange_rate) else b.rate end) as wo_rate , 2 as type
			from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b
			where a.booking_no=b.booking_no and a.booking_type in(2,3)  and a.item_category=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1
		 and b.is_deleted=0 $item_id $sql_cond $booking_date_cond $within_cond";
		}
	}
	else if($cbo_category==25) //Emblishmnet
	{
		if($wo_type==0)
		{
			 $sql="select a.company_id,a.id as book_mst_id,a.currency_id, a.is_approved, a.update_date, a.pay_mode, a.booking_no as booking_no, a.booking_date as wo_date, b.delivery_date, a.booking_type as wo_type, a.is_short, a.supplier_id as supplier_id, a.buyer_id as buyer_id, a.item_category as item_category, a.remarks as remarks,a.inserted_by as inserted_by, b.id as dtls_id, b.po_break_down_id as po_id, b.pre_cost_fabric_cost_dtls_id as pre_cost_fabric_cost_dtls_id, b.emblishment_name,b.construction as construction, b.copmposition as copmposition, b.uom as wo_uom,b.gmts_color_id, b.wo_qnty as wo_qnty,b.rate,a.exchange_rate,(case when a.exchange_rate>0 then (b.rate/a.exchange_rate) else b.rate end) as wo_rate, 1 as type,, b.trim_group as trim_group
			from wo_booking_mst a, wo_booking_dtls b
			where a.booking_no=b.booking_no and a.booking_type in(6) and b.wo_qnty>0 and a.item_category=$cbo_category and a.status_active=1 and a.is_deleted=0 and b.status_active=1
		 and b.is_deleted=0   $po_idCond  $sql_cond $booking_date_cond $within_cond
			";// and b.po_break_down_id=33350
		}
		else if ($wo_type==1 || $wo_type==2)
		{
			$sql="select a.company_id,a.id as book_mst_id,a.currency_id,a.is_approved,a.update_date,a.pay_mode, a.booking_no as booking_no, a.booking_date as wo_date, b.delivery_date, a.booking_type as wo_type, a.is_short, a.supplier_id as supplier_id, a.buyer_id as buyer_id, a.item_category as item_category, a.remarks as remarks, a.inserted_by as inserted_by, b.id as dtls_id, b.po_break_down_id as po_id, b.pre_cost_fabric_cost_dtls_id as pre_cost_fabric_cost_dtls_id, b.emblishment_name, b.construction as construction, b.copmposition as copmposition, b.uom as wo_uom,b.gmts_color_id, b.wo_qnty as wo_qnty,b.rate,a.exchange_rate,(case when a.exchange_rate>0 then (b.rate/a.exchange_rate) else b.rate end) as wo_rate, 1 as type,, b.trim_group as trim_group
			from wo_booking_mst a, wo_booking_dtls b
			where a.booking_no=b.booking_no and a.booking_type in(6) and b.wo_qnty>0 and a.is_short=$wo_type and a.item_category=$cbo_category and a.status_active=1 and a.is_deleted=0 and b.status_active=1
		 and b.is_deleted=0  $po_idCond  $sql_cond   $booking_date_cond $within_cond
			order by a.id";
		}
	}
	//echo $sql;die;

	ob_start();
	if($cbo_category==4) //Accessories
	{
		// echo $sql;
		$sql_result=sql_select($sql);
		$all_po_id=""; $bookingIdArr=array();
		foreach($sql_result as $row)
		{
			if($all_po_id=="") $all_po_id=$row[csf("po_id")];else $all_po_id.=",".$row[csf("po_id")];
			//$emblish_arr[$row[csf("po_id")]]["po_id"]=$row[csf("po_id")];
			$bookingIdArr[$row[csf("booking_no")]]=$row[csf("book_mst_id")];
		}
		//echo $all_po_id.'ds';
		$poIds=chop($all_po_id,','); $po_cond_for_in=""; $po_cond_for_in2=""; $po_cond_for_in3=""; 
		$po_ids=count(array_unique(explode(",",$all_po_id)));
		// if($db_type==2 && $po_ids>1000)
		// {
		// 	$po_cond_for_in=" and (";
		// 	$po_cond_for_in2=" and (";
		// 	$poIdsArr=array_chunk(explode(",",$poIds),999);
		// 	foreach($poIdsArr as $ids)
		// 	{
		// 		$ids=implode(",",$ids);
		// 		//$poIds_cond.=" po_break_down_id in($ids) or ";
		// 		$po_cond_for_in.=" b.id in($ids) or"; 
		// 		$po_cond_for_in2.=" b.po_break_down_id in($ids) or"; 
		// 		$po_cond_for_in3.=" d.po_breakdown_id in($ids) or"; 
		// 		$tna_po.=" b.id in($ids) or";
		// 	}
		// 	$po_cond_for_in=chop($po_cond_for_in,'or ');
		// 	$po_cond_for_in.=")";
		// 	$po_cond_for_in2=chop($po_cond_for_in2,'or ');
		// 	$po_cond_for_in2.=")";

		// 	$po_cond_for_in3=chop($po_cond_for_in3,'or ');
		// 	$po_cond_for_in3.=")";

		// 	$tna_po=chop($tna_po,'or ');
		// 	$tna_po.=")";
		// }
		// else
		// {
		// 	$po_ids=implode(",",(array_unique(explode(",",$all_po_id))));
		// 	$po_cond_for_in=" and b.id in($po_ids)";
		// 	$po_cond_for_in2=" and b.po_break_down_id  in($po_ids)";
		// 	$po_cond_for_in3=" and d.po_breakdown_id  in($po_ids)";//po_break_down_id

		// 	$tna_po=" and  b.id  in($po_ids)";//po_break_down_id
		// }

		$po_cond_for_in=where_con_using_array(array_unique(explode(",",$all_po_id)),0,"b.id");
		$po_cond_for_in2=where_con_using_array(array_unique(explode(",",$all_po_id)),0,"b.po_break_down_id");
		$po_cond_for_in3=where_con_using_array(array_unique(explode(",",$all_po_id)),0,"d.po_breakdown_id");
		$po_cond_for_in4=where_con_using_array(array_unique(explode(",",$all_po_id)),0,"f.po_break_down_id");
		$tna_po=where_con_using_array(array_unique(explode(",",$all_po_id)),0,"b.id");

		$sql_date="SELECT a.task_finish_date,a.po_number_id,a.task_number from tna_process_mst a, wo_po_break_down b where a.po_number_id = b.id and a.status_active=1 and b.is_deleted=0 and A.TASK_NUMBER in (70,71) $tna_po  group by a.task_finish_date,a.po_number_id,a.task_number,a.task_category";
		//echo $sql_date;

		$date_arr=array();

		$sql_result=sql_select($sql_date);
		foreach ($sql_result as $row) 
		{
			$date_arr[$row[csf('po_number_id')]][$row[csf('task_number')]]=$row[csf('task_finish_date')];
		}
			
		$order_sql=sql_select("select a.id as job_id, a.job_no, a.job_no_prefix_num, $select_year, a.style_ref_no, b.id as po_id, b.po_number,a.dealing_marchant,a.team_leader,b.grouping,b.po_quantity  from  wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1  and b.status_active=1 $po_cond_for_in $internal_ref_cond");
		//echo "select a.id as job_id, a.job_no, a.job_no_prefix_num, $select_year, a.style_ref_no, b.id as po_id, b.po_number,a.dealing_marchant,a.team_leader,b.grouping  from  wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and b.status_active=1 $po_cond_for_in";
		//grouping field
		$order_data_arr=array();
		//echo $order_sql[csf("po_id")];die;
		$i = 0;
		foreach($order_sql as $row)
		{
			$order_data_arr[$row[csf("po_id")]]["po_id"]=$row[csf("po_id")];
			$order_data_arr[$row[csf("po_id")]]["job_id"]=$row[csf("job_id")];
			$order_data_arr[$row[csf("po_id")]]["job_no"]=$row[csf("job_no")];
			$order_data_arr[$row[csf("po_id")]]["job_no_prefix_num"]=$row[csf("job_no_prefix_num")];
			$order_data_arr[$row[csf("po_id")]]["job_year"]=$row[csf("job_year")];
			$order_data_arr[$row[csf("po_id")]]["style_ref_no"]=$row[csf("style_ref_no")];
			
			$order_data_arr[$row[csf("po_id")]]["grouping"]=$row[csf("grouping")];
			$order_data_arr[$row[csf('po_id')]]['team_leader']=$row[csf('team_leader')];
			$order_data_arr[$row[csf('po_id')]]['dealing_marchant']=$row[csf('dealing_marchant')];

		    $order_data_arr[$row[csf("po_id")]]["po_number"]=$row[csf("po_number")];
			$order_data_arr[$row[csf("po_id")]]["order_qty"]=$row[csf("po_quantity")];


			//echo $order_data_arr[$row[csf("po_id")]]["po_number"];
		}
		unset($order_sql);
		 $pi_sql = "SELECT a.id,a.pi_number, b.work_order_no,b.item_category_id from com_pi_master_details a, com_pi_item_details b where a.id=b.pi_id and a.status_active=1 and a.is_deleted=0
					and a.item_category_id in(4,25) and a.pi_basis_id=1 
					group by  a.id,a.pi_number, b.work_order_no,b.item_category_id order by a.pi_number desc";
				$pi_sql_result=sql_select($pi_sql);
				$pi_no_arr = array();
				foreach ($pi_sql_result as $value) 
				{
					$pi_no_arr[$value[csf("work_order_no")]]['pi_no']=$value[csf("pi_number")];
					$pi_no_arr[$value[csf("work_order_no")]]['po_id']=$value[csf("id")];
					$pi_no_arr[$value[csf("work_order_no")]]['item_category_id']=$value[csf("item_category_id")];
				}
				unset($pi_sql_result);
		$trims_sql=sql_select("select b.id as po_id, a.trim_group, sum(a.rate) as trims_rate from wo_pre_cost_trim_cost_dtls a, wo_po_break_down b where  a.job_no=b.job_no_mst and a.status_active=1 $po_cond_for_in group by b.id, a.trim_group");
		$pre_cost_data_arr=array();
		foreach($trims_sql as $row)
		{
			$pre_cost_data_arr[$row[csf("po_id")]][$row[csf("trim_group")]]=$row[csf("trims_rate")];
		}
		unset($trims_sql);
		$description_sql="select b.wo_trim_booking_dtls_id, b.description, b.brand_supplier, b.item_color, b.item_size from  wo_trim_book_con_dtls b where b.status_active=1 $po_cond_for_in2";
		//echo $description_sql;
		$description_sql_result=sql_select($description_sql);
		$description_arr=array();
		foreach($description_sql_result as $row)
		{
			if( ($row[csf("description")]!=0 || $row[csf("description")]!="") && $description_check[$row[csf("wo_trim_booking_dtls_id")]][$row[csf("description")]]=="")
			{
				$description_check[$row[csf("wo_trim_booking_dtls_id")]][$row[csf("description")]]=$row[csf("description")];
				$description_arr[$row[csf("wo_trim_booking_dtls_id")]]["description"].=$row[csf("description")]."__";
			}
			if($brand_supplier_check[$row[csf("wo_trim_booking_dtls_id")]][$row[csf("brand_supplier")]]=="")
			{
				$brand_supplier_check[$row[csf("wo_trim_booking_dtls_id")]][$row[csf("brand_supplier")]]=$row[csf("brand_supplier")];
				$description_arr[$row[csf("wo_trim_booking_dtls_id")]]["brand_supplier"].=$row[csf("brand_supplier")]."__";
			}
			
			if($item_color_check[$row[csf("wo_trim_booking_dtls_id")]][$row[csf("item_color")]]=="")
			{
				$item_color_check[$row[csf("wo_trim_booking_dtls_id")]][$row[csf("item_color")]]=$row[csf("item_color")];
				$description_arr[$row[csf("wo_trim_booking_dtls_id")]]["item_color"].=$row[csf("item_color")]."__";
			}
			
			if($item_size_check[$row[csf("wo_trim_booking_dtls_id")]][$row[csf("item_size")]]=="")
			{
				$item_size_check[$row[csf("wo_trim_booking_dtls_id")]][$row[csf("item_size")]]=$row[csf("item_size")];
				$description_arr[$row[csf("wo_trim_booking_dtls_id")]]["item_size"].=$row[csf("item_size")]."__";
			}
		}
		unset($description_sql_result);
		$piBookingsql=sql_select( "select a.pi_id, a.work_order_id, a.item_group, b.po_break_down_id from com_pi_item_details a, wo_booking_dtls b where a.work_order_dtls_id=b.id and  a.item_category_id = 4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $po_cond_for_in2");
		$piBookingNo=array();
		foreach($piBookingsql as $row)
		{
			$piBookingNo[$row[csf("pi_id")]][$row[csf("po_break_down_id")]][$row[csf("item_group")]]=$row[csf("work_order_id")];
		}
		unset($piBookingsql);

		$issue_qty_data=sql_select("SELECT d.ISSUE_NUMBER,d.ISSUE_DATE,d.id,b.po_breakdown_id,a.issue_qnty as quantity, g.id as booking_id,a.rate,b.order_amount,p.item_description, p.item_group_id  from  inv_issue_master d join inv_trims_issue_dtls a on a.mst_id=d.id join product_details_master p on a.prod_id=p.id join order_wise_pro_details b on a.trans_id=b.trans_id join  wo_po_break_down e on e.id=b.po_breakdown_id join wo_booking_dtls f on e.id=f.po_break_down_id join wo_booking_mst g on g.booking_no=f.booking_no where  b.trans_type=2 and b.entry_form=25 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and f.booking_type=2 and f.is_short=2 and f.is_deleted=0 and f.status_active=1 and g.is_deleted=0 and g.status_active=1 and d.item_category=4 $po_cond_for_in4 group by d.ISSUE_NUMBER,d.ISSUE_DATE,a.issue_qnty , g.id ,a.rate,b.order_amount,d.id,b.po_breakdown_id,p.item_description, p.item_group_id");
		$issue_data_po=array();
		foreach($issue_qty_data as $row)
		{
			$woid=$row[csf('booking_id')];
			$itemgroup=$row[csf('item_group_id')];
			$po_id=$row[csf('po_breakdown_id')];

			$issue_data_po[$woid][$po_id][$itemgroup][$row[csf('item_description')]]["issue_qnty"]+=$row[csf('quantity')];
			$issue_data_po[$woid][$po_id][$itemgroup][$row[csf('item_description')]]["issue_amount"]+=$row[csf('order_amount')];
			$issue_data_po[$woid][$po_id][$itemgroup][$row[csf('item_description')]]["id"]=$row[csf('id')];
		}
		unset($issue_qty_data);
		$issue_qty_data_nonorder=sql_select("select d.id,b.po_breakdown_id,d.booking_id,p.item_description, p.item_group_id,sum(b.cons_quantity) as quantity, sum(b.cons_quantity*b.order_rate) as issue_amount from inv_issue_master d, product_details_master p, inv_trims_issue_dtls a, order_wise_pro_details b where a.mst_id=d.id and a.prod_id=p.id and a.trans_id=b.trans_id and b.trans_type=2 and d.entry_form=25 and b.entry_form=25 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.item_group_id=4 $po_cond_for_in4  group by d.id,b.po_breakdown_id,d.booking_id,p.item_description, p.item_group_id");
		$issue_data_po_nonorder=array();
		foreach($issue_qty_data_nonorder as $row)
		{
			$woid=$row[csf('booking_id')];
			$itemgroup=$row[csf('item_group_id')];
			$po_id=$row[csf('po_breakdown_id')];

			$issue_data_po_nonorder[$woid][$po_id][$itemgroup][$row[csf('item_description')]]["issue_qnty"]+=$row[csf('quantity')];
			$issue_data_po_nonorder[$woid][$po_id][$itemgroup][$row[csf('item_description')]]["issue_amount"]+=$row[csf('issue_amount')];
		}
		unset($issue_qty_data_nonorder);
		$rcv_qnty_sql="select b.mst_id, b.receive_basis, b.transaction_date as receive_date, c.booking_id as pi_wo_batch_no, b.booking_no, c.item_group_id, c.item_description, c.brand_supplier, d.po_breakdown_id as po_id, d.id as prop_id, d.quantity as rcv_qnty, d.order_amount as rcv_value, e.work_order_id
		from inv_trims_entry_dtls c, order_wise_pro_details d, inv_transaction b 
		left join com_pi_item_details e on e.pi_id=b.pi_wo_batch_no
		where b.id=c.trans_id and c.id=d.dtls_id and c.trans_id=d.trans_id and b.transaction_type=1 and b.item_category=4 and d.entry_form=24 and d.status_active=1 and d.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $po_cond_for_in3";
		//echo $rcv_qnty_sql;
		$receive_qty_data=sql_select($rcv_qnty_sql);
		$rcv_data_po=array();
		$rcv_data_po_ontime=array();
		foreach($receive_qty_data as $row)
		{
			$woid=$row[csf('pi_wo_batch_no')];
			$itemgroup=$row[csf('item_group_id')];
			$po_id=$row[csf('po_id')];
			if($row[csf('receive_basis')]==1) $woid=$row[csf('work_order_id')];
			if($row[csf('receive_basis')]==12) $woid=$bookingIdArr[$row[csf("booking_no")]];
			//echo $woid.'='.$po_id.'='.$itemgroup.'='.$row[csf('item_description')].'='.$row[csf('brand_supplier')].'<br>';
			$rcv_data_po[$woid][$po_id][$itemgroup][$row[csf('item_description')]]["rcv_qnty"]+=$row[csf('rcv_qnty')];
			$rcv_data_po[$woid][$po_id][$itemgroup][$row[csf('item_description')]]["rcv_value"]+=$row[csf('rcv_value')];
			$rcv_data_po[$woid][$po_id][$itemgroup][$row[csf('item_description')]]["receive_date"].=$row[csf('receive_date')].",";
			$rcv_data_po[$woid][$po_id][$itemgroup][$row[csf('item_description')]]["receive_basis"]=$row[csf('receive_basis')];
			
			if($propo_id_check[$row[csf('prop_id')]]=="")
			{
				$propo_id_check[$row[csf('prop_id')]]=$row[csf('prop_id')];
				$rcv_daa_po[$woid][$po_id][$itemgroup][$row[csf('item_description')]]["rcv_qnty"]+=$row[csf('rcv_qnty')];
				$rcv_daa_po[$woid][$po_id][$itemgroup][$row[csf('item_description')]]["rcv_value"]+=$row[csf('rcv_value')];
				$rcv_data_po_ontime[$row[csf('receive_date')]][$woid][$po_id][$itemgroup][$row[csf('item_description')]]["rcv_qnty"]+=$row[csf('rcv_qnty')];
				$rcv_data_po_ontime[$row[csf('receive_date')]][$woid][$po_id][$itemgroup][$row[csf('item_description')]]["rcv_value"]+=$row[csf('rcv_value')];
			}
			
			if($row[csf('mst_id')] && $mst_id_check[$row[csf('receive_date')]][$woid][$po_id][$itemgroup][$row[csf('item_description')]][$row[csf('mst_id')]]=="")
			{
				 $mst_id_check[$row[csf('receive_date')]][$woid][$po_id][$itemgroup][$row[csf('item_description')]][$row[csf('mst_id')]]=$row[csf('mst_id')];
				$rcv_data_po_ontime[$row[csf('receive_date')]][$woid][$po_id][$itemgroup][$row[csf('item_description')]]["mst_id"].=$row[csf('mst_id')].",";
			}
			
		}
		/*echo "<pre>";
		print_r($rcv_data_po_ontime); die;*/
		unset($receive_qty_data);
	
		$receive_qty_data_noorder=sql_select("select a.id as mst_id, a.receive_basis, a.receive_date, c.booking_id as pi_wo_batch_no, b.cons_quantity as rcv_qnty, c.item_group_id, c.item_description, c.brand_supplier, b.order_amount as rcv_value
		from inv_receive_master a, inv_transaction b, inv_trims_entry_dtls c
		where a.id=b.mst_id and b.id=c.trans_id and b.transaction_type=1  and b.pi_wo_batch_no>0 and b.item_category=4 and a.entry_form=24 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0");
		
		$rcv_data_nonOrder=array();
		$rcv_data_nonOrder_on_time=array();
		foreach($receive_qty_data_noorder as $row)
		{
			$woid=$row[csf('pi_wo_batch_no')];
			$itemgroup=$row[csf('item_group_id')];
			if($row[csf('receive_basis')]==1) $woid=$piBookingNo[$woid][$po_id][$itemgroup];
			$rcv_data_nonOrder[$woid][$itemgroup][$row[csf('item_description')]]["rcv_qnty"]+=$row[csf('rcv_qnty')];
			$rcv_data_nonOrder[$woid][$itemgroup][$row[csf('item_description')]]["rcv_value"]+=$row[csf('rcv_value')];
			$rcv_data_nonOrder[$woid][$itemgroup][$row[csf('item_description')]]["receive_date"].=$row[csf('receive_date')].",";
	
			$rcv_data_nonOrder_on_time[$row[csf('receive_date')]][$woid][$itemgroup][$row[csf('item_description')]]["rcv_qnty"]+=$row[csf('rcv_qnty')];
			$rcv_data_nonOrder_on_time[$row[csf('receive_date')]][$woid][$itemgroup][$row[csf('item_description')]]["rcv_value"]+=$row[csf('rcv_value')];
			if($row[csf('mst_id')] && $non_mst_id_check[$row[csf('receive_date')]][$woid][$itemgroup][$row[csf('item_description')]][$row[csf('mst_id')]]=="")
			{
				$non_mst_id_check[$row[csf('receive_date')]][$woid][$itemgroup][$row[csf('item_description')]][$row[csf('mst_id')]]=$row[csf('mst_id')];
				$rcv_data_nonOrder_on_time[$row[csf('receive_date')]][$woid][$itemgroup][$row[csf('item_description')]]["mst_id"].=$row[csf('mst_id')].",";
			}
		}
		unset($receive_qty_data_noorder);
		//echo "test";die;

		$pre_print_report_format=return_library_array( "select template_name, format_id from lib_report_template where  module_id=5 and report_id=183 and is_deleted=0 and status_active=1 and template_name=$cbo_company", "template_name", "format_id");
		?>
		<fieldset>
			<table width="2480"  cellspacing="0" >
				<tr class="form_caption" style="border:none;">
					<td align="center" style="border:none; font-size:18px;" colspan="22">
						<? echo $company_library[str_replace("'","",$cbo_company_id)]; ?>
					</td>
				</tr>
				<tr class="form_caption" style="border:none;">
					<td align="center" style="border:none;font-size:16px; font-weight:bold"  colspan="25"> <? echo $report_title ;?></td>
				</tr>
				<tr class="form_caption" style="border:none;">
					<td align="center" style="border:none;font-size:12px; font-weight:bold"  colspan="25"> <? echo "From ".change_date_format($date_from)." To ".change_date_format($date_to);?></td>
				</tr>
			</table>
			<table width="2940" cellspacing="0" border="1" class="rpt_table" rules="all">
				<thead>
					<th width="40">SL</th>
					<th width="110">WO No</th>
					<th width="70">Approved Date</th>
					<th width="70">Pay Mode</th>
					<th width="70">PI No</th>
					<th width="110">Internal Ref No</th>
					<th width="70">WO Date</th>
					<th width="70">Delivery Date</th>
					<th width="70">Lead Time</th>
					<th width="90">WO Type</th>
					<th width="100">Supplier</th>
					<th width="120">Item Name</th>
					<th width="150">Description</th>
					<th width="100">Item Category</th>
					<th width="60">UOM</th>
					<th width="80">WO Qty</th>
					<th width="70">WO Unit price</th>
					<th width="80">WO value</th>
					<th width="70">Budget Unit price</th>
					<th width="80">Precost value</th>
					<th width="80" title="(Precost value - WO value)">Deference</th>
					<th width="80" title="(Deference / Precost value)*100">Deference %</th>
					<th width="80">OTD%</th>
					<th width="80">Total Receive Qty</th>
					<th width="80">Receive Value</th>
					<th width="80">Receive Balance</th>
					<th width="80">Total Issue Qty</th>
					<th width="80">Issue Value</th>
					<th width="80">Issue Balance</th>
					<th width="120">Dealing Merchant</th>
					<th width="120">Team Leader</th>
					<th width="120">User Name</th>
					<th >Remarks</th>
				</thead>
			</table>
			<div style="max-height:350px; overflow-y:scroll; width:2960px" id="scroll_body" >
				<table cellspacing="0" cellpadding="0" border="1" class="rpt_table"  width="2940" rules="all" id="table_body" >
				<?
				$sql_result=sql_select($sql); $i=1;$item_group_sammary=array();
				$total_precost_value =0; $total_deference=0; $total_deference_per =0;
				$r=0;
				foreach($sql_result as $row)
				{
					// echo $buyer_count."<br>";
					$job_no=$order_data_arr[$row[csf("po_id")]]["job_no_prefix_num"];
						$row_count[$row[csf("book_mst_id")]]+=1;
						$row_count2[$row[csf("book_mst_id")]][$job_no][$row[csf("po_id")]]+=1;
				}
			
				$po_id_arr=array();
				foreach($sql_result as $row)
				{
					// $supplier=$row[csf("supplier_id")];
					// $buyer_id=$row[csf("buyer_id")];
					// $buyer_count =count($row_count[$supplier][$buyer_id]);

					// echo $buyer_count."<br>";
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					if ($row[csf("type")]==1)
					{
						if ($row[csf("wo_type")]==2)
						{
							if ($row[csf("is_short")]==1)
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
						elseif($row[csf("wo_type")]==5)
						{
							$wo_type="Sample With Order";
							$wo_typw_id=3;
						}
					}
					else
					{
						$wo_type="Sample Without Order";
						$wo_typw_id=4;
					}
					$lead_time=datediff("d", $row[csf("wo_date")], $row[csf("delivery_date")]);
					//$job_number=implode(",",array_unique(explode(",",$row[csf("job_no")])));
					$rcv_qnty=$rcv_balance=$issue_qnty=$issue_amount=$ontimeRcv=0;$rcv_mst_id=$issue_mst_id=$po_id="";
	
					if($row[csf('type')]==1)
					{
						//echo $row[csf('book_mst_id')].'='.$row[csf("po_id")].'='.$row[csf("trim_group")].'='.$description_arr[$row[csf("dtls_id")]]["description"].'='.$description_arr[$row[csf("dtls_id")]]["brand_supplier"].'<br>';
						$rcv_qnty=$rcv_value=$ontimeRcv=0;$rcv_mst_id="";$issue_mst_id="";
						$desc_all_arr=explode("__",chop($description_arr[$row[csf("dtls_id")]]["description"],"__"));
						foreach($desc_all_arr as $descript)
						{
							$rcv_qnty+=$rcv_data_po[$row[csf('book_mst_id')]][$row[csf("po_id")]][$row[csf("trim_group")]][$descript]["rcv_qnty"];
							$rcv_value+=$rcv_data_po[$row[csf('book_mst_id')]][$row[csf("po_id")]][$row[csf("trim_group")]][$descript]["rcv_value"];
							$issue_qnty+=$issue_data_po[$row[csf('book_mst_id')]][$row[csf("po_id")]][$row[csf("trim_group")]][$descript]["issue_qnty"];
							$issue_amount+=$issue_data_po[$row[csf('book_mst_id')]][$row[csf("po_id")]][$row[csf("trim_group")]][$descript]["issue_amount"];
							$issue_mst_id=$issue_data_po[$row[csf('book_mst_id')]][$row[csf("po_id")]][$row[csf("trim_group")]][$descript]["id"];
							
							$rcv_date_arr=array();
							$rcv_date_arr=array_unique(explode(",",chop($rcv_data_po[$row[csf('book_mst_id')]][$row[csf("po_id")]][$row[csf('trim_group')]][$descript]["receive_date"],",")));
							foreach($rcv_date_arr as $rcv_date)
							{
								if($rcv_date!="" && $rcv_date!="0000-00-00")
								{
									if(strtotime($rcv_date)<=strtotime($row[csf("delivery_date")]) && $rcv_data_po_ontime[$rcv_date][$row[csf('book_mst_id')]][$row[csf("po_id")]][$row[csf('trim_group')]][$descript]["rcv_qnty"]>0)
									{
										$ontimeRcv+=$rcv_data_po_ontime[$rcv_date][$row[csf('book_mst_id')]][$row[csf("po_id")]][$row[csf('trim_group')]][$descript]["rcv_qnty"];
										$rcv_mst_id.=chop($rcv_data_po_ontime[$rcv_date][$row[csf('book_mst_id')]][$row[csf("po_id")]][$row[csf('trim_group')]][$descript]["mst_id"],",").",";
									}
								}
							}
						}
					}
					else
					{
						$po_id=$row[csf("po_id")];
						$rcv_qnty=$rcv_value=$ontimeRcv=$issue_qnty=$issue_amount=0;$rcv_mst_id="";
						$desc_all_arr=explode("__",chop($description_arr[$row[csf("dtls_id")]]["description"],"__"));
						foreach($desc_all_arr as $descript)
						{
							$rcv_qnty=$rcv_data_nonOrder[$row[csf('book_mst_id')]][$row[csf("trim_group")]][$descript]["rcv_qnty"];
							$rcv_value=$rcv_data_nonOrder[$row[csf('book_mst_id')]][$row[csf("trim_group")]][$descript]["rcv_value"];
							$issue_qnty=$issue_data_po_nonorder[$row[csf('book_mst_id')]][$row[csf("trim_group")]][$descript]["issue_qnty"];
							$issue_amount=$issue_data_po_nonorder[$row[csf('book_mst_id')]][$row[csf("trim_group")]][$descript]["issue_amount"];
							$rcv_date_arr=array();
							$rcv_date_arr=array_unique(explode(",",chop($rcv_data_nonOrder[$row[csf('book_mst_id')]][$row[csf('trim_group')]][$descript]["receive_date"],",")));
							foreach($rcv_date_arr as $rcv_date)
							{
								if($rcv_date!="" && $rcv_date!="0000-00-00")
								{
									if(strtotime($rcv_date)<=strtotime($row[csf("delivery_date")]) && $rcv_data_nonOrder_on_time[$rcv_date][$row[csf('book_mst_id')]][$row[csf('trim_group')]][$descript]["rcv_qnty"]>0)
									{
										$ontimeRcv+=$rcv_data_nonOrder_on_time[$rcv_date][$row[csf('book_mst_id')]][$row[csf('trim_group')]][$descript]["rcv_qnty"];
										$rcv_mst_id.=chop($rcv_data_nonOrder_on_time[$rcv_date][$row[csf('book_mst_id')]][$row[csf('trim_group')]][$descript]["mst_id"],",").",";
									}
								}
							}
						}
					}
					
					$rcv_balance=$row[csf("wo_qnty")]-$rcv_qnty;
	
					$rcv_mst_id=chop($rcv_mst_id,",");
					$issue_mst_id=chop($issue_mst_id,",");
					$otd=0;
					$otd=(($ontimeRcv/$row[csf("wo_qnty")])*100);
	
					if($row[csf("wo_qnty")]>0 && $row[csf("trim_group")]>0)
					{
						$item_group_sammary[$row[csf("trim_group")]]["wo_qnty"]+=$row[csf("wo_qnty")];
						$item_group_sammary[$row[csf("trim_group")]]["wo_value"]+=$row[csf("wo_qnty")]*$row[csf("wo_rate")];
						$item_group_sammary[$row[csf("trim_group")]]["pre_value"]+=$row[csf("wo_qnty")]*$pre_cost_data_arr[$row[csf("po_id")]][$row[csf("trim_group")]];//*$pre_cost_data_arr[$row[csf("po_id")]][$row[csf("trim_group")]]
						$item_group_sammary[$row[csf("trim_group")]]["ontimeRcv"]+=$ontimeRcv;
						$item_group_sammary[$row[csf("trim_group")]]["rcv_qnty"]+=$rcv_qnty;
						$item_group_sammary[$row[csf("trim_group")]]["trim_group"]=$row[csf("trim_group")];
					}
	
					if($row[csf("supplier_id")]>0 && $row[csf("wo_qnty")]>0)
					{ //supplierArr 
						if($row[csf("pay_mode")]==3 || $row[csf("pay_mode")]==5)
						{
						 $supplier_wise_sammary[$row[csf("supplier_id")]]["supp_comp"]=$company_library[$row[csf("supplier_id")]];
						}
						else
						{
						 $supplier_wise_sammary[$row[csf("supplier_id")]]["supp_comp"]=$supplierArr[$row[csf("supplier_id")]];
						}
						$supplier_wise_sammary[$row[csf("supplier_id")]]["wo_qnty"]+=$row[csf("wo_qnty")];
						$supplier_wise_sammary[$row[csf("supplier_id")]]["wo_value"]+=$row[csf("wo_qnty")]*$row[csf("wo_rate")];
						$supplier_wise_sammary[$row[csf("supplier_id")]]["pre_value"]+=$row[csf("wo_qnty")]*$pre_cost_data_arr[$row[csf("po_id")]][$row[csf("trim_group")]];
						$supplier_wise_sammary[$row[csf("supplier_id")]]["ontimeRcv"]+=$ontimeRcv;
						$supplier_wise_sammary[$row[csf("supplier_id")]]["rcv_qnty"]+=$rcv_qnty;
						$supplier_wise_sammary[$row[csf("supplier_id")]]["supplier_id"]=$row[csf("supplier_id")];
					}
					if($row[csf("is_approved")]==1)
					{
						$approved_date=$row[csf("update_date")];
						$approved_date = date('Y-m-d', strtotime($approved_date));
					}
					else $approved_date='';
					
					if($row[csf("pay_mode")]==3 || $row[csf("pay_mode")]==5) $supplier_com=$company_library[$row[csf("supplier_id")]]; else $supplier_com=$supplierArr[$row[csf("supplier_id")]];

					$pre_print_report_format_ids=$pre_print_report_format[$cbo_company];
					$pre_print_report_ids=explode(",",$pre_print_report_format_ids);
					$print_button_first=array_shift($pre_print_report_ids);

					if($print_button_first==751){$action='print_pi'; } 
					else if($print_button_first==86){$action='pi_mst';} 
					else if($print_button_first==116){$action='print_wf';} 
					else if($print_button_first==85){$action='print_sf';} 
					else if($print_button_first==89){$action='print_f';} 
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
						<td width="40" align="center"><? echo $i;?></td>
						<td width="110"><p><? echo $row[csf("booking_no")]; ?></td>
						<td width="70"><p><? echo $approved_date; ?></td>
						<td width="70"><p><? echo $pay_mode[$row[csf('pay_mode')]]; ?></td>
						<td width="70"><p><? //echo $pi_no_arr[$row[csf("booking_no")]]['pi_no'];  ?>
						<a href="#report_details" onClick="booking_report_generate('<?=$row[csf("company_id")]; ?>','<?=$pi_no_arr[$row[csf("booking_no")]]['po_id']; ?>','4','<?=$action; ?>','167')"><?=$pi_no_arr[$row[csf("booking_no")]]['pi_no'];?>
							</a>
						
						</td>
						<td width="110"><p><? echo $order_data_arr[$row[csf("po_id")]]["grouping"]; ?></td>
						<td width="70" align="center"><p><? echo change_date_format($row[csf("wo_date")]); ?>&nbsp;</p></td>
						<td width="70" align="center"><p><?
								$date_del="";
								if($trim_type[$row[csf('trim_group')]]==1)
								{
									$date_del=$date_arr[$row[csf('po_id')]][70];
								}
								else{
									$date_del=$date_arr[$row[csf('po_id')]][71];
								}
								if(empty($date_del))
								{
									$date_del=$row[csf("delivery_date")];
								}
						 		echo change_date_format($row[csf("delivery_date")]); 
						 ?>&nbsp;</p></td>
						<td width="70" align="center"><p><? if($lead_time>0 && $lead_time<2) echo $lead_time." Day"; elseif($lead_time>1) echo $lead_time." Days"; else echo "0 Day"; ?>&nbsp;</p></td>
						<td width="90"><p><?  echo $wo_type; ?>&nbsp;</p></td>
						<td width="100"><p><? echo $supplier_com; ?>&nbsp;</p></td>
						
						<td width="120"><p><? echo $trimsGroupArr[$row[csf("trim_group")]]; ?>&nbsp;</p></td>
						<td width="150" style="word-break:break-all"><p>
						<?
						$desc_trim=chop($description_arr[$row[csf("dtls_id")]]["description"],'__');
						$desc_trims=implode(",",array_unique(explode(",",$desc_trim)));
						echo $desc_trims; 
						?>&nbsp;</p></td>
						<td width="100"><p><? echo $item_category[$row[csf("item_category")]]; ?>&nbsp;</p></td>
						<td width="60"><p><? echo $unit_of_measurement[$row[csf("wo_uom")]]; ?>&nbsp;</p></td>
						<td width="80" align="right"><p><? echo number_format($row[csf("wo_qnty")],2,'.',''); ?></p></td>
						<td width="70" align="right"><p><? echo number_format($row[csf("wo_rate")],2,'.',''); ?>&nbsp;</p></td>
						<td width="80" align="right"><p><? $wo_value=$row[csf("wo_qnty")]*$row[csf("wo_rate")]; echo number_format($wo_value,4,'.',''); ?></p></td>
						<td width="70" align="right"><p><? echo number_format($pre_cost_data_arr[$row[csf("po_id")]][$row[csf("trim_group")]],2,'.',''); ?></p></td>
						<td width="80" align="right"><p><? $precost_value=$row[csf("wo_qnty")]*$pre_cost_data_arr[$row[csf("po_id")]][$row[csf("trim_group")]]; echo number_format($precost_value,2); ?></p></td>
						<td width="80" align="right"><p><? $deference = $precost_value-$wo_value; echo number_format($deference,2,'.',''); ?></p></td>
						<td width="80" align="right"><p><? $deference_per = ($deference/$precost_value)*100; echo number_format($deference_per,2,'.',''); ?></p></td>
						<td width="80" align="right"><p><? echo number_format($otd,2);?></p></td>
						<td width="80" align="right"><p><a href='#report_details' onClick="openmypage_inhouse('<? echo $row[csf("book_mst_id")]; ?>','<? echo $row[csf("po_id")]; ?>','<? echo $row[csf("trim_group")]; ?>','<? echo chop($description_arr[$row[csf("dtls_id")]]["description"],"__"); ?>','<? echo $rcv_mst_id; ?>','2','<? echo $row[csf('type')] ?>','','booking_rec_info');"><? echo number_format($rcv_qnty,2,'.','');?></a></p></td>
						<td width="80" align="right"><p><? echo number_format($rcv_value,2,'.',''); ?></p></td>
						<td width="80" align="right"><p><? echo number_format($rcv_balance,2,'.',''); ?></p></td>
						<td width="80" align="right"><p><a href='#report_details' onClick="openmypage_inhouse('<? echo $row[csf("book_mst_id")]; ?>','<? echo $row[csf("po_id")]; ?>','<? echo $row[csf("trim_group")]; ?>','<? echo chop($description_arr[$row[csf("dtls_id")]]["description"],"__"); ?>','<? echo $issue_mst_id; ?>','2','<? echo $row[csf('type')] ?>','','booking_issue_info');"><? echo number_format($issue_qnty,2,'.','');?></a></p></td>
						<td width="80" align="right"><p><? echo number_format($issue_amount,2,'.',''); ?></p></td>
						<td width="80" align="right"><p><? $issue_balance=$rcv_qnty-$issue_qnty;echo number_format($issue_balance,2,'.',''); ?></p></td>
						<td width="120"><p><? echo $lib_team_member_arr[$order_data_arr[$row[csf("po_id")]]['dealing_marchant']]; ?></p></td>
						<td width="120"><p><? echo $lib_team_leader_name_arr[$order_data_arr[$row[csf("po_id")]]['team_leader']] ; ?></p></td>
						<td width="120" align="right"><p><? echo $userArr[$row[csf("inserted_by")]]; ?></p></td>
						<td ><p><? echo $row[csf("remarks")]; ?> &nbsp;</p></td>
					</tr>
					<?
					$order_qty_total +=$order_data_arr[$row[csf("po_id")]]["order_qty"];
					
					$tot_wo_qnty+=$row[csf("wo_qnty")];
					$tot_wo_value+=$wo_value;
					$tot_receive_qnty+=$rcv_qnty;
					$tot_receive_value+=$rcv_value;
					$tot_rcv_balance+=$rcv_balance;
					$tot_issue_qnty+=$issue_qnty;
					$tot_issue_value+=$issue_amount;
					$tot_issue_balance+=$issue_balance;
					$total_ontime_rcv+=$ontimeRcv;
					$total_precost_value +=$precost_value;
					$total_deference += $deference;
					//$total_deference_per +=$deference_per;
	
					$i++;
					$booking_mst_id=$row[csf("book_mst_id")];
					$job_no=$order_data_arr[$row[csf("po_id")]]["job_no_prefix_num"];	
					$po_id_arr[$row[csf("book_mst_id")]][$job_no][$row[csf("po_id")]]=$row[csf("po_id")];
						
				}
				?>
					
					<tfoot>
						<th >&nbsp;</th>
						<th >&nbsp;</th>
						<th >&nbsp;</th>
						<th >&nbsp;</th>
						<th >&nbsp;</th>
						<th >&nbsp;</th>
						<th >&nbsp;</th>
						<th >&nbsp;</th>
						<th >&nbsp;</th>
						<th >&nbsp;</th>
						<th >&nbsp;</th>
						<th >&nbsp;</th>
						<th >&nbsp;</th>
						<th colspan="2">Per Psc Avg Rate :</th>
						<th  align="right" title="Total WO Value/Total Order Quantity"><?=number_format($tot_wo_value/$order_qty_total,2) ;?></th>
						<th >&nbsp;</th>
						<th >&nbsp;</th>
						<th >&nbsp;</th>
						<th >&nbsp;</th>
						<th >&nbsp;</th>
						<th >&nbsp;</th>
						<th >&nbsp;</th>
						<th >&nbsp;</th>
						<th >&nbsp;</th>
						<th >&nbsp;</th>
						<th >&nbsp;</th>
						<th >&nbsp;</th>
						<th >&nbsp;</th>
						<th >&nbsp;</th>
						<th >&nbsp;</th>
						<th >&nbsp;</th>
						<th >&nbsp;</th>
					</tfoot>

					<table cellspacing="0" cellpadding="0" border="1" class="rpt_table"  width="2940" rules="all">
					<tfoot>
						<th width="40">&nbsp;</th>
						<th width="110">&nbsp;</th>
						<th width="70">&nbsp;</th>
						<th width="70">&nbsp;</th>
						<th width="70">&nbsp;</th>
						<th width="110">&nbsp;</th>
						<th width="70">&nbsp;</th>
						<th width="70">&nbsp;</th>
						<th width="70">&nbsp;</th>
						<th width="90">&nbsp;</th>
						<th width="100">&nbsp;</th>						
						<th width="120">&nbsp;</th>
						<th width="150">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="60">Total:</th>
						<th width="80" id="value_tot_wo_qnty" align="right"><? echo number_format($tot_wo_qnty,2) ;?></th>
						<th width="70">&nbsp;</th>
						<th width="80" id="value_tot_wo_value" align="right"><? echo number_format($tot_wo_value,4) ;?></th>
						<th  width="70">&nbsp;</th>
						<th width="80" id="value_tot_precost_value" align="right"><? echo number_format($total_precost_value,2) ;?></th>
						<th width="80" id="value_tot_deference" align="right"><? echo number_format($total_deference,2) ;?></th>
						<th width="80" id="value_tot_deference_per" align="right"><? echo number_format(($total_deference/$total_precost_value)*100,2) ;?></th>
						<th width="80" >&nbsp;</th>
						<th width="80" id="value_tot_receive_qnty" align="right"><? echo number_format($tot_receive_qnty,2) ;?></th>
						<th width="80" id="value_tot_receive_value" align="right"><? echo number_format($tot_receive_value,2) ;?></th>
						<th width="80" id="value_tot_rcv_balance" align="right"><? echo number_format($tot_rcv_balance,2) ;?></th>
						<th width="80" id="value_tot_issue_qnty" align="right"><? echo number_format($tot_issue_qnty,2) ;?></th>
						<th width="80" id="value_tot_issue_value" align="right"><? echo number_format($tot_issue_value,2) ;?></th>
						<th width="80" id="value_tot_issue_balance" align="right"><? echo number_format($tot_issue_balance,2) ;?>32</th>
						<th width="120">&nbsp;</th>
						<th width="120">&nbsp;</th>
						<th width="120">&nbsp;</th>
						<th >&nbsp;</th>
					</tfoot>
					</table>
				</table>
			
			</div>
			<br>
			<table cellspacing="0" cellpadding="0" border="0" width="1750" rules="all">
				<tr>
					<td valign="top">
	
						<table cellspacing="0" cellpadding="0" border="1" class="rpt_table"  width="950" rules="all">
	
							<thead>
								<tr bgcolor="#CCCCCC">
									<td colspan="10" align="center" style="font-size:16px; font-weight:bold;">Item Group Wise Summary</td>
								</tr>
								<tr>
									<th width="50">SL</th>
									<th width="150">Item Name</th>
									<th width="100">WO Qty</th>
									<th width="100">WO Value</th>
									<th width="100">Precost value</th>
									<th width="100">Difference</th>
									<th width="50">%</th>
									<th width="100">Total Rcv Qty</th>
									<th width="100">Rcv Balance</th>
									<th>OTD %</th>
								</tr>
							</thead>
							<tbody>
								<?
								$k=1;
								//print_r($item_group_sammary);die;
								foreach($item_group_sammary as $item_grp_id=>$val)
								{
									$otd=(($val["ontimeRcv"]/$val["wo_qnty"])*100);
									$item_group_sammary[$item_grp_id]["otd"]=$otd;
								}
	
								foreach($item_group_sammary as $item_group=>$val)
								{
									$mid[$item_group]  = $val["otd"];
								}
								array_multisort($mid, SORT_DESC, $item_group_sammary);
	
								foreach($item_group_sammary as $val)
								{
									if ($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
									$rcv_bal=$val["wo_qnty"]-$val["rcv_qnty"];
								   //$otd=(($val["ontimeRcv"]/$val["wo_qnty"])*100);
									?>
									<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
										<td><? echo $k; ?></td>
										<td><? echo $trimsGroupArr[$val["trim_group"]];?></td>
										<td align="right"><? echo number_format($val["wo_qnty"],2); ?></td>
										<td align="right"><? echo number_format($val["wo_value"],4); ?></td>
										<td align="right"><? echo number_format($val["pre_value"],2); ?></td>
										<td align="right"><? $different=$val["pre_value"]-$val["wo_value"];echo number_format($different,2); ?></td>
										<td align="right" title="Difference/Pre Cost*100"><? echo number_format((($different/$val["pre_value"])*100),2); ?></td>
										<td align="right"><? echo number_format($val["rcv_qnty"],2); ?></td>
										<td align="right"><? echo number_format($rcv_bal,2); ?></td>
										<td align="right"><? echo number_format($val["otd"],2); ?></td>
									</tr>
									<?
									$i++;$k++;
									$sum_tot_wo_qnty+=$val["wo_qnty"];
									$sum_tot_wo_value+=$val["wo_value"];
									$sum_tot_pre_cost+=$val["pre_value"];
									$sum_tot_different+=$val["pre_value"]-$val["wo_value"];
									$sum_tot_ontime_rcv+=$val["ontimeRcv"];
									$sum_tot_rcv_qnty+=$val["rcv_qnty"];
									$sum_tot_rcv_bal+=$rcv_bal;//$val["rcv_bal"];
								}
								$tot_otd=(($sum_tot_ontime_rcv/$sum_tot_wo_qnty)*100);
								?>
							</tbody>
							<tfoot>
								<tr>
									<th>&nbsp;</th>
									<th>Total:</th>
									<th align="right"><? echo number_format($sum_tot_wo_qnty,2); ?></th>
									<th align="right"><? echo number_format($sum_tot_wo_value,4); ?></th>
									<th align="right"><? echo number_format($sum_tot_pre_cost,2); ?></th>
									<th align="right"><? echo number_format($sum_tot_differentt,2); ?></th>
									<th align="right"><? echo number_format((($sum_tot_different/$sum_tot_pre_cost)*100),2); ?></th>
									<th align="right"><? echo number_format($sum_tot_rcv_qnty,2); ?></th>
									<th align="right"><? echo number_format($sum_tot_rcv_bal,2); ?></th>
									<th align="right"><? echo number_format($tot_otd,2); ?></th>
								</tr>
							</tfoot>
						</table>
	
					</td>
					<td valign="top">&nbsp;</td>
					<td valign="top">
						<table cellspacing="0" cellpadding="0" border="1" class="rpt_table"  width="950" rules="all">
	
							<thead>
								<tr bgcolor="#CCCCCC">
									<td colspan="10" align="center" style="font-size:16px; font-weight:bold;">Supplier Wise Summary</td>
								</tr>
								<tr>
									<th width="50">SL</th>
									<th width="150">Supplier Name</th>
									<th width="100">WO Qty</th>
									<th width="100">WO Value</th>
									<th width="100">Precost value</th>
									<th width="100">Difference</th>
									<th width="50">%</th>
									<th width="100">Total Rcv Qty</th>
									<th width="100">Rcv Balance</th>
									<th>OTD %</th>
								</tr>
							</thead>
							<tbody>
								<?
								$m=1;
								foreach($supplier_wise_sammary as $supplier_id=>$val)
								{
									$otd=(($val["ontimeRcv"]/$val["wo_qnty"])*100);
									$supplier_wise_sammary[$supplier_id]["otd"]=$otd;
								}
	
								foreach($supplier_wise_sammary as $supplier_id=>$val)
								{
									$sid[$supplier_id]  = $val["otd"];
								}
								array_multisort($sid, SORT_DESC, $supplier_wise_sammary);
								foreach($supplier_wise_sammary as $val)
								{
									if ($m%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
									$rcv_bal=$val["wo_qnty"]-$val["rcv_qnty"];
									$otd=(($val["ontimeRcv"]/$val["wo_qnty"])*100);//$supplierArr[
									?>
									<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
										<td><? echo $m; ?></td>
										<td><? echo $val["supp_comp"]; ?></td>
										<td align="right"><? echo number_format($val["wo_qnty"],2); ?></td>
										<td align="right"><? echo number_format($val["wo_value"],4); ?></td>
										<td align="right"><? echo number_format($val["pre_value"],2); ?></td>
										<td align="right"><? $different=$val["pre_value"]-$val["wo_value"];echo number_format($different,2); ?></td>
										<td align="right"><? echo number_format((($different/$val["pre_value"])*100),2); ?></td>
										<td align="right"><? echo number_format($val["rcv_qnty"],2); ?></td>
										<td align="right"><? echo number_format($rcv_bal,2); ?></td>
										<td align="right"><? echo number_format($val["otd"],2); ?></td>
									</tr>
									<?
									$i++;$m++;
									$sup_tot_wo_qnty+=$val["wo_qnty"];
									 $sup_tot_wo_value+=$val["wo_value"];
									  $sup_tot_pre_value+=$val["pre_value"];
									   $sup_tot_different+=$different;
									   // $sup_tot_wo_qnty+=$val["wo_qnty"];
										
									$sup_tot_ontime_rcv+=$val["ontimeRcv"];
									$sup_tot_rcv_qnty+=$val["rcv_qnty"];
									$sup_tot_rcv_bal+=$rcv_bal;//$val["rcv_bal"];
								}
								$sup_tot_otd=(($sup_tot_ontime_rcv/$sup_tot_wo_qnty)*100);
								?>
							</tbody>
							<tfoot>
								<tr>
									<th>&nbsp;</th>
									<th>Total:</th>
									<th align="right"><? echo number_format($sup_tot_wo_qnty,2); ?></th>
									<th align="right"><? echo number_format($sup_tot_wo_value,4); ?></th>
									<th align="right"><? echo number_format($sup_tot_pre_value,2); ?></th>
									<th align="right"><? echo number_format($sup_tot_different,2); ?></th>
									<th align="right"><? echo number_format((($sup_tot_different/$sup_tot_pre_value)*100),2); ?></th>
									<th align="right"><? echo number_format($sup_tot_rcv_qnty,2); ?></th>
									<th align="right"><? echo number_format($sup_tot_rcv_bal,2); ?></th>
									<th align="right"><? echo number_format($sup_tot_otd,2); ?></th>
								</tr>
							</tfoot>
						</table>
					</td>
				</tr>
			</table>
		</fieldset>
		<?
	}
	else if($cbo_category==25) //Emblishment
	{
		$color_namerArr = return_library_array("select id,color_name from lib_color ","id","color_name");
		$sql_result=sql_select($sql);
			$all_po_id="";
			foreach($sql_result as $row)
			{
				if($all_po_id=="") $all_po_id=$row[csf("po_id")];else $all_po_id.=",".$row[csf("po_id")];
				//$emblish_arr[$row[csf("po_id")]]["po_id"]=$row[csf("po_id")];
			}
			//echo $all_po_id.'ds';
			$poIds=chop($all_po_id,','); $po_cond_for_in=""; //$order_cond1=""; $order_cond2=""; $precost_po_cond="";
			$po_ids=count(array_unique(explode(",",$all_po_id)));
			if($db_type==2 && $po_ids>1000)
			{
			$po_cond_for_in=" and (";
			$po_cond_for_in2=" and (";
			$poIdsArr=array_chunk(explode(",",$poIds),999);
			foreach($poIdsArr as $ids)
			{
			$ids=implode(",",$ids);
			//$poIds_cond.=" po_break_down_id in($ids) or ";
			$po_cond_for_in.=" b.id in($ids) or"; 
			$po_cond_for_in2.=" d.po_break_down_id in($ids) or"; 
			}
			$po_cond_for_in=chop($po_cond_for_in,'or ');
			$po_cond_for_in.=")";
			}
			else
			{
			$po_ids=implode(",",(array_unique(explode(",",$all_po_id))));
			$po_cond_for_in=" and b.id in($po_ids)";
		//	$po_cond_for_in2=" and d.po_break_down_id  in($po_ids)";
			}
			if(!empty($all_po_id))
			{
				$tna_po=$po_cond_for_in;
			}
			

			$sql_date="SELECT a.task_finish_date,a.po_number_id,a.task_number from tna_process_mst a, wo_po_break_down b where a.po_number_id = b.id and a.status_active=1 and b.is_deleted=0 and A.TASK_NUMBER in (70,71) $tna_po  group by a.task_finish_date,a.po_number_id,a.task_number,a.task_category";
			//echo $sql_date;

			$date_arr=array();

			$sql_result=sql_select($sql_date);
			foreach ($sql_result as $row) 
			{
				$date_arr[$row[csf('po_number_id')]][$row[csf('task_number')]]=$row[csf('task_finish_date')];
			}
			$order_sql=sql_select("select a.id as job_id, a.job_no, a.job_no_prefix_num, $select_year, a.style_ref_no, b.id as po_id, b.po_number,a.dealing_marchant,a.team_leader,b.grouping  from  wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and b.status_active=1 $po_cond_for_in");
			//grouping field
			$order_data_arr=array();
			//echo $order_sql[csf("po_id")];die;
			$i = 0;
			foreach($order_sql as $row)
			{
			$order_data_arr[$row[csf("po_id")]]["po_id"]=$row[csf("po_id")];
			$order_data_arr[$row[csf("po_id")]]["job_id"]=$row[csf("job_id")];
			$order_data_arr[$row[csf("po_id")]]["job_no"]=$row[csf("job_no")];
			$order_data_arr[$row[csf("po_id")]]["job_no_prefix_num"]=$row[csf("job_no_prefix_num")];
			$order_data_arr[$row[csf("po_id")]]["job_year"]=$row[csf("job_year")];
			$order_data_arr[$row[csf("po_id")]]["style_ref_no"]=$row[csf("style_ref_no")];
			$order_data_arr[$row[csf("po_id")]]["po_number"]=$row[csf("po_number")];
			$order_data_arr[$row[csf("po_id")]]["order_qty"]=$row[csf("po_quantity")];
			$order_data_arr[$row[csf("po_id")]]["grouping"]=$row[csf("grouping")];
			$order_data_arr[$row[csf('po_id')]]['team_leader']=$row[csf('team_leader')];
			$order_data_arr[$row[csf('po_id')]]['dealing_marchant']=$row[csf('dealing_marchant')];
			//echo $order_data_arr[$row[csf("po_id")]]["po_number"];
			}
	
			$condition= new condition();
			$condition->company_name("=$cbo_company");
			if(str_replace("'","",$cbo_buyer)>0){
			$condition->buyer_name("=$cbo_buyer");
			}
			
			if($all_po_id!='')
			{
			$po_ids=implode(",",(array_unique(explode(",",$all_po_id))));
			$condition->po_id_in("$po_ids"); 
			}
			
			$condition->init();
			$emblishment= new emblishment($condition);
			$wash= new wash($condition);
			//echo $wash->getQuery();die;
			//echo $emblishment->getQuery(); die;
			$emblishment_qty_arr=$emblishment->getQtyArray_by_orderEmbnameAndEmbtype();
			$emblishment_amt_arr=$emblishment->getAmountArray_by_orderEmbnameAndEmbtype();
			$emblishment_wash_qty_arr=$wash->getQtyArray_by_orderEmbnameAndEmbtype();
			$emblishment_wash_amt_arr=$wash->getAmountArray_by_orderEmbnameAndEmbtype();
			//print_r($emblishment_wash_qty_arr);	
			$emb_sql=sql_select( "select c.id,c.emb_type,c.emb_name from  wo_po_break_down b,wo_pre_cost_embe_cost_dtls c where b.job_id=c.job_id and c.status_active=1 $po_cond_for_in");
			$pre_embl_arr=array();
			foreach($emb_sql as $row)
			{	
				$pre_embl_arr[$row[csf("id")]]["emb_type"]=$row[csf("emb_type")];
				$pre_embl_arr[$row[csf("id")]]["emb_name"]=$row[csf("emb_name")];
			}
			unset($emb_sql);
			
	?>
    <fieldset>
        <table width="2880"  cellspacing="0" >
            <tr class="form_caption" style="border:none;">
                <td align="center" style="border:none; font-size:18px;" colspan="22">
                    <? echo $company_library[str_replace("'","",$cbo_company_id)]; ?>
                </td>
            </tr>
            <tr class="form_caption" style="border:none;">
                <td align="center" style="border:none;font-size:16px; font-weight:bold"  colspan="25"> <? echo $report_title ;?></td>
            </tr>
            <tr class="form_caption" style="border:none;">
                <td align="center" style="border:none;font-size:12px; font-weight:bold"  colspan="25"> <? echo "From ".change_date_format($date_from)." To ".change_date_format($date_to);?></td>
            </tr>
        </table>
        <table width="2540" cellspacing="0" border="1" class="rpt_table" rules="all">
            <thead>
                <th width="40">SL</th>
                <th width="110">WO No</th>
                <th width="70">Approved Date</th>
                <th width="110">Internal Ref No</th>
                <th width="70">WO Date</th>
                <th width="70">Delivery Date</th>
                <th width="70">Lead Time</th>
                <th width="90">WO Type</th>
                <th width="100">Supplier</th>
                <th width="100">Buyer</th>
                <th width="50">Job Year</th>
                <th width="50">Job No.</th>
                <th width="100">Style Ref.</th>
                <th width="100">Order No</th>
				<th width="100">Order qty</th>
                <th width="120">Emblishment Name</th>
                <th width="150">Emblishment Type</th>
                <th width="100">Item Category</th>
                <th width="60">Color</th>
                <th width="80">WO Qty</th>
                <th width="70">WO Unit price</th>
                <th width="80">WO value</th>
                <th width="70">Budget Unit price</th>
                <th width="80">Precost value</th>
                <th width="80" title="(Precost value - WO value)">Defference</th>
                <th width="80" title="(Defference / Precost value)*100">Defference %</th>
                
                <th width="120">Dealing Merchant</th>
                <th width="120">Team Leader</th>
                <th width="120">User Name</th>
                <th >Remarks</th>
            </thead>
        </table>
        <div style="max-height:350px; overflow-y:scroll; width:2660px" id="scroll_body" >
            <table cellspacing="0" cellpadding="0" border="1" class="rpt_table"  width="2640" rules="all" id="table_body" >
            <?
			
			$i=1;$item_group_sammary=array();
			$total_precost_value =0; $total_deference=0; $total_deference_per =0;
			foreach($sql_result as $row)
				{
					// echo $buyer_count."<br>";
					$job_no=$order_data_arr[$row[csf("po_id")]]["job_no_prefix_num"];
						$row_count[$row[csf("book_mst_id")]]+=1;
						$row_count2[$row[csf("book_mst_id")]][$job_no][$row[csf("po_id")]]+=1;
				}
			
				$po_id_arr=array();

			foreach($sql_result as $row)
			{
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				
				if ($row[csf("wo_type")]==6)
					{
						if ($row[csf("is_short")]==1)
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
					
				$lead_time=datediff("d", $row[csf("wo_date")], $row[csf("delivery_date")]);
				$emb_type_id=$pre_embl_arr[$row[csf("pre_cost_fabric_cost_dtls_id")]]["emb_type"];
				$emb_name=$pre_embl_arr[$row[csf("id")]]["emb_name"]=$pre_embl_arr[$row[csf("pre_cost_fabric_cost_dtls_id")]]["emb_name"];
				if($emb_name==1)//Print
				{
				$emb_type=$emblishment_print_type[$pre_embl_arr[$row[csf("pre_cost_fabric_cost_dtls_id")]]["emb_type"]];
				}
				elseif($emb_name==2)//EMBRO
				{
				$emb_type=$emblishment_embroy_type[$pre_embl_arr[$row[csf("pre_cost_fabric_cost_dtls_id")]]["emb_type"]];
				}
				elseif($emb_name==4)//Spcial
				{
				$emb_type=$emblishment_spwork_type[$pre_embl_arr[$row[csf("pre_cost_fabric_cost_dtls_id")]]["emb_type"]];
				}
				elseif($emb_name==5)//Gmt
				{
				$emb_type=$emblishment_gmts_type[$pre_embl_arr[$row[csf("pre_cost_fabric_cost_dtls_id")]]["emb_type"]];
				}
				elseif($emb_name==3)//Wash
				{
				$emb_type=$emblishment_wash_type[$pre_embl_arr[$row[csf("pre_cost_fabric_cost_dtls_id")]]["emb_type"]];
				}
				
				if($emb_name!=3)//Without Wash
				{
				 $precost_qty=$emblishment_qty_arr[$row[csf("po_id")]][$emb_name][$emb_type_id];
				 $precost_value=$emblishment_amt_arr[$row[csf("po_id")]][$emb_name][$emb_type_id];
				}
				else 
				{
				 $precost_qty=$emblishment_wash_qty_arr[$row[csf("po_id")]][$emb_name][$emb_type_id];
				 $precost_value=$emblishment_wash_amt_arr[$row[csf("po_id")]][$emb_name][$emb_type_id];
				}
				$currency_id=$row[csf("currency_id")];
				if($currency_id==1) //TK exchange_rate
				{
					$wo_rate=$row[csf("rate")]/$row[csf("exchange_rate")];
				}
				else
				{
					$wo_rate=$row[csf("rate")];
				}
				
				
				if($row[csf("wo_qnty")]>0 && $emb_name>0)
				{
					$item_group_sammary[$emb_name]["wo_qnty"]+=$row[csf("wo_qnty")];
					$item_group_sammary[$emb_name]["wo_value"]+=$row[csf("wo_qnty")]*$wo_rate;
					$item_group_sammary[$emb_name]["pre_value"]+=$precost_value;
					//echo $precost_value.',';
					
					$item_group_sammary[$emb_name]["ontimeRcv"]+=$ontimeRcv;
					$item_group_sammary[$emb_name]["rcv_qnty"]+=$rcv_qnty;
					$item_group_sammary[$emb_name]["emblishment_name"]=$emb_name;
				}

				if($row[csf("supplier_id")]>0 && $row[csf("wo_qnty")]>0)
				{
					$supplier_wise_sammary[$row[csf("supplier_id")]]["wo_qnty"]+=$row[csf("wo_qnty")];
					$supplier_wise_sammary[$row[csf("supplier_id")]]["wo_value"]+=$row[csf("wo_qnty")]*$wo_rate;
					$supplier_wise_sammary[$row[csf("supplier_id")]]["pre_value"]+=$precost_value;
					
					$supplier_wise_sammary[$row[csf("supplier_id")]]["ontimeRcv"]+=$ontimeRcv;
					$supplier_wise_sammary[$row[csf("supplier_id")]]["rcv_qnty"]+=$rcv_qnty;
					$supplier_wise_sammary[$row[csf("supplier_id")]]["supplier_id"]=$row[csf("supplier_id")];
				}
				if($row[csf("is_approved")]==1)
				{
					$approved_date=$row[csf("update_date")];
					$approved_date = date('Y-m-d', strtotime($approved_date));
				}
				else $approved_date='';
				
				if($row[csf("pay_mode")]==3 || $row[csf("pay_mode")]==5) $supplier_com=$company_library[$row[csf("supplier_id")]]; else $supplier_com=$supplierArr[$row[csf("supplier_id")]];
				
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
                	<td width="40" align="center"><? echo $i;?></td>
                    <td width="110"><p><? echo $row[csf("booking_no")]; ?></td>
                    <td width="70"><p><? echo $approved_date; ?></td>
                    <td width="110"><p><? echo $order_data_arr[$row[csf("po_id")]]["grouping"]; ?></td>
                    <td width="70" align="center"><p><? echo change_date_format($row[csf("wo_date")]); ?>&nbsp;</p></td>
                    <td width="70" align="center"><p><?
                    	$date_del="";
						if($trim_type[$row[csf('trim_group')]]==1)
						{
							$date_del=$date_arr[$row[csf('po_id')]][70];
						}
						else{
							$date_del=$date_arr[$row[csf('po_id')]][71];
						}
						if(empty($date_del))
						{
							$date_del=$row[csf("delivery_date")];
						}
				 		echo change_date_format($row[csf("delivery_date")]); 
                     
                      ?>&nbsp;</p></td>
                    <td width="70" align="center"><p><? if($lead_time>0 && $lead_time<2) echo $lead_time." Day"; elseif($lead_time>1) echo $lead_time." Days"; else echo "0 Day"; ?>&nbsp;</p></td>
                    <td width="90"><p><?  echo $wo_type; ?>&nbsp;</p></td>
                    <td width="100"><p><? echo $supplier_com; ?>&nbsp;</p></td>

					<?if($booking_mst_id!=$row[csf("book_mst_id")]){?>
						<td width="100" rowspan="<?=$row_count[$row[csf("book_mst_id")]]?>"><p><? echo $buyerArr[$row[csf("buyer_id")]]; ?>&nbsp;</p></td>
						<td width="50" align="center"rowspan="<?=$row_count[$row[csf("book_mst_id")]]?>"><p><? echo $order_data_arr[$row[csf("po_id")]]["job_year"]; ?>&nbsp;</p></td>
						<td width="50" align="center"rowspan="<?=$row_count[$row[csf("book_mst_id")]]?>"><p><? echo $order_data_arr[$row[csf("po_id")]]["job_no_prefix_num"]; ?>&nbsp;</p></td>
						<td width="100"rowspan="<?=$row_count[$row[csf("book_mst_id")]]?>"><p><? echo $order_data_arr[$row[csf("po_id")]]["style_ref_no"]; ?>&nbsp;</p></td>
					
						<?	
					
						}
						if($po_id_arr[$row[csf("book_mst_id")]][$job_no][$row[csf("po_id")]]!=$row[csf("po_id")]){
							$job_no=$order_data_arr[$row[csf("po_id")]]["job_no_prefix_num"];	
						?>
						<td width="100" rowspan="<?=$row_count2[$row[csf("book_mst_id")]][$job_no][$row[csf("po_id")]]?>" style="word-break:break-all"><p><? echo $order_data_arr[$row[csf("po_id")]]["po_number"]; ?>&nbsp;</p></td>
						<td width="100" rowspan="<?=$row_count2[$row[csf("book_mst_id")]][$job_no][$row[csf("po_id")]]?>"  style="word-break:break-all"><p><? echo $order_data_arr[$row[csf("po_id")]]["order_qty"]; 
							$order_qty_total +=$order_data_arr[$row[csf("po_id")]]["order_qty"];
						?>&nbsp;</p></td>
						<?}?>
      
                    <td width="120"><p><? echo $emblishment_name_array[$emb_name]; ?>&nbsp;</p></td>
                    <td width="150" style="word-break:break-all"><p><?
					
					echo $emb_type; ?>&nbsp;</p></td>
                    <td width="100"><p><? echo $item_category[$row[csf("item_category")]]; ?>&nbsp;</p></td>
                    <td width="60"><p><? echo $color_namerArr[$row[csf("gmts_color_id")]]; ?>&nbsp;</p></td>
                    <td width="80" align="right"><p><? echo number_format($row[csf("wo_qnty")],2,'.',''); ?></p></td>
                    <td width="70" align="right" title="USD Rate"><p><? echo number_format($wo_rate,2,'.',''); ?>&nbsp;</p></td>
                    <td width="80" align="right" ><p><? $wo_value=$row[csf("wo_qnty")]*$wo_rate; echo number_format($wo_value,4,'.',''); ?></p></td>
                    <td width="70" align="right" title="Pre Embl Qty=(<? echo $precost_qty;?>) "><p><? echo number_format($precost_value/$precost_qty,2,'.',''); ?></p></td>
                    <td width="80" align="right"><p><? //$precost_value=$row[csf("wo_qnty")]*$pre_cost_data_arr[$row[csf("po_id")]][$row[csf("trim_group")]];
					 $pre_cost_value=$precost_value;//$row[csf("wo_qnty")]*($precost_value/$precost_qty);
					 echo number_format($pre_cost_value,2); ?></p></td>
                    <td width="80" align="right"><p><? $deference = $pre_cost_value-$wo_value; echo number_format($deference,2,'.',''); ?></p></td>
                    <td width="80" align="right"><p><? $deference_per = ($deference/$pre_cost_value)*100; echo number_format($deference_per,2,'.',''); ?></p></td>
                   
                    <td width="120"><p><? echo $lib_team_member_arr[$order_data_arr[$row[csf("po_id")]]['dealing_marchant']]; ?></p></td>
                    <td width="120"><p><? echo $lib_team_leader_name_arr[$order_data_arr[$row[csf("po_id")]]['team_leader']] ; ?></p></td>
                    <td width="120" align="center"><p><? echo $userArr[$row[csf("inserted_by")]]; ?></p></td>
                    <td ><p><? echo $row[csf("remarks")]; ?> &nbsp;</p></td>
				</tr>
				<?
				$tot_wo_qnty+=$row[csf("wo_qnty")];
				$tot_wo_value+=$wo_value;
				$tot_receive_qnty+=$rcv_qnty;
				$tot_receive_value+=$rcv_value;
				$tot_rcv_balance+=$rcv_balance;
				$total_ontime_rcv+=$ontimeRcv;
				$total_precost_value +=$pre_cost_value;
				$total_deference += $deference;
				//$total_deference_per +=$deference_per;

				$i++;
				$booking_mst_id=$row[csf("book_mst_id")];
				$job_no=$order_data_arr[$row[csf("po_id")]]["job_no_prefix_num"];	
				$po_id_arr[$row[csf("book_mst_id")]][$job_no][$row[csf("po_id")]]=$row[csf("po_id")];
			}
			?>
			 <tfoot>
                    <th >&nbsp;</th>
                    <th >&nbsp;</th>
                    <th >&nbsp;</th>
                    <th >&nbsp;</th>
                    <th >&nbsp;</th>
                    <th >&nbsp;</th>
                    <th >&nbsp;</th>
                    <th >&nbsp;</th>
                    <th >&nbsp;</th>
                    <th >&nbsp;</th>
                    <th >&nbsp;</th>
                    <th >&nbsp;</th>
					<th >&nbsp;</th>
					<th >&nbsp;</th>
                    <th >&nbsp;</th>
                    <th >&nbsp;</th>
					<th >&nbsp;</th>                   
					<th colspan="2">Per Psc Avg Rate :</th>
                    <th align="right"><? echo number_format($tot_wo_qnty/$order_total_qty,2) ;?></th>
                    <th >&nbsp;</th>
                    <th >&nbsp;</th>
                    <th >&nbsp;</th>					
					<th >&nbsp;</th>
					<th >&nbsp;</th>
                    <th >&nbsp;</th>                    
                    <th >&nbsp;</th>
                    <th >&nbsp;</th>
                    <th >&nbsp;</th>
                    <th >&nbsp;</th>
                </tfoot>
                <tfoot>
                    <th >&nbsp;</th>
                    <th >&nbsp;</th>
                    <th >&nbsp;</th>
                    <th >&nbsp;</th>
                    <th >&nbsp;</th>
                    <th >&nbsp;</th>
                    <th >&nbsp;</th>
                    <th >&nbsp;</th>
                    <th >&nbsp;</th>
                    <th >&nbsp;</th>
                    <th >&nbsp;</th>
                    <th >&nbsp;</th>
                    <th >Total Order Qty</th>
                    <th ><?=$order_total_qty ?></th>
                    <th >&nbsp;</th>
                    <th >&nbsp;</th>
					<th >&nbsp;</th>
                    <th >&nbsp;</th>
                    <th >Total:</th>
                    <th id="value_tot_wo_qnty" align="right"><? echo number_format($tot_wo_qnty,2) ;?></th>
                    <th >&nbsp;</th>
                    <th id="value_tot_wo_value" align="right"><? echo number_format($tot_wo_value,4) ;?></th>
                    <th >&nbsp;</th>
					
                    <th id="value_tot_precost_value" align="right"><? echo number_format($total_precost_value,2) ;?></th>
                    <th id="value_tot_deference" align="right"><? echo number_format($total_deference,2) ;?></th>
                    <th id="value_tot_deference_per" align="right"><? echo number_format(($total_deference/$total_precost_value)*100,2) ;?></th>
                    
                    <th >&nbsp;</th>
                    <th >&nbsp;</th>
                    <th >&nbsp;</th>
                    <th >&nbsp;</th>
                </tfoot>
            </table>
        </div>
        <br>
        <table cellspacing="0" cellpadding="0" border="0" width="1500" rules="all">
        	<tr>
            	<td valign="top">

                    <table cellspacing="0" cellpadding="0" border="1" class="rpt_table"  width="860" rules="all">

                        <thead>
                        	<tr bgcolor="#CCCCCC">
                                <td colspan="8" align="center" style="font-size:16px; font-weight:bold;">Emblishment Name Wise Summary</td>
                            </tr>
                            <tr>
                                <th width="50">SL</th>
                                <th width="150">Item Name</th>
                                <th width="100">WO Qty</th>
                                <th width="100">WO Value</th>
                                <th width="100">Precost value</th>
                                <th width="100">Difference</th>
                                <th width="60">%</th>
                               
                            </tr>
                        </thead>
                        <tbody>
                            <?
                            $k=1;
							//print_r($item_group_sammary);die;
							foreach($item_group_sammary as $item_grp_id=>$val)
                            {
                               	$otd=(($val["ontimeRcv"]/$val["wo_qnty"])*100);
								$item_group_sammary[$item_grp_id]["otd"]=$otd;
							}

							foreach($item_group_sammary as $item_group=>$val)
							{
								$mid[$item_group]  = $val["otd"];
							}
							array_multisort($mid, SORT_DESC, $item_group_sammary);

                            foreach($item_group_sammary as $val)
                            {
                                if ($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                               // $rcv_bal=$val["wo_qnty"]-$val["rcv_qnty"];
                               //$otd=(($val["ontimeRcv"]/$val["wo_qnty"])*100);
                                ?>
                                <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
                                    <td><? echo $k; ?></td>
                                    <td>
									<?
									//echo $trimsGroupArr[$item_group];
									echo $emblishment_name_array[$val["emblishment_name"]];
									?></td>
                                    <td align="right"><? echo number_format($val["wo_qnty"],2); ?></td>
                                     <td align="right"><? echo number_format($val["wo_value"],4); ?></td>
                                     <td align="right"><? echo number_format($val["pre_value"],2); ?></td>
                                     <td align="right"><? $difference=$val["pre_value"]-$val["wo_value"];echo number_format($difference,2); ?></td>
                                     <td align="right" title="Difference/Pre value*100"><? echo number_format((($difference/$val["pre_value"])*100),2); ?></td>
                                   
                                </tr>
                                <?
                                $i++;$k++;
                                $sum_tot_wo_qnty+=$val["wo_qnty"];
								 $sum_tot_wo_value+=$val["wo_value"];
								  $sum_tot_pre_value+=$val["pre_value"];
								   $sum_tot_difference+=$difference;
								  
                                $sum_tot_ontime_rcv+=$val["ontimeRcv"];
                                $sum_tot_rcv_qnty+=$val["rcv_qnty"];
                                $sum_tot_rcv_bal+=$rcv_bal;//$val["rcv_bal"];
                            }
                            $tot_otd=(($sum_tot_ontime_rcv/$sum_tot_wo_qnty)*100);
                            ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th>&nbsp;</th>
                                <th>Total:</th>
                                <th align="right"><? echo number_format($sum_tot_wo_qnty,2); ?></th>
                                <th align="right"><? echo number_format($sum_tot_wo_value,4); ?></th>
                                <th align="right"><? echo number_format($sum_tot_pre_value,2); ?></th>
                                <th align="right"><? echo number_format($sum_tot_difference,2); ?></th>
                                <th align="right"><? echo number_format((($sum_tot_difference/$sum_tot_pre_value)*100),2); ?></th>
                                
                            </tr>
                        </tfoot>
                    </table>

                </td>
                <td valign="top">&nbsp;</td>
                <td valign="top">
                	<table cellspacing="0" cellpadding="0" border="1" class="rpt_table"  width="760" rules="all">

                        <thead>
                        	<tr bgcolor="#CCCCCC">
                                <td colspan="8" align="center" style="font-size:16px; font-weight:bold;">Supplier Wise Summary</td>
                            </tr>
                            <tr>
                                <th width="50">SL</th>
                                <th width="150">Supplier Name</th>
                                <th width="100">WO Qty</th>
                                <th width="100">WO Value</th>
                                <th width="100">Precost value</th>
                                <th width="100">Difference</th>
                                <th width="60">%</th>
                                
                               
                            </tr>
                        </thead>
                        <tbody>
                            <?
                            $m=1;
							foreach($supplier_wise_sammary as $supplier_id=>$val)
                            {
                                $otd=(($val["ontimeRcv"]/$val["wo_qnty"])*100);
								$supplier_wise_sammary[$supplier_id]["otd"]=$otd;
							}

							foreach($supplier_wise_sammary as $supplier_id=>$val)
							{
								$sid[$supplier_id]  = $val["otd"];
							}
							array_multisort($sid, SORT_DESC, $supplier_wise_sammary);
                            foreach($supplier_wise_sammary as $val)
                            {
                                if ($m%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                                $rcv_bal=$val["wo_qnty"]-$val["rcv_qnty"];
                                $otd=(($val["ontimeRcv"]/$val["wo_qnty"])*100);
                                ?>
                                <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
                                    <td><? echo $m; ?></td>
                                    <td><? echo $supplierArr[$val["supplier_id"]]; ?></td>
                                    <td align="right"><? echo number_format($val["wo_qnty"],2); ?></td>
                                    
                                    <td align="right"><? echo number_format($val["wo_value"],2); ?></td>
                                    <td align="right"><? echo number_format($val["pre_value"],2); ?></td>
                                    <td align="right"><? $diference=$val["wo_value"]-$val["pre_value"];echo number_format($diference,2); ?></td>
                                    <td align="right"><? echo number_format($val["wo_qnty"],2); ?></td>
                                   
                                </tr>
                                <?
                                $i++;$m++;
                                $sup_tot_wo_qnty+=$val["wo_qnty"];
								$sup_tot_wo_value+=$val["wo_value"];
								$sup_tot_pre_value+=$val["pre_value"];
								$sup_tot_diference+=$diference;
								
                                $sup_tot_ontime_rcv+=$val["ontimeRcv"];
                                $sup_tot_rcv_qnty+=$val["rcv_qnty"];
                                $sup_tot_rcv_bal+=$rcv_bal;//$val["rcv_bal"];
                            }
                            $sup_tot_otd=(($sup_tot_ontime_rcv/$sup_tot_wo_qnty)*100);
                            ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th>&nbsp;</th>
                                <th>Total:</th>
                                <th align="right"><? echo number_format($sup_tot_wo_qnty,2); ?></th>
                                <th align="right"><? echo number_format($sup_tot_wo_value,2); ?></th>
                                <th align="right"><? echo number_format($sup_tot_pre_value,2); ?></th>
                                <th align="right"><? echo number_format($sup_tot_diference,2); ?></th>
                                <th align="right"><? echo number_format((($sup_tot_diference/$sup_tot_pre_value)*100),2); ?></th>
                                
                               
                            </tr>
                        </tfoot>
                    </table>
                </td>
            </tr>
        </table>
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
if ($action=="report_generate_4")
{
	//extract($_REQUEST);
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$cbo_company=str_replace("'","",$cbo_company_id);
	$wo_type=str_replace("'","",$cbo_wo_type);
	$cbo_category=str_replace("'","",$cbo_category_id);
	$cbo_buyer=str_replace("'","",$cbo_buyer_id);
	$year_id=str_replace("'","",$cbo_year_id);
	$wo_no=str_replace("'","",$txt_wo_no);
	$wo_id=str_replace("'","",$hidd_wo_id);
	$date_from=str_replace("'","",$txt_date_from);
	$date_to=str_replace("'","",$txt_date_to);
	$cbo_supplier=str_replace("'","",$cbo_supplier);
	$txt_date_category=str_replace("'","",$txt_date_category);
	$hidd_item_id=str_replace("'","",$hidd_item_id);
	$txt_item_no=str_replace("'","",$txt_item_no);
	$cbo_search_type=str_replace("'","",$cbo_search_type);
	$txt_search_common=str_replace("'","",$txt_search_common);
	$txt_job_po_id=str_replace("'","",$txt_job_po_id);
	$cbo_within_group_id=str_replace("'","",$cbo_within_group_id);

	
	if($cbo_search_type==1)
	{
		if($txt_search_common!="" && $txt_job_po_id!="")
		{
			$job_cond="and a.id in($txt_job_po_id)";
		}
		else if($txt_search_common!="" && $txt_job_po_id=="")
		{
			$job_cond="and a.job_no_prefix_num=$txt_search_common";

		}
	}
	else if($cbo_search_type==2)
	{
		if($txt_search_common!="" && $txt_job_po_id!="")
		{
			$job_cond="and a.id in($txt_job_po_id)";
		}
		else if($txt_search_common!="" && $txt_job_po_id=="")
		{
			$job_cond="and a.style_ref_no='$txt_search_common'";

		}
	}else if($cbo_search_type==3)
	{
		
			if ($txt_search_common=="") $txt_search_common=""; else $internal_ref_cond=" and b.grouping='".trim($txt_search_common)."' ";
	
	
	}
	
	if($cbo_within_group_id==1)
	{
		$within_cond="and a.pay_mode in(3,5)";
	} 
	else if($cbo_within_group_id==2)
	{ 
	  $within_cond="and a.pay_mode not in(3,5)";
	}
	else $within_cond="";
	//die;

	$sql_cond="";

	if($db_type==0)
	{
		if ($year_id==0) $year_id_cond=""; else $year_id_cond=" and YEAR(a.insert_date)=$year_id";
	}
	elseif($db_type==2)
	{
		if ($year_id==0) $year_id_cond=""; else $year_id_cond=" and TO_CHAR(a.insert_date,'YYYY')=$year_id";
	}

	if ($cbo_company!=0) $sql_cond=" and a.company_id=$cbo_company";
	if ($cbo_buyer!=0) $sql_cond.=" and a.buyer_id=$cbo_buyer";
	if ($cbo_buyer!=0) $buyer_id_cond=" and a.buyer_name=$cbo_buyer";else $buyer_id_cond="";
	if ($cbo_category!=0)  $sql_cond.=" and a.item_category=$cbo_category";
	if ($wo_no!="")  $sql_cond.=" and a.booking_no like '%$wo_no%'";
	if ($cbo_supplier>0)  $sql_cond.=" and a.supplier_id=$cbo_supplier";

	if ($hidd_item_id=="") $item_id=""; else $item_id=" and b.trim_group in ( $hidd_item_id )";

	/*if ($wo_type==1 || $wo_type==2)  $sql_cond.=" and a.booking_type in (1,2) and a.is_short='$wo_type'";
	if ($wo_type==3) $sql_cond.="  and a.booking_type=4";*/

	//echo $file_no_cond.'=='.$internal_ref_cond;die;
	if($txt_date_category==1)
	{
		if($db_type==0)
		{
			if( $date_from=="" && $date_to=="" ) $booking_date_cond=""; else $booking_date_cond=" and a.booking_date between '".$date_from."' and '".$date_to."'";
		}
		if($db_type==2)
		{
			if( $date_from=="" && $date_to=="" ) $booking_date_cond=""; else $booking_date_cond=" and a.booking_date between '".change_date_format($date_from,'dd-mm-yyyy','-',1)."' and '".change_date_format($date_to,'dd-mm-yyyy','-',1)."'";
		}
	}
	else
	{
		if($db_type==0)
		{
			if( $date_from=="" && $date_to=="" ) $booking_date_cond=""; else $booking_date_cond=" and b.delivery_date between '".$date_from."' and '".$date_to."'";
		}
		if($db_type==2)
		{
			if( $date_from=="" && $date_to=="" ) $booking_date_cond=""; else $booking_date_cond=" and b.delivery_date between '".change_date_format($date_from,'dd-mm-yyyy','-',1)."' and '".change_date_format($date_to,'dd-mm-yyyy','-',1)."'";
		}
	}


	if($db_type==0) $select_year="year(a.insert_date) as job_year"; else if($db_type==2) $select_year="to_char(a.insert_date,'YYYY') as job_year";

	$lib_team_name_arr=return_library_array( "select id, team_name from lib_marketing_team", "id", "team_name"  );
	$lib_team_leader_name_arr=return_library_array( "select id, team_leader_name from lib_marketing_team", "id", "team_leader_name"  );
	$lib_team_member_arr = return_library_array("select id, team_member_name from lib_mkt_team_member_info", "id", "team_member_name");
	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name");
	$supplierArr=return_library_array( "select id,supplier_name from lib_supplier", "id", "supplier_name");
	$buyerArr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name");
	$trimsGroupArr=return_library_array( "select id, item_name from lib_item_group", "id", "item_name");
	$userArr=return_library_array( "select id, user_name from user_passwd", "id", "user_name");
	$trim_type= return_library_array("select id, trim_type from lib_item_group",'id','trim_type');
	//$nonStyleArr=return_library_array( "select id, style_ref_no from sample_development_mst", "id", "style_ref_no");
		if($txt_search_common!="")
		{
		$sql_po=sql_select("select  b.id,b.job_no_mst  from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst   and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1  and a.company_name=$cbo_company $buyer_id_cond  $job_cond $internal_ref_cond group by   b.id,b.job_no_mst order by b.id,b.job_no_mst");
		//echo "select  b.id,b.job_no_mst  from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst   and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1  and a.company_name=$cbo_company $buyer_id_cond  $job_cond group by   b.id,b.job_no_mst order by b.id,b.job_no_mst";die;
		foreach( $sql_po as $row)
		{
			//$sql_po_qty_country_wise_arr[$row[csf('id')]][$row[csf('country_id')]]=$row[csf('order_quantity_set')];
			$po_id_arr[$row[csf('id')]]=$row[csf('id')];
		}
		unset($sql_po);
		}
		if($txt_search_common!="")
		{
			//$po_idCond="and b.po_break_down_id in(".implode(",",$po_id_arr).")";
		    $po_idCond=where_con_using_array(array_unique($po_id_arr),0,"b.po_break_down_id");
		} 
		else $po_idCond="";
	
	if($cbo_category==4) //Accessories
	{
		if($wo_type==0)
		{
			
			   $sql="select a.id as book_mst_id, a.is_approved, a.update_date, a.pay_mode, a.booking_no as booking_no, a.booking_date as wo_date, b.delivery_date, a.booking_type as wo_type, a.is_short, a.supplier_id as supplier_id, a.buyer_id as buyer_id, a.item_category as item_category, a.remarks as remarks,a.inserted_by as inserted_by, b.id as dtls_id, b.po_break_down_id as po_id, b.pre_cost_fabric_cost_dtls_id as pre_cost_fabric_cost_dtls_id, b.trim_group as trim_group, b.construction as construction, b.copmposition as copmposition, b.uom as wo_uom, c.requirment as wo_qnty,(case when a.exchange_rate>0 then (b.rate/a.exchange_rate) else b.rate end) as wo_rate, 1 as type,c.description,c.item_color
			from wo_booking_mst a, wo_booking_dtls b,wo_trim_book_con_dtls c
			where a.booking_no=b.booking_no and b.id=c.wo_trim_booking_dtls_id and a.booking_type in(2,5) and a.item_category=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1
		 and b.is_deleted=0  $po_idCond $item_id $sql_cond $booking_date_cond $within_cond
			union all
			select a.id as book_mst_id, a.is_approved,a.update_date,a.pay_mode,a.booking_no as booking_no, a.booking_date as wo_date, a.delivery_date, a.booking_type as wo_type, a.is_short, a.supplier_id as supplier_id, a.buyer_id as buyer_id, a.item_category as item_category, null as remarks,0 as inserted_by, b.id as dtls_id, 0 as po_id, 0 as pre_cost_fabric_cost_dtls_id, b.trim_group as trim_group, b.construction as construction, b.composition as copmposition, b.uom as wo_uom, b.trim_qty as wo_qnty,  (case when a.exchange_rate>0 then (b.rate/a.exchange_rate) else b.rate end) as wo_rate, 2 as type,null as description,b.fabric_color as item_color
			from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b
			where a.booking_no=b.booking_no and a.booking_type in(2,3)  and a.item_category=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1
		 and b.is_deleted=0  $item_id $sql_cond $booking_date_cond $within_cond order by book_mst_id ,po_id";// and b.po_break_down_id=33350
		}
		else if ($wo_type==1 || $wo_type==2)
		{
			$sql="select a.id as book_mst_id,a.is_approved,a.update_date,a.pay_mode, a.booking_no as booking_no, a.booking_date as wo_date, b.delivery_date, a.booking_type as wo_type, a.is_short, a.supplier_id as supplier_id, a.buyer_id as buyer_id, a.item_category as item_category, a.remarks as remarks, a.inserted_by as inserted_by, b.id as dtls_id, b.po_break_down_id as po_id, b.pre_cost_fabric_cost_dtls_id as pre_cost_fabric_cost_dtls_id, b.trim_group as trim_group, b.construction as construction, b.copmposition as copmposition, b.uom as wo_uom, c.requirment as wo_qnty, (case when a.exchange_rate>0 then (b.rate/a.exchange_rate) else b.rate end) as wo_rate,c.item_color, 1 as type,  (c.cons) as cons
			from wo_booking_mst a, wo_booking_dtls b,wo_trim_book_con_dtls c
			where a.booking_no=b.booking_no and b.id= c.wo_trim_booking_dtls_id and a.booking_type in(2) and a.is_short=$wo_type and a.item_category=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1
		 	and b.is_deleted=0 $po_idCond $sql_cond $item_id $booking_date_cond $within_cond order by a.id
			 ";
		}
		else if ($wo_type==3)
		{
			$sql="select a.id as book_mst_id,a.is_approved,a.update_date,a.pay_mode, a.booking_no as booking_no, a.booking_date as wo_date, b.delivery_date, a.booking_type as wo_type, a.is_short, a.supplier_id as supplier_id, a.buyer_id as buyer_id, a.item_category as item_category, a.remarks as remarks,a.inserted_by as inserted_by, b.id as dtls_id, b.po_break_down_id as po_id, b.pre_cost_fabric_cost_dtls_id as pre_cost_fabric_cost_dtls_id, b.trim_group as trim_group, b.construction as construction, b.copmposition as copmposition, b.uom as wo_uom, c.requirment as wo_qnty, (case when a.exchange_rate>0 then (b.rate/a.exchange_rate) else b.rate end) as wo_rate,c.item_color, 1 as type,  (c.cons) as cons
			from wo_booking_mst a, wo_booking_dtls b,wo_trim_book_con_dtls c
			where a.booking_no=b.booking_no and b.id= c.wo_trim_booking_dtls_id and a.booking_type in(5) and a.item_category=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1
		 	and b.is_deleted=0 $po_idCond $item_id $sql_cond $booking_date_cond $within_cond
			 
			order by a.id";
		}
		else
		{
			$sql="select a.id as book_mst_id,a.is_approved,a.update_date,a.pay_mode, a.booking_no as booking_no, a.booking_date as wo_date, a.delivery_date, a.booking_type as wo_type, a.is_short, a.supplier_id as supplier_id, a.buyer_id as buyer_id, a.item_category as item_category, null as remarks, b.id as dtls_id, 0 as po_id, 0 as pre_cost_fabric_cost_dtls_id, b.trim_group as trim_group, b.construction as construction, b.composition as copmposition, b.uom as wo_uom, b.trim_qty as wo_qnty, (case when a.exchange_rate>0 then (b.rate/a.exchange_rate) else b.rate end) as wo_rate,b.gmts_color , 2 as type
			from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b
			where a.booking_no=b.booking_no and a.booking_type in(2,3)  and a.item_category=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1
		 	and b.is_deleted=0 $item_id $sql_cond $booking_date_cond $within_cond
			 order by a.id";
		}
	}
	else if($cbo_category==25) //Emblishmnet
	{
		if($wo_type==0)
		{
			 $sql="select a.id as book_mst_id,a.currency_id, a.is_approved, a.update_date, a.pay_mode, a.booking_no as booking_no, a.booking_date as wo_date, b.delivery_date, a.booking_type as wo_type, a.is_short, a.supplier_id as supplier_id, a.buyer_id as buyer_id, a.item_category as item_category, a.remarks as remarks,a.inserted_by as inserted_by, b.id as dtls_id, b.po_break_down_id as po_id, b.pre_cost_fabric_cost_dtls_id as pre_cost_fabric_cost_dtls_id, b.emblishment_name,b.construction as construction, b.copmposition as copmposition, b.uom as wo_uom,b.gmts_color_id, b.wo_qnty as wo_qnty,b.rate,a.exchange_rate,(case when a.exchange_rate>0 then (b.rate/a.exchange_rate) else b.rate end) as wo_rate, 1 as type, b.trim_group as trim_group
			from wo_booking_mst a, wo_booking_dtls b
			where a.booking_no=b.booking_no and a.booking_type in(6) and b.wo_qnty>0 and a.item_category=$cbo_category and a.status_active=1 and a.is_deleted=0 and b.status_active=1
		 and b.is_deleted=0   $po_idCond  $sql_cond $booking_date_cond $within_cond
			";// and b.po_break_down_id=33350
		}
		else if ($wo_type==1 || $wo_type==2)
		{
			$sql="select a.id as book_mst_id,a.currency_id,a.is_approved,a.update_date,a.pay_mode, a.booking_no as booking_no, a.booking_date as wo_date, b.delivery_date, a.booking_type as wo_type, a.is_short, a.supplier_id as supplier_id, a.buyer_id as buyer_id, a.item_category as item_category, a.remarks as remarks, a.inserted_by as inserted_by, b.id as dtls_id, b.po_break_down_id as po_id, b.pre_cost_fabric_cost_dtls_id as pre_cost_fabric_cost_dtls_id, b.emblishment_name, b.construction as construction, b.copmposition as copmposition, b.uom as wo_uom,b.gmts_color_id, b.wo_qnty as wo_qnty,b.rate,a.exchange_rate,(case when a.exchange_rate>0 then (b.rate/a.exchange_rate) else b.rate end) as wo_rate, 1 as type,, b.trim_group as trim_group
			from wo_booking_mst a, wo_booking_dtls b
			where a.booking_no=b.booking_no and a.booking_type in(6) and b.wo_qnty>0 and a.is_short=$wo_type and a.item_category=$cbo_category and a.status_active=1 and a.is_deleted=0 and b.status_active=1
		 and b.is_deleted=0  $po_idCond  $sql_cond   $booking_date_cond $within_cond
			order by a.id";
		}
	}
	// echo $sql; 

	ob_start();
	if($cbo_category==4) //Accessories
	{
		$sql_result=sql_select($sql);
		$all_po_id=""; $bookingIdArr=array();
		foreach($sql_result as $row)
		{
			if($all_po_id=="") $all_po_id=$row[csf("po_id")];else $all_po_id.=",".$row[csf("po_id")];
			//$emblish_arr[$row[csf("po_id")]]["po_id"]=$row[csf("po_id")];
			$bookingIdArr[$row[csf("booking_no")]]=$row[csf("book_mst_id")];
		}
		//echo $all_po_id.'ds';
		$poIds=chop($all_po_id,','); $po_cond_for_in=""; $po_cond_for_in2=""; $po_cond_for_in3=""; 
		$po_ids=count(array_unique(explode(",",$all_po_id)));
		

		$po_cond_for_in=where_con_using_array(array_unique(explode(",",$all_po_id)),0,"b.id");
		$po_cond_for_in2=where_con_using_array(array_unique(explode(",",$all_po_id)),0,"b.po_break_down_id");
		$po_cond_for_in3=where_con_using_array(array_unique(explode(",",$all_po_id)),0,"d.po_breakdown_id");
		$tna_po=where_con_using_array(array_unique(explode(",",$all_po_id)),0,"b.id");

		$sql_date="SELECT a.task_finish_date,a.po_number_id,a.task_number from tna_process_mst a, wo_po_break_down b where a.po_number_id = b.id and a.status_active=1 and b.is_deleted=0 and A.TASK_NUMBER in (70,71) $tna_po  group by a.task_finish_date,a.po_number_id,a.task_number,a.task_category";
		//echo $sql_date;

		$date_arr=array();

		$sql_result=sql_select($sql_date);
		foreach ($sql_result as $row) 
		{
			$date_arr[$row[csf('po_number_id')]][$row[csf('task_number')]]=$row[csf('task_finish_date')];
		}
			
		$order_sql=sql_select("select a.id as job_id, a.job_no, a.job_no_prefix_num, $select_year, a.style_ref_no, b.id as po_id, b.po_number,a.dealing_marchant,a.team_leader,b.grouping  from  wo_po_details_master a, wo_po_break_down b where a.id=b.job_id and a.status_active=1  and b.status_active=1 $po_cond_for_in $internal_ref_cond");
		//echo "select a.id as job_id, a.job_no, a.job_no_prefix_num, $select_year, a.style_ref_no, b.id as po_id, b.po_number,a.dealing_marchant,a.team_leader,b.grouping  from  wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and b.status_active=1 $po_cond_for_in";
		//grouping field
		$order_data_arr=array();
		//echo $order_sql[csf("po_id")];die;
		$i = 0;
		foreach($order_sql as $row)
		{
			$order_data_arr[$row[csf("po_id")]]["po_id"]=$row[csf("po_id")];
			$order_data_arr[$row[csf("po_id")]]["job_id"]=$row[csf("job_id")];
			$order_data_arr[$row[csf("po_id")]]["job_no"]=$row[csf("job_no")];
			$order_data_arr[$row[csf("po_id")]]["job_no_prefix_num"]=$row[csf("job_no_prefix_num")];
			$order_data_arr[$row[csf("po_id")]]["job_year"]=$row[csf("job_year")];
			$order_data_arr[$row[csf("po_id")]]["style_ref_no"]=$row[csf("style_ref_no")];
			$order_data_arr[$row[csf("po_id")]]["po_number"]=$row[csf("po_number")];
			$order_data_arr[$row[csf("po_id")]]["grouping"]=$row[csf("grouping")];
			$order_data_arr[$row[csf('po_id')]]['team_leader']=$row[csf('team_leader')];
			$order_data_arr[$row[csf('po_id')]]['dealing_marchant']=$row[csf('dealing_marchant')];
			//echo $order_data_arr[$row[csf("po_id")]]["po_number"];
		}
		unset($order_sql);
		$trims_sql=sql_select("select b.id as po_id, a.trim_group, sum(a.rate) as trims_rate from wo_pre_cost_trim_cost_dtls a, wo_po_break_down b where  a.job_id=b.job_id and a.status_active=1 $po_cond_for_in group by b.id, a.trim_group");
		//echo "select b.id as po_id, a.trim_group, sum(a.rate) as trims_rate from wo_pre_cost_trim_cost_dtls a, wo_po_break_down b where  a.job_no=b.job_no_mst and a.status_active=1 $po_cond_for_in group by b.id, a.trim_group";
		$pre_cost_data_arr=array();
		foreach($trims_sql as $row)
		{
			$pre_cost_data_arr[$row[csf("po_id")]][$row[csf("trim_group")]]=$row[csf("trims_rate")];
		}
		unset($trims_sql);
		// $description_sql="select b.wo_trim_booking_dtls_id, b.description, b.brand_supplier, b.item_color, b.item_size from  wo_trim_book_con_dtls b where b.status_active=1 $po_cond_for_in2";
		// //echo $description_sql;
		// $description_sql_result=sql_select($description_sql);
		// $description_arr=array();
		// foreach($description_sql_result as $row)
		// {
		// 	$description=trim($row[csf("description")]);
		// 	$brand_supplier=trim($row[csf("brand_supplier")]);
		// 	$item_size=trim($row[csf("item_size")]);
		// 	if( ($description!=0 || $description!="") && $description_check[$row[csf("wo_trim_booking_dtls_id")]][$description]=="")
		// 	{
		// 		$description_check[$row[csf("wo_trim_booking_dtls_id")]][$description]=$description;
		// 		$description_arr[$row[csf("wo_trim_booking_dtls_id")]]["description"].=$description."__";
		// 	}
		// 	if($brand_supplier_check[$row[csf("wo_trim_booking_dtls_id")]][$brand_supplier]=="")
		// 	{
		// 		$brand_supplier_check[$row[csf("wo_trim_booking_dtls_id")]][$brand_supplier]=$brand_supplier;
		// 		$description_arr[$row[csf("wo_trim_booking_dtls_id")]]["brand_supplier"].=$brand_supplier."__";
		// 	}
			
		// 	if($item_color_check[$row[csf("wo_trim_booking_dtls_id")]][$row[csf("item_color")]]=="")
		// 	{
		// 		$item_color_check[$row[csf("wo_trim_booking_dtls_id")]][$row[csf("item_color")]]=$row[csf("item_color")];
		// 		$description_arr[$row[csf("wo_trim_booking_dtls_id")]]["item_color"].=$row[csf("item_color")]."__";
		// 	}
			
		// 	if($item_size_check[$row[csf("wo_trim_booking_dtls_id")]][$item_size]=="")
		// 	{
		// 		$item_size_check[$row[csf("wo_trim_booking_dtls_id")]][$item_size]=$item_size;
		// 		$description_arr[$row[csf("wo_trim_booking_dtls_id")]]["item_size"].=$item_size."__";
		// 	}
		// }
		// unset($description_sql_result);
		$piBookingsql=sql_select( "select a.pi_id, a.work_order_id, a.item_group, b.po_break_down_id from com_pi_item_details a, wo_booking_dtls b where a.work_order_dtls_id=b.id and  a.item_category_id = 4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $po_cond_for_in2");
		$piBookingNo=array();
		foreach($piBookingsql as $row)
		{
			$piBookingNo[$row[csf("pi_id")]][$row[csf("po_break_down_id")]][$row[csf("item_group")]]=$row[csf("work_order_id")];
		}
		unset($piBookingsql);
	
		/*$rcv_qnty_sql="select b.mst_id, b.receive_basis, b.transaction_date as receive_date, c.booking_id as pi_wo_batch_no, b.booking_no, c.item_group_id, c.item_description, c.brand_supplier, d.po_breakdown_id as po_id, d.quantity as rcv_qnty, d.order_amount as rcv_value, e.work_order_id
		from inv_trims_entry_dtls c, order_wise_pro_details d, inv_transaction b 
		left join com_pi_item_details e on e.pi_id=b.pi_wo_batch_no
		where b.id=c.trans_id and c.id=d.dtls_id and c.trans_id=d.trans_id and b.transaction_type=1 and b.item_category=4 and d.entry_form=24 and d.status_active=1 and d.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $po_cond_for_in3 
		group by b.mst_id, b.receive_basis, b.transaction_date, c.booking_id, b.booking_no, c.item_group_id, c.item_description, c.brand_supplier, d.po_breakdown_id, d.quantity, d.order_amount, e.work_order_id";*/
		
		// $rcv_qnty_sql="select b.mst_id, b.receive_basis, b.transaction_date as receive_date, c.booking_id as pi_wo_batch_no, b.booking_no, c.item_group_id, c.item_description, c.brand_supplier, d.po_breakdown_id as po_id, d.id as prop_id, d.quantity as rcv_qnty, d.order_amount as rcv_value, e.work_order_id, b.prod_id,c.item_color
		// from inv_trims_entry_dtls c, order_wise_pro_details d, inv_transaction b 
		// left join com_pi_item_details e on e.pi_id=b.pi_wo_batch_no
		// where b.id=c.trans_id and c.id=d.dtls_id and c.trans_id=d.trans_id and b.transaction_type=1 and b.item_category=4 and d.entry_form=24 and d.status_active=1 and d.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $po_cond_for_in3";
		$rcv_qnty_sql="select b.mst_id, b.receive_basis, b.transaction_date as receive_date, c.booking_id as pi_wo_batch_no, b.booking_no, c.item_group_id, c.item_description, c.brand_supplier, d.po_breakdown_id as po_id, d.id as prop_id, d.quantity as rcv_qnty, d.order_amount as rcv_value, b.prod_id,c.item_color
		from inv_trims_entry_dtls c, order_wise_pro_details d, inv_transaction b 
		where b.id=c.trans_id and c.id=d.dtls_id and c.trans_id=d.trans_id and b.transaction_type=1 and b.item_category=4 and d.entry_form=24 and d.status_active=1 and d.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $po_cond_for_in3";
		//echo $rcv_qnty_sql;
		$receive_qty_data=sql_select($rcv_qnty_sql);
		$rcv_data_po=array();
		$rcv_data_po_ontime=array();
		foreach($receive_qty_data as $row)
		{
			
			$itemgroup=$row[csf('item_group_id')];
			$po_id=$row[csf('po_id')];
			//if($row[csf('receive_basis')]==1) $woid=$row[csf('work_order_id')];
			//if($row[csf('receive_basis')]==12) $woid=$bookingIdArr[$row[csf("booking_no")]];
			//echo $woid.'='.$po_id.'='.$itemgroup.'='.$row[csf('item_description')].'='.$row[csf('brand_supplier')].'<br>';
			$item_description=trim($row[csf('item_description')]);

			$wo_no=$row[csf('booking_no')];
		//	$rcv_data_nonOrder[$booking_no][$itemgroup][$item_description]["receive_date"].=$row[csf('receive_date')].",";
			$rcv_data_po[$wo_no][$po_id][$itemgroup][$item_description]["receive_date"].=$row[csf('receive_date')].",";
			if($propo_id_check[$row[csf('prop_id')]][$wo_no]=="")
			{
				$propo_id_check[$row[csf('prop_id')]][$wo_no]=$row[csf('prop_id')];
			$rcv_data_po_ontime[$row[csf('receive_date')]][$wo_no][$po_id][$itemgroup][$item_description]["rcv_qnty"]+=$row[csf('rcv_qnty')];
			$rcv_data_po_ontime[$row[csf('receive_date')]][$wo_no][$po_id][$itemgroup][$item_description]["rcv_value"]+=$row[csf('rcv_value')];
		//	$rcv_data_po_ontime[$rcv_date][$row[csf('book_mst_id')]][$row[csf("po_id")]][$row[csf('trim_group')]][$descript][$row[csf("item_color")]]["mst_id"];
			//$rcv_data_po_ontime[$row[csf('receive_date')]][$wo_no][$po_id][$itemgroup][$item_description]["rcv_qnty"].=$row[csf('mst_id')].",";

			$rcv_data_poArr[$wo_no][$po_id][$itemgroup][$item_description]["rcv_qnty"]+=$row[csf('rcv_qnty')];
			$rcv_data_poArr[$wo_no][$po_id][$itemgroup][$item_description]["rcv_value"]+=$row[csf('rcv_value')];
			}

			

			if($row[csf('mst_id')] && $mst_id_check[$row[csf('receive_date')]][$wo_no][$po_id][$itemgroup][$item_description][$row[csf('mst_id')]]=="")
			{
				 $mst_id_check[$row[csf('receive_date')]][$wo_no][$po_id][$itemgroup][$item_description][$row[csf('mst_id')]]=$row[csf('mst_id')];
				$rcv_data_po_ontime[$row[csf('receive_date')]][$wo_no][$po_id][$itemgroup][$row[csf('item_description')]]["mst_id"].=$row[csf('mst_id')].",";
			}

			
		}
		//   echo "<pre>";
		//   print_r($rcv_data_poArr);
		unset($receive_qty_data);
	
		$receive_qty_data_noorder=sql_select("select a.id as mst_id, a.receive_basis, a.receive_date,c.booking_no, c.booking_id as pi_wo_batch_no, b.cons_quantity as rcv_qnty, c.item_group_id, c.item_description, c.brand_supplier, b.order_amount as rcv_value,c.item_color
		from inv_receive_master a, inv_transaction b, inv_trims_entry_dtls c
		where a.id=b.mst_id and b.id=c.trans_id and b.transaction_type=1  and b.pi_wo_batch_no>0 and b.item_category=4 and a.entry_form=24 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0");
		
		$rcv_data_nonOrder=array();
		$rcv_data_nonOrder_on_time=array();
		foreach($receive_qty_data_noorder as $row)
		{
			$woid=$row[csf('pi_wo_batch_no')];
			$booking_no=$row[csf('booking_no')];
			$itemgroup=$row[csf('item_group_id')];
			$item_description=trim($row[csf('item_description')]);
			//if($row[csf('receive_basis')]==1) $woid=$piBookingNo[$woid][$po_id][$itemgroup];
		
			$rcv_data_nonOrder[$booking_no][$itemgroup][$item_description]["rcv_qnty"]+=$row[csf('rcv_qnty')];
			$rcv_data_nonOrder[$booking_no][$itemgroup][$item_description]["rcv_value"]+=$row[csf('rcv_value')];
			$rcv_data_nonOrder[$booking_no][$itemgroup][$item_description]["receive_date"].=$row[csf('receive_date')].",";
			
	
			$rcv_data_nonOrder_on_time[$row[csf('receive_date')]][$booking_no][$itemgroup][$item_description]["rcv_qnty"]+=$row[csf('rcv_qnty')];
			$rcv_data_nonOrder_on_time[$row[csf('receive_date')]][$booking_no][$itemgroup][$item_description]["rcv_value"]+=$row[csf('rcv_value')];
			if($row[csf('mst_id')] && $non_mst_id_check[$row[csf('receive_date')]][$booking_no][$itemgroup][$item_description][$row[csf('mst_id')]]=="")
			{
				$non_mst_id_check[$row[csf('receive_date')]][$booking_no][$itemgroup][$item_description][$row[csf('mst_id')]]=$row[csf('mst_id')];
				$rcv_data_nonOrder_on_time[$row[csf('receive_date')]][$booking_no][$itemgroup][$item_description]["mst_id"].=$row[csf('mst_id')].",";
			}
		}
		unset($receive_qty_data_noorder);
		// echo "<pre>";
		// print_r($rcv_data_nonOrder);
		//echo "test";die;
		$sql_result=sql_select($sql);
		foreach($sql_result as $row)
		{
			$index=$row[csf('booking_no')].'_'.$row[csf('po_id')].'_'.$row[csf('trim_group')].'_'.$row[csf('description')];
			$wo_po_itemWiseArr[$index]['book_mst_id']=$row[csf('book_mst_id')];
			$wo_po_itemWiseArr[$index]['is_approved']=$row[csf('is_approved')];
			$wo_po_itemWiseArr[$index]['update_date']=$row[csf('update_date')];
			$wo_po_itemWiseArr[$index]['pay_mode']=$row[csf('pay_mode')];
			$wo_po_itemWiseArr[$index]['booking_no']=$row[csf('booking_no')];
			$wo_po_itemWiseArr[$index]['wo_date']=$row[csf('wo_date')];
			$wo_po_itemWiseArr[$index]['delivery_date']=$row[csf('delivery_date')];
			$wo_po_itemWiseArr[$index]['wo_type']=$row[csf('wo_type')];
			$wo_po_itemWiseArr[$index]['is_short']=$row[csf('is_short')];
			$wo_po_itemWiseArr[$index]['supplier_id']=$row[csf('supplier_id')];
			$wo_po_itemWiseArr[$index]['buyer_id']=$row[csf('buyer_id')];
			$wo_po_itemWiseArr[$index]['item_category']=$row[csf('item_category')];
			$wo_po_itemWiseArr[$index]['remarks']=$row[csf('remarks')];
			$wo_po_itemWiseArr[$index]['inserted_by']=$row[csf('inserted_by')];
			$wo_po_itemWiseArr[$index]['dtls_id']=$row[csf('dtls_id')];
			$wo_po_itemWiseArr[$index]['po_id']=$row[csf('po_id')];
			$wo_po_itemWiseArr[$index]['pre_cost_fabric_cost_dtls_id']=$row[csf('pre_cost_fabric_cost_dtls_id')];
			$wo_po_itemWiseArr[$index]['trim_group']=$row[csf('trim_group')];
			$wo_po_itemWiseArr[$index]['construction']=$row[csf('construction')];
			$wo_po_itemWiseArr[$index]['copmposition']=$row[csf('copmposition')];
			$wo_po_itemWiseArr[$index]['wo_uom']=$row[csf('wo_uom')];
			$wo_po_itemWiseArr[$index]['wo_qnty']+=$row[csf('wo_qnty')];
			$wo_po_itemWiseArr[$index]['wo_rate']=$row[csf('wo_rate')];
			$wo_po_itemWiseArr[$index]['type']=$row[csf('type')];
			$wo_po_itemWiseArr[$index]['item_color']=$row[csf('item_color')];
		}
		//print_r($wo_po_itemWiseArr);
		?>
		<fieldset>
			<table width="2780"  cellspacing="0" >
				<tr class="form_caption" style="border:none;">
					<td align="center" style="border:none; font-size:18px;" colspan="22">
						<? echo $company_library[str_replace("'","",$cbo_company_id)]; ?>
					</td>
				</tr>
				<tr class="form_caption" style="border:none;">
					<td align="center" style="border:none;font-size:16px; font-weight:bold"  colspan="25"> <? echo $report_title ;?></td>
				</tr>
				<tr class="form_caption" style="border:none;">
					<td align="center" style="border:none;font-size:12px; font-weight:bold"  colspan="25"> <? echo "From ".change_date_format($date_from)." To ".change_date_format($date_to);?></td>
				</tr>
			</table>
			<table width="2940" cellspacing="0" border="1" class="rpt_table" rules="all">
				<thead>
					<th width="40">SL</th>
					<th width="110">WO No</th>
					<th width="70">Approved Date</th>
					<th width="110">Internal Ref No</th>
					<th width="70">WO Date</th>
					<th width="70">Delivery Date</th>
					<th width="70">Lead Time</th>
					<th width="90">WO Type</th>
					<th width="100">Supplier</th>
					<th width="100">Buyer</th>
					<th width="50">Job Year</th>
					<th width="50">Job No.</th>
					<th width="100">Style Ref.</th>
					<th width="100">Order No</th>
					<th width="120">Item Name</th>
					<th width="150">Description</th>
					<th width="100">Item Category</th>
					<th width="60">UOM</th>
					<th width="80">WO Qty</th>
					<th width="70">WO Unit price</th>
					<th width="80">WO value</th>
					<th width="70">Budget Unit price</th>
					<th width="80">Precost value</th>
					<th width="80" title="(Precost value - WO value)">Deference</th>
					<th width="80" title="(Deference / Precost value)*100">Deference %</th>
					<th width="80">On Time Receive</th>
					<th width="80">OTD%</th>
					<th width="80">Total Receive Qty</th>
					<th width="80">Receive Value</th>
					<th width="80">Receive Balance</th>
					<th width="120">Dealing Merchant</th>
					<th width="120">Team Leader</th>
					<th width="120">User Name</th>
					<th >Remarks</th>
				</thead>
			</table>
			<div style="max-height:350px; overflow-y:scroll; width:2960px" id="scroll_body" >
				<table cellspacing="0" cellpadding="0" border="1" class="rpt_table"  width="2940" rules="all" id="table_body" >
				<?
				
				 $i=1;$item_group_sammary=array();
				$total_precost_value =0; $total_deference=0; $total_deference_per =0;
	
				foreach($wo_po_itemWiseArr as $index_str=>$row)
				{
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					$index_str=explode("_",$index_str);
					$booking_no=$index_str[0];
					$po_id=$index_str[1];
					$item_id=$index_str[2];
					$desc=$index_str[3];
					if ($row[("type")]==1)
					{
						if ($row[("wo_type")]==2)
						{
							if ($row[csf("is_short")]==1)
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
						elseif($row[("wo_type")]==5)
						{
							$wo_type="Sample With Order";
							$wo_typw_id=3;
						}
					}
					else
					{
						$wo_type="Sample Without Order";
						$wo_typw_id=4;
					}
					$lead_time=datediff("d", $row[("wo_date")], $row[("delivery_date")]);
					//$job_number=implode(",",array_unique(explode(",",$row[csf("job_no")])));
					$rcv_qnty=$rcv_balance=$ontimeRcv=0;$rcv_mst_id="";
	
					if($row[('type')]==1)
					{
						 
						//echo $booking_no.'='.$po_id.'='.$item_id.'='.$desc.'<br>';
						$rcv_qnty=$rcv_data_poArr[$booking_no][$po_id][$item_id][$desc]["rcv_qnty"];
						$rcv_value=$rcv_data_poArr[$booking_no][$po_id][$item_id][$desc]["rcv_value"];

						$rcv_date_arr=array();
							$rcv_date_arr=array_unique(explode(",",chop($rcv_data_po[$booking_no][$po_id][$item_id][$desc]["receive_date"],",")));
						//	echo  chop($rcv_data_po[$booking_no][$po_id][$item_id][$desc]["receive_date"],",").'=<br>';
							foreach($rcv_date_arr as $rcv_date)
							{
								if($rcv_date!="" && $rcv_date!="0000-00-00")
								{  
									if(strtotime($rcv_date)<=strtotime($row[("delivery_date")]) && $rcv_data_po_ontime[$rcv_date][$booking_no][$po_id][$item_id][$desc]["rcv_qnty"]>0)
										{
											$ontimeRcv+=$rcv_data_po_ontime[$rcv_date][$booking_no][$po_id][$item_id][$desc]["rcv_qnty"];
										
											$rcv_mst_id.=chop($rcv_data_po_ontime[$rcv_date][$booking_no][$po_id][$item_id][$desc]["mst_id"],",").",";
										}   
								}
							}

					}
					else
					{
						$po_id=$row[("po_id")];
						$rcv_qnty=$rcv_value=$ontimeRcv=0;$rcv_mst_id="";
						$rcv_date_arr=array();
						$rcv_date_arr=array_unique(explode(",",chop($rcv_data_nonOrder[$booking_no][$item_id][$desc]["receive_date"],",")));
					

						foreach($rcv_date_arr as $rcv_date)
						{
							if($rcv_date!="" && $rcv_date!="0000-00-00")
							{
								
									if(strtotime($rcv_date)<=strtotime($row[("delivery_date")]) && $rcv_data_nonOrder_on_time[$rcv_date][$booking_no][$item_id][$desc]["rcv_qnty"]>0)
									{
										$ontimeRcv+=$rcv_data_nonOrder_on_time[$rcv_date][$booking_no][$item_id][$desc]["rcv_qnty"];
										$rcv_mst_id.=chop($rcv_data_nonOrder_on_time[$rcv_date][$booking_no][$item_id][$desc]["mst_id"],",").",";
									}
								
							}
						}

						$rcv_qnty=$rcv_data_nonOrder[$booking_no][$item_id][$desc]["rcv_qnty"];
						$rcv_value=$rcv_data_nonOrder[$booking_no][$item_id][$desc]["rcv_value"];
					}
					
					// $rcv_balance=$row[csf("wo_qnty")]-$rcv_qnty;
					$rcv_balance=$row[("wo_qnty")]-$rcv_qnty;
	
					$rcv_mst_id=chop($rcv_mst_id,",");
					$otd=0;
					$otd=(($ontimeRcv/$row[("wo_qnty")])*100);
	
					if($row[("wo_qnty")]>0 && $row[("trim_group")]>0)
					{
						$item_group_sammary[$row[("trim_group")]]["wo_qnty"]+=$row[("wo_qnty")];
						$item_group_sammary[$row[("trim_group")]]["wo_value"]+=$row[("wo_qnty")]*$row[("wo_rate")];
						$item_group_sammary[$row[("trim_group")]]["pre_value"]+=$row[("wo_qnty")]*$pre_cost_data_arr[$row[("po_id")]][$row[("trim_group")]];//*$pre_cost_data_arr[$row[csf("po_id")]][$row[csf("trim_group")]]
						$item_group_sammary[$row[("trim_group")]]["ontimeRcv"]+=$ontimeRcv;
						$item_group_sammary[$row[("trim_group")]]["rcv_qnty"]+=$rcv_qnty;
						$item_group_sammary[$row[("trim_group")]]["trim_group"]=$row[("trim_group")];
					}
	
					if($row[("supplier_id")]>0 && $row[("wo_qnty")]>0)
					{ //supplierArr 
						if($row[("pay_mode")]==3 || $row[("pay_mode")]==5)
						{
						 $supplier_wise_sammary[$row[("supplier_id")]]["supp_comp"]=$company_library[$row[("supplier_id")]];
						}
						else
						{
						 $supplier_wise_sammary[$row[("supplier_id")]]["supp_comp"]=$supplierArr[$row[("supplier_id")]];
						}
						$supplier_wise_sammary[$row[("supplier_id")]]["wo_qnty"]+=$row[("wo_qnty")];
						$supplier_wise_sammary[$row[("supplier_id")]]["wo_value"]+=$row[("wo_qnty")]*$row[("wo_rate")];
						$supplier_wise_sammary[$row[("supplier_id")]]["pre_value"]+=$row[("wo_qnty")]*$pre_cost_data_arr[$row[("po_id")]][$row[("trim_group")]];
						$supplier_wise_sammary[$row[("supplier_id")]]["ontimeRcv"]+=$ontimeRcv;
						$supplier_wise_sammary[$row[("supplier_id")]]["rcv_qnty"]+=$rcv_qnty;
						$supplier_wise_sammary[$row[("supplier_id")]]["supplier_id"]=$row[("supplier_id")];
					}
					if($row[csf("is_approved")]==1)
					{
						$approved_date=$row[("update_date")];
						$approved_date = date('Y-m-d', strtotime($approved_date));
					}
					else $approved_date='';
					
					if($row[("pay_mode")]==3 || $row[("pay_mode")]==5) $supplier_com=$company_library[$row[("supplier_id")]]; else $supplier_com=$supplierArr[$row[("supplier_id")]];
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
						<td width="40" align="center"><? echo $i;?></td>
						<td width="110"><p><? echo $row[("booking_no")]; ?></td>
						<td width="70"><p><? echo $approved_date; ?></td>
						<td width="110"><p><? echo $order_data_arr[$row[("po_id")]]["grouping"]; ?></td>
						<td width="70" align="center"><p><? echo change_date_format($row[("wo_date")]); ?>&nbsp;</p></td>
						<td width="70" align="center"><p><?
								$date_del="";
								if($trim_type[$row[('trim_group')]]==1)
								{
									$date_del=$date_arr[$row[('po_id')]][70];
								}
								else{
									$date_del=$date_arr[$row[('po_id')]][71];
								}
								if(empty($date_del))
								{
									$date_del=$row[("delivery_date")];
								}
						 		echo change_date_format($row[("delivery_date")]); 
						 ?>&nbsp;</p></td>
						<td width="70" align="center"><p><? if($lead_time>0 && $lead_time<2) echo $lead_time." Day"; elseif($lead_time>1) echo $lead_time." Days"; else echo "0 Day"; ?>&nbsp;</p></td>
						<td width="90"><p><?  echo $wo_type; ?>&nbsp;</p></td>
						<td width="100"><p><? echo $supplier_com; ?>&nbsp;</p></td>
						<td width="100"><p><? echo $buyerArr[$row[("buyer_id")]]; ?>&nbsp;</p></td>
						<td width="50" align="center"><p><? echo $order_data_arr[$row[("po_id")]]["job_year"]; ?>&nbsp;</p></td>
						<td width="50" align="center"><p><? echo $order_data_arr[$row[("po_id")]]["job_no_prefix_num"]; ?>&nbsp;</p></td>
						<td width="100"><p><? echo $order_data_arr[$row[("po_id")]]["style_ref_no"]; ?>&nbsp;</p></td>
						<td width="100"  style="word-break:break-all"><p><? echo $order_data_arr[$row[("po_id")]]["po_number"]; ?>&nbsp;</p></td>
						<td width="120"><p><? echo $trimsGroupArr[$row[("trim_group")]]; ?>&nbsp;</p></td>
						<td width="150" style="word-break:break-all"><p>
						<?
						//$desc_trim=chop($description_arr[$row[csf("dtls_id")]]["description"],'__');
						//$desc_trims=implode(",",array_unique(explode(",",$desc_trim)));
						echo $desc; 
						?>&nbsp;</p></td>
						<td width="100"><p><? echo $item_category[$row[("item_category")]]; ?>&nbsp;</p></td>
						<td width="60"><p><? echo $unit_of_measurement[$row[("wo_uom")]]; ?>&nbsp;</p></td>
						<td width="80" align="right"><p><? echo number_format($row[("wo_qnty")],2,'.',''); ?></p></td>
						<td width="70" align="right"><p><? echo number_format($row[("wo_rate")],4,'.',''); ?>&nbsp;</p></td>
						<td width="80" align="right"><p><? $wo_value=$row[("wo_qnty")]*$row[("wo_rate")]; echo number_format($wo_value,4,'.',''); ?></p></td>
						<td width="70" align="right"><p><? echo number_format($pre_cost_data_arr[$row[("po_id")]][$row[("trim_group")]],4,'.',''); ?></p></td>
						<td width="80" align="right"><p><? $precost_value=$row[("wo_qnty")]*$pre_cost_data_arr[$row[("po_id")]][$row[("trim_group")]]; echo number_format($precost_value,4); ?></p></td>
						<td width="80" align="right" title="Pre Value-Wo Value"><p><? $deference = $precost_value-$wo_value; echo number_format($deference,2,'.',''); ?></p></td>
						<td width="80" align="right"><p><? $deference_per = ($deference/$precost_value)*100; echo number_format($deference_per,2,'.',''); ?></p></td>
						<td width="80" align="right" title="<?=$prod_id;?>"><p><a href='#report_details' onClick="openmypage_inhouse('<? echo $row[("book_mst_id")]; ?>','<? echo $row[("po_id")]; ?>','<? echo $row[("trim_group")]; ?>','<? echo $desc; ?>','<? echo $rcv_mst_id; ?>','1','<? echo $row[("delivery_date")]; ?>','<? echo $row[("item_color")]; ?>','booking_inhouse_info');"><? echo number_format($ontimeRcv,2,'.','');?></a> </p></td>
						<td width="80" align="right"><p><? echo number_format($otd,2);?></p></td>
						<td width="80" align="right"><p><a href='#report_details' onClick="openmypage_inhouse('<? echo $row[("book_mst_id")]; ?>','<? echo $row[("po_id")]; ?>','<? echo $row[("trim_group")]; ?>','<? echo $desc; ?>','<? echo $rcv_mst_id; ?>','2','<? echo $row[('type')] ?>','<? echo $row[("item_color")]; ?>','booking_inhouse_info');"><? echo number_format($rcv_qnty,2,'.','');?></a> </p></td>
						<td width="80" align="right"><p><? echo number_format($rcv_value,4,'.',''); ?></p></td>
						<td width="80" align="right"><p><? echo number_format($rcv_balance,2,'.',''); ?></p></td>
						<td width="120"><p><? echo $lib_team_member_arr[$order_data_arr[$row[("po_id")]]['dealing_marchant']]; ?></p></td>
						<td width="120"><p><? echo $lib_team_leader_name_arr[$order_data_arr[$row[("po_id")]]['team_leader']] ; ?></p></td>
						<td width="120" align="right"><p><? echo $userArr[$row[("inserted_by")]]; ?></p></td>
						<td ><p><? echo $row[("remarks")]; ?> &nbsp;</p></td>
					</tr>
					<?
					$tot_wo_qnty+=$row[("wo_qnty")];
					$tot_wo_value+=$wo_value;
					$tot_receive_qnty+=$rcv_qnty;
					$tot_receive_value+=$rcv_value;
					$tot_rcv_balance+=$rcv_balance;
					$total_ontime_rcv+=$ontimeRcv;
					$total_precost_value +=$precost_value;
					$total_deference += $deference;
					//$total_deference_per +=$deference_per;
	
					$i++;
				}
				?>
					<tfoot>
						<th >&nbsp;</th>
						<th >&nbsp;</th>
						<th >&nbsp;</th>
						<th >&nbsp;</th>
						<th >&nbsp;</th>
						<th >&nbsp;</th>
						<th >&nbsp;</th>
						<th >&nbsp;</th>
						<th >&nbsp;</th>
						<th >&nbsp;</th>
						<th >&nbsp;</th>
						<th >&nbsp;</th>
						<th >&nbsp;</th>
						<th >&nbsp;</th>
						<th >&nbsp;</th>
						<th >&nbsp;</th>
						<th >&nbsp;</th>
						<th >Total:</th>
						<th align="right"><? echo number_format($tot_wo_qnty,2) ;?></th>
						<th >&nbsp;</th>
						<th align="right"><? echo number_format($tot_wo_value,4) ;?></th>
						<th >&nbsp;</th>
						<th align="right"><? echo number_format($total_precost_value,4) ;?></th>
						<th align="right"><? echo number_format($total_deference,2) ;?></th>
						<th align="right"><? echo number_format(($total_deference/$total_precost_value)*100,2) ;?></th>
						<th align="right"><? echo number_format($total_ontime_rcv,2) ;?></th>
						<th >&nbsp;</th>
						<th align="right"><? echo number_format($tot_receive_qnty,2) ;?></th>
						<th align="right"><? echo number_format($tot_receive_value,4) ;?></th>
						<th align="right"><? echo number_format($tot_rcv_balance,2) ;?></th>
						<th >&nbsp;</th>
						<th >&nbsp;</th>
						<th >&nbsp;</th>
						<th >&nbsp;</th>
					</tfoot>
				</table>
			</div>
			<br>
			<table cellspacing="0" cellpadding="0" border="0" width="1750" rules="all">
				<tr>
					<td valign="top">
	
						<table cellspacing="0" cellpadding="0" border="1" class="rpt_table"  width="1050" rules="all">
	
							<thead>
								<tr bgcolor="#CCCCCC">
									<td colspan="11" align="center" style="font-size:16px; font-weight:bold;">Item Group Wise Summary</td>
								</tr>
								<tr>
									<th width="50">SL</th>
									<th width="150">Item Name</th>
									<th width="100">WO Qty</th>
									
									<th width="100">WO Value</th>
									<th width="100">Precost value</th>
									<th width="100">Difference</th>
									<th width="50">%</th>
									
									<th width="100">On Time Rcv Qty</th>
									<th width="100">Total Rcv Qty</th>
									<th width="100">Rcv Balance</th>
									<th>OTD %</th>
								</tr>
							</thead>
							<tbody>
								<?
								$k=1;
								//print_r($item_group_sammary);die;
								foreach($item_group_sammary as $item_grp_id=>$val)
								{
									$otd=(($val["ontimeRcv"]/$val["wo_qnty"])*100);
									$item_group_sammary[$item_grp_id]["otd"]=$otd;
								}
	
								foreach($item_group_sammary as $item_group=>$val)
								{
									$mid[$item_group]  = $val["otd"];
								}
								array_multisort($mid, SORT_DESC, $item_group_sammary);
	
								foreach($item_group_sammary as $val)
								{
									if ($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
									$rcv_bal=$val["wo_qnty"]-$val["rcv_qnty"];
								   //$otd=(($val["ontimeRcv"]/$val["wo_qnty"])*100);
									?>
									<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
										<td><? echo $k; ?></td>
										<td>
										<?
										//echo $trimsGroupArr[$item_group];
										echo $trimsGroupArr[$val["trim_group"]];
										?></td>
										<td align="right"><? echo number_format($val["wo_qnty"],2); ?></td>
										<td align="right"><? echo number_format($val["wo_value"],4); ?></td>
										<td align="right"><? echo number_format($val["pre_value"],2); ?></td>
										<td align="right"><? $different=$val["pre_value"]-$val["wo_value"];echo number_format($different,2); ?></td>
										<td align="right" title="Difference/Pre Cost*100"><? echo number_format((($different/$val["pre_value"])*100),2); ?></td>
												
										<td align="right"><? echo number_format($val["ontimeRcv"],2); ?></td>
										<td align="right"><? echo number_format($val["rcv_qnty"],2); ?></td>
										<td align="right"><? echo number_format($rcv_bal,2); ?></td>
										<td align="right"><? echo number_format($val["otd"],2); ?></td>
									</tr>
									<?
									$i++;$k++;
									$sum_tot_wo_qnty+=$val["wo_qnty"];
									$sum_tot_wo_value+=$val["wo_value"];
									$sum_tot_pre_cost+=$val["pre_value"];
									$sum_tot_different+=$val["pre_value"]-$val["wo_value"];
									$sum_tot_ontime_rcv+=$val["ontimeRcv"];
									$sum_tot_rcv_qnty+=$val["rcv_qnty"];
									$sum_tot_rcv_bal+=$rcv_bal;//$val["rcv_bal"];
								}
								$tot_otd=(($sum_tot_ontime_rcv/$sum_tot_wo_qnty)*100);
								?>
							</tbody>
							<tfoot>
								<tr>
									<th>&nbsp;</th>
									<th>Total:</th>
									<th align="right"><? echo number_format($sum_tot_wo_qnty,2); ?></th>
									<th align="right"><? echo number_format($sum_tot_wo_value,4); ?></th>
									<th align="right"><? echo number_format($sum_tot_pre_cost,2); ?></th>
									<th align="right"><? echo number_format($sum_tot_differentt,2); ?></th>
									<th align="right"><? echo number_format((($sum_tot_different/$sum_tot_pre_cost)*100),2); ?></th>
									<th align="right"><? echo number_format($sum_tot_ontime_rcv,2); ?></th>
									<th align="right"><? echo number_format($sum_tot_rcv_qnty,2); ?></th>
									<th align="right"><? echo number_format($sum_tot_rcv_bal,2); ?></th>
									<th align="right"><? echo number_format($tot_otd,2); ?></th>
								</tr>
							</tfoot>
						</table>
	
					</td>
					<td valign="top">&nbsp;</td>
					<td valign="top">
						<table cellspacing="0" cellpadding="0" border="1" class="rpt_table"  width="1050" rules="all">
	
							<thead>
								<tr bgcolor="#CCCCCC">
									<td colspan="11" align="center" style="font-size:16px; font-weight:bold;">Supplier Wise Summary</td>
								</tr>
								<tr>
									<th width="50">SL</th>
									<th width="150">Supplier Name</th>
									<th width="100">WO Qty</th>
									<th width="100">WO Value</th>
									<th width="100">Precost value</th>
									<th width="100">Difference</th>
									<th width="50">%</th>
									<th width="100">On Time Rcv Qty</th>
									<th width="100">Total Rcv Qty</th>
									<th width="100">Rcv Balance</th>
									<th>OTD %</th>
								</tr>
							</thead>
							<tbody>
								<?
								$m=1;
								foreach($supplier_wise_sammary as $supplier_id=>$val)
								{
									$otd=(($val["ontimeRcv"]/$val["wo_qnty"])*100);
									$supplier_wise_sammary[$supplier_id]["otd"]=$otd;
								}
	
								foreach($supplier_wise_sammary as $supplier_id=>$val)
								{
									$sid[$supplier_id]  = $val["otd"];
								}
								array_multisort($sid, SORT_DESC, $supplier_wise_sammary);
								foreach($supplier_wise_sammary as $val)
								{
									if ($m%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
									$rcv_bal=$val["wo_qnty"]-$val["rcv_qnty"];
									$otd=(($val["ontimeRcv"]/$val["wo_qnty"])*100);//$supplierArr[
									?>
									<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
										<td><? echo $m; ?></td>
										<td><? echo $val["supp_comp"]; ?></td>
										<td align="right"><? echo number_format($val["wo_qnty"],2); ?></td>
										 <td align="right"><? echo number_format($val["wo_value"],4); ?></td>
										 <td align="right"><? echo number_format($val["pre_value"],2); ?></td>
										  <td align="right"><? $different=$val["pre_value"]-$val["wo_value"];echo number_format($different,2); ?></td>
										   <td align="right"><? echo number_format((($different/$val["pre_value"])*100),2); ?></td>
										   
										  
										<td align="right"><? echo number_format($val["ontimeRcv"],2); ?></td>
										<td align="right"><? echo number_format($val["rcv_qnty"],2); ?></td>
										<td align="right"><? echo number_format($rcv_bal,2); ?></td>
										<td align="right"><? echo number_format($val["otd"],2); ?></td>
									</tr>
									<?
									$i++;$m++;
									$sup_tot_wo_qnty+=$val["wo_qnty"];
									 $sup_tot_wo_value+=$val["wo_value"];
									  $sup_tot_pre_value+=$val["pre_value"];
									   $sup_tot_different+=$different;
									   // $sup_tot_wo_qnty+=$val["wo_qnty"];
										
									$sup_tot_ontime_rcv+=$val["ontimeRcv"];
									$sup_tot_rcv_qnty+=$val["rcv_qnty"];
									$sup_tot_rcv_bal+=$rcv_bal;//$val["rcv_bal"];
								}
								$sup_tot_otd=(($sup_tot_ontime_rcv/$sup_tot_wo_qnty)*100);
								?>
							</tbody>
							<tfoot>
								<tr>
									<th>&nbsp;</th>
									<th>Total:</th>
									<th align="right"><? echo number_format($sup_tot_wo_qnty,2); ?></th>
									<th align="right"><? echo number_format($sup_tot_wo_value,4); ?></th>
									 <th align="right"><? echo number_format($sup_tot_pre_value,2); ?></th>
									  <th align="right"><? echo number_format($sup_tot_different,2); ?></th>
									  <th align="right"><? echo number_format((($sup_tot_different/$sup_tot_pre_value)*100),2); ?></th>
									
									<th align="right"><? echo number_format($sup_tot_ontime_rcv,2); ?></th>
									<th align="right"><? echo number_format($sup_tot_rcv_qnty,2); ?></th>
									<th align="right"><? echo number_format($sup_tot_rcv_bal,2); ?></th>
									<th align="right"><? echo number_format($sup_tot_otd,2); ?></th>
								</tr>
							</tfoot>
						</table>
					</td>
				</tr>
			</table>
		</fieldset>
		<?
	}
	else if($cbo_category==25) //Emblishment
	{
		$color_namerArr = return_library_array("select id,color_name from lib_color ","id","color_name");
		$sql_result=sql_select($sql);
			$all_po_id="";
			foreach($sql_result as $row)
			{
				if($all_po_id=="") $all_po_id=$row[csf("po_id")];else $all_po_id.=",".$row[csf("po_id")];
				//$emblish_arr[$row[csf("po_id")]]["po_id"]=$row[csf("po_id")];
			}
			//echo $all_po_id.'ds';
			$poIds=chop($all_po_id,','); $po_cond_for_in=""; //$order_cond1=""; $order_cond2=""; $precost_po_cond="";
			$po_ids=count(array_unique(explode(",",$all_po_id)));
			if($db_type==2 && $po_ids>1000)
			{
			$po_cond_for_in=" and (";
			$po_cond_for_in2=" and (";
			$poIdsArr=array_chunk(explode(",",$poIds),999);
			foreach($poIdsArr as $ids)
			{
			$ids=implode(",",$ids);
			//$poIds_cond.=" po_break_down_id in($ids) or ";
			$po_cond_for_in.=" b.id in($ids) or"; 
			$po_cond_for_in2.=" d.po_break_down_id in($ids) or"; 
			}
			$po_cond_for_in=chop($po_cond_for_in,'or ');
			$po_cond_for_in.=")";
			}
			else
			{
			$po_ids=implode(",",(array_unique(explode(",",$all_po_id))));
			$po_cond_for_in=" and b.id in($po_ids)";
		//	$po_cond_for_in2=" and d.po_break_down_id  in($po_ids)";
			}
			if(!empty($all_po_id))
			{
				$tna_po=$po_cond_for_in;
			}
			

			$sql_date="SELECT a.task_finish_date,a.po_number_id,a.task_number from tna_process_mst a, wo_po_break_down b where a.po_number_id = b.id and a.status_active=1 and b.is_deleted=0 and A.TASK_NUMBER in (70,71) $tna_po  group by a.task_finish_date,a.po_number_id,a.task_number,a.task_category";
			//echo $sql_date;

			$date_arr=array();

			$sql_result_date=sql_select($sql_date);
			foreach ($sql_result_date as $row) 
			{
				$date_arr[$row[csf('po_number_id')]][$row[csf('task_number')]]=$row[csf('task_finish_date')];
			}
			$order_sql=sql_select("select a.id as job_id, a.job_no, a.job_no_prefix_num, $select_year, a.style_ref_no, b.id as po_id, b.po_number,a.dealing_marchant,a.team_leader,b.grouping  from  wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and b.status_active=1 $po_cond_for_in");
			//grouping field
			$order_data_arr=array();
			//echo $order_sql[csf("po_id")];die;
			$i = 0;
			foreach($order_sql as $row)
			{
			$order_data_arr[$row[csf("po_id")]]["po_id"]=$row[csf("po_id")];
			$order_data_arr[$row[csf("po_id")]]["job_id"]=$row[csf("job_id")];
			$order_data_arr[$row[csf("po_id")]]["job_no"]=$row[csf("job_no")];
			$order_data_arr[$row[csf("po_id")]]["job_no_prefix_num"]=$row[csf("job_no_prefix_num")];
			$order_data_arr[$row[csf("po_id")]]["job_year"]=$row[csf("job_year")];
			$order_data_arr[$row[csf("po_id")]]["style_ref_no"]=$row[csf("style_ref_no")];
			$order_data_arr[$row[csf("po_id")]]["po_number"]=$row[csf("po_number")];
			$order_data_arr[$row[csf("po_id")]]["grouping"]=$row[csf("grouping")];
			$order_data_arr[$row[csf('po_id')]]['team_leader']=$row[csf('team_leader')];
			$order_data_arr[$row[csf('po_id')]]['dealing_marchant']=$row[csf('dealing_marchant')];
			//echo $order_data_arr[$row[csf("po_id")]]["po_number"];
			}
	
			$condition= new condition();
			$condition->company_name("=$cbo_company");
			if(str_replace("'","",$cbo_buyer)>0){
			$condition->buyer_name("=$cbo_buyer");
			}
			
			if($all_po_id!='')
			{
			$po_ids=implode(",",(array_unique(explode(",",$all_po_id))));
			$condition->po_id_in("$po_ids"); 
			}
			
			$condition->init();
			$emblishment= new emblishment($condition);
			$wash= new wash($condition);
			//echo $wash->getQuery();die;
			//echo $emblishment->getQuery(); die;
			$emblishment_qty_arr=$emblishment->getQtyArray_by_orderEmbnameAndEmbtype();
			$emblishment_amt_arr=$emblishment->getAmountArray_by_orderEmbnameAndEmbtype();
			$emblishment_wash_qty_arr=$wash->getQtyArray_by_orderEmbnameAndEmbtype();
			$emblishment_wash_amt_arr=$wash->getAmountArray_by_orderEmbnameAndEmbtype();
			//print_r($emblishment_wash_qty_arr);	
			$emb_sql=sql_select( "select c.id,c.emb_type,c.emb_name from  wo_po_break_down b,wo_pre_cost_embe_cost_dtls c where b.job_id=c.job_id and c.status_active=1 $po_cond_for_in");
			$pre_embl_arr=array();
			foreach($emb_sql as $row)
			{	
				$pre_embl_arr[$row[csf("id")]]["emb_type"]=$row[csf("emb_type")];
				$pre_embl_arr[$row[csf("id")]]["emb_name"]=$row[csf("emb_name")];
			}
			unset($emb_sql);
			
	?>
    <fieldset>
        <table width="2780"  cellspacing="0" >
            <tr class="form_caption" style="border:none;">
                <td align="center" style="border:none; font-size:18px;" colspan="22">
                    <? echo $company_library[str_replace("'","",$cbo_company_id)]; ?>
                </td>
            </tr>
            <tr class="form_caption" style="border:none;">
                <td align="center" style="border:none;font-size:16px; font-weight:bold"  colspan="25"> <? echo $report_title ;?></td>
            </tr>
            <tr class="form_caption" style="border:none;">
                <td align="center" style="border:none;font-size:12px; font-weight:bold"  colspan="25"> <? echo "From ".change_date_format($date_from)." To ".change_date_format($date_to);?></td>
            </tr>
        </table>
        <table width="2540" cellspacing="0" border="1" class="rpt_table" rules="all">
            <thead>
                <th width="40">SL</th>
                <th width="110">WO No</th>
                <th width="70">Approved Date</th>
                <th width="110">Internal Ref No</th>
                <th width="70">WO Date</th>
                <th width="70">Delivery Date</th>
                <th width="70">Lead Time</th>
                <th width="90">WO Type</th>
                <th width="100">Supplier</th>
                <th width="100">Buyer</th>
                <th width="50">Job Year</th>
                <th width="50">Job No.</th>
                <th width="100">Style Ref.</th>
                <th width="100">Order No</th>
                <th width="120">Emblishment Name</th>
                <th width="150">Emblishment Type</th>
                <th width="100">Item Category</th>
                <th width="60">Color</th>
                <th width="80">WO Qty</th>
                <th width="70">WO Unit price</th>
                <th width="80">WO value</th>
                <th width="70">Budget Unit price</th>
                <th width="80">Precost value</th>
                <th width="80" title="(Precost value - WO value)">Defference</th>
                <th width="80" title="(Defference / Precost value)*100">Defference %</th>
                
                <th width="120">Dealing Merchant</th>
                <th width="120">Team Leader</th>
                <th width="120">User Name</th>
                <th >Remarks</th>
            </thead>
        </table>
        <div style="max-height:350px; overflow-y:scroll; width:2560px" id="scroll_body" >
            <table cellspacing="0" cellpadding="0" border="1" class="rpt_table"  width="2540" rules="all" id="table_body" >
            <?
			
			$i=1;$item_group_sammary=array();
			$total_precost_value =0; $total_deference=0; $total_deference_per =0;

			foreach($sql_result as $row)
			{
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				
				if ($row[csf("wo_type")]==6)
					{
						if ($row[csf("is_short")]==1)
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
					
				$lead_time=datediff("d", $row[csf("wo_date")], $row[csf("delivery_date")]);
				$emb_type_id=$pre_embl_arr[$row[csf("pre_cost_fabric_cost_dtls_id")]]["emb_type"];
				$emb_name=$pre_embl_arr[$row[csf("id")]]["emb_name"]=$pre_embl_arr[$row[csf("pre_cost_fabric_cost_dtls_id")]]["emb_name"];
				if($emb_name==1)//Print
				{
				$emb_type=$emblishment_print_type[$pre_embl_arr[$row[csf("pre_cost_fabric_cost_dtls_id")]]["emb_type"]];
				}
				elseif($emb_name==2)//EMBRO
				{
				$emb_type=$emblishment_embroy_type[$pre_embl_arr[$row[csf("pre_cost_fabric_cost_dtls_id")]]["emb_type"]];
				}
				elseif($emb_name==4)//Spcial
				{
				$emb_type=$emblishment_spwork_type[$pre_embl_arr[$row[csf("pre_cost_fabric_cost_dtls_id")]]["emb_type"]];
				}
				elseif($emb_name==5)//Gmt
				{
				$emb_type=$emblishment_gmts_type[$pre_embl_arr[$row[csf("pre_cost_fabric_cost_dtls_id")]]["emb_type"]];
				}
				elseif($emb_name==3)//Wash
				{
				$emb_type=$emblishment_wash_type[$pre_embl_arr[$row[csf("pre_cost_fabric_cost_dtls_id")]]["emb_type"]];
				}
				
				if($emb_name!=3)//Without Wash
				{
				 $precost_qty=$emblishment_qty_arr[$row[csf("po_id")]][$emb_name][$emb_type_id];
				 $precost_value=$emblishment_amt_arr[$row[csf("po_id")]][$emb_name][$emb_type_id];
				}
				else 
				{
				 $precost_qty=$emblishment_wash_qty_arr[$row[csf("po_id")]][$emb_name][$emb_type_id];
				 $precost_value=$emblishment_wash_amt_arr[$row[csf("po_id")]][$emb_name][$emb_type_id];
				}
				$currency_id=$row[csf("currency_id")];
				if($currency_id==1) //TK exchange_rate
				{
					$wo_rate=$row[csf("rate")]/$row[csf("exchange_rate")];
				}
				else
				{
					$wo_rate=$row[csf("rate")];
				}
				
				
				if($row[csf("wo_qnty")]>0 && $emb_name>0)
				{
					$item_group_sammary[$emb_name]["wo_qnty"]+=$row[csf("wo_qnty")];
					$item_group_sammary[$emb_name]["wo_value"]+=$row[csf("wo_qnty")]*$wo_rate;
					$item_group_sammary[$emb_name]["pre_value"]+=$precost_value;
					//echo $precost_value.',';
					
					$item_group_sammary[$emb_name]["ontimeRcv"]+=$ontimeRcv;
					$item_group_sammary[$emb_name]["rcv_qnty"]+=$rcv_qnty;
					$item_group_sammary[$emb_name]["emblishment_name"]=$emb_name;
				}

				if($row[csf("supplier_id")]>0 && $row[csf("wo_qnty")]>0)
				{
					$supplier_wise_sammary[$row[csf("supplier_id")]]["wo_qnty"]+=$row[csf("wo_qnty")];
					$supplier_wise_sammary[$row[csf("supplier_id")]]["wo_value"]+=$row[csf("wo_qnty")]*$wo_rate;
					$supplier_wise_sammary[$row[csf("supplier_id")]]["pre_value"]+=$precost_value;
					
					$supplier_wise_sammary[$row[csf("supplier_id")]]["ontimeRcv"]+=$ontimeRcv;
					$supplier_wise_sammary[$row[csf("supplier_id")]]["rcv_qnty"]+=$rcv_qnty;
					$supplier_wise_sammary[$row[csf("supplier_id")]]["supplier_id"]=$row[csf("supplier_id")];
				}
				if($row[csf("is_approved")]==1)
				{
					$approved_date=$row[csf("update_date")];
					$approved_date = date('Y-m-d', strtotime($approved_date));
				}
				else $approved_date='';
				
				if($row[csf("pay_mode")]==3 || $row[csf("pay_mode")]==5) $supplier_com=$company_library[$row[csf("supplier_id")]]; else $supplier_com=$supplierArr[$row[csf("supplier_id")]];
				
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
                	<td width="40" align="center"><? echo $i;?></td>
                    <td width="110"><p><? echo $row[csf("booking_no")]; ?></td>
                    <td width="70"><p><? echo $approved_date; ?></td>
                    <td width="110"><p><? echo $order_data_arr[$row[csf("po_id")]]["grouping"]; ?></td>
                    <td width="70" align="center"><p><? echo change_date_format($row[csf("wo_date")]); ?>&nbsp;</p></td>
                    <td width="70" align="center"><p><?
                    	$date_del="";
						if($trim_type[$row[csf('trim_group')]]==1)
						{
							$date_del=$date_arr[$row[csf('po_id')]][70];
						}
						else{
							$date_del=$date_arr[$row[csf('po_id')]][71];
						}
						if(empty($date_del))
						{
							$date_del=$row[csf("delivery_date")];
						}
				 		echo change_date_format($row[csf("delivery_date")]); 
                     
                      ?>&nbsp;</p></td>
                    <td width="70" align="center"><p><? if($lead_time>0 && $lead_time<2) echo $lead_time." Day"; elseif($lead_time>1) echo $lead_time." Days"; else echo "0 Day"; ?>&nbsp;</p></td>
                    <td width="90"><p><?  echo $wo_type; ?>&nbsp;</p></td>
                    <td width="100"><p><? echo $supplier_com; ?>&nbsp;</p></td>
                    <td width="100"><p><? echo $buyerArr[$row[csf("buyer_id")]]; ?>&nbsp;</p></td>
                    <td width="50" align="center"><p><? echo $order_data_arr[$row[csf("po_id")]]["job_year"]; ?>&nbsp;</p></td>
                    <td width="50" align="center"><p><? echo $order_data_arr[$row[csf("po_id")]]["job_no_prefix_num"]; ?>&nbsp;</p></td>
                    <td width="100"><p><? echo $order_data_arr[$row[csf("po_id")]]["style_ref_no"]; ?>&nbsp;</p></td>
                    <td width="100"  style="word-break:break-all"><p><? echo $order_data_arr[$row[csf("po_id")]]["po_number"]; ?>&nbsp;</p></td>
                    <td width="120"><p><? echo $emblishment_name_array[$emb_name]; ?>&nbsp;</p></td>
                    <td width="150" style="word-break:break-all"><p><?
					
					echo $emb_type; ?>&nbsp;</p></td>
                    <td width="100"><p><? echo $item_category[$row[csf("item_category")]]; ?>&nbsp;</p></td>
                    <td width="60"><p><? echo $color_namerArr[$row[csf("gmts_color_id")]]; ?>&nbsp;</p></td>
                    <td width="80" align="right"><p><? echo number_format($row[csf("wo_qnty")],2,'.',''); ?></p></td>
                    <td width="70" align="right" title="USD Rate"><p><? echo number_format($wo_rate,2,'.',''); ?>&nbsp;</p></td>
                    <td width="80" align="right" ><p><? $wo_value=$row[csf("wo_qnty")]*$wo_rate; echo number_format($wo_value,4,'.',''); ?></p></td>
                    <td width="70" align="right" title="Pre Embl Qty=(<? echo $precost_qty;?>) "><p><? echo number_format($precost_value/$precost_qty,2,'.',''); ?></p></td>
                    <td width="80" align="right"><p><? //$precost_value=$row[csf("wo_qnty")]*$pre_cost_data_arr[$row[csf("po_id")]][$row[csf("trim_group")]];
					 $pre_cost_value=$precost_value;//$row[csf("wo_qnty")]*($precost_value/$precost_qty);
					 echo number_format($pre_cost_value,2); ?></p></td>
                    <td width="80" align="right"><p><? $deference = $pre_cost_value-$wo_value; echo number_format($deference,2,'.',''); ?></p></td>
                    <td width="80" align="right"><p><? $deference_per = ($deference/$pre_cost_value)*100; echo number_format($deference_per,2,'.',''); ?></p></td>
                   
                    <td width="120"><p><? echo $lib_team_member_arr[$order_data_arr[$row[csf("po_id")]]['dealing_marchant']]; ?></p></td>
                    <td width="120"><p><? echo $lib_team_leader_name_arr[$order_data_arr[$row[csf("po_id")]]['team_leader']] ; ?></p></td>
                    <td width="120" align="center"><p><? echo $userArr[$row[csf("inserted_by")]]; ?></p></td>
                    <td ><p><? echo $row[csf("remarks")]; ?> &nbsp;</p></td>
				</tr>
				<?
				$tot_wo_qnty+=$row[csf("wo_qnty")];
				$tot_wo_value+=$wo_value;
				$tot_receive_qnty+=$rcv_qnty;
				$tot_receive_value+=$rcv_value;
				$tot_rcv_balance+=$rcv_balance;
				$total_ontime_rcv+=$ontimeRcv;
				$total_precost_value +=$pre_cost_value;
				$total_deference += $deference;
				//$total_deference_per +=$deference_per;

				$i++;
			}
			?>
                <tfoot>
                    <th >&nbsp;</th>
                    <th >&nbsp;</th>
                    <th >&nbsp;</th>
                    <th >&nbsp;</th>
                    <th >&nbsp;</th>
                    <th >&nbsp;</th>
                    <th >&nbsp;</th>
                    <th >&nbsp;</th>
                    <th >&nbsp;</th>
                    <th >&nbsp;</th>
                    <th >&nbsp;</th>
                    <th >&nbsp;</th>
                    <th >&nbsp;</th>
                    <th >&nbsp;</th>
                    <th >&nbsp;</th>
                    <th >&nbsp;</th>
                    <th >&nbsp;</th>
                    <th >Total:</th>
                    <th id="value_tot_wo_qnty" align="right"><? echo number_format($tot_wo_qnty,2) ;?></th>
                    <th >&nbsp;</th>
                    <th id="value_tot_wo_value" align="right"><? echo number_format($tot_wo_value,4) ;?></th>
                    <th >&nbsp;</th>
                    <th id="value_tot_precost_value" align="right"><? echo number_format($total_precost_value,2) ;?></th>
                    <th id="value_tot_deference" align="right"><? echo number_format($total_deference,2) ;?></th>
                    <th id="value_tot_deference_per" align="right"><? echo number_format(($total_deference/$total_precost_value)*100,2) ;?></th>
                    
                    <th >&nbsp;</th>
                    <th >&nbsp;</th>
                    <th >&nbsp;</th>
                    <th >&nbsp;</th>
                </tfoot>
            </table>
        </div>
        <br>
        <table cellspacing="0" cellpadding="0" border="0" width="1500" rules="all">
        	<tr>
            	<td valign="top">

                    <table cellspacing="0" cellpadding="0" border="1" class="rpt_table"  width="860" rules="all">

                        <thead>
                        	<tr bgcolor="#CCCCCC">
                                <td colspan="8" align="center" style="font-size:16px; font-weight:bold;">Emblishment Name Wise Summary</td>
                            </tr>
                            <tr>
                                <th width="50">SL</th>
                                <th width="150">Item Name</th>
                                <th width="100">WO Qty</th>
                                <th width="100">WO Value</th>
                                <th width="100">Precost value</th>
                                <th width="100">Difference</th>
                                <th width="60">%</th>
                               
                            </tr>
                        </thead>
                        <tbody>
                            <?
                            $k=1;
							//print_r($item_group_sammary);die;
							foreach($item_group_sammary as $item_grp_id=>$val)
                            {
                               	$otd=(($val["ontimeRcv"]/$val["wo_qnty"])*100);
								$item_group_sammary[$item_grp_id]["otd"]=$otd;
							}

							foreach($item_group_sammary as $item_group=>$val)
							{
								$mid[$item_group]  = $val["otd"];
							}
							array_multisort($mid, SORT_DESC, $item_group_sammary);

                            foreach($item_group_sammary as $val)
                            {
                                if ($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                               // $rcv_bal=$val["wo_qnty"]-$val["rcv_qnty"];
                               //$otd=(($val["ontimeRcv"]/$val["wo_qnty"])*100);
                                ?>
                                <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
                                    <td><? echo $k; ?></td>
                                    <td>
									<?
									//echo $trimsGroupArr[$item_group];
									echo $emblishment_name_array[$val["emblishment_name"]];
									?></td>
                                    <td align="right"><? echo number_format($val["wo_qnty"],2); ?></td>
                                     <td align="right"><? echo number_format($val["wo_value"],4); ?></td>
                                     <td align="right"><? echo number_format($val["pre_value"],2); ?></td>
                                     <td align="right"><? $difference=$val["pre_value"]-$val["wo_value"];echo number_format($difference,2); ?></td>
                                     <td align="right" title="Difference/Pre value*100"><? echo number_format((($difference/$val["pre_value"])*100),2); ?></td>
                                   
                                </tr>
                                <?
                                $i++;$k++;
                                $sum_tot_wo_qnty+=$val["wo_qnty"];
								 $sum_tot_wo_value+=$val["wo_value"];
								  $sum_tot_pre_value+=$val["pre_value"];
								   $sum_tot_difference+=$difference;
								  
                                $sum_tot_ontime_rcv+=$val["ontimeRcv"];
                                $sum_tot_rcv_qnty+=$val["rcv_qnty"];
                                $sum_tot_rcv_bal+=$rcv_bal;//$val["rcv_bal"];
                            }
                            $tot_otd=(($sum_tot_ontime_rcv/$sum_tot_wo_qnty)*100);
                            ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th>&nbsp;</th>
                                <th>Total:</th>
                                <th align="right"><? echo number_format($sum_tot_wo_qnty,2); ?></th>
                                <th align="right"><? echo number_format($sum_tot_wo_value,4); ?></th>
                                <th align="right"><? echo number_format($sum_tot_pre_value,2); ?></th>
                                <th align="right"><? echo number_format($sum_tot_difference,2); ?></th>
                                <th align="right"><? echo number_format((($sum_tot_difference/$sum_tot_pre_value)*100),2); ?></th>
                                
                            </tr>
                        </tfoot>
                    </table>

                </td>
                <td valign="top">&nbsp;</td>
                <td valign="top">
                	<table cellspacing="0" cellpadding="0" border="1" class="rpt_table"  width="760" rules="all">

                        <thead>
                        	<tr bgcolor="#CCCCCC">
                                <td colspan="8" align="center" style="font-size:16px; font-weight:bold;">Supplier Wise Summary</td>
                            </tr>
                            <tr>
                                <th width="50">SL</th>
                                <th width="150">Supplier Name</th>
                                <th width="100">WO Qty</th>
                                <th width="100">WO Value</th>
                                <th width="100">Precost value</th>
                                <th width="100">Difference</th>
                                <th width="60">%</th>
                                
                               
                            </tr>
                        </thead>
                        <tbody>
                            <?
                            $m=1;
							foreach($supplier_wise_sammary as $supplier_id=>$val)
                            {
                                $otd=(($val["ontimeRcv"]/$val["wo_qnty"])*100);
								$supplier_wise_sammary[$supplier_id]["otd"]=$otd;
							}

							foreach($supplier_wise_sammary as $supplier_id=>$val)
							{
								$sid[$supplier_id]  = $val["otd"];
							}
							array_multisort($sid, SORT_DESC, $supplier_wise_sammary);
                            foreach($supplier_wise_sammary as $val)
                            {
                                if ($m%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                                $rcv_bal=$val["wo_qnty"]-$val["rcv_qnty"];
                                $otd=(($val["ontimeRcv"]/$val["wo_qnty"])*100);
                                ?>
                                <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
                                    <td><? echo $m; ?></td>
                                    <td><? echo $supplierArr[$val["supplier_id"]]; ?></td>
                                    <td align="right"><? echo number_format($val["wo_qnty"],2); ?></td>
                                    
                                    <td align="right"><? echo number_format($val["wo_value"],2); ?></td>
                                    <td align="right"><? echo number_format($val["pre_value"],2); ?></td>
                                    <td align="right"><? $diference=$val["wo_value"]-$val["pre_value"];echo number_format($diference,2); ?></td>
                                    <td align="right"><? echo number_format($val["wo_qnty"],2); ?></td>
                                   
                                </tr>
                                <?
                                $i++;$m++;
                                $sup_tot_wo_qnty+=$val["wo_qnty"];
								$sup_tot_wo_value+=$val["wo_value"];
								$sup_tot_pre_value+=$val["pre_value"];
								$sup_tot_diference+=$diference;
								
                                $sup_tot_ontime_rcv+=$val["ontimeRcv"];
                                $sup_tot_rcv_qnty+=$val["rcv_qnty"];
                                $sup_tot_rcv_bal+=$rcv_bal;//$val["rcv_bal"];
                            }
                            $sup_tot_otd=(($sup_tot_ontime_rcv/$sup_tot_wo_qnty)*100);
                            ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th>&nbsp;</th>
                                <th>Total:</th>
                                <th align="right"><? echo number_format($sup_tot_wo_qnty,2); ?></th>
                                <th align="right"><? echo number_format($sup_tot_wo_value,2); ?></th>
                                <th align="right"><? echo number_format($sup_tot_pre_value,2); ?></th>
                                <th align="right"><? echo number_format($sup_tot_diference,2); ?></th>
                                <th align="right"><? echo number_format((($sup_tot_diference/$sup_tot_pre_value)*100),2); ?></th>
                                
                               
                            </tr>
                        </tfoot>
                    </table>
                </td>
            </tr>
        </table>
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

if($action=="booking_issue_info")
{
	echo load_html_head_contents("Receive Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$item_description_ref=explode("__",$item_description);
	$description="";
	foreach($item_description_ref as $des)
	{
		$description.="'".$des."',";
	}
	$description=chop($description,",");
	//$description=$item_description_ref[0];
	//$brand_supp=$item_description_ref[1];
	$book_id=str_replace("'","",$book_id);
	
	//echo $book_id;die;
	?>
	<!--<div style="width:780px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>-->
	<fieldset style="width:840px; margin-left:3px">
		<div id="scroll_body" align="center">
			<table border="1" class="rpt_table" rules="all" width="840" cellpadding="0" cellspacing="0" align="center">
				<thead>
                    <th width="50">Sl</th>
                    <th width="50">Prod. ID</th>
                    <th width="50">PO. ID</th>
                    <th width="80">PO No.</th>
                    <th width="120">Job No</th>
                    <th width="120">Issue. ID</th>
                    <th width="80">Challan No</th>
                    <th width="70">Issue. Date</th>
                    <th width="150">Item Description.</th>
                    <th >Issue. Qty.</th>
				</thead>
                <tbody>
                <?
				$color_cond="";
				if($color>0){
					$color_cond="and c.item_color=$color";
				}
					$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name"  );
					$i=1;

					$mst_cond="";
					if($mst_id!="") $mst_cond=" and d.id in($mst_id)";
					if($po_id!="") $po_cond=" and b.po_breakdown_id";
					if($book_id) $book_cond=" and g.id=$book_id";
					
					$job_issue_qty_data=sql_select("SELECT e.po_number,f.job_no  from  inv_issue_master d join inv_trims_issue_dtls a on a.mst_id=d.id join product_details_master p on a.prod_id=p.id join order_wise_pro_details b on a.trans_id=b.trans_id join  wo_po_break_down e on e.id=b.po_breakdown_id join wo_booking_dtls f on e.id=f.po_break_down_id join wo_booking_mst g on g.booking_no=f.booking_no where  b.trans_type=2 and b.entry_form=25 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and f.booking_type=2 and f.is_short=2 and f.is_deleted=0 and f.status_active=1 and g.is_deleted=0 and g.status_active=1 and d.item_category=4 and p.item_group_id='$item_name' and p.item_description in($description) group by e.po_number,f.job_no");
					$job_dtlsArray=sql_select($job_issue_qty_data);
					foreach($job_dtlsArray as $row)
					{
						$po_number=$row[csf('po_number')];
						$job_no=$row[csf('job_no')];
					}
					//unset($job_dtlsArray);
					$issue_qty_data="select a.prod_id,d.issue_number,d.issue_date,d.id,b.po_breakdown_id,b.quantity,a.rate,b.order_amount,p.item_description, p.item_group_id  from  inv_issue_master d join inv_trims_issue_dtls a on a.mst_id=d.id join product_details_master p on a.prod_id=p.id join order_wise_pro_details b on a.trans_id=b.trans_id  where  b.trans_type=2 and b.entry_form=25 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and p.item_group_id='$item_name' and p.item_description in($description)";
							

					//echo $receive_qty_data;

					$dtlsArray=sql_select($issue_qty_data);
					foreach($dtlsArray as $row)
					{
						
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							?>
							<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
								<td align="center"><p><? echo $i; ?></p></td>
								<td align="center"><p><? echo $row[csf('prod_id')]; ?></p></td>
								<td align="center"><p><? echo $row[csf('po_breakdown_id')]; ?></p></td>
								<td ><p><? echo $po_number; ?></p></td>
								<td ><p><? echo $job_no; ?></p></td>
								<td ><p><? echo $row[csf('issue_number')]; ?></p></td>
								<td ><p><? echo $row[csf('challan_no')]; ?></p></td>
								<td align="center"><p><? echo  change_date_format($row[csf('issue_date')]); ?></p></td>
								<td ><p><? echo $row[csf('item_description')]; ?></p></td>
								<td align="right"><p><? echo number_format($row[csf('quantity')],2); ?></p></td>
							</tr>
							<?
							$tot_qty+=$row[csf('quantity')];
							$i++;

					}
				?>
                </tbody>
                <tfoot>
                	<tr class="tbl_bottom">
                    	<td colspan="8" align="right"></td>
                        <td align="right">Total</td>
                        <td><? echo number_format($tot_qty,2); ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </fieldset>
    <?
	exit();
}

if($action=="booking_inhouse_info")
{
	echo load_html_head_contents("Receive Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$item_description_ref=explode("__",$item_description);
	$description="";
	foreach($item_description_ref as $des)
	{
		$description.="'".$des."',";
	}
	$description=chop($description,",");
	//$description=$item_description_ref[0];
	//$brand_supp=$item_description_ref[1];
	$book_id=str_replace("'","",$book_id);
	$brand_supp_con="";
	if($brand_supp!=0){
		$brand_supp_cond="and c.brand_supplier='$brand_supp'";
	}
	
	//echo $book_id;die;
	?>
	<!--<div style="width:780px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>-->
	<fieldset style="width:840px; margin-left:3px">
		<div id="scroll_body" align="center">
			<table border="1" class="rpt_table" rules="all" width="840" cellpadding="0" cellspacing="0" align="center">
				<thead>
                    <th width="50">Sl</th>
                    <th width="50">Prod. ID</th>
                    <th width="50">PO. ID</th>
                    <th width="80">PO No.</th>
                    <th width="120">Job No</th>
                    <th width="120">Recv. ID</th>
                    <th width="80">Challan No</th>
                    <th width="70">Recv. Date</th>
                    <th width="150">Item Description.</th>
                    <th >Recv. Qty.</th>
				</thead>
                <tbody>
                <?
				$color_cond="";
				if($color>0){
					$color_cond="and c.item_color=$color";
				}
					$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name"  );
					$i=1;

					$mst_cond="";
					if($mst_id!="") $mst_cond=" and a.id in($mst_id)";
					if($po_id!="") $po_cond=" and d.po_breakdown_id";
					if($book_id) $book_cond=" and c.booking_id=$book_id";
					if($book_id) $book_cond2=" and a.booking_id=$book_id";
					if($book_id) $book_cond_pi=" and f.work_order_id=$book_id";
					if($popup_type==1)
					{
						if($po_id!='')
						{
	
							$receive_qty_data="select a.id as mst_id, a.recv_number, a.receive_date, b.pi_wo_batch_no, b.prod_id, d.id as prop_id, d.quantity as rcv_qnty, c.item_group_id, c.item_description ,a.challan_no, d.po_breakdown_id, e.po_number, e.job_no_mst , 1 as type
							from inv_receive_master a, inv_transaction b, inv_trims_entry_dtls c, order_wise_pro_details d, wo_po_break_down e
							where a.id=b.mst_id and b.id=c.trans_id and c.id=d.dtls_id and c.trans_id=d.trans_id and d.po_breakdown_id=e.id and  b.transaction_type=1 and b.item_category=4 and a.entry_form=24 and d.entry_form=24 and b.receive_basis<>1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.po_breakdown_id=$po_id  and c.item_group_id='$item_name' and c.item_description in($description) and a.is_deleted=0 $mst_cond $book_cond2 $color_cond $brand_supp_cond
							union all 
							select a.id as mst_id, a.recv_number, a.receive_date, b.pi_wo_batch_no, b.prod_id, d.id as prop_id, d.quantity as rcv_qnty, c.item_group_id, c.item_description ,a.challan_no, d.po_breakdown_id, e.po_number, e.job_no_mst , 2 as type
							from inv_receive_master a, inv_trims_entry_dtls c, order_wise_pro_details d, wo_po_break_down e, inv_transaction b
							left join com_pi_item_details f on f.pi_id=b.pi_wo_batch_no $book_cond_pi 
							where a.id=b.mst_id and b.id=c.trans_id and c.id=d.dtls_id and c.trans_id=d.trans_id and d.po_breakdown_id=e.id and  b.transaction_type=1 and b.item_category=4 and a.entry_form=24 and d.entry_form=24 and b.receive_basis=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.po_breakdown_id=$po_id  and c.item_group_id='$item_name' and c.item_description in($description) and a.is_deleted=0 $mst_cond $color_cond";
						}
						else
						{
							$receive_qty_data="select  a.id as mst_id, a.recv_number, a.receive_date, b.pi_wo_batch_no, b.prod_id, b.id as prop_id, b.cons_quantity as rcv_qnty, c.item_group_id, c.item_description,a.challan_no
							from inv_receive_master a, inv_transaction b, inv_trims_entry_dtls c
							where a.id=b.mst_id and b.id=c.trans_id and  b.transaction_type=1 and b.item_category=4 and a.entry_form=24 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  and c.item_group_id='$item_name' and c.item_description in($description) and a.is_deleted=0 $mst_cond $book_cond $color_cond $brand_supp_cond";//and  b.pi_wo_batch_no in($book_id)
						}
					}
					else if($popup_type==2)
					{
						
						if($type=='1')
						{//echo "10**".$type; die;
							
							$receive_qty_data="select a.id as mst_id, a.recv_number, a.receive_date, b.pi_wo_batch_no, b.prod_id, d.id as prop_id, d.quantity as rcv_qnty, c.item_group_id, c.item_description ,a.challan_no, d.po_breakdown_id, e.po_number, e.job_no_mst , 1 as type
							from inv_receive_master a, inv_transaction b, inv_trims_entry_dtls c , order_wise_pro_details d, wo_po_break_down e
							where a.id=b.mst_id and b.id=c.trans_id and c.id=d.dtls_id and c.trans_id=d.trans_id and d.po_breakdown_id=e.id and  b.transaction_type=1 and b.item_category=4 and a.entry_form=24 and d.entry_form=24 and b.receive_basis<>1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.po_breakdown_id=$po_id  and c.item_group_id='$item_name' and c.item_description in($description) and a.is_deleted=0 and d.status_active=1 $mst_cond $book_cond2 $color_cond $brand_supp_cond
							union all 
							select a.id as mst_id, a.recv_number, a.receive_date, b.pi_wo_batch_no, b.prod_id, d.id as prop_id, d.quantity as rcv_qnty, c.item_group_id, c.item_description ,a.challan_no, d.po_breakdown_id, e.po_number, e.job_no_mst , 2 as type
							from inv_receive_master a, inv_trims_entry_dtls c , order_wise_pro_details d, wo_po_break_down e, inv_transaction b
							left join com_pi_item_details f on f.pi_id=b.pi_wo_batch_no $book_cond_pi
							where a.id=b.mst_id and b.id=c.trans_id and c.id=d.dtls_id and c.trans_id=d.trans_id and d.po_breakdown_id=e.id and  b.transaction_type=1 and b.item_category=4 and a.entry_form=24 and d.entry_form=24 and b.receive_basis=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.po_breakdown_id=$po_id  and c.item_group_id='$item_name' and c.item_description in($description) and a.is_deleted=0 and d.status_active=1 $mst_cond $color_cond";//and  b.pi_wo_batch_no in($book_id)
						}
						else
						{
							$receive_qty_data="select  a.id as mst_id, a.recv_number, a.receive_date, b.pi_wo_batch_no, b.prod_id, b.id as prop_id, b.cons_quantity as rcv_qnty, c.item_group_id, c.item_description,a.challan_no
							from inv_receive_master a, inv_transaction b, inv_trims_entry_dtls c
							where a.id=b.mst_id and b.id=c.trans_id and  b.transaction_type=1 and b.item_category=4 and a.entry_form=24 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  and c.item_group_id='$item_name' and c.item_description in($description) and a.is_deleted=0 $mst_cond $book_cond $color_cond $brand_supp_cond";//and  b.pi_wo_batch_no in($book_id)
						}
					}

					//echo $receive_qty_data;

					$dtlsArray=sql_select($receive_qty_data);
					foreach($dtlsArray as $row)
					{
						if($prop_id_check[$row[csf('prop_id')]]=="")
						{
							$prop_id_check[$row[csf('prop_id')]]=$row[csf('prop_id')];
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							?>
							<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
								<td align="center"><p><? echo $i; ?></p></td>
								<td align="center"><p><? echo $row[csf('prod_id')]; ?></p></td>
								<td align="center"><p><? echo $row[csf('po_breakdown_id')]; ?></p></td>
								<td ><p><? echo $row[csf('po_number')]; ?></p></td>
								<td ><p><? echo $row[csf('job_no_mst')]; ?></p></td>
								<td ><p><? echo $row[csf('recv_number')]; ?></p></td>
								<td ><p><? echo $row[csf('challan_no')]; ?></p></td>
								<td align="center"><p><? echo  change_date_format($row[csf('receive_date')]); ?></p></td>
								<td ><p><? echo $row[csf('item_description')]; ?></p></td>
								<td align="right"><p><? echo number_format($row[csf('rcv_qnty')],2); ?></p></td>
							</tr>
							<?
							$tot_qty+=$row[csf('rcv_qnty')];
							$i++;
						}
					}
				?>
                </tbody>
                <tfoot>
                	<tr class="tbl_bottom">
                    	<td colspan="8" align="right"></td>
                        <td align="right">Total</td>
                        <td><? echo number_format($tot_qty,2); ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </fieldset>
    <?
	exit();
}

if($action=="booking_rec_info")
{
	echo load_html_head_contents("Receive Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$item_description_ref=explode("__",$item_description);
	$description="";
	foreach($item_description_ref as $des)
	{
		$description.="'".$des."',";
	}
	$description=chop($description,",");
	//$description=$item_description_ref[0];
	//$brand_supp=$item_description_ref[1];
	$book_id=str_replace("'","",$book_id);
	
	//echo $book_id;die;
	?>
	<!--<div style="width:780px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>-->
	<fieldset style="width:740px; margin-left:3px">
		<div id="scroll_body" align="center">
			<table border="1" class="rpt_table" rules="all" width="740" cellpadding="0" cellspacing="0" align="center"  id="table_body">
				<thead>
                    <th width="50">Sl</th>
                    <th>MRR No.</th>
                    <th>Style No</th>
                    <th>Job No</th>
                    <th>Recv. Date</th>
                    <th>Uom</th>
                    <th>Qty.</th>
				</thead>
                <tbody>
                <?
				$color_cond="";
				if($color>0){
					$color_cond="and c.item_color=$color";
				}
					$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name"  );
					$i=1;

					$mst_cond="";
					if($mst_id!="") $mst_cond=" and a.id in($mst_id)";
					if($po_id!="") $po_cond=" and d.po_breakdown_id";
					if($book_id) $book_cond=" and c.booking_id=$book_id";
					if($book_id) $book_cond2=" and a.booking_id=$book_id";
					if($book_id) $book_cond_pi=" and f.work_order_id=$book_id";
					if($popup_type==1)
					{
						if($po_id!='')
						{
							
							/*$receive_qty_data="select a.id as mst_id, a.recv_number, a.receive_date, b.pi_wo_batch_no, b.prod_id, d.quantity as rcv_qnty, c.item_group_id, c.item_description ,a.challan_no, d.po_breakdown_id, e.po_number, e.job_no_mst
							from inv_receive_master a, inv_transaction b, inv_trims_entry_dtls c, order_wise_pro_details d, wo_po_break_down e
							where a.id=b.mst_id and b.id=c.trans_id and c.id=d.dtls_id and c.trans_id=d.trans_id and d.po_breakdown_id=e.id and  b.transaction_type=1 and b.item_category=4 and a.entry_form=24 and d.entry_form=24 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.po_breakdown_id=$po_id  and c.item_group_id='$item_name' and c.item_description='$description' $brand_supp_cond and a.is_deleted=0 $mst_cond $book_cond";//and  b.pi_wo_batch_no in($book_id)
							
							$rcv_qnty_sql="select b.mst_id, b.receive_basis, b.transaction_date as receive_date, c.booking_id as pi_wo_batch_no, b.booking_no, c.item_group_id, c.item_description, c.brand_supplier, d.po_breakdown_id as po_id, d.quantity as rcv_qnty, d.order_amount as rcv_value, e.work_order_id
							from inv_trims_entry_dtls c, order_wise_pro_details d, inv_transaction b 
							left join com_pi_item_details e on e.pi_id=b.pi_wo_batch_no
							where b.id=c.trans_id and c.id=d.dtls_id and c.trans_id=d.trans_id and b.transaction_type=1 and b.item_category=4 and d.entry_form=24 and d.status_active=1 and d.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $po_cond_for_in3 
							group by b.mst_id, b.receive_basis, b.transaction_date, c.booking_id, b.booking_no, c.item_group_id, c.item_description, c.brand_supplier, d.po_breakdown_id, d.quantity, d.order_amount, e.work_order_id";*/
							
							$receive_qty_data="select g.style_ref_no,b.cons_uom,a.id as mst_id, a.recv_number, a.receive_date, b.pi_wo_batch_no, b.prod_id, d.id as prop_id, d.quantity as rcv_qnty, c.item_group_id, c.item_description ,a.challan_no, d.po_breakdown_id, e.po_number, e.job_no_mst , 1 as type
							from inv_receive_master a, inv_transaction b, inv_trims_entry_dtls c, order_wise_pro_details d, wo_po_break_down e,wo_po_details_master g
							where a.id=b.mst_id and b.id=c.trans_id and c.id=d.dtls_id and c.trans_id=d.trans_id and d.po_breakdown_id=e.id and  b.transaction_type=1 and b.item_category=4 and a.entry_form=24 and d.entry_form=24 and b.receive_basis<>1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.po_breakdown_id=$po_id  and c.item_group_id='$item_name' and c.item_description in($description) and g.job_no=e.job_no_mst and a.is_deleted=0 $mst_cond $book_cond2 $color_cond
							union all 
							select g.style_ref_no,b.cons_uom,a.id as mst_id, a.recv_number, a.receive_date, b.pi_wo_batch_no, b.prod_id, d.id as prop_id, d.quantity as rcv_qnty, c.item_group_id, c.item_description ,a.challan_no, d.po_breakdown_id, e.po_number, e.job_no_mst , 2 as type
							from inv_receive_master a, inv_trims_entry_dtls c, order_wise_pro_details d, wo_po_break_down e,wo_po_details_master g, inv_transaction b
							left join com_pi_item_details f on f.pi_id=b.pi_wo_batch_no $book_cond_pi 
							where a.id=b.mst_id and b.id=c.trans_id and c.id=d.dtls_id and c.trans_id=d.trans_id and d.po_breakdown_id=e.id and  b.transaction_type=1 and b.item_category=4 and a.entry_form=24 and d.entry_form=24 and b.receive_basis=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.po_breakdown_id=$po_id  and c.item_group_id='$item_name' and c.item_description in($description) and g.job_no=e.job_no_mst and a.is_deleted=0 $mst_cond $color_cond";
						}
						else
						{
							$receive_qty_data="select  a.id as mst_id, a.recv_number, a.receive_date, b.pi_wo_batch_no, b.prod_id, b.id as prop_id, b.cons_quantity as rcv_qnty, c.item_group_id, c.item_description,a.challan_no
							from inv_receive_master a, inv_transaction b, inv_trims_entry_dtls c
							where a.id=b.mst_id and b.id=c.trans_id and  b.transaction_type=1 and b.item_category=4 and a.entry_form=24 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  and c.item_group_id='$item_name' and c.item_description in($description) and a.is_deleted=0 $mst_cond $book_cond $color_cond";//and  b.pi_wo_batch_no in($book_id)
						}
					}
					else if($popup_type==2)
					{
						
						if($type=='1')
						{//echo "10**".$type; die;
							
							$receive_qty_data="select g.style_ref_no,b.cons_uom,a.id as mst_id, a.recv_number, a.receive_date, b.pi_wo_batch_no, b.prod_id, d.id as prop_id, d.quantity as rcv_qnty, c.item_group_id, c.item_description ,a.challan_no, d.po_breakdown_id, e.po_number, e.job_no_mst , 1 as type
							from inv_receive_master a, inv_transaction b, inv_trims_entry_dtls c , order_wise_pro_details d, wo_po_break_down e,wo_po_details_master g
							where a.id=b.mst_id and b.id=c.trans_id and c.id=d.dtls_id and c.trans_id=d.trans_id and d.po_breakdown_id=e.id and  b.transaction_type=1 and b.item_category=4 and a.entry_form=24 and d.entry_form=24 and b.receive_basis<>1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.po_breakdown_id=$po_id  and c.item_group_id='$item_name' and c.item_description in($description) and g.job_no=e.job_no_mst and a.is_deleted=0 and d.status_active=1 $mst_cond $book_cond2 $color_cond
							union all 
							select g.style_ref_no,b.cons_uom,a.id as mst_id, a.recv_number, a.receive_date, b.pi_wo_batch_no, b.prod_id, d.id as prop_id, d.quantity as rcv_qnty, c.item_group_id, c.item_description ,a.challan_no, d.po_breakdown_id, e.po_number, e.job_no_mst , 2 as type
							from inv_receive_master a, inv_trims_entry_dtls c , order_wise_pro_details d, wo_po_break_down e,wo_po_details_master g, inv_transaction b
							left join com_pi_item_details f on f.pi_id=b.pi_wo_batch_no $book_cond_pi
							where a.id=b.mst_id and b.id=c.trans_id and c.id=d.dtls_id and c.trans_id=d.trans_id and d.po_breakdown_id=e.id and  b.transaction_type=1 and b.item_category=4 and a.entry_form=24 and d.entry_form=24 and b.receive_basis=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.po_breakdown_id=$po_id  and c.item_group_id='$item_name' and c.item_description in($description) and g.job_no=e.job_no_mst and a.is_deleted=0 and d.status_active=1 $mst_cond $color_cond";//and  b.pi_wo_batch_no in($book_id)
						}
						else
						{
							$receive_qty_data="select  a.id as mst_id, a.recv_number, a.receive_date, b.pi_wo_batch_no, b.prod_id, b.id as prop_id, b.cons_quantity as rcv_qnty, c.item_group_id, c.item_description,a.challan_no
							from inv_receive_master a, inv_transaction b, inv_trims_entry_dtls c
							where a.id=b.mst_id and b.id=c.trans_id and  b.transaction_type=1 and b.item_category=4 and a.entry_form=24 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  and c.item_group_id='$item_name' and c.item_description in($description) and a.is_deleted=0 $mst_cond $book_cond $color_cond";//and  b.pi_wo_batch_no in($book_id)
						}
					}

					//echo $receive_qty_data;

					$dtlsArray=sql_select($receive_qty_data);
					foreach($dtlsArray as $row)
					{
						if($prop_id_check[$row[csf('prop_id')]]=="")
						{
							$prop_id_check[$row[csf('prop_id')]]=$row[csf('prop_id')];
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							?>
							<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
								<td align="center"><p><? echo $i; ?></p></td>
								<td align="center"><p><? echo $row[csf('recv_number')]; ?></p></td>
								<td align="center"><p><? echo $row[csf('style_ref_no')]; ?></p></td>
								<td align="center"><p><? echo $row[csf('job_no_mst')]; ?></p></td>
								<td align="center"><p><? echo  change_date_format($row[csf('receive_date')]); ?></p></td>
								<td align="center"><p><? echo $unit_of_measurement[$row[csf("cons_uom")]]?></p></td>
								<td align="right"><p><? echo number_format($row[csf('rcv_qnty')],2); ?></p></td>
							</tr>
							<?
							$tot_qty+=$row[csf('rcv_qnty')];
							$i++;
						}
					}
				?>
                </tbody>
                <tfoot>
                	<tr class="tbl_bottom">
                    	<td colspan="5" align="right"></td>
                        <td align="right">Total</td>
                        <td><? echo number_format($tot_qty,2); ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </fieldset>
    <?
	exit();
}
?>
