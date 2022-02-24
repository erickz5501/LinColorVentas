<div class="connect-sorting">
    <div class="connect-sorting-content">
        <div class="card simple-title-task ui-sortable-handle">
            <div class="card-body">

           
                @if ($total > 0)
                    <div class="table responsive tblscroll" style=" max-height: 650px; overflow: hidden">
                        <table class="table table bordered table striped mt-1">
                            <thead class="text-white" style=" background: #3B3F5C " >
                                <tr>
                                <th width="10%"></th>
                                <th class="table-th text-left text-white">Nombre</th>
                                <th class="table-th text-left text-white">PRECIO</th>
                                <th width="13%" class="table-th text-left text-white">CANT</th>
                                <th class="table-th text-left text-white">IMPORTE</th>
                                <th class="table-th text-left text-white">ACCIONES</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($cart as $item)
                                <tr>
                                    <td class="text-center table-th"></td>
                                    <td> {{$item->name}} </td>
                                    <td> S/. {{ number_format($item->price,2) }} </td>

                                    <td>
                                        <input type="number" id="r{{$item->id}}" 
                                        wire:change="updateQty({{$item->id}}, $('#r' + {{$item->id}}).val() )" 
                                        style="font-size: 1rem!important" class="form-control text-center" 
                                        value="{{$item->quantity}}">
                                    </td>

                                    <td class="text-center">
                                        <h6>
                                            S/. {{ number_format($item->price * $item->quantity,2)}}
                                        </h6>
                                    </td>

                                    <td class="text-center">

                                        <button class="btn btn-dark mbmobile" onclick="Confirm('{{ $item->id}}', 'removeItem', '¿CONFIRMAS ELIMINAR EL REGISTRO?')">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                        <button class="btn btn-dark mbmobile" wire:click.prevent="decreseQty({{$item->id}})">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                        <button class="btn btn-dark mbmobile" wire:click.prevent="increaseQty({{$item->id}})">
                                            <i class="fas fa-plus"></i>
                                        </button>

                                    </td>
                                    
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <h5 class="text-center-text-muted">
                        Agregar Productos a la venta
                    </h5>
                @endif

            <div wire:loading.inline wire:target="saveSale" >
                <h4 class="text-danger text-center">
                Guardando Venta...
                </h4>
            </div>
            </div>
        </div>
    </div>
</div>