<?php
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../includes/common.php');

$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$trans_Type="3";

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_name", 140, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --", $selected, "" );
	exit();	 
}

if($action=="load_drop_down_company_supplier")
{
	echo create_drop_down( "cbo_company_supplier", 140, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id  and b.tag_company='$data' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Party --", $selected, "","","","","","",5 );
	
	exit();
}

if ($action=="return_id_popup")
{
	echo load_html_head_contents("Popup Info","../../", 1, 1, $unicode,'','');
	$ex_data=explode('_',$data);
	?>
	<script>
		function js_set_value(id)
		{ 
			document.getElementById('return_id').value=id;
			parent.emailwindow.hide();
		}
	</script>
	</head>
	<body>
        <div align="center" style="width:100%;" >
            <form name="searchjobfrm_1"  id="searchjobfrm_1" autocomplete="off">
                <table width="750" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
                    <thead>                	 
                        <th width="150">Company Name</th>
                        <th width="110">Return ID</th>
                        <th width="200">Date Range</th>
                        <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:100px;" /></th>             
                    </thead>
                    <tbody>
                        <tr class="general">
                            <td> <input type="hidden" id="return_id">  
								<?   
									echo create_drop_down( "cbo_company_name", 150, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name",1, "-- Select Company --", $ex_data[0],"",0 );
                           ?>
                            </td>
                            <td>
                                <input type="text" name="txt_search_common" id="txt_search_common" class="text_boxes" style="width:95px" />
                            </td>
                            <td align="center">
                                <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:80px">
                                <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:80px">
                            </td> 
                            <td align="center">
                                <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_search_common').value, 'return_id_search_list_view', 'search_div', 'sub_contract_material_return_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
                            </td>
                        </tr>
                        <tr>
                            <td colspan="4" align="center" valign="middle"><? echo load_month_buttons(1);  ?></td>
                        </tr>
                    </tbody>
                </table>  
                <div id="search_div"></div>  
            </form>
        </div>
	</body>           
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if ($action=="return_id_search_list_view")
{
	$data=explode('_',$data);
	if ($data[0]!=0) $company_name=" and company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
	if ($data[1]!="" &&  $data[2]!="") $return_date = "and subcon_date between '".change_date_format($data[1], "mm-dd-yyyy", "/",1)."' and '".change_date_format($data[2], "mm-dd-yyyy", "/",1)."'"; else $return_date="";
	if ($data[3]!="") $return_id_cond=" and prefix_no_num='$data[3]'"; else $return_id_cond="";
	$company_id=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$location=return_library_array( "select id,location_name from lib_location",'id','location_name');
	$return_to=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	$arr=array (2=>$company_id,3=>$location,4=>$return_to);
	if($db_type==0)
	{
		$sql= "select id, sys_no, prefix_no_num, YEAR(insert_date) as year, company_id,location_id,party_id,subcon_date,chalan_no,remarks,status_active from sub_material_mst where trans_type=3 and entry_form=344 and status_active=1 $company_name $return_date $return_id_cond order by id DESC";
	}
	else if($db_type==2)
	{
		$sql= "select id, sys_no, prefix_no_num, TO_CHAR(insert_date,'YYYY') as year, company_id,location_id,party_id,subcon_date,chalan_no,remarks,status_active from sub_material_mst where trans_type=3 and entry_form=344 and status_active=1 $company_name $return_date $return_id_cond order by id DESC";
	}
		 
	echo  create_list_view("list_view", "Return ID,Year,Company Name,Location,Return To,Return Date,Return Challan,Remarks", "70,80,100,100,100,70,80,100","750","250",0, $sql , "js_set_value", "id", "", 1, "0,0,company_id,location_id,party_id,0,0,0", $arr , "prefix_no_num,year,company_id,location_id,party_id,subcon_date,chalan_no,remarks", "sub_contract_material_return_controller","",'0,0,0,0,0,3,0,0') ;
	exit();
}

if ($action=="load_php_data_to_form_mst")
{
	$nameArray=sql_select( "select id, sys_no, company_id, location_id, party_id, subcon_date, chalan_no, forwarder, tran_company, remarks from sub_material_mst where id='$data'" ); 
	foreach ($nameArray as $row)
	{	
		echo "document.getElementById('txt_return_no').value 			= '".$row[csf("sys_no")]."';\n";
		echo "document.getElementById('cbo_company_name').value 		= '".$row[csf("company_id")]."';\n";
		echo "load_drop_down( 'requires/sub_contract_material_return_controller', document.getElementById('cbo_company_name').value, 'load_drop_down_location', 'location_td' );\n";
		echo "document.getElementById('cbo_location_name').value		= '".$row[csf("location_id")]."';\n"; 
		echo "load_drop_down( 'requires/sub_contract_material_return_controller', document.getElementById('cbo_company_name').value, 'load_drop_down_company_supplier', 'return_to_td' );\n"; 
		echo "document.getElementById('cbo_company_supplier').value		= '".$row[csf("party_id")]."';\n"; 
		echo "document.getElementById('txt_return_date').value 			= '".change_date_format($row[csf("subcon_date")])."';\n";   
		echo "document.getElementById('txt_return_challan').value		= '".$row[csf("chalan_no")]."';\n";
		echo "document.getElementById('cbo_forwarder').value 			= '".$row[csf("forwarder")]."';\n";   
		echo "document.getElementById('txt_transport_company').value		= '".$row[csf("tran_company")]."';\n"; 
		echo "document.getElementById('txt_remarks').value				= '".$row[csf("remarks")]."';\n"; 
	    echo "document.getElementById('update_id').value            	= '".$row[csf("id")]."';\n";
		echo "set_button_status(0, '".$_SESSION['page_permission']."', 'fnc_material_return',1,1);\n";
	}
	exit();
}

if ($action=="job_popup")
{
	echo load_html_head_contents("Popup Info","../../", 1, 1, $unicode,'','');
	?>
	<script>
		function js_set_value(id)
		{
			var spl_data=id.split('_');
			//alert (spl_data[1])
			document.getElementById('order_id').value=spl_data[0];
			document.getElementById('order_no').value=spl_data[1];
			parent.emailwindow.hide();
		}
		
		function set_caption(id)
		{
			if(id==1)  document.getElementById('search_by_td_up').innerHTML='Enter Order No';
			if(id==2)  document.getElementById('search_by_td_up').innerHTML='Enter Job No';
			if(id==3)  document.getElementById('search_by_td_up').innerHTML='Enter Issue No';
		}			
	</script>
	</head>
	<body>
        <div align="center" style="width:100%;" >
            <form name="searchjobfrm_1"  id="searchjobfrm_1" autocomplete="off">
                <table width="740" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center">
                    <thead>                	 
                        <th width="140">Company Name</th>
                        <th width="130">Party Name</th>
                        <th width="150">Date Range</th>
                        <th width="70">Search By</th>
                        <th width="90" id="search_by_td_up">Select Job/Order</th>
                        <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:70px;" /></th>             
                    </thead>
                    <tbody>
                        <tr class="general">
                            <td> <input type="hidden" id="order_id">  <input type="hidden" id="order_no">
								<?   
									$data=explode("_",$data);
									echo create_drop_down( "cbo_company_name", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name",1, "-- Select Company --", $data[0],"",1 );//load_drop_down( 'sub_contract_material_return_controller', this.value, 'load_drop_down_company_supplier','return_to_td' );
                                ?>
                            </td>
                            <td>
								<? 
									echo create_drop_down( "cbo_company_supplier", 130, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id  and b.tag_company='$data[0]' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Party --", $data[1], "",1 ); 
                                ?>
                            </td>
                            <td align="center">
                                <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px">
                                <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px">
                            </td>
                            <td >
							<?
								$sarch_by_arr=array(1=>'Order No', 2=>'Job No', 3=>'Issue No'); 
								echo create_drop_down( "cbo_search_by", 70, $sarch_by_arr, "", 1, "-- Select Search --", 0, "set_caption(this.value)");
                            ?>
                            </td>
                            <td align="center" id="search_by_td">
                                <input type="text" name="txt_search_common" id="txt_search_common" class="text_boxes" style="width:85px" autocomplete=off />
                            </td> 
                            <td align="center">
                                <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_company_supplier').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_search_common').value, 'job_search_list_view', 'search_div', 'sub_contract_material_return_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:70px;" />
                            </td>
                        </tr>
                        <tr>
                            <td colspan="6" align="center"  valign="middle">
								<? echo load_month_buttons(1);  ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div id="search_div"></div>    
            </form>
        </div>
	</body>           
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if ($action=="job_search_list_view")
{
	$data=explode('_',$data);
	$sarch_by=str_replace("'","",$data[4]);
	$txt_search_common=trim(str_replace("'","",$data[5]));
	if($sarch_by==1) 
		{
			if($txt_search_common!=''){
				$search=" and b.order_no='$txt_search_common'"; 	
			}
			else{
				$search = "";
			}
		}
	else if($sarch_by==2)
		{
			if($txt_search_common!='') {
				$search="and a.job_no_prefix_num='$txt_search_common'"; 
			}
			else $search = "";
		}
	else 
		{
			$search="";
		}
	if ($data[0]!=0) $company_con=" and a.company_id='$data[0]'"; else  $company_con="";
	if ($data[1]!=0) $buyer_con=" and a.party_id='$data[1]'"; else $buyer_con="";
	if ($data[2]!="" &&  $data[3]!="") $order_rcv_date = "and b.order_rcv_date between '".change_date_format($data[2], "mm-dd-yyyy", "/",1)."' and '".change_date_format($data[3], "mm-dd-yyyy", "/",1)."'"; else $order_rcv_date ="";
	
	$party_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');
	//$arr=array ();
	if($db_type==2) $year_col="TO_CHAR(a.insert_date,'YYYY') as year"; else if($db_type==0) $year_col="YEAR(a.insert_date) as year";
	/*if($db_type==0)
	{
		$sql="select a.subcon_job, a.job_no_prefix_num, YEAR(a.insert_date) as year, b.id, b.order_no, b.cust_style_ref, b.order_rcv_date, b.delivery_date, b.status_active from subcon_ord_mst a, subcon_ord_dtls b where a.subcon_job=b.job_no_mst and a.entry_form=238 and a.status_active=1 and b.status_active=1 $company_con $buyer_con $order_rcv_date $search order by a.id DESC";
	}
	else if($db_type==2)
	{
		$sql="select a.subcon_job, a.job_no_prefix_num, TO_CHAR(a.insert_date,'YYYY') as year, b.id, b.order_no, b.cust_style_ref, b.order_rcv_date, b.delivery_date, b.status_active from subcon_ord_mst a, subcon_ord_dtls b where a.subcon_job=b.job_no_mst and a.entry_form=238 and a.status_active=1 and b.status_active=1 $company_con $buyer_con $order_rcv_date $search order by a.id DESC";
	}*/

	/*if ($sarch_by==3) {
		$sql="select a.subcon_job, a.job_no_prefix_num, $year_col, b.id, b.order_no, b.cust_style_ref, b.order_rcv_date, b.delivery_date
		from subcon_ord_mst a, subcon_ord_dtls b, sub_material_mst c, sub_material_dtls d
		where a.subcon_job=b.job_no_mst and a.entry_form=238 and a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1 and c.id=d.mst_id and d.order_id=b.id and c.entry_form=343 $company_con $buyer_con $order_rcv_date $search and c.sys_no like '%$txt_search_common%'
		order by a.id DESC";
	} else {
		$sql="select a.subcon_job, a.job_no_prefix_num, $year_col, b.id, b.order_no, b.cust_style_ref, b.order_rcv_date, b.delivery_date
		from subcon_ord_mst a, subcon_ord_dtls b
		where a.subcon_job=b.job_no_mst and a.entry_form=238 and a.status_active=1 and b.status_active=1 $company_con $buyer_con $order_rcv_date $search
		order by a.id DESC";
	}*/

	if ($sarch_by==1) {
		$sql="select a.subcon_job, a.job_no_prefix_num, $year_col, b.id, b.order_no, b.cust_style_ref, b.order_rcv_date, b.delivery_date
		from subcon_ord_mst a, subcon_ord_dtls b
		where a.subcon_job=b.job_no_mst and a.entry_form=238 and a.status_active=1 and b.status_active=1 $company_con $buyer_con $order_rcv_date $search
		order by a.id DESC";		
	} else if ($sarch_by==3) {
		$sql="select a.subcon_job, a.job_no_prefix_num, $year_col, b.id, b.order_no, b.cust_style_ref, b.order_rcv_date, b.delivery_date
		from subcon_ord_mst a, subcon_ord_dtls b, sub_material_mst c, sub_material_dtls d
		where a.subcon_job=b.job_no_mst and a.entry_form=238 and a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1 and c.id=d.mst_id and d.order_id=b.id and c.entry_form=343 $company_con $buyer_con $order_rcv_date $search and c.sys_no like '%$txt_search_common%'
		order by a.id DESC";
	} else if ($sarch_by==2){
		$sql="select a.subcon_job, a.job_no_prefix_num, $year_col, b.id, b.order_no, b.cust_style_ref, b.order_rcv_date, b.delivery_date
		from subcon_ord_mst a, subcon_ord_dtls b, sub_material_mst c, sub_material_dtls d
		where a.subcon_job=b.job_no_mst and a.entry_form=238 and a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1 and c.id=d.mst_id and d.order_id=b.id and c.entry_form=343 $company_con $buyer_con $order_rcv_date $search 
		order by a.id DESC";
	}
	else{
		$sql="select a.subcon_job, a.job_no_prefix_num, $year_col, b.id, b.order_no, b.cust_style_ref, b.order_rcv_date, b.delivery_date
		from subcon_ord_mst a, subcon_ord_dtls b, sub_material_mst c, sub_material_dtls d
		where a.subcon_job=b.job_no_mst and a.entry_form=238 and a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1 and c.id=d.mst_id and d.order_id=b.id and c.entry_form=343 $company_con $buyer_con $order_rcv_date $search and c.sys_no like '%$txt_search_common%'
		order by a.id DESC";
	}


	
	// echo $sql;die;

	echo create_list_view("list_view", "Job No,Year,Order No,Style,Order Date,Delivery Date", "70,80,100,100,100,100","650","250",0, $sql , "js_set_value", "id,order_no", "", 1, "0,0,0,0,0,0", $arr , "job_no_prefix_num,year,order_no,cust_style_ref,order_rcv_date,delivery_date","", "",'0,0,0,0,3,3') ;
	exit();
}

if ($action=="material_description_return_popup")
{
	echo load_html_head_contents("Material Description Form", "../../", 1, 1,'',1,'');
	extract($_REQUEST);
	$ex_data=explode('_',$data);

	$color_arr=return_library_array( "select id, color_name from lib_color where status_active=1 and is_deleted=0",'id','color_name');
	$size_arr=return_library_array( "select id,size_name from  lib_size where status_active=1 and is_deleted=0",'id','size_name');

	?>
    <script>
		  function js_set_value(id,val)
		  {
			  //alert (val)
		  	  $("#description_id").val(id);
			  $("#material_description").val(val);
			  parent.emailwindow.hide();
		  }
		  
		  $(document).ready(function(e) {
		setFilterGrid('tbl_list_search',-1,'');
	});
	</script>
    </head>
    <body>
            <input type="hidden" name="description_id" id="description_id">
        	<input type="hidden" name="material_description" id="material_description">

    <div style="width:970px;">
     	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="100%" class="rpt_table">
            <thead>
                <tr>
                    <th width="30">SL</th>
                    <th width="70">Rec. Chl.</th>
					<th width="80">Lot</th>
					<th width="80">Brand</th>
                    <th width="180">Material Description</th>
                    <th width="100">Color</th>
                    <th width="50">UOM</th>
                    <th width="50">G.Dia</th>
                    <th width="60">(Rec.+Issue Ret) Qty</th>
                    <th width="60">Roll/Bag</th>
                    <th width="50">Cone</th>
                    <th width="60">Iss. Qty</th>
                    <th>Balance</th> <? //echo $ex_data[2];?>
                </tr>
            </thead>
     	</table>
     </div>
     <div style="width:990px; max-height:300px;overflow-y:scroll;" >	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="970" class="rpt_table" id="tbl_list_search">
			<?
			if($ex_data[3]!=0){ $category_id=" and a.item_category_id=$ex_data[3]";}else{echo "Please Select item category First."; die; }
			if($db_type==0)
			{
				$group_cond=" group by b.chalan_no, a.material_description, a.subcon_uom, a.grey_dia";
				$id="a.id as id";
			}
			else if($db_type==2)
			{
				$group_cond=" group by b.chalan_no, a.material_description, a.subcon_uom, a.grey_dia";
				$id="listagg(cast(a.id as varchar2(4000)),',') within group (order by a.id) as id";
			}
			$issue_balance_array=array();
			 $sql_issue="select $id, b.entry_form,a.rec_challan, a.material_description,a.lot_no, a.gsm, a.fin_dia, a.color_id, a.size_id, a.subcon_uom, sum(a.subcon_roll) as subcon_roll, sum(a.quantity) as quantity, sum(a.rec_cone) as rec_cone, a.grey_dia from sub_material_dtls a, sub_material_mst b where b.id=a.mst_id and a.material_description<>' ' and b.trans_type in (2,3) and b.entry_form in (343,344) and b.status_active=1 and b.is_deleted=0 and a.order_id='$ex_data[2]' $category_id group by b.entry_form,a.lot_no,a.rec_challan, a.material_description, a.gsm, a.fin_dia, a.color_id, a.subcon_uom, a.grey_dia, a.size_id";
			$sql_issue_result=sql_select($sql_issue);
			foreach( $sql_issue_result as $row )
			{
				$entry_formId=$row[csf("entry_form")];
				if($entry_formId==343)
				{
				$issue_balance_array[$row[csf("rec_challan")]][$row[csf("material_description")]][$row[csf("gsm")]][$row[csf("fin_dia")]][$row[csf("color_id")]][$row[csf("subcon_uom")]][$row[csf("grey_dia")]][$row[csf("lot_no")]]=$row[csf("quantity")];
				}
				else
				{
					$issue_ret_balance_array[$row[csf("rec_challan")]][$row[csf("material_description")]][$row[csf("gsm")]][$row[csf("fin_dia")]][$row[csf("color_id")]][$row[csf("subcon_uom")]][$row[csf("grey_dia")]][$row[csf("lot_no")]]=$row[csf("quantity")];
				}
			}
			
			$sql="select $id, b.chalan_no, a.material_description, a.gsm, a.fin_dia, a.color_id, a.size_id, a.subcon_uom, sum(a.subcon_roll) as subcon_roll, sum(a.quantity) as quantity, sum(a.rec_cone) as rec_cone, a.grey_dia,a.lot_no,a.brand from sub_material_dtls a, sub_material_mst b where b.id=a.mst_id and a.material_description<>' ' and b.trans_type=1  and b.entry_form=288 and a.status_active=2 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.order_id='$ex_data[2]' $category_id group by b.chalan_no, a.material_description, a.gsm, a.fin_dia, a.color_id, a.subcon_uom, a.grey_dia, a.size_id,a.lot_no,a.brand";
			$i=1;
			$nameArray=sql_select($sql);
            foreach( $nameArray as $row )
            {
                if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				
				$issue_ret=$issue_ret_balance_array[$row[csf("chalan_no")]][$row[csf("material_description")]][$row[csf("gsm")]][$row[csf("fin_dia")]][$row[csf("color_id")]][$row[csf("subcon_uom")]][$row[csf("grey_dia")]][$row[csf("lot_no")]];
				$issueQty=$issue_balance_array[$row[csf("chalan_no")]][$row[csf("material_description")]][$row[csf("gsm")]][$row[csf("fin_dia")]][$row[csf("color_id")]][$row[csf("subcon_uom")]][$row[csf("grey_dia")]][$row[csf("lot_no")]];
				?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none;cursor:pointer" onClick="js_set_value('<? echo $row[csf("id")]; ?>','<? echo $row[csf("material_description")].'__'.$row[csf("grey_dia")].'__'.$row[csf("chalan_no")].'__'.$row[csf("gsm")].'__'.$row[csf("fin_dia")].'__'.$row[csf("color_id")].'__'.$color_arr[$row[csf("color_id")]].'__'.$row[csf("size_id")].'__'.$size_arr[$row[csf("size_id")]].'__'.$row[csf("lot_no")].'__'.$row[csf("brand")]; ?>');" > 
						<td width="30" align="center"><? echo $i; ?></td>
                        <td width="70"><? echo $row[csf("chalan_no")]; ?></td>
						<td width="80"><? echo $row[csf("lot_no")]; ?></td>
						<td width="80"><? echo $row[csf("brand")]; ?></td>
						<td width="180"><? echo $row[csf("material_description")]; ?></td>		
						<td width="100"><? echo $color_arr[$row[csf("color_id")]]; ?></td>		
						<td width="50"><? echo $unit_of_measurement[$row[csf("subcon_uom")]]; ?></td>
                        <td width="50"><? echo $row[csf("grey_dia")]; ?></td>
                        <td width="60" align="right" title="Recv With Issue Ret"><? echo $row[csf("quantity")]+$issue_ret; ?></td>	
                        <td width="60" align="right"><? echo $row[csf("subcon_roll")]; ?></td>	
                        <td width="50" align="right"><? echo $row[csf("rec_cone")]; ?></td>
                        <td width="60" align="right"><? echo $issue_balance_array[$row[csf("chalan_no")]][$row[csf("material_description")]][$row[csf("gsm")]][$row[csf("fin_dia")]][$row[csf("color_id")]][$row[csf("subcon_uom")]][$row[csf("grey_dia")]][$row[csf("lot_no")]]; ?></td>	
                        <td align="right"  title="Recv With Issue Ret:(<? echo $row[csf("quantity")]+$issue_ret.'-IssueQty:'.$issueQty; ?>)"><? echo ($row[csf("quantity")]+$issue_ret)-$issue_balance_array[$row[csf("chalan_no")]][$row[csf("material_description")]][$row[csf("gsm")]][$row[csf("fin_dia")]][$row[csf("color_id")]][$row[csf("subcon_uom")]][$row[csf("grey_dia")]][$row[csf("lot_no")]]; ?></td>	
					</tr>
				<? 
				$i++;
            }
   		?>
			</table>
        </div>
    </body>           
    <script src="../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
  exit();
}

if ($action=="material_return_list_view")
{
	$order_arr=return_library_array( "select id, order_no from subcon_ord_dtls",'id','order_no');
	$sql = "select id, item_category_id,lot_no,brand, material_description, quantity, subcon_uom, subcon_roll, grey_dia, status_active, order_id from sub_material_dtls where mst_id='$data' and status_active=1 and is_deleted=0"; 
	$arr=array(0=>$order_arr,3=>$item_category,6=>$unit_of_measurement,9=>$row_status);
	echo  create_list_view("list_view", "Order No,Lot,Brand,Item Category,Material Description,Return Qty,UOM,Roll,Dia,Status", "100,80,80,100,150,80,50,60,60,80","890","250",0, $sql, "get_php_form_data", "id", "'load_php_data_to_form_dtls'", 1, "order_id,0,0,item_category_id,0,0,subcon_uom,0,0,status_active",$arr,"order_id,lot_no,brand,item_category_id,material_description,quantity,subcon_uom,subcon_roll,grey_dia,status_active", "requires/sub_contract_material_return_controller","","0,0,0,0,0,0,0,0,0,0");
	exit();
}

if ($action=="load_php_data_to_form_dtls")
{

	$color_arr=return_library_array( "select id, color_name from lib_color where status_active=1 and is_deleted=0",'id','color_name');
	$size_arr=return_library_array( "select id,size_name from  lib_size where status_active=1 and is_deleted=0",'id','size_name');

	$nameArray=sql_select( "select a.id, a.mst_id, a.item_category_id, a.rec_challan, a.material_description, a.gsm, a.fin_dia, a.color_id, a.size_id, a.quantity, a.subcon_uom, a.subcon_roll, a.rec_cone, a.grey_dia, a.status_active,a.lot_no,a.brand, b.id as order_tbl_id, b.order_no from sub_material_dtls a,subcon_ord_dtls b where a.order_id=b.id and a.id='$data'" );
	foreach ($nameArray as $row)
	{	
		echo "document.getElementById('txt_orderno').value		 		= '".$row[csf("order_no")]."';\n";
		echo "document.getElementById('order_no_id').value		 		= '".$row[csf("order_tbl_id")]."';\n"; 
		echo "document.getElementById('cbo_itemcategory').value			= '".$row[csf("item_category_id")]."';\n";
		echo "document.getElementById('txt_rec_challan_no').value 		= '".$row[csf("rec_challan")]."';\n";   
		echo "document.getElementById('txt_description').value			= '".$row[csf("material_description")]."';\n";  
		echo "document.getElementById('txt_quantity').value				= '".$row[csf("quantity")]."';\n";  
		echo "document.getElementById('cbo_uom').value		 			= '".$row[csf("subcon_uom")]."';\n";
		echo "document.getElementById('txt_roll').value		 			= '".$row[csf("subcon_roll")]."';\n";
		echo "document.getElementById('txt_dia').value		 			= '".$row[csf("grey_dia")]."';\n";
		echo "document.getElementById('txt_gsm').value		 			= '".$row[csf("gsm")]."';\n";
		echo "document.getElementById('txt_fin_dia').value		 		= '".$row[csf("fin_dia")]."';\n";
		echo "document.getElementById('txt_color_id').value		 		= '".$row[csf("color_id")]."';\n";
		echo "document.getElementById('txt_color_show').value		 	= '".$color_arr[$row[csf("color_id")]]."';\n";

		echo "document.getElementById('txt_size_id').value		 		= '".$row[csf("size_id")]."';\n";
		echo "document.getElementById('txt_size_show').value		 	= '".$size_arr[$row[csf("size_id")]]."';\n";
		echo "document.getElementById('txt_lot_no').value		 		= '".$row[csf("lot_no")]."';\n";
		echo "document.getElementById('txt_brand').value		 		= '".$row[csf("brand")]."';\n";
		echo "document.getElementById('txt_cone').value		 			= '".$row[csf("rec_cone")]."';\n";
		echo "document.getElementById('update_id').value            	= '".$row[csf("mst_id")]."';\n";
		echo "document.getElementById('updateid_1').value            	= '".$row[csf("id")]."';\n";
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_material_return',1,1);\n";
		echo "change_uom('".$row[csf("item_category_id")]."');\n";
	}
	exit();	
}

/*if ($action=="load_php_data_for_dtls")
{
	$ex_data=explode('_',$data);
	$sql_rec="Select a.quantity, a.subcon_roll, a.rec_cone, a.grey_dia from sub_material_dtls a, sub_material_mst b where b.id=a.mst_id and a.order_id=$ex_data[0] and a.id=$ex_data[1] and a.status_active=1 and b.trans_type=1 and a.is_deleted=0";
	$sql_result_rec = sql_select($sql_rec);
	$sql_iss="Select sum(a.quantity) as quantity, sum (a.subcon_roll) as subcon_roll, sum (a.rec_cone) as rec_cone from sub_material_dtls a, sub_material_mst b where b.id=a.mst_id and a.order_id=$ex_data[0] and b.trans_type=3 and a.status_active=1 and a.is_deleted=0";
	$sql_result_iss = sql_select($sql_iss);
	$tot_roll=0;
	$tot_dia=$sql_result_rec[0][csf('grey_dia')]*1;//-($sql_result_iss[0][csf('grey_dia')]*1)
	$tot_qty=$sql_result_rec[0][csf('quantity')]*1-($sql_result_iss[0][csf('quantity')]*1);
	$tot_roll=$sql_result_rec[0][csf('subcon_roll')]*1-($sql_result_iss[0][csf('subcon_roll')]*1);
	$tot_cone=$sql_result_rec[0][csf('rec_cone')]*1-($sql_result_iss[0][csf('rec_cone')]*1);
	echo "$('#txt_dia').val('".$tot_dia."');\n";
	echo "$('#txt_quantity').attr('placeholder','".$tot_qty."');\n";
	echo "$('#txt_roll').attr('placeholder','".$tot_roll."');\n";
	echo "$('#txt_cone').attr('placeholder','".$tot_cone."');\n";
	exit();
}*/

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$trans_Type="3";
	if ($operation==0 || $operation==1)   // ======================Validation Check=====================
	{
		$issue_ret_cond="";
		if($operation==1)
		{
			$issue_ret_cond=" and b.id!=$updateid_1 ";
		}
	$item_categoryId=str_replace("'","",$cbo_itemcategory);
		
	 $gsm_cond = ( str_replace("'","", $txt_gsm)!="")?" and b.gsm=$txt_gsm" : "";
	 $lot_cond = ( str_replace("'","", $txt_lot_no)!="")?" and b.lot_no=$txt_lot_no" : "";
	 $material_description_cond = ( str_replace("'","", $txt_description)!="")?" and b.material_description=$txt_description" : "";
	 $color_cond = ( str_replace("'","", $txt_color_id)!="")?" and b.color_id=$txt_color_id" : "";
	 $size_id_cond = ( str_replace("'","", $txt_size_id)!="")?" and b.size_id=$txt_size_id" : "";
	 $fin_dia_cond = ( str_replace("'","", $txt_fin_dia)!="")?" and b.fin_dia=$txt_fin_dia" : "";
	 if($item_categoryId) $item_cat_cond="and b.item_category_id=$item_categoryId";else  $item_cat_cond="";
	 
	 $prevIssue_qnty=return_field_value("sum(b.quantity) as prevIssue_qnty","sub_material_dtls b, sub_material_mst a","a.id=b.mst_id and b.order_id=$order_no_id   $gsm_cond  $material_description_cond  $color_cond  $size_id_cond  $fin_dia_cond $item_cat_cond $lot_cond  and a.entry_form=343 and a.trans_type=2 and a.status_active=1  and b.status_active=1","prevIssue_qnty");
	 
	 
	$prevIssue_Ret_qnty=return_field_value("sum(b.quantity) as prevIssueRet_qnty","sub_material_dtls b, sub_material_mst a","a.id=b.mst_id and b.order_id=$order_no_id   $gsm_cond  $material_description_cond  $color_cond  $size_id_cond  $fin_dia_cond $item_cat_cond $lot_cond  and a.entry_form=344 and a.trans_type=$trans_Type and a.status_active=1  and b.status_active=1 $issue_ret_cond ","prevIssueRet_qnty");
		//echo "10**".$recv_qnty."**".$prevIssue_qnty; die;
	//	echo "17**Issue Return Quantity Exceeds Issue Quantity=".$txt_quantity.'+Issue Ret='.$prevIssue_Ret_qnty.',Issue='.$prevIssue_qnty;
		//die;

		if((str_replace("'","",$txt_quantity)+$prevIssue_Ret_qnty)>$prevIssue_qnty)
		{
			echo "17**Issue Return Quantity Exceeds Issue Quantity=".$txt_quantity.'+Issue Ret='.$prevIssue_Ret_qnty.',Issue='.$prevIssue_qnty;
			//check_table_status( $_SESSION['menu_id'],0);
			disconnect($con);
			die;
		}
		
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
		$new_return_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', 'RTN', date("Y",time()), 5, "select id, prefix_no, prefix_no_num from sub_material_mst where company_id=$cbo_company_name and entry_form=344 and trans_type=$trans_Type $year_cond=".date('Y',time())." order by id desc ", "prefix_no", "prefix_no_num" ));
		if(str_replace("'",'',$update_id)=="")
		{	
			$id=return_next_id("id", "sub_material_mst",1);// die;
			$field_array="id, prefix_no, prefix_no_num, sys_no, trans_type, company_id, location_id, party_id, subcon_date, chalan_no, forwarder, tran_company, remarks, entry_form, inserted_by, insert_date";
			$data_array="(".$id.",'".$new_return_no[1]."','".$new_return_no[2]."','".$new_return_no[0]."','".$trans_Type."',".$cbo_company_name.",".$cbo_location_name.",".$cbo_company_supplier.",".$txt_return_date.",".$txt_return_challan.",".$cbo_forwarder.",".$txt_transport_company.",".$txt_remarks.",344,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			//echo "INSERT INTO sub_material_mst (".$field_array.") VALUES ".$data_array;  die;  
			//$rID=sql_insert("sub_material_mst",$field_array,$data_array,1);
			//$return_no=$new_return_no[0];
		}
		else
		{
			$id=str_replace("'",'',$update_id);
			$field_array="location_id*party_id*subcon_date*chalan_no*forwarder*tran_company*remarks*updated_by*update_date";
			$data_array="".$cbo_location_name."*".$cbo_company_supplier."*".$txt_return_date."*".$txt_return_challan."*".$cbo_forwarder."*".$txt_transport_company."*".$txt_remarks."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"; 
		}
		$id1=return_next_id("id","sub_material_dtls", 1); 
		$field_array2="id, mst_id, order_id, item_category_id, rec_challan, material_description, quantity, subcon_uom, subcon_roll, rec_cone, grey_dia, gsm, fin_dia, color_id, size_id,lot_no,brand, inserted_by, insert_date";
		$data_array2="(".$id1.",".$id.",".$order_no_id.",".$cbo_itemcategory.",".$txt_rec_challan_no.",".$txt_description.",".$txt_quantity.",".$cbo_uom.",".$txt_roll.",".$txt_cone.",".$txt_dia.",".$txt_gsm.",".$txt_fin_dia.",".$txt_color_id.",".$txt_size_id.",".$txt_lot_no.",".$txt_brand.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')"; 
		
		if(str_replace("'",'',$update_id)=="")
		{
			//echo "INSERT INTO sub_material_mst(".$field_array.") VALUES ".$data_array; 
			$rID=sql_insert("sub_material_mst",$field_array,$data_array,0);
			$return_no=$new_return_no[0];
		}
		else
		{
			$rID=sql_update("sub_material_mst",$field_array,$data_array,"id",$update_id,0); 
			$return_no=str_replace("'",'',$txt_return_no);
		}
		//echo "INSERT INTO sub_material_dtls(".$field_array2.") VALUES ".$data_array2; die;
		$rID2=sql_insert("sub_material_dtls",$field_array2,$data_array2,1);
		//echo $rID."===".$rID2; die;
		if($db_type==0)
		{
			if($rID && $rID2)
			{
				mysql_query("COMMIT");  
				echo "0**".str_replace("'",'',$id)."**".str_replace("'",'',$id1)."**".str_replace("'",'',$return_no);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'",'',$id)."**".str_replace("'",'',$id1)."**".str_replace("'",'',$return_no);
			}
		}
		if($db_type==2)
		{
			if($rID && $rID2)
			{
				oci_commit($con);
				echo "0**".str_replace("'",'',$id)."**".str_replace("'",'',$id1)."**".str_replace("'",'',$return_no);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'",'',$id)."**".str_replace("'",'',$id1)."**".str_replace("'",'',$return_no);
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
		//echo $txt_return_no;die;
		$field_array="location_id*party_id*subcon_date*chalan_no*forwarder*tran_company*remarks*updated_by*update_date";
		$data_array="".$cbo_location_name."*".$cbo_company_supplier."*".$txt_return_date."*".$txt_return_challan."*".$cbo_forwarder."*".$txt_transport_company."*".$txt_remarks."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"; 
		
		$field_array2="order_id*item_category_id*rec_challan*material_description*quantity*subcon_uom*subcon_roll*rec_cone*grey_dia*gsm*fin_dia*color_id*size_id*lot_no*brand*updated_by*update_date";
		$data_array2="".$order_no_id."*".$cbo_itemcategory."*".$txt_rec_challan_no."*".$txt_description."*".$txt_quantity."*".$cbo_uom."*".$txt_roll."*".$txt_cone."*".$txt_dia."*".$txt_gsm."*".$txt_fin_dia."*".$txt_color_id."*".$txt_size_id."*".$txt_lot_no."*".$txt_brand."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"; 
			//echo $data_array; die;	
		$rID=sql_update("sub_material_mst",$field_array,$data_array,"id",$update_id,0); 
			
		$rID2=sql_update("sub_material_dtls",$field_array2,$data_array2,"id",$updateid_1,1); //  die;
		
		if($db_type==0)
		{
			if($rID && $rID2)
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'",'',$update_id)."**".str_replace("'",'',$updateid_1)."**".str_replace("'",'',$txt_return_no);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**";
			}
		}
		if($db_type==2)
		{
			if($rID && $rID2)
			{
				oci_commit($con);
				echo "1**".str_replace("'",'',$update_id)."**".str_replace("'",'',$updateid_1)."**".str_replace("'",'',$txt_return_no);
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
	else if ($operation==2)   // Delete Here ============================================================
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$sql=sql_select("select id from sub_material_dtls where mst_id=$update_id and status_active=1 and is_deleted=0");
		
		$field_array="updated_by*update_date*status_active*is_deleted";
		$data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'0'*'1'";
		
		$flag=1;
		$dtlsRow=count($sql);

		if($dtlsRow==1)
		{
			$rID=sql_update("sub_material_mst",$field_array,$data_array,"id","".$update_id."",1);
			if($rID==1) $flag=1; else $flag=0;
		}
		
		$rID1=sql_update("sub_material_dtls",$field_array,$data_array,"id","".$updateid_1."",1);
		if($rID1==1 && $flag==1) $flag=1; else $flag=0;
			
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "2**".str_replace("'",'',$update_id)."**".str_replace("'",'',$updateid_1)."**".str_replace("'",'',$txt_return_no)."**".$dtlsRow;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'",'',$update_id)."**".str_replace("'",'',$updateid_1)."**".str_replace("'",'',$txt_return_no);
			}
		}
		else if($db_type==2)
		{
			if($flag==1)
			{
				oci_commit($con);
				echo "2**".str_replace("'",'',$update_id)."**".str_replace("'",'',$updateid_1)."**".str_replace("'",'',$txt_return_no)."**".$dtlsRow;
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'",'',$update_id)."**".str_replace("'",'',$updateid_1)."**".str_replace("'",'',$txt_return_no);
			}
		}	
		disconnect($con);
		die;
	}
}

if($action=="inventory_return_print")
{
	extract($_REQUEST);
	$ex_data=explode('*',$data);
	//$company=$ex_data[0];
	$update_id=$ex_data[1];
	$sys_id=$ex_data[2];
	$company=$ex_data[0];
	$location=$ex_data[5];
	// echo "<pre>";
	// print_r ($ex_data);
	$imge_arr=return_library_array("select master_tble_id,image_location from common_photo_library",'master_tble_id','image_location');
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$supplier_library=return_library_array( "select id,supplier_name from  lib_supplier", "id","supplier_name"  );
	$buyer_library=return_library_array( "select id, buyer_name from   lib_buyer", "id", "buyer_name"  );
	$country_arr=return_library_array( "select id, country_name from  lib_country", "id", "country_name"  );
	$color_arr=return_library_array( "select id, color_name from  lib_color where status_active=1 and is_deleted=0", "id", "color_name"  );//die;
	//$recChallan_arr=return_library_array( "select id, chalan_no from  sub_material_mst where trans_type=1", "id", "chalan_no"  );
	
	$sql_job_po="select a.id, a.order_no, a.main_process_id, a.cust_buyer, a.cust_style_ref, b.party_id, b.subcon_job, a.process_id from  subcon_ord_dtls a, subcon_ord_mst b where a.job_no_mst=b.subcon_job and b.is_deleted=0 and b.status_active=1 and a.status_active=1 and a.is_deleted=0";
	$job_po_array=array();
	$result_job_po=sql_select($sql_job_po);
	foreach($result_job_po as $row)
	{
		$job_po_array[$row[csf('id')]]['order_no']=$row[csf('order_no')];
		$job_po_array[$row[csf('id')]]['style']=$row[csf('cust_style_ref')];
		$job_po_array[$row[csf('id')]]['buyer']=$row[csf('cust_buyer')];
	}
	
	$sql="select sys_no, party_id, subcon_date, chalan_no, forwarder, tran_company, remarks from  sub_material_mst where id='$update_id' and status_active=1 and status_active=1";
	
	$dataArray=sql_select($sql);
	$com_dtls = fnc_company_location_address($company, $location, 2);
?>
    <div style="width:930px;">
    <table width="100%" cellpadding="0" cellspacing="0" >
        <tr>
            <td width="70" align="right">
            	<img src='../../<? echo $com_dtls[2]; ?>' height='100%' width='100%' />
            </td>
            <td>
                <table width="800" cellspacing="0" align="center">
                    <tr>
                        <td colspan="6" align="center" style="font-size:22px"><strong><? echo $com_dtls[0]; ?></strong></td>
                    </tr>
                    <tr class="form_caption">
                        <td  align="center" style="font-size:14px">  
						<?

							echo $com_dtls[1];
                           /* $nameArray=sql_select( "select plot_no, level_no, road_no, block_no, contact_no, country_id, province, city, zip_code, contact_no, email, website, vat_number from lib_company where id=$company and status_active=1 and is_deleted=0"); 
                            foreach ($nameArray as $result)
                            { 
                            ?>
                                <? echo $result[csf('plot_no')]; ?> &nbsp; 
                                <? echo $result[csf('level_no')]?> &nbsp; 
                                <? echo $result[csf('road_no')]; ?> &nbsp; 
                                <? echo $result[csf('block_no')];?> &nbsp; 
                                <? echo $result[csf('city')];?> &nbsp; 
                                <? echo $result[csf('zip_code')]; ?> &nbsp; 
                                <? echo $result[csf('province')];?> &nbsp; 
                                <? echo $country_arr[$result[csf('country_id')]]; ?>&nbsp; <br>
                                <? echo $result[csf('contact_no')];?> &nbsp; 
                                <? echo $result[csf('email')];?> &nbsp; 
                                <? echo $result[csf('website')]; ?> <br>
                                <b> Vat No : <? echo $result[csf('vat_number')]; ?></b> <?
                            }*/
                        ?> 
                    </td>  
                    </tr>
                    <tr>
                		<td align="center" style="font-size:18px"><strong>Material Return Challan</strong></td>
          			</tr>
                </table>
            </td>
        </tr>
    </table> 
    <table width="900" cellspacing="0" align="" border="0">   
    	  <tr><td colspan="6" align="center"><hr></hr></td></tr>
          <tr>
                <? 
                    $party_add=$dataArray[0][csf('party_id')];
                    $nameArray=sql_select( "select address_1, web_site, buyer_email, country_id from  lib_buyer where id=$party_add"); 
                    foreach ($nameArray as $result)
                    { 
                        $address="";
                        if($result!="") $address=$result[csf('address_1')];//.'<br>'.$country_arr[$result['country_id']].'<br>'.$result['email'].'<br>'.$result['web_site']
                    }
                ?> 
                <td width="300" rowspan="2" valign="top" colspan="2" style="font-size:18px"><strong>Party : <? echo $buyer_library[$party_add].'<br>'.$address;  ?></strong></td>
                
                <td width="125" style="font-size:14px"><strong>Challan No :</strong></td><td width="170px"><? echo $dataArray[0][csf('chalan_no')]; ?></td>
                <td width="125" style="font-size:14px"><strong>Delivery Date :</strong></td><td width="170px"><? echo change_date_format($dataArray[0][csf('subcon_date')]); ?></td>
            </tr>
            <tr>
                <td style="font-size:14px"><strong>Transport Com.:</strong></td><td><? echo $dataArray[0][csf('tran_company')]; ?></td>
                <td style="font-size:14px"><strong>Forwarder:</strong></td><td><? echo $supplier_library[$dataArray[0][csf('forwarder')]]; ?></td>
            </tr>
            <tr>
                <td style="font-size:14px" colspan="2"><strong>Return No: </strong> <? echo $dataArray[0][csf('sys_no')]; ?></td>
                <td style="font-size:14px" colspan="4"><strong>Remarks:</strong>&nbsp;&nbsp;&nbsp;<? echo $dataArray[0][csf('remarks')]; ?></td>
            </tr>
        </table>
         <br>
       <div style="width:100%; max-height:1200px">
        <table cellspacing="0" width="1050" border="1" rules="all" class="rpt_table" >
            <thead style="font-size:14px">
                <th width="30">SL</th>
                <th width="200">Description</th>
                <th width="100">Stitch Length</th>
                <th width="100">Used Yarn Details</th>
                <th width="100">Color</th>
				<th width="75">Lot</th>
				<th width="75">Brand</th>
                <th width="130">Order</th>
                <th width="100">Rec. Challan</th>
                <th width="100">Cust. Style</th>
                <th width="100">Cust. Buyer</th>
                <th width="100">Finish Dia</th>
                <th width="60">Dia</th>
                <th width="60">Roll/Bag</th>
                <th >Return Qty</th>
            </thead>
            <?
            
			  $sql_recv_arr=sql_select(" select b.order_id,b.material_description,b.stitch_length ,b.used_yarn_details from sub_material_dtls b,sub_material_mst a where a.id=b.mst_id and b.status_active=2 and  a.is_deleted=0 and a.trans_type=1 and a.entry_form=288");
			  foreach($sql_recv_arr as $row)
              {
				  $recv_data_arr[$row[csf('order_id')]][$row[csf('material_description')]]['stitch_length']=$row[csf('stitch_length')];
				  $recv_data_arr[$row[csf('order_id')]][$row[csf('material_description')]]['used_yarn_details']=$row[csf('used_yarn_details')];
			  }
			
                $mst_id=$dataArray[0][csf('id')];
                $sql_dtls="select order_id, rec_challan, material_description, grey_dia, subcon_roll, quantity,gsm,color_id,fin_dia,lot_no,brand from sub_material_dtls where mst_id='$update_id'";
                
                $i=1;
                
                $dtls_value=sql_select($sql_dtls);
                foreach($dtls_value as $row)
                {
                    if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="font-size:14px">
						<td align="center"><? echo $i; ?></td>
						<td ><p><? echo $row[csf('material_description')] .' '. $row[csf('gsm')]; ?></p></td>
                        <td ><p><? echo $recv_data_arr[$row[csf('order_id')]][$row[csf('material_description')]]['stitch_length']; ?></p></td>
                        <td ><p><? echo $recv_data_arr[$row[csf('order_id')]][$row[csf('material_description')]]['used_yarn_details']; ?></p></td>
                        <td ><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
						<td ><p><? echo $row[csf('lot_no')] ?>&nbsp;</p></td>
						<td ><p><? echo $row[csf('brand')] ?>&nbsp;</p></td>
						<td ><p><? echo $job_po_array[$row[csf('order_id')]]['order_no']; ?></p></td>
						<td ><p><? echo $row[csf('rec_challan')] ?>&nbsp;</p></td>
						<td ><p><? echo $job_po_array[$row[csf('order_id')]]['style']; ?>&nbsp;</p></td>
						<td ><p><? echo $job_po_array[$row[csf('order_id')]]['buyer']; ?>&nbsp;</p></td>
                        <td ><p><? echo $row[csf('fin_dia')]; ?></p></td>
						<td align="center"><p><? echo $row[csf('grey_dia')]; ?>&nbsp;</p></td>
                        <td align="right"><p><? echo $row[csf('subcon_roll')]; ?>&nbsp;</p></td>
						<td align="right"><? echo number_format($row[csf('quantity')],2); ?>&nbsp;</td>
					</tr>
					<?
					$tot_roll+=$row[csf('subcon_roll')];
					$tot_qty+=$row[csf('quantity')];
					$i++;
                }
            ?>
                <tfoot style="font-size:12px">
                    <th colspan="11" align="right"><strong>Total</strong></th>
                    <th align="right"><? echo $tot_roll; ?>&nbsp;</th>
                    <th align="right"><? echo number_format($tot_qty,2); ?>&nbsp;</th>
                </tfoot>
            </table>
            </div> 
             <br>
			<?
			echo signature_table(51, $ex_data[0], "900px");
			if( $ex_data[4]==1)
			{
				?>
				<table width="900" cellspacing="0" >
                    <tr>
                    	<td colspan="6"></td>
                    </tr>
                    <tr>
                        <td colspan="6" align="center">,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,
                        </td>
                    </tr>
                    <tr>
                        <td colspan="6"> 
                            <table cellpadding="0" cellspacing="0" >
                                <tr>
                                    <td width="70" align="right"> 
                                    <img  src='../../<? echo $imge_arr[str_replace("'","",$company)]; ?>' height='100%' width='100%' />
                                    </td>
                                    <td align="right">
                                        <table width="800px" cellspacing="0" align="center">
                                            <tr>
                                            	<td align="center" style="font-size:18px"><strong ><? echo $company_library[$company]; ?></strong></td>
                                            </tr>
                                            <tr class="form_caption">
                                                <td  align="center" style="font-size:14px">  
                                                <?
                                                $nameArray=sql_select( "select plot_no, level_no, road_no, block_no, contact_no, country_id, province, city, zip_code, contact_no, email, website, vat_number from lib_company where id=$company and status_active=1 and is_deleted=0");
                                                foreach ($nameArray as $result)
                                                { 
                                                ?>
                                                <? echo $result[csf('plot_no')]; ?> &nbsp; 
                                                <? echo $result[csf('level_no')]?> &nbsp; 
                                                <? echo $result[csf('road_no')]; ?> &nbsp; 
                                                <? echo $result[csf('block_no')];?> &nbsp; 
                                                <? echo $result[csf('city')];?> &nbsp; 
                                                <? echo $result[csf('zip_code')]; ?> &nbsp; 
                                                <? echo $result[csf('province')];?> &nbsp; 
                                                <? echo $country_arr[$result[csf('country_id')]]; ?>&nbsp; <br>
                                                <? echo $result[csf('contact_no')];?> &nbsp; 
                                                <? echo $result[csf('email')];?> &nbsp; 
                                                <? echo $result[csf('website')]; ?> <br>
                                                <b> Vat No : <? echo $result[csf('vat_number')]; ?></b> <?
                                                }
                                                ?>
                                                </td>  
                                            </tr>
                                            <tr>
                                            	<td align="center" style="font-size:16px"><strong><u>Gate Pass</u></strong></td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>               
                        </td>
                    </tr>
                    <tr>
						<? 
                        $party_add=$dataArray[0][csf('party_id')];
                        $nameArray=sql_select( "select address_1, web_site, buyer_email, country_id from  lib_buyer where id=$party_add"); 
                        foreach ($nameArray as $result)
                        { 
                        $address="";
                        if($result!="") $address=$result[csf('address_1')];//.'<br>'.$country_arr[$result['country_id']].'<br>'.$result['email'].'<br>'.$result['web_site']
                        }
                        ?> 
                        <td width="300" rowspan="4" valign="top" colspan="2" style="font-size:18px"><strong>Party : <? echo $buyer_library[$party_add].'<br>'.$address;  ?></strong></td>
                        <td width="125" style="font-size:12px"><strong>Challan No :</strong></td><td width="170px"><? echo $dataArray[0][csf('chalan_no')]; ?></td>
                        <td width="125" style="font-size:12px"><strong>Delivery Date :</strong></td><td width="170px"><? echo change_date_format($dataArray[0][csf('subcon_date')]); ?></td>
                    </tr>
                    <tr>
                        <td style="font-size:12px"><strong>Transport Com.:</strong></td><td><? echo $dataArray[0][csf('tran_company')]; ?></td>
                        <td style="font-size:12px"><strong>Forwarder:</strong></td><td><? echo $supplier_library[$dataArray[0][csf('forwarder')]]; ?></td>
                    </tr>
                    <tr>
                    	<td style="font-size:12px" colspan="2"><strong>Return No: </strong> <? echo $dataArray[0][csf('sys_no')]; ?></td>
                    	<td colspan="2">&nbsp;</td>
                    </tr>
                    <tr>
                        <td colspan="6" align="right" style="font-size:14px">
                            <table cellspacing="0" width="350"  border="1" rules="all" class="rpt_table" >
                            <thead bgcolor="#dddddd" align="center">
                            <th width="150">Roll/Bag</th>
                            <th width="150">Weight</th>
                            </thead>
                            <tbody>
                            <tr>
                            <td align="center"><? echo $tot_roll; ?></td>
                            <td align="center"><? echo $tot_qty; ?></td>
                            </tr>
                            </tbody>
                            </table>
                        </td>
                    </tr>
				</table>
				&nbsp;<br>
				<table cellspacing="0" width="900" >
                    <thead>
                        <tr><th colspan="9">&nbsp;</th></tr>
                        <tr height="16px" style="font-size:12px">
                        <th width="50">&nbsp;</th>
                        <th width="100"><hr>Receive By</th>
                        <th width="50">&nbsp;</th>
                        <th width="100"><hr>Audited By</th>
                        <th width="50">&nbsp;</th>
                        <th width="100"><hr>Prepared By</th>
                        <th width="50">&nbsp;</th>
                        <th width="100"><hr>Gate Entry</th>
                        <th width="50">&nbsp;</th>
                        </tr>
                    </thead>
				</table>
				<?
			}
			?>
	</div>
    <?
	exit();
}
?>