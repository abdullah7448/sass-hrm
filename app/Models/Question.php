<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    // ডাটাবেসে সেভ করার জন্য কলামগুলোর পারমিশন
    protected $fillable = [
        'company_id',
        'category',
        'department',
        'type',
        'question_text',
        'options',
    ];

    // options কলামটি যেহেতু JSON, তাই এটি যেন অটোমেটিক Array তে কনভার্ট হয়ে যায় তার জন্য casts ব্যবহার করা হলো
    protected $casts = [
        'options' => 'array',
    ];

    // রিলেশন: একটি প্রশ্ন নির্দিষ্ট একটি কোম্পানির অধীনে থাকে
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    // ==========================================
    // Global IQ Question Template
    // ==========================================
    public static function getGlobalIQQuestions()
    {
        return [
            [
                'id' => 'global_iq_1', // ডাটাবেস আইডির সাথে যেন না মিলে তাই স্ট্রিং আইডি
                'company_id' => null,
                'category' => 'iq',
                'department' => 'All',
                'type' => 'mcq',
                'question_text' => 'If a bat and a ball cost $1.10 in total, and the bat costs $1.00 more than the ball, how much does the ball cost?',
                'options' => json_encode(['$0.05', '$0.10', '$0.15', '$1.00']),
                'is_global' => true // এটি দিয়ে আমরা অ্যাডমিন প্যানেলে Delete বাটন হাইড করবো
            ],
            [
                'id' => 'global_iq_2',
                'company_id' => null,
                'category' => 'iq',
                'department' => 'All',
                'type' => 'mcq',
                'question_text' => 'What comes next in the sequence? 2, 6, 14, 30, ...',
                'options' => json_encode(['46', '54', '62', '64']),
                'is_global' => true
            ],
            [
                'id' => 'global_iq_3',
                'company_id' => null,
                'category' => 'iq',
                'department' => 'All',
                'type' => 'yes_no',
                'question_text' => 'Can you work under extreme pressure and meet tight deadlines?',
                'options' => json_encode(['Yes', 'No']),
                'is_global' => true
            ]
        ];
    }
}