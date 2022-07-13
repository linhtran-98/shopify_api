<?php

use GuzzleHttp\Client;


if(!function_exists('makeGuzzleRequest')){

    function makeGuzzleRequest($method = 'GET', $url, $payload){
        
        $client   = new Client();

        $response = $client->request($method, $url, $payload); 

        $data     = (array) json_decode($response->getBody());

        return $data; 
    }
}

if(!function_exists('getAccessToken'))
{
    function getAccessToken($code, $shop_name){

        $client_id     = env('SHOPIFY_API_KEY');
        $client_secret = env('SHOPIFY_SECRET_KEY');
        $url           = 'https://'.$shop_name.'/admin/oauth/access_token';

        $payload = ['form_params' => ['client_id'     => $client_id,
                                      'client_secret' => $client_secret,
                                      'code'          => $code
                ]];
        
        $get_access_token = makeGuzzleRequest('POST', $url, $payload);

        return $get_access_token['access_token'];
    }
}

if(!function_exists('getShopInfo'))
{
    function getShopInfo($shop_name, $access_token){

        $url    = 'https://'.$shop_name.'/admin/api/2022-07/shop.json';
        $fields = 'id,name,domain,email,plan_display_name,created_at';

        $payload = ['headers' => ['X-Shopify-Access-Token' => $access_token],
                    'query'   => ['fields' => $fields]
                ];

        $shop_info = makeGuzzleRequest('GET', $url, $payload);
        
        return $shop_info['shop'];
    }
}

if(!function_exists('getProducts'))
{
    function getProducts($shop_name, $access_token){

        $url    = 'https://'.$shop_name.'/admin/api/2022-07/products.json';
        $fields = 'id,title,body_html,variants,image';

        $payload = ['headers' =>[
                        'X-Shopify-Access-Token' => $access_token
                        ],
                    'query'   => [
                        'fields' => $fields]
                ];

        $product_info = makeGuzzleRequest('GET', $url, $payload);

        return $product_info['products'];
    }
}

if(!function_exists('webhookRegister'))
{
    function webhookRegister($shop_name, $access_token, $return_webhook_address, $topic){

        $url = 'https://'.$shop_name.'/admin/api/2022-07/webhooks.json';

        foreach ($topic as $key => $value){

            $postData = [
                    'headers' => [
                        'X-Shopify-Access-Token' => $access_token,
                        'Content-Type'           => 'application/json'
                    ],
                    'query' => [
                        'webhook' => [
                                    'topic'   => $value,
                                    'address' => $return_webhook_address.$value,
                                    'format'  => 'json']]];
            
            makeGuzzleRequest('POST', $url, $postData);
        }
    }
}

if(!function_exists('productUpdate'))
{
    function productUpdate($url, $access_token, $data){

        $product_payload = ['headers' => [
                                'X-Shopify-Access-Token' => $access_token,
                                'Content-Type' => 'application/json'
                            ],
                            'query' => [
                                'product' => [
                                            'id' => $data['product_id'],
                                            'title' => $data['title'],
                                            'body_html' => $data['description']]
                        ]];
    
        $product_res = makeGuzzleRequest('PUT', $url, $product_payload);
        return $product_res['product'];
    }
}

if(!function_exists('variantUpdate'))
{
    function variantUpdate($url, $access_token, $price){

        $variant_payload = ['headers' => [
            'X-Shopify-Access-Token' => $access_token,
            'Content-Type' => 'application/json'
        ],
        'query' => [
            'variant' => [
                        'price' => $price]
        ]];

        $variant_res = makeGuzzleRequest('PUT', $url, $variant_payload);
        return $variant_res['variant'];
    }
}

if(!function_exists('createImage'))
{
    function createImage($url, $access_token, $image){

        $image_payload = ['headers' => [
                                    'X-Shopify-Access-Token' => $access_token,
                                    'Content-Type' => 'application/json'
                                ],
                                'json' => [
                                    'image' => [
                                        'attachment' => base64_encode(file_get_contents($image)),
                                        'filename' => $image->getClientOriginalName()]
                                ]];

        $image_res = makeGuzzleRequest('POST', $url, $image_payload);
        return $image_res['image'];
    }
}

if(!function_exists('updateImage'))
{
    function updateImage($url, $access_token, $image_id, $image){

        $image_payload = ['headers' => [
            'X-Shopify-Access-Token' => $access_token,
            'Content-Type' => 'application/json'
        ],
        'json' => [
            'image' => [
                'attachment' => base64_encode(file_get_contents($image)),
                'filename' => $image->getClientOriginalName()]
        ]];

        $image_res = makeGuzzleRequest('PUT', $url, $image_payload);
        return $image_res['image'];
    }
}


