<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class SettingsController extends Controller
{
    public function index()
    {
        return view('dashboard.settings');
    }

    public function updateBrand(Request $request)
    {
        abort_if($request->user()->isAdmin(), 403);

        return self::saveBrand($request, $request->user());
    }

    /** Dipakai reseller (Pengaturan) dan admin (Reseller → Atur tampilan). */
    public static function saveBrand(Request $request, User $user)
    {
        $data = $request->validate([
            'brand_name'  => 'nullable|string|max:40',
            'brand_color' => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'brand_logo'  => 'nullable|file|mimes:png,jpg,jpeg,webp|max:2048',
            'remove_logo' => 'nullable|boolean',
        ], [
            'brand_name.max'    => 'Nama brand maksimal 40 karakter.',
            'brand_color.regex' => 'Warna tidak valid.',
            'brand_logo.mimes'  => 'Logo harus PNG, JPG, atau WebP.',
            'brand_logo.max'    => 'Ukuran logo maksimal 2 MB.',
        ]);

        $update = [
            'brand_name'  => trim((string) ($data['brand_name'] ?? '')) ?: null,
            'brand_color' => isset($data['brand_color']) ? strtoupper($data['brand_color']) : null,
        ];

        if ($request->boolean('remove_logo')) {
            $update['brand_logo'] = null;
        } elseif ($request->hasFile('brand_logo')) {
            $logo = self::shrinkLogo($request->file('brand_logo')->getRealPath());
            if (! $logo) {
                return back()->withErrors(['brand_logo' => 'Logo tidak bisa dibaca atau terlalu besar (maksimal 5000 piksel). Coba simpan ulang gambarnya sebagai PNG yang lebih kecil.']);
            }
            $update['brand_logo'] = $logo;
        }

        $user->forceFill($update)->save();

        return back()->with('success', 'Tampilan halaman aktivasi disimpan.');
    }

    /** Perkecil ke maks. 320px dan simpan sebagai data URI PNG (transparansi tetap). */
    private static function shrinkLogo(string $path): ?string
    {
        $size = @getimagesize($path);
        if (! $size || $size[0] > 5000 || $size[1] > 5000) {
            return null;
        }

        $src = @imagecreatefromstring((string) file_get_contents($path));
        if (! $src) {
            return null;
        }

        $w = imagesx($src);
        $h = imagesy($src);
        $scale = min(1, 320 / max($w, $h));
        $nw = max(1, (int) round($w * $scale));
        $nh = max(1, (int) round($h * $scale));

        $dst = imagecreatetruecolor($nw, $nh);
        imagealphablending($dst, false);
        imagesavealpha($dst, true);
        imagefill($dst, 0, 0, imagecolorallocatealpha($dst, 0, 0, 0, 127));
        imagecopyresampled($dst, $src, 0, 0, 0, 0, $nw, $nh, $w, $h);

        ob_start();
        imagepng($dst, null, 9);

        return 'data:image/png;base64,' . base64_encode(ob_get_clean());
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password'         => 'required|string|min:8|confirmed',
        ], [
            'current_password.required' => 'Isi password lama.',
            'password.required'         => 'Isi password baru.',
            'password.min'              => 'Password baru minimal 8 karakter.',
            'password.confirmed'        => 'Konfirmasi password baru tidak sama.',
        ]);

        if (! Hash::check($request->current_password, $request->user()->password)) {
            return back()->withErrors(['current_password' => 'Password lama salah.']);
        }

        $request->user()->update(['password' => $request->password]);

        Auth::login($request->user());

        return back()->with('success', 'Password berhasil diubah.');
    }
}
