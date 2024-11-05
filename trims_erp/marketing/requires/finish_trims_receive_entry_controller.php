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

if ($action=="load_drop_down_location_pop")
{
	echo create_drop_down( "cbo_location_name", 160,"select id,location_name from lib_location where company_id=$data and is_deleted=0  and status_active=1  $location_credential_cond","id,location_name", 1, "-- Select --", $selected, "" );
	die;
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

if ($action=="load_room_rack_self_bin")
{
	load_room_rack_self_bin("requires/finish_trims_receive_entry_controller",$data);
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
			load_drop_down( 'finish_trims_receive_entry_controller', company+'_'+within_group, 'load_drop_down_buyer', 'buyer_td' );
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
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_party_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_type').value+'_'+document.getElementById('txt_search_string').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('cbo_within_group').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('cbo_item_group').value, 'create_order_search_list_view', 'search_div', 'finish_trims_receive_entry_controller', 'setFilterGrid(\'tbl_po_list\',-1)')" style="width:70px;" /></td>
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

	if($src_for_order==2)
	{
		$search_com_cond.="and b.source_for_order=2";
	} else {
		$search_com_cond.="and b.source_for_order in(0,1)";
	}


	$sql= "select a.id, a.subcon_job, a.job_no_prefix_num, a.company_id, a.location_id, a.party_id, a.receive_date, a.delivery_date ,a.order_no
	from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c  
	where a.entry_form=255 and a.id=b.mst_id and b.id=c.mst_id  and b.order_quantity<>0 and c.qnty<>0  and a.status_active=1 and b.status_active=1 and c.status_active=1 $order_rcv_date $company $party_id_cond $withinGroup $search_com_cond $section $trim_group_cond
	group by a.id, a.subcon_job, a.job_no_prefix_num, a.company_id, a.location_id, a.party_id, a.receive_date, a.delivery_date, a.order_no
	order by a.id DESC";// and a.subcon_job=b.job_no_mst  and b.job_no_mst=c.job_no_mst

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
    <table style="width:100%; float:left" align="center">
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
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_party_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_type').value+'_'+document.getElementById('txt_search_string').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('cbo_within_group').value+'_'+document.getElementById('cbo_year_selection').value, 'create_wo_search_list_view', 'search_div', 'finish_trims_receive_entry_controller', 'setFilterGrid(\'tbl_po_list\',-1)')" style="width:70px;" /></td>
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
 
if ($action=="purchase_requisition_popup")
{
 	echo load_html_head_contents("Popup Info", "../../../", 1, 1, $unicode,'','');
	extract($_REQUEST);
?>
<script>
	  function js_set_value(id)
	  {
		  document.getElementById('selected_job').value=id;
		  parent.emailwindow.hide();
	  }
</script>
</head>
<body>
<div align="center" style="width:100%;" >
<form name="purchaserequisition_2"  id="purchaserequisition_2" autocomplete="off">
	<table width="900" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
    	<tr>
        	<td align="center" width="100%">
            	<table  cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all" uwidth="900">
                    <thead>
                        <th width="180">Company Name</th>
                        <th width="50" style="display:none">Item Category</th>
                        <th width="100">Location</th>
                        <th width="100">Requisition No</th>
                        <th width="200">Date Range</th>
                        <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:100px;" /></th>
                    </thead>
        			<tr class="general">
                    	<td align="center"> <input type="hidden" id="selected_job">
							<?
								/*echo create_drop_down( "cbo_company_name", 152, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond $company_credential_cond order by company_name","id,company_name", 1, "-- Select Company --", $cbo_company_name, "",1);*/

								echo create_drop_down( "cbo_company_name", 160,"select id,company_name from lib_company comp where is_deleted=0 and status_active=1 $company_cond $company_credential_cond order by company_name","id,company_name", 1, "-- Select --", $cbo_company_name, "load_drop_down( 'finish_trims_purchase_requisition_controller', this.value, 'load_drop_down_location_pop','location_td');" );
                            ?>
                    	</td>
                   		<td style="display:none">
							<?
								echo create_drop_down( "cbo_item_category_id", 50, $item_category,"", 1, "-- Select --", $selected, "","","","","","1,2,3,12,13,14,24,25");
                            ?>
                        </td>
                        <td align="center" id="location_td">
							<?
								echo create_drop_down( "cbo_location_name", 160, $blank_array, "", 1, "-- Select --", $selected, "" );
                            ?>
                        </td>
                        <td align="center">

                            <input name="txt_requisition_no" id="txt_requisition_no" class="text_boxes" style="width:100px">
					 	</td>
                    	<td align="center">
                            <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px">
                            <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px">
					 	</td>
                        <td align="center">
                            <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_location_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_requisition_no').value, 'purchase_requisition_list_view', 'search_div', 'finish_trims_receive_entry_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
                       </td>
        			</tr>
             	</table>
          	</td>
        </tr>
        <tr>
            <td  align="center" height="40" valign="middle">
				<? echo load_month_buttons(1);  ?>
            </td>
        </tr>
        <tr>
            <td align="center" valign="top" id="search_div">
            </td>
        </tr>
    </table>
    </form>
   </div>
</body>
<script>
	load_drop_down( 'finish_trims_purchase_requisition_controller', document.getElementById('cbo_company_name').value, 'load_drop_down_location','location_td');
</script>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if($action=="purchase_requisition_list_view")
{
	$data=explode('_',$data);
	if ($data[0]!=0) $company=" and company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
	// if ($data[1]!=0) $item_category_id=" and item_category_id='$data[1]'"; else $item_category_id="";//{ echo "Please Select item category."; die; }

	$requisition_no=trim(str_replace("'","",$data[4]));
	//if(str_replace("'","",$requisition_no)!="") $get_cond .= "and requ_prefix_num  = '".trim(str_replace("'","",$requisition_no))."'  "; else  $get_cond="";

	if(str_replace("'","",$requisition_no)!="") $get_cond .= "and requ_no like '%$requisition_no'  "; else  $get_cond="";
	//echo $requisition_no;

	$location_cond = ($data[1]) ? " and location_id = '" . $data[1] ."'" :  "";

	if($db_type==0)
				{
	if ($data[2]!="" &&  $data[3]!="") $order_rcv_date = " and requisition_date between '".change_date_format($data[2], "yyyy-mm-dd", "-")."' and '".change_date_format($data[3], "yyyy-mm-dd", "-")."'"; else $order_rcv_date ="";
				}
	if($db_type==2)
	{
	if ($data[2]!="" &&  $data[3]!="") $order_rcv_date = " and requisition_date between '".change_date_format($data[2], 'mm-dd-yyyy','/',1)."' and '".change_date_format($data[3], 'mm-dd-yyyy','/',1)."'"; else $order_rcv_date ="";
	}

	$userCredential = sql_select("SELECT unit_id as company_id, store_location_id, item_cate_id, company_location_id as location_id FROM user_passwd where id=$user_id");
	$cre_company_id = $userCredential[0][csf('company_id')];
	$cre_location_id = $userCredential[0][csf('location_id')];
	$cre_store_location_id = $userCredential[0][csf('store_location_id')];
	$cre_item_cate_id = $userCredential[0][csf('item_cate_id')];

	$credientian_cond="";
	//if($cre_company_id>0) $credientian_cond=" and company_id in($cre_company_id)";
	//if($cre_location_id>0) $credientian_cond.=" and location_id in($cre_location_id)";
	//if($cre_store_location_id>0) $credientian_cond.=" and store_name in($cre_store_location_id)";
	//if($cre_item_cate_id!="") $credientian_cond.=" and b.item_category in($cre_item_cate_id)";


	$sql= "select id, requ_prefix_num, requisition_date, company_id, location_id, manual_req
	from trims_finish_purchase_req_mst
	where status_active=1 and is_deleted=0 $company $order_rcv_date $get_cond $credientian_cond $location_cond order by id desc";
	// echo $sql;

	$company=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$location=return_library_array("select id,location_name from lib_location",'id','location_name');
	/*$department=return_library_array("select id,department_name from lib_department",'id','department_name');
	$section=return_library_array("select id,section_name from lib_section",'id','section_name');
	$store=return_library_array("select id,store_name from lib_store_location where company_id='$data[0]' and status_active=1",'id','store_name');*/
	//$arr=array (2=>$company,3=>$item_category,4=>$location,5=>$department,6=>$section,7=>$store);

	$arr=array (2=>$company,3=>$location);

	echo create_list_view("list_view", "Requisition No,Requisition Date,Company,Location,Manual Req", "80,80,150,150","600","250",0, $sql , "js_set_value", "id", "",1,"0,0,company_id,location_id,0", $arr , "requ_prefix_num,requisition_date,company_id,location_id,manual_req","purchase_requisition_controller", '', '0,3,0,0,0,0,0,0,0') ;
	exit();
}


if ($action=="load_php_data_to_form")
{
	$nameArray=sql_select( "select id, entry_form, subcon_job, job_no_prefix, job_no_prefix_num, company_id, location_id, order_rcv_id,order_rcv_no, supplier_id, currency_id, paymode_id, source_id, exchange_rate, wo_date, delivery_date, attention from trims_subcon_ord_mst where id='$data' and entry_form=450 and status_active=1 and is_deleted=0" );
	foreach ($nameArray as $row)
	{	
		echo "document.getElementById('txt_wo_no').value 				= '".$row[csf("subcon_job")]."';\n";
		echo "document.getElementById('hid_wo_id').value          		= '".$row[csf("id")]."';\n";  
		echo "document.getElementById('cbo_company_name').value 		= '".$row[csf("company_id")]."';\n";
		echo "$('#cbo_company_name').attr('disabled','true')".";\n";
		echo "load_drop_down( 'requires/finish_trims_receive_entry_controller', document.getElementById('cbo_company_name').value, 'load_drop_down_location', 'location_td' );\n";	
		echo "load_drop_down( 'requires/finish_trims_receive_entry_controller', document.getElementById('cbo_company_name').value, 'load_supplier_dropdown', 'supplier_td' );\n";	
		echo "document.getElementById('cbo_supplier_id').value			= '".$row[csf("supplier_id")]."';\n";

		echo "document.getElementById('cbo_location_name').value 		= '".$row[csf("location_id")]."';\n";
		echo "document.getElementById('cbo_currency').value				= '".$row[csf("currency_id")]."';\n";
		echo "document.getElementById('cbo_source_id').value			= '".$row[csf("source_id")]."';\n";
		echo "document.getElementById('txt_exchange_rate').value        = '".$row[csf("exchange_rate")]."';\n";
		
		//echo "document.getElementById('cbo_paymode_id').value			= '".$row[csf("paymode_id")]."';\n";
		//echo "document.getElementById('txt_attention').value			= '".$row[csf("attention")]."';\n";
		//echo "document.getElementById('txt_wo_date').value				= '".change_date_format($row[csf("wo_date")])."';\n"; 
		//echo "document.getElementById('txt_delivery_date').value		= '".change_date_format($row[csf("delivery_date")])."';\n"; 
		
		echo "$('#cbo_location_name').attr('disabled','true')".";\n";
		echo "$('#cbo_supplier_id').attr('disabled','true')".";\n";
		echo "$('#cbo_currency').attr('disabled','true')".";\n";
		echo "$('#cbo_source_id').attr('disabled','true')".";\n";
		echo "$('#txt_exchange_rate').attr('disabled','true')".";\n";
		echo "set_button_status(0,'".$_SESSION['page_permission']."', 'fnc_job_order_entry',1);\n";	
	}
	exit();	
}

if ($action=="load_php_req_data_to_form")
{
	$nameArray=sql_select( "select id, requ_no, company_id, location_id, requisition_date, pay_mode, source, manual_req, currency_id, delivery_date, req_by, remarks, template_id, status_active from trims_finish_purchase_req_mst where id='$data' and is_deleted=0");
	foreach ($nameArray as $row)
	{	
		echo "document.getElementById('txt_wo_no').value 				= '".$row[csf("requ_no")]."';\n";
		echo "document.getElementById('hid_wo_id').value          		= '".$row[csf("id")]."';\n";  
		echo "document.getElementById('cbo_company_name').value 		= '".$row[csf("company_id")]."';\n";
		echo "$('#cbo_company_name').attr('disabled','true')".";\n";
		echo "load_drop_down( 'requires/finish_trims_receive_entry_controller', document.getElementById('cbo_company_name').value, 'load_drop_down_location', 'location_td' );\n";	
		echo "load_drop_down( 'requires/finish_trims_receive_entry_controller', document.getElementById('cbo_company_name').value, 'load_supplier_dropdown', 'supplier_td' );\n";	
		//echo "document.getElementById('cbo_supplier_id').value			= '".$row[csf("supplier_id")]."';\n";

		echo "document.getElementById('cbo_location_name').value 		= '".$row[csf("location_id")]."';\n";
		echo "document.getElementById('cbo_currency').value				= '".$row[csf("currency_id")]."';\n";
		//echo "document.getElementById('cbo_source_id').value			= '".$row[csf("source_id")]."';\n";
		echo "document.getElementById('txt_exchange_rate').value        = '".$row[csf("exchange_rate")]."';\n";
		
		//echo "document.getElementById('cbo_paymode_id').value			= '".$row[csf("paymode_id")]."';\n";
		//echo "document.getElementById('txt_attention').value			= '".$row[csf("attention")]."';\n";
		//echo "document.getElementById('txt_wo_date').value				= '".change_date_format($row[csf("wo_date")])."';\n"; 
		//echo "document.getElementById('txt_delivery_date').value		= '".change_date_format($row[csf("delivery_date")])."';\n"; 
		
		echo "$('#cbo_location_name').attr('disabled','true')".";\n";
		//echo "$('#cbo_supplier_id').attr('disabled','true')".";\n";
		echo "$('#cbo_currency').attr('disabled','true')".";\n";
		//echo "$('#cbo_source_id').attr('disabled','true')".";\n";
		echo "$('#txt_exchange_rate').attr('disabled','true')".";\n";
		echo "set_button_status(0,'".$_SESSION['page_permission']."', 'fnc_job_order_entry',1);\n";	
	}
	exit();	
}


if( $action=='order_dtls_list_view' ) 
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
	
	//echo "select a.id, a.mst_id, a.job_no_mst, a.description, a.color_id, a.size_id, a.qnty, a.rate, a.amount, a.order_rcv_break_id, a.order_rcv_id ,b.id as dtls_id ,b.order_quantity as wo_qnty, b.order_uom, b.rate, b.amount, b.section, b.sub_section, b.item_group  as trim_group , b.order_rcv_dtls_id from trims_subcon_ord_breakdown a , trims_subcon_ord_dtls b where a.job_no_mst=b.job_no_mst and b.id=a.mst_id and b.mst_id =$mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by b.id";

	$rcv_qty_result=sql_select( "select a.order_rcv_id , a.wo_id , a.wo_dtls_id, a.wo_break_id, a.rcv_qty from trims_receive_dtls a  where entry_form =451 and a.status_active=1 and a.is_deleted=0 ");		
	foreach ($rcv_qty_result as  $row) 
	{
		//echo $mstIDS.'=='.$ids.'=='.$book_con_dtls_ids.'++'; 
		$rec_qty_arr[$row[csf("order_rcv_id")]][$row[csf("wo_break_id")]]["cum_qty"] +=$row[csf("rcv_qty")];
	}

	$sql="select a.id, a.mst_id, a.job_no_mst, a.description, a.color_id, a.size_id, a.qnty, a.rate, a.amount, a.order_rcv_break_id, a.order_rcv_id ,b.id as dtls_id ,b.order_quantity as wo_qnty, b.order_uom, b.rate, b.amount, b.section, b.sub_section, b.item_group  as trim_group , b.order_rcv_dtls_id, b.order_rcv_id  from trims_subcon_ord_breakdown a , trims_subcon_ord_dtls b where a.job_no_mst=b.job_no_mst and b.id=a.mst_id and b.mst_id =$mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by b.id";		

	$qry_result=sql_select(	$sql );
	foreach ($qry_result as  $row) 
	{
		//echo $mstIDS.'=='.$ids.'=='.$book_con_dtls_ids.'++'; 
		$wo_arr[$row[csf("order_rcv_id")]][$row[csf("id")]]["description"] =$row[csf("description")];
		$wo_arr[$row[csf("order_rcv_id")]][$row[csf("id")]]["color_id"] =$row[csf("color_id")];
		$wo_arr[$row[csf("order_rcv_id")]][$row[csf("id")]]["size_id"] =$row[csf("size_id")];
		$wo_arr[$row[csf("order_rcv_id")]][$row[csf("id")]]["qnty"] =$row[csf("qnty")];
		$wo_arr[$row[csf("order_rcv_id")]][$row[csf("id")]]["dtls_id"] =$row[csf("dtls_id")];
		$wo_arr[$row[csf("order_rcv_id")]][$row[csf("id")]]["order_uom"] =$row[csf("order_uom")];
		$wo_arr[$row[csf("order_rcv_id")]][$row[csf("id")]]["section"] =$row[csf("section")];
		$wo_arr[$row[csf("order_rcv_id")]][$row[csf("id")]]["sub_section"] =$row[csf("sub_section")];
		$wo_arr[$row[csf("order_rcv_id")]][$row[csf("id")]]["trim_group"] =$row[csf("trim_group")];
	}
	//echo "<pre>";
	//print_r($wo_arr);
	$tblRow=1; $i=1;
	foreach($wo_arr as $rcv_id=> $rcv_id_data)
	{
	?>
		<thead>
			<tr bgcolor="<? echo $bgcolor; ?>" id="row_<? echo $tblRow; ?>" align="center">
				<th width="150" class="must_entry_caption">Order No :
				</th>
				<th colspan="2" align="left"><? echo $order_receive_arr[$rcv_id];?><input type="hidden" name="hdnWoId" id="hdnWoId_<? echo $i; ?>"></th>
				<th colspan="9">&nbsp;</th>
			</tr>
			<tr>
        		<th width="110" class="must_entry_caption">Section</th>
                <th width="110" >Sub Section</th>
                <th width="110" class="must_entry_caption">Trims Group</th>
                <th width="170">Item Description</th>
                <th width="100">Item Color</th>
                <th width="90">Item Size</th>
                <th width="60">Order UOM</th>
                <th width="60" class="must_entry_caption">WO Qty</th>
                <th width="60">Cum. Receive Qty</th>
                <th width="70" class="must_entry_caption">Receive Qnty</th>
                <th width="70">Received Balance</th>
                <th width="110">Remarks</th>
        	</tr>
		</thead>
		<tbody>
		<?
		foreach($rcv_id_data as $woBrkId=> $rows)
		{ 
			$cum_rcv_qty=$rec_qty_arr[$rcv_id][$woBrkId]["cum_qty"];
			$wo_qty=$rows['qnty'];
			$balance=$wo_qty-$cum_rcv_qty;
			?>
			<tr bgcolor="<? echo $bgcolor; ?>" id="row_<? echo $tblRow; ?>" align="center">
				<td><?

					//$dataBreakDtls=$data_dreak_arr[$dtls_id];
					if($rows['section']==1) $subID='1,2,3,23,29,30,31,32,33,34,35,36,37,38,39,40';
					else if($rows['section']==3) $subID='4,5,18';
					else if($rows['section']==5) $subID='6,7,8,9,10,11,12,13,16,17,17,24';
					else if($rows['section']==10) $subID='14,15';
					else if($rows['section']==7) $subID='19,20,21,25,26,27,28';
					else if($rows['section']==9) $subID='22';
					else $subID='0';
					echo create_drop_down( "cboSection_".$tblRow, 110, $trims_section,"", 1, "-- Select Section --",$rows['section'],"load_sub_section($tblRow)",1,'','','','','','',"cboSection[]"); ?></td>
				<td id="subSectionTd_<? echo $tblRow; ?>"><? echo create_drop_down( "cboSubSection_".$tblRow, 110, $trims_sub_section,"", 1, "-- Select Section --",$rows['sub_section'],"load_sub_section_value($tblRow)",1,$subID,'','','','','',"cboSubSection[]"); ?></td>			
				<td><? echo create_drop_down( "cboItemGroup_".$tblRow, 110, "select id, item_name from lib_item_group where item_category=4 and status_active=1","id,item_name", 1, "-- Select --",$rows['trim_group'], "",1,'','','','','','',"cboItemGroup[]"); ?></td>
				<td><input id="txtItemDesc_<? echo $tblRow; ?>" name="txtItemDesc[]" value="<? echo $rows['description']; ?>" type="text"  class="text_boxes_numeric" style="width:157px" readonly/></td>
				<td><input id="txtColorName_<? echo $tblRow; ?>" name="txtColorName[]" value="<? echo $color_library[$rows['color_id']]; ?>" type="text"  class="text_boxes_numeric" style="width:87px" readonly/>
					<input id="txtColorId_<? echo $tblRow; ?>" name="txtColorId[]" value="<? echo  $rows['color_id']; ?>" type="hidden"  class="text_boxes_numeric" style="width:87px" readonly/></td>
				<td><input id="txtSizeName_<? echo $tblRow; ?>" name="txtSizeName[]" value="<? echo $size_arr[$rows['size_id']]; ?>" type="text"  class="text_boxes_numeric" style="width:77px" readonly/>
					<input id="txtSizeId_<? echo $tblRow; ?>" name="txtSizeId[]" value="<? echo $rows['size_id']; ?>" type="hidden"  class="text_boxes_numeric" style="width:77px" readonly/></td>

				<td><? echo create_drop_down( "cboUom_".$tblRow, 60, $unit_of_measurement,"", 1, "-- Select --",$rows['order_uom'],"", 1,'','','','','','',"cboUom[]"); ?>	</td>
				
				<td><input id="txtWoQty_<? echo $tblRow; ?>" name="txtWoQty[]" value="<? echo number_format($wo_qty,4,'.',''); ?>" type="text"  class="text_boxes_numeric" style="width:47px" readonly/></td>
				<td><input id="txtCumRcvQty_<? echo $tblRow; ?>" name="txtCumRcvQty[]" value="<? echo number_format($cum_rcv_qty,4,'.',''); ?>" type="text"  class="text_boxes_numeric" style="width:47px" readonly/></td>
				<td><input id="txtRcvQty_<? echo $tblRow; ?>" name="txtRcvQty[]" value="" type="text"  class="text_boxes_numeric" style="width:57px"/></td>
				
				<td><input id="txtRcvBal_<? echo $tblRow; ?>" name="txtRcvBal[]"  value="<? echo number_format($balance,4,'.',''); ?>"  type="text" style="width:57px"  class="text_boxes_numeric"  disabled />
	            </td>
	            <td><input id="txtRemark_<? echo $tblRow; ?>" name="txtRemark[]" value="" type="text"  class="text_boxes" style="width:97px"/>
	            	<input type="hidden" id="hdnDtlsUpdateId_<? echo $tblRow; ?>" name="hdnDtlsUpdateId[]"   value="" class="text_boxes_numeric" style="width:40px" />
	            	<input type="hidden" id="hdnWoBrkId_<? echo $tblRow; ?>" name="hdnWoBrkId[]" type="hidden"  value="<? echo  $woBrkId; ?>" class="text_boxes_numeric" style="width:40px" />
					<input type="hidden" id="hdnRcvId_<? echo $tblRow; ?>" name="hdnRcvId[]" value="<? echo $rcv_id; ?>">
					<input type="hidden" id="hdnWoDtlsId_<? echo $tblRow; ?>" name="hdnWoDtlsId[]" value="<? echo $rows['dtls_id']; ?>">
				</td>
			</tr>
			<?
			$tblRow++;
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

if( $action=='order_dtls_req_list_view' ) 
{
	//echo $data; die;2_FAL-TOR-19-00092_1_3895
	$data=explode('_',$data);
	$buttonStatus=$data[0];
	$mst_id=$data[1];
	$job_no=$data[2];
	$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name" );
	$size_arr=return_library_array( "select id,size_name from lib_size",'id','size_name');
	$requ_no_arr=return_library_array( "select id,requ_no from trims_finish_purchase_req_mst",'id','requ_no');
	$tblRow=0;
	
	//echo "select a.id, a.mst_id, a.job_no_mst, a.description, a.color_id, a.size_id, a.qnty, a.rate, a.amount, a.order_rcv_break_id, a.order_rcv_id ,b.id as dtls_id ,b.order_quantity as wo_qnty, b.order_uom, b.rate, b.amount, b.section, b.sub_section, b.item_group  as trim_group , b.order_rcv_dtls_id from trims_subcon_ord_breakdown a , trims_subcon_ord_dtls b where a.job_no_mst=b.job_no_mst and b.id=a.mst_id and b.mst_id =$mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by b.id";

	$rcv_qty_result=sql_select( "select a.order_rcv_id , a.wo_id , a.wo_dtls_id, a.wo_break_id, a.rcv_qty from trims_receive_dtls a  where entry_form =451 and a.status_active=1 and a.is_deleted=0 ");
	foreach ($rcv_qty_result as  $row) 
	{
		//echo $mstIDS.'=='.$ids.'=='.$book_con_dtls_ids.'++'; 
		$rec_qty_arr[$row[csf("order_rcv_id")]][$row[csf("wo_break_id")]]["cum_qty"] +=$row[csf("rcv_qty")];
	}

	/*$sql="select a.id, a.mst_id, a.job_no_mst, a.description, a.color_id, a.size_id, a.qnty, a.rate, a.amount, a.order_rcv_break_id, a.order_rcv_id ,b.id as dtls_id ,b.order_quantity as wo_qnty, b.order_uom, b.rate, b.amount, b.section, b.sub_section, b.item_group  as trim_group , b.order_rcv_dtls_id, b.order_rcv_id  from trims_subcon_ord_breakdown a , trims_subcon_ord_dtls b where a.job_no_mst=b.job_no_mst and b.id=a.mst_id and b.mst_id =$mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by b.id";	*/	

	$sql="select b.id as dtls_id, b.mst_id as order_rcv_id, b.item_group_id as trim_group, b.item_description as description, b.color_id as color_id, b.size_id as size_id, b.uom as order_uom, b.quantity as qnty, b.rate, b.amount, b.remarks, b.status_active
		from trims_finish_purchase_req_dtls b
		where b.mst_id=$mst_id";
	$qry_result=sql_select(	$sql );
	foreach ($qry_result as  $row) 
	{
		//echo $mstIDS.'=='.$ids.'=='.$book_con_dtls_ids.'++'; 
		$req_arr[$row[csf("mst_id")]][$row[csf("dtls_id")]]["description"] =$row[csf("description")];
		$req_arr[$row[csf("mst_id")]][$row[csf("dtls_id")]]["color_id"] =$row[csf("color_id")];
		$req_arr[$row[csf("mst_id")]][$row[csf("dtls_id")]]["size_id"] =$row[csf("size_id")];
		$req_arr[$row[csf("mst_id")]][$row[csf("dtls_id")]]["qnty"] =$row[csf("qnty")];
		$req_arr[$row[csf("mst_id")]][$row[csf("dtls_id")]]["dtls_id"] =$row[csf("dtls_id")];
		$req_arr[$row[csf("mst_id")]][$row[csf("dtls_id")]]["order_uom"] =$row[csf("order_uom")];
		//$req_arr[$row[csf("mst_id")]][$row[csf("dtls_id")]]["section"] =$row[csf("section")];
		//$req_arr[$row[csf("mst_id")]][$row[csf("dtls_id")]]["sub_section"] =$row[csf("sub_section")];
		$req_arr[$row[csf("mst_id")]][$row[csf("dtls_id")]]["trim_group"] =$row[csf("trim_group")];
	}
	//echo "<pre>";
	//print_r($req_arr);
	$tblRow=1; $i=1;
	foreach($req_arr as $req_id=> $req_id_data)
	{
	?>
		<thead>
			<tr bgcolor="<? echo $bgcolor; ?>" id="row_<? echo $tblRow; ?>" align="center" style="display: none;">
				<th width="150" class="must_entry_caption">Order No :
				</th>
				<th colspan="2" align="left"><? echo $requ_no_arr[$req_id];?><input type="hidden" name="hdnWoId" id="hdnWoId_<? echo $i; ?>"></th>
				<th colspan="9">&nbsp;</th>
			</tr>
			<tr>
        		<th width="110">Section</th>
                <th width="110" >Sub Section</th>
                <th width="110" class="must_entry_caption">Trims Group</th>
                <th width="170">Item Description</th>
                <th width="100">Item Color</th>
                <th width="90">Item Size</th>
                <th width="60">Order UOM</th>
                <th width="60" class="must_entry_caption">WO Qty</th>
                <th width="60">Cum. Receive Qty</th>
                <th width="70" class="must_entry_caption">Receive Qnty</th>
                <th width="70">Received Balance</th>
                <th width="110">Remarks</th>
        	</tr>
		</thead>
		<tbody>
		<?
		foreach($req_id_data as $reqDtlsId=> $rows)
		{ 
			$cum_rcv_qty=$rec_qty_arr[$rcv_id][$woBrkId]["cum_qty"];
			$wo_qty=$rows['qnty'];
			$balance=$wo_qty-$cum_rcv_qty;
			?>
			<tr bgcolor="<? echo $bgcolor; ?>" id="row_<? echo $tblRow; ?>" align="center">
				<td><?
					//$dataBreakDtls=$data_dreak_arr[$dtls_id];
					/*if($rows['section']==1) $subID='1,2,3';
					else if($rows['section']==3) $subID='4,5,18';
					else if($rows['section']==5) $subID='6,7,8,9,10,11,12,13';
					else if($rows['section']==10) $subID='14,15';
					else if($rows['section']==7) $subID='19,20';
					else $subID='0';*/
					echo create_drop_down( "cboSection_".$tblRow, 110, $trims_section,"", 1, "-- Select Section --",0,"",1,'','','','','','',"cboSection[]"); ?></td>
				<td id="subSectionTd_<? echo $tblRow; ?>"><? echo create_drop_down( "cboSubSection_".$tblRow, 110, $trims_sub_section,"", 1, "-- Select Section --",$rows['sub_section'],"load_sub_section_value($tblRow)",1,$subID,'','','','','',"cboSubSection[]"); ?></td>			
				<td><? echo create_drop_down( "cboItemGroup_".$tblRow, 110, "select id, item_name from lib_item_group where item_category=4 and status_active=1","id,item_name", 1, "-- Select --",$rows['trim_group'], "",1,'','','','','','',"cboItemGroup[]"); ?></td>
				<td><input id="txtItemDesc_<? echo $tblRow; ?>" name="txtItemDesc[]" value="<? echo $rows['description']; ?>" type="text"  class="text_boxes_numeric" style="width:157px" readonly/></td>
				<td><input id="txtColorName_<? echo $tblRow; ?>" name="txtColorName[]" value="<? echo $color_library[$rows['color_id']]; ?>" type="text"  class="text_boxes" style="width:87px" readonly/>
					<input id="txtColorId_<? echo $tblRow; ?>" name="txtColorId[]" value="<? echo  $rows['color_id']; ?>" type="hidden"  class="text_boxes_numeric" style="width:87px" readonly/></td>
				<td><input id="txtSizeName_<? echo $tblRow; ?>" name="txtSizeName[]" value="<? echo $size_arr[$rows['size_id']]; ?>" type="text"  class="text_boxes" style="width:77px" readonly/>
					<input id="txtSizeId_<? echo $tblRow; ?>" name="txtSizeId[]" value="<? echo $rows['size_id']; ?>" type="hidden"  class="text_boxes_numeric" style="width:77px" readonly/></td>

				<td><? echo create_drop_down( "cboUom_".$tblRow, 60, $unit_of_measurement,"", 1, "-- Select --",$rows['order_uom'],"", 1,'','','','','','',"cboUom[]"); ?>	</td>
				
				<td><input id="txtWoQty_<? echo $tblRow; ?>" name="txtWoQty[]" value="<? echo number_format($wo_qty,4,'.',''); ?>" type="text"  class="text_boxes_numeric" style="width:47px" readonly/></td>
				<td><input id="txtCumRcvQty_<? echo $tblRow; ?>" name="txtCumRcvQty[]" value="<? echo number_format($cum_rcv_qty,4,'.',''); ?>" type="text"  class="text_boxes_numeric" style="width:47px" readonly/></td>
				<td><input id="txtRcvQty_<? echo $tblRow; ?>" name="txtRcvQty[]" value="" type="text"  class="text_boxes_numeric" style="width:57px"/></td>
				
				<td><input id="txtRcvBal_<? echo $tblRow; ?>" name="txtRcvBal[]"  value="<? echo number_format($balance,4,'.',''); ?>"  type="text" style="width:57px"  class="text_boxes_numeric"  disabled />
	            </td>
	            <td><input id="txtRemark_<? echo $tblRow; ?>" name="txtRemark[]" value="" type="text"  class="text_boxes" style="width:97px"/>
	            	<input type="hidden" id="hdnDtlsUpdateId_<? echo $tblRow; ?>" name="hdnDtlsUpdateId[]"   value="" class="text_boxes_numeric" style="width:40px" />
	            	<input type="hidden" id="hdnWoBrkId_<? echo $tblRow; ?>" name="hdnWoBrkId[]" type="hidden"  value="" class="text_boxes_numeric" style="width:40px" />
					<input type="hidden" id="hdnRcvId_<? echo $tblRow; ?>" name="hdnRcvId[]" value="">
					<input type="hidden" id="hdnWoDtlsId_<? echo $tblRow; ?>" name="hdnWoDtlsId[]" value="<? echo $rows['dtls_id']; ?>">
				</td>
			</tr>
			<?
			$tblRow++;
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

if( $action=='order_dtls_list_view_update' ) 
{
	//echo $data; die;2_FAL-TOR-19-00092_1_3895
	$data=explode('_',$data);
	$buttonStatus=$data[0];
	$mst_id=$data[1];
	$wo_id=$data[2];
	$rcvBasis=$data[3];
	$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name" );
	$size_arr=return_library_array( "select id,size_name from lib_size",'id','size_name');
	$order_receive_arr=return_library_array( "select id,subcon_job from subcon_ord_mst",'id','subcon_job');
	$tblRow=0;
	
	//echo "select a.id, a.mst_id, a.job_no_mst, a.description, a.color_id, a.size_id, a.qnty, a.rate, a.amount, a.order_rcv_break_id, a.order_rcv_id ,b.id as dtls_id ,b.order_quantity as wo_qnty, b.order_uom, b.rate, b.amount, b.section, b.sub_section, b.item_group  as trim_group , b.order_rcv_dtls_id from trims_subcon_ord_breakdown a , trims_subcon_ord_dtls b where a.job_no_mst=b.job_no_mst and b.id=a.mst_id and b.mst_id =$mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by b.id";

	$rcv_qty_result=sql_select( "select a.order_rcv_id , a.wo_id , a.wo_dtls_id, a.wo_break_id, a.rcv_qty from trims_receive_dtls a  where entry_form =451 and mst_id !=$mst_id and a.status_active=1 and a.is_deleted=0 ");		
	foreach ($rcv_qty_result as  $row) 
	{
		//echo $mstIDS.'=='.$ids.'=='.$book_con_dtls_ids.'++'; 
		$rec_qty_arr[$row[csf("order_rcv_id")]][$row[csf("wo_break_id")]]["cum_qty"] +=$row[csf("rcv_qty")];
	}

	$rcv_result=sql_select( "select a.id, a.mst_id, a.order_rcv_id , a.wo_id , a.wo_dtls_id, a.wo_break_id, a.rcv_qty, a.remarks from trims_receive_dtls a  where entry_form =451 and mst_id=$mst_id and a.status_active=1 and a.is_deleted=0 order by a.id");		
	foreach ($rcv_result as  $row) 
	{
		//echo $mstIDS.'=='.$ids.'=='.$book_con_dtls_ids.'++'; 
		
		if($rcvBasis==7){
			$wo_rcv_id=$row[csf("wo_id")];
			$wo_rcv_brk_dtls_id=$row[csf("wo_dtls_id")];
		}else{
			$wo_rcv_id=$row[csf("order_rcv_id")];
			$wo_rcv_brk_dtls_id=$row[csf("wo_break_id")];
		}
		$rcv_arr[$wo_rcv_id][$wo_rcv_brk_dtls_id]["wo_id"] =$row[csf("wo_id")];
		$rcv_arr[$wo_rcv_id][$wo_rcv_brk_dtls_id]["wo_dtls_id"] =$row[csf("wo_dtls_id")];
		$rcv_arr[$wo_rcv_id][$wo_rcv_brk_dtls_id]["rcv_qty"] =$row[csf("rcv_qty")];
		$rcv_arr[$wo_rcv_id][$wo_rcv_brk_dtls_id]["remarks"] =$row[csf("remarks")];
		$rcv_arr[$wo_rcv_id][$wo_rcv_brk_dtls_id]["id"] =$row[csf("id")];

		//$rec_qty_arr[$row[csf("wo_break_id")]]['qty'] +=$row[csf("rcv_qty")];
	}

	
	if($rcvBasis==7){
		$sql="select b.id as dtls_id, b.mst_id as order_rcv_id, b.item_group_id as trim_group, b.item_description as description, b.color_id as color_id, b.size_id as size_id, b.uom as order_uom, b.quantity as qnty, b.rate, b.amount, b.remarks, b.status_active
		from trims_finish_purchase_req_dtls b
		where b.mst_id=$wo_id";
		$qry_result=sql_select(	$sql );
		foreach ($qry_result as  $row) 
		{
			//echo $mstIDS.'=='.$ids.'=='.$book_con_dtls_ids.'++'; 
			$wo_arr[$row[csf("order_rcv_id")]][$row[csf("dtls_id")]]["description"] =$row[csf("description")];
			$wo_arr[$row[csf("order_rcv_id")]][$row[csf("dtls_id")]]["color_id"] =$row[csf("color_id")];
			$wo_arr[$row[csf("order_rcv_id")]][$row[csf("dtls_id")]]["size_id"] =$row[csf("size_id")];
			$wo_arr[$row[csf("order_rcv_id")]][$row[csf("dtls_id")]]["qnty"] =$row[csf("qnty")];
			$wo_arr[$row[csf("order_rcv_id")]][$row[csf("dtls_id")]]["dtls_id"] =$row[csf("dtls_id")];
			$wo_arr[$row[csf("order_rcv_id")]][$row[csf("dtls_id")]]["order_uom"] =$row[csf("order_uom")];
			//$wo_arr[$row[csf("order_rcv_id")]][$row[csf("dtls_id")]]["section"] =$row[csf("section")];
			//$wo_arr[$row[csf("order_rcv_id")]][$row[csf("dtls_id")]]["sub_section"] =$row[csf("sub_section")];
			$wo_arr[$row[csf("order_rcv_id")]][$row[csf("dtls_id")]]["trim_group"] =$row[csf("trim_group")];
		}
	}
	else{
		$qry_result=sql_select( "select a.id, a.job_no_mst, a.description, a.color_id, a.size_id, a.qnty, a.rate, a.amount, a.order_rcv_break_id, a.order_rcv_id ,b.id as dtls_id ,b.order_quantity as wo_qnty, b.order_uom, b.rate, b.amount, b.section, b.sub_section, b.item_group  as trim_group , b.order_rcv_dtls_id, b.order_rcv_id, a.mst_id  from trims_subcon_ord_breakdown a , trims_subcon_ord_dtls b where a.job_no_mst=b.job_no_mst and b.id=a.mst_id and b.mst_id =$wo_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by b.id");

		foreach ($qry_result as  $row) 
		{
			//echo $mstIDS.'=='.$ids.'=='.$book_con_dtls_ids.'++'; 
			$wo_arr[$row[csf("order_rcv_id")]][$row[csf("id")]]["description"] =$row[csf("description")];
			$wo_arr[$row[csf("order_rcv_id")]][$row[csf("id")]]["color_id"] =$row[csf("color_id")];
			$wo_arr[$row[csf("order_rcv_id")]][$row[csf("id")]]["size_id"] =$row[csf("size_id")];
			$wo_arr[$row[csf("order_rcv_id")]][$row[csf("id")]]["qnty"] =$row[csf("qnty")];
			$wo_arr[$row[csf("order_rcv_id")]][$row[csf("id")]]["dtls_id"] =$row[csf("dtls_id")];
			$wo_arr[$row[csf("order_rcv_id")]][$row[csf("id")]]["order_uom"] =$row[csf("order_uom")];
			$wo_arr[$row[csf("order_rcv_id")]][$row[csf("id")]]["section"] =$row[csf("section")];
			$wo_arr[$row[csf("order_rcv_id")]][$row[csf("id")]]["sub_section"] =$row[csf("sub_section")];
			$wo_arr[$row[csf("order_rcv_id")]][$row[csf("id")]]["trim_group"] =$row[csf("trim_group")];
			$wo_arr[$row[csf("order_rcv_id")]][$row[csf("id")]]["mst_id"] = $row[csf("mst_id")];
		}
	}
	//echo "<pre>";
	//print_r($rcv_arr);
	$tblRow=1; $i=1;
	foreach($wo_arr as $rcv_id=> $rcv_id_data)
	{
		if($rcvBasis==7){
			$style_cond='style="display:none;"';
		}
		
	?>
		<thead>
			<tr bgcolor="<? echo $bgcolor; ?>" id="row_<? echo $tblRow; ?>" align="center" <? echo $style_cond; ?> >
				<th width="150" class="must_entry_caption">Order No :
				</th>
				<th colspan="2" align="left"><? echo $order_receive_arr[$rcv_id];?><input type="hidden" name="hdnWoId" id="hdnWoId_<? echo $i; ?>"></th>
				<th colspan="9">&nbsp;</th>
			</tr>
			<tr>
        		<th width="110" class="must_entry_caption">Section</th>
                <th width="110" >Sub Section</th>
                <th width="110" class="must_entry_caption">Trims Group</th>
                <th width="170">Item Description</th>
                <th width="100">Item Color</th>
                <th width="90">Item Size</th>
                <th width="60">Order UOM</th>
                <th width="60" class="must_entry_caption">WO Qty</th>
                <th width="60">Cum. Receive Qty</th>
                <th width="70" class="must_entry_caption">Receive Qnty</th>
                <th width="70">Received Balance</th>
                <th width="110">Remarks</th>
        	</tr>
		</thead>
		<tbody>
		<?
		foreach($rcv_id_data as $woBrkId=> $rows)
		{ 
			//echo $rcv_arr[$rcv_id] [$woBrkId]["rcv_qty"].'=='.$rcv_id.'=='.$woBrkId.'++';
			$rcv_qty=$rcv_arr[$rcv_id] [$woBrkId]["rcv_qty"];
			$remarks=$rcv_arr[$rcv_id] [$woBrkId]["remarks"];
			$dtlsUpdateId=$rcv_arr[$rcv_id] [$woBrkId]["id"];
			$cum_rcv_qty=$rec_qty_arr[$rcv_id] [$woBrkId]["cum_qty"];
			$wo_qty=$rows['qnty'];
			$balance=$wo_qty-($cum_rcv_qty+$rcv_qty);
			if($rows['section']==1) $subID='1,2,3,23,29,30,31,32,33,34,35,36,37,38,39,40';
			else if($rows['section']==3) $subID='4,5,18';
			else if($rows['section']==5) $subID='6,7,8,9,10,11,12,13,16,17,17,24';
			else if($rows['section']==10) $subID='14,15';
			else if($rows['section']==7) $subID='19,20,21,25,26,27,28';
			else if($rows['section']==9) $subID='22';
			else $subID='0';
			if($rcvBasis==7){
				$basis_wise_rcv_id=$woBrkId='';
			}else{
				$basis_wise_rcv_id=$rcv_id;
			}
			?>
			<tr bgcolor="<? echo $bgcolor; ?>" id="row_<? echo $tblRow; ?>" align="center">
				<td><?
					//$dataBreakDtls=$data_dreak_arr[$dtls_id];
					echo create_drop_down( "cboSection_".$tblRow, 110, $trims_section,"", 1, "-- Select Section --",$rows['section'],"load_sub_section($tblRow)",1,'','','','','','',"cboSection[]"); ?></td>
				<td id="subSectionTd_<? echo $tblRow; ?>"><? echo create_drop_down( "cboSubSection_".$tblRow, 110, $trims_sub_section,"", 1, "-- Select Section --",$rows['sub_section'],"load_sub_section_value($tblRow)",1,$subID,'','','','','',"cboSubSection[]"); ?></td>			
				<td><? echo create_drop_down( "cboItemGroup_".$tblRow, 110, "select id, item_name from lib_item_group where item_category=4 and status_active=1","id,item_name", 1, "-- Select --",$rows['trim_group'], "",1,'','','','','','',"cboItemGroup[]"); ?></td>
				<td><input id="txtItemDesc_<? echo $tblRow; ?>" name="txtItemDesc[]" value="<? echo $rows['description']; ?>" type="text"  class="text_boxes" style="width:157px" readonly/></td>
				<td><input id="txtColorName_<? echo $tblRow; ?>" name="txtColorName[]" value="<? echo $color_library[$rows['color_id']]; ?>" type="text"  class="text_boxes" style="width:87px" readonly/>
					<input id="txtColorId_<? echo $tblRow; ?>" name="txtColorId[]" value="<? echo  $rows['color_id']; ?>" type="hidden"  class="text_boxes_numeric" style="width:87px" readonly/></td>
				<td><input id="txtSizeName_<? echo $tblRow; ?>" name="txtSizeName[]" value="<? echo $size_arr[$rows['size_id']]; ?>" type="text"  class="text_boxes" style="width:77px" readonly/>
					<input id="txtSizeId_<? echo $tblRow; ?>" name="txtSizeId[]" value="<? echo $rows['size_id']; ?>" type="hidden"  class="text_boxes_numeric" style="width:77px" readonly/></td>

				<td><? echo create_drop_down( "cboUom_".$tblRow, 60, $unit_of_measurement,"", 1, "-- Select --",$rows['order_uom'],"", 1,'','','','','','',"cboUom[]"); ?>	</td>
				
				<td><input id="txtWoQty_<? echo $tblRow; ?>" name="txtWoQty[]" value="<? echo number_format($rows['qnty'],4,'.',''); ?>" type="text"  class="text_boxes_numeric" style="width:47px" readonly/></td>
				<td><input id="txtCumRcvQty_<? echo $tblRow; ?>" name="txtCumRcvQty[]" value="<? echo number_format($cum_rcv_qty,4,'.',''); ?>" type="text"  class="text_boxes_numeric" style="width:47px" readonly/></td>
				<td><input id="txtRcvQty_<? echo $tblRow; ?>" name="txtRcvQty[]" value="<? echo $rcv_qty; ?> " type="text"  class="text_boxes_numeric" style="width:57px"/></td>
				
				<td><input id="txtRcvBal_<? echo $tblRow; ?>" name="txtRcvBal[]"  value="<? echo number_format($balance,4,'.',''); ?> " type="text" style="width:57px"  class="text_boxes_numeric"  disabled />
	            </td>
	            <td><input id="txtRemark_<? echo $tblRow; ?>" name="txtRemark[]" value="<? echo $remarks; ?>" type="text"  class="text_boxes" style="width:97px"/>
	            	<input type="hidden" id="hdnDtlsUpdateId_<? echo $tblRow; ?>" name="hdnDtlsUpdateId[]"   value="<? echo $dtlsUpdateId; ?>" class="text_boxes_numeric" style="width:40px" />
	            	<input type="hidden" id="hdnWoBrkId_<? echo $tblRow; ?>" name="hdnWoBrkId[]" type="hidden"  value="<? echo  $woBrkId; ?>" class="text_boxes_numeric" style="width:40px" />
					<input type="hidden" id="hdnRcvId_<? echo $tblRow; ?>" name="hdnRcvId[]" value="<? echo $basis_wise_rcv_id; ?>">
					<input type="hidden" id="hdnWoDtlsId_<? echo $tblRow; ?>" name="hdnWoDtlsId[]" value="<? echo $rows['dtls_id']; ?>">
				</td>
			</tr>
			<?
			$tblRow++;
		}
	}
	exit();
}


if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	//echo '<pre>';
	//print_r($cbo_company_name);die;
	$user_id=$_SESSION['logic_erp']['user_id'];
	
	if ($operation==0) // Insert Start Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		if($db_type==0) $insert_date_con="and YEAR(insert_date)=".date('Y',time())."";
		else if($db_type==2) $insert_date_con="and TO_CHAR(insert_date,'YYYY')=".date('Y',time())."";
		
		$new_job_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', 'FTRE', date("Y",time()), 5, "select job_no_prefix,job_no_prefix_num from trims_receive_mst where entry_form=451 and company_id=$cbo_company_name $insert_date_con order by id desc ", "job_no_prefix", "job_no_prefix_num" ));
		//var data="action=save_update_delete&operation="+operation+'&total_row='+i+'&txt_wo_no='+txt_wo_no+'&cbo_company_name='+cbo_company_name+'&txt_system_id='+txt_system_id+'&hid_wo_id='+hid_wo_id+'&txt_order_receive_date='+txt_order_receive_date+'&txt_challan_no='+txt_challan_no+'&txt_challan_date='+txt_challan_date+'&cbo_store_name='+cbo_store_name+'&update_id='+update_id+data_all;
		if($db_type==0){
			$txt_order_receive_date=change_date_format(str_replace("'",'',$txt_order_receive_date),'yyyy-mm-dd');
			$txt_challan_date=change_date_format(str_replace("'",'',$txt_challan_date),'yyyy-mm-dd');
			
		}else{
			$txt_order_receive_date=change_date_format(str_replace("'",'',$txt_order_receive_date), "", "",1);
			$txt_challan_date=change_date_format(str_replace("'",'',$txt_challan_date), "", "",1);
		}
		$id=return_next_id("id","trims_receive_mst",1);
		$id1=return_next_id( "id", "trims_receive_dtls",1);//cbo_receive_basis

		$field_array="id, entry_form, subcon_job, job_no_prefix, job_no_prefix_num, company_id, receive_basis, wo_id, receive_date, challan_date, challan_no, store_id, inserted_by, insert_date";
		$data_array="(".$id.", 451, '".$new_job_no[0]."', '".$new_job_no[1]."', '".$new_job_no[2]."', '".$cbo_company_name."', '".$cbo_receive_basis."', '".$hid_wo_id."', '".$txt_order_receive_date."', '".$txt_challan_date."', '".$txt_challan_no."', '".$cbo_store_name."', ".$_SESSION['logic_erp']['user_id'].", '".$pc_date_time."')";
		
		$txt_job_no=$new_job_no[0];
		$field_array2="id, mst_id, order_rcv_id, wo_id, wo_dtls_id, wo_break_id, rcv_qty, remarks, entry_form, inserted_by, insert_date";
		
		$data_array2 	="";  $add_commaa=0; 

		for($i=1; $i<=$total_row; $i++)
		{			
			$txtRcvQty			= "txtRcvQty_".$i;
			$txtRemark			= "txtRemark_".$i;
			$hdnRcvId			= "hdnRcvId_".$i;
			$hdnWoDtlsId		= "hdnWoDtlsId_".$i;
			$hdnWoBrkId 		= "hdnWoBrkId_".$i;
			//$hdnDtlsUpdateId 	= "hdnDtlsUpdateId_".$i;
			if ($add_commaa!=0) $data_array2 .=","; $add_comma=0;
			if(str_replace("'",'',$$hdnWoBrkId)!='') $$hdnWoBrkId=str_replace("'",'',$$hdnWoBrkId); else $$hdnWoBrkId=$$hdnWoBrkId;
			$data_array2 .="(".$id1.",".$id.",'".str_replace("'",'',$$hdnRcvId)."', '".$hid_wo_id."',".str_replace("'",'',$$hdnWoDtlsId).",".$$hdnWoBrkId.",".str_replace(",",'',$$txtRcvQty).",".$$txtRemark.", 451, ".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			
			
			$id1=$id1+1; $add_commaa++;
			//echo "10**INSERT INTO subcon_ord_breakdown (".$field_array3.") VALUES ".$data_array3; die;			
		}

		//echo "10**INSERT INTO subcon_ord_mst (".$field_array.") VALUES ".$data_array; die;
		//echo "10**INSERT INTO trims_receive_dtls (".$field_array2.") VALUES ".$data_array2; die;
		$flag=1;
		$rID=sql_insert("trims_receive_mst",$field_array,$data_array,1);
		if($rID==1) $flag=1; else $flag=0;
		if($flag==1)
		{
			$rID2=sql_insert("trims_receive_dtls",$field_array2,$data_array2,1);
			if($rID2==1) $flag=1; else $flag=0;
		}
		//echo "10**".$rID.'**'.$rID2; die;
		
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
		
		if($db_type==0){
			$txt_order_receive_date=change_date_format(str_replace("'",'',$txt_order_receive_date),'yyyy-mm-dd');
			$txt_challan_date=change_date_format(str_replace("'",'',$txt_challan_date),'yyyy-mm-dd');
			
		}else{
			$txt_order_receive_date=change_date_format(str_replace("'",'',$txt_order_receive_date), "", "",1);
			$txt_challan_date=change_date_format(str_replace("'",'',$txt_challan_date), "", "",1);
		}
		$update_id=str_replace("'",'',$update_id);
		$id1=return_next_id( "id", "trims_receive_dtls",1);

		$field_array="receive_date*challan_date*challan_no*store_id*updated_by*update_date";
 		$data_array="'".$txt_order_receive_date."'*'".$txt_challan_date."'*'".$txt_challan_no."'*'".$cbo_store_name."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";

 		$field_array2="rcv_qty*remarks*updated_by*update_date";
 		$field_array3="id, mst_id, order_rcv_id, wo_id, wo_dtls_id, wo_break_id, rcv_qty, remarks, entry_form, inserted_by, insert_date";
		
		$data_array2 =""; $flag=true;
		for($i=1; $i<=$total_row; $i++)
		{			
			$txtRcvQty			= "txtRcvQty_".$i;
			$txtRemark			= "txtRemark_".$i;
			$hdnRcvId			= "hdnRcvId_".$i;
			$hdnWoDtlsId		= "hdnWoDtlsId_".$i;
			$hdnWoBrkId 		= "hdnWoBrkId_".$i;
			$hdnDtlsUpdateId 	= "hdnDtlsUpdateId_".$i;
			if(str_replace("'",'',$$hdnDtlsUpdateId)!="" )
			{
				//$data_array2[str_replace("'",'',$$hdnDtlsUpdateId)]=explode("*",("".str_replace(",",'',$$txtRcvQty)."*'".str_replace("'",'',$$txtRemark)."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));

				$data_array2[str_replace("'",'',$$hdnDtlsUpdateId)]=explode("*",("".str_replace(",",'',$$txtRcvQty)."*".$$txtRemark."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
				$hdn_dtls_id_arr[]=str_replace("'",'',$$hdnDtlsUpdateId);
			}
			else
			{
				if(trim(str_replace("'",'',$$txtRcvQty))!="" ){
					//echo "10**".str_replace("'",'',$$txtRcvQty).'=='
					if ($add_commaa!=0) $data_array3 .=","; $add_comma=0;
					if(str_replace("'",'',$$hdnWoBrkId)!='') $$hdnWoBrkId=str_replace("'",'',$$hdnWoBrkId); else $$hdnWoBrkId=$$hdnWoBrkId;
					$data_array3 .="(".$id1.",".$update_id.",'".str_replace("'",'',$$hdnRcvId)."', '".$hid_wo_id."',".str_replace("'",'',$$hdnWoDtlsId).",".$$hdnWoBrkId.",".str_replace(",",'',$$txtRcvQty).",".$$txtRemark.", 451, ".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
					$id1++;
				}
				
			}
		}

		if($flag==1){
			//echo "10**".$field_array."**".$data_array."**".$update_id; die;
			$rID=sql_update("trims_receive_mst",$field_array,$data_array,"id",$update_id,0);  
			if($rID) $flag=1; else $flag=0;
		}
		//echo '10**mmmmmmmmmm'.$deleted_rcv_id; die;
		if($data_array2!=""){
			//echo "10**".bulk_update_sql_statement( "trims_receive_dtls", "id",$field_array2,$data_array2,$hdn_dtls_id_arr); die;
			$rID1=execute_query(bulk_update_sql_statement( "trims_receive_dtls", "id",$field_array2,$data_array2,$hdn_dtls_id_arr),1);
			if($rID1) $flag=1; else $flag=0;
		}
		if($flag==1 && $data_array3!="")
		{
			//echo "10**INSERT INTO trims_receive_dtls (".$field_array3.") VALUES ".$data_array3; die;
			$rID2=sql_insert("trims_receive_dtls",$field_array3,$data_array3,1);
			if($rID2==1) $flag=1; else $flag=0;
		}
		//echo "10**".$rID."**".$rID1."**".$rID2."**".$rID3."**".$rID4."**".$rID5."**".$rID6."**".$flag; die;

		//$rIDBooking=execute_query( "update wo_booking_mst set lock_another_process=1 where booking_no ='".$txt_order_no."'",1);
		//if($rIDBooking==1) $flag=1; else $flag=0;
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'",'',$txt_system_id)."**".str_replace("'",'',$update_id);
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
				echo "1**".str_replace("'",'',$txt_system_id)."**".str_replace("'",'',$update_id);
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
			$rID=sql_update("trims_receive_mst",$field_array,$data_array,"id",$update_id,0);  
			if($rID) $flag=1; else $flag=0;
		}

		if($flag==1){
			//echo "10**".$field_array."**".$data_array."**".$update_id; die;
			$rID=sql_update("trims_receive_dtls",$field_array,$data_array,"mst_id",$update_id,0);  
			if($rID) $flag=1; else $flag=0;
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
				echo "2**".str_replace("'",'',$txt_system_id)."**".str_replace("'",'',$update_id);
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
				echo "2**".str_replace("'",'',$txt_system_id)."**".str_replace("'",'',$update_id);
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

if($action=="trims_finish_rcv_print")
{
	//select id, item_name from lib_item_group where item_category=4 and status_active=1
	extract($_REQUEST);
	$data=explode('*',$data);
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name" );
	$size_arr=return_library_array( "select id,size_name from lib_size",'id','size_name');
	$order_receive_arr=return_library_array( "select id,subcon_job from subcon_ord_mst",'id','subcon_job');
	$trim_group_arr=return_library_array( "select id, item_name from lib_item_group where item_category=4",'id','item_name');
	$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
	$store_name_arr=return_library_array( "select id, store_name from lib_store_location",'id','store_name');
	$source_for_order = array(1 => 'In-House', 2 => 'Sub-Contract');
	$image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$data[0]'","image_location");
	
	$rcv_qty_result=sql_select( "select a.order_rcv_id , a.wo_id , a.wo_dtls_id, a.wo_break_id, a.rcv_qty from trims_receive_dtls a  where entry_form =451 and mst_id !=$data[1] and a.status_active=1 and a.is_deleted=0");		
	foreach ($rcv_qty_result as  $row) 
	{
		//echo $mstIDS.'=='.$ids.'=='.$book_con_dtls_ids.'++'; 
		$rec_qty_arr[$row[csf("order_rcv_id")]][$row[csf("wo_break_id")]]["cum_qty"] +=$row[csf("rcv_qty")];
	}

	$qry_result=sql_select( "select a.id, a.job_no_mst, a.description, a.color_id, a.size_id, a.qnty as wo_qnty, a.rate, a.amount, a.order_rcv_break_id, a.order_rcv_id ,b.id as dtls_id ,b.order_quantity , b.order_uom, b.rate, b.amount, b.section, b.sub_section, b.item_group  as trim_group , b.order_rcv_dtls_id, b.order_rcv_id, a.mst_id, c.rcv_qty, c.remarks  from trims_subcon_ord_breakdown a , trims_subcon_ord_dtls b, trims_receive_dtls c where a.job_no_mst=b.job_no_mst and b.id=a.mst_id and a.id=c.wo_break_id and b.mst_id=c.wo_id and c.mst_id =$data[1] and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 order by c.id");
	//echo "select a.id, a.job_no_mst, a.description, a.color_id, a.size_id, a.qnty, a.rate, a.amount, a.order_rcv_break_id, a.order_rcv_id ,b.id as dtls_id ,b.order_quantity as wo_qnty, b.order_uom, b.rate, b.amount, b.section, b.sub_section, b.item_group  as trim_group , b.order_rcv_dtls_id, b.order_rcv_id, a.mst_id, c.rcv_qty, c.remarks  from trims_subcon_ord_breakdown a , trims_subcon_ord_dtls b, trims_receive_dtls c where a.job_no_mst=b.job_no_mst and b.id=a.mst_id and a.id=c.wo_break_id and b.mst_id=c.wo_id and c.mst_id =$data[1] and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 order by c.id";
	foreach ($qry_result as  $row) 
	{
		//echo $mstIDS.'=='.$ids.'=='.$book_con_dtls_ids.'++'; 
		$wo_arr[$row[csf("order_rcv_id")]][$row[csf("section")]][$row[csf("sub_section")]][$row[csf("trim_group")]][$row[csf("description")]][$row[csf("color_id")]][$row[csf("size_id")]][$row[csf("order_uom")]]["wo_qnty"] +=$row[csf("wo_qnty")];
		$wo_arr[$row[csf("order_rcv_id")]][$row[csf("section")]][$row[csf("sub_section")]][$row[csf("trim_group")]][$row[csf("description")]][$row[csf("color_id")]][$row[csf("size_id")]][$row[csf("order_uom")]]["rcv_qty"] +=$row[csf("rcv_qty")];
		$wo_arr[$row[csf("order_rcv_id")]][$row[csf("section")]][$row[csf("sub_section")]][$row[csf("trim_group")]][$row[csf("description")]][$row[csf("color_id")]][$row[csf("size_id")]][$row[csf("order_uom")]]["remarks"] .=$row[csf("remarks")].',';
		$wo_arr[$row[csf("order_rcv_id")]][$row[csf("section")]][$row[csf("sub_section")]][$row[csf("trim_group")]][$row[csf("description")]][$row[csf("color_id")]][$row[csf("size_id")]][$row[csf("order_uom")]]["wo_brk_id"] .=$row[csf("id")].',';
	}

	$sql_mst="select a.id as wo_id ,a.subcon_job as wo_no ,a.job_no_prefix ,a.job_no_prefix_num ,a.company_id ,a.location_id ,a.order_rcv_id, a.order_rcv_no ,a.supplier_id ,a.currency_id ,a.paymode_id ,a.source_id ,a.exchange_rate ,a.wo_date ,a.delivery_date ,a.attention , b.id as rcv_id, b.subcon_job , b.receive_date, b.challan_date, b.challan_no, b.store_id, a.inserted_by from trims_subcon_ord_mst a , trims_receive_mst b where a.id=b.wo_id and b.entry_form=451 and a.entry_form=450 and a.company_id=$data[0] and b.id='$data[1]'  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	//select id, entry_form, subcon_job, job_no_prefix, job_no_prefix_num, company_id, wo_id, receive_date, challan_date, challan_no, store_id from trims_receive_mst where entry_form=451 and company_id=$data[0] and 
	$dataArray=sql_select($sql_mst);
	$inserted_by=$dataArray[0][csf("inserted_by")];

	//echo "<pre>";
	//print_r($wo_arr);
	//die;

	$section_rowspan_arr=array(); $sub_section_rowspan_arr=array(); $trim_group_rowspan_arr=array(); $description_rowspan_arr=array(); $color_rowspan_arr=array(); $size_rowspan_arr=array(); $order_uom_rowspan_arr=array();
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
					foreach($trim_group_id_data as $description=> $description_data)
					{
						$description_rowspan=0;	
						foreach($description_data as $color_id=> $color_id_data)
						{
							$color_id_rowspan=0;
							foreach($color_id_data as $size_id=> $size_id_data)
							{
								$size_id_rowspan=0;	
								foreach($size_id_data as $order_uom=> $row)
								{
									$section_rowspan++;
									$sub_section_rowspan++;
									$trim_group_rowspan++;
									$description_rowspan++;
									$color_id_rowspan++;
									$size_id_rowspan++;
								}
								$size_rowspan_arr[$rcv_id][$section_id][$sub_section_id][$trim_group_id][$description][$color_id][$size_id]=$size_id_rowspan;
							}
							$color_rowspan_arr[$rcv_id][$section_id][$sub_section_id][$trim_group_id][$description][$color_id]=$color_id_rowspan;
						}
						$description_rowspan_arr[$rcv_id][$section_id][$sub_section_id][$trim_group_id][$description]=$description_rowspan;
					}
					$trim_group_rowspan_arr[$rcv_id][$section_id][$sub_section_id][$trim_group_id]=$trim_group_rowspan;
					$section_rowspan++; $sub_section_rowspan++;
				}
				$sub_section_rowspan_arr[$rcv_id][$section_id][$sub_section_id]=$sub_section_rowspan;
			}
			$section_rowspan_arr[$rcv_id][$section_id]=$section_rowspan;
		}
	}
	//echo "<pre>";
	//print_r($section_rowspan_arr);
	?>
	<div style="width:1060px;">
		<table width="1060" cellspacing="0" align="center" border="0">
			<tr>
				<td colspan="6"></td>
			</tr>
			<tr>
				<td colspan="2" rowspan="2">
					<img src="../../<? echo $image_location; ?>" height="60" width="200" style="float:left;">
				</td>
				<td colspan="4" align="center" style="font-size:x-large">
					<strong><? echo $company_library[$data[0]]; ?></strong>
				</td>
			</tr>
			<tr>
				<td colspan="4" align="center">
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
			</tr>
			<tr>
				<td colspan="2" ></td>
				<td colspan="4" align="center" style="font-size:large">
					<strong>Finish Trims Receive Entry</strong>
				</td>
			</tr>
			<tr>
				<td width="170">Company Name : </td> <td width="175"><? echo $company_library[$data[0]]; ?></td>
				<td width="130"></td> <td width="175"></td>
				<td width="130"></td> <td width="175"></td>
				<td width="150">Recive No:</td><td width="300px"> <strong> <? echo $dataArray[0][csf('subcon_job')]  ?></strong></td>
			</tr>
			<tr>
				<td>Source :</td><td><? echo $source_for_order[$dataArray[0][csf('source_id')]]; ?></td>
				<td colspan="4">&nbsp;</td> 
				<td>Work Order No:</td><td colspan="3"><? echo $dataArray[0][csf('wo_no')]; ?></td>
			</tr>
			<tr>
				<td width="170">Challan No :</td> <td width="175"><? echo $dataArray[0][csf('challan_no')]; ?></td>
				<td width="130"></td> <td width="175"></td>
				<td width="130"></td> <td width="175"></td>
				<td width="150">Challan Date :</td><td width="300px"><? echo change_date_format($dataArray[0][csf('challan_date')]);  ?></td>
			</tr>
			<tr>
				<td>Currency :	</td><td><? echo $currency[$dataArray[0][csf('currency_id')]]; ?></td>
				<td width="130"></td> <td width="175"></td>
				<td width="130"></td> <td width="175"></td>
				<td>Supplier :</td><td colspan="3"><? echo $supplier_arr[$dataArray[0][csf('supplier_id')]]; ?></td>
			</tr>
			<tr>
				<td>Store :	</td><td><? echo $store_name_arr[$dataArray[0][csf('store_id')]]; ?></td>
				<td colspan="6">&nbsp;</td> 
			</tr>
		</table>
		<br>
		<div style="width:100%;">
			<table align="left" cellspacing="0" width="1060"  border="1" rules="all" class="rpt_table"  >
				<?
				$tblRow=1; $i=1;
				foreach($wo_arr as $rcv_id=> $rcv_id_data)
				{
					?>
					<thead>
						<tr bgcolor="<? echo $bgcolor; ?>" id="row_<? echo $tblRow; ?>" align="center">
							<th colspan="12"  style="text-align:left ">Order No : <? echo $order_receive_arr[$rcv_id];?></th>
						</tr>
						<tr>
			        		<th width="110">Section</th>
			                <th width="110" >Sub Section</th>
			                <th width="110" class="must_entry_caption">Trims Group</th>
			                <th width="170">Item Description</th>
			                <th width="130">Item Color</th>
			                <th width="90">Item Size</th>
			                <th width="60">Order UOM</th>
			                <th width="60" class="must_entry_caption">WO Qty</th>
			                <th width="60">Cum. Receive Qty</th>
			                <th width="70" class="must_entry_caption">Receive Qnty</th>
			                <th width="70">Received Balance</th>
			                <th width="110">Remarks</th>
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
								$trim_group_rowspan=1; $uom_wise_qnty=0;	
								foreach($trim_group_id_data as $description=> $description_data)
								{
									$description_rowspan=0;	
									foreach($description_data as $color_id=> $color_id_data)
									{
										$color_id_rowspan=0;
										foreach($color_id_data as $size_id=> $size_id_data)
										{
											$size_id_rowspan=0;	
											foreach($size_id_data as $order_uom=> $row)
											{
												$wo_brk_id=array_unique(explode(",",chop($row['wo_brk_id'],',')));
												$cum_rcv_qty="";	
												foreach ($wo_brk_id as $woBrkId)
												{
													$cum_rcv_qty+=$rec_qty_arr[$rcv_id][$woBrkId]["cum_qty"];
													//if($color_name=="") $color_name=$color_arr[$color_id]; else $color_name.=','.$color_arr[$color_id];
												}
												$rcv_qty=$row['rcv_qty'];
												$uom_wise_qnty +=$rcv_qty;
												//$cum_rcv_qty=$rec_qty_arr[$rcv_id][$woBrkId]["cum_qty"];
												$wo_qnty=$row['wo_qnty'];
												$balance=$wo_qnty-($cum_rcv_qty+$rcv_qty);
												$remarks=chop($row['remarks'],',');
												$uom_wise_rcv_qty +=$rcv_qty;
												//Section 	Sub Section 	Trims Group 	Item Description 	Item Color 	Item Size 	Order UOM 	WO Qty 	Cum. Receive Qty 	Receive Qnty 	Received Balance 	Remarks
												?>
												<tr bgcolor="<? echo $bgcolor; ?>" id="row_<? echo $tblRow; ?>" align="center">
													<? if($section_rowspan==0){ ?> <td rowspan="<? echo $section_rowspan_arr[$rcv_id][$section_id]; ?>"><p><?  echo $trims_section[$section_id] ; ?></p></td><? } ?>
													<? if($sub_section_rowspan==0){ ?> <td rowspan="<? echo $sub_section_rowspan_arr[$rcv_id][$section_id][$sub_section_id]; ?>"><p><?  echo $trims_sub_section[$sub_section_id] ; ?></p></td><? } ?>
													<? if($trim_group_rowspan==1){ ?> <td rowspan="<? echo $trim_group_rowspan_arr[$rcv_id][$section_id][$sub_section_id][$trim_group_id]; ?>"><p><?  echo $trim_group_arr[$trim_group_id] ; ?></p></td><? } ?>
													<? if($description_rowspan==0){ ?> <td rowspan="<? echo $description_rowspan_arr[$rcv_id][$section_id][$sub_section_id][$trim_group_id][$description]; ?>"><p><?  echo $description ; ?></p></td><? } ?>
													<? if($color_id_rowspan==0){ ?> <td rowspan="<? echo $color_rowspan_arr[$rcv_id][$section_id][$sub_section_id][$trim_group_id][$description][$color_id]; ?>"><p><?  echo $color_library[$color_id] ; ?></p></td><? } ?>
													<? if($size_id_rowspan==0){ ?> <td rowspan="<? echo $size_rowspan_arr[$rcv_id][$section_id][$sub_section_id][$trim_group_id][$description][$color_id][$size_id]; ?>"><p><?  echo $size_arr[$size_id] ; ?></p></td><? } ?>
													<td><p><?  echo $unit_of_measurement[$order_uom] ; ?></p></td>
													<td align="right"><p><?  echo $wo_qnty ; ?></p></td>
													<td align="right"><p><?  echo $cum_rcv_qty ; ?></p></td>
													<td align="right"><p><?  echo $rcv_qty ; ?></p></td>
													<td align="right"><p><?  echo $balance ; ?></p></td>
													<td ><p><?  echo $remarks ; ?></p></td>
												</tr>
												<?
												$tblRow++; $section_rowspan++; $sub_section_rowspan++; $trim_group_rowspan++; $description_rowspan++; $color_id_rowspan++; $size_id_rowspan++;
											}
										}
									}
								}
								?> <tr>
									<td colspan="6" align="right"><strong>UOM Total:</strong></td>
									<td align="right"><p><?  echo $uom_wise_qnty ; ?></p></td>
									<td colspan="2">&nbsp;</td>
									</tr><? 
								$section_rowspan++; $sub_section_rowspan++;
							}
						}
					}
				}?>
				</table>
			</div>
		<br>
	</div>
	<?
    	echo signature_table(211, $data[0], "1100px","","",$inserted_by);
    ?>
</div>
<?
}
if ($action=="job_popup")
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
                    <th width="100" id="search_by_td">WO / Requisition</th>
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
                    <td align="center">
                        <input type="text" name="txt_search_req" id="txt_search_req" class="text_boxes" style="width:100px" placeholder="" />
                    </td>
                    <td align="center"><? echo create_drop_down( "cbo_year_selection", 100, $year,"", 1, "-- Select --", date('Y'), "",0 ); ?></td>
                    <td align="center">
                        <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px">
                        <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px">
                    </td>
                    <td align="center">
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_party_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_type').value+'_'+document.getElementById('txt_search_string').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('cbo_within_group').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('txt_search_req').value, 'create_job_search_list_view', 'search_div', 'finish_trims_receive_entry_controller', 'setFilterGrid(\'tbl_po_list\',-1)')" style="width:70px;" /></td>
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
	
if($action=="create_job_search_list_view")
{	
	$data=explode('_',$data);
	$party_id=str_replace("'","",$data[1]);
	$search_by=str_replace("'","",$data[4]);
	$search_str=trim(str_replace("'","",$data[5]));
	$search_type =$data[6];
	$within_group =$data[7];
	$search_req =$data[9];
	
	//echo $search_req; die;
	if($db_type==0) { $year_cond=" and YEAR(a.insert_date)=$data[8]";   }
	if($db_type==2) {$year_cond=" and to_char(a.insert_date,'YYYY')=$data[8]";}

	if($data[0]!=0) $company=" and a.company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
	if($search_str!="") $search_com_cond="and a.job_no_prefix_num='$search_str'";
	
	if($search_req!="") $search_com_cond="and b.job_no_prefix_num='$search_req'";

	if($db_type==0)
	{ 
		if ($data[2]!="" &&  $data[3]!="") $rcv_date = "and a.wo_date between '".change_date_format($data[2],'yyyy-mm-dd')."' and '".change_date_format($data[3],'yyyy-mm-dd')."'"; else $rcv_date ="";
		$ins_year_cond=" ,year(a.insert_date)";
	}
	else
	{
		if ($data[2]!="" &&  $data[3]!="") $rcv_date = "and a.wo_date between '".change_date_format($data[2], "", "",1)."' and '".change_date_format($data[3], "", "",1)."'"; else $rcv_date ="";
		$ins_year_cond=" ,TO_CHAR(a.insert_date,'YYYY')";
	}
	
	//$sql= "select a.id, a.subcon_job, a.job_no_prefix_num, $ins_year_cond as year, a.company_id, a.location_id, a.wo_date, a.delivery_date from trims_subcon_ord_mst a where a.entry_form=450 and a.status_active=1 and a.status_active=1 $wo_date $company $search_com_cond order by a.id DESC";
	
	
	$sql= "select  a.id, b.subcon_job as req_subcon_job,a.subcon_job, a.company_id, a.wo_id, a.receive_date, a.challan_date, a.challan_no, a.store_id, a.receive_basis $ins_year_cond as year from trims_receive_mst a,trims_subcon_ord_mst b where a.entry_form=451 and a.wo_id=b.id and  b.entry_form=450 and a.status_active=1 and a.status_active=1 $rcv_date $company $search_com_cond order by a.id DESC";
	//echo $sql;
	$data_array=sql_select($sql);
	//echo "<pre>";
	//print_r($data_array);
	?>
    <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="605" >
        <thead>
            <th width="30">SL</th>
            <th width="100">System ID</th> 
            <th width="100">WO / Requisition</th>
            <th width="80">Receive Date</th>
            <th width="100">Challan No</th>
            <th width="80">Challan Date</th>
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
                if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                ?>
                <tr bgcolor="<? echo $bgcolor; ?>" onClick='js_set_value("<? echo $row[csf('id')].'_'.$row[csf('subcon_job')].'_'.$row[csf('wo_id')].'_'.$row[csf('receive_basis')]; ?>")' style="cursor:pointer" >
                    <td width="30"><? echo $i; ?></td>
                    <td width="100"><? echo $row[csf('subcon_job')]; ?></td>
                    <td width="100"><? echo $row[csf('req_subcon_job')]; ?></td>
                    <td width="80" style="text-align:center;"><? echo change_date_format($row[csf('receive_date')]); ?></td>
                    <td width="100"><? echo $row[csf('challan_no')]; ?></td>
                    <td width="80" style="text-align:center;"><? echo change_date_format($row[csf('challan_date')]); ?></td>
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
 
if ($action=="load_php_rcv_data_to_form")
{
	$data=explode('_',$data);
	$mst_id=$data[0];
	$rcvBasis=$data[2];
	//$nameArray=sql_select( "select id, requ_no, company_id, location_id, requisition_date, pay_mode, source, manual_req, currency_id, delivery_date, req_by, remarks, template_id, status_active from trims_finish_purchase_req_mst where id='$data' and is_deleted=0");

	if($rcvBasis==7){
		$nameArray=sql_select( "select a.id as wo_id ,a.requ_no as wo_no ,a.company_id ,a.location_id, b.receive_basis ,a.currency_id ,a.delivery_date , b.id as rcv_id, b.subcon_job , b.receive_date, b.challan_date, b.challan_no, b.store_id from trims_finish_purchase_req_mst a , trims_receive_mst b where a.id=b.wo_id and b.entry_form=451 and b.id='$mst_id'  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0" );
	}else{
		$nameArray=sql_select( "select a.id as wo_id ,a.subcon_job as wo_no ,a.job_no_prefix ,a.job_no_prefix_num ,a.company_id ,a.location_id, b.receive_basis ,a.order_rcv_id, a.order_rcv_no ,a.supplier_id ,a.currency_id ,a.paymode_id ,a.source_id ,a.exchange_rate ,a.wo_date ,a.delivery_date ,a.attention , b.id as rcv_id, b.subcon_job , b.receive_date, b.challan_date, b.challan_no, b.store_id from trims_subcon_ord_mst a , trims_receive_mst b where a.id=b.wo_id and b.entry_form=451 and a.entry_form=450 and b.id='$mst_id'  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0" );
	}
	
	foreach ($nameArray as $row)
	{	
		echo "document.getElementById('txt_wo_no').value 				= '".$row[csf("wo_no")]."';\n";
		echo "document.getElementById('hid_wo_id').value          		= '".$row[csf("wo_id")]."';\n";
		echo "document.getElementById('txt_system_id').value          	= '".$row[csf("subcon_job")]."';\n";

		//echo "document.getElementById('cbo_company_name').value 		= '".$row[csf("company_id")]."';\n";
		echo "$('#cbo_company_name').attr('disabled','true')".";\n";
		echo "load_drop_down( 'requires/finish_trims_receive_entry_controller', document.getElementById('cbo_company_name').value, 'load_drop_down_location', 'location_td' );\n";	
		echo "load_drop_down( 'requires/finish_trims_receive_entry_controller', document.getElementById('cbo_company_name').value, 'load_supplier_dropdown', 'supplier_td' );\n";	
		echo "document.getElementById('cbo_supplier_id').value			= '".$row[csf("supplier_id")]."';\n";

		echo "document.getElementById('cbo_location_name').value 		= '".$row[csf("location_id")]."';\n";
		echo "document.getElementById('cbo_currency').value				= '".$row[csf("currency_id")]."';\n";
		echo "document.getElementById('cbo_source_id').value			= '".$row[csf("source_id")]."';\n";
		echo "document.getElementById('txt_exchange_rate').value        = '".$row[csf("exchange_rate")]."';\n";
		echo "document.getElementById('txt_order_receive_date').value   = '".change_date_format($row[csf("receive_date")])."';\n"; 
		echo "document.getElementById('txt_challan_date').value         = '".change_date_format($row[csf("challan_date")])."';\n"; 
		echo "document.getElementById('txt_challan_no').value        	= '".$row[csf("challan_no")]."';\n";
		echo "document.getElementById('cbo_store_name').value        	= '".$row[csf("store_id")]."';\n";
		echo "document.getElementById('update_id').value        		= '".$row[csf("rcv_id")]."';\n";
		echo "document.getElementById('cbo_receive_basis').value        = '".$row[csf("receive_basis")]."';\n";

		
		//echo "document.getElementById('cbo_paymode_id').value			= '".$row[csf("paymode_id")]."';\n";
		//echo "document.getElementById('txt_attention').value			= '".$row[csf("attention")]."';\n";
		//echo "document.getElementById('txt_wo_date').value				= '".change_date_format($row[csf("wo_date")])."';\n"; 
		//echo "document.getElementById('txt_delivery_date').value		= '".change_date_format($row[csf("delivery_date")])."';\n"; 
		
		//echo "$('#cbo_currency').attr('disabled','true')".";\n";
		echo "set_button_status(1,'".$_SESSION['page_permission']."', 'fnc_job_order_entry',1);\n";	
	}
	exit();	
}

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
		load_drop_down( 'finish_trims_receive_entry_controller', company+'_'+1+'_'+party_name, 'load_drop_down_buyer', 'buyer_td' );
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
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_party_name').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('cbo_search_category').value+'_'+<? echo $company;?>+'_'+document.getElementById('cbo_search_type').value, 'create_booking_search_list_view', 'search_div', 'finish_trims_receive_entry_controller','setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
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
				var response_data = return_global_ajax_value(prod_ids + "**" + row_num + "**" + id , 'populate_prod_data', '', 'finish_trims_receive_entry_controller');
				$("#tbl_share_details_entry tbody:last").append(response_data);
	    	}
	    	else
	    	{
	    		var row_num =  $('#tbl_share_details_entry tbody tr').length; //$('#txt_tot_row').val();
		        var response_data = return_global_ajax_value(prod_ids + "**" + row_num , 'populate_prod_data', '', 'finish_trims_receive_entry_controller');
		        $('#tbl_share_details_entry tbody').prepend(response_data);
		        var tot_row = $('#tbl_share_details_entry tbody tr').length;
	    	}
	       // freeze_window(5); //release_freezing();
	    }

		function openmypage_material(data)
		{
			//alert(data);
			page_link='finish_trims_receive_entry_controller.php?action=material_description_popup&data='+data;
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
	                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_item_group').value+'_'+document.getElementById('txt_search_string').value+'_'+document.getElementById('txt_product_id').value+'_'+document.getElementById('txt_brand_id').value+'_'+document.getElementById('cbo_section').value, 'create_description_search_list_view', 'search_div', 'finish_trims_receive_entry_controller', 'setFilterGrid(\'tbl_po_list\',-1)')" style="width:70px;" />
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
		//echo "load_drop_down( 'requires/finish_trims_receive_entry_controller', document.getElementById('cbo_company_name').value, 'load_drop_down_location', 'location_td' );\n";	
			echo "document.getElementById('cbo_location_name').value 		= '".$row[csf("location_id")]."';\n";
		//echo "load_drop_down( 'requires/finish_trims_receive_entry_controller', document.getElementById('cbo_company_name').value, 'load_drop_down_buyer', 'buyer_td' );\n";
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

