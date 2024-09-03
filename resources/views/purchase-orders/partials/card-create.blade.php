@if(empty($order))
<form method="POST" action="{{ url(route('laravel-crm.purchase-orders.store')) }}">
@else
<form method="POST" action="{{ url(route('laravel-crm.purchase-orders.store')) }}?order={{ $order->id }}">     
@endif        
    
    @csrf
    @component('laravel-crm::components.card')

        @component('laravel-crm::components.card-header')

            @slot('title')
                {{ ucfirst(__('laravel-crm::lang.create_purchase_order')) }}@isset($order)(s) {{ __('laravel-crm::lang.from_order') }} <a href="{{ route('laravel-crm.orders.show', $order) }}">{{ $order->order_id }}</a> @endisset
            @endslot

            @slot('actions')
                @include('laravel-crm::partials.return-button',[
                    'model' => new \VentureDrake\LaravelCrm\Models\PurchaseOrder(),
                    'route' => 'purchase-orders',
                    'text' => 'back_to_purchase_orders'
                ])
            @endslot

        @endcomponent

        @component('laravel-crm::components.card-body')

            @include('laravel-crm::purchase-orders.partials.fields')

        @endcomponent

        @component('laravel-crm::components.card-footer')
                <a href="{{ url(route('laravel-crm.purchase-orders.index')) }}" class="btn btn-outline-secondary">{{ ucfirst(__('laravel-crm::lang.cancel')) }}</a>
                <button type="submit" class="btn btn-primary" name="action" value="create_and_add_another">{{ ucfirst(__('laravel-crm::lang.create_and_add_another')) }}</button>
                <button type="submit" class="btn btn-primary" name="action">{{ ucfirst(__('laravel-crm::lang.create_purchase_order')) }}</button>
        @endcomponent

    @endcomponent
</form>