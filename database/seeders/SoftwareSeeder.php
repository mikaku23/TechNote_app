<?php

namespace Database\Seeders;

use App\Models\software;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SoftwareSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        software::insert([
            [
                'nama' => 'Microsoft Office 2019',
                'versi' => '2019',
                'kategori' => 'Productivity',
                'lisensi' => 'XXXXX-XXXXX-XXXXX-XXXXX-XXXXX',
                'developer' => 'Microsoft',
                'tgl_rilis' => '2018-09-24',
                'deskripsi' => 'Suite of productivity applications including Word, Excel, and PowerPoint.',
            ],
            [
                'nama' => 'Adobe Photoshop CC',
                'versi' => '2020',
                'kategori' => 'Graphics',
                'lisensi' => 'YYYYY-YYYYY-YYYYY-YYYYY-YYYYY',
                'developer' => 'Adobe',
                'tgl_rilis' => '2020-01-01',
                'deskripsi' => 'Professional image editing software.',
            ],
            [
                'nama' => 'AutoCAD 2021',
                'versi' => '2021',
                'kategori' => 'Design',
                'lisensi' => 'ZZZZZ-ZZZZZ-ZZZZZ-ZZZZZ-ZZZZZ',
                'developer' => 'Autodesk',
                'tgl_rilis' => '2021-01-01',
                'deskripsi' => 'Professional CAD software.',
            ],
            [
                'nama' => 'Visual Studio Code',
                'versi' => '1.60',
                'kategori' => 'Development',
                'lisensi' => 'Free',
                'developer' => 'Microsoft',
                'tgl_rilis' => '2021-09-01',
                'deskripsi' => 'Source-code editor developed by Microsoft.',
            ],

            [
                'nama' => 'Slack',
                'versi' => '4.20',
                'kategori' => 'Communication',
                'lisensi' => 'Free/Paid',
                'developer' => 'Slack Technologies',
                'tgl_rilis' => '2021-08-01',
                'deskripsi' => 'Collaboration hub that connects work with the people you work with.',
            ],

            [
                'nama' => 'Zoom',
                'versi' => '5.7',
                'kategori' => 'Communication',
                'lisensi' => 'Free/Paid',
                'developer' => 'Zoom Video',
                'tgl_rilis' => '2021-07-01',
                'deskripsi' => 'Video conferencing software.',
            ],

            [
                'nama' => 'Trello',
                'versi' => '2.0',
                'kategori' => 'Productivity',
                'lisensi' => 'Free/Paid',
                'developer' => 'Atlassian',
                'tgl_rilis' => '2021-06-01',
                'deskripsi' => 'Project management tool that uses boards, lists, and cards.',
            ],
            [
                'nama' => 'GitHub Desktop',
                'versi' => '2.9',
                'kategori' => 'Development',
                'lisensi' => 'Free',
                'developer' => 'GitHub',
                'tgl_rilis' => '2021-05-01',
                'deskripsi' => 'Desktop application for GitHub repositories.',
            ],

            [
                'nama' => 'Notion',
                'versi' => '2.0',
                'kategori' => 'Productivity',
                'lisensi' => 'Free/Paid',
                'developer' => 'Notion Labs Inc.',
                'tgl_rilis' => '2021-04-01',
                'deskripsi' => 'All-in-one workspace for notes, tasks, databases, and collaboration.',
            ],

            [
                'nama' => 'Spotify',
                'versi' => '1.1',
                'kategori' => 'Entertainment',
                'lisensi' => 'Free/Paid',
                'developer' => 'Spotify Ltd.',
                'tgl_rilis' => '2021-03-01',
                'deskripsi' => 'Digital music service that gives you access to millions of songs.',
            ],
        ]);
    }
}
