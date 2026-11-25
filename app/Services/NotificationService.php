<?php

namespace App\Services;

use App\Enums\SaleStatus;
use App\Models\User;
use App\UserRole;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Ringkasan kondisi aplikasi yang perlu ditindaklanjuti pengguna.
 * Dihitung real-time dari database tanpa tabel notifikasi terpisah.
 */
class NotificationService
{
    /**
     * @return Collection<int, array{label: string, count: int, route: string, tone: string}>
     */
    public function forUser(User $user): Collection
    {
        $items = collect();

        $canVerifyPurchases = in_array($user->role, [UserRole::Admin, UserRole::Pengelola], true);

        if ($canVerifyPurchases) {
            $pendingPurchases = (int) DB::table('purchases')->where('status', 'pending')->count();

            if ($pendingPurchases > 0) {
                $items->push([
                    'label' => $pendingPurchases.' pembelian menunggu verifikasi',
                    'count' => $pendingPurchases,
                    'route' => route('purchases.index', ['status' => 'pending']),
                    'tone' => 'danger',
                ]);
            }
        }

        $pendingSales = (int) DB::table('sales')
            ->whereIn('status', [SaleStatus::Pending->value, SaleStatus::Processing->value])
            ->count();

        if ($pendingSales > 0) {
            $items->push([
                'label' => $pendingSales.' pesanan perlu ditindaklanjuti',
                'count' => $pendingSales,
                'route' => route('sales.index'),
                'tone' => 'brand',
            ]);
        }

        $lowStock = (int) DB::table('ingredients')
            ->where('is_active', true)
            ->whereColumn('current_stock', '<=', 'minimum_stock')
            ->count();

        if ($lowStock > 0) {
            $items->push([
                'label' => $lowStock.' bahan stok menipis',
                'count' => $lowStock,
                'route' => route('ingredients.index', ['status' => 'menipis']),
                'tone' => 'warning',
            ]);
        }

        return $items;
    }
}
