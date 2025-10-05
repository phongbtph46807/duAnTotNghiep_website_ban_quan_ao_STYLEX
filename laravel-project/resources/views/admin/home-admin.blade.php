@extends('layouts.admin-layout')
@section('content')

<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Bảng điều khiển</h3>
                <p class="text-subtitle text-muted"></p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb mb-0 d-flex align-items-start">
                        <li class="breadcrumb-item active d-flex align-items-start" aria-current="page">
                            <div class="d-flex flex-column me-2">
                                <span>{{ auth()->user()->name }}</span>
                                <span class="fw-bold small text-muted">{{ auth()->user()->email }}</span>
                            </div>
                            <i class="bi bi-person-circle text-primary mt-n1" style="font-size: 2rem;"></i>
                        </li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>
@endsection
