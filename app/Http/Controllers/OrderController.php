<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\OrderService;
use App\Models\ProductTransaction;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\StorePaymentRequest;
use App\Http\Requests\StoreCheckBookingRequest;
use App\Http\Requests\StoreCustomerDataRequest;

class OrderController extends Controller
{

    protected $orderService;

    public function __construct(OrderService $orderService) {

        $this->orderService = $orderService;
        
    }

    public function saveOrder(StoreOrderRequest $request, Product $product) {

        $validated = $request->validated();
        $validated['product_id'] = $product->id;

        $this->orderService->beginOrder($validated);

        return redirect()->route('front.booking', $product->slug);
        
    }

    public function booking() {

        $data = $this->orderService->getOrderDetails();
        // dd($data);
        return view('order.order', $data);
        
    }

    public function customerData() {

        $data = $this->orderService->getOrderDetails();
        // dd($data);
        return view('order.customer_data', $data);

    }

    public function saveCustomerData(StoreCustomerDataRequest $request) {

        $validated = $request->validated();

        $this->orderService->updateCustomerData($validated);

        return redirect()->route('front.payment');
        
    }

    public function payment() {

        $data = $this->orderService->getOrderDetails();
        // dd($data);
        return view ('order.payment', $data);
        
    }

    public function paymentConfirm(StorePaymentRequest $request) {

        $validated = $request->validated();
        // dd('Validated Data:', $validated);

        $productTransactionId = $this->orderService->paymentConfirm($validated);
        // dd('Product Transaction ID:', $productTransactionId);

        if($productTransactionId){

            return redirect()->route('front.order_finished', $productTransactionId)->with('success', 'Payment successfully processed!');
        }
        
        return redirect()->route('front.index')->withErrors(['error' => 'Payment failed. Please try again']);
    }

    public function orderFinished( ProductTransaction $productTransaction) {
        // dd($productTransaction);    
        return view('order.order_finished', compact('productTransaction'));    
    }

    public function checkBooking() {
        
        return view('order.my_order');
    }

    public function checkBookingDetails(StoreCheckBookingRequest $request) {

        $validated = $request->validated();

        $orderDetails = $this->orderService->getMyOrderDetails($validated);
        
        if ($orderDetails) {
            return view('order.my_order_details', compact('orderDetails'));
        }

        return redirect()->route('front.check_booking')->with(['error', 'Transaction Not Found']);        
    }
}
