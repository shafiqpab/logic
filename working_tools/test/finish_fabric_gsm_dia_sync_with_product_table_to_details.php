<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();

$composition_arr=array();
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from  lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id order by b.id asc";
	$data_array=sql_select($sql_deter);
	if(count($data_array)>0)
	{
		foreach( $data_array as $row )
		{
			if(array_key_exists($row[csf('id')],$composition_arr))
			{
				$composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
			}
			else
			{
				$composition_arr[$row[csf('id')]]=$row[csf('construction')].", ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
			}
		}
	}


$sql =  sql_select("SELECT a.prod_id, b.detarmination_id, a.gsm as dtls_gsm, to_char(a.width) as dtls_dia, b.gsm as prod_gsm,  to_char(b.dia_width) as prod_dia, max(b.product_name_details), a.id
from pro_finish_fabric_rcv_dtls a, product_details_master b
where a.prod_id = b.id and (a.gsm != b.gsm or to_char(a.width) != to_char(b.dia_width) )
and b.item_category_id=2 and a.status_active=1
group by a.prod_id, b.detarmination_id, a.gsm, b.gsm, to_char(a.width), to_char(b.dia_width), a.id
order by a.prod_id");
if(empty($sql))
{
	echo "Data Not Found";
	die;
}

foreach ($sql as $val) 
{
	$item_desc= $composition_arr[$val[csf('detarmination_id')]];
	$prod_name_dtls= $composition_arr[$val[csf('detarmination_id')]].", ".$val[csf('prod_gsm')].", ".$val[csf('prod_dia')];

	//echo "update product_details_master set product_name_details='".$prod_name_dtls."', item_description='".$item_desc."' where id=".$val[csf("prod_id")]."<br>";

	//echo "update pro_finish_fabric_rcv_dtls set gsm='".$val[csf('prod_gsm')]."', width='".$val[csf('prod_dia')]."' where id=".$val[csf("id")]."<br><br>";

	execute_query("update product_details_master set product_name_details='".$prod_name_dtls."', item_description='".$item_desc."' where id=".$val[csf("prod_id")],0);
	execute_query("update pro_finish_fabric_rcv_dtls set gsm='".$val[csf('prod_gsm')]."', width='".$val[csf('prod_dia')]."' where id=".$val[csf("id")],0);
}

oci_commit($con); 
echo "Success";
disconnect($con);
die;
 

?>