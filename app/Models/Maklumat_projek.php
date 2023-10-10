<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Maklumat_projek extends Model
{
    use HasFactory;
    protected $table = 'maklumat_projek';

    protected $fillable = [
                            'nama_projek',
                            'kod_projek',
                            'kategori_projek',
                            'jenis_kategori_projek',
                            'kos_keseluruhan',
                            'status_perlaksanaan',
                            'kemajuan_semasa',
                            'rolling_plan',
                            'maksud_pembangunan_kementerian',
                            'butiran_program',
                            'sektor_utama',
                            'sub_sektor',
                            'kawasan',
                            'indikator_projek',
                            'tahun_jangka_mula',
                            'tempoh_perlaksanan',
                            'sub_kategori',
                            'sektor',
                            'koridor_pembangunan',
                            'tahun_jangka_siap',
                            'dibuat_oleh',
                            'dibuat_pada',
                            'dikemaskini_oleh',
                            'dikemaskini_pada',
                            'row_status'
                        ];

}
