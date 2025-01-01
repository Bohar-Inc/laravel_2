<?php

namespace App\Livewire;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Products Page - Laravel Filament')]
class ProductsPage extends Component
{
    #[Url]
    public $selected_categories=[];

    #[Url]
    public $selected_brands=[];

    #[Url]
    public $featured;

    #[Url]
    public $on_sale;

    #[Url]
    public $price_range=300000;

    use WithPagination;
    public function render()
    {
        $productQuery=Product::query()->where('is_active',1);
        if (!empty($this->selected_brands)) {
            $productQuery->whereIn('brand_id',$this->selected_brands);
        }
        if (!empty($this->selected_categories)) {
            $productQuery->whereIn('category_id',$this->selected_categories);
        }
        if ($this->featured) {
            $productQuery->where('is_featured',1);
        }
        if ($this->on_sale) {
            $productQuery->where('on_sale',1);
        }

        if ($this->price_range) {
            $productQuery->whereBetween('price',[0,$this->price_range]);
        }
        $brands=Brand::where('is_active',1)->get(['id','name','slug']);
        $categories=Category::where('is_active',1)->get(['id','name','slug']);
        return view('livewire.products-page',['products'=>$productQuery->paginate(6),'brands'=>$brands,'categories'=>$categories]);
    }
}
