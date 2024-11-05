<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();
//die; 
/*UPDATE subcon_production_mst e
   SET e.within_group =
          (  SELECT c.within_group
               FROM subcon_production_dtls b,
                    subcon_production_mst a,
                    subcon_ord_mst c,
                    subcon_ord_dtls d
              WHERE     a.id = b.mst_id
                    AND c.id = d.mst_id
                    AND CAST (d.id AS VARCHAR (4000)) = b.order_id
                    AND a.entry_form = 307
                    AND e.id = a.id
           GROUP BY b.mst_id, c.within_group)
 WHERE e.entry_form = 307;*/
$kk=1;
//$sqls="select c.mst_id, a.within_group from subcon_ord_mst a,subcon_ord_dtls b, subcon_production_dtls c where a.id=b.mst_id AND CAST (b.id AS VARCHAR (4000)) = c.order_id and  a.entry_form = 278 group by c.mst_id, a.within_group order by c.mst_id asc";

$sqls="select d.id, a.within_group from subcon_ord_mst a,subcon_ord_dtls b, subcon_production_dtls c,subcon_production_mst d where d.id=c.mst_id and d.entry_form=307 and a.id=b.mst_id AND CAST (b.id AS VARCHAR (4000)) = c.order_id and  a.entry_form = 278 group by d.id, a.within_group order by d.id asc";
$row_data=sql_select($sqls);
$mst_id=''; $within_group='';
foreach($row_data as $val)
{
	$mst_id=$val[csf("id")]; $within_group=$val[csf("within_group")]; 
	$update_issue_prefix=execute_query("UPDATE subcon_production_mst set within_group=$within_group where  id=$mst_id and entry_form=307");
	$test_data="";
	$kk++;
	 
}
echo $kk; //die;

if($update_issue_prefix)
{
	oci_commit($con); 
	echo "Success";

}
else
{
	oci_rollback($con);
	echo "failed";
}



 
?>