<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Data;
use App\Models\DataLatih;
use App\Models\DataUji;
use Illuminate\Http\Request;

class NaiveBayesController extends Controller
{
    public function index()
    {
        $model = $this->naivebayes();
        
        // uji 1 data

        // $data_uji = [
        //     'kode_jam' => '7',
        //     'kode_hari' => 5,
        // ];

        // $uji = array();

        // foreach($model['output'] as $item) {
        //     $uji[$item]['output'] = $model['probabilitas_output'][$item];
        //     $uji[$item]['kode_jam'] = $model['probabilitas_kode_jam'][$data_uji['kode_jam']][$item];
        //     $uji[$item]['kode_hari'] = $model['probabilitas_kode_hari'][$data_uji['kode_hari']][$item];

        //     $uji[$item] = array_product($uji[$item]);
        // }

        // return json_decode(array_search(max($uji), $uji));

        
        // uji 168 data

        // $semua_data_uji = DataUji::get();

        // $uji_semua = array();

        // foreach($semua_data_uji as $item) {
        //     $data_uji = [
        //         'kode_jam' => $item->kode_jam,
        //         'kode_hari' => $item->kode_hari,
        //     ];
    
        //     $uji = array();
    
        //     foreach($model['output'] as $item2) {
        //         $uji[$item2]['output'] = $model['probabilitas_output'][$item2];
        //         $uji[$item2]['kode_jam'] = $model['probabilitas_kode_jam'][$data_uji['kode_jam']][$item2];
        //         $uji[$item2]['kode_hari'] = $model['probabilitas_kode_hari'][$data_uji['kode_hari']][$item2];
    
        //         $uji[$item2] = array_product($uji[$item2]);
        //     }
    
        //     $uji_semua[]['hasil'] = (array_search(max($uji), $uji) == $item->output);
        // }

        // $data_uji = collect($uji_semua)->count();
        // $benar = collect($uji_semua)->where('hasil', true)->count();
        // $salah = collect($uji_semua)->where('hasil', false)->count();

        // return [
        //     'data_uji' => $data_uji,
        //     'benar' => $benar,
        //     'salah' => $salah,
        //     'akurasi' => $benar / $data_uji,
        // ];


        // uji 168 data confussion matrix

        // $semua_data_uji = DataUji::get();

        // $uji_semua = array();

        // $uji_semua['tp'] = 0;
        // $uji_semua['fn'] = 0;
        // $uji_semua['fp'] = 0;
        // $uji_semua['tn'] = 0;

        // foreach($semua_data_uji as $item) {
        //     $data_uji = [
        //         'kode_jam' => $item->kode_jam,
        //         'kode_hari' => $item->kode_hari,
        //     ];
    
        //     $uji = array();
    
        //     foreach($model['output'] as $item2) {
        //         $uji[$item2]['output'] = $model['probabilitas_output'][$item2];
        //         $uji[$item2]['kode_jam'] = $model['probabilitas_kode_jam'][$data_uji['kode_jam']][$item2];
        //         $uji[$item2]['kode_hari'] = $model['probabilitas_kode_hari'][$data_uji['kode_hari']][$item2];
    
        //         $uji[$item2] = array_product($uji[$item2]);
        //     }

        //     $aktual = json_decode($item->output);
        //     $prediksi = json_decode(array_search(max($uji), $uji));

        //     for($i = 0; $i < count($aktual); $i++) {
        //         if($aktual[$i] == 1 && $prediksi[$i] == 1) {
        //             $uji_semua['tp'] += 1;
        //         } elseif($aktual[$i] == 1 && $prediksi[$i] == 0) {
        //             $uji_semua['fn'] += 1;
        //         } elseif($aktual[$i] == 0 && $prediksi[$i] == 1) {
        //             $uji_semua['fp'] += 1;
        //         } elseif($aktual[$i] == 0 && $prediksi[$i] == 0) {
        //             $uji_semua['tn'] += 1;
        //         }
        //     }
        // }

        // $accuracy = ($uji_semua['tp'] + $uji_semua['tn']) / ($uji_semua['tp'] + $uji_semua['fn'] + $uji_semua['fp'] + $uji_semua['tn']);
        // $precision = ($uji_semua['tp']) / ($uji_semua['tp'] + $uji_semua['fp']);
        // $recall = ($uji_semua['tp']) / ($uji_semua['tp'] + $uji_semua['fn']);
        // $f1_score = (2 * $recall * $precision) / ($recall + $precision);

        // return [
        //     'accuracy' => $accuracy,
        //     'precision' => $precision,
        //     'recall' => $recall,
        //     'f-1 score' => $f1_score,
        // ];
    }

    public function naivebayes()
    {
        $data = Data::get();

        DataLatih::truncate();
        DataUji::truncate();

        $data->each(function ($item) {
            $output = [
                $this->normalize($item->teras_rumah),
                $this->normalize($item->ruang_tamu),
                $this->normalize($item->kamar_utama),
                $this->normalize($item->kamar_kedua),
                $this->normalize($item->dapur),
                $this->normalize($item->toilet),
            ];

            DataLatih::create([
                'kode_jam' => $item->kode_jam,
                'kode_hari' => $item->kode_hari,
                'output' => json_encode($output),
            ]);

            if($item->id <= 168) {
                DataUji::create([
                    'kode_jam' => $item->kode_jam,
                    'kode_hari' => $item->kode_hari,
                    'output' => json_encode($output),
                ]);
            }
        });

        $kode_jam = DataLatih::get()->pluck('kode_jam')->toArray();
        $kode_jam = array_map("unserialize", array_unique(array_map("serialize", $kode_jam)));

        $kode_hari = DataLatih::get()->pluck('kode_hari')->toArray();
        $kode_hari = array_map("unserialize", array_unique(array_map("serialize", $kode_hari)));

        $output = DataLatih::get()->pluck('output')->toArray();
        $output = array_map("unserialize", array_unique(array_map("serialize", $output)));

        $probabilitas_output = array();

        foreach($output as $item) {
            $jumlah_output = DataLatih::where('output', $item)->count();
            $jumlah_data = DataLatih::count();

            $probabilitas_output[$item] = $jumlah_output / $jumlah_data;
        }

        $probabilitas_kode_jam = array();

        foreach($kode_jam as $item) {
            foreach($output as $item2) {
                $jumlah_kode_jam_output = DataLatih::where('output', $item2)->where('kode_jam', $item)->count();
                $jumlah_output = DataLatih::where('output', $item2)->count();

                $probabilitas_kode_jam[$item][$item2] = $jumlah_kode_jam_output / $jumlah_output;
            }
        }

        $probabilitas_kode_hari = array();

        foreach($kode_hari as $item) {
            foreach($output as $item2) {
                $jumlah_kode_hari_output = DataLatih::where('output', $item2)->where('kode_hari', $item)->count();
                $jumlah_output = DataLatih::where('output', $item2)->count();

                $probabilitas_kode_hari[$item][$item2] = $jumlah_kode_hari_output / $jumlah_output;
            }
        }

        $model = array();

        $model = [
            'output' => $output,
            'probabilitas_output' => $probabilitas_output,
            'probabilitas_kode_jam' => $probabilitas_kode_jam,
            'probabilitas_kode_hari' => $probabilitas_kode_hari,
        ];

        return $model;
    }

    public function normalize($value) {
        if ($value > 0) {
            return 1;
        }

        return 0;
    }
}
