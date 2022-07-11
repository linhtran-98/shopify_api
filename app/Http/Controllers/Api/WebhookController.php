<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Jobs\CreateProduct;
use App\Jobs\UpdateProduct;
use App\Jobs\DeleteProduct;

class WebhookController extends Controller
{
    public function create(Request $request){

        $job = new CreateProduct($request->all());
        dispatch($job)->delay(now()->addSecond(1));
    }

    public function update(Request $request){

        $job = new UpdateProduct($request->all());
        dispatch($job)->delay(now()->addSecond(1));
    }

    public function delete(Request $request){

        $job = new DeleteProduct($request->all());
        dispatch($job)->delay(now()->addSecond(1));
    }
}
