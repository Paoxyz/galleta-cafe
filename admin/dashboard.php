<?php require '../includes/layout.php'; require_role(['admin']); $d=db();
$one=fn($q)=>$d->query($q)->fetchColumn();
$st=[['Sales today',money($one("SELECT COALESCE(SUM(total),0) FROM orders WHERE DATE(created_at)=CURDATE() AND status<>'cancelled'"))],
['Orders today',$one('SELECT COUNT(*) FROM orders WHERE DATE(created_at)=CURDATE()')],['Customers',$one("SELECT COUNT(*) FROM users WHERE role='customer'")],
['Products',$one('SELECT COUNT(*) FROM products')],['Pending',$one("SELECT COUNT(*) FROM orders WHERE status='pending'")],['Completed',$one("SELECT COUNT(*) FROM orders WHERE status='completed'")]];
$sales=$d->query("SELECT DATE(created_at) d,SUM(total) t FROM orders WHERE status<>'cancelled' AND created_at>=CURDATE()-INTERVAL 6 DAY GROUP BY d ORDER BY d")->fetchAll();
$best=$d->query("SELECT product_name n,SUM(qty) q FROM order_items i JOIN orders o ON o.id=i.order_id WHERE o.status<>'cancelled' GROUP BY product_name ORDER BY q DESC LIMIT 5")->fetchAll();
$rec=$d->query('SELECT o.*,u.name cust FROM orders o JOIN users u ON u.id=o.customer_id ORDER BY o.id DESC LIMIT 8')->fetchAll();
head('Admin'); ?><h1 class="text-xl font-semibold mb-4">Dashboard</h1>
<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3 mb-4"><?php foreach($st as $s): ?><div class="<?=card('p-4')?>"><div class="text-xs text-mute"><?=$s[0]?></div><div class="text-xl font-semibold"><?=$s[1]?></div></div><?php endforeach ?></div>
<div class="grid lg:grid-cols-3 gap-4"><div class="<?=card('p-4 lg:col-span-2')?>"><h2 class="font-medium mb-2">Sales – last 7 days</h2><div class="relative h-64"><canvas id="c"></canvas></div></div>
<div class="<?=card('p-4')?>"><h2 class="font-medium mb-2">Best sellers</h2><?php foreach($best as $b): ?><div class="flex justify-between text-sm py-1"><span><?=e($b['n'])?></span><b><?=$b['q']?></b></div><?php endforeach ?></div></div>
<div class="<?=card('p-4 mt-4 overflow-x-auto')?>"><h2 class="font-medium mb-2">Recent transactions</h2><table class="w-full text-sm min-w-[32rem]"><?php foreach($rec as $o): ?><tr class="border-t border-line"><td class="py-2"><?=e($o['order_no'])?></td><td><?=e($o['cust'])?></td><td><?=$o['status']?></td><td><?=$o['payment_status']?></td><td class="text-right"><?=money($o['total'])?></td></tr><?php endforeach ?></table></div>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1"></script><script>new Chart(document.getElementById('c'),{type:'bar',data:{labels:<?=json_encode(array_column($sales,'d'))?>,datasets:[{label:'₱',data:<?=json_encode(array_map('floatval',array_column($sales,'t')))?>,backgroundColor:'#EFA6C8',borderRadius:8}]},options:{maintainAspectRatio:false,plugins:{legend:{display:false}}}})</script><?php foot();
