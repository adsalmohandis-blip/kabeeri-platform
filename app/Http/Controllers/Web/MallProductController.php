<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\MallMirrorProduct;
use App\Support\Ui\V13ExternalExperience;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class MallProductController extends Controller
{
    public function index(Request $request): View
    {
        $products = V13ExternalExperience::applyMallSearch(
            MallMirrorProduct::query()->where('mirror_status', 'published'),
            'products',
            $request->query('q'),
        )
            ->orderBy('product_name')
            ->paginate(24)
            ->withQueryString();

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
