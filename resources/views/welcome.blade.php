@extends('inc.outline')

@section('content')

    <div class="row mt-5">
        <div class="col-sm-8 mx-auto">
            <div class="card">
                <div class="card-header">
                    Obtain Access Token
                </div>
                <h4 id="access_token"></h4>
                <div class="card-body">
                    <button id="getAccessToken" class="btn btn-primary">Request Access Token</button>
                </div>
            </div>

            <div class="card mt-5">
                <div class="card-header">
                    Register URLs
                </div>
                <div class="card-body">
                    <button class="btn btn-primary">Register URLs</button>
                </div>
            </div>

            <div class="card mt-5 mb-5">
                <div class="card-header">
                    Simulate Transaction
                </div>
                <div class="card-body">
                    <form action="#">
                        @csrf
                        <div class="form-group">
                            <label for="amount">Amount</label>
                            <input type="number" name="amount" class="form-control" id="amount">
                        </div>

                        <div class="form-group">
                            <label for="account">Account</label>
                            <input type="number" name="account" class="form-control" id="account">
                        </div>
                        <button class="btn btn-primary">Simulate Payment</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- <div class="card text-center" style="margin-top: 50px">
        <div class="card-header">
            Daraja
        </div>
        <div class="card-body">
            <h5 class="card-title">C2B</h5>
            <p class="card-text">With supporting text below as a natural lead-in to additional content.</p>
            <a href="#" class="btn btn-primary">Transact</a>
        </div>
        <div class="card-footer text-muted">
            Powered by ByteCity
        </div>
    </div> --}}
@endsection