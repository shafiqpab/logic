Note
-----------------------------------------------------------------------------------------------------------------------------
// This 2 queries are so IMPORTANT and we synchronise data manually because color id stored dtls table with comma separator//
-----------------------------------------------------------------------------------------------------------------------------

at first run 1st query
-------------------------


 select  b.id, b.program_qnty,--sum (c.program_qnty) as program_qnty
 c.program_qnty, sum(d.color_prog_qty) as color_prog_qty
 from PPL_PLANNING_INFO_ENTRY_DTLS b, PPL_PLANNING_ENTRY_PLAN_DTLS c,  PPL_COLOR_WISE_BREAK_DOWN d 
where b.id=c.dtls_id and b.mst_id=c.mst_id and c.dtls_id=d.program_no and c.mst_id=d.plan_id
 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  and d.status_active=1 and d.is_deleted=0 
 --and b.program_qnty < sum(d.color_prog_qty) 
 group by b.id, b.program_qnty , c.program_qnty
 
 HAVING  sum(d.color_prog_qty) > b.program_qnty  order by b.id desc


Run 2nd query after 1st query completed with data synchronise done
----------------------------------------------------------------------
select  b.id, b.program_qnty,sum (c.program_qnty) as program_qnty
-- c.program_qnty
  ,sum(d.color_prog_qty) as color_prog_qty
 from PPL_PLANNING_INFO_ENTRY_DTLS b, PPL_PLANNING_ENTRY_PLAN_DTLS c,  PPL_COLOR_WISE_BREAK_DOWN d 
where b.id=c.dtls_id and b.mst_id=c.mst_id and c.dtls_id=d.program_no and c.mst_id=d.plan_id
 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  and d.status_active=1 and d.is_deleted=0 
 --and b.program_qnty < sum(d.color_prog_qty) 
 group by b.id, b.program_qnty --,c.program_qnty
 

 
 --HAVING  sum(d.color_prog_qty) > sum(c.program_qnty)  order by b.id desc
 HAVING  sum(d.color_prog_qty) > b.program_qnty  order by b.id desc