@extends('layouts.app')

@section('title', __('Payments'))

@section('content')
<div class="page-header">
    <h1 class="page-title">@lang('Payments')</h1>
    <div class="page-options">
        <form method="get" action="{{ route('settings.payments') }}" autocomplete="off" class="d-flex">
            <div class="input-group">
                <select name="is_paid" class="form-control w-auto ml-2">
                    <option value="">@lang('All statuses')</option>
                    <option value="1" {{ (Request::get('is_paid') == '1' ? 'selected' : '') }}>@lang('Paid')</option>
                    <option value="0" {{ (Request::get('is_paid') == '0' ? 'selected' : '') }}>@lang('Not paid')</option>
                </select>

                <span class="input-group-btn ml-2 mr-4">
                    <button class="btn btn-primary" type="submit">
                        <i class="fe fe-filter"></i>
                    </button>
                </span>
            </div>
        </form>
    </div>
</div>

<div class="row">
    <div class="col-md-9">

        @if($data->count() > 0)
            <div class="card">
                <div id="payments-chart" style="height: 10rem"></div>
                <div class="table-responsive">
                    <table class="table card-table table-vcenter text-nowrap">
                        <thead>
                            <tr>
                                <th>@lang('User')</th>
                                <th>@lang('Package')</th>
                                <th>@lang('Gateway')</th>
                                <th>@lang('Total')</th>
                                <th>@lang('Status')</th>
                                <th>@lang('Date')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data as $item)
                            <tr>
                                <td>
                                    {{ $item->user->name }}
                                </td>
                                <td>
                                    {{ $item->package->title }}
                                </td>
                                <td>
                                    @lang('pilot.payment_' . $item->gateway)
                                    <div class="small text-muted">{{ $item->reference }}</div>
                                </td>
                                <td>
                                    {{ $item->total }}
                                    {{ $item->currency }}
                                </td>
                                <td>
                                    @if($item->is_paid)
                                        <span class="text-green"><i class="fe fe-check"></i> @lang('Paid')</span>
                                    @else
                                        <span class="text-muted"><i class="fe fe-minus"></i> @lang('Not paid')</span>
                                    @endif
                                </td>
                                <td>
                                    {{ $item->created_at->format('H:i M d, Y') }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        {{ $data->appends( Request::all() )->links() }}

        @if($data->count() == 0)
            <div class="alert alert-primary text-center">
                <i class="fe fe-alert-triangle mr-2"></i> @lang('No payments found')
            </div>
        @endif

    </div>
    <div class="col-md-3">
        @include('partials.settings-sidebar')
    </div>
</div>
@stop

@push('scripts')
<script>
$(document).ready(function(){

    c3.generate({
        bindto: '#payments-chart',
        data: {
            columns: [
                ['paid', {{ $chart['paid'] }}],
                ['not_paid', {{ $chart['not_paid'] }}]
            ],
            type: 'area',
            groups: [
                [ 'paid', 'not_paid']
            ],
            colors: {
                'paid': '#5eba00',
                'not_paid': '#cd201f'
            },
            names: {
                'paid': '@lang('Paid')',
                'not_paid': '@lang('Not paid')'
            }
        },
        axis: {
            y: {
                padding: {
                    bottom: 0,
                },
                show: false,
                tick: {
                    outer: false
                }
            },
            x: {
                padding: {
                    left: 0,
                    right: 0
                },
                show: false
            }
        },
        legend: {
            position: 'inset',
            padding: 0,
            inset: {
                anchor: 'top-left',
                x: 20,
                y: 8,
                step: 10
            }
        },
        tooltip: {
            format: {
                title: function (x) {
                    return '';
                }
            }
        },
        padding: {
            bottom: 0,
            left: -1,
            right: -1
        },
        point: {
            show: false
        }
    });

});
</script>
@endpush