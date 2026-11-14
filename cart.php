<?php require 'includes/layout.php';
$_SESSION['cart'] ??= []; $cart=&$_SESSION['cart'];
if($_SERVER['REQUEST_METHOD']==='POST'){ check_csrf(); $id=(int)($_POST['id']??0); $return=in_array($_POST['return']??'', ['index.php','menu.php'], true)?$_POST['return']:'cart.php'; $ajax=($_SERVER['HTTP_X_REQUESTED_WITH']??'')==='XMLHttpRequest';
 $respond=function()use($ajax,$cart,$id){if($ajax){header('Content-Type: application/json');echo json_encode(['ok'=>true,'id'=>$id,'qty'=>(int)($cart[$id]??0),'count'=>array_sum($cart)]);exit;}};
 switch($_POST['act']??''){
  case 'add': $cart[$id]=min(20,($cart[$id]??0)+1); $respond(); flash('Added to cart.'); go('menu.php');
  case 'inc': $cart[$id]=min(20,($cart[$id]??0)+1); $respond(); go($return);
  case 'dec': if(--$cart[$id]<=0) unset($cart[$id]); $respond(); go($return);
  case 'del': unset($cart[$id]); break;
  case 'note': $_SESSION['note']=substr($_POST['note'],0,300); break;
 }
 go('cart.php');
}
$items=[]; $sub=0;
if($cart){ $in=implode(',',array_fill(0,count($cart),'?')); $s=db()->prepare("SELECT * FROM products WHERE id IN($in)"); $s->execute(array_keys($cart));
 foreach($s->fetchAll() as $p){ $p['qty']=$cart[$p['id']]; $sub+=$p['qty']*$p['price']; $items[]=$p; } }
head('Cart',true); ?>
<div class="mx-auto max-w-4xl">
<div class="mb-6 flex flex-wrap items-end justify-between gap-3"><div><p class="text-xs font-semibold uppercase tracking-[.25em] text-mute">Your selections</p><h1 class="mt-1 text-3xl font-semibold">Your cart</h1><p class="mt-1 text-sm text-mute"><?=count($items)?> item<?=count($items)!==1?'s':''?> ready for your café moment.</p></div><?php if($items): ?><a href="<?=BASE?>/menu.php" class="text-sm font-semibold text-[#C978A1] hover:text-[#352B32]">+ Add more items</a><?php endif ?></div>
<?php if(!$items): ?>
<div class="<?=card('overflow-hidden')?>"><div class="bg-gradient-to-br from-soft to-white p-8 text-center sm:p-14"><div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-white text-4xl shadow-sm">☕</div><h2 class="mt-5 text-2xl font-semibold">Your cart is waiting</h2><p class="mx-auto mt-2 max-w-sm text-sm leading-6 text-mute">Add a drink, pastry, or favorite meal and it will appear here.</p><a href="<?=BASE?>/menu.php" class="<?=BTN?> mt-6 inline-block">Browse the menu</a></div></div>
<?php else: ?>
<div class="grid gap-5 lg:grid-cols-[1fr_21rem] lg:items-start">
<div class="space-y-3">
<?php foreach($items as $p): ?><article class="<?=card('p-3 sm:p-4')?>"><div class="flex gap-3 sm:gap-4"><div class="h-24 w-24 shrink-0 overflow-hidden rounded-2xl sm:h-28 sm:w-28"><?=img($p,'h-full w-full object-cover')?></div><div class="min-w-0 flex-1"><div class="flex items-start justify-between gap-2"><div><h2 class="font-semibold leading-tight"><?=e($p['name'])?></h2><p class="mt-1 text-sm text-mute"><?=money($p['price'])?> each</p></div><form method="post"><?=csrf()?><input type="hidden" name="id" value="<?=$p['id']?>"><button name="act" value="del" aria-label="Remove <?=e($p['name'])?>" class="rounded-full px-2 py-1 text-lg leading-none text-mute hover:bg-soft hover:text-red-600">×</button></form></div><div class="mt-4 flex items-center justify-between gap-3"><div class="inline-flex items-center rounded-full border border-line bg-light p-1"><form method="post"><?=csrf()?><input type="hidden" name="id" value="<?=$p['id']?>"><button name="act" value="dec" class="h-8 w-8 rounded-full text-lg hover:bg-white" aria-label="Decrease quantity">−</button></form><span class="w-8 text-center text-sm font-semibold"><?=$p['qty']?></span><form method="post"><?=csrf()?><input type="hidden" name="id" value="<?=$p['id']?>"><button name="act" value="inc" class="h-8 w-8 rounded-full text-lg hover:bg-white" aria-label="Increase quantity">+</button></form></div><b class="text-lg text-[#C978A1]"><?=money($p['qty']*$p['price'])?></b></div></div></div></article><?php endforeach ?>
<form method="post" class="<?=card('p-4')?>"><?=csrf()?><input type="hidden" name="act" value="note"><label class="text-sm font-semibold" for="order-note">Special instructions <span class="font-normal text-mute">(optional)</span></label><textarea id="order-note" class="<?=INP?> mt-2 min-h-20 resize-y" name="note" maxlength="300" placeholder="Less sugar, extra ice, or anything our team should know…"><?=e($_SESSION['note']??'')?></textarea><button class="<?=BTN?> mt-3 !px-4 !py-2 text-sm">Save note</button></form>
</div>
<aside class="<?=card('p-5 sm:p-6')?> lg:sticky lg:top-24"><p class="text-xs font-semibold uppercase tracking-[.2em] text-mute">Order summary</p><div class="mt-4 flex justify-between text-sm text-mute"><span>Subtotal</span><span><?=money($sub)?></span></div><div class="my-4 border-t border-line"></div><div class="flex items-end justify-between gap-3"><span class="font-semibold">Total</span><b class="text-2xl text-[#C978A1]"><?=money($sub)?></b></div><p class="mt-2 text-xs leading-5 text-mute">Taxes and service charges, if applicable, are calculated at checkout.</p><a href="<?=BASE?>/checkout.php" class="<?=BTN?> mt-5 block w-full text-center">Continue to checkout</a></aside>
</div>
<?php endif; ?></div><?php foot();
