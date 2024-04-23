<?php

namespace App\Helpers;

use App\Models\Product;
use Illuminate\Support\Facades\Cookie;

class CartManagement{
    //add item to cart
    static public function addItemToCart( $product_id){
        $cart_items = self::getCartItemsFromCookies();
        $existing_item = null;

        foreach($cart_items as $key => $item){
            if(is_array($item) && $item['product_id'] == $product_id){
                $existing_item = $key;
                break;
            }
        }

        if($existing_item !== null){

            $cart_items[$existing_item]['quantity']++;
            $cart_items[$existing_item]['total_amount'] = $cart_items[$existing_item]['quantity'] * $cart_items[$existing_item]['unit_amount'];
        }else{
            $product = Product::where('id', $product_id)->first();
            if($product){
                $cart_items[] = [
                    'product_id' => $product->id,
                    'name' => $product->name,
                    'images' => $product->images[0] ?? '',
                    'quantity' => 1,
                    'unit_amount' => $product->price < $product->sale_price ? $product->sale_price : $product->price,
                    'total_amount' => $product->price < $product->sale_price ? $product->sale_price : $product->price,
                ];
            }
        }

        self::addCartItemToCookie($cart_items);
        return count($cart_items);
    }

    static public function addItemToCarWithQty($product_id, $quantity = 1){
        $cart_items = self::getCartItemsFromCookies();
        $existing_item = null;

        foreach($cart_items as $key => $item){
            if(is_array($item) && $item['product_id'] == $product_id){
                $existing_item = $key;
                break;
            }
        }

        if($existing_item !== null){
            $cart_items[$existing_item]['quantity'] = $quantity;
            $cart_items[$existing_item]['total_amount'] = $cart_items[$existing_item]['quantity'] * $cart_items[$existing_item]['unit_amount'];
        }else{
            $product = Product::where('id', $product_id)->first();
            if($product){
                $cart_items[] = [
                    'product_id' => $product->id,
                    'name' => $product->name,
                    'unit_amount' => $product->price ?? 0,
                    'quantity' => $quantity,
                    'total_amount' => $product->price ?? 0,
                    'images' => $product->images
                ];
            }
        }

        self::addCartItemToCookie($cart_items);
        return count($cart_items);
    }

    //remove item from cart
    static public function removeItemFromCart($product_id){
        $cart_items = self::getCartItemsFromCookies();

        foreach($cart_items as $key => $item){
            if($item['product_id'] == $product_id){
                unset($cart_items[$key]);
            }
        }

        self::addCartItemToCookie($cart_items);

        return $cart_items;
    }

    //add cart item to cookie
    static public function addCartItemToCookie($cart_items){
        Cookie::queue('cart_items', json_encode($cart_items), 60*24*30);
    }

    //clean cart items from cookie
    static public function clearCartItemsFromCookies(){
        Cookie::queue(Cookie::forget('cart_items'));
    }

    //get all cart items from cookie
    static public function getCartItemsFromCookies(){
        $cart_items= json_decode(Cookie::get('cart_items'), true);
        if(!$cart_items){
            $cart_items = [];
        }

        return $cart_items;
    }

    //incremente item quantity\
    static public function incrementQuantityToCartItem($product_id){
        $cart_items = self::getCartItemsFromCookies();

        foreach($cart_items as $key => $item){
            if($item['product_id'] == $product_id){
                $cart_items[$key]['quantity'] ++;
                $cart_items[$key]['total_amount'] = $cart_items[$key]['quantity'] * $cart_items[$key]['unit_amount'];
            }
        }

        self::addCartItemToCookie($cart_items);

        return $cart_items;
    }

    //decrement item quantity
    static public function decrementQuantityToCartItem($product_id){
        $cart_items = self::getCartItemsFromCookies();

        foreach($cart_items as $key => $item){
            if($item['product_id'] == $product_id){
                if($cart_items[$key]['quantity'] > 1){
                    $cart_items[$key]['quantity'] --;
                    $cart_items[$key]['total_amount'] = $cart_items[$key]['quantity'] * $cart_items[$key]['unit_amount'];
                }

            }
        }

        self::addCartItemToCookie($cart_items);

        return $cart_items;
    }

    //calculate grand total
    static public function calculateGrandTotal($cart_items){
        return array_sum(array_column($cart_items, 'total_amount'));
    }
}
