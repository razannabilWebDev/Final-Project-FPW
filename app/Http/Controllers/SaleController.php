<?php

namespace App\Http\Controllers;

use App\Enums\SaleStatus;
use App\Exceptions\InsufficientStockException;
use App\Http\Requests\StoreSaleRequest;
use App\Http\Requests\UpdateSaleStatusRequest;
use App\Models\Sale;
use App\Services\SaleService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SaleController extends Controller
{
    public function __construct(private readonly SaleService $sales) {}

    public function index(Request $request): View
    {
        $query = Sale::with('items')->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }
        if ($request->filled('search')) {
            $search = $request->string('search');
            $query->where(fn ($q) => $q
                ->where('transaction_code', 'like', "%{$search}%")
                ->orWhere('note', 'like', "%{$search}%"));
        }

        $sales = $query->paginate(15)->withQueryString();
        $statuses = SaleStatus::cases();

        return view('sales.index', compact('sales', 'statuses'));
    }

    public function create(): View
    {
        return view('sales.create');
    }

    public function store(StoreSaleRequest $request): RedirectResponse
    {
        try {
            $sale = $this->sales->create($request->validated(), $request->user());
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', 'Penjualan gagal disimpan: '.$e->getMessage());
        }

        return redirect()->route('sales.show', $sale)
            ->with('success', 'Penjualan berhasil dicatat.');
    }

    public function show(Sale $sale): View
    {
        return view('sales.show', compact('sale'));
    }

    public function updateStatus(UpdateSaleStatusRequest $request, Sale $sale): RedirectResponse
    {
        try {
            $this->sales->updateStatus(
                $sale,
                SaleStatus::fromString($request->string('status')->toString()),
                $request->user(),
                $request->string('note')->toString() ?: null,
            );
        } catch (InsufficientStockException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Status transaksi diperbarui.');
    }
}
