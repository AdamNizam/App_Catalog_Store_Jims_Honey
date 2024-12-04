<?php

namespace App\Repositories;

use App\Models\PromoCode;
use App\Repositories\Contracts\CodePromoRepositoryInterface;

class CodePromoRepository implements CodePromoRepositoryInterface
{
    public function getAllPromoCode(){

        return PromoCode::latest()->get();

    }

    public function findByCode(string $code){

        return PromoCode::where('code', $code)->first();

    }
}