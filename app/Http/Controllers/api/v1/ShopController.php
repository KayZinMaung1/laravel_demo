<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Http\Resources\ShopResource;
use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;
use App\Utils\ErrorType;
use Exception;


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
       return response()->json(["status" => "success", "data" => ShopResource::collection($shops), "total" => count($shops)]);
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try{
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

            return jsend_success(new ShopResource($shop), JsonResponse::HTTP_CREATED);
        }
        catch(Exception $ex){
             return jsend_error(ErrorType::SAVE_ERROR);    
        }
            
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
                return jsend_success(new ShopResource($shop));
            }
        }
        return jsend_fail(['error'=>"Unauthorized."],JsonResponse::HTTP_UNAUTHORIZED);
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
       try{
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
                    return jsend_success(new ShopResource($shop),JsonResponse::HTTP_CREATED);
                }
                
            }
            return jsend_fail(['error'=> 'Unauthorized.'],JsonResponse::HTTP_UNAUTHORIZED);
          
        }catch(Exception $ex){
            return jsend_error(ErrorType::UPDATE_ERROR);
            }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Shop  $shop
     * @return \Illuminate\Http\Response
     */
    public function destroy(Shop $shop)
    {
       try{
        $user = Auth::user();
        $shops = $user->shops;
        foreach($shops as $single_shop){
            if($single_shop->id == $shop->id){
                $shop->delete();
                return jsend_success(null,JsonResponse::HTTP_NO_CONTENT);
            }
        }
        return jsend_fail(['error'=>'Unauthorized.'],JsonResponse::HTTP_UNAUTHORIZED);
       }
       catch(Exception $ex){
            return jsend_error(ErrorType::DELETE_ERROR);
       }
    }
}
