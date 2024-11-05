<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../includes/common.php');
$con=connect();

$dataSet =  sql_select("select COL.TABLE_NAME,COL.COLUMN_NAME, COL.COLUMN_ID,COL.DATA_LENGTH,COL.DATA_PRECISION,COL.DATA_SCALE
from sys.dba_tab_columns col
inner join sys.dba_tables t on col.owner = t.owner and col.table_name = t.table_name
where col.owner='LOGIC3RDVERSION_TEST' and data_scale!='0' and data_type='NUMBER' 
and col.column_name not in ('UPDATED_BY','INSERTED_BY')
and col.table_name not in('YEAR_CLOSE_ITEM_REF_120321','YEAR_CLOSE_ITEM_120321')
order by t.owner, t.table_name, col.column_id");
/*select COL.TABLE_NAME,COL.COLUMN_NAME, COL.COLUMN_ID,COL.DATA_LENGTH,COL.DATA_PRECISION,COL.DATA_SCALE
from sys.dba_tab_columns col
inner join sys.dba_tables t on col.owner = t.owner and col.table_name = t.table_name
where col.owner='LOGIC3RDVERSION_TEST' --and data_scale!='0' 
and data_type='NUMBER' 
and col.column_name not in ('UPDATED_BY','INSERTED_BY')
and COL.COLUMN_NAME like '%_DP'
and col.table_name not in('YEAR_CLOSE_ITEM_REF_120321','YEAR_CLOSE_ITEM_120321')
order by t.owner, t.table_name, col.column_id*/

if(!empty($dataSet))
{
	foreach ($dataSet as $row) 
	{
		$table_name 	= $row['TABLE_NAME'];
		$column_name 	= $row['COLUMN_NAME'];
		$new_column 	= $row['COLUMN_NAME']."_DP";
		$precision 		= $row['DATA_PRECISION'];
		$scale 			= $row['DATA_SCALE'];

		//echo "select $column_name from $table_name; <br />";
		echo "alter table $table_name add $new_column NUMBER($precision); <br />";
		echo "update $table_name set $new_column = $column_name; <br />";
		echo "update $table_name set $column_name = null; <br />";
		echo "alter table $table_name modify $column_name NUMBER($precision,$scale); <br />";
		echo "update $table_name set $column_name = $new_column; <br /> <br />";
		//echo "alter table $table_name drop column $new_column; <br /> <br />";

		execute_query("alter table $table_name add $new_column NUMBER($precision)");
		execute_query("update $table_name set $new_column = $column_name");
		execute_query("update $table_name set $column_name = null");
		execute_query("alter table $table_name modify $column_name NUMBER($precision,$scale)");
		execute_query("update $table_name set $column_name = $new_column");
		//execute_query("alter table $table_name drop column $new_column");
	}
}



oci_commit($con); 
echo "Success";
disconnect($con);
die;
 
?>