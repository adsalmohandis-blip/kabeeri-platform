<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\MallMirrorProduct;
use Illuminate\Contracts\View\View;

class MallProductController extends Controller
{
    public function index(): View
    {
        $products = MallMirrorProduct::query()
            ->where('mirror_status', 'published')
            ->orderBy('product_name')
            ->paginate(24);

        return view('mall.products.index', [
            'products' => $products,
            'pageTitle' => 'Products',
        ]);
    }

    public function show(MallMirrorProduct $product): View
    {
        if ($product->mirror_status !== 'published') {
            abort(404);
        }

        return view('mall.products.show', [
            'product' => $product,
            'pageTitle' => $product->product_name,
        ]);
    }
}
