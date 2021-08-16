@extends('layouts.app')

@section('title', 'Ваши фиды')

@section('content')
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="user-text">
                @if(session('status'))
                <div class="alert alert-success">
                    {{ session('status') }}
                </div>
                @endif
            </div>
            <a href="{{route('accounts.create')}}" class="btn btn-primary">
                Добавить фид
            </a>
            <div class="table-responsive">
                <table class="table bg-light table-bordered">
                    <thead>
                        <tr>
                            <th scope="col" style="width: 7%">Название Фида</th>
                            <th scope="col" style="width: 40%">XML фид ЦИАН</th>
                            <th scope="col" style="width: 40%">API ключ ЦИАН</th>
                            <th scope="col" style="width: 5%">Баланс по API</th>
                            <th scope="col" style="width: 5%">Баллы аукциона </th>
                            <th scope="col" style="width: 3%"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($accounts as $account)
                            <tr>
                                <td>{{$account->name}}</td>
                                <td><div class="table-container">{{$account->xml_feed}}</div></td>
                                <td><div class="table-container">{{$account->cyan_key}}</div></td>
                                <td>{{$account->balance}}</td>
                                <td>{{$account->auction_points}}</td>
                                <td class="text-right">
                                    <form onsubmit="if(confirm('Удалить?')){return true}else{return false}" action="{{ route('accounts.destroy', $account) }}"
                                    method="post" class="d-flex">
                                        @csrf
                                        <input type="hidden" name="_method" value="DELETE">  
                                        <a href="{{ route('accounts.edit', $account) }}" class="btn btn-default">
                                            <i class="fa fa-edit"></i>
                                        </a>                                        
                                        <button type="submit" class="btn">
                                            <i class="far fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </td>
                                
                            <tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center mt-2"><h3>Данные отсутствуют</h3></td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>  
        </div>
    </div>
@endsection
  