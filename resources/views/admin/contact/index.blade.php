@extends('admin.layouts.app')
@section('title', 'Quản lí liên hệ')
@section('content')

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h4 class="card-title mb-0">Danh sách liên hệ</h4>
            </div><!-- end card header -->
            <div class="card-body">
                <div class="listjs-table" id="customerList">
                    <div class="table-responsive table-card mt-3 mb-1">
                        <table class="table align-middle text-center table-nowrap">
                            <thead class="table-light">
                            <tr>
                                <th data-sort="customer_id">ID</th>
                                <th data-sort="customer_name">Email</th>
                                <th data-sort="email">Tin nhắn</th>
                            </tr>
                            </thead>
                            <tbody class="list form-check-all">
                            @foreach($contacts as $contact)
                                <tr>
                                    <td class="id">
                                        #{{ $contact->id }}
                                    </td>
                                    <td class="customer_name">{{ $contact->email }}</td>
                                    <td class="email">{{ $contact->content }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
