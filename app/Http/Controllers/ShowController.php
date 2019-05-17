<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use App\Feed;

class ShowController extends BaseController
{
    public function shows()
    {
        $feeds = Feed::all();
        return view('shows', ['feeds' => $feeds]);
    }

    public function search()
    {
        throw new \Exception("To Implement");
    }

    public function feed()
    {
        throw new \Exception("To Implement");
    }

    public function episode()
    {
        throw new \Exception("To Implement");
    }
}
