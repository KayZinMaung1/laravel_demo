<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Http\Resources\CompanyResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Company;
use App\Models\Shop;
use App\Utils\ErrorType;
use Exception;
use Illuminate\Http\JsonResponse;

class CompanyController extends Controller
{

    const NAME = 'name';
    const PHONE_NO = 'phone_no';
    const ADDRESS = 'address';
    const SHOP_ID = 'shop_id';
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Shop $shop)
    {
        $companies = [];
        $user = Auth::user();
        $shops = $user->shops;

        //if shop_id is not passsing in request, return user's all companies 
        if($shop->id == null && $shop->companies->isEmpty()){
            foreach($shops as $shop){
                foreach($shop->companies as $company){
                    array_push($companies,$company);
                }
            }
        }
    
        //if shop_id is passing , return that shop's companies
        else{
            foreach($shops as $single_shop){
                if($single_shop->id == $shop->id){
                    foreach($shop->companies as $company){
                        array_push($companies,$company);
                    }
                    break;
                }
            }
        }
        return response()->json(['status'=>'Success','data'=>CompanyResource::collection($companies),'total'=> Count($companies)]);
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
            foreach($shops as $shop){
                if($shop->id == $shop_id){
                    $name = $request->get(self::NAME);
                    $address = $request->get(self::ADDRESS);
                    $phone_no = $request->get(self::PHONE_NO);
                    
                    $company = new Company();
                    $company->name = $name;
                    $company->address = $address;
                    $company->phone_no = $phone_no;
                    $company->shop_id = $shop_id;

                    $company->save();
                    return jsend_success(new CompanyResource($company),JsonResponse::HTTP_CREATED);
                }
            }
            return jsend_fail(['error'=>'Unauthorized.'],JsonResponse::HTTP_UNAUTHORIZED);   
       }
       catch(Exception $ex){
            return jsend_error(ErrorType::SAVE_ERROR);
       }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Company  $company
     * @return \Illuminate\Http\Response
     */
    public function show(Company $company)
    {
        $user = Auth::user();
        $shops = $user->shops;
        foreach($shops as $shop){
            if($shop->id == $company->shop_id){
                return jsend_success(new CompanyResource($company));
            }
        }
        return jsend_fail(['error'=>'Unauthorized.'],JsonResponse::HTTP_UNAUTHORIZED);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Company  $company
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Company $company)
    {
        try{
            $shop_id = $request->get(self::SHOP_ID);
            $user = Auth::user();
            $shops = $user->shops;
        
            foreach($shops as $shop){
                
                if($shop->id == $shop_id){
                    $name = $request->get(self::NAME);
                    $address = $request->get(self::ADDRESS);
                    $phone_no = $request->get(self::PHONE_NO);
                    
            
                    $company->name = $name;
                    $company->address = $address;
                    $company->phone_no = $phone_no;
                    $company->shop_id = $shop_id;
            
                    $company->save();
                    return jsend_success(new CompanyResource($company),JsonResponse::HTTP_CREATED);
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
     * @param  \App\Models\Company  $company
     * @return \Illuminate\Http\Response
     */
    public function destroy(Company $company)
    {
        $user = Auth::user();
        $shops = $user->shops;
        foreach($shops as $shop){
            if($shop->id == $company->shop_id){
                $company->delete();
                return jsend_success(null,JsonResponse::HTTP_NO_CONTENT);
            }
        }
        return jsend_fail(['error'=>'Unauthorized.'],JsonResponse::HTTP_UNAUTHORIZED);
    }
}
