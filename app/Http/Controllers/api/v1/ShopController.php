<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShopController extends Controller
{
    
    const NAME = 'name';
    const ADDRESS = 'address';
    const EMPLOYEES = 'employees';
    const PHONE_NO_ONE = 'phone_no_one';
    const PHONE_NO_TWO = 'phone_no_two';
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       $user = Auth::user();
       $shops = $user->shops;
       return $shops;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $name = $request->get(self::NAME);
        $address = $request->get(self::ADDRESS);
        $employees = $request->get(self::EMPLOYEES);
        $phone_no_one = $request->get(self::PHONE_NO_ONE);
        $phone_no_two = $request->get(self::PHONE_NO_TWO);

        $shop = new Shop();
        $shop->name = $name;
        $shop->address = $address;
        $shop->employees = $employees;
        $shop->phone_no_one = $phone_no_one;
       
        if($request->has(self::PHONE_NO_TWO)){
            $shop->phone_no_two = $phone_no_two;
        }
        $shop->save();

        $user = Auth::user();
        $user->shops()->attach($shop->id);

        return $shop;

    }

    /**
     * Display the specified resource. 
     *
     * @param  \App\Models\Shop  $shop
     * @return \Illuminate\Http\Response
     */
    public function show(Shop $shop)
    {
        $user = Auth::user();
        $shops = $user->shops;
        foreach($shops as $single_shop){
            if($single_shop->id == $shop->id){
                return $single_shop;
            }
        }
        return "Unauthorized";
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Shop  $shop
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Shop $shop)
    {
        $user = Auth::user();
        $shops = $user->shops;
        foreach($shops as $single_shop){

            if($single_shop->id == $shop->id){
                $name = $request->get(self::NAME);
                $address = $request->get(self::ADDRESS);
                $employees = $request->get(self::EMPLOYEES);
                $phone_no_one = $request->get(self::PHONE_NO_ONE);
                $phone_no_two = $request->get(self::PHONE_NO_TWO);
                
                $shop->name = $name;
                $shop->address = $address;
                $shop->employees = $employees;
                $shop->phone_no_one = $phone_no_one;
               
                if($request->has(self::PHONE_NO_TWO)){
                    $shop->phone_no_two = $phone_no_two;
                }
                
                $shop->save();
                return $shop;
            }
        }
        return "unauthorized";
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Shop  $shop
     * @return \Illuminate\Http\Response
     */
    public function destroy(Shop $shop)
    {
        $user = Auth::user();
        $shops = $user->shops;
        foreach($shops as $single_shop){
            if($single_shop->id == $shop->id){
                $shop->delete();
                return $shop;
            }
        }
        return "authorized";
    }
}
