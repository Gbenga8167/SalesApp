@extends('backend.student_backend.student_dashboard')
@section('student')


<div class="container">
  <h4 style="text-align:center; margin-top:45px; color:green">
      {{ $cbtTest->subject->subject_name ?? 'CBT Test' }} - CBT Test
  </h4>

  @if($testStatus === 'not_started')
    <div class="alert alert-warning" style="text-align:center; padding-bottom:200px">
      <p style="margin-top:100px">
        Test has not started yet. <br><br>
        It will start in :
        <span id="countdown" class="alert alert-danger"></span>
      </p>
    </div>
  @else
    <center>
      <br>
      <a href="{{ route('student.begin.test', $cbtTest->id) }}" class="btn btn-success">Begin Test</a>
    </center>
  @endif
</div>

@if($testStatus === 'not_started')
<script>
  // These are raw integer millisecond timestamps from the server (UTC)
  const startTime = {{ $startTime }};
  const serverNow = {{ $serverNow }};

  // Client clock (may be off); compute drift from server
  const clientNow = Date.now();
  const offset = serverNow - clientNow; // add this to client time to get server-true time

  const el = document.getElementById('countdown');

  function pad(n){ return n < 10 ? '0' + n : n; }

  function tick(){
    const now = Date.now() + offset;     // server-aligned "now" in ms
    const diff = startTime - now;        // ms until start

    if (diff <= 0) {
  el.textContent = 'Starting...';
  clearInterval(timer); // stop ticking
  setTimeout(() => location.reload(true), 2000); // force hard reload after 2s
  return;
}


    const totalSeconds = Math.floor(diff / 1000);
    const days    = Math.floor(totalSeconds / 86400);
    const hours   = Math.floor((totalSeconds % 86400) / 3600);
    const minutes = Math.floor((totalSeconds % 3600) / 60);
    const seconds = totalSeconds % 60;

    let text = '';
    if (days > 0) text += days + 'd -- ';
    text += hours + 'h -- ' + pad(minutes) + 'm -- ' + pad(seconds) + 's';
    el.textContent = text;

    // Styling thresholds
    if (diff <= 3600000) { // < 1 hour
      el.style.color = 'green';
      el.style.fontWeight = 'bold';
    }
    if (diff <= 600000) {  // < 10 minutes
      el.style.color = 'red';
      el.style.fontWeight = 'bold';
      el.style.animation = 'blink 1s step-start infinite';
    }
  }

  // Blink animation
  const style = document.createElement('style');
  style.textContent = `@keyframes blink { 50% { opacity: 0; } }`;
  document.head.appendChild(style);

  tick();
  const timer = setInterval(tick, 1000);

</script>
@endif

@endsection
