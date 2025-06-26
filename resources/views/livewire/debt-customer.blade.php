<div>
    <x-slot name="header">
        <div class="flex justify-between">
            <h2 class="text-3xl font-semibold leading-tight text-gray-800">
                Debt Customer
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="flex justify-end">
            <x-input wire:model.live.debounce.300ms="search" icon="search" class="sm:!w-1/4" shadowless="true"
                placeholder="Cari Customer atau Produk" />
        </div>

        <div class="relative mt-5 overflow-x-auto shadow-md sm:rounded-lg">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3">No</th>
                        <th scope="col" class="px-6 py-3">Customer Name</th>
                        <th scope="col" class="px-6 py-3">Nama Produk</th>
                        <th scope="col" class="px-6 py-3">Jumlah Hutang</th>
                        <th scope="col" class="px-6 py-3">Jumlah Bayar</th>
                        <th scope="col" class="px-6 py-3">Tanggal Terakhir Bayar</th>
                        <th scope="col" class="px-6 py-3">Tanggal Beli</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($debtCustomers as $index => $debtCustomer)
                        <tr class="bg-white border-b">
                            <td class="px-6 py-4">{{ $index + 1 }}</td>
                            <td class="px-6 py-4">{{ $debtCustomer->customer_name }}</td>
                            <td class="px-6 py-4">{{ $debtCustomer->nama_produk ?? 'N/A' }}</td>
                            <td class="px-6 py-4">{{ rupiah_format($debtCustomer->debt_amount) }}</td>
                            <td class="px-6 py-4">{{ rupiah_format($debtCustomer->paid_amount) }}</td>
                            <td class="px-6 py-4">
                                {{ $debtCustomer->last_payment_date ? date('d M Y', strtotime($debtCustomer->last_payment_date)) : 'Belum ada pembayaran' }}
                            </td>
                            <td class="px-6 py-4">
                                {{ date('d M Y', strtotime($debtCustomer->purchase_date)) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-4 text-center">Tidak ada data hutang customer</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $debtCustomers->links() }}
        </div>
    </div>
</div>
