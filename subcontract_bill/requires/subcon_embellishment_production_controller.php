<?php
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../includes/common.php');

$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

//$process_knitting="2";
//$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
//$brand_arr=return_library_array( "select id, brand_name from lib_brand",'id','brand_name');



if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_name", 150, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "--Select Location--", $selected, "","","","","","",3 );
	exit();	 
}

if ($action=="load_drop_down_party_name")
{
	echo create_drop_down( "cbo_party_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id  and b.tag_company='$data' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Party --", $selected, "","1","","","","",4 );
	exit();	
}

if ($action=="load_drop_down_party_name_pop")
{
	echo create_drop_down( "cbo_party_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id  and b.tag_company='$data' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Party --", $selected, "","0","","","","",4 );
	exit();	
}

if($action=="load_drop_down_company_supplier")
{
	$data = explode("**",$data);
	if($data[0]==3)
	{
		//echo create_drop_down( "cbo_company_supplier", 140, "select id, supplier_name from lib_supplier where find_in_set(2,party_type) and find_in_set($data[1],tag_company) and status_active=1 and is_deleted=0","id,supplier_name", 1, "--Select Supplier--", 1, "" );
		
		echo create_drop_down( "cbo_company_supplier", 140, "select a.id, a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=2 and  a.status_active=1 and a.is_deleted=0 order by a.supplier_name","id,supplier_name", 1, "--Select Supplier--", $selected, "" );
	}
	else if($data[0]==1)
	{
		if($data[1]!="")
		{
			 echo create_drop_down( "cbo_company_supplier", 140,"select id,company_name from lib_company where is_deleted=0 and status_active=1 order by company_name","id,company_name", 1, "--Select Supplier--", $data[1], "","" );	
		}
		else
		{
			 echo create_drop_down( "cbo_company_supplier", 140, $blank_array,"", 1, "--Select Company--", $selected, "",0 );
		}
	}
	else
	{
		echo create_drop_down( "cbo_company_supplier", 140, $blank_array,"", 1, "--Select Supplier--", $selected, "",0 );
	}
	exit();	
}


if($action=="load_drop_down_company_supplier_location")
{
	$data = explode("**",$data);
	if($data[0]==3)
	{
		echo create_drop_down( "cbo_location_name_s", 140, $blank_array,"", 1, "--Select Company--", $selected, "",1 );
	}
	else if($data[0]==1)
	{
		if($data[1]!="")
		{
			echo create_drop_down( "cbo_location_name_s", 140, "select id,location_name from lib_location where company_id='$data[1]' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "--Select Location--", $data[2], "","" );
			
		}
		else
		{
			 echo create_drop_down( "cbo_location_name_s", 140, $blank_array,"", 1, "--Select Company--", $selected, "",0 );
		}
	}
	else
	{
		echo create_drop_down( "cbo_location_name_s", 140, $blank_array,"", 1, "--Select Supplier--", $selected, "",0 );
	}
	exit();	
}


/*/Search Saved data/*/
if($action=="search_embel_production")
{
	echo load_html_head_contents("Popup Info","../../", 1, 1, $unicode,'','');
	extract($_REQUEST);
	
	
	//$hiddenBatchId = implode(",", array_unique(explode(",",$hiddenBatchId)));
	//echo "reazz".$hiddenBatchId;
	
	//cbo_company_id='+cbo_company_id+'&hiddenBatchId='+hiddenBatchId+'&hiddenBatchAgainst='+hiddenBatchAgainst
	
	//echo create_drop_down( "cbo_location_name", 150, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "--Select Location--", $selected, "","","","","","",3 );
	///echo load_drop_down( "subcon_embellishment_production_controller", $cbo_company_id, "load_drop_down_location", "location_td");
	?>
	  <script>
		function js_set_value( str) 
		{
			$('#hidden_embl_against_id').val( str );
			//alert(str);return;
			parent.emailwindow.hide();
		}
		
				

	  </script>
    </head>
    <body>
		<div align="center" style="width:100%;" >
             <fieldset>
                <form name="searchjobfrm_1"  id="searchjobfrm_1" autocomplete="off">
                    <table width="" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
                        <thead>                	 
                            <th width="150">Company Name</th>
                            <th width="150">Location</th>
							<th width="80">System  No</th>
                            <th width="80">Batch No</th>
                            <th width="230">Date Range</th>
                            <th width="90">
                            <input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:70px;" />
                            </th>           
                        </thead>
                        <tbody>
                            <tr>
                                <td> 
                                    <?   echo create_drop_down( "cbo_company_id", 150, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name",1, "-- Select Company --",$cbo_company_id,"load_drop_down( 'subcon_embellishment_production_controller', this.value, 'load_drop_down_location', 'location_td');",0 );
                                    ?>
                                </td>
                                <td id="location_td">
                                    <? 
                                        echo create_drop_down( "cbo_location_name", 150, $blank_array,"", 1, "--Select Location--", $selected,"","","","","","",3);
                                    ?>
                                </td>
                                <td >
                                    <input name="txt_system_number" id="txt_system_number" class="text_boxes" style="width:80px">
                                </td>
								 <td >
                                    <input name="txt_batch_number" id="txt_batch_number" class="text_boxes" style="width:80px">
                                </td>
                                
                                <td align="center">
                                    <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px">To<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px">
                                </td> 
                                <td align="center">
                                    <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_id').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_location_name').value+'_'+document.getElementById('txt_system_number').value+'_'+document.getElementById('txt_batch_number').value, 'system_number_search_list_view', 'search_div', 'subcon_embellishment_production_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:70px;" />
                                    
                                     <input type="hidden" id="hidden_embl_against_id" name="hidden_embl_against_id" value="" />
                                </td>
                            </tr>
                            <tr>
                                <td colspan="5" align="center" height="40" valign="middle">
                                    <? echo load_month_buttons(1);  ?>
                                </td>
                            </tr>
                        </tbody>
                    </table> 
                </form>
             </fieldset>   
             <fieldset style="width:500px;">
                <legend>Embellishment Data</legend>
                <div id="search_div" ></div>
              </fieldset>
		</div>
    </body>           
    <script src="../../includes/functions_bottom.js" type="text/javascript"></script>
    <script>
    	//$("#cbo_company_id").val(<? //echo $cbo_company_id; ?>);
		load_drop_down( 'subcon_embellishment_production_controller', <? echo $cbo_company_id ?>, 'load_drop_down_location', 'location_td');
    </script>
    </html>
    <?
	exit();
}

if($action=="system_number_search_list_view")
{
	 $data=explode('_',$data);
	
	if($db_type==0)
	{
		$date_from= change_date_format($data[1],'yyyy-mm-dd');
		$date_to= change_date_format($data[2],'yyyy-mm-dd');
	}
	else
	{
		$date_from= change_date_format($data[1], "", "",1) ;
		$date_to= change_date_format($data[2], "", "",1);
	}
	
	if($data[0] ==0){
		echo "Select Company first"; die;
	}else{
		$company_cond= " and a.company_id = $data[0]";
	}
	
	
	if($data[3] !="0"){
		$location_cond= " and a.location_id = $data[3]";
	}
	if($data[4] !=""){
		$system_no_cond=" and a.prefix_no_num= $data[4]";
	}
	if($data[5] !=""){
		$system_no_cond.=" and b.batch_no like '%".trim($data[5])."%'";
	}
	//echo $system_no_cond.'SSSA';
	$date_cond="";
	if($data[1]!="" && $data[2]!=""){
		$date_cond=" and a.product_date between '".$date_from."' and '".$date_to."'";
	}
	else if($data[1]=="" && $data[2]!=""){
		$date_cond=" and a.product_date <= '".$date_to."'";
	}
	else if($data[1]!="" && $data[2]==""){
		$date_cond=" and a.product_date >= '".$date_from."'";
	}
	
	$sql="select a.id, a.sys_no, a.company_id, a.location_id,a.product_date, a.prod_source, a.serv_company, a.serv_location, a.batch_against from subcon_embel_production_mst a,subcon_embel_production_dtls b where a.id=b.mst_id and a.status_active = 1 and a.is_deleted = 0  and b.status_active = 1 and b.is_deleted = 0 $company_cond $location_cond $system_no_cond $date_cond group by  a.id, a.sys_no, a.company_id, a.location_id,a.product_date, a.prod_source, a.serv_company, a.serv_location, a.batch_against order by a.id";
	 
	 $arr= array(2=>$knitting_source,3=>$batch_against);
	 
     echo create_list_view ( "list_view", "System No,Product Date,Prod Source,Batch Against", "150,100,100,120","500","300",1, $sql, "js_set_value", "id,batch_against","", 1, "0,0,prod_source,batch_against", $arr, "sys_no,product_date,prod_source,batch_against","0,0,0,0", 'setFilterGrid("list_view",-1);','0,3,0,0') ;

	exit();	 
}

/*/Data borwse from table "pro_batch_create_mst & pro_batch_create_dtls"/*/
if ($action=="batch_search_popup")
{
	echo load_html_head_contents("Popup Info","../../", 1, 1, $unicode,'','');
	extract($_REQUEST);
	if( $cbo_company_id > 0 ) $disable=1;  else  $disable=0;
	
	$prevBatchIds = implode(",", array_unique(explode(",",$prevBatchIds)));
	?>
	  <script>
		var selected_id = new Array();
		//var selectedAgainst = new Array();
		
		var prev_batch_against='<? echo $hiddenBatchAgainst; ?>';
		var batch_id='<? echo $prevBatchIds; ?>';
		
		function toggle( x, origColor ) 
		{
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		
		function js_set_value( str) 
		{
			var cur_batch_against=$('#txtIndividualbatchAgainst' + str).val();
			if((prev_batch_against=="" || selected_id.length==0) && batch_id=="")
			{
				prev_batch_against=cur_batch_against;
			}
			else
			{
				if(prev_batch_against != cur_batch_against)
				{
					alert("Batch Number Mix not Allowed !");
					return;
				}
			}
			
			
			
			toggle( document.getElementById( 'search' + str ), '#FFFFCC' );
			//alert(str); return;
			if( jQuery.inArray( $('#txtIndividualBatchId' + str).val(), selected_id ) == -1 ) 
			{
				selected_id.push( $('#txtIndividualBatchId' + str).val() );
			}
			else 
			{
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == $('#txtIndividualBatchId' + str).val() ) break;
				}
				selected_id.splice( i, 1 );
			}
			
			var id = '';
			//var AgainstId = '';
			
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				//AgainstId += selectedAgainst[i] + ',';
			}
			
			id = id.substr( 0, id.length - 1 );
			//AgainstId = AgainstId.substr( 0, AgainstId.length - 1 );
			
			$('#hidden_batch_ids').val( id );
			$('#hidden_Batch_Against').val( prev_batch_against );
			
		}
		
		function reset_hide_field()
		{
			$('#hidden_batch_ids').val( '' );
			selected_id = new Array();
		}
	  </script>
    </head>
    <body>
        <div align="center" style="width:100%;" >
        <form name="searchjobfrm_1"  id="searchjobfrm_1" autocomplete="off">
        <table width="850" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
            <thead>                	 
                <th width="150">Company Name</th>
                <th width="150">Batch No</th>
                <th width="200">Date Range</th>
                <th width="100">
                	<input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:100px;" />
                </th>           
            </thead>
            <tbody>
                <tr>
                    <td> 
						<?   echo create_drop_down( "cbo_company_name", 150, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name",1, "-- Select Company --", $cbo_company_id,"",$disable );
                        ?>
                    </td>
                    <td >
                    	<input name="txt_batch_number" id="txt_batch_number" class="text_boxes" style="width:150px">
                    </td>
                    
                    <td align="center">
                        <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:80px"> To 
                        <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:80px">
                    </td> 
                    <td align="center">
                     <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_batch_number').value+'_'+document.getElementById('hidden_batch_ids').value+'_'+document.getElementById('hidden_Batch_Against').value+'_'+document.getElementById('hidden_deleted_id').value+'_'+document.getElementById('hidden_prevBatchDtlsIds').value, 'batch_number_search_list_view', 'search_div', 'subcon_embellishment_production_controller', 'setFilterGrid(\'tbl_list_search\',-1)')" style="width:100px;" />
                        
                    <input type="hidden" id="hidden_batch_ids" name="hidden_batch_ids" class="text_boxes" value="<? //echo $prevBatchIds; ?>" /> 
                    <input type="hidden" id="hidden_prevBatchDtlsIds" name="hidden_prevBatchDtlsIds" class="text_boxes" value="<? echo $prevBatchDtlsIds; ?>" /> 
                    <input type="hidden" name="hidden_Batch_Against" id="hidden_Batch_Against" class="text_boxes" value="<? echo $hiddenBatchAgainst; ?>">
                    <input type="hidden" name="hidden_deleted_id" id="hidden_deleted_id" class="text_boxes" value="<? echo $txt_deleted_dtls_id; ?>">
                    </td>
                </tr>
                <tr>
                    <td colspan="5" align="center" height="40" valign="middle">
						<? echo load_month_buttons(1);  ?>
                    </td>
                </tr>
                <tr>
                    <td colspan="5" align="center" valign="top" id=""><div id="search_div"></div></td>
                </tr>
            </tbody>
        </table>    
        </form>
        </div>
    </body>           
    <script src="../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit();
}

if ($action=="batch_number_search_list_view")
{
	//echo $data; //die;
	$data=explode('_',$data);
	
	$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
	$color_arr=return_library_array( "select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
	
	$batch_qty_arr=return_library_array( "select a.id,  sum(b.roll_no) as batchqty from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and a.company_id=$data[0] and a.status_active=1 and a.is_deleted=0 and a.entry_form=150 and  b.id not in (select c.batch_dtls_id from subcon_embel_production_dtls c where b.id=c.batch_dtls_id and c.batch_id=a.id and c.status_active=1 and c.is_deleted=0 ) group by a.id", "id", "batchqty");
	
	
	
	$company_id =$data[0];
	
	
	//$batch_no=" and a.batch_no like '%".trim($data[3])."%'";
	if(trim($data[3]) != ""){
		//$batch_no=" and a.batch_no like '".trim($data[3])."'";
		$batch_no=" and a.batch_no = '".trim($data[3])."'";
	}
	
	if($db_type==0)
	{
		$date_from= change_date_format($data[1],'yyyy-mm-dd');
		$date_to= change_date_format($data[2],'yyyy-mm-dd');
	}
	else
	{
		$date_from= change_date_format($data[1], "", "",1) ;
		$date_to= change_date_format($data[2], "", "",1);
	}
	
	$date_cond="";
	if($data[1]!="" && $data[2]!=""){
		$date_cond=" and a.batch_date between '".$date_from."' and '".$date_to."'";
	}
	else if($data[1]=="" && $data[2]!=""){
		$date_cond=" and a.batch_date <= '".$date_to."'";
	}
	else if($data[1]!="" && $data[2]==""){
		$date_cond=" and a.batch_date >= '".$date_from."'";
	}
	
	
	if($data[4] != ""){
		$batchIds = " and a.id not in($data[4])";	
	}
	
	if($data[5] != ""){

		$batchAganistIds = " and a.batch_against in($data[5])";	
	}
	
	/*
	if($data[6] != ""){

		$deletedIds = " or b.id in($data[6])";	
	}
	*/
	if(trim($data[7]) != ""){

		$hidden_prevBatchDtlsIds = " and b.id not in($data[7])";	
	}
	
	$po_id_arr=array();
	if($db_type==2) $group_concat="  listagg(cast(b.order_no AS VARCHAR2(4000)),',') within group (order by b.order_no) as order_no" ;
	else if($db_type==0) $group_concat=" group_concat(b.order_no) as order_no" ;
	
    $sql_po=sql_select("select a.mst_id,$group_concat from pro_batch_create_dtls a, subcon_ord_dtls b where a.po_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.mst_id");
	$po_name_arr=array();
	foreach($sql_po as $p_name)
	{
		$po_name_arr[$p_name[csf('mst_id')]]=implode(",",array_unique(explode(",",$p_name[csf('order_no')])));	
	}
	//echo "<pre>";
	//print_r($po_name_arr);
	
	//$arr=array(2=>$po_name_arr,5=>$batch_against,6=>$batch_for,7=>$color_arr);
	
	//$sql = "select a.id, a.batch_no, a.extention_no, a.batch_weight, a.total_trims_weight, a.batch_date, a.batch_against, a.batch_for, a.booking_no, a.color_id from pro_batch_create_mst a where a.company_id=$company_id  and a.status_active=1 and a.entry_form=150 and a.is_deleted=0 $batch_no  $date_cond $batchIds $batchAganistIds order by a.id "; 
	
	$sql = "select a.id, a.batch_no, a.extention_no, a.batch_weight, a.total_trims_weight, a.batch_date, a.batch_against, a.batch_for, a.booking_no, a.color_id from pro_batch_create_mst a , pro_batch_create_dtls b where a.id=b.mst_id and a.company_id=$company_id and a.status_active=1 and a.is_deleted=0 and a.entry_form=150 $batch_no  $date_cond $batchIds $batchAganistIds $hidden_prevBatchDtlsIds  and  b.id not in (select c.batch_dtls_id from subcon_embel_production_dtls c where b.id=c.batch_dtls_id and c.batch_id=a.id and c.status_active=1 and c.is_deleted=0 ) group by  a.id, a.batch_no, a.extention_no, a.batch_weight, a.total_trims_weight, a.batch_date, a.batch_against, a.batch_for, a.booking_no, a.color_id"; 
	
	//echo $sql;	 
	//echo  create_list_view("list_view", "Batch No,Ext. No,Order No,Batch Weight, Batch Date,Batch Against,Batch For, Color", "100,70,150,80,80,80,85,80","810","250",0, $sql, "js_set_value", "id,batch_no,batch_against", "", 1, "0,0,id,0,0,batch_against,batch_for,color_id", $arr, "batch_no,extention_no,id,batch_weight,batch_date,batch_against,batch_for,color_id", "",'','0,0,0,2,3,0,0');
	?>
   
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="848" class="rpt_table" >
                    <thead>
                        <th width="30">SL</th>
                        <th width="100">Batch No</th>
                        <th width="70">Ext. No</th>
                        <th width="120">Order No</th>
                        <th width="70">Batch Qty-Pcs</th>
                        <th width="70">Batch Weight</th>
                        <th width="80">Batch Date</th>
                        <th width="80">Batch Against</th>
                        <th width="85">Batch For</th>
                        <th width="">Color</th>
                    </thead>
                </table>
                <div style="overflow-y:scroll; max-height:267px;" id="buyer_list_view" align="center">
                    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="830" class="rpt_table" id="tbl_list_search" >
                    	<tbody>
                    <?
						$i=1;
    					$result = sql_select($sql);
                        //"batch_no,extention_no,id,batch_weight,batch_date,batch_against,batch_for,color_id"
                        foreach($result as $row)
                        {
                            if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                             
                            
                            ?>
                            <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i;?>)"> 
                                <td width="30" align="center"><?php echo "$i"; ?>
                                    <input type="hidden" name="txtIndividualBatchId" id="txtIndividualBatchId<?php echo $i ?>" value="<? echo $row[csf('id')]; ?>"/>	
                                    <input type="hidden" name="txtIndividualBatchNumber" id="hiddenBatchNumber<?php echo $i ?>" value="<? echo $row[csf('batch_no')]; ?>"/>
                                    <input type="hidden" name="txtIndividualbatchAgainst" id="txtIndividualbatchAgainst<?php echo $i ?>" value="<? echo $row[csf('batch_against')]; ?>"/>
                                </td>	
                                <td width="100" align="center"> <?php echo $row[csf('batch_no')]; ?> </td>
                                <td width="70" align="center"> <?php echo $row[csf('extention_no')]; ?> </td>
                                <td width="120" align="center"> <?php echo $po_name_arr[$row[csf('id')]]; ?> </td>
                                <td width="70" align="center"> <?php echo $batch_qty_arr[$row[csf('id')]]; ?> </td>
                                <td width="70" align="center"> <?php echo $row[csf('batch_weight')]; ?> </td>
                                <td width="80" align="center"> <?php echo $row[csf('batch_date')]; ?> </td>
                                <td width="80" align="center"> <?php echo $batch_against[$row[csf('batch_against')]]; ?> </td>
                                <td width="85" align="center"> <?php echo $batch_for[$row[csf('batch_no')]]; ?> </td>
                                <td width="" align="center"> <?php echo $color_arr[$row[csf('color_id')]]; ?> </td>
                            </tr>
                            <?
                            $i++;
                        }
                    ?>
                        <!--<input type="hidden" name="txt_process_row_id" id="txt_process_row_id" value="<?php //echo $process_row_id; ?>"/>-->
                        </tbody>
                    </table>
                </div>
                 <table width="848" cellspacing="0" cellpadding="0" style="border:none" align="center" class="rpt_table" >
                    <tr>
                        <td align="center" height="30" valign="bottom">
                            
                                <!--<div style="width:50%; float:left" align="left">
                                    <input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()" /> Check / Uncheck All
                                </div>-->
                                <div style="width:100%; float:left" align="center">
                                    <input type="button" name="close" onClick="if($('#hidden_batch_ids').val() != '') parent.emailwindow.hide(); else return;" class="formbutton" value="Close" style="width:100px" />
                                </div>
                            
                        </td>
                    </tr>
                </table>
            </form>
        </fieldset>
	<?
exit();	
}


if ($action=="populate_data_from_embellishment_mst")
{
	$data = explode("_",$data);
	$dataArray=sql_select( "select id, sys_no, company_id, location_id, product_date, prod_source, serv_company, serv_location, batch_against from subcon_embel_production_mst where status_active=1 and is_deleted =0 and id = $data[0] " );
	foreach ($dataArray as $row)
	{	
		if($row[csf("serv_company")] == "") $serv_company = 0; else  $serv_company = $row[csf("serv_company")];
		
		if($row[csf("serv_location")] == "") $serv_location = 0;  else $serv_location = $row[csf("serv_location")];
		
		
		echo "document.getElementById('cbo_company_id').value 			= '".$row[csf("company_id")]."';\n";
		echo "$('#cbo_company_id').attr('disabled','true')".";\n";
		
		
		echo "load_drop_down( 'requires/subcon_embellishment_production_controller', document.getElementById('cbo_company_id').value, 'load_drop_down_location', 'location_td' );\n";
		
		echo "document.getElementById('cbo_location_name').value 		= '".$row[csf("location_id")]."';\n";
		echo "$('#cbo_location_name').attr('disabled','true')".";\n";
		echo "document.getElementById('txt_batch_date').value 			= '".change_date_format($row[csf("product_date")])."';\n";
		  
		echo "document.getElementById('cbo_source').value 				= '".$row[csf("prod_source")]."';\n";
		//echo "$('#cbo_source').attr('disabled','true')".";\n";
		
		echo "load_drop_down( 'requires/subcon_embellishment_production_controller', '".$row[csf("prod_source")]."'+'**'+$('#cbo_company_id').val()+'**'+$('#cbo_location_name').val(), 'load_drop_down_company_supplier', 'issue_to_td' );\n";
		echo "load_drop_down( 'requires/subcon_embellishment_production_controller', '".$row[csf("prod_source")]."'+'**'+$('#cbo_company_id').val()+'**'+$('#cbo_location_name').val(), 'load_drop_down_company_supplier_location', 'issue_to_location_td' );\n";
		
		
		echo "document.getElementById('cbo_company_supplier').value 	= '".$serv_company."';\n";
		//echo "$('#cbo_company_supplier').attr('disabled','true')".";\n";
		
		echo "document.getElementById('cbo_location_name_s').value 		= '".$serv_location."';\n";
		//echo "$('#cbo_location_name_s').attr('disabled','true')".";\n";
		
		echo "document.getElementById('txtEmbelProductionSerch').value 	= '".$row[csf("sys_no")]."';\n";
		echo "document.getElementById('update_id').value 				= '".$row[csf("id")]."';\n";
		echo "document.getElementById('hiddenBatchAgainst').value 		= '".$row[csf("batch_against")]."';\n";
	
		echo "set_button_status(1,'".$_SESSION['page_permission']."', 'fnc_embel_entry',1);\n";	
	}
	exit();	
}


//List View from Embellishment Production Page//
if( $action == 'embellishment_details' ) 
{
	$color_arr=return_library_array( "select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
	$party_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	
	$party_id_by_Order=return_library_array( " select b.id, a.party_id from subcon_ord_mst a, subcon_ord_dtls b where a.subcon_job =b.job_no_mst and a.is_deleted=0 and a.status_active=1",'id','party_id');
	$po_style_arr=return_library_array( " select b.id, b.cust_style_ref from subcon_ord_mst a, subcon_ord_dtls b where a.subcon_job =b.job_no_mst and a.is_deleted=0 and a.status_active=1",'id','cust_style_ref');
	
	
	$data=explode('**',$data);
	$embl_id=$data[0];
	$batch_against=$data[1];
	
	
	//== Array Block for information===//
	if($db_type==0)  
    {
       $gmts_item_id_cond = "group_concat(c.item_id) as gmts_item_id";
    }
    else
    {
       $gmts_item_id_cond = "listagg(cast(c.item_id as varchar2(4000)),',') within group (order by c.item_id) as gmts_item_id";
    }

	$po_data_array=sql_select( "select $gmts_item_id_cond, b.id, b.order_no as po_number from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c where a.subcon_job=b.job_no_mst and b.id=c.order_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.id, b.order_no");
	$po_array=array(); 
	$po_item_array=array();
	foreach($po_data_array as $poRow)
	{
		$po_array[$poRow[csf('id')]]=$poRow[csf('po_number')];
		$po_item_array[$poRow[csf('id')]]=$poRow[csf('gmts_item_id')];
	}

	$po_item_color_qty=array();
	$item_wise_order_qty_array=sql_select("select b.order_uom,c.order_id, c.item_id, c.color_id, sum(c.qnty) as po_qnty from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c where a.id=c.mst_id and b.id=c.order_id and a.subcon_job=b.job_no_mst group by c.order_id, c.color_id, c.item_id,b.order_uom");
	foreach ($item_wise_order_qty_array as $val) 
    {
    	$po_item_color_qty[$val[csf("order_id")]][$val[csf("item_id")]][$val[csf("color_id")]]["qty"]=$val[csf("po_qnty")];
    	$po_item_color_qty[$val[csf("order_id")]][$val[csf("item_id")]][$val[csf("color_id")]]["uom"]=$val[csf("order_uom")];
    }

	$batch_qty_arr=array();
    $batch_dtls_sql="select a.color_id, b.po_id, b.prod_id, sum(b.roll_no) as roll_no from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.po_id, b.prod_id,a.color_id";
    $batchArray = sql_select($batch_dtls_sql);
    foreach ($batchArray as $value) 
    {
    	$batch_qty_arr[$value[csf("po_id")]][$value[csf("prod_id")]][$value[csf("color_id")]]=$value[csf("roll_no")];
    }
	//== Array Block for information===//
	
	$data_array=sql_select("select a.id as emblId, b.id, b.mst_id, b.batch_id, b.batch_no, b.batch_against, b.color_id, b.po_no, b.po_id, b.party_id, b.prod_id, b.process_id, b.batch_qty, b.reje_qty, b.repro_qty, b.qcpass_qty, b.operator_name, b.operator_id, b.shift_id, b.batch_dtls_id  
from subcon_embel_production_mst a, subcon_embel_production_dtls b where a.id=b.mst_id and b.mst_id=$embl_id and a.status_active =1 and a.is_deleted=0 and b.status_active = 1 and b.is_deleted = 0 order by b.id DESC"); 
	
	$tblRow += count($data_array);
	
	foreach($data_array as $row)
	{
		
		$gmts_item_array=array();
		$item_array=explode(",",$po_item_array[$row[csf('po_id')]]);
		foreach($item_array as $item)
		{
			$gmts_item_array[$item]=$garments_item[$item];
		}		
		//echo "<pre>";
		//print_r($gmts_item_array); die;
		$need_multiply=($po_item_color_qty[$row[csf("po_id")]][$row[csf("prod_id")]][$row[csf("color_id")]]["uom"]==2)?12:1;

		?>
            <tr class="general" name="tr[]" id="tr_<? echo $tblRow; ?>">
                <td>
                    <input type="text" name="txtSl[]"  id="txtSl_<? echo $tblRow; ?>" value="<? echo $tblRow; ?>" class="text_boxes_numeric" style="text-align:center; width:30px" disabled />
                </td>
                <td>
                    <input type="text" name="txtBatchNo[]"  id="txtBatchNo_<? echo $tblRow; ?>"  value="<? echo $row[csf('batch_no')]; ?>" class="text_boxes_numeric" style="width:100px" placeholder="Display"disabled />
                     <input type="hidden" name="hiddenBatchId[]"  id="hiddenBatchId_<? echo $tblRow; ?>" value="<? echo $row[csf('batch_id')]; ?>" class="text_boxes_numeric" style="width:50px" placeholder="Display"disabled />
                     <input type="hidden" name="hiddenBatchDtlsId[]"  id="hiddenBatchDtlsId_<? echo $tblRow; ?>" value="<? echo $row[csf('batch_dtls_id')]; ?>" class="text_boxes_numeric" style="width:50px" placeholder="Display"disabled />
                     <input type="hidden" name="updateIdDtls[]"  id="updateIdDtls_<? echo $tblRow; ?>" value="<? echo $row[csf('id')]; ?>" class="text_boxes_numeric" style="width:50px" placeholder="updateIDDtls"disabled />
                </td>
                <td>
                    <input type="text" name="txtBatchcolor[]"  id="txtBatchcolor_<? echo $tblRow; ?>" value="<? echo $color_arr[$row[csf('color_id')]]; ?>" class="text_boxes" style="width:80px" placeholder="Display"disabled />
                    <input type="hidden" name="txtBatchColorId[]"  id="txtBatchColorId_<? echo $tblRow; ?>" value="<? echo $row[csf('color_id')]; ?>" style="width:30px" disabled />
                </td>
                <td id="">	
                    <input type="text" name="txtPoNo[]"  id="txtPoNo_<? echo $tblRow; ?>" value="<? echo $po_array[$row[csf('po_id')]]; ?>" class="text_boxes" style="width:100px"  placeholder="Display" disabled  />
                    <input type="hidden" name="txtPoId[]" id="txtPoId_<? echo $tblRow; ?>" value="<? echo $row[csf('po_id')]; ?>" style="width:50px" class="text_boxes" readonly />
                    
                </td>
                <td>
                    <?
                      // echo create_drop_down( "cboJobParty_".$tblRow, 150, "select buy.id,buy.buyer_name from lib_buyer buy where buy.status_active =1 and buy.is_deleted=0","id,buyer_name",1, "-- Select Party --",$party_id_by_Order[$row[csf('po_id')]],"", "1","","","","","","","cboJobParty[]");
                       
                    ?>
                    <input type="text" name="cboJobPartyName[]"  id="cboJobPartyName_<? echo $tblRow; ?>" value="<? echo $party_arr[$party_id_by_Order[$row[csf('po_id')]]]; ?>" class="text_boxes" style="width:150px" readonly disabled/>
                    <input type="hidden" name="cboJobParty[]"  id="cboJobParty_<? echo $tblRow; ?>" value="<? echo $party_id_by_Order[$row[csf('po_id')]]; ?>"   readonly disabled/>
                </td>
                
                <td>
                    <input type="text" name="txtStyle[]"  id="txtStyle_<? echo $tblRow; ?>" value="<? echo $po_style_arr[$row[csf('po_id')]]; ?>" class="text_boxes" style="width:80px"   placeholder="Display" disabled  />
                </td>
                <td id="">
                    <? //echo create_drop_down( "cboGmtsItem_".$tblRow, 80, $gmts_item_array,"", 1, "-- Select Item --", $row[csf('prod_id')], "",'1','','','','','','','cboGmtsItem[]'); ?>
                    <input type="text" name="cboGmtsItem[]"  id="cboGmtsItem_<? echo $tblRow; ?>" value="<? echo $gmts_item_array[$row[csf('prod_id')]]; ?>" class="text_boxes"  style="width:80px" disabled />
                    <input type="hidden" name="txtGmtsItem[]"  id="txtGmtsItem_<? echo $tblRow; ?>" value="<? echo $row[csf('prod_id')]; ?>" class="text_boxes"  style="width:50px" disabled />
                </td>
                <td>
                    <input type="text" name="txtProcessName[]" id="txtProcessName_<? echo $tblRow; ?>" class="text_boxes" style="width:80px;"  tabindex="12"   placeholder="Dbl.Click" readonly onDblClick="process_popup('<? echo $row[csf('process_id')]; ?>');" title="Bbl. Click" />
                    <input type="hidden" name="txtProcessId[]" id="txtProcessId_<? echo $tblRow; ?>" value="<? echo $row[csf('process_id')]; ?>" />
                </td>
                
                
                <td>
                    <input type="text" name="txtBatchQty[]"  id="txtBatchQty_<? echo $tblRow; ?>" value="<? echo $row[csf('batch_qty')]*1; ?>" class="text_boxes_numeric"  style="width:50px"   placeholder="Display" disabled  />
                </td>
                <td>
                    <input type="text" name="txtRejectQty[]"  id="txtRejectQty_<? echo $tblRow; ?>" value="<? echo $row[csf('reje_qty')]*1; ?>" class="text_boxes_numeric" onKeyUp="calculate_batch_qnty(<? echo $tblRow; ?>,'1');" style="width:50px"   placeholder="Write"  />
                </td>
                <td>
                    <input type="text" name="txtNeedReProcQty[]"  id="txtNeedReProcQty_<? echo $tblRow; ?>" value="<? echo $row[csf('repro_qty')]*1; ?>"  class="text_boxes_numeric" onKeyUp="calculate_batch_qnty(<? echo $tblRow; ?>,'2');"   placeholder="Write"  style="width:50px" />
                </td>
                
                <td>
                    <input type="text" name="txtQcPassQty[]"  id="txtQcPassQty_<? echo $tblRow; ?>" value="<? echo $row[csf('qcpass_qty')]*1; ?>" class="text_boxes_numeric"  style="width:50px" readonly />
                </td>

                <td>
                <input type="text" name="txtOperatorName[]" id="txtOperatorName_<? echo $tblRow; ?>" value="<? echo $row[csf('operator_name')]; ?>"  onKeyPress="Javascript: if (event.keyCode==40){ emp_code_onkeypress(this.value,<? echo $tblRow; ?>)}" onBlur="emp_code_onkeypress( this.value,<? echo $tblRow; ?> )"  class="text_boxes" style="width:100px;"   placeholder="Write"   tabindex="4" />
                <input type="hidden" name="txtOperatorId[]" id="txtOperatorId_<? echo $tblRow; ?>" value="<? echo $row[csf('operator_id')]; ?>" style="width:60px"/>

                </td>
                <td>
                    <?
                        echo create_drop_down( "cboShift_".$tblRow, 80, $shift_name,"", 1, '- Select -', $row[csf('shift_id')], "",'','','','','','','','cboShift[]' );
                    ?>
                </td>
                <td>
                	<input type="button" name="btnRowDelete[]"  id="btnRowDelete_<? echo $tblRow; ?>" value="-" class="formbutton" onClick="fn_deleteRow(<? echo $tblRow; ?>)"  style="width:30px"  />
            </td>
            </tr>
		<?
	$tblRow-- ;
	}
	exit();
}


//List View from SubCon Batch For Gmts Wash/Dyeing/Printing Page//
if( $action == 'populate_batch_data') 
{
	
	$color_arr=return_library_array( "select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
	$party_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	
	$party_id_by_Order=return_library_array( " select b.id, a.party_id from subcon_ord_mst a, subcon_ord_dtls b where a.subcon_job =b.job_no_mst and a.is_deleted=0 and a.status_active=1",'id','party_id');
	$po_style_arr=return_library_array( " select b.id, b.cust_style_ref from subcon_ord_mst a, subcon_ord_dtls b where a.subcon_job =b.job_no_mst and a.is_deleted=0 and a.status_active=1",'id','cust_style_ref');
	
	
	
	
	$data=explode('**',$data);
	$batch_id=$data[0];
	if($data[1]==0){
		$tblRow = 1;
	}else{
		$tblRow += $data[1]+1;
	}
	$batch_against=$data[2];
	//$batch_for=$data[1];
	
	
	//$i=$data[2];
	if($db_type==0)  
    {
       $gmts_item_id_cond = "group_concat(c.item_id) as gmts_item_id";
    }
    else
    {
       $gmts_item_id_cond = "listagg(cast(c.item_id as varchar2(4000)),',') within group (order by c.item_id) as gmts_item_id";
    }

	$po_data_array=sql_select( "select $gmts_item_id_cond, b.id, b.order_no as po_number from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c where a.subcon_job=b.job_no_mst and b.id=c.order_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.id, b.order_no");
	$po_array=array(); 
	$po_item_array=array();
	foreach($po_data_array as $poRow)
	{
		$po_array[$poRow[csf('id')]]=$poRow[csf('po_number')];
		$po_item_array[$poRow[csf('id')]]=$poRow[csf('gmts_item_id')];
	}

	$po_item_color_qty=array();
	$item_wise_order_qty_array=sql_select("select b.order_uom,c.order_id, c.item_id, c.color_id, sum(c.qnty) as po_qnty from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c where a.id=c.mst_id and b.id=c.order_id and a.subcon_job=b.job_no_mst group by c.order_id, c.color_id, c.item_id,b.order_uom");
	foreach ($item_wise_order_qty_array as $val) 
    {
    	$po_item_color_qty[$val[csf("order_id")]][$val[csf("item_id")]][$val[csf("color_id")]]["qty"]=$val[csf("po_qnty")];
    	$po_item_color_qty[$val[csf("order_id")]][$val[csf("item_id")]][$val[csf("color_id")]]["uom"]=$val[csf("order_uom")];
    }

	$batch_qty_arr=array();
    $batch_dtls_sql="select a.color_id, b.po_id, b.prod_id, sum(b.roll_no) as roll_no from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.po_id, b.prod_id,a.color_id";
    $batchArray = sql_select($batch_dtls_sql);
    foreach ($batchArray as $value) 
    {
    	$batch_qty_arr[$value[csf("po_id")]][$value[csf("prod_id")]][$value[csf("color_id")]]=$value[csf("roll_no")];
    }
	
	
	
	
	//$data_array=sql_select("select a.id as batch_id, a.color_id, a.batch_no, a.entry_form, a.batch_date, a. batch_against, a.batch_for, a.company_id, a.booking_no_id, a.booking_no, a.booking_without_order, a.extention_no, a.color_id, a.batch_weight, a.color_range_id, a.process_id, a.shift_id, b.id, b.prod_id, b.batch_qnty, b.po_id, b.roll_no, b.batch_qnty, b.program_no, b.po_batch_no, b.dtls_id, b.fabric_from, b.barcode_no, b.roll_id, b.body_part_id, b.gsm, b.grey_dia  from pro_batch_create_mst a, pro_batch_create_dtls b  where b.mst_id in($batch_id) and a.id=b.mst_id and b.status_active=1 and b.is_deleted=0 order by b.id DESC"); 
	
	
	
	$data_query="select a.id as batch_id, a.color_id, a.batch_no, a.entry_form, a.batch_date, a. batch_against, a.batch_for, a.company_id, a.booking_no_id, a.booking_no, a.booking_without_order, a.extention_no, a.color_id, a.batch_weight, a.color_range_id, a.process_id, a.shift_id, b.id, b.prod_id, b.batch_qnty, b.po_id, b.roll_no, b.batch_qnty, b.program_no, b.po_batch_no, b.dtls_id, b.fabric_from, b.barcode_no, b.roll_id, b.body_part_id, b.gsm, b.grey_dia  
	from pro_batch_create_mst a, pro_batch_create_dtls b  where b.mst_id in($batch_id) and a.id=b.mst_id and b.status_active=1 and b.is_deleted=0
	and  b.id not in (select c.batch_dtls_id from subcon_embel_production_dtls c where b.id=c.batch_dtls_id and c.batch_id=a.id and c.status_active=1 and c.is_deleted=0 ) 
	order by b.id DESC"; 	
	
	//echo $data_query;
	$data_array=sql_select($data_query);
	
	
	
	
	$tblRow += count($data_array);
	
	foreach($data_array as $row)
	{
		$tblRow-- ;
		$gmts_item_array=array();
		$item_array=explode(",",$po_item_array[$row[csf('po_id')]]);
		foreach($item_array as $item)
		{
			$gmts_item_array[$item]=$garments_item[$item];
		}		
		
		$need_multiply=($po_item_color_qty[$row[csf("po_id")]][$row[csf("prod_id")]][$row[csf("color_id")]]["uom"]==2)?12:1;

		$chkgmtsqty=($po_item_color_qty[$row[csf("po_id")]][$row[csf("prod_id")]][$row[csf("color_id")]]["qty"]*$need_multiply-$batch_qty_arr[$row[csf("po_id")]][$row[csf("prod_id")]][$row[csf("color_id")]])+$row[csf('roll_no')];
		?>
        <tr class="general" name="tr[]" id="tr_<? echo $tblRow; ?>">
            <td>
                <input type="text" name="txtSl[]"  id="txtSl_<? echo $tblRow; ?>" value="<? echo $tblRow; ?>" class="text_boxes_numeric" style="text-align:center; width:30px" disabled />
            </td>
            <td>
                <input type="text" name="txtBatchNo[]"  id="txtBatchNo_<? echo $tblRow; ?>"  value="<? echo $row[csf('batch_no')]; ?>" class="text_boxes_numeric" style="width:100px" placeholder="Display"disabled />
                 <input type="hidden" name="hiddenBatchId[]"  id="hiddenBatchId_<? echo $tblRow; ?>" value="<? echo $row[csf('batch_id')]; ?>" class="text_boxes_numeric" style="width:50px" placeholder="Display"disabled />
                 <input type="hidden" name="hiddenBatchDtlsId[]"  id="hiddenBatchDtlsId_<? echo $tblRow; ?>" value="<? echo $row[csf('id')]; ?>" class="text_boxes_numeric" style="width:50px" placeholder="Display"disabled />
                 <input type="hidden" name="updateIdDtls[]"  id="updateIdDtls_<? echo $tblRow; ?>" value="<? echo ""; ?>" class="text_boxes_numeric" style="width:50px" placeholder="updateIDDtls"disabled />
            </td>
            <td>
                <input type="text" name="txtBatchcolor[]"  id="txtBatchcolor_<? echo $tblRow; ?>" value="<? echo $color_arr[$row[csf('color_id')]]; ?>" class="text_boxes" style="width:80px" placeholder="Display"disabled />
                <input type="hidden" name="txtBatchColorId[]"  id="txtBatchColorId_<? echo $tblRow; ?>" value="<? echo $row[csf('color_id')]; ?>" style="width:30px" disabled />
            </td>
            <td id="">	
                <input type="text" name="txtPoNo[]"  id="txtPoNo_<? echo $tblRow; ?>" value="<? echo $po_array[$row[csf('po_id')]]; ?>" class="text_boxes" style="width:100px"  placeholder="Display" disabled  />
                <input type="hidden" name="txtPoId[]" id="txtPoId_<? echo $tblRow; ?>" value="<? echo $row[csf('po_id')]; ?>" style="width:50px" class="text_boxes" readonly />
                
            </td>
            <td>
                <?
                   //echo create_drop_down( "cboJobParty_".$tblRow, 150, "select buy.id,buy.buyer_name from lib_buyer buy where buy.status_active =1 and buy.is_deleted=0","id,buyer_name",1, "-- Select Party --",$party_id_by_Order[$row[csf('po_id')]],"", "1","","","","","","","cboJobParty[]");
                   
                ?>
                <input type="text" name="cboJobPartyName[]"  id="cboJobPartyName_<? echo $tblRow; ?>" value="<? echo $party_arr[$party_id_by_Order[$row[csf('po_id')]]]; ?>" class="text_boxes" style="width:150px" readonly disabled />
                
    			<input type="hidden" name="cboJobParty[]"  id="cboJobParty_<? echo $tblRow; ?>" value="<? echo $party_id_by_Order[$row[csf('po_id')]]; ?>"   readonly />
            </td>
            
            <td>
                <input type="text" name="txtStyle[]"  id="txtStyle_<? echo $tblRow; ?>" value="<? echo $po_style_arr[$row[csf('po_id')]]; ?>" class="text_boxes" style="width:80px"   placeholder="Display" disabled  />
            </td>
            <td id="">
                <? //echo create_drop_down( "cboGmtsItem_".$tblRow, 80, $gmts_item_array,"", 1, "-- Select Item --", $row[csf('prod_id')], "",'1','','','','','','','cboGmtsItem[]'); ?>
                <input type="text" name="cboGmtsItem[]"  id="cboGmtsItem_<? echo $tblRow; ?>" value="<? echo $gmts_item_array[$row[csf('prod_id')]]; ?>" class="text_boxes"  style="width:80px" readonly disabled />
                <input type="hidden" name="txtGmtsItem[]"  id="txtGmtsItem_<? echo $tblRow; ?>" value="<? echo $row[csf('prod_id')]; ?>" class="text_boxes"  style="width:50px" />
            </td>
            <td>
                <input type="text" name="txtProcessName[]" id="txtProcessName_<? echo $tblRow; ?>" class="text_boxes" style="width:80px;"  tabindex="12"   placeholder="Dbl.Click" readonly onDblClick="process_popup('<? echo $row[csf('process_id')]; ?>');" title="Bbl. Click" />
                <input type="hidden" name="txtProcessId[]" id="txtProcessId_<? echo $tblRow; ?>" value="<? echo $row[csf('process_id')]; ?>" />
            </td>
            <td>
                <input type="text" name="txtBatchQty[]"  id="txtBatchQty_<? echo $tblRow; ?>" value="<? echo $row[csf('roll_no')];//echo $row[csf('batch_qnty')]; ?>" class="text_boxes_numeric"  style="width:50px"   placeholder="Display" disabled  />
            </td>
            <td>
                <input type="text" name="txtRejectQty[]"  id="txtRejectQty_<? echo $tblRow; ?>" value="" class="text_boxes_numeric" onKeyUp="calculate_batch_qnty(<? echo $tblRow; ?>,'1');" style="width:50px"   placeholder="Write"  />
            </td>
            <td>
                <input type="text" name="txtNeedReProcQty[]"  id="txtNeedReProcQty_<? echo $tblRow; ?>" value="" class="text_boxes_numeric" onKeyUp="calculate_batch_qnty(<? echo $tblRow; ?>,'2');"   placeholder="Write"  style="width:50px" />
            </td>
            
            <td>
                <input type="text" name="txtQcPassQty[]"  id="txtQcPassQty_<? echo $tblRow; ?>" value="<? echo $row[csf('roll_no')]; //echo $row[csf('batch_qnty')]; ?>" class="text_boxes_numeric"  style="width:50px" readonly />
            </td>

            <td>
            <input type="text" name="txtOperatorName[]" id="txtOperatorName_<? echo $tblRow; ?>" value="" onKeyPress="Javascript: if (event.keyCode==40){ emp_code_onkeypress(this.value,<? echo $tblRow; ?>)}" onBlur="emp_code_onkeypress( this.value,<? echo $tblRow; ?> )" class="text_boxes" style="width:100px;"   placeholder="Write"   tabindex="4" />
            <input type="hidden" name="txtOperatorId[]" id="txtOperatorId_<? echo $tblRow; ?>" value="<? echo $row[csf('po_id')]; ?>" style="width:60px"/>

            </td>
            <td id="">
                <?
                    echo create_drop_down( "cboShift_".$tblRow, 80, $shift_name,"", 1, '- Select -', $row[csf('shift_id')], "",'','','','','','','','cboShift[]' );
                ?>
            </td>
            <td>
                <input type="button" name="btnRowDelete[]"  id="btnRowDelete_<? echo $tblRow; ?>" value="-" class="formbutton" onClick="fn_deleteRow(<? echo $tblRow; ?>)"  style="width:30px"  />
            </td>
        </tr>
		<?
	}
	exit();
}

if($action=="process_name_popup")
{
  	echo load_html_head_contents("Process Name Info","../../", 1, 1, '','1','');
	extract($_REQUEST);
?>
	<script>
	
		$(document).ready(function(e) {
			setFilterGrid('tbl_list_search',-1);
		});
		
		var selected_id = new Array(); var selected_name = new Array();
		
		function check_all_data() 
		{
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length; 

			tbl_row_count = tbl_row_count-1;
			for( var i = 1; i <= tbl_row_count; i++ ) {
				js_set_value( i );
			}
		}
		
		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		
		function set_all()
		{
			var old=document.getElementById('txt_process_row_id').value; 
			if(old!="")
			{   
				old=old.split(",");
				for(var k=0; k<old.length; k++)
				{   
					js_set_value( old[k] ) 
				} 
			}
		}
		
		function js_set_value( str ) 
		{
			
			toggle( document.getElementById( 'search' + str ), '#FFFFCC' );
			
			if( jQuery.inArray( $('#txt_individual_id' + str).val(), selected_id ) == -1 ) {
				selected_id.push( $('#txt_individual_id' + str).val() );
				selected_name.push( $('#txt_individual' + str).val() );
				
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == $('#txt_individual_id' + str).val() ) break;
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
			
			$('#hidden_process_id').val(id);
			$('#hidden_process_name').val(name);
		}
    </script>

</head>

<body>
<div align="center">
	<fieldset style="width:275px;margin-left:10px">
        <form name="searchprocessfrm_1"  id="searchprocessfrm_1" autocomplete="off">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="272" class="rpt_table" >
                <thead>
                    <th width="50">SL</th>
                    <th>Process Name</th>
                </thead>
            </table>
            <div style="width:274px; overflow-y:scroll; max-height:300px;" id="buyer_list_view" align="center">
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="255" class="rpt_table" id="tbl_list_search" >
                <?

                	if($hiddenBatchAgainst==6){ $new_subprocess_array = $emblishment_wash_type;}
                	else if($hiddenBatchAgainst==10){ $new_subprocess_array = $emblishment_print_type;}
                	else if($hiddenBatchAgainst==7){ $new_subprocess_array = $emblishment_gmts_type;}

                    $i=1; $process_row_id=''; 

					$hidden_process_id=explode(",",$process_id);
                    foreach($new_subprocess_array as $id=>$name)
                    {
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						 
						if(in_array($id,$hidden_process_id)) 
						{ 
							if($process_row_id=="") $process_row_id=$i; else $process_row_id.=",".$i;
						}
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" >
							<td width="50" align="center"><?php echo "$i"; ?>
								<input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i ?>" value="<? echo $id; ?>"/>	
								<input type="hidden" name="txt_individual" id="txt_individual<?php echo $i ?>" value="<? echo $name; ?>"/>
                                <input type="hidden" name="txt_mandatory" id="txt_mandatory<?php echo $i ?>" value="<? echo $mandatory; ?>"/>
							</td>	
							<td><p><? echo $name; ?></p></td>
						</tr>
						<?
						$i++;
                    }
                ?>
                    <input type="hidden" name="txt_process_row_id" id="txt_process_row_id" value="<?php echo $process_row_id; ?>"/>
                </table>
            </div>
             
        </form>
    </fieldset>
</div>    
</body>           
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
<script>
	set_all();
</script>
</html>
<?
exit();
}


//Employee Name AutoComplite
if ($action == "employee_name") 
{
	if($db_type==0)
	{
		$sql = "select concat(first_name,' ',middle_name,' ',last_name,':',emp_code) as employeeName from lib_employee where status_active=1 and  is_deleted =0 and company_id =$data";
	}else{
		$sql = "select (first_name|| ' ' ||middle_name|| ' ' ||last_name  || ':' ||  emp_code) as employeeName  from lib_employee where status_active=1 and  is_deleted =0 and company_id =$data";
	
	}
	
	echo "[" . substr(return_library_autocomplete($sql, "employeeName"), 0, -1) . "]";
    exit();
	
}

//Employe name Vairfication//
if($action=="emp_code_onkeypress")
{
	$data=explode("__",$data);
	$sql = "select id, emp_code, (first_name|| ' ' ||middle_name|| ' ' ||last_name) as employeeName  from lib_employee where status_active=1 and  is_deleted =0 and company_id =$data[1] and (first_name|| ' ' ||middle_name|| ' ' ||last_name)='$data[0]'";
	//echo $sql;
	$sql_rows=sql_select($sql, 1);
	foreach($sql_rows as $rows):
		echo $rows[csf("id")]."_".$rows[csf("emp_code")]."_".$rows[csf("employeeName")];
	endforeach; 
}


if ($action=="check_embl_production")
{
	$data = explode("_",$data);
	//$previousId = implode(",", array_unique( explode(",",$data[2])));
	$batch_against="";
	if($data[0] != ""){
		$batch_no = " and a.batch_no='$data[0]' ";
	}
	elseif($data[1]){
		$batch_against = " and a.batch_against='$data[1]'";
	}else{
		return; 
	}
	
	if($data[2] != ""){
		//$previousIds = " and a.id not in($previousId) ";
	}
	
	//$data_array=sql_select("select a.id as batch_id, a.color_id, a.batch_no, a.entry_form, a.batch_date, a. batch_against, a.batch_for, a.company_id, a.booking_no_id, a.booking_no, a.booking_without_order, a.extention_no, a.color_id, a.batch_weight, a.color_range_id, a.process_id, a.shift_id, b.id, b.prod_id, b.batch_qnty, b.po_id, b.roll_no, b.batch_qnty, b.program_no, b.po_batch_no, b.dtls_id, b.fabric_from, b.barcode_no, b.roll_id, b.body_part_id, b.gsm, b.grey_dia  from pro_batch_create_mst a, pro_batch_create_dtls b  where b.mst_id in($batch_id) and a.id=b.mst_id and b.status_active=1 and b.is_deleted=0 order by b.id"); 
	//$sql="select a.id as mst_id, b.id, b.asset_no, b.asset_id, b.asset_type from fam_asset_placement_dtls b, fam_asset_placement_mst a  where a.id=b.mst_id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $assetNoCond $assetIdCond";
	
	//$sql="select a.id as batch_id, a.batch_no, a. batch_against, a.batch_for, a.company_id   from pro_batch_create_mst a  where  a.status_active=1 and a.is_deleted=0 $batch_no $batch_against ";
	
	
	$sql = "select a.id as batch_id, a.batch_no, a. batch_against, a.batch_for, a.company_id  from pro_batch_create_mst a , pro_batch_create_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and a.entry_form=150 $batch_no $batch_against and  b.id not in (select c.batch_dtls_id from subcon_embel_production_dtls c where b.id=c.batch_dtls_id and c.batch_id=a.id and c.status_active=1 and c.is_deleted=0 ) group by  a.id, a.batch_no, a. batch_against, a.batch_for, a.company_id"; 
	
	//echo $sql; //die;
	
	$data_array=sql_select($sql);
	
	if(count($data_array)==0){
		//echo "0"."_"."0"."_"."This Asset is not found !!"; die;
		echo "0"."_"."0"."_"."This Batch is not found !!"; die;
	}
	else
	{
		foreach ($data_array as $row)
		{
			//$assetInfo = $row[csf("asset_no")]."*".$row[csf("mst_id")];
			echo "1"."_". $row[csf("batch_id")]."_".$row[csf("batch_against")]."_".$row[csf("company_id")];
		}
	}
	exit();
}



if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	//$process_knitting="2";
	
	
	if($db_type==0)
	{
		$batch_date= change_date_format(str_replace("'","",$txt_batch_date),'yyyy-mm-dd');
	}
	else
	{
		$batch_date= change_date_format(str_replace("'","",$txt_batch_date), "", "",1) ;
	}	
	
	if(str_replace("'","",$cbo_company_supplier) == ""){
		$cbo_company_supplier='0';
	}
	
	if(str_replace("'","",$cbo_location_name_s) == ""){
		$cbo_location_name_s='0';
	}
	
	if ($operation==0)   // Insert Here==============================================================
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		if($db_type==0)
		{
			$year_cond=" and YEAR(insert_date)";	
		}
		else if($db_type==2)
		{
			$year_cond=" and TO_CHAR(insert_date,'YYYY')";	
		}
		
		
		//$txt_batch_date=str_replace("'","",$txt_batch_date);
		
		$new_return_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_id), '', 'EMP', date("Y",time()), 5, "select id,prefix_no,prefix_no_num from  subcon_embel_production_mst where company_id=$cbo_company_id  $year_cond=".date('Y',time())." order by id desc ", "prefix_no", "prefix_no_num" ));
		
		
		$id=return_next_id( "id", "subcon_embel_production_mst", 1 ) ; 
		$field_array="id, prefix_no, prefix_no_num, sys_no, company_id, location_id, product_date, prod_source, serv_company, serv_location, batch_against,status_active,is_deleted, inserted_by, insert_date";
		$data_array="(".$id.",'".$new_return_no[1]."','".$new_return_no[2]."','".$new_return_no[0]."',".$cbo_company_id.",".$cbo_location_name.",'".$batch_date."',".$cbo_source.",".$cbo_company_supplier.",".$cbo_location_name_s.",".$hiddenBatchAgainst.",1,0,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')"; 
		
		
		$PoIdValidation="";
		$data_array_dtls="";
		$id_dtls=return_next_id( "id", "subcon_embel_production_dtls", 1 ) ;
		$field_array_dtls="id, mst_id, batch_id, batch_no,batch_dtls_id, color_id, po_no, po_id, party_id, prod_id, process_id, batch_qty, reje_qty, repro_qty, qcpass_qty, operator_name, operator_id, shift_id, batch_against,status_active,is_deleted, inserted_by, insert_date";
		for($i=1;$i<=$total_row;$i++)
		{
			
			$txtBatchNo="txtBatchNo_".$i; 
			$hiddenBatchId="hiddenBatchId_".$i;
			$hiddenBatchDtlsId="hiddenBatchDtlsId_".$i;
			$txtBatchColorId="txtBatchColorId_".$i;
			$txtPoNo="txtPoNo_".$i;
			$txtPoId="txtPoId_".$i;
			$cboJobParty="cboJobParty_".$i;
			$txtGmtsItem="txtGmtsItem_".$i;
			$txtProcessId="txtProcessId_".$i;
			$txtBatchQty="txtBatchQty_".$i;
			$txtRejectQty="txtRejectQty_".$i;
			$txtNeedReProcQty="txtNeedReProcQty_".$i;
			$txtQcPassQty="txtQcPassQty_".$i;
			$txtOperatorName="txtOperatorName_".$i;
			$txtOperatorId="txtOperatorId_".$i;
			$cboShift="cboShift_".$i;
			$updateIdDtls="updateIdDtls_".$i;
			
			if( ($$txtQcPassQty*1) > 0 )
			{
				if($data_array_dtls!="") $data_array_dtls.=",";
				$data_array_dtls.="(".$id_dtls.",".$id.",".$$hiddenBatchId.",'".$$txtBatchNo."','".$$hiddenBatchDtlsId."','".$$txtBatchColorId."','".$$txtPoNo."','".$$txtPoId."','".$$cboJobParty."','".$$txtGmtsItem."','".$$txtProcessId."','".$$txtBatchQty."','".$$txtRejectQty."','".$$txtNeedReProcQty."','".$$txtQcPassQty."','".$$txtOperatorName."','".$$txtOperatorId."','".$$cboShift."',".$hiddenBatchAgainst.",1,0,'".$_SESSION['logic_erp']['user_id']."','".$pc_date_time."')"; 
				
				$id_dtls=$id_dtls+1;
				
				if($PoIdValidation!="") 
					$PoIdValidation .= ",".$$txtPoId;
				else
					$PoIdValidation .= $$txtPoId;
			}
			
		}
		
		//Start: Insert Validation Check//
		if ( $PoIdValidation != "" ) 
		{
			$asset_id_library = return_library_array("SELECT po_no, po_id FROM subcon_embel_production_dtls WHERE po_id in($PoIdValidation) AND status_active=1 AND is_deleted=0", "po_id", "po_no");
			
			if(count($asset_id_library) > 0)
			{
				echo "11**";
				disconnect($con);
				die;
			}
		}
		//End: Insert Validation Check//
		

		
		
		
		
		//echo "10**insert into subcon_embel_production_mst ($field_array) values $data_array "; die;
		//echo "10**insert into subcon_embel_production_dtls ($field_array_dtls) values $data_array_dtls "; die;
		$rID=sql_insert("subcon_embel_production_mst",$field_array,$data_array,0);
		$rID1=sql_insert("subcon_embel_production_dtls",$field_array_dtls,$data_array_dtls,0);
		
		//echo "10**".$rID."**".$rID1; die;
		if($db_type==0)
		{
			if($rID && $rID1)
			{
				mysql_query("COMMIT");  
				echo "0**".str_replace("'",'',$id)."**".$new_return_no[0]."**".str_replace("'",'',$hiddenBatchAgainst);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'",'',$id);
			}
		}
		if($db_type==2)
		{
			if($rID && $rID1)
			{
				oci_commit($con);
				echo "0**".str_replace("'",'',$id)."**".$new_return_no[0]."**".str_replace("'",'',$hiddenBatchAgainst);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'",'',$id)."**".$new_return_no[0];
			}
		}	
		disconnect($con);
		die;
	}
	else if ($operation==1)   // Update Here============================================================
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		$field_array_update="company_id*location_id*product_date*prod_source*serv_company*serv_location*batch_against*updated_by*update_date";
		$data_array_update="".$cbo_company_id."*".$cbo_location_name."*'".$batch_date."'*".$cbo_source."*".$cbo_company_supplier."*".$cbo_location_name_s."*".$hiddenBatchAgainst."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"; 		
		
		
		$data_array_dtls_update="";
		$field_array_dtls_update="mst_id*batch_id*batch_no*batch_dtls_id*color_id*po_no*po_id*party_id*prod_id*process_id*batch_qty*reje_qty*repro_qty*qcpass_qty*operator_name*operator_id*shift_id*batch_against*status_active*is_deleted*updated_by*update_date";
		
		$data_array_dtls="";
		$id_dtls=return_next_id( "id", "subcon_embel_production_dtls", 1 ) ;
		$field_array_dtls="id, mst_id, batch_id, batch_no, batch_dtls_id, color_id, po_no, po_id, party_id, prod_id, process_id, batch_qty, reje_qty, repro_qty, qcpass_qty, operator_name, operator_id, shift_id, batch_against,status_active,is_deleted, inserted_by, insert_date";
		
		for($i=1;$i<=$total_row;$i++)
		{
			$txtBatchNo="txtBatchNo_".$i; 
			$hiddenBatchId="hiddenBatchId_".$i;
			$hiddenBatchDtlsId="hiddenBatchDtlsId_".$i;
			$txtBatchColorId="txtBatchColorId_".$i;
			$txtPoNo="txtPoNo_".$i;
			$txtPoId="txtPoId_".$i;
			$cboJobParty="cboJobParty_".$i;
			$txtGmtsItem="txtGmtsItem_".$i;
			$txtProcessId="txtProcessId_".$i;
			$txtBatchQty="txtBatchQty_".$i;
			$txtRejectQty="txtRejectQty_".$i;
			$txtNeedReProcQty="txtNeedReProcQty_".$i;
			$txtQcPassQty="txtQcPassQty_".$i;
			$txtOperatorName="txtOperatorName_".$i;
			$txtOperatorId="txtOperatorId_".$i;
			$cboShift="cboShift_".$i;
			$updateIdDtls="updateIdDtls_".$i;
			
			$updateIds = str_replace("'","",$$updateIdDtls);
			if( ($$txtQcPassQty*1) > 0 )
			{
			
				if( $updateIds != "")
				{
					
					$updateIdDtls_array[]=$updateIds;
					//if($data_array_dtls_update != "") $data_array_dtls_update .= ","; 	
					$data_array_dtls_update[$updateIds] = explode("*",("".$update_id."*".$$hiddenBatchId."*'".$$txtBatchNo."'*'".$$hiddenBatchDtlsId."'*'".$$txtBatchColorId."'*'".$$txtPoNo."'*'".$$txtPoId."'*'".$$cboJobParty."'*'".$$txtGmtsItem."'*'".$$txtProcessId."'*'".$$txtBatchQty."'*'".$$txtRejectQty."'*'".$$txtNeedReProcQty."'*'".$$txtQcPassQty."'*'".$$txtOperatorName."'*'".$$txtOperatorId."'*'".$$cboShift."'*".$hiddenBatchAgainst."*1*0*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
					
				}else{
					
					if($data_array_dtls!="") $data_array_dtls.=","; 	
					$data_array_dtls.="(".$id_dtls.",".$update_id.",".$$hiddenBatchId.",'".$$txtBatchNo."','".$$hiddenBatchDtlsId."','".$$txtBatchColorId."','".$$txtPoNo."','".$$txtPoId."','".$$cboJobParty."','".$$txtGmtsItem."','".$$txtProcessId."','".$$txtBatchQty."','".$$txtRejectQty."','".$$txtNeedReProcQty."','".$$txtQcPassQty."','".$$txtOperatorName."','".$$txtOperatorId."','".$$cboShift."',".$hiddenBatchAgainst.",1,0,'".$_SESSION['logic_erp']['user_id']."','".$pc_date_time."')";
					
					$id_dtls=$id_dtls+1;
				}
			
			}
			
		}
		//echo "10**".bulk_update_sql_statement("subcon_embel_production_dtls", "id", $field_array_dtls_update, $data_array_dtls_update, $updateIdDtls_array); die;
		//echo "10**insert into subcon_embel_production_dtls ($field_array_dtls) values $data_array_dtls "; die;
		
		$rID=$rID1=$rID2=1;
		
		$rID=sql_update("subcon_embel_production_mst",$field_array_update,$data_array_update,"id",$update_id,0);
		
		if($data_array_dtls !=""){
			$rID1=sql_insert("subcon_embel_production_dtls",$field_array_dtls,$data_array_dtls,0);
		}
		
		if($data_array_dtls_update !=""){
			$rID2=execute_query(bulk_update_sql_statement( "subcon_embel_production_dtls", "id", $field_array_dtls_update, $data_array_dtls_update, $updateIdDtls_array ));
		}
		
		
		
		if(str_replace("'","",$txtSubConEmbDtlsIds) != ""){
			/*$field_delete_array="updated_by*update_date*status_active*is_deleted";
			$data_delete_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'0'*'1'";
			$DDataArr = explode(",",str_replace("'","",$txtSubConEmbDtlsIds));
			foreach($DDataArr as $id ){
				$rID3=sql_update("subcon_embel_production_dtls",$field_delete_array,$data_delete_array,"id",$id,0);
			}*/
			

			$DDataArr = explode(",",str_replace("'","",$txtSubConEmbDtlsIds));
			foreach($DDataArr as $id ){
				//$rID3=sql_update("subcon_embel_production_dtls",$field_delete_array,$data_delete_array,"id",$id,0);
				$rID3=execute_query("delete from subcon_embel_production_dtls where id=$id");
			}
		}
		
		
		
		$update_id=str_replace("'","",$update_id);
		$hiddenBatchAgainst=str_replace("'","",$hiddenBatchAgainst);
		$txtEmbelProductionSerch=str_replace("'","",$txtEmbelProductionSerch);
		
		//echo "10**".$rID."**".$rID1."**".$rID2."**".$rID3;die;
		if($db_type==0)
		{
			if($rID && $rID1 && $rID2)
			{
				mysql_query("COMMIT");  
				echo "1**".$update_id."****".$hiddenBatchAgainst;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".$update_id."****".$hiddenBatchAgainst;
			}
		}
		if($db_type==2)
		{
			if($rID && $rID1 && $rID2)
			{
				oci_commit($con);
				echo "1**".$update_id."**".$txtEmbelProductionSerch."**".$hiddenBatchAgainst;
			}
			else
			{
				oci_rollback($con);
				echo "10**".$update_id."**".$txtEmbelProductionSerch."**".$hiddenBatchAgainst;
			}
		}	
		disconnect($con);
 		die;
	}
	else if ($operation==2)   // Delete Here ============================================================
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$field_array="updated_by*update_date*status_active*is_deleted";
		$data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'0'*'1'";
		$rID=sql_delete("subcon_embel_production_mst",$field_array,$data_array,"id","".$update_id."",1);
		$rID1=sql_delete("subcon_embel_production_dtls",$field_array,$data_array,"mst_id","".$update_id."",1);
		//echo "10**".$rID."**".$rID1; die;	
		if($db_type==0)
		{
			if($rID && $rID1)
			{
				mysql_query("COMMIT");  
				echo "2**".str_replace("'",'',$update_id);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'",'',$update_id);
			}
		}
		if($db_type==2)
		{
			if ($rID && $rID1) {
                oci_commit($con);
                echo "2**" .str_replace("'",'',$update_id);
            } else {
                oci_rollback($con);
                echo "10**" .str_replace("'",'',$update_id);
            }
		}
		disconnect($con);
		die;
	}
}

?>