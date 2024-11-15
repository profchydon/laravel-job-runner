@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h1 class="mb-4">Admin Dashboard - Job Logs</h1>

    <div class="table-responsive">
        <table class="table table-striped table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Class Name</th>
                    <th>Method Name</th>
                    <th>Status</th>
                    <th>Retry Count</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($jobs as $job)
                    <tr>
                        <td>{{ $job->id }}</td>
                        <td>{{ $job->class_name }}</td>
                        <td>{{ $job->method_name }}</td>
                        <td>
                            <span class="badge
                                @if ($job->status === 'successful') bg-success
                                @elseif ($job->status === 'failed') bg-danger
                                @elseif ($job->status === 'running') bg-primary
                                @elseif ($job->status === 'pending') bg-warning
                                @else bg-secondary
                                @endif">
                                {{ ucfirst($job->status) }}
                            </span>
                        </td>
                        <td>{{ $job->retry_count }}</td>
                        <td>
                            <a href="" class="btn btn-info btn-sm">View</a>
                            @if ($job->status === 'running')
                                <form method="POST" action="{{ route('admin.jobs.cancel', $job->id) }}" style="display:inline;">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="btn btn-danger btn-sm">Cancel</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">No job logs found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-center">
        {{ $jobs->links('pagination::bootstrap-4') }}
    </div>
</div>
@endsection
