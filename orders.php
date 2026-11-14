<?php require 'includes/layout.php';
$lookup=trim($_GET['order']??''); $found=null; $items=[];
if($lookup){ $s=db()->prepare('SELECT o.*,u.name customer FROM orders o JOIN users u ON u.id=o.customer_id WHERE o.order_no=? LIMIT 1'); $s->execute([$lookup]); $found=$s->fetch();
 if($found){ $i=db()->prepare('SELECT oi.*,p.image FROM order_items oi LEFT JOIN products p ON p.id=oi.product_id WHERE oi.order_id=?'); $i->execute([$found['id']]); $items=$i->fetchAll(); } }
$os=[];
if(user() && !$lookup){ $s=db()->prepare('SELECT o.*,u.name customer FROM orders o JOIN users u ON u.id=o.customer_id WHERE o.customer_id=? ORDER BY o.id DESC'); $s->execute([user()['id']]); $os=$s->fetchAll(); }
$flow=['pending'=>'Pending','confirmed'=>'Accepted','preparing'=>'Preparing','ready'=>'Ready','completed'=>'Completed']; $flowKeys=array_keys($flow);
function order_nickname($o){ if(preg_match('/^Customer nickname:\s*(.+)$/m',$o['notes']??'',$m)) return trim($m[1]); return $o['customer']; }
function show_order($o,$items,$flow,$flowKeys,$lookup=false){
 $step=array_search($o['status'],$flowKeys); $label=$flow[$o['status']]??ucfirst($o['status']); ?>
<div class="<?=card('p-4 mb-4')?>"><div class="flex items-start justify-between flex-wrap gap-2"><div class="min-w-0"><b class="text-lg break-all"><?=e($o['order_no'])?></b><div class="text-sm text-mute truncate">For <?=e(order_nickname($o))?></div></div><span class="text-sm px-3 py-1 rounded-full whitespace-nowrap shrink-0 <?=$o['status']==='cancelled'?'bg-red-100 text-red-700':'bg-soft'?>"><?=$label?></span></div>
<div class="text-xs text-mute mt-2"><?=date('M j, g:i A',strtotime($o['created_at']))?> · <?=e($o['order_type'])?> · <?=e($o['payment_method'])?> (<?=$o['payment_status']?>)</div>
<?php if($step!==false): ?><div class="flex gap-1 my-3"><?php foreach($flowKeys as $k=>$f): ?><div class="flex-1 h-1.5 rounded-full <?=$k<=$step?'bg-brand':'bg-soft'?>"></div><?php endforeach ?></div><?php endif ?>
<div class="mt-4 space-y-2"><?php foreach($items as $x): ?><div class="flex items-center gap-3 rounded-2xl bg-light p-2"><div class="h-14 w-14 shrink-0 overflow-hidden rounded-xl"><?=img($x,'h-full w-full object-cover')?></div><div class="min-w-0 flex-1"><div class="truncate text-sm font-medium"><?=e($x['product_name'])?></div><div class="text-xs text-mute"><?=$x['qty']?> × <?=money($x['price'])?></div></div><b class="text-sm"><?=money($x['qty']*$x['price'])?></b></div><?php endforeach ?></div>
<?php if($o['reject_reason']): ?><p class="text-sm text-red-700 mt-2">Reason: <?=e($o['reject_reason'])?></p><?php endif ?><div class="text-right font-semibold mt-4"><?=money($o['total'])?></div></div>
<?php }
head('My Orders',true); echo '<meta http-equiv="refresh" content="20">'; ?>
<div class="mx-auto max-w-4xl"><div class="mb-6"><p class="text-xs font-semibold uppercase tracking-[.25em] text-mute">Order tracking</p><h1 class="mt-1 text-3xl font-semibold">My orders</h1><p class="mt-1 text-sm text-mute">Enter your order ID to see the latest status of your order.</p></div>
<form method="get" class="<?=card('p-4 mb-5')?> flex flex-col sm:flex-row gap-2"><input class="<?=INP?>" name="order" value="<?=e($lookup)?>" placeholder="Order ID, e.g. ALEX-4821" required><button class="<?=BTN?>">Check order</button></form>
<?php if($lookup && !$found): ?><div class="rounded-2xl bg-red-100 text-red-800 px-4 py-3 mb-4 text-sm">We could not find that order ID. Please check the ticket and try again.</div><?php endif;
if($found){ $foundItems=$items; show_order($found,$foundItems,$flow,$flowKeys,true); }
if(user() && !$lookup): ?><h2 class="mb-3 font-medium">Your recent orders</h2><?php if(!$os): ?><div class="<?=card('p-10 text-center text-mute')?>">No orders yet.</div><?php endif; foreach($os as $o){$i=db()->prepare('SELECT oi.*,p.image FROM order_items oi LEFT JOIN products p ON p.id=oi.product_id WHERE oi.order_id=?');$i->execute([$o['id']]);show_order($o,$i->fetchAll(),$flow,$flowKeys);} endif; ?></div><?php foot();
