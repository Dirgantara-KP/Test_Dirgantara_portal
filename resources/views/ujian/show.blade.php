<x-app-layout>
    <div class="max-w-3xl mx-auto py-8">
        <div class="border rounded p-6 mb-6">
            <h1 class="text-lg font-semibold mb-2">{{ $event->nama_event }}</h1>
            <div class="text-sm text-gray-600">
                <div>Nama Peserta: {{ $peserta->nama }}</div>
                <div>NIK: {{ $peserta->nik }}</div>
                <div>Organisasi: {{ $peserta->organisasi }}</div>
                <div>Job Title: {{ $peserta->jobTitle?->nama_jobtitle }}</div>
            </div>
        </div>

        @if ($kataPengantarPg)
            <div class="prose mb-6">{!! $kataPengantarPg->deskripsi !!}</div>
        @endif

        <form id="form-ujian">
            @csrf
            @foreach ($soals as $i => $soal)
                <div class="border rounded p-4 mb-4" data-soal-id="{{ $soal->id }}">
                    <p class="font-medium mb-2">{{ $i + 1 }}. {{ $soal->pertanyaan }}</p>

                    @if ($soal->gambar_pertanyaan)
                        <img src="{{ Storage::url($soal->gambar_pertanyaan) }}" class="max-w-xs mb-2">
                    @endif

                    @if ($soal->isPg())
                        <div class="space-y-2">
                            @foreach ($soal->jawabanSoals as $opsi)
                                <label class="flex items-center gap-2">
                                    <input type="radio" name="jawaban[{{ $soal->id }}][jawaban_soal_id]"
                                           value="{{ $opsi->id }}"
                                           {{ (int) ($jawabanTersimpan[$soal->id] ?? null) === $opsi->id ? 'checked' : '' }}>
                                    <span>{{ $opsi->jawaban }}</span>
                                    @if ($opsi->gambar_jawaban)
                                        <img src="{{ Storage::url($opsi->gambar_jawaban) }}" class="h-10">
                                    @endif
                                </label>
                            @endforeach
                        </div>
                    @else
                        <textarea name="jawaban[{{ $soal->id }}][jawaban_esai]" rows="3"
                                  class="w-full border rounded p-2">{{ $jawabanTersimpan[$soal->id] ?? '' }}</textarea>
                    @endif

                    <input type="hidden" class="soal-id-field" value="{{ $soal->id }}">
                </div>
            @endforeach

            <button type="button" id="btn-selesai" class="px-6 py-2 bg-indigo-600 text-white rounded">
                Selesai & Kumpulkan
            </button>
            <span id="status-simpan" class="text-sm text-gray-500 ml-3"></span>
        </form>
    </div>

    <script>
        const eventId = {{ $event->id }};
        const simpanUrl = "{{ route('ujian.simpan', $event) }}";
        const selesaiUrl = "{{ route('ujian.selesai', $event) }}";
        let terjawab = 0;

        function kumpulkanJawaban() {
            const data = [];
            document.querySelectorAll('[data-soal-id]').forEach(box => {
                const soalId = box.dataset.soalId;
                const radio = box.querySelector('input[type=radio]:checked');
                const textarea = box.querySelector('textarea');
                if (radio) {
                    data.push({ soal_id: soalId, jawaban_soal_id: radio.value });
                } else if (textarea && textarea.value.trim() !== '') {
                    data.push({ soal_id: soalId, jawaban_esai: textarea.value });
                }
            });
            return data;
        }

        function simpanOtomatis() {
            fetch(simpanUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                },
                body: JSON.stringify({ jawaban: kumpulkanJawaban() }),
            }).then(() => {
                document.getElementById('status-simpan').innerText = 'Tersimpan otomatis';
            });
        }

        // "keterangan: simpan per 10 data" -> auto-save tiap 10 kali perubahan jawaban
        document.getElementById('form-ujian').addEventListener('change', () => {
            terjawab++;
            if (terjawab % 10 === 0) simpanOtomatis();
        });

        document.getElementById('btn-selesai').addEventListener('click', () => {
            if (!confirm('Yakin ingin mengumpulkan jawaban? Jawaban tidak bisa diubah lagi setelah ini.')) return;
            fetch(simpanUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                },
                body: JSON.stringify({ jawaban: kumpulkanJawaban() }),
            }).then(() => { window.location.href = selesaiUrl; });
        });
    </script>
</x-app-layout>
