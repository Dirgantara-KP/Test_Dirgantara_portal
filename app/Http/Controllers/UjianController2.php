<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\HasilJawabanPeserta;
use App\Models\KataPengantar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UjianController extends Controller
{
    // Daftar event yang bisa dikerjakan peserta yang sedang login
    public function index()
    {
        $peserta = Auth::user()->peserta;

        $events = $peserta->events()->orderBy('tanggal_event')->get();

        // Kirim status verifikasi password per event ke view
        $verifiedPasswords = [];
        foreach ($events as $event) {
            $verifiedPasswords[$event->id] = session('event_password_verified_' . $event->id, false);
        }

        return view('ujian.index', compact('events', 'verifiedPasswords'));
    }

    /**
     * Verifikasi password event oleh peserta.
     */
    public function verifikasiPassword(Request $request, Event $event)
    {
        $peserta = Auth::user()->peserta;

        abort_unless($peserta->events()->where('events.id', $event->id)->exists(), 403, 'Anda tidak terdaftar di event ini');

        $request->validate([
            'password_event' => 'required|string',
        ]);

        if (blank($event->password_event)) {
            // Event tidak punya password, langsung verifikasi
            session(['event_password_verified_' . $event->id => true]);
            return redirect()->route('ujian.show', $event);
        }

        if ($request->password_event === $event->password_event) {
            session(['event_password_verified_' . $event->id => true]);
            return redirect()->route('ujian.show', $event);
        }

        return redirect()->route('ujian.index')
            ->with('error', 'Password event tidak sesuai!');
    }

    // Halaman pengerjaan soal untuk 1 event
    public function show(Event $event)
    {
        $peserta = Auth::user()->peserta;

        abort_unless($peserta->events()->where('events.id', $event->id)->exists(), 403, 'Anda tidak terdaftar di event ini');

        // "soal terbuka kalau waktu dan tanggal tepat" -> dicek di sini
        abort_unless($event->sedangBerlangsung(), 403, 'Ujian belum dibuka atau sudah ditutup sesuai jadwal.');

        $pivot = DB::table('event_peserta')
            ->where('event_id', $event->id)->where('peserta_id', $peserta->id)->first();

        if ($pivot->status_pengerjaan === 'selesai') {
            return redirect()->route('ujian.index')->with('info', 'Anda sudah menyelesaikan ujian ini.');
        }

        if ($pivot->status_pengerjaan === 'belum') {
            DB::table('event_peserta')
                ->where('id', $pivot->id)
                ->update(['status_pengerjaan' => 'berlangsung', 'waktu_mulai' => now()]);
        }

        // Ambil soal berdasarkan Job Title yang dipilih di Event (via event_soal),
        // bukan berdasarkan Job Title peserta (cara lama).
$soals = $event->soals()
            ->with(['tipeSoal', 'jawabanSoals'])
            ->get();

$kataPengantarPg = KataPengantar::whereHas('tipeSoal', fn ($q) => $q->whereRaw('LOWER(kode) = ?', ['pg']))
            ->first();
        $kataPengantarEsai = KataPengantar::whereHas('tipeSoal', fn ($q) => $q->whereRaw('LOWER(kode) = ?', ['esai']))
            ->first();

        // jawaban yang sudah tersimpan sebelumnya (kalau sempat terputus di tengah jalan)
        $jawabanTersimpan = HasilJawabanPeserta::where('event_id', $event->id)
            ->where('peserta_id', $peserta->id)
            ->pluck('jawaban_esai', 'soal_id')
            ->merge(
                HasilJawabanPeserta::where('event_id', $event->id)
                    ->where('peserta_id', $peserta->id)
                    ->pluck('jawaban_soal_id', 'soal_id')
            );

        return view('ujian.show', compact('event', 'peserta', 'soals', 'kataPengantarPg', 'kataPengantarEsai', 'jawabanTersimpan'));
    }

    // Autosave jawaban (dipanggil tiap kelipatan 10 soal terjawab, sesuai catatan "simpan per 10 data")
    public function simpanJawaban(Request $request, Event $event)
    {
        $peserta = Auth::user()->peserta;
        abort_unless($event->sedangBerlangsung(), 403);

        $data = $request->validate([
            'jawaban' => 'required|array',
            'jawaban.*.soal_id' => 'required|exists:soals,id',
            'jawaban.*.jawaban_soal_id' => 'nullable|exists:jawaban_soals,id',
            'jawaban.*.jawaban_esai' => 'nullable|string',
        ]);

        foreach ($data['jawaban'] as $item) {
            HasilJawabanPeserta::updateOrCreate(
                [
                    'event_id' => $event->id,
                    'peserta_id' => $peserta->id,
                    'soal_id' => $item['soal_id'],
                ],
                [
                    'jawaban_soal_id' => $item['jawaban_soal_id'] ?? null,
                    'jawaban_esai' => $item['jawaban_esai'] ?? null,
                    'is_benar' => isset($item['jawaban_soal_id'])
                        ? \App\Models\JawabanSoal::find($item['jawaban_soal_id'])?->nilai
                        : null,
                ]
            );
        }

        return response()->json(['status' => 'tersimpan']);
    }

    // Submit final -> hitung skor PG otomatis, esai menunggu dinilai admin
    public function selesai(Event $event)
    {
        $peserta = Auth::user()->peserta;

        $skorPg = HasilJawabanPeserta::where('event_id', $event->id)
            ->where('peserta_id', $peserta->id)
            ->where('is_benar', true)
            ->count();

        DB::table('event_peserta')
            ->where('event_id', $event->id)->where('peserta_id', $peserta->id)
            ->update([
                'status_pengerjaan' => 'selesai',
                'waktu_selesai' => now(),
                'skor_pg' => $skorPg,
            ]);

        return redirect()->route('ujian.index')->with('success', 'Ujian selesai dikumpulkan. Terima kasih.');
    }

    // Peserta melihat hasil (benar/salah) setelah submit
    public function hasil(Event $event)
    {
        $peserta = Auth::user()->peserta;

        $pivot = DB::table('event_peserta')
            ->where('event_id', $event->id)->where('peserta_id', $peserta->id)->first();

        abort_unless($pivot && $pivot->status_pengerjaan === 'selesai', 403, 'Hasil belum tersedia, Anda belum menyelesaikan ujian ini.');

        $jawaban = HasilJawabanPeserta::with(['soal.jawabanSoals', 'jawabanSoal'])
            ->where('event_id', $event->id)
            ->where('peserta_id', $peserta->id)
            ->get();

        return view('ujian.hasil', compact('event', 'jawaban', 'pivot'));
    }
}

