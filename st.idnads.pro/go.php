<?php
$conn = new mysqli(
  "localhost",
  "idnafevn_wauser",          // DB USER
  "z0cxwM[igKg)",  // DB PASSWORD
  "idnafevn_amz_idn"   // DB NAME (SESUIKAN DENGAN DATABASE TEMPAT wa_list)
);

if ($conn->connect_error) die("DB Error");

// ambil sapaan dari admin
$g = $conn->query("SELECT greeting FROM settings WHERE id=1")
          ->fetch_assoc();
$greeting = $g['greeting'] ?? '';

// ambil WA aktif
$q = $conn->query("SELECT * FROM wa_list WHERE active=1");
$list = [];
while ($r = $q->fetch_assoc()) $list[] = $r;

if (!$list) die("No active WA");

$pick = $list[array_rand($list)];

// hit counter
$conn->query("UPDATE wa_list SET hit=hit+1 WHERE id={$pick['id']}");

// redirect dengan sapaan global
$text = $greeting ? '?text='.urlencode($greeting) : '';
header("Location: https://wa.me/{$pick['phone']}{$text}");
exit;