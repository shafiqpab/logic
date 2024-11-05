<?
/*-------------------------------------------- Comments
Purpose			: 	This form Daily Order Entry List
				
Functionality	:	
JS Functions	:
Created by		:	Reza 
Creation date 	: 	15/12/2013
Updated by 		: 		
Update date		: 		   
QC Performed BY	:		
QC Date			:	
Comments		:
*/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Supplier List", "../../", 1, 1, $unicode,1,'');
?>	
<script>
	var permission='<? echo $permission; ?>';
	
	
        function toggle( x, origColor ) {
                var newColor = 'green';
                if ( x.style ) {
                    x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
                }
            }
            
	
        function js_set_value(str)
        {
            
			toggle( document.getElementById( 'tr_' + str), '#FFFFFF' );
		}
	
</script>

</head>
<body>
    <div style="width:1050px; margin:0 auto;">
		<? echo load_freeze_divs ("../../");  ?>
        <form id="supplierList_1">
                <table cellspacing="0" width="100%"  border="1" rules="all" class="rpt_table" id="tbl_suppler_list" >
                                   
                    <thead>
                        <tr>
                            <th width="50"><strong>SL</strong></th>
                            <th width="60"><strong>Company</strong></th>
                            <th width="80"><strong>Order No</strong></th>
                            <th width="80"><strong>Buyer</strong></th>
                            <th width="90"><strong>Style</strong></th>
                            <th width="70"><strong>Order Qty</strong></th>
                            <th width="70"><strong>Unit Price</strong></th>
                            <th width="100"><strong>Value</strong></th>
                            <th width="100"><strong>Ship Date</strong></th>
                            <th width="60"><strong>Lead Time</strong></th>
                            <th width="60"><strong>SMV Per-Unit</strong></th>
                            <th><strong>Dealing Marchant</strong></th>
                        </tr>
                    </thead>
                    <tbody>
                   
                <?
		$before = mktime(0,0,0,date("m"),date("d")-1,date("Y"));
		$bfdate=date("Y-m-d", $before);
				$sql_con="SELECT 
					a.company_name,
					b.po_number,
					a.buyer_name,
					a.style_ref_no,
					b.po_quantity,
					b.unit_price,
					(b.unit_price*b.po_quantity) as value,
					b.pub_shipment_date,
					DATEDIFF(b.pub_shipment_date,b.po_received_date) AS leadtime,
					a.set_smv,
					a.dealing_marchant
				FROM wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.insert_date LIKE '$bfdate%' and b.status_active=1";
		//echo $sql_con;			
				$sql_data=sql_select($sql_con);
				
				$buyer_nameArr = return_library_array("select id,short_name from lib_buyer","id","short_name");
				$company_nameArr = return_library_array("select id,company_short_name from lib_company","id","company_short_name");
				$dealing_marchantArr = return_library_array("select id,team_leader_name from lib_marketing_team","id","team_leader_name");
				
				$total_orderqty=0;
				$total_value=0;
				$sl=1;
				foreach($sql_data as $row){
				$bgcolor=($sl%2==0)?"#E9F3FF":"#FFFFFF";
				
				?>    
                        <tr bgcolor="<? echo $bgcolor ; ?>" id="tr_<? echo $sl; ?>" onClick="js_set_value(<? echo $sl; ?>)" style="cursor:pointer;">
                            <td><? echo $sl; ?></td>
                            <td align="center"><? echo $company_nameArr[$row[csf('company_name')]]; ?></td>
                            <td align="center"><? echo $row[csf('po_number')]; ?></td>
                            <td><? echo $buyer_nameArr[$row[csf('buyer_name')]]; ?></td>
                            <td align="center"><? echo $row[csf('style_ref_no')]; ?></td>
                            <td align="right"><? echo $row[csf('po_quantity')]; $total_orderqty+=$row[csf('po_quantity')]; ?></td>
                            <td align="right"><? echo number_format($row[csf('unit_price')],2,'.',','); ?></td>
                            <td align="right"><? echo number_format($row[csf('value')],2,'.',','); $total_value+=$row[csf('value')];?></td>
                            <td align="center"><? echo $row[csf('pub_shipment_date')]; ?></td>
                            <td align="center"><? echo $row[csf('leadtime')]; ?></td>
                            <td align="center"><? echo $row[csf('set_smv')]; ?></td>
                            <td><? echo $dealing_marchantArr[$row[csf('dealing_marchant')]]; ?></td>
                        </tr>
                 <? $sl++; } ?>
                        <tr bgcolor="#E9F3FF">
                            <td colspan="5"><strong>Total:</strong></td>
                            <td width="70" align="right"><strong><?php echo $total_orderqty; ?></strong></td>
                            <td width="70"></td>
                            <td width="100" align="right"><strong><?php echo number_format($total_value,2,'.',',');?></strong></td>
                            <td colspan="4"></td>
                        </tr>
                    <tbody>
                </table>
        </form>
    </div>
    
    
    
</body>

<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>