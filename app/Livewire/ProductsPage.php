<?php

namespace App\Livewire;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Products - Store New')]

class ProductsPage extends Component
{

    use WithPagination;

    public $selected_categories = [];
    public $selected_brands = [];
    public $selected_status_is_featured;
    public $selected_status_on_sale;

    public function render()
    {
        $brands = Brand::where('is_active', 1)->get();
        $categories = Category::where('is_active', 1)->get();
        $products = Product::where('is_active', 1);

        if(!empty($this->selected_categories)){
            $products->whereIn('category_id', $this->selected_categories);
        }

        if(!empty($this->selected_brands)){
            $products->whereIn('brand_id', $this->selected_brands);
        }

        if($this->selected_status_is_featured){
            $products->where('is_featured', 1);
        }

        if($this->selected_status_on_sale){
            $products->where('on_sale', 1);
        }

        return view('livewire.products-page',
            [
                'brands' => $brands,
                'categories' => $categories,
                'products' => $products->paginate(10)
            ]);
    }
}
