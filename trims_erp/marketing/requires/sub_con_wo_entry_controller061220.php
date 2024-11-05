<?
include('../../../includes/common.php'); 
session_start();
  
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }

if ($action=="load_drop_down_location")
{
	$data=explode("_",$data);
	if($data[1]==1) $dropdown_name="cbo_location_name";
	else $dropdown_name="cbo_party_location";
	$location_arr=return_library_array( "select id, location_name from lib_location where company_id='$data[0]' and status_active =1 and is_deleted=0 order by location_name",'id','location_name');
	if(count($location_arr)==1) $selected = key($location_arr); else $selected=0;
	echo create_drop_down( "cbo_location_name", 150, $location_arr,"", 1, "-- Select Location --", $selected, "" );
	exit();
}

if($action=="load_drop_down_embl_type")
{
	$data=explode('_',$data);
	
	if($data[0]==1) $emb_type=$emblishment_print_type;
	else if($data[0]==2) $emb_type=$emblishment_embroy_type;
	else if($data[0]==3) $emb_type=$emblishment_wash_type;
	else if($data[0]==4) $emb_type=$emblishment_spwork_type;
	else if($data[0]==5) $emb_type=$emblishment_gmts_type;
	
	echo create_drop_down( "cboembtype_".$data[1], 80,$emb_type,"", 1, "-- Select --", "", "","","" ); 
	
	exit();
}

if ($action=="load_drop_down_buyer")
{
	$data=explode("_",$data);

	if($data[1]==1) $load_function="fnc_load_party(2,document.getElementById('cbo_within_group').value)";
	else $load_function="";
	
	if($data[1]==1)
	{
		echo create_drop_down( "cbo_party_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $data[2], "$load_function");
	}
	else
	{
		echo create_drop_down( "cbo_party_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data[0]' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Party --", $data[2], "" );
	}	
	exit();	 
} 

if ($action=="load_supplier_dropdown")
{
	$ex_data = explode('_',$data);
	$company=$ex_data[0];
	$item_category=$ex_data[1];
	$supplier=$ex_data[2];
	echo create_drop_down( "cbo_supplier_id", 151,"select DISTINCT(c.supplier_name),c.id from  lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$company' and b.party_type in(96) and c.status_active=1 and c.is_deleted=0 order by c.supplier_name",'id,supplier_name', 1, '-- Select Supplier --',$supplier,'',0);
	exit();
}

if($action=="check_conversion_rate")
{
	$conversion_date=date("Y/m/d");
	$exchange_rate=set_conversion_rate( $data, $conversion_date );
	echo $exchange_rate;
	exit();	
}

if ($action=="work_order_popup")
{
	echo load_html_head_contents("Job Popup Info","../../../", 1, 1, $unicode,'','');
	?>
	<script>
		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		} 
		var selected_id = new Array; var selected_id_dtls = new Array; var selected_id_break = new Array; var selected_qty = new Array; var subconArr = new Array;
		
		function check_all_data(is_checked)
		{
			var tbl_row_count = document.getElementById( 'tbl_po_list' ).rows.length;
			tbl_row_count = tbl_row_count - 1; 
			for( var i = 1; i <= tbl_row_count; i++ ) {
				var woMixCheck=js_set_value( i );
				if(woMixCheck==1) return;
			}
		}
		function js_set_value(str) 
		{  	//alert(str);
			var subcon_job = $('#hidden_subcon_job'+str).val();
			//var dtls_id = $('#hidden_dtls_id'+str).val();
			//var breaks_id = $('#hidden_breaks_id'+str).val();
			//var hidden_qty = $('#hidden_qty'+str).val();
			
			if(subconArr.length==0){
				subconArr.push( subcon_job );
			}

			/*else if( jQuery.inArray( subcon_job, subconArr )==-1 &&  subconArr.length>0){
				alert("Work Order Mixed is Not Allow");return true;
			}*/
			
			toggle( document.getElementById( 'search' + str ), '#FFFFCC' );
			//selected_id.push( subcon_job );
			if( jQuery.inArray( subcon_job, selected_id  ) == -1 ) {
				selected_id.push( subcon_job );
				//selected_id_dtls.push( dtls_id );
				//selected_id_break.push( breaks_id );
				//selected_qty.push( hidden_qty );
			}else{
				subconArr.splice( i, 1 );
				selected_id.splice( i, 1 );
				//selected_id_dtls.splice( i, 1 );
				//selected_id_break.splice( i, 1 );
				//selected_qty.splice( i, 1 );
			}
			var id ='';  var id_dtls = ''; var id_break = ''; var qty = 0; var qnty=0;
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				//id_dtls += selected_id_dtls[i] + '_';
				//id_break += selected_id_break[i] + '_';
				//qty += selected_qty[i]*1;
			}
			//qnty=Math.round(qty);
			//qnty=qty.toFixed(4);
			//id 		= id.substr( 0, id.length - 1 );

			if(id!=""&& id !=null) id= id.substr( 0, id.length - 1 );
			/*if(id_dtls!=""&& id_dtls !=null) id_dtls= id_dtls.substr( 0, id_dtls.length - 1 );
			if(id_break!=""&& id_break !=null) id_break= id_break.substr( 0, id_break.length - 1 );*/
			//if(qty!=""&& qty !=null) qty= qty.substr( 0, qty.length - 1 );
			//alert(id_break);
			$('#all_subcon_job').val( id );	
			/*$('#all_sub_dtls_id').val( id_dtls );
			$('#all_sub_break_id').val( id_break );
			$('#total_order_qty').val( qnty );*/
		}
			
		function fnc_load_party_popup(type,within_group)
		{
			var company = $('#cbo_company_name').val();
			var party_name = $('#cbo_party_name').val();
			var location_name = $('#cbo_location_name').val();
			var within_group = $('#cbo_within_group').val();
			load_drop_down( 'sub_con_wo_entry_controller', company+'_'+within_group, 'load_drop_down_buyer', 'buyer_td' );
		}
		function search_by(val)
		{
			$('#txt_search_string').val('');
			if(val==1 || val==0)
			{
				$('#search_by_td').html('System ID');
			}
			else if(val==2)
			{
				$('#search_by_td').html('W/O No');
			}
			else if(val==4)
			{
				$('#search_by_td').html('Buyer Po');
			}
			else if(val==5)
			{
				$('#search_by_td').html('Buyer Style');
			}
		}
	</script>
</head>
<body>
<div align="center" style="width:100%;" >
    <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
        <table width="940" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
            <thead> 
                <tr>
                    <th colspan="9"><? echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
                </tr>
                <tr>               	 
                    <th width="140" class="must_entry_caption">Company Name</th>
                    <th width="60">Within Group</th>                           
                    <th width="140">Party Name</th>
                    <th width="90">Trims Group</th>
                    <th width="80">Search By</th>
                    <th width="100" id="search_by_td">System ID</th>
                    <th width="170">Date Range</th>                            
                    <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:70px;" /></th>
                </tr>           
            </thead>
            <tbody>
                <tr class="general">
                    <td><input type="hidden" id="selected_job"><? $data=explode("_",$data); ?>  <!--  echo $data;-->
                        <? 
                        echo create_drop_down( "cbo_company_name", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $data[0], "fnc_load_party_popup(1,document.getElementById('cbo_within_group').value);",1); ?>
                    </td>
                    <td>
                        <?php echo create_drop_down( "cbo_within_group", 60, $yes_no,"", 0, "--  --", $data[3], "fnc_load_party_popup(1,this.value);" ); ?>
                    </td>
                    <td id="buyer_td">
                        <? 
						echo create_drop_down( "cbo_party_name", 150, "","", 1, "-- Select Party --", $data[2], "fnc_load_party_popup(1,this.value);" );   	  	 
                        ?>
                    </td>
                    <td><? echo create_drop_down( "cbo_item_group", 90, "select id, item_name from lib_item_group where item_category=4 and status_active=1","id,item_name", 1, "-- Select --",$selected, "",0,'','','','','','',"cboItemGroup[]"); ?>	</td>
                    <td>
						<?
                            $search_by_arr=array(1=>"System ID",2=>"W/O No",4=>"Buyer Po",5=>"Buyer Style");
                            echo create_drop_down( "cbo_type",80, $search_by_arr,"",0, "",1,'search_by(this.value)',0 );
                        ?>
                    </td>
                    <td align="center">
                        <input type="text" name="txt_search_string" id="txt_search_string" class="text_boxes" style="width:100px" placeholder="" />
                    </td>
                    
                    <td align="center">
                        <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px">
                        <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px">
                    </td>
                    <td align="center">
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_party_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_type').value+'_'+document.getElementById('txt_search_string').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('cbo_within_group').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('cbo_item_group').value, 'create_order_search_list_view', 'search_div', 'sub_con_wo_entry_controller', 'setFilterGrid(\'tbl_po_list\',-1)')" style="width:70px;" /></td>
                    </tr>
                    <tr>
                    	<td align="center"><? echo create_drop_down( "cbo_year_selection", 60, $year,"", 1, "-- Select --", date('Y'), "",0 ); ?></td>
                        <td colspan=6 align="center" valign="middle">
                            <? echo load_month_buttons();  ?>
                            <input type="hidden" name="hidden_mst_id" id="hidden_mst_id" class="datepicker" style="width:70px">
                        </td>
                        <td align="center">&nbsp;</td>
                    </tr>
                    <tr>
                        <td colspan="9" align="center" valign="top" id=""><div id="search_div"></div></td>
                    </tr>
                </tbody>
            </table>
            </form>
        </div>
    </body>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
    exit();
}
if($action=="create_order_search_list_view")
{	
	$data=explode('_',$data);
	$party_id=str_replace("'","",$data[1]);
	$search_by=str_replace("'","",$data[4]);
	$search_str=trim(str_replace("'","",$data[5]));
	$search_type =$data[6];
	$within_group =$data[7];
	//$section_id =$data[9];
	$trim_group_id =$data[9];
	//$src_for_order =$data[11];
	if($db_type==0) { $year_cond=" and YEAR(a.insert_date)=$data[8]";   }
	if($db_type==2) {$year_cond=" and to_char(a.insert_date,'YYYY')=$data[8]";}

	if($data[0]!=0) $company=" and a.company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
	//if($section_id!='') $section=" and b.section=$section_id"; else { $section=''; }
	if($trim_group_id!=''  && $trim_group_id!=0) $trim_group_cond=" and b.item_group=$trim_group_id"; else { $trim_group_cond=''; }
	//echo $search_type."==".$search_str."==".$trim_group_id; die;
	$job_cond=""; $style_cond=""; $po_cond=""; $search_com_cond="";
	if($search_type==1)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.job_no_prefix_num='$search_str'";
			else if($search_by==2) $search_com_cond="and a.order_no='$search_str'";
			else if ($search_by==4) $search_com_cond=" and b.buyer_po_no = '$search_str' ";
			else if ($search_by==5) $search_com_cond=" and b.buyer_style_ref = '$search_str' ";
		}
	}
	else if($search_type==2)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.job_no_prefix_num like '$search_str%'";  
			else if($search_by==2) $search_com_cond="and a.order_no like '$search_str%'";  
			else if ($search_by==4) $search_com_cond=" and b.buyer_po_no like '$search_str%'";
			else if ($search_by==5) $search_com_cond=" and b.buyer_style_ref like '$search_str%'";  
		}
	}
	else if($search_type==3)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.job_no_prefix_num like '%$search_str'";  
			else if($search_by==2) $search_com_cond="and a.order_no like '%$search_str'";  
			else if ($search_by==4) $search_com_cond=" and b.buyer_po_no like '%$search_str'";
			else if ($search_by==5) $search_com_cond=" and b.buyer_style_ref like '%$search_str'";  
		}
	}
	else if($search_type==4 || $search_type==0)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.job_no_prefix_num like '%$search_str%'";  
			else if($search_by==2) $search_com_cond="and a.order_no like '%$search_str%'";  
			else if ($search_by==4) $search_com_cond=" and b.buyer_po_no like '%$search_str%'"; 
			else if ($search_by==5) $search_com_cond=" and b.buyer_style_ref like '%$search_str%'";   
		}
	}	

	if($party_id!=0) $party_id_cond=" and a.party_id='$party_id'"; else $party_id_cond="";
	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	if($db_type==0)
	{ 
		if ($data[2]!="" &&  $data[3]!="") $order_rcv_date = "and a.receive_date between '".change_date_format($data[2],'yyyy-mm-dd')."' and '".change_date_format($data[3],'yyyy-mm-dd')."'"; else $order_rcv_date ="";
		//$buyerBuyer_concat=" , group_concat(b.buyer_buyer) as buyer_buyer";
	}
	else
	{
		if ($data[2]!="" &&  $data[3]!="") $order_rcv_date = "and a.receive_date between '".change_date_format($data[2], "", "",1)."' and '".change_date_format($data[3], "", "",1)."'"; else $order_rcv_date ="";
		//$buyerBuyer_concat=" , listagg(CAST(b.buyer_buyer as VARCHAR(4000)),',') within group (order by b.id) as buyer_buyer";
	}
	if ($within_group!=0) $withinGroup=" and a.within_group='$within_group'"; else $withinGroup="";
	if($within_group==1)
	{
		$party_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
		$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	}
	else
	{
		
		$party_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	}

	if($db_type==0) $dtls_id_cond="group_concat(b.id) as dtls_id";
	else if($db_type==2) $dtls_id_cond="rtrim(xmlagg(xmlelement(e,b.id,',').extract('//text()') order by b.id).GetClobVal(),',') as dtls_id";
	if($db_type==0) $breaks_id_cond="group_concat(b.id) as breaks_id";
	else if($db_type==2) $breaks_id_cond="rtrim(xmlagg(xmlelement(e,c.id,',').extract('//text()') order by c.id).GetClobVal(),',') as breaks_id";
	if($db_type==0) $buyerBuyer_concat="group_concat(b.buyer_buyer) as buyer_buyer";
	else if($db_type==2) $buyerBuyer_concat="rtrim(xmlagg(xmlelement(e,b.buyer_buyer,',').extract('//text()') order by b.id).GetClobVal(),',') as buyer_buyer";

	/*if($src_for_order==2){
		$search_com_cond.="and b.source_for_order=2";
	} else {
		$search_com_cond.="and b.source_for_order in(0,1)";
	}*/

	$search_com_cond.="and b.source_for_order=2";


	$sql= "select a.id, a.subcon_job, a.job_no_prefix_num, a.company_id, a.location_id, a.party_id, a.receive_date, a.delivery_date ,a.order_no
	from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c  
	where a.entry_form=255 and a.subcon_job=b.job_no_mst and b.job_no_mst=c.job_no_mst and a.id=b.mst_id and b.id=c.mst_id  and b.order_quantity<>0 and c.qnty<>0  and a.status_active=1 and b.status_active=1 and c.status_active=1 $order_rcv_date $company $party_id_cond $withinGroup $search_com_cond $section $trim_group_cond
	group by a.id, a.subcon_job, a.job_no_prefix_num, a.company_id, a.location_id, a.party_id, a.receive_date, a.delivery_date, a.order_no
	order by a.id DESC";

	$data_array=sql_select($sql);
	?>
     <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="685" >
        <thead>
            <th width="30">SL</th>
            <th width="150">Receive No</th>
            <th width="150">W/O No</th>
            <th width="80">Ord Receive Date</th>
            <th>Delivery Date</th>
        </thead>
        </table>
        <div style="width:685px; max-height:260px;overflow-y:scroll;" >	 
        	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="665" class="rpt_table" id="tbl_po_list">
	        	<tbody>
	            <? 
	            //print_r($data_array);
	            $i=1;
	            $group_arr=return_library_array( "select id, item_name from lib_item_group where status_active=1 and item_category=4", "id", "item_name");
	            foreach($data_array as $row)
	            {
	            	//echo $row;
	            	if ($i%2==0)  
	            		$bgcolor="#E9F3FF";
	            	else
	            		$bgcolor="#FFFFFF";	
	            	?>	
	            	<tr bgcolor="<? echo $bgcolor; ?>" id="search<? echo $i; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value(<? echo $i;?>)" > 
	            		<td width="30"><? echo $i; ?></td>
	            		<td width="150" align="center"><? echo $row[csf('subcon_job')]; ?></td>
	            		<td width="150"><? echo $row[csf('order_no')]; ?></td>
	            		<td width="80" style="text-align:center;"><? echo change_date_format($row[csf('receive_date')]); ?></td>
	            		<td style="text-align:center;"><? echo change_date_format($row[csf('delivery_date')]); ?>
	            		<input  class="text_boxes" type="hidden" name="hidden_subcon_job<? echo $i; ?>" id="hidden_subcon_job<? echo $i; ?>" value="<? echo $row[csf('id')]; ?>" style="width:70px">
	            		<input class="text_boxes" type="hidden"  name="hidden_dtls_id<? echo $i; ?>" id="hidden_dtls_id<? echo $i; ?>" value="<? echo $dtls_ids;//$row[csf('dtls_id')]; ?>" style="width:70px">
	            		<input class="text_boxes" type="hidden" name="hidden_breaks_id<? echo $i; ?>" id="hidden_breaks_id<? echo $i; ?>" value="<? echo $breaks_ids; //$row[csf('breaks_id')]; ?>" style="width:70px">
	            		<input class="text_boxes" type="hidden" name="hidden_qty<? echo $i; ?>" id="hidden_qty<? echo $i; ?>" value="<? echo $row[csf('qnty')]; ?>" style="width:70px">	
	            		</td>
		            </tr>
		            <? 
		            $i++; 
	            	/*}*/
	            } 
	            ?>
	        </tbody>
	    </table>
	</div>
    <table style="width:680px;" align="center">
        <tr>
            <td align="center" height="30" valign="bottom">
                <div style="width:100%">
                    <div style="width:45%; float:left" align="left">
                        <input type="checkbox" name="check_all" id="check_all" onClick="check_all_data(this.checked)" /> Check / Uncheck All
                    </div>
                    <div style="width:53%; float:left" align="left">
                        <input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px" />
                        <input type="hidden"  id="all_subcon_job" />
				    	<input type="hidden"  id="all_sub_dtls_id" />
				    	<input type="hidden"  id="all_sub_break_id" />
				    	<input type="hidden"  id="total_order_qty" />
                    </div>
                </div>
            </td>
        </tr>
    </table>
    
	` 
	<?    
	exit();
}

if($action=="return_subcon_jobs")
{
	$name_cond="listagg(subcon_job,',') within group (order by subcon_job)";
	$subcon_jobs = return_field_value("$name_cond as subcon_job", "subcon_ord_mst ", "status_active =1 and id in ($data)", "subcon_job");
	//$data=explode("_",$data);
	//$uom=return_field_value( "uom_id","lib_booked_uom_setup","company_id=$data[0] and section_id=$data[1] and sub_section_id=$data[2]");
	echo $subcon_jobs;
	exit();	
}


if( $action=='order_dtls_list_view' ) 
{
	$data=explode('_',$data);
	$buttonStatus=$data[0];
	$mst_id=$data[1];
	$currency_id=$data[2];
	$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name" );
	$size_arr=return_library_array( "select id,size_name from lib_size",'id','size_name');
	$tblRow=0;
	
	$sub_wo_result=sql_select( "select a.id, a.mst_id, a.job_no_mst, a.description, a.color_id, a.size_id, a.qnty, a.rate, a.amount, a.order_rcv_break_id, a.order_rcv_id ,b.id as dtls_id ,b.order_quantity as sub_wo_qnty,  b.order_uom, b.rate, b.amount, b.section, b.sub_section, b.item_group  as trim_group , b.order_rcv_dtls_id, b.order_rcv_id,b.remarks ,c.job_no_mst ,b.order_quantity as wo_qnty from trims_subcon_ord_breakdown a , trims_subcon_ord_dtls b, subcon_ord_dtls c where  c.mst_id in ($mst_id) and c.source_for_order=2 and a.job_no_mst=b.job_no_mst and b.id=a.mst_id and c.mst_id=a.order_rcv_id and b.section=c.section and b.sub_section=c.sub_section and b.item_group=c.item_group and b.order_uom=c.order_uom and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.id,a.mst_id,a.job_no_mst,a.description,a.color_id,a.size_id,a.qnty,a.rate,a.amount,a.order_rcv_break_id,a.order_rcv_id,b.id ,b.order_quantity ,b.order_uom,b.rate,b.amount,b.section,b.sub_section,b.item_group ,b.order_rcv_dtls_id,b.order_rcv_id,b.remarks,c.job_no_mst,b.order_quantity order by b.id");	
	$sub_wo_arr=array(); 
	foreach ($sub_wo_result as $rows)
	{
		$sub_wo_arr[$rows[csf("order_rcv_id")]][$rows[csf("job_no_mst")]][$rows[csf("section")]][$rows[csf("sub_section")]][$rows[csf("trim_group")]][$rows[csf("order_uom")]]["sub_wo_qnty"] +=$rows[csf("qnty")];

		$sub_brk_wo_arr[$rows[csf("job_no_mst")]][$rows[csf("section")]][$rows[csf("sub_section")]][$rows[csf("trim_group")]][$rows[csf("order_uom")]][trim($rows[csf("description")])][$rows[csf("color_id")]][$rows[csf("size_id")]][$rows[csf("order_rcv_break_id")]]["sub_brk_wo_qnty"] +=$rows[csf("qnty")];
	}
	//echo "<pre>";
	//print_r($sub_wo_arr);
	$search_com_cond.="and b.source_for_order=2";
	
	$qry_result=sql_select( "select a.id, a.mst_id, a.order_id, a.job_no_mst, a.book_con_dtls_id, a.description, a.color_id, a.size_id, a.qnty, a.rate, a.amount,a.is_revised,a.style,b.order_uom, b.section, b.sub_section, b.item_group from subcon_ord_breakdown a , subcon_ord_dtls b where a.job_no_mst=b.job_no_mst and b.id=a.mst_id and b.mst_id in ($mst_id) $search_com_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by a.id");	
	$data_dreak_arr=array(); $data_dreak=''; $add_comma=0; $k=1; $attachedBrkQty=0; $balanceBrkQty=0;
	foreach ($qry_result as $rows)
	{
		if(trim($rows[csf("description")])=="") $rows[csf('description')]=0;
		if($rows[csf('color_id')]=="") $rows[csf('color_id')]=0;
		if($rows[csf('size_id')]=="") $rows[csf('size_id')]=0;
		if($rows[csf('qnty')]=="") $rows[csf('qnty')]=0;
		if($rows[csf('rate')]=="") $rows[csf('rate')]=0;
		if($rows[csf('amount')]=="") $rows[csf('amount')]=0;
		if($rows[csf('book_con_dtls_id')]=="") $rows[csf('book_con_dtls_id')]=0;
		if(!in_array($rows[csf('mst_id')],$temp_arr_mst_id))
		{
			$temp_arr_mst_id[]=$rows[csf('mst_id')];
			$add_comma=0; $data_dreak='';
		}
		$k++;
		if($bookConsIds_arr[$rows[csf('book_con_dtls_id')]]=="") $bookConsIds_arr[$rows[csf('book_con_dtls_id')]]=0;
		//echo $rows[csf("job_no_mst")].'=='.$rows[csf("mst_id")].'=='.$rows[csf("section")].'=='.$rows[csf("sub_section")].'=='.$rows[csf("item_group")].'=='.$rows[csf("order_uom")].'=='.trim($rows[csf("description")]).'=='.$rows[csf("color_id")].'=='.$rows[csf("size_id")].'++';
		
		$attachedBrkQty=$sub_brk_wo_arr[$rows[csf("job_no_mst")]][$rows[csf("section")]][$rows[csf("sub_section")]][$rows[csf("item_group")]][$rows[csf("order_uom")]][trim($rows[csf("description")])][$rows[csf("color_id")]][$rows[csf("size_id")]][$rows[csf("id")]]["sub_brk_wo_qnty"];
		if($attachedBrkQty=='') $attachedBrkQty=0;
		$balanceBrkQty=$rows[csf('qnty')]-$attachedBrkQty;
		
		//echo $rows[csf('qnty')].'=='.$attachedBrkQty.'=='.$balanceBrkQty.'++';
		
		if($balanceBrkQty>0){
			$data_dreak_arr[$rows[csf('job_no_mst')]][$rows[csf('section')]][$rows[csf('sub_section')]][$rows[csf('item_group')]][$rows[csf('order_uom')]]['info'].= trim($rows[csf("description")]).'_'.$color_library[$rows[csf('color_id')]].'_'.$size_arr[$rows[csf('size_id')]].'_'.$balanceBrkQty.'_0_0_'.$rows[csf('book_con_dtls_id')].'_'.$rows[csf('id')].'_'.$rows[csf('is_revised')].'_'.$rows[csf('style')].'***';
			$data_dreak_arr[$rows[csf('job_no_mst')]][$rows[csf('section')]][$rows[csf('sub_section')]][$rows[csf('item_group')]][$rows[csf('order_uom')]]['break_ids'].=$rows[csf('id')].','; 
		}
	}
	
	//echo "<pre>";
	//int_r($bookConsIds_arr);die;
	//print_r($data_dreak_arr);

	$sql = "SELECT id, mst_id, job_no_mst, order_id, order_no, buyer_po_id as po_break_down_id, booking_dtls_id, order_quantity as wo_qnty, order_uom, rate, amount, delivery_date, buyer_po_no, buyer_style_ref, buyer_buyer, section, sub_section, item_group as trim_group, rate_domestic,  amount_domestic, is_with_order, booked_uom, booked_conv_fac, booked_qty, is_revised,source_for_order from subcon_ord_dtls where mst_id in ($mst_id) and source_for_order=2 and status_active=1 and is_deleted=0 order by id ASC";

	$data_array=sql_select($sql);

	$wo_arr=array(); $job_no_msts=''; $ids=''; $book_con_dtls_ids='';
	foreach ($data_array as  $row) 
	{
		//echo $mstIDS.'=='.$ids.'=='.$book_con_dtls_ids.'++'; 
		$wo_arr[$row[csf("mst_id")]][$row[csf("job_no_mst")]][$row[csf("section")]][$row[csf("sub_section")]][$row[csf("trim_group")]][$row[csf("order_uom")]]["qty"] +=$row[csf("wo_qnty")];
		$wo_arr[$row[csf("mst_id")]][$row[csf("job_no_mst")]][$row[csf("section")]][$row[csf("sub_section")]][$row[csf("trim_group")]][$row[csf("order_uom")]]["amount"] +=$row[csf("amount")];
		$wo_arr[$row[csf("mst_id")]][$row[csf("job_no_mst")]][$row[csf("section")]][$row[csf("sub_section")]][$row[csf("trim_group")]][$row[csf("order_uom")]]["wo_dtls_id"] .=$row[csf("id")].',';
		$job_no_msts .= $row[csf("job_no_mst")].',';
	}


	
	//echo "<pre>";
	//print_r($sub_wo_arr);

	$tblRow=1; $i=1;
	foreach($wo_arr as $mst_id=> $mst_id_data)
	{
		
		foreach($mst_id_data as $job_no_mst=> $job_no_mst_data)
		{ 
			?>
			<thead>
				<tr bgcolor="<? echo $bgcolor; ?>" id="row_<? echo $tblRow; ?>" align="center">
					<th width="150" style="color: blue;" >Order No :</th>
					<th colspan="2" align="left"><? echo $job_no_mst;?><input type="hidden" name="hdnWoId" id="hdnWoId_<? echo $i; ?>"></th>
					<th colspan="5">&nbsp;</th>
					<th style="display: none;" style="color: blue;" name="rateTd[]" id="rateTd"></th>
				</tr>
				<tr>
            		<th width="150" style="color: blue;">Section</th>
                    <th width="150" >Sub Section</th>
                    <th width="150" style="color: blue;">Trims Group</th>
                    <th width="100" style="color: blue;">Order UOM</th>
                    <th width="110" style="color: blue;">Order Qty</th>
                    <th width="110" style="color: blue;" class="rateTd" >Rate (<? echo $currency[$currency_id] ?>)</th>
                    <th width="110" style="color: blue;">Amount</th>
                    <th width="200">Remarks</th>
            	</tr>
			</thead>
			<tbody>
			<?
			foreach($job_no_mst_data as $section_id=> $section_data)
			{
				foreach($section_data as $sub_section_id=> $sub_section_data)
				{ 
					foreach($sub_section_data as $trim_group_id=> $trim_group_data)
					{
						//$mst_ids='';
						$attached_Qty=0; $balance_Qty=0;
						foreach($trim_group_data as $order_uom_id => $row)
						{
							//$dataBreakDtls=$data_dreak_arr[$job_no_mst][$section_id][$sub_section_id][$trim_group_id][$order_uom_id];
							//echo $job_no_mst.'='.$section_id.'='.$sub_section_id.'='.$trim_group_id.'='.$order_uom_id.'++';
							$attached_Qty=$sub_wo_arr[$mst_id][$job_no_mst][$section_id][$sub_section_id][$trim_group_id][$order_uom_id]['sub_wo_qnty'];
							//echo $row['qty'].'=='.$attached_Qty.'+';
							$balance_Qty=$row['qty']-$attached_Qty;
							if($balance_Qty>0){
								$dataBreakDtls=$data_dreak_arr[$job_no_mst][$section_id][$sub_section_id][$trim_group_id][$order_uom_id]['info'];
								$dataBreakIdss=$data_dreak_arr[$job_no_mst][$section_id][$sub_section_id][$trim_group_id][$order_uom_id]['break_ids'];
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" id="row_<? echo $tblRow; ?>" align="center">
									<td><?
										
										if($section_id==1) $subID='1,2,3';
										else if($section_id==3) $subID='4,5,18';
										else if($section_id==5) $subID='6,7,8,9,10,11,12,13';
										else if($section_id==10) $subID='14,15';
										else if($section_id==7) $subID='19,20';
										else $subID='0';
										echo create_drop_down( "cboSection_".$tblRow, 150, $trims_section,"", 1, "-- Select Section --",$section_id,"load_sub_section($tblRow)",1,'','','','','','',"cboSection[]"); ?></td>
									<td id="subSectionTd_<? echo $tblRow; ?>"><? echo create_drop_down( "cboSubSection_".$tblRow, 150, $trims_sub_section,"", 1, "-- Select Section --",$sub_section_id,"load_sub_section_value($tblRow)",1,$subID,'','','','','',"cboSubSection[]"); ?></td>			
									<td><? echo create_drop_down( "cboItemGroup_".$tblRow, 150, "select id, item_name from lib_item_group where item_category=4 and status_active=1","id,item_name", 1, "-- Select --",$trim_group_id, "",1,'','','','','','',"cboItemGroup[]"); ?></td>
									<td><? echo create_drop_down( "cboUom_".$tblRow, 100, $unit_of_measurement,"", 1, "-- Select --",$order_uom_id,"", 1,'','','','','','',"cboUom[]"); ?>	</td>
									<td><input id="txtOrderQuantity_<? echo $tblRow; ?>" name="txtOrderQuantity[]" value="<? echo number_format($balance_Qty,4,'.',''); ?>" class="text_boxes_numeric" type="text"  style="width:97px" onClick="openmypage_order_qnty(<? echo $tblRow; ?>,<? echo $i; ?>)"  placeholder="Click To Search" readonly /></td>
									<td><input id="txtRate_<? echo $tblRow; ?>" name="txtRate[]" value="<? echo 0; ?>" type="text"  class="text_boxes_numeric" style="width:97px"/></td>
									<td><input id="txtAmount_<? echo $tblRow; ?>" name="txtAmount[]"  value="<? echo 0; ?>" type="text" style="width:97px"  class="text_boxes_numeric"  disabled />
									<td><input id="txtRemarks_<? echo $tblRow; ?>" name="txtRemarks[]"  value="" type="text" style="width:187px"  class="text_boxes"  />
										<input id="hdnDtlsUpdateId_<? echo $tblRow; ?>" name="hdnDtlsUpdateId[]" type="hidden" value="" class="text_boxes_numeric" style="width:40px" />
										<input type="hidden" id="hdnWoDtlsIds_<? echo $tblRow; ?>" name="hdnWoDtlsIds[]" value="<?=$row['wo_dtls_id']; ?>">
										<input type="hidden" id="hdnBreakDtlsDatas_<? echo $tblRow; ?>" name="hdnBreakDtlsDatas[]" value="<?=$dataBreakDtls; ?>">
										<input type="hidden" id="hdnRcvId_<? echo $tblRow; ?>" name="hdnRcvId[]" value="<?=$mst_id; ?>">
										<input type="hidden" id="hdnBalance_<? echo $tblRow; ?>" name="hdnBalance[]" value="<?= number_format($balance_Qty,4,'.',''); ?>">
										<input type="hidden" id="hdnRcvBrkIds_<? echo $tblRow; ?>" name="hdnRcvBrkIds[]" value="<?= chop($dataBreakIdss,','); ?>">
						                
						            </td>
								</tr>
								<?
								$tblRow++;
							}
						}
					}
				}

			}
			$i++;
			?></tbody><?
		}
	}
	
	//echo $sql; //die; 
	//print_r($data_array);
	$ind=0;
	$min_dates=$del_date_arr[0];
	foreach($del_date_arr as $v) 
	{
		if(strtotime($min_dates)>strtotime($v))$min_dates=$v;
	}
	?><input type="hidden" id="min_date_id" name="min_date_id" value="<? echo change_date_format($min_dates);?>"><?
	exit();
}


if ($action=="wo_popup")
{
	echo load_html_head_contents("WO Popup Info","../../../", 1, 1, $unicode,'','');
	?>
	<script>
		function js_set_value(id)
		{ 
			$("#hidden_mst_id").val(id);
			document.getElementById('selected_job').value=id;
			parent.emailwindow.hide();
		}
		
		function fnc_load_party_popup(type,within_group)
		{
			var company = $('#cbo_company_name').val();
			var party_name = $('#cbo_party_name').val();
			var location_name = $('#cbo_location_name').val();
			load_drop_down( 'trims_order_receive_controller', company+'_'+within_group, 'load_drop_down_buyer', 'buyer_td' );
		}
		function search_by(val)
		{
			$('#txt_search_string').val('');
			if(val==1 || val==0)
			{
				$('#search_by_td').html('System ID');
			}
			else if(val==2)
			{
				$('#search_by_td').html('W/O No');
			}
			else if(val==3)
			{
				$('#search_by_td').html('Buyer Job');
			}
			else if(val==4)
			{
				$('#search_by_td').html('Buyer Po');
			}
			else if(val==5)
			{
				$('#search_by_td').html('Buyer Style');
			}
		}
	</script>
</head>
<body>
<div align="center" style="width:100%;" >
    <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
        <table width="700" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
            <thead> 
                <tr>
                    <th colspan="8" style="display: none;"><? echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
                </tr>
                <tr>               	 
                    <th width="140" class="must_entry_caption">Company Name</th>
                    <th width="100" id="search_by_td">System ID</th>
                    <th width="100">Year</th>
                    <th width="170">Date Range</th>                            
                    <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:70px;" /></th>
                </tr>           
            </thead>
            <tbody>
                <tr class="general">
                    <td><input type="hidden" id="selected_job"><? $data=explode("_",$data); ?>  <!--  echo $data;-->
                        <? 
                        echo create_drop_down( "cbo_company_name", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $data[0], "fnc_load_party_popup(1,document.getElementById('cbo_within_group').value);",1); ?>
                    </td>
                    <td style="display: none;">
                        <?php echo create_drop_down( "cbo_within_group", 100, $yes_no,"", 0, "--  --", $data[3], "fnc_load_party_popup(1,this.value);" ); ?>
                        <? echo create_drop_down( "cbo_party_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data[0]' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Party --", $data[2], "" );   	 
                        ?>
                        <?
                            $search_by_arr=array(1=>"System ID",2=>"W/O No",3=>"Buyer Job",4=>"Buyer Po",5=>"Buyer Style");
                            echo create_drop_down( "cbo_type",100, $search_by_arr,"",0, "",1,'search_by(this.value)',0 );
                        ?>
                    </td>
                    
                    <td align="center">
                        <input type="text" name="txt_search_string" id="txt_search_string" class="text_boxes" style="width:100px" placeholder="" />
                    </td>
                    <td align="center"><? echo create_drop_down( "cbo_year_selection", 100, $year,"", 1, "-- Select --", date('Y'), "",0 ); ?></td>
                    <td align="center">
                        <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px">
                        <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px">
                    </td>
                    <td align="center">
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_party_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_type').value+'_'+document.getElementById('txt_search_string').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('cbo_within_group').value+'_'+document.getElementById('cbo_year_selection').value, 'create_wo_search_list_view', 'search_div', 'sub_con_wo_entry_controller', 'setFilterGrid(\'tbl_po_list\',-1)')" style="width:70px;" /></td>
                    </tr>
                    <tr>
                        <td colspan="6" align="center" valign="middle">
                            <? echo load_month_buttons();  ?>
                            <input type="hidden" name="hidden_mst_id" id="hidden_mst_id" class="datepicker" style="width:70px">
                        </td>
                    </tr>
                    <tr>
                        <td colspan="6" align="center" valign="top" id=""><div id="search_div"></div></td>
                    </tr>
                </tbody>
            </table>    
            </form>
        </div>
    </body>           
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
    exit();
}
	
if($action=="create_wo_search_list_view")
{	
	$data=explode('_',$data);
	$party_id=str_replace("'","",$data[1]);
	$search_by=str_replace("'","",$data[4]);
	$search_str=trim(str_replace("'","",$data[5]));
	$search_type =$data[6];
	$within_group =$data[7];
	if($db_type==0) { $year_cond=" and YEAR(a.insert_date)=$data[8]";   }
	if($db_type==2) {$year_cond=" and to_char(a.insert_date,'YYYY')=$data[8]";}

	if($data[0]!=0) $company=" and a.company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
	if($search_str!="") $search_com_cond="and a.job_no_prefix_num='$search_str'";
	

	if($db_type==0)
	{ 
		if ($data[2]!="" &&  $data[3]!="") $wo_date = "and a.wo_date between '".change_date_format($data[2],'yyyy-mm-dd')."' and '".change_date_format($data[3],'yyyy-mm-dd')."'"; else $wo_date ="";
		$ins_year_cond="year(a.insert_date)";
	}
	else
	{
		if ($data[2]!="" &&  $data[3]!="") $wo_date = "and a.wo_date between '".change_date_format($data[2], "", "",1)."' and '".change_date_format($data[3], "", "",1)."'"; else $wo_date ="";
		$ins_year_cond="TO_CHAR(a.insert_date,'YYYY')";
	}

	$sql= "select a.id, a.subcon_job, a.job_no_prefix_num, $ins_year_cond as year, a.company_id, a.location_id, a.wo_date, a.delivery_date from trims_subcon_ord_mst a where a.entry_form=450 and a.status_active=1 and a.status_active=1 $wo_date $company $search_com_cond order by a.id DESC";
	//echo $sql;
	$data_array=sql_select($sql);
	//echo "<pre>";
	//print_r($data_array);
	?>
    <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="605" >
        <thead>
            <th width="30">SL</th>
            <th width="150">WO No</th>
            <th width="80">WO Date</th>
            <th width="80">Delivery Date</th>
            <th>Year</th>
            
        </thead>
        </table>
        <div style="width:625px; max-height:270px;overflow-y:scroll;" >	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="605" class="rpt_table" id="tbl_po_list">
        <tbody>
            <? 
            $i=1; 
            foreach($data_array as $row)
            {  
            	$color_ids =$buyer_po_ids =$buyer_po_nos =$buyer_styles ='';
                if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                /*$color_ids = $row[csf('color_id')]->load();
               
                if($within_group!=1)
				{
					$buyer_po_nos = $row[csf('buyer_po_no')]->load();
                	$buyer_styles = $row[csf('buyer_style')]->load();
				}
				else
				{
					$buyer_po_ids = $row[csf('buyer_po_id')]->load();
				}

				$excolor_id=array_unique(explode(",",$color_ids));
				$color_name="";	
				foreach ($excolor_id as $color_id)
				{
					if($color_name=="") $color_name=$color_arr[$color_id]; else $color_name.=','.$color_arr[$color_id];
				}
				if($within_group==1)
				{
					$buyer_po=""; $buyer_style="";
					$buyer_po_id=explode(",",$buyer_po_ids);
					foreach($buyer_po_id as $po_id)
					{
						if($buyer_po=="") $buyer_po=$buyer_po_arr[$po_id]['po']; else $buyer_po.=','.$buyer_po_arr[$po_id]['po'];
						if($buyer_style=="") $buyer_style=$buyer_po_arr[$po_id]['style']; else $buyer_style.=','.$buyer_po_arr[$po_id]['style'];
					}
					$buyer_po=implode(",",array_unique(explode(",",$buyer_po)));
					$buyer_style=implode(",",array_unique(explode(",",$buyer_style)));
				}
				else
				{
					$buyer_po=implode(",",array_unique(explode(",",$buyer_po_nos)));
					$buyer_style=implode(",",array_unique(explode(",",$buyer_styles)));
				}*/
                ?>
                <tr bgcolor="<? echo $bgcolor; ?>" onClick='js_set_value("<? echo $row[csf('id')].'_'.$row[csf('subcon_job')]; ?>")' style="cursor:pointer" >
                    <td width="30"><? echo $i; ?></td>
                    <td width="150"><? echo $row[csf('subcon_job')]; ?></td>
                    <td width="80" style="text-align:center;"><? echo change_date_format($row[csf('wo_date')]); ?></td>
                    <td width="80" style="text-align:center;"><? echo change_date_format($row[csf('delivery_date')]); ?></td>
                    <td style="text-align:center;"><? echo $row[csf('year')]; ?></td>
                </tr>
				<? 
                $i++; 
            } 
            ?>
        </tbody>
    </table>
	<?    
	exit();
}
 
if ($action=="load_php_data_to_form")
{
	$nameArray=sql_select( "select id, entry_form, subcon_job, job_no_prefix, job_no_prefix_num, company_id, location_id, order_rcv_id,order_rcv_no, supplier_id, currency_id, paymode_id, source_id, exchange_rate, wo_date, delivery_date, attention,remarks from trims_subcon_ord_mst where id='$data' and entry_form=450 and status_active=1 and is_deleted=0" );
	foreach ($nameArray as $row)
	{	
		echo "document.getElementById('txt_wo_no').value 				= '".$row[csf("subcon_job")]."';\n";  
		echo "document.getElementById('cbo_company_name').value 		= '".$row[csf("company_id")]."';\n";
		echo "$('#cbo_company_name').attr('disabled','true')".";\n";
		echo "load_drop_down( 'requires/sub_con_wo_entry_controller', document.getElementById('cbo_company_name').value, 'load_drop_down_location', 'location_td' );\n";	
		echo "load_drop_down( 'requires/sub_con_wo_entry_controller', document.getElementById('cbo_company_name').value, 'load_supplier_dropdown', 'supplier_td' );\n";	
		echo "document.getElementById('txt_ord_rec_id').value 			= '".$row[csf("order_rcv_id")]."';\n";
		echo "document.getElementById('txt_ord_rec_no').value			= '".$row[csf("order_rcv_no")]."';\n";
		echo "document.getElementById('txt_prev_ord_rec_id').value		= '".$row[csf("order_rcv_id")]."';\n";
		echo "document.getElementById('cbo_supplier_id').value			= '".$row[csf("supplier_id")]."';\n";

		echo "document.getElementById('cbo_location_name').value 		= '".$row[csf("location_id")]."';\n";
		echo "document.getElementById('cbo_currency').value				= '".$row[csf("currency_id")]."';\n";
		
		echo "document.getElementById('cbo_paymode_id').value			= '".$row[csf("paymode_id")]."';\n";
		echo "document.getElementById('cbo_source_id').value			= '".$row[csf("source_id")]."';\n";
		echo "document.getElementById('txt_exchange_rate').value        = '".$row[csf("exchange_rate")]."';\n";
		echo "document.getElementById('txt_attention').value			= '".$row[csf("attention")]."';\n";
		echo "document.getElementById('txt_remarks').value				= '".$row[csf("remarks")]."';\n";
			
		echo "document.getElementById('txt_wo_date').value				= '".change_date_format($row[csf("wo_date")])."';\n"; 
		echo "document.getElementById('txt_delivery_date').value		= '".change_date_format($row[csf("delivery_date")])."';\n"; 
		echo "document.getElementById('update_id').value          		= '".$row[csf("id")]."';\n";
		echo "$('#cbo_currency').attr('disabled','true')".";\n";
		echo "$('#txt_ord_rec_no').attr('disabled','true')".";\n";
			
		echo "set_button_status(1,'".$_SESSION['page_permission']."', 'fnc_job_order_entry',1);\n";	
	}
	exit();	
}


if( $action=='order_dtls_list_view_update' ) 
{
	//echo $data; die;2_FAL-TOR-19-00092_1_3895
	$data=explode('_',$data);
	$buttonStatus=$data[0];
	$mst_id=$data[1];
	$job_no=$data[2];
	$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name" );
	$size_arr=return_library_array( "select id,size_name from lib_size",'id','size_name');
	$order_receive_arr=return_library_array( "select id,subcon_job from subcon_ord_mst",'id','subcon_job');
	$tblRow=0;
	
	//echo  "select a.id, a.mst_id, a.job_no_mst, a.description, a.color_id, a.size_id, a.qnty, a.rate, a.amount, a.order_rcv_break_id, a.order_rcv_id ,b.id as dtls_id ,b.order_quantity as sub_wo_qnty, sum(c.order_quantity) as wo_qnty, b.order_uom, b.rate, b.amount, b.section, b.sub_section, b.item_group  as trim_group , b.order_rcv_dtls_id, b.order_rcv_id,b.remarks  from trims_subcon_ord_breakdown a , trims_subcon_ord_dtls b, subcon_ord_dtls c where a.job_no_mst=b.job_no_mst and b.id=a.mst_id and b.mst_id =$mst_id and c.mst_id=a.order_rcv_id and b.section=c.section and b.sub_section=c.sub_section and b.item_group=c.item_group and b.order_uom=c.order_uom and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.mst_id, a.job_no_mst, a.description, a.color_id, a.size_id, a.qnty, a.rate, a.amount, a.order_rcv_break_id, a.order_rcv_id ,b.id ,b.order_quantity, b.order_uom, b.rate, b.amount, b.section, b.sub_section, b.item_group , b.order_rcv_dtls_id, b.order_rcv_id,b.remarks order by b.id";
	$qry_result=sql_select( "select a.id, a.mst_id, a.job_no_mst, a.style, a.description, a.color_id, a.size_id, a.qnty, a.rate as brk_rate, a.amount as brk_amount, a.order_rcv_break_id, a.order_rcv_id ,b.id as dtls_id ,b.order_quantity as sub_wo_qnty, sum(c.order_quantity) as wo_qnty, b.order_uom, b.rate, b.amount, b.section, b.sub_section, b.item_group  as trim_group , b.order_rcv_dtls_id, b.order_rcv_id,b.remarks  from trims_subcon_ord_breakdown a , trims_subcon_ord_dtls b, subcon_ord_dtls c where a.job_no_mst=b.job_no_mst and b.id=a.mst_id and b.mst_id =$mst_id and c.mst_id=a.order_rcv_id and b.section=c.section and b.sub_section=c.sub_section and b.item_group=c.item_group and b.order_uom=c.order_uom and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.mst_id, a.job_no_mst, a.style, a.description, a.color_id, a.size_id, a.qnty, a.rate, a.amount, a.order_rcv_break_id, a.order_rcv_id ,b.id ,b.order_quantity, b.order_uom, b.rate, b.amount, b.section, b.sub_section, b.item_group , b.order_rcv_dtls_id, b.order_rcv_id,b.remarks order by b.id");	

	$data_dreak_arr=array(); $data_dreak=''; $add_comma=0; $k=1;
	foreach ($qry_result as $rows)
	{
		if($rows[csf('description')]=="") $rows[csf('description')]=0;
		if($rows[csf('color_id')]=="") $rows[csf('color_id')]=0;
		if($rows[csf('size_id')]=="") $rows[csf('size_id')]=0;
		if($rows[csf('qnty')]=="") $rows[csf('qnty')]=0;
		if($rows[csf('brk_rate')]=="") $rows[csf('brk_rate')]=0;
		if($rows[csf('brk_amount')]=="") $rows[csf('brk_amount')]=0;
		if($rows[csf('book_con_dtls_id')]=="") $rows[csf('book_con_dtls_id')]=0;
		/*if(!in_array($rows[csf('mst_id')],$temp_arr_mst_id))
		{
			$temp_arr_mst_id[]=$rows[csf('mst_id')];
			$add_comma=0; $data_dreak='';
		}*/
		$k++;
		if($bookConsIds_arr[$rows[csf('book_con_dtls_id')]]=="") $bookConsIds_arr[$rows[csf('book_con_dtls_id')]]=0;
		/***********Save***********/
		//$data_dreak_arr[$rows[csf('job_no_mst')]][$rows[csf('section')]][$rows[csf('sub_section')]][$rows[csf('item_group')]][$rows[csf('order_uom')]].=$rows[csf('description')].'_'.$color_library[$rows[csf('color_id')]].'_'.$size_arr[$rows[csf('size_id')]].'_'.$rows[csf('qnty')].'_'.$rows[csf('rate')].'_'.$rows[csf('amount')].'_'.$rows[csf('book_con_dtls_id')].'_'.$rows[csf('id')].'_'.$rows[csf('is_revised')].'***';
		
		/***********Update***********/
		//echo $rows[csf('rate')].'++';
		$data_dreak_arr[$rows[csf('dtls_id')]]['info'].=$rows[csf('description')].'_'.$color_library[$rows[csf('color_id')]].'_'.$size_arr[$rows[csf('size_id')]].'_'.$rows[csf('qnty')].'_'.$rows[csf('brk_rate')].'_'.$rows[csf('brk_amount')].'_0_'.$rows[csf('order_rcv_break_id')].'_'.$rows[csf('id')].'_'.$rows[csf('style')].'***';
		$data_dreak_arr[$rows[csf('dtls_id')]]['break_ids'].=$rows[csf('order_rcv_break_id')].',';


		$wo_arr[$rows[csf("order_rcv_id")]][$rows[csf("dtls_id")]][$rows[csf("section")]][$rows[csf("sub_section")]][$rows[csf("trim_group")]][$rows[csf("order_uom")]]["qty"] =$rows[csf("sub_wo_qnty")];
		$wo_arr[$rows[csf("order_rcv_id")]][$rows[csf("dtls_id")]][$rows[csf("section")]][$rows[csf("sub_section")]][$rows[csf("trim_group")]][$rows[csf("order_uom")]]["wo_qnty"] =$rows[csf("wo_qnty")];
		$wo_arr[$rows[csf("order_rcv_id")]][$rows[csf("dtls_id")]][$rows[csf("section")]][$rows[csf("sub_section")]][$rows[csf("trim_group")]][$rows[csf("order_uom")]]["amount"] =$rows[csf("amount")];
		$wo_arr[$rows[csf("order_rcv_id")]][$rows[csf("dtls_id")]][$rows[csf("section")]][$rows[csf("sub_section")]][$rows[csf("trim_group")]][$rows[csf("order_uom")]]["wo_dtls_id"] =$rows[csf("order_rcv_dtls_id")];
		$wo_arr[$rows[csf("order_rcv_id")]][$rows[csf("dtls_id")]][$rows[csf("section")]][$rows[csf("sub_section")]][$rows[csf("trim_group")]][$rows[csf("order_uom")]]["remarks"] =$rows[csf("remarks")];
	}

	$sub_qry_result=sql_select( "select a.order_rcv_id ,b.id as dtls_id ,b.order_quantity as sub_wo_qnty, c.order_quantity as wo_qnty, b.order_uom, b.section, b.sub_section, b.item_group  as trim_group  from trims_subcon_ord_breakdown a , trims_subcon_ord_dtls b, subcon_ord_dtls c where a.job_no_mst=b.job_no_mst and b.id=a.mst_id and b.mst_id !=$mst_id and c.mst_id=a.order_rcv_id and b.section=c.section and b.sub_section=c.sub_section and b.item_group=c.item_group and b.order_uom=c.order_uom and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by b.id");	
	foreach ($sub_qry_result as $row)
	{
		$sub_wo_arr[$row[csf("order_rcv_id")]][$row[csf("dtls_id")]][$row[csf("section")]][$row[csf("sub_section")]][$row[csf("trim_group")]][$row[csf("order_uom")]]["sub_wo_qnty"] =$row[csf("remarks")];
	}

	
	//echo "<pre>";
	//print_r($data_dreak_arr);
	//2_FAL-TOR-19-00041_1_3797
/*
	$sql = "SELECT id, mst_id, job_no_mst, order_id, order_no, buyer_po_id as po_break_down_id, booking_dtls_id, order_quantity as wo_qnty, order_uom, rate, amount, delivery_date, buyer_po_no, buyer_style_ref, buyer_buyer, section, sub_section, item_group as trim_group, rate_domestic,  amount_domestic, is_with_order, booked_uom, booked_conv_fac, booked_qty, is_revised,source_for_order from subcon_ord_dtls where mst_id in ($mst_id) and source_for_order=2 and status_active=1 and is_deleted=0 order by id ASC";

	$data_array=sql_select($sql);

	$wo_arr=array(); $job_no_msts=''; $ids=''; $book_con_dtls_ids='';
	foreach ($data_array as  $row) 
	{
		//echo $mstIDS.'=='.$ids.'=='.$book_con_dtls_ids.'++'; 
		$wo_arr[$row[csf("mst_id")]][$row[csf("job_no_mst")]][$row[csf("section")]][$row[csf("sub_section")]][$row[csf("trim_group")]][$row[csf("order_uom")]]["qty"] +=$row[csf("wo_qnty")];
		$wo_arr[$row[csf("mst_id")]][$row[csf("job_no_mst")]][$row[csf("section")]][$row[csf("sub_section")]][$row[csf("trim_group")]][$row[csf("order_uom")]]["amount"] +=$row[csf("amount")];
		$wo_arr[$row[csf("mst_id")]][$row[csf("job_no_mst")]][$row[csf("section")]][$row[csf("sub_section")]][$row[csf("trim_group")]][$row[csf("order_uom")]]["wo_dtls_id"] .=$row[csf("id")].',';
		$job_no_msts .= $row[csf("job_no_mst")].',';
	}


	$sub_wo_result=sql_select( "select a.id, a.mst_id, a.job_no_mst, a.description, a.color_id, a.size_id, a.qnty, a.rate, a.amount, a.order_rcv_break_id, a.order_rcv_id ,b.id as dtls_id ,b.order_quantity as sub_wo_qnty,  b.order_uom, b.rate, b.amount, b.section, b.sub_section, b.item_group  as trim_group , b.order_rcv_dtls_id, b.order_rcv_id,b.remarks ,c.job_no_mst ,b.order_quantity as wo_qnty from trims_subcon_ord_breakdown a , trims_subcon_ord_dtls b, subcon_ord_dtls c where a.job_no_mst=b.job_no_mst and b.id=a.mst_id and c.mst_id=a.order_rcv_id and b.section=c.section and b.sub_section=c.sub_section and b.item_group=c.item_group and b.order_uom=c.order_uom and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 order by b.id");	

	$sub_wo_arr=array(); 
	foreach ($sub_wo_result as $rows)
	{
		$sub_wo_arr[$rows[csf("order_rcv_id")]][$rows[csf("job_no_mst")]][$rows[csf("section")]][$rows[csf("sub_section")]][$rows[csf("trim_group")]][$rows[csf("order_uom")]]["sub_wo_qnty"] +=$rows[csf("sub_wo_qnty")];
		//$sub_wo_arr[$rows[csf("order_rcv_id")]][$rows[csf("job_no_mst")]][$rows[csf("section")]][$rows[csf("sub_section")]][$rows[csf("trim_group")]][$rows[csf("order_uom")]]["wo_qnty"] +=$rows[csf("wo_qnty")];
	}*/

	$tblRow=1; $i=1;
	foreach($wo_arr as $order_rcv_id=> $order_rcv_id_data)
	{
		?>
		<thead>
			<tr bgcolor="<? echo $bgcolor; ?>" id="row_<? echo $tblRow; ?>" align="center">
				<th width="150" class="must_entry_caption">Order No :
				</th>
				<th colspan="2" align="left"><? echo $order_receive_arr[$order_rcv_id]; ?><input type="hidden" name="hdnWoId" id="hdnWoId_<? echo $i; ?>"></th>
				<th colspan="5">&nbsp;</th>
			</tr>
			<tr>
        		<th width="150" class="must_entry_caption">Section</th>
                <th width="150" >Sub Section</th>
                <th width="150" class="must_entry_caption">Trims Group</th>
                <th width="100" class="must_entry_caption">Order UOM</th>
                <th width="110" class="must_entry_caption">Order Qty</th>
                <th width="110" class="must_entry_caption rateTd">Rate</th>
                <th width="110" class="must_entry_caption">Amount</th>
                <th width="200">Remarks</th>
        	</tr>
		</thead>
		<tbody>
		<?
		foreach($order_rcv_id_data as  $dtls_id=> $dtls_id_data)
		{ 
			foreach($dtls_id_data as $section_id=> $section_data)
			{
				foreach($section_data as $sub_section_id=> $sub_section_data)
				{ 
					foreach($sub_section_data as $trim_group_id=> $trim_group_data)
					{
						//$mst_ids='';
						foreach($trim_group_data as $order_uom_id => $row)
						{
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" id="row_<? echo $tblRow; ?>" align="center">
								<td><?

									$dataBreakDtls=$data_dreak_arr[$dtls_id]['info'];
									$dataBreakIdss=$data_dreak_arr[$dtls_id]['break_ids'];
									if($section_id==1) $subID='1,2,3';
									else if($section_id==3) $subID='4,5,18';
									else if($section_id==5) $subID='6,7,8,9,10,11,12,13';
									else if($section_id==10) $subID='14,15';
									else if($section_id==7) $subID='19,20';
									else $subID='0';
									$sub_wo_qnty=$sub_wo_arr[$order_rcv_id][$dtls_id][$section_id][$sub_section_id][$trim_group_id][$order_uom_id]["sub_wo_qnty"];
									$balance_Qty=$row['wo_qnty']-$sub_wo_qnty;
									echo create_drop_down( "cboSection_".$tblRow, 150, $trims_section,"", 1, "-- Select Section --",$section_id,"load_sub_section($tblRow)",1,'','','','','','',"cboSection[]"); ?></td>
								<td id="subSectionTd_<? echo $tblRow; ?>"><? echo create_drop_down( "cboSubSection_".$tblRow, 150, $trims_sub_section,"", 1, "-- Select Section --",$sub_section_id,"load_sub_section_value($tblRow)",1,$subID,'','','','','',"cboSubSection[]"); ?></td>			
								<td><? echo create_drop_down( "cboItemGroup_".$tblRow, 150, "select id, item_name from lib_item_group where item_category=4 and status_active=1","id,item_name", 1, "-- Select --",$trim_group_id, "",1,'','','','','','',"cboItemGroup[]"); ?></td>
								<td><? echo create_drop_down( "cboUom_".$tblRow, 100, $unit_of_measurement,"", 1, "-- Select --",$order_uom_id,"", 1,'','','','','','',"cboUom[]"); ?>	</td>
								<td><input id="txtOrderQuantity_<? echo $tblRow; ?>" name="txtOrderQuantity[]" value="<? echo number_format($row['qty'],4,'.',''); ?>" class="text_boxes_numeric" type="text"  style="width:97px" onClick="openmypage_order_qnty(<? echo $tblRow; ?>,<? echo $i; ?>)" placeholder="Click To Search" readonly /></td>
								<td><input id="txtRate_<? echo $tblRow; ?>" name="txtRate[]" value="<? echo number_format($row['amount']/$row['qty'],4,'.',''); ?>" type="text"  class="text_boxes_numeric" style="width:97px" readonly/></td>
								<td><input id="txtAmount_<? echo $tblRow; ?>" name="txtAmount[]"  value="<? echo number_format($row['amount'],4,'.',''); ?>" type="text" style="width:97px"  class="text_boxes_numeric"  disabled />
								<td><input id="txtRemarks_<? echo $tblRow; ?>" name="txtRemarks[]"  value="<? echo $row['remarks']; ?>" type="text" style="width:187px"  class="text_boxes"  />
									<input id="hdnDtlsUpdateId_<? echo $tblRow; ?>" name="hdnDtlsUpdateId[]" type="hidden"  value="<?=$dtls_id; ?>" class="text_boxes_numeric" style="width:40px" />
									<input type="hidden" id="hdnWoDtlsIds_<? echo $tblRow; ?>" name="hdnWoDtlsIds[]" value="<?=$row['wo_dtls_id']; ?>">
									<input type="hidden" id="hdnBreakDtlsDatas_<? echo $tblRow; ?>" name="hdnBreakDtlsDatas[]" value="<?=$dataBreakDtls; ?>">
									<input type="hidden" id="hdnRcvId_<? echo $tblRow; ?>" name="hdnRcvId[]" value="<?=$order_rcv_id; ?>">
									<input type="hidden" id="hdnBalance_<? echo $tblRow; ?>" name="hdnBalance[]" value="<?= number_format($balance_Qty,4,'.',''); ?>">
									<input type="hidden" id="hdnRcvBrkIds_<? echo $tblRow; ?>" name="hdnRcvBrkIds[]" value="<?= chop($dataBreakIdss,','); ?>">
					                
					            </td>
							</tr>
							<?
							$tblRow++;
						}
					}
				}
			}
		}
		$i++;
		?></tbody><?
	}
	
	//echo $sql; //die; 
	//print_r($data_array);
	$ind=0;
	$min_dates=$del_date_arr[0];
	foreach($del_date_arr as $v) 
	{
		if(strtotime($min_dates)>strtotime($v))$min_dates=$v;
	}
	?><input type="hidden" id="min_date_id" name="min_date_id" value="<? echo change_date_format($min_dates);?>"><?
	exit();
}


if($action=="order_qty_popup")
{
	//echo $action; die;
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode,'','');
	extract($_REQUEST);

	$color_arr=return_library_array( "select id,color_name from lib_color",'id','color_name');
	$size_arr=return_library_array( "select id,size_name from  lib_size",'id','size_name');

	$qry_result=sql_select( "select a.id, a.mst_id, a.order_id, a.job_no_mst, a.book_con_dtls_id, a.description, a.color_id, a.size_id, a.qnty, a.rate, a.amount,a.is_revised,a.style,b.order_uom, b.section, b.sub_section, b.item_group from subcon_ord_breakdown a , subcon_ord_dtls b where a.job_no_mst=b.job_no_mst and b.id=a.mst_id and a.id in ($hdnRcvBrkIds) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by a.id");	
	$data_dreak_arr=array(); $data_dreak=''; $add_comma=0; $k=1; $attachedBrkQty=0; $balanceBrkQty=0;
	foreach ($qry_result as $rows)
	{
		if(trim($rows[csf("description")])=="") $rows[csf('description')]=0;
		if($rows[csf('color_id')]=="") $rows[csf('color_id')]=0;
		if($rows[csf('size_id')]=="") $rows[csf('size_id')]=0;
		if($rows[csf('qnty')]=="") $rows[csf('qnty')]=0;
		if($rows[csf('rate')]=="") $rows[csf('rate')]=0;
		if($rows[csf('amount')]=="") $rows[csf('amount')]=0;
		if($rows[csf('book_con_dtls_id')]=="") $rows[csf('book_con_dtls_id')]=0;
		/*if(!in_array($rows[csf('mst_id')],$temp_arr_mst_id))
		{
			$temp_arr_mst_id[]=$rows[csf('mst_id')];
			$add_comma=0; $data_dreak='';
		}*/
		$k++;
		if($bookConsIds_arr[$rows[csf('book_con_dtls_id')]]=="") $bookConsIds_arr[$rows[csf('book_con_dtls_id')]]=0;
		//echo $rows[csf("job_no_mst")].'=='.$rows[csf("mst_id")].'=='.$rows[csf("section")].'=='.$rows[csf("sub_section")].'=='.$rows[csf("item_group")].'=='.$rows[csf("order_uom")].'=='.trim($rows[csf("description")]).'=='.$rows[csf("color_id")].'=='.$rows[csf("size_id")].'++';
		

		$data_dreak_arr[$rows[csf('id')]]['description']= trim($rows[csf("description")]);
		$data_dreak_arr[$rows[csf('id')]]['color']= $color_arr[$rows[csf('color_id')]];
		$data_dreak_arr[$rows[csf('id')]]['color_id']= $rows[csf('color_id')];
		$data_dreak_arr[$rows[csf('id')]]['size']= $size_arr[$rows[csf('size_id')]];
		$data_dreak_arr[$rows[csf('id')]]['size_id']= $rows[csf('size_id')];
		$data_dreak_arr[$rows[csf('id')]]['style']= $rows[csf('style')];		
	}
	//echo "<pre>";
	//print_r($data_dreak_arr);
    ?>
    <script>
    	var str_color = [<? echo substr(return_library_autocomplete( "select color_name from lib_color group by color_name ", "color_name" ), 0, -1); ?> ];
    	var str_size = [<? echo substr(return_library_autocomplete( "select size_name from lib_size group by size_name ", "size_name" ), 0, -1); ?> ];
		
		function set_auto_complete(type)
		{
			if(type=='color_return')
			{
				$(".txt_color").autocomplete({
					source: str_color
				});
			}
		}

		function set_auto_complete_size(type)
		{
			if(type=='size_return')
			{
				$(".txt_size").autocomplete({
					source: str_size
				});
			}
		}

		function fnc_close()
		{
			var tot_row=$('#tbl_share_details_entry tbody tr').length;
			
			var data_break_down="";
			for(var i=1; i<=tot_row; i++)
			{
				/*if (form_validation('txtorderquantity_'+i,'Quantity')==false)
				{
					return;
				}*/
				//alert($("#txtsize_"+i).val());
				if($("#txtdescription_"+i).val()=="") $("#txtdescription_"+i).val(0)
				if($("#txtcolor_"+i).val()=="") $("#txtcolor_"+i).val(0);
				if($("#txtsize_"+i).val()=="") $("#txtsize_"+i).val(0);
				if($("#txtorderquantity_"+i).val()=="") $("#txtorderquantity_"+i).val(0);
				if($("#txtorderrate_"+i).val()=="") $("#txtorderrate_"+i).val(0);
				if($("#txtorderamount_"+i).val()=="") $("#txtorderamount_"+i).val(0);
				if($("#hidbookingconsid_"+i).val()=="") $("#hidbookingconsid_"+i).val(0);
				if($("#hiddenid_"+i).val()=="") $("#hiddenid_"+i).val(0);
				if($("#txtcolorId_"+i).val()=="") $("#txtcolorId_"+i).val(0);
				if($("#txtsizeID_"+i).val()=="") $("#txtsizeID_"+i).val(0);
				if($("#hiddenUpid_"+i).val()=="") $("#hiddenUpid_"+i).val(0);
				
				if(data_break_down=="")
				{
					data_break_down+=$('#txtdescription_'+i).val()+'_'+$('#txtcolor_'+i).val()+'_'+$('#txtsize_'+i).val()+'_'+$('#txtorderquantity_'+i).val()+'_'+$('#txtorderrate_'+i).val()+'_'+$('#txtorderamount_'+i).val()+'_'+$('#hidbookingconsid_'+i).val()+'_'+$('#hiddenid_'+i).val()+'_'+$('#hiddenUpid_'+i).val()+'_'+$('#txtStyle_'+i).val();
				}
				else
				{
					data_break_down+="***"+$('#txtdescription_'+i).val()+'_'+$('#txtcolor_'+i).val()+'_'+$('#txtsize_'+i).val()+'_'+$('#txtorderquantity_'+i).val()+'_'+$('#txtorderrate_'+i).val()+'_'+$('#txtorderamount_'+i).val()+'_'+$('#hidbookingconsid_'+i).val()+'_'+$('#hiddenid_'+i).val()+'_'+$('#hiddenUpid_'+i).val()+'_'+$('#txtStyle_'+i).val();
				}
			}
			//alert(data_break_down);
			$('#hidden_break_tot_row').val( data_break_down );
			//alert(tot_row);//return;
			parent.emailwindow.hide();
		}

		function sum_total_qnty(id)
		{
			var ddd={ dec_type:5, comma:0, currency:''};
			var tot_row=$('#tbl_share_details_entry tbody tr').length;
			var orderrate=$("#txtorderrate_"+id).val()*1;
			var orderquantity=$("#txtorderquantity_"+id).val()*1;
			var balancequantity=$("#hiddenOrderQuantity_"+id).val()*1;
			if(orderquantity>balancequantity)
			{
				alert("No Balance Quantity");
				$("#txtorderquantity_"+id).val(balancequantity);
				//return;
			}
			$("#txtorderamount_"+id).val(orderquantity*orderrate);
			//math_operation( "txt_total_order_qnty", "txtorderquantity_", "+", tot_row,ddd );
			//math_operation( "txt_average_rate", "txtorderrate_", "+", tot_row,ddd );
			//math_operation( "txt_total_order_amount", "txtorderamount_", "+", tot_row,ddd );
			
			var tot_row=$('#tbl_share_details_entry tbody tr').length;
			
			var qty=0; var amt=0;
			for(var i=1; i<=tot_row; i++)
			{
				qty+=$("#txtorderquantity_"+i).val()*1;
				amt+=$("#txtorderamount_"+i).val()*1;
			}
			
			var rate=amt/qty;
			$("#txt_average_rate").val( number_format(rate,4,'.','' ) );
			$("#txt_total_order_qnty").val( number_format(qty,4,'.','' ) );
			$("#txt_total_order_amount").val( number_format(amt,2,'.','' ) );
		}

		function fnc_copy_chk(is_checked)
		{
			
			if(is_checked==true){
				var orderrate= $("#txtorderrate_1").val();
				var tot_row=$('#tbl_share_details_entry tbody tr').length;
				//alert(tot_row);
				var ord_qty =0; var amount=0;
				for(var i=1; i<=tot_row; i++)
				{
					$("#txtorderrate_"+i).val(orderrate);
					ord_qty =$("#txtorderquantity_"+i).val()*1;
					
					amount= orderrate*ord_qty;
					$("#txtorderamount_"+i).val(amount);
					
					//qty+=$("#txtorderquantity_"+i).val()*1;
					//amt+=$("#txtorderamount_"+i).val()*1;
					//alert(i);
				}
				//var rate=amt/qty;
				//$("#txt_average_rate").val( number_format(rate,4,'.','' ) );
			}else{
			}
		}

	</script>
</head>
<body onLoad="set_auto_complete('color_return'); set_auto_complete_size('size_return');">
	<div align="center" style="width:100%;" >
		<form name="qntypopup_1"  id="qntypopup_1" autocomplete="off">
			<table class="rpt_table" width="630px" cellspacing="0" cellpadding="0" rules="all" id="tbl_share_details_entry">
				<thead>
					<th width="190">Style</th>
					<th width="190">Description</th>
					<th width="100">Color</th>
					<th width="80">Size</th>
					<th width="70">Order Qty</th>
					<th width="60">Rate <input type="checkbox" name="copy_chk" id="copy_chk" onClick="fnc_copy_chk(this.checked)" /></th>
					<th>Amount</th>
				</thead>
				<tbody>
					<input type="hidden" name="txtDeletedId" id="txtDeletedId" class="text_boxes_numeric" style="width:90px" readonly />
					<input type="hidden" name="hidden_break_tot_row" id="hidden_break_tot_row" class="text_boxes" style="width:90px" />
					<?
					//echo "<pre>".$action.'=='.$cboUom."</pre>"; die;
					if($breakDtlsDatas!=''){
						$breakDtlsDatas=chop($breakDtlsDatas,'***');
						$data_array=explode("***",$breakDtlsDatas);
						$is_available_datas=count($data_array);
					}
					else
					{
						$is_available_datas=0;
					}
					//echo $within_group;
					$k=0;
					//echo count($data_array);
					if($within_group==1) $disabled="disabled"; else $disabled="";
					if($is_available_datas>0)
					{
						foreach($data_array as $row)
						{
							$data=explode('_',$row);
							$k++;
							$description=$data_dreak_arr[$data[7]]['description'];
							$color=$data_dreak_arr[$data[7]]['color'];
							$color_id=$data_dreak_arr[$data[7]]['color_id'];
							$size=$data_dreak_arr[$data[7]]['size'];
							$size_id=$data_dreak_arr[$data[7]]['size_id'];
							$style=$data_dreak_arr[$data[7]]['style'];
							$rate=number_format($data[4],4,'.','');
							$amount=number_format($data[5],2,'.','');
							if($rate==0) $rate=''; else $rate=$rate;
							if($amount==0) $amount=''; else $amount=$amount;


							//if($data[7]=='') $rate=number_format($data[4],4,'.',''); else $rate='';
							//if($data[7]=='') $amount=number_format($data[5],4,'.',''); else $amount='';
							?>
							<tr>
								<td><input type="text" id="txtStyle_<? echo $k;?>" name="txtStyle_<? echo $k;?>" class="text_boxes" style="width:177px" value="<? echo $style; ?>"  disabled="disabled" />
								<td><input type="text" id="txtdescription_<? echo $k;?>" name="txtdescription_<? echo $k;?>" class="text_boxes" style="width:177px" value="<? echo $description; ?>" disabled="disabled" />
								</td>
								<td>
									<input type="text" id="txtcolor_<? echo $k;?>" name="txtcolor_<? echo $k;?>" class="text_boxes txt_color" style="width:90px" value="<? echo $color; ?>"  disabled="disabled" >
									<input type="hidden" id="txtcolorId_<? echo $k;?>" name="txtcolorId_<? echo $k;?>" class="text_boxes_numeric" style="width:90px" value="<? echo $color_id; ?>"  /></td>
								<td><input type="text" id="txtsize_<? echo $k;?>" name="txtsize_<? echo $k;?>" class="text_boxes txt_size" style="width:70px" value="<? echo $size; ?>"  disabled="disabled" >
									<input type="hidden" id="txtsizeID_<? echo $k;?>" name="txtsizeID_<? echo $k;?>" class="text_boxes_numeric" style="width:70px" value="<? echo $size_id; ?>"></td>
								<td>
									<input type="text" id="txtorderquantity_<? echo $k;?>" name="txtorderquantity_<? echo $k;?>" class="text_boxes_numeric" style="width:70px" onKeyUp="sum_total_qnty(<? echo $k;?>);" value="<? echo number_format($data[3],4,'.',''); ?>" <? echo $disabled; ?> />
									<input type="hidden" id="hiddenOrderQuantity_<? echo $k;?>" name="hiddenOrderQuantity_<? echo $k;?>" class="text_boxes_numeric" placeholder="<? echo $data[3]; ?>" style="width:70px" value="<? echo $data[3]; ?>"  />
								</td>
								<td><input type="text" id="txtorderrate_<? echo $k;?>" name="txtorderrate_<? echo $k;?>"  class="text_boxes_numeric" style="width:60px" onKeyUp="sum_total_qnty(<? echo $k;?>)" value="<? echo $rate ; ?>" <? echo $disabled; ?> />
								</td>
								<td><input type="text" id="txtorderamount_<? echo $k;?>" name="txtorderamount_<? echo $k;?>" class="text_boxes_numeric" style="width:70px" value="<? echo $amount; ?>" disabled/>
									<input type="hidden" id="hidbookingconsid_<? echo $k; ?>" name="hidbookingconsid_<? echo $k; ?>"  style="width:15px;" class="text_boxes" value="<? echo $data[6]; ?>" />
                                    <input type="hidden" id="hiddenid_<? echo $k; ?>" name="hiddenid_<? echo $k; ?>"  style="width:15px;" class="text_boxes" value="<? echo $data[7]; ?>" />
                                    <input type="hidden" id="hiddenUpid_<? echo $k; ?>" name="hiddenUpid_<? echo $k; ?>"  style="width:15px;" class="text_boxes" value="<? echo $data[8]; ?>" />
                                </td>
							</tr>
							<?
						}
					}
					else
					{
						?>
                        <tr>
                        	<td><input type="text" id="txtStyle_1" name="txtStyle_1" class="text_boxes" style="width:177px" value="" /></td>
                        	<td><input type="text" id="txtdescription_1" name="txtdescription_1" class="text_boxes" style="width:177px" value="" /></td>
							<td>
								<input type="text" id="txtcolor_1" name="txtcolor_1" class="text_boxes txt_color" style="width:90px" value="" >
								<input type="hidden" id="txtcolorId_1" name="txtcolorId_1" class="text_boxes_numeric" style="width:90px" value=""  /></td>
							<td><input type="text" id="txtsize_1" name="txtsize_1" class="text_boxes txt_size" style="width:70px" value=""  >
								<input type="hidden" id="txtsizeID_1" name="txtsizeID_1" class="text_boxes_numeric" style="width:70px" value=""></td>
							<td>
								<input type="text" id="txtorderquantity_1" name="txtorderquantity_1" class="text_boxes_numeric" style="width:70px" onKeyUp="sum_total_qnty(1);" value="" <? echo $disabled; ?> />
								<input type="hidden" id="hiddenOrderQuantity_1" name="hiddenOrderQuantity_1" class="text_boxes_numeric" style="width:70px" value=""  />
							</td>
							<td><input type="text" id="txtorderrate_1" name="txtorderrate_1"  class="text_boxes_numeric" style="width:60px" onKeyUp="sum_total_qnty(1)" value="" <? echo $disabled; ?> />
							</td>
							<td><input type="text" id="txtorderamount_1" name="txtorderamount_1" class="text_boxes_numeric" style="width:70px" value="" disabled/>
								<input type="hidden" id="hidbookingconsid_1" name="hidbookingconsid_1"  style="width:15px;" class="text_boxes" value="" />
                                <input type="hidden" id="hiddenid_1" name="hiddenid_1"  style="width:15px;" class="text_boxes" value="" /></td>
							<td>
                        </tr>
						<?
					}
					?> 
				</tbody>
				<tfoot>
					<th colspan="4">Total</th> 
					<th><input type="text" id="txt_total_order_qnty" name="txt_total_order_qnty" class="text_boxes_numeric" readonly style="width:70px" value="<? echo $break_tot_qty;//number_format($break_tot_qty,4); ?>"; /></th>
					<th><input type="text" id="txt_average_rate" name="txt_average_rate" class="text_boxes_numeric" readonly style="width:61px" value="<? echo $break_avg_rate; ?>"; /></th>
					<th><input type="text" id="txt_total_order_amount" name="txt_total_order_amount" class="text_boxes_numeric" readonly style="width:70px" value="<? echo $break_total_value; ?>"; /></th>
					<th></th>
				</tfoot>
			</table> 
			<table>
				<tr>
					<td align="center"><input type="button" name="main_close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" /></td>
				</tr>
			</table>
		</form>
	</div>
</body>
<script>sum_total_qnty(0);</script>        
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	/*echo '<pre>';
	print_r($cbo_company_name);die;*/
	$user_id=$_SESSION['logic_erp']['user_id'];

	/*$sub_wo_result=sql_select( "select a.id, a.mst_id, a.job_no_mst, a.description, a.color_id, a.size_id, a.qnty, a.rate, a.amount, a.order_rcv_break_id, a.order_rcv_id ,b.id as dtls_id ,b.order_quantity as sub_wo_qnty,  b.order_uom, b.rate, b.amount, b.section, b.sub_section, b.item_group  as trim_group , b.order_rcv_dtls_id, b.order_rcv_id,b.remarks ,c.job_no_mst ,b.order_quantity as wo_qnty from trims_subcon_ord_breakdown a , trims_subcon_ord_dtls b, subcon_ord_dtls c where a.job_no_mst=b.job_no_mst and b.id=a.mst_id and c.mst_id=a.order_rcv_id and b.section=c.section and b.sub_section=c.sub_section and b.item_group=c.item_group and b.order_uom=c.order_uom and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 order by b.id");	

	$sub_wo_arr=array(); 
	foreach ($sub_wo_result as $rows)
	{
		$sub_wo_arr[$rows[csf("order_rcv_id")]][$rows[csf("job_no_mst")]][$rows[csf("section")]][$rows[csf("sub_section")]][$rows[csf("trim_group")]][$rows[csf("order_uom")]]["sub_wo_qnty"] +=$rows[csf("sub_wo_qnty")];
		$sub_wo_arr[$rows[csf("order_rcv_id")]][$rows[csf("job_no_mst")]][$rows[csf("section")]][$rows[csf("sub_section")]][$rows[csf("trim_group")]][$rows[csf("order_uom")]]["wo_qnty"] +=$rows[csf("wo_qnty")];
	}*/
	
	if ($operation==0) // Insert Start Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		/*$receive_date=strtotime(str_replace("'",'',$txt_order_receive_date));
		$delivery_date=strtotime(str_replace("'",'',$txt_delivery_date));
		$current_date=strtotime(date("d-m-Y"));
		if($receive_date>$delivery_date)
		{
			echo "26**"; disconnect($con); die;
		}
		else if($receive_date != $current_date)
		{
			echo "25**"; disconnect($con); die;
		}*/

		
		if($db_type==0) $insert_date_con="and YEAR(insert_date)=".date('Y',time())."";
		else if($db_type==2) $insert_date_con="and TO_CHAR(insert_date,'YYYY')=".date('Y',time())."";
		
		$new_job_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', 'SCWO', date("Y",time()), 5, "select job_no_prefix,job_no_prefix_num from trims_subcon_ord_mst where entry_form=450 and company_id=$cbo_company_name $insert_date_con order by id desc ", "job_no_prefix", "job_no_prefix_num" ));
		//$new_job_no = explode("*", return_next_id_by_sequence("INV_RECEIVE_MASTER_PK_SEQ", "subcon_ord_mst",$con,1,$cbo_company_name,'TOR',1,date("Y",time()),1 ));
		/*if(str_replace("'",'',$txt_order_no)==""){
			$txt_order_no=$new_job_no[0];
		}else{
			$txt_order_no=str_replace("'",'',$txt_order_no);
		}*/

		/*if (is_duplicate_field( "order_no", "subcon_ord_mst", "order_no='$txt_order_no' and company_id=$cbo_company_name and status_active=1 and is_deleted=0" ) == 1)
		{
			echo "11**0"; disconnect($con); die;
		}
		else
		{*/
			//echo "10**select order_no from subcon_ord_mst where order_no='$txt_order_no' and company_id=$cbo_company_name and status_active=1 and is_deleted=0 and id !=$update_id"; die;
		if($db_type==0){
			$txt_delivery_date=change_date_format(str_replace("'",'',$txt_delivery_date),'yyyy-mm-dd');
			$txt_wo_date=change_date_format(str_replace("'",'',$txt_wo_date),'yyyy-mm-dd');
			
		}else{
			$txt_delivery_date=change_date_format(str_replace("'",'',$txt_delivery_date), "", "",1);
			$txt_wo_date=change_date_format(str_replace("'",'',$txt_wo_date), "", "",1);
		}
		$id=return_next_id("id","trims_subcon_ord_mst",1);
		$id1=return_next_id( "id", "trims_subcon_ord_dtls",1);
		$id3=return_next_id( "id", "trims_subcon_ord_breakdown", 1 );
		$rID3=true;

		$field_array="id, entry_form, subcon_job, job_no_prefix, job_no_prefix_num, company_id, location_id, order_rcv_id, order_rcv_no, supplier_id, currency_id, paymode_id, source_id, exchange_rate, wo_date, delivery_date, attention, remarks, inserted_by, insert_date";

		$data_array="(".$id.", 450, '".$new_job_no[0]."', '".$new_job_no[1]."', '".$new_job_no[2]."', '".$cbo_company_name."', '".$cbo_location_name."', '".$txt_ord_rec_id."', '".$txt_ord_rec_no."', '".$cbo_supplier_id."', '".$cbo_currency."', '".$cbo_paymode_id."', '".$cbo_source_id."','".$txt_exchange_rate."','".$txt_wo_date."', '".$txt_delivery_date."', '".$txt_attention."', '".$txt_remarks."', ".$_SESSION['logic_erp']['user_id'].", '".$pc_date_time."')";
		
		$txt_job_no=$new_job_no[0];
		
		$field_array2="id, mst_id, job_no_mst, order_quantity, order_uom, rate, amount, section, sub_section, item_group, order_rcv_dtls_id, order_rcv_id, remarks, inserted_by, insert_date";
		$field_array3="id, mst_id, job_no_mst, style, description, color_id, size_id, qnty, rate, amount, order_rcv_break_id, order_rcv_id, inserted_by, insert_date";

		$color_library_arr=return_library_array( "select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
		$size_library_arr=return_library_array( "select id, size_name from lib_size where status_active=1 and is_deleted=0", "id", "size_name");
		$data_array2 	= $data_array3="";  $add_commaa=0; $add_commadtls=0; $new_array_color=array();  $new_array_size=array();

		for($i=1; $i<=$total_row; $i++)
		{			
			$cboSection				= "cboSection_".$i;
			$cboSubSection			= "cboSubSection_".$i;
			$cboItemGroup			= "cboItemGroup_".$i;
			$txtOrderQuantity		= "txtOrderQuantity_".$i;
			$cboUom 				= "cboUom_".$i;
			$txtRate 				= "txtRate_".$i;
			$txtAmount 				= "txtAmount_".$i;			
			$txtRemarks 			= "txtRemarks_".$i;			
			
			$hdnDtlsdata 			= "hdnDtlsdata_".$i;
			$hdnDtlsUpdateId 		= "hdnDtlsUpdateId_".$i;
			$hdnWoDtlsIds 			= "hdnWoDtlsIds_".$i;
			$hdnRcvId 				= "hdnRcvId_".$i;
			$hdnWoDtlsId =chop($$hdnWoDtlsIds ,',');
			if ($add_commaa!=0) $data_array2 .=","; $add_comma=0;

			$data_array2 .="(".$id1.",".$id.",'".$new_job_no[0]."',".str_replace(",",'',$$txtOrderQuantity).",".$$cboUom.",".$$txtRate.",".str_replace(",",'',$$txtAmount).",".$$cboSection.",".$$cboSubSection.",".$$cboItemGroup.",".$hdnWoDtlsId.",".str_replace("'",'',$$hdnRcvId).",".$$txtRemarks.", ".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			$dtls_data=explode("***",chop(str_replace("'",'',$$hdnDtlsdata),'***'));
			/*echo "10**".$total_row; 
			print_r($dtls_data);
			die;*/
			for($j=0; $j<count($dtls_data); $j++)
			{
				$exdata=explode("_",$dtls_data[$j]);
				
				$description="'".$exdata[0]."'";
				$colorname="'".$exdata[1]."'";
				$sizename="'".$exdata[2]."'";
				$qty="'".str_replace(",",'',$exdata[3])."'";
				$booked_qty=str_replace(",",'',$exdata[3])*str_replace("'",'',$$txtConvFactor);
				$rate="'".str_replace(",",'',$exdata[4])."'";
				$amount="'".str_replace(",",'',$exdata[5])."'";
				$book_con_dtls_id="'".$exdata[6]."'";
				$rcvBrkId="'".$exdata[7]."'";
				$style="'".$exdata[9]."'";
				
				if (str_replace("'", "", trim($colorname)) != "") {
					if (!in_array(str_replace("'", "", trim($colorname)),$new_array_color)){
						$color_id = return_id( str_replace("'", "", trim($colorname)), $color_library_arr, "lib_color", "id,color_name","450");
						$new_array_color[$color_id]=str_replace("'", "", trim($colorname));
					}
					else $color_id =  array_search(str_replace("'", "", trim($colorname)), $new_array_color);
				} else $color_id = 0;
				
				if(str_replace("'","",$sizename)!="")
				{ 
					if (!in_array(str_replace("'","",$sizename),$new_array_size))
					{
						$size_id = return_id( str_replace("'","",$sizename), $size_library_arr, "lib_size", "id,size_name","450");  
						//echo $$txtColorName.'='.$color_id.'<br>';
						$new_array_size[$size_id]=str_replace("'","",$sizename);
					}
					else $size_id =  array_search(str_replace("'","",$sizename), $new_array_size); 
				}
				else
				{
					$size_id=0;
				}
				
				if ($add_commadtls!=0) $data_array3 .=",";
				$data_array3.="(".$id3.",".$id1.",'".$new_job_no[0]."',".$style.",".$description.",'".$color_id."','".$size_id."',".$qty.",".$rate.",".$amount.",".$rcvBrkId.",".str_replace("'",'',$$hdnRcvId).", ".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				$id3=$id3+1; $add_commadtls++;
			}
			
			$id1=$id1+1; $add_commaa++;
			//echo "10**INSERT INTO subcon_ord_breakdown (".$field_array3.") VALUES ".$data_array3; die;			
		}

		//echo "10**INSERT INTO subcon_ord_mst (".$field_array.") VALUES ".$data_array; die;
		//echo "10**INSERT INTO trims_subcon_ord_breakdown (".$field_array3.") VALUES ".$data_array3; die;
		//echo "10**INSERT INTO trims_subcon_ord_dtls (".$field_array2.") VALUES ".$data_array2; die;
		$flag=1;
		$rID=sql_insert("trims_subcon_ord_mst",$field_array,$data_array,1);
		if($rID==1) $flag=1; else $flag=0;
		if($flag==1)
		{
			$rID2=sql_insert("trims_subcon_ord_dtls",$field_array2,$data_array2,1);
			if($rID2==1) $flag=1; else $flag=0;
		}
		
		if($data_array3!="" && $flag==1)
		{
			$rID3=sql_insert("trims_subcon_ord_breakdown",$field_array3,$data_array3,1);
			if($rID3==1) $flag=1; else $flag=0;
		}
		//echo "10**".$rID."**".$rID2."**".$rID3; die;
		/*if(str_replace("'","",$cbo_within_group)==1)
		{
			if($flag==1)
			{
				$rIDBooking=execute_query( "update wo_booking_mst set lock_another_process=1 where booking_no ='".$txt_order_no."'",1);
				if($rIDBooking==1) $flag=1; else $flag=0;
			}
		}*/
		
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "0**".str_replace("'",'',$txt_job_no)."**".str_replace("'",'',$id);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'",'',$txt_job_no)."**".str_replace("'",'',$id);
			}
		}
		else if($db_type==2)
		{
			if($flag==1)
			{
				oci_commit($con);
				echo "0**".str_replace("'",'',$txt_job_no)."**".str_replace("'",'',$id);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'",'',$txt_job_no)."**".str_replace("'",'',$id);
			}
		}
		
		disconnect($con);
		die;
	}
	else if ($operation==1)   // Update Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		/*$sub_order_no = return_field_value("trims_job", "trims_job_card_mst", "order_no='$txt_order_no' and received_id=$update_id and entry_form=257 and status_active = 1", "trims_job");
		//echo "10**".$sub_order_no; die;
		if($sub_order_no!="")
		{
			echo "20**".str_replace("'",'',$txt_job_no)."**".str_replace("'",'',$update_id)."**".$sub_order_no;
			disconnect($con);
			die;
		}*/
		if($db_type==0){
			$txt_delivery_date=change_date_format(str_replace("'",'',$txt_delivery_date),'yyyy-mm-dd');
			$txt_wo_date=change_date_format(str_replace("'",'',$txt_wo_date),'yyyy-mm-dd');
			
		}else{
			$txt_delivery_date=change_date_format(str_replace("'",'',$txt_delivery_date), "", "",1);
			$txt_wo_date=change_date_format(str_replace("'",'',$txt_wo_date), "", "",1);
		}
		$update_id=str_replace("'",'',$update_id);
		$id=return_next_id("id","trims_subcon_ord_mst",1);
		$id1=return_next_id( "id", "trims_subcon_ord_dtls",1);
		$id3=return_next_id( "id", "trims_subcon_ord_breakdown", 1 );

		$field_array="location_id*order_rcv_id*order_rcv_no*supplier_id*currency_id*paymode_id*source_id*exchange_rate*wo_date*delivery_date*attention*remarks*updated_by*update_date";
 		$data_array="'".$cbo_location_name."'*'".$txt_ord_rec_id."'*'".$txt_ord_rec_no."'*'".$cbo_supplier_id."'*'".$cbo_currency."'*'".$cbo_paymode_id."'*'".$cbo_source_id."'*'".$txt_exchange_rate."'*'".$txt_wo_date."'*'".$txt_delivery_date."'*'".$txt_attention."'*'".$txt_remarks."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		
		//$txt_job_no=$new_job_no[0];
		
		$field_array2="order_quantity*order_uom*rate*amount*order_rcv_dtls_id*order_rcv_id*remarks*updated_by*update_date";
		$field_array3="id, mst_id, job_no_mst, order_quantity, order_uom, rate, amount, section, sub_section, item_group, order_rcv_dtls_id, order_rcv_id,remarks, inserted_by, insert_date";
		$field_array4="id, mst_id, job_no_mst, description, color_id, size_id, qnty, rate, amount, order_rcv_break_id, order_rcv_id, inserted_by, insert_date";
		$field_array5="description*color_id*size_id*qnty*rate*amount*updated_by*update_date";

		$color_library_arr=return_library_array( "select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
		$size_library_arr=return_library_array( "select id, size_name from lib_size where status_active=1 and is_deleted=0", "id", "size_name");
		$data_array2 = $data_array3= $data_array4= $data_array5="";  $add_commaa=0; $add_commadtls=0; $new_array_color=array();  $new_array_size=array();

		for($i=1; $i<=$total_row; $i++)
		{			
			//$cboSection				= "cboSection_".$i;
			//$cboSubSection			= "cboSubSection_".$i;
			//$cboItemGroup			= "cboItemGroup_".$i;
			$txtOrderQuantity		= "txtOrderQuantity_".$i;
			$cboUom 				= "cboUom_".$i;
			$txtRate 				= "txtRate_".$i;
			$txtAmount 				= "txtAmount_".$i;			
			$txtRemarks 			= "txtRemarks_".$i;	
			$hdnDtlsdata 			= "hdnDtlsdata_".$i;
			$hdnDtlsUpdateId 		= "hdnDtlsUpdateId_".$i;
			$hdnWoDtlsIds 			= "hdnWoDtlsIds_".$i;
			$hdnRcvId 				= "hdnRcvId_".$i;
			$hdnWoDtlsId =chop($$hdnWoDtlsIds ,',');
			$txtAmount=str_replace(",",'',$$txtAmount);

			if(str_replace("'",'',$$hdnDtlsUpdateId)!="" )
			{
				$data_array2[str_replace("'",'',$$hdnDtlsUpdateId)]=explode("*",("".str_replace(",",'',$$txtOrderQuantity)."*'".str_replace("'",'',$$cboUom)."'*".str_replace("'",'',$$txtRate)."*'".str_replace("'",'',$txtAmount)."'*".str_replace("'",'',$$hdnWoDtlsId)."*".str_replace("'",'',$$hdnRcvId)."*".$$txtRemarks."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
				$hdn_dtls_id_arr[]=str_replace("'",'',$$hdnDtlsUpdateId);
			}
			else
			{
				if ($add_commaa!=0) $data_array3 .=","; $add_comma=0;

				$data_array3 .="(".$id1.",".$id.",'".$new_job_no[0]."',".str_replace(",",'',$$txtOrderQuantity).",".$$cboUom.",".$$txtRate.",".str_replace(",",'',$$txtAmount).",".$$cboSection.",".$$cboSubSection.",".$$cboItemGroup.",".$hdnWoDtlsId.",".str_replace("'",'',$$hdnRcvId).",".$$txtRemarks.", ".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				$id1++;
			}
			//echo '10**'.$data_array5; //die;
			$dtls_data=explode("***",chop(str_replace("'",'',$$hdnDtlsdata),'***'));
				
			for($j=0; $j<count($dtls_data); $j++)
			{

				$exdata=explode("_",$dtls_data[$j]);
			
				$description="'".$exdata[0]."'";
				$colorname="'".$exdata[1]."'";
				$sizename="'".$exdata[2]."'";
				$qty="'".str_replace(",",'',$exdata[3])."'";
				$booked_qty=str_replace(",",'',$exdata[3])*str_replace("'",'',$$txtConvFactor);
				$rate="'".str_replace(",",'',$exdata[4])."'";
				$amount="'".str_replace(",",'',$exdata[5])."'";
				$book_con_dtls_id="'".$exdata[6]."'";
				$rcvBrkId="'".$exdata[7]."'";
				$brkUpdateId="'".$exdata[8]."'";
				$style="'".$exdata[9]."'";
				//echo "10**".$brkUpdateId."==";
				/***********Save***********/
				//$data_dreak_arr[$rows[csf('job_no_mst')]][$rows[csf('section')]][$rows[csf('sub_section')]][$rows[csf('item_group')]][$rows[csf('order_uom')]].=$rows[csf('description')].'_'.$color_library[$rows[csf('color_id')]].'_'.$size_arr[$rows[csf('size_id')]].'_'.$rows[csf('qnty')].'_'.$rows[csf('rate')].'_'.$rows[csf('amount')].'_'.$rows[csf('book_con_dtls_id')].'_'.$rows[csf('id')].'_'.$rows[csf('is_revised')].'***';
				
				/***********Update***********/
				//$data_dreak_arr[$rows[csf('dtls_id')]].=$rows[csf('description')].'_'.$color_library[$rows[csf('color_id')]].'_'.$size_arr[$rows[csf('size_id')]].'_'.$rows[csf('qnty')].'_'.$rows[csf('rate')].'_'.$rows[csf('amount')].'_0_'.$rows[csf('order_rcv_break_id')].'_'.$rows[csf('id')].'***';
				
				if (str_replace("'", "", trim($colorname)) != "") {
					if (!in_array(str_replace("'", "", trim($colorname)),$new_array_color)){
						$color_id = return_id( str_replace("'", "", trim($colorname)), $color_library_arr, "lib_color", "id,color_name","450");
						$new_array_color[$color_id]=str_replace("'", "", trim($colorname));
					}
					else $color_id =  array_search(str_replace("'", "", trim($colorname)), $new_array_color);
				} else $color_id = 0;
				
				if(str_replace("'","",$sizename)!="")
				{ 
					if (!in_array(str_replace("'","",$sizename),$new_array_size))
					{
						$size_id = return_id( str_replace("'","",$sizename), $size_library_arr, "lib_size", "id,size_name","450");  
						//echo $$txtColorName.'='.$color_id.'<br>';
						$new_array_size[$size_id]=str_replace("'","",$sizename);
					}
					else $size_id =  array_search(str_replace("'","",$sizename), $new_array_size); 
				}
				else
				{
					$size_id=0;
				}
				//echo "10**".str_replace("'","",$brkUpdateId).'==';
				if(str_replace("'","",$brkUpdateId)==0 || str_replace("'","",$brkUpdateId)=='')
				{
					//echo "10**".str_replace("'","",$brkUpdateId).'==';
					if ($add_commadtls!=0) $data_array3 .=",";
					$data_array4.="(".$id3.",".$id1.",'".$txt_wo_no."',".$description.",'".$color_id."','".$size_id."',".$qty.",".$rate.",".$amount.",".$rcvBrkId.",".str_replace("'",'',$$hdnRcvId).", ".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
					$id3=$id3+1; $add_commadtls++;
			
					//if(str_replace("'",'',$$hdnDtlsUpdateId)!='') $dtlsIdForBreak=str_replace("'",'',$$hdnDtlsUpdateId); else $dtlsIdForBreak=$dtlsIdForBreak;
					
				}
				else
				{
					$data_array5[str_replace("'","",$brkUpdateId)]=explode("*",("".$description."*".$color_id."*".$size_id."*".$qty."*".$rate."*".$amount."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
					$hdn_break_id_arr[]		=str_replace("'","",$brkUpdateId);
				}
				
			}
		}
		//echo '10**mmmmmmmmmm'.$data_array; die;
		$recv_id_mst=array_unique(explode(",",chop($txt_ord_rec_id,',')));
		$prev_recv_id_mst=array_unique(explode(",",chop($txt_prev_ord_rec_id,',')));
		$deleted_rcv_ids=array_diff($prev_recv_id_mst,$recv_id_mst);
		
		$deleted_rcv_id=implode(",",$deleted_rcv_ids);
		$flag=1; $user_id=$_SESSION['logic_erp']['user_id'];
		if($deleted_rcv_id!='')
		{
			$rID5=execute_query("UPDATE trims_subcon_ord_dtls SET updated_by=$user_id, update_date='$pc_date_time', status_active=0, is_deleted=1 WHERE order_rcv_id in ($deleted_rcv_id) and mst_id=$update_id");
			if($rID5) $flag=1; else $flag=0; 

			$rID6=execute_query("UPDATE trims_subcon_ord_breakdown SET updated_by=$user_id, update_date='$pc_date_time', status_active=0, is_deleted=1 WHERE order_rcv_id in ($deleted_rcv_id) and job_no_mst=$txt_wo_no");
			if($rID6) $flag=1; else $flag=0; 
			 
		}

		if($flag==1){
			//echo "10**".$field_array."**".$data_array."**".$update_id; die;
			$rID=sql_update("trims_subcon_ord_mst",$field_array,$data_array,"id",$update_id,0);  
			if($rID) $flag=1; else $flag=0;
		}
		//echo '10**mmmmmmmmmm'.$deleted_rcv_id; die;
		if($data_array2!=""){
			//echo "10**".bulk_update_sql_statement( "trims_subcon_ord_dtls", "id",$field_array2,$data_array2,$hdn_dtls_id_arr); die;
			$rID1=execute_query(bulk_update_sql_statement( "trims_subcon_ord_dtls", "id",$field_array2,$data_array2,$hdn_dtls_id_arr),1);
			if($rID1) $flag=1; else $flag=0;
		}

		if($data_array3!="" && $flag==1 && $rID==1){
			//echo "10**INSERT INTO trims_subcon_ord_dtls (".$field_array3.") VALUES ".$data_array3; die;
			$rID2=sql_insert("trims_subcon_ord_dtls",$field_array3,$data_array3,1);
			if($rID2==1) $flag=1; else $flag=0;
		}

		if($data_array4!="" &&  $flag==1 && ($rID1==1 || $rID2==1 )){
			$rID3=sql_insert("trims_subcon_ord_breakdown",$field_array4,$data_array4,1);
			if($rID3==1) $flag=1; else $flag=0;
		}

		if($data_array5!=""){
			//echo "10**".bulk_update_sql_statement( "trims_subcon_ord_breakdown", "id",$field_array5,$data_array5,$hdn_break_id_arr); die;
			$rID4=execute_query(bulk_update_sql_statement( "trims_subcon_ord_breakdown", "id",$field_array5,$data_array5,$hdn_break_id_arr),1);
			if($rID4) $flag=1; else $flag=0;
		}
		//echo "10**".$rID."**".$rID1."**".$rID2."**".$rID3."**".$rID4."**".$rID5."**".$rID6."**".$flag; die;

		//$rIDBooking=execute_query( "update wo_booking_mst set lock_another_process=1 where booking_no ='".$txt_order_no."'",1);
		//if($rIDBooking==1) $flag=1; else $flag=0;
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'",'',$txt_wo_no)."**".str_replace("'",'',$update_id);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**";
			}
		}
		else if($db_type==2)
		{
			if($rID)
			{
				oci_commit($con);
				echo "1**".str_replace("'",'',$txt_wo_no)."**".str_replace("'",'',$update_id);
			}
			else
			{
				oci_rollback($con);
				echo "10**";
			}
		}
		disconnect($con);
		die; 
	}
	else if ($operation==2)   // delete here
	{

		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");  
		}
		
		/*$next_process=return_field_value( "trims_job", "trims_job_card_mst"," entry_form=257 and $update_id=received_id and status_active=1 and is_deleted=0");
		if($next_process!=''){
			echo "20**".str_replace("'",'',$txt_job_no)."**".str_replace("'",'',$update_id)."**".str_replace("'",'',$txt_order_no)."**".$next_process;
			disconnect($con);
			die;
		}*/
		$update_id=str_replace("'",'',$update_id);
		$flag=1;
		$field_array="status_active*is_deleted*updated_by*update_date";
		$data_array="0*1*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"; 

		if($flag==1){
			//echo "10**".$field_array."**".$data_array."**".$update_id; die;
			$rID=sql_update("trims_subcon_ord_mst",$field_array,$data_array,"id",$update_id,0);  
			if($rID) $flag=1; else $flag=0;
			//echo "10**".$rID."**".$flag."**".$update_id; die;
		}

		if($flag==1){
			//echo "10**".$field_array."**".$data_array."**".$update_id; die;
			$rID1=sql_update("trims_subcon_ord_dtls",$field_array,$data_array,"mst_id",$update_id,0);  
			if($rID1) $flag=1; else $flag=0;
		}

		if($flag==1){
			//echo "10**".$field_array."**".$data_array."**".$update_id; die;
			$rID2=sql_update("trims_subcon_ord_breakdown",$field_array,$data_array,"job_no_mst","'".$txt_wo_no."'",0);  
			if($rID2) $flag=1; else $flag=0;
		}



		/*if(str_replace("'",'',$cbo_within_group)==1)
		{
			if($flag==1)
			{
				$rID3=execute_query( "update wo_booking_mst set lock_another_process=0 where booking_no =$order_no",1);
				if($rID3) $flag=1; else $flag=0; 
			} 
		}*/
		//echo "10**".$rID."**".$rID1."**".$rID2."**".$rID3."**".$flag; die;
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "2**".str_replace("'",'',$txt_wo_no)."**".str_replace("'",'',$update_id);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**";
			}
		}
		else if($db_type==2)
		{
			if($rID)
			{
				oci_commit($con);
				echo "2**".str_replace("'",'',$txt_wo_no)."**".str_replace("'",'',$update_id);
			}
			else
			{
				oci_rollback($con);
				echo "10**";
			}
		}
		disconnect($con);
		die; 
	}
}

function sql_updates($strTable,$arrUpdateFields,$arrUpdateValues,$arrRefFields,$arrRefValues, $commit)
{
	$strQuery = "UPDATE ".$strTable." SET ";
	$arrUpdateFields=explode("*",$arrUpdateFields);
	$arrUpdateValues=explode("*",$arrUpdateValues);


	if(is_array($arrUpdateFields))
	{
		$arrayUpdate = array_combine($arrUpdateFields,$arrUpdateValues);
		$Arraysize = count($arrayUpdate);
		$i = 1;
		foreach($arrayUpdate as $key=>$value):
			$strQuery .= ($i != $Arraysize)? $key."=".$value.", ":$key."=".$value." WHERE ";
			$i++;
		endforeach;
	}
	else
	{
		$strQuery .= $arrUpdateFields."=".$arrUpdateValues." WHERE ";
	}

	//$arrRefFields=explode("*",$arrRefFields);
	//$arrRefValues=explode("*",$arrRefValues);
	$strQuery .= $arrRefFields." in (".$arrRefValues.")";
	 echo "10**".$strQuery;die;
    global $con;
	$stid =  oci_parse($con, $strQuery);
	$exestd=oci_execute($stid,OCI_NO_AUTO_COMMIT);
	if ($exestd)
		return "1";
	else
		return "0";

	die;
	$_SESSION['last_query']=$_SESSION['last_query'].";;".$strQuery;
	if ($commit==1)
	{
		if (!oci_error($stid))
		{

		$pc_time= add_time(date("H:i:s",time()),360);
		$pc_date_time = date("d-M-Y h:i:s",strtotime(add_time(date("H:i:s",time()),360)));
	    $pc_date = date("d-M-Y",strtotime(add_time(date("H:i:s",time()),360)));

		$strQuery= "INSERT INTO activities_history ( session_id,user_id,ip_address,entry_time,entry_date,module_name,form_name,query_details,query_type) VALUES ('".$_SESSION['logic_erp']["history_id"]."','".$_SESSION['logic_erp']["user_id"]."','".$_SESSION['logic_erp']["pc_local_ip"]."','".$pc_time."','".$pc_date."','".$_SESSION["module_id"]."','".$_SESSION['menu_id']."','".encrypt($_SESSION['last_query'])."','1')";

		mysql_query("SET character_set_results = 'utf8', character_set_client = 'utf8', character_set_connection = 'utf8', character_set_database = 'utf8', character_set_server = 'utf8'");

		$resultss=oci_parse($con, $strQuery);
		oci_execute($resultss);
		$_SESSION['last_query']="";
		oci_commit($con);
		return "0";
		}
		else
		{
			oci_rollback($con);
			return "10";
		}
	}
	else return 1;
	die;
}

if($action=="trims_subcon_wo_print")
{
	//select id, item_name from lib_item_group where item_category=4 and status_active=1
	extract($_REQUEST);
	$data=explode('*',$data);
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name" );
	$size_arr=return_library_array( "select id,size_name from lib_size",'id','size_name');
	//$order_receive_arr=return_library_array( "select id,subcon_job from subcon_ord_mst",'id','subcon_job');
	$trim_group_arr=return_library_array( "select id, item_name from lib_item_group where item_category=4",'id','item_name');
	$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
	$store_name_arr=return_library_array( "select id, store_name from lib_store_location",'id','store_name');
	$source_for_order = array(1 => 'In-House', 2 => 'Sub-Contract');
	$image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$data[0]'","image_location");
	
	/*$rcv_qty_result=sql_select( "select a.order_rcv_id , a.wo_id , a.wo_dtls_id, a.wo_break_id, a.rcv_qty from trims_receive_dtls a  where entry_form =451 and mst_id !=$data[1] and a.status_active=1 and a.is_deleted=0");		
	foreach ($rcv_qty_result as  $row) 
	{
		//echo $mstIDS.'=='.$ids.'=='.$book_con_dtls_ids.'++'; 
		$rec_qty_arr[$row[csf("order_rcv_id")]][$row[csf("wo_break_id")]]["cum_qty"] +=$row[csf("rcv_qty")];
	}*/

	/*$brk_qry_result=sql_select( "select a.id, a.mst_id, a.order_id, a.job_no_mst, a.book_con_dtls_id, a.description, a.color_id, a.size_id, a.qnty, a.rate, a.amount,a.is_revised,a.style,b.order_uom, b.section, b.sub_section, b.item_group from subcon_ord_breakdown a , subcon_ord_dtls b where a.job_no_mst=b.job_no_mst and b.id=a.mst_id and a.id in ($hdnRcvBrkIds) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by a.id");	
	$data_dreak_arr=array(); $data_dreak=''; $add_comma=0; $k=1; $attachedBrkQty=0; $balanceBrkQty=0;
	foreach ($brk_qry_result as $rows)
	{
		if(trim($rows[csf("description")])=="") $rows[csf('description')]=0;
		if($rows[csf('color_id')]=="") $rows[csf('color_id')]=0;
		if($rows[csf('size_id')]=="") $rows[csf('size_id')]=0;
		if($rows[csf('qnty')]=="") $rows[csf('qnty')]=0;
		if($rows[csf('rate')]=="") $rows[csf('rate')]=0;
		if($rows[csf('amount')]=="") $rows[csf('amount')]=0;
		if($rows[csf('book_con_dtls_id')]=="") $rows[csf('book_con_dtls_id')]=0;
		
		$k++;
		if($bookConsIds_arr[$rows[csf('book_con_dtls_id')]]=="") $bookConsIds_arr[$rows[csf('book_con_dtls_id')]]=0;
		//echo $rows[csf("job_no_mst")].'=='.$rows[csf("mst_id")].'=='.$rows[csf("section")].'=='.$rows[csf("sub_section")].'=='.$rows[csf("item_group")].'=='.$rows[csf("order_uom")].'=='.trim($rows[csf("description")]).'=='.$rows[csf("color_id")].'=='.$rows[csf("size_id")].'++';
		

		$data_dreak_arr[$rows[csf('id')]]['description']= trim($rows[csf("description")]);
		$data_dreak_arr[$rows[csf('id')]]['color']= $color_arr[$rows[csf('color_id')]];
		$data_dreak_arr[$rows[csf('id')]]['color_id']= $rows[csf('color_id')];
		$data_dreak_arr[$rows[csf('id')]]['size']= $size_arr[$rows[csf('size_id')]];
		$data_dreak_arr[$rows[csf('id')]]['size_id']= $rows[csf('size_id')];
		$data_dreak_arr[$rows[csf('id')]]['style']= $rows[csf('style')];		
	}*/

	//echo "select a.id, a.job_no_mst,c.style, c.description, c.color_id, c.size_id, a.qnty, a.rate, a.amount, a.order_rcv_break_id, a.order_rcv_id ,b.id as dtls_id ,b.order_quantity as wo_qnty, b.order_uom, b.section, b.sub_section, b.item_group  as trim_group , b.order_rcv_dtls_id, b.order_rcv_id,b.remarks, a.mst_id  from trims_subcon_ord_breakdown a , trims_subcon_ord_dtls b,subcon_ord_breakdown c where a.job_no_mst=b.job_no_mst and b.id=a.mst_id and b.mst_id =$data[1] and a.order_rcv_break_id=c.id  and a.qnty<>0 and b.order_quantity<>0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 order by a.id";
	$qry_result=sql_select( "select a.id, a.job_no_mst,c.style, c.description, c.color_id, c.size_id, a.qnty, a.rate, a.amount, a.order_rcv_break_id, a.order_rcv_id ,b.id as dtls_id ,b.order_quantity as wo_qnty, b.order_uom, b.section, b.sub_section, b.item_group  as trim_group , b.order_rcv_dtls_id, b.order_rcv_id,b.remarks, a.mst_id  from trims_subcon_ord_breakdown a , trims_subcon_ord_dtls b,subcon_ord_breakdown c where a.job_no_mst=b.job_no_mst and b.id=a.mst_id and b.mst_id =$data[1] and a.order_rcv_break_id=c.id  and a.qnty<>0 and b.order_quantity<>0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 order by a.id");

	foreach ($qry_result as  $row) 
	{
		//echo $mstIDS.'=='.$ids.'=='.$book_con_dtls_ids.'++'; 
		$wo_arr[$row[csf("order_rcv_id")]][$row[csf("section")]][$row[csf("sub_section")]][$row[csf("trim_group")]][$row[csf("style")]][$row[csf("description")]][$row[csf("color_id")]][$row[csf("size_id")]][$row[csf("order_uom")]][$row[csf("id")]]["qnty"] =$row[csf("qnty")];
		$wo_arr[$row[csf("order_rcv_id")]][$row[csf("section")]][$row[csf("sub_section")]][$row[csf("trim_group")]][$row[csf("style")]][$row[csf("description")]][$row[csf("color_id")]][$row[csf("size_id")]][$row[csf("order_uom")]][$row[csf("id")]]["rate"] =$row[csf("rate")];
		$wo_arr[$row[csf("order_rcv_id")]][$row[csf("section")]][$row[csf("sub_section")]][$row[csf("trim_group")]][$row[csf("style")]][$row[csf("description")]][$row[csf("color_id")]][$row[csf("size_id")]][$row[csf("order_uom")]][$row[csf("id")]]["amount"] =$row[csf("amount")];
		$wo_arr[$row[csf("order_rcv_id")]][$row[csf("section")]][$row[csf("sub_section")]][$row[csf("trim_group")]][$row[csf("style")]][$row[csf("description")]][$row[csf("color_id")]][$row[csf("size_id")]][$row[csf("order_uom")]][$row[csf("id")]]["remarks"] =$row[csf("remarks")];
		$wo_arr[$row[csf("order_rcv_id")]][$row[csf("section")]][$row[csf("sub_section")]][$row[csf("trim_group")]][$row[csf("style")]][$row[csf("description")]][$row[csf("color_id")]][$row[csf("size_id")]][$row[csf("order_uom")]][$row[csf("id")]]["order_rcv_break_id"] =$row[csf("order_rcv_break_id")];
		$remarks_arr[$row[csf("order_rcv_id")]][$row[csf("section")]][$row[csf("sub_section")]][$row[csf("trim_group")]]["remarks"] .=$row[csf("remarks")].'#';
	}

	$sql_mst="select a.id as wo_id ,a.subcon_job as wo_no ,a.job_no_prefix ,a.job_no_prefix_num ,a.company_id ,a.location_id ,a.order_rcv_id, a.order_rcv_no ,a.supplier_id ,a.currency_id ,a.paymode_id ,a.source_id ,a.exchange_rate ,a.wo_date ,a.delivery_date ,a.attention,a.remarks from trims_subcon_ord_mst a where  a.entry_form=450 and a.company_id=$data[0] and a.id='$data[1]' and a.status_active=1 and a.is_deleted=0";
	
	$dataArray=sql_select($sql_mst);
	$orderRcvId=$dataArray[0][csf('order_rcv_id')];

	//echo "SELECT a.mst_id, a.job_no_mst, a.buyer_style_ref, a.section, b.style from subcon_ord_dtls, subcon_ord_breakdown b  where a.id=b.mst_id and a.job_no_mst=b.job_no_mst and a.mst_id in ($orderRcvId) and a.status_active=1 and a.is_deleted=0 order by a.id ASC";
	$ord_rcv_sql = "SELECT a.mst_id, a.job_no_mst, a.buyer_style_ref, a.section, b.style,b.id from subcon_ord_dtls a, subcon_ord_breakdown b where a.id=b.mst_id and a.job_no_mst=b.job_no_mst and a.mst_id in ($orderRcvId) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by a.id ASC";
	$ord_rcvArray=sql_select($ord_rcv_sql);
	foreach ($ord_rcvArray as  $row) 
	{
		//echo $mstIDS.'=='.$ids.'=='.$book_con_dtls_ids.'++'; 
		$order_receive_arr[$row[csf("mst_id")]] =$row[csf("job_no_mst")];
		$order_receive_style_arr[$row[csf("mst_id")]][$row[csf("section")]][$row[csf("id")]]["buyer_style_ref"] .=$row[csf("style")].'#';
	}
	//echo "<pre>";
	//print_r($order_receive_style_arr);
	//die;

	$section_rowspan_arr=array(); $sub_section_rowspan_arr=array(); $trim_group_rowspan_arr=array();  $style_rowspan_arr=array(); $description_rowspan_arr=array(); $color_rowspan_arr=array(); $size_rowspan_arr=array(); $order_uom_rowspan_arr=array(); $id_rowspan_arr=array();
    foreach($wo_arr as $rcv_id=> $rcv_id_data)
	{
		foreach($rcv_id_data as $section_id=> $section_id_data)
		{
			$section_rowspan=0;	
			foreach($section_id_data as $sub_section_id=> $sub_section_data)
			{
				$sub_section_rowspan=0;	
				foreach($sub_section_data as $trim_group_id=> $trim_group_id_data)
				{
					$trim_group_rowspan=1;
					foreach($trim_group_id_data as $style=> $style_data)
					{
						$style_rowspan=0;
						foreach($style_data as $description=> $description_data)
						{
							$description_rowspan=0;	
							foreach($description_data as $color_id=> $color_id_data)
							{
								$color_id_rowspan=0;
								foreach($color_id_data as $size_id=> $size_id_data)
								{
									$size_id_rowspan=0;	
									foreach($size_id_data as $order_uom=> $order_uom_data)
									{
										$id_rowspan=0;	
										foreach($order_uom_data as $id=> $row)
										{
											$section_rowspan++;
											$sub_section_rowspan++;
											$trim_group_rowspan++;
											$style_rowspan++;
											$description_rowspan++;
											$color_id_rowspan++;
											$size_id_rowspan++;
											$id_rowspan++;
										}
										$id_rowspan_arr[$rcv_id][$section_id][$sub_section_id][$trim_group_id][$style][$description][$color_id][$size_id][$id]=$id_rowspan;
									}
									$size_rowspan_arr[$rcv_id][$section_id][$sub_section_id][$trim_group_id][$style][$description][$color_id][$size_id]=$size_id_rowspan;
								}
								$color_rowspan_arr[$rcv_id][$section_id][$sub_section_id][$trim_group_id][$style][$description][$color_id]=$color_id_rowspan;
							}
							$description_rowspan_arr[$rcv_id][$section_id][$sub_section_id][$trim_group_id][$style][$description]=$description_rowspan;
						}
						$style_rowspan_arr[$rcv_id][$section_id][$sub_section_id][$trim_group_id][$style]=$style_rowspan;
					}
					$trim_group_rowspan_arr[$rcv_id][$section_id][$sub_section_id][$trim_group_id]=$trim_group_rowspan;
					$sub_section_rowspan++; $section_rowspan++;
				}
				$sub_section_rowspan_arr[$rcv_id][$section_id][$sub_section_id]=$sub_section_rowspan;
				
			}
			$section_rowspan_arr[$rcv_id][$section_id]=$section_rowspan;
		}
	}
	//echo "<pre>";
	//print_r($trim_group_rowspan_arr);
	?>
	<div style="width:1060px;">
		<table width="1060" cellspacing="0" align="center" border="0">
			<tr rowspan="2">
				<td colspan="6"></td>
			</tr>
			<tr>
				<td colspan="1" rowspan="3" style="width: 100px;">
					<img src="../../<? echo $image_location; ?>" height="60" width="200" style="float:left;">
				</td>
				<td colspan="2" align="center" style="font-size:x-large">
					<strong><? echo $company_library[$data[0]]; ?></strong>
				</td>
				<td colspan="3" align="center"></td>
			</tr>
			<tr>
				<td colspan="2" align="center">
					<?
					$nameArray=sql_select( "select plot_no, level_no, road_no, block_no, country_id, province, city, zip_code, email, website, vat_number from lib_company where id=$data[0] and status_active=1 and is_deleted=0"); 
					foreach ($nameArray as $result)
					{ 
						?>
						<? echo $result[csf('plot_no')]; ?>,
						Level No: <? echo $result[csf('level_no')]?>,
						<? echo $result[csf('road_no')]; ?>, 
						<? echo $result[csf('block_no')];?>, 
						<? echo $result[csf('city')];?>, 
						<? echo $result[csf('zip_code')]; ?>, 
						<?php echo $result[csf('province')];?>, 
						<? echo $country_arr[$result[csf('country_id')]]; ?><br> 
						Email Address: <? echo $result[csf('email')];?> 
						Website No: <? echo $result[csf('website')];?> <br>
						<b> Vat No : <? echo $result[csf('vat_number')]; ?></b> <?
							//$party_id= $result[csf('party_id')];
					}

					?> 
				</td>
				<td colspan="3" align="center"></td>
			</tr>
			<tr>
	            <td colspan="2" style="font-size:x-large; text-align:center;" align="center"><strong ><? echo "Accessories Work Order"; ?></strong> </td>
	            <td colspan="3" align="center"></td>
	        </tr>
			<tr><td colspan="6">&nbsp;</td></tr>
		</table>
		<table width="1060" cellspacing="0" align="center" border="0">
			<tr style="" >
				<td width="100">Supplier</td> 
				<td width="350" ><? echo " : ".$supplier_arr[$dataArray[0][csf('supplier_id')]]; ?></td>
				<td width="140"><strong>Work Order No.</strong></td>
				<td width="250" > <strong> <? echo " : ".$dataArray[0][csf('wo_no')];  ?></strong></td>
				<td width="70">Wo Date</td>
				<td><?  echo " : ".change_date_format($dataArray[0][csf('wo_date')]); ?></td>
			</tr>
			<tr>
				<td>Delivery Date</td>
				<td ><? echo " : ".change_date_format($dataArray[0][csf('delivery_date')]); ?></td>
				<td >Attention</td>
				<td ><? echo " : ".$dataArray[0][csf('attention')]; ?></td>
				
				<td >Currency</td>
				<td ><? echo " : ".$currency[$dataArray[0][csf('currency_id')]];  ?></td>
			</tr>
			<tr>
				<td >Pay Mode</td>
				<td  colspan="5"><? echo " : ".$pay_mode[$dataArray[0][csf('paymode_id')]];  ?></td>
			</tr>
			<tr>
				<td >Remarks</td> 
				<td colspan="5" style="word-break: break-all;" ><p><? echo " : ".$dataArray[0][csf('remarks')]; ?></p></td>
			</tr>
		</table>
		<br>
		<div style="width:100%;">
			<table align="left" cellspacing="0" width="1060"  border="1" rules="all" class="rpt_table"  >
				<?
				$tblRow=1; $i=1; $total_amount=0;
				foreach($wo_arr as $rcv_id=> $rcv_id_data)
				{
					?>
					<thead>
						<tr bgcolor="<? echo $bgcolor; ?>" id="row_<? echo $tblRow; ?>" align="center">
							<th colspan="12"  style="text-align:left ">Order No : <? echo $order_receive_arr[$rcv_id];?></th>
						</tr>
						<tr>
			        		<th width="90">Section</th>
			                <th width="90" >Sub Section</th>
			                <th width="110">Trims Group</th>
			                <th width="150">Style</th>
			                <th width="120">Item Description</th>
			                <th width="130">Item Color</th>
			                <th width="90">Item Size</th>
			                <th width="60">Order UOM</th>
			                <th width="80">WO Qty</th>
			                <th width="80">Rate(<? echo $currency[$dataArray[0][csf('currency_id')]];  ?>)</th>
			                <th width="80">Amount</th>
			                <th >Reamrks</th>
			        	</tr>
					</thead>
					<tbody>
					<?
					foreach($rcv_id_data as $section_id=> $section_id_data)
					{
						$section_rowspan=0;
						foreach($section_id_data as $sub_section_id=> $sub_section_data)
						{
							$sub_section_rowspan=0;	
							foreach($sub_section_data as $trim_group_id=> $trim_group_id_data)
							{
								$trim_group_rowspan=1; $uom_wise_qnty=0; $uom_wise_amount=0;
								foreach($trim_group_id_data as $style=> $style_data)
								{
									$style_rowspan=0;
									foreach($style_data as $description=> $description_data)
									{
										$description_rowspan=0;
										foreach($description_data as $color_id=> $color_id_data)
										{
											$color_id_rowspan=0;
											foreach($color_id_data as $size_id=> $size_id_data)
											{
												$size_id_rowspan=0;	 $uom_wise_rcv_qty=0;
												foreach($size_id_data as $order_uom=> $order_uom_data)
												{
													foreach($order_uom_data as $id=> $row)
													{
														$qnty=$row['qnty'];
														$uom_wise_qnty +=$qnty;
														$rate=$row['rate'];
														$amount=$row['amount'];
														$uom_wise_amount +=$amount;
														$total_amount +=$amount;
														?>
														<tr bgcolor="<? echo $bgcolor; ?>" id="row_<? echo $tblRow; ?>" align="left">
															<? if($section_rowspan==0){ ?> <td rowspan="<? echo $section_rowspan_arr[$rcv_id][$section_id]; ?>"><p><?  echo $trims_section[$section_id] ; ?></p></td><? } ?>
															<? if($sub_section_rowspan==0){ ?> <td rowspan="<? echo $sub_section_rowspan_arr[$rcv_id][$section_id][$sub_section_id]; ?>"><p><?  echo $trims_sub_section[$sub_section_id] ; ?></p></td><? } ?>
															<? if($trim_group_rowspan==1){ ?> 
																<td rowspan="<? echo $trim_group_rowspan_arr[$rcv_id][$section_id][$sub_section_id][$trim_group_id]; ?>"><p><?  echo $trim_group_arr[$trim_group_id] ; ?></p></td>
															<? } ?>
															<? if($style_rowspan==0){ ?> 
																<td style="border:1px solid black" rowspan="<? echo $style_rowspan_arr[$rcv_id][$section_id][$sub_section_id][$trim_group_id][$style]; ?>"><p><? echo $style;
																//$buyer_style_ref=implode(",",array_unique(explode("#",$order_receive_style_arr[$rcv_id][$section_id][$row['order_rcv_break_id']]["buyer_style_ref"])));
															 	 ?></p></td>
															<? } ?>
															<? if($description_rowspan==0){ ?> 
															 	<td style="border:1px solid black" rowspan="<? echo $description_rowspan_arr[$rcv_id][$section_id][$sub_section_id][$trim_group_id][$style][$description]; ?>"><p><?  echo $description ; ?></p></td>
															<? } ?>
															<? if($color_id_rowspan==0){ ?> <td style="border:1px solid black" rowspan="<? echo $color_rowspan_arr[$rcv_id][$section_id][$sub_section_id][$trim_group_id][$style][$description][$color_id]; ?>"><p><?  echo $color_library[$color_id] ; ?></p></td><? } ?>
															<? if($size_id_rowspan==0){ ?> <td style="border:1px solid black" rowspan="<? echo $size_rowspan_arr[$rcv_id][$section_id][$sub_section_id][$trim_group_id][$style][$description][$color_id][$size_id]; ?>"><p><?  echo $size_arr[$size_id] ; ?></p></td><? } ?>
															<td><p><?  echo $unit_of_measurement[$order_uom] ; ?></p></td>
															<td><p align="right"><?  echo number_format($qnty,4); ?></p></td>
															<td><p align="right"><?  echo number_format($rate,4) ; ?></p></td>
															<td><p align="right"><?  echo number_format($amount,2) ; ?></p></td>
															<? if($trim_group_rowspan==1){ ?> <td style="border:1px solid black" rowspan="<? echo $trim_group_rowspan_arr[$rcv_id][$section_id][$sub_section_id][$trim_group_id]; ?>" ><p><? 
																$remarks=implode(",",array_unique(explode("#",$remarks_arr[$rcv_id][$section_id][$sub_section_id][$trim_group_id]["remarks"])));
															 	echo chop($remarks,',') ; ?></p></td><? } ?>
														</tr>
														<?
														$tblRow++; $section_rowspan++; $sub_section_rowspan++; $trim_group_rowspan++; $description_rowspan++; $color_id_rowspan++; $size_id_rowspan++; $style_rowspan++;

													}
												}
											}
										}
									}
								}
								?> <tr>
									<td colspan="5" align="right"><strong>UOM Total:</strong></td>
									<td align="right"><p><strong><?  echo number_format($uom_wise_qnty,4); ?></strong></p></td>
									<td >&nbsp;</td>
									<td align="right"><p><strong><?  echo number_format($uom_wise_amount,2); ?></strong></p></td>
									</tr><? 
								$section_rowspan++; $sub_section_rowspan++;
							}
						}
					}
				}

				$currency_sign_arr=array(1=>'৳',2=>'$',3=>'€',4=>'€',5=>'$',6=>'£',7=>'¥');
				$currency_sign = $currency_sign_arr[$dataArray[0][csf("currency_id")]];
				$currency_id = $dataArray[0][csf("currency_id")];
				//$mcurrency, $dcurrency;
				$dcurrency="";
				if($currency_id==1){
					$mcurrency='Taka';
					$dcurrency='Paisa';
				}else if($currency_id==2){
					$mcurrency='USD';
					$dcurrency='CENTS';
				}else if($currency_id==3){
					$mcurrency='EURO';
					$dcurrency='CENTS';
				}
				
				?>
				</table>
			</div>
		<br>
		<div style="width: 1060px ;  margin-top:5px; float: left;"  align="left">
			<table style="border: 1px;">
				<tr>
					<td><p><strong>Total Work Order Amount (In Word) : <? echo number_to_words(number_format($total_amount,2), $mcurrency, $dcurrency);?></strong></p></td>
				</tr>
			</table>
		</div>
		<br>
		<div style="width: 1060px ;  margin-top:5px; float: left;"  align="left">
			<? 
			   	echo get_spacial_instruction($data[1],"100%",450);
			?>
		</div>
		<br>
	</div>
	<div style="width: 1060px ;  margin-top:5px; float: left;"  align="left">
		<? 
		   echo signature_table(210, $data[0], "1060px");
		?>
	</div>
</div>
<?
}

 
/*if ($action=="load_php_data_to_form")
{
	$data=implode(",",array_unique(explode(",",$data)));
	$nameArray=sql_select( "select a.id, a.subcon_job, a.company_id, a.location_id, a.party_id, a.currency_id, a.party_location, a.delivery_date, a.within_group, a.party_location, a.order_id, a.order_no,b.section from subcon_ord_mst a ,subcon_ord_dtls b where a.subcon_job=b.job_no_mst and a.id in($data) and a.status_active=1 and b.status_active=1 group by  a.id, a.subcon_job, a.company_id, a.location_id, a.party_id, a.currency_id, a.party_location, a.delivery_date, a.within_group, a.party_location, a.order_id, a.order_no,b.section" );
	foreach ($nameArray as $row)
	{	
		echo "document.getElementById('txt_recv_no').value 				= '".$row[csf("subcon_job")]."';\n";
		echo "document.getElementById('hid_recv_id').value 				= '".$row[csf("id")]."';\n";
		echo "document.getElementById('cbo_company_name').value 		= '".$row[csf("company_id")]."';\n";
		echo "document.getElementById('cbo_within_group').value 		= '".$row[csf("within_group")]."';\n";  
		echo "$('#cbo_company_name').attr('disabled','true')".";\n";
		echo "$('#cbo_section').attr('disabled','true')".";\n";
		echo "fnc_load_party(1,".$row[csf("within_group")].");\n";	
	//echo "load_drop_down( 'requires/sub_con_wo_entry_controller', document.getElementById('cbo_company_name').value, 'load_drop_down_location', 'location_td' );\n";	
		echo "document.getElementById('cbo_location_name').value 		= '".$row[csf("location_id")]."';\n";
	//echo "load_drop_down( 'requires/sub_con_wo_entry_controller', document.getElementById('cbo_company_name').value, 'load_drop_down_buyer', 'buyer_td' );\n";
		echo "document.getElementById('cbo_party_name').value			= '".$row[csf("party_id")]."';\n";
		//cbo_company_name cbo_location_name cbo_within_group cbo_party_name cbo_party_location txt_delivery_date cbo_section txt_order_no txt_order_qty
		echo "fnc_load_party(2,".$row[csf("within_group")].");\n";	 
		echo "document.getElementById('cbo_party_location').value		= '".$row[csf("party_location")]."';\n";	
		echo "document.getElementById('txt_delivery_date').value		= '".change_date_format($row[csf("delivery_date")])."';\n";  
		echo "document.getElementById('hid_order_id').value          	= '".$row[csf("order_id")]."';\n";
		echo "document.getElementById('txt_order_no').value         	= '".$row[csf("order_no")]."';\n";
		//echo "document.getElementById('cbo_section').value        		= '".$row[csf("section")]."';\n";
		//echo "document.getElementById('txt_order_qty').value         	= '".$row[csf("remarks")]."';\n";
		echo "$('#txt_order_no').attr('disabled','true')".";\n";
		echo "$('#cbo_within_group').attr('disabled','true')".";\n";
		echo "$('#cbo_party_name').attr('disabled','true')".";\n";
		//echo "document.getElementById('update_id').value          		= '".$row[csf("id")]."';\n";	
		//echo "set_button_status(1,'".$_SESSION['page_permission']."', 'fnc_job_order_entry',1);\n";	
	}
	exit();	
}*/

if ($action=="order_popup")
{	
	echo load_html_head_contents("Order Search","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
	<script>
	function js_set_value(id)
	{
		//alert(booking_no); 
		document.getElementById('hidd_booking_data').value=id;
		parent.emailwindow.hide();
	}

	function fnc_load_party_order_popup(company,party_name)
	{   	
		load_drop_down( 'sub_con_wo_entry_controller', company+'_'+1+'_'+party_name, 'load_drop_down_buyer', 'buyer_td' );
		$('#cbo_party_name').attr('disabled',true);
	}
	
	function search_by(val,type)
	{
		if(type==1)
		{
			if(val==1 || val==0)
			{
				$('#txt_search_common').val('');
				$('#search_td').html('W/O No');
			}
			else if(val==2)
			{
				$('#txt_search_common').val('');
				$('#search_td').html('Job NO');
			}
			else if(val==3)
			{
				$('#txt_search_common').val('');
				$('#search_td').html('Style Ref.');
			}
			else if(val==4)
			{
				$('#txt_search_common').val('');
				$('#search_td').html('Buyer Po');
			}
		}
	}
</script>
</head>
<body onLoad="fnc_load_party_order_popup(<? echo $company;?>,<? echo $party_name;?>)">
	<div align="center" style="width:100%;" >
		<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
            <table  cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
                <thead>
                    <tr>
                        <th colspan="7" align="center">
                            <? echo create_drop_down( "cbo_search_category", 110, $string_search_type,'', 1, "-- Search Catagory --" ); ?>
                        </th>
                    </tr>
                    <tr>                	 
                        <th width="150">Party Name</th>
                        <th width="80">Search Type</th>
                        <th width="100" id="search_td">W/O No</th>
                        <th width="60">W/O Year</th>
                        <th colspan="2" width="120">W/O Date Range</th>
                        <th>
                            <input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
                            <input type="hidden" id="hidd_booking_data">
                        </th>
                    </tr>                                 
                </thead>
                <tr class="general">
                    <td id="buyer_td"><? echo create_drop_down( "cbo_party_name", 150, $blank_array,"", 1, "-- Select Buyer --" ); ?></td>
                    <td>
                        <? 
                            $searchtype_arr=array(1=>"W/O No",2=>"Buyer Job",3=>"Buyer Style Ref.",4=>"Buyer Po");
                            echo create_drop_down( "cbo_search_type", 80, $searchtype_arr,"", 0, "", 1, "search_by(this.value,1)",0,"" );
                        ?>
                    </td>
                    <td><input name="txt_search_common" id="txt_search_common" class="text_boxes" style="width:90px"></td>
                    <td><? echo create_drop_down( "cbo_year_selection", 60, $year,"", 1, "-- Select --", date('Y'), "",0 ); ?></td>
                    <td><input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" placeholder="From"></td>
                    <td><input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px" placeholder="To"></td> 
                    <td>
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_party_name').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('cbo_search_category').value+'_'+<? echo $company;?>+'_'+document.getElementById('cbo_search_type').value, 'create_booking_search_list_view', 'search_div', 'sub_con_wo_entry_controller','setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
                    </td>
                </tr>
                <tr>
                    <td colspan="7"align="center" height="30" valign="middle"><?  echo load_month_buttons(); ?></td>
                </tr>
            </table>
            <div id="search_div"></div>   
        </form>
    </div>
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if($action=="create_booking_search_list_view")
{	
	$data=explode('_',$data);
	$search_type=$data[7];
	
	if ($data[0]!=0) $company=" and a.company_id='$data[0]'"; else { echo "Please Select Party First."; die; }
	//if ($data[0]!=0 && ) $buyer=" and buyer_id='$data[1]'"; else  $buyer="";//{ echo "Please Select Buyer First."; die; }
	if($db_type==0) { $year_cond=" and YEAR(a.insert_date)=$data[4]"; } else if($db_type==2) { $year_cond=" and to_char(a.insert_date,'YYYY')=$data[4]"; }
	$master_company=$data[6];

	$woorder_cond=""; $job_cond=""; $style_cond=""; $po_cond="";
	if($data[5]==1)
	{
		if ($data[1]!="")
		{
			if ($search_type==1) $woorder_cond=" and a.booking_no = '$data[1]' ";
			if ($search_type==2) $job_cond=" and a.job_no_prefix_num = '$data[1]' ";
			if ($search_type==3) $style_cond=" and a.style_ref_no = '$data[1]' ";
			if ($search_type==4) $po_cond=" and b.po_number = '$data[1]' ";
		}
	}
	if($data[5]==2)
	{
		if ($data[1]!="")
		{
			if ($search_type==1) $woorder_cond=" and a.booking_no like '$data[1]%' ";
			if ($search_type==2) $job_cond=" and a.job_no_prefix_num like '$data[1]%' ";
			if ($search_type==3) $style_cond=" and a.style_ref_no like '$data[1]%' ";
			if ($search_type==4) $po_cond=" and b.po_number like '$data[1]%' ";
		}
	}
	if($data[5]==3)
	{
		if ($data[1]!="")
		{
			if ($search_type==1) $woorder_cond=" and a.booking_no like '%$data[1]' ";
			if ($search_type==2) $job_cond=" and a.job_no_prefix_num like '%$data[1]' ";
			if ($search_type==3) $style_cond=" and a.style_ref_no like '%$data[1]' ";
			if ($search_type==4) $po_cond=" and b.po_number like '%$data[1]' ";
		}	
	}
	if($data[5]==4 || $data[5]==0)
	{
		if ($data[1]!="")
		{
			if ($search_type==1) $woorder_cond=" and a.booking_no like '%$data[1]%' ";
			if ($search_type==2) $job_cond=" and a.job_no_prefix_num like '%$data[1]%' ";
			if ($search_type==3) $style_cond=" and a.style_ref_no like '%$data[1]%' ";
			if ($search_type==4) $po_cond=" and b.po_number like '%$data[1]%' ";
		}
	}
	
	$po_ids='';
	
	if($db_type==0) $id_cond="group_concat(b.id)";
	else if($db_type==2) $id_cond="listagg(b.id,',') within group (order by b.id)";
	if(($job_cond!="" && $search_type==2) || ($style_cond!="" && $search_type==3)|| ($po_cond!="" && $search_type==4))
	{
		$po_ids = return_field_value("$id_cond as id", "wo_po_details_master a, wo_po_break_down b", "a.job_no=b.job_no_mst $job_cond $style_cond $po_cond", "id");
		if ($po_ids=="")
		{
			$po_idsCond="";
			echo "Not Found."; die;
		}
	}
	
	if ($po_ids!="") $po_idsCond=" and b.po_break_down_id in ($po_ids)"; else $po_idsCond="";
	$buyer_po_arr=array();
	$po_sql ="Select a.style_ref_no, a.job_no, a.buyer_name, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst";
	$po_sql_res=sql_select($po_sql);
	foreach ($po_sql_res as $row)
	{
		$buyer_po_arr[$row[csf("id")]]['style']=$row[csf("style_ref_no")];
		$buyer_po_arr[$row[csf("id")]]['po']=$row[csf("po_number")];
		$buyer_po_arr[$row[csf("id")]]['buyer']=$row[csf("buyer_name")];
		$buyer_po_arr[$row[csf("id")]]['job']=$row[csf("job_no")];
	}
	unset($po_sql_res);
	
	$pre_cost_trims_arr=array();
	$pre_sql ="Select id, emb_name, emb_type, body_part_id  from wo_pre_cost_embe_cost_dtls where status_active=1 and is_deleted=0";
	$pre_sql_res=sql_select($pre_sql);
	foreach ($pre_sql_res as $row)
	{
		$pre_cost_trims_arr[$row[csf("id")]]['emb_name']=$row[csf("emb_name")];
		$pre_cost_trims_arr[$row[csf("id")]]['emb_type']=$row[csf("emb_type")];
		$pre_cost_trims_arr[$row[csf("id")]]['body_part_id']=$row[csf("body_part_id")];
	}
	unset($pre_sql_res);
	
	if($db_type==0)
	{
		if ($data[2]!="" &&  $data[3]!="") $booking_date = "and a.booking_date between '".change_date_format($data[2], "yyyy-mm-dd", "-")."' and '".change_date_format($data[3], "yyyy-mm-dd", "-")."'"; else $booking_date ="";
		$wo_year="YEAR(a.insert_date)";
		$pre_cost_trims_cond="group_concat(b.pre_cost_fabric_cost_dtls_id)";
		$gmts_item_cond="group_concat(b.gmt_item)";
		$po_id_cond="group_concat(b.po_break_down_id)";
	}
	else if($db_type==2)
	{
		if ($data[2]!="" &&  $data[3]!="") $booking_date = "and a.booking_date between '".change_date_format($data[2], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."'"; else $booking_date ="";
		$wo_year="to_char(a.insert_date,'YYYY')";
		$pre_cost_trims_cond="listagg(b.pre_cost_fabric_cost_dtls_id,',') within group (order by b.pre_cost_fabric_cost_dtls_id)";
		$gmts_item_cond="listagg(b.gmt_item,',') within group (order by b.gmt_item)";
		$po_id_cond="listagg(b.po_break_down_id,',') within group (order by b.po_break_down_id)";
	} 
	
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$arr=array (0=>$comp,1=>$buyer_arr);
	//$sql= "select $wo_year as year, id, booking_type, booking_no, company_id, buyer_id, job_no, booking_date from  wo_booking_mst where booking_type=6 and status_active=1 and is_deleted=0 $booking_date $company $woorder_cond $year_cond order by booking_no"; 
	//$sql= "select $wo_year as year, a.id, a.booking_type, a.booking_no, a.booking_no_prefix_num, a.company_id, a.buyer_id, a.job_no, a.booking_date, a.currency_id, $pre_cost_trims_cond as pre_cost_trims_id, $gmts_item_cond as gmts_item, $po_id_cond as po_id from  wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.booking_type=6 and a.status_active=1 and a.lock_another_process!=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $booking_date $company $woorder_cond $year_cond $po_idsCond group by a.insert_date, a.id, a.booking_type, a.booking_no, a.booking_no_prefix_num, a.company_id, a.buyer_id, a.job_no, a.booking_date, a.currency_id order by a.id DESC";

	$sql= "select a.id, a.subcon_job, a.company_id, a.location_id, a.party_id, a.party_location, a.currency_id, a.receive_date, a.delivery_date,a.rec_start_date, a.order_id, a.order_no, a.exchange_rate, b.id, b.mst_id, b.order_id, b.order_no, b.booked_uom,b.delivery_date, b.buyer_po_no, b.buyer_style_ref, b.buyer_buyer,  b.section, b.item_group  from subcon_ord_mst a, subcon_ord_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form=255 group by a.id, a.subcon_job, a.company_id, a.location_id, a.party_id, a.party_location, a.currency_id, a.receive_date, a.delivery_date, a.rec_start_date, a.order_id, a.order_no, a.exchange_rate,b.id, b.mst_id, b.order_id, b.order_no, b.booked_uom,b.delivery_date, b.buyer_po_no, b.buyer_style_ref, b.buyer_buyer, b.section, b.item_group";
	// a.subcon_job=job_no_mst a
	$data_array=sql_select($sql);
	?>
	<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="640" >
        <thead>
            <th width="30">SL</th>
            <th width="60">W/O Year</th>
            <th width="60">W/O No</th>
            <th width="70">W/O Date</th>
            <th width="100">Buyer</th>
            <th width="100">Buyer Po</th>
            <th width="100">Buyer Style</th>
            <th width="100">Buyer Job</th>
        </thead>
        </table>
        <div style="width:640px; max-height:270px;overflow-y:scroll;" >	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="620" class="rpt_table" id="list_view">
        <tbody>
            <? 
            $i=1;
            foreach($data_array as $row)
            {  
                if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                ?>
                <tr bgcolor="<? echo $bgcolor; ?>" onClick='js_set_value("<? echo $row[csf('id')].'_'.$row[csf('booking_no')].'_'.$row[csf('currency_id')]; ?>")' style="cursor:pointer" >
                    <td width="30"><? echo $i; ?></td>
                    <td width="60" align="center"><? echo $row[csf('year')]; ?></td>
                    <td width="60" align="center"><? echo $row[csf('booking_no_prefix_num')]; ?></td>
                    <td width="70"><? echo change_date_format($row[csf('booking_date')]); ?></td>
                    <td width="100" style="word-break:break-all"><? echo $buyer_name; ?></td>
                    <td width="100" style="word-break:break-all"><? echo $po_no; ?></td>
                    <td width="100" style="word-break:break-all"><? echo $buyer_style; ?></td>
                    <td width="100" style="word-break:break-all"><? echo $buyer_job; ?></td>
                </tr>
				<? 
                $i++; 
            } 
            ?>
        </tbody>
    </table>
	<?
	exit();
}

if ($action=="populate_data_from_search_popup")
{
	//echo $action."nazim"; die;
	$data=explode('_',$data);
	$nameArray=sql_select( "select id, booking_type, booking_no, company_id, buyer_id, job_no, booking_date,currency_id from  wo_booking_mst where booking_type=6 and status_active=1 and is_deleted=0 and id='$data[0]'" );
	//$sql= "select to_char(insert_date,'YYYY') as year, id, booking_type, booking_no, company_id, buyer_id, job_no, booking_date from  wo_booking_mst where booking_type=6 and status_active=1 and is_deleted=0 $booking_date $company $order_cond order by booking_no";
	foreach ($nameArray as $row)
	{	
		echo "document.getElementById('txt_order_no').value 	= '".$row[csf("booking_no")]."';\n";  
		echo "document.getElementById('cbo_party_name').value 	= '".$row[csf("company_id")]."';\n";
		echo "document.getElementById('hid_order_id').value		= '".$row[csf("id")]."';\n";
		echo "document.getElementById('cbo_currency').value		= '".$row[csf("currency_id")]."';\n";
	}
	exit();	
}



if($action=="row_metarial_popup")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode,'','');
	extract($_REQUEST);
	
	//echo $cboUom."nazim"; die;
    ?>
    <script>
    	var str_color = [<? echo substr(return_library_autocomplete( "select color_name from lib_color group by color_name ", "color_name" ), 0, -1); ?> ];
    	var str_size = [<? echo substr(return_library_autocomplete( "select size_name from lib_size group by size_name ", "size_name" ), 0, -1); ?> ]; 
		var chkBoxVal= <? echo $txtCopyChk;  ?>; 
		var hdnBrkDelId= <? echo $hdnBrkDelId;  ?>; 
		var dtlsUpdateId= <? echo $hdnDtlsUpdateId;  ?>; 
		//alert(hdnDtlsUpdateId); //return;
		function add_share_row( i ) 
		{
			//var row_num=$('#tbl_share_details_entry tbody tr').length-1;
			var row_num=$('#tbl_share_details_entry tbody tr').length;
			if (row_num!=i)
			{
				return false;
			}
			i++;
			$("#tbl_share_details_entry tbody tr:last").clone().find("input,select").each(function() {
				$(this).attr({
					'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
					'name': function(_, name) { return name + i },
					'value': function(_, value) { return value }              
				});
			}).end().appendTo("#tbl_share_details_entry tbody");
			$('#increaseset_'+i).removeAttr("onClick").attr("onClick","add_share_row("+i+");");
			$('#decreaseset_'+i).removeAttr("onClick").attr("onClick","fn_deletebreak_down_tr("+i+")");
			$('#decreaseset_'+i).removeAttr("onClick").attr("onClick","fn_deletebreak_down_tr("+i+")");

			$('#txtdescription_'+i).val('');
			$('#txtSpecification_'+i).val('');
			$('#txtUnit_'+i).val('');
			$('#txtUnitPcs_'+i).val('');
			$('#txtConsQty_'+i).val('');
			$('#txtProcessLoss_'+i).val('');
			$('#txtProcessLossQty_'+i).val('');
			$('#txtReqQty_'+i).val('');
			$('#txtRemarks_'+i).val('');
			$('#hiddenid_'+i).val('');
			$('#hiddenProdid_'+i).val('');
			
			set_all_onclick();
		}		
		
		function fn_deletebreak_down_tr(rowNo) 
		{ 
			var numRow = $('table#tbl_share_details_entry tbody tr').length;
			var hiddenProdid=$('#hiddenProdid_'+rowNo).val(); 
			if(numRow!=1 && hiddenProdid!='')
			{
				var updateIdDtls=$('#hiddenid_'+rowNo).val();
				var txtDeletedId=$('#txtDeletedId').val();
				var selected_id='';
				if(updateIdDtls!='')
				{
					if(txtDeletedId=='') selected_id=updateIdDtls; else selected_id=txtDeletedId+','+updateIdDtls;
					$('#txtDeletedId').val( selected_id );
				}
				$("#row_"+rowNo).remove();
				/*$('#tbl_share_details_entry tbody tr:last').remove();*/
			}
			else
			{
				return false;
			}
			//sum_total_qnty(rowNo);
		}

		function fnc_close()
		{
			var tot_row=$('#tbl_share_details_entry tbody tr').length-1;
			//alert(tot_row);
			if(tot_row<1){
				alert('Please Chose any Item');return;
			}
			else
			{
				var data_break_down="";
				check_field=0;
				//alert(check_field+'iii');
				$("#tbl_share_details_entry tbody tr").each(function()
				{
					//alert(check_field+'hhh');
					var txtdescription 		= $(this).find('input[name="txtdescription[]"]').val();
					var txtSpecification 	= $(this).find('input[name="txtSpecification[]"]').val();
					var txtUnit 			= $(this).find('select[name="txtUnit[]"]').val();
					var txtUnitPcs 			= $(this).find('input[name="txtUnitPcs[]"]').val()*1;
					var txtConsQty 			= $(this).find('input[name="txtConsQty[]"]').val()*1;
					var txtProcessLoss 		= $(this).find('input[name="txtProcessLoss[]"]').val()*1;
					var txtProcessLossQty 	= $(this).find('input[name="txtProcessLossQty[]"]').val()*1;
					var txtReqQty 			= $(this).find('input[name="txtReqQty[]"]').val()*1;
					var txtRemarks 			= $(this).find('input[name="txtRemarks[]"]').val();
					var hiddenid 			= $(this).find('input[name="hiddenid[]"]').val();
					var hiddenProdid 		= $(this).find('input[name="hiddenProdid[]"]').val();
					//alert(txtConsQty);
					//var txtdescription 		= $(this).find('input[name="txtdescription[]"]').val();
					
					if((txtdescription !='' &&  txtdescription!=0) && (txtConsQty=='' ||  txtConsQty==0))
					{
						alert('Please Fill up Cons. Qty.');
						check_field=1 ; return;
						
					}

					//alert(check_field+"b");
					if((txtdescription!='' && txtdescription!=0) && check_field ==0)
					{
						if(data_break_down=="")
						{
							data_break_down+=txtdescription+'_'+txtSpecification+'_'+txtUnit+'_'+txtUnitPcs+'_'+txtConsQty+'_'+txtProcessLoss+'_'+txtProcessLossQty+'_'+txtReqQty+'_'+txtRemarks+'_'+hiddenid+'_'+hiddenProdid;
						}
						else
						{
							data_break_down+="**"+txtdescription+'_'+txtSpecification+'_'+txtUnit+'_'+txtUnitPcs+'_'+txtConsQty+'_'+txtProcessLoss+'_'+txtProcessLossQty+'_'+txtReqQty+'_'+txtRemarks+'_'+hiddenid+'_'+hiddenProdid;
						}
					}
				});
				//alert(data_break_down+"c");
				if(check_field==0)
				{
					
					var copy_basis=$('input[name="copy_basis"]:checked').val();
					$('#is_copy').val( copy_basis );
					//alert(copy_basis+'kkk');
					/*if ($('#is_copy').is(":checked"))
					{
					  	$('#is_copy').val( 1 );
					}
					else
					{
						$('#is_copy').val( 2 );
					}*/
					$('#hidden_break_tot_row').val( data_break_down );
					parent.emailwindow.hide();
				}
			}
			
		}

		function create_description_row(prod_ids,id,row)
	    {
	    	if(id!=0 && id!='')
	    	{
				//$("#row_"+row).remove();
				var row_num =  $('#tbl_share_details_entry tbody tr').length;
				var response_data = return_global_ajax_value(prod_ids + "**" + row_num + "**" + id , 'populate_prod_data', '', 'sub_con_wo_entry_controller');
				$("#tbl_share_details_entry tbody:last").append(response_data);
	    	}
	    	else
	    	{
	    		var row_num =  $('#tbl_share_details_entry tbody tr').length; //$('#txt_tot_row').val();
		        var response_data = return_global_ajax_value(prod_ids + "**" + row_num , 'populate_prod_data', '', 'sub_con_wo_entry_controller');
		        $('#tbl_share_details_entry tbody').prepend(response_data);
		        var tot_row = $('#tbl_share_details_entry tbody tr').length;
	    	}
	       // freeze_window(5); //release_freezing();
	    }

		function openmypage_material(data)
		{
			//alert(data);
			page_link='sub_con_wo_entry_controller.php?action=material_description_popup&data='+data;
			title='Product List';
			emailwindow=dhtmlmodal.open('EmailBox','iframe', page_link, title, 'width=730px, height=400px, center=1, resize=0, scrolling=0','../../')
			var datas=(data).split('_');
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0]
				var theemailprod=this.contentDoc.getElementById("all_ids").value;
				if (theemailprod!="")
				{
					create_description_row(theemailprod,datas[1],datas[2]);
				}
			}
		}

		function metarial_calculate(row)
		{
			var order_qty ='<? echo $txtJobQuantity ?>';
			//var cons_qty =order_qty;
			//$('#txtConsQty_'+row).val(cons_qty.toFixed(2));
			var cons_qty=$('#txtConsQty_'+row).val()*1;
			//var cons_qty=1;
			var txtProcessLoss=$('#txtProcessLoss_'+row).val()*1;
			
			var processLossQty=(cons_qty*txtProcessLoss)/100;
			var reqQty=processLossQty+cons_qty;
			$('#txtProcessLossQty_'+row).val(processLossQty.toFixed(4));
			$('#txtReqQty_'+row).val(reqQty);

			//alert(order_qty);
			/*var unitPcs=$('#txtUnitPcs_'+row).val()*1;
			if(unitPcs==0)unitPcs=1;
			var cons_qty =order_qty/unitPcs;*/
			/*var cons_qty =order_qty;
			$('#txtConsQty_'+row).val(cons_qty.toFixed(2));
			process_calculate(row);*/
		}

		function process_calculate(row)
		{
			var txtProcessLoss=$('#txtProcessLoss_'+row).val()*1;
			var cons_qty=$('#txtConsQty_'+row).val()*1;
			var processLossQty=(cons_qty*txtProcessLoss)/100;
			var reqQty=processLossQty+cons_qty;
			$('#txtProcessLossQty_'+row).val(processLossQty.toFixed(4));
			$('#txtReqQty_'+row).val(reqQty);
		}
	</script>
	</head>
	<body>
		<div align="center" style="width:100%;" >
			<form name="qntypopup_1"  id="qntypopup_1" autocomplete="off">
				<table class="rpt_table" width="700px" cellspacing="0" cellpadding="0" rules="all" id="tbl_share_details_entry">
					<thead>
						<tr>
							<th>&nbsp;</th>
							<th>&nbsp;</th>
							<th>Booked UOM</th>
							<th> 
								<? 
									echo create_drop_down( "txtUnit_".$tblRow, 80, $unit_of_measurement,"", 1, "-- Select --",$cboUom,"", 1,'','','','','','',"txtUnit[]");
								?>
							<th colspan="6" >
				                <input type="radio" name="copy_basis" id="copy_0" value="0" checked >No Copy 
				                <input type="radio" name="copy_basis" id="copy_1" value="1">Copy Item Wise
				                <input type="radio" name="copy_basis" id="copy_2" value="2" >Copy to All
							</th>	
						</tr>
						<tr>
							<th rowspan="2" width="30">Sl.</th>
							<th rowspan="2" width="130">Description</th>
							<th rowspan="2" width="100">Specification</th>
							<th rowspan="2" width="80" >Cons Uom</th>
							<th rowspan="2" width="70" style="display: none;" >Pcs/ Unit</th>
							<th rowspan="2" width="60" class="must_entry_caption">Cons/Unit</th>
							<th rowspan="2" width="80">Process Loss %</th>
							<th rowspan="2" width="80">Process Loss Qty.</th>
							<th rowspan="2" width="80">Total Cons/Unit</th>
							<th rowspan="2" width="80">Remarks</th>
							<th rowspan="2"></th>
						</tr>
					</thead>
					<tbody id="description_list_view">
						<input type="hidden" name="txtDeletedId" id="txtDeletedId" class="text_boxes_numeric" style="width:90px" readonly />
						<input type="hidden" name="hidden_break_tot_row" id="hidden_break_tot_row" class="text_boxes" style="width:90px" />
						<input type="hidden" name="is_copy" id="is_copy" class="text_boxes" style="width:90px" />
	                    <? //echo $data_dreak;
	                    $k=0; 
	                    //echo $data_break."==="; die;
	                    if($data_break!=''){
	                    	$data_array=explode("**",$data_break);
							$count_dtls_data=count($data_array);
	                    }else{
	                    	$count_dtls_data=0;
	                    }
						//echo $count_dtls_data; die;
						if($count_dtls_data>0)
						{
							$k++;
							?>
							<tr id="row_<? echo $k;?>">
								<td><input type="text" id="txtSl_<? echo $k;?>" name="txtSl_<? echo $k;?>" class="text_boxes" readonly style="width:30px" value="<? echo $k; ?>" />
								</td>
								<td><input type="text" id="txtdescription_<? echo $k;?>" name="txtdescription[]" class="text_boxes" readonly onClick='openmypage_material("<? echo $company.'_0_'.$k; ?>")' placeholder="Click" style="width:120px" value="" />
									<input type="hidden" id="hiddenProdid_<? echo $k; ?>" name="hiddenProdid[]"  style="width:15px;" class="text_boxes" value="" readonly  />
								</td>
								<td>
									<input type="text" id="txtSpecification_<? echo $k;?>" name="txtSpecification[]" class="text_boxes" style="width:90px" value="" <? echo $disabled; ?> readonly /></td>
								<td>
									<?
										echo create_drop_down( "txtUnit_".$tblRow, 80, $unit_of_measurement,"", 1, "-- Select --",'',"", 1,'','','','','','',"txtUnit[]");
									?>	
								</td>
								<td style="display: none;" >
									<input type="text" id="txtUnitPcs_<? echo $k;?>" name="txtUnitPcs[]" class="text_boxes_numeric" style="width:60px" onKeyUp="metarial_calculate(<? echo $k;?>)"  value="" <? echo $disabled; ?> />
								</td>
								<td><input type="text" id="txtConsQty_<? echo $k;?>" name="txtConsQty[]"  class="text_boxes_numeric" style="width:50px" onKeyUp="metarial_calculate(<? echo $k;?>)" value="" <? echo $disabled; ?> /></td>
								<td>
									<input type="text" id="txtProcessLoss_<? echo $k;?>" name="txtProcessLoss[]" class="text_boxes_numeric"  onKeyUp="process_calculate(<? echo $k;?>)"   style="width:70px" value=""  />
								</td>
								<td><input type="text" id="txtProcessLossQty_<? echo $k;?>" name="txtProcessLossQty[]" class="text_boxes_numeric" style="width:70px" value="" readonly /></td>
								<td><input type="text" id="txtReqQty_<? echo $k;?>" name="txtReqQty[]" class="text_boxes_numeric" style="width:70px" value="" disabled/></td>
								<td><input type="text" id="txtRemarks_<? echo $k;?>" name="txtRemarks[]" class="text_boxes" style="width:70px" value=""/></td>
								<td>
									<input type="hidden" id="hidbookingconsid_<? echo $k; ?>" name="hidbookingconsid[]"  style="width:15px;" class="text_boxes" value="" />
	                                <input type="hidden" id="hiddenid_<? echo $k; ?>" name="hiddenid[]"  style="width:15px;" class="text_boxes" value="" />
									<input type="button" id="decreaseset_<? echo $k;?>" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(<? echo $k;?> ,'tbl_share_details_entry' );" <? echo $disabled; ?>/>
								</td>  
							</tr>
							<?
							foreach($data_array as $row)
							{
								$data=explode('_',$row);
								//echo $row; die;
								$k++;
								?>
								<tr id="row_<? echo $k;?>">
									<td><input type="text" id="txtSl_<? echo $k;?>" name="txtSl_<? echo $k;?>" class="text_boxes" style="width:30px" value="<? echo $k; ?>" readonly />
									</td>
									<td><input type="text" id="txtdescription_<? echo $k;?>" name="txtdescription[]" class="text_boxes" placeholder="Double Click" style="width:120px" value="<? echo $data[0]; ?>" readonly />
										<input type="hidden" id="hiddenProdid_<? echo $k; ?>" name="hiddenProdid[]"  style="width:15px;" class="text_boxes" value="<? echo $data[10]; ?>" readonly />
									</td>
									<td>
										<input type="text" id="txtSpecification_<? echo $k;?>" name="txtSpecification[]" class="text_boxes" style="width:90px" value="<? echo $data[1]; ?>" <? echo $disabled; ?> readonly /></td>
									<td ><?
											echo create_drop_down( "txtUnit_".$tblRow, 80, $unit_of_measurement,"", 1, "-- Select --", $data[2],"", 1,'','','','','','',"txtUnit[]"); 
										?>	
									</td>
									<td style="display: none;" >
										<input type="text" id="txtUnitPcs_<? echo $k;?>" name="txtUnitPcs[]" value="<? echo $data[3]; ?>" class="text_boxes_numeric" style="width:60px" onKeyUp="metarial_calculate(<? echo $k;?>)" value="<? echo $data[3]; ?>" <? echo $disabled; ?> />
									</td>
									<td><input type="text" id="txtConsQty_<? echo $k;?>" name="txtConsQty[]"  class="text_boxes_numeric" style="width:50px" onKeyUp="metarial_calculate(<? echo $k;?>)" value="<? echo $data[4]; ?>" <? echo $disabled; ?> />
									</td>
									<td>
										<input type="text" id="txtProcessLoss_<? echo $k;?>" name="txtProcessLoss[]" class="text_boxes_numeric"  style="width:70px" value="<? echo $data[5]; ?>"   onKeyUp="process_calculate(<? echo $k;?>)"   />
									</td>
									<td><input type="text" id="txtProcessLossQty_<? echo $k;?>" name="txtProcessLossQty[]"class="text_boxes_numeric" style="width:70px" value="<? echo number_format($data[6],4,'.',''); ?>" readonly /></td>
									<td><input type="text" id="txtReqQty_<? echo $k;?>" name="txtReqQty[]" class="text_boxes_numeric" style="width:70px" value="<? echo $data[7]; ?>" disabled/></td>
									<td><input type="text" id="txtRemarks_<? echo $k;?>" name="txtRemarks[]" class="text_boxes" style="width:70px" value="<? echo $data[8]; ?>" /></td>
									<td>
										<input type="hidden" id="hidbookingconsid_<? echo $k; ?>" name="hidbookingconsid[]"  style="width:15px;" class="text_boxes" />
	                                    <input type="hidden" id="hiddenid_<? echo $k; ?>" name="hiddenid[]"  style="width:15px;" value="<? echo $data[9]; ?>" class="text_boxes"  />
										
										<input type="button" id="decreaseset_<? echo $k;?>" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(<? echo $k;?> ,'tbl_share_details_entry' );" <? echo $disabled; ?>/>
									</td>  
								</tr>
								<?
							}
						}
						else
						{
							$k++;
							?>
	                        <tr>
								<td><input type="text" id="txtSl_<? echo $k;?>" name="txtSl_<? echo $k;?>" class="text_boxes" style="width:30px" value="<? echo $k; ?>" />
								</td>
								<td><input type="text" id="txtdescription_<? echo $k;?>" name="txtdescription[]" class="text_boxes" onClick='openmypage_material("<? echo $company.'_0_'.$k; ?>")' placeholder="Click" readonly style="width:120px" value="<? echo $data[0]; ?>" />
									<input type="hidden" id="hiddenProdid_<? echo $k; ?>" name="hiddenProdid[]"  style="width:15px;" readonly class="text_boxes" value="<? echo $data[10]; ?>"  />
								</td>
								<td>
									<input type="text" id="txtSpecification_<? echo $k;?>" name="txtSpecification[]" class="text_boxes" readonly style="width:90px" value="<? echo $color_arr[$data[1]]; ?>" <? echo $disabled; ?> /></td>
								<td><?
										echo create_drop_down( "txtUnit_".$tblRow, 80, $unit_of_measurement,"", 1, "-- Select --", $data[2],"", 1,'','','','','','',"txtUnit[]");  ?>	</td>
								<td  style="display: none;" >
									<input type="text" id="txtUnitPcs_<? echo $k;?>" name="txtUnitPcs[]" class="text_boxes_numeric"  style="width:60px" onKeyUp="metarial_calculate(<? echo $k;?>)"  value="<? echo $data[3]; ?>" <? echo $disabled; ?> />
								</td>
								<td><input type="text" id="txtConsQty_<? echo $k;?>" name="txtConsQty[]"  class="text_boxes_numeric" style="width:50px" onKeyUp="metarial_calculate(<? echo $k;?>)" value="<? echo $data[4]; ?>" <? echo $disabled; ?> /></td>
								<td>
									<input type="text" id="txtProcessLoss_<? echo $k;?>" name="txtProcessLoss[]" class="text_boxes_numeric"  onKeyUp="process_calculate(<? echo $k;?>)"   style="width:70px" value="<? echo $data[3]; ?>"  />
								</td>
								<td><input type="text" id="txtProcessLossQty_<? echo $k;?>" name="txtProcessLossQty[]" class="text_boxes_numeric" style="width:70px" value="<? echo number_format($data[5],4,'.',''); ?>" readonly /></td>
								<td><input type="text" id="txtReqQty_<? echo $k;?>" name="txtReqQty[]" class="text_boxes_numeric" style="width:70px" value="<? echo number_format($data[5],4,'.',''); ?>" disabled/></td>
								<td><input type="text" id="txtRemarks_<? echo $k;?>" name="txtRemarks[]" class="text_boxes" style="width:70px" value="<? echo $data[5]; ?>"/></td>
								<td>
									<input type="hidden" id="hidbookingconsid_<? echo $k; ?>" name="hidbookingconsid[]"  style="width:15px;" class="text_boxes" value="" />
	                                <input type="hidden" id="hiddenid_<? echo $k; ?>" name="hiddenid[]"  style="width:15px;" class="text_boxes" value="<? echo $data[9]; ?>" />
									<input type="button" id="decreaseset_<? echo $k;?>" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(<? echo $k;?> ,'tbl_share_details_entry' );" <? echo $disabled; ?>/>
								</td>  
							</tr>
							<?
						}
						?> 
					</tbody>
				</table> 
				<table>
					<tr>
						<td align="center"><input type="button" name="main_close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" /></td>
					</tr>
				</table>
			</form>
		</div>
	</body>
	<script>
		$( "#is_copy" ).val( chkBoxVal );
		if(chkBoxVal==1){
			$( "#copy_1" ).attr( "checked", true );
		}else if(chkBoxVal==2){
			$( "#copy_2" ).attr( "checked", true );
		}else{
			$( "#copy_0" ).attr( "checked", true );
		}

		if(dtlsUpdateId!='' && dtlsUpdateId!=0){
			 $("input[type=radio]").attr('disabled', true);

		}
		
		/*if(hdnBrkDelId!='' && hdnBrkDelId!=0){
			$( "#txtDeletedId" ).val( hdnBrkDelId );
		} */

	/*if(chkBoxVal==1)
	{
		$( "#is_copy" ).attr( "checked", true );
	}
	else{
		$( "#is_copy" ).attr( "checked", false );
	}*/
	//metarial_calculate(0);</script>        
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}	

	if ($action=="material_description_popup")
	{
		echo load_html_head_contents("Description Popup Info","../../../", 1, 1, $unicode,'','');
		extract($_REQUEST);
		//echo $data."---";
		?>
		<script>
			/*function js_set_value(id)
			{ 
				$("#hidden_mst_id").val(id);
				document.getElementById('selected_job').value=id;
				parent.emailwindow.hide();
			}*/

			function toggle( x, origColor ) {
				var newColor = 'yellow';
				if ( x.style ) {
					x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
				}
			} 
			var selected_id = new Array;
			function js_set_value(str) 
			{  // alert(str);
				var subcon_job = $('#txt_prod_id'+str).val();
				//alert(subcon_job);
				toggle( document.getElementById( 'search' + str ), '#FFFFCC' );
				
				if( jQuery.inArray( subcon_job, selected_id ) == -1 ) {
					selected_id.push( subcon_job );
				}
				else 
				{
					for( var i = 0; i < selected_id.length; i++ ) 
					{
						if( selected_id[i] == subcon_job ) break;
					}
					selected_id.splice( i, 1 );
				}
				var id ='';  var id_dtls = ''; var id_break = '';
				for( var i = 0; i < selected_id.length; i++ ) {
					id += selected_id[i] + ',';
				}
				id 		= id.substr( 0, id.length - 1 );
				$('#all_ids').val( id );
			}
		</script>
	</head>
	<body>
	<div align="center" style="width:100%;" >
	    <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
	        <table width="700" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
	            <thead> 
	                <tr>           	 
	                    <th width="120" class="must_entry_caption">Company Name</th>
	                    <th width="70">Item Group</th>  
	                    <th width="100">Section</th>                         
	                    <th width="140">Description</th>
	                    <th width="100">Brand</th>
	                    <th width="70">Product ID</th>
	                    <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:70px;" /></th>
	                </tr>           
	            </thead>
	            <tbody>
	                <tr class="general">
	                    <td><input type="hidden" id="selected_job"><? $data=explode("_",$data); ?>  <!--  echo $data;-->
	                        <? echo create_drop_down( "cbo_company_name", 120, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $data[0], "",1); ?>
	                    </td>
	                    <td id="item_group_td">
	                        <?
	                            echo create_drop_down( "cbo_item_group", 70, "select id,item_name from lib_item_group where item_category in (101) and status_active=1 and is_deleted=0 order by item_name", "id,item_name", 1, "-- Select --", 0, "", $disabled,"" );
	                         ?>
	                    </td>
	                    <td id="section_td">
	                    	<? echo create_drop_down( "cbo_section", 100, $trims_section,"", 1, "-- Select Section --","",'',0,'','','','','','',"cboSection[]"); ?>
	                    </td>
	                    <td align="center">
	                        <input type="text" name="txt_search_string" id="txt_search_string" class="text_boxes" style="width:127px" placeholder="" />
	                    </td>
	                    <td align="center">
	                        <input type="text" name="txt_brand_id" id="txt_brand_id" class="text_boxes" style="width:87px" placeholder="" />
	                    </td>
	                    <td align="center">
	                        <input type="text" name="txt_product_id" id="txt_product_id" class="text_boxes_numeric" style="width:57px" placeholder="" />
	                    </td>
	                    <td align="center">
	                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_item_group').value+'_'+document.getElementById('txt_search_string').value+'_'+document.getElementById('txt_product_id').value+'_'+document.getElementById('txt_brand_id').value+'_'+document.getElementById('cbo_section').value, 'create_description_search_list_view', 'search_div', 'sub_con_wo_entry_controller', 'setFilterGrid(\'tbl_po_list\',-1)')" style="width:70px;" />
	                        <input type="hidden" name="hidden_mst_id" id="hidden_mst_id" class="datepicker" style="width:70px">
	                    </td>
	                    </tr>
	                    <tr>
	                        <td colspan="7" align="center" valign="top" id=""><div id="search_div"></div></td>
	                    </tr>
	                    <tr>
	                        <td colspan="7" align="center" valign="top" id=""> 
	                        	<div style="width:100%; float:left" align="center">
	    							<input type="button" class="formbutton" id="close" style="width:80px" onClick="parent.emailwindow.hide();" value="Close" />
	    							<input type="hidden"  id="all_ids" />
	    						</div>
	    					</td>
	                    </tr>
	                </tbody>
	            </table>    
	            </form>
	        </div>
	    </body>           
	    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	    </html>
	    <?
	    exit();
	}

	if($action=="create_description_search_list_view")
	{	
		$data=explode('_',$data);
		$group_id=str_replace("'","",$data[1]);
		$description_str=str_replace("'","",$data[2]);
		$product_id=trim(str_replace("'","",$data[3]));
		$brand_name=trim(str_replace("'","",$data[4]));
		$section_id=str_replace("'","",$data[5]);
		if($data[0]!=0) $company=" and company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
		if($group_id!=0) $group=" and item_group_id=$group_id"; else { $group=''; }	
		if($description_str!='') $description=" and item_description like '%$description_str%'"; else { $description=''; }	
		if($product_id!='') $product=" and id='$product_id'"; else { $product=''; }	
		if($brand_name!='') $brand=" and brand_name='$brand_name'"; else { $brand=''; }	
		if($section_id!=0)$section=" and section_id='$section_id'"; else { $section=''; }	

		$sql="select id,company_id, item_code,item_description,item_group_id,item_size,current_stock,brand_name,origin,model,sub_group_name,unit_of_measure ,section_id from product_details_master where status_active=1 and is_deleted=0 and item_category_id=101 $company $group $description $product $brand $section and status_active=1 and is_deleted=0";
		//echo $sql;
		$data_array=sql_select($sql);
		?>
	     <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="700" >
	        <thead>
	            <th width="30">SL</th>
	            <th width="105">Item Group</th>
	            <th width="105">Section</th>
	            <th width="50">UOM</th>
	            <th width="200">Description</th>
	            <th width="120">Brand</th>
	            <th>Product ID</th>
	        </thead>
	    </table>
	        <div style="width:700px; max-height:280px;overflow-y:scroll;" >	 
	        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="680" class="rpt_table" id="tbl_po_list">
	        <tbody>
	            <? 
	            $i=1;
	            $itemGroup_arr=return_library_array( "select id,item_name from lib_item_group where item_category in (101) and status_active=1 and is_deleted=0 order by item_name",'id','item_name');
	            foreach($data_array as $row)
	            {  
	                if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
	            	if ($i%2==0)  
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";	
					?>	
					<tr bgcolor="<? echo $bgcolor; ?>" id="search<? echo $i; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value(<? echo $i;?>)" > 
		                <td width="30"><? echo $i; ?></td>
		                <td width="105"><? echo $itemGroup_arr[$row[csf('item_group_id')]]; ?></td>
		                <td width="105"><? echo $trims_section[$row[csf('section_id')]]; ?></td>
		                <td width="50"><? echo $unit_of_measurement[$row[csf('unit_of_measure')]]; ?></td>
		                <td width="200" style="word-break:break-all" ><? echo $row[csf('item_description')]; ?></td>
		                <td width="120" style="word-break:break-all" ><? echo $row[csf('brand_name')]; ?></td>
		                <td>
		                	<? echo $row[csf('id')]; ?>
		                	<input name="txt_prod_id<? echo $i; ?>" id="txt_prod_id<? echo $i; ?>" type="hidden" value="<? echo $row[csf('id')]; ?>" />
		                </td>
		            </tr>
					<? 
		            $i++; 
	            } 
	            ?>
	        </tbody>
	    </table>
		<?    
		exit();
	}

	if ($action == "populate_prod_data") 
	{
	    $ex_data = explode("**", $data);
	    $prod_id =   $ex_data[0] ; 
	    $updateId =   $ex_data[2] ; 
		if(!$prod_id)$prod_id=0;
	    
	    $sql="select id,company_id,item_code,item_description,item_group_id,item_size,current_stock,brand_name,origin,model,sub_group_name,unit_of_measure from product_details_master where status_active=1 and is_deleted=0 and item_category_id=101 and id in($prod_id) and status_active=1 and is_deleted=0";
        $result = sql_select($sql);
        $count=count($result);
        $i=$ex_data[1]+$count;
        //$machine= trim(implode(",",[$value]));
        foreach ($result as $row)
        { 
	        ?>
	        <tr id="row_<? echo $i;?>">
				<td><input type="text" id="txtSl_<? echo $i;?>" name="txtSl_<? echo $i;?>" class="text_boxes" style="width:30px" value="<? echo $i; ?>" />
				</td>
				<td title="<? echo $row[csf('item_description')]; ?>"><input type="text" id="txtdescription_<? echo $i;?>" name="txtdescription[]" class="text_boxes" onClick="openmypage_material(<? echo $company."_".$data[9]; ?>)" placeholder="Click" style="width:120px" value="<? echo $row[csf('item_description')]; ?>" readonly />
					<input type="hidden" id="hiddenProdid_<? echo $i; ?>" name="hiddenProdid[]" value="<? echo $row[csf('id')]; ?>"  style="width:15px;" class="text_boxes" readonly />
				</td>
				<td>
					<input type="text" id="txtSpecification_<? echo $i;?>" name="txtSpecification[]" class="text_boxes" style="width:90px" value="<? echo $row[csf('item_description')]; ?>" <? echo $disabled; ?> readonly /></td>
				<td><?
					echo create_drop_down( "txtUnit_".$tblRow, 80, $unit_of_measurement,"", 1, "-- Select --",$row[csf('unit_of_measure')],"", 1,'','','','','','',"txtUnit[]");
                ?></td>
				<td style="display:none;">
					<input type="text" id="txtUnitPcs_<? echo $i;?>" name="txtUnitPcs[]" onKeyUp="metarial_calculate(<? echo $i;?>)"  class="text_boxes_numeric" style="width:60px" />
				</td>
				<td><input type="text" id="txtConsQty_<? echo $i;?>" name="txtConsQty[]"  class="text_boxes_numeric" style="width:50px" onKeyUp="metarial_calculate(<? echo $i;?>)"  />
				</td>
				<td>
					<input type="text" id="txtProcessLoss_<? echo $i;?>" name="txtProcessLoss[]" class="text_boxes_numeric" style="width:70px"  onKeyUp="process_calculate(<? echo $i;?>)" value=""  />
				</td>
				<td><input type="text" id="txtProcessLossQty_<? echo $i;?>" name="txtProcessLossQty[]"   class="text_boxes_numeric" style="width:70px" readonly /></td>
				<td><input type="text" id="txtReqQty_<? echo $i;?>" name="txtReqQty[]" class="text_boxes_numeric" style="width:70px"  disabled/></td>
				<td><input type="text" id="txtRemarks_<? echo $i;?>" name="txtRemarks[]" class="text_boxes" style="width:70px" /></td>
				<td>
					<input type="hidden" id="hidbookingconsid_<? echo $i; ?>" name="hidbookingconsid[]"  style="width:15px;" class="text_boxes"  />
                    <input type="hidden" id="hiddenid_<? echo $i; ?>" name="hiddenid[]" value="<? echo $updateId; ?>"  style="width:15px;" class="text_boxes"  />
					<input type="button" id="decreaseset_<? echo $i;?>" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(<? echo $i;?> ,'tbl_share_details_entry' );" <? echo $disabled; ?>/>
				</td>  
			</tr>
	        <?
	        $i--;
             
        }
    exit(); 
	}


	if ($action=="job_popup")
	{
		echo load_html_head_contents("Job Popup Info","../../../", 1, 1, $unicode,'','');
		?>
		<script>
			function js_set_value(id)
			{ 
				$("#hidden_mst_id").val(id);
				document.getElementById('selected_job').value=id;
				parent.emailwindow.hide();
			}
			
			function fnc_load_party_popup(type,within_group)
			{
				var company = $('#cbo_company_name').val();
				var party_name = $('#cbo_party_name').val();
				var location_name = $('#cbo_location_name').val();
				var within_group = $('#cbo_within_group').val();
				load_drop_down( 'sub_con_wo_entry_controller', company+'_'+within_group, 'load_drop_down_buyer', 'buyer_td' );
			}
			function search_by(val)
			{
				$('#txt_search_string').val('');
				if(val==1 || val==0){
					$('#search_by_td').html('Job ID');
				}else if(val==2){
					$('#search_by_td').html('W/O No');
				}else if(val==4){
					$('#search_by_td').html('Buyer Po');
				}else if(val==5){
					$('#search_by_td').html('Buyer Style');
				}else if(val==6){
					$('#search_by_td').html('Receive No.');
				}
			}
		</script>
	</head>
	<body>
	<div align="center" style="width:100%;" >
	    <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
	        <table width="940" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
	            <thead> 
	                <tr>
	                    <th colspan="9"><? echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
	                </tr>
	                <tr>
	                    <th width="140" class="must_entry_caption">Company Name</th>
	                    <th width="60">Within Group</th>                           
	                    <th width="140">Party Name</th>
	                    <th width="80">Search By</th>
	                    <th width="100" id="search_by_td">Job ID</th>
	                    <th width="80">Section</th>
	                    <th width="60">Year</th>
	                    <th width="170">Date Range</th>                            
	                    <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:70px;" /></th>
	                </tr>           
	            </thead>
	            <tbody>
	                <tr class="general">
	                    <td><input type="hidden" id="selected_job"><? $data=explode("_",$data); ?>  <!--  echo $data;-->
	                        <? 
	                        echo create_drop_down( "cbo_company_name", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $data[0], "fnc_load_party_popup(1,document.getElementById('cbo_within_group').value);",1); ?>
	                    </td>
	                    <td>
	                        <?php echo create_drop_down( "cbo_within_group", 60, $yes_no,"", 0, "--  --", $data[3], "fnc_load_party_popup(1,this.value);" ); ?>
	                    </td>
	                    <td id="buyer_td">
	                        <? 
							echo create_drop_down( "cbo_party_name", 150, "","", 1, "-- Select Party --", $data[2], "fnc_load_party_popup(1,this.value);" );   	 	 
	                        ?>
	                    </td>
	                    <td>
	                    	<?
	                            $search_by_arr=array(1=>"Job ID",2=>"W/O No",4=>"Buyer Po",5=>"Buyer Style",6=>"Receive No.");
	                            echo create_drop_down( "cbo_type",80, $search_by_arr,"",0, "",1,'search_by(this.value)',0 );
	                        ?>
	                    </td>
	                    <td align="center">
	                        <input type="text" name="txt_search_string" id="txt_search_string" class="text_boxes" style="width:100px" placeholder="" />
	                    </td>
	                    <td><? echo create_drop_down( "cbo_section", 80, $trims_section,"", 1, "-- Select Section --","",'',0,'','','','','','',"cboSection[]"); ?></td>
	                    <td align="center"><? echo create_drop_down( "cbo_year_selection", 60, $year,"", 1, "-- Select --", date('Y'), "",0 ); ?></td>
	                    <td align="center">
	                        <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px">
	                        <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px">
	                    </td>
	                    <td align="center">
	                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_party_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_type').value+'_'+document.getElementById('txt_search_string').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('cbo_within_group').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('cbo_section').value, 'create_job_search_list_view', 'search_div', 'sub_con_wo_entry_controller', 'setFilterGrid(\'tbl_po_list\',-1)')" style="width:70px;" /></td>
	                    </tr>
	                    <tr>
	                        <td colspan="9" align="center" valign="middle">
	                            <? echo load_month_buttons();  ?>
	                            <input type="hidden" name="hidden_mst_id" id="hidden_mst_id" class="datepicker" style="width:70px">
	                        </td>
	                    </tr>
	                    <tr>
	                        <td colspan="9" align="center" valign="top" id=""><div id="search_div"></div></td>
	                    </tr>
	                </tbody>
	            </table>    
	            </form>
	        </div>
	    </body>           
	    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	    </html>
	    <?
	    exit();
	}

	if($action=="create_job_search_list_view")
	{	
		$data=explode('_',$data);
		$party_id=str_replace("'","",$data[1]);
		$search_by=str_replace("'","",$data[4]);
		$search_str=trim(str_replace("'","",$data[5]));
		$search_type =$data[6];
		$within_group =$data[7];
		$section_id =$data[9];
		if($db_type==0) { $year_cond=" and YEAR(a.insert_date)=$data[8]";   }
		if($db_type==2) {$year_cond=" and to_char(a.insert_date,'YYYY')=$data[8]";}
		if($data[0]!=0) $company=" and a.company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
		if($party_id!=0) $party_id_cond=" and a.party_id='$party_id'"; else $party_id_cond="";
		if($section_id!=0) $section_id_cond=" and a.section_id='$section_id'"; else $section_id_cond="";

		if($db_type==0){ 
			if ($data[2]!="" &&  $data[3]!="") $delivery_date = "and a.delivery_date between '".change_date_format($data[2],'yyyy-mm-dd')."' and '".change_date_format($data[3],'yyyy-mm-dd')."'"; else $delivery_date ="";
		}else{
			if ($data[2]!="" &&  $data[3]!="") $delivery_date = "and a.delivery_date between '".change_date_format($data[2], "", "",1)."' and '".change_date_format($data[3], "", "",1)."'"; else $delivery_date ="";
		}
		if ($within_group!=0) $withinGroup=" and a.within_group='$within_group'"; else $withinGroup="";

		
		$job_cond=""; $style_cond=""; $po_cond=""; $search_com_cond="";
		if($search_type==1){
			if($search_str!=""){
				if($search_by==1) $search_com_cond="and a.job_no_prefix_num='$search_str'";
				else if($search_by==2) $search_com_cond="and a.order_no='$search_str'";
				else if ($search_by==4) $search_com_cond=" and b.buyer_po_no = '$search_str' ";
				else if ($search_by==5) $search_com_cond=" and b.buyer_style_ref = '$search_str' ";
				else if ($search_by==6) $search_com_cond=" and a.received_no = '$search_str' ";
			}
		}else if($search_type==2){
			if($search_str!="")
			{
				if($search_by==1) $search_com_cond="and a.job_no_prefix_num like '$search_str%'";  
				else if($search_by==2) $search_com_cond="and a.order_no like '$search_str%'";  
				else if ($search_by==4) $search_com_cond=" and b.buyer_po_no like '$search_str%'";
				else if ($search_by==5) $search_com_cond=" and b.buyer_style_ref like '$search_str%'";
				else if ($search_by==6) $search_com_cond=" and a.received_no like '$search_str%'"; 
			}
		}else if($search_type==3){
			if($search_str!="")
			{
				if($search_by==1) $search_com_cond="and a.job_no_prefix_num like '%$search_str'";  
				else if($search_by==2) $search_com_cond="and a.order_no like '%$search_str'";  
				else if ($search_by==4) $search_com_cond=" and b.buyer_po_no like '%$search_str'";
				else if ($search_by==5) $search_com_cond=" and b.buyer_style_ref like '%$search_str'";
				else if ($search_by==6) $search_com_cond=" and a.received_no like '%$search_str'";
			}
		}else if($search_type==4 || $search_type==0){
			if($search_str!="")
			{
				if($search_by==1) $search_com_cond="and a.job_no_prefix_num like '%$search_str%'";  
				else if($search_by==2) $search_com_cond="and a.order_no like '%$search_str%'";  
				else if ($search_by==4) $search_com_cond=" and b.buyer_po_no like '%$search_str%'"; 
				else if ($search_by==5) $search_com_cond=" and b.buyer_style_ref like '%$search_str%'";
				else if ($search_by==6) $search_com_cond=" and a.received_no like '%$search_str%'";  
			}
		}
		
		if($db_type==0) {
			$ins_year_cond="year(a.insert_date)";
		}else if($db_type==2){
			$ins_year_cond="TO_CHAR(a.insert_date,'YYYY')";
		}
		if($within_group==1){
		$party_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
		}else{
			$party_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
		}	
		
		$sql= "select a.id, a.trims_job, a.job_no_prefix_num, $ins_year_cond as year, a.company_id, a.location_id, a.party_id,  a.order_no,a.received_no,received_id, a.delivery_date,a.section_id 
		from trims_job_card_mst a, trims_job_card_dtls b
		where a.entry_form=257 and a.trims_job=b.job_no_mst and a.id=b.mst_id and a.status_active=1 and b.status_active=1 $delivery_date $company $party_id_cond $search_com_cond  $withinGroup $section_id_cond $year_cond
		group by a.id, a.trims_job, a.job_no_prefix_num, a.insert_date, a.company_id, a.location_id, a.party_id, a.order_no ,a.received_no,received_id,a.delivery_date,a.section_id 
		order by a.id DESC";
		//echo $sql;

		$data_array=sql_select($sql);
		?>
	     <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="785" >
	        <thead>
	            <th width="30">SL</th>
	            <th width="120">Party</th>
	            <th width="120">Job ID</th>
	            <th width="100">Section</th>
	            <th width="60">Year</th>
	            <th width="120">W/O No.</th>
	            <th width="120">Receive No.</th>
	            <th>Delivery Date</th>
	        </thead>
	        </table>
	        <div style="width:785px; max-height:270px;overflow-y:scroll;" >	 
	        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="765" class="rpt_table" id="tbl_po_list">
	        <tbody>
	            <? 
	            $i=1;
	            foreach($data_array as $row)
	            {  
	                if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
	                ?>
	                <tr bgcolor="<? echo $bgcolor; ?>" onClick='js_set_value("<? echo $row[csf('id')].'_'.$row[csf('trims_job')].'_'.$row[csf('received_id')].'_'.$row[csf('received_no')]; ?>")' style="cursor:pointer" >
	                    <td width="30"><? echo $i; ?></td>
	                    <td width="120" ><? echo $party_arr[$row[csf('party_id')]]; ?></td>
	                    <td width="120" style="text-align:center;" ><? echo $row[csf('job_no_prefix_num')]; ?></td>
	                    <td width="100"><? echo $trims_section[$row[csf('section_id')]]; ?></td>
	                    <td width="60" style="text-align:center;"><? echo $row[csf('year')]; ?></td>
	                    <td width="120"><? echo $row[csf('order_no')]; ?></td>
	                    <td width="120"><? echo $row[csf('received_no')]; ?></td>
	                    <td style="text-align:center;"><? echo change_date_format($row[csf('delivery_date')]); ?></td>
	                </tr>
					<? 
	                $i++; 
	            }
	            ?>
	        </tbody>
	    </table>
		<?
		exit();
	}

	if ($action=="load_mst_php_data_to_form")
	{
		//echo "select id, trims_job, company_id, location_id, party_id, currency_id, within_group, delivery_date, party_location, entry_form,  order_id, order_no, order_qty, received_no, received_id, section_id from trims_job_card_mst  where entry_form=257 and id=$data and status_active=1 order by a.id DESC" ;  
		$nameArray=sql_select( "select id, trims_job, company_id, location_id, party_id, currency_id, within_group, delivery_date, party_location, entry_form,  order_id, order_no, order_qty, received_no, received_id, section_id 
		 from trims_job_card_mst  where entry_form=257 and id=$data and status_active=1 order by id DESC" );
		foreach ($nameArray as $row)
		{	
			echo "document.getElementById('update_id').value 				= '".$row[csf("id")]."';\n";
			echo "document.getElementById('txt_job_no').value 				= '".$row[csf("trims_job")]."';\n";
			echo "document.getElementById('txt_recv_no').value 				= '".$row[csf("received_no")]."';\n";
			echo "document.getElementById('hid_recv_id').value 				= '".$row[csf("received_id")]."';\n";
			echo "document.getElementById('cbo_company_name').value 		= '".$row[csf("company_id")]."';\n";
			echo "document.getElementById('cbo_within_group').value 		= '".$row[csf("within_group")]."';\n";  
			echo "$('#cbo_company_name').attr('disabled','true')".";\n";
			echo "fnc_load_party(1,".$row[csf("within_group")].");\n";	
		//echo "load_drop_down( 'requires/sub_con_wo_entry_controller', document.getElementById('cbo_company_name').value, 'load_drop_down_location', 'location_td' );\n";	
			echo "document.getElementById('cbo_location_name').value 		= '".$row[csf("location_id")]."';\n";
		//echo "load_drop_down( 'requires/sub_con_wo_entry_controller', document.getElementById('cbo_company_name').value, 'load_drop_down_buyer', 'buyer_td' );\n";
			echo "document.getElementById('cbo_party_name').value			= '".$row[csf("party_id")]."';\n";
			//cbo_company_name cbo_location_name cbo_within_group cbo_party_name cbo_party_location txt_delivery_date cbo_section txt_order_no txt_order_qty
			echo "fnc_load_party(2,".$row[csf("within_group")].");\n";	 
			echo "document.getElementById('cbo_party_location').value		= '".$row[csf("party_location")]."';\n";	
			echo "document.getElementById('txt_delivery_date').value		= '".change_date_format($row[csf("delivery_date")])."';\n";  
			echo "document.getElementById('hid_order_id').value          	= '".$row[csf("order_id")]."';\n";
			echo "document.getElementById('txt_order_no').value         	= '".$row[csf("order_no")]."';\n";
			echo "document.getElementById('txt_order_qty').value         	= '".$row[csf("order_qty")]."';\n";
			echo "document.getElementById('cbo_section').value        		= '".$row[csf("section_id")]."';\n";
			echo "$('#txt_order_no').attr('disabled','true')".";\n";
			echo "$('#cbo_within_group').attr('disabled','true')".";\n";
			echo "$('#cbo_party_name').attr('disabled','true')".";\n";
			//echo "document.getElementById('update_id').value          		= '".$row[csf("id")]."';\n";	
			//echo "set_button_status(1,'".$_SESSION['page_permission']."', 'fnc_job_order_entry',1);\n";	
		}
		exit();	
	}


	if($action=="color_popup")
	{
		extract($_REQUEST);
		echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
		//echo $style_id;die;

		?>
	    <script>
			/*function js_set_value(data)
			{
				$('#txt_selected_no').val(data);
				parent.emailwindow.hide();
			}*/
			var str_color = [<? echo substr(return_library_autocomplete( "select color_name from lib_color group by color_name ", "color_name" ), 0, -1); ?> ];

			function set_auto_complete(type)
			{
				if(type=='color_return')
				{
					$(".txt_color").autocomplete({
						source: str_color
					});
				}
			}
			function fnc_close()
			{
				var tot_row=$('#tbl_color_list tbody tr').length;
				var data_break_down="";
				for(var i=1; i<=tot_row; i++)
				{
					if (form_validation('txtRowColor_'+i,'Color')==false)
					{
						return;
					}

					if($("#txtRowColor_"+i).val()=="") $("#txtRowColor_"+i).val(0)
					if(data_break_down=="")
					{
						data_break_down+=$('#txtRowColor_'+i).val();
					}
					else
					{
						data_break_down+="__"+$('#txtRowColor_'+i).val();
					}
				}
				//alert(data_break_down);
				$('#hidden_break_tot_row').val( data_break_down );
				parent.emailwindow.hide();
			}

	    </script>
	    </script>
</head>
<body onLoad="set_auto_complete('color_return');">
	<div align="center" style="width:100%;" >
		<form name="qntypopup_1"  id="qntypopup_1" autocomplete="off">
	    <?
		$company=str_replace("'","",$company);
		$type=str_replace("'","",$type);
		$impression=str_replace("'","",$impression);
		$hdnRawcolor=str_replace("'","",$hdnRawcolor);
		$rawcolor=explode("__",$hdnRawcolor);
		$rawColorRow=count($rawcolor);
		
		?>
		<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="250" >
	        <thead>
	            <th width="30">SL</th>
	            <th width="220" class="must_entry_caption">Color</th>
	        </thead>
	        </table>
	        
	        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="250" class="rpt_table" id="tbl_color_list">
	        <tbody>
	        	<input type="hidden" name="hidden_break_tot_row" id="hidden_break_tot_row" class="text_boxes" style="width:90px" />
	            <?
	            //echo $rawColorRow; die;
	            //print_r($rawcolor); die;
	            if($rawColorRow>0)
	            {	
	            	$x=1;
	            	for($i=0; $i<$impression;$i++)
		            {
		            	if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
		                ?>
		                <tr bgcolor="<? echo $bgcolor; ?>">
		                    <td width="30"><? echo $x; ?></td>
		                    <td width="220"><input class="text_boxes txt_color" type="text" name="txtRowColor[]" id="txtRowColor_<? echo $x ?>" value="<? echo $rawcolor[$i]; ?>" style="width:207px;"/></td>
		                </tr>
						<? 
						$x++;
		            }
	            } 
	            else
	            {

	            	for($i=1; $i<=$impression;$i++)
		            {
		            	if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
		                ?>
		                <tr bgcolor="<? echo $bgcolor; ?>">
		                    <td width="30"><? echo $i; ?></td>
		                    <td width="220"><input class="text_boxes" type="text" name="txtRowColor[]" id="txtRowColor_<? echo $i ?>" style="width:207px;"/></td>
		                </tr>
						<? 
		            }
	            }
	            ?>
	        </tbody>
	    </table>
	    <table>
				<tr>
					<td align="center"><input type="button" name="main_close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" /></td>
				</tr>
			</table>
		</form>
	
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

