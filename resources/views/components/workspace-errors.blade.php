@if(session('status'))<div class="notice success" role="status"> <x-market-icon name="check"/>{{ session('status') }}</div>@endif
@if($errors->any())<div class="notice error" role="alert"><strong>Please check the following:</strong><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
