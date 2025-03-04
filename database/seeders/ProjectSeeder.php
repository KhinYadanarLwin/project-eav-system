<?php

namespace Database\Seeders;

use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\Project;
use App\Models\Timesheet;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $user = User::first(); // Or use a default user if none exists

        if (!$user) {
            $user = User::create([
                'first_name' => 'Super',
                'last_name' => 'Admin',
                'email' => 'super_admin@eav.com',
                'password' => bcrypt('12345678'),
            ]);
        }
        $project = Project::create([
            'name' => 'ProjectA',
            'status' => '1', //Created
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);
        $attributes = [
            ['name' => 'department', 'type' => 'text', 'value' => 'IT'],
            ['name' => 'start_date', 'type' => 'date', 'value' => '2026-01-01'],
            ['name' => 'end_date', 'type' => 'date', 'value' => '2026-03-31'],
        ];

        foreach ($attributes as $attr) {
            $attribute = Attribute::updateOrCreate(['name' => $attr['name']], ['type' => $attr['type'], 'created_by' => $user->id,'updated_by' => $user->id]);
            AttributeValue::updateOrCreate(
                [
                    'entity_id' => $project->id,
                    'attribute_id' => $attribute->id
                ],
                [
                    'value' => $attr['value'],
                    'created_by' => $user->id,
                    'updated_by' => $user->id,
                ]
            );
        }

        $timesheets = [
            [
                'task_name' => 'Develop Authentication Feature',
                'date' => '2026-01-01',
                'hours' => 15,
                'user_id' => $user->id,
                'project_id' => $project->id,
            ],
            [
                'task_name' => 'Design Database Schema',
                'date' => '2026-01-03',
                'hours' => 10,
                'user_id' => $user->id,
                'project_id' => $project->id,
            ],
        ];
        Timesheet::upsert($timesheets, ['task_name', 'user_id', 'project_id'], ['date', 'hours']);
    }
}
