<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use App\Variant;
use App\Product;
use App\Image;
use App\Shop;

class ProductController extends Controller
{
    public function index(Request $request){

        $shop_id = session()->get('shop_id');
        $title   = 'Dashboard';

        $products = Product::where('shop_id', '=', $shop_id)->orderByDesc('id')->get();

        return view('admin.products.index', [
            'title'    => $title, 
            'products' => $products
        ]);
    }

    public function create(){
        
        $title = 'Create product';
        return view('admin.products.create')->with(compact('title'));
    }

    public function store(Request $request){

        $shop_info = Shop::select('id', 'domain', 'access_token')->find(session()->get('shop_id'));

        $data = $request->validate([
            'title'       => 'required',
            'price'       => 'required',
            'image'       => 'nullable',
            'description' => 'nullable'
        ],[
            'title.required'       => 'Tên sản phẩm không hợp lệ',
            'price.required'       => 'Giá sản phẩm không hợp lệ'
        ]);

        $this->saveToShopify($shop_info->domain, $shop_info->access_token, $data);

        return back()->with('success', 'Thêm sản phẩm thành công');
    }

    public function saveToShopify($domain, $access_token, $data){
        
        // save product
        $product_url     = 'https://'.$domain.'/admin/api/2022-07/products.json';

        $product_payload = ['headers' => [
                                'X-Shopify-Access-Token' => $access_token,
                                'Content-Type' => 'application/json'
                            ],
                            'query' => [
                                'product' => [
                                            'title' => $data['title'],
                                            'body_html' => $data['description']]
                        ]];
    
        $product_data = makeGuzzleRequest('POST', $product_url, $product_payload);
        
        // Update defaut variant to get price
        $variant_id = $product_data['product']->variants[0]->id;
        $variant_url = 'https://'.$domain.'/admin/api/2022-07/variants/'.$variant_id.'.json';
        
        $variant_payload = ['headers' => [
                                'X-Shopify-Access-Token' => $access_token,
                                'Content-Type' => 'application/json'
                            ],
                            'query' => [
                                'variant' => [
                                            'price' => $data['price']]
                            ]];

        makeGuzzleRequest('PUT', $variant_url, $variant_payload);

        // update image
        if(isset($data['image']))
        {
            $image_url = 'https://'.$domain.'/admin/api/2022-07/products/'.$product_data['product']->id.'/images.json';
            
            $image_payload = ['headers' => [
                                'X-Shopify-Access-Token' => $access_token,
                                'Content-Type' => 'application/json'
                            ],
                            'json' => [
                                'image' => [
                                    'attachment' => base64_encode(file_get_contents($data['image'])),
                                    'filename' => $data['image']->getClientOriginalName()]
                            ]];

            makeGuzzleRequest('POST', $image_url, $image_payload);
        }
    }

    public function edit($id){
        
        $title   = 'Sửa sản phẩm';
        $product = Product::find($id);

        if(!is_null($product))
        {
            return view('admin.products.edit', ['title' => $title, 'product' => $product]);
        }

        return abort(404);
    }

    public function update(Request $request){

        $product_data = $request->all();
        $shop_info = Shop::select('domain', 'access_token')->find(session()->get('shop_id'));

        // update product
        $product_url     = 'https://'.$shop_info->domain.'/admin/api/2021-10/products/'.$product_data['product_id'].'.json';
        
        $product_payload = ['headers' => [
                                'X-Shopify-Access-Token' => $shop_info->access_token,
                                'Content-Type' => 'application/json'
                            ],
                            'query' => [
                                'product' => [
                                            'id' => $product_data['product_id'],
                                            'title' => $product_data['title'],
                                            'body_html' => $product_data['description']]
                        ]];
    
        $product_response = makeGuzzleRequest('PUT', $product_url, $product_payload);

        // update variant default
        $variant_id = $product_response['product']->variants[0]->id;
        $variant_url = 'https://'.$shop_info->domain.'/admin/api/2022-07/variants/'.$variant_id.'.json';
        
        $variant_payload = ['headers' => [
                                'X-Shopify-Access-Token' => $shop_info->access_token,
                                'Content-Type' => 'application/json'
                            ],
                            'query' => [
                                'variant' => [
                                            'price' => $product_data['price']]
                            ]];

        makeGuzzleRequest('PUT', $variant_url, $variant_payload);
        // sleep(5);
        return redirect()->route('products')->with('success', 'Sửa sản phẩm thành công');
    }

    public function delete(Request $request){

        $shop_info = Shop::select('domain', 'access_token')->find(session()->get('shop_id'));
        $product   = Product::find($request->product_id);

        if(!is_null($shop_info) && !is_null($product))
        {
            DB::beginTransaction();
            try {
                Product::destroy($product->id);

                $url_delete = 'https://'.$shop_info->domain.'/admin/api/2022-01/products/'.$product->id.'.json';
                $payload    = ['headers' => ['X-Shopify-Access-Token' => $shop_info->access_token]];

                makeGuzzleRequest('DELETE', $url_delete, $payload);

                DB::commit();
                return back()->with('success', 'Xóa sản phẩm thành công');

            } catch (Throwable $e){

                DB::rollback();
                report($e);
            }
        }
        
        return back()->with('error', 'Có lỗi xảy ra');
    }

}
