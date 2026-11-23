<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ActivityLogSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();
        $adminId = (int) DB::table('users')->where('email', 'admin@smartfood.test')->value('id');
        $pengelolaId = (int) DB::table('users')->where('email', 'pengelola@smartfood.test')->value('id');
        $stafId = (int) DB::table('users')->where('email', 'staf@smartfood.test')->value('id');

        $saleId = DB::table('sales')->where('transaction_code', 'TRX-20261110-0007')->value('id');
        $purchaseId = DB::table('purchases')->where('purchase_code', 'PUR-20261118-0001')->value('id');
        $adjustmentId = DB::table('stock_adjustments')->where('reason', 'Penyesuaian stok fisik harian dummy')->value('id');
        $forecastId = DB::table('production_forecasts')->where('data_status', 'cukup')->value('id');
        $userId = DB::table('users')->where('email', 'staf@smartfood.test')->value('id');

        $logs = [
            [$adminId, 'login', 'User', $adminId, ['role' => 'admin']],
            [$adminId, 'create-user', 'User', $pengelolaId, ['role' => 'pengelola']],
            [$pengelolaId, 'verify-purchase', 'Purchase', $purchaseId, ['status' => 'approved']],
            [$adminId, 'adjust-stock', 'StockAdjustment', $adjustmentId, ['reason' => 'Penyesuaian stok fisik harian dummy']],
            [$stafId, 'cancel-sale', 'Sale', $saleId, ['status' => 'cancelled']],
            [$adminId, 'adjust-forecast', 'ProductionForecast', $forecastId, ['method' => 'moving_average']],
            [$stafId, 'update-profile', 'User', $userId, ['field' => 'name']],
        ];

        foreach ($logs as [$actorId, $action, $subjectType, $subjectId, $metadata]) {
            if ($subjectId === null || DB::table('activity_logs')
                ->where('action', $action)
                ->where('subject_type', $subjectType)
                ->where('subject_id', $subjectId)
                ->exists()) {
                continue;
            }

            DB::table('activity_logs')->insert([
                'user_id' => $actorId,
                'action' => $action,
                'subject_type' => $subjectType,
                'subject_id' => $subjectId,
                'metadata' => json_encode($metadata),
                'created_at' => $now,
            ]);
        }
    }
}
