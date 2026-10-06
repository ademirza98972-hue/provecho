<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Card;
use App\Models\CardLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ResellerController extends Controller
{
    public function index()
    {
        $resellers = User::where('role', 'reseller')
            ->withCount([
                'cards',
                'cards as active_count' => fn ($q) => $q->where('status', 'active'),
            ])
            ->orderBy('name')
            ->get();

        $scans = CardLog::where('action', 'scan')
            ->join('cards', 'cards.id', '=', 'card_logs.card_id')
            ->whereNotNull('cards.reseller_id')
            ->groupBy('cards.reseller_id')
            ->pluck(DB::raw('COUNT(*)'), 'cards.reseller_id');

        return view('dashboard.resellers', compact('resellers', 'scans'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'username' => 'required|alpha_dash|max:50|unique:users,username',
            'password' => 'required|string|min:8',
        ], [
            'username.unique'     => 'Username sudah dipakai.',
            'username.alpha_dash' => 'Username hanya boleh huruf, angka, strip, dan garis bawah.',
            'password.min'        => 'Password minimal 8 karakter.',
        ]);

        User::create($data + ['role' => 'reseller']);

        return back()->with('success', "Akun reseller {$data['name']} dibuat. Kirim username dan password-nya ke reseller.");
    }

    public function assignIds(Request $request)
    {
        $data = $request->validate([
            'reseller_id' => 'required|integer',
            'ids_text'    => 'required|string|max:20000',
        ], [
            'reseller_id.required' => 'Pilih reseller tujuan.',
            'ids_text.required'    => 'Tempel minimal satu ID card.',
        ]);

        $reseller = User::where('role', 'reseller')->findOrFail($data['reseller_id']);
        $ids = self::parseIds($data['ids_text']);
        if (! $ids) {
            return back()->withInput()->withErrors(['ids_text' => 'Tidak ada ID card yang terbaca.']);
        }

        $cards = Card::whereIn('id', $ids)->with('reseller:id,name')->get()->keyBy('id');
        $problems = [];
        foreach ($ids as $id) {
            $card = $cards->get($id);
            if (! $card) {
                $problems[] = "{$id}: tidak ditemukan, cek lagi ketikannya.";
            } elseif ($card->reseller_id && $card->reseller_id !== $reseller->id) {
                $problems[] = "{$id}: sudah milik {$card->reseller->name}.";
            } elseif (! $card->reseller_id && $card->status !== 'inactive') {
                $problems[] = "{$id}: sudah aktif atau nonaktif, bukan card stok.";
            }
        }

        if ($problems) {
            return back()->withInput()->withErrors(['ids_text' => 'Tidak ada card yang dipindahkan. Perbaiki dulu ID berikut:'] + $problems);
        }

        Card::whereIn('id', $ids)->update(['reseller_id' => $reseller->id]);

        return back()->with('success', count($ids) . " card sekarang milik {$reseller->name}.");
    }

    /** Menerima ID per baris/koma/spasi, termasuk link penuh hasil scan QR (…/c/PVXXXXXX). */
    public static function parseIds(string $text): array
    {
        $ids = [];
        foreach (preg_split('/[\s,;]+/', $text, -1, PREG_SPLIT_NO_EMPTY) as $token) {
            $token = preg_replace('/[?#].*$/', '', $token);
            if (str_contains($token, '/')) {
                $token = basename(rtrim($token, '/'));
            }
            if ($token !== '') {
                $ids[] = strtoupper($token);
            }
        }

        return array_values(array_unique($ids));
    }

    public function password(Request $request, User $reseller)
    {
        abort_if($reseller->isAdmin(), 404);

        $data = $request->validate(['password' => 'required|string|min:8'], [
            'password.min' => 'Password minimal 8 karakter.',
        ]);

        $reseller->forceFill(['password' => $data['password'], 'remember_token' => Str::random(60)])->save();
        DB::table('sessions')->where('user_id', $reseller->id)->delete();

        return back()->with('success', "Password {$reseller->name} diganti. Sesi login lamanya sudah dikeluarkan.");
    }

    public function destroy(User $reseller)
    {
        abort_if($reseller->isAdmin(), 404);

        $n = $reseller->cards()->count();
        DB::table('sessions')->where('user_id', $reseller->id)->delete();
        $reseller->delete();

        return redirect()->route('dashboard.resellers.index')
            ->with('success', "Akun {$reseller->name} dihapus. {$n} card-nya kembali ke stok admin dan tetap berfungsi.");
    }
}
