<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Bus\Queueable;
use Illuminate\Http\Request;
use App\Product;
use App\Variant;
use App\Image;
use App\Shop;

class CreateProduct implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $request;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($request)
    {
        $this->request = $request;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $product = $this->request;
        
        $findProduct = Product::find($product['id']);

        if(!$findProduct)
        {
            $variant = $product['variants'][0];
            
            $image = !empty($product['image']['src']) ? $product['image']['src'] : null;
            
            $shop = Shop::select('id')->where('name', '=', $product['vendor'])->first();
    
            $data_product = [
                'id'          => $product['id'],
                'title'       => $product['title'],
                'description' => $product['body_html'],
                'image'       => $image,
                'price'       => $variant['price'],
                'shop_id'     => $shop->id
            ];
    
            // DB::beginTransaction();
    
            try {
    
                Product::create($data_product);
                // DB::commit();
                // all good
            } catch (Throwable $e) {
                
                // DB::rollback();
                report($e);
            }
        }
    }
}
