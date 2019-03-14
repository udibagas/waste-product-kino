<?php 

namespace App\Imports;

use App\KonversiBerat;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class KonversiBeratImport implements ToModel, WithChunkReading
{
    public function model(array $row)
    {
        $konversi = KonversiBerat::where('material_id', $row[2])->first();

        if ($konversi) {
            $konversi->update([
                'kategori_jual' => $row[0],
                'finished_good' => $row[1],
                'material_description' => $row[3],
                'berat' => $row[4] ? str_replace(',', '.', $row[4]) : 0,
                'keterangan' => $row[5]
            ]);

            return $konversi;
        }

        return new KonversiBerat([
           'kategori_jual' => $row[0],
           'finished_good' => $row[1],
           'material_id' => $row[2],
           'material_description' => $row[3],
           'berat' => $row[4] ? str_replace(',', '.', $row[4]) : 0,
           'keterangan' => $row[5]
        ]);
    }

    public function batchSize(): int
    {
        return 100;
    }

    public function chunkSize(): int
    {
        return 500;
    }
}