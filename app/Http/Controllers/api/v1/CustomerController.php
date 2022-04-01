<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\CustomerResource;
use App\Utils\ErrorType;
use Illuminate\Support\Facades\Auth;
use App\Models\Shop;
use Exception;

class CustomerController extends Controller
{
    const NAME = 'name';
    const ADDRESS = 'address';
    const PHONE_NO = 'phone_no';
    const SHOP_ID = 'shop_id';
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Shop $shop)
    {
        $customers = [];
        $user = Auth::user();
        $shops = $user->shops;

            //if shop_id is not passsing in request, return user's all customers 
            if($shop->id == null && $shop->customers->isEmpty()){
                foreach($shops as $single_shop){
                    foreach( $single_shop->customers as $customer ){
                        array_push($customers,$customer);
                    }
                }
            }
            //if shop_id is passing , return that shop's customers
            else{
                foreach($shops as $single_shop){
                    if($single_shop->id == $shop->id){
                        foreach( $shop->customers as $customer){
                            array_push($customers,$customer);
                        }
                        break;                   
                    }
                }
            }
        return response()->json(['status'=>'success','data'=> CustomerResource::collection($customers),'total'=>count($customers)]);

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
            $shop_id = $request->get(self::SHOP_ID);
            
            $user = Auth::user();
            $shops = $user->shops;
            foreach($shops as $single_shop){
                if($single_shop->id == $shop_id){
                    $name = $request->get(self::NAME);
                    $address = $request->get(self::ADDRESS);
                    $phone_no = $request->get(self::PHONE_NO);
                    
                    $customer = new Customer();
                    $customer->name = $name;
                    $customer->address = $address;
                    $customer->phone_no = $phone_no;
                    $customer->shop_id = $shop_id;
    
                    $customer->save();
                    return jsend_success(new CustomerResource($customer),JsonResponse::HTTP_CREATED);
                }
            }
            return jsend_fail(['error'=> 'Unauthorized.'], jsonResponse::HTTP_UNAUTHORIZED);
        }
        catch(Exception $ex){
            return jsend_error(ErrorType::SAVE_ERROR);
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function show(Customer $customer)
    {
        $user = Auth::user();
        $shops = $user->shops;
        foreach($shops as $shop){
            if($shop->id == $customer->shop_id){
                return jsend_success(new CustomerResource($customer));
            }
        }
        return jsend_fail(['error'=>'Unauthorized.'],JsonResponse::HTTP_UNAUTHORIZED);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Customer $customer)
    {
       try{   
        $shop_id = $request->get(self::SHOP_ID);
        $user = Auth::user();
        $shops = $user->shops;
        foreach($shops as $shop){
            if($shop->id == $customer->shop_id && $shop->id == $shop_id){
                $name = $request->get(self::NAME);
                $address = $request->get(self::ADDRESS);
                $phone_no = $request->get(self::PHONE_NO);
                
                $customer->name = $name;
                $customer->address = $address;
                $customer->phone_no = $phone_no;
                $customer->shop_id = $shop_id;

                $customer->save();
                return jsend_success(new CustomerResource($customer),JsonResponse::HTTP_CREATED);
            }
        }
        return jsend_fail(['error'=>'Unauthorized.'],JsonResponse::HTTP_UNAUTHORIZED);
       }
       catch(Exception $ex){
          return jsend_error(ErrorType::UPDATE_ERROR);
       }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function destroy(Customer $customer)
    {
        $user = Auth::user();
        $shops = $user->shops;
        foreach($shops as $shop){
            if($shop->id == $customer->shop_id){
                $customer->delete();
                return jsend_success(null,JsonResponse::HTTP_NO_CONTENT);
            }
        }
        return jsend_fail(['error'=>'Unauthorized.'],JsonResponse::HTTP_UNAUTHORIZED);
    }
}
