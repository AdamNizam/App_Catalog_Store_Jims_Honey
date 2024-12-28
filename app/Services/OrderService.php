<?php

namespace App\Services;

use App\Models\ProductTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Repositories\Contracts\OrderRepositoryInterface;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use App\Repositories\Contracts\CodePromoRepositoryInterface;

class OrderService{

    protected $categoryRepository; 
    protected $promoCodeRepository;
    protected $orderRepository;
    protected $productRepository;

    public function __construct(
        CodePromoRepositoryInterface $promoCodeRepository,
        CategoryRepositoryInterface $categoryRepository,
        OrderRepositoryInterface $orderRepository,
        ProductRepositoryInterface $productRepository
    ) {
        $this->categoryRepository = $categoryRepository;
        $this->productRepository = $productRepository;
        $this->orderRepository = $orderRepository;
        $this->promoCodeRepository = $promoCodeRepository;

    }

    public function beginOrder(array $data) {

        $orderData = [
            'product_size' => $data['product_size'],
            'size_id' => $data['size_id'],
            'product_id' => $data['product_id']
        ]; 

        $this->orderRepository->saveToSession($orderData);        
    }

    public function getMyOrderDetails(array $validated) {

        return $this->orderRepository->finByTrxIdAndPhoneNumber(
            $validated['booking_trx_id'],
            $validated['phone']
        );
        
    }

    public function getOrderDetails(){

        $orderData = $this->orderRepository->getOrderDataFromSession();
        $product = $this->productRepository->find($orderData['product_id']);

        $quantity = isset($orderData['quantity']) ? $orderData['quantity'] : 1;
        $subTotalAmount = $product->price * $quantity;

        $taxtRate = 0;
        $totalTaxt = $subTotalAmount * $taxtRate;

        $grandTotalAmount = $subTotalAmount + $totalTaxt - ($orderData['discount'] ?? 0) ;

        $orderData['sub_total_amount'] = $subTotalAmount;
        $orderData['total_tax'] = $totalTaxt;
        $orderData['grand_total_amount'] = $grandTotalAmount;

        return compact('orderData', 'product');
    }

    public function applyPromoCode(string $code, int $subTotalAmount) {

        $promo = $this->promoCodeRepository->findByCode($code);

        if($promo){
            $discount = $promo->discount_amount;
            $grandTotalAmount = $subTotalAmount - $discount;
            $promoCodeId = $promo->id;

            return [
                'discount' => $discount,
                'grandTotalAmount' => $grandTotalAmount,
                'promoCodeId' => $promoCodeId ];
        }

        return ['error' => 'Kode promo tidak tersedia!'];
        
    }

    public function saveBookingTransaction(array $data) {

        $this->orderRepository->saveToSession($data);
        
    }

    public function updateCustomerData(array $data) {

        $this->orderRepository->updateSessionData($data);
        
    }

    public function paymentConfirm(array $validated) {

        $orderData = $this->orderRepository->getOrderDataFromSession();

        // Log untuk memastikan data dari session tidak kosong
        Log::info('Order Data from Session:', $orderData);

        // Jika data dari session kosong, log error dan kembalikan null
        if (empty($orderData)) {
            Log::error('Order data is missing from session.');
            session()->flash('error', 'Order data is missing. Please try again.');
            return null;
        }

        $productTransactionId = null;

        try {
            DB::transaction( function() use ($validated, &$productTransactionId, $orderData) {

                if(isset($validated['proof'])) {
                    $proofPath = $validated['proof']->store('proofs', 'public');
                    Log::info('Proof uploaded to path:', ['path' => $proofPath]);
                    $validated['proof'] = $proofPath;
                }

                $validated['name'] = $orderData ['name'];
                $validated['email'] = $orderData ['email'];
                $validated['phone'] = $orderData ['phone'];
                $validated['address'] = $orderData ['address'];
                $validated['post_code'] = $orderData ['post_code'];
                $validated['city'] = $orderData ['city'];
                $validated['quantity'] = $orderData ['quantity'];
                $validated['sub_total_amount'] = $orderData ['sub_total_amount'];
                $validated['grand_total_amount'] = $orderData ['grand_total_amount'];
                $validated['discount_amount'] = $orderData ['total_discount_amount'];
                $validated['promo_code_id'] = $orderData ['promo_code_id'];
                $validated['product_id'] = $orderData ['product_id'];
                $validated['product_size'] = $orderData ['product_id'];
                $validated['is_paid'] = false;
                $validated['booking_trx_id'] = ProductTransaction::generateUniqueTrxId();

                $newTransaction = $this->orderRepository->createTransaction($validated);

                $productTransactionId = $newTransaction->id;

                $this->orderRepository->clearSession();
            });

        } catch (\Exception $e) {
           Log::error('Error in payment confirmation: ' . $e->getMessage());
           session()->flash('error', $e->getMessage());
           return null;
        }   
        
        return $productTransactionId;
    }

}