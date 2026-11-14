<?php
// Galleta Café – core config & helpers
date_default_timezone_set('Asia/Manila');
session_start();
const BASE = '/galleta-cafe';            // folder name inside htdocs
const DB_HOST='localhost', DB_NAME='pink_cafe_db', DB_USER='root', DB_PASS='';
function db(): PDO { static $p; return $p ??= new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8mb4',DB_USER,DB_PASS,[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC]); }
function e($s){ return htmlspecialchars((string)$s, ENT_QUOTES); }
function money($n){ return '₱'.number_format((float)$n,2); }
function go($p){ header('Location: '.BASE.'/'.$p); exit; }
function csrf(){ $_SESSION['csrf'] ??= bin2hex(random_bytes(16)); return '<input type="hidden" name="csrf" value="'.$_SESSION['csrf'].'">'; }
function check_csrf(){ if(($_POST['csrf']??'')!==($_SESSION['csrf']??'x')){ http_response_code(419); die('Invalid session token. Go back and retry.'); } }
function user(){ return $_SESSION['user'] ?? null; }
function require_role(array $roles){ $u=user(); if(!$u){ go('login.php'); } if(!in_array($u['role'],$roles)){ http_response_code(403); die('Access denied.'); } }
function flash($m=null,$t='ok'){ if($m){ $_SESSION['flash']=[$m,$t]; return; } $f=$_SESSION['flash']??null; unset($_SESSION['flash']); return $f; }
function setting($k,$d=''){ static $s; $s ??= db()->query('SELECT k,v FROM cafe_settings')->fetchAll(PDO::FETCH_KEY_PAIR); return $s[$k] ?? $d; }
function log_act($a){ $u=user(); db()->prepare('INSERT INTO activity_logs(user_id,action) VALUES(?,?)')->execute([$u['id']??null,$a]); }
function cafe_photo($name){
  $photos=[
    'Spanish Latte'=>'https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?auto=format&fit=crop&w=900&q=85',
    'Caramel Macchiato'=>'https://images.unsplash.com/photo-1461023058943-07fcbe16d735?auto=format&fit=crop&w=900&q=85',
    'Iced Americano'=>'https://images.unsplash.com/photo-1517701604599-bb29b565090c?auto=format&fit=crop&w=900&q=85',
    'Cafe Latte'=>'https://images.unsplash.com/photo-1541167760496-1628856ab772?auto=format&fit=crop&w=900&q=85',
    'Vanilla Latte'=>'https://images.unsplash.com/photo-1570968915860-54d5c301fa9f?auto=format&fit=crop&w=900&q=85',
    'Cappuccino'=>'https://images.unsplash.com/photo-1572442388796-11668a67e53d?auto=format&fit=crop&w=900&q=85',
    'Matcha Latte'=>'https://images.unsplash.com/photo-1536256263959-770b48d82b0a?auto=format&fit=crop&w=900&q=85',
    'Strawberry Milk'=>'https://images.unsplash.com/photo-1553787499-6f9133860278?auto=format&fit=crop&w=900&q=85',
    'Chocolate Milk'=>'https://images.unsplash.com/photo-1544787219-7f47ccb76574?auto=format&fit=crop&w=900&q=85',
    'Blueberry Soda'=>'https://images.unsplash.com/photo-1513558161293-caad8c7e9e8d?auto=format&fit=crop&w=900&q=85',
    'Classic Milk Tea'=>'https://images.unsplash.com/photo-1558857563-b371033873b8?auto=format&fit=crop&w=900&q=85',
    'Wintermelon Milk Tea'=>'https://images.unsplash.com/photo-1525385133512-2f3bdd039054?auto=format&fit=crop&w=900&q=85',
    'Okinawa Milk Tea'=>'https://images.unsplash.com/photo-1558857563-b371033873b8?auto=format&fit=crop&w=900&q=85',
    'Brown Sugar Milk Tea'=>'https://images.unsplash.com/photo-1558857563-b371033873b8?auto=format&fit=crop&w=900&q=85',
    'Java Chip Frappe'=>'https://images.unsplash.com/photo-1572490122747-3968b75cc699?auto=format&fit=crop&w=900&q=85',
    'Strawberry Frappe'=>'https://images.unsplash.com/photo-1551024709-8f23befc6f87?auto=format&fit=crop&w=900&q=85',
    'Cookies and Cream Frappe'=>'https://images.unsplash.com/photo-1579954115545-a95591f28bfc?auto=format&fit=crop&w=900&q=85',
    'Butter Croissant'=>'https://images.unsplash.com/photo-1555507036-ab1f4038808a?auto=format&fit=crop&w=900&q=85',
    'Chocolate Croissant'=>'https://images.unsplash.com/photo-1623334044303-241021148842?auto=format&fit=crop&w=900&q=85',
    'Cinnamon Roll'=>'https://images.unsplash.com/photo-1509365465985-25d11c17e812?auto=format&fit=crop&w=900&q=85',
    'Strawberry Cheesecake'=>'https://images.unsplash.com/photo-1565958011703-44f9829ba187?auto=format&fit=crop&w=900&q=85',
    'Chocolate Cake'=>'https://images.unsplash.com/photo-1578985545062-69928b1d9587?auto=format&fit=crop&w=900&q=85',
    'Blueberry Cheesecake'=>'https://images.unsplash.com/photo-1533134242443-d4fd215305ad?auto=format&fit=crop&w=900&q=85',
    'Chicken Alfredo'=>'https://images.unsplash.com/photo-1473093295043-cdd812d0e601?auto=format&fit=crop&w=900&q=85',
    'Carbonara'=>'https://images.unsplash.com/photo-1555949258-eb67b1ef0ceb?auto=format&fit=crop&w=900&q=85',
    'Chicken Sandwich'=>'https://images.unsplash.com/photo-1528735602780-2552fd46c7af?auto=format&fit=crop&w=900&q=85',
    'French Fries'=>'https://images.unsplash.com/photo-1573080496219-bb080dd4f877?auto=format&fit=crop&w=900&q=85',
    'Nachos'=>'https://images.unsplash.com/photo-1513456852971-30c0b8199d4d?auto=format&fit=crop&w=900&q=85',
    'Mozzarella Sticks'=>'https://images.unsplash.com/photo-1548340748-6d2b7d7da280?auto=format&fit=crop&w=900&q=85'
  ];
  return $photos[$name]??'https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?auto=format&fit=crop&w=900&q=85';
}
function img($p,$class='w-full h-40 object-cover'){
  if($p['image'] && is_file(__DIR__.'/../assets/uploads/'.$p['image'])) return '<img src="'.BASE.'/assets/uploads/'.e($p['image']).'" class="'.e($class).'" alt="'.e($p['name']).'" loading="lazy">';
  $photo=cafe_photo($p['name']);
  return '<img src="'.e($photo).'" onerror="this.onerror=null;this.src=\''.e(cafe_photo('Spanish Latte')).'\';" class="'.e($class).'" alt="'.e($p['name']).'" loading="lazy">';
}
