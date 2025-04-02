<x-app-layout>
    <div class="container">
        <div class="card">
            <div class="card-body">
                <div class="row justify-content-center">
                    <div class="col-md-10">
                        <div class="card">
                            <div class="card-header">Trip List</div>

                            <div class="card-body">
                                @if (session('success'))
                                    <div class="alert alert-success">
                                        {{ session('success') }}
                                    </div>
                                @endif

                                <a href="{{ route('trips.create') }}" class="mb-3 btn btn-primary">Available Trips</a>

                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Origin</th>
                                            <th>Destination</th>
                                            <th>Van Location</th>
                                            <th>Trip Started</th>
                                            <th>Trip Completed</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($trips as $trip)
                                            <tr>
                                                <td>
                                                    Longitude: {{ $trip->origin['longitude'] ?? 'N/A' }}<br>
                                                    Latitude: {{ $trip->origin['latitude'] ?? 'N/A' }}
                                                </td>
                                                <td>
                                                    Longitude: {{ $trip->destination['longitude'] ?? 'N/A' }}<br>
                                                    Latitude: {{ $trip->destination['latitude'] ?? 'N/A' }}
                                                </td>
                                                <td>
                                                    Longitude: {{ $trip->van_location['longitude'] ?? 'N/A' }}<br>
                                                    Latitude: {{ $trip->van_location['latitude'] ?? 'N/A' }}
                                                </td>
                                                <td>{{ $trip->is_started ? 'Yes' : 'No' }}</td>
                                                <td>{{ $trip->is_complete ? 'Yes' : 'No' }}</td>
                                                <td>
                                                    <a href="{{ route('trips.show', $trip->id) }}" class="btn btn-info btn-sm">View</a>
                                                    <form action="{{ route('trips.destroy', $trip->id) }}" method="POST" style="display: inline-block;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this trip?')">Delete</button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>

                                {{ $trips->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
