<?php

namespace App\Http\Controllers;

Use App\Shop;
// Use App\Image;
// Use App\Variant;
Use App\Product;
use GuzzleHttp\Client;
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
        $shop_name = $request->shop;
        $code = $request->code;

        $access_token = $this->getAccessToken($code, $shop_name);

        $shop_info = $this->getShop($shop_name, $access_token);
        $products = $this->getProducts($shop_name, $access_token);

        if(!Shop::find($shop_info->id))
        {
            $this->saveShopToDB($shop_info, $access_token);
            $this->saveProductToDB($products, $shop_info->id);
            $this->webhookRegister($shop_name, $access_token, $webhook_address);
        }

        session()->flash('shop_id', $shop_info->id);
        session()->flash('shop_name', $shop_info->name);

        return redirect()->route('products');
    }
    
    /**
     * getAccessToken
     *
     * @param  string $code
     * @param  string $shop_name
     * @return string
     */
    public function getAccessToken($code, $shop_name){

        $client_id = env('SHOPIFY_API_KEY');
        $client_secret = env('SHOPIFY_SECRET_KEY');
        $url = 'https://'.$shop_name.'/admin/oauth/access_token';

        $payload = ['form_params' => [
            'client_id' => $client_id,
            'client_secret' => $client_secret,
            'code' => $code]];
        
        $get_access_token = $this->makeGuzzleRequest('POST', $url, $payload);

        return $get_access_token['access_token'];
    }
        
    /**
     * getShop - call api get shop info
     *
     * @param  string $shop_name
     * @param  string $access_token
     * @return array
     */
    public function getShop($shop_name, $access_token){

        $url = 'https://'.$shop_name.'/admin/api/2022-07/shop.json';
        $fields = 'id,name,domain,email,plan_display_name,created_at';

        $payload = ['headers' => ['X-Shopify-Access-Token' => $access_token],
                    'query' => ['fields' => $fields]];

        $shop_info = $this->makeGuzzleRequest('GET', $url, $payload);

        return $shop_info['shop'];
    }
    
    /**
     * getProducts - call api get products
     *
     * @param  string $shop_name
     * @param  string $access_token
     * @return array
     */
    public function getProducts($shop_name, $access_token){

        $url = 'https://'.$shop_name.'/admin/api/2022-07/products.json';
        $fields = 'id,title,body_html,variants,image';

        $payload = ['headers' => ['X-Shopify-Access-Token' => $access_token],
                    'query' => ['fields' => $fields]];

        $product_info = $this->makeGuzzleRequest('GET', $url, $payload);

        return $product_info['products'];
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
                'id' => $shop->id,
                'name' => $shop->name,
                'domain' => $shop->domain,
                'email' => $shop->email,
                'shopify_domain' => $shop->domain,
                'access_token' => $access_token,
                'plan' => $shop->plan_display_name,
                'created_at' => $created_at
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
    
    /**
     * webhookRegister
     *
     * @param  string $shop_name
     * @param  string $access_token
     * @param  string $return_webhook_address
     * @return void
     */
    public function webhookRegister($shop_name, $access_token, $return_webhook_address){

        $topic = ['products/create', 'products/update', 'products/delete'];

        $url = 'https://'.$shop_name.'/admin/api/2022-07/webhooks.json';

        foreach ($topic as $key => $value) {

            $postData = [
                    'headers' => [
                        'X-Shopify-Access-Token' => $access_token,
                        'Content-Type' => 'application/json'
                    ],
                    'query' => [
                        'webhook' => [
                                    'topic' => $value,
                                    'address' => $return_webhook_address.$value,
                                    'format' => 'json']]];
            
            $this->makeGuzzleRequest('POST', $url, $postData);
        }
    }
    
    /**
     * makeGuzzleRequest - make guzzle request
     *
     * @param  string $method
     * @param  string $url
     * @param  string $payload
     * @return array
     */
    public function makeGuzzleRequest($method = 'GET', $url, $payload){
        
        $client = new Client();

        $response = $client->request($method, $url, $payload); 

        $data = (array) json_decode($response->getBody());

        return $data; 
    }
    
}