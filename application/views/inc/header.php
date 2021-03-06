<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Document</title>
    <link rel="stylesheet" href="<?=base_url()?>assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?=base_url()?>assets/css/style.css">
    <link rel="stylesheet" href="<?=base_url()?>assets/css/open-iconic-bootstrap.css">
  </head>
<body>

<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <a class="navbar-brand" href="<?=site_url()?>">Shop</a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>

  <div class="collapse navbar-collapse" id="navbarSupportedContent">
    <ul class="navbar-nav mr-auto">
      <li class="nav-item active">
        <a class="nav-link" href="<?=site_url()?>">Home <span class="sr-only">(current)</span></a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="#">About us</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="#">Contact us</a>
      </li>
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          Categories
        </a>
        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
          <?foreach($categories as $category):?>
            <a class="dropdown-item" href="<?=base_url('category/'.$category->id)?>"><?=$category->title?></a>
          <?endforeach;?>
        </div>
      </li>
    </ul>
    
    <?if(isset($user['logged']) && $user['logged']):?>
      <div class="dropdown">
        <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <?=$user['first_name']?>
        </button>
        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
          <a class="dropdown-item" href="<?=base_url('cart')?>">Shopping cart</a>
        <?if($user['level'] >= 1):?>
          <div class="dropdown-divider"></div>
          <a class="dropdown-item" href="<?=base_url('manager/items')?>">Products</a>
          <a class="dropdown-item" href="<?=base_url('manager/categories')?>">Categories</a>
        <?endif;?>
        <?if($user['level'] == 2):?>
          <a class="dropdown-item" href="<?=base_url('manager/users')?>">Users</a>
        <?endif;?>
          <div class="dropdown-divider"></div>
          <a class="dropdown-item" href="<?=base_url('home/logout')?>">Logout</a>
        </div>
      </div>
    <?else:?>
      <a class="nav-link" id="login" href="<?=base_url('home/login')?>">Enter</a>
    <?endif;?>
    <form class="form-inline my-2 my-lg-0" action="" method="get">
      <input class="form-control mr-sm-2" name="search" type="search" placeholder="Search" aria-label="Search">
      <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Search</button>
    </form>
  </div>
</nav>

<? if(isset($alert) && is_array($alert)):?>
  <div class="container">
    <div class="row">
      <div class="col-6">
        <div class="alert alert-<?=$alert['type']?>"><?=$alert['message']?></div>
      </div>
    </div>
  </div>
<? endif; ?>