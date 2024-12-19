<td>
    <div class="d-flex">
        <div class="flex-shrink-0 avatar-md bg-light rounded p-1">
            <img src="{{ $order->plan->image }}" alt="" class="img-fluid d-block">
        </div>
        <div class="flex-grow-1 ms-3">
            <h5 class="fs-15"><a href="{{ route('plans.edit', $order->plan->id) }}" class="link-primary">{{ $order->plan->title }}</a></h5>
            <h6 class="fs-15"><a href="{{ route('plans.edit', $order->plan->id) }}" class="link-primary">{{ \Illuminate\Support\Str::limit($order->plan->description,30) }}</a></h6>
        </div>
    </div>
</td>
<td>
    {{ $order->plan_package_day->plan_package->plan_category->category->title}}
</td>
<td>
    {{ $order->plan_package_day->plan_package->package->title}}
</td>
<td>
    {{ $order->plan_package_day->day_meal_count->title}}
</td>
<td> {{ $order->plan_package_day_price ?? 0 }} @lang('SAR') </td>

