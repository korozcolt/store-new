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

    #[Url()]
    public $selected_categories = [];
    #[Url()]
    public $selected_brands = [];
    #[Url()]
    public $selected_status_is_featured;
    #[Url()]
    public $selected_status_on_sale;
    #[Url()]
    public $price_range = 100000;
    #[Url()]
    public $sort = 'latest';

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

        if($this->price_range){
            $products->whereBetween('price', [0, $this->price_range]);
        }

        if($this->sort == 'latest'){
            $products->latest();
        }

        if($this->sort == 'price'){
            $products->orderBy('price');
        }


        return view('livewire.products-page',
            [
                'brands' => $brands,
                'categories' => $categories,
                'products' => $products->paginate(10)
            ]);
    }
}
