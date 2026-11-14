<?php require 'includes/layout.php';
if($_SERVER['REQUEST_METHOD']==='POST'){ check_csrf();
 $n=trim($_POST['name']); $em=filter_var(trim($_POST['email']),FILTER_VALIDATE_EMAIL); $pw=$_POST['password'];
 if(!$n||!$em||strlen($pw)<6) flash('Enter a name, valid email and a password of 6+ characters.','err');
 else try{ db()->prepare("INSERT INTO users(name,email,phone,password,role) VALUES(?,?,?,?, 'customer')")->execute([$n,$em,trim($_POST['phone']),password_hash($pw,PASSWORD_DEFAULT)]); flash('Account created. Please login.'); go('login.php'); }
 catch(PDOException $x){ flash('That email is already registered.','err'); } }
head('Register'); ?>
<form method="post" class="<?=card('max-w-sm mx-auto p-6 space-y-3')?>"><h1 class="text-xl font-semibold">Create account</h1><?=csrf()?>
<input class="<?=INP?>" name="name" placeholder="Full name" required><input class="<?=INP?>" name="email" type="email" placeholder="Email" required>
<input class="<?=INP?>" name="phone" placeholder="Phone"><input class="<?=INP?>" name="password" type="password" placeholder="Password (6+ chars)" required>
<button class="<?=BTN?> w-full">Register</button></form><?php foot();
