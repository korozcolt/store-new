<?php

namespace App\Helpers;

use App\Models\Product;
use Illuminate\Support\Facades\Cookie;
use App\Helpers\SiteSetting as SiteSettingHelper;

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
            $cart_items[$existing_item]['subtotal_amount'] = $cart_items[$existing_item]['quantity'] * $cart_items[$existing_item]['unit_amount'];
            $cart_items[$existing_item]['tax_amount'] = SiteSettingHelper::getTaxes($cart_items[$existing_item]['subtotal_amount']);
            $cart_items[$existing_item]['total_amount'] = $cart_items[$existing_item]['subtotal_amount'] + $cart_items[$existing_item]['tax_amount'];
        }else{
            $product = Product::where('id', $product_id)->first();
            if($product){
                $unit_amount = $product->price < $product->sale_price ? $product->sale_price : $product->price;
                $cart_items[] = [
                    'product_id' => $product->id,
                    'name' => $product->name,
                    'images' => $product->images[0] ?? '',
                    'quantity' => 1,
                    'unit_amount' => $unit_amount,
                    'subtotal_amount' => $unit_amount,
                    'tax_amount' => SiteSettingHelper::getTaxes($unit_amount),
                    'total_amount' => $unit_amount + SiteSettingHelper::getTaxes($unit_amount),
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
            $cart_items[$existing_item]['subtotal_amount'] = $cart_items[$existing_item]['quantity'] * $cart_items[$existing_item]['unit_amount'];
            $cart_items[$existing_item]['tax_amount'] = SiteSettingHelper::getTaxes($cart_items[$existing_item]['subtotal_amount']);
            $cart_items[$existing_item]['total_amount'] = $cart_items[$existing_item]['subtotal_amount'] + $cart_items[$existing_item]['tax_amount'];
        }else{
            $product = Product::where('id', $product_id)->first();
            if($product){
                $unit_amount = $product->price < $product->sale_price ? $product->sale_price : $product->price;
                $cart_items[] = [
                    'product_id' => $product->id,
                    'name' => $product->name,
                    'images' => $product->images,
                    'quantity' => $quantity,
                    'subtotal_amount' => $unit_amount * $quantity,
                    'tax_amount' => SiteSettingHelper::getTaxes($unit_amount * $quantity),
                    'total_amount' => ($unit_amount * $quantity) + SiteSettingHelper::getTaxes($unit_amount * $quantity),
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
                $cart_items[$key]['quantity']++;
                $cart_items[$key]['subtotal_amount'] = $cart_items[$key]['quantity'] * $cart_items[$key]['unit_amount'];
                $cart_items[$key]['tax_amount'] = SiteSettingHelper::getTaxes($cart_items[$key]['subtotal_amount']);
                $cart_items[$key]['total_amount'] = $cart_items[$key]['subtotal_amount'] + $cart_items[$key]['tax_amount'];
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
                    $cart_items[$key]['quantity']--;
                    $cart_items[$key]['subtotal_amount'] = $cart_items[$key]['quantity'] * $cart_items[$key]['unit_amount'];
                    $cart_items[$key]['tax_amount'] = SiteSettingHelper::getTaxes($cart_items[$key]['subtotal_amount']);
                    $cart_items[$key]['total_amount'] = $cart_items[$key]['subtotal_amount'] + $cart_items[$key]['tax_amount'];
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

    // Calculamos el subtotal sumando los subtotal_amount de cada item
    static public function calculateSubtotal($cart_items) {
        return array_sum(array_column($cart_items, 'subtotal_amount'));
    }

    // Calculamos los impuestos sumando los tax_amount de cada item
    static public function calculateTaxes($cart_items) {
        return array_sum(array_column($cart_items, 'tax_amount'));
    }
}
