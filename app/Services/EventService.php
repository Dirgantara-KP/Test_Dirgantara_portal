<?php

namespace App\Services;

use App\Models\Event;
use App\Models\Peserta;
use Illuminate\Support\Facades\DB;

class EventService
{
    /**
     * Attach peserta secara otomatis berdasarkan Job Title yang dipilih di event.
     * Jika peserta sudah terdaftar di event, tidak akan diduplikasi.
     * Setiap peserta baru akan dibuatkan baris pivot dengan status 'belum'.
     */
    public function attachPesertaOtomatis(Event $event, array $jobTitleIds): int
    {
        $pesertaIds = Peserta::whereIn('job_title_id', $jobTitleIds)
            ->pluck('id')
            ->toArray();

        return $this->attachPeserta($event, $pesertaIds);
    }

    /**
     * Attach peserta spesifik ke event.
     * Hanya peserta yang belum terdaftar yang akan ditambahkan.
     * Setiap peserta baru akan dibuatkan baris pivot dengan status 'belum'.
     */
    public function attachPeserta(Event $event, array $pesertaIds): int
    {
        $existingIds = DB::table('event_peserta')
            ->where('event_id', $event->id)
            ->whereIn('peserta_id', $pesertaIds)
            ->pluck('peserta_id')
            ->toArray();

        $newIds = array_diff($pesertaIds, $existingIds);

        if (empty($newIds)) {
            return 0;
        }

        $now = now();
        $insertData = [];
        foreach ($newIds as $pesertaId) {
            $insertData[] = [
                'event_id' => $event->id,
                'peserta_id' => $pesertaId,
                'status_pengerjaan' => 'belum',
                'skor_pg' => 0,
                'skor_esai_manual' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        DB::table('event_peserta')->insert($insertData);

        // Update counter jml_peserta
        $event->hitungJmlPeserta();

        return count($newIds);
    }

    /**
     * Sinkronisasi peserta di event: hapus semua yang tidak ada di daftar, tambah yang baru.
     * Hanya untuk mode manual (bukan otomatis berdasarkan job title).
     */
    public function syncPeserta(Event $event, array $pesertaIds): void
    {
        $event->pesertas()->sync($pesertaIds);

        // Update status_pengerjaan untuk peserta yang baru di-attach
        DB::table('event_peserta')
            ->where('event_id', $event->id)
            ->whereNotIn('status_pengerjaan', ['sedang', 'selesai'])
            ->update([
                'status_pengerjaan' => 'belum',
                'skor_pg' => 0,
                'skor_esai_manual' => 0,
            ]);

        $event->hitungJmlPeserta();
    }

    /**
     * Hapus semua peserta dari event.
     */
    public function detachAllPeserta(Event $event): void
    {
        DB::table('event_peserta')
            ->where('event_id', $event->id)
            ->delete();

        $event->hitungJmlPeserta();
    }

    /**
     * Sinkronisasi Job Title ke event.
     */
    public function syncJobTitles(Event $event, array $jobTitleIds): void
    {
        $event->jobTitles()->sync($jobTitleIds);
    }
}

