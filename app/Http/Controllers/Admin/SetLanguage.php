<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App;
use Auth;
use App\Models\User;

class SetLanguage extends Controller
{
    function setUserLanguage(Request $data) {
        // dd(backpack_user());

        // $this->validate($data,[
        //     'lang'  => ['required'],
        // ],[
        //     'lang.required' => __("base.Error while changing language"),
        // ]);
        $user = User::find(backpack_user()->id);
        $user->language = $data['lang'];
        $user->save();
        // Set the language
        App::setLocale($user->language);
        return redirect()->back();
    }
}
