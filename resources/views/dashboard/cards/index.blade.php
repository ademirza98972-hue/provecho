@extends('layouts.app')
@section('title', 'Kelola Card')
@section('content')

<div class="stack">

    {{-- Toolbar: cari / filter / generate --}}
    <div class="panel panel-body">
        <div class="toolbar">
            <form method="GET" action="{{ route('dashboard.cards.index') }}" class="toolbar-group">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari ID atau nama toko" style="width:220px">
                <select name="status" style="width:155px">
                    <option value="">Semua status</option>
                    <option value="active"   {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Belum aktif</option>
                    <option value="disabled" {{ request('status') === 'disabled' ? 'selected' : '' }}>Dinonaktifkan</option>
                </select>
                <button type="submit" class="btn btn-outline btn-sm">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
                    Cari
                </button>
                @if(request('search') || request('status'))
                    <a href="{{ route('dashboard.cards.index') }}" class="btn btn-ghost btn-sm">Reset</a>
                @endif
            </form>

            <form method="POST" action="{{ route('dashboard.cards.generate') }}" class="toolbar-group">
                @csrf
                <input type="number" name="count" min="1" max="500" value="10" style="width:84px" aria-label="Jumlah card">
                <button type="submit" class="btn btn-primary btn-sm">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14"/><path d="M5 12h14"/></svg>
                    Generate Card
                </button>
            </form>
        </div>
    </div>

    {{-- Tabel + pilih card untuk dicetak --}}
    <form method="POST" action="{{ route('dashboard.cards.export.pdf') }}"
          x-data="{ ids: @js($cards->pluck('id')), sel: [], mode: 'a4' }">
        @csrf
        <input type="hidden" name="mode" :value="mode">

        <div class="panel">
            <div class="panel-head">
                <span class="panel-title">
                    <span x-show="!sel.length">{{ $cards->total() }} card</span>
                    <span x-show="sel.length" x-cloak><span x-text="sel.length"></span> card dipilih</span>
                </span>

                <div class="toolbar-group">
                    <button type="button" class="btn btn-ghost btn-sm" x-show="sel.length" x-cloak @click="sel = []">
                        Batal pilih
                    </button>
                    <select x-model="mode" class="btn-sm" x-show="sel.length" x-cloak style="border:1px solid var(--border);border-radius:6px;padding:5px 8px;font-size:12px;font-weight:600;background:#fff;cursor:pointer">
                        <option value="a4">Cetak A4</option>
                        <option value="sticker">Sticker 100×50 cm</option>
                    </select>
                    <button type="submit" class="btn btn-primary btn-sm" :disabled="!sel.length">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9V3h12v6"/><path d="M6 18H5a2 2 0 0 1-2-2v-4a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v4a2 2 0 0 1-2 2h-1"/><rect x="6" y="14" width="12" height="7" rx="1.5"/></svg>
                        Cetak Terpilih
                    </button>
                </div>
            </div>

            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th class="check">
                                <input type="checkbox" aria-label="Pilih semua"
                                       :checked="ids.length > 0 && sel.length === ids.length"
                                       @change="sel = $event.target.checked ? [...ids] : []">
                            </th>
                            <th>ID Card</th>
                            <th>Status</th>
                            <th>Nama Toko</th>
                            <th>Diaktifkan</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($cards as $card)
                        <tr>
                            <td class="check">
                                <input type="checkbox" name="ids[]" value="{{ $card->id }}" x-model="sel"
                                       aria-label="Pilih {{ $card->id }}">
                            </td>
                            <td><span class="mono">{{ $card->id }}</span></td>
                            <td>
                                <span class="badge badge-{{ $card->status }}">
                                    <span class="dot"></span>
                                    {{ ['active' => 'Aktif', 'inactive' => 'Belum aktif', 'disabled' => 'Nonaktif'][$card->status] }}
                                </span>
                            </td>
                            <td>{{ $card->owner_name ?? '—' }}</td>
                            <td style="color:var(--muted)">{{ $card->activated_at?->format('d M Y') ?? '—' }}</td>
                            <td style="text-align:right">
                                <a href="{{ route('dashboard.cards.show', $card) }}" class="btn btn-outline btn-sm">Detail</a>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="empty">Tidak ada card yang cocok</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $cards->links('vendor.pagination.simple') }}
        </div>
    </form>

</div>

@push('styles')
<style>[x-cloak]{display:none!important}</style>
@endpush

@endsection
