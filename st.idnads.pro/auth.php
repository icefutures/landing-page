<?php
session_start();

// BELUM LOGIN
if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit;
}
