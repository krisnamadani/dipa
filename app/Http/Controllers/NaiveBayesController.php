<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Data;
use App\Models\Dataset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NaiveBayesController extends Controller
{
    public function index()
    {
        // single

        // $time = Carbon::now()->format('H');
        // $day = Carbon::now()->dayOfWeek + 1;

        // return $this->naivebayes($time, $day);

        
        // multiple

        $hasil = $this->run_naivebayes();

        $total = collect($hasil)->count();
        $benar = collect($hasil)->where('hasil', true)->count();
        $salah = collect($hasil)->where('hasil', false)->count();

        $return = [
            'total_data' => $total,
            'benar' => $benar,
            'salah' => $salah,
            'akurasi' => $benar / $total,
        ];

        return $return;
    }

    public function naivebayes($uji_waktu, $uji_kode_hari)
    {
        $data = Data::get();

        Dataset::truncate();
        $data->each(function ($item) {
            $output = [
                $this->normalize($item->teras_rumah),
                $this->normalize($item->ruang_tamu),
                $this->normalize($item->kamar_utama),
                $this->normalize($item->kamar_kedua),
                $this->normalize($item->dapur),
                $this->normalize($item->toilet),
            ];

            Dataset::create([
                'waktu' => Carbon::parse($item->waktu)->format('H'),
                'kode_hari' => $item->kode_hari,
                'output' => json_encode($output),
            ]);
        });

        $waktu = Dataset::get()->pluck('waktu')->toArray();
        $waktu = array_map("unserialize", array_unique(array_map("serialize", $waktu)));

        $kode_hari = Dataset::get()->pluck('kode_hari')->toArray();
        $kode_hari = array_map("unserialize", array_unique(array_map("serialize", $kode_hari)));

        $output = Dataset::get()->pluck('output')->toArray();
        $output = array_map("unserialize", array_unique(array_map("serialize", $output)));

        $probabilitas_output = array();

        foreach($output as $item) {
            $jumlah_output = Dataset::where('output', $item)->count();
            $jumlah_data = Dataset::count();

            $probabilitas_output[$item] = $jumlah_output / $jumlah_data;
        }

        $probabilitas_waktu = array();

        foreach($waktu as $item) {
            foreach($output as $item2) {
                $jumlah_waktu_output = Dataset::where('output', $item2)->where('waktu', $item)->count();
                $jumlah_output = Dataset::where('output', $item2)->count();
                $probabilitas_waktu[$item][$item2] = $jumlah_waktu_output / $jumlah_output;
            }
        }

        $probabilitas_kode_hari = array();

        foreach($kode_hari as $item) {
            foreach($output as $item2) {
                $jumlah_kode_hari_output = Dataset::where('output', $item2)->where('kode_hari', $item)->count();
                $jumlah_output = Dataset::where('output', $item2)->count();
                $probabilitas_kode_hari[$item][$item2] = $jumlah_kode_hari_output / $jumlah_output;
            }
        }

        $data_uji = [
            'waktu' => $uji_waktu,
            'kode_hari' => $uji_kode_hari,
        ];

        $uji = array();

        foreach($output as $item) {
            $uji[$item]['output'] = $probabilitas_output[$item];
            $uji[$item]['waktu'] = $probabilitas_waktu[$data_uji['waktu']][$item];
            $uji[$item]['kode_hari'] = $probabilitas_kode_hari[$data_uji['kode_hari']][$item];

            $uji[$item] = array_product($uji[$item]);
        }

        return array_search(max($uji), $uji);
    }

    public function run_naivebayes()
    {
        $data = Data::get();

        Dataset::truncate();
        $data->each(function ($item) {
            $output = [
                $this->normalize($item->teras_rumah),
                $this->normalize($item->ruang_tamu),
                $this->normalize($item->kamar_utama),
                $this->normalize($item->kamar_kedua),
                $this->normalize($item->dapur),
                $this->normalize($item->toilet),
            ];

            Dataset::create([
                'waktu' => Carbon::parse($item->waktu)->format('H'),
                'kode_hari' => $item->kode_hari,
                'output' => json_encode($output),
            ]);
        });

        $waktu = Dataset::get()->pluck('waktu')->toArray();
        $waktu = array_map("unserialize", array_unique(array_map("serialize", $waktu)));

        $kode_hari = Dataset::get()->pluck('kode_hari')->toArray();
        $kode_hari = array_map("unserialize", array_unique(array_map("serialize", $kode_hari)));

        $output = Dataset::get()->pluck('output')->toArray();
        $output = array_map("unserialize", array_unique(array_map("serialize", $output)));

        $probabilitas_output = array();

        foreach($output as $item) {
            $jumlah_output = Dataset::where('output', $item)->count();
            $jumlah_data = Dataset::count();

            $probabilitas_output[$item] = $jumlah_output / $jumlah_data;
        }

        $probabilitas_waktu = array();

        foreach($waktu as $item) {
            foreach($output as $item2) {
                $jumlah_waktu_output = Dataset::where('output', $item2)->where('waktu', $item)->count();
                $jumlah_output = Dataset::where('output', $item2)->count();
                $probabilitas_waktu[$item][$item2] = $jumlah_waktu_output / $jumlah_output;
            }
        }

        $probabilitas_kode_hari = array();

        foreach($kode_hari as $item) {
            foreach($output as $item2) {
                $jumlah_kode_hari_output = Dataset::where('output', $item2)->where('kode_hari', $item)->count();
                $jumlah_output = Dataset::where('output', $item2)->count();
                $probabilitas_kode_hari[$item][$item2] = $jumlah_kode_hari_output / $jumlah_output;
            }
        }

        $dataset = Dataset::get();

        $uji_all = array();

        foreach($dataset as $item) {
            $data_uji = [
                'waktu' => $item->waktu,
                'kode_hari' => $item->kode_hari,
            ];
    
            $uji = array();
    
            foreach($output as $item2) {
                $uji[$item2]['output'] = $probabilitas_output[$item2];
                $uji[$item2]['waktu'] = $probabilitas_waktu[$data_uji['waktu']][$item2];
                $uji[$item2]['kode_hari'] = $probabilitas_kode_hari[$data_uji['kode_hari']][$item2];
    
                $uji[$item2] = array_product($uji[$item2]);
            }
    
            $uji_all[]['hasil'] = (array_search(max($uji), $uji) == $item->output);
        }

        return $uji_all;
    }

    public function normalize($value) {
        if ($value > 0) {
            return 1;
        }

        return 0;
    }
}
