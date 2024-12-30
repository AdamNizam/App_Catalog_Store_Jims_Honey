<?php

namespace App\Livewire;

use App\Models\Product;
use App\Services\OrderService;
use Livewire\Component;

class OrderForm extends Component
{
    public Product $product;
    public $orderData;
    public $subTotalAmount;
    public $promoCode = null;
    public $promoCodeId = null;
    public $quantity = 1;
    public $discount = 0;
    public $grandTotalAmount;
    public $totalDiscountAmount = 0;
    public $name;
    public $email;

    protected $orderService;

    public function boot(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function mount(Product $product, $orderData)
    {
        $this->product = $product;
        $this->orderData = $orderData;
        $this->subTotalAmount = $product->price;
        $this->grandTotalAmount = $product->price;
    }

    public function updatedQuantity()
    {
        $this->validateOnly('quantity',
        ['quantity' => 'required|integer|min:1|max:'. $this->product->stock],
        ['quantity.max' => 'Stock Tidak Tersedia']
        );
        $this->calculateTotal();
    }

    public function calculateTotal(): void
    {
        $this->subTotalAmount = $this->product->price * $this->quantity;
        $this->grandTotalAmount = $this->subTotalAmount - $this->discount;
    }

    public function incrementQuantity()
     {
        if($this->quantity < $this->product->stock){
            $this->quantity++;
            $this->calculateTotal();
        }
    }

    public function decrementQuantity()
    {
        if($this->quantity > 1){
            $this->quantity--;
            $this->calculateTotal();
        }
    }

    public function updatedPromoCode()
    {
        $this->applyPromoCode();
    }

    public function applyPromoCode()
    {

        if(!$this->promoCode) {
            $this->resetDiscount();
            return;
        }

        $result = $this->orderService->applyPromoCode($this->promoCode, $this->subTotalAmount);

        if(isset($result['error'])) {
            session()->flash('error', $result['error']);
            $this->resetDiscount();
        }else {
            session()->flash('message', 'Kode Promo Tersedia, yay!');
            $this->discount = $result['discount'];
            $this->calculateTotal();
            $this->promoCodeId = $result['promoCodeId'];
            $this->totalDiscountAmount = $result['discount'];
        }

    }

    protected function resetDiscount()
    {
        $this->discount = 0;
        $this->calculateTotal();
        $this->promoCode = null;
        $this->totalDiscountAmount = 0;
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'quantity' => 'required|integer|min:1|max:'. $this->product->stock,
        ];
    }

    protected function gatherBookingData(array $validatedData) : array
    {
        return [
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'grand_total_amount' => $this->grandTotalAmount,
            'sub_total_amount' => $this->subTotalAmount,
            'total_discount_amount' => $this->totalDiscountAmount,
            'discount' => $this->discount,
            'promo_code' => $this->promoCode,
            'promo_code_id' => $this->promoCodeId,
            'quantity' => $this->quantity
        ];
    }

    public function submit()
    {
        $validatedData = $this->validate();
        $bookingData = $this->gatherBookingData($validatedData);
        $this->orderService->updateCustomerData($bookingData);
        return redirect()->route('front.customer_data');
    }

    public function render()
    {
        return view('livewire.order-form');
    }
}
