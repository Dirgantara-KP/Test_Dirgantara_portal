<x-app-layout>
    <div class="max-w-3xl mx-auto py-8">
        <h1 class="text-xl font-semibold mb-4">Daftar Ujian Saya</h1>

        @if (session('success'))
            <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
        @endif
        @if (session('info'))
            <div class="mb-4 p-3 bg-blue-100 text-blue-800 rounded">{{ session('info') }}</div>
        @endif

        <div class="space-y-3">
            @forelse ($events as $event)
                <div class="border rounded p-4 flex justify-between items-center">
                    <div>
                        <div class="font-medium">{{ $event->nama_event }}</div>
                        <div class="text-sm text-gray-500">
                            {{ $event->tanggal_event->format('d M Y') }},
                            {{ \Illuminate\Support\Carbon::parse($event->jam_mulai)->format('H:i') }} -
                            {{ \Illuminate\Support\Carbon::parse($event->jam_selesai)->format('H:i') }}
                        </div>
                        <div class="text-sm">Status: {{ $event->pivot->status_pengerjaan }}</div>
                    </div>

                    @if ($event->pivot->status_pengerjaan === 'selesai')
                        <a href="{{ route('ujian.hasil', $event) }}" class="text-indigo-600 text-sm underline">Lihat Hasil</a>
                    @elseif ($event->sedangBerlangsung())
                        <a href="{{ route('ujian.show', $event) }}"
                           class="px-4 py-2 bg-indigo-600 text-white rounded">Mulai / Lanjutkan</a>
                    @else
                        <span class="text-gray-400 text-sm">Belum dibuka</span>
                    @endif
                </div>
            @empty
                <p class="text-gray-500">Belum ada jadwal ujian untuk Anda.</p>
            @endforelse
        </div>
    </div>
</x-app-layout>
