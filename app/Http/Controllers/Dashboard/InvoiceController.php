<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Card;
use App\Models\Invoice;
use App\Support\Brand;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $invoices = Invoice::with('items', 'user:id,name')
            ->when($request->filled('search'), fn ($q) => $q->where(fn ($q) => $q
                ->where('number', 'like', "%{$request->search}%")
                ->orWhere('buyer_name', 'like', "%{$request->search}%")
                ->orWhere('buyer_phone', 'like', "%{$request->search}%")))
            ->latest()->paginate(20)->withQueryString();

        return view('dashboard.invoices.index', compact('invoices'));
    }

    public function create(Request $request)
    {
        $ids = implode("\n", array_filter((array) $request->input('ids', [])));

        return view('dashboard.invoices.form', ['ids' => $ids, 'invoice' => new Invoice]);
    }

    public function edit(Invoice $invoice)
    {
        $invoice->load('items');

        return view('dashboard.invoices.form', ['ids' => $invoice->items->pluck('card_id')->implode("\n"), 'invoice' => $invoice]);
    }

    public function store(Request $request)
    {
        $invoice = $this->save($request, new Invoice);

        return redirect()->route('dashboard.invoices.show', $invoice)->with('success', "Nota {$invoice->number} dibuat.");
    }

    public function update(Request $request, Invoice $invoice)
    {
        $this->save($request, $invoice);

        return redirect()->route('dashboard.invoices.show', $invoice)->with('success', "Nota {$invoice->number} diperbarui.");
    }

    private function save(Request $request, Invoice $invoice): Invoice
    {
        $data = $request->validate([
            'card_ids'       => 'required|string|max:5000',
            'buyer_name'     => 'required|string|max:100',
            'buyer_phone'    => Card::BUYER_RULES['buyer_phone'],
            'payment_method' => ['required', Rule::in(array_keys(Invoice::PAYMENT_METHODS))],
            'price'          => 'required|integer|min:0|max:100000000',
            'discount'       => 'nullable|integer|min:0',
            'notes'          => 'nullable|string|max:500',
        ], Card::BUYER_MESSAGES + [
            'card_ids.required'   => 'Isi minimal satu ID card.',
            'buyer_name.required' => 'Nama pembeli wajib diisi.',
            'price.required'      => 'Harga per card wajib diisi.',
        ]);

        $ids = array_values(array_unique(preg_split('/[\s,;]+/', strtoupper($data['card_ids']), -1, PREG_SPLIT_NO_EMPTY)));
        $found = Card::whereIn('id', $ids)->pluck('id')->all();
        $missing = array_diff($ids, $found);
        if ($missing) {
            throw ValidationException::withMessages(['card_ids' => 'ID card tidak ditemukan: ' . implode(', ', $missing)]);
        }

        $discount = (int) ($data['discount'] ?? 0);
        if ($discount > $data['price'] * count($ids)) {
            throw ValidationException::withMessages(['discount' => 'Diskon tidak boleh melebihi subtotal.']);
        }

        $buyer = Card::cleanBuyer(['buyer_name' => $data['buyer_name'], 'buyer_phone' => $data['buyer_phone'] ?? null]);

        return DB::transaction(function () use ($request, $invoice, $data, $ids, $discount, $buyer) {
            if (! $invoice->exists) {
                $invoice->number  = Invoice::nextNumber();
                $invoice->user_id = $request->user()->id;
            }
            $invoice->fill($buyer + [
                'payment_method' => $data['payment_method'],
                'discount'       => $discount,
                'notes'          => trim((string) ($data['notes'] ?? '')) ?: null,
            ])->save();

            // Card yang dikeluarkan dari nota: data pembeli dari nota ini ikut dikosongkan.
            $removed = $invoice->items()->whereNotIn('card_id', $ids)->pluck('card_id');
            Card::whereIn('id', $removed)->where('order_number', $invoice->number)
                ->update(['order_number' => null, 'buyer_name' => null, 'buyer_phone' => null]);

            $invoice->items()->delete();
            $invoice->items()->createMany(array_map(fn ($id) => ['card_id' => $id, 'price' => $data['price']], $ids));

            // Nomor nota jadi nomor pesanan card, jadi klaim garansi bisa dicari dari halaman Kelola Card.
            Card::whereIn('id', $ids)->update($buyer + ['order_number' => $invoice->number]);

            return $invoice;
        });
    }

    public function show(Invoice $invoice)
    {
        $brand = $this->brand($invoice);

        return view('dashboard.invoices.show', compact('invoice', 'brand'));
    }

    /** Halaman pembeli dari link WhatsApp, tanpa login: preview nota + tombol unduh. */
    public function publicShow(Invoice $invoice)
    {
        $brand = $this->brand($invoice);

        return view('invoice.public', compact('invoice', 'brand'));
    }

    /**
     * Unduhan juga lewat link bertanda tangan, termasuk dari dashboard: pengelola unduhan
     * di sebagian HP meminta ulang file tanpa cookie login, jadi link yang butuh sesi gagal.
     */
    public function download(Invoice $invoice)
    {
        $brand = $this->brand($invoice);
        // dompdf butuh file lokal, bukan URL.
        if (str_starts_with($brand['logo'], '/')) {
            $brand['logo'] = public_path(ltrim($brand['logo'], '/'));
        }

        return Pdf::loadView('dashboard.invoices.pdf', compact('invoice', 'brand'))->setPaper('a5')
            ->setOption('isFontSubsettingEnabled', true) // tanpa ini font DejaVu ikut utuh, PDF jadi >1 MB
            ->download($invoice->number . '.pdf');
    }

    private function brand(Invoice $invoice): array
    {
        $invoice->load('items', 'user');
        $u = $invoice->user;
        $brand = Brand::make($u?->brand_name, $u?->brand_color, $u?->brand_logo);
        // logo.png 676 KB membuat PDF 1,5 MB dan halaman lambat; ikon 180px cukup untuk nota.
        if (! $u?->brand_logo) {
            $brand['logo'] = '/apple-touch-icon.png';
        }

        return $brand;
    }
}
