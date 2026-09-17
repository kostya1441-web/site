<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\Setting;

class HomeController extends Controller
{
    public function index(): void
    {
        $this->view('pages/home', [
            'title'       => Setting::get('site_title'),
            'description' => Setting::get('site_description'),
            'categories'  => Category::active(),
            'featured'    => Product::featured(8),
            'latest'      => Product::latest(4),
        ]);
    }
}
