<?php
require 'includes/layout.php';

$u=user();
$featured=db()->query("SELECT p.*,c.name cat FROM products p JOIN categories c ON c.id=p.category_id WHERE p.available=1 ORDER BY CASE p.name WHEN 'Spanish Latte' THEN 1 WHEN 'Caramel Macchiato' THEN 2 WHEN 'Iced Americano' THEN 3 WHEN 'Matcha Latte' THEN 4 WHEN 'Strawberry Frappe' THEN 5 WHEN 'Chocolate Croissant' THEN 6 ELSE 99 END,p.featured DESC,p.best_seller DESC,p.name LIMIT 6")->fetchAll();
$photos=[
 'Spanish Latte'=>'https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?auto=format&fit=crop&w=900&q=85',
 'Caramel Macchiato'=>'https://images.unsplash.com/photo-1461023058943-07fcbe16d735?auto=format&fit=crop&w=900&q=85',
 'Iced Americano'=>'https://images.unsplash.com/photo-1517701604599-bb29b565090c?auto=format&fit=crop&w=900&q=85',
 'Matcha Latte'=>'https://images.unsplash.com/photo-1536256263959-770b48d82b0a?auto=format&fit=crop&w=900&q=85',
 'Strawberry Frappe'=>'https://images.unsplash.com/photo-1551024709-8f23befc6f87?auto=format&fit=crop&w=900&q=85',
 'Chocolate Croissant'=>'https://images.unsplash.com/photo-1555507036-ab1f4038808a?auto=format&fit=crop&w=900&q=85'
];
$heroPhoto='https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?auto=format&fit=crop&w=1200&q=85';
$promoPhoto='https://images.unsplash.com/photo-1513558161293-caad8c7e9e8d?auto=format&fit=crop&w=1000&q=85';
$aboutPhoto='https://images.unsplash.com/photo-1445116572660-236099ec97a0?auto=format&fit=crop&w=1200&q=85';
function home_image($p,$fallback,$class,$alt,$loading='lazy'){
 $src=($p['image']??'') && is_file(__DIR__.'/assets/uploads/'.$p['image'])?BASE.'/assets/uploads/'.e($p['image']):$fallback;
 return '<img src="'.e($src).'" onerror="this.onerror=null;this.src=\''.e($fallback).'\';" class="'.e($class).'" alt="'.e($alt).'" loading="'.$loading.'">';
}
head('Home'); ?>
<style>
.editorial-display{font-family:Georgia,'Times New Roman',serif;letter-spacing:-.04em}
.editorial-shadow{box-shadow:0 24px 70px rgba(110,62,87,.14)}
</style>

<section class="relative isolate min-h-[34rem] sm:min-h-[40rem] lg:min-h-[36rem] flex items-center overflow-hidden rounded-[2rem] bg-[#FFF8FB] px-5 py-10 sm:px-10 lg:px-16">
<div class="absolute -left-20 -top-24 -z-10 h-72 w-72 rounded-[48%_52%_65%_35%] bg-soft/80"></div>
<div class="absolute right-8 top-10 -z-10 h-16 w-16 rounded-full border border-brand/60"></div>
<div class="grid w-full items-center gap-10 lg:grid-cols-[.9fr_1.1fr]">
<div class="relative z-10 max-w-xl">
<p class="mb-5 text-xs font-semibold uppercase tracking-[.28em] text-mute">Galleta Café · Since 2024</p>
<h1 class="editorial-display text-5xl leading-[.94] text-[#352B32] sm:text-7xl lg:text-[6.5rem]">Brewed to<br><span class="text-[#C978A1]">Perfection.</span></h1>
<p class="mt-7 max-w-md text-base leading-7 text-[#82747D] sm:text-lg"><?=e(setting('tagline','Fresh coffee, sweet bites, cozy moments.'))?> Discover handcrafted drinks, fresh treats, and a cozy corner made for lingering.</p>
<div class="mt-8 flex flex-wrap items-center gap-3"><a href="<?=BASE?>/menu.php" class="<?=BTN?> !px-6">Order now</a><a href="#about" class="rounded-full border border-[#F2D5E3] bg-white px-6 py-2.5 font-medium text-[#352B32] transition hover:bg-soft">Discover more <span aria-hidden="true">↗</span></a></div>
</div>
<div class="relative flex min-h-[20rem] items-center justify-center lg:min-h-[34rem]">
<div class="absolute right-0 top-1/2 h-[19rem] w-[15rem] -translate-y-1/2 rounded-[48%_52%_45%_55%] bg-soft sm:h-[28rem] sm:w-[23rem] lg:h-[34rem] lg:w-[27rem]"></div>
<div class="relative h-[22rem] w-[17rem] rotate-3 overflow-hidden rounded-[48%_52%_45%_55%] border-[10px] border-white editorial-shadow sm:h-[30rem] sm:w-[24rem] lg:h-[37rem] lg:w-[29rem]">
<?=home_image(['image'=>''],$heroPhoto,'h-full w-full object-cover','Latte art and coffee at Galleta Café','eager')?>
</div>
<div class="absolute bottom-1 left-0 hidden rounded-full bg-white px-4 py-3 text-xs font-semibold tracking-wide text-[#82747D] shadow sm:block">Slow mornings welcome</div>
</div>
</div>
</section>

<section class="grid items-center gap-8 py-16 sm:py-24 lg:grid-cols-[.9fr_1.1fr] lg:gap-16">
<div class="relative mx-auto w-full max-w-md lg:mx-0">
<div class="absolute -bottom-5 -left-5 h-24 w-24 rounded-full bg-soft"></div>
<div class="relative h-[22rem] overflow-hidden rounded-[52%_48%_38%_62%] border-8 border-white editorial-shadow sm:h-[28rem]">
<?=home_image(['image'=>''],$promoPhoto,'h-full w-full object-cover','Handcrafted iced coffee','lazy')?>
</div>
</div>
<div class="max-w-xl">
<p class="text-xs font-semibold uppercase tracking-[.25em] text-mute">A little sweetness</p>
<h2 class="editorial-display mt-3 text-4xl leading-tight text-[#352B32] sm:text-6xl">Something special,<br><span class="text-[#C978A1]">just for you.</span></h2>
<p class="mt-5 leading-7 text-[#82747D]">Refresh your day with handcrafted beverages and freshly prepared treats. Every recipe is made to bring a little happiness to every moment, whether you are catching up with friends or taking a quiet break.</p>
<a href="<?=BASE?>/menu.php" class="mt-7 inline-flex items-center gap-2 font-semibold text-[#C978A1] hover:text-[#352B32]">Explore our menu <span aria-hidden="true">→</span></a>
</div>
</section>

<section class="rounded-[2rem] bg-white px-4 py-14 sm:px-8 sm:py-20" id="favorites">
<div class="mb-8 flex flex-col justify-between gap-3 sm:flex-row sm:items-end"><div><p class="text-xs font-semibold uppercase tracking-[.25em] text-mute">From our counter</p><h2 class="editorial-display mt-2 text-4xl text-[#352B32] sm:text-5xl">Our customer favorites</h2></div><p class="max-w-xs text-sm leading-6 text-[#82747D]">Discover the flavors everyone loves, made fresh for your next café moment.</p></div>
<div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
<?php foreach($featured as $p): $photo=$photos[$p['name']]??$heroPhoto; ?><article class="group overflow-hidden rounded-3xl border border-[#F2D5E3] bg-[#FFF8FB] transition hover:-translate-y-1 hover:shadow-xl">
<div class="h-56 overflow-hidden"><?=home_image($p,$photo,'h-full w-full object-cover transition duration-500 group-hover:scale-105',$p['name'])?></div>
<div class="p-5"><div class="flex items-start justify-between gap-3"><div><h3 class="font-semibold text-[#352B32]"><?=e($p['name'])?></h3><p class="mt-1 text-sm leading-5 text-[#82747D]"><?=e($p['description'])?></p></div><b class="shrink-0 text-[#C978A1]"><?=money($p['price'])?></b></div>
<?php if($p['available']): ?><form method="post" action="cart.php" class="mt-5"><?=csrf()?><input type="hidden" name="act" value="add"><input type="hidden" name="id" value="<?=$p['id']?>"><button class="w-full rounded-full border border-[#EFA6C8] py-2.5 text-sm font-semibold text-[#352B32] transition hover:bg-brand">Add to cart</button></form><?php endif ?></div>
</article><?php endforeach ?>
</div>
<div class="mt-8 text-center"><a href="<?=BASE?>/menu.php" class="font-semibold text-[#C978A1] hover:text-[#352B32]">See the full menu <span aria-hidden="true">→</span></a></div>
</section>

<section id="about" class="grid items-center gap-10 py-16 sm:py-24 lg:grid-cols-[1.1fr_.9fr] lg:gap-20">
<div class="order-2 max-w-xl lg:order-1"><p class="text-xs font-semibold uppercase tracking-[.25em] text-mute">Our story</p><h2 class="editorial-display mt-3 text-4xl leading-tight text-[#352B32] sm:text-6xl">A little place,<br><span class="text-[#C978A1]">a lot of love.</span></h2><p class="mt-6 leading-7 text-[#82747D]">Galleta Café is a welcoming neighborhood café built around good coffee, honest food, and the people who make every visit special. We pour each cup with care and create a space where you can slow down, connect, and feel at home.</p><a href="<?=BASE?>/menu.php" class="mt-7 inline-flex rounded-full bg-[#352B32] px-6 py-3 font-medium text-white transition hover:bg-[#C978A1]">Visit us through the menu</a></div>
<div class="relative order-1 lg:order-2"><div class="absolute -right-3 -top-5 h-20 w-20 rounded-full border border-brand"></div><div class="h-[24rem] overflow-hidden rounded-[38%_62%_55%_45%] border-8 border-white editorial-shadow sm:h-[32rem]"><?=home_image(['image'=>''],$aboutPhoto,'h-full w-full object-cover','The cozy interior of Galleta Café','lazy')?></div></div>
</section>

<section class="grid gap-4 border-y border-[#F2D5E3] py-12 sm:grid-cols-3 sm:gap-8">
<div><div class="mb-3 text-2xl text-[#C978A1]">✦</div><h3 class="font-semibold text-[#352B32]">Freshly brewed</h3><p class="mt-2 text-sm leading-6 text-[#82747D]">Quality coffee and handcrafted drinks prepared for you.</p></div>
<div><div class="mb-3 text-2xl text-[#C978A1]">♡</div><h3 class="font-semibold text-[#352B32]">Made with love</h3><p class="mt-2 text-sm leading-6 text-[#82747D]">Carefully prepared food and beverages in every order.</p></div>
<div><div class="mb-3 text-2xl text-[#C978A1]">◌</div><h3 class="font-semibold text-[#352B32]">Cozy atmosphere</h3><p class="mt-2 text-sm leading-6 text-[#82747D]">A welcoming place to relax, connect, and stay awhile.</p></div>
</section>

<section class="relative my-16 overflow-hidden rounded-[2rem] bg-soft px-6 py-14 text-center sm:px-10 sm:py-20"><div class="absolute -right-12 -top-20 h-52 w-52 rounded-full bg-white/60"></div><div class="absolute -bottom-24 -left-12 h-48 w-48 rounded-[45%] bg-brand/40"></div><div class="relative"><p class="text-xs font-semibold uppercase tracking-[.25em] text-mute">Your next café moment</p><h2 class="editorial-display mt-3 text-4xl text-[#352B32] sm:text-6xl">Your next favorite cup<br>is waiting.</h2><p class="mx-auto mt-5 max-w-lg text-[#82747D]">Make your day a little sweeter. Explore our menu and order your favorites today.</p><a href="<?=BASE?>/menu.php" class="<?=BTN?> mt-7 inline-block">Order now</a></div></section>

<footer id="contact" class="border-t border-[#F2D5E3] pt-10 text-sm text-[#82747D]"><div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-4"><div><div class="flex items-center gap-2 text-base font-semibold text-[#352B32]"><img src="<?=BASE?>/assets/images/logo/logo.jpg" class="h-9 w-9 rounded-full object-cover" alt="">Galleta Café</div><p class="mt-3 max-w-xs leading-6">Fresh coffee, sweet bites, and cozy moments made with care.</p></div><div><h3 class="font-semibold text-[#352B32]">Explore</h3><a class="mt-3 block hover:text-[#C978A1]" href="#favorites">Favorites</a><a class="mt-2 block hover:text-[#C978A1]" href="#about">About us</a><a class="mt-2 block hover:text-[#C978A1]" href="<?=BASE?>/menu.php">Menu</a></div><div><h3 class="font-semibold text-[#352B32]">Visit us</h3><p class="mt-3 leading-6">Mon–Sun · 8:00 AM–9:00 PM<br>Galleta Café, Philippines</p></div><div><h3 class="font-semibold text-[#352B32]">Say hello</h3><p class="mt-3 leading-6">hello@galleta.cafe<br>Follow along · Instagram · Facebook</p></div></div><div class="mt-10 border-t border-[#F2D5E3] py-5 text-xs">© <?=date('Y')?> Galleta Café. Made for good days.</div></footer>
<?php foot();
