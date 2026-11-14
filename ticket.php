<?php
require 'config/app.php';
$order=trim($_GET['order']??'');
if(!$order){ http_response_code(400); exit('Order ID is required.'); }
$s=db()->prepare('SELECT o.*,u.name customer FROM orders o JOIN users u ON u.id=o.customer_id WHERE o.order_no=? LIMIT 1'); $s->execute([$order]); $o=$s->fetch();
if(!$o){ http_response_code(404); exit('Order not found.'); }
$i=db()->prepare('SELECT * FROM order_items WHERE order_id=?'); $i->execute([$o['id']]); $items=$i->fetchAll();
$nickname=$o['customer']; if(preg_match('/^Customer nickname:\s*(.+)$/m',$o['notes']??'',$m)) $nickname=trim($m[1]);
$safe=preg_replace('/[^A-Za-z0-9_-]/','',$o['order_no']);
header('Content-Type: text/plain; charset=utf-8'); header('Content-Disposition: attachment; filename="galleta-ticket-'.$safe.'.txt"');
echo "GALLETA CAFE\n"; echo "ORDER TICKET\n\n"; echo "Order ID: ".$o['order_no']."\n"; echo "Nickname: ".$nickname."\n"; echo "Status: ".ucfirst($o['status'])."\n"; echo "Order type: ".$o['order_type']."\n"; echo "Date: ".date('M j, Y g:i A',strtotime($o['created_at']))."\n\n"; echo "ITEMS\n";
foreach($items as $x) echo $x['qty']." x ".$x['product_name']." - ".money($x['price']*$x['qty'])."\n";
echo "\nTOTAL: ".money($o['total'])."\n"; echo "\nPresent this ticket or provide the Order ID at the counter.\n";
