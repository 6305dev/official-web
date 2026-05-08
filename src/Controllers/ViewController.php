<?php

namespace App\Controllers;

use App\Databases\SQLite;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class ViewController
{
    private SQLite $sqlite;
    private Environment $twig;

    public function __construct()
    {
        $dbPath = __DIR__ . '/../../src/Databases/data.db';
        $this->sqlite = new SQLite($dbPath);

        $loader = new FilesystemLoader(__DIR__ . '/../../src/Pages');
        $this->twig = new Environment($loader, [
            'cache' => false,
            'auto_reload' => true,
        ]);

        $this->twig->addFilter(new \Twig\TwigFilter('tgl_indo', function ($date) {
            $months = [
                'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
            ];
            $timestamp = is_numeric($date) ? $date : strtotime($date);
            if (!$timestamp) return $date;

            $d = date('d', $timestamp);
            $m = $months[date('n', $timestamp) - 1];
            $y = date('Y', $timestamp);

            return "$d $m $y";
        }));
    }

    public function render(string $template, array $data = []): void
    {
        echo $this->twig->render($template . '.twig', $data);
    }

    public function home(): void
    {
        $result = $this->sqlite->read('articles', [], 6);
        $this->render('home', ['title' => 'DISDUKCAPIL TAPIN', 'data' => $result]);
    }

    public function preview(string $permalink): void
    {
        $result = $this->sqlite->read('pages', ['permalink' => $permalink]);
        $this->render(
            'preview',
            [
                'title' => strtoupper($result[0]['title']) . ' - DISDUKCAPIL TAPIN',
                'data' => $result[0]
            ]
        );
    }

    public function document(string $category): void
    {

        switch ($category) {
            case 'ppid':
                $subcategory = 'UMUM';
                $subtitle = 'PPID';
                $result = $this->sqlite->read('documents', ['category' => $subcategory]);
                $this->render('document', [
                    'title' => $subtitle . ' - DISDUKCAPIL TAPIN',
                    'subtitle' => $subtitle,
                    'data' => $result
                ]);
                break;

            case 'laporan-hasil-skm':
                $subcategory = 'SKM';
                $subtitle = 'Laporan Hasil SKM';
                $result = $this->sqlite->read('documents', ['category' => $subcategory]);
                $this->render('document', [
                    'title' => $subtitle . ' - DISDUKCAPIL TAPIN',
                    'subtitle' => $subtitle,
                    'data' => $result
                ]);
                break;

            case 'laporan-pengaduan':
                $subcategory = 'PENGADUAN';
                $subtitle = 'Laporan Konsultasi dan Pengaduan';
                $result = $this->sqlite->read('documents', ['category' => $subcategory]);
                $this->render('document', [
                    'title' => $subtitle . ' - DISDUKCAPIL TAPIN',
                    'subtitle' => $subtitle,
                    'data' => $result
                ]);
                break;

            case 'undang-undang-adminduk':
                $subcategory = 'UU';
                $subtitle = 'Undang - Undang Adminduk';
                $result = $this->sqlite->read('documents', ['category' => $subcategory]);
                $this->render('document', [
                    'title' => $subtitle . ' - DISDUKCAPIL TAPIN',
                    'subtitle' => $subtitle,
                    'data' => $result
                ]);
                break;

            case 'standar-operasional-prosedur':
                $subcategory = 'SOP';
                $subtitle = 'Standar Operasional Prosedur';
                $result = $this->sqlite->read('documents', ['category' => $subcategory]);
                $this->render('document', [
                    'title' => $subtitle . ' - DISDUKCAPIL TAPIN',
                    'subtitle' => $subtitle,
                    'data' => $result
                ]);
                break;

            case 'standar-pelayan':
                $subcategory = 'SP';
                $subtitle = 'Standar Pelayanan';
                $result = $this->sqlite->read('documents', ['category' => $subcategory]);
                $this->render('document', [
                    'title' => $subtitle . ' - DISDUKCAPIL TAPIN',
                    'subtitle' => $subtitle,
                    'data' => $result
                ]);
                break;

            default:
                $this->render('404', [
                    'title' => ' - DISDUKCAPIL TAPIN'
                ]);
                break;
        }
    }

    public function login(): void
    {
        $this->render('login', ['title' => 'LOGIN - DISDUKCAPIL TAPIN']);
    }

    public function dashboard(): void
    {
        $data = $this->sqlite->read('articles', [], 10);
        $documents = $this->sqlite->read('documents');
        $this->render('dashboard', [
            'title' => 'DASHBOARD - DISDUKCAPIL TAPIN', 
            'data' => $data,
            'documents' => $documents
        ]);
    }


    public function notFound(): void
    {
        $this->render('404', ['title' => 'Halaman Tidak Ditemukan']);
    }
}
