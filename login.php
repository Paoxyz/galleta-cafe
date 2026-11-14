<?php require 'includes/layout.php';
if($_SERVER['REQUEST_METHOD']==='POST'){ check_csrf();
 $s=db()->prepare('SELECT * FROM users WHERE email=?'); $s->execute([trim($_POST['email'])]); $u=$s->fetch();
 if($u && $u['active'] && password_verify($_POST['password'],$u['password'])){
  session_regenerate_id(true); $_SESSION['user']=['id'=>$u['id'],'name'=>$u['name'],'role'=>$u['role']]; log_act('login');
  go($u['role']==='admin'?'admin/dashboard.php':($u['role']==='server'?'server/board.php':'index.php')); }
 flash('Invalid credentials or inactive account.','err'); }
head('Login'); ?>
<form method="post" class="<?=card('max-w-sm mx-auto p-6 space-y-3')?>"><h1 class="text-xl font-semibold">Welcome back</h1><?=csrf()?>
<input class="<?=INP?>" name="email" type="email" placeholder="Email" required><input class="<?=INP?>" name="password" type="password" placeholder="Password" required>
<button class="<?=BTN?> w-full">Login</button><p class="text-sm text-mute">No account? <a class="underline" href="register.php">Register</a></p></form>
<?php foot();
