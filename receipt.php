<?php
require 'includes/layout.php'; require_role(['server','admin']);
$no=trim($_GET['order']??'');
if(!$no){ http_response_code(400); die('Order ID is required.'); }
$s=db()->prepare('SELECT o.*,u.name customer FROM orders o JOIN users u ON u.id=o.customer_id WHERE o.order_no=? LIMIT 1'); $s->execute([$no]); $o=$s->fetch();
if(!$o){ http_response_code(404); die('Order not found.'); }
$i=db()->prepare('SELECT * FROM order_items WHERE order_id=?'); $i->execute([$o['id']]); $items=$i->fetchAll();
$nickname=$o['customer']; if(preg_match('/^Customer nickname:\s*(.+)$/m',$o['notes']??'',$m)) $nickname=trim($m[1]);
head('Receipt'); ?>
<style>
@media print{body{background:#fff!important}.no-print{display:none!important}main{padding:0!important;max-width:none!important}.receipt{border:0!important;box-shadow:none!important;width:80mm!important;margin:0 auto!important}}
.receipt{max-width:23rem;margin:0 auto}
</style>
<div class="no-print mb-4 flex flex-wrap items-center justify-between gap-3"><a href="<?=BASE?>/server/board.php" class="text-sm font-semibold text-[#C978A1]">← Back to order board</a><button type="button" onclick="window.print()" class="<?=BTN?>">Print receipt</button></div>
<article class="<?=card('receipt p-6')?>"><div class="text-center border-b border-dashed border-line pb-4"><img src="<?=BASE?>/assets/images/logo/logo.jpg" class="mx-auto h-16 w-16 rounded-full object-cover" alt="Galleta Café"><h1 class="mt-2 text-xl font-semibold">Galleta Café</h1><p class="text-xs text-mute"><?=e(setting('tagline','Fresh coffee, sweet bites, cozy moments.'))?></p></div>
<div class="border-b border-dashed border-line py-4 text-sm"><div class="flex justify-between gap-3"><span>Order</span><b><?=e($o['order_no'])?></b></div><div class="flex justify-between gap-3"><span>Nickname</span><b><?=e($nickname)?></b></div><div class="flex justify-between gap-3"><span>Date</span><span><?=date('M j, Y g:i A',strtotime($o['created_at']))?></span></div><div class="flex justify-between gap-3"><span>Type</span><span><?=e($o['order_type'])?></span></div></div>
<div class="border-b border-dashed border-line py-4"><?php foreach($items as $x): ?><div class="flex justify-between gap-3 py-1 text-sm"><span><?=$x['qty']?> × <?=e($x['product_name'])?></span><span><?=money($x['price']*$x['qty'])?></span></div><?php endforeach ?></div>
<div class="space-y-1 py-4 text-sm"><div class="flex justify-between"><span>Subtotal</span><span><?=money($o['subtotal'])?></span></div><div class="flex justify-between"><span>Service charge</span><span><?=money($o['service_charge'])?></span></div><div class="flex justify-between"><span>Tax</span><span><?=money($o['tax'])?></span></div><div class="mt-2 flex justify-between border-t border-line pt-2 text-lg font-semibold"><span>Total</span><span><?=money($o['total'])?></span></div><div class="flex justify-between pt-1 text-xs text-mute"><span>Payment</span><span><?=e($o['payment_method'])?> · <?=e($o['payment_status'])?></span></div></div>
<?php if($o['notes']): ?><div class="border-t border-dashed border-line pt-3 text-xs"><b>Notes</b><p class="mt-1 whitespace-pre-line text-mute"><?=e(preg_replace('/^Customer nickname:.+\n?/','',$o['notes']))?></p></div><?php endif ?><p class="border-t border-dashed border-line pt-4 text-center text-xs text-mute">Thank you for visiting Galleta Café.</p></article>
<?php foot();
