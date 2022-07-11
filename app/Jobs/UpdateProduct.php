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
// use App\Variant;
// use App\Image;
use App\Shop;

class UpdateProduct implements ShouldQueue
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
        $variant = $product['variants'][0];
        $image = !empty($product['image']['src']) ? $product['image']['src'] : null;

        $data_product = [
            'title'       => $product['title'],
            'description' => $product['body_html'],
            'image'       => $image,
            'price'       => $variant['price']
        ];

        try {

            Product::where('id', '=', $product['id'])->update($data_product);
        } catch (Throwable $e) {
            
            report($e);
        }
    }
}
