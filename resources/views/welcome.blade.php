<!DOCTYPE html>
<html>
<head>
    <title>Quiz</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
<div id="quiz-container">
    <div id="name-form" style="display: none;">
        @csrf
        <label for="name">Enter your name:</label>
        <input type="text" id="name" name="name" required>
        <button type="button" id="submit-name">Next</button>
    </div>
    <div id="welcome-message"></div>

    <div id="question-container"></div>
</div>
<script>
    $(document).ready(function () {
        let skippedCount = 0;
        let correctCount = 0;
        let wrongCount = 0;

        // Function to fetch user name from the server
        function getUserName() {
            $.ajax({
                url: "{{ route('getUserName') }}",
                method: 'GET',
                success: function (response) {
                    if (response.user_name) {
                        // If user name is available, hide name form and show welcome message
                        $('#name-form').hide();
                        $('#welcome-message').text('Welcome, ' + response.user_name + '! Attempt the quiz below.');
                        // Proceed to load the questions
                        loadQuestion();
                    } else {
                        // If user name is not available, show name form
                        $('#name-form').show();
                    }
                }
            });
        }

        // Call the function to get user name when the page loads
        getUserName();

        // Submit user name when Next button is clicked
        $('#submit-name').on('click', function () {
            const userName = $('#name').val().trim();
            if (userName !== '') {
                // Submit the user name via AJAX
                $.ajax({
                    url: "{{ route('quiz.storeName') }}",
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {name: userName},
                    success: function (response) {
                        // Once user name is submitted, update UI and load the questions
                        $('#name-form').hide();
                        $('#welcome-message').text('Welcome, ' + userName + '! Attempt the quiz below.');
                        loadQuestion();
                    }
                });
            } else {
                alert('Please enter your name.');
            }
        });

        // Function to load questions via AJAX
        function loadQuestion() {
            $.ajax({
                url: "{{ route('quiz.show') }}",
                method: 'GET',
                data: {skipped_questions: skippedCount},
                success: function (response) {
                    if (response.redirect) {
                        window.location.href = response.redirect;
                    } else {
                        renderQuestion(response);
                    }
                }
            });
        }

        // Function to render questions
        function renderQuestion(response) {
            let questionHtml = `
                <form id="question-form">
                    <h1>Question</h1>
                    <p>${response.question.question_text}</p>
                    ${response.answers.map(answer => `
                        <input type="radio" name="answer" value="${answer.id}" required> ${answer.answer_text}<br>
                    `).join('')}
                    <input type="hidden" name="question_id" value="${response.question.id}">
                    <button type="button" class="skip" data-question-id="${response.question.id}">Skip</button>

                    <button type="submit">Next</button>
                </form>
            `;
            $('#question-container').html(questionHtml);

            // Handle skip button click
            $('.skip').on('click', function () {
                const questionId = $(this).data('question-id');

                // Submit the skipped question along with its ID
                $.ajax({
                    url: "{{ route('quiz.submit') }}",
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        question_id: questionId,
                        skipped: true // Add a flag to indicate that the question was skipped
                    },
                    success: function (response) {
                        skippedCount++;
                        loadQuestion();
                    }
                });
            });
        }

        // Submit answer when question form is submitted
        $(document).on('submit', '#question-form', function (e) {
            e.preventDefault();
            $.ajax({
                url: "{{ route('quiz.submit') }}",
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: $(this).serialize(),
                success: function (response) {
                    if (response.correct) {
                        correctCount++;
                    } else {
                        wrongCount++;
                    }

                    // After handling the response, load the next question
                    loadQuestion();
                }
            });
        });
    });
</script>

</body>
</html>
