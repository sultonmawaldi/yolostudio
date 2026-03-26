<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Gallery;
use Carbon\Carbon;

class GallerySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Self Photo Studio',
            'Personal Selfphoto Studio',
            'Pas Photo',
            'Widebox Maroon',
            'Widebox Grey',
            'Corner Box',
            'Gorden Box',
        ];

        for ($i = 1; $i <= 12; $i++) {
            $category = $categories[array_rand($categories)];

            Gallery::create([
                'title' => "Gallery $i",
                'description' => "Deskripsi singkat gallery $i",
                'image' => "sample$i.jpg", // pastikan file ada di uploads/gallery/
                'category' => $category,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}
