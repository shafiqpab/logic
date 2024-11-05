<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
include('../../../includes/common.php');
$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];
if ($action=="supplier_popup")
{
    echo load_html_head_contents("Popup Info", "../../../", 1, 1,'',1,'');
    $data=explode('_',$data);
  //  print_r ($data);
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
           $('#supplier_id').val( id );
            $('#supplier_val').val( ddd );
        } 
    </script>  
    <input type="hidden" id="supplier_id" />
    <input type="hidden" id="supplier_val" />
 <?
    $countryArr = return_library_array("select id,country_name from  lib_country where status_active=1 and is_deleted=0","id","country_name");
    
    // $sql="select a.id, a.supplier_name, a.short_name, a.country_id from lib_supplier a where FIND_IN_SET($data[0],tag_company) and a.status_active=1 order by a.supplier_name "; 
 //echo $data[1];
   if($data[1]==''){
    $party_cond="";
   }else{
    $party_cond="and b.party_type in ($data[1])";
   }
	 $sql = "select c.id,c.supplier_name,c.short_name,b.party_type,c.country_id from lib_supplier_tag_company a, lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$data[0]' $party_cond  and c.status_active=1  and c.is_deleted=0 group by c.id, c.supplier_name,c.short_name,b.party_type,c.country_id order by c.supplier_name";
    // echo $sql;
    $arr=array(2=>$party_type_supplier,3=>$countryArr);
    echo  create_list_view("list_view", "Supplier,Short Name,Party Type,Country", "160,100,170,120","580","360",0, $sql , "js_set_value", "id,supplier_name", "", 1, "0,0,party_type,country_id", $arr , "supplier_name,short_name,party_type,country_id", "",'setFilterGrid("list_view",-1);','0,0,0,0','',1) ;
    exit(); 
}

if ($action=="report_generate")
{
    extract($_REQUEST);
    $party_id=str_replace("'","",$cbo_party_type);
    $supplier_id=str_replace("'","",$txt_supplier_id);
    
    if ($party_id==0) $part_id =""; else $part_id =" and b.party_type in ( $party_id )";
    if ($supplier_id==0) $suppl_id =""; else $suppl_id =" and a.id in ( $supplier_id )";
    
    
    
        $sql_con="select a.id, a.supplier_name, a.short_name, a.contact_person, a.contact_no, a.designation, a.address_1, a.address_2, a.remark, a.email, a.web_site, a.country_id, a.status_active, 
        a.credit_limit_days, a.credit_limit_amount, a.credit_limit_amount_currency, b.party_type, c.tag_company,a.tin_number,a.vat_number,a.owner_name
        from lib_supplier a,lib_supplier_party_type b, lib_supplier_tag_company c 
        where a.id=c.supplier_id and c.tag_company=$cbo_company_id and a.id=b.supplier_id and a.is_deleted=0  and a.status_active=1 $part_id $suppl_id order by a.supplier_name ";
         //echo $sql_con;die;           
        $sql_data=sql_select($sql_con);
        
        $country_arr = return_library_array("select id,country_name from  lib_country where status_active=1 and is_deleted=0","id","country_name");
    
    $company_library=sql_select("select id, company_name, plot_no, level_no,road_no,city from lib_company where id=".$cbo_company_id."");
     ob_start();
    ?>

    <div style="width:1930px; margin:0 auto;">
    <span style="font-size:20px"><center><b><? echo  $company_library[0][csf('company_name')];?></b></center></span>
    <table width="1870" id="table_header_1">
        <tr>
            <td colspan="14" align="center" class="form_caption" style="font-size:16px"><center><strong><u><? echo $report_title; ?> Report</u></strong></center></td>
        </tr>
    </table>
        
       <div style="width:1876px;">
        <table cellspacing="0" width="1870"  border="1" rules="all" class="rpt_table">
            <thead>
                <tr>
                    <th width="40">SL</th>
                    <th width="150" align="center">Supplier Name</th>
                    <th width="100" align="center">Supplier Type </th>
                    <th width="75" align="center">Short Name</th>
                    <th width="100" align="center">Contact Person</th>
                    <th width="90" align="center">Designation</th>
                    <th width="75" align="center">Contact No.</th>
                    <th width="150" align="center">Address</th>
                    <th width="150" align="center">Address 2</th>
                    <th width="100" align="center">Email</th>
                    <th width="100" align="center">Country</th>
                    <th width="100" align="center">TIN Number</th>
                    <th width="100" align="center">VAT Number</th>
                    <th width="100" align="center">Owner Info</th>
                    <th width="100" align="center">Website</th>
                    <th width="100" align="center">Remarks</th>
                
                    <th width="80" align="center">Credit Limit (Days)</th>
                    <th width="80" align="center">Credit Limit (Amount)</th>
                    <th width="80" align="center">Currency</th>
                </tr>         
            </thead>
         </table>
         <div id="scroll_body" style="width:1890px; max-height:400px; overflow-y:scroll">
        <table cellspacing="0" width="1868"  border="1" rules="all" class="rpt_table"  id="table_body" >
          <?
          $i=1;
        foreach( $sql_data as $row)
        {
            $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF"; 
            ?>
            <tr bgcolor="<? echo $bgcolor ; ?>"  onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                <td width="40"><? echo $i; ?></td>
                <td width="150"><p><? echo $row[csf("supplier_name")]; ?></p></td>
                <td width="100"><p><? echo $party_type_supplier[$row[csf("party_type")]]; ?></p></td>
                <td width="75"><p><? echo $row[csf("short_name")]; ?></p></td>
                <td width="100"><p><? echo $row[csf("contact_person")]; ?></p></td>
                <td width="90"><p><? echo $row[csf("designation")]; ?></p></td>
                <td width="75"><p><? echo $row[csf("contact_no")]; ?></p></td>
                <td width="150"><p><? echo $row[csf("address_1")];?></p></td>
                <td width="150"><p><? echo $row[csf("address_2")];?></p></td>
                <td width="100"><p><? echo $row[csf("email")]; ?></p></td>
                <td width="100"><p><? echo $country_arr[$row[csf("country_id")]]; ?></p></td>
                <td width="100"><p><? echo $row[csf("tin_number")]; ?></p></td>
                <td width="100"><p><? echo $row[csf("vat_number")]; ?></p></td>
                <td width="100"><a href="##" onClick="openmypage_owner_info('<?=$row[csf("owner_name")];?>','<?=$row[csf("owner_nid")];?>','<?=$row[csf("owner_name")];?>','<?=$row[csf("owner_contact")];?>','<?=$row[csf("owner_email")];?>')"><? echo $row[csf("owner_name")]; ?></a></td>
                <td width="100"><p><? echo $row[csf("web_site")]; ?></p></td>
                <td width="100"><p><? echo $row[csf("remark")]; ?></p></td>
                <td width="80"><p><? echo $row[csf("credit_limit_days")]; ?></p></td>
                <td width="80"><p><? echo $row[csf("credit_limit_amount")]; ?></p></td>
                <td width="80"><p><? echo $currency[$row[csf("credit_limit_amount_currency")]]; ?></p></td>
            </tr>
            
        <?  
        $i++;
        }
?>      
      </table>
     <table class="tbl_bottom" width="1870" id="report_table_footer" cellpadding="0" cellspacing="0" border="1" rules="all">
     <tr><td colspan="19">&nbsp;</td></tr>
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
    $create_new_doc = fopen($filename, 'w') or die('can not open');    
    $is_created = fwrite($create_new_doc, $html) or die('can not write');
    echo "$html****$filename"; 
    exit();
}


if ($action=="report_generate_party_wise")
{
    extract($_REQUEST);
    //$data=explode('*',$data);
    $party_id=str_replace("'","",$cbo_party_type);
    $supplier_id=str_replace("'","",$txt_supplier_id);
    //print_r ($supplier_id);
    
    if ($party_id==0) $part_id =""; else $part_id =" and b.party_type in ( $party_id )";
    if ($supplier_id==0) $suppl_id =""; else $suppl_id =" and a.id in ( $supplier_id )";
    
    ?>
    <div id="scroll_body" align="center" style="height:auto; width:1270px; margin:0 auto; padding:0;">
    <?
    $company_library=sql_select("select id, company_name, plot_no, level_no,road_no,city from lib_company where id=".$cbo_company_id."");
    
    foreach( $company_library as $row)
    {
?>
        <span style="font-size:20px"><center><b><? echo $row[csf('company_name')];?></b></center></span>
<?
    }
?>
    <table width="1260px" align="center">
        <tr>
            <td colspan="6" align="center" style="font-size:18px"><center><strong><u><? echo $report_title; ?> Report</u></strong></center></td>
        </tr>
    </table>
    <?
        $sql_con="select a.id, a.supplier_name, a.short_name, a.contact_person, a.contact_no, a.designation, a.address_1, a.email, a.web_site, a.country_id, a.status_active, 
        a.credit_limit_days, a.credit_limit_amount, a.credit_limit_amount_currency, b.party_type, c.tag_company 
        from lib_supplier a,lib_supplier_party_type b, lib_supplier_tag_company c 
        where a.id=c.supplier_id and c.tag_company=$cbo_company_id and a.id=b.supplier_id and a.is_deleted=0  and a.status_active=1 $part_id $suppl_id order by b.party_type, a.supplier_name ";
        //echo $sql_con;            
        $sql_data=sql_select($sql_con);
        
        $item_category_array=array();
        $country_arr = return_library_array("select id,country_name from  lib_country where status_active=1 and is_deleted=0","id","country_name");
    ?>
        <div style="width:1270px; height:auto">
        <table align="right" cellspacing="0" width="1260px"  border="1" rules="all" class="rpt_table" id="tbl_suppler_list" >
            <?
        foreach( $sql_data as $row)
        {
            if (!in_array($row[csf("party_type")],$item_category_array) )
            {
                $item_category_array[]=$row[csf('party_type')];
            ?>
            <thead bgcolor="#dddddd" align="center">
                <tr>
                    <td colspan="13" align="left"><b>Category : <? echo $party_type_supplier[$row[csf("party_type")]]; ?></b></td>
                </tr>
                <tr>
                    <th width="40">SL</th>
                    <th width="150" align="center">Supplier Name</th>
                    <th width="75" align="center">Short Name</th>
                    <th width="100" align="center">Contact Person</th>
                    <th width="90" align="center">Designation</th>
                    <th width="75" align="center">Contact No.</th>
                    <th width="150" align="center">Address</th>
                    <th width="100" align="center">Email</th>
                    <th width="100" align="center">Country</th>
                    <th width="100" align="center">Website</th>
                    <th width="80" align="center">Credit Limit (Days)</th>
                    <th width="80" align="center">Credit Limit (Amount)</th>
                    <th width="80" align="center">Currency</th>
                </tr>         
            </thead>
            <tbody>
            <?
            $i=1;
            }
                if ($i%2==0)  
                $bgcolor="#E9F3FF";
            else
                $bgcolor="#FFFFFF";
            ?>
            <tr bgcolor="<? echo $bgcolor ; ?>">
                <td><? echo $i; ?></td>
                <td><? echo $row[csf("supplier_name")]; ?></td>
                <td><? echo $row[csf("short_name")]; ?></td>
                <td><? echo $row[csf("contact_person")]; ?></td>
                <td><? echo $row[csf("designation")]; ?></td>
                <td><? echo $row[csf("contact_no")]; ?></td>
                <td><? echo $row[csf("address_1")]; ?></td>
                <td><? echo $row[csf("email")]; ?></td>
                <td><? echo $country_arr[$row[csf("country_id")]]; ?></td>
                <td><? echo $row[csf("web_site")]; ?></td>
                <td><? echo $row[csf("credit_limit_days")]; ?></td>
                <td><? echo $row[csf("credit_limit_amount")]; ?></td>
                <td><? echo $currency[$row[csf("credit_limit_amount_currency")]]; ?></td>
            </tr>
            </tbody>
        <?  
        $i++;
        }
?>
        </table>
    </div>
    </div>
<?
}

if($action=="owner_info")
{
	echo load_html_head_contents("Owner Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	//echo $party_type_id;
	?>
		
	</head>
	<body>
		<div align="center">
			<fieldset style="width:690px;margin-left:10px">
		    	
		        <form name="searchprocessfrm_1"  id="searchprocessfrm_1" autocomplete="off">
		            <table width="680">
		               <tr>
		               		<td width="60">Owner Name</td>
		               		<td width="180"><input type="text" name="owner_name" id="owner_name" class="text_boxes" style="width:180px;" value="<?php echo $owner_name;?>"></td>

		               		<td width="60">Owner NID</td>
		               		<td width="180"><input type="text" name="owner_nid" id="owner_nid" class="text_boxes" style="width:180px;" value="<?php echo $owner_nid;?>"></td>
		               </tr>
		               <tr>
		               		<td colspan="4">&nbsp;</td>
		               </tr>
		               <tr>
		               		<td width="60">Owner Contact</td>
		               		<td width="180"><input type="text" name="owner_contact" id="owner_contact" class="text_boxes" style="width:180px;" value="<?php echo $owner_contact;?>"></td>

		               		<td width="60">Owner Email</td>
		               		<td width="180"><input type="text" name="owner_email" id="owner_email" class="text_boxes" style="width:180px;" value="<?php echo $owner_email;?>"></td>
		               </tr>
		               <tr>
		               		<td colspan="4">&nbsp;</td>
		               </tr>
		               
		               <tr>
		               		<td colspan="4" style="justify-content: center;text-align: center;"><input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px" /></td>
		               </tr>
		            </table>
		            
		            
		        </form>
		    </fieldset>
		</div>    
	</body>           
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	
	</html>
	<?
	exit();
}

?>
