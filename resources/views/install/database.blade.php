@extends('layouts.install')

@section('content')

    <div class="row">
        <div class="col-lg-6">

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Permissions</h3>
                </div>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Folder</th>
                            <th class="text-center">Permission</th>
                            <th class="text-center">Required</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($results['permissions'] as $permission)
                        <tr>
                            <td>
                                {{ $permission['folder'] }}
                            </td>
                            <td width="110" class="text-center">
                                {{ $permission['permission'] }}
                            </td>
                            <td width="110" class="text-center">
                                {{ $permission['required'] }}
                            </td>
                            <td width="110" class="text-center">
                                @if($permission['success'])
                                    <i class="fe fe-check-circle text-success"></i>
                                @else
                                    <i class="fe fe-x-circle text-red"></i>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">PHP version</h3>
                </div>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Installed</th>
                            <th class="text-center">Required</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                {{ $results['php']['installed'] }}
                            </td>
                            <td width="110" class="text-center">
                                {{ $results['php']['required'] }}
                            </td>
                            <td width="110" class="text-center">
                                @if($results['php']['success'])
                                    <i class="fe fe-check-circle text-success"></i>
                                @else
                                    <i class="fe fe-x-circle text-red"></i>
                                @endif
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">PHP extensions</h3>
                </div>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Extension</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($results['extensions'] as $extension)
                        <tr>
                            <td>
                                {{ $extension['extension'] }}
                            </td>
                            <td width="110" class="text-center">
                                @if($extension['success'])
                                    <span class="badge w-100 badge-success">INSTALLED</span>
                                @else
                                    <span class="badge w-100 badge-danger">NOT INSTALLED</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        </div>
        <div class="col-lg-6">

            <form method="post" action="{{ route('install.db') }}" autocomplete="off">
                @csrf

                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Application &amp; Database</h3>
                    </div>
                    <div class="card-body">

                        <div class="form-group">
                            <label class="form-label">Application URL</label>
                            <input type="text" name="APP_URL" value="{{ request()->root() }}" class="form-control" placeholder="Application URL" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Database hostname</label>
                            <input type="text" name="DB_HOST" value="{{ old('DB_HOST', 'localhost') }}" class="form-control" placeholder="Database hostname" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Database port</label>
                            <input type="text" name="DB_PORT" value="{{ old('DB_PORT', '3306') }}" class="form-control" placeholder="Database port" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Database name</label>
                            <input type="text" name="DB_DATABASE" value="{{ old('DB_DATABASE') }}" class="form-control" placeholder="Database name" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Database username</label>
                            <input type="text" name="DB_USERNAME" value="{{ old('DB_USERNAME') }}" class="form-control" placeholder="Database username" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Database password</label>
                            <input type="text" name="DB_PASSWORD" value="{{ old('DB_PASSWORD') }}" class="form-control" placeholder="Database password">
                        </div>

                    </div>
                    <div class="card-footer">
                        @if($passed)
                            <button type="submit" class="btn btn-block btn-primary">Next step</button>
                        @else
                            <button type="button" class="btn btn-block btn-secondary disabled" disabled>DM Pilot can't be installed. Please check all requirements.</button>
                        @endif
                    </div>
                </div>

            </form>

        </div>

    </div>

@endsection