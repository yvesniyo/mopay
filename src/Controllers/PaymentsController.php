<?php
namespace Yves\Mopay\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\Request;
use PhpParser\Node\Stmt\TryCatch;
use Yves\Mopay\Models\Payment;
use Yves\Mopay\Utils\PaymentCart;
use Yves\Mopay\Utils\PaymentForm;
use Yves\Mopay\Utils\PaymentFormItem;
use Yves\Mopay\Utils\PaymentProductCart;

class PaymentsController extends Controller{


    public function initByForm(Request $request){
        $request = $request->all();
        $validator = validator($request, [
            "extra"=> "required",
        ]);
        if($validator->fails()){
            // return redirect()->back()->withErrors($validator->errors())->withInput($request);
            return response()->json([
                "status"=> 422,
                "message"=> "User inputs can not be processed",
                "errors"=> $validator->errors(),
                "error"=> $validator->errors()->toArray(),
            ]);
        }
        try {
            $paymentForm = decrypt($request["extra"]);
        } catch (DecryptException $th) {
            return response()->json([
                "status"=> 422,
                "message"=> "User inputs can not be processed",
                "errors"=> [],
                "error"=> $th->getMessage(),
            ]);
        }
        
        $editablesInForm = $paymentForm->editableItems();
        $itemsInForm = $paymentForm->items();
        $vars = [];
        foreach ($itemsInForm as $item) {
            if(!$item->editable){
                ${$item->name} = $item->value;
            }else{
                ${$item->name} = $request[$item->name];
            }
            $vars[$item->name] = ${$item->name};
        }


        $payment = Payment::request($amount,$msisdn,$client_name,null,null,$email);
        if($payment){
            return view("mopay::payment_result", compact("payment"));
        }
        $failed = true;
        return view("mopay::payment_result", compact("payment","failed"));      
        
    }

    public function form(Request $request){

        $paymentForm = new PaymentForm();

        // adding items
        $paymentForm->addItem(new PaymentFormItem(PaymentFormItem::AMOUNT,"1000"));
        $paymentForm->addItem(new PaymentFormItem(PaymentFormItem::CURRENCY,"Rwf"));
        $paymentForm->addItem(new PaymentFormItem(PaymentFormItem::MSISDN,"250783588642",false));
        $paymentForm->addItem(new PaymentFormItem(PaymentFormItem::CLIENT_NAME,"Mukunzi Joshua"));

        $paymentCart = new PaymentCart();
        $paymentCart->addProduct(new PaymentProductCart("Ticket",1000));
        $paymentForm->setCart($paymentCart);

        return view("mopay::form",[
            "form"=> $paymentForm,
            "extra"=> encrypt($paymentForm),
        ]);
    }

    public function webhook(Request $request){
        $datas = $request->all();
        $validator = validator($datas,[
            "status"=>'required|integer',
            "external_id"=> "required|exists:mopay_payments,external_id",
            "reference_id"=> "required",
            "amount"=> "required",
            "msisdn"=> "required|exists:mopay_payments,msisdn",
        ]);
        if($validator->fails()){
            return response()->json([
                "status"=> 422,
                "message"=>" User input can not be processed",
                "errors"=> $validator->errors()
            ], 422);
        }
        $datas = $request->only([
            "status",
            "external_id",
            "reference_id",
            "amount",
            "msisdn"
        ]);
        $datas["message"] = $datas["message"] ?? "Completed";
        $payment = Payment::find($datas["external_id"]);
        $payment->update($datas);
        $payment->save();
        return response()->json([
            "status"=> 200,
            "message"=> "success",
        ]);
    }

}