<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Product;
use App\Models\Category;
use Livewire\WithPagination;

class ProductController extends Component
{

    use WithPagination;

    public $name, $description, $price, $stock, $alerts, $category_id, $search, $selected_id, $pagetitle, $componentName;
    private $pagination=5;

    public function mount(){
        $this->pagetitle = "Listado";
        $this->componentName = "Producto";
    }
    public function paginationView(){ //Paginacion personalizada
        return 'vendor.livewire.bootstrap';
    }

    public function render()
    {
        if(strlen($this->search) > 0){
            $products = Product::join('categories as c','c.id','products.category_id') 
                        ->select('products.*','c.name as category')
                        ->where('products.name','like','%'. $this->search .'%')
                        ->orwhere('c.name','like','%'. $this->search .'%')
                        ->orderby('products.name','asc')
                        ->paginate($this->pagination);

        }else{
            $products = Product::join('categories as c','c.id','products.category_id') 
            ->select('products.*','c.name as category')
            ->orderby('products.name','asc')
            ->paginate($this->pagination);
        }

        return view('livewire.product.product', ['data'=>$products, 'categories' => Category::orderBy('name', 'asc')->get()
                                                ])->extends('layouts.theme.app')->section('content');
    }

    protected $listeners = [
        'deleteRow' => 'Destroy'
    ];

    public function Destroy(Product $product){
        $product->delete();

        $this->resetUI();
        $this->emit('product-deleted','Producto Eliminado');//Cerramos el modal  
    }

    public function Store(){
        $rules= [
            'name' => 'required|unique:products|min:3',
            'description' => 'required',
            'price' => 'required',
            'stock' => 'required',
            'alerts' => 'required',
            'category_id' => 'required|not_in:Elegir'

        ];
        $this->validate($rules);

        $product = Product::create([
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'stock' => $this->stock,
            'alerts' => $this->alerts,
            'category_id' => $this->category_id
        ]);
        $product->save();
        $this-> resetUI(); 
        $this-> emit('product-added', 'Producto Reguistrado');
    }

    public function Edit(Product $product){
        $this->selected_id = $product->id;
        $this->name = $product->name;
        $this->description = $product->description;
        $this->price = $product->price;
        $this->stock = $product->stock;
        $this->alerts = $product->alerts;
        $this->category_id = $product->category_id;
        //dd($product);
        $this->emit('modal-show' , 'Show modal');
    }

    public function Update(){

        $rules= [
            'name' => "required|min:3",
            'description' => 'required',
            'price' => 'required',
            'stock' => 'required',
            'alerts' => 'required',
            'category_id' => 'required|not_in:Elegir'

        ];
        $this->validate($rules);

        $product = Product::find($this->selected_id);
        $product -> update([
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'stock' => $this->stock,
            'alerts' => $this->alerts,
            'category_id' => $this->category_id
        ]);

        $product->save();
        $this-> resetUI(); 
        $this-> emit('product-updated', 'Producto Actualizado');
    }

    public function resetUI(){
        $this->name = '';
        $this->description = '';
        $this->price = '';
        $this->stock = '';
        $this->alerts = '';
        $this->category_id = 'Elegir';
        $this->search = '';
        $this->selected_id = 0;
    }

}
