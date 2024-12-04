<?php

namespace App\Repositories\Contracts; 

interface ProductRepositoryInterface
{

    public function getPopularProducts($limit);

    public function getAllNewProducts();

    public function find($id);

    public function getPrice($ticketId);

    public function searchByName(string $keyword);
}