<?
include('../includes/common.php');
$url = rtrim($_GET['url'], '/');
$url = explode('/', $url);
$url_table_search  = $url[0];
$url_column_search = $url[1];
?>
<!DOCTYPE html>
<html>
<head>
    <style>
    *{margin: 0;padding: 0;outline: 0;}
    body{font-family: sans-serif; font-size: 14px;}
    table {
        border-collapse: collapse;
    }
    h2{margin: 10px; color: #CA932F}

    th {
        border: 1px solid #CA932F;
        background-color: #CA932F;
        padding: 2px;
        text-align: left;
    }
    td{
        border: 1px solid #f2f2f2;
        text-align: left;
        padding: 2px;
    }
</style>
</head>
<body>
    <div style="width: 100%">
    <h2 align="center">Table Information</h2> 
    <?
        if ($url_table_search != '') $url_table_search_cond=" and table_name like '$url_table_search'"; else $url_table_search_cond="";
        if ($url_column_search != '') $url_column_search_cond=" and column_name like '$url_column_search'"; else $url_column_search_cond="";

        $sql = "SELECT table_name, column_name, column_id, data_type, data_length as data_length_bytes, num_nulls as number_of_null_values
            from all_tab_columns
            where owner='LOGIC3RDVERSION' $url_table_search_cond $url_column_search_cond 
            order by table_name, column_id";
        $sql_res = sql_select($sql);
        foreach ($sql_res as $val)
        {
            $row_span_arr[$val[csf("table_name")]]++;
        }
        ?>
        <div width="1200" align="center">
            <table width="1200">
                <tr>
                    <th width="50">SL</th>
                    <th width="400">Table Name</th>
                    <th width="300">Column Name</th>
                    <th width="100">Column Id</th>
                    <th width="150">Data Type(Bytes)</th>
                    <th width="200">Number Of Null Values</th>
                </tr>
            </table>
            <table width="1200" border="1" rules="all">
                <?
                $i = 1;
                foreach ($sql_res as $row)
                {                    
                    //if ($i%2==1) $bgcolor="#DDD"; else $bgcolor="#FFF";
                    $tempData[$row[csf('table_name')]] += 1;
					?>
                    
                        <? if($tempData[$row[csf('table_name')]] == 1){ ?>
                        <tr bgcolor="<? echo $bgcolor; ?>">
                        <td width="50" rowspan="<? echo $row_span_arr[$row[csf('table_name')]]; ?>"><? echo $i; ?></td>
                        <td width="400" rowspan="<? echo $row_span_arr[$row[csf('table_name')]]; ?>"><? echo strtolower($row[csf("table_name")]); ?></td>
                        <? $i++;} else { ?>
                        	<tr>
                         <? } ?>
                        <td width="300"><? echo strtolower($row[csf("column_name")]); ?></td>
                        <td width="100"><? echo strtolower($row[csf("column_id")]); ?></td>
                        <td width="150"><? echo strtolower($row[csf("data_type")]).'('.$row[csf('data_length_bytes')].')'; ?></td>
                        <td width="200"><? echo $row[csf("number_of_null_values")]; ?></td>
                    </tr>
                    <?
                }
                ?>
            </table>
            <br>
        </div>
    </div>
</body>
</html>
