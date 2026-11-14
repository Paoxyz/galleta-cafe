<?php require 'includes/layout.php'; $no=$_SESSION['guest_order_no']??''; $nickname=$_SESSION['guest_nickname']??''; unset($_SESSION['guest_order_no'],$_SESSION['guest_nickname']); if(!$no) go('menu.php'); head('Order Confirmed'); ?>
<div class="<?=card('max-w-xl mx-auto p-8 text-center')?>">
<div class="text-5xl mb-4">✓</div><h1 class="text-2xl font-semibold">Order received, <?=e($nickname)?>!</h1>
<p class="text-mute mt-2">When your order is ready, our team will call the nickname you provided.</p>
<p class="text-sm text-mute mt-4">Your order number is:</p><p class="text-2xl font-semibold mt-1"><?=e($no)?></p>
<div class="flex flex-wrap justify-center gap-3 mt-6"><a href="<?=BASE?>/orders.php?order=<?=rawurlencode($no)?>" class="<?=BTN?>">View my order</a><a href="<?=BASE?>/menu.php" class="px-5 py-2.5 rounded-full border border-line font-medium hover:bg-soft">Browse menu again</a></div>
</div><?php foot();
