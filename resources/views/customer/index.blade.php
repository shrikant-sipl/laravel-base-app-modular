@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <a class="btn btn-primary" href="{{ route('customer.create') }}">Add customer</a>
                        <table border="0" width="100%">
                            <tr>
                                <th>Name</th>
                                <th>Gender</th>
                                <th>Email</th>
                                <th>Mobile</th>
                                <th>Created Date</th>
                                <th>Action</th>
                            </tr>
                            @if(count($customers) > 0)
                                @foreach($customers as $customer)
                                    <tr>
                                        <td>{{ $customer->full_name }}</td>
                                        <td>{{ $customer->gender ? $customer->gender : 'N/A' }}</td>
                                        <td>{{ $customer->email ? $customer->email : 'N/A' }}</td>
                                        <td>{{ $customer->mobile ? $customer->mobile : 'N/A' }}</td>
                                        <td>{{ $customer->created_at ? Helpers::convertDate($customer->created_at) : 'N/A' }}</td>
                                        <td>
                                            <a class="btn btn-primary" href="{{ route('customer.show', $customer->id) }}">view</a>
                                            <a class="btn btn-primary" href="{{ route('customer.edit', $customer->id) }}">edit</a>
                                            {!! Form::open(['url' => route('customer.destroy', $customer->id), 'method' => 'DELETE']) !!}
                                                <button class="btn btn-primary" type="submit" id="delete-customer">Delete</button>
                                            {!! Form::close() !!}
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
