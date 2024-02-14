@foreach (session('flash_notification', collect())->toArray() as $message)
    @if ($message['overlay'])
        @include('flash::modal', [
            'modalClass' => 'flash-modal',
            'title'      => $message['title'],
            'body'       => $message['message']
        ])
    @else

    <div id="toast-container" class="toast-container toast-top-right"><div class="toast toast-@if($message['level'] =='danger'){{'error'}}@else{{'success'}}@endif" aria-live="polite" style="display: block;"><button type="button" class="toast-close-button" role="button">×</button><div class="toast-title">@if($message['level'] =='danger'){{'Error'}}@else{{'Success'}}@endif!</div><div class="toast-message"> {!! $message['message'] !!}</div></div></div>

    <!-- <div class="alert alert-{{ $message['level'] }}" role="alert">
        <h4 class="alert-heading">{{ $message['level'] }}</h4>
        @if ($message['important'])
        <button type="button"
                class="close"
                data-dismiss="alert"
                aria-hidden="true"
        >&times;</button>
        @endif
        <div class="alert-body">
        {!! $message['message'] !!}
        </div>
    </div> -->

    @endif
@endforeach

{{ session()->forget('flash_notification') }}
