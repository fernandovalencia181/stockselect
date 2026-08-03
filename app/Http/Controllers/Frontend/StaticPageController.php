<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class StaticPageController extends Controller
{
    public function privacy()
    {
        return view('frontend.pages.privacy');
    }

    public function terms()
    {
        return view('frontend.pages.terms');
    }

    public function cookies()
    {
        return view('frontend.pages.cookies');
    }

    public function legal()
    {
        return view('frontend.pages.legal');
    }

    public function faq()
    {
        return view('frontend.pages.faq');
    }

    public function sizeGuide()
    {
        return view('frontend.pages.size-guide');
    }

    public function tracking(Request $request)
    {
        $order = null;
        $searched = false;

        if ($request->isMethod('post')) {
            $request->validate([
                'order_id' => 'required',
                'email' => 'required|email',
            ]);

            $order = Order::with('items.product')
                ->where('id', $request->order_id)
                ->where('customer_email', $request->email)
                ->first();

            $searched = true;
            
            if (!$order) {
                return back()->withErrors(['tracking' => 'No hemos encontrado ningún pedido con esos datos.'])->withInput();
            }
        }

        return view('frontend.pages.tracking', compact('order', 'searched'));
    }
}
