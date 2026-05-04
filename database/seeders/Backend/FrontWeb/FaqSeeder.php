<?php

namespace Database\Seeders\Backend\FrontWeb;

use App\Models\Backend\FrontWeb\Faq;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory   as Faker;
class FaqSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();
        $questions = [
                'What is We Delivery?',
                'How do I contact you?',
                'How can a sender track their parcel delivery?',
                'How do I send a product/ courier via we Delivery?',
                'I want to hold a parcel for more than 3 days before home delivery. Is it possible?',
                'Can you do product exchange from customers?',
                'Can you deliver to addresses inside Cantonment or other restricted areas?',
                'I do not have a Facebook page, can I register as a sender?',
                'What kind of products does WeDelivery deliver?',
            ];
            foreach ($questions as $key => $question) {
                $faq                = new Faq();
                $faq->question      = $question;
                $faq->answer   = $faker->sentence(30);
                $faq->position      = ($key+1);
                $faq->save();
            }

    }
}
