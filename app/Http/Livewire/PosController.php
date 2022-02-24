<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Denomination;
use App\Models\Product;
use Livewire\WithPagination;
use Darryldecode\Cart\Facades\CartFacade as Cart;

class PosController extends Component
{
    use WithPagination;

    public $total, $items, $cash, $change, $efectivo, $status, $user_id, 
            $search, $selected_id, $itemsQuantity;
    private $pagination = 10;

    public function paginationView(){ //Paginacion personalizada
        return 'vendor.livewire.bootstrap';
    }

    public function mount(){
        $this->efectivo = 0;
        $this->change = 0;
        $this->total = Cart::getTotal();
        $this->itemsQuantity = Cart::getTotalQuantity();
    }

    public function render()
    {
        return view('livewire.pos.component', ['denominations'=>Denomination::orderBy('value','desc')->get(),
                                                'cart'=>Cart::getContent()->sortBy('name')
                                                ])
                                                ->extends('layouts.theme.app')
                                                ->section('content');

    }

    public function ACash($value){
        $this->efectivo += ($value == 0 ? $this->total : $value);
        $this->change = ($this->efectivo - $this->total);
    }

    protected $listener = [
        'scan-code' => 'ScanCode',
        'removeItem' => 'removeItem',
        'clearCart' => 'clearCart',
        'saveSale' => 'saveSale'
    ];

    public function ScanCode($barcode, $cant =1){

        $product = Product::where('barcode',$barcode)->first();

        if($product == null || empty($empty)){
            $this->emit('scan-notFound', 'El producto no esta registrado');
        }else{
            if($this->inCart($product->id)){
                $this->increaseQty($product->id);
                return;
            }

            if($product->stock < 1){
                $this-> emit('no-stock', 'Stock insuficiente');
                return;
            }

            Cart::add($product->id, $product->mame, $product->price, $cant);
            $this->total = Cart::getTotal();

            $this->emit('scan-ok', 'Producto agregado');

        }

    }

    public function InCart($productId){

        $exist = Cart::get($productId);

        if($exist)
            return true;
        else
            return false;

    }

    public function increaseQty($productId, $cant = 1){

        $title = '';
        $product = Product::find($productId);
        $exist = Cart::get($productId);

        if($exist)
            $title = 'Cantidad ACtualizada';
        else
            $title = 'Producto agregado';

        if($exist){
            if($product->stock < ($cant + $exist->quantity)){
                $this-> emit('no-stock', 'Stock insuficiente');
                return;
            }
        }

        Cart::add($product->id, $product->mame, $product->price, $cant);

        $this->total = Cart::getTotal();
        $this->itemsQuantity = Cart::getTotalQuantity();

        $this->emit('scan-ok', $title);

    }

    public function updateQty($productId, $cant = 1){
        $title = '';
        $product = Product::find($productId);
        $exist = Cart::get($productId);

        if($exist)
            $title = 'Cantidad ACtualizada';
        else
            $title = 'Producto agregado';

        if($exist){
            if($product->stock < $cant){
                $this-> emit('no-stock', 'Stock insuficiente');
                return;
            }
        }

        $this->removeItem($productId);

        if($cant > 0){
            Cart::add($product->id, $product->mame, $product->price, $cant);
            $this->total = Cart::getTotal();
            $this->itemsQuantity = Cart::getTotalQuantity();

            $this->emit('scan-ok', $title);
        }

    }

    public function removeItem($productId){

        Cart::remove($productId);

        $this->total = Cart::getTotal();
        $this->itemsQuantity = Cart::getTotalQuantity();

        $this->emit('scan-ok', 'Producto eliminado');

    }

    public function decreseQty($productId){

        $item = Cart::get($productId);
        Cart::remove($productId);

        $newQty = ($item->quantity) - 1;

        if($newQty > 0){
            Cart::add($item->id, $item->mame, $item->price, $newQty);
        }

        $this->total = Cart::getTotal();
        $this->itemsQuantity = Cart::getTotalQuantity();

        $this->emit('scan-ok', 'Cantidad actualizada');
    }

}
