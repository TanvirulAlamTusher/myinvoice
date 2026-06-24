<?php

namespace Database\Seeders;

use App\Models\InvoiceTemplate;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class InvoiceTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */

    public function run(): void
    {
        $templates = [

            [
                'name' => 'Classic A4',
                'view_name' => 'classic',
                'preview_image' => null,
                'is_default' => true,
                'status' => true,
            ],

            [
                'name' => 'Modern A4',
                'view_name' => 'modern',
                'preview_image' => null,
                'is_default' => false,
                'status' => true,
            ],

            [
                'name' => 'Thermal POS',
                'view_name' => 'thermal',
                'preview_image' => null,
                'is_default' => false,
                'status' => true,
            ],

        ];

        foreach ($templates as $template) {

            InvoiceTemplate::updateOrCreate(

                [
                    'view_name' => $template['view_name']
                ],

                $template
            );
        }
    }
}
