<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Category;
use Illuminate\Support\Facades\Storage;
use Livewire\WithFileUploads; //Se usa para subir imagenes - Trait
use Livewire\WithPagination;

class CategoriesController extends Component
{
    use WithFileUploads; //Se usa para subir imagenes
    use WithPagination;

    public $name, $search, $selected_id, $pagetitle, $componentName;
    private $pagination = 5; //La cantidad de registros por paginación

    public function mount(){ //Este metodo se usa para iniciar propiedades
        $this->pagetitle='Listado';
        $this->componentName='Categoria';
        $this->selected_id= 0;
    }

    public function paginationView(){ //Paginacion personalizada
        return 'vendor.livewire.bootstrap';
    }

    public function render()
    {
        if (strlen($this->search) > 0) {
            $data = Category::where('name', 'like', '%'.$this->search.'%' )->paginate($this->pagination);
        }else{
            $data = Category::orderBy('id', 'asc')->paginate($this->pagination);
        }
        // $data = Category::all();
        return view('livewire.category.categories', ['categories'=>$data])
        ->extends('layouts.theme.app')
        ->section('content');   
    }

    public function resetUI(){
        $this->name = '';
        $this->search = '';
        $this->selected_id = 0;
    }    

    public function Store(){

        $rules = [
            "name" => 'required|unique:categories|min:3'
        ];

        $this->validate($rules);
        
        $category = Category::create([
            'name' => $this->name
        ]);

        $category->save();
        $this->resetUI();
        $this-> emit('category-added', 'Se añadio la categoria'); //Cerramos el modal


    }

    public function Update(){
        $rules = [
            "name" => "required|unique:categories|min:3:categories,name{$this->selected_id}"
        ];

        $this->validate($rules);
        $category = Category::find($this->selected_id);

        $category->update([
            'name' => $this->name
        ]);
        $this->resetUI();
        $this-> emit('category-updated', 'Se actualizo la categoria'); //Cerramos el moda
    }

    public function Edit($id){

        $record = Category::find($id, ['id', 'name']);
        $this->name = $record->name;
        $this->selected_id = $record->id;

        $this-> emit('show-modal', 'Show modal!');

    }

    protected $listeners = [
        'deleteRow' => 'Destroy'
    ];

    public function Destroy(Category $category){
        $category->delete();

        $this->resetUI();
        $this->emit('category-deleted','Categoría Eliminada');//Cerramos el modal  
    }

}
