<?php

namespace App\Services;

use App\Repositories\Contracts\CategoryRepositoryInterface;
use App\Repositories\Contracts\ProductRepositoryInterface;

class FrontService
{
    protected $categoryRepository;
    protected $productRepository;

    public function __construct(
        ProductRepositoryInterface $productRepository,
        CategoryRepositoryInterface $categoryRepository )
    {
        $this->categoryRepository = $categoryRepository;
        $this->productRepository = $productRepository;
    }

    public function searchProducts(string $keyword) {

        return $this->productRepository->searchByName($keyword);
        
    }

    public function getFrontPageData() {

        $categories = $this->categoryRepository->getAllCategories();
        $popularProducts = $this->productRepository->getPopularProducts(8);
        $newProducts = $this->productRepository->getAllNewProducts();

        return compact('categories', 'popularProducts', 'newProducts');
        
    }

}

