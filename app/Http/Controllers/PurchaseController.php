<?php

namespace App\Http\Controllers;

use App\Enums\PurchaseStatus;
use App\Http\Requests\StorePurchaseRequest;
use App\Http\Requests\VerifyPurchaseRequest;
use App\Models\Ingredient;
use App\Models\Purchase;
use App\Services\PurchaseService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\Response;

class PurchaseController extends Controller
{
    public function __construct(private readonly PurchaseService $purchases) {}

    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));
        $status = (string) $request->query('status', '');

        $filtered = Purchase::query()
            ->with(['creator:id,name', 'items.ingredient:id,name'])
            ->withCount('items')
            ->when($search !== '', fn ($query) => $query->where(function ($query) use ($search): void {
                $query->where('purchase_code', 'like', "%{$search}%")
                    ->orWhere('supplier_name', 'like', "%{$search}%");
            }))
            ->when($status !== '', fn ($query) => $query->where('status', $status));

        $purchases = (clone $filtered)
            ->latest('purchase_date')
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        $all = Purchase::query();

        return view('purchases.index', [
            'purchases' => $purchases,
            'totalPurchase' => (clone $all)->sum('total'),
            'transactionCount' => (clone $all)->count(),
            'pendingCount' => (clone $all)->where('status', PurchaseStatus::Pending->value)->count(),
            'ingredients' => Ingredient::query()->where('is_active', true)->orderBy('name')->get(['id', 'name', 'base_unit']),
            'suppliers' => (clone $all)->whereNotNull('supplier_name')->distinct()->orderBy('supplier_name')->pluck('supplier_name'),
            'statuses' => PurchaseStatus::cases(),
            'search' => $search,
            'status' => $status,
        ]);
    }

    public function store(StorePurchaseRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('receipt')) {
            $data['receipt_path'] = $request->file('receipt')->store('purchases/receipts', 'local');
        }

        try {
            $this->purchases->create($data, $request->user());
        } catch (InvalidArgumentException $exception) {
            return back()->withInput()->with('error', $exception->getMessage());
        }

        // Staf tidak memiliki akses ke rekap; mereka kembali ke dashboard dengan toast sukses.
        $target = $request->user()->can('manage-stock') ? route('purchases.index') : route('dashboard');

        return redirect($target)->with('success', 'Pembelian berhasil dicatat dan menunggu verifikasi.');
    }

    public function show(Purchase $purchase): View
    {
        $purchase->load(['items.ingredient', 'creator:id,name', 'verifier:id,name']);

        return view('purchases.partials.detail', ['purchase' => $purchase]);
    }

    public function receipt(Purchase $purchase): Response
    {
        abort_unless($purchase->receipt_path !== null && Storage::disk('local')->exists($purchase->receipt_path), 404);

        return response()->file(Storage::disk('local')->path($purchase->receipt_path), [
            'Content-Type' => Storage::disk('local')->mimeType($purchase->receipt_path) ?? 'application/octet-stream',
            'Content-Disposition' => 'inline; filename="nota-'.$purchase->purchase_code.'.'.pathinfo($purchase->receipt_path, PATHINFO_EXTENSION).'"',
        ]);
    }

    public function verify(VerifyPurchaseRequest $request, Purchase $purchase): RedirectResponse
    {
        $approve = $request->string('decision')->toString() === PurchaseStatus::Approved->value;

        try {
            $this->purchases->verify(
                $purchase,
                $approve,
                $request->string('rejection_note')->toString() ?: null,
                $request->user(),
            );
        } catch (InvalidArgumentException $exception) {
            return back()->with('error', $exception->getMessage());
        }

        return redirect()->route('purchases.index', [
            'modal' => 'detail-purchase-modal',
            'id' => $purchase->id,
        ])->with('success', $approve
            ? 'Pembelian disetujui. Stok bahan telah bertambah.'
            : 'Pembelian ditolak. Stok tidak berubah.');
    }
}
