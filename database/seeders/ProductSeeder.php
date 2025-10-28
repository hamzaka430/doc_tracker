<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            [
                'name' => 'iPhone 15 Pro',
                'batch_no' => 'IP15-001',
                'stage' => 'Production',
                'status' => 'pending',
                'pre_line_clearance' => true,
                'in_process' => false,
                'post_line_clearance' => false,
                'remarks' => 'Initial batch for testing',
            ],
            [
                'name' => 'Samsung Galaxy S24',
                'batch_no' => 'SG24-002',
                'stage' => 'QA Sign',
                'status' => 'pending',
                'pre_line_clearance' => true,
                'in_process' => true,
                'post_line_clearance' => false,
                'remarks' => 'Quality assurance in progress',
            ],
            [
                'name' => 'MacBook Pro M3',
                'batch_no' => 'MBP-003',
                'stage' => 'Transfer Note',
                'status' => 'submitted',
                'pre_line_clearance' => true,
                'in_process' => true,
                'post_line_clearance' => true,
                'remarks' => 'Completed and ready for shipment',
                'submission_date' => now()->subDays(2)->toDateString(),
                'submission_time' => '14:30:00',
            ],
            [
                'name' => 'iPad Air 5th Gen',
                'batch_no' => 'IPA-004',
                'stage' => 'On Process',
                'status' => 'pending',
                'pre_line_clearance' => false,
                'in_process' => false,
                'post_line_clearance' => false,
                'remarks' => 'Just started production',
            ],
            [
                'name' => 'Dell XPS 13',
                'batch_no' => 'DXS-005',
                'stage' => 'Hold',
                'status' => 'pending',
                'pre_line_clearance' => true,
                'in_process' => true,
                'post_line_clearance' => false,
                'remarks' => 'Waiting for component availability',
            ],
            [
                'name' => 'Surface Laptop 5',
                'batch_no' => 'SL5-006',
                'stage' => 'Production',
                'status' => 'submitted',
                'pre_line_clearance' => true,
                'in_process' => true,
                'post_line_clearance' => true,
                'remarks' => 'Final batch completed successfully',
                'submission_date' => now()->subDays(1)->toDateString(),
                'submission_time' => '09:15:00',
            ],
            [
                'name' => 'Google Pixel 8',
                'batch_no' => 'GP8-007',
                'stage' => 'Return',
                'status' => 'pending',
                'pre_line_clearance' => false,
                'in_process' => false,
                'post_line_clearance' => false,
                'remarks' => 'Quality issues detected, needs rework',
            ],
            [
                'name' => 'HP Pavilion 15',
                'batch_no' => 'HPP-008',
                'stage' => 'Specific Person',
                'status' => 'pending',
                'pre_line_clearance' => true,
                'in_process' => false,
                'post_line_clearance' => false,
                'remarks' => 'Assigned to senior technician John Doe',
            ],
        ];

        foreach ($products as $product) {
            \App\Models\Product::create($product);
        }
    }
}
