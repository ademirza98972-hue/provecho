<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Card;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportController extends Controller
{
    /** Export daftar card aktif beserta statistik scan ke CSV. */
    public function cards(Request $request): StreamedResponse
    {
        $cards = Card::where('status', 'active')
            ->withCount(['logs as scan_count' => fn ($q) => $q->where('action', 'scan')])
            ->withMax('logs as last_scan_at', 'created_at')
            ->orderByDesc('scan_count')
            ->get();

        $filename = 'provecho-cards-' . now()->format('Y-m-d') . '.csv';

        return response()->streamDownload(function () use ($cards) {
            $out = fopen('php://output', 'w');

            // BOM untuk Excel supaya UTF-8 terbaca benar
            fwrite($out, "\xEF\xBB\xBF");

            fputcsv($out, [
                'ID Card',
                'Nama Toko',
                'Alamat',
                'Status',
                'Total Scan',
                'Scan Terakhir',
                'Diaktifkan',
                'Link Google Review',
            ]);

            foreach ($cards as $card) {
                fputcsv($out, [
                    $card->id,
                    $card->owner_name ?? '',
                    $card->owner_address ?? '',
                    $card->status,
                    $card->scan_count,
                    $card->last_scan_at ?? '',
                    $card->activated_at?->format('Y-m-d H:i') ?? '',
                    $card->google_url ?? '',
                ]);
            }

            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /** Export log aktivitas ke CSV. */
    public function activity(Request $request): StreamedResponse
    {
        $filename = 'provecho-activity-' . now()->format('Y-m-d') . '.csv';

        return response()->streamDownload(function () use ($request) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");

            fputcsv($out, [
                'Waktu',
                'ID Card',
                'Nama Toko',
                'Aksi',
                'IP Address',
                'User Agent',
            ]);

            \App\Models\CardLog::with('card')
                ->orderByDesc('created_at')
                ->chunk(500, function ($logs) use ($out) {
                    foreach ($logs as $log) {
                        fputcsv($out, [
                            $log->created_at->format('Y-m-d H:i:s'),
                            $log->card_id,
                            $log->card->owner_name ?? '',
                            $log->action,
                            $log->ip_address ?? '',
                            $log->user_agent ?? '',
                        ]);
                    }
                });

            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
