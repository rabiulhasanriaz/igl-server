@if(!empty($packages))
	@foreach ($packages as $package)
		<tr>
			<td>{{ $loop->iteration }}</td>
			<td>{{ $package->package_price }} Tk</td>
			<td>{{ $package->package_details }}</td>
			<td>{{ $package->validity }} Tk</td>
			<td>
				<button type="button" class="btn btn-primary btn-sm" data-toggle="modal" onclick="buyPackage('{{ $package->id }}', '{{ $package->package_price }}')">
					Buy Now
				</button>
			</td>
		</tr>
	@endforeach
@endif