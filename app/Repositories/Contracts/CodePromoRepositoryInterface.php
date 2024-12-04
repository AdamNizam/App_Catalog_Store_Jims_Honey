<?php

namespace App\Repositories\Contracts; 

interface CodePromoRepositoryInterface
{
    public function getAllPromoCode();

    public function findByCode(string $code);

}