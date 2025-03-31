<x-app-layout>
    <div class="container">
        <div class="card">
            <div class="card-body">
                <div class="row justify-content-center">
                    <div class="col-md-10">
                        <div class="card">
                            <div class="card-header">Vans List</div>

                            <div class="card-body">
                                @if (session('success'))
                                    <div class="alert alert-success">
                                        {{ session('success') }}
                                    </div>
                                @endif

                                <a href="{{ route('vans.create') }}" class="mb-3 btn btn-primary">Register Van</a>

                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>VanID</th>
                                            <th>VanCapacity</th>
                                            <th>NumberPlate</th>
                                            <th>Longitude</th>
                                            <th>Latitude</th>
                                            <th>VanOperator</th>
                                            <th>Driver</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($vans as $van)
                                            <tr>
                                                <td>{{ $van->VanID }}</td>
                                                <td>{{ $van->VanCapacity }}</td>
                                                <td>{{ $van->NumberPlate }}</td>
                                                <td>{{ $van->Longitude }}</td>
                                                <td>{{ $van->Latitude }}</td>
                                                <td>{{ $van->VanOperator }}</td>
                                                <td>{{ $van->Driver }}</td>
                                                <td>
                                                    <a href="{{ route('drivers.show', $van->VanID) }}"
                                                        class="btn btn-info btn-sm">View</a>
                                                    <a href="{{ route('drivers.edit', $van->VanID) }}"
                                                        class="btn btn-primary btn-sm">Edit</a>
                                                    <form action="{{ route('drivers.destroy', $van->VanID) }}"
                                                        method="POST" style="display: inline-block;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-sm"
                                                            onclick="return confirm('Are you sure you want to delete this driver?')">Delete</button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>

                                {{ $vans->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
