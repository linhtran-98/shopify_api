<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use App\Product;
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
    
    /**
     * store- save product to db & shopify
     *
     * @param  Request $request
     * @return redirect
     */
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
        
        $create_data = $this->saveToShopify($shop_info->domain, $shop_info->access_token, $data);
        $create_data['shop_id'] = $shop_info->id;

        Product::create($create_data);
        return redirect()->route('products')->with('success', 'Thêm sản phẩm thành công');
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
        
        $data_create = [
            'id' => $product_data['product']->id,
            'title' => $product_data['product']->title,
            'description' => $product_data['product']->body_html
        ];
        // Update defaut variant to get price
        $variant_id = $product_data['product']->variants[0]->id;
        $variant_url = 'https://'.$domain.'/admin/api/2022-07/variants/'.$variant_id.'.json';
        $variant_res = variantUpdate($variant_url, $access_token, $data['price']);
        $data_create['price'] = $variant_res->price;

        // add image
        if(isset($data['image']))
        {
            $image_url = 'https://'.$domain.'/admin/api/2022-07/products/'.$product_data['product']->id.'/images.json';
            
            $image_res = createImage($image_url, $access_token, $data['image']);

            $data_create['image'] = $image_res->src;
        }

        return $data_create;
        
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

        $product_data = $request->validate([
            'product_id'  => 'required',
            'title'       => 'required',
            'price'       => 'required',
            'image'       => 'nullable',
            'description' => 'nullable'
        ],[
            'product_id.required' => 'Có lỗi xảy ra',
            'title.required'      => 'Tên sản phẩm không hợp lệ',
            'price.required'      => 'giá sản phẩm không hợp lệ'
        ]);

        $shop_info = Shop::select('domain', 'access_token')->find(session()->get('shop_id'));

        // update product
        $product_url     = 'https://'.$shop_info->domain.'/admin/api/2021-10/products/'.$product_data['product_id'].'.json';
        $product_res     = productUpdate($product_url, $shop_info->access_token, $product_data);
        $update_data     = ['title'       => $product_data['title'],
                            'description' => $product_data['description']
                        ];

        // update variant default
        $variant_id  = $product_res->variants[0]->id;
        $variant_url = 'https://'.$shop_info->domain.'/admin/api/2022-07/variants/'.$variant_id.'.json';
        $variant_res = variantUpdate($variant_url, $shop_info->access_token, $product_data['price']);

        $update_data['price'] = $variant_res->price;
        // update image
        if(isset($product_data['image']))
        {
            // if image product in shopify empty -> create else update
            if(is_null($product_res->image))
            {
                $image_url = 'https://'.$shop_info->domain.'/admin/api/2022-07/products/'.$product_data['product_id'].'/images.json';
                $image_res = createImage($image_url, $shop_info->access_token, $product_data['image']);
                $update_data['image'] = $image_res->src;
            }else
            {
                $image_url = 'https://'.$shop_info->domain.'/admin/api/2022-07/products/'.$product_data['product_id'].'/images/'.$product_res->image->id.'.json';
                $image_res = updateImage($image_url, $shop_info->access_token, $product_res->image->id, $product_data['image']);
                $update_data['image'] = $image_res->src;
            }
        }

        // dd($update_data);

        // save to db
        Product::where('id', '=', $product_res->id)->update($update_data);

        return back()->with('success', 'Sửa sản phẩm thành công');
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
