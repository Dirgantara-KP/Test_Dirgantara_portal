<?php

namespace App\Imports;

use App\Models\JobTitle;
use App\Models\Peserta;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithUpserts;
use Maatwebsite\Excel\Concerns\WithValidation;

/**
 * Format kolom Excel (baris pertama = header):
 * nik | nama | organisasi | email | job_title
 *
 * - job_title diisi nama job title (akan dicari / dibuat otomatis)
 */
class PesertaImport implements ToModel, WithHeadingRow, WithUpserts, WithValidation
{
    public function model(array $row)
    {
        $jobTitle = null;
        if (! empty(trim($row['job_title']))) {
            $jobTitle = JobTitle::firstOrCreate(['nama_jobtitle' => trim($row['job_title'])]);
        }

        return new Peserta([
            'nik' => trim($row['nik']),
            'nama' => trim($row['nama']),
            'organisasi' => ! empty($row['organisasi']) ? trim($row['organisasi']) : null,
            'email' => trim($row['email']),
            'job_title_id' => $jobTitle?->id,
        ]);
    }

    public function rules(): array
    {
        return [
            'nik' => 'required',
            'nama' => 'required',
            'email' => 'required',
            'job_title' => 'nullable',
            'organisasi' => 'nullable',
        ];
    }

    public function uniqueBy()
    {
        return 'nik';
    }
}
