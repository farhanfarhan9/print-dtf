<div>
    <x-slot name="header">
        <div class="flex justify-between">
            <h2 class="text-3xl font-semibold leading-tight text-gray-800">
                Internal Process
            </h2>
        </div>
    </x-slot>
    <div class="pt-12">
        @forelse ($internals as $execution_date => $internalProcesses)
            <div class="mb-10">
                <x-card>
                    <div class="mb-2">Batch {{ \Carbon\Carbon::parse($execution_date)->format('d F Y') }}</div>
                    @php
                        $byMachines = $internalProcesses->groupBy('machine_no');
                    @endphp
                    @foreach ($byMachines as $key => $byMachine)
                        @if ($key == null)
                            Belum ada Mesin
                        @else
                            Mesin ke-{{ $key }}
                        @endif
                        @foreach ($byMachine as $internal)
                            <x-card shadow='false' cardClasses="border mb-5">
                                {{ $internal->purchase_order_id }}
                            </x-card>
                        @endforeach
                    @endforeach
                </x-card>
            </div>
        @empty
            No Data
        @endforelse
    </div>
</div>
