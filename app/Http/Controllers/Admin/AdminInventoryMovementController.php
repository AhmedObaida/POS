<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreInventoryMovementRequest;
use App\Models\InventoryMovement;
use App\Models\Product;
use App\Services\InventoryService;
use Illuminate\Http\Request;
use InvalidArgumentException;

class AdminInventoryMovementController extends Controller
{
    /** @var InventoryService */
    protected $inventoryService;

    public function __construct(InventoryService $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }

    public function index(Request $request)
    {
        $query = InventoryMovement::query()->with('product')->latest('id');

        if ($request->filled('product_id')) {
            $query->where('product_id', $request->get('product_id'));
        }
        if ($request->filled('type')) {
            $query->where('type', $request->get('type'));
        }

        $movements = $query->paginate(25)->withQueryString();
        $products = Product::query()->orderBy('name')->limit(200)->get();

        return view('admin.inventory.index', compact('movements', 'products'));
    }

    public function create(Request $request)
    {
        $selectedProductId = old('product_id', $request->get('product_id'));
        $selectedProduct = null;
        if ($selectedProductId) {
            $selectedProduct = Product::query()->find($selectedProductId, ['id', 'name', 'sku', 'stock_quantity']);
        }

        return view('admin.inventory.create', compact('selectedProduct'));
    }

    public function store(StoreInventoryMovementRequest $request)
    {
        $data = $request->validated();

        try {
            if ($data['type'] === 'restock') {
                $this->inventoryService->restock((int) $data['product_id'], (int) $data['quantity'], $data['notes'] ?? null);
            } else {
                $qty = (int) $data['quantity'];
                $direction = $request->get('adjustment_direction', 'add');
                $delta = $direction === 'subtract' ? -$qty : $qty;
                $this->inventoryService->adjust((int) $data['product_id'], $delta, $data['notes'] ?? null);
            }
        } catch (InvalidArgumentException $e) {
            return redirect()->back()->withInput()->withErrors(['quantity' => $e->getMessage()]);
        }

        return redirect()->route('admin.inventory.index')->with('success', __('messages.inventory_updated'));
    }
}
