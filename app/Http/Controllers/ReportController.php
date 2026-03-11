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
            return $this->exportExcel($query->get());
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

    private function exportExcel($orders)
    {
        $fileName = 'Laporan_Laba_Rugi_' . date('Ymd_His') . '.xlsx';

        $writer = \Spatie\SimpleExcel\SimpleExcelWriter::streamDownload($fileName);

        foreach ($orders as $order) {
            foreach ($order->details as $detail) {
                $laba = $detail->subtotal_netto - ($detail->qty * $detail->harga_modal_saat_transaksi);

                $writer->addRow([
                    'No. Faktur' => $order->no_faktur,
                    'Tanggal Transaksi' => \Carbon\Carbon::parse($order->tanggal_transaksi)->format('Y-m-d'),
                    'Customer' => $order->customer->nama ?? 'Umum',
                    'Nama Barang' => $detail->item->nama_barang ?? 'Unknown',
                    'Qty' => (int) $detail->qty,
                    'Total Modal (HPP)' => (float) ($detail->qty * $detail->harga_modal_saat_transaksi),
                    'Total Pendapatan (Netto)' => (float) $detail->subtotal_netto,
                    'Laba Kotor' => (float) $laba,
                    'Diinput Oleh' => $order->user->name ?? 'System'
                ]);
            }
        }

        return $writer->toBrowser();
    }
}
