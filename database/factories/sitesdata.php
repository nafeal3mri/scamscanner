<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        // $links = [
        //     'http://www.spa.gov.sa/',
        //     'http://www.okaz.com.sa/',
        //     'https://sabq.org/',
        //     'http://www.alriyadh.com/',
        //     'http://www.alweeam.com.sa/',
        //     'http://www.alsharq.net.sa/',
        //     'https://www.almowaten.net/',
        //     'http://www.alwatan.com.sa',
        //     'http://www.ajel.sa/',
        //     'http://www.alyaum.com/',
        //     'https://al-marsd.com/',
        //     'http://www.al-jazirah.com/',
        //     'http://akhbaar24.argaam.com/',
        //     'https://aawsat.com/',
        //     'http://www.aleqt.com/',
        //     'http://www.mapnews.com/',
        //     'http://www.alhayat.com/',
        //     'http://www.an7a.com/',
        //     'http://www.al-madina.com/',
        //     'http://www.fajr.sa/',
        //     'http://www.alarabiya.net/',
        //     'https://www.skynewsarabia.com/',
        //     'http://www.bbc.com/arabic',
        //     'https://arabic.cnn.com/',
        //     'http://www.maaal.com/',
        //     'http://www.rasdnews.net/'
        // ];
        // foreach ($links as $value) {

        //    return [
        //         'domain_url' => $value,
        //         'page_title' => '',
        //         'page_icon' => '',
        //         'description' => '', 
        //         'created_at' =>  now(),
        //     ];
        // }
        // return [
        //     'name' => $this->faker->name(),
        //     'email' => $this->faker->unique()->safeEmail(),
        //     'email_verified_at' => now(),
        //     'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
        //     'remember_token' => Str::random(10),
        // ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function unverified()
    {
    }
}
