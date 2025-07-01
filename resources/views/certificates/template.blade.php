<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Certificate</title>
</head>

<body style="text-align:center; font-family:sans-serif">
    <h1>Certificate of Completion</h1>
    <p>This certifies that</p>
    <h2>{{ $student->name }}</h2>
    <p>has successfully completed the quiz</p>
    <h3>{{ $quiz->title }}</h3>
    <p>on course <strong>{{ $course->title }}</strong></p>
    <p>with score: <strong>{{ $score }}</strong></p>
    <p>Date: {{ $date }}</p>
</body>

</html>