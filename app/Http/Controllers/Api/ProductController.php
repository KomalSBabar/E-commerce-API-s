<?php

namespace App\Http\Controllers\Api;
use App\Models\Product;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\ProductResource;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\ProductStoreRequest;
use App\Http\Requests\ProductUpdateRequest;
use Illuminate\Support\Facades\Storage;


class ProductController extends Controller
{
    public function index(){
        $products = Product::get();
        if($products->count()>0){
            return ProductResource::collection($products);
        }else{
            return response()->json(['message' => 'No record available'],200);
        }
    }

    public function store(ProductStoreRequest $request)
    {
        // Create the product with validated data from the request
        $product = Product::create($request->validated());

        // If an image is uploaded, process it
        if ($request->hasFile('image')) {
            // Store the image and get the path
            $imagePath = $request->file('image')->store('product_images', 'public');

            // Directly assign the image_url and save the product
            $product->image = $imagePath; // Assign the image path directly
            $product->save();  // Save the product to persist the change
        }

        return new ProductResource($product);
    }

    
    public function show(Product $product){
        return new ProductResource($product);
    }
  
    public function update(ProductUpdateRequest $request, Product $product)
    {
        // Update the product with validated data
        $product->update($request->validated());
    
        // If an image is uploaded, handle the image
        if ($request->hasFile('image')) {
            // Delete the old image if a new one is uploaded
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
    
            // Store the new image and update the image path
            $product->image = $request->file('image')->store('product_images', 'public');
            $product->save(); // Save the product with the new image
        }
    
        return new ProductResource($product);
    }
    
    

    public function destroy(Product $product){

        $product->delete();
        return response()->json([
            'message' => 'Product Deleted Successfully',
        ],200);

    }
}
