<?php require '../includes/layout.php'; require_role(['server','admin']);
if($_SERVER['REQUEST_METHOD']==='POST'){ check_csrf(); $id=(int)$_POST['id']; $a=$_POST['act']; $next=['confirm'=>'confirmed','prepare'=>'preparing','ready'=>'ready','complete'=>'completed'];
 $pdo=db(); $pdo->beginTransaction();
 if($a==='reject'){ $r=trim($_POST['reason']??'')?:'Unavailable'; $pdo->prepare("UPDATE orders SET status='cancelled',reject_reason=?,server_id=? WHERE id=?")->execute([substr($r,0,200),user()['id'],$id]); }
 elseif(isset($next[$a])){ $pdo->prepare('UPDATE orders SET status=?,server_id=? WHERE id=?')->execute([$next[$a],user()['id'],$id]);
  if($a==='complete'){ $pdo->prepare("UPDATE orders SET payment_status='paid' WHERE id=?")->execute([$id]); $pdo->prepare('INSERT INTO payments(order_id,method,amount) SELECT id,payment_method,total FROM orders WHERE id=?')->execute([$id]); } }
 $pdo->commit(); log_act("order $id: $a"); go('server/board.php'); }
$cols=['pending'=>'New','confirmed'=>'Confirmed','preparing'=>'Preparing','ready'=>'Ready','completed'=>'Completed'];
$btn=['pending'=>['confirm','Accept'],'confirmed'=>['prepare','Start preparing'],'preparing'=>['ready','Mark ready'],'ready'=>['complete','Complete']];
$os=db()->query("SELECT o.*,u.name cust FROM orders o JOIN users u ON u.id=o.customer_id WHERE o.status<>'cancelled' AND (o.status<>'completed' OR DATE(o.created_at)=CURDATE()) ORDER BY o.id")->fetchAll();
$it=db()->prepare('SELECT * FROM order_items WHERE order_id=?'); $cnt=array_count_values(array_column($os,'status'));
head('Order Board'); echo '<meta http-equiv="refresh" content="15">'; ?>
<div class="flex justify-between items-center mb-4"><h1 class="text-xl font-semibold">Order board</h1><span class="text-xs text-mute">Auto-refreshes every 15s</span></div>
<div class="flex lg:grid lg:grid-cols-5 gap-3 overflow-x-auto pb-3">
<?php foreach($cols as $st=>$label): ?><section class="min-w-[17rem] lg:min-w-0 flex-1"><h2 class="font-medium mb-2"><?=$label?> <span class="text-xs bg-soft rounded-full px-2"><?=$cnt[$st]??0?></span></h2>
<?php foreach($os as $o) if($o['status']===$st): $it->execute([$o['id']]); ?>
<div class="<?=card('p-3 mb-3 '.($st==='pending'?'ring-2 ring-brand':''))?>"><div class="flex justify-between"><b><?=e($o['order_no'])?></b><span class="text-xs text-mute"><?=date('g:i A',strtotime($o['created_at']))?></span></div>
<div class="flex items-center justify-between gap-2 text-sm"><span><?=e($o['cust'])?> · <?=e($o['order_type'])?></span><a href="<?=BASE?>/receipt.php?order=<?=rawurlencode($o['order_no'])?>" class="text-xs font-semibold text-[#C978A1] hover:underline">Print receipt</a></div>
<ul class="text-sm my-2"><?php foreach($it->fetchAll() as $x): ?><li><?=$x['qty']?> × <?=e($x['product_name'])?></li><?php endforeach ?></ul>
<?php if($o['notes']): ?><p class="text-xs bg-soft rounded-xl p-2 mb-2">📝 <?=e($o['notes'])?></p><?php endif ?>
<div class="text-xs text-mute mb-2"><?=money($o['total'])?> · <?=e($o['payment_method'])?> · <?=$o['payment_status']?></div>
<?php if(isset($btn[$st])): ?><form method="post" class="space-y-2"><?=csrf()?><input type="hidden" name="id" value="<?=$o['id']?>"><button name="act" value="<?=$btn[$st][0]?>" class="<?=BTN?> w-full !py-3"><?=$btn[$st][1]?></button>
<?php if($st==='pending'): ?><input class="<?=INP?> !py-2 text-sm" name="reason" placeholder="Reject reason"><button name="act" value="reject" class="w-full text-sm text-red-700 underline">Reject order</button><?php endif ?></form><?php endif ?></div><?php endif ?></section><?php endforeach ?></div>
<?php foot();
