<?php

namespace App\Http\Controllers;

use App\Actions\ProductDataService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function __construct(protected ProductDataService $productDataService){
    }

    public function index(){
        $data = $this->productDataService->getData();
        return view('welcome', ['data' => $data]);
    }

    public function save(Request $request){
        $validated = $request->validate([
            'product_name' => 'required|string|max:255',
            'quantity_in_stock' => 'required|integer|min:0',
            'price_per_item' => 'required|numeric|min:0'
        ]);

        $data = $this->productDataService->getData();
        $record = [
            'id' => $this->productDataService->generateId($data),
            'product_name' => $validated['product_name'],
            'quantity_in_stock' => $validated['quantity_in_stock'],
            'price_per_item' => $validated['price_per_item'],
            'datetime_submitted' => Carbon::now()->toDateTimeString()
        ];
        $data[] = $record;
        $this->productDataService->saveData($data);

        return response()->json(['success' => true, 'data' => $data]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required',
            'product_name' => 'required|string|max:255',
            'quantity_in_stock' => 'required|integer|min:0',
            'price_per_item' => 'required|numeric|min:0'
        ]);

        $data = $this->productDataService->getData();
        foreach ($data as $index => $item) {
            if ($item['id'] == $validated['id']) {
                $data[$index]['product_name'] = $validated['product_name'];
                $data[$index]['quantity_in_stock'] = $validated['quantity_in_stock'];
                $data[$index]['price_per_item'] = $validated['price_per_item'];
            }
        }
        $this->productDataService->saveData($data);

        return response()->json(['success' => true, 'data' => $data]);
    }



}
