<?php require 'includes/layout.php';
$cart=$_SESSION['cart']??[]; if(!$cart) go('cart.php');
$in=implode(',',array_fill(0,count($cart),'?')); $s=db()->prepare("SELECT * FROM products WHERE id IN($in) AND available=1"); $s->execute(array_keys($cart)); $items=$s->fetchAll();
$sub=0; foreach($items as &$p){ $p['qty']=$cart[$p['id']]; $sub+=$p['qty']*$p['price']; } unset($p);
$svc=round($sub*(float)setting('service_rate',0)/100,2); $tax=round(($sub+$svc)*(float)setting('tax_rate',0)/100,2); $total=$sub+$svc+$tax;
$methods=array_map('trim',explode(',',setting('payment_methods','Cash,GCash')));
if($_SERVER['REQUEST_METHOD']==='POST'){ check_csrf();
 $type=in_array($_POST['type'],['dine-in','takeaway'])?$_POST['type']:'dine-in'; $pm=in_array($_POST['pay'],$methods)?$_POST['pay']:$methods[0];
 $nickname=substr(trim($_POST['nickname']??''),0,80); $u=user();
 if(!$nickname){ flash('Please enter a nickname so our team knows who to serve.','err'); }
 else {
  $notes=trim($_SESSION['note']??''); $notes='Customer nickname: '.$nickname.($notes ? "\n".$notes : '');
  $pdo=db(); $pdo->beginTransaction();
  try{
   $orderNick=strtoupper(substr(preg_replace('/[^A-Za-z0-9]/','',$nickname),0,10)) ?: 'GUEST';
   do{$no=$orderNick.'-'.random_int(1000,9999);$exists=$pdo->prepare('SELECT 1 FROM orders WHERE order_no=?');$exists->execute([$no]);}while($exists->fetchColumn());
   if(!$u){ $email='guest+'.date('YmdHis').random_int(1000,9999).'@guest.local'; $pdo->prepare('INSERT INTO users(name,email,password,role) VALUES(?,?,?,\'customer\')')->execute([$nickname,$email,password_hash(bin2hex(random_bytes(16)),PASSWORD_DEFAULT)]); $u=['id'=>$pdo->lastInsertId()]; }
   $pdo->prepare('INSERT INTO orders(order_no,customer_id,order_type,subtotal,service_charge,tax,total,payment_method,notes) VALUES(?,?,?,?,?,?,?,?,?)')->execute([$no,$u['id'],$type,$sub,$svc,$tax,$total,$pm,substr($notes,0,300)]);
   $oid=$pdo->lastInsertId(); $ins=$pdo->prepare('INSERT INTO order_items(order_id,product_id,product_name,price,qty) VALUES(?,?,?,?,?)');
   foreach($items as $p) $ins->execute([$oid,$p['id'],$p['name'],$p['price'],$p['qty']]);
   $pdo->commit(); unset($_SESSION['cart'],$_SESSION['note']);
   if(user()){ flash("Order $no placed! Track it below."); go('orders.php'); }
   $_SESSION['guest_order_no']=$no; $_SESSION['guest_nickname']=$nickname; go('guest-order.php');
  }catch(PDOException $x){ $pdo->rollBack(); flash('Could not place order. Please try again.','err'); }
 }
}
head('Checkout'); ?><h1 class="text-xl font-semibold mb-4">Checkout</h1>
<div class="grid md:grid-cols-2 gap-4"><div class="<?=card('p-4')?>"><h2 class="font-medium mb-2">Order summary</h2>
<?php foreach($items as $p): ?><div class="flex justify-between text-sm py-1"><span><?=$p['qty']?> × <?=e($p['name'])?></span><span><?=money($p['qty']*$p['price'])?></span></div><?php endforeach ?><hr class="my-2 border-line">
<div class="flex justify-between text-sm"><span>Subtotal</span><span><?=money($sub)?></span></div><div class="flex justify-between text-sm"><span>Service charge</span><span><?=money($svc)?></span></div><div class="flex justify-between text-sm"><span>Tax</span><span><?=money($tax)?></span></div>
<div class="flex justify-between font-semibold mt-2"><span>Total</span><span><?=money($total)?></span></div></div>
<form method="post" class="<?=card('p-4 space-y-3')?>"><?=csrf()?><div class="rounded-2xl bg-soft p-3 text-sm"><b>How should we call you?</b><p class="text-mute mt-1">Use a nickname or first name. This is what our team will use when your order is ready.</p></div><label class="block text-sm">Nickname <span class="text-mute">(required)</span><input required maxlength="80" class="<?=INP?>" name="nickname" value="<?=e($_POST['nickname']??'')?>" placeholder="e.g. Alex, Sam, Cookie"></label><label class="block text-sm">Order type<select name="type" class="<?=INP?>"><option value="dine-in">Dine-in</option><option value="takeaway">Takeaway</option></select></label>
<label class="block text-sm">Payment method<select name="pay" class="<?=INP?>"><?php foreach($methods as $m): ?><option><?=e($m)?></option><?php endforeach ?></select></label><button class="<?=BTN?> w-full">Confirm order</button></form></div><?php foot();
