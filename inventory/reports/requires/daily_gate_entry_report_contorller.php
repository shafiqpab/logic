<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if($action=="print_button_variable_setting")
{
    $print_report_format_arr = return_library_array("select format_id,format_id from lib_report_template where template_name =$data and module_id=6 and report_id=124 and is_deleted=0 and status_active=1","format_id","format_id");
    echo "print_report_button_setting('".implode(',',$print_report_format_arr)."');\n";
    exit();
}

if ($action=="load_drop_down_supplier")
{	
    echo create_drop_down( "cbo_supplier", 100, "select a.id,a.supplier_name from lib_supplier a,lib_supplier_tag_company b where a.id=b.supplier_id 	 and b.tag_company=$data order by supplier_name","id,supplier_name", 1, "-- Select --", 0, "",0 );  
	exit();
}

if($action=="load_drop_down_sent")
{
	$data = explode("_",$data);
	if($data[0]==1)
	{
		
	    echo create_drop_down( "cbo_search_by", 100, "select id,buyer_name from  lib_buyer  where status_active=1 and is_deleted=0  order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected,"","0" );
    }
	else if($data[0]==2)
	{
	    echo create_drop_down( "cbo_search_by", 100, "select id,supplier_name from  lib_supplier  where status_active=1 and is_deleted=0  order by supplier_name","id,supplier_name", 1, "-- Select Supplier --", $selected,"","0" );
	}
    else if($data[0]==3)
	{
	    echo create_drop_down( "cbo_search_by", 100, "select id,other_party_name from  lib_other_party where status_active=1 and is_deleted=0  order by other_party_name","id,other_party_name", 1, "-- Select Other Party --", $selected,"","0" );
	}
	else
	{
	    echo create_drop_down( "cbo_search_by", 100, $blank_array,"", 1, "-- Select --", $selected, "","","" );	
	}
	
	exit();
}

//style search------------------------------//
if($action=="chalan_surch")
{
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	//echo $style_id;die;

	?>
        
    <script>
	function js_set_value(str)
	{
	
		var splitData = str.split("_");	  	 
		$("#hidden_chalan_id").val(splitData[0]); 
		$("#hidden_chalan_no").val(splitData[1]); 
		$("#hidden_search_number").val($("#cbo_search_by").val()); 
	
		parent.emailwindow.hide();
	}
	</script>

	</head>

	<body>
	<div align="center" style="width:100%;" >
	<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
	<table width="800" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center">
            <thead>
                <tr>                	 
                    <th width="150">Search By</th>
                    <th id="search_by_td_up" width="200">Enter Booking No</th>
                    <th width="200">Date Range</th>
                    <th><input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px" class="formbutton"  /></th>           
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <? 
						$search_arr=array(1=>"Gate in Chalan No",2=>"Gate in System ID",3=>"Gatepass Chalan No",4=>" Gatepass System ID"); 
						$dd="change_search_event(this.value, '0*0*0*0*0', '0*0*0*0*0', '../../') ";
                            echo create_drop_down( "cbo_search_by", 170, $search_arr,"",1, "--Select--", "",$dd,0 );
                        ?>
                    </td>
                     <td align="center" id="search_by_td">				
                        <input type="text" style="width:130px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />	
                    </td>   
                    <td align="center">
                        <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" placeholder="From Date" />
                        <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" placeholder="To Date" />
                     </td> 
                     <td align="center">
                        <input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view( document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $company; ?>+'_'+<? echo $category; ?>, 'create_chalan_search_list_view', 'search_div', 'daily_gate_entry_report_contorller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />				
                    </td>
            </tr>
        	<tr>                  
            	<td align="center" height="40" valign="middle" colspan="4">
					<? echo load_month_buttons(1);  ?>
                    <!-- Hidden field here-------->
                    <input type="hidden" id="hidden_chalan_id" value="" />
                    <input type="hidden" id="hidden_chalan_no" value="" />
                    <input type="hidden" id="hidden_search_number" value="" />
                  
                    <!---------END------------->
                </td>
            </tr>    
            </tbody>
         </tr>         
        </table>    
        <div align="center" valign="top" id="search_div"> </div> 
        </form>
   </div>
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
}

if($action=="create_chalan_search_list_view")
{
	
 	$ex_data = explode("_",$data);
	$txt_search_by = $ex_data[0];
	$txt_search_common = trim($ex_data[1]);
	$txt_date_from = $ex_data[2];
	$txt_date_to = $ex_data[3];
	$company = $ex_data[4];
	$item_category= $ex_data[5];
 	
	$sql_cond="";
	
	if(trim($txt_search_by)==0) { echo "please select Search By";die;}
	if(trim($txt_search_common)!="")
	{
		if(trim($txt_search_by)==1) // for gate in challan
		{
			$sql_cond .= " and a.challan_no LIKE '%$txt_search_common%'";	
		}
		else if(trim($txt_search_by)==2) // for gate in System ID
		{
			$sql_cond .= " and a.sys_number LIKE '%$txt_search_common%'";				
		}

		else if(trim($txt_search_by)==3) // for gatepass  challan
		{
			$sql_cond .= " and a.challan_no LIKE '%$txt_search_common%'";	
		}
		else if(trim($txt_search_by)==4) // for gatepass System ID
		{
			$sql_cond .= " and a.sys_number LIKE '%$txt_search_common%'";				
		}

 	}
	
	if(trim($item_category)!=0) { $sql_cond .= " and b.item_category_id=$item_category"; }
	if(trim($txt_search_by)==1 || trim($txt_search_by)==2)
	{
 		if( $txt_date_from!="" || $txt_date_to!="" ) $sql_cond .= " and a.in_date  between '".change_date_format($txt_date_from,'yyyy-mm-dd')."' and '".change_date_format($txt_date_to,'yyyy-mm-dd')."'";
	}
    else{
		if( $txt_date_from!="" || $txt_date_to!="" ) $sql_cond .= " and a.OUT_DATE  between '".change_date_format($txt_date_from,'yyyy-mm-dd')."' and '".change_date_format($txt_date_to,'yyyy-mm-dd')."'";
	}
	if(trim($company)!="") $sql_cond .= " and a.company_id='$company'";
	
	
	//if($txt_search_by==1 )
	//{
	if(trim($txt_search_by)==1 || trim($txt_search_by)==2)
	{
 		$sql = "select a.id ,a.challan_no as chalan,a.sys_number_prefix_num,a.in_date,b.sample_id,party_type 	from  inv_gate_in_mst a, inv_gate_in_dtl b 
		where a.id=b.mst_id and a.status_active=1 $sql_cond";
	}
	else{
		$sql = "SELECT a.id ,a.challan_no as chalan,a.sys_number_prefix_num,b.sample_id,a.OUT_DATE from inv_gate_pass_mst a, inv_gate_pass_dtls b 
		where a.id=b.mst_id and a.status_active=1 $sql_cond";
	}
	//echo $sql;		
	//a.supplier_id in (select id from lib_supplier where FIND_IN_SET($company,tag_company) )
	//}
   
   	/*if($txt_search_by==2)
		{
			$sql = "select id ,sys_number as chalan,supplier_name,receive_date,sample 	
					from  inv_gate_in_mst
					where  status_active=1 $sql_cond";//a.supplier_id in (select id from lib_supplier where FIND_IN_SET($company,tag_company) )
		}*/
	//echo $sql;die;//"select id,sample_name from lib_sample where status_active=1 order by sample_name","id,sample_name"
	$result = sql_select($sql);
  	$party_type_arr=array(1=>"Buyer",2=>"Supplier",3=>"Other Party");
	$sample_arr=return_library_array( "select id,sample_name from lib_sample where status_active=1 order by sample_name",'id','sample_name');
	$arr=array(2=>$party_type_arr,3=>$sample_arr);
	if(trim($txt_search_by)==1 || trim($txt_search_by)==2)
	{
		echo  create_list_view("list_view", "Challan No/System ID, Receive Date, Supplier, Sample","150,100,120,150","600","400",0, $sql , "js_set_value", "id,sys_number_prefix_num", "", 1, "0,0,party_type,sample_id", $arr, "sys_number_prefix_num,in_date,party_type,sample_id", "",'','0,3,0,0,0,0') ;	
	}
	else{
		echo  create_list_view("list_view", "Challan No/System ID, Receive Date, Supplier, Sample","150,100,120,150","600","400",0, $sql , "js_set_value", "id,sys_number_prefix_num", "", 1, "0,0,party_type,sample_id", $arr, "sys_number_prefix_num,out_date,party_type,sample_id", "",'','0,3,0,0,0,0') ;
	}
	exit();
}

//order search------------------------------//
if($action=="pi_search")
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
	
	if($company==0) $company_name=""; else $company_name=" and a.company_id=$company";
	if($category==0) $item_cate=""; else $item_cate="and b.item_category_id=$category";
	$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
	
	$sql ="select a.id,a.pi_reference,a.sys_number_prefix_num,a.party_type,a.in_date	
					from  inv_gate_in_mst a,inv_gate_in_dtl b 
					where  a.id=b.mst_id and a.status_active=1 $company_name $item_cate "; 
	$arr=array(1=>$supplier_arr);
	echo create_list_view("list_view", "PI/WO/REQ,Sys No,Receive Date","150,150,100,","450","310",0, $sql , "js_set_value", "id,pi_reference", "", 1, "0,0,0", $arr, "pi_reference,sys_number_prefix_num,in_date", "","setFilterGrid('list_view',-1)","0","",1) ;	
	echo "<input type='hidden' id='txt_selected_id' />";
	echo "<input type='hidden' id='txt_selected' />";
	exit();
}

//report generated here--------------------//
if($action=="generate_report")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$cbo_item_cat=str_replace("'","",$cbo_item_cat);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_location=str_replace("'","",$cbo_location);
	$cbo_group=str_replace("'","",$cbo_group);
 	$cbo_party_type=str_replace("'","",$cbo_party_type);
	$cbo_search_by=str_replace("'","",$cbo_search_by);
	
	$txt_pi_no=str_replace("'","",$txt_pi_no);
	$cbo_gate_type=str_replace("'","",$cbo_gate_type);
	$txt_challan=str_replace("'","",$txt_challan);
	$cbo_sample=str_replace("'","",$cbo_sample);
	$sample_chk=str_replace("'","",$sample_chk_id);
	//$txt_search_item=str_replace("'","",$txt_search_item);
	$hidden_pi_id=str_replace("'","",$hidden_pi_id);
	

	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$sample_arr=return_library_array(" select id,sample_name from lib_sample where status_active=1 order by sample_name",'id','sample_name');
	$supplier_name_library=return_library_array( "select id, supplier_name from  lib_supplier", "id", "supplier_name");
	$other_party_name_arr=return_library_array( "select id, other_party_name from  lib_other_party",'id','other_party_name');
	$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
	$buyer_name_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$location_arr=return_library_array("select id,location_name from lib_location", "id", "location_name");

	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);
	if($sample_chk==1)
	{
		if($cbo_sample==0) $sample_cond=" and b.sample_id>0";
	}
	else
	{
		 if($cbo_sample!=0) $sample_cond=" and b.sample_id=$cbo_sample"; else $sample_cond="";
		//  $sample_cond="";	
	}
    $group_cond = '';
	if($cbo_group != 0){
        $group_cond = " and a.within_group = $cbo_group";
    }
	if($cbo_item_cat!=0) $item_category_cond=" and b.item_category_id=$cbo_item_cat"; else $item_category_cond.="";
	//if($cbo_sample!=0) $sample_cond=" and b.sample_id=$cbo_sample"; else $sample_cond="";
	
	//echo $sample_cond;die;
    if($cbo_company_name!=0) $company_conds.=" and a.company_id=$cbo_company_name"; else $company_conds.="";
	$location_conds='';
	if($cbo_location!="") $location_conds=" and a.com_location_id in($cbo_location)"; else $location_conds="";

    $buyer_condition="";
	if($cbo_party_type==1)
	{
		if(str_replace("'","",$cbo_search_by)!="")  $buyer_condition=" and a.buyer_name='".str_replace("'","",$cbo_search_by)."'"; else $buyer_condition="";
	}
	if($cbo_party_type==2)
	{
		//if(str_replace("'","",$txt_challan)!="")  $sql_condition.=" and a.sys_number='".str_replace("'","",$txt_challan)."'"; else $sql_condition.="";
	}
    //if($hidden_pi_id!="") $sql_condition.=" and a.id in ($hidden_pi_id)"; else $sql_condition.="";
    // if($hidden_pi_id==""  && $txt_pi_no!="") $sql_condition.=" and a.pi_wo_req_number='$txt_pi_no'"; else $sql_condition.="";
	if($cbo_party_type!=0) $search_by_cond=" and a.party_type=$cbo_party_type"; else $search_by_cond="";
	//echo  $cbo_party_type;die;
	if($txt_challan!="") $challan_sys_cond=" and a.sys_number_prefix_num='$txt_challan'"; else $challan_sys_cond="";
	//if($txt_challan!=0) $challan_sys_cond=" and a.challan_no=$txt_challan"; else $challan_sys_cond="";
	if($txt_pi_no!="") $pi_refernce_cond=" and a.pi_reference='$txt_pi_no'"; else $pi_refernce_cond="";
	//echo $challan_sys_cond;die;
	if($db_type==0)
	{
		if($txt_date_from!="" && $txt_date_to!="") $date_cond.=" and a.in_date between '".change_date_format($txt_date_from,"yyyy-mm-dd")."' and '".change_date_format($txt_date_to,"yyyy-mm-dd")."' "; else $date_cond.=""; 
	}
	else
	{
		if($txt_date_from!="" && $txt_date_to!="") $date_cond="and a.in_date between '".date("j-M-Y",strtotime($txt_date_from))."' and '".date("j-M-Y",strtotime($txt_date_to))."' "; else $date_cond="";
	} 
		
	$order_array=array();
	$order_sql="select a.style_ref_no, a.job_no,a.buyer_name, b.id, b.po_number, b.po_quantity from  wo_po_details_master a, wo_po_break_down b where a.id=b.job_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $buyer_condition";
	
	$order_sql_result=sql_select($order_sql);
	$po_ids=array();
	foreach ($order_sql_result as $row)
	{
		$order_array[$row[csf('id')]][$row[csf('po_number')]]['buyer']=$row[csf('buyer_name')];
		$order_array[$row[csf('id')]][$row[csf('po_number')]]['style']=$row[csf('style_ref_no')];
		array_push($po_ids, $row[csf('po_number')]);
		//$po_ids[]=$row[csf('po_number')];
	}
	
	$po_ids=array_unique($po_ids);
	$po_cond='';
	if(!empty($buyer_condition) && count($po_ids))
	{
		$po_cond=where_con_using_array($po_ids,1,"b.buyer_order");
	}
	
	//echo "<pre>";
	//print_r($po_cond); die;
	if($cbo_gate_type==0 || $cbo_gate_type==1)
	{
		$sql="SELECT a.id, a.sys_number, a.sending_company, a.in_date, a.challan_no, a.gate_pass_no, a.carried_by, a.pi_reference, b.sample_id,	b.item_category_id,	a.party_type, b.item_description, b.quantity, b.buyer_order, b.uom, b.uom_qty, b.rate, b.amount, b.remarks, a.time_hour, a.time_minute, c.buyer_order_id
		from inv_gate_in_mst a, inv_gate_in_dtl b, inv_gate_pass_dtls c 
		where a.id=b.mst_id and b.get_pass_dtlsid=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $company_conds $group_cond $location_conds $challan_sys_cond $date_cond $item_category_cond $search_by_cond $pi_refernce_cond $sample_cond $po_cond
		order by b.id";
		$data_result=sql_select($sql);
	}
	
	ob_start();
	?>
	<style type="text/css">
    	.nsbreak{word-break: break-all;}
    </style>
    <div style="height:auto; clear:both;">
	<?		
	
	// Start Get In
	if($cbo_gate_type==0 || $cbo_gate_type==1)
	{
		if(count($data_result)>0)
		{
		    ?>
        	<div style="width:1925px;">
				<table width="1905" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_header_1" align="left">
                    <tr class="form_caption" style="border:none;">
                        <td colspan="20" align="center" style="border:none;font-size:16px; font-weight:bold" >Daily Gate Entry Report </td> 
                    </tr>
                    <tr style="border:none;">
                        <td colspan="20" align="center" style="border:none; font-size:14px;">
                            Company Name : <? echo $company_arr[str_replace("'","",$cbo_company_name)]; ?>                                
                        </td>
                    </tr>
               	</table>
               	<br />
				<table width="1905" border="1" cellpadding="0" cellspacing="0" class="rpt_table nsbreak" rules="all" id="table_header_1" align="left">
					<thead>
						<tr>
							<th width="30">SL</th>
							<th width="100">Receive date</th>
							<th width="120">System ID</th>
							<th width="125">PI/WO/REQ</th>
							<th width="125">Challan No</th>
							<th width="100">Gate Pass No</th>
							<th width="100">Sample Type</th>
							<th width="100">Item Category</th>
                            <th width="140">Description</th>
							<th width="80">Receive Qty</th>
							<th width="60">UOM</th>
                            <th width="50">Rate</th>  
                            <th width="80">Amount</th> 
                         	<? if($cbo_party_type==1) { ?>
                                <th width="120">Buyer</th>
                            <? } else if($cbo_party_type==2) { ?>
                                <th width="120">Supplier</th>
                            <? } else if($cbo_party_type==3) { ?>
                                <th width="120">Other Party</th>
                            <? } else { ?>
                               <th width="120">All</th>
							<? } ?>
                            <th width="110">Buyer Order</th>
                            <th width="90">Buyer</th>
                            <th width="100">Style</th>
							<th width="80">In time</th>
                            <th width="80">Carried By</th>
                            <th>Remarks</th>
						</tr>
					</thead>
			   	</table> 
				<div style="width:1925px; overflow-y: scroll; max-height:400px; " id="scroll_body" align="left">
					<table width="1905" class="rpt_table nsbreak" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body" align="left">
						<tbody>
						<?
						$i=$k=1;
						$total_receive=0;
						$temp_arr=array();
						foreach($data_result as $value)
						{
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							$party_type=$value[csf("party_type")];	
							$buyer_order=$value[csf("buyer_order")];	
							$sending_company=$value[csf("sending_company")];

							if($party_type==1) $search_by_name=$buyer_name_arr[$sending_company];
							else if($party_type==2) $search_by_name=$supplier_arr[$sending_company];
							else if($party_type==3) $search_by_name=$other_party_name_arr[$sending_company];
							?>
							<tr  bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
								<?
								if(!in_array($value[csf("sys_number")],$temp_arr))
								{
									$temp_arr[]=$value[csf("sys_number")];
									?>
									<td width="30" align="center"><p><? echo $k; ?>&nbsp;</p></td>
									<?
									$k++;
								}
								else
								{
									?>
									<td width="30"><p>&nbsp;</p></td>
									<?
								}
								?>
								<td width="100"><p><? echo change_date_format($value[csf("in_date")]); ?>&nbsp;</p></td>
								<td width="120" align="center"><p><?  echo $value[csf("sys_number")]; ?>&nbsp;</p></td>
								<td width="125" align="center"><p><? echo $value[csf("pi_reference")]; ?>&nbsp;</p></td>
								<td width="125" align="center"><p><? echo $value[csf("challan_no")]; ?>&nbsp;</p></td>
								<td width="100" align="center"><p><? echo $value[csf("gate_pass_no")]; ?>&nbsp;</p></td>
								<td width="100" align="center"><p><? echo $sample_arr[$value[csf("sample_id")]]; ?>&nbsp;</p></td>
								<td width="100" align="center"><p><? echo $item_category[$value[csf("item_category_id")]]; ?>&nbsp;</p></td>
								<td width="140" align="center"><p><? echo $value[csf("item_description")]; ?>&nbsp;</p></td>
								<td width="80" align="right"><p><? $total_receive+=$value[csf("quantity")]; echo number_format($value[csf("quantity")],2); ?></p></td>
								<td width="60" align="center"><p><? echo $unit_of_measurement[$value[csf("uom")]]; ?>&nbsp;</p></td>
								<td width="50" align="center"><p><? echo number_format($value[csf("rate")]); ?>&nbsp;</p></td>
								<td width="80" align="center"><p><? echo number_format($value[csf("amount")]); ?>&nbsp;</p></td>
								<td width="120" align="center"><p><? echo $search_by_name; ?>&nbsp;</p></td>
								<td width="110" align="center"><p><? echo $buyer_order; ?>&nbsp;</p></td>
								<td width="90" align="center"><p><? echo $buyer_name_arr[$order_array[$value[csf("buyer_order_id")]][$buyer_order]['buyer']]; ?>&nbsp;</p></td>
								<td width="100"  align="center"><p><? echo $order_array[$value[csf("buyer_order_id")]][$buyer_order]['style']; ?>&nbsp;</p></td>
								<td width="80" align="center"><p>
								<?
									if($value[csf("time_hour")]==24)
									{ 
										$hour=$value[csf("time_hour")]-12;
										$am="AM";
									}
									else  if($value[csf("time_hour")]==12)
									{ 
										$hour=$value[csf("time_hour")];
										$am="PM";
									}
									else  if($value[csf("time_hour")]>12 && $value[csf("time_hour")]<24)
									{ 
										$hour=$value[csf("time_hour")]-12;
										$am="PM";
									} 
									else
									{
										$hour=$value[csf("time_hour")];
										$am="AM";
									}
									echo $hour.":".$value[csf("time_minute")]." ".$am ; ?>&nbsp;</p></td>								
								<td width="80"><p><? echo $value[csf("carried_by")]; ?></p></td>
								<td ><p><? echo $value[csf("remarks")]; ?></p></td>
							</tr>
							<?
							$total_uom_qty+=$value[csf("uom_qty")];
							$total_amount+=$value[csf("amount")];
							$i++;
						}
						?>   
						</tbody>
						<tfoot>
							<th colspan="9" >Total:</th>
							<th id="value_total_receive"><? echo number_format($total_receive,0); ?></th>
							<th id=""></th>
							<th id=""><? //echo number_format($total_uom_qty,0); ?></th>
							<th id=""></th>
							<th id=""><? echo number_format($total_amount,0); ?></th>
							<th id=""></th>
							<th id=""></th>
							<th id=""></th>
							<th id=""></th>
							<th id=""></th>
							<th id=""></th>
							<th></th>
						</tfoot>
					</table>
				</div>
			</div>
			<?
		}
	}
	// End Gate IN 
	
	if($db_type==0)
	{
		if($txt_date_from!="" && $txt_date_to!="") $out_date_cond=" and a.out_date between '".change_date_format($txt_date_from,"yyyy-mm-dd")."' and '".change_date_format($txt_date_to,"yyyy-mm-dd")."' "; else $out_date_cond=""; 
		if($txt_date_from!="" && $txt_date_to!="") $out_date_cond_scan=" and c.out_date between '".change_date_format($txt_date_from,"yyyy-mm-dd")."' and '".change_date_format($txt_date_to,"yyyy-mm-dd")."' "; else $out_date_cond_scan=""; 
	}
	else
	{
		if($txt_date_from!="" && $txt_date_to!="") $out_date_cond="and a.out_date between '".date("j-M-Y",strtotime($txt_date_from))."' and '".date("j-M-Y",strtotime($txt_date_to))."' "; else $out_date_cond="";
		
		if($txt_date_from!="" && $txt_date_to!="") $out_date_cond_scan="and c.out_date between '".date("j-M-Y",strtotime($txt_date_from))."' and '".date("j-M-Y",strtotime($txt_date_to))."' "; else $out_date_cond_scan="";
	}
	
	
	$sql_data=sql_select(" select out_date, gate_pass_id, out_time from inv_gate_out_scan where status_active=1 and is_deleted=0");
	$gate_scan_array=array();
	foreach($sql_data as $row)
	{
		$gate_scan_array[$row[csf('gate_pass_id')]]['out_date']=$row[csf('out_date')];
		$gate_scan_array[$row[csf('gate_pass_id')]]['out_time']=$row[csf('out_time')];
	}
	
	
	$sql_gate=sql_select("select gate_pass_id from inv_gate_out_scan where status_active=1 and is_deleted=0");
	$i=1;
	foreach($sql_gate as $row_g)
	{
		if($i!==1) $row_cond.=",";
		$row_cond.=$row_g[csf('gate_pass_id')];
		$i++;
	}
			
	$sql_gate_in=sql_select("select gate_pass_no from inv_gate_in_mst where status_active=1 and is_deleted=0 and gate_pass_no is not null ");
	$k=1;
	$gatePassNoArr=array();
	$in_row_cond=array();
	foreach($sql_gate_in as $row_in)
	{
		$in_row_cond[$row_in[csf('gate_pass_no')]]="'".$row_in[csf('gate_pass_no')]."'";
		$gatePassNoArr[].=$row_in[csf('gate_pass_no')];
		$k++;
	}
	
	$sysNo_cond_in=where_con_using_array($in_row_cond,0,'a.sys_number not ');
	
	if($cbo_gate_type==0 || $cbo_gate_type==2) //Gate Out
	{
		$sql_out="SELECT a.id, a.company_id, a.com_location_id, a.sys_number, a.delivery_as, a.sys_number_prefix_num, a.sent_by, a.within_group, a.basis, a.issue_purpose, a.sent_to, c.out_date, a.returnable, a.est_return_date, a.challan_no, a.carried_by, a.attention, b.buyer_order, b.buyer_order_id, a.get_pass_no, b.sample_id, b.item_category_id, b.item_description, b.quantity, b.uom, b.uom_qty, b.remarks, a.time_hour, a.time_minute, b.no_of_bags,a.location_name
		from inv_gate_pass_mst a, inv_gate_pass_dtls b, inv_gate_out_scan c	
		where a.id=b.mst_id and c.gate_pass_id=a.sys_number and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $company_conds $group_cond $location_conds $out_date_cond_scan $item_category_cond $sample_cond $challan_sys_cond order by b.id";
		// echo $sql_out;
		$get_out_data=sql_select($sql_out);	
	}
	
	if($cbo_gate_type==0 || $cbo_gate_type==3) // Gate Out Pending
	{		
	 	$sql_pending="SELECT a.id, a.company_id, a.issue_id, a.delivery_as, a.sys_number, a.sys_number_prefix_num, a.sent_by, a.within_group, a.basis, a.issue_purpose, a.sent_to, a.out_date, a.returnable, a.est_return_date, a.challan_no, a.carried_by, a.attention, b.buyer_order, b.buyer_order_id, a.get_pass_no, b.sample_id, b.item_category_id, b.item_description,	b.quantity, b.uom, b.uom_qty, b.remarks, a.time_hour, a.time_minute, b.no_of_bags, a.location_name
		from inv_gate_pass_mst a, inv_gate_pass_dtls b	
		where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.sys_number not in(select a.gate_pass_id from inv_gate_out_scan a where a.status_active=1 and a.is_deleted=0 ) $company_conds $group_cond $location_conds $out_date_cond $item_category_cond $challan_sys_cond $sample_cond
		order by b.id";
		//echo $sql_pending;die;
		$get_pending_data=sql_select($sql_pending);		
	}	
	
	if($cbo_gate_type==0 || $cbo_gate_type==4) // Return Pending
	{
		 $sql_ret_pending="SELECT a.id, a.company_id, a.sys_number, a.delivery_as, a.sys_number_prefix_num, a.sent_by, a.issue_purpose, a.within_group, a.basis, a.sent_to, a.out_date, a.returnable, a.est_return_date, a.challan_no, a.carried_by, a.attention, b.buyer_order, b.buyer_order_id, a.get_pass_no, b.sample_id, b.item_category_id, b.item_description, b.quantity, b.uom, b.uom_qty,b.remarks, a.time_hour, a.time_minute, b.no_of_bags ,a.location_name
		from inv_gate_pass_mst a, inv_gate_pass_dtls b, inv_gate_out_scan c	
		where a.id=b.mst_id and a.sys_number=c.gate_pass_id  and a.returnable=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.sys_number not in (select gate_pass_no from inv_gate_in_mst where status_active=1 and is_deleted=0 and gate_pass_no is not null) $company_conds $group_cond $location_conds $out_date_cond $item_category_cond $challan_sys_cond $sample_cond order by b.id";
		//echo $sql_ret_pending;die;
		$get_return_data=sql_select($sql_ret_pending);	
	}
 
	$print_report_format=return_field_value("format_id","lib_report_template","template_name =".$cbo_company_name." and module_id=6 and report_id=124 and is_deleted=0 and status_active=1");
	$print_button=explode(",",$print_report_format);
// print_r($print_button_first);die;

	$print_button_first=array_shift($print_button);
	// print_r($print_button_first);die;

	// echo $print_button_first.'D';die; 222,259,242,359
	if($print_button_first==115) $getpass_button="get_out_entry_print";
	else if($print_button_first==116) $getpass_button="print_to_html_report";
	else if($print_button_first==136) $getpass_button="get_out_entry_emb_issue_print";
	else if($print_button_first==137) $getpass_button="print_to_html_report5";
	else if($print_button_first==196) $getpass_button="print_to_html_report6";
	else if($print_button_first==199) $getpass_button="print_to_html_report7";
	else if($print_button_first==206) $getpass_button="get_out_entry_print8_fashion";
	else if($print_button_first==207) $getpass_button="print_to_html_report9";
	else if($print_button_first==208) $getpass_button="print_to_html_report10";
	else if($print_button_first==212) $getpass_button="print_to_html_report11";
	else if($print_button_first==271) $getpass_button="print_to_html_report14";
	else if($print_button_first==42) $getpass_button="print_to_html_report_15";
	else if($print_button_first==362) $getpass_button="print_to_html_report_15_v2";
	else if($print_button_first==227) $getpass_button="print_to_html_report16";
	else if($print_button_first==227) $getpass_button="get_out_entry_print12";
	else if($print_button_first==191) $getpass_button="print_to_html_report_13";
	else if($print_button_first==161) $getpass_button="get_out_entry_print6";
	else if($print_button_first==235) $getpass_button="get_out_entry_print9";
	else if($print_button_first==274) $getpass_button="get_out_entry_print10";
	else if($print_button_first==707) $getpass_button="print_to_html_report17";
	else if($print_button_first==738) $getpass_button="get_out_entry_printamt";
	else if($print_button_first==747) $getpass_button="get_out_entry_print14";

	else if($print_button_first==222) $getpass_button="get_out_entry_print";
	else  $getpass_button="";

	$selectSqlForReportBtn = sql_select("SELECT FORMAT_ID FROM LIB_REPORT_TEMPLATE WHERE TEMPLATE_NAME = $cbo_company_name AND MODULE_ID = 6 AND REPORT_ID = 38 AND STATUS_ACTIVE = 1 AND IS_DELETED=0 ");
	foreach($selectSqlForReportBtn as $findIndexVal)
	{
		$index = explode(',',$findIndexVal['FORMAT_ID']);
		$FirstIndexBtn = $index[0];
	}
	if($FirstIndexBtn==274)
	{
		$getpass_button = "get_out_entry_print10";
	}
	
	
	?>
	<br /><br />
	<?
	if ($cbo_gate_type==0 || $cbo_gate_type==2) //Gate Out
	{
	    if (count($get_out_data)>0)
	    {
	        ?>
			<div style="width:2190px;">
				<table width="2170" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="" align="left">
					<tr class="form_caption" style="border:none;">
						<td colspan="20" align="center" style="border:none;font-size:16px; font-weight:bold" >Daily Gate Out Report </td> 
					</tr>
					<tr style="border:none;">
						<td colspan="20" align="center" style="border:none; font-size:14px;">
							Company Name : <? echo $company_arr[str_replace("'","",$cbo_company_name)]; ?>                                
						</td>
					</tr>
			    </table>
			    <br />
				<table width="2170" border="1" cellpadding="0" cellspacing="0" class="rpt_table nsbreak" rules="all" id="table_header_2" align="left">
					<thead>
						<tr>
							<th width="30">SL</th>
							<th width="80">Out date</th>
							<th width="120">Gate Pass No</th>
							<th width="120">Sample Type</th>
							<th width="120">Item Category</th>
							<th width="150">Description</th>
							<th width="80">Quantity</th>
							<th width="60">UOM</th>
							<th width="100">No Of Bag/Roll</th>
                            <th width="100">Delivery As</th>
							<th width="100">Sent By</th>
							<th width="100">Sent to</th>
							<th width="100">To Location</th>
							<th width="100">Attention</th>
							<th width="60">Return able</th>
                            <th width="70">Est. Return Date</th>
							<th width="110">Buyer Order</th>
                            <th width="90">Buyer</th>
                        	<th width="100">Style</th>
							<th width="100">Purpose</th>
							<th width="80">Out time</th>
							<th width="100">Carried By</th>
							<th>Remarks</th>
						</tr>
					</thead>
			    </table> 
				<div style="width:2190px; overflow-y: scroll; max-height:500px;" id="scroll_body" align="left">
					<table width="2170" class="rpt_table nsbreak" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body_1" align="left">
						<tbody>
						<?
						$i=$k=1;$tot_quantity=0;$temp_arr=array();
						//count($get_out_data);
						foreach($get_out_data as $val)
						{
							if ($i%2==0) $bgcolor="#E9F3FF";							
							else $bgcolor="#FFFFFF";							
							$gate_out_date=$gate_scan_array[$val[csf('sys_number')]]['out_date'];
							$gate_out_time=$gate_scan_array[$val[csf('sys_number')]]['out_time'];
							$basis=$val[csf('basis')];
							$within_group=$val[csf('within_group')];

							if($basis==1)
							{ 
								if($within_group==1) $send_to_company=$company_arr[$val[csf('sent_to')]];
								else $send_to_company=$val[csf('sent_to')];
							}
							else if($basis==8 || $basis==9)
							{
								$send_to_company=$val[csf('sent_to')];	
							}
							else if($basis==12)
							{
								$send_to_company=$supplier_name_library[$val[csf('sent_to')]];
							}
							else
							{
								if ($basis==2 || $basis==3 || $basis==4 || $basis==5 || $basis==6 || $basis==7  ||  $basis==10 || $basis==13 ||  $basis==14 ||  $basis==11 ||  $basis==15)
								{
									if($within_group==2) $send_to_company=$supplier_name_library[$val[csf('sent_to')]];
									else $send_to_company=$company_arr[$val[csf('sent_to')]];
								}
								else $send_to_company=$val[csf('sent_to')];
							}
							?>
							<tr  bgcolor="<? echo $bgcolor; ?>" onClick="change_color('trout_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="trout_<? echo $i; ?>">
                            	<?
								if(!in_array($val[csf('sys_number')],$temp_arr))
								{
									$temp_arr[]=$val[csf('sys_number')];
									?>
									<td width="30" align="center"><p><? echo $k; ?>&nbsp;</p></td>
                                    <?
									$k++;
								}
								else
								{
									?>
									<td width="30"><p>&nbsp;</p></td>
                                    <?
								}
								?>
								<td width="80"><p><? echo change_date_format($gate_out_date); ?>&nbsp;</p></td>
								<td width="120"><p><a href='#report_details' onClick="fnc_get_pass_print('<? echo $val[csf('company_id')]; ?>','<? echo $val[csf('sys_number')]; ?>','<? echo $getpass_button; ?>','<? echo $val[csf('basis')]; ?>','<? echo $val[csf('com_location_id')]; ?>','<? echo $val[csf('returnable')]; ?>','<? echo $val[csf('challan_no')]; ?>','<? echo $val[csf('issue_id')]; ?>');"><? echo $val[csf('sys_number')]; ?></a> &nbsp;</p></td>
								
								<td width="120"><p><? echo $sample_arr[$val[csf("sample_id")]]; ?>&nbsp;</p></td>
								<td width="120"><p><? echo $item_category[$val[csf("item_category_id")]]; ?>&nbsp;</p></td>
								<td width="150"><p><? echo  $val[csf("item_description")]; ?>&nbsp;</p></td>
								<td width="80" align="right"><p><? echo number_format($val[csf("quantity")],2); ?></p></td>
								<td width="60" align="center"><p><? echo $unit_of_measurement[$val[csf("uom")]]; ?>&nbsp;</p></td>
								<td width="100" align="center"><p><? echo $val[csf("no_of_bags")]; ?>&nbsp;</p></td>
                                <td width="100" align="center"><p><? echo $basis_arr[$val[csf("delivery_as")]]; ?>&nbsp;</p></td>
								<td width="100" align="center"><p><? echo $val[csf("sent_by")]; ?>&nbsp;</p></td>
								<td width="100" align="center"><p><? echo $send_to_company; ?>&nbsp;</p></td>
								<td width="100" align="center"><p><? echo  $val[csf("location_name")]; ?>&nbsp;</p></td>
								<td width="100" align="center"><p><? echo $val[csf("attention")]; ?>&nbsp;</p></td>
								<td width="60" align="center"><p><? echo  $yes_no[$val[csf("returnable")]]; ?>&nbsp;</p></td>
                                <td width="70"><p><? echo change_date_format($val[csf("est_return_date")]); ?>&nbsp;</p></td>
								<td width="110"  align="center"><p><? echo $val[csf("buyer_order")]; ?>&nbsp;</p></td>
                                <td width="90"  align="center"><p><? echo $buyer_name_arr[$order_array[$val[csf("buyer_order_id")]][$val[csf("buyer_order")]]['buyer']]; ?>&nbsp;</p></td>
                                <td width="100"  align="center"><p><? echo $order_array[$val[csf("buyer_order_id")]][$val[csf("buyer_order")]]['style']; ?>&nbsp;</p></td>
								<td width="100" align="center"><p><? echo $val[csf("issue_purpose")]; ?>&nbsp;</p></td>
								<td width="80" align="center"><p><? echo  $gate_out_time;//$hour.":".$value[csf("time_minute")]." ".$am ; ?>&nbsp;</p></td>
								<td width="100" align="center"><p><? echo $val[csf("carried_by")]; ?>&nbsp;</p></td>
								<td width=""><p><? echo $val[csf("remarks")]; ?>&nbsp;</p></td>
							</tr>
							<?
							$tot_quantity+=$val[csf("quantity")];
							$tot_uom_qty+=$val[csf("uom_qty")];
							$i++;
						}
						?>   
						</tbody>
						<tfoot>
							<th colspan="7">Total</th><th><? echo $tot_quantity;?></th>
							<th></th><th></th><th><? //echo $tot_uom_qty;?></th> <th></th><th></th> <th></th><th></th> <th></th><th></th><th></th>
                            <th></th> <th></th> <th></th> <th></th> <th></th> <th></th> <th></th>
						</tfoot>
				    </table>
                </div>
            </div>  
			<?
		}
	} //Out End
	
	?>
    <br /><br />
	<?
	if ($cbo_gate_type==0 || $cbo_gate_type==3) // Gate Out Pending
	{
		if (count($get_pending_data)>0)
		{
		   ?>
		    <div style="width:2190px;">
				<table width="2170" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="" align="left">
					<tr class="form_caption" style="border:none;">
						<td colspan="20" align="center" style="border:none;font-size:16px; font-weight:bold" >Daily Gate Out Pending </td> 
					</tr>
					<tr style="border:none;">
						<td colspan="20" align="center" style="border:none; font-size:14px;">
							Company Name : <? echo $company_arr[str_replace("'","",$cbo_company_name)]; ?>                                
						</td>
					</tr>
			    </table>
			    <br />
				<table width="2170" border="1" cellpadding="0" cellspacing="0" class="rpt_table nsbreak" rules="all" id="table_header_2" align="left">
					<thead>
						<tr>
							<th width="30">SL</th>
							<th width="80" >Out date</th>
							<th width="120">Gate Pass No</th>
							<th width="120">Sample Type</th>
							<th width="120">Item Category</th>
							<th width="150">Description</th>
							<th width="80">Quantity</th>
							<th width="60">UOM</th>
							<th width="100">No Of Bag/Roll</th>
                            <th width="100">Delivery As</th>
							<th width="100">Sent By</th>
							<th width="100">Sent to</th>
							<th width="100">To Location</th>
							<th width="100">Attention</th>
							<th width="60">Return able</th>
                            <th width="70">Est. Return Date</th>
							<th width="110">Buyer Order</th>
                            <th width="90">Buyer</th>
                        	<th width="100">Style</th>
							<th width="100">Purpose</th>
							<th width="80">Out time</th>
							<th width="100">Carried By</th>
							<th>Remarks</th>
						</tr>
					</thead>
			    </table> 
				<div style="width:2190px; max-height:500px;" id="scroll_body" align="left">
					<table width="2170" class="rpt_table nsbreak" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body_2" align="left">
						<tbody>
						<?
						$i=$k=1;$tot_quantity=0;$temp_arr=array();
						//count($get_out_data);
						foreach($get_pending_data as $val)
						{
							if ($i%2==0) $bgcolor="#E9F3FF";							
							else $bgcolor="#FFFFFF";							
							$gate_out_date=$gate_scan_array[$val[csf('sys_number')]]['out_date'];
							$gate_out_time=$gate_scan_array[$val[csf('sys_number')]]['out_time'];
							$basis=$val[csf('basis')];
							$within_group=$val[csf('within_group')];

							if($basis==1)
							{ 
								if($within_group==1) $send_to_company=$company_arr[$val[csf('sent_to')]];
								else $send_to_company=$val[csf('sent_to')];
							}
							else if($basis==8 || $basis==9)
							{
								$send_to_company=$val[csf('sent_to')];	
							}
							else if($basis==12)
							{
								//$send_to_company=$supplier_name_library[$val[csf('sent_to')]];
								$send_to_company=$val[csf('sent_to')];
							}
							else
							{
								//echo $within_group.'=='.$basis;
								if($basis==2 || $basis==3 || $basis==4 || $basis==5 || $basis==6 || $basis==7  ||  $basis==10 || $basis==13 ||  $basis==14 ||  $basis==11 ||  $basis==15)
								{
									if($within_group==2) $send_to_company=$supplier_name_library[$val[csf('sent_to')]];
									else $send_to_company=$company_arr[$val[csf('sent_to')]];
								}
								else $send_to_company=$val[csf('sent_to')];
							}
							
							?>
							<tr  bgcolor="<? echo $bgcolor; ?>" onClick="change_color('troutp_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="troutp_<? echo $i; ?>">
                            	<?
								if(!in_array($val[csf("sys_number")],$temp_arr))
								{
									$temp_arr[]=$val[csf("sys_number")];
									?>
									<td width="30" align="center"><p><? echo $k; ?>&nbsp;</p></td>
                                    <?
									$k++;
								}
								else
								{
									?>
									<td width="30"><p>&nbsp;</p></td>
                                    <?
								}
								?>
								<td width="80"><p><? echo change_date_format($gate_out_date); ?>&nbsp;</p></td>
								<td width="120"><p><a href='#report_details' onClick="fnc_get_pass_print('<? echo $val[csf('company_id')]; ?>','<? echo $val[csf('sys_number')]; ?>','<? echo $getpass_button; ?>','<? echo $val[csf('basis')]; ?>','<? echo $val[csf('com_location_id')]; ?>','<? echo $val[csf('returnable')]; ?>','<? echo $val[csf('challan_no')]; ?>','<? echo $val[csf('issue_id')]; ?>');"><? echo $val[csf('sys_number')]; ?></a>&nbsp;</p></td>
								<td width="120"><p><? echo $sample_arr[$val[csf("sample_id")]]; ?>&nbsp;</p></td>
								<td width="120"><p><? echo $item_category[$val[csf("item_category_id")]]; ?>&nbsp;</p></td>
								<td width="150"><p><? echo  $val[csf("item_description")]; ?>&nbsp;</p></td>
								<td width="80" align="right"><p><? echo number_format($val[csf("quantity")],2); ?></p></td>
								<td width="60" align="center"><p><? echo $unit_of_measurement[$val[csf("uom")]]; ?>&nbsp;</p></td>
								<td width="100" align="center"><p><? echo $val[csf("no_of_bags")]; ?>&nbsp;</p></td>
                                <td width="100" align="center"><p><? echo $basis_arr[$val[csf("delivery_as")]]; ?>&nbsp;</p></td>
								<td width="100" align="center"><p><? echo $val[csf("sent_by")]; ?>&nbsp;</p></td>
								<td width="100" align="center"><p><? echo $send_to_company; ?>&nbsp;</p></td>
								<td width="100" align="center"><p><? echo $val["LOCATION_NAME"]; ?>&nbsp;</p></td>
								<td width="100" align="center"><p><? echo  $val[csf("attention")]; ?>&nbsp;</p></td>
								<td width="60" align="center"><p><? echo  $yes_no[$val[csf("returnable")]]; ?>&nbsp;</p></td>
                                <td width="70"><p><?  echo  change_date_format($val[csf("est_return_date")]); ?>&nbsp;</p></td>
								<td width="110" align="center"><p><? echo $val[csf("buyer_order")]; ?>&nbsp;</p></td>
                                <td width="90" align="center"><p><? echo $buyer_name_arr[$order_array[$val[csf("buyer_order_id")]][$val[csf("buyer_order")]]['buyer']]; ?>&nbsp;</p></td>
                                <td width="100" align="center"><p><? echo $order_array[$val[csf("buyer_order_id")]][$val[csf("buyer_order")]]['style']; ?>&nbsp;</p></td>
								<td width="100" align="center"><p><? echo $val[csf("issue_purpose")]; ?>&nbsp;</p></td>
								<td width="80" align="center"><p><? echo  $gate_out_time;//$hour.":".$value[csf("time_minute")]." ".$am ; ?>&nbsp;</p></td>
								<td width="100" align="center"><p><? echo $val[csf("carried_by")]; ?>&nbsp;</p></td>
								<td width=""><p><? echo $val[csf("remarks")]; ?>&nbsp;</p></td>
							</tr>
							<?
							$tot_quantity+=$val[csf("quantity")];
							$tot_uom_qty+=$val[csf("uom_qty")];
							$i++;
						}
						?>   
						</tbody>
						<tfoot>
							<th colspan="6">Total</th>
                            <th><? echo $tot_quantity;?></th>
							<th></th> 
							<th></th> 
                            <th><? //echo $tot_uom_qty;?></th> 
                            <th></th> 
                            <th></th> 
                            <th></th> 
                            <th></th> 
                            <th></th>
                            <th></th> 
                            <th></th>
                             <th></th> 
                             <th></th> 
                             <th></th> 
                             <th></th>
                             <th></th>
                            <th></th>
						</tfoot>
				    </table>
                </div>
            </div>    
			<?
		}
	} //Out Pending
	
	?>
    <br /><br />
	<?
	if ($cbo_gate_type==0 || $cbo_gate_type==4) // Return Pending
	{
		if (count($get_return_data)>0)
		{
			?>
		    <div style="width:2190px;">
				<table width="2170" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="" align="left">
					<tr class="form_caption" style="border:none;">
						<td colspan="21" align="center" style="border:none;font-size:16px; font-weight:bold" >Return Pending </td> 
					</tr>
					<tr style="border:none;">
						<td colspan="21" align="center" style="border:none; font-size:14px;">
							Company Name : <? echo $company_arr[str_replace("'","",$cbo_company_name)]; ?>                                
						</td>
					</tr>
			    </table>
			    <br />
				<table width="2170" border="1" cellpadding="0" cellspacing="0" class="rpt_table nsbreak" rules="all" id="table_header_2" align="left">
					<thead>
						<tr>
							<th width="30" >SL</th>
							<th width="80" >Out date</th>
							<th width="120">Gate Pass No</th>
							<th width="120">Sample Type</th>
							<th width="120">Item Category</th>
							<th width="150">Description</th>
							<th width="80">Quantity</th>
							<th width="60">UOM</th>
							<th width="100">No Of Bag/Roll</th>
                            <th width="100">Delivery As</th>
							<th width="100">Sent By</th>
							<th width="100">Sent to</th>
							<th width="100">To Location</th>
							<th width="100">Attention</th>
							<th width="60">Return able</th>
                            <th width="70">Est. Return Date</th>
							<th width="110">Buyer Order</th>
                            <th width="90">Buyer</th>
                        	<th width="100">Style</th>
							<th width="100">Purpose</th>
							<th width="80">Out time</th>
							<th width="100">Carried By</th>
							<th>Remarks</th>
						</tr>
					</thead>
				</table> 
				<div style="width:2190px; max-height:500px;" id="scroll_body" align="left">
					<table width="2170" class="rpt_table nsbreak" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body_3" align="left">
						<tbody>
						<?
						$i=$k=1;$total_out=0;
						$temp_arr=array();
						$tot_quantity=0;
						//count($get_out_data);
						foreach($get_return_data as $val)
						{
							if(!in_array($val[csf('sys_number')], $gatePassNoArr))
							{
								if ($i%2==0) $bgcolor="#E9F3FF";								
								else $bgcolor="#FFFFFF";								
								$gate_out_date=$gate_scan_array[$val[csf('sys_number')]]['out_date'];
								$gate_out_time=$gate_scan_array[$val[csf('sys_number')]]['out_time'];
								$basis=$val[csf('basis')];
								$within_group=$val[csf('within_group')];

								if($basis==1)
								{ 
									if($within_group==1) $send_to_company=$company_arr[$val[csf('sent_to')]];
									else $send_to_company=$val[csf('sent_to')];
								}
								else if($basis==8 || $basis==9)
								{
									$send_to_company=$val[csf('sent_to')];	
								}
								else if($basis==12)
								{
									$send_to_company=$supplier_name_library[$val[csf('sent_to')]];
								}
								else
								{
									//echo $within_group.'=='.$basis;
									if($basis==2 || $basis==3 || $basis==4 || $basis==5 || $basis==6 || $basis==7  ||  $basis==10 || $basis==13 ||  $basis==14 ||  $basis==11 ||  $basis==15)
									{
										if($within_group==2) $send_to_company=$supplier_name_library[$val[csf('sent_to')]];
										else $send_to_company=$company_arr[$val[csf('sent_to')]];
									}
									else $send_to_company=$val[csf('sent_to')];
								}
								?>
								<tr  bgcolor="<? echo $bgcolor; ?>" onClick="change_color('troutrp_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="troutrp_<? echo $i; ?>">
									<?
									if(!in_array($val[csf("sys_number")],$temp_arr))
									{
										$temp_arr[]=$val[csf("sys_number")];
										?>
										<td width="30" align="center"><p><? echo $k; ?>&nbsp;</p></td>
	                                    <?
										$k++;
									}
									else
									{
										?>
										<td width="30"><p>&nbsp;</p></td>
	                                    <?
									}
									?>
									<td width="80"><p><? echo change_date_format($gate_out_date); ?>&nbsp;</p></td>
									<td width="120"><p><a href='#report_details' onClick="fnc_get_pass_print('<? echo $val[csf('company_id')]; ?>','<? echo $val[csf('sys_number')]; ?>','<? echo $getpass_button; ?>','<? echo $val[csf('basis')]; ?>','<? echo $val[csf('com_location_id')]; ?>','<? echo $val[csf('returnable')]; ?>','<? echo $val[csf('challan_no')]; ?>','<? echo $val[csf('issue_id')]; ?>');"><? echo $val[csf('sys_number')]; ?></a>&nbsp;</p></td>
									<td width="120"><p><? echo $sample_arr[$val[csf("sample_id")]]; ?>&nbsp;</p></td>
									<td width="120"><p><? echo $item_category[$val[csf("item_category_id")]]; ?>&nbsp;</p></td>
									<td width="150"><p><? echo  $val[csf("item_description")]; ?>&nbsp;</p></td>
									<td width="80" align="right"><p><? echo number_format($val[csf("quantity")],2); ?></p></td>
									<td width="60" align="center"><p><? echo $unit_of_measurement[$val[csf("uom")]]; ?>&nbsp;</p></td>
									<td width="100" align="center"><p><? echo $val[csf("no_of_bags")]; ?>&nbsp;</p></td>
                                    <td width="100" align="center"><p><? echo $basis_arr[$val[csf("delivery_as")]]; ?>&nbsp;</p></td>
									<td width="100" align="center"><p><? echo $val[csf("sent_by")]; ?>&nbsp;</p></td>
									<td width="100" align="center"><p><? echo $send_to_company; ?>&nbsp;</p></td>
									<td width="100" align="center"><p><? echo $val[csf("location_name")]; ?>&nbsp;</p></td>
									<td width="100" align="center"><p><? echo  $val[csf("attention")]; ?>&nbsp;</p></td>
									<td width="60" align="center"><p><? echo  $yes_no[$val[csf("returnable")]]; ?>&nbsp;</p></td>
	                                <td width="70"><p><?  echo  change_date_format($val[csf("est_return_date")]); ?>&nbsp;</p></td>
									<td width="110"  align="center"><p><? echo $val[csf("buyer_order")]; ?>&nbsp;</p></td>
	                                <td width="90"  align="center"><p><? echo $buyer_name_arr[$order_array[$val[csf("buyer_order_id")]][$val[csf("buyer_order")]]['buyer']]; ?>&nbsp;</p></td>
	                                <td width="100"  align="center"><p><? echo $order_array[$val[csf("buyer_order_id")]][$val[csf("buyer_order")]]['style']; ?>&nbsp;</p></td>
									<td width="100" align="center"><p><? echo $val[csf("issue_purpose")]; ?>&nbsp;</p></td>
									<td width="80" align="center"><p><? echo  $gate_out_time;//$hour.":".$value[csf("time_minute")]." ".$am ; ?>&nbsp;</p></td>
									<td width="100" align="center"><p><? echo $val[csf("carried_by")]; ?>&nbsp;</p></td>
									<td width=""><p><? echo $val[csf("remarks")]; ?>&nbsp;</p></td>
								</tr>
								<?
								$tot_quantity+=$val[csf("quantity")];
								$tot_uom_qty+=$val[csf("uom_qty")];
								$i++;
							}
						}
						?>   
						</tbody>
						<tfoot>
							<th colspan="7">Total</th><th><? echo $tot_quantity;?></th>
							<th></th><th></th> <th><? //echo $tot_uom_qty;?></th> <th></th<th></th>> <th></th> <th></th> <th></th> <th></th>  <th></th> <th></th> <th></th> 
                            <th></th> <th></th> <th></th><th></th><th></th>
						</tfoot>						
					</table>
	            </div>
        	</div>
			<?
		}				
	} // Return Pending	
	?>
    </div>
    <!-- </fieldset> -->
	<?    
	$html = ob_get_contents();
	ob_clean();
	foreach (glob("*.xls") as $filename) {
		//if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');	
	$is_created = fwrite($create_new_doc, $html);
	echo "$html####$filename"; 
	exit();
}

if($action=="generate_report2")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$cbo_item_cat=str_replace("'","",$cbo_item_cat);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	
	$cbo_party_type=str_replace("'","",$cbo_party_type);
	$cbo_search_by=str_replace("'","",$cbo_search_by);
	
	$txt_pi_no=str_replace("'","",$txt_pi_no);
	$cbo_gate_type=str_replace("'","",$cbo_gate_type);
	$txt_challan=str_replace("'","",$txt_challan);
	$cbo_sample=str_replace("'","",$cbo_sample);
	$sample_chk=str_replace("'","",$sample_chk_id);

	$hidden_pi_id=str_replace("'","",$hidden_pi_id);

	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$sample_arr=return_library_array(" select id,sample_name from lib_sample where status_active=1 order by sample_name",'id','sample_name');
	$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
	$supplier_name_library=return_library_array( "select id, supplier_name from  lib_supplier", "id", "supplier_name");
	$other_party_name_arr=return_library_array( "select id, other_party_name from  lib_other_party",'id','other_party_name');
	$buyer_name_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');

	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);

	$sample_cond='';
	if($sample_chk==1)
	{
		if($cbo_sample==0) $sample_cond=" and b.sample_id>0";
	}
	else
	{
		if($cbo_sample!=0) $sample_cond=" and b.sample_id=$cbo_sample";
	}
    $group_cond = '';
    if($cbo_group != 0){
        $group_cond = " and a.within_group = $cbo_group";
    }
	$item_category_cond=$company_conds='';
	$search_by_cond=$challan_sys_cond='';
	$pi_refernce_cond='';
	if ($cbo_item_cat !=0) $item_category_cond=" and b.item_category_id=$cbo_item_cat";	
    if ($cbo_company_name !=0) $company_conds.=" and a.company_id=$cbo_company_name";	
	if ($cbo_party_type !=0) $search_by_cond=" and a.party_type=$cbo_party_type";
	if($txt_challan !='') $challan_sys_cond=" and a.sys_number_prefix_num='$txt_challan'";
	if($txt_pi_no !='') $pi_refernce_cond=" and a.pi_reference='$txt_pi_no'";

	$date_cond='';
	if($db_type==0)
	{
		if ($txt_date_from !='' && $txt_date_to !='') $date_cond.=" and a.in_date between '".change_date_format($txt_date_from,"yyyy-mm-dd")."' and '".change_date_format($txt_date_to,"yyyy-mm-dd")."' "; 
	}
	else
	{
		if ($txt_date_from !='' && $txt_date_to !='') $date_cond="and a.in_date between '".date("j-M-Y",strtotime($txt_date_from))."' and '".date("j-M-Y",strtotime($txt_date_to))."' ";
	} 
		
	$order_array=array();
	$order_sql="SELECT a.style_ref_no, a.job_no,a.buyer_name, b.id, b.po_number, b.po_quantity from  wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	$order_sql_result=sql_select($order_sql);
	foreach ($order_sql_result as $row)
	{
		$order_array[$row[csf('po_number')]]['buyer']=$row[csf('buyer_name')];
		$order_array[$row[csf('po_number')]]['style']=$row[csf('style_ref_no')];
	}

	if($cbo_gate_type==0 || $cbo_gate_type==1)
	{
		$sql="SELECT a.id, a.sys_number, a.sending_company, a.in_date, a.challan_no, a.gate_pass_no, a.carried_by, a.pi_reference, b.sample_id,	b.item_category_id,	a.party_type, b.item_description, b.quantity, b.buyer_order, b.uom,	b.uom_qty,	b.rate, b.amount, b.remarks, a.time_hour, a.time_minute
		from inv_gate_in_mst a,inv_gate_in_dtl b 
		where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $company_conds $group_cond $challan_sys_cond $date_cond $item_category_cond $search_by_cond $pi_refernce_cond $sample_cond
		order by b.id";
		$data_result=sql_select($sql);
	}
	$table_width=1500;
	//echo $cbo_gate_type.'system';die;
	ob_start();
	?>
	<style type="text/css">
        .nsbreak{word-break: break-all;}
    </style>

    <div style="height:auto; clear:both;">		
		<?		
		if($cbo_gate_type==0 || $cbo_gate_type==1) // Gate IN 
		{
			if (count($data_result)>0)
			{
			    ?>
		    	<table width="<? echo $table_width; ?>" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_header_1" align="left"> 
                    <tr class="form_caption" style="border:none;">
						<td colspan="15" align="center" style="border:none;font-size:16px; font-weight:bold" >Daily Gate Entry Report</td> 
					</tr>
					<tr style="border:none;">
						<td colspan="15" align="center" style="border:none; font-size:14px;">
							<? echo $company_arr[$cbo_company_name]; ?>
						</td>
					</tr>
					<tr style="border:none;">
						<td colspan="15" align="center" style="border:none; font-size:14px;">
							Date Range : <? echo change_date_format($txt_date_from).' to '.change_date_format($txt_date_to); ?>  
						</td>
					</tr>
               	</table>
				<table width="<? echo $table_width; ?>" border="1" cellpadding="0" cellspacing="0" class="rpt_table nsbreak" rules="all" id="table_header_1" align="left"> 
					<thead>
						<tr>
							<th width="30">SL</th>
							<th width="120">System ID</th>
							<th width="120">Gate Pass No</th>
							<th width="80">Receive date</th>
							<th width="120">Sample Type</th>
							<th width="120">Item Category</th>
                            <th width="150">Item Description</th>
							<th width="80">Receive Qty</th>
							<th width="60">UOM</th>
                            <th width="60">Rate</th>  
                            <th width="80">Amount</th>
							<? if ($cbo_party_type==1) { ?>
                                <th width="120">Buyer</th>
                            <? } else if($cbo_party_type==2) { ?>
                                <th width="120">Supplier</th>
                            <? } else if($cbo_party_type==3) { ?>
                                <th width="120">Other Party</th>
                            <? } else { ?>
                                <th width="120">All</th>
							<? } ?>
							<th width="100">In time</th>
							<th width="100">Carried By</th>
							<th>Remarks</th>
						</tr>
					</thead>
			   	</table> 
				<div style="width:<? echo $table_width+20; ?>px; overflow-y: scroll; max-height:400px;" id="scroll_body">
					<table width="<? echo $table_width; ?>" class="rpt_table nsbreak" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body" align="left">
						<tbody>
						<?
						$i=$k=1;$total_receive=0;$temp_arr=array();
						foreach($data_result as $value)
						{
							if ($i%2==0) $bgcolor="#E9F3FF"; 
							else $bgcolor="#FFFFFF";
							$party_type=$value[csf("party_type")];	
							$buyer_order=$value[csf("buyer_order")];	
							$sending_company=$value[csf("sending_company")];
							if ($party_type==1) $search_by_name=$buyer_name_arr[$sending_company];
							else if ($party_type==2) $search_by_name=$supplier_arr[$sending_company];
							else if ($party_type==3) $search_by_name=$other_party_name_arr[$sending_company];
							?>
							<tr  bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                            	<?
								if(!in_array($value[csf("sys_number")],$temp_arr))
								{
									$temp_arr[]=$value[csf("sys_number")];
									?>
									<td width="30" align="center"><p><? echo $k; ?>&nbsp;</p></td>
                                    <?
									$k++;
								}
								else
								{
									?>
									<td width="30"><p>&nbsp;</p></td>
                                    <?
								}
								?>								
								<td width="120"  align="center"><p><?  echo $value[csf("sys_number")]; ?>&nbsp;</p></td>
                                <td width="120" align="center"><p><? echo $value[csf("gate_pass_no")]; ?>&nbsp;</p></td>
                                <td width="80"><p><? echo change_date_format($value[csf("in_date")]); ?>&nbsp;</p></td>
                                <td width="120" align="center"><p><? echo $sample_arr[$value[csf("sample_id")]]; ?>&nbsp;</p></td>
								<td width="120" align="center"><p><? echo $item_category[$value[csf("item_category_id")]]; ?>&nbsp;</p></td>
								<td width="150" align="center"><p><? echo $value[csf("item_description")]; ?>&nbsp;</p></td>
								<td width="80"  align="right"><p><? $total_receive+=$value[csf("quantity")]; echo number_format($value[csf("quantity")]); ?></p></td>
								<td width="60" align="center"><p><? echo $unit_of_measurement[$value[csf("uom")]]; ?>&nbsp;</p></td>
                                <td width="60" align="center"><p><? echo number_format($value[csf("rate")]); ?>&nbsp;</p></td>
                                <td width="80" align="center"><p><? echo number_format($value[csf("amount")]); ?></p></td>
                                <td width="120"  align="center"><p><? echo $search_by_name; ?>&nbsp;</p></td>
								<td width="100" align="center"><p>
								<?
									if($value[csf("time_hour")]==24)
									{ 
										$hour=$value[csf("time_hour")]-12;
										$am="AM";
									}
									else  if($value[csf("time_hour")]==12)
									{ 
										$hour=$value[csf("time_hour")];
										$am="PM";
									}
									else  if($value[csf("time_hour")]>12 && $value[csf("time_hour")]<24)
									{ 
										$hour=$value[csf("time_hour")]-12;
										$am="PM";
									} 
									else
									{
										$hour=$value[csf("time_hour")];
										$am="AM";
									}
									// echo $value[csf("time_hour")];
									echo $hour.":".$value[csf("time_minute")]." ".$am ; ?>&nbsp;</p></td>
                                 
                                <td width="100"><p><? echo $value[csf("carried_by")]; ?></p></td>
								<td ><p><? echo $value[csf("remarks")]; ?></p></td>
							</tr>
							<?
							$total_uom_qty+=$value[csf("uom_qty")];
							$total_amount+=$value[csf("amount")];
							$i++;
						}
						?>   
						</tbody>
	                    <tfoot>
	                    	<th colspan="7" >Total:</th>
	                        <th id="value_total_receive"><? echo number_format($total_receive,0); ?></th>
	                        <th></th>
							<th></th> 
                            <th></th> 
                            <th></th> 
                            <th></th> 
                            <th></th> 
                            <th></th>
	                    </tfoot>
					</table>
				</div>
				<?
			}
		}// Gate IN End
		
		$out_date_cond=$out_date_cond_scan='';
		if($db_type==0)
		{
			if($txt_date_from !='' && $txt_date_to !='') $out_date_cond=" and a.out_date between '".change_date_format($txt_date_from,"yyyy-mm-dd")."' and '".change_date_format($txt_date_to,"yyyy-mm-dd")."' "; 
			if($txt_date_from !='' && $txt_date_to !='') $out_date_cond_scan=" and c.out_date between '".change_date_format($txt_date_from,"yyyy-mm-dd")."' and '".change_date_format($txt_date_to,"yyyy-mm-dd")."' "; 
		}
		else
		{
			if($txt_date_from !='' && $txt_date_to !='') $out_date_cond="and a.out_date between '".date("j-M-Y",strtotime($txt_date_from))."' and '".date("j-M-Y",strtotime($txt_date_to))."' ";			
			if($txt_date_from !='' && $txt_date_to !='') $out_date_cond_scan="and c.out_date between '".date("j-M-Y",strtotime($txt_date_from))."' and '".date("j-M-Y",strtotime($txt_date_to))."' ";
		}
		// echo $out_date_cond_scan;

		$sql_data=sql_select("select out_date, gate_pass_id, out_time from inv_gate_out_scan where status_active=1 and is_deleted=0");
		$gate_scan_array=array();
		foreach($sql_data as $row)
		{
			$gate_scan_array[$row[csf('gate_pass_id')]]['out_date']=$row[csf('out_date')];
			$gate_scan_array[$row[csf('gate_pass_id')]]['out_time']=$row[csf('out_time')];
		}

		$sql_gate=sql_select("select gate_pass_id from inv_gate_out_scan where status_active=1 and is_deleted=0");
		$i=1;
		foreach($sql_gate as $row_g)
		{
			if($i!==1) $row_cond.=",";
			$row_cond.=$row_g[csf('gate_pass_id')];
			$i++;
		}
				
		$sql_gate_in=sql_select("select gate_pass_no from inv_gate_in_mst where status_active=1 and is_deleted=0 and gate_pass_no is not null ");
		$k=1;
		$gatePassNoArr=array();
		foreach($sql_gate_in as $row_in)
		{
			// if($k!==1) $in_row_cond.=",";
			// $in_row_cond.="'".$row_in[csf('gate_pass_no')]."'";
			$in_row_cond[$row_in[csf('gate_pass_no')]]="'".$row_in[csf('gate_pass_no')]."'";
			$gatePassNoArr[].=$row_in[csf('gate_pass_no')];
			$k++;
		}

		/*$sysNo=chop($in_row_cond,','); $sysNo_cond_in="";
		$sysNos=count(array_unique(explode(",",$in_row_cond)));
		if($db_type==2 && $sysNos>1000)
		{
			$sysNo_cond_in=" and (";
			$sysNoArr=array_chunk(array_unique(explode(",",$sysNo)),999);
			foreach($sysNoArr as $ids)
			{
				$ids=implode(",",$ids);
				$sysNo_cond_in.="  a.sys_number not in ($ids) or"; 
			}
			$sysNo_cond_in=chop($sysNo_cond_in,'or ');
			$sysNo_cond_in.=")";
		}
		else
		{
			$sysNo_cond_in=" and a.sys_number not in ($sysNo)";
		}*/
		$sysNo_cond_in=where_con_using_array($in_row_cond,0,'a.sys_number not ');
		// echo $sysNo_cond_in;die;

		if($cbo_gate_type==0 || $cbo_gate_type==2) //Gate Out
		{
			$sql_out="SELECT a.id, a.company_id, a.com_location_id, a.issue_id, a.sys_number, a.sys_number_prefix_num, a.sent_by, a.within_group, a.basis,	a.issue_purpose, a.sent_to, c.out_date, a.returnable, a.est_return_date, a.challan_no, a.carried_by, buyer_order, a.get_pass_no, b.sample_id, b.item_category_id, b.item_description, b.quantity, b.uom, b.uom_qty, b.remarks, a.time_hour, a.time_minute, b.no_of_bags
			from inv_gate_pass_mst a, inv_gate_pass_dtls b,inv_gate_out_scan c	
			where a.id=b.mst_id and c.gate_pass_id=a.sys_number and a.status_active=1  and a.is_deleted=0 and b.status_active=1  and b.is_deleted=0 $company_conds $group_cond $out_date_cond_scan $item_category_cond $sample_cond $challan_sys_cond 
			order by b.id";
			//echo $sql_out;
			$get_out_data=sql_select($sql_out);		
		}

		if($cbo_gate_type==0 || $cbo_gate_type==3) // Gate Out Pending
		{			
		    $sql_pending="SELECT a.id, a.company_id, a.com_location_id, a.issue_id, a.sys_number, a.sys_number_prefix_num, a.sent_by, a.within_group, a.basis, a.issue_purpose, a.sent_to, a.out_date, a.returnable, a.est_return_date, a.challan_no, a.carried_by, buyer_order, a.get_pass_no, b.sample_id, b.item_category_id, b.item_description, b.quantity, b.uom, b.uom_qty, b.remarks, a.time_hour, a.time_minute, b.no_of_bags 
			from inv_gate_pass_mst a, inv_gate_pass_dtls b	
			where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.sys_number not in(select a.gate_pass_id from inv_gate_out_scan a where a.status_active=1 and a.is_deleted=0 ) $company_conds $group_cond $out_date_cond $item_category_cond $challan_sys_cond $sample_cond
			order by b.id";

			//echo $sql_pending;
		    $get_pending_data=sql_select($sql_pending);		
		}
		if($cbo_gate_type==0 || $cbo_gate_type==4) // Return Pending
		{
			$sql_ret_pending="SELECT a.id, a.company_id, a.com_location_id, a.issue_id, a.sys_number, a.sys_number_prefix_num, a.sent_by, a.issue_purpose, a.within_group, a.basis, a.sent_to, a.out_date, a.returnable, a.est_return_date, a.challan_no, a.carried_by, buyer_order, a.get_pass_no, b.sample_id, b.item_category_id, b.item_description, b.quantity, b.uom, b.uom_qty, b.remarks, a.time_hour, a.time_minute, b.no_of_bags 
			from inv_gate_pass_mst a, inv_gate_pass_dtls b,inv_gate_out_scan c	
			where a.id=b.mst_id and a.sys_number=c.gate_pass_id and a.returnable=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $sysNo_cond_in  $company_conds $group_cond $out_date_cond $item_category_cond $challan_sys_cond $sample_cond 
			order by b.id";
			$get_return_data=sql_select($sql_ret_pending);		
		}


		//$print_report_format=return_library_array( "select template_name, format_id from lib_report_template where module_id=6 and report_id=124 and is_deleted=0 and status_active=1",'template_name','format_id');

	$print_report_format=return_field_value("format_id","lib_report_template","template_name =".$cbo_company_name." and module_id=6 and report_id=38 and is_deleted=0 and status_active=1");

	

	$print_button=explode(",",$print_report_format);
	$print_button_first=array_shift($print_button);

	//echo $print_button_first.'D';
	if($print_button_first==115) $getpass_button="get_out_entry_print";
	else if($print_button_first==116) $getpass_button="print_to_html_report";
	else if($print_button_first==136) $getpass_button="get_out_entry_emb_issue_print";
	else if($print_button_first==137) $getpass_button="print_to_html_report5";
	else if($print_button_first==196) $getpass_button="print_to_html_report6";
	else if($print_button_first==199) $getpass_button="print_to_html_report7";
	else if($print_button_first==206) $getpass_button="get_out_entry_print8_fashion";
	else if($print_button_first==207) $getpass_button="print_to_html_report9";
	else if($print_button_first==208) $getpass_button="print_to_html_report10";
	else if($print_button_first==212) $getpass_button="print_to_html_report11";
	else if($print_button_first==271) $getpass_button="print_to_html_report14";
	else if($print_button_first==42) $getpass_button="print_to_html_report_15";
	else if($print_button_first==362) $getpass_button="print_to_html_report_15_v2";
	else if($print_button_first==227) $getpass_button="print_to_html_report16";
	else if($print_button_first==227) $getpass_button="get_out_entry_print12";
	else if($print_button_first==191) $getpass_button="print_to_html_report_13";
	else if($print_button_first==161) $getpass_button="get_out_entry_print6";
	else if($print_button_first==235) $getpass_button="get_out_entry_print9";
	else if($print_button_first==274) $getpass_button="get_out_entry_print10";
	else if($print_button_first==707) $getpass_button="print_to_html_report17";
	else if($print_button_first==738) $getpass_button="get_out_entry_printamt";
	else if($print_button_first==747) $getpass_button="get_out_entry_print14";
	else  $getpass_button="";

	//echo $getpass_button; die;



		?>
		<br/><br/>
		<?
		if($cbo_gate_type==0 || $cbo_gate_type==2) //Gate Out
		{
			if(count($get_out_data)>0)
			{
			   ?>
				<div style="width:<? echo $table_width; ?>px;">
				    <table width="<? echo $table_width; ?>" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="" align="left"> 
						<tr class="form_caption" style="border:none;">
							<td colspan="15" align="center" style="border:none;font-size:16px; font-weight:bold" >Daily Gate Out Report</td> 
						</tr>
						<tr style="border:none;">
							<td colspan="15" align="center" style="border:none; font-size:14px;">
								<? echo $company_arr[$cbo_company_name]; ?>
							</td>
						</tr>
						<tr style="border:none;">
							<td colspan="15" align="center" style="border:none; font-size:14px;">
								Date Range : <? echo change_date_format($txt_date_from).' to '.change_date_format($txt_date_to); ?>  
							</td>
						</tr>
					</table>
					<br />
					<table width="<? echo $table_width; ?>" border="1" cellpadding="0" cellspacing="0" class="rpt_table nsbreak" rules="all" id="table_header_2" align="left"> 
						<thead>
							<tr>
								<th width="30" >SL</th>
								<th width="120">Basis</th>
								<th width="120">Gate Pass No</th>
								<th width="80">Gate Pass Date</th>
								<th width="120">Sample Type</th>
								<th width="120">Item Category</th>
								<th width="150">Item Description</th>
								<th width="80">Quantity</th>
								<th width="60">UOM</th>
								<th width="100">Sent By</th>
								<th width="100">Sent to</th>
								<th width="60">Return able</th>
								<th width="100" >Purpose</th>
								<th width="100" >Carried By</th>
								<th>Remarks</th>
							</tr>
						</thead>
				   </table> 
				    <div style="width:<? echo $table_width+20; ?>px; overflow-y: scroll; max-height:400px;" id="scroll_body" align="left">
						<table width="<? echo $table_width; ?>" class="rpt_table nsbreak" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body_1" align="left">
							<tbody>
							<?
							$i=$k=1;$tot_quantity=0;
							$temp_arr=array();
							foreach($get_out_data as $val)
							{
								if ($i%2==0) $bgcolor="#E9F3FF";
								else $bgcolor="#FFFFFF";
								$gate_out_date=$gate_scan_array[$val[csf('sys_number')]]['out_date'];
								$gate_out_time=$gate_scan_array[$val[csf('sys_number')]]['out_time'];
								$basis=$val[csf('basis')];
								$within_group=$val[csf('within_group')];

								if($basis==1)
								{ 
									if($within_group==1) $send_to_company=$company_arr[$val[csf('sent_to')]];
									else $send_to_company=$val[csf('sent_to')];
								}
								else if($basis==8 || $basis==9)
								{
									$send_to_company=$val[csf('sent_to')];	
								}
								else if($basis==12)
								{
									$send_to_company=$supplier_name_library[$val[csf('sent_to')]];
								}
								else
								{
									//echo $within_group.'=='.$basis;
									if($basis==2 || $basis==3 || $basis==4 || $basis==5 || $basis==6 || $basis==7  ||  $basis==10 || $basis==13 ||  $basis==14 ||  $basis==11 ||  $basis==15)
									{
										if($within_group==2) $send_to_company=$supplier_name_library[$val[csf('sent_to')]];
										else $send_to_company=$company_arr[$val[csf('sent_to')]];
									}
									else $send_to_company=$val[csf('sent_to')];	
								}
								?>
								<tr  bgcolor="<? echo $bgcolor; ?>" onClick="change_color('trout_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="trout_<? echo $i; ?>">
	                            	<?
									if(!in_array($val[csf('sys_number')],$temp_arr))
									{
										$temp_arr[]=$val[csf('sys_number')];
										?>
										<td width="30" align="center"><p><? echo $k; ?>&nbsp;</p></td>
	                                    <?
										$k++;
									}
									else
									{
										?>
										<td width="30"><p>&nbsp;</p></td>
	                                    <?
									}
									?>
									<td width="120"><p><? echo $get_pass_basis[$val[csf("basis")]]; ?>&nbsp;</p></td>				
									<td width="120"><p><a href='#report_details' onClick="fnc_get_pass_print('<? echo $val[csf('company_id')]; ?>','<? echo $val[csf('sys_number')]; ?>','<? echo $getpass_button; ?>','<? echo $val[csf('basis')]; ?>','<? echo $val[csf('com_location_id')]; ?>','<? echo $val[csf('returnable')]; ?>','<? echo $val[csf('challan_no')]; ?>','<? echo $val[csf('issue_id')]; ?>');"><? echo $val[csf('sys_number')]; ?></a> &nbsp;</p></td>
									<td width="80"><p><? echo change_date_format($gate_out_date); ?>&nbsp;</p></td>
									<td width="120"><p><? echo $sample_arr[$val[csf("sample_id")]]; ?>&nbsp;</p></td>
									<td width="120"><p><? echo $item_category[$val[csf("item_category_id")]]; ?>&nbsp;</p></td>
									<td width="150"><p><? echo  $val[csf("item_description")]; ?>&nbsp;</p></td>
									<td width="80" align="right"><p><? echo number_format($val[csf("quantity")],2); ?></p></td>
									<td width="60" align="center"><p><? echo $unit_of_measurement[$val[csf("uom")]]; ?>&nbsp;</p></td>
									<td width="100" align="center"><p><? echo $val[csf("sent_by")]; ?>&nbsp;</p></td>
									<td width="100" align="center"><p><? echo $send_to_company; ?>&nbsp;</p></td>
									<td width="60" align="center"><p><? echo  $yes_no[$val[csf("returnable")]]; ?>&nbsp;</p></td>	
									<td width="100" align="center"><p><? echo $val[csf("issue_purpose")]; ?>&nbsp;</p></td>		
									<td width="100" align="center"><p><? echo $val[csf("carried_by")]; ?>&nbsp;</p></td>
									<td width=""><p><? echo $val[csf("remarks")]; ?>&nbsp;</p></td>
								</tr>
								<?
								$tot_quantity+=$val[csf("quantity")];
								$tot_uom_qty+=$val[csf("uom_qty")];
								$i++;
							}
							?>   
							</tbody>
							<tfoot>
								<th colspan="7">Total</th>
								<th><? echo $tot_quantity;?></th>
								<th></th> 
								<th></th> 
	                            <th></th> 
	                            <th></th> 
	                            <th></th> 
	                            <th></th> 
	                            <th></th>
							</tfoot>
						</table>
                	</div>
                </div> 
				<?
			}
		} //Out End
		?>
        <br/><br/>
		<?
		if($cbo_gate_type==0 || $cbo_gate_type==3) // Gate Out Pending
	    {
			if(count($get_pending_data)>0)
		    {
			    ?>
				<div style="width:<? echo $table_width; ?>px;">
					<table width="<? echo $table_width; ?>" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="" align="left"> 
						<tr class="form_caption" style="border:none;">
							<td colspan="15" align="center" style="border:none;font-size:16px; font-weight:bold" >Gate Pass Report</td> 
						</tr>
						<tr style="border:none;">
							<td colspan="15" align="center" style="border:none; font-size:14px;">
								<? echo $company_arr[$cbo_company_name]; ?>
							</td>
						</tr>
						<tr style="border:none;">
							<td colspan="15" align="center" style="border:none; font-size:14px;">
								Date Range : <? echo change_date_format($txt_date_from).' to '.change_date_format($txt_date_to); ?>  
							</td>
						</tr>
					</table>
					<br />
					<table width="<? echo $table_width; ?>" border="1" cellpadding="0" cellspacing="0" class="rpt_table nsbreak" rules="all" id="table_header_2" align="left"> 
						<thead>
							<tr>
								<th width="30">SL</th>
								<th width="120">Basis</th>
								<th width="120">Gate Pass No</th>
								<th width="80">Gate Pass Date</th>
								<th width="120">Sample Type</th>
								<th width="120">Item Category</th>
								<th width="150">Item Description</th>
								<th width="80">Quantity</th>
								<th width="60">UOM</th>
								<th width="100">Sent By</th>
								<th width="100">Sent to</th>
								<th width="60">Return able</th>
								<th width="100">Purpose</th>
								<th width="100">Carried By</th>
								<th>Remarks</th>
							</tr>
						</thead>
				    </table> 
				    <div style="width:<? echo $table_width+20; ?>px; overflow-y: scroll; max-height:400px;" id="scroll_body" align="left">
						<table width="<? echo $table_width; ?>" class="rpt_table nsbreak" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body_2" align="left">
							<tbody>
							<?
							$i=$k=1;$tot_quantity=0;$temp_arr=array();
							//count($get_out_data);
							foreach($get_pending_data as $val)
							{
								if ($i%2==0) $bgcolor="#E9F3FF";
								else $bgcolor="#FFFFFF";
								$gate_out_date=$gate_scan_array[$val[csf('sys_number')]]['out_date'];
								$gate_out_time=$gate_scan_array[$val[csf('sys_number')]]['out_time'];
								$basis=$val[csf('basis')];
								$within_group=$val[csf('within_group')];

								if($basis==1)
								{ 
									if($within_group==1) $send_to_company=$company_arr[$val[csf('sent_to')]];
									else $send_to_company=$val[csf('sent_to')];
								}
								else if($basis==8 || $basis==9)
								{
									$send_to_company=$val[csf('sent_to')];	
								}
								else if($basis==12)
								{
									$send_to_company=$supplier_name_library[$val[csf('sent_to')]];
								}
								else
								{
									if($basis==2 || $basis==3 || $basis==4 || $basis==5 || $basis==6 || $basis==7  ||  $basis==10 || $basis==13 ||  $basis==14 ||  $basis==11 ||  $basis==15)
									{
										if($within_group==2) $send_to_company=$supplier_name_library[$val[csf('sent_to')]];
										else $send_to_company=$company_arr[$val[csf('sent_to')]];
									}
									else $send_to_company=$val[csf('sent_to')];
								}								
								?>
								<tr  bgcolor="<? echo $bgcolor; ?>" onClick="change_color('troutp_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="troutp_<? echo $i; ?>">
	                            	<?
									if(!in_array($val[csf("sys_number")],$temp_arr))
									{
										$temp_arr[]=$val[csf("sys_number")];
										?>
										<td width="30" align="center"><p><? echo $k; ?>&nbsp;</p></td>
	                                    <?
										$k++;
									}
									else
									{
										?>
										<td width="30"><p>&nbsp;</p></td>
	                                    <?
									}
									?>
									<td width="120"><p><? echo $get_pass_basis[$val[csf("basis")]]; ?>&nbsp;</p></td>	

									<!-- <td width="120"><p><a href='#report_details' onClick="fnc_get_pass_print('<? //echo $val[csf('company_id')]; ?>','<? //echo $val[csf('sys_number')]; ?>');"><? //echo $val[csf('sys_number')]; ?></a>&nbsp;</p></td> -->

									<td width="120"><p><a href='#report_details' onClick="fnc_get_pass_print('<? echo $val[csf('company_id')]; ?>','<? echo $val[csf('sys_number')]; ?>','<? echo $getpass_button; ?>','<? echo $val[csf('basis')]; ?>','<? echo $val[csf('com_location_id')]; ?>','<? echo $val[csf('returnable')]; ?>','<? echo $val[csf('challan_no')]; ?>','<? echo $val[csf('issue_id')]; ?>');"><? echo $val[csf('sys_number')]; ?></a>&nbsp;</p></td>


									<td width="80"><p><? echo change_date_format($val[csf('out_date')]); ?>&nbsp;</p></td>
									<td width="120"><p><? echo $sample_arr[$val[csf("sample_id")]]; ?>&nbsp;</p></td>
									<td width="120"><p><? echo $item_category[$val[csf("item_category_id")]]; ?>&nbsp;</p></td>
									<td width="150"><p><? echo  $val[csf("item_description")]; ?>&nbsp;</p></td>
									<td width="80" align="right"><p><? echo number_format($val[csf("quantity")],2); ?></p></td>
									<td width="60" align="center"><p><? echo $unit_of_measurement[$val[csf("uom")]]; ?>&nbsp;</p></td>
									<td width="100" align="center"><p><? echo $val[csf("sent_by")]; ?>&nbsp;</p></td>
									<td width="100" align="center"><p><? echo $send_to_company; ?>&nbsp;</p></td>
									<td width="60" align="center"><p><? echo  $yes_no[$val[csf("returnable")]]; ?>&nbsp;</p></td>
									<td width="100" align="center"><p><? echo $val[csf("issue_purpose")]; ?>&nbsp;</p></td>
									<td width="100" align="center"><p><? echo $val[csf("carried_by")]; ?>&nbsp;</p></td>
									<td width=""><p><? echo $val[csf("remarks")]; ?>&nbsp;</p></td>
								</tr>
								<?
								$tot_quantity+=$val[csf("quantity")];
								$tot_uom_qty+=$val[csf("uom_qty")];
								$i++;
							}
							?>   
							</tbody>
							<tfoot>
								<th colspan="7">Total</th>
	                            <th><? echo $tot_quantity;?></th>
								<th></th> 
								<th></th> 
	                            <th></th> 
	                            <th></th> 
	                            <th></th> 
	                            <th></th> 
	                            <th></th>
							</tfoot>
						</table>
                	</div>
                </div>
				<?
			}
		} //Out Pending
		?>
        <br/><br/>
		<?
	    if($cbo_gate_type==0 || $cbo_gate_type==4) // Return Pending
		{
		    if(count($get_return_data)>0)
			{
				?>
				<div style="width:<? echo $table_width; ?>px;">
					<table width="<? echo $table_width; ?>" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="" align="left"> 
						<tr class="form_caption" style="border:none;">
							<td colspan="15" align="center" style="border:none;font-size:16px; font-weight:bold" >Return Pending</td> 
						</tr>
						<tr style="border:none;">
							<td colspan="15" align="center" style="border:none; font-size:14px;">
								<? echo $company_arr[$cbo_company_name]; ?>
							</td>
						</tr>
						<tr style="border:none;">
							<td colspan="15" align="center" style="border:none; font-size:14px;">
								Date Range : <? echo change_date_format($txt_date_from).' to '.change_date_format($txt_date_to); ?>  
							</td>
						</tr>
					</table>
					<br />
					<table width="<? echo $table_width; ?>" border="1" cellpadding="0" cellspacing="0" class="rpt_table nsbreak" rules="all" id="table_header_2" align="left"> 
						<thead>
							<tr>
								<th width="30" >SL</th>
								<th width="120">Basis</th>
								<th width="120">Gate Pass No</th>
								<th width="80">Gate Pass Date</th>
								<th width="120">Sample Type</th>
								<th width="120">Item Category</th>
								<th width="150">Item Description</th>
								<th width="80">Quantity</th>
								<th width="60">UOM</th>
								<th width="100">Sent By</th>
								<th width="100">Sent to</th>
								<th width="60">Return able</th>
								<th width="100">Purpose</th>
								<th width="100">Carried By</th>
								<th>Remarks</th>
							</tr>
						</thead>
				    </table> 
					<div style="width:<? echo $table_width+20; ?>px; overflow-y: scroll; max-height:400px;" id="scroll_body" align="left">
						<table width="<? echo $table_width; ?>" class="rpt_table nsbreak" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body_3" align="left">
							<tbody>
							<?
							$i=$k=1;$total_out=0;
							$temp_arr=array();$tot_quantity=0;
							foreach($get_return_data as $val)
							{
								if(!in_array($val[csf('sys_number')], $gatePassNoArr))
								{
									if ($i%2==0)
									$bgcolor="#E9F3FF";
									else
									$bgcolor="#FFFFFF";
									$gate_out_date=$gate_scan_array[$val[csf('sys_number')]]['out_date'];
									$gate_out_time=$gate_scan_array[$val[csf('sys_number')]]['out_time'];
									$basis=$val[csf('basis')];
									$within_group=$val[csf('within_group')];

									if($basis==1)
									{ 
										if($within_group==1) $send_to_company=$company_arr[$val[csf('sent_to')]];
										else $send_to_company=$val[csf('sent_to')];	
									}
									else if($basis==8 || $basis==9)
									{
										$send_to_company=$val[csf('sent_to')];	
									}
									else if($basis==12)
									{
										$send_to_company=$supplier_name_library[$val[csf('sent_to')]];
									}
									else
									{
										if($basis==2 || $basis==3 || $basis==4 || $basis==5 || $basis==6 || $basis==7  ||  $basis==10 || $basis==13 ||  $basis==14 ||  $basis==11 ||  $basis==15)
										{
											if($within_group==2) $send_to_company=$supplier_name_library[$val[csf('sent_to')]];
											else $send_to_company=$company_arr[$val[csf('sent_to')]];
										}
										else $send_to_company=$val[csf('sent_to')];	
									}
									?>
									<tr  bgcolor="<? echo $bgcolor; ?>" onClick="change_color('troutrp_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="troutrp_<? echo $i; ?>">
										<?
										if(!in_array($val[csf("sys_number")],$temp_arr))
										{
											$temp_arr[]=$val[csf("sys_number")];
											?>
											<td width="30" align="center"><p><? echo $k; ?>&nbsp;</p></td>
		                                    <?
											$k++;
										}
										else
										{
											?>
											<td width="30"><p>&nbsp;</p></td>
		                                    <?
										}
										?>
										<td width="120"><p><? echo $get_pass_basis[$val[csf("basis")]]; ?>&nbsp;</p></td>			
										<td width="120"><p><a href='#report_details' onClick="fnc_get_pass_print('<? echo $val[csf('company_id')]; ?>','<? echo $val[csf('sys_number')]; ?>','<? echo $getpass_button; ?>','<? echo $val[csf('basis')]; ?>','<? echo $val[csf('com_location_id')]; ?>','<? echo $val[csf('returnable')]; ?>','<? echo $val[csf('challan_no')]; ?>','<? echo $val[csf('issue_id')]; ?>');"><? echo $val[csf('sys_number')]; ?></a>&nbsp;</p></td>
										<td width="80"><p><? echo change_date_format($gate_out_date); ?>&nbsp;</p></td>
										<td width="120"><p><? echo $sample_arr[$val[csf("sample_id")]]; ?>&nbsp;</p></td>
										<td width="120"><p><? echo $item_category[$val[csf("item_category_id")]]; ?>&nbsp;</p></td>
										<td width="150"><p><? echo  $val[csf("item_description")]; ?>&nbsp;</p></td>
										<td width="80" align="right"><p><? echo number_format($val[csf("quantity")],2); ?></p></td>
										<td width="60" align="center"><p><? echo $unit_of_measurement[$val[csf("uom")]]; ?>&nbsp;</p></td>
										<td width="100" align="center"><p><? echo $val[csf("sent_by")]; ?>&nbsp;</p></td>
										<td width="100" align="center"><p><? echo $send_to_company; ?>&nbsp;</p></td>
										<td width="60" align="center"><p><? echo  $yes_no[$val[csf("returnable")]]; ?>&nbsp;</p></td>
										<td width="100" align="center"><p><? echo $val[csf("issue_purpose")]; ?>&nbsp;</p></td>
										<td width="100" align="center"><p><? echo $val[csf("carried_by")]; ?>&nbsp;</p></td>
										<td width=""><p><? echo $val[csf("remarks")]; ?>&nbsp;</p></td>
									</tr>
									<?
									$tot_quantity+=$val[csf("quantity")];
									$tot_uom_qty+=$val[csf("uom_qty")];
									$i++;
								}
							}
							?>   
							</tbody>
							<tfoot>
								<th colspan="7">Total</th>
								<th><? echo $tot_quantity;?></th>
								<th></th> 
								<th></th> 
	                            <th></th> 
	                            <th></th> 
	                            <th></th> 
	                            <th></th> 
	                            <th></th>
							</tfoot>							
						</table>
	                </div>
        		</div>
			    <?
			}				
		}  //Return Pending
		?>
    </div>
	<?	 
	$html = ob_get_contents();
	ob_clean();	
	foreach (glob("*.xls") as $filename) {
	    @unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');	
	$is_created = fwrite($create_new_doc, $html);
	echo "$html####$filename"; 
	exit();
}

if($action=="generate_report3")
{
    $process = array( &$_POST );
    extract(check_magic_quote_gpc( $process ));
    $cbo_item_cat=str_replace("'","",$cbo_item_cat);
    $cbo_company_name=str_replace("'","",$cbo_company_name);

    $cbo_party_type=str_replace("'","",$cbo_party_type);
    $cbo_search_by=str_replace("'","",$cbo_search_by);

    $txt_pi_no=str_replace("'","",$txt_pi_no);
    $cbo_gate_type=str_replace("'","",$cbo_gate_type);
    $txt_challan=str_replace("'","",$txt_challan);
    $cbo_sample=str_replace("'","",$cbo_sample);
    $sample_chk=str_replace("'","",$sample_chk_id);

    $hidden_pi_id=str_replace("'","",$hidden_pi_id);

    $company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
    $sample_arr=return_library_array(" select id,sample_name from lib_sample where status_active=1 order by sample_name",'id','sample_name');
    $supplier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
    $supplier_name_library=return_library_array( "select id, supplier_name from  lib_supplier", "id", "supplier_name");
    $other_party_name_arr=return_library_array( "select id, other_party_name from  lib_other_party",'id','other_party_name');
    $buyer_name_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
    $location_arr=return_library_array("select id,location_name from lib_location", "id", "location_name");
    $department_arr=return_library_array("select a.id,a.department_name from  lib_department a, lib_division b where b.id=a.division_id and a.status_active =1 and a.is_deleted=0 and b.company_id=$cbo_company_name", "id", "department_name");

    $txt_date_from=str_replace("'","",$txt_date_from);
    $txt_date_to=str_replace("'","",$txt_date_to);

    $sample_cond='';
    if($sample_chk==1)
    {
        if($cbo_sample==0) $sample_cond=" and b.sample_id>0";
    }
    else
    {
        if($cbo_sample!=0) $sample_cond=" and b.sample_id=$cbo_sample";
    }
    $group_cond = '';
    if($cbo_group != 0){
        $group_cond = " and a.within_group = $cbo_group";
    }
    $item_category_cond=$company_conds='';
    $search_by_cond=$challan_sys_cond='';
    $pi_refernce_cond='';
    if ($cbo_item_cat !=0) $item_category_cond=" and b.item_category_id=$cbo_item_cat";
    if ($cbo_company_name !=0) $company_conds.=" and a.company_id=$cbo_company_name";
    if ($cbo_party_type !=0) $search_by_cond=" and a.party_type=$cbo_party_type";
    if($txt_challan !='') $challan_sys_cond=" and a.sys_number_prefix_num='$txt_challan'";
    if($txt_pi_no !='') $pi_refernce_cond=" and a.pi_reference='$txt_pi_no'";

    $date_cond='';
    if($db_type==0)
    {
        if ($txt_date_from !='' && $txt_date_to !='') $date_cond.=" and a.in_date between '".change_date_format($txt_date_from,"yyyy-mm-dd")."' and '".change_date_format($txt_date_to,"yyyy-mm-dd")."' ";
    }
    else
    {
        if ($txt_date_from !='' && $txt_date_to !='') $date_cond="and a.in_date between '".date("j-M-Y",strtotime($txt_date_from))."' and '".date("j-M-Y",strtotime($txt_date_to))."' ";
    }

    $order_array=array();
    $order_sql="SELECT a.style_ref_no, a.job_no,a.buyer_name, b.id, b.po_number, b.po_quantity from  wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
    $order_sql_result=sql_select($order_sql);
    foreach ($order_sql_result as $row)
    {
        $order_array[$row[csf('po_number')]]['buyer']=$row[csf('buyer_name')];
        $order_array[$row[csf('po_number')]]['style']=$row[csf('style_ref_no')];
    }

    $table_width=2060;
    //echo $cbo_gate_type.'system';die;
    ob_start();
    ?>
    <style type="text/css">
        .nsbreak{word-break: break-all;}
    </style>

    <div style="height:auto; clear:both;">
        <?
        $out_date_cond=$out_date_cond_scan='';
        if($db_type==0)
        {
            if($txt_date_from !='' && $txt_date_to !='') $out_date_cond=" and a.out_date between '".change_date_format($txt_date_from,"yyyy-mm-dd")."' and '".change_date_format($txt_date_to,"yyyy-mm-dd")."' ";
            if($txt_date_from !='' && $txt_date_to !='') $out_date_cond_scan=" and c.out_date between '".change_date_format($txt_date_from,"yyyy-mm-dd")."' and '".change_date_format($txt_date_to,"yyyy-mm-dd")."' ";
        }
        else
        {
            if($txt_date_from !='' && $txt_date_to !='') $out_date_cond="and a.out_date between '".date("j-M-Y",strtotime($txt_date_from))."' and '".date("j-M-Y",strtotime($txt_date_to))."' ";
            if($txt_date_from !='' && $txt_date_to !='') $out_date_cond_scan="and c.out_date between '".date("j-M-Y",strtotime($txt_date_from))."' and '".date("j-M-Y",strtotime($txt_date_to))."' ";
        }
        // echo $out_date_cond_scan;

        $sql_data=sql_select("select out_date, gate_pass_id, out_time from inv_gate_out_scan where status_active=1 and is_deleted=0");
        $gate_scan_array=array();
        foreach($sql_data as $row)
        {
            $gate_scan_array[$row[csf('gate_pass_id')]]['out_date']=$row[csf('out_date')];
            $gate_scan_array[$row[csf('gate_pass_id')]]['out_time']=$row[csf('out_time')];
        }

        $sql_gate=sql_select("select gate_pass_id from inv_gate_out_scan where status_active=1 and is_deleted=0");
        $i=1;
        foreach($sql_gate as $row_g)
        {
            if($i!==1) $row_cond.=",";
            $row_cond.=$row_g[csf('gate_pass_id')];
            $i++;
        }

        $sql_gate_in=sql_select("select gate_pass_no from inv_gate_in_mst where status_active=1 and is_deleted=0 and gate_pass_no is not null ");
        $k=1;
        $gatePassNoArr=array();
        foreach($sql_gate_in as $row_in)
        {
            $in_row_cond[$row_in[csf('gate_pass_no')]]="'".$row_in[csf('gate_pass_no')]."'";
            $gatePassNoArr[].=$row_in[csf('gate_pass_no')];
            $k++;
        }
        $gateInStatus = return_library_array("select sum(quantity) as qty, a.gate_pass_no from inv_gate_in_mst a, inv_gate_pass_mst b, inv_gate_in_dtl c where b.sys_number = a.gate_pass_no and a.id = c.mst_id and a.RETURNABLE = 1 group by a.gate_pass_no", "gate_pass_no", "qty");
        $sysNo_cond_in=where_con_using_array($in_row_cond,0,'a.sys_number not ');
        // echo $sysNo_cond_in;die;

        if($cbo_gate_type==0 || $cbo_gate_type==2) //Gate Out
        {
            $sql_out="SELECT a.id, a.company_id, a.com_location_id, a.location_id, a.department_id, a.location_name, a.delivery_as, a.issue_id, a.sys_number, a.sys_number_prefix_num, a.sent_by, a.within_group, a.basis,	a.issue_purpose, a.sent_to, c.out_date, a.returnable, a.est_return_date, a.challan_no, a.carried_by, buyer_order, a.get_pass_no, b.sample_id, b.item_category_id, b.item_description, b.quantity, b.uom, b.uom_qty, b.remarks, a.time_hour, a.time_minute, b.no_of_bags
			from inv_gate_pass_mst a, inv_gate_pass_dtls b,inv_gate_out_scan c	
			where a.id=b.mst_id and c.gate_pass_id=a.sys_number and a.status_active=1  and a.is_deleted=0 and b.status_active=1  and b.is_deleted=0 $company_conds $group_cond $out_date_cond_scan $item_category_cond $sample_cond $challan_sys_cond 
			order by b.id";
            //echo $sql_out;
            $get_out_data=sql_select($sql_out);
        }

			//        if($cbo_gate_type==0 || $cbo_gate_type==3) // Gate Out Pending
			//        {
			//            $sql_pending="SELECT a.id, a.company_id, a.com_location_id, a.location_id, a.department_id, a.location_name, a.delivery_as, a.issue_id, a.sys_number, a.sys_number_prefix_num, a.sent_by, a.within_group, a.basis, a.issue_purpose, a.sent_to, a.out_date, a.returnable, a.est_return_date, a.challan_no, a.carried_by, buyer_order, a.get_pass_no, b.sample_id, b.item_category_id, b.item_description, b.quantity, b.uom, b.uom_qty, b.remarks, a.time_hour, a.time_minute, b.no_of_bags
			//			from inv_gate_pass_mst a, inv_gate_pass_dtls b
			//			where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.sys_number not in(select a.gate_pass_id from inv_gate_out_scan a where a.status_active=1 and a.is_deleted=0 ) $company_conds $group_cond $out_date_cond $item_category_cond $challan_sys_cond $sample_cond
			//			order by b.id";
			//
			//            //echo $sql_pending;
			//            $get_pending_data=sql_select($sql_pending);
			//        }
			//        if($cbo_gate_type==0 || $cbo_gate_type==4) // Return Pending
			//        {
			//            $sql_ret_pending="SELECT a.id, a.company_id, a.com_location_id,  a.location_id, a.department_id, a.location_name, a.delivery_as, a.issue_id, a.sys_number, a.sys_number_prefix_num, a.sent_by, a.issue_purpose, a.within_group, a.basis, a.sent_to, a.out_date, a.returnable, a.est_return_date, a.challan_no, a.carried_by, buyer_order, a.get_pass_no, b.sample_id, b.item_category_id, b.item_description, b.quantity, b.uom, b.uom_qty, b.remarks, a.time_hour, a.time_minute, b.no_of_bags
			//			from inv_gate_pass_mst a, inv_gate_pass_dtls b,inv_gate_out_scan c
			//			where a.id=b.mst_id and a.sys_number=c.gate_pass_id and a.returnable=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $sysNo_cond_in  $company_conds $group_cond $out_date_cond $item_category_cond $challan_sys_cond $sample_cond
			//			order by b.id";
			//            $get_return_data=sql_select($sql_ret_pending);
			//        }


        //$print_report_format=return_library_array( "select template_name, format_id from lib_report_template where module_id=6 and report_id=124 and is_deleted=0 and status_active=1",'template_name','format_id');

        $print_report_format=return_field_value("format_id","lib_report_template","template_name =".$cbo_company_name." and module_id=6 and report_id=38 and is_deleted=0 and status_active=1");



        $print_button=explode(",",$print_report_format);
        $print_button_first=array_shift($print_button);

        //echo $print_button_first.'D';
        if($print_button_first==115) $getpass_button="get_out_entry_print";
        else if($print_button_first==116) $getpass_button="print_to_html_report";
        else if($print_button_first==136) $getpass_button="get_out_entry_emb_issue_print";
        else if($print_button_first==137) $getpass_button="print_to_html_report5";
        else if($print_button_first==196) $getpass_button="print_to_html_report6";
        else if($print_button_first==199) $getpass_button="print_to_html_report7";
        else if($print_button_first==206) $getpass_button="get_out_entry_print8_fashion";
        else if($print_button_first==207) $getpass_button="print_to_html_report9";
        else if($print_button_first==208) $getpass_button="print_to_html_report10";
        else if($print_button_first==212) $getpass_button="print_to_html_report11";
        else if($print_button_first==271) $getpass_button="print_to_html_report14";
        else if($print_button_first==42) $getpass_button="print_to_html_report_15";
        else if($print_button_first==362) $getpass_button="print_to_html_report_15_v2";
        else if($print_button_first==227) $getpass_button="print_to_html_report16";
        else if($print_button_first==227) $getpass_button="get_out_entry_print12";
        else if($print_button_first==191) $getpass_button="print_to_html_report_13";
        else if($print_button_first==161) $getpass_button="get_out_entry_print6";
        else if($print_button_first==235) $getpass_button="get_out_entry_print9";
        else if($print_button_first==274) $getpass_button="get_out_entry_print10";
        else if($print_button_first==707) $getpass_button="print_to_html_report17";
        else if($print_button_first==738) $getpass_button="get_out_entry_printamt";
        else if($print_button_first==747) $getpass_button="get_out_entry_print14";
        else  $getpass_button="";

        //echo $getpass_button; die;



        ?>
        <br/><br/>
        <?
        if($cbo_gate_type==0 || $cbo_gate_type==2) //Gate Out
        {
            if(count($get_out_data)>0)
            {
                ?>
                <div style="width:<? echo $table_width; ?>px;">
                    <table width="<? echo $table_width; ?>" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="" align="left">
                        <tr class="form_caption" style="border:none;">
                            <td colspan="15" align="center" style="border:none;font-size:16px; font-weight:bold" >Daily Gate In and Out Report</td>
                        </tr>
                        <tr style="border:none;">
                            <td colspan="15" align="center" style="border:none; font-size:14px;">
                                <? echo $company_arr[$cbo_company_name]; ?>
                            </td>
                        </tr>
                        <tr style="border:none;">
                            <td colspan="15" align="center" style="border:none; font-size:14px;">
                                Date Range : <? echo change_date_format($txt_date_from).' to '.change_date_format($txt_date_to); ?>
                            </td>
                        </tr>
                    </table>
                    <br />
                    <table width="<? echo $table_width; ?>" border="1" cellpadding="0" cellspacing="0" class="rpt_table nsbreak" rules="all" id="table_header_2" align="left">
                        <thead>
                        <tr>
                            <th width="30" >SL</th>
                            <th width="120">Basis</th>
                            <th width="120">Gate Pass No</th>
                            <th width="80">Gate Pass Date</th>
                            <th width="120">Sample Type</th>
                            <th width="90">Within Group</th>
                            <th width="110">From Location</th>
                            <th width="100">Department</th>
                            <th width="120">Item Category</th>
                            <th width="150">Item Description</th>
                            <th width="80">Quantity</th>
                            <th width="60">UOM</th>
                            <th width="100">Sent By</th>
                            <th width="100">Sent to</th>
                            <th width="110">To Location</th>
                            <th width="60">Returnable</th>
                            <th width="90">Gate In Status</th>
                            <th width="100">Delivery As</th>
                            <th width="100" >Purpose</th>
                            <th width="100" >Carried By</th>
                            <th>Remarks</th>
                        </tr>
                        </thead>
                    </table>
                    <div style="width:<? echo $table_width+20; ?>px; overflow-y: scroll; max-height: 550px;" id="scroll_body" align="left">
                        <table width="<? echo $table_width; ?>" class="rpt_table nsbreak" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body_1" align="left">
                            <tbody>
                            <?
                            $i=$k=1;$tot_quantity=0;
                            $temp_arr=array();
                            foreach($get_out_data as $val)
                            {
                                if ($i%2==0) $bgcolor="#E9F3FF";
                                else $bgcolor="#FFFFFF";
                                $gate_out_date=$gate_scan_array[$val[csf('sys_number')]]['out_date'];
                                $gate_out_time=$gate_scan_array[$val[csf('sys_number')]]['out_time'];
                                $basis=$val[csf('basis')];
                                $within_group=$val[csf('within_group')];

                                if($basis==1)
                                {
                                    if($within_group==1) $send_to_company=$company_arr[$val[csf('sent_to')]];
                                    else $send_to_company=$val[csf('sent_to')];
                                }
                                else if($basis==8 || $basis==9)
                                {
                                    $send_to_company=$val[csf('sent_to')];
                                }
                                else if($basis==12)
                                {
                                    $send_to_company=$supplier_name_library[$val[csf('sent_to')]];
                                }
                                else
                                {
                                    //echo $within_group.'=='.$basis;
                                    if($basis==2 || $basis==3 || $basis==4 || $basis==5 || $basis==6 || $basis==7  ||  $basis==10 || $basis==13 ||  $basis==14 ||  $basis==11 ||  $basis==15)
                                    {
                                        if($within_group==2) $send_to_company=$supplier_name_library[$val[csf('sent_to')]];
                                        else $send_to_company=$company_arr[$val[csf('sent_to')]];
                                    }
                                    else $send_to_company=$val[csf('sent_to')];
                                }
                                ?>
                                <tr  bgcolor="<? echo $bgcolor; ?>" onClick="change_color('trout_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="trout_<? echo $i; ?>">
                                    <?
                                    if(!in_array($val[csf('sys_number')],$temp_arr))
                                    {
                                        $temp_arr[]=$val[csf('sys_number')];
                                        ?>
                                        <td width="30" align="center"><p><? echo $k; ?>&nbsp;</p></td>
                                        <?
                                        $k++;
                                    }
                                    else
                                    {
                                        ?>
                                        <td width="30"><p>&nbsp;</p></td>
                                        <?
                                    }
                                    ?>
                                    <td width="120"><p><? echo $get_pass_basis[$val[csf("basis")]]; ?>&nbsp;</p></td>
                                    <td width="120"><p><a href='#report_details' onClick="fnc_get_pass_print('<? echo $val[csf('company_id')]; ?>','<? echo $val[csf('sys_number')]; ?>','<? echo $getpass_button; ?>','<? echo $val[csf('basis')]; ?>','<? echo $val[csf('com_location_id')]; ?>','<? echo $val[csf('returnable')]; ?>','<? echo $val[csf('challan_no')]; ?>','<? echo $val[csf('issue_id')]; ?>');"><? echo $val[csf('sys_number')]; ?></a> &nbsp;</p></td>
                                    <td width="80"><p><? echo change_date_format($gate_out_date); ?>&nbsp;</p></td>
                                    <td width="120"><p><? echo $sample_arr[$val[csf("sample_id")]]; ?>&nbsp;</p></td>
                                    <td width="90" align="center"><p><?=$val[csf("within_group")] == 1 ? 'Yes' : ($val[csf("within_group")] == 2 ? 'No' : '')?></p></td>
                                    <td width="110"><p><?=$location_arr[$val[csf("com_location_id")]]?></p></td>
                                    <td width="100"><p><?=$department_arr[$val[csf("department_id")]]?></p></td>
                                    <td width="120"><p><? echo $item_category[$val[csf("item_category_id")]]; ?>&nbsp;</p></td>
                                    <td width="150"><p><? echo  $val[csf("item_description")]; ?>&nbsp;</p></td>
                                    <td width="80" align="right"><p><? echo number_format($val[csf("quantity")],2); ?></p></td>
                                    <td width="60" align="center"><p><? echo $unit_of_measurement[$val[csf("uom")]]; ?>&nbsp;</p></td>
                                    <td width="100" align="center"><p><? echo $val[csf("sent_by")]; ?>&nbsp;</p></td>
                                    <td width="100" align="center"><p><? echo $send_to_company; ?>&nbsp;</p></td>
                                    <td width="110"><?=$val[csf("within_group")] == 1 ? $location_arr[$val[csf("location_id")]] : $val[csf("location_name")]?></td>
                                    <td width="60" align="center"><p><? echo  $yes_no[$val[csf("returnable")]]; ?>&nbsp;</p></td>
                                    <td width="90">
                                        <?
                                        if($val[csf("returnable")] == 1){
                                            if(isset($gateInStatus[$val[csf("sys_number")]])){
                                                if($gateInStatus[$val[csf("sys_number")]] >= $val[csf("quantity")]){
                                                    echo "Complete";
                                                }else{
                                                 echo "Partial";
                                                }
                                            }else{
                                                echo "Pending";
                                            }
                                        }
                                        ?>
                                    </td>
                                    <td width="100"><p><?=$basis_arr[$val[csf("delivery_as")]]?></p></td>
                                    <td width="100" align="center"><p><? echo $val[csf("issue_purpose")]; ?>&nbsp;</p></td>
                                    <td width="100" align="center"><p><? echo $val[csf("carried_by")]; ?>&nbsp;</p></td>
                                    <td width=""><p><? echo $val[csf("remarks")]; ?>&nbsp;</p></td>
                                </tr>
                                <?
                                $tot_quantity+=$val[csf("quantity")];
                                $tot_uom_qty+=$val[csf("uom_qty")];
                                $i++;
                            }
                            ?>
                            </tbody>
                            <tfoot>
                            <th colspan="10">Total</th>
                            <th><? echo $tot_quantity;?></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <?
            }
        } //Out End
        ?>
    </div>
    <?
    $html = ob_get_contents();
    ob_clean();
    foreach (glob("*.xls") as $filename) {
        @unlink($filename);
    }
    //---------end------------//
    $name=time();
    $filename=$user_id."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');
    $is_created = fwrite($create_new_doc, $html);
    echo "$html####$filename";
    exit();
}

if($action=="generate_report4")
{
    $process = array( &$_POST );
    extract(check_magic_quote_gpc( $process ));
    $cbo_item_cat=str_replace("'","",$cbo_item_cat);
    $cbo_company_name=str_replace("'","",$cbo_company_name);
    $cbo_party_type=str_replace("'","",$cbo_party_type);
    $cbo_search_by=str_replace("'","",$cbo_search_by);
    $txt_pi_no=str_replace("'","",$txt_pi_no);
    $cbo_gate_type=str_replace("'","",$cbo_gate_type);
    $txt_challan=str_replace("'","",$txt_challan);
    $cbo_sample=str_replace("'","",$cbo_sample);
    $sample_chk=str_replace("'","",$sample_chk_id); 
    $hidden_pi_id=str_replace("'","",$hidden_pi_id);
	$txt_date_from=str_replace("'","",$txt_date_from);
    $txt_date_to=str_replace("'","",$txt_date_to);

    $company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
    $sample_arr=return_library_array(" select id,sample_name from lib_sample where status_active=1 order by sample_name",'id','sample_name');
	$user_library=return_library_array( "select id, user_full_name from user_passwd", "id", "user_full_name"  );
    $supplier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
    $supplier_name_library=return_library_array( "select id, supplier_name from  lib_supplier", "id", "supplier_name");
    $other_party_name_arr=return_library_array( "select id, other_party_name from  lib_other_party",'id','other_party_name');
    $buyer_name_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
    $location_arr=return_library_array("select id,location_name from lib_location", "id", "location_name");
    $department_arr=return_library_array("select a.id,a.department_name from  lib_department a, lib_division b where b.id=a.division_id and a.status_active =1 and a.is_deleted=0 and b.company_id=$cbo_company_name", "id", "department_name");

    $sample_cond='';
    if($sample_chk==1)
    {
        if($cbo_sample==0) $sample_cond=" and b.sample_id>0";
    }
    else
    {
        if($cbo_sample!=0) $sample_cond=" and b.sample_id=$cbo_sample";
    }
    $group_cond = '';
    if($cbo_group != 0){
        $group_cond = " and a.within_group = $cbo_group";
    }
    $item_category_cond=$company_conds='';
    $search_by_cond=$challan_sys_cond='';
    $pi_refernce_cond='';
    if ($cbo_item_cat !=0) $item_category_cond=" and b.item_category_id=$cbo_item_cat";
    if ($cbo_company_name !=0) $company_conds.=" and a.company_id=$cbo_company_name";
    if ($cbo_party_type !=0) $search_by_cond=" and a.party_type=$cbo_party_type";
    if($txt_challan !='') $challan_sys_cond=" and a.sys_number_prefix_num='$txt_challan'";
    if($txt_pi_no !='') $pi_refernce_cond=" and a.pi_reference='$txt_pi_no'";

    $date_cond='';
    if($db_type==0)
    {
        if ($txt_date_from !='' && $txt_date_to !='') $date_cond.=" and a.in_date between '".change_date_format($txt_date_from,"yyyy-mm-dd")."' and '".change_date_format($txt_date_to,"yyyy-mm-dd")."' ";
    }
    else
    {
        if ($txt_date_from !='' && $txt_date_to !='') $date_cond="and a.in_date between '".date("j-M-Y",strtotime($txt_date_from))."' and '".date("j-M-Y",strtotime($txt_date_to))."' ";
    }

    $order_array=array();
    $order_sql="SELECT a.style_ref_no, a.job_no,a.buyer_name, b.id, b.po_number, b.po_quantity from  wo_po_details_master a, wo_po_break_down b where a.id=b.JOB_ID and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_name=$cbo_company_name";
    $order_sql_result=sql_select($order_sql);
    foreach ($order_sql_result as $row)
    {
        $order_array[$row[csf('id')]]['buyer']=$row[csf('buyer_name')];
        $order_array[$row[csf('id')]]['style']=$row[csf('style_ref_no')];
    }

	//TABLE WIDTH
    $table_width=2950;
    //echo $cbo_gate_type.'system';die;
    ob_start();
    ?>
    <style type="text/css">
        .nsbreak{word-break: break-all;}
    </style>

    <div style="height:auto; clear:both;">
        <?
        $out_date_cond=$out_date_cond_scan='';
        if($db_type==0)
        {
            if($txt_date_from !='' && $txt_date_to !='') $out_date_cond=" and a.out_date between '".change_date_format($txt_date_from,"yyyy-mm-dd")."' and '".change_date_format($txt_date_to,"yyyy-mm-dd")."' ";
            if($txt_date_from !='' && $txt_date_to !='') $out_date_cond_scan=" and c.out_date between '".change_date_format($txt_date_from,"yyyy-mm-dd")."' and '".change_date_format($txt_date_to,"yyyy-mm-dd")."' ";
        }
        else
        {
            if($txt_date_from !='' && $txt_date_to !='') $out_date_cond="and a.out_date between '".date("j-M-Y",strtotime($txt_date_from))."' and '".date("j-M-Y",strtotime($txt_date_to))."' ";
            if($txt_date_from !='' && $txt_date_to !='') $out_date_cond_scan="and c.out_date between '".date("j-M-Y",strtotime($txt_date_from))."' and '".date("j-M-Y",strtotime($txt_date_to))."' ";
        }
        // echo $out_date_cond_scan;

        $sql_data=sql_select("select out_date, gate_pass_id, out_time from inv_gate_out_scan where status_active=1 and is_deleted=0");
        $gate_scan_array=array();
        foreach($sql_data as $row)
        {
            $gate_scan_array[$row[csf('gate_pass_id')]]['out_date']=$row[csf('out_date')];
            $gate_scan_array[$row[csf('gate_pass_id')]]['out_time']=$row[csf('out_time')];
        }

        $sql_gate=sql_select("select gate_pass_id from inv_gate_out_scan where status_active=1 and is_deleted=0");
        $i=1;
        foreach($sql_gate as $row_g)
        {
            if($i!==1) $row_cond.=",";
            $row_cond.=$row_g[csf('gate_pass_id')];
            $i++;
        }

		$sql_gate_in=sql_select("SELECT a.gate_pass_no,a.sys_number,a.sys_number_prefix_num,a.sending_company,a.party_challan,a.inv_gate_pass_mst_id,b.quantity,c.buyer_order, a.inserted_by  as gate_in_user
		from inv_gate_in_mst a, inv_gate_in_dtl b ,inv_gate_pass_dtls c 
		where a.id=b.mst_id and b.get_pass_dtlsid=c.id and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.gate_pass_no is not null and a.company_id=$cbo_company_name order by a.id");
        $k=1;
        $gatePassNoArr=array();
		$getPassDataArr=array();
        foreach($sql_gate_in as $row_in)
        {
			$getPassDataArr[$row_in[csf('inv_gate_pass_mst_id')]]['party_challan']=$row_in[csf('party_challan')];
			$getPassDataArr[$row_in[csf('inv_gate_pass_mst_id')]][$row_in[csf('buyer_order')]]['sys_number'] .= $row_in[csf('sys_number')].",";
			$getPassDataArr[$row_in[csf('sys_number')]]['sys_number_prefix_num'] = $row_in[csf('sys_number_prefix_num')];
			$getPassDataArr[$row_in[csf('inv_gate_pass_mst_id')]]['quantity']+=$row_in[csf('quantity')];
			$getPassDataArr[$row_in[csf('inv_gate_pass_mst_id')]]['sending_company']=$row_in[csf('sending_company')];
			$getPassDataArr[$row_in[csf('inv_gate_pass_mst_id')]]['gate_in_user']=$row_in[csf('gate_in_user')];
			$getPassDataOrderArr[$row_in[csf('inv_gate_pass_mst_id')]][$row_in[csf('buyer_order')]]['quantity'] += $row_in[csf('quantity')];

            $in_row_cond[$row_in[csf('gate_pass_no')]]="'".$row_in[csf('gate_pass_no')]."'";
            $gatePassNoArr[].=$row_in[csf('gate_pass_no')];
            $k++;
        }
        $gateInStatus = return_library_array("select sum(quantity) as qty, a.gate_pass_no from inv_gate_in_mst a, inv_gate_pass_mst b, inv_gate_in_dtl c where b.sys_number = a.gate_pass_no and a.id = c.mst_id and a.RETURNABLE = 1 group by a.gate_pass_no", "gate_pass_no", "qty");
        $sysNo_cond_in=where_con_using_array($in_row_cond,0,'a.sys_number not ');
        // echo $sysNo_cond_in;die;

        if($cbo_gate_type==0 || $cbo_gate_type==2) //Gate Out
        {
            $sql_out="SELECT a.id, a.company_id, a.com_location_id, a.location_id, a.challan_no, a.department_id, a.location_name, a.delivery_as, a.issue_id, a.sys_number, a.sys_number_prefix_num, a.sent_by, a.within_group, a.basis,a.issue_purpose, a.sent_to, c.out_date, a.returnable, a.est_return_date, a.challan_no, a.carried_by, b.buyer_order, a.get_pass_no, b.sample_id, b.item_category_id, b.item_description, b.quantity, b.uom, b.uom_qty, b.remarks, a.time_hour, a.time_minute, b.no_of_bags,b.buyer_order_id, a.inserted_by as gate_pass_user, c.inv_gate_pass_mst_id
			from inv_gate_pass_mst a left join inv_gate_out_scan c on c.gate_pass_id=a.sys_number, inv_gate_pass_dtls b
			where a.id=b.mst_id and a.status_active=1  and a.is_deleted=0 and b.status_active=1  and b.is_deleted=0 $company_conds $group_cond $out_date_cond_scan $item_category_cond $sample_cond $challan_sys_cond 
			order by b.id";
            // echo $sql_out;
            $get_out_data=sql_select($sql_out);
        }

        $print_report_format=return_field_value("format_id","lib_report_template","template_name =".$cbo_company_name." and module_id=6 and report_id=38 and is_deleted=0 and status_active=1");
        $print_button=explode(",",$print_report_format);
        $print_button_first=array_shift($print_button);
        //echo $print_button_first.'D';
        if($print_button_first==115) $getpass_button="get_out_entry_print";
        else if($print_button_first==116) $getpass_button="print_to_html_report";
        else if($print_button_first==136) $getpass_button="get_out_entry_emb_issue_print";
        else if($print_button_first==137) $getpass_button="print_to_html_report5";
        else if($print_button_first==196) $getpass_button="print_to_html_report6";
        else if($print_button_first==199) $getpass_button="print_to_html_report7";
        else if($print_button_first==206) $getpass_button="get_out_entry_print8_fashion";
        else if($print_button_first==207) $getpass_button="print_to_html_report9";
        else if($print_button_first==208) $getpass_button="print_to_html_report10";
        else if($print_button_first==212) $getpass_button="print_to_html_report11";
        else if($print_button_first==271) $getpass_button="print_to_html_report14";
        else if($print_button_first==42) $getpass_button="print_to_html_report_15";
        else if($print_button_first==362) $getpass_button="print_to_html_report_15_v2";
        else if($print_button_first==227) $getpass_button="print_to_html_report16";
        else if($print_button_first==227) $getpass_button="get_out_entry_print12";
        else if($print_button_first==191) $getpass_button="print_to_html_report_13";
        else if($print_button_first==161) $getpass_button="get_out_entry_print6";
        else if($print_button_first==235) $getpass_button="get_out_entry_print9";
        else if($print_button_first==274) $getpass_button="get_out_entry_print10";
        else if($print_button_first==707) $getpass_button="print_to_html_report17";
        else if($print_button_first==738) $getpass_button="get_out_entry_printamt";
        else if($print_button_first==747) $getpass_button="get_out_entry_print14";
        else  $getpass_button="";

        ?>
        <br/><br/>
        <?
        if($cbo_gate_type==0 || $cbo_gate_type==2) //Gate Out
        {
            if(count($get_out_data)>0)
            {
                ?>
                <div style="width:<? echo $table_width; ?>px;">
                    <table width="<? echo $table_width; ?>" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="" align="left">
                        <tr class="form_caption" style="border:none;">
                            <td colspan="15" align="center" style="border:none;font-size:16px; font-weight:bold" >Daily Gate In and Out Report</td>
                        </tr>
                        <tr style="border:none;">
                            <td colspan="15" align="center" style="border:none; font-size:14px;">
                                <? echo $company_arr[$cbo_company_name]; ?>
                            </td>
                        </tr>
                        <tr style="border:none;">
                            <td colspan="15" align="center" style="border:none; font-size:14px;">
                                Date Range : <? echo change_date_format($txt_date_from).' to '.change_date_format($txt_date_to); ?>
                            </td>
                        </tr>
                    </table>
                    <br />
                    <table width="<? echo $table_width; ?>" border="1" cellpadding="0" cellspacing="0" class="rpt_table nsbreak" rules="all" id="table_header_2" align="left">
                        <thead>
                        <tr>
                            <th width="30" >SL</th>
                            <th width="120">Basis</th>
                            <th width="120">Gate Pass No</th>
                            <th width="80">Gate Pass Date</th>
                            <th width="100">Gate Out Status</th>
                            <th width="80">System Challan No.</th>
                            <th width="120">Sample Type</th>
                            <th width="90">Within Group</th>
                            <th width="110">From Location</th>
                            <th width="100">Department</th>
                            <th width="120">Item Category</th>
                            <th width="150">Item Description</th>
                            <th width="80">Buyer</th>
                            <th width="80">Style</th>
                            <th width="80">Order NO</th>
                            <th width="80">Send Quantity</th>
                            <th width="60">UOM</th>
                            <th width="100">Sent By</th>
                            <th width="100">Sent to</th>
                            <th width="110">To Location</th>
                            <th width="60">Returnable</th>
                            <th width="120">Gate Pass Insert User</th>
                            <th width="90">Gate In ID</th>
                            <th width="90">Recv Qty</th>
                            <th width="90">Balance</th>
                            <th width="90">Gate In Status</th>
                            <th width="100">Delivery As</th>
                            <th width="100" >Purpose</th>
                            <th width="100" >Carried By</th>
                            <th width="100" >Gate In Insert User</th>
                            <th>Remarks</th>
                        </tr>
                        </thead>
                    </table>
                    <div style="width:<? echo $table_width+20; ?>px; overflow-y: scroll; max-height: 550px;" id="scroll_body" align="left">
                        <table width="<? echo $table_width; ?>" class="rpt_table nsbreak" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body_1" align="left">
                            <tbody>
								<?
								$i=$k=1;$tot_quantity=0;
								$temp_arr=array();
								foreach($get_out_data as $val)
								{
									if ($i%2==0) $bgcolor="#E9F3FF";
									else $bgcolor="#FFFFFF";
									$gate_out_date=$gate_scan_array[$val[csf('sys_number')]]['out_date'];
									$gate_out_time=$gate_scan_array[$val[csf('sys_number')]]['out_time'];
									$basis=$val[csf('basis')];
									$within_group=$val[csf('within_group')];

									$out_status=$val[csf('inv_gate_pass_mst_id')];
									if($get_out_status==""){
										$get_out_status= "No";
									}else{
										$get_out_status= "Yes";
									}
									
									$company_id=$val[csf('company_id')];
									$sys_number=$val[csf('sys_number')];
									?>
									<tr  bgcolor="<? echo $bgcolor; ?>" onClick="change_color('trout_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="trout_<? echo $i; ?>">
										<?
										if(!in_array($val[csf('sys_number')],$temp_arr))
										{
											$temp_arr[]=$val[csf('sys_number')];
											?>
											<td width="30" align="center"><p><? echo $k; ?>&nbsp;</p></td>
											<?
											$k++;
										}
										else
										{
											?>
											<td width="30"><p>&nbsp;</p></td>
											<?
										}
										?>
										<td width="120"><p><? echo $get_pass_basis[$val[csf("basis")]]; ?>&nbsp;</p></td>
										<td width="120"><p><a href='#report_details' onClick="fnc_get_pass_print('<? echo $val[csf('company_id')]; ?>','<? echo $val[csf('sys_number')]; ?>','<? echo $getpass_button; ?>','<? echo $val[csf('basis')]; ?>','<? echo $val[csf('com_location_id')]; ?>','<? echo $val[csf('returnable')]; ?>','<? echo $val[csf('challan_no')]; ?>','<? echo $val[csf('issue_id')]; ?>');"><? echo $val[csf('sys_number')]; ?></a> &nbsp;</p></td>
										<td width="80"><p><? echo change_date_format($gate_out_date); ?>&nbsp;</p></td>
										<td width="100"><p><? echo $get_out_status; ?>&nbsp;</p></td>
										<td width="80"><p><?
											//echo $getPassDataArr[$val[csf('id')]]['party_challan']; 
											echo $val[csf('challan_no')]; 
											?>&nbsp;</p></td>
										<td width="120"><p><? echo $sample_arr[$val[csf("sample_id")]]; ?>&nbsp;</p></td>
										<td width="90" align="center"><p><?=$val[csf("within_group")] == 1 ? 'Yes' : ($val[csf("within_group")] == 2 ? 'No' : '')?></p></td>
										<td width="110"><p><?=$location_arr[$val[csf("com_location_id")]]?></p></td>
										<td width="100"><p><?=$department_arr[$val[csf("department_id")]]?></p></td>
										<td width="120"><p><? echo $item_category[$val[csf("item_category_id")]]; ?>&nbsp;</p></td>
										<td width="150"><p><? echo  $val[csf("item_description")]; ?>&nbsp;</p></td>
										<td width="80" align="right"><p><? echo $buyer_name_arr[$order_array[$val[csf('buyer_order_id')]]['buyer']]; ?></p></td>
										<td width="80" align="right"><p><? echo $order_array[$val[csf('buyer_order_id')]]['style']; ?></p></td>
										<td width="80" align="center"><p><? echo $val[csf("buyer_order")]; ?></p></td> 
										<td width="80" align="right"><p><? echo number_format($val[csf("quantity")],2); ?></p></td>
										<td width="60" align="center"><p><? echo $unit_of_measurement[$val[csf("uom")]]; ?>&nbsp;</p></td>
										<td width="100" align="center"><p><? echo $val[csf("sent_by")]; ?>&nbsp;</p></td>
										<td width="100" align="center"><p><? echo $val[csf('sent_to')]; ?>&nbsp;</p></td>
										<td width="110"><?=$val[csf("within_group")] == 1 ? $location_arr[$val[csf("location_id")]] : $val[csf("location_name")]?></td>
										<td width="60" align="center"><p><? echo  $yes_no[$val[csf("returnable")]]; ?>&nbsp;</p></td>
										<td width="120" align="center"><p><? echo $user_library[$val[csf("gate_pass_user")]]; ?>&nbsp;</p></td>
										<td width="90" align="center"><p><?
										$sys_no_dtls_arr = array_filter(array_unique(explode(",", $getPassDataArr[$val[csf('id')]][$val[csf('buyer_order')]]['sys_number'])));
										//print_r($sys_no_dtls_arr);
											foreach ($sys_no_dtls_arr as $sys_no_dtls) 
											{
												?>
												<a href='##' onClick="gate_in_report('<?php echo $company_id; ?>', '<?php echo $sys_no_dtls; ?>', '<?php echo $getPassDataArr[$val[csf('id')]]['sending_company']; ?>', '<?php echo $val[csf("com_location_id")]; ?>')"><? echo $getPassDataArr[$sys_no_dtls]['sys_number_prefix_num'] .',' ; ?></a>
												<?
											}									 
											?>&nbsp;</p></td>
										<td width="90" align="right">
											<p>
												<a href="##" onClick="fnc_rec_qty_in_out_details('<?php echo $company_id; ?>', '<?php echo $sys_number; ?>', '<?php echo $val[csf("buyer_order")]; ?>', 'rec_qty_popup', 'Rec Quantity')">
													<?php
													if($val[csf('buyer_order')]){
														echo number_format($getPassDataOrderArr[$val[csf('id')]][$val[csf('buyer_order')]]['quantity'], 2); 
													}
													else{
														echo number_format($getPassDataArr[$val[csf('id')]]['quantity'], 2); 
													}						
													?>&nbsp;
												</a>
											</p>
										</td>
										<td width="90" align="right"><p>
											<?
											// echo number_format($val[csf("quantity")]-$getPassDataArr[$val[csf('id')]]['quantity'],2); 

											if($val[csf('buyer_order')]){
												$balance =  number_format($val[csf("quantity")]-$getPassDataOrderArr[$val[csf('id')]][$val[csf('buyer_order')]]['quantity'],2);
											}
											else{
												$balance =  number_format($val[csf("quantity")]-$getPassDataArr[$val[csf('id')]]['quantity'],2);
											}
											echo $balance;  
											?>&nbsp;</p>
										</td>
										<td align="center" width="90">
												<?
												if($balance>0){
													echo "Partial";
												}
												else{
													echo "Complete";
												}
											?>
										</td>
										<td width="100"><p><?=$basis_arr[$val[csf("delivery_as")]]?></p></td>
										<td width="100" align="center"><p><? echo $val[csf("issue_purpose")]; ?>&nbsp;</p></td>
										<td width="100" align="center"><p><? echo $val[csf("carried_by")]; ?>&nbsp;</p></td>
										<td width="100" align="center"><p><? echo $user_library[$getPassDataArr[$val[csf('id')]]['gate_in_user']]; ?>&nbsp;</p></td>
										<td width=""><p><? echo $val[csf("remarks")]; ?>&nbsp;</p></td>
									</tr>
									<?
									$tot_quantity+=$val[csf("quantity")];
									$tot_uom_qty+=$val[csf("uom_qty")];

									if($val[csf('buyer_order')]){
										$total_rcv_qty += $getPassDataOrderArr[$val[csf('id')]][$val[csf('buyer_order')]]['quantity']; 
										$total_balance +=  ($val[csf("quantity")]-$getPassDataOrderArr[$val[csf('id')]][$val[csf('buyer_order')]]['quantity']);
									}
									else{
										$total_rcv_qty += $getPassDataArr[$val[csf('id')]]['quantity']; 
										$total_balance +=  ($val[csf("quantity")]-$getPassDataArr[$val[csf('id')]]['quantity']);
									}
									$i++;
								}
								?>
                            </tbody>
							<tfoot>
								<tr>
									<th colspan="15">Total</th>
									<th><? echo number_format($tot_quantity);?></th>
									<th></th>
									<th></th>
									<th></th>
									<th></th>
									<th></th>
									<th></th>
									<th><? echo number_format($total_rcv_qty); ?>&nbsp;</th>
									<th><? echo number_format($total_balance); ?>&nbsp;</th>
									<th></th>
									<th></th>
									<th></th>
									<th></th>
									<th></th>
									<th></th>
									<th></th>
								</tr>
							</tfoot>
                        </table>
                    </div>
                </div>
                <?
            }
        } //Out End
        ?>
    </div>
    <?
    $html = ob_get_contents();
    ob_clean();
    foreach (glob("*.xls") as $filename) {
        @unlink($filename);
    }
    //---------end------------//
    $name=time();
    $filename=$user_id."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');
    $is_created = fwrite($create_new_doc, $html);
    echo "$html####$filename";
    exit();
}


if($action=='rec_qty_popup') {
	echo load_html_head_contents('Popup Info', '../../../', 1, 1, $unicode);
	extract($_REQUEST);
	?>
	<style>
		tbody tr td {
			text-align: center;
		}
	</style>
    <table width="795" cellpadding="0" cellspacing="0" class="rpt_table" border="1" rules="all">
    	<thead>
        	<tr> 					
            	<th width="30"> ID</th>
            	<th width="150">Gate In ID</th>
                <th width="100">Date</th>
                <th width="100">Party Challan</th>
                <th width="80"> Qty</th>
                <th width="120">Balance</th>
            </tr>
        </thead>
        <tbody>
		<?php
		
		if($buyer_order){$order_con = " and c.BUYER_ORDER = '$buyer_order' ";};

		$sql="SELECT a.id, a.sys_number, a.sending_company,a.party_challan, a.in_date, a.challan_no, a.gate_pass_no, a.carried_by, a.pi_reference, b.sample_id,	b.item_category_id,	a.party_type, b.item_description, b.quantity, b.buyer_order, b.uom, b.uom_qty, b.rate, b.amount, b.remarks, a.time_hour, a.time_minute, c.buyer_order_id,c.quantity as get_pass_qty
		from inv_gate_in_mst a, inv_gate_in_dtl b, inv_gate_pass_dtls c 
		where a.id=b.mst_id and b.get_pass_dtlsid=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.company_id=$cbo_company_name and a.gate_pass_no='$gat_pass_system_no' $order_con order by b.id";

		// echo $sql;
		$sql_result=sql_select($sql); $t=1;
		$total_qc_qty=0;
		foreach($sql_result as $row)
		{
			if($t==1)
			{
				$bal =$row[csf('get_pass_qty')]-$row[csf('quantity')];
			}
			else{
				$bal=$bal-$row[csf('quantity')];
			}
			if ($t%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF"; ?>
        	<tr bgcolor="<?php echo $bgcolor; ?>" onclick="change_color('tr_<?php echo $t; ?>','<?php echo $bgcolor; ?>')" id="tr_<?php echo $t; ?>">
        		<td><p><?php echo $t;?></p></td>
                <td><p><?php echo $row[csf('sys_number')]; ?></p></td>
                <td><p><?php echo change_date_format($row[csf('in_date')]); ?></p></td>
                <td><p><?php echo $row[csf('party_challan')]; ?></p></td>
                <td><p><?php echo number_format($row[csf('quantity')]); ?></p></td>
                <td><p><?php echo number_format($bal,2); ?></p></td>
            </tr>
            <?php
            $total_qc_qty += $row[csf('quantity')];
			$t++;
		}
		?>
        </tbody>
        <tfoot>
        	<tr>
                <th></th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th><b>Total:</b></th>
                <th align="center"><b><?echo number_format($total_qc_qty);?>&nbsp;&nbsp;</b></th>
                <th>&nbsp;</th>
            </tr>
        </tfoot>
    </table>
    <?php
}
?>

