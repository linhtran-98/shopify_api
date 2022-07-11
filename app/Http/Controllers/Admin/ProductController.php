<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use GuzzleHttp\Client;

use Illuminate\Http\Request;
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
        
        $product_client = new Client();
        $product_res = $product_client->request('POST', $product_url, [
                'headers' => [
                    'X-Shopify-Access-Token' => $access_token,
                    'Content-Type' => 'application/json'
                ],
                'query' => [
                    'product' => [
                                'title' => $data['title'],
                                'body_html' => $data['description']
                    ]
                ]
        ]);

        $product_data = (array) json_decode($product_res->getBody());
        
        // Update defaut variant
        $variant_id = $product_data['product']->variants[0]->id;

        $variant_url = 'https://'.$domain.'/admin/api/2022-07/variants/'.$variant_id.'.json';
        
        $variant_client = new Client();
        $variant_res = $variant_client->request('PUT', $variant_url, [
                'headers' => [
                    'X-Shopify-Access-Token' => $access_token,
                    'Content-Type' => 'application/json'
                ],
                'query' => [
                    'variant' => [
                                'price' => $data['price']
                    ]
                ]
        ]);

        // update image
        if(isset($data['image']))
        {
            $url_image = 'https://'.$domain.'/admin/api/2022-07/products/'.$product_data['product']->id.'/images.json';
            $client_image = new Client();
            $image_res = $client_image->request('POST', $url_image, [
                    'headers' => [
                        'X-Shopify-Access-Token' => $access_token,
                        'Content-Type' => 'application/json'
                    ],
                    'json' => [
                        'image' => [
                            'attachment' => base64_encode(file_get_contents($data['image'])),
                            'filename' => $data['image']->getClientOriginalName()
                        ]
                    ]
            ]);
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

    public function delete(){

    }

}
