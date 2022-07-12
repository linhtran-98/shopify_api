<?php

namespace App\Http\Controllers;

Use App\Shop;
Use App\Product;
// use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Jobs\GetDataWebhook;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;

 
class ShopifyController extends Controller
{
    public function getShopName(Request $request){

        return view('GetShopName'); 
    }
    
    /**
     * shopify
     *
     * @param  Request $request
     * @return Redirect
     */
    public function shopify(Request $request){

        $shop_name = $request->shop_name;
        $api_key = env('SHOPIFY_API_KEY');
        $scope = 'read_products, write_products';
        // $redirect_uri = 'http://localhost:8000/authen';
        $redirect_uri = env('NGROK_URL').'/authen';

        $url = $shop_name.'/admin/oauth/authorize?client_id='.$api_key.'&scope='.$scope.'&redirect_uri='.$redirect_uri;
        return redirect($url);
    }
    
    /**
     * authen
     *
     * @param  Request $request
     * @return Redirect
     */
    public function authen(Request $request){

        $webhook_address = env('NGROK_URL').'/api/';
        $shop_name       = $request->shop;
        $code            = $request->code;

        $access_token = getAccessToken($code, $shop_name);
        $shop_info    = getShopInfo($shop_name, $access_token);
        $products     = getProducts($shop_name, $access_token);

        if(!Shop::find($shop_info->id))
        {
            $this->saveShopToDB($shop_info, $access_token);
            $this->saveProductToDB($products, $shop_info->id);
            webhookRegister($shop_name, $access_token, $webhook_address);
        }

        session()->flash('shop_id', $shop_info->id);
        session()->flash('shop_name', $shop_info->name);

        return redirect()->route('products');
    }
    /**
     * shopStore - save shop info from shopify store to app
     *
     * @param  array $shop
     * @param  string $access_token
     * @return void
     */
    public function saveShopToDB($shop, $access_token){
        
        if(!is_null($shop))
        {
            $created_at = substr($shop->created_at,0,10);
            
            $data = [
                'id'             => $shop->id,
                'name'           => $shop->name,
                'domain'         => $shop->domain,
                'email'          => $shop->email,
                'shopify_domain' => $shop->domain,
                'access_token'   => $access_token,
                'plan'           => $shop->plan_display_name,
                'created_at'     => $created_at
            ];
            
            Shop::create($data);
        }
    }
    
    /**
     * productStore - save products from shopify store to app
     *
     * @param  array $products
     * @param  bigInt $shop_id
     * @return void
     */
    public function saveProductToDB($products, $shop_id){

        if(!is_null($products))
        {
            foreach ($products as $product){

                $image = !empty($product->image->src) ? $product->image->src : null;
                
                Product::create([
                    'id'          => $product->id,
                    'title'       => $product->title,
                    'description' => $product->body_html,
                    'image'       => $image,
                    'price'       => $product->variants[0]->price,
                    'shop_id'     => $shop_id
                ]);
            }
        }
    }
}