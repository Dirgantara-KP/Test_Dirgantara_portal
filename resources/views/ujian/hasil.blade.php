<x-app-layout>
    <div class="max-w-3xl mx-auto py-8">
        <div class="border rounded p-6 mb-6">
            <h1 class="text-lg font-semibold mb-2">Hasil Ujian: {{ $event->nama_event }}</h1>
            <div class="text-sm text-gray-600">
                <div>Skor PG (otomatis): {{ $pivot->skor_pg ?? '-' }}</div>
                <div>Skor Esai (dinilai admin): {{ $pivot->skor_esai_manual ?? 'Menunggu penilaian' }}</div>
                <div class="font-medium mt-1">
                    Skor Akhir: {{ ($pivot->skor_pg ?? 0) + ($pivot->skor_esai_manual ?? 0) }}
                    @if (is_null($pivot->skor_esai_manual) && $jawaban->whereNotNull('jawaban_esai')->isNotEmpty())
                        <span class="text-yellow-600">(sementara, esai belum dinilai)</span>
                    @endif
                </div>
            </div>
        </div>

        @foreach ($jawaban as $i => $j)
            <div class="border rounded p-4 mb-3">
                <p class="font-medium mb-2">{{ $i + 1 }}. {{ $j->soal->pertanyaan }}</p>

                @if ($j->soal->isPg())
                    <p class="text-sm">Jawaban Anda: <span class="font-medium">{{ $j->jawabanSoal->jawaban ?? '-' }}</span></p>
                    <p class="text-sm">Jawaban Benar:
                        <span class="font-medium">{{ optional($j->soal->jawabanSoals->firstWhere('nilai', true))->jawaban }}</span>
                    </p>
                    @if ($j->is_benar)
                        <span class="inline-block mt-1 px-2 py-1 text-xs rounded bg-green-100 text-green-700">Benar</span>
                    @else
                        <span class="inline-block mt-1 px-2 py-1 text-xs rounded bg-red-100 text-red-700">Salah</span>
                    @endif
                @else
                    <p class="text-sm">Jawaban Anda: {{ $j->jawaban_esai }}</p>
                    <span class="inline-block mt-1 px-2 py-1 text-xs rounded {{ is_null($j->nilai_manual) ? 'bg-gray-100 text-gray-600' : 'bg-blue-100 text-blue-700' }}">
                        {{ is_null($j->nilai_manual) ? 'Menunggu penilaian' : 'Nilai: ' . $j->nilai_manual }}
                    </span>
                @endif
            </div>
        @endforeach

        <a href="{{ route('ujian.index') }}" class="text-indigo-600 text-sm">&larr; Kembali ke daftar ujian</a>
    </div>
</x-app-layout>
