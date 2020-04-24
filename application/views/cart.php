<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div class="container">
    <div class="row">
        <div class="col-12">
            <h1>Shopping cart</h1>
        </div>
        <div class="col-12">
            <?if(!isset($items) || count($items) == 0):?>
                <div class="alert alert-warning">You cart is empty</div>
            <?else:?>

            <?foreach ($items as $id => $item) :?>
                <div class="row">
                    <div class="col-2">
                        <img src="<?=base_url('uploads/' . $item->image)?>" alt="" class="img-thumbnail">
                    </div>
                    <div class="col-6"><h3><?=$item->title?></h3></div>
                    <div class="col-3"><h3><?=$item->price?></h3></div>
                    <div class="col-1">
                        <a class="btn btn-danger delete" href="<?=base_url() . 'cart?del=' . ($id+1)?>">
                            <span class="oi oi-trash"></span>
                        </a>
                    </div>
                </div>
            <?endforeach;?>
                <hr>
                <div class="row">
                    <div class="col-8"><h2>Total:</h2></div>
                    <div class="col-4"><h2><?=$total?> USD</h2></div>
                </div>
                <div class="row">
                    <div class="col-4 offset-md-8">
                        <a class="btn btn-success btn-block" href="<?=base_url('checkout')?>">Checkout</a>
                    </div>
                </div>

            <?endif;?>
        </div>
    </div>
</div>