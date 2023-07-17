@extends('layouts.noauth')
@section('content')
  

<?php
  
?>

<main class="form-signin w-100 m-auto">
  <center>
  <form action="" method="POST">
  	<input type="hidden" name="_token" value="{{ csrf_token() }}">
    <img class="mb-4" src="/assets/brand/bootstrap-logo.svg" alt="" width="72" height="57">
    <h1 class="h3 mb-3 fw-normal">Please log in</h1>

    @if(Session::has('message_danger'))
      <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>Error!</strong> {{ Session::get('message_danger') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    @endif


    <div class="form-floating">
      <input type="email" class="form-control" name="email" placeholder="name@example.com">
      <label for="email">Email address</label>
    </div>
    <div class="form-floating">
      <input type="password" class="form-control" name="password" placeholder="Password">
      <label for="password">Password</label>
    </div>

    <!-- <div class="form-check text-start my-3">
      <input class="form-check-input" type="checkbox" value="remember-me" name="flexCheckDefault">
      <label class="form-check-label" for="flexCheckDefault">
        Remember me
      </label>
    </div> -->
    <button class="btn btn-primary w-100 py-2" type="submit">Log in</button>
    <p class="mt-5 mb-3 text-body-secondary">&copy; {!! date('Y') !!}</p>
  </form>
  </center>
</main>

@endsection