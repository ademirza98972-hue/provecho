@extends('layouts.app')
@section('title', 'Dashboard')
@section('content')

<div class="stack">
    <div class="stat-grid">
        <div class="stat">
            <div class="stat-label" style="color:var(--muted)">Total Card</div>
            <div class="stat-num">{{ $stats['total'] }}</div>
        </div>
        <div class="stat">
            <div class="stat-label" style="color:var(--ok)"><span class="dot"></span>Aktif</div>
            <div class="stat-num">{{ $stats['active'] }}</div>
        </div>
        <div class="stat">
            <div class="stat-label" style="color:var(--warn)"><span class="dot"></span>Belum Aktif</div>
            <div class="stat-num">{{ $stats['inactive'] }}</div>
        </div>
        <div class="stat">
            <div class="stat-label" style="color:var(--bad)"><span class="dot"></span>Dinonaktifkan</div>
            <div class="stat-num">{{ $stats['disabled'] }}</div>
        </div>
    </div>

    <div class="panel">
        <div class="panel-head">
            <span class="panel-title">Terakhir Diaktifkan</span>
            <a href="{{ route('dashboard.cards.index') }}" class="btn btn-ghost btn-sm">
                Lihat Semua
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
            </a>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>ID Card</th>
                        <th>Nama Toko</th>
                        <th>Diaktifkan</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recent as $card)
                    <tr>
                        <td><span class="mono">{{ $card->id }}</span></td>
                        <td>{{ $card->owner_name }}</td>
                        <td style="color:var(--muted)">{{ $card->activated_at?->diffForHumans() }}</td>
                        <td style="text-align:right">
                            <a href="{{ route('dashboard.cards.show', $card) }}" class="btn btn-outline btn-sm">Detail</a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="empty">Belum ada card yang diaktifkan</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection
