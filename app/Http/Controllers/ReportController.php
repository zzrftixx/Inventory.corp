<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SalesOrder;
use App\Models\SalesOrderDetail;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ProfitLossExport;

class ReportController extends Controller
{
    public function profitLoss(Request $request)
    {
        $query = SalesOrder::with(['details.item', 'customer', 'user'])
            ->where('status', '!=', 'Batal')
            ->orderBy('tanggal_transaksi', 'desc');

        if ($request->filled('start_date')) {
            $query->whereDate('tanggal_transaksi', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('tanggal_transaksi', '<=', $request->end_date);
        }

        if ($request->has('export') && $request->export == 'excel') {
            // Provide a graceful fallback to native CSV if maatwebsite fails to load
            if (class_exists(\Maatwebsite\Excel\Facades\Excel::class) && class_exists(\App\Exports\ProfitLossExport::class)) {
                return Excel::download(new ProfitLossExport($query->get()), 'Laporan_Laba_Rugi_' . date('Ymd_His') . '.xlsx');
            } else {
                return $this->exportCsv($query->get());
            }
        }

        $salesOrders = $query->paginate(20)->withQueryString();

        // Calculate Totals overall
        $allFiltered = $query->get();
        $summary = [
            'total_pendapatan' => 0,
            'total_modal' => 0,
            'laba_kotor' => 0
        ];

        foreach ($allFiltered as $order) {
            foreach ($order->details as $detail) {
                $summary['total_pendapatan'] += $detail->subtotal_netto;
                $summary['total_modal'] += ($detail->qty * $detail->harga_modal_saat_transaksi);
            }
        }
        $summary['laba_kotor'] = $summary['total_pendapatan'] - $summary['total_modal'];

        return view('reports.profit_loss', compact('salesOrders', 'summary'));
    }

    private function exportCsv($orders)
    {
        $fileName = 'Laporan_Laba_Rugi_' . date('Y_m_d_H_i_s') . '.csv';

        $headers = array(
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        );

        $columns = [
            'No. Faktur',
            'Tanggal Transaksi',
            'Customer',
            'Nama Barang',
            'Qty',
            'Total Modal (HPP)',
            'Total Pendapatan (Netto)',
            'Laba Kotor',
            'Diinput Oleh'
        ];

        $callback = function () use ($orders, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns, ';');

            foreach ($orders as $order) {
                foreach ($order->details as $detail) {
                    $laba = $detail->subtotal_netto - ($detail->qty * $detail->harga_modal_saat_transaksi);
                    fputcsv($file, [
                        $order->no_faktur,
                        $order->tanggal_transaksi,
                        $order->customer->nama ?? 'Umum',
                        $detail->item->nama_barang ?? 'Unknown',
                        $detail->qty,
                        $detail->qty * $detail->harga_modal_saat_transaksi,
                        $detail->subtotal_netto,
                        $laba,
                        $order->user->name ?? 'System'
                    ], ';');
                }
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
