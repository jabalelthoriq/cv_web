<!DOCTYPE html>
<html>
<head>
    <title>Test Jobs</title>
</head>
<body style="background: white; color: black; padding: 20px;">
    <h1>TEST PAGE - JOBS DATA</h1>
    <p>Job Count: {{ $jobData->count() }}</p>
    
    <table border="1" cellpadding="5">
        <tr>
            <th>Job Title</th>
            <th>Company</th>
            <th>Match Score</th>
        </tr>
        @foreach($jobData as $job)
        <tr>
            <td>{{ $job['job_title'] }}</td>
            <td>{{ $job['company'] }}</td>
            <td>{{ $job['match_score'] }}%</td>
        </tr>
        @endforeach
    </table>
</body>
</html>