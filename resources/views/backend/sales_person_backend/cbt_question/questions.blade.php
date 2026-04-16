
@extends('backend.student_backend.student_dashboard')
@section('student')

<style>
/* ===== Progressive Nav - Desktop (default) ===== */
#questionNavigator {
    white-space: normal;
    overflow: visible;
}

/* ===== Progressive Nav - Mobile Only ===== */
@media (max-width: 768px) {
    #questionNavigator {
        white-space: nowrap;
        overflow-x: auto;
        overflow-y: hidden;
        -webkit-overflow-scrolling: touch;
        padding-bottom: 5px;
    }

    #questionNavigator .question-nav-btn {
        display: inline-block;
        width: 30px;
        height: 30px;
        font-size: 0.75rem;
        margin-right: 4px;
    }
}

</style>


<div class="container">
    <h4 class="text-center my-2" style="color:green"> 
        Subject : {{ $cbtTest->subject->subject_name ?? 'CBT Test' }} - {{ $cbtTest->assessment_type }}
    </h4>
    <h5 class="text-center" style="color:red">Instruction : {{ ucwords($cbtTest->title) }}</h5>
    <!--<p class="text-center warning" style="color:green">Answer All Questions.</p> -->

    <div id="timer" class="alert alert-info text-center">Loading timer…</div>
    <div class="alert alert-secondary text-center mb-3">
    Question <strong><span id="currentQuestionNumber">1</span></strong> 
    of <strong>{{ count($questions) }}</strong>
    </div>


    <form id="cbtForm">
        @csrf
        @foreach($questions as $index => $question)
            @php
                $existingAnswer = $attempt->answers()->where('cbt_question_id', $question->id)->first();
                $selectedOption = $existingAnswer ? strtoupper($existingAnswer->selected_option) : null;
            @endphp

            <div class="card my-3 p-3 question-card" data-index="{{ $index }}" style="display: {{ $index === 0 ? 'block' : 'none' }}">
                <p><strong>Q{{ $index + 1 }}:</strong> <b>{{ ucfirst($question->question_text) }}</b></p>

                @foreach(['A','B','C','D'] as $opt)
                    <label>
                        <input type="radio"
                               autocomplete='off'
                               name="question_{{ $question->id }}"
                               value="{{ $opt }}"
                               data-attempt="{{ $attempt->id }}"
                               data-question="{{ $question->id }}"
                               {{ $selectedOption === $opt ? 'checked' : '' }}>
                        {{ $opt }}. {{ ucfirst($question->{'option_'.strtolower($opt)}) }}
                    </label><br>
                @endforeach
            </div>
        @endforeach

        <div class="text-left my-4">
            <button type="button" id="prevBtn" class="btn btn-secondary" style="display:none">Previous</button>
            <button type="button" id="nextBtn" class="btn btn-primary">Next</button>
            <button type="button" id="submitBtn" class="btn btn-success" style="display:none">Submit Test</button>
        </div>

        <!-- ===== PROGRESSIVE QUESTION NAVIGATOR ===== -->
<!-- ===== PROGRESSIVE QUESTION NAVIGATOR (RESPONSIVE) ===== -->
<div class="my-4 text-center" id="questionNavigator">
    @foreach($questions as $index => $question)
        <button type="button"
                class="btn btn-sm mb-1 question-nav-btn"
                data-index="{{ $index }}"
                style="width:35px; height:35px; border-radius:50%; background-color:gray; color:white; display:inline-block; margin-right:5px;">
            {{ $index + 1 }}
        </button>
    @endforeach
</div>

    </form>
</div>


<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
(function () {
    // ==== TIMER (server-locked, timezone safe) ====
    const endTime   = Number(@json($endTime));   // UTC timestamp from server
    const serverNow = Number(@json($serverNow)); // UTC now from server
    const clientNow = Date.now();

    // adjust for client/server clock drift
    const offset = serverNow - clientNow;

    const timerEl = document.getElementById('timer');
    function formatTwo(n){ return n < 10 ? '0'+n : n; }

    function tick() {
        const now = Date.now() + offset; // always aligned to server UTC
        const diff = endTime - now;

        if (diff <= 0) {
            timerEl.textContent = 'Time is up! Submitting...';
            submitTest(true);
            return;
        }


        const totalSeconds = Math.floor(diff / 1000);
        const hours = Math.floor(totalSeconds / 3600);
        const minutes = Math.floor((totalSeconds % 3600) / 60);
        const seconds = totalSeconds % 60;
        timerEl.textContent = hours > 0
            ? `Time Remaining: ${hours}h ${formatTwo(minutes)}m ${formatTwo(seconds)}s`
            : `Time Remaining: ${minutes}m ${formatTwo(seconds)}s`;
    }
    const timerInterval = setInterval(tick, 1000);
    tick();

    // ==== SAVE ANSWER (AJAX) ====
    document.querySelectorAll('input[type="radio"]').forEach(option => {
        option.addEventListener('change', function(){
            const attemptId = this.dataset.attempt;
            const questionId = this.dataset.question;
            const selected = this.value;
            
            fetch("{{ url('student/cbt/save-answer') }}/" + attemptId + "/" + questionId, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ selected_option: selected })
            })

            // NavColor
            .then(() => updateNavColors())

            .catch(err => {
                console.error('Save failed', err);
                alert('Failed to save answer. Please check your connection.');
            });
        });
    });

    // ==== NAVIGATION ====
    const questionCards = document.querySelectorAll('.question-card');
    let currentIndex = {{ $currentIndex }};

//QUESTION NAVIGATION COLOR    
const navButtons = document.querySelectorAll('.question-nav-btn');

function updateNavColors() {
    questionCards.forEach((card, i) => {
        const btn = navButtons[i];
        const answered = Array.from(card.querySelectorAll('input[type="radio"]')).some(r => r.checked);
        btn.style.backgroundColor = answered ? 'green' : 'gray';
    });

// Scroll only on mobile screens
if (window.innerWidth <= 768) {
    const currentBtn = navButtons[currentIndex];
    if (currentBtn) {
        currentBtn.scrollIntoView({ behavior: 'smooth', inline: 'center' });
    }
  }
}

// Make navigator buttons clickable
navButtons.forEach(btn => {
    btn.addEventListener('click', () => {
        const index = parseInt(btn.dataset.index);
        currentIndex = index;
        showQuestion(currentIndex);
        updateNavColors();
    });
});

// Call this initially to set colors
updateNavColors();



    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    const submitBtn = document.getElementById('submitBtn');

    function showQuestion(index) {
        questionCards.forEach((card, i) => card.style.display = (i === index) ? 'block' : 'none');
        prevBtn.style.display   = (index === 0) ? 'none' : 'inline-block';
        nextBtn.style.display   = (index === questionCards.length - 1) ? 'none' : 'inline-block';
        submitBtn.style.display = (index === questionCards.length - 1) ? 'inline-block' : 'none';

        //page remain at same page at any fresh
        document.getElementById('currentQuestionNumber').textContent = index + 1;

        //block muultible brower usage 
        fetch("{{ url('student/cbt/save-progress') }}/{{ $attempt->id }}", {
    method: 'POST',
    headers: {
        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
        'Content-Type': 'application/json'
    },
    body: JSON.stringify({ index: index })
});

    // Update progressive nav colors
    updateNavColors();

}

    

    nextBtn.addEventListener('click', () => {
        if (currentIndex < questionCards.length - 1) {
            currentIndex++;
            showQuestion(currentIndex);
        }
    });

    prevBtn.addEventListener('click', () => {
        if (currentIndex > 0) {
            currentIndex--;
            showQuestion(currentIndex);
        }
    });

    showQuestion(currentIndex);

    // ==== SUBMIT (auto or manual) via AJAX ====
function submitTest(auto = false) {
    //clearInterval(timerInterval);

   if (!auto) {

    // Check unanswered questions
    let unanswered = [];

    questionCards.forEach((card, index) => {
        const radios = card.querySelectorAll('input[type="radio"]');
        const answered = Array.from(radios).some(r => r.checked);
        if (!answered) {
            unanswered.push(index + 1);
        }
    });

    let warningText = "You can not change your answers after submitting.";

    if (unanswered.length > 0) {
        warningText = `You have ${unanswered.length} unanswered question(s).\n\nAre you sure you want to submit?`;
    }

    Swal.fire({
        title: 'Submit Test?',
        text: warningText,
        icon: unanswered.length > 0 ? 'warning' : 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, Submit!',
        cancelButtonText: 'Review Answers'
    }).then((result) => {
        if (result.isConfirmed) {
            submitTest(true); // continue submission
        } else {
            //setInterval(tick, 1000); // restart timer if cancelled
        }
    });

    return;
}

     const totalQuestions = {{ count($questions) }};


    clearInterval(timerInterval);

    fetch("{{ route('student.cbt.submit', $attempt->id) }}", {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
            'Accept': 'application/json'
        }
    })
    .then(res => res.ok ? res.json() : Promise.reject(res))
    .then(data => {
        if (data && data.success) {
            Swal.fire({
                title: 'Test Submitted!',
                //html: `<strong>You Scored:</strong> ${data.score} / ${totalQuestions}`,
                text: 'You Scored: ' + data.score+ ' out of ' + totalQuestions,
                icon: 'success',
                confirmButtonText: 'OK',
                confirmButtonColor: '#3085d6'
            }).then(() => {
                window.location.href = "{{ route('student.index') }}";
            });
        } else {
            Swal.fire({
                title: 'Submission Failed',
                text: 'Could not submit. Please try again.',
                icon: 'error',
                confirmButtonText: 'OK'
            });
        }
    })
    .catch(err => {
        console.error('Submit failed', err);
        window.location.href = "{{ route('student.index') }}";
    });
}

document.getElementById('submitBtn').addEventListener('click', () => submitTest(false));
})();




(function() {
    const attemptId = "{{ $attempt->id }}";
    const storageKey = `cbt_open_${attemptId}`;

    // ===== Check if test is already open in another tab =====
    if (localStorage.getItem(storageKey)) {
        // Already open in another tab
        Swal.fire({
            title: 'Multiple Tabs Detected',
            text: 'This CBT test is already open in another tab. You cannot open multiple tabs.',
            icon: 'error',
            confirmButtonText: 'OK'
        }).then(() => {
            window.location.href = "{{ route('student.index') }}"; // redirect away
        });
    } else {
        // Mark this tab as active
        localStorage.setItem(storageKey, 'true');
    }

    // ===== Listen for tab close or refresh =====
    window.addEventListener('beforeunload', () => {
        localStorage.removeItem(storageKey);
    });

    // ===== Optional: detect if storage changes (another tab opened) =====
    window.addEventListener('storage', (event) => {
        if (event.key === storageKey && event.newValue) {
            Swal.fire({
                title: 'Another Tab Opened',
                text: 'You opened this CBT test in another tab. This tab will now be closed.',
                icon: 'warning',
                confirmButtonText: 'OK'
            }).then(() => {
                window.location.href = "{{ route('student.index') }}"; // redirect away
            });
        }
    });
})();



// ===== CLIENT-SIDE ANTI-COPY / ANTI-HIGHLIGHT / ANTI-RIGHT-CLICK =====
(function() {
    // Disable right click
    document.addEventListener('contextmenu', function(e) {
        e.preventDefault();
        Swal.fire({
            icon: 'warning',
            title: 'Action Not Allowed',
            text: 'Right-click is disabled during this test.'
        });
    });

    // Disable copy, cut, paste
    ['copy', 'cut', 'paste'].forEach(evt => {
        document.addEventListener(evt, function(e) {
            e.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: 'Action Not Allowed',
                text: `${evt.charAt(0).toUpperCase() + evt.slice(1)} is disabled during this test.`
            });
        });
    });

    // Disable text selection / highlight
    document.addEventListener('selectstart', function(e) {
        e.preventDefault();
    });
    document.addEventListener('mousedown', function(e) {
        if (e.detail > 1) e.preventDefault(); // prevent double-click selection
    });

    // Optional: disable keyboard shortcuts (Ctrl+C, Ctrl+X, Ctrl+V, Ctrl+A)
    document.addEventListener('keydown', function(e) {
        if (e.ctrlKey && ['c','x','v','a'].includes(e.key.toLowerCase())) {
            e.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: 'Action Not Allowed',
                text: 'Keyboard shortcuts for copy, cut, paste, select-all are disabled.'
            });
        }
    });
})();



</script>


@endsection
