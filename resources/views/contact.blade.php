@extends('layouts.app')
@section('content')

<main class="pt-90">
    <div class="mb-4 pb-4"></div>
    <section class="contact-us container">
        <div class="mw-930">
            <h2 class="page-title">CONTACT US</h2>
        </div>
    </section>

    <hr class="mt-2 text-secondary " />
    <div class="mb-4 pb-4"></div>

    <section class="contact-us container">
        <div class="mw-930">
            <div class="contact-us__form">
                @if(session('success'))
                    <div class="alert alert-success text-center">
                        {{ session('success') }}
                    </div>
                @endif

           
                <form name="contact-us-form" class="needs-validation" novalidate="" method="POST" action="{{ route('home.contact.store') }}">
                    @csrf 

                    <h3 class="mb-5">Get In Touch</h3>

                    <div class="form-floating my-4">
                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                               name="name" placeholder="Name *" value="{{ old('name') }}" required>
                        <label for="contact_us_name">Name *</label>
                        @error('name') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-floating my-4">
                        <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                               name="phone" placeholder="Phone *" value="{{ old('phone') }}" required>
                        <label for="contact_us_phone">Phone *</label>
                        @error('phone') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-floating my-4">
                        <input type="email" class="form-control @error('email') is-invalid @enderror" 
                               name="email" placeholder="Email address *" value="{{ old('email') }}" required>
                        <label for="contact_us_email">Email address *</label>
                        @error('email') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>

                    <div class="my-4">
                        <textarea class="form-control form-control_gray @error('comment') is-invalid @enderror" 
                                  name="comment" placeholder="Your Message" cols="30" rows="8" required>{{ old('comment') }}</textarea>
                        @error('comment') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>

                    <div class="my-4">
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>

                </form>
            </div>
        </div>
    </section>
</main>

@endsection
