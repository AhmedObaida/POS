<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProductRequest;
use App\Http\Requests\Admin\UpdateProductRequest;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query()->with('category')->orderBy('name');

        if ($request->filled('search')) {
            $s = $request->get('search');
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', '%'.$s.'%')
                    ->orWhere('sku', 'like', '%'.$s.'%')
                    ->orWhere('barcode', 'like', '%'.$s.'%');
            });
        }
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->get('category_id'));
        }
        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }
        if ($request->boolean('low_stock')) {
            $query->whereColumn('stock_quantity', '<=', 'minimum_stock_alert');
        }

        $products = $query->paginate(15)->withQueryString();
        $categories = Category::query()->orderBy('name')->get();

        return view('admin.products.index', compact('products', 'categories'));
    }

    public function search(Request $request)
    {
        $q = $request->get('q', '');
        $products = Product::query()
            ->active()
            ->where(function ($query) use ($q) {
                $query->where('name', 'like', '%'.$q.'%')
                    ->orWhere('sku', 'like', '%'.$q.'%')
                    ->orWhere('barcode', 'like', '%'.$q.'%');
            })
            ->orderBy('name')
            ->limit(20)
            ->get(['id', 'name', 'sku', 'retail_price', 'wholesale_price', 'stock_quantity', 'barcode']);

        return response()->json($products);
    }

    public function create()
    {
        $categories = Category::query()->orderBy('name')->get();

        return view('admin.products.create', compact('categories'));
    }

    public function store(StoreProductRequest $request)
    {
        $data = $request->validated();
        unset($data['image']);
        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('products', 'public');
        }
        Product::query()->create($data);

        return redirect()->route('admin.products.index')->with('success', __('messages.product_created'));
    }

    public function edit(Product $product)
    {
        $categories = Category::query()->orderBy('name')->get();

        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(UpdateProductRequest $request, Product $product)
    {
        $data = $request->validated();
        unset($data['image']);
        if ($request->hasFile('image')) {
            if ($product->image_path) {
                Storage::disk('public')->delete($product->image_path);
            }
            $data['image_path'] = $request->file('image')->store('products', 'public');
        }
        $product->update($data);

        return redirect()->route('admin.products.index')->with('success', __('messages.product_updated'));
    }

    public function destroy(Product $product)
    {
        if ($product->invoiceItems()->exists()) {
            return redirect()->route('admin.products.index')->with('error', __('messages.product_delete_blocked_flash'));
        }
        if ($product->image_path) {
            Storage::disk('public')->delete($product->image_path);
        }
        $product->delete();

        return redirect()->route('admin.products.index')->with('success', __('messages.product_deleted'));
    }
}
