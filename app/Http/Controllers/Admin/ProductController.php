<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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
        $title = 'Dashboard';

        $products = Product::where('shop_id', '=', $shop_id)->orderByDesc('id')->get();

        return view('admin.products.index', [
            'title' => $title, 
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
            'title' => 'required',
            'price' => 'required',
            'image' => 'nullable',
            'description' => 'required'
        ],[
            'title.required' => 'Tên sản phẩm không hợp lệ',
            'price.required' => 'Giá sản phẩm không hợp lệ',
            'description.required' => 'Chi tiết sản phẩm không hợp lệ'
        ]);

        $this->saveToShopify($shop_info->domain, $shop_info->access_token, $data);

        return back()->with('success', 'Thêm sản phẩm thành công');
    }

    public function saveToShopify($domain, $access_token, $data){
        
        // save product
        $product_url = 'https://'.$domain.'/admin/api/2022-07/products.json';
        $product_payload = ['headers' => [
                'X-Shopify-Access-Token' => $access_token,
                'Content-Type' => 'application/json'
            ],
                'query' => [
                    'product' => [
                                'title' => $data['title'],
                                'body_html' => $data['description']
                    ]
        ]];
    
        $product_data = makeGuzzleRequest('POST', $product_url, $product_payload);
        
        // Update defaut variant
        $variant_id = $product_data['product']->variants[0]->id;
        $variant_url = 'https://'.$domain.'/admin/api/2022-07/variants/'.$variant_id.'.json';
        
        $variant_payload = ['headers' => [
                'X-Shopify-Access-Token' => $access_token,
                'Content-Type' => 'application/json'
            ],
            'query' => [
                'variant' => [
                            'price' => $data['price']
                ]
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
                        'filename' => $data['image']->getClientOriginalName()
                    ]
                ]];

            makeGuzzleRequest('POST', $image_url, $image_payload);
        }
    }

    public function edit($id){
        
        $title = 'Sửa sản phẩm';
        $product = Product::find($id);

        if(!is_null($product)){

            return view('admin.products.edit', ['title' => $title, 'product' => $product]);
        }
        return abort(404);
    }

    public function update(Request $request){

        dd($request->all());
    }

    public function delete(Request $request){

        dd($request->all());
    }

}
