<?php

namespace App\Repositories;

use App\Models\Product;
use App\Repositories\Contracts\ProductRepositoryInterface;

class ProductRepository implements ProductRepositoryInterface
{
    public function getPopularProducts ($limit = 4) { //default limit

        Return Product::where('is_popular', true)->latest()->take($limit)->get();
        
    }

    public function searchByName(string $keyword){

        return Product::where('name', 'LIKE', '%' . $keyword . '%')->get();
        
    }

    public function getAllNewProducts() {

        Return Product::latest()->get();        
    }

    public function find($id) {

        Return Product::find($id);       
    }

    public function getPrice($productId) {

        $product = $this->find($productId);
        return $product ? $product->price : 0;        
    }
}
